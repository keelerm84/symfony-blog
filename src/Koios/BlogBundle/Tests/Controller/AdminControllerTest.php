<?php

namespace Koios\BlogBundle\Tests\Controller;

use Koios\BlogTestCase;

class AdminControllerTest extends BlogTestCase
{
    protected $client;
    protected $router;

    public function setUp() {
        // TODO Refactor these credentials to a configuration file
        $headers = array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'password'
        );
        $this->client = static::createClient(array(), $headers);
        $this->router = static::$kernel->getContainer()->get('router');
    }

    public function testEditBlog()
    {
        $crawler = $this->client->request('GET',
                   $this->router->generate('KoiosBlogBundle_admin_blog_edit', array('id' => 1)), array(), array(), array());

        $form = $crawler->selectButton('submit')->form();

        $form->setValues(array(
                'form[title]' => 'New Title',
                'form[blog]' => 'New Content',
                'form[tags]' => 'new tag'
            ));

        $crawler = $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertCount(1, $crawler->filter('body:contains("New Title")'));
    }

    public function testFailEditBlog()
    {
        $crawler = $this->client->request('GET',
                   $this->router->generate('KoiosBlogBundle_admin_blog_edit', array('id' => 1)), array(), array(), array());

        $form = $crawler->selectButton('submit')->form();

        $form->setValues(array(
                'form[title]' => '',
                'form[blog]' => 'New Content',
                'form[tags]' => 'new tag'
            ));

        $crawler = $this->client->submit($form);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('body:contains("You must specify Title")'));
    }

    public function testCreateBlog()
    {
        $crawler = $this->client->request('GET',
                   $this->router->generate('KoiosBlogBundle_admin_blog_create'), array(), array(), array());

        $form = $crawler->selectButton('submit')->form();

        $form->setValues(array(
                'form[title]' => 'New Title',
                'form[blog]' => 'New Content',
                'form[tags]' => 'new tag'
            ));

        $crawler = $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertCount(1, $crawler->filter('body:contains("New Title")'));
    }

    public function testFailCreateBlog()
    {
        $crawler = $this->client->request('GET',
                   $this->router->generate('KoiosBlogBundle_admin_blog_create'), array(), array(), array());

        $form = $crawler->selectButton('submit')->form();

        $form->setValues(array(
                'form[title]' => '',
                'form[blog]' => 'New Content',
                'form[tags]' => 'new tag'
            ));

        $crawler = $this->client->submit($form);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('body:contains("You must specify Title")'));
    }

    public function testDeleteBlog()
    {
        $crawler = $this->client->request('GET',
                 $this->router->generate('KoiosBlogBundle_admin'), array(), array(), array());

        // Select based on button value, or id or name for buttons
        $form = $crawler->selectButton('Delete Selected')->form();

        $form->setValues(array('blogs' => array('1' => 1, '2' => 1)));

        $crawler = $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertCount(0, $crawler->filter('body:contains("A day with Symfony2")'));
        $this->assertCount(0, $crawler->filter('body:contains("The pool on the root must have a leak")'));
        $this->assertCount(1, $crawler->filter('body:contains("Misdirection. What the eyes see and the ears hear, the mind believes")'));
    }
}