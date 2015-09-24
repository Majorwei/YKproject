<?php
class User
{
    private $objMysql;
    public $objMsg;
    
    public function __construct(){
        //$this->objMysql=  new MysqlDb($host = 'bdm123604245.my3w.com',$username = 'bdm123604245',$pass='goodpenyou2015',$database = "bdm123604245_db");
        $this->objMysql=  new MysqlDb($host = 'bdm123604245.my3w.com',$username = 'bdm123604245',$pass='goodpenyou2015',$database = "bdm123604245_db");
        $this->objMsg  =  new Message();
    }
    /**
     * 登录
     * @param array $arrParam
     * @return []
     */
    public function login($arrParam){
        /* $arrParam['phone'] = '15801682517';
        $arrParam['pass']  = '1';  */
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        if( empty($arrParam['user']) || empty($arrParam['pass']) ){
            return $arrRet;exit;
        }
        $strSql = "SELECT user_id,nick_name,phone,status,source create_time,identifier,mechine_type,head_pic,slogan,gender,ischeck FROM YK_User where phone='".$arrParam['user']."' and pass_word='".$arrParam['pass']."'";   
        $q      = $this->objMysql->query($strSql);
        $result = $this->objMysql->fetch_assoc($q);
        if($result){
            return array( "status"=>"1","msg"=>"登录成功","data"=>$result); exit;
        }else{
            return $arrRet;exit;
        }
        
    }
    /**
     *发送短信 
     */
    public function sendMessage( $arrParam ){
        $arrRet            = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        $arrParam['phone'] = base64_decode($arrParam['phone']);
        if( strlen($arrParam['phone'] ) != 11 || !preg_match('/^([1][3-8])\d{9}$/', $arrParam['phone']) ){
            return array( "status"=>"-1","msg"=>"手机格式不正确","data"=>array() ); exit;
        }
        $arrParam['flag'] = empty($arrParam['flag'])?1:$arrParam['flag'];
        $code   = rand(100000,999999);
        //检查用户是否注册
        if( $arrParam['flag'] == 1 ){
            $this->_checkUser( $arrParam['phone'] );
            $textCode = '【好喷友】您好，您正在注册好喷友，您的验证码为：'.$code.'请查收';
        }
        if( $arrParam['flag'] ==2 ){
            $this->_checkRegister( $arrParam['phone'] );
            $textCode = '【好喷友】您好，您正在修改密码，您的验证码为：'.$code.'请查收';
        }
        //记录验证码
        $result = $this->_recordCode( $arrParam['phone'] , $code );
        
        if( !$result ){
            return $arrRet; exit;
        }
        
        //发送短信
        $sendRes  = $this->objMsg->sendMsg($arrParam['phone'], $textCode);
        return array( 'status'=>'1','msg'=>'发送成功','data'=>array() );
    }
    
    /**
     * 检查用户是否已经注册
     * @先主要检查手机号
     * 
     */
    public function _checkUser( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        $strSql = " select * from YK_Code where phone='".$phone."'";
        $q      = $this->objMysql->query($strSql);
        $resCheck = $this->objMysql->fetch_assoc($q);
        if( isset($resCheck['id']) ){
            if( $resCheck['ischeck'] > 0 ){
                return array( "status"=>"-1","msg"=>"对不起，此手机已被注册","data"=>array()); exit;
            }else if ($resCheck['time']+60 > $create_time){
    			return array('status'=>'-1','msg'=>'请求过于频繁，请稍等片刻',"data"=>array()); exit;
    		}
        }
        return true;
    }
    
