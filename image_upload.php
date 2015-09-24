<?php
 require_once 'common.php';
 require_once './tools/tools.class.php';
 define("IMAGEPATH", 'upload_image');
 header('Content-type:text/json');

 ini_set("display_errors", 1);
 ini_set('upload_max_filesize',120);
 
 $stype = intval($_POST['type']);
 $Uid  = intval($_POST['uid']);
 
 $allowtype=array('jpg','png','jpeg','gif');        
 $mulu="im/";  
 if(isset($_FILES['myfiles'])) {
 $rs=explode(".",'temp.jpg');            
 $type=strtolower(end($rs));
 if(!in_array($type,$allowtype)){       
    echo json_encode(array('success' =>false,'message'=>'不是图片类型' ));  
     exit;  
 } 

$str = Tools::createRroundStr(8);

 
$imagename = md5($str.time());
$_path = Tools::createUploadFilePath(IMAGEPATH);
$imagepath = $_path .$imagename.'.'.$type;

           
$rs=move_uploaded_file($_FILES['myfiles']['tmp_name'],$imagepath); 

if($rs){
    if( $stype ==1 ){
        $update_time = time();
        $headpic = $imagepath;
        $objMysql = new MysqlDb($host = 'bdm123604245.my3w.com',$username = 'bdm123604245',$pass='goodpenyou2015',$database = "bdm123604245_db");
        $strSql = "update YK_User set head_pic='".$headpic."',update_time = $update_time  where user_id='".$Uid."'";
        $result = $objMysql->query($strSql);
        
        $data = array(
                'success' =>true ,
                'imagepath'=>$imagepath,
                'viewpath'=>$imagepath,
        );
    }else if( $stype ==2 ){
        
        $update_time = time();
        $imgPath = $imagepath;
        $objMysql = new MysqlDb($host = 'bdm123604245.my3w.com',$username = 'bdm123604245',$pass='goodpenyou2015',$database = "bdm123604245_db");
        $strSql = "insert into YK_Video (cover,create_time) values ('".$imgPath."','".$update_time."')";
        $result = $objMysql->query($strSql);
        $id     = mysql_insert_id();
        $data = array(
                'success' =>true ,
                'imagepath'=>$imagepath,
                'vid'     =>strval($id),
        );
        
    }
	
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