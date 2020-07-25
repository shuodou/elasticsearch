<?php


namespace app\api\validate;


use app\common\validate\BaseValidate;

class SyncSourceProductValidate extends BaseValidate
{

    public $rule =[
        "channel" =>"require|isNotEmpty",
    ];

    public $message = [
        "channel" =>"渠道必须",
    ];

}