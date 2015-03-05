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
function getRandomName(){
    $dict=array("漓","殇","梦","阑","雪","蝶","凪","爱","樱","莹","羽","魑","蔷","海","雨","悠","紫","岚","晗","薰","柊","滢","萤","雅","夏","绪","菀","莎","姗","诗","冰","兰","云","灵","柔","依","筱","娜","青","月","若","雁","茹","清","瑛","夕","姬","芳","凉","轻");
    $fname="";
    $time=mt_rand(3,7);
    for($i=0;$i<$time;$i++){
        $map=mt_rand(3,7);
        for($j=0;$j<$map;$j++){
            $fname=$fname.$dict[mt_rand(0,49)];
        }
        if($i!=$time-1){
            $fname=$fname."·";
        }
    }
    return $fname;
}
class userAction extends Action {
    public function index(){
        islogin();
        $userQuery=M('user');
        $iv['uid']=session('uid');
        $userdata=$userQuery->where($iv)->select();
        $nm=$userdata[0];
        session('data',$nm['data']);
        
        //组队信息
        if($nm['gid']!=0&&$nm['type']!=0){
            $groupQuery=M('group');
            $qgv['gid']=$nm['gid'];
            $groupData=$groupQuery->where($qgv)->select();
            $groupData=$groupData[0];
            
            $nm['gname']=$groupData['gname'];
            $nm['ginfo']=$groupData['info'];
            $nm['gnum']=$groupData['gnum'];
            session('data',$groupData['data']);
            $nm['u0']=$groupData['u0'];
            $nm['u1']=$groupData['u1'];
            $nm['u2']=$groupData['u2'];
            $nm['u3']=$groupData['u3'];
            $nm['u4']=$groupData['u4'];
            
            if($nm['u0']!=0) {
                $te=$userQuery->where('uid='.$nm['u0'])->getField('name, type');
                foreach($te as $name => $type){
                    $nm['gn0']['name']=$name;
                    $nm['gn0']['type']=$type;
                }
            }
            if($nm['u1']!=0) {
                $te=$userQuery->where('uid='.$nm['u1'])->getField('name, type');
                foreach($te as $name => $type){
                    $nm['gn1']['name']=$name;
                    $nm['gn1']['type']=$type;
                }
            }
            if($nm['u2']!=0) {
                $te=$userQuery->where('uid='.$nm['u2'])->getField('name, type');
                foreach($te as $name => $type){
                    $nm['gn2']['name']=$name;
                    $nm['gn2']['type']=$type;
                }
            }
            if($nm['u3']!=0) {
                $te=$userQuery->where('uid='.$nm['u3'])->getField('name, type');
                foreach($te as $name => $type){
                    $nm['gn3']['name']=$name;
                    $nm['gn3']['type']=$type;
                }
            }
            if($nm['u4']!=0) {
                $te=$userQuery->where('uid='.$nm['u4'])->getField('name, type');  
                foreach($te as $name => $type){
                    $nm['gn4']['name']=$name;
                    $nm['gn4']['type']=$type;
                }
            }
        }
        
        //邀请信息
        if($nm['type']==0){
            $inviteQuery=M('invite');
            
            $vv['duid']=session('uid');
            $vv['eu']=0;
            $inResult=$inviteQuery->where($vv)->select();
            $i=0;
            $inviteInfo=array();
            foreach($inResult as $vo){
                $inviteInfo[$i]['iid']=$vo['iid'];
                
                $groupQuery=M('group');
                $gpInfo=$groupQuery->where('gid='.$vo['sgid'])->select();
                $gpInfo=$gpInfo[0];
                $inviteInfo[$i]['sgname']=$gpInfo['gname'];
                $inviteInfo[$i]['sginfo']=$gpInfo['info'];
                $inviteInfo[$i]['sgnum']=$gpInfo['gnum'];
                $inviteInfo[$i]['sendtime']=$vo['sendtime'];
                $userQuery=M('user');
                $inviteInfo[$i]['un0']=$userQuery->where('uid='.$gpInfo['u0'])->getField('name');
                $i++;
            }
            $nm['inviteInfo']=$inviteInfo;
        }
        session('name',$nm['name']);
        session('email',$nm['email']);
        session('type',$nm['type']);
        session('gid',$nm['gid']);
        $this->assign($nm);
        $this->display();
    }
    public function view($uid){
        $userQuery=M('user');
        $userData=$userQuery->where('uid='.$uid)->select();
        if(count($userData)==0){
            echo "<meta charset=\"utf-8\">";
            echo "不存在该用户。";
            exit();
        }
        
        $userData=$userData[0];
        
        $nm['user']=$userData;
        $gid=$userData['gid'];
        $groupQuery=M('group');
        $groupData=$groupQuery->where('gid='.$gid)->select();
        $groupData=$groupData[0];
        
        $nm['group']=$groupData;
        $nm['u0']=$groupData['u0'];
        $nm['u1']=$groupData['u1'];
        $nm['u2']=$groupData['u2'];
        $nm['u3']=$groupData['u3'];
        $nm['u4']=$groupData['u4'];
        
        if($nm['u0']!=0) {
            $te=$userQuery->where('uid='.$nm['u0'])->getField('name, type');
            foreach($te as $name => $type){
                $nm['gn0']['name']=$name;
                $nm['gn0']['type']=$type;
            }
        }
        if($nm['u1']!=0) {
            $te=$userQuery->where('uid='.$nm['u1'])->getField('name, type');
            foreach($te as $name => $type){
                $nm['gn1']['name']=$name;
                $nm['gn1']['type']=$type;
            }
        }
        if($nm['u2']!=0) {
            $te=$userQuery->where('uid='.$nm['u2'])->getField('name, type');
            foreach($te as $name => $type){
                $nm['gn2']['name']=$name;
                $nm['gn2']['type']=$type;
            }
        }
        if($nm['u3']!=0) {
            $te=$userQuery->where('uid='.$nm['u3'])->getField('name, type');
            foreach($te as $name => $type){
                $nm['gn3']['name']=$name;
                $nm['gn3']['type']=$type;
            }
        }
        if($nm['u4']!=0) {
            $te=$userQuery->where('uid='.$nm['u4'])->getField('name, type');  
            foreach($te as $name => $type){
                $nm['gn4']['name']=$name;
                $nm['gn4']['type']=$type;
            }
        }
        $this->assign($nm);
        $this->display();
        
    }
    public function nglist(){
        $userQuery=M('user');
        $nm['ulist']=$userQuery->where('type=0')->select();
        $this->assign($nm);
        $this->display();
    }
    public function updateinfo(){
        islogin();
        $userQuery=M('user');
        $iv['uid']=session('uid');
        $userdata=$userQuery->where($iv)->select();
        
        $nm=$userdata[0];
        session('name',$nm['name']);
        session('email',$nm['email']);
        session('type',$nm['type']);
        session('data',$nm['data']);
        session('gid',$nm['gid']);
        $this->assign($nm);
        $this->display();
    }
    public function upname(){
        islogin();
        //读取post
        $iv['name']=$this->_post('name');
        $userQuery=M('user');
        
        $userData=$userQuery->where($iv)->select();
        $num=count($userData);
        
        if($num!=0){
            $randomName=getRandomName();
            echo "这个名字已经被其他人用过了。<br>摆在你面前的，现在有两条路：一是和那个人去交涉，让他换个新名字；二是你自己再想个新名字吧。<br>不然，我帮你想一个？你就叫<code>".$randomName."</code>怎么样？";
            exit();
        }else{
            $find['uid']=session('uid');
            $updStat=$userQuery->where($find)->save($iv);
            if($updStat==1){
                echo "success";
            }else{
                echo "好像出了点什么问题，无法修改名字。";
            }
        }
    }
    public function uppass(){
        islogin();
        //读取post
        $iv['pass']=passencode($this->_post('pass'));
        $iv['uid']=session('uid');
        $new['pass']=passencode($this->_post('newpass'));
        $userQuery=M('user');
        
        $updStat=$userQuery->where($iv)->save($new);
        if($updStat==1){
            echo "success";
        }else{
            echo "好像出了点什么问题，无法修改密码。<br>你应该优先检查下你的原密码是不是输错了。";
        }
    }
    public function upinfo(){
        islogin();
        //读取post
        $new['info']=$this->_post('info');
        $iv['uid']=session('uid');
        
        $userQuery=M('user');
        $updStat=$userQuery->where($iv)->save($new);
        if($updStat==1){
            echo "success";
        }else{
            echo "Error: 无法修改。";
        }
    }
    public function upgname(){
        islogin();
        if(session('type')!=6){
            echo "你并不是队长哦。";
            exit();
        }
        $it['gname']=$this->_post('newgname');
        $iv['gid']=session('gid');
        
        $groupQuery=M('group');
        $gC=$groupQuery->where($it)->select();
        $countGName=count($gC);
        if($countGName!=0){
            echo "这个名字已经有了。是不是可以打算换一个呢？";
            exit();
        }
        $updStat=$groupQuery->where($iv)->save($it);
        if($updStat==1){
            echo "success";
        }else{
            echo "好像出了点什么问题。。。无法保存新名字。";
        }
    }
    public function upginfo(){
        islogin();
        if(session('type')!=6){
            echo "你并不是队长哦。";
            exit();
        }
        $it['info']=$this->_post('newginfo');
        $iv['gid']=session('gid');
        $groupQuery=M('group');
        $updStat=$groupQuery->where($iv)->save($it);
        if($updStat==1){
            echo "success";
        }else{
            echo "好像出了点什么问题。。。无法保存新的简介。";
        }
    }
    public function invite(){
        islogin();
        if(session('type')!=6){
            echo "你并不是队长哦。";
            exit();
        }
        $nv['name']=$this->_post('dname');
        $userQuery=M('user');
        $stu=$userQuery->where($nv)->select();
        if(count($stu)!=1){
            echo "好像我们找不到这个人嘛。。。";
            exit();
        }else{
            if($stu[0]['type']!=0||$stu[0]['gid']!=0){
                echo "这个人好像已经有组队了。";
                exit();
            }
        }
        $duid=$stu[0]['uid'];
        $groupQuery=M('group');
        $gv['gid']=session('gid');
        $groupdata=$groupQuery->where($gv)->select();
        $gdata=$groupdata[0];
        if($duid==$gdata['u0']||$duid==$gdata['u1']||$duid==$gdata['u2']||$duid==$gdata['u3']||$duid==$gdata['u4']){
            echo "这个人已经邀请过了。";
            exit();
        }
        $pos=$this->_post('pos');
        $iniv['pos']=$pos;
        $iniv['sgid']=session('gid');
        $iniv['duid']=$duid;
        
        $inviteQuery=M('invite');
        $newiid=$inviteQuery->add($iniv);
        if(!$newiid){
            echo "邀请发送失败。";
            exit();
        }else{
            $gt['u'.$pos]=$duid;
			$groupvector['gid']=session('gid');
            $updStat=$groupQuery->where($groupvector)->save($gt);
            if($updStat==1){
                echo "success";
            }else{
                echo "虽然邀请发送成功了。但是组队信息更改失败。请联系管理员。";
            } 
        }
    }
    public function definvite($iid){
        islogin();
        if(session('type')!=0){
            echo "你已经是有身份的人了。";
            exit();
        }
        $inviteQuery=M('invite');
        $ifv['iid']=$iid;
        $ivData=$inviteQuery->where($ifv)->select();
        $ivData=$ivData[0];
        
        $uid=session('uid');
        if($ivData['duid']!=$uid){
            echo "这个邀请好像并不是针对你的哦。";
            exit();
        }
        $sv['gid']=$ivData['sgid'];
        $pos=$ivData['pos'];
        $groupQuery=M('group');
        if($pos==1){
            $waitUid=$groupQuery->where($sv)->getField('u1');
            $tu['u1']=0;
        }else if($pos==2){
            $waitUid=$groupQuery->where($sv)->getField('u2');
            $tu['u2']=0;
        }else if($pos==3){
            $waitUid=$groupQuery->where($sv)->getField('u3');
            $tu['u3']=0;
        }else if($pos==4){
            $waitUid=$groupQuery->where($sv)->getField('u4');
            $tu['u4']=0;
        }else{
            exit("邀请数据错误。");
        }

        $eu['eu']=1;
        if($waitUid==$uid){
            $upStat=$groupQuery->where($sv)->save($tu);
            if(!$upStat) exit("修改组队数据错误，请联系管理员。");
            $epStat=$inviteQuery->where($ifv)->save($eu);
            if(!$epStat) exit("修改邀请数据错误，请联系管理员");
            echo "success";
        }else{
            $epStat=$inviteQuery->where($ifv)->save($eu);
            if(!$epStat) exit("修改邀请数据错误，请联系管理员");
            echo "success";
        }
    }
    public function accinvite($iid){
        islogin();
        if(session('type')!=0){
            echo "你已经是有身份的人了。";
            exit();
        }
        $inviteQuery=M('invite');
        $ifv['iid']=$iid;
        $ivData=$inviteQuery->where($ifv)->select();
        $ivData=$ivData[0];
        $uid=session('uid');
        if($ivData['duid']!=$uid){
            echo "这个邀请好像并不是针对你的哦。";
            exit();
        }
        $sv['gid']=$ivData['sgid'];
        $pos=$ivData['pos'];
        $groupQuery=M('group');
        $gpData=$groupQuery->where($sv)->select();
        $gpData=$gpData[0];
        if($pos==1){
            $waitUid=$gpData['u1'];
        }else if($pos==2){
            $waitUid=$gpData['u2'];
        }else if($pos==3){
            $waitUid=$gpData['u3'];
        }else if($pos==4){
            $waitUid=$gpData['u4'];
        }else{
            exit("邀请数据错误。");
        }
        if($waitUid!=$uid){
            $eu['eu']=1;
            $upIvSt=$inviteQuery->where('iid='.$iid)->save($eu);
            if(!$upIvSt) exit("邀请信息更新错误。");
            echo "对方已经取消邀请。";
            exit();
        }else{
            $updUser['type']=1;
            $updUser['gid']=$ivData['sgid'];
            $userQuery=M('user');
            $upUrSt=$userQuery->where('uid='.$uid)->save($updUser);
            if(!$upUrSt) exit("用户信息更新错误。");
            $updGrp['gnum']=$gpData['gnum']+1;
            $upGpSt=$groupQuery->where('gid='.$ivData['sgid'])->save($updGrp);
            if(!$upGpSt) exit("组队信息更新错误。");
            $eu['eu']=1;
            $upIvSt=$inviteQuery->where('iid='.$iid)->save($eu);
            if(!$upIvSt) exit("邀请信息更新错误。");
            echo "success";
        }
    }
    public function exitgroup(){
        islogin();
        if(session('type')!=1){
            exit("不是队员不能退队。");
        }
        $uid=session('uid');
        $userQuery=M('user');
        $updUsr['type']=0;
        $updUsr['gid']=0;
        $upUrSt=$userQuery->where('uid='.$uid)->save($updUsr);
        if(!$upUrSt) exit("用户信息更新错误。");
        
        $gid=session('gid');
        $groupQuery=M('group');
        $gpData=$groupQuery->where('gid='.$gid)->select();
        $gpData=$gpData[0];
        $updGp['gnum']=$gpData['gnum']-1;
        if($gpData['u1']==$uid){
            $updGp['u1']=0;
        }else if($gpData['u2']==$uid){
            $updGp['u2']=0;
        }else if($gpData['u3']==$uid){
            $updGp['u3']=0;
        }else if($gpData['u4']==$uid){
            $updGp['u4']=0;
        }else{
            exit("组队信息读取错误。");
        }
        $upGpSt=$groupQuery->where('gid='.$gid)->save($updGp);
        if(!$upGpSt) exit("组队信息更新错误。");
        else echo "success";
    }
    public function delmember($pos){
        islogin();
        if(session('type')!=6) exit("只有队长可以操作。");
        $gid=session('gid');
        $groupQuery=M('group');
        $gpData=$groupQuery->where('gid='.$gid)->select();
        $gpData=$gpData[0];
        $uid=$gpData['u'.$pos];
        
        $userQuery=M('user');
        $usrData=$userQuery->where('uid='.$uid)->select();
        $usrData=$usrData[0];
        
        if($usrData['gid']!=$gid){
            $upGp['u'.$pos]=0;
            $upGpSt=$groupQuery->where('gid='.$gid)->save($upGp);
            if(!$upGpSt) exit("更新组队信息失败。");
            else echo "success";
        }else{
            $upUr['type']=0;
            $upUr['gid']=0;
            $upUrSt=$userQuery->where('uid='.$uid)->save($upUr);
            if(!$upUrSt) exit("更新用户信息失败。");
            
            $upGp['u'.$pos]=0;
            $upGp['gnum']=$gpData['gnum']-1;
            $upGpSt=$groupQuery->where('gid='.$gid)->save($upGp);
            if(!$upGpSt) exit("更新组队信息失败。");
            else echo "success";
        }
    }
    public function delgroup(){
        islogin();
        if(session('type')!=6) exit("只有队长可以操作。");
        $gid=session('gid');
        $groupQuery=M('group');
        $gpData=$groupQuery->where('gid='.$gid)->select();
        $gpData=$gpData[0];
        
        $userQuery=M('user');
        $upUr['type']=0;
        $upUr['gid']=0;
        for($i=0;$i<5;$i++){
            $thisuid=$gpData['u'.$i];
            if($thisuid!=0){
                $upUrSt=$userQuery->where('uid='.$thisuid)->save($upUr);
                if(!$upUrSt) exit("更新用户信息失败。");
            }
        }
        $delStat=$groupQuery->where('gid='.$gid)->delete();
        if(!$delStat) exit("组队删除失败。");
        else echo "success";
    }
}