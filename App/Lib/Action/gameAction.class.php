<?php
function islogin(){
    if(session('login')!="1"){
        echo '<meta charset="utf-8"><script type="text/javascript">alert("未登录。");
              window.location.href="/";</script>';
        exit();
    }
    if(session('type')==8){
    	echo '<meta charset="utf-8"><script type="text/javascript">alert("您的账号已经被封禁。请联系管理员");
              window.location.href="/";</script>';
        exit();
    }
}
function testuser(){	
	//测试状态：
	//测试用户浏览器的特殊Cookie
	//$testTrue=cookie('TestTempValueB83219');
	//if($testTrue!="Namidoccbc7tk8895948"){
	//	echo '<meta charset="utf-8"><script type="text/javascript">alert("测试中。暂未开放。");
    //         window.location.href="/";</script>';
    //    exit();
	//}
}

function selectData(){
	$saveData="";
	if(session('gid')==0){
		$userQuery=M('user');
		$saveData=$userQuery->where("uid=".session('uid'))->getField('data');
	}else{
		$groupQuery=M('group');
		$saveData=$groupQuery->where("gid=".session('gid'))->getField('data');
	}
	return $saveData;
}

function getFinish($data){
	$ans=array();
	$ans['t1']=getBit($data,0);
	$ans['t2']=getBit($data,1);
	$ans['t3']=getBit($data,2);
	$ans['t4']=getBit($data,3);
	$ans['t5']=getBit($data,4);
	$ans['t6']=getBit($data,5);
	$ans['t7']=getBit($data,6);
	$ans['meta']=getBit($data,7);
	return $ans;
}

function alertandjump(){
	echo '<meta charset="utf-8"><script type="text/javascript">alert("本区未开启，将强制传送回酒馆。");window.location.href="/game";</script>';
}
function aexit($alert,$url){
	echo '<meta charset="utf-8"><script type="text/javascript">alert("'.$alert.'");window.location.href="'.$url.'";</script>';
}

function getBit($n,$pos){
	return ($n>>$pos)&1;
}
function setBit($n,$pos){
	return $n|(1<<$pos);
}
class gameAction extends Action {
    protected function _initialize() {
        header("Content-Type:text/html; charset=utf-8");
    }
	public function aj3flja23jrf(){
		cookie('TestTempValueB83219','Namidoccbc7tk8895948',60540);
		echo "success";
	}
	//index方法，无参数
	//首页跳转
    public function index() {
        islogin();
		testuser();
        
		if(session('gid')==0){
			//该用户无组队
			$userQuery=M('user');
			$userData=$userQuery->where("uid=".session('uid'))->getField('data');
			if($userData[0]==1){
				header("Location: /game/home");
			}else{
				header("Location: /game/start");
			}
		}else{
			//该用户有组队
			$userQuery=M('user');
			$userData=$userQuery->where("uid=".session('uid'))->getField('data');
			if($userData=="1"){
				header("Location: /game/home");
			}else{
				header("Location: /game/start");
			}
		}
    }
	//start方法，无参数
	//初始界面
	public function start(){
		islogin();
		testuser();
		
		$userQuery=M('user');
		$upData['data']="1";
		$userQuery->where("uid=".session('uid'))->save($upData);
		if(session('gid')==0){
			//该用户无组队
			$userQuery=M('user');
			$userData=$userQuery->where("uid=".session('uid'))->getField('data');
			$upData['data']="1,0,0,0,0,0,0,0,0,0,0,0,0,{\"cdr\":1.0,\"cdt\":0,\"tipr\":1.0,\"zts\":0,\"errortime\":0}";
			$userQuery->where("uid=".session('uid'))->save($upData);
		}else{
			//该用户有组队
			$groupQuery=M('group');
			$gpData=$groupQuery->where("gid=".session('gid'))->getField('data');
			if($gpData[0]==""){
				$ugd['data']="1,0,0,0,0,0,0,0,0,0,0,0,0,{\"cdr\":1.0,\"cdt\":0,\"tipr\":1.0,\"zts\":0,\"errortime\":0}";
				$groupQuery->where("gid=".session('gid'))->save($ugd);
			}

			if(session('type')==6){
				$pmQuery=M('mail');
				$data['sgid']=8800;
				$data['sgname']="SYSTEM";
				$data['suid']=0;
				$data['rgid']=session('gid');
				$data['content']="<p>新手教程：欢迎来到席西碧大陆。这里是酒馆，这大陆上唯一的一个不受七国控制的地方。</p>
				<p>众多的人都聚集在这儿，开展他们的冒险。</p>
				<p>您要不要也来试一试？如果决定好去冒险，就和我左手边这位老板娘搭话吧。</p>
				<p>哦，对了，给你介绍一下酒馆里的人吧。我可以联系到那些悬赏者。</p>
				<p>壁炉里面烤了只兔子，虽然它自称为牛。它那里有这片大陆上来自官方的消息。</p>
				<p>前面那桌子上，那位穿灰色盔甲的人。他叫蜡烛，别看他那样，他总能给你带来一些八卦的新闻。</p>
				<p>我右手边坐在吧台前边的那位，他叫调贝勒，是我们这里的男仆。他很喜欢钱，但是有时候也会给你给一点。有空了可以去问他要。</p>
				<p>坐在吧台最里面的那位是小K，我们的收银员。他还有一些个人的趣味，卖一些奇怪的东西。</p>
				<p>什么？你问吧台上那只惟妙惟肖的雕像？不，那不是雕像，那是我们上次冒险的战利品。你也可以和它搭话。</p>
				<p>还有酒馆里的其他人。我建议在你出发之前和他们都说说话。有可能会有些什么帮助的。</p>
				";
				$data['eu']=0;
				$pmQuery->add($data);
			}

		}
		$this->display();
	}
	
