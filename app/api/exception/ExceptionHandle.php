<?php

namespace app\api\exception;


use app\common\lib\SeasLogUtils;
use think\Response;
use think\exception\Handle;
use Throwable;
use think\facade\Request;

/**
 * 功能描述： 异常重写
 * Class ExceptionHandle
 * @package app\api\exception
 * @module
 * @createDate 2020/6/12 16:54
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class ExceptionHandle extends Handle
{

    private $code;

    private $msg;

    private $errCode;

    public function render($request, Throwable $e): Response
    {
        // 添加自定义异常处理机制
        if ($e instanceof BaseException) {
            $this->code = $e->code;
            $this->msg = config("error." . $e->errCode);
            if (empty($this->msg) || !empty($e->msg)) {
                $this->msg = $e->msg;
            }
            $this->errCode = $e->errCode;
        } else {
            if (env('app_debug')) {
                return parent::render($request, $e);
            }
            $this->code = 500;
            $this->errCode = 999;
            $this->msg = config("error." . $this->errCode);
            $msg = json_decode($e->getMessage());
            if(is_object($msg)){
                $msg->request_url = Request::url();
            }elseif (is_array($msg)){
                $msg['request_url'] = Request::url();
            }else{
                $msg = $e->getMessage();
            }
            SeasLogUtils::debug(json_encode($msg));
        }

        $result = [
            "msg" => $this->msg,
            "errCode" => $this->errCode,
            "request_url" => Request::url()
        ];
        return json($result, $this->code);
    }
}