<?php 



function show($d){
    echo "<pre>";
    print_r($d);
    echo "</pre>";
}

class __stopwatch{
    public $time;
    public $starttime;
    
    public function __construct($autostart=true){
        if($autostart) $this->starttime = microtime(true);
    }
    
    public function play(){
        $this->starttime = microtime(true);
    }
    
    public function stop($n=6){
        $end = microtime(true)*1000;
        $starttime = $this->starttime*1000;
        $this->time = number_format($end - $starttime,$n);
        //$this->time = number_format($this->time,$n);
        return $this->time;
    }
}


function getconn(){
    static $conn=null;
    if(null===$conn){
        $conn = new __pdo(DBUSER, DBPASSWORD, DBHOST, DBNAME);
    }
   return $conn;
}

function pdo_test2(){
    $conn = getconn();
    $conn->test();
}

function pdo_test(){
    $host = 'localhost';
    $dbname="fastvdo";
    $user='frank';
    $password='10281028';
    
    $dsn = "mysql:host=$host;dbname=$dbname;charset=UTF-8";
    $conn = new PDO($dsn,$user,$password);
    $sql = "select * from video where id >= ?";
    $stmt = $conn->prepare($sql);
    
    $stmt->execute(array(0));
    
    $data = array();
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $data[]=$row;
    }
    
    echo "<pre>";print_r($data);
    
    $dbh = null;
}

function create_card($n){
    $datas = json_decode(file_get_contents(APPPATH.'database/links'),true);
    $conn = getconn();
    
    clear();
    
    //print_r($datas);return;
    for($i=1;$i<=$n;$i++){ 
        echo $i."\n";
        $k = array_rand($datas);
        $d = $datas[$k];
        $d['id']=$i;
        
        // 檔案 
        file_put_contents(APPPATH.'card/'.$i,json_encode($d));
        
        $params = array($d['id'],$d['width'],$d['height'], $d['title'], $d['link'], $d['image'], $d['category']);
        
        // mysql 
        $conn->query("insert into card set id=?,width=?,height=?,title=?,link=?,image=?,category=?",$params);
        
        // mongodb
        
    }
    //file_put_contents(APPPATH.'database/data',json_encode($rs));
}

function clear(){ 
    array_map('unlink', glob("card/*"));
    
    $conn = getconn();
    $conn->query('TRUNCATE TABLE `card`');
    //$fs = glob('card/*');
    
    
    //unlink($fs);
}


/*
**** select 

*/

class __pdo{
    
    public $is_connected;
    
    public $conn;
    
    public function __construct($user, $password, $host, $dbname ,$options=array()){
    
        $options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8';
        
        $this->is_connected=true;
        
        try{
            
            $this->conn = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $user, $password, $options);
            
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        }catch(PDOExecption $e){
            
            $this->is_connected=false;
            
            throw new Execption($e->getMessage());
        }
    }
    
    public function test(){
        $rows = $this->getRows('select * from video where id > ? and id < ?',array(10,10000));
        show($rows);
    }
    
    public function close(){
        $this->pdo = null;
        
        $this->is_connected=false;
    }
    
    public function getRow($query,$params=array(),$fetch_mode=''){
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            if($fetch_mode==='') return $stmt->fetch();
            else return $stmt->fetch($fetch_mode);
        }catch(PDOExecption $e){
            throw new Exception($e->getMessage());
        }
    }
    
    public function getRows($query, $params=array(), $fetch_mode=''){
        try{
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            if($fetch_mode==='') return $stmt->fetchAll(0);
            else return $stmt->fetchAll($fetch_mode);
        }catch(PDOExecption $e){
            throw new Exception($e->getMessage());
        }
    }
    
    public function query($query, $params=array()){
        try{
            $stmt = $this->conn->prepare($query); 
            $stmt->execute($params);
        }catch(PDOException $e){
            throw new Exception($e->getMessage());
        }
    }
}



/*
-- phpMyAdmin SQL Dump
-- version 4.2.4
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 產生時間： 2014 年 06 月 24 日 12:59
-- 伺服器版本: 5.5.38
-- PHP 版本： 5.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- 資料庫： `bench`
--

-- --------------------------------------------------------

--
-- 資料表結構 `data`
--

CREATE TABLE IF NOT EXISTS `data` (
`id` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `title` varchar(300) NOT NULL,
  `link` varchar(300) NOT NULL,
  `image` varchar(300) NOT NULL,
  `category` varchar(300) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- 已匯出資料表的索引
--

--
-- 資料表索引 `data`
--
ALTER TABLE `data`
 ADD PRIMARY KEY (`id`);

--
-- 在匯出的資料表使用 AUTO_INCREMENT
--

--
-- 使用資料表 AUTO_INCREMENT `data`
--
ALTER TABLE `data`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

*/