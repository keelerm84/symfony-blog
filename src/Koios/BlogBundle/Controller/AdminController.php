<?php

namespace Koios\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Koios\BlogBundle\Form\BlogType;
use Koios\BlogBundle\Entity\Blog;

/**
 * Admin controller.
 */
class AdminController extends Controller
{
    public function indexAction() {
        $client = new \Guzzle\Service\Client();

        $req = $client->get("http://localhost:9900/app_dev.php/api/blogs");
        $req->setHeader('Content-type', 'application/json');
        $req->setHeader('Accept', 'application/json');
        $response = $req->send();

        $blogs = json_decode($req->send()->getBody(true));

        return $this->render('KoiosBlogBundle:Admin:index.html.twig', array('blogs' => $blogs->blogs));
    }

    public function deleteAction(Request $request) {
        $blog_ids = $request->get('blogs', false);

        if(is_array($blog_ids)) {
            $client = new \Guzzle\Service\Client();

            $req = $client->delete("http://localhost:9900/app_dev.php/api/admin/blogs/?" . http_build_query(array('blogs' => array_keys($blog_ids))));
            $req->setHeader('Content-type', 'application/json');
            $req->setHeader('Accept', 'application/json');
            $req->setAuth('admin', 'password');

            $response = $req->send();
        }

        return $this->redirect($this->generateUrl('KoiosBlogBundle_admin', array()));
    }

    public function createAction() {
        $request = $this->getRequest();
        $form = $this->getForm();

        if('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            if($form->isValid()) {
                $data = $form->getData();

                $client = new \Guzzle\Service\Client();

                try {
                    $request = $client->post('http://localhost:9900/app_dev.php/api/admin/blog/create/')
                        ->addPostFields(array(
                                'title' => $data['title'],
                                'blog'  => $data['blog'],
                                'tags'  => $data['tags']
                            ));
                    $request->setAuth('admin', 'password');
                    $request->send();

                    $this->get('session')->setFlash('blogger-notice', 'Successfully created new blog entry');

                    return $this->redirect($this->generateUrl('KoiosBlogBundle_admin', array()));
                } catch ( \Guzzle\Http\Exception\BadResponseException $e) {
                    $this->get('session')->setFlash('blogger-error', $e->getMessage());
                     }
            }
        }

        return $this->render('KoiosBlogBundle:Admin:create.html.twig', array(
                'form'  => $form->createView()
            ));
    }

    public function editAction($blog_id) {
        $request = $this->getRequest();
        $form = $this->getForm();

        $client = new \Guzzle\Service\Client();
        $req = $client->get("http://localhost:9900/app_dev.php/api/blog/{$blog_id}");
        $req->setHeader('Content-type', 'application/json');
        $req->setHeader('Accept', 'application/json');
        $response = $req->send();

        $blog = json_decode($req->send()->getBody(true));

        if('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            if($form->isValid()) {
                $data = $form->getData();

                $client = new \Guzzle\Service\Client();

                try {
                    $request = $client->put('http://localhost:9900/app_dev.php/api/admin/blog/' . $blog_id, array(),
                               json_encode(array(
                                       'blog' => $data['blog'],
                                       'title' => $data['title'],
                                       'tags' => $data['tags']
                                   )));
                    $request->setAuth('admin', 'password');
                    $request->send();

                    $this->get('session')->setFlash('blogger-notice', 'Successfully update blog entry');

                    return $this->redirect($this->generateUrl('KoiosBlogBundle_admin', array()));
                } catch ( \Guzzle\Http\Exception\BadResponseException $e) {
                    $this->get('session')->setFlash('blogger-error', $e->getMessage());
                }
            }
        } else if ( 'GET' == $request->getMethod() ) {
            $form->setData(array(
                    'title' => $blog->blog->title,
                    'blog' => $blog->blog->blog,
                    'tags' => $blog->blog->tags
                ));
        }

        return $this->render('KoiosBlogBundle:Admin:edit.html.twig', array(
                'form'  => $form->createView(),
                'blog' => $blog->blog
            ));
    }

    protected function getForm() {
        return $this->createFormBuilder()
            ->add('title', 'text')
            ->add('blog', 'ckeditor')
            ->add('tags', 'text')
            ->getForm();
    }
}
