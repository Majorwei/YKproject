<?php
class Video
{
    private $objMysql;
    
    public function __construct(){
        //$this->objMysql=  new MysqlDb($host = 'bdm123604245.my3w.com',$username = 'bdm123604245',$pass='goodpenyou2015',$database = "bdm123604245_db");
        $this->objMysql=  new MysqlDb($host = 'bdm123604245.my3w.com',$username = 'bdm123604245',$pass='goodpenyou2015',$database = "bdm123604245_db");
    }
    /**
     * 获取我的视频列表
     * @param array $arrParam
     * @return array
     */
    public function getMyVideo( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        $arrParam['field'] = empty($arrParam['field']) ? 'id':$arrParam['field'];
        $arrParam['bepose'] = empty($arrParam['bepose']) ? 0:$arrParam['bepose'];
        $arrParam['bepose'] = $arrParam['bepose']*10;
        $arrParam['length'] = empty($arrParam['length']) ? 10:$arrParam['length'];
        $strSql = "select *,B.nick_name from YK_Video A left join YK_User B on A.owner_id = B.user_id where A.owner_id=".$arrParam['uid']." order by A.create_time desc limit ".$arrParam['bepose'].",".$arrParam['length'];                     
        $q      = $this->objMysql->query($strSql);
        while ( !! $res = $this->objMysql->fetch_assoc($q) ){
            $result[] = $res;
        }
        $numSql = "select count(*) as total from YK_Video where owner_id=".$arrParam['uid'];
        $numq   = $this->objMysql->query($numSql);
        $numRes = $this->objMysql->fetch_assoc($numq);
        foreach ( $result as $key=> $val ){
            $strSql1 = "select count(*) as penNum from YK_VideoFocus  where video_id = ".$val['id'];
            $q1      = $this->objMysql->query($strSql1);
            $r1      = $this->objMysql->fetch_assoc($q1);
            $result[$key]['penNum'] = $r1['penNum'];
            $strSql1 = "select count(*) as pinNum from YK_Comment  where videoId = ".$val['id'];
            $q1      = $this->objMysql->query($strSql1);
            $r1      = $this->objMysql->fetch_assoc($q1);
            $result[$key]['pinNum'] = $r1['pinNum'];
        }
        if(!empty($result) ){
           return array( 'status'=>'1','msg'=>'ok','data'=>$result ,'total'=>$numRes['total']);
        }else{
            return $arrRet;
        }
        
        
    }
    
    /**
     * 保存视频
     */
    public function addVideo( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        $strSql = "insert into YK_Video (video_name,video_address,video_desc,video_area,video_type,owner_id) values ('".$arrParam['video_name']."','".$arrParam['video_address']."','".$arrParam['video_desc']."','".$arrParam['video_area']."','".$arrParam['video_type']."','".$arrParam['uid']."')";
        $result = $this->objMysql->query($strSql);
        if( $result ){
            return array('status'=>'1','msg'=>'ok',"data"=>array()); exit;
        }else{
            return $arrRet;exit;
        }
    }
    /**
     * 根据视频ID获取视频详情
     */
    public function getVideoDetail( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        $strSql = "select * from YK_Video where id=".$arrParam['vid'];
        $q      = $this->objMysql->query($strSql);
        $result = $this->objMysql->fetch_assoc($q);
        if($result){
            return array( 'status'=>'1','msg'=>'ok','data'=>$result );
        }else{
            return $arrRet;
        }
        
    }
    
    /**
     * 评论内容及条数
     */
    public function getVideoComment( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        $numSql = "select count(*) as total from YK_Comment where videoId=".$arrParam['vid'];
        $numq   = $this->objMysql->query($numSql);
        $numRes = $this->objMysql->fetch_assoc($numq);
        
        $arrParam['field'] = empty($arrParam['field']) ? 'CommentId':$arrParam['field'];
        $arrParam['orderby'] = empty($arrParam['orderby']) ? 'desc':$arrParam['orderby'];
        $arrParam['bepose'] = empty($arrParam['bepose']) ? 0:$arrParam['bepose'];
        $arrParam['bepose'] = $arrParam['bepose']*10;
        $arrParam['length'] = empty($arrParam['length']) ? 10:$arrParam['length'];
        $strSql = "select * from YK_Comment where videoId=".$arrParam['vid']." order by ".$arrParam['field']." ".$arrParam['orderby']." limit ".$arrParam['bepose'].",".$arrParam['length'];
        $q      = $this->objMysql->query($strSql);
        while ( !! $res = $this->objMysql->fetch_assoc($q) ){
            $result[] = $res;
        }
        
        if($result){
            return array( 'status'=>'1','msg'=>'ok','data'=>$result ,'total'=>$numRes['total']);
        }else{
            return $arrRet;
        }
    }
    
