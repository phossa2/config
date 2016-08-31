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

use Phossa2\Shared\Reader\Reader;
use Phossa2\Config\Message\Message;
use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Config\Exception\InvalidArgumentException;

/**
 * ConfigFileLoader
 *
 * Read configs from file.
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.8
 * @since   2.0.0 added
 * @since   2.0.8 updated
 */
class ConfigFileLoader extends ObjectAbstract implements ConfigLoaderInterface
{
    /**
     * Config root directory
     *
     * @var    string
     * @access protected
     */
    protected $root_dir;

    /**
     * config file type
     *
     * @var    string
     * @access protected
     */
    protected $file_type;

    /**
     * cached subdirs to load files
     *
     * @var    array
     * @access protected
     */
    protected $sub_dirs = [];

    /**
     * default environment
     *
     * @var    string
     * @access protected
     */
    protected $environment;

    /**
     * Constructor
     *
     * @param  string $rootDir
     * @param  string $environment
     * @param  string $fileType
     * @throws InvalidArgumentException if any argument invalid
     * @access public
     * @api
     */
    public function __construct(
        /*# string */ $rootDir,
        /*# string */ $environment = '',
        /*# string */ $fileType = 'php'
    ) {
        $this
            ->setRootDir($rootDir)
            ->setFileType($fileType)
            ->setEnvironment($environment);
    }

    /**
     * {@inheritDoc}
     */
    public function load(
        /*# string */ $group,
        /*# string */ $environment = ''
    )/*# : array */ {
        $data = [];
        $env  = $environment ?: $this->environment;

        foreach ($this->globFiles($group, $env) as $file) {
            $grp = basename($file, '.' . $this->file_type);
            if (!isset($data[$grp])) {
                $data[$grp] = [];
            }
            $data[$grp] = array_replace_recursive(
                $data[$grp],
                (array) Reader::readFile($file)
            );
        }
        return $data;
    }

    /**
     * Set config file root directory
     *
     * @param  string $rootDir
     * @return $this
     * @throws InvalidArgumentException if root dir is unknown
     * @access public
     * @api
     */
    public function setRootDir(/*# string */ $rootDir)
    {
        $dir = realpath($rootDir);

        if (false === $dir) {
            throw new InvalidArgumentException(
                Message::get(Message::CONFIG_ROOT_INVALID, $rootDir),
                Message::CONFIG_ROOT_INVALID
            );
        }

        $this->root_dir = $dir . \DIRECTORY_SEPARATOR;

        return $this;
    }

    /**
     * Set config file type
     *
     * @param  string $fileType
     * @return $this
     * @throws InvalidArgumentException if unsupported file type
     * @access public
     * @api
     */
    public function setFileType(/*# string */ $fileType)
    {
        if (!Reader::isSupported($fileType)) {
            throw new InvalidArgumentException(
                Message::get(Message::CONFIG_FILE_TYPE_UNKNOWN, $fileType),
                Message::CONFIG_FILE_TYPE_UNKNOWN
            );
        }

        $this->file_type = $fileType;
        return $this;
    }

    /**
     * Set default environment
     *
     * @param  string $environment
     * @return $this
     * @access public
     * @api
     */
    public function setEnvironment(/*# string */ $environment)
    {
        $this->environment = $environment;
        $this->getSearchDirs($environment);
        return $this;
    }

    /**
     * Returns an array of files to read from
     *
     * @param  string $group
     * @param  string $environment
     * @return array
     * @access protected
     */
    protected function globFiles(
        /*# string */ $group,
        /*# string */ $environment
    )/*# : array */ {
        $files = [];
        $group = '' === $group ? '*' : $group;
        foreach ($this->getSearchDirs($environment) as $dir) {
            $file  = $dir . $group . '.' . $this->file_type;
            $files = array_merge($files, glob($file));
        }
        return $files;
    }

    /**
     * Get the search directories
     *
     * @param  string $env
     * @return array
     * @access protected
     */
    protected function getSearchDirs(/*# string */ $env)/*# : array */
    {
        if (!isset($this->sub_dirs[$env])) {
            $this->sub_dirs[$env] = $this->buildSearchDirs($env);
        }
        return $this->sub_dirs[$env];
    }

    /**
     * Build search directories
     *
     * @param  string $env
     * @return array
     * @access protected
     */
    protected function buildSearchDirs(/*# string */ $env)/*# : array */
    {
        $path = $this->root_dir;
        $part = preg_split(
            '/[\/\\\]/',
            trim($env, '/\\'),
            0,
            \PREG_SPLIT_NO_EMPTY
        );
        $subdirs = [$path];
        foreach ($part as $dir) {
            $path .= $dir . \DIRECTORY_SEPARATOR;
            if (false === file_exists($path)) {
                trigger_error(
                    Message::get(Message::CONFIG_ENV_UNKNOWN, $env),
                    \E_USER_WARNING
                );
                break;
            }
            $subdirs[] = $path;
        }
        return $subdirs;
    }
}
