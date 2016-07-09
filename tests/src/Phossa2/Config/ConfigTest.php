<?php

namespace Phossa2\Config;

use Phossa2\Config\Loader\ConfigFileLoader;
use Phossa2\Shared\Tree\Tree;

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
     * Test constructor
     *
     * @covers Phossa2\Config\Reference\Config::__construct
     */
    public function testConstructor()
    {
        $data = [
            'system.id' => 'phossa',
        ];

        // import data directly
        $config1 = new Config(null, null, $data);
        $this->assertEquals(
            'phossa',
            $config1->get('system.id')
        );

        // import Tree directly
        $config2 = new Config(null, new Tree($data));
        $this->assertEquals(
            'phossa',
            $config2->get('system.id')
        );
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
     * Exception test
     *
     * @covers Phossa2\Config\Reference\Config::get()
     * @expectedException Phossa2\Config\Exception\LogicException
     * @expectedExceptionCode Phossa2\Config\Message\Message::CONFIG_REFERENCE_UNKNOWN
     */
    public function testGet4()
    {
        $this->object = new Config(
            new ConfigFileLoader(__DIR__.'/testData', 'production/host1')
        );
        $this->object->setErrorType(Config::ERROR_EXCEPTION);

        // resolve to unknown reference
        $this->assertEquals(
            '${dbx.unknown}',
            $this->object->get('db.unknown')
        );
    }

    /**
     * @covers Phossa2\Config\Reference\Config::set()
     */
    public function testSet()
    {
        $this->assertFalse($this->object->has('bingo.wow'));
        $this->object->setWritable(true);

        $this->object->set('bingo.wow', 1);

        $this->assertTrue($this->object->has('bingo.wow'));
    }

    /**
     * @covers Phossa2\Config\Reference\Config::has()
     */
    public function testHas()
    {
        // has
        $this->assertTrue($this->object->has('db.auth.port'));

        // no
        $this->assertFalse($this->object->has('bingo.wow'));
    }

    /**
     * Test array access
     *
     * @covers Phossa2\Config\Reference\Config::offsetExists()
     * @covers Phossa2\Config\Reference\Config::offsetGet()
     * @covers Phossa2\Config\Reference\Config::offsetSet()
     * @covers Phossa2\Config\Reference\Config::offsetUnset()
     */
    public function testArrayAccess()
    {
        $this->object->setWritable(true);

        // offsetExists
        $this->assertTrue(isset($this->object['db.auth.port']));

        // offsetGet
        $this->assertTrue(3306 === $this->object['db.auth.port']);

        // offsetSet
        $this->object['db.auth.port'] = 3307;
        $this->assertTrue(3307 === $this->object['db.auth.port']);

        // offsetUnset
        unset($this->object['db.auth.port']);
        $this->assertFalse(isset($this->object['db.auth.port']));
    }
}
