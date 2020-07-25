<?php


namespace app\api\validate;


use app\common\validate\BaseValidate;

class SearchProductValidate extends BaseValidate
{

    public $rule =[
        "channel" =>"require|isNotEmpty",
        "merType" =>"isNotEmpty",
        "brandCode" =>"isNotEmpty",
        "keyWord" =>"isNotEmpty",
    ];

    public $message = [
        "channel" =>"渠道channel必须",
    ];

}