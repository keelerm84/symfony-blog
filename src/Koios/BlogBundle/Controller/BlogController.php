<?php

namespace Koios\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Guzzle\Service\Description\ServiceDescription;

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
      $client = $this->get('backend_client');
      $blog = $client->getBlog($id);
      $comments = $client->getBlogComments($id);

      $response = $this->render('KoiosBlogBundle:Blog:show.html.twig', array('blog' => $blog['blog'], 'comments' => $comments['comments']));
      $response->setPublic();
      $response->setMaxAge(90);
      $response->setSharedMaxAge(90);
      $response->setLastModified(new \DateTime(min($blog['lastModified'], $comments['lastModified'])));

      return $response;
    }
}
