<?php

/*####################################################
โปรแกรม: SMEweb เวอร์ชั่น: 1.4f  
คือโปรแกรมบริหารเว็บไซต์ Content Manager System (CMS)
พัฒนาขึ้นมาจาก ภาษา PHP HTML และ JAVASCRIPT 
เป็นโปรแกรมเปิดเผย Source Code แจกจ่ายให้ใช้งานได้ฟรี โดยไม่มีค่าใช้จ่าย 
ท่านสามารถ เผยแพร่ ทำซ้ำ แก้ไข ดัดแปลง โปรแกรมนี้ได้ ภายใต้ข้อกำหนดและเงื่อนไข GPL 
ทางผู้พัฒนา จะไม่รับผิดชอบความเสียหายที่เกิดขึ้น จากโปรแกรมนี้ในทุกกรณี

GPL คืออะไร?
อ่านเอกสาร GPL ภาษาไทยได้ที่ http://developer.thai.net/gpl/
อ่านเอกสาร GPL ต้นฉบับได้ที่ http://www.gnu.org/copyleft/gpl.html

Copyright (C) 2007  Mr.Monsun Uthayanugul 
E-mail: admin@ebizzi.net  Homepage: http://www.ebizzi.net/
#####################################################*/

/*####################################################
SMEShop Version 2.0 - Development from SMEWeb 1.5f 
Copyright (C) 2016 Mr.Jakkrit Hochoey
E-Mail: support@siamecohost.com Homepage: http://www.siamecohost.com
#####################################################*/

session_start();

define('TIMEZONE', 'Asia/Bangkok'); //Set PHP TimeZone
date_default_timezone_set(TIMEZONE); //Set MySQL TimeZone

include ("config.php");
require("phpmailer/class.phpmailer.php"); // path to the PHPMailer class

if((trim($_POST["custemail"]))&&(trim($_POST["subject"]))&&(trim($_POST["details"]))&&(trim($_POST["b5"])))
{
	
////////////////////////////////////////////////////////////////////////////////////////// Add to contactus  //////////////////////////////////////////////////////////////////////////////

$custname = $_POST[ 'custname' ];
$custname = stripslashes( $custname );
$custname = mysqli_real_escape_string($connection,$custname );

$custemail = $_POST[ 'custemail' ];
$custemail = stripslashes( $custemail );
$custemail = mysqli_real_escape_string($connection,$custemail );

$subject = $_POST[ 'subject' ];
$subject = stripslashes( $subject );
$subject = mysqli_real_escape_string($connection,$subject );

$details = $_POST[ 'details' ];
$details = stripslashes( $details );
$details = mysqli_real_escape_string($connection,$details);

$custid = $_SESSION["member"]['user'];

if(mysqli_query($connection,"insert into ".$fix."contactus values ('','$custid','$custname','$custemail','$subject','$details','".$createon."','1','0')"))
{

////////////////////////////////////////////////////////////////////////////////////////// Add to Database  //////////////////////////////////////////////////////////////////////////////

	$send=1;

    if(!$_SESSION["nspam"]){ unset($send); }
    if($_POST["b5"]!=$_SESSION["nspam"]){ unset($send);  }
	
	if($send){
	
		if($sendmailtype=="0") {
			//ส่งอีเมล์ด้วยฟังก์ชั่น Mail() ของ PHP
			$headers  = "MIME-Version: 1.0\r\n"; 
			$headers .= "Content-type: text/plain; charset=utf-8\r\n"; 
			$headers .= "From: $custemail\r\n"; 
			$headers .= "Return-Path: $custemail\r\n\r\n";
			@mail($emailcontact,$subject,$details,$headers);
			/*
			if(@mail($emailcontact,$subject,$details,$headers)) {
				echo "<meta name=language content=TH>";
				echo "<meta http-equiv=Content-Type content=text/html; charset=utf-8>";
				echo "<center><font color=green><h3>"._LANG_57."</h3></font></center>";
			} else {
				echo "<meta name=language content=TH>";
				echo "<meta http-equiv=Content-Type content=text/html; charset=utf-8>";
				echo "<center><font color=red><h3>"._LANG_58."</h3></font></center>";
			}
			*/
		}

		if($sendmailtype=="1") {
			//ส่งอีเมล์ด้วย SMTP ของโฮสต์ ด้วยฟังก์ชั่น PHPMailer
			$mail = new PHPMailer();
			$mail->CharSet = "utf-8"; 
			$mail->IsSMTP();
			$mail->Mailer = "smtp";
			$mail->SMTPAuth = true;
			$mail->Host = $smtp_hostname; //ใส่ SMTP Mail Server ของท่าน
			$mail->Port = $smtp_portno; // หมายเลข Port สำหรับส่งอีเมล์
			$mail->Username = $smtp_username; //ใส่ Email Username ของท่าน (ที่ Add ไว้แล้วใน Plesk Control Panel)
			$mail->Password = $smtp_password; //ใส่ Password ของอีเมล์ (รหัสผ่านของอีเมล์ที่ท่านตั้งไว้) 
			
			$mail->From = $custemail;
			$mail->FromName = $custname;
			$mail->AddAddress($emailcontact);
			$mail->AddReplyTo($custemail);
			
			//ต้องทำการเข้ารหัสก่อน มิฉะนั้น Subject จะแสดงภาษาไทยไม่ได้ (เฉพาะ tis-620)
			//$subject = "=?tis-620?B?".base64_encode($subject)."?=";
			
			$subject =$subject;
			
			$mail->Subject = $subject;   			

			$mail->Body     = $details;
			
			if($attachfile != "") {
				$uploaddir = "uploads/";
				$uploadfile = $uploaddir . basename($_FILES['fattachfile']['name']);
				move_uploaded_file($_FILES['attachfile']['tmp_name'], $uploadfile);
				$mail-> Addattachment($uploadfile);
			}
			
			$mail->Send();
			/*
			if($mail->Send()) {
				echo "<meta name=language content=TH>";
				echo "<meta http-equiv=Content-Type content=text/html; charset=tis-620>";
				echo "<center><font color=green><h3>"._LANG_57."</h3></font></center>";		
			} else {	
				echo "<meta name=language content=TH>";
				echo "<meta http-equiv=Content-Type content=text/html; charset=tis-620>";
				echo "<center><font color=red><h3>"._LANG_58."</h3></font></center>";
				echo '<center>Error: ' . $mail->ErrorInfo."</center>";
			}
			*/
		}
		
	}
	echo "<div class='boxshadow boxlemon' align=center><h1>บันทึกข้อมูลของท่านเรียบร้อยแล้ว</h1></div><br>";
} else {
	echo "<div class='boxshadow boxred' align=center><h1>Error: ไม่สามารถบันทึกข้อมูลของท่านได้ในขณะนี้</h1></div><br>";
}
}

