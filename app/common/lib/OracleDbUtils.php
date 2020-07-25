<?php

namespace app\common\lib;

use  app\common\oracle\driver\Oci8;
use  app\common\oracle\Query as OciQuery;

/**
 * 功能描述：oracle db
 * Class OracleDbUtils
 * @package app\common\lib
 * @module
 * @createDate 2020/6/22 16:08
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class OracleDbUtils
{

    private $db = null;

    static private $instance;

    private function __construct()
    {
        $this->db = $this->setOracleDb();
    }

    static public function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __clone(){

    }

    private function setOracleDb()
    {
        $config = [
            'database' => env("oracle_dsn"),
            'username' => env("oracle_user"),
            'password' => env("oracle_psw")
        ];
        $driver = new Oci8($config);
        return  new OciQuery($driver);
    }

    public function getDb(){
        return $this->db;
    }
}