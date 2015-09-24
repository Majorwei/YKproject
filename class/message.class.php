<?php
/**
 * 短信接口类
 * @author Major
 * @date 2015-07-10 00:43:25
 */

class Message
{
    private $objMysql;
    const SERIAL_NUMBER = '6SDK-EMY-6688-KKULR';  //cdkey
    const ACCOUNT_PASS  = '567351';               //password
    const SEQID         = '7758521';
    const REGISTER_ACCOUNT_URL = 'http://sdk4report.eucp.b2m.cn:8080/sdkproxy/regist.action';                  //注册账号（激活账号）
    const REGISTER_COMPANY_URL = 'http://sdk4report.eucp.b2m.cn:8080/sdkproxy/registdetailinfo.action';        //注册企业信息
    const QUERY_BALANCE_URL    = 'http://sdk4report.eucp.b2m.cn:8080/sdkproxy/querybalance.action';            //查询余额接口
    const RECHARGE_MONEY_URL   = 'http://sdk4report.eucp.b2m.cn:8080/sdkproxy/chargeup.action';                //充值接口
    const SEND_ATONCE_MSG_URL  = 'http://sdk4report.eucp.b2m.cn:8080/sdkproxy/sendsms.action';                 //发送即时短信接口
    const SEND_LATER_MSG_URL   = 'http://sdk4report.eucp.b2m.cn:8080/sdkproxy/sendtimesms.action';             //发送定时短信接口
    
    /**
     * 构造方法设置时区
     */
    public function __construct(){
        date_default_timezone_set('Asia/Shanghai');
    }
    
    /**
     * 序列号注册（账号密码需要先注册一下）
     */
    public function registerAccount(){
        $requestUrl = self::REGISTER_ACCOUNT_URL.'?cdkey='.self::SERIAL_NUMBER.'&password='.self::ACCOUNT_PASS;
        return $this->httpRequest($requestUrl);
    }
    
    /**
     * 企业信息注册
     */
    public function registerCompany(){
        $ename     = '北京煦色韶光文化传媒有限公司';
        $linkman   = '程昱航';
        $phonenum  = '18701017287';
        $mobile    = '18701017287';
        $email     = '2692453392@qq.com';
        $fax       = '88888888-8888';
        $address   = '北京市海淀区苏州街55号3层01-A342';
        $postcode  = '100080';
        $url        = self::REGISTER_COMPANY_URL.'?cdkey='.self::SERIAL_NUMBER.'&password='.self::ACCOUNT_PASS;
        $requestUrl = $url.'&ename='.$ename.'&linkman='.$linkman.'&phonenum='.$phonenum.'&mobile='.$mobile.'&email='.$email.'&fax='.$fax.'&address='.$address.'&postcode='.$postcode;            
        return $this->httpRequest($requestUrl);
    }
    
    /**
     * 查询短信余额接口
     */
    public function queryBalance(){
        $requestUrl = self::QUERY_BALANCE_URL.'?cdkey='.self::SERIAL_NUMBER.'&password='.self::ACCOUNT_PASS;
        return $this->httpRequest($requestUrl);
    }
    
    /**
     * 充值接口
     */
    public function recharge($cardNo,$cardPass){
        $requestUrl = self::RECHARGE_MONEY_URL.'?cdkey='.self::SERIAL_NUMBER.'&password='.self::ACCOUNT_PASS.'&cardno='.$cardNo.'&cardpass='.$cardPass;
        return $this->httpRequest($requestUrl);
    }
    
    /**
     * 发送即时短信接口
     */
    public function sendMsg( $phone, $code ){
        $code = urlencode($code);
        $addserial = time();
        $seqid  = $addserial.rand(1,100000);
        $requestUrl = self::SEND_ATONCE_MSG_URL.'?cdkey='.self::SERIAL_NUMBER.'&password='.self::ACCOUNT_PASS.'&phone='.$phone.'&message='.$code.'&seqid='.$seqid.'&addserial='.$addserial;  
        return $this->httpRequest($requestUrl);
    }
    
    /**
     * 发送定时短信接口
     */
    public function sendLaterMsg( $phone, $code ){
        $addserial = time();
        $seqid     = $addserial.rand(1,100000);
        $sendtime  = date('YmdHis');
        $requestUrl = self::SEND_LATER_MSG_URL.'?cdkey='.self::SERIAL_NUMBER.'&password='.self::ACCOUNT_PASS.'&phone='.$phone.'&message='.$code.'&seqid='.$seqid.'&addserial='.$addserial.'&sendtime='.$sendtime;
        return $this->httpRequest($requestUrl);
    }
    
    /**
     * curl请求
     */
    private function httpRequest( $url ){
        // $header[] = "Accept:application/json, text/javascript, */*; q=0.01";
        /*$header[]="Accept-Encoding:gzip, deflate";
        $header[] = "Cache-Control:max-age=0";
        $header[]= "Accept-Language:en-US,en;q=0.5";
        $header[] = "Content-Length:37";
        $header[]="Content-Type:application/x-www-form-urlencoded; charset=UTF-8";
        $header[]="Cookie:__qca=P0-116849880-1387336057175; __utma=140029553.335591273.1387336057.1389609300.1389617402.102; __utmz=140029553.1389617402.102.89.utmcsr=google|utmccn=(organic)|utmcmd=organic|utmctr=(not%20provided); _ga=GA1.2.335591273.1387336057; sgt=id=3380ce36-a139-4845-bd20-5bb3fd4174ec; usr=t=qrthm51g2UyV&s=QtuIYj84zEOR";
        $header[]="X-Requested-With:XMLHttpRequest"; */
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //执行命令
        $data = curl_exec($curl);
        //关闭URL请求
        curl_close($curl);
        //显示获得的数据
        return $data;
    }

    
}