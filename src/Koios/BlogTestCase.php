<?php

namespace Koios;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as WTC;

class BlogTestCase extends WTC
{
    public function tearDown()
    {
        parent::tearDown();
        $this->restoreDatabase();
    }

    public function assertResponseCode($response, $expected_code)
    {
        $this->assertEquals($expected_code, $response->getStatusCode());
    }

    public function restoreDatabase()
    {
        copy(__DIR__ . '/../../app/cache/test/test.db.bk', __DIR__ . '/../../app/cache/test/test.db');
        chmod(__DIR__ . '/../../app/cache/test/test.db', 0777);
    }
}