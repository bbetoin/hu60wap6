<?php
# 防止CC攻击

if (!$ENABLE_CC_BLOCKING) {
	return;
}

if(isset($CC_IP_LIMIT[$_SERVER['REMOTE_ADDR']])) {
	$CC_LIMIT[1]=$CC_IP_LIMIT[$_SERVER['REMOTE_ADDR']];
}

$tm=unpack('v',pack('v',$_SERVER['REQUEST_TIME']));$tm=$tm[1];

$ip = (hexdec(substr(md5($_SERVER['REMOTE_ADDR']), 0, 8)) % (256*256)) * 4;

if($CC_USE_MEMCACHE) {
	$key="cc/$ip";
	$jc=cache::get($key);
} else {
	if(!is_file($CC_DATA))
		file_put_contents($CC_DATA,str_repeat(chr(0),256*256*4));
	$CC_DATA=fopen($CC_DATA,'r+');
	fseek($CC_DATA,$ip);
	$jc=fread($CC_DATA,4);
}

$jc=unpack('v2',$jc);
$tm2=$jc[1];
$jc=$jc[2];

//var_dump($tm, $tm2, $jc, $CC_LIMIT);die;

if(($tm2=$tm-$tm2)<$CC_LIMIT[0] && $tm2>=0)
{
	if($jc>=$CC_LIMIT[1] /*&& $jc<$CC_LIMIT[1]*2*/)
	{
		header('HTTP/1.1 403 Forbidden');
		header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>刷新不规范，虎友两行泪</title>
    </head>
    <body>
        <h4>
            虎绿林第三区交通委提醒您：<br />
            网址千万条，耐心第一条。<br />
            刷新不规范，虎友两行泪。
        </h4>
        虎绿林低速网络限速<?php echo $CC_LIMIT[0]; ?>秒内最多访问<?php echo $CC_LIMIT[1]; ?>次（包括网页和图片），您已超速。<br />
        作为惩罚，吊销您的虎绿林通行证<?php echo $CC_LIMIT[0]-$tm2; ?>秒钟，在这段时间内您将不能访问虎绿林。<br />
        您的IP地址为<?php echo $_SERVER['REMOTE_ADDR']; ?>， 违章记录已存档。
    </body>
</html>
<?php
		// 超速用户访问日志
		$tm2=fopen($CC_BLOCK_LOG,'a+');
		fwrite($tm2,"<超速> $_SERVER[REMOTE_ADDR] <".date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME'])."> [PATH] $_SERVER[REQUEST_URI] [REF] $_SERVER[HTTP_REFERER]\n");
		fclose($tm2);

		exit;
	} else {
		$jc=$jc+1;
	}
} else {
	$jc=1;
}
$jc=pack('v2',$tm,$jc);
if($CC_USE_MEMCACHE) {
	cache::set($key,$jc,$CC_LIMIT[0]);
} else {
	fseek($CC_DATA,$ip);
	fwrite($CC_DATA,$jc);
	fclose($CC_DATA);
}

// 正常用户访问日志
/*
$tm2=fopen($CC_ACCESS_LOG,'a+');
fwrite($tm2,"<正常> $_SERVER[REMOTE_ADDR] <".date('Y-m-d H:i:s',$_SERVER['REQUEST_TIME'])."> [PATH] $_SERVER[REQUEST_URI] [REF] $_SERVER[HTTP_REFERER]\n");
fclose($tm2);
*/

unset($ip,$tm,$tm2,$jc,$key,$CC_DATA,$CC_LIMIT,$CC_USE_MEMCACHE,$CC_BLOCK_LOG,$CC_ACCESS_LOG,$CC_IP_LIMIT,$ENABLE_CC_BLOCKING);
