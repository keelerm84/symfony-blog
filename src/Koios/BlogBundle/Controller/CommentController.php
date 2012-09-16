<?php
// src/Koios/BlogBundle/Controller/CommentController.php

namespace Koios\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Koios\BlogBackendBundle\Entity\Comment;
use Koios\BlogBundle\Form\CommentType;

/**
 * Comment controller.
 */
class CommentController extends Controller
{
    public function newAction($blog_id)
    {
        return $this->render('KoiosBlogBundle:Comment:form.html.twig', array(
                'blog_id' => $blog_id,
                'form' => $this->getForm()->createView()
            ));
    }

    public function createAction($blog_id)
    {
        $blog = $this->getBlog($blog_id);

        $request = $this->getRequest();
        $form    = $this->getForm();
        $form->bindRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $client = new \Guzzle\Service\Client();
            try {
                $request = $client->post('http://localhost:9900/app_dev.php/api/comment/')
                    ->addPostFields(array(
                            'user'    => $data['user'],
                            'comment' => $data['comment'],
                            'blog_id' => $blog_id
                        ));
                $request->send();

                return $this->redirect($this->generateUrl('KoiosBlogBundle_blog_show', array(
                            'id'   => $blog->id,
                            'slug' => $blog->slug
                        )));
            } catch ( \Guzzle\Http\Exception\BadResponseException $e) {
                $this->get('session')->setFlash('blogger-error', $e->getMessage());
            }
        }

        return $this->render('KoiosBlogBundle:Comment:create.html.twig', array(
                'title' => $blog->title,
                'form'  => $form->createView()
            ));
    }

    protected function getForm() {
        return $this->createFormBuilder()
            ->add('user', 'text')
            ->add('comment', 'textarea')
            ->getForm();
    }

    protected function getBlog($id)
    {
        $client = new \Guzzle\Service\Client();
        $req = $client->get("http://localhost:9900/app_dev.php/api/blog/{$id}");
        $req->setHeader('Content-type', 'application/json');
        $req->setHeader('Accept', 'application/json');
        $response = $req->send();

        $blog = json_decode($req->send()->getBody(true));

        return $blog->blog;
    }

}