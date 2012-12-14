<?php

namespace Koios\BlogBackendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Koios\BlogBackendBundle\Entity\Blog;
use Koios\BlogBackendBundle\Entity\Comment;

class CommentRepositoryTest extends WebTestCase
{
    protected $commentRepo = null;
    protected $blogRepo = null;
    protected $em = null;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->em          = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->commentRepo = $this->em->getRepository('KoiosBlogBackendBundle:Comment');
        $this->blogRepo    = $this->em->getRepository('KoiosBlogBackendBundle:Blog');
    }

    public function testGetCommentsForBlog()
    {
        $unapproved = $this->commentRepo->getCommentsForBlog(1, false);
        $this->assertEquals(count($unapproved), 1);

        $approved = $this->commentRepo->getCommentsForBlog(1, true);
        $this->assertEquals(count($approved), 1);
    }

    public function testGetLatestComments()
    {
        $comments = $this->commentRepo->getLatestComments(2);

        $this->assertEquals(count($comments), 2);

        $this->assertLessThanOrEqual($comments[0]->getCreated(), $comments[1]->getCreated());
    }

    public function testAddAndRetrievalCommentsToBlog()
    {
        backupDatabase();
        $blog = new Blog();

        $comment = new Comment();
        $comment->setUser('test');
        $comment->setComment('PHPUnit test comment');
        $comment->setBlog($blog);

        $this->assertEquals($blog->getComments()[0], $comment);
        restoreDatabase();
    }

    public function testCommentIdRetrieval()
    {
        $comment = $this->commentRepo->find(1);

        $this->assertEquals($comment->getId(), 1);
    }

    /**
     * @expectedException \Doctrine\DBAL\DBALException
     */
    public function testNoNameException()
    {
        $comment = new Comment();
        $this->em->persist($comment);
        $this->em->flush();
    }

    /**
     * @expectedException \Doctrine\DBAL\DBALException
     */
    public function testNoCommentException()
    {
        $comment = new Comment();
        $comment->setUser('tester');
        $this->em->persist($comment);
        $this->em->flush();
    }
}
