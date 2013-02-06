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
     * @param string $directory
     */
    public function setWorkingDirectory($directory)
    {
        if ('/' != $directory[0]) {
            throw new \InvalidArgumentException('Working directory must be an absolute path');
        }
        $this->workingDirectory = $directory;
        return $this;
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
        return $this->params->get($key);
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
