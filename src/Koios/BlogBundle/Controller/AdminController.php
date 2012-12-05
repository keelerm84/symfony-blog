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
        $client = $this->get('backend_client');
        $command = $client->getCommand("GetBlogs");
        $blogs = $client->execute($command);

        return $this->render('KoiosBlogBundle:Admin:index.html.twig', array('blogs' => $blogs));
    }

    public function deleteAction(Request $request) {
        $ids = $request->get('blogs', false);

        if(is_array($ids)) {
            $client = $this->get('backend_client');
            $command = $client->getCommand("DeleteBlogs", array(
                           'blogs' => array_keys($ids),
                           'headers' => array('Authorization' => 'Basic ' . base64_encode('admin:password'))));
            $client->execute($command);
        }

        return $this->redirect($this->generateUrl('KoiosBlogBundle_admin', array()));
    }

    public function createAction() {
      $request = $this->getRequest();
      $form = $this->getForm();

      if('POST' == $request->getMethod()) {
          $client = $this->get('backend_client');

          $form->bindRequest($request);
          $data = $form->getData();
          $data['headers'] = array('Authorization' => 'Basic ' . base64_encode('admin:password'));

          $command = $client->getCommand("CreateBlog", $data);

          try {
            $client->execute($command);

            $commentsModified = strtotime($command->getResponse()->getLastModified());

            $this->get('session')->setFlash('blogger-notice', 'Successfully created new blog entry');

            return $this->redirect($this->generateUrl('KoiosBlogBundle_admin', array()));
          } catch ( \Guzzle\Http\Exception\BadResponseException $e) {
            $error = '<ul>' . implode('</li><li>', json_decode($e->getResponse()->getBody(true))) . '</ul>';
            $this->get('session')->setFlash('blogger-error', $error);
          } catch ( \Exception $e) {
            $this->get('session')->setFlash('blogger-error', 'An unknown error occurred.');
          }
      }

      return $this->render('KoiosBlogBundle:Admin:create.html.twig', array('form'  => $form->createView()));
    }

    public function editAction($id) {
      $request = $this->getRequest();
      $form = $this->getForm();

      $client = $this->get('backend_client');
      $command = $client->getCommand("GetBlog", array('id' => $id));
      $blog = $command->execute($command);

      if('POST' == $request->getMethod()) {
        $form->bindRequest($request);

        if($form->isValid()) {
          $data = $form->getData();

          $command = $client->getCommand("EditBlog", array(
                                'id'      => $id,
                                'blog'    => $data['blog'],
                                'title'   => $data['title'],
                                'tags'    => $data['tags'],
                                'headers' => array('Authorization' => 'Basic ' . base64_encode('admin:password'))
                            ));

          try {
            $client->execute($command);

            $this->get('session')->setFlash('blogger-notice', 'Successfully update blog entry');

            return $this->redirect($this->generateUrl('KoiosBlogBundle_admin', array()));
          } catch ( \Guzzle\Http\Exception\BadResponseException $e) {
            $error = '<ul>' . implode('</li><li>', json_decode($e->getResponse()->getBody(true))) . '</ul>';
            $this->get('session')->setFlash('blogger-error', $error);
          }
        }
      } else if ( 'GET' == $request->getMethod() ) {
        $form->setData(array(
                    'title' => $blog['title'],
                    'blog' => $blog['blog'],
                    'tags' => $blog['tags']
                ));
      }

      return $this->render('KoiosBlogBundle:Admin:edit.html.twig', array(
                'form'  => $form->createView(),
                'blog' => $blog
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
