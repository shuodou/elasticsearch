<?php


namespace app\api\exception;


class ProductNotExistsException extends BaseException
{

    public $code    = 400;

    public $errCode = 20004;

}