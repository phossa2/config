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
 * @version 2.1.0
 * @since   2.0.0 added
 * @since   2.0.7 changed DelegatorAware* stuff
 * @since   2.0.10 using recursive getDelegator
 * @since   2.0.12 changed `set()` return value
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
     * @var    string
     * @access private
     */
    private $cached_id;

    /**
     * @var    mixed
     * @access private
     */
    private $cached_value;

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
        if ($this->has($id)) {
            // cached from has()
            $val = $this->cached_value;

            // dereference
            $this->deReferenceArray($val);

            return null === $val ? $default : $val;
        }
        return $default;
    }

    /**
     * {@inheritDoc}
     */
    public function has(/*# string */ $id)/*# : bool */
    {
        // checked already
        if ($id === $this->cached_id) {
            return null !== $this->cached_value;
        }

        // default result
        $this->cached_id = $id;
        $this->cached_value = null;

        // try get config
        try {
            $this->loadConfig((string) $id);
            $this->cached_value = $this->config->getNode((string) $id);
            return null !== $this->cached_value;
        } catch (\Exception $e) {
            $this->throwError($e->getMessage(), $e->getCode());
            return false;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function set(/*# string */ $id, $value)/*# : bool */
    {
        if ($this->isWritable()) {
            // lazy load, no dereference
            $this->loadConfig((string) $id);

            // replace the node
            $this->cached_id = null;
            $this->config->addNode($id, $value);

            return $this->has($id);
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
        // load super global
        if ('' !== $group && '_' === $group[0]) {
            return $this->loadGlobal($group);
        }

        // load from config
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
     * @throws LogicException if super global unknown
     * @access protected
     */
    protected function loadGlobal(/*# string */ $group)
    {
        if (!isset($GLOBALS[$group])) {
            $this->throwError(
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
     * - returns 'system' from $id 'system.dir.tmp'
     * - '.system.tmpdir' is invalid
     *
     * @param  string $id
     * @return string
     * @access protected
     */
    protected function getGroupName(/*# string */ $id)/*# : string */
    {
        return explode(
            $this->config->getDelimiter(),
            ltrim($id, $this->config->getDelimiter())
        )[0];
    }

    /**
     * Override 'referenceLookup()' in ReferenceTrait.
     *
     * Delegator support goes here
     *
     * @since 2.0.10 using recursive getDelegator
     * {@inheritDoc}
     */
    protected function referenceLookup(/*# string */ $name)
    {
        if ($this->hasDelegator()) {
            // get delegator recursively
            $delegator = $this->getDelegator(true);
            $val = $delegator->get($name);
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
