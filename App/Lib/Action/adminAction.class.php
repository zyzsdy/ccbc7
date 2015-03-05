<?php
function islogin(){
    if(session('login')!="1"){
        echo '<meta charset="utf-8"><script type="text/javascript">alert("未登录。");
              window.location.href="/";</script>';
        exit();
    }
}
function isAdmin(){
    islogin();
    $tp=session('type');
    if($tp==2||$tp==4){
        
    }else{
        exit("CCBC7 | 你没有权限访问这里。");
    }
}
function aexit($alert,$url){
	echo '<meta charset="utf-8"><script type="text/javascript">alert("'.$alert.'");window.location.href="'.$url.'";</script>';
	exit();
}
function sortByAllnum($a,$b){
	if($a['rank']>$b['rank']) return -1;
	else if($a['rank']<$b['rank']) return 1;
	if($a['fmfin']>$b['fmfin']) return -1;
	else if($a['fmfin']<$b['fmfin']) return 1;
	if($a['metanum']>$b['metanum']) return -1;
	else if($a['metanum']<$b['metanum']) return 1;
	if($a['allnum']>$b['allnum']) return -1;
	else if($a['allnum']<$b['allnum']) return 1;
	if($a['gid']>$b['gid']) return -1;
	else if($a['gid']<$b['gid']) return 1;
	else return 0;
}
function getBit($n,$pos){
	return ($n>>$pos)&1;
}
class adminAction extends Action {
    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }
    public function index() {
        isAdmin();
        $this->display();
    }
	public function userlist(){
		isAdmin();
		$userQuery=M('user');
		$userData=$userQuery->order('uid')->select();
		$as['ulist']=$userData;
		$this->assign($as);
		$this->display();
	}
	public function grouplist(){
		isAdmin();
		$groupQuery=M('group');
		$groupData=$groupQuery->select();
		foreach($groupData as &$gp){
			switch ($gp['gid']) {
				case 9:$gp['rank']=9000;break;
				case 90:$gp['rank']=8999;break;
				case 15:$gp['rank']=8998;break;
				case 78:$gp['rank']=8997;break;
				case 65:$gp['rank']=8996;break;
				default:$gp['rank']=0;break;
			}
			$savedataStr=$gp['data'];
			$saveArray=explode(",",$savedataStr,14);
			$gp['nrfin']=$saveArray[1];
			$gp['fmfin']=$saveArray[11];
			$nrnum=0;
			$allnum=intval($saveArray[1]);
			$allnum+=intval($saveArray[11]);
			$gp['metanum']=0;
			for($i=0;$i<7;$i++){
				$datanum=intval($saveArray[$i+4]);
				for($j=0;$j<8;$j++){
					$tt=getBit($datanum,$j);
					if($j<2){
						$nrnum+=$tt;
					}
					$allnum+=$tt;
				}
				if($datanum>127) $gp['metanum']++;
			}
			$gp['nrnum']=$nrnum;
			$gp['allnum']=$allnum;
			$openareas="";
			$opaData=intval($saveArray[3]);
			for($i=0;$i<7;$i++){
				$ca=getBit($opaData,$i)?true:false;
				if($ca){
					$openareas.=(chr(ord("A")+$i)." ");
				}
			}
			$gp['openareas']=$openareas;
		}
		uasort($groupData,"sortByAllnum");
		$as['ulist']=$groupData;
		$this->assign($as);
		$this->display();
	}

	public function timustat(){
		isAdmin();
		//总计
		$groupQuery=M('group');
		$groupData=$groupQuery->select();
		$tmStat=array();
		foreach($groupData as $gp){
			$savedataStr=$gp['data'];
			$saveArray=explode(",",$savedataStr,14);
			for($i=0;$i<7;$i++){
				$datanum=intval($saveArray[$i+4]);
				for($j=0;$j<8;$j++){
					$tmStat[$i][$j]+=getBit($datanum,$j);
				}
			}
			$tmStat['nm']+=intval($saveArray[1]);
			$tmStat['mm']+=intval($saveArray[11]);
		}
		//砸题石记录
		$ztsQuery=M('status');
		$ztsData=$ztsQuery->where("uid=0 and res='OK'")->select();
		$ztsStat=array();
		foreach ($ztsData as $zp) {
			$areaId=ord($zp['tmid'][0])-ord('A');
			$probId=$zp['tmid'][1]-1;
			$ztsStat[$areaId][$probId]++;
		}
		//剩余记录
		$rsStat=array();
		for($i=0;$i<7;$i++){
			for($j=0;$j<8;$j++){
				$rsStat[$i][$j]=$tmStat[$i][$j]-$ztsStat[$i][$j];
			}
		}
		$rsStat['nm']=$tmStat['nm'];
		$rsStat['mm']=$tmStat['mm'];
		$as['ts']=$tmStat;
		$as['zts']=$ztsStat;
		$as['rs']=$rsStat;
		//print_r($as);
		$this->assign($as);
		$this->display();
	}

	public function loginlist($start=0){
		isAdmin();
		$loginQuery=M('login');
		$loginData=$loginQuery->order('id desc')->limit($start,500)->select();
		//$loginData=$loginQuery->order('id desc')->select();
		$as['ulist']=$loginData;
		$this->assign($as);
		$this->display();
	}
	public function loginlistu($uid){
		isAdmin();
		$loginQuery=M('login');
		$loginData=$loginQuery->where('uid='.$uid)->order('id desc')->select();
		$as['ulist']=$loginData;
		$this->assign($as);
		$this->display('loginlist');
	}
	
	public function mailtest($email){
		$mailSender=new SaeMail();
		$mailArr['from']="reg@ccbc7.tk";
		$mailArr['to']=$email;
		$mailArr['smtp_host']="smtp.ym.163.com";
		$mailArr['smtp_port']=25;
		$mailArr['smtp_username']="reg@ccbc7.tk";
		$mailArr['smtp_password']="cipherccbc7";
		$mailArr['subject']="测试邮件的标题";
		$mailArr['content']='<style>a {color:white;}</style>
	<div style="background-color:#000;text-align:center; font-family:微软雅黑; font-size: 16px;color:white">
	<p><img src="http://bcs.duapp.com/feelyblog/logom.jpg" height="96" width="200"></p>
	<hr noshade style="height:2px;">
	<p>感谢您注册CCBC7，以下是你的账号信息：</p>
	<p>E-mail：'.$email.'</p>
	<p>昵称：'.session('name').'</p>
	<p>&nbsp;</p>
	<p>如果账号信息有误，你可以登陆网站<a href="http://ccbc7.tk" target="_blank">http://ccbc7.tk</a>进行修改。</p>
	<p>&nbsp;</p>
	<p>报名过程还没有完成，请查看<a href="http://ccbc7.tk/reg/regproc" target="_blank">报名流程http://ccbc7.tk/reg/regproc</a>完成下一步的组队注册。</p>
	<p>我们在8月8日等着你的到来！</p>
	<hr noshade style="height:2px;">
	<p style="font-size:12px">请密切关注百度密码吧获得报名的最新信息</p>
	<p style="font-size:12px">本邮件发自系统，请不要直接回复该邮件。如有疑问，请向i@ccbc7.tk发邮件询问。</p>
	<p style="font-size:12px">本邮件仅作为注册信息提示，我们不会验证你填入的邮箱有效性。</p>
	<p style="font-size:12px">如果你并没有注册过CCBC7，可能是他人误填了你的邮箱地址，请不要理会此邮件。</p>
	</div>';
		$mailArr['content_type']="HTML";
		$mailSender->setOpt($mailArr);
		$mr=$mailSender->send();
		
			echo $mailSender->errno();
			echo $mailSender->errmsg();
	}
	public function getemailaddress(){
		isAdmin();
		$userQuery=M('user');
		$userData=$userQuery->order('uid')->select();
		$emailArr=array();
		foreach ($userData as $value) {
			$emailArr[]=$value['email'];
		}
		$count=0;
		foreach($emailArr as $em){
			$Strstr .= ($em."; ");
			$count++;
			if($count%30==0){
				$Strstr .= "<br><br>";
			}
		}
		echo $Strstr;
	}
	public function sendemail($uids,$mailc,$subject){
		isAdmin();
		$mailStr=htmlspecialchars_decode($mailc);

		//查询Email地址
		if($uids=="ALL"){
			$userQuery=M('user');
			$userData=$userQuery->order('uid')->select();
			$emailArr=array();
			foreach ($userData as $value) {
				$emailArr[]=$value['email'];
			}
		}else{
			$map['uid']=array('in',$uids);
			$userQuery=M('user');
			$userData=$userQuery->where($map)->select();
			$emailArr=array();
			foreach ($userData as $value) {
				$emailArr[]=$value['email'];
			}
		}
		

		//Email发送
		$sucNum=0;
		$failNum=0;
		$mailSender=new SaeMail();
		foreach ($emailArr as $email) {
			$mailArr['from']="npc@ccbc7.tk";
			$mailArr['to']=$email;
			$mailArr['smtp_host']="smtp.ym.163.com";
			$mailArr['smtp_port']=25;
			$mailArr['smtp_username']="npc@ccbc7.tk";
			$mailArr['smtp_password']="cipherccbc7";
			$mailArr['subject']=$subject;
			$mailArr['content']=$mailStr;
			$mailArr['content_type']="HTML";
			$mailSender->setOpt($mailArr);
			$mr=$mailSender->send();
			if($mailSender->errno()==0){
				$sucNum++;
			}else{
				$failNum++;
			}
			$mailSender->clean();
		}

		echo "Email队列完成\n成功：".$sucNum."，失败：".$failNum;
	}
	
	public function problemedit(){
		isAdmin();
		$this->display();
	}
	public function tmedit($tmid){
		isAdmin();
		$timuQuery=M('timu');
		$tv['tmid']=$tmid;
		$timuData=$timuQuery->where($tv)->find();
		$this->assign($timuData);
		$this->display();
	}
	public function tmpreview($tmid){
		isAdmin();
		$tv['tmid']=$tmid;
		$tmData=M('timu')->where($tv)->find();
		$this->assign($tmData);
		$this->display();
	}
	public function tmeditdo(){
		isAdmin();
		$tmid=$this->_post('tmid');
		$answer=$this->_post('answer');
		$content=$this->_post('content');
		$content=htmlspecialchars_decode($content);
		$timuQuery=M('timu');
		$tv['tmid']=$tmid;
		$tv['answer']=$answer;
		$tv['md5ans']=md5($answer);
		$tv['content']=$content;
		$svs=$timuQuery->save($tv);
		if($svs==false){
			echo '数据库错误：保存失败。';
		}else{
			echo '保存成功。';
		}
	}
	public function getstatus($offset=0){
		isAdmin();
		$statuQuery=M('status');
		
		$status=$statuQuery->limit($offset,500)->order('id desc')->select();
		$as['nowoffset']=$offset;
		$as['status']=$status;
		$this->assign($as);
		$this->display();
	}
	public function groupinfo($gid){
		isAdmin();
		if($gid==""){
			exit("参数错误。");
		}
		
		$groupQuery=M('group');
		$as=$groupQuery->where("gid=".$gid)->find();
		$userQuery=M('user');
		for($i=0;$i<5;$i++){
			$as['user'.$i]=$userQuery->where("uid=".$as['u'.$i])->find();
		}
		
		//存档解码
		$saveArraystr=explode(",",$as['data'],14);
		for($i=0;$i<7;$i++){
			$as['sec'][$i]['open']=getBit(intval($saveArraystr[3]),$i);
			$as['sec'][$i]['name']=chr($i+ord("A"));
			for($j=0;$j<7;$j++){
				$as['sec'][$i]['prob'][$j]['fin']=getBit(intval($saveArraystr[4+$i]),$j);
			}
			$as['sec'][$i]['meta']=getBit(intval($saveArraystr[4+$i]),7);
		}
		$as['normalmeta']=$saveArraystr[1];
		$as['finalmeta']=$saveArraystr[11];
		$as['newareaflag']=$saveArraystr[2];
		$as['coinnum']=$saveArraystr[12];

		$itemStatus=json_decode($saveArraystr[13],true);
		$as['errortime']=$itemStatus['errortime'];
		$as['zts']=$itemStatus['zts'];
		$as['tipr']=$itemStatus['tipr'];
		$as['cdr']=$itemStatus['cdr'];
		$as['cdt']=$itemStatus['cdt'];

		//本组最近100条提交记录
		$stQuery=M('status');
		$stData=$stQuery->where('gid='.$gid)->order('id desc')->limit(100)->select();
		$as['laststatus']=$stData;
		
		$this->assign($as);
		$this->display();
	}
	
	public function searchlog($key="uid",$way="equal",$cond="0",$order="id",$orderway=""){
		isAdmin();
		
		$statuQuery=M('status');
		
		if($way=="equal"){
			$where=$key."='".urldecode($cond)."'";
		}else{
			$where=$key." LIKE '%".urldecode($cond)."%'";
		}
		
		echo "SQL查询子句：where ".$where." order by ".$order." ".$orderway;
		$data=$statuQuery->where($where)->order($order." ".$orderway)->select();
		$as['result']=$data;
		
		$this->assign($as);
		$this->display();
	}
	public function imgupload(){
		isAdmin();
		$imguQuery=M('imgupload');
		
		$imgData=$imguQuery->order('id desc')->select();
		foreach($imgData as &$k){
			switch($k['markpos']){
				case "lu": $k['mpos']="左上"; break;
				case "ru": $k['mpos']="右上"; break;
				case "lb": $k['mpos']="左下"; break;
				case "rb": $k['mpos']="右下"; break;
			}
		}
		$as['imglist']=$imgData;
		$this->assign($as);
		$this->display();
	}
	public function uploaddo(){
		import('ORG.Net.UploadFile');
		
		isAdmin();
		//获取题号、版本等信息
		$tmid=$this->_post('up_tmid');
		$ver=$this->_post('up_ver');
		$markpos=$this->_post('up_mark');
		//保存上传的文件
		$upload=new UploadFile();
		$upload->maxSize=4000000;
		$upload->allowExts=array('png','jpg');
		$upload->savePath='./probimg/'.$tmid.'/'.$ver.'/';
		
		if(!$upload->upload()){
			aexit("上传错误：".$upload->getErrorMsg(),"/admin");
		}else{
			$info=$upload->getUploadFileInfo();
		}
		//记录到数据库
		$imgQuery=M('imgupload');
		$iuv['tmid']=$tmid;
		$iuv['ver']=$ver;
		$iuv['oriurl']='http://ccbc7-probimg.stor.sinaapp.com/'.$tmid.'/'.$ver.'/'.$info[0]['savename'];
		$iuv['lnkurl']='/images/img/tmid/'.$tmid.'/ver/'.$ver.'/name/'.$info[0]['savename'];
		$iuv['markpos']=$markpos;
		$imgQuery->add($iuv);
		aexit("上传成功","/admin");
	}
	public function banlist(){
		isAdmin();
		$userQuery=M('user');
		$userData=$userQuery->where('type=8')->order('uid')->select();
		$as['banlist']=$userData;
		$this->assign($as);
		$this->display();
	}
	public function banit($banuid){
		isAdmin();
		$userQuery=M('user');
		$uv['uid']=$banuid;
		$uv['type']=8;
		$res=$userQuery->save($uv);
		if($res){
			echo "封禁成功。";
		}else{
			echo "数据库出错。";
		}
	}
	public function unban($uid,$type){
		isAdmin();
		$userQuery=M('user');
		$uv['uid']=$uid;
		$uv['type']=$type;
		$res=$userQuery->save($uv);
		if($res){
			echo "取消成功。";
		}else{
			echo "数据库出错。";
		}
	}
	public function announce(){
		isAdmin();
		$annoQuery=M('anno');
		$annoData=$annoQuery->order('id desc')->select();
		$as['annolist']=$annoData;
		$this->assign($as);
		$this->display();
	}
	public function addannounce($content){
		isAdmin();
		$annoQuery=M('anno');
		$data['content']=$content;
		$data['other']="系统公告。";
		$res=$annoQuery->add($data);
		if($res){
			echo "公告发布成功。";
		}else{
			echo "公告发布失败。";
		}
	}
	public function jindu(){
		isAdmin();
		$jdQuery=M('jdbb');
		$jdData=$jdQuery->order('id desc')->select();
		$as['jdlist']=$jdData;
		$this->assign($as);
		$this->display();
	}
	public function addjindu($title,$content){
		isAdmin();
		$jdQuery=M('jdbb');
		$data['title']=$title;
		$data['content']=$content;
		$res=$jdQuery->add($data);
		if($res){
			echo "发布成功。";
		}else{
			echo "发布失败。";
		}
	}
	public function spm($rgid=0){
		isAdmin();
		$pmQuery=M('mail');
		$pmData=$pmQuery->where('sgid=8888')->order('mid desc')->select();
		$as['spmlist']=$pmData;
		$as['r_rgid']=$rgid;
		$this->assign($as);
		$this->display();
	}
	public function pmsend($rgid,$content){
		isAdmin();
		$pmQuery=M('mail');
		$data['sgid']=8888;
		$data['sgname']="C7出题组";
		$data['suid']=session('uid');
		$data['rgid']=$rgid;
		$data['content']=$content;
		$data['eu']=0;
		$res=$pmQuery->add($data);
		if($res){
			echo "发送成功。";
		}else{
			echo "发送失败。";
		}
	}
	public function rpm(){
		isAdmin();
		$pmQuery=M('mail');
		$pmData=$pmQuery->where('rgid=8888')->order('mid desc')->select();
		$as['rpmlist']=$pmData;
		$this->assign($as);
		$this->display();
	}
	public function convertPerson(){
		isAdmin();
		$as['ulist']=M('user')->where('type=0')->order('uid')->select();
		$this->assign($as);
		$this->display();
	}
	public function getConvertPerson($uid){
		isAdmin();
		//读取个人信息
		$userQuery=M('user');
		$userData=$userQuery->where('uid='.$uid)->find();

		//检查重名？
		$CgnV['gname']=$userData['name'];
		$groupQuery=M('group');
        $gC=$groupQuery->where($CgnV)->select();
        $countGName=count($gC);
        if($countGName!=0){
            echo "这个名字已经有了。";
            exit();
        }

        //准备组队数据
        $gv['gname']=$userData['name'];
        $gv['gnum']=1;
        $gv['u0']=$uid;
        $gv['info']="系统自动生成——个人组队";

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
	}
}

?>
