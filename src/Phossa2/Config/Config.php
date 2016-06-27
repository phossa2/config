<?php
/**
 * Phossa Project
 *
 * PHP version 5.4
 *
 * @category  Library
 * @package   Phossa2\Config
 * @copyright Copyright (c) 2016 phossa.com
 * @license   http://mit-license.org/ MIT License
 * @link      http://www.phossa.com/
 */
/*# declare(strict_types=1); */

namespace Phossa2\Config;

use Phossa2\Shared\Tree\Tree;
use Phossa2\Config\Message\Message;
use Phossa2\Shared\Tree\TreeInterface;
use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Shared\Reference\ReferenceTrait;
use Phossa2\Config\Exception\LogicException;
use Phossa2\Config\Loader\ConfigLoaderInterface;
use Phossa2\Shared\Reference\ReferenceInterface;

/**
 * Config
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
class Config extends ObjectAbstract implements ConfigInterface, ReferenceInterface
{
    use ReferenceTrait;

    /**
     * error type
     *
     * @var    int
     */
    const ERROR_IGNORE    = 0;
    const ERROR_WARNING   = 1;
    const ERROR_EXCEPTION = 2;

    /**
     * the config loader
     *
     * @var    ConfigLoaderInterface
     * @access protected
     */
    protected $loader;

    /**
     * the config tree
     *
     * @var    TreeInterface
     * @access protected
     */
    protected $config;

    /**
     * cache loaded group names
     *
     * @var    array
     * @access protected
     */
    protected $loaded = [];

    /**
     * How to dealing with error, ignore/trigger_error/exception etc.
     *
     * @var    int
     * @access protected
     */
    protected $error_type;

    /**
     * Constructor
     *
     * @param  ConfigLoaderInterface $loader
     * @param  TreeInterface $configTree
     * @param  int $errorType
     * @access public
     * @api
     */
    public function __construct(
        ConfigLoaderInterface $loader,
        TreeInterface $configTree = null,
        /*# int */ $errorType = self::ERROR_WARNING
    ) {
        // the config loader
        $this->loader = $loader;

        // the config tree
        if (null === $configTree) {
            $this->config = new Tree();
        }

        // set error type
        $this->error_type = $errorType;
    }

    /**
     * {@inheritDoc}
     */
    public function get(/*# string */ $key, $default = null)
    {
        try {
            // lazy load
            $this->loadConfig((string) $key);

            //  get value
            $val = $this->config->getNode($key);

            // dereference
            $this->deReferenceArray($val);

            return null === $val ? $default : $val;

        // if dereference exception catched
        } catch (\Exception $e) {
            $this->setError($e->getMessage(), $e->getCode());
            return $default;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set(/*# string */ $key, $value)
    {
        // clear reference cache
        $this->clearReferenceCache();

        // lazy load, no dereference
        $this->loadConfig((string) $key);

        // replace the node
        $this->config->addNode($key, $value);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function has(/*# string */ $key)/*# : bool */
    {
        // update error type
        $err = $this->error_type;
        $this->error_type = self::ERROR_IGNORE;

        // check $key
        $result = null !== $this->get((string) $key);

        // restore error type
        $this->error_type = $err;

        return $result;
    }

    /**
     * Load config
     *
     * @param  string $key
     * @return $this
     * @throws LogicException if current $error_type is to throw exception
     * @access protected
     */
    protected function loadConfig(/*# string */ $key)
    {
        // get group name
        $group = $this->getGroupName($key);

        // check cache
        if (isset($this->loaded[$group])) {
            return $this;
        }

        try {
            // mark as loaded
            $this->loaded[$group] = true;

            // loading the group
            return $this->loadByGroup($group);

        } catch (\Exception $e) {
            return $this->setError(
                Message::get(Message::CONFIG_GROUP_UNKNOWN, $group) .
                    $e->getMessage(),
                Message::CONFIG_GROUP_UNKNOWN
            );
        }
    }

    /**
     * Load one group config, force loading all groups if $group == ''
     *
     * @param  string $group
     * @return $this
     * @throws \Exception group loading issues
     * @access protected
     */
    protected function loadByGroup(/*# string */ $group)
    {
        // if super global
        if (substr($group, 0, 1) === '_') {
            return $this->loadGlobal($group);
        }

        // load from config file
        $conf = $this->loader->load($group);

        foreach ($conf as $grp => $data) {
            $this->config->addNode($grp, $data);
        }

        return $this;
    }

    /**
     * Load super globals
     *
     * @param  string $group
     * @return $this
     * @throws LogicException if global unknown
     * @access protected
     */
    protected function loadGlobal(/*# string */ $group)
    {
        if (!isset($GLOBALS[$group])) {
            throw new LogicException(
                Message::get(Message::CONFIG_GLOBAL_UNKNOWN, $group),
                Message::CONFIG_GLOBAL_UNKNOWN
            );
        }

        // load super global
        $this->config->addNode($group, $GLOBALS[$group]);

        return $this;
    }

    /**
     * Get group name
     *
     * @param  string $key
     * @return string
     * @access protected
     */
    protected function getGroupName(/*# string */ $key)/*# : string */
    {
        // first field of the $key
        return explode($this->config->getDelimiter(), $key)[0];
    }

    /**
     * throw exception if current $error_type is to throw exception
     *
     * {@inheritDoc}
     */
    protected function resolveUnknown(/*# string */ $name)
    {
        // try load & dereference etc.
        $val = $this->get($name);

        // warn if reference unknown
        if (null === $val) {
            $this->setError(
                Message::get(Message::CONFIG_REFERENCE_UNKNOWN, $name),
                Message::CONFIG_REFERENCE_UNKNOWN
            );
        }

        return $val;
    }

    /**
     * {@inheritDoc}
     */
    protected function getReference(/*# string */ $name)
    {
        return $this->config->getNode($name);
    }

    /**
     * Dealing errors
     *
     * @param  string $message
     * @param  int $code
     * @return $this
     * @throws LogicException if current $error_type is to throw exception
     * @access protected
     */
    protected function setError(/*# string */ $message, /*# int */ $code)
    {
        switch ($this->error_type) {
            case self::ERROR_WARNING:
                trigger_error($message, \E_USER_WARNING);
                break;
            case self::ERROR_EXCEPTION:
                throw new LogicException($message, $code);
            default:
                break;
        }
        return $this;
    }
}
