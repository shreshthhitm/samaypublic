<?php
include 'init.php';
include ROOT_DIR_COMMON.'functions.php';

if(!empty($_POST["form_phone"])) {
	$mobile = $_POST['form_phone'];
	$user_id = $_POST['user_id'];
	$user_role = get_value_from_id("users","role","id",$user_id);
	$old_mobile = get_value_from_id("commissioners","contact_no","user_id",$user_id);
	$email = get_value_from_id("users","email","id",$user_id);
	
    /*$message2 = "Dear ".$name.",<br/><br/>Thank you for enquiring with TaxSponsor, our colleague will get in touch with you shortly.";
	if(!isset($_POST['form_password'])){
		$message2 .= "<br/>We have generated a random password for you to login at ".SITEURL."/client-login/</p>. Your password is: ".$password;
	}*/
	
	if($user_role == 'board'){
		$user = "as Admin for the Board";
	}else{
		$user = "for ".get_value_from_id("commissioners","name","user_id",$user_id);
	}
	$link1 = $mobile;
	$smsMsg1 = "You have been linked to SAMAY ".$user.".";
	//sendSMS($mobile, $smsMsg1);

	$link2 = $old_mobile;
	$smsMsg2 = "You have been delinked from SAMAY ".$user.".";
	//sendSMS($old_mobile, $smsMsg2);
	
	//$smsMsg = str_replace("<br/>", "%0a", $message2);
	//$smsMsg = "You have been ".$ldMsg." SAMAY for ".$user;
	//sendSMS($mobile, $smsMsg);
	
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$co_qu = "UPDATE commissioners SET contact_no = '$mobile' WHERE user_id=$user_id";
	if($myPDO->query($co_qu)){
		$sms_msg_linked = (ENV == 'PROD' ? $sms_mob['linked'] : '');
		$sms_msg_delinked = (ENV == 'PROD' ? $sms_mob['delinked'] : '');
		sendSMS($old_mobile, $template_id['delinked'], $sms_msg_delinked);
		sendSMS($mobile, $template_id['linked'], $sms_msg_linked);
		//send_mail_function($email, $strSubject_link, $mail_msg_linked, $site_email, $site_name);
		//send_mail_function($email, $strSubject_delink, $mail_msg_delinked, $site_email, $site_name);
		send_mail_function($email, $strSubject_mobile_upd, $mail_msg_mobile_upd, $site_email, $site_name);
		
		echo json_encode(array("type"=>"success", "message1"=>$link1." - ".$smsMsg1, "message2"=>$link2." - ".$smsMsg2));
	}else{
		echo json_encode(array("type"=>"failure"));
	}
	/*$to = "$email"; //change to ur mail address
	$strSubject = "SAMAY | Password Recovery Link";
	$message = '<p>Password Recovery Link : '.$link.'</p>' ;              
	$headers = 'MIME-Version: 1.0'."\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
	$headers .= "From: info@crossboltsolutions.in";            
	$mail_sent = mail($to, $strSubject, $message, $headers);
	//$mail_sent = true;
	if($mail_sent){
		echo 1;
	}else{
		echo 0;
	}*/
	
}else{
	header("Location:".SITEURL);	
}
?>
