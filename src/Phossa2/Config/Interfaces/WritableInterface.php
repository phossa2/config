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
 * WritableInterface
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.8
 * @since   2.0.0 added
 */
interface WritableInterface
{
    /**
     * Set $id with value
     *
     * @param  string $id id/key/name
     * @param  mixed value
     * @return $this
     * @throws LogicException if not writable
     * @access public
     * @api
     */
    public function set(/*# string */ $id, $value);

    /**
     * Is writable ?
     *
     * @return bool
     * @access public
     * @api
     */
    public function isWritable()/*# : bool */;

    /**
     * Set to true, false or the writer object
     *
     * @param  mixed|bool $writable
     * @return bool true for success, false for failure
     * @access public
     * @api
     */
    public function setWritable($writable)/*# : bool */;
}
