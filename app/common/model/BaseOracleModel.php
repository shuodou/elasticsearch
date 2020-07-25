<?php

namespace app\common\model;

use app\common\lib\OracleDbUtils;

/**
 * 功能描述：oracle 基础 model
 * Class BaseOracleModel
 * @package app\common\model
 * @module
 * @createDate 2020/6/22 16:09
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class BaseOracleModel
{

    protected $db;

    public function __construct()
    {
        $this->db = OracleDbUtils::getInstance()->getDb();
    }

}