<?php
/**
 * 类目
 * @author Major
 *
 */
class Cate
{
    private $objMysql;
    
    public function __construct(){
        //$this->objMysql=  new MysqlDb($host = 'bdm123604245.my3w.com',$username = 'bdm123604245',$pass='goodpenyou2015',$database = "bdm123604245_db");
        $this->objMysql=  new MysqlDb($host = 'bdm123604245.my3w.com',$username = 'bdm123604245',$pass='goodpenyou2015',$database = "bdm123604245_db");
    }
    
    /**
     * 获取有料信息
     */
    public function  getYouLiaoInfo(  ){
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        $strSql = "select * from YK_Category  A left join YK_Category  B on A.id = B.parent_id  where A.id=1";
        $q      = $this->objMysql->query($strSql);
        while ( !! $result = $this->objMysql->fetch_assoc($q) ){
            $ret['cate'][] = $result;
        }
        $strSql1 = "select img_one,img_two,img_three from YK_CategoryInfo where cate_id=1";
        $q1      = $this->objMysql->query($strSql1);
        $resTmp  = $this->objMysql->fetch_assoc($q1);
        foreach ($resTmp as $key => $val ){
            $ret['pic'][] =$val;
        }
        if( $ret ){
            return array('status'=>'1','msg'=>'ok',"data"=>$ret); exit;
        }else{
            return $arrRet;
        }
    }
    
    /**
     * 获取二级菜单视频数据
     */
    public function getErjiInfo($arrParam){
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        $arrParam['cid']= isset($arrParam['cid'])?$arrParam['cid']:0;
        if ($arrParam['cid']<4){
            return array('status'=>'-1','msg'=>'参数错误',"data"=>''); exit;
        }
        $arrParam['bepose'] = isset($arrParam['bepose'])?$arrParam['bepose']:0;
        $arrParam['bepose'] = $arrParam['bepose']*10;
        $arrParam['length'] = isset($arrParam['length'])?$arrParam['length']:10;
        $strSql = "select * from YK_Videosort  A left join YK_Video  B on A.video_id = B.id where A.cate_id = ".$arrParam['cid']."  order by  create_time  desc  limit ".$arrParam['bepose'].",".$arrParam['length'];
        $q      = $this->objMysql->query($strSql);
        while ( !! $result = $this->objMysql->fetch_assoc($q) ){
            $ret[] = $result;
        }
         if(empty($ret)){
            return array('status'=>'1','msg'=>'暂无数据',"data"=>$ret); exit;
        }
        $strSql1  = "select count(*) as num from YK_Videosort  A left join YK_Video  B on A.video_id = B.id where A.cate_id = 4  ";
        $q1       = $this->objMysql->query($strSql1);
        $result1  = $this->objMysql->fetch_assoc($q1);
        foreach ( $ret  as $key => $val ){
            $strSql = "select user_id,nick_name,phone,status,source create_time,identifier,mechine_type,head_pic,backgroud,ischeck,slogan,gender as nums from YK_User  where user_id='".$val['owner_id']."'";
            $q      = $this->objMysql->query($strSql);
            $result = $this->objMysql->fetch_assoc($q);
            $ret[$key]['ownerInfo'] = $result; 
        }
        return array('status'=>'1','msg'=>'ok','data'=>$ret,'total'=>$result1['num']); exit;
    }
    /**
     * 获取同城数据
     */
    public function  getTongchengInfo( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        $arrParam['bepose'] = isset($arrParam['bepose'])?$arrParam['bepose']:0;
        $arrParam['bepose'] = $arrParam['bepose']*10;
        $arrParam['length'] = isset($arrParam['length'])?$arrParam['length']:10;
        $arrParam['uid']    = isset( $arrParam['uid'] ) ? $arrParam['uid']:0;
        if( $arrParam['uid'] == 0 ){
            return array('status'=>'-1','msg'=>'参数错误',"data"=>$ret); exit;
        }
        $strSql = "select * from YK_Video where video_area='".$arrParam['video_area']."' and owner_id != '".$arrParam['uid']."' order by  create_time limit ".$arrParam['bepose'].",".$arrParam['length'];
        $q      = $this->objMysql->query($strSql);
        while ( !! $result = $this->objMysql->fetch_assoc($q) ){
            $ret[] = $result;
        }
        if(empty($ret)){
            return array('status'=>'1','msg'=>'暂无数据',"data"=>$ret); exit;
        }
        $strSql10  = "select count(*) as num from YK_Video where video_area='".$arrParam['video_area']."' and owner_id != ".$arrParam['uid'];
        $q10       = $this->objMysql->query($strSql10);
        $result10  = $this->objMysql->fetch_assoc($q10);
        foreach ( $ret  as $key => $val ){
            $strSql = "select user_id,nick_name,phone,status,source create_time,identifier,mechine_type,head_pic,backgroud,ischeck,slogan,gender as nums from YK_User  where user_id='".$val['owner_id']."'";
            $q      = $this->objMysql->query($strSql);
            $result = $this->objMysql->fetch_assoc($q);
            $ret[$key]['ownerInfo'] = $result; 
            $strSql1 = "select count(*) as penNum from YK_VideoFocus where video_id=".$val['id'];
            $q1      = $this->objMysql->query($strSql1);
            $result1 = $this->objMysql->fetch_assoc($q1);
            $ret[$key]['penNum'] = $result1['penNum'];
            $strSql2 = "select count(*) as pinglunNum from YK_Comment where videoId=".$val['id'];
            $q2      = $this->objMysql->query($strSql2);
            $result2 = $this->objMysql->fetch_assoc($q2);
            $ret[$key]['pinglunNum'] = $result2['pinglunNum'];
            $strSql3 = "select count(*) as isfocus from YK_VideoFocus where video_id=".$val['id']." and follower_id=".$arrParam['uid'];
            $q3      = $this->objMysql->query($strSql3);
            $result3 = $this->objMysql->fetch_assoc($q3);
            $ret[$key]['isfocus'] = intval($result3['isfocus']);
            
        }
        return array('status'=>'1','msg'=>'ok','data'=>$ret,'total'=>$result10['num']); exit;
    }
    