	//home方法，无参数
	//酒馆
	public function home(){
		islogin();
		
		$saveData=selectData();
		//存档解码
		$saveArraystr=explode(",",$saveData,14);
		$invalid=($saveArraystr[0]==0?false:true);
		$normalMetaFin=($saveArraystr[1]==0?false:true);
		$newAreaFlag=($saveArraystr[2]==0?false:true);
		$openArea=intval($saveArraystr[3]);
		$queStatus['A']=intval($saveArraystr[4]);
		$queStatus['B']=intval($saveArraystr[5]);
		$queStatus['C']=intval($saveArraystr[6]);
		$queStatus['D']=intval($saveArraystr[7]);
		$queStatus['E']=intval($saveArraystr[8]);
		$queStatus['F']=intval($saveArraystr[9]);
		$queStatus['G']=intval($saveArraystr[10]);
		$finMetaFin=($saveArraystr[11]==0?false:true);
		$coinNum=intval($saveArraystr[12]);
		$itemStatus=json_decode($saveArraystr[13],true);
		
		//存档无效
		if(!$invalid){
			echo '<meta charset="utf-8"><script type="text/javascript">alert("当前状态无效。");window.location.href="/";</script>';
			exit();
		}
		
		
		//存档解码输出
		//echo $invalid?"有效":"无效";
		//echo "<br>".($normalMetaFin?"普通meta通过":"普通meta没有通过");
		//echo "<br>".($newAreaFlag?"新区能开放":"新区不能开放");
		//echo "<br>新区开放状态：".$openArea;
		//echo "<br>A-G区题目状态：".$queStatus['A']." ".$queStatus['B']." ".$queStatus['C']." ";
		//echo $queStatus['D']." ".$queStatus['E']." ".$queStatus['F']." ".$queStatus['G'];
		//echo "<br>".($finMetaFin?"完成final":"没有完成final");
		//echo "<br>当前金币数量：".$coinNum;
		//echo "<br>道具状态：【冷却比例：".$itemStatus['cdr']."，冷却次数：".$itemStatus['cdt']."，罚时系数：".$itemStatus['tipr']."，上次砸题时间：".$itemStatus['zts']."】，上次错误时间".$itemStatus['errortime'];
		//echo "<br>对上次错误时间进行判断：剩余".(time()-$itemStatus['errortime']);
		$as['nmfinish']=$saveArraystr[1];
		$as['fmfinish']=$saveArraystr[11];


		//读取系统公告：
		$annoQuery=M('anno');
		$as['announcement']=$annoQuery->order('id desc')->limit(1)->find();
		//读取进度播报数据；
		$as['jd_list']=M('jdbb')->order('id desc')->select();
		//检查是否有新消息：
		$mv['eu']=0;
		$mv['rgid']=session('gid');
		$mData=M('mail')->where($mv)->select();
		if(count($mData)!=0){
			$as['newmail']=1;
		}else{
			$as['newmail']=0;
		}
		//礼包已领？
		$lastLbDate=M('group')->where('gid='.session('gid'))->getField('errortime');
		$nowDate=localtime(time(),true);
		$nowDay=$nowDate['tm_year']*1000+$nowDate['tm_yday'];
		if(session('gid')!=0&&$nowDay>$lastLbDate){
			$as['lbstatus']=1;
		}else{
			$as['lbstatus']=0;
		}

		$this->assign($as);
		$this->display();
	}
	//map方法，无参数
	//大地图
	public function map(){
		islogin();
		testuser();
		
		//取存档
		$saveData=selectData();
		//存档解码
		$saveArraystr=explode(",",$saveData,14);
		$invalid=($saveArraystr[0]==0?false:true);
		$normalMetaFin=($saveArraystr[1]==0?false:true);
		$newAreaFlag=($saveArraystr[2]==0?false:true);
		$openArea=intval($saveArraystr[3]);
		$as['A']['meta']=intval($saveArraystr[4])>127?1:0;
		$as['B']['meta']=intval($saveArraystr[5])>127?1:0;
		$as['C']['meta']=intval($saveArraystr[6])>127?1:0;
		$as['D']['meta']=intval($saveArraystr[7])>127?1:0;
		$as['E']['meta']=intval($saveArraystr[8])>127?1:0;
		$as['F']['meta']=intval($saveArraystr[9])>127?1:0;
		$as['G']['meta']=intval($saveArraystr[10])>127?1:0;
		$finMetaFin=($saveArraystr[11]==0?false:true);		
		//存档无效
		if(!$invalid){
			echo '<meta charset="utf-8"><script type="text/javascript">alert("当前状态无效。");window.location.href="/";</script>';
			exit();
		}
		
		//是否允许开新区？
		$as['showOp']=0;
		if($newAreaFlag&&$normalMetaFin){
			if(session('gid')==0){
				$as['showOp']=1;
			}else{
				if(session('type')==6)
					$as['showOp']=1;
			}
		}
		//普通Meta未完成
		if(!$normalMetaFin){
			$as['A']['open']=1;
			$as['B']['open']=1;
			$as['C']['open']=1;
			$as['D']['open']=1;
			$as['E']['open']=1;
			$as['F']['open']=1;
			$as['G']['open']=1;
			$as['norMeta']['open']=1;
			$as['finMeta']['open']=0;
			$as['norMeta']['meta']=0;
			$as['finMeta']['meta']=0;
		}else{
			//普通Meta完成
			$as['norMeta']['meta']=1;
			$as['norMeta']['open']=0;
			$as['finMeta']['meta']=$finMetaFin?1:0;
			$as['A']['open']=getBit($openArea,0);
			$as['B']['open']=getBit($openArea,1);
			$as['C']['open']=getBit($openArea,2);
			$as['D']['open']=getBit($openArea,3);
			$as['E']['open']=getBit($openArea,4);
			$as['F']['open']=getBit($openArea,5);
			$as['G']['open']=getBit($openArea,6);
			$finKey=0;
			for($i=0;$i<7;$i++){
				$finKey+=$as[chr(ord('A')+$i)]['meta'];
			}
			if($finKey>=4){
				$as['finMeta']['open']=1;
			}
		}
		

		$this->assign($as);
		$this->display();
	}
	//section方法
	//参数1：:URL参数。接受的值：A、B、C、D、E、F、G、N、M
	public function section(){
		islogin();
		testuser();
		
		//取存档
		$saveData=selectData();
		//开放区域存档
		//存档解码
		$saveArraystr=explode(",",$saveData,14);
		$invalid=($saveArraystr[0]==0?false:true);
		$normalMetaFin=($saveArraystr[1]==0?false:true);
		$openArea=intval($saveArraystr[3]);	
		//存档无效
		if(!$invalid){
			echo '<meta charset="utf-8"><script type="text/javascript">alert("当前状态无效。");window.location.href="/";</script>';
			exit();
		}
		$as['normalmeta']=$normalMetaFin?1:0;
		
		$sect = $this->_param(2);
		if($sect=='A'){
			if($normalMetaFin&&(getBit($openArea,0)==0)){
				alertandjump();
				exit();
			}
			$as['finish']=getFinish($saveArraystr[4]);
			$as['nowArea']="A";
			$this->assign($as);
			$this->display('Section:A');
		}else if($sect=='B'){
			if($normalMetaFin&&(getBit($openArea,1)==0)){
				alertandjump();
				exit();
			}
			$as['finish']=getFinish($saveArraystr[5]);
			$as['nowArea']="B";
			$this->assign($as);
			$this->display('Section:B');
		}else if($sect=='C'){
			if($normalMetaFin&&(getBit($openArea,2)==0)){
				alertandjump();
				exit();
			}
			$as['finish']=getFinish($saveArraystr[6]);
			$as['nowArea']="C";
			$this->assign($as);
			$this->display('Section:C');
		}else if($sect=='D'){
			if($normalMetaFin&&(getBit($openArea,3)==0)){
				alertandjump();
				exit();
			}
			$as['finish']=getFinish($saveArraystr[7]);
			$as['nowArea']="D";
			$this->assign($as);
			$this->display('Section:D');
		}else if($sect=='E'){
			if($normalMetaFin&&(getBit($openArea,4)==0)){
				alertandjump();
				exit();
			}
			$as['finish']=getFinish($saveArraystr[8]);
			$as['nowArea']="E";
			$this->assign($as);
			$this->display('Section:E');
		}else if($sect=='F'){
			if($normalMetaFin&&(getBit($openArea,5)==0)){
				alertandjump();
				exit();
			}
			$as['finish']=getFinish($saveArraystr[9]);
			$as['nowArea']="F";
			$this->assign($as);
			$this->display('Section:F');
		}else if($sect=='G'){
			if($normalMetaFin&&(getBit($openArea,6)==0)){
				alertandjump();
				exit();
			}
			$as['finish']=getFinish($saveArraystr[10]);
			$as['nowArea']="G";
			$this->assign($as);
			$this->display('Section:G');
		}else if($sect=='N'){
			if($normalMetaFin){
				echo '<meta charset="utf-8"><script type="text/javascript">alert("你已经没必要再来这里了。");
              window.location.href="/game";</script>';
			 	exit();
			}
			else{
				echo '<meta charset="utf-8"><script type="text/javascript">
              window.location.href="/game/city/N/M";</script>';
			 	exit();
			}
		}else if($sect=='M'){
			$finKey=0;
			for($i=0;$i<7;$i++){
				if($saveArraystr[4+$i]>127){
					$finKey++;
				}
			}
			if($finKey<4){
				echo '<meta charset="utf-8"><script type="text/javascript">alert("你还不能来这里。");
              window.location.href="/game";</script>';
			 	exit();
			}
			else{
				echo '<meta charset="utf-8"><script type="text/javascript">
              window.location.href="/game/city/M/M";</script>';
			 	exit();
			}
		}else{
			echo '<meta charset="utf-8"><script type="text/javascript">alert("参数错误。");
              window.location.href="/game";</script>';
			 exit();
		}
	}
	
