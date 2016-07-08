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

namespace Phossa2\Config\Interfaces;

/**
 * WritableInterface
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface WritableInterface
{
    /**
     * Set configuration
     *
     * @param  string $key configuration key
     * @param  mixed values
     * @return $this
     * @throws LogicException if error type is to throw exception
     * @access public
     * @api
     */
    public function set(/*# string */ $key, $value);

    /**
     * Is writable ?
     *
     * @return bool
     * @access public
     * @api
     */
    public function isWritable()/*# : bool */;

    /**
     * Set to false or the writer object
     *
     * @param  mixed|false $writable
     * @return $this
     * @access public
     * @api
     */
    public function setWritable($writable);
}
