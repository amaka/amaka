<?php
/**
 * OfficineSoftware Amaka
 *
 * @license   http://www.opensource.org/licenses/bsd-license.php
 * @copyright Copyright (c) 2012 Andrea Turso
 * @author    Andrea Turso <andrea.turso@gmail.com>
 */
namespace Officine\Amaka;

use Zend\Config\Config;
use Zend\Stdlib\ArrayUtils;

/**
 * The Context hierarchy abstracts the state of the environment where
 * the script is run providing methods to query and alter the state
 * of the context. Example of contexts are the CliContext and HttpContext.
 */
class Context
{
    /**
     * The absolute path to the directory where our context resides
     *
     * @var string
     */
    private $workingDirectory;

    private $params;

    public function __construct(array $params = array())
    {
        $this->params = new Config($params, true);
    }

    /**
     *
     *
     * @param string $path
     */
    public function setWorkingDirectory($path)
    {
        if (! $this->isAbsolutePath($path)) {
            throw new \InvalidArgumentException('Working directory must be an absolute path');
        }
        $this->workingDirectory = $path;
        return $this;
    }

    public function isAbsolutePath($path)
    {
        return $this->isUnixAbsolutePath($path) || $this->isWindowsAbsolutePath($path);
    }

    public function isUnixAbsolutePath($path)
    {
        return '/' == substr($path, 0, 1);
    }

    public function isWindowsAbsolutePath($path)
    {
        if ('\\\\' == substr($path, 0, 2) || '\\' == substr($path, 0, 1)) {
            return true;
        }

        if (preg_match('/^[a-z]+\:(.*)/i', $path)) {
            return true;
        }
        return false;
    }

    /**
     *
     *
     * @return string
     */
    public function getWorkingDirectory()
    {
        return $this->workingDirectory;
    }

    /**
     * Returns a copy of the parameters
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params->toArray();
    }

    /**
     * Retrieves a single parameter by key
     *
     * @param string $key
     * @return mixed
     */
    public function getParam($key)
    {
        $value = $this->params->get($key);

        if ($value instanceof Config) {
            return $value->toArray();
        }
        return $value;
    }

    /**
     * Sets a parameter value by key
     *
     * @param string $key
     * @param mixed $value
     */
    public function setParam($key, $value)
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Merges the content of the $params array with the
     * parameters already registered.
     *
     * @param array $params
     */
    public function setParams($params)
    {
        if (empty($params)) {
            return;
        }

        if (! ArrayUtils::isHashTable($params)) {
            throw new \InvalidArgumentException('Only hash tables (key-value pairs) are accepted as params.');
        }
        $this->params->merge(new Config($params));

        return $this;
    }
}
