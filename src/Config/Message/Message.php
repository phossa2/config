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

    /*
     * Config reference "%s" unknown
     */
    const CONFIG_REFERENCE_UNKNOWN = 1606221010;

    /*
     * Config group "%s" unknown
     */
    const CONFIG_GROUP_UNKNOWN = 1606221011;

    /*
     * Uknown super global "%s"
     */
    const CONFIG_GLOBAL_UNKNOWN = 1606221012;

    /*
     * Unknown environment "%s"
     */
    const CONFIG_ENV_UNKNOWN = 1606221013;

    /*
     * Config is not writable for "%s"
     */
    const CONFIG_NOT_WRITABLE = 1606221014;

    /**
     * {@inheritDoc}
     */
    protected static $messages = [
        self::CONFIG_KEY_INVALID => 'Config key "%s" is not valid',
        self::CONFIG_ROOT_INVALID => 'Config root "%s" is not valid',
        self::CONFIG_FILE_TYPE_UNKNOWN => 'Config file type "%s" unknown',
        self::CONFIG_REFERENCE_UNKNOWN => 'Config reference "%s" unknown',
        self::CONFIG_GROUP_UNKNOWN => 'Config group "%s" unknown',
        self::CONFIG_GLOBAL_UNKNOWN => 'Uknown super global "%s"',
        self::CONFIG_ENV_UNKNOWN => 'Unknown environment "%s"',
        self::CONFIG_NOT_WRITABLE => 'Config is not writable for "%s"',
    ];
}
