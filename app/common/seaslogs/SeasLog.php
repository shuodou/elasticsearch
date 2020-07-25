<?php


namespace app\common\seaslogs;


class SeasLog
{

    /**
     * 功能描述：info
     * @createDate 2020/6/16 17:50
     * @param $message
     * @param array $contents
     * @param string $module
     * @author panzf
     */
    public static function info($message, $contents = array(), $module = ''){

        if(empty($module)){
            $module = 'commonLogs';
        }
        $params = array();
        if($contents){
            foreach($contents as $k=>$v){
                if(is_array($v) || is_object($v)){
                    $v = json_encode($v, JSON_UNESCAPED_UNICODE);
                }
                $params[$k] = $v;
            }
        }
        \SeasLog::info($message, $params, $module);

    }


    /**
     * 功能描述：debug
     * @createDate 2020/6/16 17:49
     * @param  string $message
     * @param array $contents
     * @param string $module
     * @author panzf
     */
    public static function debug($message, $contents = array(), $module = ''){
        $params = array();
        if($contents){
            foreach($contents as $k=>$v){
                if(is_array($v) || is_object($v)){
                    $v = json_encode($v, JSON_UNESCAPED_UNICODE);
                }
                $params[$k] = $v;
            }
        }
        \SeasLog::debug($message, $params, $module);
    }
}