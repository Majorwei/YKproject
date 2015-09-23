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
     * 获取省份列表
     * @param unknown $arrParam
     * @return multitype:string |multitype:string multitype:
     */
    public function getProviceList($arrParam){
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        $strSql = "SELECT * FROM YK_Province ";
        $q      = $this->objMysql->query($strSql);
        while( !! $result = $this->objMysql->fetch_assoc($q)){
            //$result['ProvinceName'] = mb_convert_encoding($result['ProvinceName'], 'utf8','gbk');
            $ret[] = $result;
        }
        if($ret){
            return array( "status"=>"1","msg"=>"ok","data"=>$ret); exit;
        }else{
            return $arrRet;exit;
        }
    }
    //根据省份ID获取城市列表
    public function getCityList( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        $strSql = "SELECT * FROM YK_City where ProvinceID = ".$arrParam['pid'];
        $q      = $this->objMysql->query($strSql);
        while( !! $result = $this->objMysql->fetch_assoc($q)){
            //$result['CityName'] = mb_convert_encoding($result['CityName'], 'utf8','gbk');
            $ret[] = $result;
        }
        if($ret){
            return array( "status"=>"1","msg"=>"ok","data"=>$ret); exit;
        }else{
            return $arrRet;exit;
        }
    }
    //根据城市ID获取地区列表
    public function getZoneList( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        $strSql = "SELECT * FROM YK_District where CityID = ".$arrParam['cid'];
        $q      = $this->objMysql->query($strSql);
        var_dump($q);exit;
        while( !! $result = $this->objMysql->fetch_assoc($q)){
            //$result['DistrictName'] = mb_convert_encoding($result['DistrictName'], 'utf8','gbk');
            $ret[] = $result;
        }
        if($ret){
            return array( "status"=>"1","msg"=>"ok","data"=>$ret); exit;
        }else{
            return $arrRet;exit;
        }
    }
    
    /**
     * 根据省份名称获取省份ID
     */
    public function getProvinceInfo( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        $strSql = "select * from YK_Province where ProvinceName like '%".$arrParam['name']."%'";
        $q      = $this->objMysql->query($strSql);
        $result = $this->objMysql->fetch_assoc($q);
        //$result['ProvinceName'] = mb_convert_encoding($result['ProvinceName'], 'utf8','gbk');
        if($result){
            return array( "status"=>"1","msg"=>"ok","data"=>$result); exit;
        }else{
            return $arrRet;
        }
    }
    
}