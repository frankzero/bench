<?php 
class database
{
    private $file;
    private $file_id;
    private $is_json;
    public $data;
    
    public function __construct($file=null,$is_json=true){
        if($file!=null){
            $this->load($file,$is_json);
        }
    }
    public function load($file,$is_json=true){
        $this->file = 'db/'.$file;
        $this->file_id = 'id_'.$file;
        $this->is_json = $is_json;
        if(file_exists($this->file)){
            $str = file_get_contents($this->file);
            if($is_json) $this->data = json_decode($str,true);
            else $this->data = $str;
        }else{
            $this->data = array();
        }
    }
    //用push會幫你加id
    public function push($data){
        $tmp = array();
        $tmp['id'] = $this->get_id();
        $tmp = array_merge($tmp,$data);
        //$data['id'] = $this->get_id();
        $this->data[] = $tmp;
        return $tmp;
    }
    public function get_id(){
        $id = new database($this->file_id,false);
        if(!$id->data) $id->data=0;
        $id->data++;
        $id->save();
        return $id->data;
    }
    public function save(){
        if($this->is_json) file_put_contents($this->file,json_encode($this->data),LOCK_EX);
        else file_put_contents($this->file,$this->data,LOCK_EX);
    }
    public function clear(){
        $a = array();
        if(!$this->is_json){
            $a = '';
        }
        $this->data = $a;
        file_put_contents($this->file,$a,LOCK_EX);
    }
}

function unique_id($num){
    $t = array(
    'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'
    ,'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'
    ,'0','1','2','3','4','5','6','7','8','9'
    );
    $r = array();
    for($i=0;$i<$num;$i++)
    {
        $r[] = $t[rand(0,61)];
    }
    return implode('',$r);
}

function show($text){
    echo "<pre>";
    print_r($text);
    echo "</pre>";
}


//做分類
function make_category(){
    $links = new database('links');
    $categorys =array();
    for($i=0,$imax=count($links->data);$i<$imax;$i++){
        $l = $links->data[$i];
        $category = $l['category'];
        if(empty($categorys[$category])){
            $categorys[$category] = array();
        }
        $categorys[$category][]=$l;
    }
    
    foreach((array) $categorys as $category => $datas){
        $db = new database('category_'.$category);
        $db->data = $datas;
        $db->save();
    }
}


class __qlink{
    
    public function addlink($width, $height, $title, $link, $image, $category){
        $p = array(
            'width'=>$width,
            'height'=>$height,
            'title'=>$title,
            'link'=>$link,
            'image'=>$image,
            'category'=>$category
        );
        $links = new database('links');
        $p = $links->push($p);
        $links->save();
        return $p;
    }
    
    public function editlink($id, $width, $height, $title, $link, $image, $category){
        $links = new database('links');
        for($i=0,$imax=count($links->data);$i<$imax;$i++){
            if($links->data[$i]['id'] == $id){
                $links->data[$i]['width'] = $width;
                $links->data[$i]['height'] = $height;
                $links->data[$i]['title'] = $title;
                $links->data[$i]['link'] = $link;
                $links->data[$i]['image'] = $image;
                $links->data[$i]['category'] = $category;
                
                $links->save();
                return $links->data[$i];
            }
        }
        return false;
    }
    
    public function deletelink($id){
        $links = new database('links');
        for($i=0,$imax=count($links->data);$i<$imax;$i++){
            if($links->data[$i]['id'] == $id){
                array_splice($links->data,$i,1);
                $links->save();
                break;
            }
        }
    }
    
    public function makestatic($link){
        file_put_contents(APPPATH.'static/'.$link['id'],$this->getcontent($link));
    }
    
    public function deletestatic($id){
        unlink(APPPATH.'static/'.$id);
    }
    
    public function makejson($link){
        $json = array();
        $json[] = $link['title'];
        $json[] = $link['link'];
        $json[] = URL.'qlink/'.$link['image'];
        file_put_contents(APPPATH.'json/'.$link['id'],json_encode($json));
    }
    
    public function deletejson($id){
        unlink(APPPATH.'json/'.$id);
    }
    
    public function makecategory(){
        $links = new database('links');
        $categorys =array();
        $category_id = array();
        for($i=0,$imax=count($links->data);$i<$imax;$i++){
            $l = $links->data[$i];
            $category = $l['category'];
            
            if(empty($categorys[$category])){
                $categorys[$category] = array();
            }
            
            $categorys[$category][]=$l['id'];
            $category_id[]=$l['id'];
        }
        
        foreach((array) $categorys as $category => $datas){
            $db = new database('category_id_'.$category);
            $db->data = $datas;
            $db->save();
        }
        $db = new database('category_id');
        $db->data = $category_id;
        $db->save();
    }
    
