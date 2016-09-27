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

use Phossa2\Shared\Base\ObjectAbstract;

/**
 * DummyLoader
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.8
 * @since   2.0.0 added
 */
class DummyLoader extends ObjectAbstract implements ConfigLoaderInterface
{
    /**
     * Always returns empty array
     *
     * {@inheritDoc}
     */
    public function load(
        /*# string */ $group,
        /*# string */ $environment = ''
    )/*# : array */ {
        return [];
    }
}
