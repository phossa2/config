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

use Phossa2\Config\Interfaces\ConfigInterface;
use Phossa2\Config\Interfaces\WritableInterface;

/**
 * Implementation of ArrayAccess for ConfigInterface/WritableInterface
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     ConfigInterface
 * @see     WritableInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
trait ArrayAccessTrait
{
    public function offsetExists($offset)/*# : bool */
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->set($offset, null);
    }

    // from ConfigInterface
    abstract public function has(/*# string */ $id)/*# : bool */;
    abstract public function get(/*# string */ $id, $default = null);

    // from WritableInterface
    abstract public function set(/*# string */ $id, $value)/*#: bool */;
}
