(function(){
	//创建界面
	var d=document.getElementsByTagName('body')[0];
	var toolOutside=document.createElement("div");
	toolOutside.style.background="#FFFFCC";
	toolOutside.id="toolDiv";
	toolOutside.style.height="500px";
	toolOutside.style.width="400px";
	toolOutside.style.position="fixed";
	toolOutside.style.left="0";
	toolOutside.style.bottom="0";
	toolOutside.style.display="none";
	toolOutside.innerHTML=pushHTML();
	toolOutside.style.zIndex=999;
	d.appendChild(toolOutside);

	//绑定响应，按下Ctrl-U时弹出。
	$(document).keydown(function(e){
		if(e.ctrlKey&&e.which==85){
			var nowTextInSelect=getSelectedText();
			$("#pretext").val(nowTextInSelect);
			$("#toolDiv").show();
			e.preventDefault();
		}
	});
	//界面主体
	function pushHTML(){
		return "<button class=\"close\" onClick=\"hideDiv()\">&times;</button>\
		<div style=\"font-family:微软雅黑 !important;color: #000000;text-align:center !important\">\
					<h3>CCBC7助手</h3>\
					<p><textarea id=\"pretext\" rows=\"8\" style=\"margin: 0px 0px 10px; width: 370px;\"></textarea>\
					<p>\
						<select id=\"ToolFunList\" onChange=\"FunListDo($(this).val());\">\
							<option value=\"0\">字符变换功能--请选择</option>\
							<option value=\"1\">清空</option>\
							<option value=\"2\">汉字转拼音</option>\
							<option value=\"3\">频率统计</option>\
							<option value=\"4\">去空格</option>\
							<option value=\"5\">倒序</option>\
							<option value=\"6\">按词倒序</option>\
							<option value=\"7\">每行一词</option>\
							<option value=\"8\">加空格</option>\
							<option value=\"9\">大小写转换</option>\
							<option value=\"10\">批量替换</option>\
							<option value=\"11\">进制转换</option>\
							<option value=\"12\">MD5/SHA1/CRC32</option>\
							<option value=\"13\">ASCII/Unicode</option>\
							<option value=\"dea\">---------------</option>\
							<option value=\"14\">恺撒变换</option>\
							<option value=\"15\">Atbash</option>\
							<option value=\"16\">栅栏变换</option>\
						</select>\
						<span id=\"moreInfo\"></span>\
					</p>\
					<p style=\"color:#666666;font-size:10\">专注帮助你的密码工具 Powered by Namido</p>\
				</div>";
	}
})();

function FunListDo(funi){
	if(funi==0)
		$("#moreInfo").html("");
	else if(funi=="dea"){
		$("#moreInfo").html("");
		alert("这是一条分隔线，没事不要点。。。");
	}
	else if(funi==1){
		$("#pretext").val("");
		$("#moreInfo").html("");
		$("#ToolFunList").val(0);
	}
	else if(funi==2)
		Pinyin();
	else if(funi==3)
		freqAnalysis();
	else if(funi==4)
		deleteSpace();
	else if(funi==5)
		AllReverse();
	else if(funi==6)
		WordReverse();
	else if(funi==7)
		RowWord();
	else if(funi==8)
		AddSpace();
	else if(funi==9)
		ToogleUpLow();
	else if(funi==10)
		Allreplace();
	else if(funi==11)
		baseNumConvert();
	else if(funi==12)
		getHash();
	else if(funi==13)
		toascii();
	else if(funi==14)
		caesar();
	else if(funi==15)
		atbash();
	else if(funi==16)
		railFence();

}

