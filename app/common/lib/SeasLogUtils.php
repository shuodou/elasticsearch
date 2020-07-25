<?php

namespace app\common\lib;

use app\common\seaslogs\SeasLog;

/**
 * 功能描述：seasLog 日志
 * Class SeasLogUtils
 * @package app\common\lib
 * @module
 * @createDate 2020/6/22 16:09
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class SeasLogUtils
{
    public static function info($message, $params = array()){
        $module = 'ElasticSearch/apiLogs';
        SeasLog::info($message, $params, $module);
    }

    public static function debug($message, $params = array()){
        $module = 'ElasticSearch/apiLogs';
        SeasLog::debug($message, $params, $module);
    }

}