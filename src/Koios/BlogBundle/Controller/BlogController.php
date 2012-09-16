<?php

namespace Koios\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Blog controller.
 */
class BlogController extends Controller
{
    /**
     * Show a blog entry
     */
    public function showAction($id, $slug)
    {
        $client = new \Guzzle\Service\Client();
        $req = $client->get("http://localhost:9900/app_dev.php/api/blog/{$id}");
        $req->setHeader('Content-type', 'application/json');
        $req->setHeader('Accept', 'application/json');
        $response = $req->send();

        $blog = json_decode($req->send()->getBody(true));

        $req = $client->get("http://localhost:9900/app_dev.php/api/blog/{$id}/comments");
        $req->setHeader('Content-type', 'application/json');
        $req->setHeader('Accept', 'application/json');

        $comments = json_decode($req->send()->getBody(true));

        return $this->render('KoiosBlogBundle:Blog:show.html.twig', array('blog' => $blog->blog, 'comments' => $comments->comments));
    }
}