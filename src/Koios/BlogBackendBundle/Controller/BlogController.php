<?php

namespace Koios\BlogBackendBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Koios\BlogBackendBundle\Entity\Blog;
use Koios\BlogBackendBundle\Entity\Comment;
use FOS\Rest\Util\Codes;
use Guzzle\Http\Message\Response;

class BlogController extends Controller {

    /**
     * @Rest\View
     */
    public function getBlogsAction($page = 1, $per_page = 25) {
        $em = $this->getDoctrine()->getEntityManager();
        $offset = ($page - 1) * $per_page;
        return $em->getRepository('KoiosBlogBackendBundle:Blog')->findBy(array(), array('created' => 'DESC'), $per_page, $offset);
    }

    /**
     * @Rest\View
     */
    public function getBlogAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        return $em->find('KoiosBlogBackendBundle:Blog', $id);
    }

    /**
     * @Rest\View
     */
    public function getCommentsAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $blog = $em->find('KoiosBlogBackendBundle:Blog', $id);

        if (!$blog instanceof Blog ) {
            throw new NotFoundHttpException("Not an instance of blog");
        }

		return $em->getRepository('KoiosBlogBackendBundle:Comment')->getLatestComments(null);
    }

    public function postBlogCommentAction() {
        $request = $this->getRequest();

        $comment = new Comment();
        $comment->setBlog($this->getBlogAction($request->get('id')));
        $comment->setUser($request->get('user'));
        $comment->setComment($request->get('comment'));

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($comment);
        $em->flush();

        new Response(Codes::HTTP_CREATED);
    }

    /**
     * @Rest\View
     */
    public function getLatestCommentsAction($limit) {
        $em = $this->getDoctrine()->getEntityManager();
        return $em->getRepository('KoiosBlogBackendBundle:Comment')->getLatestComments($limit);
    }

    /**
     * @Rest\View
     */
    public function getTagWeightsAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $tags = $em->getRepository('KoiosBlogBackendBundle:Blog')->getTags();
        return $em->getRepository('KoiosBlogBackendBundle:Blog')->getTagWeights($tags);
    }
}