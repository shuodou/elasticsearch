<?php

namespace app\common\lib;

use app\api\exception\RedisAuthException;
use app\api\exception\RedisConnectException;

/**
 * 功能描述：redis 客户端连接
 * Class RedisUtils
 * @package app\common\lib
 * @module
 * @createDate 2020/6/19 16:06
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class RedisUtils
{
    private $redis = "";

    private static $_instance = null;

    public static function getInstance() {
        if(empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        $this->redis = new \Redis();
        $config = $this->getConfig();
        $result = $this->redis->connect($config['host'], $config['port'], 120);
        if($result === false) {
            throw new RedisConnectException();
        }
        if($config['password']){
            //授权
            if( !$this->redis->auth($config['password'])){
                throw  new RedisAuthException();
            }
        }
    }

    private function getConfig()
    {
        $config = [];
        $config['host'] = env('redis.rdhost');
        $config['port'] = env('redis.rdport');
        $config['password'] = env('redis.rdpasswd');
        return $config;
    }

    public function getClient()
    {
        return $this->redis;
    }

}