<?php
namespace Phossa2\Config\Loader;

/**
 * ConfigFileLoader test case.
 */
class ConfigFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var    ConfigFileLoader
     * @access private
     */
    protected $loader;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->loader = new ConfigFileLoader(__DIR__ . '/conf');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->loader = null;
        parent::tearDown();
    }

    /**
     * getPrivateProperty
     *
     * @param 	string $propertyName
     * @return	the property
     */
    public function getPrivateProperty($propertyName, $object) {
        $reflector = new \ReflectionClass($object);
        $property  = $reflector->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * @covers Phossa2\Config\Loader\ConfigFileLoader::setFileType()
     */
    public function testSetFileType()
    {
        $loader = $this->loader;
        $loader->setFileType('ini');

        $this->assertEquals(__DIR__ . \DIRECTORY_SEPARATOR . 'conf',
            $this->getPrivateProperty('root_dir', $this->loader)
        );

        $this->assertEquals('ini',
            $this->getPrivateProperty('file_type', $this->loader)
        );
    }

    /**
     * if no such root dir
     *
     * @covers Phossa2\Config\Loader\ConfigFileLoader::setRootDir()
     * @expectedException Phossa2\Config\Exception\InvalidArgumentException
     * @expectedExceptionCode Phossa2\Config\Message\Message::CONFIG_ROOT_INVALID
     */
    public function testSetRootDir()
    {
        $loader = $this->loader;
        $loader->setRootDir('/not_such_dir');
    }

    /**
     * load group 'config_good' in root dir
     *
     * @covers Phossa2\Config\Loader\ConfigFileLoader::load()
     */
    public function testLoad1()
    {
        $this->assertEquals(
            ['config_good' => ['test' => 'wow', 'bingo' => 'xxx']],
            $this->loader->load('config_good')
        );
    }

    /**
     * load group 'config_good', env 'production'
     *
     * @covers Phossa2\Config\Loader\ConfigFileLoader::load()
     */
    public function testLoad2()
    {
        $this->assertEquals(
            [ 'config_good' => ['test' => 'prod',  'bingo' => 'xxx']],
            $this->loader->load('config_good', 'production')
        );
    }

    /**
     * load group 'config_good', env 'production/host1'
     *
     * @covers Phossa2\Config\Loader\ConfigFileLoader::load()
     */
    public function testLoad3()
    {
        $this->assertEquals(
            ['config_good' => ['test' => 'prod',  'bingo' => 'yyy']],
            $this->loader->load('config_good', 'production\\host1')
        );
    }

    /**
     * load all
     *
     * @covers Phossa2\Config\Loader\ConfigFileLoader::load()
     */
    public function testLoad4()
    {
        $loader = $this->loader;
        $loader->setRootDir(__DIR__ . '/conf/production');

        $this->assertEquals(
            [
                'all' =>['all' => 'all'],
                'config_good' => ['test' => 'prod', 'bingo' => 'yyy']
            ],
            $loader->load('', 'host1')
        );
    }

    /**
     * load other type, json
     *
     * @covers Phossa2\Config\Loader\ConfigFileLoader::load()
     */
    public function testLoad5()
    {
        $loader = $this->loader;
        $loader->setFileType('json');

        $this->assertEquals(
            [ 'config_good' => ['test' => 'json']],
            $loader->load('config_good')
        );
    }
}
