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
    public function newAction($id)
    {
        return $this->render('KoiosBlogBundle:Comment:form.html.twig', array(
                'id' => $id,
                'form' => $this->getForm()->createView()
            ));
    }

    public function createAction($id)
    {
        $client = $this->get('backend_client');
        $blog = $client->getBlog($id);

        $request = $this->getRequest();
        $form    = $this->getForm();
        $form->bindRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            try {
                $client->createComment($data['user'], $data['comment'], $id);

                return $this->redirect($this->generateUrl('KoiosBlogBundle_blog_show', array(
                            'id'   => $id,
                            'slug' => $blog['blog']['slug']
                        )));
            } catch ( \Guzzle\Http\Exception\BadResponseException $e) {
                $this->get('logger')->err($e->getMessage());
                $this->get('session')->setFlash('blogger-error', $e->getMessage());
            }
        }

        return $this->render('KoiosBlogBundle:Comment:create.html.twig', array(
                'title' => $blog['blog']['title'],
				'id'    => $id,
                'form'  => $form->createView()
            ));
    }

    protected function getForm() {
        return $this->createFormBuilder()
            ->add('user', 'text')
            ->add('comment', 'textarea')
            ->getForm();
    }
}