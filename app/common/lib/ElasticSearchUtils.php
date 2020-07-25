<?php

namespace app\common\lib;


use Elasticsearch\ClientBuilder;
use think\facade\Log;

/**
 * 功能描述：es客户端连接
 * Class ElasticSearchUtils
 * @package app\common\lib
 * @module
 * @createDate 2020/6/22 16:08
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class ElasticSearchUtils
{
    private $elkClient = null;

    static private $instance;

    private function __construct($host)
    {
        $this->elkClient = $this->setClient($host);
    }

    static public function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self(self::getHost());
        }
        return self::$instance;

    }

    private function __clone(){

    }

    /**
     * 功能描述：连接
     * @createDate 2020/5/18 09:42
     * @param $hosts
     * @return \Elasticsearch\Client|null
     * @author panzf
     */
    private function setClient($hosts)
    {
        $elkClient = null;
        try{
            $elkClient = ClientBuilder::create()->setHosts($hosts) ->build();
        }catch (Exception $e){
            Log::write($e->getMessage(),'notice');
        }

        return $elkClient;
    }

    /**
     * 功能描述：
     * @createDate 2020/5/18 09:42
     * @return \Elasticsearch\Client|null
     * @author panzf
     */
    public  function  getClient(){
        return $this->elkClient;
    }
/**
 * PHP Version xxxx
 *
 * @link http://www.xxxx.com
 * @copyright
 */
    /**
     * 功能描述：es服务地址
     * @createDate 2020/6/12 09:35
     * @return array
     * @author panzf
     */
    private static function getHost()
    {
        $host  = env('es.host', '');
        $port  = env('es.port', '');
        return [$host.':'.$port];
    }
}