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

use Phossa2\Config\Interfaces\WritableInterface;

/**
 * Implementation of WritableInterface
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     WritableInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
trait WritableTrait
{
    /**
     * @var    false|mixed
     * @access protected
     */
    protected $writable = false;

    /**
     * {@inheritDoc}
     */
    public function isWritable()/*# : bool */
    {
        return false !== $this->writable;
    }

    /**
     * {@inheritDoc}
     */
    public function setWritable($writable)
    {
        $this->writable = $writable;
        return $this;
    }

    // from WritableInterface
    abstract public function set(/*# string */ $id, $value);
}
