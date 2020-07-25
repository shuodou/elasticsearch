<?php

namespace app\api\middleware;

/**
 * 功能描述：返回结果处理
 * Class OutputMiddleware
 * @package app\api\middleware
 * @module
 * @createDate 2020/6/22 09:36
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class OutputMiddleware
{
    /**
     * 功能描述：输出数据处理
     * @createDate 2020/6/12 17:34
     * @param $request
     * @param \Closure $next
     * @return mixed
     * @author panzf
     */
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        $data = $response->getData();
        if(is_bool($data)){
            $result = $data;
            $data = array();
            $data['flag'] = $result;
        }
        if($response->getCode() == 200){
            $data['errCode'] = 0;
        }
        $response->data($data);
        return $response;
    }


}