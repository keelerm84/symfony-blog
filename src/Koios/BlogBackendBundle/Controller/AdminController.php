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
        $blog = new Blog();
        $blog->setTitle($request->get('title'));
        $blog->setAuthor('admin');
        $blog->setBlog($request->get('blog'));
        $blog->setTags($request->get('tags'));

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($blog);
        $em->flush();

        $response = new Response();
        $response->setStatusCode(Codes::HTTP_CREATED);
        return $response;
    }

    public function editAction($blog_id) {
        $request = $this->getRequest();

        $data = json_decode($request->getContent());

        $em = $this->getDoctrine()->getEntityManager();
        $blog = $em->find('KoiosBlogBackendBundle:Blog', $blog_id);
        $blog->setTitle($data->title);
        $blog->setBlog($data->blog);
        $blog->setTags($data->tags);

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
}
