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
	  $command = $client->getCommand("GetBlog", array('id' => $id));
	  $blog = $command->execute($command);

	  $command = $client->getCommand("GetBlogComments", array('id' => $id));
	  $comments = $command->execute($command);

	  return $this->render('KoiosBlogBundle:Blog:show.html.twig', array('blog' => $blog, 'comments' => $comments));
    }
}