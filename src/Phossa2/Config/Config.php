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

namespace Phossa2\Config;

use Phossa2\Shared\Tree\Tree;
use Phossa2\Config\Message\Message;
use Phossa2\Shared\Reference\ReferenceTrait;
use Phossa2\Config\Exception\LogicException;
use Phossa2\Config\Loader\ConfigLoaderInterface;

/**
 * Config
 *
 * @package Phossa2\Config
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
class Config implements ConfigInterface
{
    use ReferenceTrait;

    /**
     * the config loader
     *
     * @var    ConfigLoaderInterface
     * @access protected
     */
    protected $loader;

    /**
     * the config tree
     *
     * @var    Tree
     * @access protected
     */
    protected $tree;

    /**
     * Constructor
     *
     * @param  ConfigLoaderInterface $loader
     * @param  array $initData
     * @access public
     * @api
     */
    public function __construct(
        ConfigLoaderInterface $loader,
        array $initData = []
    ) {
        // the config loader
        $this->loader = $loader;

        // the config tree
        $this->tree = new Tree($initData);
    }

    /**
     * {@inheritDoc}
     */
    public function get(/*# string */ $key, $default = null)
    {
        // lazy load
        $this->loadConfig($key);

        //  get value
        $val = $this->getReference($key);

        return null === $val ? $default : $val;
    }

    /**
     * {@inheritDoc}
     */
    public function set(/*# string */ $key, $value)
    {
        // lazy load but not dereferenced
        $this->loadConfig($key);

        // replace the node
        $this->tree->addNode($key, $value);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function has(/*# string */ $key)/*# : bool */
    {
        return null !== $this->get((string) $key);
    }

    /**
     * {@inheritDoc}
     */
    public function del(/*# string */ $key)
    {
        return $this->tree->deleteNode((string) $key);
    }

    /**
     * Load config
     *
     * @param  string $name
     * @return $this
     * @access protected
     */
    protected function loadConfig(/*# string */ $name)
    {
        // get group name
        $group = $this->getGroupName($name);

        // skip defined globals
        if ('_' !== $group[0] || !isset($GLOBALS[$group])) {
            $this->loadByGroup($group);
        }

        return $this;
    }

    /**
     * Load one group config, force loading all groups if $group == ''
     *
     * @param  string $group
     * @return $this
     * @access protected
     */
    protected function loadByGroup(/*# string */ $group)
    {
        if (!$this->tree->hasNode($group)) {
            $conf = $this->loader->load($group);
            foreach ($conf as $grp => $data) {
                $this->tree->addNode($grp, $data);
            }
        }
        return $this;
    }

    /**
     * Get group name
     *
     * @param  string $name
     * @return string
     * @access protected
     */
    protected function getGroupName(/*# string */ $name)/*# : string */
    {
        return explode('.', $name)[0];
    }

    /**
     * For unknown reference $name
     *
     * @param  string $name
     * @return mixed
     * @access protected
     */
    protected function resolveUnknown(/*# string */ $name)
    {
        $val = $this->get($name);

        if (null === $val) {
            throw new LogicException(
                Message::get(Message::CONFIG_REFERENCE_UNKNOWN, $name),
                Message::CONFIG_REFERENCE_UNKNOWN
            );
        }

        return $val;
    }

    /**
     * - check super globals if name like '_SERVER.HTTP_HOST'
     * - search the parameter pool to find the right value
     *
     * {@inheritDoc}
     */
    protected function getReference(/*# string */ $name)
    {
        // get '${_SERVER.HTTP_HOST}' etc.
        if ('_' === $name[0]) {
            return $this->getSuperGlobal($name);

        // get raw value of $name
        } else {
            return $this->tree->getNode($name);
        }
    }

    /**
     * Get super global value
     *
     * @param  string $name something like '_SERVER.HTTP_HOST'
     * @return string|array
     * @access protected
     */
    protected function getSuperGlobal(/*# string */ $name)
    {
        $pos = strpos($name, '.');
        if (false !== $pos) {
            $pref = substr($name, 0, $pos);
            $suff = substr($name, $pos + 1);
            if (isset($GLOBALS[$pref][$suff])) {
                return $GLOBALS[$pref][$suff];
            }
        } else {
            $pref = $name;
            if (isset($GLOBALS[$pref])) {
                return $GLOBALS[$pref];
            }
        }
        return null;
    }
}
