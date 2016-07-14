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

use Phossa2\Config\Interfaces\ConfigInterface;
use Phossa2\Config\Interfaces\WritableInterface;
use Phossa2\Shared\Reference\DelegatorInterface as GenericDelegatorInterface;

/**
 * DelegatorInterface
 *
 * Own delegator interface
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa2\Shared\Reference\DelegatorInterface
 * @see     ConfigInterface
 * @see     WritableInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface DelegatorInterface extends GenericDelegatorInterface, ConfigInterface, WritableInterface
{
    /**
     * Add config to the delegator
     *
     * Alias of `addRegistry()`
     *
     * @param  ConfigInterface $config
     * @return $this
     * @access public
     * @api
     */
    public function addConfig(ConfigInterface $config);
}
