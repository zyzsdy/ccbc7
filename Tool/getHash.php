<?php

$str=$_GET['str'];
$callback=$_GET['callback'];

$ans['crc32']=crc32($str);
$ans['md5']=md5($str);
$ans['sha1']=sha1($str);

echo $callback;
echo "(";
echo json_encode($ans);
echo ")";