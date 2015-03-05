<?php
/* =========无限精度进制转换 API===============
 * By Zyzsdy
 * 2014-05-21
 * 本代码遵循GPL协议
 * ============================================
 * Thanks POJ 1220
 * And taesimple@csdnblog的解法
 */

$callback=$_GET['callback'];
$or_num=explode(";", $_GET['num']);
$b1=intval($_GET['ob']);
$b2=intval($_GET['db']);

$ans['res']=array();
$ans['count']=0;
foreach ($or_num as $value) {
	array_push($ans['res'],main($b1,$b2,$value));
	$ans['count']++;
}
echo $callback;
echo "(";
echo json_encode($ans);
echo ")";

//字符映射到整数
function char_2_int($char){  
	$c=ord($char);
	if($c >= ord('0') && $c <= ord('9')) $n = $c-ord('0');
	else if($c >= ord('A') && $c <= ord('Z')) $n = $c-ord('A')+10;
	else $n = $c-ord('a')+36;
	return $n;
}
//整数映射到字符
function int_2_char($n){  
	if($n >= 0 && $n <= 9) $c = chr($n+ord('0'));
	else if($n >= 10 && $n <= 35) $c = chr($n+ord('A')-10);
	else $c = chr($n+ord('a')-36);
	return $c;
}
function main($b1,$b2,$or_num){
	$str1=$or_num;
	$str2="";
	$quit=false;
	while(!$quit){
		$last=0;
		$quto="";
        for($i = 0; $i < strlen($str1); $i++){  //一次求商取余（关键）
            $cur = $last*$b1+char_2_int($str1[$i]);  //对输入数从高位开始进行逐位加权累加，存入cur
			if($cur < $b2){  //若cur的值小于基b2，则继续累加，且记商的对应位为0
				$last = $cur;
				$quto.= "0";
				continue;
			}
			else{  //若cur的值大于基b2，则进行除法运算，保存商的对应位，本次余数纳入加权累加中
				$quto.= int_2_char(floor($cur/$b2));
				$last = $cur%$b2;
			}
		}
		$quit = true;
		for($j = 0; $j < strlen($quto); $j++)  //检测商是否为0，若为0则表示转换完毕，结束循环
			if($quto[$j] != '0') $quit = false;
		$str2 .= int_2_char($last);  //保存本次除法运算的余数作为转换后新数的一位
		$str1 = ltrim($quto,"0");
	}
	return strrev($str2);
}