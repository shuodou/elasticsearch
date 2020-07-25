<?php

namespace app\api\validate;

use app\common\validate\BaseValidate;

class ElasticSearchProductValidate extends BaseValidate
{
    public $rule =[
        "keyWord" =>"require|isNotEmpty",
        "sourceCode" =>"require|isNotEmpty",
    ];

    public $message = [
        "keyWord" =>"查询关键字必须",
        "sourceCode" =>"来源必须",

    ];

}