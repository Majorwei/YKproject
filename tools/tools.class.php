<?php
/**
 * @desc   工具类
 * @author Major
 * @date   2015-06-20 23:25:42
 */
class Tools
{
    
   public function __construct()
   {
      
   }

   public static function responseData($status,$msg,$data=array(),$total=1){
       if(empty($total)) $total=1;
       $result = array('status'=>$status,'msg'=>$msg,'data'=>$data,'total'=>$total);
       echo  self::JSON($result);
   }
   
   /**************************************************************
    *
    * 使用特定function对数组中所有元素做处理
    * @param string &$array  要处理的字符串
    * @param string $function 要执行的函数
    * @return boolean $apply_to_keys_also  是否也应用到key上
    * @access public
    *
    *************************************************************/
   function arrayRecursive(&$array, $function, $apply_to_keys_also = false)
   {
       static $recursive_counter = 0;
       if (++$recursive_counter > 1000) {
           die('possible deep recursion attack');
       }
       foreach ($array as $key => $value) {
           if (is_array($value)) {
               self::arrayRecursive($array[$key], $function, $apply_to_keys_also);
           } else {
               $array[$key] = $function($value);
           }
            
           if ($apply_to_keys_also && is_string($key)) {
               $new_key = $function($key);
               if ($new_key != $key) {
                   $array[$new_key] = $array[$key];
                   unset($array[$key]);
               }
           }
       }
       $recursive_counter--;
   }
   
   /**************************************************************
    *
    * 将数组转换为JSON字符串（兼容中文）
    * @param array $array  要转换的数组
    * @return string  转换得到的json字符串
    * @access public
    *
    *************************************************************/
   public static  function JSON($array) {
       self::arrayRecursive($array, 'urlencode', true);
       $json = json_encode($array);
       return urldecode($json);
   }
   
   
   public static function convertToPlainText($string){
   
       $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
               '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
               '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
               '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
       );
       $plainText = preg_replace($search, '', $string);
   
