<?php
error_reporting(E_ALL);
if(empty($_COOKIE['nom']) || $_COOKIE['nom']!=='nomA123999'){
    include('tpl/login.html');
    exit;
}

define('URL','http://qlink.eznewlife.com/');
define('ROOTPATH','/var/www/vhosts/qlink.eznewlife.com/httpdocs/');
define('APPPATH','/var/www/vhosts/qlink.eznewlife.com/httpdocs/qlink/');

include('lib.php'); 

if(!empty($_SERVER['HTTP_X_FILE_NAME'])){
    $HTTP_X_FILE_NAME = $_SERVER['HTTP_X_FILE_NAME'];
    $HTTP_X_FILE_TYPE = $_SERVER['HTTP_X_FILE_TYPE'];
    //echo $HTTP_X_FILE_NAME."\n";
    //echo $HTTP_X_FILE_TYPE."\n";
    $tmp = explode('.',$HTTP_X_FILE_NAME);
    $sub = $tmp[1];
    if(empty($sub)) $sub='.jpg';
    $sub = strtolower($sub);
    switch($sub){
        case 'bmp':
        case 'jpg':
        case 'gif':
        case 'png':
            break;
        default:
            $sub = 'jpg';
            break;
    }
    $time = time();
    $filename = $time.'.'.$sub;
    $str = file_get_contents('php://input');
    $file = "images/".$filename;
    file_put_contents($file,$str);
    echo $file;
    exit;
}

if(!empty($_POST['act'])) $act = $_POST['act'];
else $act = '';
//echo 55;
//print_r($_SERVER);
$current_url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

switch($act){
    case 'addlink':
        $qlink = new ___qlink();
        if(empty($_POST['id'])){
            echo 'addlink';
            $qlink->addlink($_POST['width'], $_POST['height'], $_POST['title'], $_POST['link'], $_POST['image'], $_POST['category']);
        }else{
            $_POST['id'] = preg_replace('/[^0-9]+/','',$_POST['id']);
            $qlink->editlink($_POST['id'], $_POST['width'], $_POST['height'], $_POST['title'], $_POST['link'], $_POST['image'], $_POST['category']);
        }
        header('Location:'.$current_url);
        //header('Location:http://admin.qlink.eznewlife.com/qadmin.php?p=2');
        break;
    case 'deletelink':
        $qlink = new ___qlink();
        $_POST['id'] = preg_replace('/[^0-9]+/','',$_POST['id']);
        
        $qlink->deletelink($_POST['id']);
        
        header('Location:'.$current_url);
        break;
    case 'addcategory':
        $category = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $_POST['category']);
        if(!empty($category)){
            $db = new database('category');
            $found = false;
            for($i=0,$imax=count($db->data);$i<$imax;$i++){
                $d = $db->data[$i];
                if($d==$category) $found=true;
            }
            if(!$found) $db->data[] = $category;
            $db->save();
        }
        header('Location:'.$current_url);
        break;
    case 'makestatic':
        $qlink = new ___qlink();
        $qlink->make_all();
        header('Location:'.$current_url);
        break;
    default:
        break;
}


$categorys = (new database('category'))->data;

$content = page::getcontent();

$nav = page::getnav();

include('tpl/qadmin.html');


?> 