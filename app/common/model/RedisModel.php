<?php

namespace app\common\model;

use app\common\lib\RedisUtils;


/**
 * 功能描述：redis 基础 model
 * Class RedisModel
 * @package app\common\model
 * @module
 * @createDate 2020/6/22 16:10
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class RedisModel
{

    private $redis;

    public function __construct()
    {
        $this->redis = RedisUtils::getInstance()->getClient();
    }


    /**
     * 功能描述：
     * @createDate 2020/6/19 15:42
     * @param $key
     * @param $val
     * @return bool|int
     * @author panzf
     */
    public function lPush($key,$val)
    {
        if(is_array($val)){
            $val = json_encode($val);
        }
        return $this->redis->lPush($key, $val);
    }

    /**
     * 功能描述：
     * @createDate 2020/6/19 15:43
     * @param $key
     * @return bool|mixed
     * @author panzf
     */
    public function rPop($key)
    {
        return $this->redis->rPop($key);
    }

    /**
     * 功能描述：返回列表长度
     * @createDate 2020/6/19 16:01
     * @param $key
     * @return bool|int
     * @author panzf
     */
    public function lLen($key)
    {
        return $this->redis->lLen($key);
    }



}