       return $plainText;
   }
   public static function createRroundStr($length){
       $str = '';
       $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
       $max = strlen($strPol)-1;
   
       for($i=0;$i<$length;$i++){
           $str.=$strPol[rand(0,$max)];
       }
       return $str;
   }
   public static function createUploadFilePath($default_path=''){
   
       $year = date('Y',time());
       $month = date('m',time());
       $day = date('d',time());
       	
       if(!is_dir($default_path.'/'.$year)){
           mkdir($default_path.'/'.$year, 0777 , true);
       }
       if(!is_dir($default_path.'/'.$year.'/'.$month)){
           mkdir($default_path.'/'.$year.'/'.$month, 0777);
       }
       if(!is_dir($default_path.'/'.$year.'/'.$month.'/'.$day)){
           mkdir($default_path.'/'.$year.'/'.$month.'/'.$day, 0777);
       }
       return $default_path.'/'.$year.'/'.$month.'/'.$day.'/';
   }
   /**
    * Clean user submited data safely
    */
   public static function cleanUserSubmited($data)
   {
       if(is_array($data))	{
           $ret = array();
           foreach($data as $key=>$value)	{
               $ret[$key] = $this->cleanUserSubmited($value);
           }
           return $ret;
       } else	{
           if(!is_numeric($data)){
               if(get_magic_quotes_gpc()){
                   $data = stripslashes($data);
               }
               	
               //Escape string for database ; equivalant to mysql_real_escape_string
               $data = strtr($data, array(
                       "\x00" => '\x00',
                       "\n" => '\n',
                       "\r" => '\r',
                       '\\' => '\\\\',
                       "'" => "\'",
                       '"' => '\"',
                       "\x1a" => '\x1a'
               ));
           }
   
           return $data;
       }
   }
   
   /**
    * clear spaces and line break in texts
    */
   public static function nl2blank($data){
       return preg_replace('#\r?\n#', '', $data);
   }
   
   
   /**
    Find the nth position of char in string.
     
    */
   public static function strnpos($haystack, $needle, $occurance, $pos = 0) {
       	
       $res = explode($needle,$haystack);
       $res = array_slice($res, $pos,  $occurance);
       return implode($needle,$res);
   }
   
   /**
    * extend substr. Chinese words count with 2, and letters count with 1
    * @param $string
    * @param $length
    * @param $etc
    * @return string
    */
   public static function zh_substr($string, $length = 20, $etc = '...')
   {
       $strcut = '';
       $strLength = 0;
       $width  = 0;
       $wordLength = 0;
       $string = strip_tags($string);
       $len = strlen($string);
       for($i = 0; $i < $len;  ) {
           if ( $width>=$length){
               break;
           }
           if( ord($string[$strLength]) > 127 ){
               if ( $width + 2 >$length){
                   break;
               }
               $strLength += 3;
               $width     += 2;
               $i +=3;
           }
           else{
               $strLength += 1;
               $width     += 1;
               $i +=1;
           }
           $wordLength +=1;
       }
       return mb_substr($string, 0, $wordLength,'utf-8').($len != $strLength ? $etc : '');
   }
   
   /**
    * 对字符串进行简单编码，不让搜索蜘蛛识别
    * @param $text
    * @return unknown_type
    */
   public static function encodeText($text) {
       $encoded_text = '';
   
       for ($i = 0; $i < strlen($text); $i++)
       {
           $char = $text{$i};
           $r = rand(0, 100);
   
           # roughly 10% raw, 45% hex, 45% dec
           # '@' *must* be encoded. I insist.
           if ($r > 90 && $char != '@')
           {
           $encoded_text .= $char;
           }
           else if ($r < 45)
           {
           $encoded_text .= '&#x'.dechex(ord($char)).';';
           }
           else
           {
           $encoded_text .= '&#'.ord($char).';';
           }
           }
   
           return $encoded_text;
   }
   
   /**
   * 检查字符串是不是为空
   * @param string $str
   * @return bool
   */
   public static function is_empty($str) {
   if(!isset($str) || is_null($str) || trim($str) == '' )
   return true;
   return false;
   }
   
   /**
   * 检查字符串是不是全部是中文
   * @param $str
   * @return bool
   */
       public static function isAllZhChars($str) {
       if(preg_match('/^[\x{4E00}-\x{9FA5}]+$/u', $str) > 0) {
       return true;
       }
       return false;
       }
       /**
       * 获取数组中相同的键的值组成数组
       * @param $array ,$string
       * @return array
           */
           public static function is_get_by_key($array, $string){
                   if (!trim($string)) return false;
           preg_match_all("/\"$string\";\w{1}:(?:\d+:|)(.*?);/", serialize($array), $res);
           return $res[1];
   }
   /**
   * 折线图y轴控制数据已便显示出好的效果只针对小数问题
   * @param $array ,$string
   * @return array
   */
   public function is_highcharts_data($szarr,$js=0.1,$xs=1){
   $mxystr='';
   $tm = floatval(trim(max($szarr),'"'));
   $tn = floatval(trim(min($szarr),'"'));
   $tn = $this->rfloor($tn,$xs);
   $tnones = $this->rfloor($tn,$xs);
   $tm = $this->round_up($tm,$xs);
   $inum = bcsub($tm,$tn,$xs)/$js;
   for($i=0;$i<floor($inum);$i++){
   $tn = bcadd($tn,$js,$xs);
   $mxystr.=$tn.',';
   }
   $xyarr = $tnones.','.$mxystr.$tm;
    
   return  json_encode(explode(',',$xyarr));
   }
   //小数只入不舍
   public function round_up ( $value, $precision ){
   $pow = pow ( 10, $precision );
   return ( ceil ( $pow * $value ) + ceil ( $pow * $value - ceil ( $pow * $value ) ) ) / $pow;
   }
   //小数只舍不入
   public function rfloor($real,$decimals = 2) {
           return substr($real, 0,strrpos($real,'.',0) + (1 + $decimals));
       }
   
    
}