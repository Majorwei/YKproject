<?php
/**
 * @desc       需要加载的公共文件
 * @author     Major
 * @date       2015-06-20 23:06:45
 */

//定义根路径
define("BASE_DIR", dirname(DIR__FILE__).'/' );  

//定义类文件目录
define("CLASS_DIR", dirname(DIR__FILE__).'/'."class/");

//定义API类文件
define("API_DIR", dirname(DIR__FILE__). '/'."api/");

function autoload($classname){
    $filename = CLASS_DIR.$classname.'.class.php';
    if(file_exists($filename)){
        include_once $filename;
    }else{
        echo "  no class file !";
    }
}

spl_autoload_register('autoload');

return $arrMethod = array
(
    "login"             => "User",
    "register"          => "User",
    "checkCode"         => "User",
    "getUser"           => "User",
    "sendMessage"       => "User",  
    "sendMsg"           => "Message",
    "passChange"        => "User",
    "addFriend"         => "User",
    "getFriends"        => "User",
    "getMailListStatus" => "User",
    "getBlackList"      => "User",
    "addToBlack"        => "User",
    "removeBlack"       => "User",
    "otherLogin"        => "User",
    "improveUserInfo"   => "User",
    "getMyVideo"        => "Video",
    "getVideoDetail"    => "Video",
    "getVideoComment"   => "Video",
    "getVideoFocus"     => "Video",
    "getCommentList"    => "Video",
    "searchVideoList"   => "Video",
    "followVideo"       => "Video",
    "getProviceList"    => "Area",
    "getCityList"       => "Area",
    "getZoneList"       => "Area",
    "getProvinceInfo"   => "Area",
    "saveFile"          => "Stream",
    "commentVideo"      => "Video",
    "getYouLiaoInfo"    => "Cate",
    "getErjiInfo"       => "Cate",
    "getFaxianInfo"     => "Cate",
    "getTongchengInfo"  => "Cate",
    "getFriendArea"     => "Cate",
	"getAppInfo"        => "App"
);



