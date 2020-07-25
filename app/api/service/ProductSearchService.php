<?php

namespace app\api\service;

use app\api\exception\CreateIndexException;
use app\api\exception\ParameterException;
use app\api\exception\ProductNotExistsException;
use app\api\model\AllProductModel;
use app\common\service\ElasticSearchService;

/**
 * 功能描述：es service
 * Class ProductSearchService
 * @package app\api\service
 * @module
 * @createDate 2020/6/22 16:07
 * @version    V1.0.1
 * @author     panzf
 * @copyright
 */
class ProductSearchService
{

    private $index = 'index_product_goods_';
    private $type = 'product_goods';

    /**
     * 功能描述：品牌 品类 关键字搜索
     * @createDate 2020/6/16 14:08
     * @param $param
     * @param string $channel
     * @param int $from
     * @param int $size
     * @return mixed
     * @throws ParameterException
     * @author panzf
     */
    public function searchProducts($param, $channel, $from = 0, $size = 10)
    {

        if (empty($param['merType']) && empty($param['brandCode']) && empty($param['keyWord'])) {
            throw  new ParameterException(['msg' => '品类,品牌,搜索关键字都不能为空']);
        }
        $query = [
            "query" => [
                "function_score" => [
                    "query" => [
                        "bool" => [
                            "must" => []
                        ]
                    ],
                    "field_value_factor" => [
                        "field" => "heat",
                        "modifier" => "log1p",
                        "factor" => 0.3
                    ]
                ]
            ],
            "aggs" => [
                "mer_type_list" => [
                    "terms" => [
                        "field" => "mer_type"
                    ]
                ]
            ],
            "from" => $from,
            "size" => $size,
        ];
        // 品类
        if (!empty($param['merType'])) {
            if (is_array($param['merType'])) {
                $merTypeList = array_values($param['merType']);
            } else {
                $merTypeList[] = $param['merType'];
            }
            $query['query']['function_score']['query']['bool']['must'][] = [
                "terms" => [
                    "mer_type" => $merTypeList
                ]
            ];
        }
        // 品牌
        if (!empty($param['brandCode'])) {
            if (is_array($param['brandCode'])) {
                $brandCodeList = array_values($param['brandCode']);
            } else {
                $brandCodeList[] = $param['brandCode'];
            }
            $query['query']['function_score']['query']['bool']['must'][] = [
                "terms" => [
                    "brand_code" => $brandCodeList
                ]
            ];
        }
        // 搜索关键字
        if ($param['keyWord']) {
            $param['keyWord'] = str_replace("年", ' 年', $param['keyWord']);
            $param['keyWord'] = str_replace("寸", ' 寸', $param['keyWord']);
            $query['query']['function_score']['query']['bool']['must'][] = [
                "match" => [
                    "keywords.ik_max_word" => [
                        "query" => $param['keyWord'],
                        "operator" => "and",  // 全匹配
                        //"minimum_should_match" => "2", // 至少包含几个
                    ]
                ]
            ];
        }
        $sql = [
            'index' => $this->index . $channel,
            'type' => $this->type,
            'body' => $query
        ];
        $es = new ElasticSearchService();
        $res = $es->searchDoc($sql);
        $searchList = $res['hits']['hits'];
        $products = array();
        $domain = env('image.domain');
        $thumbs = env('image.thumb');
        $noPic = env('image.nopic');
        foreach ($searchList as $val) {
            $source = $val['_source'];
            $products[] = [
                'mer_id' => $source['mer_id'],
                'brand_name' => $source['brand_name'],
                'mer_name' => $source['mer_name'],
                'nickname' => $source['nickname'],
                'mer_img' => empty($source['mer_img']) ? $domain . $noPic : $domain . $thumbs . $source['mer_img'],
                'image' => empty($source['mer_img'])? '':$source['mer_img'],
                'max_quote' => $source['max_quote'],
                'keywords' => $source['keywords'],
                'mer_type' => $source['mer_type'],
                'brand_code' => $source['brand_code'],
                'partner_code' => $source['partner_code'],
                'heat' => $source['heat'],
            ];
        }
        $result['total'] = $res['hits']['total'];
        $result['metTypeList'] = $res['aggregations']['mer_type_list']['buckets'];
        $result['products'] = $products;
        return $result;
    }

