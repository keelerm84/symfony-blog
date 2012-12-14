<?php

namespace Koios\BlogBackendBundle\Tests\Entity;

use Koios\BlogBackendBundle\Entity\Blog;

class BlogTest extends \PHPUnit_Framework_TestCase {
    public function testSlugify() {
        $blog = new Blog();

        $this->assertEquals('hello-world', $blog->slugify('Hello World'));
        $this->assertEquals('a-day-with-symfony2', $blog->slugify('A Day With Symfony2'));
        $this->assertEquals('hello-world', $blog->slugify('Hello    world'));
        $this->assertEquals('symblog', $blog->slugify('symblog '));
        $this->assertEquals('symblog', $blog->slugify(' symblog'));
    }

    public function testSetSlug() {
        $blog = new Blog();

        $blog->setSlug('Symfony2 Blog');
        $this->assertEquals('symfony2-blog', $blog->getSlug());
        $blog->setSlug('');
        $this->assertEquals('n-a', $blog->getSlug());
    }

    public function testSetTitle() {
        $blog = new Blog();

        $blog->setTitle('Hello World');
        $this->assertEquals('hello-world', $blog->getSlug());
        $this->assertEquals('Hello World', $blog->getTitle());
    }

    public function testSetAuthor() {
        $blog = new Blog();

        $blog->setAuthor('author');
        $this->assertEquals('author', $blog->getAuthor());
    }

    public function testSetBlog() {
        $blog = new Blog();

        $blog->setBlog('Set the blog entry here');
        $this->assertEquals('Set the blog entry here', $blog->getBlog());
        $this->assertEquals('Set the', $blog->getBlog(7));
    }

    public function testSetImage() {
        $blog = new Blog();

        $blog->setImage('image.png');
        $this->assertEquals('image.png', $blog->getImage());
    }

    public function testSetTags() {
        $blog = new Blog();

        $blog->setTags('symfony,php,phpunit');
        $this->assertEquals('symfony,php,phpunit', $blog->getTags());
    }

    public function testSetCreated() {
        $blog = new Blog();

        $dateTime = new \DateTime();
        $blog->setCreated($dateTime);
        $this->assertEquals($dateTime, $blog->getCreated());
    }

    public function testSetUpdated() {
        $blog = new Blog();

        $dateTime = new \DateTime();
        $blog->setUpdated($dateTime);
        $this->assertEquals($dateTime, $blog->getUpdated());

        $dateTime = new \DateTime();
        $blog->setUpdatedValue($dateTime);
        $this->assertEquals($dateTime, $blog->getUpdated());
    }
}