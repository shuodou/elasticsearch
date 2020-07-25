<?php

namespace app\common\model;

use app\common\lib\ElasticSearchUtils;

/**
 * 功能描述：es 基础 model
 * Class ElasticSearchModel
 * @package app\common\model
 * @module
 * @createDate 2020/6/22 16:10
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class ElasticSearchModel
{
    private $elkClient;

    public function __construct()
    {
        $this->elkClient = ElasticSearchUtils::getInstance()->getClient();
    }

    /**
     * 功能描述：搜索文档
     * @createDate 2020/6/12 10:02
     * @param $params
     * @return mixed
     * @author panzf
     */
    public function searchDoc($params)
    {
        return $this->elkClient->search($params);
    }

    /**
     * 功能描述：添加文档
     * @createDate 2020/6/12 13:02
     * @param $params
     * @return array
     * @author panzf
     */
    public function addDoc($params)
    {
        return $this->elkClient->index($params);
    }

    /**
     * 功能描述：更新文档
     * @createDate 2020/6/12 13:02
     * @param $params
     * @return array
     * @author panzf
     */
    public function updateDoc($params)
    {
        return $this->elkClient->update($params);
    }

    /**
     * 功能描述：获取文档
     * @createDate 2020/6/12 13:02
     * @param $params
     * @return array
     * @author panzf
     */
    public function getDoc($params)
    {
        return $this->elkClient->get($params);
    }

    /**
     * 功能描述：删除文档
     * @createDate 2020/6/17 11:07
     * @param $params
     * @return array
     * @author panzf
     */
    public function delDoc($params)
    {
        return $this->elkClient->delete($params);
    }

    /**
     * 功能描述：创建索引
     * @createDate 2020/6/12 13:02
     * @param $params
     * @return array
     * @author panzf
     */
    public function addIndex($params)
    {
        return $this->elkClient->indices()->create($params);
    }


    /**
     * 功能描述：addMapping
     * @createDate 2020/6/12 13:02
     * @param $params
     * @return array
     * @author panzf
     */
    public function addMapping($params)
    {
        return $this->elkClient->indices()->putMapping($params);
    }


    /**
     * 功能描述：判断索引是否存在
     * @createDate 2020/6/12 13:01
     * @param $params
     * @return bool
     * @author panzf
     */
    public function checkIndexExists($params)
    {
        return $this->elkClient->indices()->exists($params);
    }


    /**
     * 功能描述：删除索引
     * @createDate 2020/6/17 09:56
     * @param $params
     * @return array
     * @author panzf
     */
    public function delIndex($params)
    {
        return $this->elkClient->indices()->delete($params);
    }

    /**
     * 功能描述：批量操作文档
     * @createDate 2020/6/17 09:56
     * @param $params
     * @return mixed
     * @author panzf
     */
    public function bulkDoc($params)
    {
        return $this->elkClient->bulk($params);
    }

    /**
     * 功能描述：条件删除
     * @createDate 2020/6/18 13:44
     * @param $params
     * @return array
     * @author panzf
     */
    public function deleteByQuery($params)
    {
        return $this->elkClient->deleteByQuery($params);
    }

}