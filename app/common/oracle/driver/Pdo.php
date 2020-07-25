<?php
/**
 * The Orno Component Library
 *
 * @author  Phil Bennett @philipobenito
 * @license http://www.wtfpl.net/txt/copying/ WTFPL
 */
namespace  app\common\oracle\driver;

use  app\common\oracle\exception;

/**
 * PDO Driver
 *
 * A database abstraction for PHP PDO driver
 */
class Pdo implements DriverInterface
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
     * The PDO object
     *
     * @var \PDO
     */
    protected $connection;

    /**
     * The PDO statement object
     *
     * @var \PDOStatement
     */
    protected $statement;

    /**
     * Configuration array
     *
     * @var array
     */
    protected $config = [
        'database' => null,
        'username' => null,
        'password' => null
    ];

    /**
     * Constructor
     *
     * @throws \Orno\Db\Exception\UnsupportedException
     * @param  array $config
     */
    public function __construct(array $config = [])
    {
        if (! extension_loaded('pdo')) {
            throw new Exception\UnsupportedException(
                sprintf('%s requires the PDO extension to be loaded', __CLASS__)
            );
        }

        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Orno\Db\Exception\ConnectionException
     * @param  array $config
     * @return \Orno\Db\Driver\Pdo
     */
    public function connect(array $config = [])
    {
        if ($this->connection instanceof \PDO) {
            return $this;
        }

        $options = (isset($config['options'])) ? $config['options'] : [];

        $options[\PDO::ATTR_PERSISTENT] = (isset($config['persistent'])) ? (bool) $config['persistent'] : true;

        $database = (isset($config['database'])) ? $config['database'] : $this->config['database'];
        $username = (isset($config['username'])) ? $config['username'] : $this->config['username'];
        $password = (isset($config['password'])) ? $config['password'] : $this->config['password'];
        $charset  = (isset($config['charset']))  ? $config['charset']  : 'UTF8';

        try {
            $this->connection = new \PDO($database, $username, $password, $options);

            // prior to 5.3.6 the charset key in the connection string is ignored
            // so we can check the PHP version and force charset this way
            if (version_compare(phpversion(), '5.3.6') < 0) {
                $this->connection->exec("SET NAMES $charset");
            }
        } catch (\PDOException $e) {
            throw new Exception\ConnectionException($e->getMessage(), $e->getCode());
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    public function disconnect()
    {
        unset($this->statement);
        unset($this->connection);
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Orno\Db\Exception\ConnectionException
     * @param  string $query
     * @return \Orno\Db\Driver\Pdo
     */
    public function prepareQuery($query)
    {
        $this->connect($this->config);

        $this->statement = $this->connection->prepare($query);
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Orno\Db\Exception\BindingException
     * @throws \Orno\Db\Exception\NoResourceException
     * @param  mixed   $placeholder
     * @param  mixed   $value
     * @param  integer $type
     * @param  integer $maxlen
     * @return \Orno\Db\Driver\Pdo
     */
    public function bind($placeholder, $value, $type = self::PARAM_STR, $maxlen = 0)
    {
        if (! $this->statement instanceof \PDOStatement) {
            throw new Exception\NoResourceException(
                sprintf('%s expects a query to have been prepared', __METHOD__)
            );
        }

        $type = $this->getValueType($type);

        if ($maxlen > 0) {
            $this->statement->bindParam($placeholder, $value, $type, (int) $maxlen);
        } else {
            $this->statement->bindParam($placeholder, $value, $type);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Orno\Db\Exception\NoResourceException
     * @return boolean
     */
    public function execute()
    {
        if (! $this->statement instanceof \PDOStatement) {
            throw new Exception\NoResourceException(
                sprintf('%s expects a query to have been prepared', __METHOD__)
            );
        }

        return $this->statement->execute();
    }

    /**
     * {@inheritdoc}
     *
     * @return \Orno\Db\Driver\Pdo
     */
    public function transaction()
    {
        $this->connect($this->config);
        $this->connection->beginTransaction();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function commit()
    {
        return $this->connection->commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollback()
    {
        return $this->connection->rollback();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Orno\Db\Exception\NoResourceException
     */
    public function fetch()
    {
        if (! $this->statement instanceof \PDOStatement) {
            throw new Exception\NoResourceException(
                sprintf('%s expects a query to have been prepared and executed', __METHOD__)
            );
        }

        return $this->statement->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Orno\Db\Exception\NoResourceException
     */
    public function fetchObject()
    {
        if (! $this->statement instanceof \PDOStatement) {
            throw new Exception\NoResourceException(
                sprintf('%s expects a query to have been prepared and executed', __METHOD__)
            );
        }

        return $this->statement->fetchObject();
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Orno\Db\Exception\NoResourceException
     */
    public function fetchAll()
    {
        if (! $this->statement instanceof \PDOStatement) {
            throw new Exception\NoResourceException(
                sprintf('%s expects a query to have been prepared and executed', __METHOD__)
            );
        }

        return $this->statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get Value Type
     *
     * Unify value types accross all drivers
     *
     * @param  integer $value
     * @return integer
     */
    protected function getValueType($type)
    {
        switch ($type) {
            case self::PARAM_STR:
                $type = \PDO::PARAM_STR;
                break;
            case self::PARAM_INT:
                $type = \PDO::PARAM_INT;
                break;
            case self::PARAM_BOOL:
                $type = \PDO::PARAM_BOOL;
                break;
            case self::PARAM_BIN:
                $type = \PDO::PARAM_BIN;
                break;
            case self::PARAM_FLT:
                $type = \PDO::PARAM_STR;
                break;
        }

        return $type;
    }
}
