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

use Phossa2\Config\Exception\InvalidArgumentException;

/**
 * ConfigInterface
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface ConfigInterface
{
    /**
     * Get a configure value
     *
     * @param  string $key configuration key
     * @param  mixed default value if any
     * @return mixed
     * @throws InvalidArgumentException if $key not a string
     * @access public
     * @api
     */
    public function get(/*# string */ $key, $default = null);

    /**
     * Set configuration
     *
     * @param  string $key configuration key
     * @param  mixed values
     * @return $this
     * @throws InvalidArgumentException if $key not a string
     * @access public
     * @api
     */
    public function set(/*# string */ $key, $value);

    /**
     * Has a configure by key ?
     *
     * @param  string $key configuration key
     * @return bool
     * @throws InvalidArgumentException if $key not a string
     * @access public
     * @api
     */
    public function has(/*# string */ $key)/*# : bool */;

    /**
     * Delete a configure by key
     *
     * @param  string $key configuration key
     * @return $this
     * @throws InvalidArgumentException if $key not a string
     * @access public
     * @api
     */
    public function del(/*# string */ $key);
}
