<?php

namespace app\api\middleware;

use app\api\exception\IpCheckException;

/**
 * 功能描述： ip 验证
 * Class IpCheck
 * @package app\api\middleware
 * @module
 * @createDate 2020/6/22 08:59
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class IpCheckMiddleware
{

    /**
     * 功能描述：ip限制
     * @createDate 2020/6/19 18:25
     * @param $request
     * @param \Closure $next
     * @return mixed
     * @throws IpCheckException
     * @author panzf
     */
    public function handle($request, \Closure $next)
    {
        if(!$this->checkIp($request->ip())){
            throw  new IpCheckException();
        }
        return $next($request);
    }

    /**
     * 功能描述：验证ip
     * @createDate 2020/6/22 08:55
     * @param $ip
     * @return bool
     * @author panzf
     */
    private function checkIp($ip)
    {
        $ipList = [
            '127.0.0.1',
        ];
        $result = false;
        if (in_array($ip, $ipList)) {
            $result = true;
        }
        return $result;
    }

}