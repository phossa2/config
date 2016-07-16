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

use Phossa2\Config\Message\Message;

/*
 * Provide zh_CN translation
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
return [
    Message::CONFIG_KEY_INVALID => '配置名称  "%s" 形式错误',
    Message::CONFIG_ROOT_INVALID => '配置根目录 "%s" 错误',
    Message::CONFIG_FILE_TYPE_UNKNOWN => '未知的配置文件格式 "%s"',
    Message::CONFIG_REFERENCE_UNKNOWN => '未知替代变量 "%s"',
    Message::CONFIG_GROUP_UNKNOWN => '未知配置群 "%s"',
    Message::CONFIG_GLOBAL_UNKNOWN => '未知超级全局变量 "%s"',
    Message::CONFIG_ENV_UNKNOWN => '未知环境目录 "%s"',
    Message::CONFIG_NOT_WRITABLE => '配置只可读，写入 "%s"失败',
];
