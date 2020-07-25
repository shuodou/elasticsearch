<?php


namespace app\api\validate;


use app\common\validate\BaseValidate;

class DeleteDocValidate extends BaseValidate
{

    public $rule =[
        "id" =>"require|isNotEmpty",
        "channel" =>"require|isNotEmpty",
    ];

    public $message = [
        "id" =>"商品id必须",
        "channel" =>"渠道channel必须",
    ];

}