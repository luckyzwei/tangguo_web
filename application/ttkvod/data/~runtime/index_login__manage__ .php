<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3c.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML><HEAD><TITLE>天天看影视-管理系统</TITLE>
<META http-equiv=Content-Type content="text/html; charset=utf-8">
<META content= name=keywords>
<META content= name=description>
<LINK href="<?php echo $this->mThemePath;?>images/style.css" type=text/css rel=stylesheet>
<META content="MSHTML 6.00.3790.4732" name=GENERATOR></HEAD>
<BODY>
<DIV id=top>
<TABLE height=98 cellSpacing=0 cellPadding=0 width=1000 border=0>
  <TBODY>
  <TR>
    <TD>&nbsp;</TD></TR></TBODY></TABLE></DIV><!--top end-->
<DIV id=main>
<TABLE height=279 cellSpacing=0 cellPadding=0 width=1000 border=0>
  <TBODY>
  <TR>
    <TD width=283>
      <DIV id=main_left></DIV></TD>
    <TD width=438>
      <DIV id=main_con>
	<form method="post" action="<?php echo $this->mRouter->urlEx('index', 'login')?>">
      <TABLE cellSpacing=0 cellPadding=0 width=200 border=0>
        <TBODY>
        <TR>
          <TD width=50 height=21>用户名：</TD>
          <TD align=left width=139 height=21><LABEL><input id="username" name="username" maxlength="18" type="text" class="yo" /></LABEL></TD></TR>
        <TR>
          <TD width=50 height=21>密&nbsp;&nbsp;&nbsp;&nbsp;码：</TD>
          <TD align=left width=139 height=21><LABEL><input type="password" name="password" maxlength="18" type="text" class="yo" /> </LABEL></TD></TR>
        <TR>
          <TD width=50 height=21>验证码：</TD>
          <TD align=left width=139 height=21><LABEL><input type="text" name="safeCode" size="4"  maxlength="4" type="text" class="ya" /></LABEL><img src="<?php echo $this->mRouter->urlEx('index', 'code')?>" height=20 width=80 onClick="this.src='<?php echo $this->mRouter->urlEx('index', 'code')?>/tt/'+(new Date()).getSeconds()" alt="看不清?请单击" style="cursor:pointer"/></TD></TR>
        <TR>
          <TD colSpan=2 height=30><INPUT id=logon 
            style="MARGIN-TOP: 10px; MARGIN-LEFT: 53px" type=image height=21 
            width=69 src="<?php echo $this->mThemePath;?>images/pic14.jpg" border=0 name=submit> <INPUT 
            id=close type=image height=21 width=69 src="<?php echo $this->mThemePath;?>images/pic15.jpg" 
            border=0 name=close></TD></TR></TBODY></TABLE></form></DIV></TD>
    <TD width=283>
      <DIV id=main_right></DIV></TD></TR></TBODY></TABLE></DIV><!--main end--><!--main2 start-->
<DIV id=main2>
<TABLE height=57 cellSpacing=0 cellPadding=0 width=1000 border=0>
  <TBODY>
  <TR>
    <TD width=284>
      <DIV id=main2_left></DIV></TD>
    <TD width=438>
      <DIV id=main2_con>
      <DIV id=main2c_left></DIV>
      <DIV id=main2c_con></DIV></DIV></TD>
    <TD width=282>
      <DIV id=main2_right></DIV></TD></TR></TBODY></TABLE></DIV><!--main2 end--><!--bottom start-->
<DIV id=bottom>
<P>本系统建议使用或IE7以上版本浏览计算机屏幕分辩使用1024*768或以上<BR></P></DIV><!--bottom end--></BODY></HTML>