    /**
     * 获取朋友圈视频
     */
    public function getFriendArea($arrParam){
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        if(!isset($arrParam['uid'])){
            return $arrRet;exit;
        }
        $arrParam['bepose'] = isset($arrParam['bepose'])?$arrParam['bepose']:0;
        $arrParam['bepose'] = $arrParam['bepose']*10;
        $arrParam['length'] = isset($arrParam['length'])?$arrParam['length']:10;
        $strSql = "select B.user_id as friends_id,B.head_pic,B.backgroud,B.mechine_type,B.source,B.phone,B.nick_name,B.slogan,B.gender,A.to_id from YK_Friend A INNER JOIN YK_User B on A.to_id = B.user_id WHERE A.status=1 and A.from_id=".$arrParam['uid']." limit ".$arrParam['bepose'].",".$arrParam['length'] ;
        $q      = $this->objMysql->query($strSql);
        while( !!$result = $this->objMysql->fetch_assoc($q)){
            $ret[] = $result;
        }
        $strSql11 = "select count(*) as total from YK_Friend A INNER JOIN YK_User B on A.to_id = B.user_id WHERE A.status=1 and A.from_id=".$arrParam['uid'] ;
        $q11      = $this->objMysql->query($strSql11);
        $resultTotal = $this->objMysql->fetch_assoc($q11);
        if(empty($ret)){
            return array('status'=>'1','msg'=>'暂无数据',"data"=>$ret); exit;
        } 
        foreach ( $ret  as $key => $val ){
            $strSql = "select * from YK_Video  where owner_id = '".$val['to_id']."' order by create_time desc limit 1";
            $q      = $this->objMysql->query($strSql);
            $result = $this->objMysql->fetch_assoc($q);
            if(empty($result)){
                $ret[$key]['videoInfo']= "";
                $ret[$key]['commentInfo'] = "";
            }else{
                $ret[$key]['videoInfo'] = $result;
                $strSql1 = "select A.*,B.nick_name from YK_Comment A left join YK_User B on A.userId = B.user_id where A.videoId = ".$result['id']." limit 3";
                $q1     = $this->objMysql->query($strSql1);
                while (!! $result1 = $this->objMysql->fetch_assoc($q1)){
                    $ret[$key]['commentInfo'][] = $result1;
                } 
                $strSql2 = "select count(*) pinlunNum from YK_Comment where videoId = ".$result['id']." limit 3";
                $q2     = $this->objMysql->query($strSql2);
                $result2 = $this->objMysql->fetch_assoc($q2);
                $ret[$key]['videoInfo']['pinlunNum'] = intval($result2['pinlunNum']);
                
                $strSql3 = "select count(*) penNum from YK_VideoFocus where video_id = ".$result['id']." limit 3";
                $q3     = $this->objMysql->query($strSql3);
                $result3 = $this->objMysql->fetch_assoc($q3);
                $ret[$key]['videoInfo']['penNum'] = intval($result3['penNum']);
            }
            
        }
        return array('status'=>'1','msg'=>'ok','data'=>$ret,'total'=>$resultTotal['total']); exit;
    }
    
