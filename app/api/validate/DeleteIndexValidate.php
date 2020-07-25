<?php


namespace app\api\validate;


use app\common\validate\BaseValidate;

class DeleteIndexValidate extends BaseValidate
{
    public $rule =[
        "channel" =>"require|isNotEmpty",
    ];

    public $message = [
        "channel" =>"渠道channel必须",
    ];
}