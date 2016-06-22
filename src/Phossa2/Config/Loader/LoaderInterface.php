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

/**
 * ConfigLoaderInterface
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface ConfigLoaderInterface
{
    /**
     * Load group configs base on environment.
     *
     * Load all avaiable groups if $group is ''.
     *
     * @param  string $group
     * @param  null|string $environment
     * @return array
     * @throws LogicException if something goes wrong
     * @access public
     * @api
     */
    public function load(
        /*# string */ $group,
        $environment = null
    )/*# : array */;
}
