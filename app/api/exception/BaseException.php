<?php
namespace app\api\exception;


use think\Exception;

/**
 * 功能描述：基础异常
 * Class BaseException
 * @package app\api\exception
 * @module
 * @createDate 2020/6/12 16:54
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class BaseException extends Exception
{

    public $code    = 400;

    public $msg     = "";

    public $errCode = 10000;

    function __construct($param = []){

        if(!is_array($param)){
            return;
        }

        if(array_key_exists("code",$param)){
            $this->code = $param['code'];
        }

        if(array_key_exists("msg",$param)){
            $this->msg = $param['msg'];
        }

        if(array_key_exists("errCode",$param)){
            $this->errCode = $param['errCode'];
        }
    }
}