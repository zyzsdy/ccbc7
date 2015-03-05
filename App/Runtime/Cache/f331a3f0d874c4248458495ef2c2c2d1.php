<?php if (!defined('THINK_PATH')) exit();?><!doctype html><html lang="zh-cn"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="IE=edge"><title>CCBC-7</title><link type="text/css" href="http://ccbc7-static.stor.sinaapp.com/config/bootstrap.min.css" rel="stylesheet" media="screen"><link type="text/css" href="http://ccbc7-static.stor.sinaapp.com/tpl/style.css" rel="stylesheet" media="screen"><link type="text/css" href="http://ccbc7-static.stor.sinaapp.com/config/bootstrap-responsive.min.css" rel="stylesheet" media="screen"><!--[if lt IE 9]><script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script><script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script><![endif]--><style type="text/css">.c-countdown {
    text-align:center;
    margin-top: 60px;
    font-size: 18px;
}
</style></head><body><div class="container"><div id="logo"><img src="http://ccbc7-static.stor.vipsinaapp.com/config/notlogo.jpg" height="257" width="534"></div><div class="c-countdown"><?php if(($_SESSION['login']) == "1"): ?><a class="btn btn-primary btn-large" href="/game"><?php echo (session('name')); ?></a><a class="btn btn-large" onClick="unlogin();">注销</a><?php else: ?><a class="btn btn-primary btn-large" href="/login">点击登录</a><a class="btn btn-large" href="/reg">参与围观</a><?php endif; ?><p>&nbsp;</p><p>系统公告：<?php echo ($last_anno); ?></p></div></div><!--载入Javascript脚本--><!--页面底部的脚本载入有利于提高页面载入速度--><script type="text/javascript" src="http://ccbc7-static.stor.sinaapp.com/config/jquery-1.10.2.min.js"></script><script type="text/javascript" src="http://ccbc7-static.stor.sinaapp.com/config/bootstrap.min.js"></script><script type="text/javascript">    function unlogin(){
        $.get("/login/unlogin",{},function(data){
            if(data=="success"){
                alert("成功注销");
                window.location.href="/";
            }else{
                alert("注销失败。"+data);
            }
        });
    }
    </script></body></html>