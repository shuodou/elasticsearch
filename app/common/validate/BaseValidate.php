<?php

namespace app\common\validate;

use app\api\exception\ParameterException;
use think\Validate;
use think\facade\Request;

/**
 * 功能描述：参数校验 基础类
 * Class BaseValidate
 * @package app\common\validate
 * @module
 * @createDate 2020/6/22 16:12
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class BaseValidate extends Validate
{

    /**
     * 功能描述：验证参数
     * @createDate 2020/6/12 17:00
     * @return bool
     * @throws ParameterException
     * @author panzf
     */
    public function goCheck()
    {
        $param = Request::param();
        $result = $this->batch(false)->check($param);
        if ($result) {
            return true;
        } else {
            throw new ParameterException(['msg' => $this->error]);
        }
    }


    /**
     * 功能描述：正整数
     * @createDate 2020/6/12 17:00
     * @param $value
     * @param string $rule
     * @param $data
     * @param string $field
     * @return bool
     * @author panzf
     */
    protected function isPositiveInteger($value, $rule = "", $data, $field = "")
    {

        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 功能描述：不为空
     * @createDate 2020/6/12 17:00
     * @param $value
     * @param string $rule
     * @param $data
     * @param string $field
     * @return bool
     * @author panzf
     */
    public function isNotEmpty($value, $rule = "", $data, $field = "")
    {
        if (empty($value)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 功能描述：手机号
     * @createDate 2020/6/12 17:00
     * @param $value
     * @return bool
     * @author panzf
     */
    public function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';

        $result = preg_match($rule, $value);

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 功能描述：
     * @createDate 2020/6/12 17:01
     * @param $arrays
     * @return array
     * @throws ParameterException
     * @author panzf
     */
    public function getDataByRule($arrays)
    {
        if (array_key_exists('user_id', $arrays) | array_key_exists('uid', $arrays)) {
            // 不允许包含user_id或者uid，防止恶意覆盖user_id外键
            throw new ParameterException([
                'msg' => '参数中包含有非法的参数名user_id或者uid'
            ]);
        }
        $newArray = [];

        foreach ($this->rule as $key => $value) {
            $newArray[$key] = $arrays[$key];
        }

        return $newArray;
    }


}