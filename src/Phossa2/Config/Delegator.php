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

use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Config\Traits\ArrayAccessTrait;
use Phossa2\Shared\Reference\DelegatorTrait;
use Phossa2\Config\Interfaces\ConfigInterface;
use Phossa2\Shared\Reference\DelegatorInterface;

/**
 * Delegator
 *
 * Implmentation of DelegatorInterface
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     DelegatorInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
class Delegator extends ObjectAbstract implements \ArrayAccess, DelegatorInterface, ConfigInterface
{
    use ArrayAccessTrait, DelegatorTrait;

    /**
     * constructor
     *
     * @access public
     */
    public function __construct()
    {
        // at least one dummy config registry
        (new Config())->setDelegator($this);
    }

    /**
     * {@inheritDoc}
     */
    public function get(/*# string */ $key, $default = null)
    {
        if ($this->hasInLookup($key)) {
            return $this->getFromLookup($key);
        }
        return $default;
    }

    /**
     * {@inheritDoc}
     */
    public function has(/*# string */ $key)/*# : bool */
    {
        return $this->hasInLookup($key);
    }

    /**
     * {@inheritDoc}
     */
    public function set(/*# string */ $key, $value)
    {
        // set with the dummy registry
        $reg = $this->lookup_pool[0];
        $reg->set($key, $value);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    protected function isValidRegistry($registry)/*# : bool */
    {
        return $registry instanceof ConfigInterface;
    }

    /**
     * {@inheritDoc}
     */
    protected function hasInRegistry(
        $registry,
        /*# string */ $key
    )/*# : bool */ {
        /* @var $registry ConfigInterface */
        return $registry->has($key);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFromRegistry(
        $registry,
        /*# string */ $key
    ) {
        /* @var $registry ConfigInterface */
        return $registry->get($key);
    }
}