    /**
     * 功能描述： 品类品牌搜索
     * @createDate 2020/6/16 11:28
     * @param $merType 品类
     * @param $brandCode 品牌
     * @param $channel 渠道
     * @param $from   开始
     * @param $limit  分页大小
     * @return mixed
     * @throws ParameterException
     * @author panzf
     */
    public function brandProduct($merType, $brandCode, $channel, $from = 0, $limit = 20)
    {
        if (empty($merType) && empty($brandCode)) {
            throw  new ParameterException(['msg' => '品类或者品牌都不能为空']);
        }
        $query = [
            "query" => [
                "bool" => [
                ]
            ]
        ];
        // 品类
        if (!empty($merType)) {
            if (is_array($merType)) {
                $merTypeList = array_values($merType);
            } else {
                $merTypeList[] = $merType;
            }
            $query['query']['bool']['must'][] = [
                "terms" => [
                    "mer_type" => $merTypeList
                ]
            ];
        }
        // 品牌
        if (!empty($brandCode)) {
            if (is_array($brandCode)) {
                $brandCodeList = array_values($brandCode);
            } else {
                $brandCodeList[] = $brandCode;
            }
            $query['query']['bool']['must'][] = [
                "terms" => [
                    "brand_code" => $brandCodeList
                ]
            ];
        }
        $query['sort'] = ['heat' => ['order' => 'desc']];
        $query['from'] = $from;
        $query['size'] = $limit;
        $params = [
            'index' => $this->index . $channel,
            'type' => $this->type,
            'body' => $query
        ];

        $es = new ElasticSearchService();
        $res = $es->searchDoc($params);
        $searchList = $res['hits']['hits'];
        $products = array();
        $domain = env('image.domain');
        $thumbs = env('image.thumb');
        $noPic = env('image.nopic');
        foreach ($searchList as $val) {
            $source = $val['_source'];
            $products[] = [
                'mer_id' => $source['mer_id'],
                'brand_name' => $source['brand_name'],
                'mer_name' => $source['mer_name'],
                'nickname' => $source['nickname'],
                'mer_img' => empty($source['mer_img']) ? $domain . $noPic : $domain . $thumbs . $source['mer_img'],
                'image' => empty($source['mer_img'])? '':$source['mer_img'],
                'max_quote' => $source['max_quote'],
                'keywords' => $source['keywords'],
                'mer_type' => $source['mer_type'],
                'brand_code' => $source['brand_code'],
                'partner_code' => $source['partner_code'],
                'heat' => $source['heat'],
            ];
        }
        $result['total'] = $res['hits']['total'];
        $result['products'] = $products;
        return $result;
    }

    /**
     * 功能描述：同步商品
     * @createDate 2020/6/17 13:51
     * @param $channel
     * @param int $limit
     * @return int
     * @throws CreateIndexException
     * @throws \app\api\exception\IndexBeingException
     * @author panzf
     */
    public function syncSourceProduct($channel, $limit = 1000)
    {
        set_time_limit(0);
        $es = new ElasticSearchService();
        // 验证索引是否存在
        if (!$es->checkIndexExists(['index' => $this->index . $channel])) {
            if (!$this->addChannelIndex($channel)) {
                throw new CreateIndexException('创建索引失败');
            }
        }
        $updateNum = 0;
        $productModel = new AllProductModel();
        $productList = $productModel->getProductByChannel($channel);
        $params['index'] = $this->index . $channel;
        $params['type'] = $this->type;
        $i = 0;
        $flag = false;
        $replace_str = array("\r\n", "\n", "\r");
        $replace = '';
        foreach ($productList as $product) {
            $flag = true;
            $i++;
            $params['body'][] = [
                'index' => [
                    '_id' => $product['MERID']
                ]
            ];
            $info['mer_id'] = $product['MERID'];
            $info['brand_name'] = $product['PNAME'];
            $info['mer_name'] = $product['MERNAME'];
            $info['nickname'] = $product['NICKNAME'];
            $info['mer_img'] = $product['MERIMG'];
            $info['max_quote'] = $product['MAXQUOTE'];
            $info['brand_code'] = $product['BRANDCODE'];
            $info['mer_type'] = $product['MERTYPE'];
            $info['partner_code'] = $product['PARTNERCODE'];
            $info['keywords'] = str_replace($replace_str, $replace, $product['KEYWORDS']);
            $info['heat'] = $product['REDU'] * 1;
            $params['body'][] = $info;
            if ($i % $limit === 0) {
                if ($es->bulkDoc($params)) {
                    $updateNum = $i;
                    $flag = false;
                    $params['body'] = [];
                } else {
                    $params['body'] = [];
                }
            }
        }
        if ($flag) {
            if ($es->bulkDoc($params)) {
                $updateNum = $i;
                $params['body'] = [];
            } else {
                $params['body'] = [];
            }
        }
        return $updateNum;
    }

