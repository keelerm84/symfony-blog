<?php

namespace Koios\BlogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    public function testAbout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/about');

        $this->assertEquals(1, $crawler->filter('h1:contains("About symblog")')->count());
    }

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        // Check there are some blog entries on the page
        $this->assertTrue($crawler->filter('article.blog')->count() > 0);

        // Find the first link, get the title, ensure this is loaded on the next page
        $blogLink   = $crawler->filter('article.blog h2 a')->first();
        $blogTitle  = $blogLink->text();
        $crawler    = $client->click($blogLink->link());

        // Check the h2 has the blog title in it
        $this->assertEquals(1, $crawler->filter('h2:contains("' . $blogTitle .'")')->count());
    }

    public function testContact()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contact');

        $this->assertEquals(1, $crawler->filter('h1:contains("Contact symblog")')->count());

        // Select based on button value, or id or name for buttons
        $form = $crawler->selectButton('Submit')->form();

        $form['contact[name]']       = 'name';
        $form['contact[subject]']    = 'Subject';
        $form['contact[email]']      = 'email@email.com';
        $form['contact[body]']       = 'The comment body must be at least 50 characters long as there is a validation constrain on the Enquiry entity';

        $crawler = $client->submit($form);

        if ( $profile = $client->getProfile() ) {
            $swiftMailerProfiler = $profile->getCollector('swiftmailer');

            $this->assertEquals(1, $swiftMailerProfiler->getMessageCount());

            $messages = $swiftMailerProfiler->getMessages();
            $message = array_shift($messages);

            $symblogEmail = $client->getContainer()->getParameter('koios_blog.emails.contact_email');

            $this->assertArrayHasKey($symblogEmail, $message->getTo());
        }

        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('body:contains("Your contact enquiry was successfully sent.  Thank you!")')->count());
    }
}