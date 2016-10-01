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
 * ConfigInterface
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.7
 * @since   2.0.0 added
 * @since   2.0.7 removed exception in `get()`
 */
interface ConfigInterface
{
    /**
     * Get a configure value. returns $default if not found
     *
     * @param  string $id configuration id/key/name
     * @param  mixed $default default value, if $id not found
     * @return mixed
     * @access public
     * @since  2.0.7 removed exception
     * @api
     */
    public function get(/*# string */ $id, $default = null);

    /**
     * Has a configure by $id ?
     *
     * @param  string $id configuration id/key/name
     * @return bool
     * @access public
     * @api
     */
    public function has(/*# string */ $id)/*# : bool */;
}
