<?php 
ini_set('display_errors',1);
error_reporting(E_ALL);
define('ROOTPATH','/var/www/vhosts/default/htdocs/');
define('APPPATH','/var/www/vhosts/default/htdocs/bench/');

define('DBHOST','localhost');
define('DBNAME','bench');
define('DBUSER','frank');
define('DBPASSWORD','10281028');
include(APPPATH.'lib.php');

$conn = getconn();

$rows = $conn->getRows("select * from card where id <100");
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<style>
body {
	background: #fafafa url(http://jackrugile.com/images/misc/noise-diagonal.png);
	color: #444;
	font: 100%/30px 'Helvetica Neue', helvetica, arial, sans-serif;
	text-shadow: 0 1px 0 #fff;
}

strong {
	font-weight: bold; 
}

em {
	font-style: italic; 
}

table {
	background: #f5f5f5;
	border-collapse: separate;
	box-shadow: inset 0 1px 0 #fff;
	font-size: 12px;
	line-height: 24px;
	margin: 30px auto;
	text-align: left;
	width: 1000px;
}	

th {
	background: url(http://jackrugile.com/images/misc/noise-diagonal.png), linear-gradient(#777, #444);
	border-left: 1px solid #555;
	border-right: 1px solid #777;
	border-top: 1px solid #555;
	border-bottom: 1px solid #333;
	box-shadow: inset 0 1px 0 #999;
	color: #fff;
  font-weight: bold;
	padding: 10px 15px;
	position: relative;
	text-shadow: 0 1px 0 #000;	
}

th:after {
	background: linear-gradient(rgba(255,255,255,0), rgba(255,255,255,.08));
	content: '';
	display: block;
	height: 25%;
	left: 0;
	margin: 1px 0 0 0;
	position: absolute;
	top: 25%;
	width: 100%;
}

th:first-child {
	border-left: 1px solid #777;
	box-shadow: inset 1px 1px 0 #999;
}

th:last-child {
	box-shadow: inset -1px 1px 0 #999;
}

td {
	border-right: 1px solid #fff;
	border-left: 1px solid #e8e8e8;
	border-top: 1px solid #fff;
	border-bottom: 1px solid #e8e8e8;
	padding: 10px 15px;
    padding:0;
	position: relative;
	transition: all 300ms;
    text-align:center;
}

td:first-child {
	box-shadow: inset 1px 0 0 #fff;
}	

td:last-child {
	border-right: 1px solid #e8e8e8;
	box-shadow: inset -1px 0 0 #fff;
}	

tr {
	background: url(http://jackrugile.com/images/misc/noise-diagonal.png);	
}

tr:nth-child(odd) td {
	background: #f1f1f1 url(http://jackrugile.com/images/misc/noise-diagonal.png);	
}

tr:last-of-type td {
	box-shadow: inset 0 -1px 0 #fff; 
}

tr:last-of-type td:first-child {
	box-shadow: inset 1px -1px 0 #fff;
}	

tr:last-of-type td:last-child {
	box-shadow: inset -1px -1px 0 #fff;
}	


</style>
</head>
<body>
<button onclick="create();">重新產生資料</button>
<div id="mygrid">

<table class="">
<tr>
    <th>id</th>
    <th>width</th>
    <th>height</th>
    <th>title</th>
    <th>link</th>
    <th>image</th>
    <th>category</th>
    <th>功能</th>
</tr>
<?for($i=0,$imax=count($rows);$i<$imax;$i++):?>
<?$r=$rows[$i]; ?>
<tr>
    <td><?=$r['id'];?></td>
    <td><textarea style="width:75px;height:100%;" gridid="<?=$r['id'];?>" field="width"><?=$r['width']?></textarea></td>
    <td><textarea style="width:75px;height:100%;" gridid="<?=$r['id'];?>" field="height"><?=$r['height'];?></textarea></td>
    <td><textarea style="width:212px;height:100%;" gridid="<?=$r['id'];?>" field="title"><?=$r['title'];?></textarea></td>
    <td><textarea style="width:212px;height:100%;" gridid="<?=$r['id'];?>" field="link"><?=$r['link'];?></textarea></td>
    <td><textarea style="width:212px;height:100%;" gridid="<?=$r['id'];?>" field="image"><?=$r['image'];?></textarea></td>
    <td><textarea style="width:75px;height:100%;" gridid="<?=$r['id'];?>" field="category"><?=$r['category'];?></textarea></td>
    <td><button type="button" gridid="<?=$r['id'];?>">刪除</button></td>
</tr>
<?endfor;?>
</table>

</div>

<script src="//code.jquery.com/jquery-1.10.2.js"></script>
<script type="text/javascript" src="http://fun-vdo.com/quiz/plugin/EZ/EZ.js"></script>

<script>
EZ.api = function(params, callback){
    EZ.ajax('api.php', params, callback);
};

function mycallback(e){
    console.log('callback');
    location.replace('');
}

$('#mygrid').find('textarea').focus(function(e){
    e = e || window.event;
    var dom = e.target || e.srcElement;
    
    oldtext = dom.value;
    
});

$('#mygrid').find('textarea').blur(function(e){
    e = e || window.event;
    var dom = e.target || e.srcElement;
    
    newtext = dom.value;
    if(oldtext != newtext){
        var p = {};
        p.id=$(dom).attr('gridid');
        p.field = $(dom).attr('field');
        p.value = newtext;
        p.cmd='update';
        EZ.api(p,EZ.emptyFN);
    }
    
});

$('#mygrid').find('button').click(function(e){
    e = e || window.event;
    var dom = e.target || e.srcElement;
    
    var id = $(dom).attr('gridid');
    if(id){
        var p = {};
        p.id=id;
        p.cmd='delete';
        EZ.api(p,mycallback);
    }
});

function create(){
    var p = {};
    p.cmd='create';
    EZ.api(p,mycallback);
}
var oldtext;
var newtext;
</script>
</body>
</html>