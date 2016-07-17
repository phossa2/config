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

use Phossa2\Shared\Delegator\DelegatorTrait;
use Phossa2\Config\Interfaces\WritableInterface;

/**
 * DelegatorWritableTrait
 *
 * Writable for delegator
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     WritableInterface
 * @version 2.0.8
 * @since   2.0.0 added
 * @since   2.0.7 changed to Delegator\DelegatorTrait
 */
trait DelegatorWritableTrait
{
    use WritableTrait, DelegatorTrait;

    /**
     * Override `isWritable()` in the WritableTrait.
     *
     * Delegator's writability is base on its registries
     *
     * {@inheritDoc}
     */
    public function isWritable()/*# : bool */
    {
        foreach ($this->lookup_pool as $reg) {
            if ($reg instanceof WritableInterface && $reg->isWritable()) {
                $this->clearLookupCache();
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
    public function setWritable($writable)/*# : bool */
    {
        if (is_object($writable)) {
            $this->writable = $writable;

        } elseif (false === $writable) {
            return $this->setRegistryWritableFalse();

        } elseif (!$this->isWritable()) {
            return $this->setRegistryWritableTrue();
        }

        return true;
    }

    /**
     * Set writable to FALSE in all registries
     *
     * @return bool
     * @access protected
     */
    protected function setRegistryWritableFalse()/*# : bool */
    {
        $res = true;
        foreach ($this->lookup_pool as $reg) {
            if ($reg instanceof WritableInterface) {
                $res = $reg->setWritable(false);
            }
            if (false === $res) {
                return $res;
            }
        }
        return $res;
    }

    /**
     * Set writable to TRUE at first matching registry
     *
     * @return bool
     * @access protected
     */
    protected function setRegistryWritableTrue()/*# : bool */
    {
        $res = false;
        foreach ($this->lookup_pool as $reg) {
            if ($reg instanceof WritableInterface) {
                $res = $reg->setWritable(true);
            }

            if (true === $res) {
                return $res;
            }
        }
        return $res;
    }
}
