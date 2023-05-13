<?php
include 'init.php';
include 'common/functions.php';

if($_POST['form_password'] != ""){
	global $myPDO;
    $pass_encrypt = md5($_POST['form_password']);
    $user_id = $_POST['user_id'];
	$type = get_value_from_id("users","role","id",$user_id);
    $myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt_edit = $myPDO->query("UPDATE users SET password = '$pass_encrypt', active_key='' WHERE id='$user_id'");
    if($stmt_edit->rowCount() > 0) {
		$to = get_value_from_id("users","email","id",$user_id);
		$strSubject = "CBIC SAMAY | Password Reset";
		$message = '<tr><th style="padding: 0;"><h3 style="background-color: #0073b1;padding: 20px 0;color: #fff;margin: 0;">Password Reset | CBIC Samay</h3></th></tr>';
		$message .= '<tr><td style="background-color: #edf0f3;padding: 15px;">You have successfully reset your password.</td></tr>';
		send_mail_function($to, $strSubject, $message, $site_email, $site_name);
		echo json_encode(array(
				"statusCode"=>'success',
				"message"=>$type,
				"link"=>SITEURL
			));
    }else {
		echo json_encode(array(
				"statusCode"=>'failure',
				"message"=>'Some error occured while updating your password...!'
			));
    }
}else {
    header("Location:".SITEURL);
}
?>