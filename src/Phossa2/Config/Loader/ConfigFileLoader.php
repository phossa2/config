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
use Phossa2\Config\Exception\LogicException;
use Phossa2\Config\Exception\InvalidArgumentException;

/**
 * ConfigFileLoader
 *
 * Read configs from file.
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
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
        return $this
            ->setRootDir($rootDir)
            ->setFileType($fileType)
            ->setEnvironment($environment);
    }

    /**
     * {@inheritDoc}
     */
    public function load(
        /*# string */ $group,
        $environment = null
    )/*# : array */ {
        try {
            $data = [];
            foreach ($this->globFiles($group, $environment) as $file) {
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
        } catch (\Exception $e) {
            throw new LogicException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Set config file root directory
     *
     * @param  string $rootDir
     * @return $this
     * @throws InvalidArgumentException if dir is bad
     * @access public
     * @api
     */
    public function setRootDir(/*# string */ $rootDir)
    {
        $this->root_dir = realpath($rootDir);

        if (false === $this->root_dir) {
            throw new InvalidArgumentException(
                Message::get(Message::CONFIG_ROOT_INVALID, $rootDir),
                Message::CONFIG_ROOT_INVALID
            );
        }

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
        $this->file_type = $fileType;
        return $this;
    }

    /**
     * Set environment
     *
     * @param  string $environment
     * @return $this
     * @access public
     * @api
     */
    public function setEnvironment(/*# string */ $environment)
    {
        $this->sub_dirs = $this->buildSearchDirs($environment);
        return $this;
    }

    /**
     * Returns an array of files to read from
     *
     * @param  string $group
     * @param  null|string $environment
     * @return array
     * @access protected
     */
    protected function globFiles(
        /*# string */ $group,
        $environment
        )/*# : array */ {
            $files = [];
            foreach($this->buildSearchDirs($environment) as $dir) {
                // append trailing '/'
                $dir .= \DIRECTORY_SEPARATOR;

                // group file
                $file = $dir . $group . '.' . $this->file_type;

                // all groups
                if ('' === $group) {
                    $files = array_merge(
                        $files, glob($dir . '*.' . $this->file_type)
                        );

                    // one group
                } elseif (is_file($file)) {
                    $files[] = $file;
                }
            }

            return $files;
    }

    /**
     * Build search directories
     *
     * @param  null|string $environment
     * @return array
     * @access protected
     */
    protected function buildSearchDirs($environment)/*# : array */
    {
        if (null === $environment) {
            return $this->sub_dirs;
        } else {
            $path = $this->root_dir;
            $subdirs = [$path];
            $subs = preg_split('/[\/\\\]/', trim($environment, '/\\'), 0,
                \PREG_SPLIT_NO_EMPTY);

            foreach($subs as $dir) {
                $path = $path . \DIRECTORY_SEPARATOR . $dir;
                $subdirs[] = $path;
            }

            return $subdirs;
        }
    }
}
