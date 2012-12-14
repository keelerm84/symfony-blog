<?php

namespace Koios\BlogBundle\Tests\Twig\Extensions;

use Koios\BlogBundle\Twig\Extensions\KoiosBlogBundleExtension;

class KoiosBlogBundleExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreatedAgo()
    {
        $blog = new KoiosBlogBundleExtension();

        $this->assertEquals("0 seconds ago", $blog->createdAgo($this->getDateTime(0)->format('Y-m-d H:i:s')));
        $this->assertEquals("34 seconds ago", $blog->createdAgo($this->getDateTime(-34)->format('Y-m-d H:i:s')));
        $this->assertEquals("1 minute ago", $blog->createdAgo($this->getDateTime(-60)->format('Y-m-d H:i:s')));
        $this->assertEquals("2 minutes ago", $blog->createdAgo($this->getDateTime(-120)->format('Y-m-d H:i:s')));
        $this->assertEquals("1 hour ago", $blog->createdAgo($this->getDateTime(-3600)->format('Y-m-d H:i:s')));
        $this->assertEquals("1 hour ago", $blog->createdAgo($this->getDateTime(-3601)->format('Y-m-d H:i:s')));
        $this->assertEquals("2 hours ago", $blog->createdAgo($this->getDateTime(-7200)->format('Y-m-d H:i:s')));

        // Cannot create time in the future
        $this->setExpectedException('\InvalidArgumentException');
        $blog->createdAgo($this->getDateTime(60));
        $blog->createdAgo($this->getDateTime(60)->format('Y-m-d H:i:s'));
    }

    protected function getDateTime($delta)
    {
        return new \DateTime(date("Y-m-d H:i:s", time()+$delta));
    }
}