    public function getFaxianInfo(){
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        $arrParam['bepose'] = isset($arrParam['bepose'])?$arrParam['bepose']:0;
        $arrParam['bepose'] = $arrParam['bepose']*10;
        $arrParam['length'] = isset($arrParam['length'])?$arrParam['length']:10;
        $strSql = "select B.* from YK_Videosort A left join YK_Video B  on A.video_id = B.id where A.cate_id=2 limit ".$arrParam['bepose'].",".$arrParam['length'];
        $q      = $this->objMysql->query($strSql);
        while ( !! $result = $this->objMysql->fetch_assoc($q) ){
            $ret[] = $result;
        }
        if(empty($ret)){
            return array('status'=>'1','msg'=>'暂无数据',"data"=>$ret); exit;
        }
        $strSql10  = "select count(0) as num from YK_Videosort A left join YK_Video B  on A.video_id = B.id where C.cate_id=2";
        $q10       = $this->objMysql->query($strSql10);
        $result10  = @$this->objMysql->fetch_assoc($q10);
        foreach ( $ret  as $key => $val ){
            $strSql = "select user_id,nick_name,phone,status,source create_time,identifier,mechine_type,head_pic,backgroud,ischeck,slogan,gender as nums from YK_User  where user_id='".$val['owner_id']."'";
            $q      = $this->objMysql->query($strSql);
            $result = $this->objMysql->fetch_assoc($q);
            $ret[$key]['ownerInfo'] = $result;
            $strSql1 = "select count(*) as penNum from YK_VideoFocus where video_id=".$val['id'];
            $q1      = $this->objMysql->query($strSql1);
            $result1 = $this->objMysql->fetch_assoc($q1);
            $ret[$key]['penNum'] = $result1['penNum'];
            $strSql2 = "select count(*) as pinglunNum from YK_Comment where videoId=".$val['id'];
            $q2      = $this->objMysql->query($strSql2);
            $result2 = $this->objMysql->fetch_assoc($q2);
            $ret[$key]['pinglunNum'] = $result2['pinglunNum'];
            $strSql3 = "select count(*) as isfocus from YK_VideoFocus where video_id=".$val['id']." and follower_id=".$arrParam['uid'];
            $q3      = $this->objMysql->query($strSql3);
            $result3 = $this->objMysql->fetch_assoc($q3);
            $ret[$key]['isfocus'] = intval($result3['isfocus']);
        }
        return array('status'=>'1','msg'=>'ok','data'=>$ret,'total'=>$result10['num']); exit;
    }
}