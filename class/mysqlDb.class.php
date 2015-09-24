<?php
/**
 * mysql连接类
 * @author playcrab
 *
 */
class MysqlDb {
    
    //主机
    private $host;
    //数据库的username
    private $name;
    //数据库的password
    private $pass;
    //数据库名称
    private $database;
    //编码形式
    private $ut;
    //构造函数
    
    function __construct($host = '',$username = '',$pass='',$database = ""){
        $this->host = $host;
        $this->name = $username;
        $this->pass = $pass;
        $this->database = $database;
        $this->ut = "utf8";
        $this->connect();
    }
    
    //数据库的链接
    function connect(){
        $link=mysql_connect($this->host,$this->name,$this->pass) or die($this->error());
        mysql_select_db($this->database,$link) or die("没该数据库：".$this->database);
        mysql_query("SET NAMES '$this->ut'");
    }
    
    function query($sql, $type = '') {
        $query = mysql_query($sql);
        return $query;
    }
    
    function fetch_assoc($query) {
        return mysql_fetch_assoc($query);
    }
    
    //向$table表中插入值
    function fn_insert($table,$name,$value){
        $this->query("insert into $table ($name) values ($value)");
    }
    
    function fetch_array($query){
        return mysql_fetch_array($query);
    }
   
}