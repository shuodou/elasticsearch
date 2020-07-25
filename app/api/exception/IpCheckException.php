<?php

namespace app\api\exception;

/**
 * 功能描述：ip无权访问
 * Class CheckIpException
 * @package app\api\exception
 * @module
 * @createDate 2020/6/22 08:58
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class IpCheckException extends BaseException
{

    public $code    = 400;

    public $errCode = 10001;

}