<?php

use HelloFresh\Stats\StatsD\SilentClient;
use PHPUnit\Framework\TestCase;

class SilentClientTest extends TestCase
{
    public function testNewInstance()
    {
        $client = new SilentClient();
        $this->assertInstanceOf(SilentClient::class, $client);
        $this->assertRegExp('/^StatsD\\\Client::\[[a-zA-Z0-9]+\]$/', (string) $client);
    }

    public function testStaticInstance()
    {
        $client1 = SilentClient::instance('instance1');
        $this->assertInstanceOf(SilentClient::class, $client1);
        $client2 = SilentClient::instance('instance2');
        $client3 = SilentClient::instance('instance1');
        $this->assertEquals('StatsD\Client::[instance2]', (string) $client2);
        $this->assertFalse((string) $client1 === (string) $client2);
        $this->assertTrue((string) $client1 === (string) $client3);
    }
}
