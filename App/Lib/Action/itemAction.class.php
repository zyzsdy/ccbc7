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
function getBit($n,$pos){
	return ($n>>$pos)&1;
}
function setBit($n,$pos){
	return $n|(1<<$pos);
}
class itemAction extends Action{
    public function shop(){
    	islogin();
    	$saveData=selectData();
    	$saveArraystr=explode(",",$saveData,14);
    	$as['coinnum']=intval($saveArraystr[12]);

    	$itemStatus=json_decode($saveArraystr[13],true);
    	if($itemStatus['cdt']!=0)
    		$as['item_cd_available']=0;
    	else
    		$as['item_cd_available']=1;

    	if($saveArraystr[1]==0){
    		$as['newArea']=1;
    	}else{
    		$as['newArea']=$saveArraystr[2];
    	}
    	if($itemStatus['zts']!=0)
    		$as['item_zts_available']=0;
    	else
    		$as['item_zts_available']=1;
    	if($itemStatus['tipr']!=1)
    		$as['item_tip_available']=0;
    	else
    		$as['item_tip_available']=1;
    	$this->assign($as);
    	$this->display();
    }
    public function buy($itemnum){
    	islogin();
    	if(session('type')!=6){
    		exit("只有队长才能购买道具哦。请联系你的队长吧。");
    	}
    	$saveData=selectData();
    	$saveArraystr=explode(",",$saveData,14);
    	$coinnum=intval($saveArraystr[12]);
    	$itemStatus=json_decode($saveArraystr[13],true);
    	$coin=array(2,3,8,12,25,25,25);
    	$itemname=array("无用的石头","时之尘","时之沙","传送卷轴","贤者之石","牺牲契约","时光沙漏");
    	if($coinnum<$coin[$itemnum]){
    		exit("金币不足");
    	}else{
    		$saveArraystr[12] -= $coin[$itemnum];
    	}
    	if($itemnum==0){
    		$rdnum=rand(0,99);
    		echo "生成100以内的随机整数。若数字比34小。则获得1G奖励。\n\n你抽到的数字为：".$rdnum;
    		if($rdnum<34) $saveArraystr[12] += 1;
    	}else if($itemnum==1){
    		$itemStatus['cdr']=0.4;
    		$itemStatus['cdt']=5;
    		echo "购买了 时之尘 。\n\n接下来的 5 次错误回答。冷却时间将降低60%";
    	}else if($itemnum==2){
    		$itemStatus['cdr']=0.2;
    		$itemStatus['cdt']=10;
    		echo "购买了 时之沙 。\n\n接下来的 10 次错误回答。冷却时间将降低80%";
    	}else if($itemnum==3){
    		$saveArraystr[2]=1;
    		echo "购买了 传送卷轴 。\n\n你现在可以开新区了。";
    	}else if($itemnum==4){
    		$itemStatus['zts']=9000;
    		echo "购买了 贤者之石 。\n\n你需要在题目页下方答题栏上的按钮使用。";
    	}else if($itemnum==5){
    		$itemStatus['tipr']=0.8;
    		echo "购买了 牺牲契约 。\n\n以后你购买的提示将记为9小时。";
    	}else if($itemnum==6){
    		$itemStatus['cdr']=0.8;
    		$itemStatus['cdt']=10000000;
    		echo "购买了 时光沙漏 。\n\n你的错误回答冷却时间已经永久的降低20%";
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
		//记录
		$sv['gid']=session('gid');
		$sv['uid']=session('uid');
		$sv['gname']=M('group')->where('gid='.session('gid'))->getField('gname');
		$sv['uname']=session('name');
		$sv['answer']="购买了道具".$itemnum." ".$itemname[$itemnum].$rdnum;
		M('status')->add($sv);
    }
    public function zts($tmid){
    	islogin();
    	if(session('type')!=6){
    		exit("只有队长才能使用贤者之石。");
    	}
    	$saveData=selectData();
    	$saveArraystr=explode(",",$saveData,14);
    	$itemStatus=json_decode($saveArraystr[13],true);
    	if($itemStatus['zts']==0){
    		exit("你还没有贤者之石。");
    	}
    	$reCdtime=time()-$itemStatus['zts'];
    	if($reCdtime<57600){
    		exit("贤者之石冷却中。剩余".(57600-$reCdtime)."秒。");
    	}
    	$areaId=$tmid[0];
    	$probId=$tmid[1];
    	if($areaId=="N"||$areaId=="M"||$probId=="M"){
    		exit("贤者之石对Meta是不起作用的。");
    	}

    	$areaId=ord($tmid[0])-ord("A");
		$probId=intval($tmid[1])-1;
		$oriData=$saveArraystr[4+$areaId];
		$saveArraystr[4+$areaId]=setBit($oriData,$probId);

		$tmv['tmid']=$tmid;
		$answer=M('timu')->where($tmv)->getField('answer');
		$itemStatus['zts']=time();

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
		//记录
		$sv['gid']=session('gid');
		$sv['uid']=0;
		$sv['gname']=M('group')->where('gid='.session('gid'))->getField('gname');
		$sv['uname']="贤者之石";
		$sv['tmid']=$tmid;
		$sv['answer']=$answer;
		$sv['res']="OK";
		M('status')->add($sv);

		echo "贤者之石已经帮你解决了这个问题了。本题答案是 ".$answer." 。接下来的16个小时你都不能再使用贤者之石了。";
    }
}