    /**
     * 检查是否是注册用户
     */
    public function _checkRegister(){
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        $strSql = " select * from YK_User where phone='".$phone."'";
        $q      = $this->objMysql->query($strSql);
        $resCheck = $this->objMysql->fetch_assoc($q);
        if( isset($resCheck['id']) ){
            return true;
        }else{
            return array( 'status'=>'-1','msg'=>'对不起，此账号不存在','data'=>array() );exit;
        }
        
    }
    //记录验证码
    private function _recordCode($phone,$code){
        $create_time = time();
        $strSql = " select * from YK_Code where phone='".$phone."' ";
        $q      = $this->objMysql->query($strSql);
        $resCheck = $this->objMysql->fetch_assoc($q);
        if( isset($resCheck['id']) ){
            $upSql     = "update YK_Code set code = '".$code."',ischeck = 0 where id = ".$resCheck['id'];
            $upQuery   = $this->objMysql->query($upSql);
            return $upQuery;
        }else{
            $inSql = "insert into YK_Code ( phone,code,time ) values ('".$phone."','".$code."','".$create_time."')";
            $inQuery   = $this->objMysql->query($inSql);
            return $inQuery;
        }
    }
    /**
     * 验证验证码是否正确
     */
    public function checkCode( $phone,$code ){
        if( strlen($phone) !== 11 || !preg_match('/^([1][3-8])\d{9}$/', $phone) ){
            return array('status'=>'-1','msg'=>'手机号格式不正确',"data"=>array()); exit;
        }
        if( strlen($code) !=6  ){
            return array('status'=>'-1','msg'=>'验证码格式不正确,请输入6位数字',"data"=>array()); exit;
        }
        
        $codeSql = "select * from YK_Code where phone='".$phone."'";
        $codeQ      = $this->objMysql->query($codeSql);
        $resCheck = $this->objMysql->fetch_assoc($codeQ);
        if( $resCheck['code'] != $code ){
            return array('status'=>'-1','msg'=>'验证码有误',"data"=>array()); exit;
        }
        return array('status'=>'1','msg'=>'验证通过',"data"=>array()); exit;
    }
     
    /**
     * 注册
     * @param array $arrParam
     * @return json:
     */
    public function register( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        //验证非空
        if( empty( $arrParam['phone'] ) || empty($arrParam['pass'])  ){
            return array('status'=>'-1','msg'=>'手机号和密码不能为空',"data"=>array()); exit;
        }
        //注册时间
        $create_time = time();
        //验证验证码是否正确 正确返回验证码表中对应行的ID
        $ykCodeRes    = $this->checkCode($arrParam['phone'], $arrParam['code']);
        if( $ykCodeRes['status'] == 1 ){
            $upSql   = "update YK_Code set ischeck = 1 where phone='".$arrParam['phone']."'";
            $upRet   = $this->objMysql->query($upSql);
            if( !$upRet ){
                return array('status'=>'-1','msg'=>'对不起，验证失败',"data"=>array()); exit;
            }
            $strSql  = "insert into YK_User ( phone,pass_word,create_time,head_pic,backgroud ) values ('".$arrParam['phone']."','".$arrParam['pass']."','".$create_time."','head.jpg','backgroud.jpg')";
            $result  = $this->objMysql->query($strSql);
            $strSql = "SELECT user_id,nick_name,phone,status,source create_time,identifier,mechine_type,head_pic,ischeck,slogan,gender FROM YK_User where phone='".$arrParam['phone']."' ";
            $q      = $this->objMysql->query($strSql);
            $loginInfo = $this->objMysql->fetch_assoc($q);
            if( $result ){
                return array('status'=>'1','msg'=>'恭喜您，注册成功',"data"=>$loginInfo); exit;
            }else{
                return $arrRet;exit;
            }
        }else{
            return  $arrRet;exit;
        }
    }
    
    /**
     * 修改密码
     * 
     */
    public function passChange ( $arrParam ){
        $update_time = time();
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        if( empty($arrParam['pass']) ){
            return array('status'=>'-1','msg'=>'密码不能为空',"data"=>array()); exit;
        }
        if( empty($arrParam['phone']) ){
            return array('status'=>'-1','msg'=>'手机号不能为空',"data"=>array()); exit;
        }
        if( strlen($phone) !== 11 || !preg_match('/^([1][3-8])\d{9}$/', $phone) ){
            return array('status'=>'-1','msg'=>'手机号格式不正确',"data"=>array()); exit;
        }
        
        $strSql = "update YK_User set pass_word='".$arrParam['pass']."',update_time = $update_time  where phone='".$arrParam['phone']."'";
        $result = $this->objMysql->query($strSql);
        if( $result ){
            return array('status'=>'1','msg'=>'修改密码成功，请记住新密码',"data"=>array()); exit;
        }else{
            return $arrRet;exit;
        }
        
    }
    
