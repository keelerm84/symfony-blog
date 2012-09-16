<?php

namespace Koios\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Koios\BlogBundle\Entity\Enquiry;
use Koios\BlogBundle\Form\EnquiryType;


class PageController extends Controller
{
    public function indexAction()
    {
        $client = new \Guzzle\Service\Client();

        $req = $client->get("http://localhost:9900/app_dev.php/api/blogs");
        $req->setHeader('Content-type', 'application/json');
        $req->setHeader('Accept', 'application/json');

        $response = $req->send();
        $blogs = json_decode($response->getBody(true));

        return $this->render('KoiosBlogBundle:Page:index.html.twig', array('blogs' => $blogs->blogs));
    }

    public function aboutAction()
    {
        return $this->render('KoiosBlogBundle:Page:about.html.twig');
    }

    public function contactAction()
    {
        $enquiry = new Enquiry();
        $form = $this->createForm(new EnquiryType(), $enquiry);

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $message = \Swift_Message::newInstance()
                    ->setSubject('Contact Enquiry')
                    ->setFrom('enquiries@koios.com')
                    ->setTo($this->container->getParameter('koios_blog.emails.contact_email'))
                    ->setBody($this->renderView('KoiosBlogBundle:Page:contactEmail.txt.twig', array('enquiry' => $enquiry)));

                $this->get('mailer')->send($message);

                $this->get('session')->setFlash('blogger-notice', 'Your contact enquiry was successfully sent.  Thank you!');

                return $this->redirect($this->generateUrl('KoiosBlogBundle_contact'));
            }
        }

        return $this->render('KoiosBlogBundle:Page:contact.html.twig', array('form' => $form->createView()));
    }

    public function sidebarAction() {
        $client = new \Guzzle\Service\Client();

        $limit = $this->container->getParameter('koios_blog.comments.latest_comment_limit');
        $req = $client->get("http://localhost:9900/app_dev.php/api/comments/latest/{$limit}");
        $req->setHeader('Content-type', 'application/json');
        $req->setHeader('Accept', 'application/json');

        $comments = json_decode($req->send()->getBody(true));

        $req = $client->get("http://localhost:9900/app_dev.php/api/tagWeights");
        $req->setHeader('Content-type', 'application/json');
        $req->setHeader('Accept', 'application/json');

        $tagWeights = json_decode($req->send()->getBody(true), true);

        return $this->render('KoiosBlogBundle:Page:sidebar.html.twig', array(
                'tags'           => $tagWeights['weights'],
                'latestComments' => $comments->comments
        ));
    }
}
