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

namespace Phossa2\Config\Message;

use Phossa2\Shared\Message\Message as BaseMessage;

/**
 * Mesage class for Phossa2\Config
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
class Message extends BaseMessage
{
    /*
     * Config key "%s" is not valid
     */
    const CONFIG_KEY_INVALID = 1606221007;

    /*
     * Config root "%s" is not valid
     */
    const CONFIG_ROOT_INVALID = 1606221008;

    /*
     * Config file type "%s" unknown
     */
    const CONFIG_FILE_TYPE_UNKNOWN = 1606221009;

    /**
     * {@inheritDoc}
     */
    protected static $messages = [
        self::CONFIG_KEY_INVALID => 'Config key "%s" is not valid',
        self::CONFIG_ROOT_INVALID => 'Config root "%s" is not valid',
        self::CONFIG_FILE_TYPE_UNKNOWN => 'Config file type "%s" unknown',
    ];
}