    /**
     * 加好友
     */
    public function addFriend ( $arrParam ){
        $create_time = time();
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        if( empty($arrParam) || empty($arrParam['myid']) || empty($arrParam['fid']) ){
            return array( 'status'=>'-1','msg'=>'参数错误，请求失败','data'=>array() );
        }
        $Sql    = "select * from YK_Friend  where from_id = '".$arrParam['myid']."' and to_id=".$arrParam['fid'];
        $q      = $this->objMysql->query($Sql);
        $ret    = $this->objMysql->fetch_assoc($q);
        if( !empty( $ret ) ){
            return array( 'status'=>'-1','msg'=>'此人已是你好友，请不要重复添加','data'=>array() );
        }
        $strSql = "insert into YK_Friend ( from_id,to_id,create_time,update_time,status ) values ('".$arrParam['myid']."','".$arrParam['fid']."','".$create_time."','".$create_time."',1)";
        $result = $this->objMysql->query($strSql);
        if( $result ){
            return array('status'=>'1','msg'=>'ok',"data"=>array()); exit;
        }else{
            return $arrRet;exit;
        }
    }
    
    /**
     * 获取好友列表
     */
    public function getFriends ( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        if ( empty($arrParam) || $arrParam['uid'] < 1 ){
            return array( 'status'=>'-1','msg'=>'对不起，参数不正确','data'=>array() ); exit;
        }
        $strSql = "select B.user_id as friends_id,B.head_pic,B.backgroud,B.mechine_type,B.source,B.phone,B.nick_name,B.slogan,B.gender from YK_Friend A INNER JOIN YK_User B on A.to_id = B.user_id WHERE A.status=1 and A.from_id=".$arrParam['uid'];
        $q      = $this->objMysql->query($strSql);
        while( !!$result = $this->objMysql->fetch_assoc($q)){
            $ret[] = $result;
        }
        if( isset($arrParam['field']) && $arrParam['field'] === 'penNum' ){//按噴数排序
            foreach ( $ret as $key => $val ){
                $strSql1 = "select  count(B.video_id) as penNum,B.video_id  from YK_Video  A  left join YK_VideoFocus B  on A.id = B.video_id  where A.owner_id='".$val['friends_id']."' GROUP BY B.video_id ORDER BY penNum desc Limit 1";
                $q1      = $this->objMysql->query($strSql1);
                $result1 = $this->objMysql->fetch_assoc($q1);
                $strSql2 = "select count(*) as focusNum from YK_Friend  where from_id= '".$val['friends_id']."' ";
                $q2      = $this->objMysql->query($strSql2);
                $result2 = $this->objMysql->fetch_assoc($q2);
                if(empty($result1)){
                    $ret[$key]['penNum'] =  0;
                    $ret[$key]['video_id'] =  $result1['video_id'];
                }else{
                    $ret[$key]['penNum']   =  $result1['penNum']; 
                    $ret[$key]['video_id'] =  $result1['video_id'];
                } 
                if(empty($result2)){
                    $ret[$key]['focusNum'] =  0;
                }else{
                    $ret[$key]['focusNum'] =  $result2['focusNum'];
                }
            }
            
            $ret = $this->_kuaiSu($ret,'penNum');
        }else{
            foreach ( $ret as $key => $val ){
                $strSql1 = "select  count(B.video_id) as penNum,B.video_id  from YK_Video  A  left join YK_VideoFocus B  on A.id = B.video_id  where A.owner_id='".$val['friends_id']."' GROUP BY B.video_id ORDER BY penNum desc Limit 1";
                $q1      = $this->objMysql->query($strSql1);
                $result1 = $this->objMysql->fetch_assoc($q1);
                $strSql2 = "select count(*) as focusNum from YK_Friend  where from_id= '".$val['friends_id']."' ";
                $q2      = $this->objMysql->query($strSql2);
                $result2 = $this->objMysql->fetch_assoc($q2);
                if(empty($result1)){
                    $ret[$key]['penNum'] =  0;
                    $ret[$key]['video_id'] =  $result1['video_id'];
                }else{
                    $ret[$key]['penNum']   =  $result1['penNum']; 
                    $ret[$key]['video_id'] =  $result1['video_id'];
                } 
                if(empty($result2)){
                    $ret[$key]['focusNum'] =  0;
                }else{
                    $ret[$key]['focusNum'] =  $result2['focusNum'];
                }
            }
            
            $ret = $this->_kuaiSu($ret,'focusNum');
        }
        
        if( $ret && !empty($ret) ){
            return array('status'=>'1','msg'=>'ok',"data"=>$ret); exit;
        }else{
            return $arrRet;
        }
    }
    /**
     * 获取通讯录好友状态
     * @param array $arrParam
     * @return array
     */
    public function getMailListStatus( $arrParam ){
        $arrRet  = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        $findRes = strpos($arrParam['phone'],',');
        if($findRes){
            $arrParam['phone'] = explode(',',$arrParam['phone']);
        }else{
            array( 'status'=>'-1','msg'=>'对不起，参数错误','data'=>array() );
        }
        foreach ( $arrParam['phone'] as $key=>$val ){
            $strSql = "select * from YK_User where phone='".$val."' limit 1";
            $q      = $this->objMysql->query($strSql);
            $result = $this->objMysql->fetch_assoc($q);
            if( isset($result) && !empty($result) ){
                $ret[$val] = $result['user_id'];
            }
        }
        
        if( $ret ){
           return array('status'=>'1','msg'=>'ok',"data"=>$ret); exit;
        }
        return $arrRet;
    }
    
