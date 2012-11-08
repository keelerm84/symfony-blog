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
      $this->get('logger')->err('Logger is working client side');
      $client = $this->get('backend_client');
      $command = $client->getCommand("GetBlog", array('id' => $id));
      $blog = $command->execute($command);

      $blogModified = strtotime($command->getResponse()->getLastModified());

      $command = $client->getCommand("GetBlogComments", array('id' => $id));
      $comments = $command->execute($command);

      $commentsModified = strtotime($command->getResponse()->getLastModified());

      $response = $this->render('KoiosBlogBundle:Blog:show.html.twig', array('blog' => $blog, 'comments' => $comments));
      $response->setPublic();
      $response->setMaxAge(90);
      $response->setSharedMaxAge(90);
      $response->setLastModified(new \DateTime(min($blogModified, $commentsModified)));

      return $response;
    }
}
