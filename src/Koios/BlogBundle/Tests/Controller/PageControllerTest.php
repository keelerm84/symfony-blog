<?php

namespace Koios\BlogBundle\Tests\Controller;

use Koios\BlogTestCase;

class PageControllerTest extends BlogTestCase
{
    public function testAbout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/about');

        $this->assertCount(1, $crawler->filter('h1:contains("About symblog")'));
    }

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        // Check there are some blog entries on the page
        $this->assertGreaterThan(0, $crawler->filter('article.blog')->count());

        // Find the first link, get the title, ensure this is loaded on the next page
        $blogLink   = $crawler->filter('article.blog h2 a')->first();
        $blogTitle  = $blogLink->text();
        $crawler    = $client->click($blogLink->link());

        // Check the h2 has the blog title in it
        $this->assertCount(1, $crawler->filter('h2:contains("' . $blogTitle .'")'));
    }

    public function testContact()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contact');

        $this->assertCount(1, $crawler->filter('h1:contains("Contact symblog")'));

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

        $this->assertCount(1, $crawler->filter('body:contains("Your contact enquiry was successfully sent.  Thank you!")'));
    }
}