//栅栏密码
function railFence(){
	$("#moreInfo").html("<br><input type=\"text\" class=\"span1\" id=\"rf_fencenum\" placeholder=\"栏数\">\
		<button class=\"btn\" onClick=\"rf_en()\">加密</button><button class=\"btn\" onClick=\"rf_dc()\">解密</button>\
		");
}
function rf_en(){
	var num=$("#rf_fencenum").val();
	var strIn=$("#pretext").val();
	var strOut=[];
	for(var i=0;i<num;i++){
		strOut.push("");
	}
	var strTop=0;
	while(strTop<strIn.length){
		for(var i=0;i<num;i++){
			if(strTop<strIn.length){
				strOut[i]+=strIn.charAt(strTop);
			}else{
				strOut[i]+="@";
			}
			strTop++;
		}
	}
	$("#pretext").val(strOut.join(""));
}
function rf_dc(){
	var num=$("#rf_fencenum").val();
	var strIn=$("#pretext").val();
	var strArr=[];
	var udNum=Math.ceil(strIn.length/num);
	var strTop=0;
	for(var i=0;i<num;i++){
		strArr.push("");
		for(var j=0;j<udNum;j++){
			if(strTop<strIn.length)
				strArr[i]+=strIn.charAt(strTop);
			else strArr[i]+="@";
			strTop++;
		}
	}
	var strOut="";
	for(var i=0;i<udNum;i++){
		for(var j=0;j<num;j++){
			strOut+=strArr[j].charAt(i);
		}
	}
	$("#pretext").val(strOut);
}

//埃特巴什变换
function atbash(){
	$("#moreInfo").html("<button class=\"btn\" onClick=\"atbash()\">执行</button>");
	var strIn=$("#pretext").val().split("");
	var strOut=[];
	for(k in strIn){
		var ord=strIn[k].charCodeAt(0);
		if(ord>=65&&ord<=90){
			ord=(25-ord+65)%26;
			if(ord<0) ord+=26;
			ord+=65;
			strOut.push(String.fromCharCode(ord));
		}else if(ord>=97&&ord<=122){
			ord=(25-ord+97)%26;
			if(ord<0) ord+=26;
			ord+=97;
			strOut.push(String.fromCharCode(ord));
		}else{
			strOut.push(String.fromCharCode(ord));
		}
	}
	$("#pretext").val(strOut.join(""));
}


//凯撒变换
function caesar(){
	$("#moreInfo").html("<br><input type=\"text\" id=\"caesar_vector\" class=\"span1\" placeholder=\"变换位数\">\
		<button class=\"btn\" onClick=\"do_caesar()\">变换</button><button class=\"btn\" onClick=\"unicode_caesar()\">Unicode变换</button>\
		");
}
function do_caesar(){
	var num=parseInt($("#caesar_vector").val());
	var strIn=$("#pretext").val().split("");
	var strOut=[];
	for(k in strIn){
		var ord=strIn[k].charCodeAt(0);
		if(ord>=65&&ord<=90){
			ord=(ord-65+num)%26;
			if(ord<0) ord+=26;
			ord+=65;
			strOut.push(String.fromCharCode(ord));
		}else if(ord>=97&&ord<=122){
			ord=(ord-97+num)%26;
			if(ord<0) ord+=26;
			ord+=97;
			strOut.push(String.fromCharCode(ord));
		}else{
			strOut.push(String.fromCharCode(ord));
		}
	}
	$("#pretext").val(strOut.join(""));
}
function unicode_caesar(){
	var num=parseInt($("#caesar_vector").val());
	var strIn=$("#pretext").val().split("");
	var strOut=[];
	for(k in strIn){
		var ord=strIn[k].charCodeAt(0);
		ord+=num;
		strOut.push(String.fromCharCode(ord));
	}
	$("#pretext").val(strOut.join(""));
}

//ASCII转换
function toascii(){
	$("#moreInfo").html("<br><button class=\"btn\" onClick=\"char_to_ascii()\">文字->ASCII</button>\
		<button class=\"btn\" onClick=\"ascii_to_char()\">ASCII->文字</button>");
}
function char_to_ascii(){
	var strIn=$("#pretext").val().split("");
	var strOut=[];
	for(k in strIn){
		strOut.push(strIn[k].charCodeAt(0));
	}
	$("#pretext").val(strOut.join(" "));
}
function ascii_to_char(){
	var strIn=$("#pretext").val().split(/\s+/);
	var strOut=[];
	for(k in strIn){
		strOut.push(String.fromCharCode(parseInt(strIn[k])));
	}
	$("#pretext").val(strOut.join(""));
}

