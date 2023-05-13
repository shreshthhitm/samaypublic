<?php
include_once 'init.php';
include_once ROOT_DIR_COMMON.'functions.php';
//include_once 'functions.php';
//session_start();

/*class Controller
{
    function __construct() {
        $this->processMobileVerification();
    }
    function processMobileVerification()
    {*/
	        switch ($_POST["otp_action"]) {
			case "send_otp":
				if($_POST['otp_type'] == 'sms'){
					$mobile_number = $_POST['otp_val'];
					//$service = get_value_from_id("category","category_type","id",$service_id);
					//$service = get_value_from_id('category', 'category_type', 'id', '1');
					/*$numbers = array(
						$mobile_number
					);*/
					/*if(is_phone_exists($mobile_number)){
						echo json_encode(array("type"=>"error", "message"=>'Mobile Number already exists.'));
					}else{*/
						$numbers = $mobile_number;
						$otp = rand(100000, 999999);
						//$otp = 123456;
						$_SESSION['session_otp'] = $otp;
						$_SESSION['verify_otp'] = 0;
						$message = "Your OTP to update mobile no. at CBIC Samay Portal is " . $otp.". -CBIC";
						//echo "done";
						if($_POST['otp_self'] == 'yes'){
							$otp_receiver_no = $_POST['otp_val'];
						}else{
							if($_SESSION['sess_userrole'] == 'admin'){
								$otp_receiver_no = get_value_from_id("settings","option_value","option_name","admin_phone");
							}else{
								$otp_receiver_no = get_value_from_id("commissioners","contact_no","user_id",$_SESSION['sess_user_id']);
							}
						}
						//$otp_receiver_no = get_value_from_id("commissioners","contact_no","user_id",$_SESSION['sess_user_id']);
						$sms_msg = (ENV == 'PROD' ? $message : $otp);
						try{
							$send = sendSMS($otp_receiver_no, $template_id['otp_msg'], $sms_msg);
							//echo $_SESSION['session_otp'];
							//echo json_encode(array("service"=>$service));
							$json = array("type"=>"success", "receiver"=>$otp_receiver_no, "message"=>"OTP Sent!");
							echo json_encode($json);
						}catch(Exception $e){
							die('Error: '.$e->getMessage());
							//echo json_encode(array("type"=>"error", "message"=>$e->getMessage()));
						}
						//die();
					//}
				}else{
					$email_id = $_POST['otp_val'];
					//$service = get_value_from_id("category","category_type","id",$service_id);
					//$service = get_value_from_id('category', 'category_type', 'id', '1');
					/*$numbers = array(
						$mobile_number
					);*/
					if(is_email_exists($email_id)){
						echo json_encode(array("type"=>"error", "message"=>'Email Id already exists.'));
					}else{
						//$numbers = $mobile_number;
						$otp_email = rand(100000, 999999);
						//$otp = 123456;
						$_SESSION['session_otp_email'] = $otp_email;
						$_SESSION['verify_otp_email'] = 0;
						//echo "done";
						try{
							$to = "$email_id"; //change to ur mail address
							$strSubject = "CBIC | Email Verification OTP";
							$message = "Your OTP to register at CBIC Samay Portal is " . $otp_email;
							$headers = 'MIME-Version: 1.0'."\r\n";
							$headers .= 'Content-type: text/html; charset=iso-8859-1'."\r\n";
							$headers .= "From: shreshth.hitm@gmail.com";            
							//$mail_sent = mail($to, $strSubject, $message, $headers);
							//echo $_SESSION['session_otp'];
							//echo json_encode(array("service"=>$service));
							//echo json_encode(array("type"=>"success", "message"=>"OTP Sent!"));
							echo json_encode(array("type"=>"success", "message"=>$message));
						}catch(Exception $e){
							die('Error: '.$e->getMessage());
							//echo json_encode(array("type"=>"error", "message"=>$e->getMessage()));
						}
					}
				}
				break;
				
			case "verify_otp":
				$otp = $_POST['otp'];
				//echo $_SESSION['session_otp'];
				if($_POST['otp_type'] == 'sms'){
					if ($otp == $_SESSION['session_otp']) {
						unset($_SESSION['session_otp']);
						$_SESSION['verify_otp'] = 1;
						echo json_encode(array("type"=>"success", "message"=>"Your mobile number is verified!"));
					} else {
						$_SESSION['verify_otp'] = 0;
						echo json_encode(array("type"=>"error", "message"=>"Mobile number verification failed"));
					}
				}else{
					if ($otp == $_SESSION['session_otp_email']) {
						unset($_SESSION['session_otp_email']);
						$_SESSION['verify_otp_email'] = 1;
						echo json_encode(array("type"=>"success", "message"=>"Your email-id is verified!"));
					} else {
						$_SESSION['verify_otp_email'] = 0;
						echo json_encode(array("type"=>"error", "message"=>"Email verification failed"));
					}
				}
				break;
		}
    /*}
}
$controller = new Controller();*/
?>