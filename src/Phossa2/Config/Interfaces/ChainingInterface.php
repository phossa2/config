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

use Phossa2\Shared\Delegator\DelegatorAwareInterface;

/**
 * ChainingInterface
 *
 * Delegator may have its own delegator
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     DelegatorAwareInterface
 * @version 2.0.16
 * @since   2.0.16 added
 */
interface ChainingInterface extends DelegatorAwareInterface
{
    /**
     * If delegator itself has upper-level delegator
     *
     * @param  string $id
     * @return mixed|null
     * @access public
     * @api
     */
    public function delegatedGet(/*# string */ $id);
}