    /**
     * 功能描述：同步商品(分页)
     * @createDate 2020/6/24 15:09
     * @param $channel
     * @param int $limit
     * @return int
     * @throws CreateIndexException
     * @throws \app\api\exception\IndexBeingException
     * @author panzf
     */
    public function syncSourcePageProduct($channel, $limit = 1000)
    {
        set_time_limit(0);
        $es = new ElasticSearchService();
        // 验证索引是否存在
        if (!$es->checkIndexExists(['index' => $this->index . $channel])) {
            if (!$this->addChannelIndex($channel)) {
                throw new CreateIndexException('创建索引失败');
            }
        }

        $productModel = new AllProductModel();
        $updateNum = 0;
        $page = 1;
        $counts = $productModel->totalCount($channel);
        $totalPage = ceil($counts / $limit);
        $params['index'] = $this->index . $channel;
        $params['type'] = $this->type;
        $i = 0;
        $replace = '';
        $replace_str = array("\r\n", "\n", "\r");

        for ($page; $page <= $totalPage; $page++) {
            $start = ($page - 1) * $limit;
            $end = $page * $limit;
            $productList = $productModel->getPageProductByChannel($channel,$start,$end);
            foreach ($productList as $product) {
                $i++;
                $params['body'][] = [
                    'index' => [
                        '_id' => $product['MERID']
                    ]
                ];
                $info['mer_id'] = $product['MERID'];
                $info['brand_name'] = $product['PNAME'];
                $info['mer_name'] = $product['MERNAME'];
                $info['nickname'] = $product['NICKNAME'];
                $info['mer_img'] = $product['MERIMG'];
                $info['max_quote'] = $product['MAXQUOTE'];
                $info['brand_code'] = $product['BRANDCODE'];
                $info['mer_type'] = $product['MERTYPE'];
                $info['partner_code'] = $product['PARTNERCODE'];
                $info['keywords'] = str_replace($replace_str, $replace, $product['KEYWORDS']);
                $info['heat'] = $product['REDU'] * 1;
                $params['body'][] = $info;
            }
            // 批量写入
            if ($es->bulkDoc($params)) {
                $updateNum += $i;
            }
            $i = 0;
            $params['body'] = [];
        }

        return $updateNum;
    }


    /**
     * 功能描述：单个商品同步
     * @createDate 2020/6/18 15:23
     * @param $id
     * @param $channel
     * @return bool
     * @throws ProductNotExistsException
     * @author panzf
     */
    public function syncOneProduct($id, $channel)
    {
        $productModel = new AllProductModel();
        $productInfo = $productModel->getProductInfo($id, $channel);
        if (empty($productInfo)) {
            throw new ProductNotExistsException();
        }
        $info = [];
        $info['mer_id'] = $productInfo['MERID'];
        $info['brand_name'] = $productInfo['PNAME'];
        $info['mer_name'] = $productInfo['MERNAME'];
        $info['nickname'] = $productInfo['NICKNAME'];
        $info['max_quote'] = $productInfo['MAXQUOTE'];
        $info['brand_code'] = $productInfo['BRANDCODE'];
        $info['brand_code'] = $productInfo['BRANDCODE'];
        $info['mer_type'] = $productInfo['MERTYPE'];
        $info['partner_code'] = $productInfo['PARTNERCODE'];
        $info['keywords'] = $productInfo['KEYWORDS'];
        $info['heat'] = $productInfo['REDU'] * 1;
        // es 是否存
        $esInfo = $this->getDoc($id, $channel);
        if (empty($esInfo)) {
            $result = $this->addDoc($info, $channel);
        } else {
            $result = $this->updateDoc($id, $info, $channel);
        }
        return $result;
    }


    /**
     * 功能描述：更新文档
     * @createDate 2020/6/16 14:07
     * @param $id
     * @param $body
     * @param $channel
     * @return bool
     * @author panzf
     */
    public function updateDoc($id, $body, $channel)
    {
        $params = [
            'index' => $this->index . $channel,
            'type' => $this->type,
            'id' => $id,
            'body' => [
                'doc' => $body
            ]
        ];
        $es = new ElasticSearchService();
        return $es->updateDoc($params);
    }