	//City方法
	//显示题目
	public function city(){
		islogin();
		testuser();
		
		//取存档
		$saveData=selectData();
		//存档解码
		$saveArraystr=explode(",",$saveData,14);
		$invalid=($saveArraystr[0]==0?false:true);
		$normalMetaFin=($saveArraystr[1]==0?false:true);
		$openArea=intval($saveArraystr[3]);	
		//存档无效
		if(!$invalid){
			echo '<meta charset="utf-8"><script type="text/javascript">alert("当前状态无效。");window.location.href="/";</script>';
			exit();
		}
		
		$section=$this->_param(2);
		$probid=$this->_param(3);
		
		//判断参数有效性
		if(ord($section)<ord('A')||ord($section)>ord('G')){
			if($section!='N'&&$section!='M'){
				aexit("参数错误","/game");
				exit();
			}
		}
		if($probid!="M"){
			if($probid<1||$probid>7){
				aexit("参数错误","/game");
				exit();
			}
		}
		if(session('type')==6)
			if(ord($section)>=ord('A')&&ord($section)<=ord('G'))
				if($probid!="M")
					$as['zts_ready']=1;

		$itemStatus=json_decode($saveArraystr[13],true);
		$reCdtime=time()-$itemStatus['zts'];
		if($reCdtime<57600)
			$as['zts_ready']=0;
		if($itemStatus['zts']==0)
			$as['zts_ready']=0;
		
		//判断题目可以访问么？
		$readok=false;
		
		if($section=="N"){
			$readok=true;//NormalMeta可以访问
		}
		if(!$normalMetaFin){
			if($probid==1||$probid==2)
				$readok=true;
		}else{
			$section_id=ord($section)-ord("A");
			if($section_id>=0&&$section_id<7){
				if(getBit($openArea,$section_id)==1){
					if($probid!='M')
						$readok=true;
				}
				if($probid=="M"){
					$areaTimuStatus=intval($saveArraystr[4+$section_id]);
					$ert=0;
					for($i=0;$i<7;$i++){
						if(getBit($areaTimuStatus,$i)==1){
							$ert++;
						}
					}
					if($ert>=5){
						$readok=true;
					}
				}
			}//end if
			
			
			
			if($section=="M"){
				$finKey=0;
				for($i=0;$i<7;$i++){
					if(intval($saveArraystr[4+$i])>127){
						$finKey++;
					}
				}
				if($finKey>=4){
					$readok=true;
				}
			}//end if
		}//end if
		
		//不可访问的情况弹回酒馆。
		if(!$readok){
			echo '<meta charset="utf-8"><script type="text/javascript">alert("你暂时还不能来这里。\n\n请至少完成本区域的5题哦。");history.back();</script>';
			exit();
		}
		
		//读取题目数据库
		$timuQuery=M('timu');
		$tv['tmid']=$section.$probid;
		$timuData=$timuQuery->where($tv)->getField('content');
		
		//读取上一条记录
		$statuQuery=M('status');
		$sv=array();
		if(session('gid')==0){
			$sv['uid']=session('uid');
		}else{
			$sv['gid']=session('gid');
		}
		$sv['tmid']=$tv['tmid'];
		$statuData=$statuQuery->where($sv)->order('id desc')->limit(1)->select();
		$statuStr=$statuData[0]['stime']." @".$statuData[0]['uname']." ".$statuData[0]['answer']." ".$statuData[0]['res'];
		$as['lastst']=$statuStr;
		$as['tmid']=$tv['tmid'];
		$as['content']=$timuData;
		$this->assign($as);
		$this->display();
	}
	
	
	public function checkanswer(){
		//检查答案正确性
		//POST 方法传递参数
		//参数：tmid - 题号； answer - 答案
		
		$tmid=$this->_post('tmid');
		$answer=$this->_post('answer');
		
		//其余需要的参数从session中取得。
		$sv['gid']=session('gid');
		$sv['uid']=session('uid');
		$sv['uname']=session('name');
		$sv['tmid']=$tmid;
		$sv['answer']=$answer;
		

		$answer=trim(strtolower($answer));
		//获取组队名和存档
		if($sv['gid']==0){
			//个人用户
			$tQuery=M('user');
			$saveData=$tQuery->where("uid=".session('uid'))->getField('data');

			# debug 
			# echo "个人存档";
		}else{
			//组队用户
			$mQuery=M('group');
			$mData=$mQuery->where("gid=".session('gid'))->find();
			$saveData=$mData['data'];
			$sv['gname']=$mData['gname'];

			# debug 
			# echo "组队存档";
		}
		
		//更新个人数据
		$tQuery=M('user');
		$ntData=$tQuery->where("uid=".session('uid'))->getField('type');
		session('type',$ntData);
		
		//存档解析
		$saveArraystr=explode(",",$saveData,14);
		$invalid=($saveArraystr[0]==0?false:true);

		# echo $saveData;
		# print_r($saveArraystr);
		# echo $invalid?"True":"False";
		
		//Errortime 冷却时间计算
		$itemStatus=json_decode($saveArraystr[13],true);
		$cdTime=300*floatval($itemStatus['cdr']);
		$errortime=intval($itemStatus['errortime']);
		$recdtime=(time()-$errortime);
		//Debug echo "Errortime:".$errortime."<br>";
		
		//开始判定
		
		if(session('login')!="1"){//Ert 0 登录错误
			$sv['res']="Ert 0";
			echo "Ert 0 未登录";
		}else if(session('type')==8){//Ert 1 用户权限错误
			$sv['res']="Ert 1";
			echo "Ert 1 您好。您已经被封禁，请联系管理员处理。";
			session('login',0);
		}else if($tmid==""){//Ert 2 参数未指定
			$sv['res']="Ert 2";
			echo "Ert 2 参数未指定";
		}else if($answer==""){//Ert 3 答案为空
			$sv['res']="Ert 3";
			echo "Ert 3 答案为空";
		}else if(!$invalid){//Ert 4 存档无效
			$sv['res']="Ert 4";
			echo "Ert 4 存档无效";
		}else if($recdtime<$cdTime){//Cooling 冷却中		
			$sv['res']="Cooling";
			echo "Cooling 冷却中 还有 ".($cdTime-$recdtime)." 秒<br>请不要反复刷新。反复刷新可能会被封禁。";
		}else{//正确性判定开始
			$timuQuery=M('timu');
			$tv['tmid']=$tmid;
			$tmData=$timuQuery->where($tv)->find();
			if(strtolower($tmData['answer'])==$answer){//判定正确
			
				//设置存档Data
				if($tmid=="NM"){//Normal Meta
					if($saveArraystr[1]==0) $saveArraystr[12] += 2;//Coin
					
					$saveArraystr[1]=1;
					
					if(session('gid')!=0){
						$saveArraystr[2]=1;//开放新区
							$data['sgid']=8800;
							$data['sgname']="SYSTEM";
							$data['suid']=0;
							$data['rgid']=session('gid');
							$data['content']="<p>天呐！你都做了什么？你把祭坛给摧毁了！连带毁坏了封印恶魔的法阵！</p>
												<p>恶魔已经苏醒了！</p>
												<p>勇士，没有时间了！</p>
												<p>赶紧前往七个大陆收集新的封印材料，趁恶魔还没有跨洋踏足我们的世界！</p>";
							$data['eu']=0;
							M('mail')->add($data);
					}
					
					
				}else if($tmid=="MM"){//Final Meta
					$saveArraystr[11]=1;
					$saveArraystr[12] += 100;//Coin
				}else{
					$areaId=ord($tmid[0])-ord("A");
					$probId=$tmid[1];
					if($probId=="M"){
						$probId=7;
						$saveArraystr[2]=1;//开放新区
						if(getBit($saveArraystr[4+$areaId],$probId)==0) $saveArraystr[12] += 3;//Coin
					}
					else{
						$probId=intval($probId)-1;
						if(getBit($saveArraystr[4+$areaId],$probId)==0) if($probId<=2){
							$saveArraystr[12] += 1;//Coin
						}else{
							$saveArraystr[12] += 2;//Coin
						}
					}
					
					$oriData=$saveArraystr[4+$areaId];
					$saveArraystr[4+$areaId]=setBit($oriData,$probId);
				}
				$sv['res']="OK";
				echo "OK 答案正确";
			}else if($tmData['md5ans']==md5($answer)){//Ert 5 MD5已碰撞
				$sv['res']="Ert 5";
				echo "Ert 5 MD5已碰撞";
			}else{//判定错误
				$itemStatus['errortime']=time();
				//减CD道具扣减。
				$itemStatus['cdt']--;
				$sv['res']="Wrong";
				echo "Wrong 答案错误";
			}
			
			
			if($itemStatus['cdt']<=0){
				$itemStatus['cdr']=1.0;
				$itemStatus['cdt']=0;
			}
		}

		//存档生成
		$saveArraystr[13]=json_encode($itemStatus);
		$savedataNew=implode(",",$saveArraystr);
		
		//存档写回
		if(session('gid')==0){//个人用户
			$tQuery=M('user');
			$tv['uid']=session('uid');
			$tv['data']=$savedataNew;
			$tst=$tQuery->save($tv);
			//if($tst!=1) echo"数据库错误。";
		}else{
			$tQuery=M('group');
			$tv['gid']=session('gid');
			$tv['data']=$savedataNew;
			$tst=$tQuery->save($tv);
			//if($tst!=1) echo"数据库错误。";
		}
		
		//记录写入
		$statuQuery=M('status');
		$statuQuery->add($sv);
	}
	
