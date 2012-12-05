<?php

namespace Koios\BlogBackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Koios\BlogBackendBundle\Entity\Blog;
use Koios\BlogBackendBundle\Entity\Comment;
use FOS\Rest\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller {
    public function createAction(Request $request) {
        $errors = $this->formIsValid($request);

        if ( true !== $errors ) return $this->generateBadRequest($errors);

        $data = json_decode($request->getContent());

        $blog = new Blog();

        $blog->setAuthor('admin');
        $blog->setTitle($data->title);
        $blog->setBlog($data->blog);
        $blog->setTags($data->tags);

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($blog);
        $em->flush();

        $response = new Response();
        $response->setStatusCode(Codes::HTTP_CREATED);
        return $response;
    }

    public function editAction($id) {
        $request = $this->getRequest();
        $errors = $this->formIsValid($request);

        if ( true !== $errors ) return $this->generateBadRequest($errors);

        $data = json_decode($request->getContent());

        $em = $this->getDoctrine()->getEntityManager();
        $blog = $em->find('KoiosBlogBackendBundle:Blog', $id);
        $blog->setTitle($data->title);
        $blog->setBlog($data->blog);
        $blog->setTags($data->tags);
        $blog->setUpdated(new \DateTime());

        $em->persist($blog);
        $em->flush();

        $response = new Response();
        $response->setStatusCode(Codes::HTTP_CREATED);
        return $response;
    }

    public function deleteAction(Request $request) {
        $em = $this->getDoctrine()->getEntityManager();

        foreach($request->get('blogs') as $id) {
            $blog = $em->find('KoiosBlogBackendBundle:Blog', $id);
            $em->remove($blog);
        }

        $em->flush();

        $response = new Response();
        $response->setStatusCode(Codes::HTTP_CREATED);
    }

    protected function formIsValid($request) {
        $errors = array();

        $required = array(
            'blog'  => -1,
            'title' => 255,
            'tags'  => -1
        );

        $data = json_decode($request->getContent());

        foreach($required as $field => $maxlen) {
            if ( ! isset($data->$field) ) {
                $errors[] = 'You must specify ' . ucwords($field);
            } else if ( 0 < $maxlen && $maxlen < strlen($data->$field) ) {
                $errors[] = $field . ' cannot be more than ' . $maxlen . ' characters.';
            }
        }

        return count($errors) ? $errors : true;
    }
    protected function generateBadRequest($errors) {
        $response = new Response();
        $response->setStatusCode(Codes::HTTP_BAD_REQUEST);
        $response->setContent(json_encode($errors));

        return $response;
    }
}
