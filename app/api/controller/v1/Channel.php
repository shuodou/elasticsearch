<?php

namespace app\api\controller\v1;

use app\api\service\ProductSearchService;
use app\api\validate\DeleteAllProductValidate;
use app\api\validate\DeleteDocValidate;
use app\api\validate\DeleteIndexValidate;
use app\api\validate\GetDocValidate;
use app\api\validate\SearchProductValidate;
use app\api\validate\SyncSourceProductValidate;
use app\api\validate\SyncOneProductValidate;
use app\BaseController;


/**
 * 功能描述：es
 * Class Channel
 * @package app\api\controller\v1
 * @module
 * @createDate 2020/6/22 16:06
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class Channel extends BaseController
{

    /**
     * 功能描述：商品搜索
     * @createDate 2020/6/17 15:07
     * @param ProductSearchService $ProductSearchService
     * @return \think\response\Json
     * @throws \app\api\exception\ParameterException
     * @author panzf
     */
    public function searchProducts(ProductSearchService $ProductSearchService)
    {
        (new SearchProductValidate())->goCheck();
        $params['merType'] = $this->request->post('merType','');
        $params['brandCode'] = $this->request->post('brandCode','');
        $params['keyWord'] = $this->request->post('keyWord','',['strip_tags','htmlspecialchars']);
        $channel = $this->request->post('channel');
        $page = $this->request->post('page',1);
        $limit = $this->request->post('limit',10);
        $form = ($page-1)*$limit;
        $list = $ProductSearchService->searchProducts($params,$channel,$form,$limit);
        if($list['total']){
            $result['page'] = $page;
        }
        return json($list);
    }

    /**
     * 功能描述：品牌品类搜索
     * @createDate 2020/6/22 09:35
     * @param ProductSearchService $ProductSearchService
     * @return \think\response\Json
     * @throws \app\api\exception\ParameterException
     * @author panzf
     */
    public function brandProduct(ProductSearchService $ProductSearchService)
    {
        (new SearchProductValidate())->goCheck();
        $merType = $this->request->post('merType','');
        $brandCode = $this->request->post('brandCode','');
        $channel = $this->request->post('channel');
        $page = $this->request->post('page',1);
        $limit = $this->request->post('limit',20);
        $form = ($page-1)*$limit;
        $list = $ProductSearchService->brandProduct($merType,$brandCode,$channel,$form,$limit);
        if($list['total']){
            $result['page'] = $page;
        }
        return json($list);

    }

    /**
     * 功能描述：同步渠道商品
     * @createDate 2020/6/17 13:56
     * @param ProductSearchService $ProductSearchService
     * @return \think\response\Json
     * @throws \app\api\exception\CreateIndexException
     * @throws \app\api\exception\IndexBeingException
     * @throws \app\api\exception\ParameterException
     * @author panzf
     */
    public function syncSourceProduct(ProductSearchService $ProductSearchService)
    {
        (new SyncSourceProductValidate())->goCheck();
        $channel = $this->request->get('channel', '');
        $limit = $this->request->get('limit', 1000);
        $result = $ProductSearchService->syncSourceProduct($channel, $limit);
        return json(['updateNum' => $result]);
    }

    /**
     * 功能描述：单个商品同步
     * @createDate 2020/6/18 15:28
     * @param ProductSearchService $ProductSearchService
     * @return \think\response\Json
     * @throws \app\api\exception\ParameterException
     * @throws \app\api\exception\ProductNotExistsException
     * @author panzf
     */
    public function syncOneProduct(ProductSearchService $ProductSearchService)
    {
        (new SyncOneProductValidate())->goCheck();
        $id = $this->request->param('id');
        $channel = $this->request->param('channel');
        $result = $ProductSearchService->syncOneProduct($id,$channel);
        return json($result);
    }

    /**
     * 功能描述：获取单个文档
     * @createDate 2020/6/17 15:01
     * @param ProductSearchService $ProductSearchService
     * @return \think\response\Json
     * @throws \app\api\exception\ParameterException
     * @author panzf
     */
    public function getDoc(ProductSearchService $ProductSearchService)
    {
        (new GetDocValidate())->goCheck();
        $id = $this->request->get('id','');
        $channel = $this->request->param('channel');
        $result = $ProductSearchService->getDoc($id,$channel);
        return json($result);
    }


    /**
     * 功能描述：删除索引
     * @createDate 2020/6/18 15:31
     * @param ProductSearchService $ProductSearchService
     * @return \think\response\Json
     * @throws \app\api\exception\ParameterException
     * @author panzf
     */
    public function delIndex(ProductSearchService $ProductSearchService)
    {
        (new DeleteIndexValidate())->goCheck();
        $index = $this->request->param('index', '');
        $channel = $this->request->param('channel', '');
        $result = $ProductSearchService->delIndex($channel,$index);
        return json($result);
    }

    /**
     * 功能描述：删除单个文档
     * @createDate 2020/6/17 14:01
     * @param ProductSearchService $ProductSearchService
     * @return \think\response\Json
     * @throws \app\api\exception\ParameterException
     * @author panzf
     */
    public function delDoc(ProductSearchService $ProductSearchService)
    {
        (new DeleteDocValidate())->goCheck();
        $id = $this->request->param('id');
        $channel = $this->request->param('channel');
        $result = $ProductSearchService->delDoc($id,$channel);
        return json($result);
    }

    /**
     * 功能描述：清空doc
     * @createDate 2020/6/22 16:06
     * @param ProductSearchService $ProductSearchService
     * @return \think\response\Json
     * @throws \app\api\exception\ParameterException
     * @author panzf
     */
    public function deleteByQuery(ProductSearchService $ProductSearchService)
    {
        (new DeleteAllProductValidate())->goCheck();
        $channel = $this->request->param('channel');
        $result = $ProductSearchService->deleteByQuery($channel);
        return json($result);
    }

}