    public function getcontent($p){
        $width=$p['width'];
        $height=$p['height'];
        $title=$p['title'];
        $link=$p['link'];
        $target='_blank'; // 1. 空字串 不另開 2._blank=>另開視窗 
        $image=$p['image'];
        ob_start();
        include('tpl/index.html');
        $out = ob_get_contents();
        ob_end_clean();
        return $out;
    }
    
    public function addcategory($category){
        $db = new database('category');
        $found=false;
        
        for($i=0,$imax=count($db->data);$i<$imax;$i++){
            $d = $db->data[$i];
            if($d==$category) $found=true;
        }
        
        if(!$found) {
            $db->data[] = $category;
            $db->save();
            return true;
        }else{
            return false;
        }
    }
}

class ___qlink extends __qlink{
    
    public function __construct(){
    
    }
    
    public function addlink($width, $height, $title, $link, $image, $category){
        $link = parent::addlink($width, $height, $title, $link, $image, $category);
        
        // 靜態網頁 
        $this->makestatic($link);
        
        // json 資料
        $this->makejson($link);
        
        // 分類
        $this->makecategory();
    }
    
    public function editlink($id, $width, $height, $title, $link, $image, $category){
        
        $link = parent::editlink($id, $width, $height, $title, $link, $image, $category);
        
        // 靜態網頁 
        $this->makestatic($link);
        
        // json 資料
        $this->makejson($link);
        
        // 分類
        $this->makecategory();
        
    }
    
    public function deletelink($id){
        
       parent::deletelink($id);
       
       // 刪除靜態網頁
       $this->deletestatic($id);
       
       //刪除 json
       $this->deletejson($id);
       
       //分類 
       $this->makecategory();
    }
    
    public function addcategory($category){
        parent::addcategory($category);
        
    }
    
    public function make_all(){
        $links = new database('links');
        $categorys =array();
        $category_id = array();
        for($i=0,$imax=count($links->data);$i<$imax;$i++){
            $l = $links->data[$i];
            $category = $l['category'];
            if(empty($categorys[$category])){
                $categorys[$category] = array();
            }
            $categorys[$category][]=$l['id'];
            $category_id[]=$l['id'];
            
            //產生靜態 
            file_put_contents('static/'.$l['id'],$this->getcontent($l));
            
            //產生json
            $json = array();
            $json[] = $l['title'];
            $json[] = $l['link'];
            $json[] = 'http://'.$_SERVER["SERVER_NAME"].'/qlink/'.$l['image'];
            file_put_contents('json/'.$l['id'],json_encode($json));
            
        }
        
        foreach((array) $categorys as $category => $datas){
            $db = new database('category_id_'.$category);
            $db->data = $datas;
            $db->save();
        }
        $db = new database('category_id');
        $db->data = $category_id;
        $db->save();
    }
}

class page{
    
    /*
        一頁幾格
    */
    static $max = 15;
    
    public static function getcontent(){
        if(empty($_GET['p'])) $_GET['p'] = 1;
        else $_GET['p'] = preg_replace('/[^0-9]+/','',$_GET['p']);
        
        $from = ($_GET['p']-1)*self::$max;
        
        $to = $from+self::$max-1;
        
        $links = new database('links');
        
        $data = $links->data;
        $data = array_reverse($data);
        $content = '';
        
        for($i=$from;$i<=$to;$i++){
            if(empty($data[$i])) break;
            $l = $data[$i];
            $content.=self::html($l);
        }
        return $content;
    }
    
    public static function html($l){
        $id = $l['id'];
        $link = $l['link'];
        $image = $l['image'];
        $title = $l['title'];
        $width = $l['width'];
        $height = $l['height'];
        $category = $l['category'];
        $target = '_blank';
        
        $p=' clickfor="editlink" ';
        $p.='link_id="'.$id.'" ';
        $p.='width="'.$width.'" ';
        $p.='height="'.$height.'" ';
        $p.='title="'.$title.'" ';
        $p.='link="'.$link.'" ';
        $p.='image="'.$image.'" ';
        $p.='category="'.$category.'" ';
        
        $content = '';
        $content.='<a class="link_wrapper" style="width:'.$width.'px;height:'.$height.'px;" '.$p.'>';
        $content.='<img src="'.$image.'" style="width:'.$width.'px;height:'.$height.'px;" '.$p.'>';
        $content.='<h2 '.$p.'>'.$title.'</h2></a>';
        return $content;
    }
    
    public static function totalpage(){
        $data = (new database('links'))->data;
        $count = count($data);
        $total = floor(($count-1)/15 + 1);
        return $total;
    }
    
    /*
        產生 導覽行 
    */
    public static function getnav(){
        $html = '';
        $max = self::totalpage();
        
        $html.='<div class="nav">';
        for($i=1;$i<=$max;$i++){
            $html.= '<a href="?p='.$i.'">'.$i.'</a>';
        }
        $html.='</div>';
        return $html;
    }
}