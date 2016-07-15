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

namespace Phossa2\Config\Traits;

use Phossa2\Config\Interfaces\ChainingInterface;
use Phossa2\Shared\Delegator\DelegatorAwareTrait;

/**
 * ChainingTrait
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @see     ChainingInterface
 * @version 2.0.7
 * @since   2.0.7 added
 */
trait ChainingTrait
{
    use DelegatorAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function delegatedGet(/*# string */ $id)
    {
        if ($this->hasDelegator()) {
            $delegator = $this->getDelegator();
            if ($delegator instanceof ChainingInterface) {
                return $delegator->delegatedGet($id);
            } else {
                return $delegator->get($id);
            }
        } else {
            return $this->get($id);
        }
    }

    // from ConfigInterface
    abstract public function get(/*# string */ $id, $default = null);
}
