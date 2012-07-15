<?php

namespace Koios\BlogBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase {
    public function testAddBlogComment() {
        $client = static::createClient();

        $crawler = $client->request('GET', '/16/a-day-with-symfony');

        $this->assertEquals(1, $crawler->filter('h2:contains("A day with Symfony2")')->count());

        $form = $crawler->selectButton('Submit')->form();

        $crawler = $client->submit($form, array(
            'koios_blogbundle_commenttype[user]' => 'name',
            'koios_blogbundle_commenttype[comment]' => 'comment'
        ));

        $crawler = $client->followRedirect();

        $articleCrawler = $crawler->filter('section.previous-comments article')->last();

        $this->assertEquals('name', $articleCrawler->filter('header span.highlight')->text());
        $this->assertEquals('comment', $articleCrawler->filter('p')->last()->text());

        $this->assertEquals(10, $crawler->filter('aside.sidebar section')->last()
                                        ->filter('article')->count());

        $this->assertEquals('name', $crawler->filter('aside.sidebar section')->last()
                                            ->filter('article')->first()
                                            ->filter('header span.highlight')->text());
    }
}