    /**
     * 根据用户ID获取用户信息
     */
    public function  getUser( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        if( empty($arrParam['uid']) ){
            return array( 'status'=>'-1','msg'=>'用户ID不能为空','data'=>array() );
        }
        $strSql = "SELECT user_id,nick_name,phone,status,source create_time,identifier,mechine_type,head_pic,backgroud,ischeck,slogan,gender FROM YK_User where user_id=".$arrParam['uid'];
        $q      = $this->objMysql->query($strSql);
        $result = $this->objMysql->fetch_assoc($q);
        if( $result ){
            return array('status'=>'1','msg'=>'ok',"data"=>$result); exit;
        }else{
            return $arrRet;
        }
    }
    
    /**
     * 获取黑名单列表
     */
    public function getBlackList( $arrParam ){
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        if ( empty($arrParam) || $arrParam['uid'] < 1 ){
            return array( 'status'=>'-1','msg'=>'对不起，参数不正确','data'=>array() ); exit;
        }
        $strSql = "select B.user_id as friends_id,B.head_pic,B.backgroud,B.mechine_type,B.source,B.phone,B.nick_name,B.slogan,B.gender from YK_Blacklist A INNER JOIN YK_User B on A.to_id = B.user_id WHERE A.status=1 and A.from_id=".$arrParam['uid'];
        $q      = $this->objMysql->query($strSql);
        while( !!$result = $this->objMysql->fetch_assoc($q)){
            $ret[] = $result;
        }
        if( isset($arrParam['field']) && $arrParam['field'] === 'penNum' ){//按噴数排序
            foreach ( $ret as $key => $val ){
                $strSql1 = "select  count(B.video_id) as penNum,B.video_id  from YK_Video  A  left join YK_VideoFocus B  on A.id = B.video_id  where A.owner_id='".$val['friends_id']."' GROUP BY B.video_id ORDER BY penNum desc Limit 1";
                $q1      = $this->objMysql->query($strSql1);
                $result1 = $this->objMysql->fetch_assoc($q1);
                $strSql2 = "select count(*) as focusNum from YK_Blacklist  where from_id= '".$val['friends_id']."' ";
                $q2      = $this->objMysql->query($strSql2);
                $result2 = $this->objMysql->fetch_assoc($q2);
                if(empty($result1)){
                    $ret[$key]['penNum'] =  0;
                    $ret[$key]['video_id'] =  $result1['video_id'];
                }else{
                    $ret[$key]['penNum']   =  $result1['penNum']; 
                    $ret[$key]['video_id'] =  $result1['video_id'];
                } 
                if(empty($result2)){
                    $ret[$key]['focusNum'] =  0;
                }else{
                    $ret[$key]['focusNum'] =  $result2['focusNum'];
                }
            }
            
            $ret = $this->_kuaiSu($ret,'penNum');
        }else{
            foreach ( $ret as $key => $val ){
                $strSql1 = "select  count(B.video_id) as penNum,B.video_id  from YK_Video  A  left join YK_VideoFocus B  on A.id = B.video_id  where A.owner_id='".$val['friends_id']."' GROUP BY B.video_id ORDER BY penNum desc Limit 1";
                $q1      = $this->objMysql->query($strSql1);
                $result1 = $this->objMysql->fetch_assoc($q1);
                $strSql2 = "select count(*) as focusNum from YK_Blacklist  where from_id= '".$val['friends_id']."' ";
                $q2      = $this->objMysql->query($strSql2);
                $result2 = $this->objMysql->fetch_assoc($q2);
                if(empty($result1)){
                    $ret[$key]['penNum'] =  0;
                    $ret[$key]['video_id'] =  $result1['video_id'];
                }else{
                    $ret[$key]['penNum']   =  $result1['penNum']; 
                    $ret[$key]['video_id'] =  $result1['video_id'];
                } 
                if(empty($result2)){
                    $ret[$key]['focusNum'] =  0;
                }else{
                    $ret[$key]['focusNum'] =  $result2['focusNum'];
                }
            }
            
            $ret = $this->_kuaiSu($ret,'focusNum');
        }
        
        if( $ret && !empty($ret) ){
            return array('status'=>'1','msg'=>'ok',"data"=>$ret); exit;
        }else{
            return $arrRet;
        }
    }
    
