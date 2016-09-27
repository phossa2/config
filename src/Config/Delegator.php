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

use Phossa2\Config\Message\Message;
use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Config\Traits\ArrayAccessTrait;
use Phossa2\Config\Exception\LogicException;
use Phossa2\Config\Interfaces\ConfigInterface;
use Phossa2\Config\Interfaces\WritableInterface;
use Phossa2\Config\Interfaces\DelegatorInterface;
use Phossa2\Config\Traits\DelegatorWritableTrait;
use Phossa2\Shared\Delegator\DelegatorAwareTrait;
use Phossa2\Shared\Delegator\DelegatorAwareInterface;

/**
 * Delegator
 *
 * Delegator for config
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     DelegatorInterface
 * @see     \ArrayAccess
 * @see     WritableInterface
 * @version 2.0.12
 * @since   2.0.0 added
 * @since   2.0.7 changed DelegatorInterface, added ChainingInterface
 * @since   2.0.10 removed ChainingInterface
 * @since   2.0.12 changed set() return value
 */
class Delegator extends ObjectAbstract implements DelegatorInterface, \ArrayAccess, WritableInterface, DelegatorAwareInterface
{
    use ArrayAccessTrait, DelegatorWritableTrait, DelegatorAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function get(/*# string */ $id, $default = null)
    {
        if ($this->hasInLookup($id)) {
            return $this->getFromLookup($id);
        }
        return $default;
    }

    /**
     * {@inheritDoc}
     */
    public function has(/*# string */ $id)/*# : bool */
    {
        return $this->hasInLookup($id);
    }

    /**
     * {@inheritDoc}
     */
    public function addConfig(ConfigInterface $config)
    {
        return $this->addRegistry($config);
    }

    /**
     * @since 2.0.12 changed return value
     *
     * {@inheritDoc}
     */
    public function set(/*# string */ $id, $value)/*# : bool */
    {
        if ($this->isWritable()) {
            $this->writable->set($id, $value);
            return $this->writable->has($id);
        } else {
            throw new LogicException(
                Message::get(Message::CONFIG_NOT_WRITABLE, $id),
                Message::CONFIG_NOT_WRITABLE
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function hasInRegistry(
        $registry,
        /*# string */ $id
    )/*# : bool */ {
        /* @var $registry ConfigInterface */
        return $registry->has($id);
    }

    /**
     * {@inheritDoc}
     */
    protected function getFromRegistry(
        $registry,
        /*# string */ $id
    ) {
        /* @var $registry ConfigInterface */
        return $registry->get($id);
    }
}
