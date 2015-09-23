<?php
 require_once 'common.php';
 require_once './tools/tools.class.php';
 define("IMAGEPATH", 'upload_image');
 header('Content-type:text/json');

 ini_set("display_errors", 1);
 ini_set('upload_max_filesize',120);
 $time_start = microtime();
 $stype = intval($_POST['type']);
 $cover = $_POST['cover'];
 $vid   = $_POST['vid'];
 $Uid  = intval($_POST['uid']);
 $video_area  = $_POST['video_area'];
 
 $video_name  = $_POST['video_name'];
 $video_type  = $_POST['video_type'];
 
 $allowtype=array('wmv','avi','wma','mp3','mp4','3gp','rmvb','flv');        
 $mulu="im/";  
 if(isset($_FILES['myfiles'])) {
 $rs=explode(".",'temp.mp4');            
 $type=strtolower(end($rs));
 if(!in_array($type,$allowtype)){       
    echo json_encode(array('success' =>false,'message'=>'不是图片类型' ));  
     exit;  
 } 

$str = Tools::createRroundStr(8);

 
$imagename = md5($str.time());
$_path = Tools::createUploadFilePath(IMAGEPATH);
$videoPath = $_path .$imagename.'.'.$type;

           
$rs=move_uploaded_file($_FILES['myfiles']['tmp_name'],$videoPath); 

if($rs){
    if( $stype ==3 ){
        $update_time = time();
        $videoPath = $videoPath;
        $objMysql = new MysqlDb($host = 'bdm123604245.my3w.com',$username = 'bdm123604245',$pass='goodpenyou2015',$database = "bdm123604245_db");
        $strSql = "update YK_Video set video_address='".$videoPath."',owner_id= '".$Uid."',video_name= '".$video_name."',video_type= '".$video_type."',video_area='".$video_area."' where id=".$vid;
        $result = $objMysql->query($strSql);
        
    }
    $time_end = microtime();
	$data = array(
			'success' =>true ,
			'imagepath'=>$videoPath,  
 		 );
	echo json_encode($data);
}else{
	$data = array(
			'success' =>false ,
			 
			'message'=>"上传失败"
		 );
	echo json_encode($data);
}
 


};



?>