    /**
     * 添加黑名单
     */
    public function addToBlack( $arrParam ){
        $create_time = time();
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        if( empty($arrParam) || empty($arrParam['myid']) || empty($arrParam['fid']) ){
            return array( 'status'=>'-1','msg'=>'参数错误，请求失败','data'=>array() );
        }
        $strSql = "insert into YK_Blacklist ( from_id,to_id,create_time,update_time,status ) values ('".$arrParam['myid']."','".$arrParam['fid']."','".$create_time."','".$create_time."',1)";
        $result = $this->objMysql->query($strSql);
        if( $result ){
            return array('status'=>'1','msg'=>'ok',"data"=>array()); exit;
        }else{
            return $arrRet;exit;
        }
        
    }
    
    /**
     * 把黑名单好友从黑名单中删除
     */
    public function removeBlack( $arrParam ){
        $create_time = time();
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        if( empty($arrParam) || empty($arrParam['myid']) || empty($arrParam['fid']) ){
            return array( 'status'=>'-1','msg'=>'参数错误，请求失败','data'=>array() );
        }
        $blackSql    = "select * from YK_Friend where from_id=".$arrParam['myid']." and to_id=".$arrParam['fid']." and status=2";
        $blackq      = $this->objMysql->query($blackSql);
        $blackRes    = $this->objMysql->fetch_assoc($blackq);
        //从黑名单中删除
        if( $blackRes ){
            $strSql = "update YK_Blacklist set status=2 ,update_time=$create_time where from_id=".$arrParam['myid']." and to_id=".$arrParam['fid'] ." and status =1";
            $result = $this->objMysql->query($strSql);
            if( $result ){
                return array('status'=>'1','msg'=>'从黑名单中删除成功',"data"=>array()); exit;
            }else{
                return $arrRet;exit;
            }
        }else{
            return array('status'=>'-1','msg'=>'删除失败',"data"=>array()); exit;
        }
    }
    
