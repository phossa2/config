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
        $this->config1->setDelegator($this->delegator);

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
        $this->config1->setDelegator($this->delegator);
        $this->config2->setDelegator($this->delegator);

        // resolve to unknown reference
        $this->assertEquals(
            'dbx',
            $this->delegator->get('db.unknown')
        );
    }
}
