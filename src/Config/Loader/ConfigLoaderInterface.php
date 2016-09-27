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

namespace Phossa2\Config\Loader;

/**
 * ConfigLoaderInterface
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.8
 * @since   2.0.0 added
 * @since   2.0.8 updated
 */
interface ConfigLoaderInterface
{
    /**
     * Load group configs base on environment.
     *
     * - if $environment == '', use the default environment
     * - if $group == '', load all avaiable groups
     *
     * @param  string $group
     * @param  string $environment
     * @return array
     * @access public
     * @since  2.0.8 default environment to '', removed exception
     * @api
     */
    public function load(
        /*# string */ $group,
        /*# string */ $environment = ''
    )/*# : array */;
}
