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
use Phossa2\Shared\Base\ObjectAbstract;

/**
 * CachedConfigLoader
 *
 * Load config from one cache file (serialized)
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.8
 * @since   2.0.8 added
 */
class CachedConfigLoader extends ObjectAbstract implements ConfigLoaderInterface
{
    /**
     * the full path of cache file
     *
     * @var    string
     * @access protected
     */
    protected $cache_file;

    /**
     * loaded flag
     *
     * @var    bool
     * @access protected
     */
    protected $loaded = false;

    /**
     * Constructor
     *
     * @param  string $cacheFile
     * @throws NotFoundException if cache file not found
     * @access public
     * @api
     */
    public function __construct(/*# string */ $cacheFile)
    {
        $this->cache_file = $cacheFile;
    }

    /**
     * {@inheritDoc}
     */
    public function load(
        /*# string */ $group,
        /*# string */ $environment = ''
    )/*# : array */ {
        $data = [];
        if (!$this->loaded) {
            $this->loaded = true;
            $data = (array) Reader::readFile($this->cache_file, 'serialized');
        }
        return $data;
    }
}
