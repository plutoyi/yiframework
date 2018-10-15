	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<title>系统发生错误</title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
	<meta name="Generator" content="EditPlus"/>
	<style>
	body{
		font-family: \'Microsoft Yahei\', Verdana, arial, sans-serif;
		font-size:14px;
	}
	a{text-decoration:none;color:#174B73;}
	a:hover{ text-decoration:none;color:#FF6600;}
	h2{
		border-bottom:1px solid #DDD;
		padding:8px 0;
		font-size:25px;
	}
	.title{
		margin:4px 0;
		color:#F60;
		font-weight:bold;
	}
	.message,#trace{
		padding:1em;
		border:solid 1px #000;
		margin:10px 0;
		background:#FFD;
	}
	.message{
		background:#FFD;
		color:#2E2E2E;
			border:1px solid #E0E0E0;
	}
	#trace{
		background:#E7F7FF;
		border:1px solid #E0E0E0;
		color:#535353;
	}
	.notice{
		padding:10px;
		margin:5px;
		color:#666;
		background:#FCFCFC;
	}
	.red{
		color:red;
		font-weight:bold;
	}
	</style>
	</head>
	<body>
	<div class="notice">
	<h2>系统发生错误 </h2>
	<div >您可以选择 [ <A HREF="">重试</A> ] [ <A HREF="javascript:history.back()">返回</A> ] 或者 [ <A HREF="http://127.0.0.1/yiframework/index.php">回到首页</A> ]</div>

	<p><strong>错误位置:</strong>　FILE: <span class="red">%s</span> LINE: <span class="red"> %s </span></p>
	<p><strong>错误代码:</strong>　CODE: <span class="red">%s</span></p>
	<p class="title">[ 错误信息 ]</p>
	<p class="message">%s</p>

	<p class="title">[ TRACE ]</p>
	<p id="trace">%s</p>
	</div>
	<div align="center" style="margin:5pt;font-family:Verdana"> Powered by Devin.yang </div>
	</body>
	</html>