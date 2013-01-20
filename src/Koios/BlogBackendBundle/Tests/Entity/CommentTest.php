<?php

namespace Koios\BlogBackendBundle\Tests\Entity;

use Koios\BlogBackendBundle\Entity\Comment;
use Koios\BlogBackendBundle\Entity\Blog;

class CommentTest extends \PHPUnit_Framework_TestCase {
    public function testSetUser() {
        $comment = new Comment();

        $comment->setUser('commenter');
        $this->assertEquals('commenter', $comment->getUser());
    }

    public function testSetComment() {
        $comment = new Comment();

        $comment->setComment('Here is a comment for the blog');
        $this->assertEquals('Here is a comment for the blog', $comment->getComment());
    }

    public function testSetApproved() {
        $comment = new Comment();

        $comment->setApproved(true);
        $this->assertTrue($comment->getApproved());

        $comment->setApproved(false);
        $this->assertFalse($comment->getApproved());
    }

    public function testSetCreated() {
        $comment = new Comment();

        $dateTime = new \DateTime();
        $comment->setCreated($dateTime);
        $this->assertEquals($dateTime, $comment->getCreated());
    }

    public function testSetUpdated() {
        $comment = new Comment();

        $dateTime = new \DateTime();
        $comment->setUpdated($dateTime);
        $this->assertEquals($dateTime, $comment->getUpdated());

        $comment->setUpdatedValue();
        $this->assertEquals($dateTime, $comment->getUpdated());
    }

    public function testSetBlog() {
        $comment = new Comment();
        $blog = new Blog();

        $comment->setBlog($blog);
        $this->assertEquals($blog, $comment->getBlog());
    }

}