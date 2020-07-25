<?php

namespace app\api\exception;


class RedisAuthException extends BaseException
{
    public $code    = 400;

    public $errCode = 30002;
}