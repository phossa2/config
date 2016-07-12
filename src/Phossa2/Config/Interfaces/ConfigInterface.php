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
     * @param  string $id configuration id/key/name
     * @param  mixed default value if $id not found
     * @return mixed
     * @throws LogicException if error type is to throw exception
     * @access public
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
