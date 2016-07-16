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
use Phossa2\Config\Traits\ChainingTrait;
use Phossa2\Config\Traits\ArrayAccessTrait;
use Phossa2\Config\Exception\LogicException;
use Phossa2\Config\Interfaces\ConfigInterface;
use Phossa2\Config\Interfaces\WritableInterface;
use Phossa2\Config\Interfaces\ChainingInterface;
use Phossa2\Config\Interfaces\DelegatorInterface;
use Phossa2\Config\Traits\DelegatorWritableTrait;

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
 * @see     ChainingInterface
 * @version 2.0.7
 * @since   2.0.0 added
 * @since   2.0.7 changed DelegatorInterface, added ChainingInterface
 */
class Delegator extends ObjectAbstract implements DelegatorInterface, \ArrayAccess, WritableInterface, ChainingInterface
{
    use ArrayAccessTrait, DelegatorWritableTrait, ChainingTrait;

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
     * {@inheritDoc}
     */
    public function set(/*# string */ $id, $value)
    {
        if ($this->isWritable()) {
            $this->writable->set($id, $value);
            return $this;
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