	//每日礼包
	public function mrlb(){
		islogin();
		testuser();
		//检查是否是队长
		//if(session('type')!=6){
		//	echo "必须要队长才可以哦。";
		//	exit();
		//}
		//取存档
		$saveData=M('group')->where('gid='.session('gid'))->getField('data');
		$lastDate=M('group')->where('gid='.session('gid'))->getField('errortime');
		//存档解码
		$saveArraystr=explode(",",$saveData,14);
		$invalid=($saveArraystr[0]==0?false:true);
		//存档无效
		if(!$invalid){
			echo "当前状态无效";
			exit();
		}
		$nowDate=localtime(time(),true);
		$nowDay=$nowDate['tm_year']*1000+$nowDate['tm_yday'];
		if($nowDay>$lastDate){
			$saveArraystr[12]+=6;//Coin
		}else{
			echo "本日已领。";
			exit();
		}
		$dv['gid']=session('gid');
		$dv['errortime']=$nowDay;
		$dv['data']=implode(",",$saveArraystr);
		M('group')->save($dv);
		echo "领取完成。金币增加 6G";
	}
	//开放新区
	public function openarea($area){
		islogin();
		testuser();
		
		//检查是否是队长
		if(session('gid')!=0&&session('type')!=6){
			echo "你没有权限执行这个操作";
			exit();
		}
		
		//取存档
		$saveData=selectData();
		//存档解码
		$saveArraystr=explode(",",$saveData,14);
		$invalid=($saveArraystr[0]==0?false:true);
		$normalMetaFin=($saveArraystr[1]==0?false:true);
		$openFlag=($saveArraystr[2]==0?false:true);
		$openArea=intval($saveArraystr[3]);	
		//存档无效
		if(!$invalid){
			echo "当前状态无效";
			exit();
		}
		if(!$normalMetaFin){
			echo "当前不能开新区。";
			exit();
		}
		if(!$openFlag){
			echo "当前不能开新区。";
			exit();
		}
		
		if($area==""){
			echo "参数错误";
			exit();
		}
		$area=intval($area);
		if($area<1||$area>7){
			echo "参数错误";
			exit();
		}
		if(getBit($openArea,$area-1)==1){
			echo "区域已开放。";
			exit();
		}
		
		//开放这一地区
		$saveArraystr[2]=0;
		$saveArraystr[3]=setBit($openArea,$area-1);
		
		//存档写回
		$savedataNew=implode(",",$saveArraystr);
		
		if(session('gid')==0){//个人用户
			$tQuery=M('user');
			$tv['uid']=session('uid');
			$tv['data']=$savedataNew;
			$tst=$tQuery->save($tv);
			if($tst!=1) echo"数据库错误。";
			else echo "开放成功";
		}else{
			$tQuery=M('group');
			$tv['gid']=session('gid');
			$tv['data']=$savedataNew;
			$tst=$tQuery->save($tv);
			if($tst!=1) echo"数据库错误。";
			else echo "开放成功";
		}
		
		//记录
		$sv['gid']=session('gid');
		$sv['uid']=session('uid');
		$sv['uname']=session('name');
		$sv['gname']=M('group')->where("gid=".session('gid'))->getField('gname');
		$sv['answer']="开放了区域 ".chr($area-1+ord("A"));
		
		M('status')->add($sv);	
	}

