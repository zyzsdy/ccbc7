<?php
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
class standingAction extends Action{
    public function index(){
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
		$nm['ulist']=$groupData;
        $this->assign($nm);
        $this->display();
    }
}