//HASH变换
function getHash(){
	var strIn=$("#pretext").val();
	getJSONp("http://ccbc7.tk/Tool/getHash.php",{
		"str" : strIn,
	},function(data){
		$("#moreInfo").html("<button class=\"btn\" onClick=\"getHash()\">执行</button><br>\
			CRC32:"+data.crc32+"<br>MD5:"+data.md5+"<br>SHA1:"+data.sha1);
	});
}
//进制转换
function baseNumConvert(){
	$("#moreInfo").html("<br><input type=\"text\" id=\"base_ob\" class=\"span2\" placeholder=\"原始进制2-62\">\
		<input type=\"text\" id=\"base_db\" class=\"span2\" placeholder=\"目标进制2-62\">\
		<button class=\"btn\" onClick=\"doBaseConvert()\">执行</button><br>\
		2-62进制转换 A-Z表示10-36 若转换16进制请先全转大写\
		");
}
function doBaseConvert(){
	var ob=parseInt($("#base_ob").val());
	var db=parseInt($("#base_db").val());
	if(ob<2||ob>62||db<2||db>62){
		alert("输入数据不合法。");
		return;
	}
	if(ob==db){
		alert("源和目标进制相同，无需转换。");
		return;
	}
	var strIn=$("#pretext").val().split(/\s+/).join(";");
	getJSONp("http://ccbc7.tk/Tool/baseConvert.php",{
		"ob" : ob,
		"db" : db,
		"num" : strIn,
	},function(data){
		strOut=data.res.join(" ");
		$("#pretext").val(strOut);
	});
}

//批量替换
function Allreplace(){
	$("#moreInfo").html("<br><input type=\"text\" id=\"re_ori\" class=\"span2\" placeholder=\"查找...\">\
		<input type=\"text\" id=\"re_des\" class=\"span2\" placeholder=\"替换为...\">\
		<button class=\"btn\" onClick=\"doReplace()\">执行</button>");
}
function doReplace(){
	var ori_exp="/"+$("#re_ori").val()+"/g";
	var ori=eval(ori_exp);
	var des=$("#re_des").val();
	var strIn=$("#pretext").val();
	$("#pretext").val(strIn.replace(ori,des));
}

//大小写转换
function ToogleUpLow(){
	$("#moreInfo").html("<button class=\"btn\" onClick=\"All_toUpper()\">全转大写</button><button class=\"btn\" onClick=\"All_toLower()\">全转小写</button>");
}
function All_toUpper(){
	var strIn=$("#pretext").val();
	$("#pretext").val(strIn.toUpperCase());
}
function All_toLower(){
	var strIn=$("#pretext").val();
	$("#pretext").val(strIn.toLowerCase());
}

//每行一词
function RowWord(){
	$("#moreInfo").html("<button class=\"btn\" onClick=\"RowWord()\">执行</button>");
	var strIn=$("#pretext").val();
	$("#pretext").val(strIn.split(/\s+/).join('\n'));
}
//添加空格
function AddSpace(){
	$("#moreInfo").html("<input class=\"span1\" type=\"text\" id=\"Add_spacenum\" value=\"2\" /><button class=\"btn\" onClick=\"doAddSpace()\">执行</button>");
}
function doAddSpace(){
	var num=$("#Add_spacenum").val();
	var n=parseInt(num);
	if(n==NaN){
		alert("请输入一个有效的数字。");
		return;
	}
	var strIn=$("#pretext").val();
	var strOut="";
	for(var i=0;i<strIn.length;i++){
		strOut+=strIn.charAt(i);
		if((i+1)%n==0) strOut+=" ";
	}
	$("#pretext").val(strOut);
}
//倒序
function Reverse(str){
	var strOut="";
	for(var i=0; i<str.length; i++){
		strOut=str.charAt(i)+strOut;
	}
	return strOut;
}
function AllReverse(){
	$("#moreInfo").html("<button class=\"btn\" onClick=\"AllReverse()\">执行</button>");
	var strIn=$("#pretext").val();
	$("#pretext").val(Reverse(strIn));
}
function WordReverse(){
	$("#moreInfo").html("<button class=\"btn\" onClick=\"WordReverse()\">执行</button>");
	var strIn=$("#pretext").val().split(/\s+/);
	var strOut=[];
	for(k in strIn){
		strOut.push(Reverse(strIn[k]));
	}
	$("#pretext").val(strOut.join(" "));
}