    /**
     * 根据视频ID获取视频的关注数
     */
    public function getVideoFocus( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        $numSql = "select count(*) as nums from YK_VideoFocus where video_id=".$arrParam['vid'];
        $numq   = $this->objMysql->query($numSql);
        $numRes = $this->objMysql->fetch_assoc($numq);
        if($numRes ){
            return array( 'status'=>'1','msg'=>'ok','data'=>$numRes ,'total'=>$numRes['total']);
        }else{
            return $arrRet;
        }
    }
    
    /**
     * 获取视频评论列表
     */
    public function getCommentList($arrParam){
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        $numSql = "select A.*,B.head_pic,B.backgroud,B.mechine_type,B.source,B.phone,B.nick_name,B.slogan,B.gender from YK_Comment A inner join YK_User B  on A.userId = B.user_id where videoId=".$arrParam['vid'];
        $numq   = $this->objMysql->query($numSql);
        while( !!$numRes = $this->objMysql->fetch_assoc($numq)){
            $ret[] = $numRes;
        }
        if( $ret ){
            return array( 'status'=>'1','msg'=>'ok','data'=>$ret);
        }else{
            return $arrRet;
        }
    }
    
    /**
     * 搜索视频
     */
    public function searchVideoList($arrParam){
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        $numSql = "select A.*,B.head_pic,B.backgroud,B.mechine_type,B.source,B.phone,B.nick_name,B.slogan,B.gender from YK_Video A left join YK_User B on A.owner_id = B.user_id  where name like '%".$arrParam['name']."%'";
        $numq   = $this->objMysql->query($numSql);
        while( !!$numRes = $this->objMysql->fetch_assoc($numq)){
            $ret[] = $numRes;
        }
        if( $ret ){
            return array( 'status'=>'1','msg'=>'ok','data'=>$ret);
        }else{
            return $arrRet;
        }
    }
    
    /**
     * 评论视频
     */
    public  function commentVideo( $arrParam ){
        date_default_timezone_set('Asia/Shanghai');
        $arrRet = array( 'status'=>'-1','msg'=>'fail','data'=>array() );
        $createTime = date('Y-m-d H:i:s');
        if( !isset($arrParam['uid']) || empty( $arrParam['uid'] ) ){
            return array( 'status'=>'-1','msg'=>'参数错误','data'=>array() ); exit;
        }
        if( !isset($arrParam['vid']) || empty($arrParam['vid']) ){
            return array( 'status'=>'-1','msg'=>'参数错误','data'=>array() ); exit;
        }
        $strSql = "insert into YK_Comment (userId,commentDate,content,videoId) values ('".$arrParam['uid']."','".$createTime."','".$arrParam['content']."','".$arrParam['vid']."')";
        $result  = $this->objMysql->query($strSql);
        if($result){
            return array( 'status'=>'1','msg'=>'评论成功','data'=>array() ); exit; 
        }else{
            return array( 'status'=>'-1','msg'=>'评论失败','data'=>array() ); exit;
        }
    }
    
   /**
    * 关注视频
    */ 
    public function followVideo($arrParam){
        if(!isset($arrParam['uid']) ||  empty($arrParam['uid'])){
            return array( 'status'=>'-1','msg'=>'参数错误,未登录','data'=>array() ); exit;
        }
        
        if(!isset($arrParam['vid']) ||  empty($arrParam['vid'])){
            return array( 'status'=>'-1','msg'=>'参数错误,视频ID不存在','data'=>array() ); exit;
        }
        
        $numSql = "select count(*) as num from YK_VideoFocus where video_id='".$arrParam['vid']."' and follower_id = ".$arrParam['uid'];
        $numq   = $this->objMysql->query($numSql);
        $numRes = $this->objMysql->fetch_assoc($numq);
        if( $numRes['num'] >0 ){
            return array( 'status'=>'-1','msg'=>'对不起，您点过赞了','data'=>array() ); exit;
        }
        
        $createTime = time();
        $strSql = "insert into YK_VideoFocus (video_id,follower_id,create_time,update_time) values ('".$arrParam['vid']."','".$arrParam['uid']."','".$createTime."','".$createTime."')";
        $result  = $this->objMysql->query($strSql);
        if($result){
            return array( 'status'=>'1','msg'=>'点赞成功','data'=>array() ); exit;
        }else{
            return array( 'status'=>'-1','msg'=>'点赞失败失败','data'=>array() ); exit;
        }
        
    }
   
    
    
    
}