	//历史公告
	public function announce(){
		islogin();
		$annoQuery=M('anno');
		$annoData=$annoQuery->order('id desc')->select();
		$as['annolist']=$annoData;
		$this->assign($as);
		$this->display();
	}

	public function getpost($id){
		islogin();
		$jdQuery=M('jdbb');
		$data=$jdQuery->where('id='.$id)->find();
		echo json_encode($data);
	}
	public function spm(){
		islogin();
		$pmQuery=M('mail');
		$pmData=$pmQuery->where('sgid='.session('gid'))->order('mid desc')->select();
		$as['spmlist']=$pmData;
		$this->assign($as);
		$this->display();
	}
	public function sendmail($content){
		islogin();
		$pmQuery=M('mail');
		$data['sgid']=session('gid');
		$data['sgname']=M('group')->where('gid='.session('gid'))->getField('gname');
		$data['suid']=session('uid');
		$data['rgid']=8888;
		$data['content']=$content;

		$saveData=selectData();
    	$saveArraystr=explode(",",$saveData,14);
    	$itemStatus=json_decode($saveArraystr[13],true);
    	if($itemStatus['tipr']!=1)
    		$data['eu']=2;
    	else
			$data['eu']=0;

		$res=$pmQuery->add($data);
		if($res){
			echo "发送成功。";
		}else{
			echo "发送失败。";
		}
	}
	public function rpm(){
		islogin();
		$pmQuery=M('mail');
		$pmData=$pmQuery->where('rgid='.session('gid'))->order('mid desc')->select();
		$as['spmlist']=$pmData;
		$this->assign($as);

		//print_r($as);
		$this->display();
	}
	public function pmyd($mid){
		islogin();
		$pmQuery=M('mail');
		$pmData=$pmQuery->where('mid='.$mid)->find();
		if($pmData['rgid']==session('gid')){
			$eak['mid']=$mid;
			$eak['eu']=1;
			$res=$pmQuery->save($eak);
			if($res){
				echo "success";
			}else{
				echo "保存失败。";
			}
		}else{
			echo "用户认证错误。";
		}
	}
	public function ned(){
		islogin();
		//if(session('gid')!=0) aexit("这不是你应该来的地方。","/game");
		//取存档
		$saveData=selectData();
		//存档解码
		$saveArraystr=explode(",",$saveData,14);
		if($saveArraystr[1]==1){
			$this->display();
		}else{
			aexit("你不能来这里。","/game");
		}
	}
	public function ed(){
		islogin();
		//取存档
		$saveData=selectData();
		//存档解码
		$saveArraystr=explode(",",$saveData,14);
		if($saveArraystr[11]==1||session('type')==2){
			$this->display();
		}else{
			aexit("你不能来这里。","/game");
		}
	}
	public function newmsg(){
		//检查是否有新消息：
		$mv['eu']=0;
		$mv['rgid']=session('gid');
		$mData=M('mail')->where($mv)->select();
		if(count($mData)!=0){
			echo 1;
		}else{
			echo 0;
		}
	}
}

?>