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
        $blog = $this->getBlog($id);

        $request = $this->getRequest();
        $form    = $this->getForm();
        $form->bindRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $client = $this->get('backend_client');
            try {
			  $command = $client->getCommand("CreateComment", array('user' => $data['user'], 'comment' => $data['comment'], 'id' => $id));
			  $client->execute($command);

                return $this->redirect($this->generateUrl('KoiosBlogBundle_blog_show', array(
                            'id'   => $id,
                            'slug' => $blog['slug']
                        )));
            } catch ( \Guzzle\Http\Exception\BadResponseException $e) {
                $this->get('session')->setFlash('blogger-error', $e->getMessage());
            }
        }

        return $this->render('KoiosBlogBundle:Comment:create.html.twig', array(
                'title' => $blog['title'],
				'id' => $id,
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
	  $client = $this->get('backend_client');
	  $command = $client->getCommand("GetBlog", array('id' => $id));
	  $blog = $client->execute($command);

	  return $blog;
    }
}