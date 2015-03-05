<?php
function islogin(){
    if(session('login')!="1"){
        echo '<meta charset="utf-8"><script type="text/javascript">alert("未登录。");
              window.location.href="/";</script>';
        exit();
    }
}
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
class regAction extends Action {
    public function index(){
        if(session('login')=="1"){
            echo '<meta charset="utf-8"><script type="text/javascript">alert("您已经登录，请先注销。");
                  window.location.href="/";</script>';
            exit();
        }
        $this->display();
    }
    public function checkinfo($email,$name){
        $email=urldecode($email);
        $name=urldecode($name);
        $userQuery=M('user');
        $iv['email']=$email;
        $iv2['name']=$name;
        $emailC=$userQuery->where($iv)->select();
        $num1=count($emailC);
        $nameC=$userQuery->where($iv2)->select();
        $num2=count($nameC);
        echo $num1.",".$num2;
    }
    public function insert(){ 
        //获取post数据
        $iv['name']=$this->_post('name');
        mystrfit($iv['name']);
        $iv['email']=$this->_post('email');
        mystrfit($iv['email']);
        $pass=$this->_post('pass');
        $iv['info']=$this->_post('info');
        mystrfit($iv['info']);
		$type=$this->_post('type');
        
		if($type=="BDID"){
			$iv['pass']=$pass;
		}else{
        	$iv['pass']=passencode($pass);
		}
        
        $userQuery=M('user');
        $uid=$userQuery->add($iv);
                    
        if($uid){
			$mailSender=new SaeMail();
			$mailArr['from']="reg@ccbc7.tk";
			$mailArr['to']=$iv['email'];
			$mailArr['smtp_host']="smtp.ym.163.com";
			$mailArr['smtp_port']=25;
			$mailArr['smtp_username']="reg@ccbc7.tk";
			$mailArr['smtp_password']="cipherccbc7";
			$mailArr['subject']="注册成功——CCBC7";
			$mailArr['content']='<style>a {color:white;}</style>
		<div style="background-color:#000;text-align:center; font-family:微软雅黑; font-size: 16px;color:white">
		<p><img src="http://bcs.duapp.com/feelyblog/logom.jpg" height="96" width="200"></p>
		<hr noshade style="height:2px;">
		<p>感谢您注册CCBC7，以下是你的账号信息：</p>
		<p>E-mail：'.$iv['email'].'</p>
		<p>昵称：'.$iv['name'].'</p>
		<p>&nbsp;</p>
		<p>如果账号信息有误，你可以登陆网站<a href="http://ccbc7.tk" target="_blank">http://ccbc7.tk</a>进行修改。</p>
		<p>&nbsp;</p>
		<hr noshade style="height:2px;">
		<p style="font-size:12px">CCBC7是百度密码吧举办的密码大赛，请密切关注百度密码吧</p>
		<p style="font-size:12px">本邮件发自系统，请不要直接回复该邮件。如有疑问，请向i@ccbc7.tk发邮件询问。</p>
		<p style="font-size:12px">本邮件仅作为注册信息提示，我们不会验证你填入的邮箱有效性。</p>
		<p style="font-size:12px">如果你并没有注册过CCBC7，可能是他人误填了你的邮箱地址，请不要理会此邮件。</p>
		</div>';
			$mailArr['content_type']="HTML";
			$mailSender->setOpt($mailArr);
			$mr=$mailSender->send();
			
			if($mailSender->errno()==0){
            	
			}else{
				exit( $mailSender->errmsg() );
			}

            //检查重名？
            $CgnV['gname']=$iv['name'];
            $groupQuery=M('group');
            $gC=$groupQuery->where($CgnV)->select();
            $countGName=count($gC);
            if($countGName!=0){
                echo "这个名字已经有了。";
                exit();
            }

            //准备组队数据
            $gv['gname']=$iv['name'];
            $gv['gnum']=1;
            $gv['u0']=$uid;
            $gv['info']="系统自动生成——赛后自由注册";

            //组队数据添加
            $newGid=$groupQuery->add($gv);
            if(!$newGid){
                echo "添加组队时数据库错误。";
                exit();
            }

            //回写用户数据
            $uv['type']=6;
            $uv['gid']=$newGid;
            $upD=$userQuery->where('uid='.$uid)->save($uv);
            if($upD==1){
                echo "success";
            }else{
                echo "组队建立成功，GID为".$newGid."。但是更新信息出错。";
            }
			
        }else{
            echo "Error: 不能创建新用户。";
        }
    }
    public function groupreg(){
        islogin();
        $type=session('type');
        if($type!=0){
            echo "据我这些日子的观测，你好像已经有一个队伍了。";
            exit();
        }
        //获取post数据
        $iv['gname']=$this->_post('gname');
        $iv['info']=$this->_post('info');
        $it['gname']=$iv['gname'];
        //$vcode=$this->_post('vcode');
        
        //if($vcode!="I LIKE CCBC7."){
        //    echo "验证码不正确。再想想指引者的话吧。";
        //    exit();
        //}
        $groupQuery=M('group');
        $gC=$groupQuery->where($it)->select();
        $countGName=count($gC);
        if($countGName!=0){
            echo "这个名字已经有了。是不是可以打算换一个呢？";
            exit();
        }
        $userQuery=M('user');
        $us['uid']=session('uid');
        $iv['u0']=$us['uid'];
        
        $newGid=$groupQuery->add($iv);
        if(!$newGid){
            echo "在创建组的时候好像出错了。";
            exit();
        }
        
        $uv['type']=6;
        $uv['gid']=$newGid;
        $updata=$userQuery->where($us)->save($uv);
        
        if($updata==1){
            echo "success";
        }else{
            echo "组队建立成功，你的队伍GID为".$newGid."。但是在更新你的信息的时候出错了。<br>请联系管理员处理。";
        }
    }
	public function regproc(){
		echo "<style>body{background-color:#000000;}</style>";
		echo "<p style=\"text-align:center;\"><img src=\"http://ccbc7-static.stor.sinaapp.com/img/regproc.jpg\"></p>";
	}
}