echo "<script language=javascript src=\"js/checkcontactus.js\"></script>";

echo "
<table width=\"100%\" class=\"mytables\"><form name=\"contactus\" method=\"post\" enctype=\"multipart/form-data\" onsubmit=\"return checkcontactusform()\">
<tr bgcolor=\"$syscolor1\"><td colspan=2 align=center><h3><i class='fa fa-edit'></i> แบบฟอร์มติดต่อสอบถาม</h3></td></tr>
<tr bgcolor=\"$syscolor1\"><td width=30%>ส่งถึง</td><td><input class=\"tblogin\" type=email size=40 value=\"$emailcontact\" readonly></td></tr>
<tr bgcolor=\"$syscolor1\"><td>ชื่อ-นามสกุล</td><td><input class=\"tblogin\" type=text size=40 name=custname value='".$_SESSION['member']['name']."'> *</td></tr>
<tr bgcolor=\"$syscolor1\"><td>อีเมล์ของท่าน</td><td><input class=\"tblogin\" type=text size=40 name=custemail value='".$_SESSION['member']['email']."'> *</td></tr>
<tr bgcolor=\"$syscolor1\"><td>เรื่อง</td><td><input class=\"tblogin\" type=text size=40 name=subject> *</td></tr>
<tr bgcolor=\"$syscolor1\"><td>รายละเอียด</td><td valign=top><textarea name=details rows=5 cols=40></textarea> *</td></tr>";

if($smtp_attachfile==1){
	echo "<tr bgcolor=\"$syscolor1\"><td>ไฟล์แนบ</td><td><input type =\"file\" name=\"attachfile\"><br><font color=red>เฉพาะไฟล์ .png/.jpg/.jpeg/.gif/.doc/.docx/.pdf เท่านั้น</td></tr>";
}

echo "
<tr bgcolor=\"$syscolor1\">
	<td align=right><table cellspacing=1 cellpadding=0 bgcolor=red><tr><td><img src=\"img.php\"></td></tr></table></td>
	<td><input type=text size=8 name=b5> * <font size=2>ใส่รหัสที่ท่านเห็นในกรอบสีแดง</font></td>
</tr>";


echo "<tr bgcolor=\"$syscolor1\"><td colspan=2 align=center><input type=submit value=\" "._LANG_56." \" class=\"myButton\"></td></tr></form></table></center>";
?>