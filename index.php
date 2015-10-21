<?php
error_reporting(0);
require_once './common.php';
require_once './tools/tools.class.php';
header('Content-type:text/json');
//获取数据
$arrParam = $_POST;
// $arrParam['method'] = 'getFaxianInfo';
// $arrParam['uid']  = '2';
// $arrParam['video_area']   = '北京';
// $arrParam['length']  = 10;
// $arrParam['bepose']  = 1;
// $arrParam['vid']  =1;
// $arrParam['pass']   = 'e9c78b115fdef2bddfee90c59d3a85ed';
// $arrParam['head_pic']   = 'head.jpg';

// $arrParam['identifier']   = '11111111111111111';

//传过来的token  验证安全性
/*$token    = $arrParam['token'];
$yzToken  = check();

if( $token != $yzToken ){
    Tools::responseData('-1', '请求非法');exit;
}*/

$method   = isset($arrParam['method']) ? $arrParam['method']:'';
//验证方法名是否存在
if( empty($method) ){
    Tools::responseData('-1', 'fail');exit;
}
unset($arrParam['method']);
//unset($arrParam['token']);
//请求类名
$className = $arrMethod[$method];

$instantName = '$obj'.$className;

$instantName = new $className();
$result      = $instantName->$method($arrParam);
if( isset( $result['status'] ) ){
    Tools::responseData($result['status'], $result['msg'],$result['data'],$result['total']); exit;
}else{
    Tools::responseData(-9999, 'fail');
}


//返回验证appSecret
function check($appSecret){
    $times    = date('Y-m-d');
    $appSecret= md5(md5('haopenyou'.$times.'$%#*').'apppassword');
    return $appSecret;
}



