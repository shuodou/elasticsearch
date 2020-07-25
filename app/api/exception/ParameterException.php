<?php
namespace app\api\exception;


/**
 * 功能描述：验证类异常
 * Class ParameterException
 * @package app\api\exception
 * @module
 * @createDate 2020/6/12 16:55
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class ParameterException extends BaseException
{

    public $code    = 400;

    public $msg     = "error message";

    public $errCode = 10000;


}