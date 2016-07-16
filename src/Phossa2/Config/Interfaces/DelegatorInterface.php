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

use Phossa2\Shared\Delegator\DelegatorInterface as GenericDelegatorInterface;

/**
 * DelegatorInterface
 *
 * Phossa2\Config's delegator interface
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa2\Shared\Delegator\DelegatorInterface
 * @see     ConfigInterface
 * @version 2.0.7
 * @since   2.0.0 added
 * @since   2.0.7 changed GenericDelegatorInterface
 */
interface DelegatorInterface extends GenericDelegatorInterface, ConfigInterface
{
    /**
     * Add one config registry to the delegator
     *
     * @param  ConfigInterface $config
     * @return $this
     * @access public
     * @api
     */
    public function addConfig(ConfigInterface $config);
}
