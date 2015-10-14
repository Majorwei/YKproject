<?php
class Area
{
    private $objMysql;
    public $objMsg;
    
    public function __construct(){
        //$this->objMysql=  new MysqlDb($host = 'bdm123604245.my3w.com',$username = 'bdm123604245',$pass='goodpenyou2015',$database = "bdm123604245_db");
        $this->objMysql=  new MysqlDb($host = 'bdm123604245.my3w.com',$username = 'bdm123604245',$pass='goodpenyou2015',$database = "bdm123604245_db");
    }
    
    /**
     * 获取app状态
     */
    public function getAppInfo( $appid = 1 ){
    	$strSql = "select * from YK_App where id=$appid ";
    	$q      = $this->objMysql->query($strSql);
    	$result = $this->objMysql->fetch_assoc($q);
    	if ($result !== false ){
    		return array('status'=>'1','msg'=>'ok',"data"=>$result); exit;
    	}else{
    		return array('status'=>'-1','msg'=>'fail',"data"=>array()); exit;
    	}
    }
    
}