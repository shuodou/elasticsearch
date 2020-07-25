<?php
/**
 * The Orno Component Library
 *
 * @author  Phil Bennett @philipobenito
 * @license http://www.wtfpl.net/txt/copying/ WTFPL
 */
namespace  app\common\oracle;

use  app\common\oracle\driver\DriverInterface;

/**
 * Query
 *
 * Object to handle calls to database driver objects, provides an interface to write
 * ANSI/ISO SQL without object oriented query building with support for transactions.
 */
class Query
{
    /**
     * Type constants
     */
    const PARAM_STR  = 1;
    const PARAM_INT  = 2;
    const PARAM_BOOL = 3;
    const PARAM_BIN  = 4;
    const PARAM_FLT  = 5;

    /**
     * Database connection driver
     *
     * @var \db\driver\DriverInterface
     */
    protected $driver;

    /**
     * Configuration array
     *
     * @var array
     */
    protected $config;

    /**
     * Constructor
     *
     * @param DriverInterface $driver
     * @param array $config
     */
    public function __construct(DriverInterface $driver, array $config = [])
    {
        $this->driver = $driver;
        $this->config = $config;
    }

    /**
     * Get Driver
     *
     * Return an instance of the driver
     *
     * @return \db\driver\DriverInterface
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Set Driver
     *
     * Provide an implementation of the driver interface
     *
     * @param  DriverInterface $driver
     * @return \db\Query
     */
    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;
        return $this;
    }

    /**
     * Prepare
     *
     * Prepare a query with the database driver
     *
     * @param  string $query
     * @return \db\Query
     */
    public function prepare($query)
    {
        if (! empty($this->config)) {
            $this->getDriver()->connect($this->config);
        }

        $this->getDriver()->prepareQuery($query);
        return $this;
    }

    /**
     * Bind
     *
     * Bind a value to a placeholder in the most recent prepared query
     *
     * @param  mixed $placeholder
     * @param  mixed $value
     * @param  int   $type
     * @return \db\Query
     */
    public function bind($placeholder, $value, $type = self::PARAM_STR)
    {
        if($placeholder){
            if(substr($placeholder,0,1) != ':'){
                $placeholder = ':'.$placeholder;
            }
        }
        $type = $this->getType($value);
        $this->getDriver()->bind($placeholder, $value, $type);
        return $this;
    }

    /**
     * 功能描述：绑定固定参数
     * @author    weimingze    2019年4月16日  下午1:09:58
     */
    public function bindValue($placeholder, $value, $type = self::PARAM_STR){
        $this->bind($placeholder, $value, $type);
    }

    /**
     * 功能描述：绑定变量参数
     * @author    weimingze    2019年4月16日  下午1:09:30
     */
    public function bindParam($placeholder, &$value, $type = self::PARAM_STR){
        if($placeholder){
            if(substr($placeholder,0,1) != ':'){
                $placeholder = ':'.$placeholder;
            }
        }
        $type = $this->getType($value);
        $this->getDriver()->bindParam($placeholder, $value, $type);
        return $this;
    }

    private function getType($value){
        $type = self::PARAM_STR;
        if($value){
            if(is_string($value)){
                $type = self::PARAM_STR;
            }elseif(is_bool($value)){
                $type = self::PARAM_BOOL;
            }elseif(is_float($value)){
                $type = self::PARAM_FLT;
            }elseif(is_double($value)){
                $type = self::PARAM_FLT;
            }elseif(is_integer($value)){
                $type = self::PARAM_INT;
            }
        }
        return $type;
    }

    /**
     * Execute
     *
     * Execute the most recent prepared query
     *
     * @return \db\Query
     */
    public function execute()
    {
        $this->getDriver()->execute();
        return $this;
    }

    /**
     * Transaction
     *
     * Start a transaction with the database driver
     *
     * @return \db\Query
     */
    public function transaction()
    {
        if (! empty($this->config)) {
            $this->getDriver()->connect($this->config);
        }

        $this->getDriver()->transaction();
        return $this;
    }

    /**
     * Commit
     *
     * Commit the transaction
     *
     * @return \db\Query
     */
    public function commit()
    {
        $this->getDriver()->commit();
        return $this;
    }

    /**
     * Rollback
     *
     * Rollback the transaction
     *
     * @return \db\Query
     */
    public function rollback()
    {
        $this->getDriver()->rollback();
        return $this;
    }

    /**
     * Fetch
     *
     * Fetch an associative array of the next row in the result set
     *
     * @return array
     */
    public function fetch()
    {
        return $this->getDriver()->fetch();
    }

    /**
     * Fetch Object
     *
     * Fetch stdClass object of the next row in the result set
     *
     * @return object
     */
    public function fetchObject()
    {
        return $this->getDriver()->fetchObject();
    }

    /**
     * Fetch
     *
     * Fetch a multi-dimensional array of all rows in the result set
     *
     * @return array
     */
    public function fetchAll()
    {
        return $this->getDriver()->fetchAll();
    }

    /**
     * 功能描述：操作影响行数
     * @createdate 2019/4/11 17:38
     * @author panzf
     * @return mixed
     */
    public function numRows()
    {
        return $this->getDriver()->numRows();
    }
}
