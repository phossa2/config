<?php

namespace Phossa2\Config;

use Phossa2\Config\Loader\ConfigFileLoader;

/**
 * Delegator test case.
 */
class DelegatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Delegator
     */
    private $delegator;

    /**
     * @var Config
     */
    private $config1;

    /**
     * @var Config
     */
    private $config2;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
        $this->delegator = new Delegator();

        $this->config1 = new Config(
            new ConfigFileLoader(__DIR__.'/testData', '')
        );
        $this->config1->setErrorType(Config::ERROR_EXCEPTION);

        $this->config2 = new Config(
            new ConfigFileLoader(__DIR__.'/testData/production/host2', '')
        );
        $this->config2->setErrorType(Config::ERROR_EXCEPTION);
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
     * Unknown reference found
     *
     * @covers Phossa2\Config\Reference\Delegator::get()
     * @expectedException Phossa2\Config\Exception\LogicException
     * @expectedExceptionCode Phossa2\Config\Message\Message::CONFIG_REFERENCE_UNKNOWN
     */
    public function testGet1()
    {
        $this->delegator->addConfig($this->config1);

        // resolve to unknown reference
        $this->assertEquals(
            '${dbx.unknown}',
            $this->delegator->get('db.unknown')
        );
    }

    /**
     * Unkown reference resolved
     *
     * @covers Phossa2\Config\Reference\Delegator::get()
     */
    public function testGet2()
    {
        // 2 configs in the delegator
        $this->delegator->addConfig($this->config1);
        $this->delegator->addConfig($this->config2);

        // resolve to unknown reference
        $this->assertEquals(
            'dbx',
            $this->delegator->get('db.unknown')
        );
    }

    /**
     * Unkown reference resolved
     *
     * @covers Phossa2\Config\Reference\Delegator::get()
     */
    public function testGet3()
    {
        // both writable now
        $config1 = (new Config())->setWritable(true);
        $config2 = (new Config())->setWritable(true);

        $config1->setErrorType(Config::ERROR_IGNORE);
        $config2->setErrorType(Config::ERROR_IGNORE);

        $delegator = new Delegator();

        $config1['db.user'] = '${system.user}';
        $config2['system.user'] = 'root';

        // reference unresolved
        $this->assertEquals('${system.user}', $config1['db.user']);

        $delegator->addConfig($config1);
        $delegator->addConfig($config2);

        // reference resolved
        $this->assertEquals('root', $config1['db.user']);

        // delegator
        $this->assertEquals('root', $delegator['db.user']);

        // not in config2
        $this->assertEquals(null, $config2['db.user']);

        // overwrite the first config
        $delegator['db.user'] = 'test';
        $this->assertEquals('test', $delegator['db.user']);
        $this->assertEquals('test', $config1['db.user']);

        // diable writable for first config
        $config1->setWritable(false);

        // new key added
        $delegator['db.new'] = 'new';
        $this->assertEquals('new', $delegator['db.new']);
        $this->assertEquals('new', $config2['db.new']);
    }

    /**
     * Test delegator set writable
     *
     * @covers Phossa2\Config\Reference\Delegator::isWritable()
     * @covers Phossa2\Config\Reference\Delegator::setWritable()
     */
    public function testSetWritable()
    {
        // both readonly
        $config1 = (new Config())->setWritable(false);
        $config2 = (new Config())->setWritable(false);

        $delegator = new Delegator();
        $delegator->addConfig($config1);
        $delegator->addConfig($config2);

        // delegator is readonly
        $this->assertFalse($delegator->isWritable());

        // set writable
        $delegator->setWritable(true);
        $this->assertTrue($delegator->isWritable());
        $this->assertTrue($config1->isWritable());
        $this->assertFalse($config2->isWritable());

        // set non writable
        $delegator->setWritable(false);
        $this->assertFalse($delegator->isWritable());
        $this->assertFalse($config1->isWritable());
        $this->assertFalse($config2->isWritable());
    }

    /**
     * Delegator chaining & writable
     *
     * @covers Phossa2\Config\Reference\Delegator::delegatedGet()
     */
    public function testDelegatedGet()
    {
        $config1 = new Config();
        $config2 = new Config();

        // ignore errors
        $config1->setErrorType(Config::ERROR_IGNORE);
        $config2->setErrorType(Config::ERROR_IGNORE);

        $delegator1 = new Delegator();
        $delegator2 = new Delegator();

        $config1['db.user'] = '${system.user}';
        $config2['system.user'] = 'root';

        // reference unresolved
        $this->assertEquals('${system.user}', $config1['db.user']);

        // delegation tree
        $delegator1->addConfig($config1);
        $delegator2->addConfig($delegator1);
        $delegator2->addConfig($config2);

        $this->assertEquals('root', $config1['db.user']);
        $this->assertEquals('root', $delegator2['db.user']);

        // set readonly
        $config1->setWritable(false);
        $config2->setWritable(false);

        $this->assertFalse($delegator2->isWritable());
        $delegator2->setWritable(true);
        $this->assertTrue($config1->isWritable());
        $this->assertFalse($config2->isWritable());
    }
}
