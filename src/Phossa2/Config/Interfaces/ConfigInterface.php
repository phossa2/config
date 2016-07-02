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

use Phossa2\Config\Exception\LogicException;

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
     * Get a configure value. returns NULL if not found
     *
     * @param  string $key configuration key
     * @param  mixed default value if not found
     * @return mixed
     * @throws LogicException if error type is to throw exception
     * @access public
     * @api
     */
    public function get(/*# string */ $key, $default = null);

    /**
     * Has a configure by key ?
     *
     * @param  string $key configuration key
     * @return bool
     * @access public
     * @api
     */
    public function has(/*# string */ $key)/*# : bool */;

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
}
