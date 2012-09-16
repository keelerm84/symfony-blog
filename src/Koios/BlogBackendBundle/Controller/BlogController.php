<?php

namespace Koios\BlogBackendBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Koios\BlogBackendBundle\Entity\Blog;
use Koios\BlogBackendBundle\Entity\Comment;
use FOS\RestBundle\Util\Codes;

class BlogController extends Controller {

    /**
     * @Rest\View
     */
    public function getBlogsAction($page = 1, $per_page = 25) {
        $em = $this->getDoctrine()->getEntityManager();
        $offset = ($page - 1) * $per_page;
        $blogs = $em->getRepository('KoiosBlogBackendBundle:Blog')->findBy(array(), array('created' => 'DESC'), $per_page, $offset);

        return array('blogs' => $blogs);
    }

    /**
     * @Rest\View
     */
    public function getBlogAction($blog_id) {
        $em = $this->getDoctrine()->getEntityManager();
        $blog = $em->find('KoiosBlogBackendBundle:Blog', $blog_id);

        if (!$blog instanceof Blog ) {
            throw new NotFoundHttpException(var_dump($blog));
        }

        return array('blog' => $blog);
    }

    /**
     * @Rest\View
     */
    public function getCommentsAction($blog_id) {
        $em = $this->getDoctrine()->getEntityManager();
        $blog = $em->find('KoiosBlogBackendBundle:Blog', $blog_id);

        if (!$blog instanceof Blog ) {
            throw new NotFoundHttpException(var_dump($blog));
        }

        return array('comments' => $blog->getComments());
    }

    public function postBlogCommentAction() {
        $request = $this->getRequest();

        $comment = new Comment();
        $comment->setBlog($this->getBlogAction($request->get('blog_id'))['blog']);
        $comment->setUser($request->get('user'));
        $comment->setComment($request->get('comment'));

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($comment);
        $em->flush();

        $response = new Response();
        $response->setStatusCode(Codes::HTTP_CREATED);
    }

    /**
     * @Rest\View
     */
    public function getLatestCommentsAction($limit) {
        $em = $this->getDoctrine()->getEntityManager();
        $latestComments = $em->getRepository('KoiosBlogBackendBundle:Comment')->getLatestComments($limit);

        return array('comments' => $latestComments);
    }

    /**
     * @Rest\View
     */
    public function getTagWeightsAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $tags = $em->getRepository('KoiosBlogBackendBundle:Blog')->getTags();
        $tagWeights = $em->getRepository('KoiosBlogBackendBundle:Blog')->getTagWeights($tags);

        return array('weights' => $tagWeights);
    }
}