<?php
function passencode($pass){
    $str1="q4m0ttrqewri-12351";
    $str2="12f31*324628^";
    $pt1=md5($pass.$str2);
    $pt2=sha1($pt1.$str2);
    return sha1($str1.$pt2.$pass);
}
function mystrfit($str_fit){
    $dsans=strpbrk($str_fit,"<>?!#/\\\'\";%{}");
    if ($dsans!=false){
	    echo 'Error: 输入存在非法字符。'.$str_fit;
	    exit();
    }
}
function aexit($alert,$url){
	echo '<meta charset="utf-8"><script type="text/javascript">alert("'.$alert.'");window.location.href="'.$url.'";</script>';
	exit();
}
function get_ip(){
//	if($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]){
//		$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
//	}
//	else if($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]){
//		$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
//	}
//	else if($HTTP_SERVER_VARS["REMOTE_ADDR"]){
//		$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
//	}
//	else if(getenv("HTTP_X_FORWARDED_FOR")){
//		$ip = getenv("HTTP_X_FORWARDED_FOR");
//	}
//	else if(getenv("HTTP_CLIENT_IP")){
//		$ip = getenv("HTTP_CLIENT_IP");
//	}
//	else if(getenv("REMOTE_ADDR")){
		$ip = getenv("REMOTE_ADDR");
//	}
//	else{
//		$ip = "Unknown";
//	}
	return $ip;
}
class loginAction extends Action {
    public function index(){
        if(session('login')=="1"){
            echo '<meta charset="utf-8"><script type="text/javascript">alert("您已经登录。");
                  window.location.href="/";</script>';
            exit();
        }
        $this->display();
    }
    public function dologin($email,$pass){
        $iv['pass']=passencode($pass);
        $iv['email']=$email;
        
        $userQuery=M('user');
        $data=$userQuery->where($iv)->select();
		
        if($data){
            $type=$data[0]['type'];
            session('uid',$data[0]['uid']);
            session('name',$data[0]['name']);
            session('type',$type);
            session('gid',$data[0]['gid']);
            
            session('login','1');
            if($type==8){
                echo "banned";
            }else{
                echo "success";
            }
        }else{
            echo "eps";
        }
		
		$ec['email']=$email;
		$ec['uid']=session('uid');
		$ec['username']=session('name');
		$ec['ip']=get_ip();
		$loginQuery=M('login');
		$loginQuery->add($ec);
    }
    public function unlogin(){
        $login_status=session('login');
        if('1'!=$login_status){
            echo "Error: 你本来就没登录，不要在这里试着注销了。";
            return;
        }else{
            session('login','0');
            echo "success";
        }
    }
	public function resetpassword(){
		$this->display();
	}
	public function getpassemail($email){
		$iv['email']=$email;
        $userQuery=M('user');
        $data=$userQuery->where($iv)->find();
		
		if($data){
			$name=$data['name'];
			$find_key=md5("CCBC7".$data['uid'].$data['pass'].$email."Namido");
			$find_back_url="http://ccbc7.tk/login/resetpass/email/".urlencode($email)."/key/".$find_key;
			
			$mailSender=new SaeMail();
			$mailArr['from']="account@ccbc7.tk";
			$mailArr['to']=$iv['email'];
			$mailArr['smtp_host']="smtp.ym.163.com";
			$mailArr['smtp_port']=25;
			$mailArr['smtp_username']="account@ccbc7.tk";
			$mailArr['smtp_password']="cipherccbc7";
			$mailArr['subject']="CCBC7密码找回确认信--".$name;
			$mailArr['content']='<style>a {color:white;}</style>
	<div style="background-color:#000;text-align:center; font-family:微软雅黑; font-size: 16px;color:white">
	<p><img src="http://bcs.duapp.com/feelyblog/logom.jpg" height="96" width="200"></p>
	<hr noshade style="height:2px;">
	<p>CCBC7密码找回确认信。</p>
	<p>&nbsp;</p>
	<p>你正在试图找回你在CCBC7注册的账号：'.$name.' &lt;'.$email.'&gt;</p>
	<p>&nbsp;</p>
	<p>请点击以下链接或把链接复制到浏览器地址栏完成最后一步：</p>
	<p><a href="'.$find_back_url.'" target="_blank">'.$find_back_url.'</a></p>
	<p>&nbsp;</p>
	<hr noshade style="height:2px;">
	<p style="font-size:12px">请密切关注百度密码吧获得最新信息</p>
	<p style="font-size:12px">本邮件发自系统，请不要直接回复该邮件。如有疑问，请向i@ccbc7.tk发邮件询问。</p>
	</div>';
			$mailArr['content_type']="HTML";
			$mailSender->setOpt($mailArr);
			$mr=$mailSender->send();
			
			if($mailSender->errno()==0){
            	echo "success";
			}else{
				echo $mailSender->errmsg();
			}
		}else{
			echo "错误：找不到该用户。";
		}
	}
	public function resetpass($email,$key){
		$userQuery=M('user');
		$iv['email']=urldecode($email);
		$data=$userQuery->where($iv)->find();
		if(!$data){
			exit("参数错误。");
		}
		$tr_key=md5("CCBC7".$data['uid'].$data['pass'].$email."Namido");
		if($tr_key!=$key){
			exit("认证失败。");
		}
		
		$as['email']=$email;
		$as['key']=$key;
		$this->assign($as);
		$this->display();
	}
	public function uppass(){
		$email=$this->_post('email');
		$key=$this->_post('key');
		$pass=passencode($this->_post('newpass'));
		
		$userQuery=M('user');
		$iv['email']=$email;
		$data=$userQuery->where($iv)->find();
		if(!$data){
			exit("参数错误。");
		}
		$tr_key=md5("CCBC7".$data['uid'].$data['pass'].$email."Namido");
		if($tr_key!=$key){
			exit("认证失败。");
		}
		
		$new['pass']=$pass;
		$updStat=$userQuery->where($iv)->save($new);
		if($updStat==1){
			echo 'success';
		}else{
			echo "错误：数据库出错。";
		}
	}
	public function b($code="0", $state="x"){
		if($state!="nzym") exit("验证错误");
		
		$at_param['grant_type']="authorization_code";
		$at_param['code']=$code;
		$at_param['client_id']="l3wrD2mCUM6qsZVoYzLzocr8";
		$at_param['client_secret']="iSo4momnnvDtttxaBBqmAvGGI8DKqgpy";
		$at_param['redirect_uri']="http%3A%2F%2Fccbc7.tk%2Flogin%2Fb";
		$_at_post_param=array();
		foreach ($at_param as $k => $v){
			$_at_post_param[]=$k."=".$v;
		}
		$at_post_data=implode("&",$_at_post_param);
		//echo $at_post_data."<br>";
		
		
		$at_link=curl_init();
		curl_setopt($at_link, CURLOPT_URL, "https://openapi.baidu.com/oauth/2.0/token");
		curl_setopt($at_link, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($at_link, CURLOPT_USERAGENT, "NamidoPuzzle/5.0 (PHP; SAE) FeelyBlog/1.0 CCBC7/1.0");
		//curl_setopt($at_link, CURLOPT_PORT, 443);
		curl_setopt($at_link, CURLOPT_POST, 1);
		curl_setopt($at_link, CURLOPT_POSTFIELDS, $at_post_data);
		
		$at_dataraw=curl_exec($at_link);
		curl_close($at_link);
				
		$at_data=json_decode($at_dataraw, true);
		$access_token=$at_data['access_token'];
		//print_r($at_data);
		
		$url="https://openapi.baidu.com/rest/2.0/passport/users/getLoggedInUser?access_token=".$access_token;
		
		$p_link=curl_init();
		curl_setopt($p_link, CURLOPT_URL, $url);
		curl_setopt($p_link, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($p_link, CURLOPT_USERAGENT, "NamidoPuzzle/5.0 (PHP; SAE) FeelyBlog/1.0 CCBC7/1.0");
		//curl_setopt($p_link, CURLOPT_PORT, 443);
		
		$p_dataraw=curl_exec($p_link);
		curl_close($p_link);
		
		$p_data=json_decode($p_dataraw, true);
		
		$bdid=$p_data['uid'];
		$uv['pass']="BDID".$bdid;
		
		$userQuery=M('user');
		$userData=$userQuery->where($uv)->select();
		if($userData){
			$type=$userData[0]['type'];
            session('uid',$userData[0]['uid']);
            session('name',$userData[0]['name']);
            session('type',$type);
            session('gid',$userData[0]['gid']);
            
            session('login','1');

			$ec['email']=$userData[0]['email'];
			$ec['uid']=$userData[0]['uid'];
			$ec['username']=$userData[0]['name'];
			$ec['ip']=get_ip();
			$loginQuery=M('login');
			$loginQuery->add($ec);
            if($type==8){
                aexit("当前账号已被封禁，请联系管理员。","/");
            }else{
                aexit("您已登录成功。","/");
            }
		}else{
			$as['uname']=$p_data['uname'];
			$as['bdid']="BDID".$bdid;
			$this->assign($as);
			$this->display();
		}
	}
}