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

namespace Phossa2\Config\Traits;

use Phossa2\Shared\Reference\DelegatorTrait;
use Phossa2\Config\Interfaces\WritableInterface;

/**
 * DelegatorWritableTrait
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     WritableInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
trait DelegatorWritableTrait
{
    use WritableTrait, DelegatorTrait;

    /**
     * Override `isWritable()` in the WritableTrait
     *
     * {@inheritDoc}
     */
    public function isWritable()/*# : bool */
    {
        foreach ($this->lookup_pool as $reg) {
            if ($reg instanceof WritableInterface &&
                $reg->isWritable()
            ) {
                $this->setWritable($reg);
                return true;
            }
        }
        return false;
    }

    /**
     * Override `setWritable()` in the WritableTrait
     *
     * {@inheritDoc}
     */
    public function setWritable($writable)
    {
        // only if is boolean
        if (is_bool($writable)) {
            if ($writable !== $this->isWritable()) {
                $this->setRegistryWritable($writable);
            }

        // set the writable registry
        } else {
            $this->writable = $writable;
        }
        return $this;
    }

    /**
     * Set underlying registries's writability
     *
     * @param  bool $writable
     * @return $this
     * @access protected
     */
    protected function setRegistryWritable(/*# bool */ $writable)
    {
        if (false === $writable) {
            return $this->setRegistryWritableFalse();
        } else {
            return $this->setRegistryWritableTrue();
        }
    }

    /**
     * Set writable to FALSE in all registries
     *
     * @return $this
     * @access protected
     */
    protected function setRegistryWritableFalse()
    {
        foreach ($this->lookup_pool as $reg) {
            if ($reg instanceof WritableInterface) {
                $reg->setWritable(false);
            }
        }
        return $this;
    }

    /**
     * Set writable to TRUE at first matching registry
     *
     * @return $this
     * @access protected
     */
    protected function setRegistryWritableTrue()
    {
        foreach ($this->lookup_pool as $reg) {
            if ($reg instanceof WritableInterface) {
                $reg->setWritable(true);
                return $this;
            }
        }
        return $this;
    }
}
