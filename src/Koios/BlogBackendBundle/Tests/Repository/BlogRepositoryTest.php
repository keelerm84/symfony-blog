<?php

namespace Koios\BlogBackendBundle\Tests\Repository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogRepositoryTest extends WebTestCase
{
    protected $repo = null;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->repo = $kernel->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('KoiosBlogBackendBundle:Blog');
    }

    public function testGetLastestBlogs()
    {
        $blogs = $this->repo->getLatestBlogs(2);

        $this->assertTrue(count($blogs) == 2);

        $this->assertLessThanOrEqual($blogs[0]->getCreated(), $blogs[1]->getCreated());
    }

    public function testGetTags()
    {
        $tags = $this->repo->getTags();

        $expected_tags = array('symfony2', 'php', 'misdirection', '!trusting');

        $this->assertTrue(count($expected_tags) == count(array_intersect($tags, $expected_tags)));
    }

    /**
     * @dataProvider tagWeightProvider
     */
    public function testTagWeights($tags, $tagWeights)
    {
        $weights = $this->repo->getTagWeights($tags);

        foreach($weights as $tag => $weight) {
            $this->assertEquals($tagWeights[$tag], $weight);
        }
    }

    public function tagWeightProvider()
    {
        return array(
            array(array('php'), array('php' => 1)),
            array(array('symfony2', 'symfony2', 'symfony2', 'symfony2', 'symfony2', 'symfony2', 'php'), array('symfony2' => 5, 'php' => 1))
        );
    }

    public function testNoTagWeight()
    {
        $this->assertTrue(0 === count($this->repo->getTagWeights(array())));
    }

    public function testBlogIdRetrieval()
    {
        $blog = $this->repo->find(1);

        $this->assertEquals($blog->getId(), 1);
    }

    public function testBlogToString()
    {
        $blog = $this->repo->find(1);
        $this->assertEquals('A day with Symfony2', $blog);
    }
}
