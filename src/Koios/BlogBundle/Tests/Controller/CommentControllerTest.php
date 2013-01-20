<?php

namespace Koios\BlogBundle\Tests\Controller;

use Koios\BlogTestCase;

class CommentControllerTest extends BlogTestCase
{
    protected $router;
    protected $client;

    public function setUp() {
        $this->client = static::createClient(array(), array());
        $this->router = static::$kernel->getContainer()->get('router');
    }

    public function testCreateComment()
    {
        $crawler = $this->client->request('GET',
                   $this->router->generate('KoiosBlogBundle_blog_show', array('id' => 1, 'slug' => 'a-day-with-symfony2')), array(), array(), array());

        // Select based on button value, or id or name for buttons
        $form = $crawler->selectButton('Submit')->form();

        $form['form[user]']       = 'user';
        $form['form[comment]']    = 'Here is a comment.';

        $crawler = $this->client->submit($form);

        $crawler = $this->client->followRedirect();

        $this->assertCount(1, $crawler->filter('body:contains("Here is a comment.")'));
    }

    public function testCreateCommentFailure()
    {
        $crawler = $this->client->request('GET',
                   $this->router->generate('KoiosBlogBundle_blog_show', array('id' => 1, 'slug' => 'a-day-with-symfony2')), array(), array(), array());

        // Select based on button value, or id or name for buttons
        $form = $crawler->selectButton('Submit')->form();

        $form['form[comment]']    = 'Here is a comment.';

        $crawler = $this->client->submit($form);

        $this->assertNotEmpty($crawler->filter('.blogger-error'), 'Blogger error should be empty');
    }
}