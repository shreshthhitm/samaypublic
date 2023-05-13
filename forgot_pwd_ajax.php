<?php
include 'init.php';
include 'common/functions.php';

if($_POST['email'] != ""){
	$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt_edit = $myPDO->query("SELECT * FROM users WHERE email='$email'");
	if($stmt_edit->rowCount() > 0) {
		$row_edit = $stmt_edit->fetch(PDO::FETCH_ASSOC);
		$active_code = md5(uniqid(rand(5, 15), true));
		$link = SITEURL.'/?user_id='.$row_edit['id'].'&key='.$active_code;         
		$myPDO->query("UPDATE users SET active_key = '$active_code' WHERE email='$email'");
		$to = "$email"; //change to ur mail address
		$strSubject = "CBIC SAMAY | Password Recovery Link";
		$message = '<tr><th style="padding: 0;"><h3 style="background-color: #0073b1;padding: 20px 0;color: #fff;margin: 0;">Password Recovery Link | CBIC Samay</h3></th></tr>';
		$message .= '<tr><td style="background-color: #edf0f3;padding: 15px;">Click on the link below to reset your password:-<br/>'.$link.'</td></tr>';              
		//$headers = 'MIME-Version: 1.0'."\r\n";
		//$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
		//$headers .= "From: info@crossboltsolutions.in";
		//$mail_sent = mail($to, $strSubject, $message, $headers);
		$mail_sent = send_mail_function($to, $strSubject, $message, $site_email, $site_name);
		//$mail_sent = true;
		if($mail_sent){
			echo 1;
		}else{
			echo 0;
		}
	}else{
		echo 0;
	}
}else{
	header("Location:".SITEURL);
}
?>