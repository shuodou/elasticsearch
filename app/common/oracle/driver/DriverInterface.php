<?php
/**
 * The Orno Component Library
 *
 * @author  Phil Bennett @philipobenito
 * @license http://www.wtfpl.net/txt/copying/ WTFPL
 */
namespace app\common\oracle\driver;

/**
 * Driver Interface
 *
 * Contract to which all database drivers should conform
 */
interface DriverInterface
{
    /**
     * Connect
     *
     * Connect to the database
     *
     * @param array $config
     */
    public function connect(array $config = []);

    /**
     * Disconnect
     *
     * Kill the connection to the database
     *
     * @return boolean
     */
    public function disconnect();

    /**
     * Prepare Query
     *
     * Prepare a query statement
     *
     * @param string $query
     */
    public function prepareQuery($query);

    /**
     * Bind
     *
     * Bind the value of a referenced variable to a placeholder in the prepared query
     *
     * @param string  $placeholder
     * @param mixed   $value - Referenced variable
     * @param integer $type
     * @param integer $maxlen
     */
    public function bind($placeholder, $value, $type, $maxlen);

    /**
     * Execute
     *
     * Execute a prepared query
     *
     * @return boolean
     */
    public function execute();

    /**
     * Transaction
     *
     * Begin a transaction
     */
    public function transaction();

    /**
     * Commit
     *
     * Commit all executions made in the current transaction
     *
     * @return boolean
     */
    public function commit();

    /**
     * Rollback
     *
     * Rollback all executions made in the current transaction
     *
     * @return boolean
     */
    public function rollback();

    /**
     * Fetch
     *
     * Return an associative array of the next row in the result set
     *
     * <code>
     * [
     *     $column1 => $value1,
     *     $column2 => $value2
     * ]
     * </code>
     *
     * @return array
     */
    public function fetch();

    /**
     * Fetch Object
     *
     * Return a stdClass object of the next row in the result set
     *
     * @return object
     */
    public function fetchObject();

    /**
     * Fetch All
     *
     * Return a multi-deminesional array of all rows from the result set
     *
     * <code>
     * [
     *     0 => [
     *         $column1 => $value1,
     *         $column2 => $value2
     *     ],
     *     1 => [
     *         $column1 => $value1,
     *         $column2 => $value2
     *     ]
     * ]
     * </code>
     *
     * @return array
     */
    public function fetchAll();


    public function numRows();
}
