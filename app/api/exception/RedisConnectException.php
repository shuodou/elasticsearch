<?php

namespace app\api\exception;


class RedisConnectException extends BaseException
{
    public $code    = 400;

    public $errCode = 30001;
}