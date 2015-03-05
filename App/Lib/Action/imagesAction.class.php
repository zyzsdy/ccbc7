<?php
function testuser(){	
	//测试状态：
	//测试用户浏览器的特殊Cookie
	//$testTrue=cookie('TestTempValueB83219');
	//if($testTrue!="Namidoccbc7tk8895948"){
	//	return false;
	//}
	return true;
}
class imagesAction extends Action {
	protected function _initialize() {
        session(array('cache_limiter'=>'public'));
        header('Cache-control: max-age=300');
        header('Pragma: public');
    }
    public function index() {
        echo "CCBC7 | 无法显示，参数不正确";
    }
	protected function errorimg($message){
		$img=file_get_contents('http://ccbc7-probimg.stor.sinaapp.com/TEST/2/53a66d9fdc561.png');
		$re_img=new SaeImage($img);
		$re_img->annotate("Error ".$message."\nFrom ".session('name')."\nCCBC7.tk © 2014", 1.0, SAE_SouthWest, array("name"=>SAE_MicroHei,"size"=>13,"weight"=>600,"color"=>"white"));
		//图片输出
		$re_img->exec('png',true);
		exit();
	}
	public function img($tmid="TEST",$ver="1",$name="53a64db1804c8.png"){
		if(session('login')!="1"){
			$this->errorimg("未登录 | 您必须先登录才能查看图片。");
		}
		if(!testuser()){
			$this->errorimg("测试中 | 当前暂未开放。");
		}
		//缓存输出
		$ETag=md5($tmid.$ver.$name);
		$http_match=$this->_server('HTTP_IF_NONE_MATCH');
		if($http_match==$ETag){
			header('HTTP/1.1 304 Not Modified');
			exit();
		}else{
			header('Etag: '.$ETag);
		}
		//header('Content-Type: image/png');
		
		//读取图片
		$img=file_get_contents('http://ccbc7-probimg.stor.sinaapp.com/'.$tmid.'/'.$ver.'/'.$name);
		
		//读取水印位置
		$wm_pos=M('imgupload')->where(array("tmid"=>$tmid,"ver"=>$ver))->getField('markpos');
		switch($wm_pos){
			case "lu": $img_grav=SAE_NorthWest;break;
			case "ru": $img_grav=SAE_NorthEast;break;
			case "lb": $img_grav=SAE_SouthWest;break;
			case "rb": $img_grav=SAE_SouthEast;break;
			default: $img_grav=SAE_SouthWest;
		}
		//初始化图片
		$re_img=new SaeImage($img);
		$re_img->annotate("From ".session('name')."\nCCBC7.tk © 2014", 1.0, $img_grav, array("name"=>SAE_MicroHei,"size"=>13,"weight"=>600,"color"=>"#ECECEC"));
		//图片输出
		$re_img->exec('png',true);
		
		$error_no=$re_img->errno();
		$error_msg=$re_img->errmsg();
		
		if($error_no!=0){
			$this->errorimg($error_no." - ".$error_msg);
		}
	}
}