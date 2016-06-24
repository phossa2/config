<?php

namespace Phossa2\Config;

use Phossa2\Config\Loader\ConfigFileLoader;

/**
 * Config test case.
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    private $object;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->object = new Config(
            new ConfigFileLoader(__DIR__.'/testData/')
        );
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->object = null;
        parent::tearDown();
    }

    /**
     * Test root level config
     *
     * @covers Phossa2\Config\Reference\Config::get()
     */
    public function testGet1()
    {
        $this->assertEquals(
            'www',
            $this->object->get('db.auth.user')
        );
        $this->assertEquals(
            'localhost',
            $this->object->get('db.auth.host')
        );
        $this->assertEquals(
            3306,
            $this->object->get('db.auth.port')
        );

        $this->assertEquals(
            'warning',
            $this->object->get('logger.watchdog.level')
        );
    }

    /**
     * Test root/production level config
     *
     * @covers Phossa2\Config\Reference\Config::get()
     */
    public function testGet2()
    {
        $this->object = new Config(
            new ConfigFileLoader(__DIR__.'/testData', 'production')
        );

        $this->assertEquals(
            'www',
            $this->object->get('db.auth.user')
        );
        $this->assertEquals(
            'dbhost',
            $this->object->get('db.auth.host')
        );
        $this->assertEquals(
            3506,
            $this->object->get('db.auth.port')
        );

        $this->assertEquals(
            'warning',
            $this->object->get('logger.watchdog.level')
        );
        $this->assertEquals(
            'Prod1',
            $this->object->get('logger.prod1.channel')
        );
    }

    /**
     * Test root/production/host1 level config
     *
     * @covers Phossa2\Config\Reference\Config::get()
     */
    public function testGet3()
    {
        $this->object = new Config(
            new ConfigFileLoader(__DIR__.'/testData', 'production/host1')
        );

        $this->assertEquals(
            'bingo',
            $this->object->get('db.auth.user')
        );

        $this->assertEquals(
            'nopass',
            $this->object->get('db.auth.pass')
        );

        $this->assertEquals(
            'dbhost',
            $this->object->get('db.auth.host')
        );

        $this->assertEquals(
            3506,
            $this->object->get('db.auth.port')
        );
    }

    /**
     * @covers Phossa2\Config\Reference\Config::set()
     */
    public function testSet()
    {
    }

    /**
     * @covers Phossa2\Config\Reference\Config::has()
     */
    public function testHas()
    {
    }
}