    /**
     * 第三方登录
     */
    public function otherLogin($arrParam){
        $create_time = time();
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        if(empty($arrParam['identifier'])){
            return array( 'status'=>'-1','msg'=>'对不起，授权失败','data'=>array() );
        }
        $strSql = "select user_id,nick_name,phone,status,source create_time,identifier,mechine_type,head_pic,ischeck from YK_User where source = '".$arrParam['source']."' and identifier = '".$arrParam['identifier']."'";
        $q      = $this->objMysql->query($strSql);
        $result = $this->objMysql->fetch_assoc($q);
        if($result ){
            //$result['nick_name'] = mb_convert_encoding($result['nick_name'], 'utf8','gbk');
            return array('status'=>'1','msg'=>'登录成功',"data"=>$result); exit;
        }else{
           $msg  = $this->_autoAccount(); 
           $strSql1 = "insert into YK_User ( nick_name,phone,pass_word,identifier,source,head_pic,province_name ,create_time) values ('".$arrParam['nick_name']."','".$msg['account']."','".$msg['pass']."','".$arrParam['identifier']."','".$arrParam['source']."','".$arrParam['head_pic']."','".$arrParam['province_name']."','".$create_time."')";        
           $result1 = $this->objMysql->query($strSql1);
           unset($msg['pass']);
           if( $result1 ){
               $strSql2 = "select user_id,nick_name,phone,status,source create_time,identifier,mechine_type,head_pic,ischeck from YK_User where source = '".$arrParam['source']."' and identifier = '".$arrParam['identifier']."'";
               $q2      = $this->objMysql->query($strSql2);
               $result2 = $this->objMysql->fetch_assoc($q2);
               //$result2['nick_name'] = mb_convert_encoding($result2['nick_name'], 'utf8','gbk');
               return array('status'=>'1','msg'=>$msg,"data"=>$result2); exit;
           }else{
               return $arrRet;exit;
           }
            
        }
    }
    
    /**
     * 第三方登录自动生成账号密码
     */
    private function _autoAccount(){
        $result['account'] = "HPY_".time();
        //返回给用户的密码
        $result['password']= $result['account'];
        //入库的密码
        $result['pass']    = md5($result['account']);
        return $result;
    }
    
    /**
     * 完善/修改用户信息
     */
    public function improveUserInfo( $arrParam ){
        $update_time = time();
        if( !isset($arrParam['uid']) && empty($arrParam['uid']) ){
            return array( 'status'=>'-1','msg'=>'对不起，参数错误，用户ID不存在','data'=>array() ); exit;
        }
        $userId      = $arrParam['uid'];
        unset($arrParam['uid']);
        $arrRet = array( 'status'=>'-1','msg'=>'对不起，请求失败','data'=>array() );
        if( !isset($arrParam['nick_name']) && !isset($arrParam['head_pic']) && !isset($arrParam['slogan']) && !isset($arrParam['gender']) && !isset($arrParam['backgroud']) ){
            return array( 'status'=>'-1','msg'=>'对不起，没有要修改的东西','data'=>array() ); exit;
        }
        if( empty($arrParam['nick_name']) && empty($arrParam['head_pic']) && empty($arrParam['slogan']) && empty($arrParam['gender']) && empty($arrParam['backgroud']) ){
            return array( 'status'=>'-1','msg'=>'对不起，对不起参数错误，请认真核对','data'=>array() ); exit;
        }
        if( count($arrParam) == 1 ){
            foreach ( $arrParam as $key=>$val){
                $strSql = "update YK_User set ".$key."='".$val."',update_time = $update_time  where user_id='".$userId."'";
                $result = $this->objMysql->query($strSql);
                if( $result ){
                    return array('status'=>'1','msg'=>'修改用户信息成功',"data"=>array()); exit;
                }else{
                    return $arrRet;exit;
                }
            }
            
        }else{
            return array( 'status'=>'-1','msg'=>'对不起，一次只能修改一项','data'=>array() ); exit;
        }
        
    }
    
    /**
     * 快速排序
     */
    public function _kuaiSu($array,$field){
        $len = count($array);
        if($len <= 1)
        {
            return $array;
        }
        $key = $array[0];
        $left = array();
        $right = array();
        for($i=1; $i<$len; ++$i)
        {
            if($array[$i][$field] > $key[$field])
            {
                $left[] = $array[$i];
            }
            else
            {
                $right[] = $array[$i];
            }
        }
        $left  = $this->_kuaiSu($left,$field);
        $right = $this->_kuaiSu($right,$field);
        return array_merge($left, array($key), $right);
    }
    
}  
