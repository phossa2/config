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
use Phossa2\Config\Loader\DummyLoader;
use Phossa2\Shared\Tree\TreeInterface;
use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Config\Traits\WritableTrait;
use Phossa2\Config\Traits\ArrayAccessTrait;
use Phossa2\Shared\Reference\ReferenceTrait;
use Phossa2\Config\Exception\LogicException;
use Phossa2\Config\Interfaces\ConfigInterface;
use Phossa2\Config\Loader\ConfigLoaderInterface;
use Phossa2\Shared\Reference\ReferenceInterface;
use Phossa2\Config\Interfaces\WritableInterface;
use Phossa2\Config\Interfaces\ChainingInterface;
use Phossa2\Shared\Delegator\DelegatorAwareTrait;
use Phossa2\Shared\Delegator\DelegatorAwareInterface;

/**
 * Config
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     ConfigInterface
 * @see     WritableInterface
 * @see     \ArrayAccess
 * @see     ReferenceInterface
 * @see     DelegatorAwareInterface
 * @version 2.0.7
 * @since   2.0.0 added
 * @since   2.0.7 changed DelegatorAware* stuff
 */
class Config extends ObjectAbstract implements ConfigInterface, WritableInterface, \ArrayAccess, ReferenceInterface, DelegatorAwareInterface
{
    use ReferenceTrait, DelegatorAwareTrait, ArrayAccessTrait, WritableTrait;

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
    protected $error_type = self::ERROR_WARNING;

    /**
     * Constructor
     *
     * @param  ConfigLoaderInterface $loader config loader if any
     * @param  TreeInterface $configTree config tree if any
     * @param  array $configData config data for the tree
     * @access public
     * @api
     */
    public function __construct(
        ConfigLoaderInterface $loader = null,
        TreeInterface $configTree = null,
        array $configData = []
    ) {
        $this->loader = $loader ?: new DummyLoader();
        $this->config = $configTree ?: new Tree($configData);
    }

    /**
     * {@inheritDoc}
     */
    public function get(/*# string */ $id, $default = null)
    {
        try {
            // lazy load
            $this->loadConfig((string) $id);

            //  get value
            $val = $this->config->getNode((string) $id);

            // dereference
            $this->deReferenceArray($val);

            return null === $val ? $default : $val;

        // if dereference exception catched
        } catch (\Exception $e) {
            $this->throwError($e->getMessage(), $e->getCode());
            return $default;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function has(/*# string */ $id)/*# : bool */
    {
        try {
            // update error type
            $err = $this->error_type;
            $this->setErrorType(self::ERROR_IGNORE);

            // lazy load
            $this->loadConfig((string) $id);

            //  get value
            $result = null !== $this->config->getNode((string) $id);

            // restore error type
            $this->setErrorType($err);

            return $result;

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set(/*# string */ $id, $value)
    {
        if ($this->isWritable()) {
            // lazy load, no dereference
            $this->loadConfig((string) $id);

            // replace the node
            $this->config->addNode($id, $value);

            return $this;
        } else {
            $this->throwError(
                Message::get(Message::CONFIG_NOT_WRITABLE),
                Message::CONFIG_NOT_WRITABLE
            );
        }
    }

    /**
     * Set error type
     *
     * @param  int $type
     * @return $this
     * @access public
     * @api
     */
    public function setErrorType(/*# int */ $type)
    {
        $this->error_type = (int) $type;
        return $this;
    }

    /**
     * Load config
     *
     * @param  string $id
     * @return $this
     * @throws LogicException if current $error_type is to throw exception
     * @access protected
     */
    protected function loadConfig(/*# string */ $id)
    {
        // get group name
        $group = $this->getGroupName($id);

        // $group loaded ?
        if (isset($this->loaded[$group])) {
            return $this;
        }

        // mark as loaded
        $this->loaded[$group] = true;

        // loading the group
        return $this->loadByGroup($group);
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
     * @param  string $id
     * @return string
     * @access protected
     */
    protected function getGroupName(/*# string */ $id)/*# : string */
    {
        // first field of the $id
        return explode($this->config->getDelimiter(), $id)[0];
    }

    /**
     * Override 'referenceLookup()' in ReferenceTrait.
     *
     * Delegator support goes here
     *
     * {@inheritDoc}
     */
    protected function referenceLookup(/*# string */ $name)
    {
        if ($this->hasDelegator()) {
            $delegator =  $this->getDelegator();
            if ($delegator instanceof ChainingInterface) {
                $val = $delegator->delegatedGet($name);
            } else {
                $val = $delegator->get($name);
            }
        } else {
            $val = $this->getReference($name);
        }
        return $val;
    }

    /**
     * throw exception if current $error_type is to throw exception
     *
     * {@inheritDoc}
     */
    protected function resolveUnknown(/*# string */ $name)
    {
        // warn if reference unknown
        $this->throwError(
            Message::get(Message::CONFIG_REFERENCE_UNKNOWN, $name),
            Message::CONFIG_REFERENCE_UNKNOWN
        );

        return null;
    }

    /**
     * {@inheritDoc}
     */
    protected function getReference(/*# string */ $name)
    {
        return $this->get($name);
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
    protected function throwError(/*# string */ $message, /*# int */ $code)
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
