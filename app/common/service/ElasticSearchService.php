<?php

namespace app\common\service;

use app\api\exception\CreateIndexException;
use app\api\exception\CreateMappingException;
use app\api\exception\IndexBeingException;
use app\common\lib\SeasLogUtils;
use app\common\model\ElasticSearchModel;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use think\Exception;

/**
 * 功能描述：es 基础 service
 * Class ElasticSearchService
 * @package app\common\service
 * @module
 * @createDate 2020/6/22 16:11
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class ElasticSearchService
{
    private  $model = null;

    public function __construct()
    {
        $this->model = new ElasticSearchModel();
    }

    /**
     * 功能描述：获取文档
     * @createDate 2020/6/12 09:56
     * @param $params
     * @return mixed
     * @author panzf
     */
    public function searchDoc($params)
    {
        return $this->model->searchDoc($params);
    }

    /**
     * 功能描述：创建索引
     * @createDate 2020/6/15 15:20
     * @param $params
     * @return array|null
     * @throws CreateIndexException
     * @throws IndexBeingException
     * @author panzf
     */
    public function addIndex($params)
    {
        $result  = false;
        try{
            if(!$this->checkIndexExists(['index'=>$params['index']])){
                $result = $this->model->addIndex($params);
            }else{
                throw  new IndexBeingException();
            }
        }catch (BadRequest400Exception $exception){
            SeasLogUtils::debug($exception->getMessage());
            throw  new CreateIndexException(['msg'=>$exception->getMessage()]);
        }
        return $result;
    }

    /**
     * 功能描述：addMapping
     * @createDate 2020/6/15 15:38
     * @param $params
     * @return bool
     * @throws CreateMappingException
     * @author panzf
     */
    public function addMapping($params)
    {
        $result  = false;
        try{
            $res = $this->model->addMapping($params);
            if (is_array($res) && isset($res['acknowledged']) && $res['acknowledged']) {
                $result = true;
            }
        }catch (Exception $exception){
            SeasLogUtils::debug($exception->getMessage());
            throw  new CreateMappingException(['msg'=>$exception->getMessage()]);
        }
        return $result;
    }

    /**
     * 功能描述：索引是否错在
     * @createDate 2020/6/12 11:03
     * @param $params
     * @return bool
     * @author panzf
     */
    public function checkIndexExists($params)
    {
        return $this->model->checkIndexExists($params);
    }

    /**
     * 功能描述：获取文档
     * @createDate 2020/6/12 13:01
     * @param $params
     * @return array
     * @author panzf
     */
    public function getDoc($params)
    {
        $result = array();
        try{
            $result = $this->model->getDoc($params);
        }catch (Missing404Exception $exception){
            SeasLogUtils::debug($exception->getMessage());
        }
        return $result;
    }

    /**
     * 功能描述：添加文档
     * @createDate 2020/6/12 13:01
     * @param $params
     * @return bool
     * @author panzf
     */
    public function addDoc($params)
    {
        $result = true;
        try{
            $this->model->addDoc($params);
        }catch (Exception $exception){
            SeasLogUtils::debug($exception->getMessage());
            $result = false;
        }
        return $result;

    }

    /**
     * 功能描述：更新文档
     * @createDate 2020/6/12 13:01
     * @param $params
     * @return bool
     * @author panzf
     */
    public function updateDoc($params)
    {
        $result = true;
        try{
            $this->model->updateDoc($params);
        }catch (\Exception $exception){
            SeasLogUtils::debug("updateDoc:".$exception->getMessage());
            $result = false;
        }
        return $result;

    }

    /**
     * 功能描述：删除文档
     * @createDate 2020/6/17 11:07
     * @param $params
     * @return bool
     * @author panzf
     */
    public function delDoc($params)
    {
        $result = true;
        try{
            $res = $this->model->delDoc($params);
            if(!$res['found']){
                $result = false;
            }
        }catch (\Exception $exception){
            SeasLogUtils::debug("delDoc:".$exception->getMessage());
            $result = false;
        }
        return $result;
    }

    /**
     * 功能描述：删除索引
     * @createDate 2020/6/12 13:11
     * @param $params
     * @return bool
     * @author panzf
     */
    public function delIndex($params)
    {
        $result = true;
        try{
            $res = $this->model->delIndex($params);
            if(!$res['acknowledged']){
                $result = false;
            }
        }catch (\Exception $exception){
            SeasLogUtils::debug("delIndex:".$exception->getMessage());
            $result = false;
        }
        return $result;
    }

    /**
     * 功能描述：批量操作文档
     * @createDate 2020/6/17 10:13
     * @param $params
     * @return bool
     * @author panzf
     */
    public function bulkDoc($params)
    {
        $result = true;
        try{
            $this->model->bulkDoc($params);
        }catch (Exception $exception){
            SeasLogUtils::debug($exception->getMessage());
            $result = false;
        }
        return $result;
    }

    /**
     * 功能描述：
     * @createDate 2020/6/18 13:45
     * @param $params
     * @return bool
     * @author panzf
     */
    public function deleteByQuery($params)
    {
        $result = true;
        try{
            $this->model->deleteByQuery($params);
        }catch (\Exception $exception){
            SeasLogUtils::debug($exception->getMessage());
            $result = false;
        }
        return $result;
    }



}