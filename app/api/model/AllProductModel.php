<?php


namespace app\api\model;


use app\common\model\BaseOracleModel;

class AllProductModel extends BaseOracleModel
{

    /**
     * 功能描述：渠道商品
     * @createDate 2020/6/17 13:51
     * @param $channel
     * @return mixed
     * @author panzf
     */
    public function getProductByChannel($channel)
    {
        $sql = "  select 
                        t1.merid,
                        t2.pname,
                        t1.mername,
                        t1.nickname,
                        t1.merimg,
                        nvl(t1.maxquote, 0) maxquote,
                        t1.keywords,
                        t1.redu,
                        t1.brandcode,
                        t1.mertype,
                        t3.partnercode
                  from goods t1 
                  left join brands t2 on t1.brandcode = t2.pcode
                  left join channel_brands t3 on t1.brandcode = t3.brandcode
                  where     
                        t1.mersource = '1' 
                        and t1.enabled = 'Y' 
                        and t1.delflag = 'N' 
                        and t3.partnercode = :partnerCode ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind('partnerCode', $channel);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * 功能描述：总数
     * @createDate 2020/6/24 14:52
     * @param $channel
     * @return mixed
     * @author panzf
     */
    public function totalCount($channel)
    {
        $sql = "  select 
                        count(t1.merid) as counts
                  from goods t1 
                  left join brands t2 on t1.brandcode = t2.pcode
                  left join channel_brands t3 on t1.brandcode = t3.brandcode
                  where     
                        t1.mersource = '1' 
                        and t1.enabled = 'Y' 
                        and t1.delflag = 'N' 
                        and t3.partnercode = :partnerCode ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind('partnerCode', $channel);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['COUNTS'];
    }

    /**
     * 功能描述：商品分页数据
     * @createDate 2020/6/24 14:59
     * @param $channel
     * @param $start
     * @param $end
     * @return mixed
     * @author panzf
     */
    public function getPageProductByChannel($channel,$start,$end)
    {
        $sql = " select * from( select rownum r, t4.* from (  
                     select 
                            t1.merid,
                            t2.pname,
                            t1.mername,
                            t1.nickname,
                            t1.merimg,
                            nvl(t1.maxquote, 0) maxquote,
                            t1.keywords,
                            t1.redu,
                            t1.brandcode,
                            t1.mertype,
                            t3.partnercode
                      from goods t1 
                      left join brands t2 on t1.brandcode = t2.pcode
                      left join channel_brands t3 on t1.brandcode = t3.brandcode
                      where     
                            t1.mersource = '1' 
                            and t1.enabled = 'Y' 
                            and t1.delflag = 'N' 
                            and t3.partnercode = :partnerCode 
                 ) t4 ) t5 where t5.r > :startSeat and t5.r<= :endSeat 
         ";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('partnerCode', $channel);
        $stmt->bindParam("startSeat", $start);
        $stmt->bindParam("endSeat", $end);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * 功能描述：商品详情
     * @createDate 2020/6/18 15:16
     * @param $id
     * @param $channel
     * @return mixed
     * @author panzf
     */
    public function getProductInfo($id,$channel)
    {
        $sql = "  select 
                        t1.merid,
                        t2.pname,
                        t1.mername,
                        t1.nickname,
                        t1.merimg,
                        nvl(t1.maxquote, 0) maxquote,
                        t1.keywords,
                        t1.redu,
                        t1.brandcode,
                        t1.mertype,
                        t3.partnercode
                  from goods t1 left join brands t2 on t1.brandcode = t2.pcode
                  left join channel_brands t3 on t1.brandcode = t3.brandcode
                  where     
                        t1.mersource = '1' 
                        and t1.enabled = 'Y' 
                        and t1.delflag = 'N' 
                        and t1.merid = :id and t3.partnercode = :partnerCode ";
        $stmt = $this->db->prepare($sql);
        $stmt->bind('partnerCode', $channel);
        $stmt->bind('id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }
    
}