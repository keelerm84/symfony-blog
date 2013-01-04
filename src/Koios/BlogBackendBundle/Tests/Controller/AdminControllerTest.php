<?php

namespace Koios\BlogBackendBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Koios\BlogBackendBundle\Controller\AdminController;

class AdminControllerTest extends WebTestCase {
    protected $client;
    protected $router;

    public function setUp() {
        // TODO Refactor these credentials to a configuration file
        $headers = array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'password',
            'CONTENT_TYPE'  => 'application/json',
            'HTTP_ACCEPT'   => 'application/json'
        );
        $this->client = static::createClient(array(), $headers);
        $this->router = static::$kernel->getContainer()->get('router');
    }

    public function tearDown() {
        restoreDatabase();
    }

    public function testCreateBlog() {
        $data = array('title' => 'Title', 'blog'  => 'Here is a blog', 'tags'  => 'test, symfony');

        $this->client->request('POST', $this->router->generate('KoiosBlogBackendBundle_create_blog'),
            array(), array(),
            array(),
            json_encode($data)
        );

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateBlogWithMissingFields() {
        $this->client->request('POST', $this->router->generate('KoiosBlogBackendBundle_create_blog'), array(), array());

        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals(count(json_decode($response->getContent(), true)), 3);
    }

    public function testCreateBlogWithLongTitle() {
        $data = array(
            'title' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',
            'blog'  => 'Here is a blog',
            'tags'  => 'test, symfony'
        );

        $this->client->request('POST', $this->router->generate('KoiosBlogBackendBundle_create_blog'), array(), array(), array(), json_encode($data));

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(count(json_decode($this->client->getResponse()->getContent(), true)), 1);
    }

    public function testEditBlog() {
        $data = array('title' => 'Title', 'blog'  => 'Here is a blog', 'tags'  => 'test, symfony');

        $this->client->request('PUT', $this->router->generate('KoiosBlogBackendBundle_edit_blog', array('id' => 1)), array(), array(), array(), json_encode($data)
        );

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteBlog() {
        $this->client->request('DELETE', $this->router->generate('KoiosBlogBackendBundle_delete_blog'), array('blogs' => array(1)), array()
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