    /**
     * 功能描述：添加文档
     * @createDate 2020/6/16 14:07
     * @param $body
     * @param $channel
     * @return bool
     * @author panzf
     */
    public function addDoc($body, $channel)
    {
        $params = [
            'index' => $this->index . $channel,
            'type' => $this->type,
            'id' => $body['mer_id'],
            'body' => $body
        ];
        $es = new ElasticSearchService();
        return $es->addDoc($params);
    }

    /**
     * 功能描述：获取单个文档
     * @createDate 2020/6/16 14:06
     * @param $id
     * @param $channel
     * @return array
     * @author panzf
     */
    public function getDoc($id, $channel)
    {
        $info = [];
        $params = [
            'index' => $this->index . $channel,
            'type' => $this->type,
            'id' => $id
        ];
        $es = new ElasticSearchService();
        $result = $es->getDoc($params);
        if (!empty($result) && isset($result['_source'])) {
            $info = $result['_source'];
        }
        return $info;
    }

    /**
     * 功能描述：addChannelIndex
     * @createDate 2020/6/17 13:54
     * @param string $channel
     * @param int $shards
     * @param int $replicas
     * @return array|null
     * @throws CreateIndexException
     * @throws \app\api\exception\IndexBeingException
     * @author panzf
     */
    public function addChannelIndex($channel, $shards = 1, $replicas = 0)
    {
        $type = $this->type;
        $params = [
            'index' => $this->index . $channel, //索引名称
            'body' => [
                "settings" => [
                    "number_of_shards" => $shards,
                    "number_of_replicas" => $replicas,
                    "analysis" => [
                        "analyzer" => [
                            "ik_max_word_analyzer" => [
                                "type" => "custom",
                                "tokenizer" => "ik_max_word",
                                "filter" => ["lowercase",],
                            ],
                            "ik_smart_analyzer" => [
                                "type" => "custom",
                                "tokenizer" => "ik_smart",
                                "filter" => ["lowercase",],
                            ],
                        ]
                    ]
                ],
                "mappings" => [
                    $type => [
                        'dynamic' => 'strict',
                        'properties' => [
                            'mer_id' => [
                                'type' => 'keyword',
                            ],
                            'brand_name' => [
                                'type' => 'keyword',
                            ],
                            'mer_name' => [
                                'type' => 'keyword',
                            ],
                            'nickname' => [
                                'type' => 'keyword',
                            ],
                            'mer_img' => [
                                'type' => 'keyword',
                            ],
                            'max_quote' => [
                                'type' => 'integer',
                            ],
                            'brand_code' => [
                                'type' => 'keyword',
                            ],
                            'mer_type' => [
                                'type' => 'keyword',
                            ],
                            'partner_code' => [
                                'type' => 'keyword',
                            ],
                            "keywords" => [
                                "type" => "text",
                                "fields" => [
                                    "ik_max_word" => [
                                        "type" => "text",
                                        "analyzer" => "ik_max_word_analyzer",// es 字段 分词
                                        "search_analyzer" => "ik_smart_analyzer" // 搜索关键字分词
                                    ],
                                ],
                            ],
                            'heat' => [
                                'type' => 'integer',
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $es = new ElasticSearchService();

        return $es->addIndex($params);
    }

    /**
     * 功能描述：删除索引
     * @createDate 2020/6/12 13:14
     * @param string $index
     * @param string $channel
     * @return bool
     * @author panzf
     */
    public function delIndex($channel, $index = '')
    {
        $result = false;
        if (empty($index)) {
            $index = $this->index . $channel;
        }
        $params = [
            'index' => $index,
        ];
        $es = new ElasticSearchService();
        if ($es->checkIndexExists($params)) {
            $result = $es->delIndex($params);
        }
        return $result;
    }

    /**
     * 功能描述：删除文档
     * @createDate 2020/6/17 11:10
     * @param $id
     * @param string $channel
     * @return bool
     * @author panzf
     */
    public function delDoc($id, $channel)
    {
        $params = [
            'index' => $this->index . $channel,
            'type' => $this->type,
            'id' => $id
        ];
        $es = new ElasticSearchService();
        return $es->delDoc($params);
    }

    /**
     * 功能描述：批量条件删除 渠道
     * @createDate 2020/6/18 14:23
     * @param $channel
     * @return bool
     * @author panzf
     */
    public function deleteByQuery($channel)
    {
        $params = [
            'index' => $this->index . $channel,
            'type' => $this->type,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            "term" => [
                                "partner_code" => $channel
                            ]
                        ]
                    ]
                ]
            ]
        ];
        $es = new ElasticSearchService();
        return $es->deleteByQuery($params);
    }

}