//汉字转拼音
//并不在本地完成，而是调用了API
function Pinyin(){
	$("#moreInfo").html("<button class=\"btn\" onClick=\"Pinyin()\">执行</button>");
	var text=$("#pretext").val();
	getJSONp("http://skapi.sinaapp.com/pinyin/index.php",{
		"ie" : "utf-8",
		"wd" : text,
	},function(data){
		$("#pretext").val(data.pinyin.join(" "));
	});
}

//频率统计
//密码机器不知道哪个版本中的代码。
//我只改了一点点
function freqAnalysis(){
	$("#moreInfo").html("<button class=\"btn\" onClick=\"freqAnalysis()\">执行</button>");
	var ALPHABET = "abcdefghijklmnopqrstuvwxyz";
	var aFreq=new Array(26);
	for(var i=0;i<26;i++)
		aFreq[i]=0;
	var strIn=$("#pretext").val().toLowerCase();
	var strOut="字符    频率    数量\n";
	var num=0;
	for (i=0; i<strIn.length; i++){
		var idx=ALPHABET.indexOf(strIn.charAt(i));
		if(idx != -1){
			aFreq[idx]++;
			num++;
		}
	}
	for (i=0; i<26; i++){
		var par1=ALPHABET.charAt(i).toString();
		var par2=(Math.floor(aFreq[i]*100/num)/100).toString() + "%";
		var par3=(aFreq[i]).toString();
		strOut += par1 + moreSpace(par1,8) + par2 + moreSpace(par2,8) + par3 + "\n";
	}
	strOut = "共出现" + num + "个英文字符\n\n" + strOut;
	alert(strOut);
}
function moreSpace(str, destlength){
	var len=str.length;
	var Out=[];
	for(var i=0;i<destlength-len;i++){
		Out.push(" ");
	}
	return Out.join("");
}

//去空格（偷懒版）
//按空格分开成数组然后在直接join就是去空格了？
function deleteSpace(){
	$("#moreInfo").html("<button class=\"btn\" onClick=\"deleteSpace()\">执行</button>");
	var strIn=$("#pretext").val();
	$("#pretext").val(strIn.split(" ").join(""));
}


//关闭弹出框
function hideDiv(){
	$("#toolDiv").hide();
}

//工具函数——发送JSONp请求并获取返回结果。
function getJSONp(url, param, cb) {
    var fid='nmp_JSONP_'+(new Date()).getTime();
    var p=['callback='+fid];
    for (k in param) {
        p.push(k+'='+param[k]);
    }
    url+=(url.indexOf('?')==-1?'?':'&')+p.join('&');
    var o=document.createElement('script');
    o.type='text/javascript';
    o.src=url;
    window[fid]=function(){
        cb.apply(o, arguments);
        document.body.removeChild(o);
        delete window[fid];
        delete o;
    }
    document.body.appendChild(o);
}

//工具函数——获取选中内容
function getSelectedText(){  
    if(window.getSelection)
        return window.getSelection().toString();
    else if(document.getSelection)
        return document.getSelection();
    else if(document.selection)
        return document.selection.createRange().text;
    return null;
} 