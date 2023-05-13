<?php
$host = DB_HOST;
$dbname = DB_NAME;
$username  = DB_USER;
$passwd = DB_PASS;


try {
	$myPDO = new PDO("mysql:host=$host;dbname=$dbname", $username, $passwd);
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo 'Could not connect to the database'.$e->getMessage();
}

if(!$myPDO){
	echo "Could not connect";
}
date_default_timezone_set('Asia/Calcutta');

	$company_name = "CBIC SAMAY";
	//$company_address = nl2br(htmlentities("1, Gwalior Road, Mehar Complex,\nBaluganj, Agra – 282001", ENT_QUOTES, 'UTF-8'));
	//$company_address = "1, Gwalior Road, Mehar Complex,\nBaluganj, Agra – 282001";
	$company_address = "";
	/*$contact_person = "Manjeet Chahar";
	$company_phone = "8077363006";*/
	$contact_person = get_value_from_id('settings', 'option_value', 'option_name', 'admin_name');
	$company_phone = get_value_from_id('settings', 'option_value', 'option_name', 'admin_phone');
	$company_gstin = "";
	//$site_email = "info@crossboltsolutions.in";
	$site_email = (ENV == 'PROD' ? "samay-cbic@icegate.gov.in" : "shreshth@varito.in");
	$site_name = "CBIC SAMAY";
	$basename = basename($_SERVER["SCRIPT_FILENAME"]);
	
	//Put your sms provider api
	$msgUrl="http://login.snabdigitals.com/api/sendhttp.php";
	//Your authentication key
	$msgAuthKey = "16783AMWNXO30N5f22a2e4P15";
	//Sender ID,While using route4 sender id should be 6 characters long.
	$msgSenderId = "TAXSPO";
	//Define route 
	$msgRoute = "4";
	//Define Country Code
	$msgCountry = "91";
	
	$fast2sms_url = (ENV == 'PROD' ? 'http://sms.cbec.gov.in:8080/sms/send' : 'https://www.fast2sms.com/dev/bulkV2');
	$fast2sms_auth = 'Pm2uLQB7IGadlWKpMcNvUeVyYFEw9O1bJzRoACkSh48qs50n63XOJ0nMFGZbWjsEt4xyqrgm79kNCiSV';
	$fast2sms_sender = 'GSAMAY';
	
	$userrole = sanitize($_SESSION['sess_userrole'], 'string');
	$user_id = sanitize($_SESSION['sess_user_id'], 'int');
	$formation_id = ($userrole == 'admin' ? '' : sanitize($_SESSION['sess_fid'], 'int'));
	
	if(ENV == 'PROD'){
		$template_id = array(
					"linked"=>"1107166929106458714",
					"delinked"=>"1107166929120703952",
					"otp_msg"=>"1107166929054330520",
					"rem20days"=>"1107166929206482010",
					"remcivil"=>"1107166929196662121",
					"remslp"=>"1107166929180735311",
					"remfinal"=>"1107168025490426632",
					"ct_request"=>"1107168025495431462",
					"ct_receive"=>"1107168025501932483",
					"ct_success"=>"1107168025506759986",
					"ct_decline_sender"=>"1107168025511692045",
					"ct_decline_receiver"=>"1107168025517198715",
					"ct_rem"=>"1107168025569041865",
				);
	}else{
		$template_id = array(
					"linked"=>"140519",
					"delinked"=>"140520",
					"otp_msg"=>"141194",
					"rem20days"=>"140518",
					"rem35days"=>"140518",
					"rem90days"=>"140518",
				);
	}
				
	$sms_mob = array(
				"linked"=>"Welcome - You have been linked to SAMAY. -CBIC",
				"delinked"=>"Deactivated- You Have been delinked from SAMAY. -CBIC",
				"receive"=>"Charge transfer request has been received on SAMAY. -CBIC",
				"decline_receiver"=>"Charge transfer request on SAMAY has been declined. -CBIC",
				"charge_reminder"=>"Charge transfer request is pending on SAMAY. -CBIC",
			);
				
	$errors = array(
				1=>"Invalid user name or password, Try again!",
				2=>"Please login to access this area!",
				3=>"Your account is deactivated. Please contact administrator!",
				4=>"You are not authorized to access this area!",
				5=>"Either your password reset link is invalid or expired. Please click on forgot password again to generate new password reset link!",
				6=>"You have been logged out, please login again to access.",
			);
			
	$transfer_noti = array(
				1=>"You just sent a charge transfer request, that's why you have been logged out, please login again to access.",
				2=>"You just accepted the charge transfer request, that's why you have been logged out, please login again to access.",
			);
				
	$strSubject_link = "CBIC SAMAY | Welcome";
	$strSubject_delink = "CBIC SAMAY | Deactivation";
	$strSubject_pwd_upd = "CBIC SAMAY | Password Update";
	$strSubject_mobile_upd = "CBIC SAMAY | Mobile Update";
	
	$mail_msg_linked = '<tr><th style="padding: 0;"><h3 style="background-color: #0073b1;padding: 20px 0;color: #fff;margin: 0;">Welcome | CBIC Samay</h3></th></tr>';
	$mail_msg_linked .= '<tr><td style="background-color: #edf0f3;padding: 15px;">Welcome - You have been linked to SAMAY Portal.</td></tr>';
	
	$mail_msg_delinked = '<tr><th style="padding: 0;"><h3 style="background-color: #0073b1;padding: 20px 0;color: #fff;margin: 0;">Deactivation | CBIC Samay</h3></th></tr>';
	$mail_msg_delinked .= '<tr><td style="background-color: #edf0f3;padding: 15px;">Deactivated - You have been delinked from SAMAY Portal.</td></tr>';
	
	$mail_msg_pwd_upd = '<tr><th style="padding: 0;"><h3 style="background-color: #0073b1;padding: 20px 0;color: #fff;margin: 0;">Password Update Confirmation | CBIC Samay</h3></th></tr>';
	$mail_msg_pwd_upd .= '<tr><td style="background-color: #edf0f3;padding: 15px;">Your Password has been updated successfully on SAMAY Portal.</td></tr>';
	
	$mail_msg_mobile_upd = '<tr><th style="padding: 0;"><h3 style="background-color: #0073b1;padding: 20px 0;color: #fff;margin: 0;">Mobile No. Update Confirmation | CBIC Samay</h3></th></tr>';
	$mail_msg_mobile_upd .= '<tr><td style="background-color: #edf0f3;padding: 15px;">Your mobile no. has been updated successfully with us.</td></tr>';
	
	$cgst_per = 9;
	$sgst_per = 9;
	
	$payu_mode = 0; //Set this 0=test, 1=live
	$merchant_key = 'rjQUPktU';
	//$merchant_key = '08MbB2JY';
	$merchant_salt = 'e5iIg1jwi8';
	//$merchant_salt = 'O5uLuhc1ok';
	$fees = '550';
	$evt_current_id = 1;
	
	$batch_limit = 25;
	
	$display_orders_limit = 20;
	
	$role_title = array("manager"=>"Comm./ADG","cc_user"=>"CC/DG");
	
	//$user_roles = array("manager"=>"Commissioner","cc_user"=>"Chief Commissioner","high"=>"High Court","tribunal"=>"Tribunal Court");
	//$user_roles = array("manager"=>"Pr. Comm./Comm./Pr. ADG/ADG","cc_user"=>"Pr. CC/CC/Pr. DG/DG","high"=>"High Court","tribunal"=>"Tribunal Court");
	$user_roles_wo_board = array("cc_user"=>"CC/DG","manager"=>"Comm./ADG");
	$user_roles = array("cc_user"=>"CC/DG","manager"=>"Comm./ADG","board"=>"Board");
	$get_editable_role = array("admin"=>"cc_user,manager,board","cc_user"=>"cc_user,manager","manager"=>"manager");
	//$courts = array("High Court","Tribunal");
	//$courts_assoc = array("high"=>"High Court","tribunal"=>"Tribunal");
	$courts = array("high"=>"High Court","tribunal"=>"Tribunal");
	//$comm_order_status = array("1"=>"Decision not taken yet","2"=>"Order Accepted","3"=>"Order referred to CC/DG Office");
	$comm_order_status = array("1"=>"Pending at Commissioner","2"=>"Order Accepted","3"=>"Pending at Chief Commissioner");
	$cc_order_status = array("1"=>"Order Accepted","2"=>"Appeal/Petition to be filed in Supreme Court","3"=>"Appeal/Petition to be filed in High Court","4"=>"Application to be filed in Tribunal");
	//$comm_after_cc_order_status = array("1"=>"Under Process","2"=>"Forwarded to Board");	//Proposal Status
	$comm_after_cc_order_status = array("2"=>"Forwarded to Board");	//Proposal Status
	//$comm_after_cc_order_status_same_court = array("1"=>"Under Process","2"=>"Appeal Filed");	//Proposal Status
	$comm_after_cc_order_status_same_court = array("2"=>"Appeal Filed");	//Proposal Status
	$board_order_status = array("1"=>"Board decided not to file SLP or CA","2"=>"Appeal/Petition Filed");	//Board Status
	$comm_after_board_order_status = array("1"=>"Appeal Pending","2"=>"Appeal Decided");	//Appeal Status
	
	$pasc = array("0"=>"No","1"=>"Yes");	//Possibilities of Approaching Supreme Court
	
	$dashboard_status = array("Total Orders Registered","Orders Pending at ".$user_roles['cc_user'],"Appeal/Petition filed in High Court","Action Pending after ".$user_roles['cc_user']." Decision","Orders Accepted by ".$user_roles['manager']."/".$user_roles['cc_user']."/Board","Orders Pending with the ".$user_roles['manager'],"Proposal forwarded to Board for filing Appeal/Petition","Appeal/Petition Filed in Supreme Court","Appeal Decided","Application Filed in Tribunal","Pending Beyond 20 Days");
	
	/*$dashboard_query = array(
							"1",	//0
							"(OS.comm_status=3 AND OS.cc_status IS NULL)",	//1
							"(OS.cc_status=3 AND OS.proposal_date IS NOT NULL)",	//2
							"(OS.cc_status!=1 AND OS.proposal_status!=2)",	//3
							"(OS.comm_status=2 OR OS.cc_status=1 OR OS.board_status=1)",	//4
							"(OS.comm_status=1)",	//5
							"((OS.cc_status=1 OR OS.cc_status=2) AND OS.proposal_status=2 AND OS.board_status IS NULL)",	//6
							"(OS.board_status=2 AND appeal_status!=2)",	//7
							"(OS.appeal_status=2)",	//8
							"(OS.cc_status=4 AND proposal_status=2)"	//9
						);*/
	$dashboard_query = array(
							"1",	//0
							"(OS.comm_status=3 AND OS.cc_status IS NULL)",	//1
							"(OS.cc_status=3 AND OS.proposal_date IS NOT NULL)",	//2
							"(OS.cc_status!=1 AND OS.proposal_status!=2)",	//3
							"(OS.comm_status=2 OR OS.cc_status=1 OR OS.board_status=1)",	//4
							"(OS.comm_status=1)",	//5
							"((OS.cc_status=1 OR OS.cc_status=2) AND OS.proposal_status=2 AND OS.board_status IS NULL)",	//6
							"(OS.board_status=2 AND appeal_status!=2)",	//7
							"(OS.appeal_status=2)",	//8
							"(OS.cc_status=4 AND proposal_status=2)"	//9
						);
						
	$status_bg = array(0=>'info',1=>'success',2=>'danger');
	$status_label = array(0=>'Pending',1=>'Approved',2=>'Rejected');
	$status_charge_bg = array(0=>'danger',1=>'success');
	$status_charge_label = array(0=>'Pending',1=>'Approved');
	$transfer_reason = array('due_to_leave'=>'Due To Leave','other_than_leave'=>'Other Than Leave');
	
	$current_url = BASEURL.$_SERVER['PHP_SELF'].($_SERVER['QUERY_STRING'] != '' ? '?'.$_SERVER['QUERY_STRING'] : '');
	
	function addToUrl($url, $key, $value = null) {
        $query = parse_url($url, PHP_URL_QUERY);
        if ($query) {
            parse_str($query, $queryParams);
            $queryParams[$key] = $value;
            $url = str_replace("?$query", '?' . http_build_query($queryParams), $url);
        } else {
            $url .= '?' . urlencode($key) . '=' . urlencode($value);
        }
        return htmlspecialchars($url);
    }
	
	$filters = [
		'string' => FILTER_SANITIZE_STRING,
		'string[]' => [
			'filter' => FILTER_SANITIZE_STRING,
			'flags' => FILTER_REQUIRE_ARRAY
		],
		'email' => FILTER_SANITIZE_EMAIL,
		'int' => [
			'filter' => FILTER_SANITIZE_NUMBER_INT,
			'flags' => FILTER_REQUIRE_SCALAR
		],
		'int[]' => [
			'filter' => FILTER_SANITIZE_NUMBER_INT,
			'flags' => FILTER_REQUIRE_ARRAY
		],
		'float' => [
			'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
			'flags' => FILTER_FLAG_ALLOW_FRACTION
		],
		'float[]' => [
			'filter' => FILTER_SANITIZE_NUMBER_FLOAT,
			'flags' => FILTER_REQUIRE_ARRAY
		],
		'url' => FILTER_SANITIZE_URL,
	];
	
	function array_trim(array $items){
		return array_map(function ($item) {
			if (is_string($item)) {
				return trim($item);
			} elseif (is_array($item)) {
				return array_trim($item);
			} else
				return $item;
		}, $items);
	}
	
	//For PHP>=7.4
	/*function sanitize($inputs, $fields=NULL, $default_filter = FILTER_SANITIZE_STRING, $trim = true){
		global $filters;
		$input_arr = array('myfield'=>$inputs);
		$field_arr = array('myfield'=>$fields);
		if (!empty($fields)) {
			$options = array_map(fn($field) => $filters[$field], $field_arr);
			$data = filter_var_array($input_arr, $options);
		} else {
			$data = filter_var_array($input_arr, $default_filter);
		}
		$return = $trim ? array_trim($data) : $data;
		return $return['myfield'];
	}
	
	function sanitize_data(array $inputs, array $fields = [], int $default_filter = FILTER_SANITIZE_STRING, array $filters = FILTERS, bool $trim = true){
		if ($fields) {
			$options = array_map(fn($field) => $filters[$field], $fields);
			$data = filter_var_array($inputs, $options);
		} else {
			$data = filter_var_array($inputs, $default_filter);
		}

		return $trim ? array_trim($data) : $data;
	}*/
	
	function sanitize1($inputs, $fields=NULL, $default_filter = FILTER_SANITIZE_STRING, $trim = true){
		global $filters;
		if (!empty($fields)) {
			$data = filter_var($inputs, $filters[$fields]);
		} else {
			$data = filter_var($inputs, $default_filter);
		}
	    //$return = $trim ? array_trim($data) : $data;
	    $return = $data;
		return $data;
	}
	$map_fields = function($field){
	    global $filters;
	    return $filters[$field];
	};
	function sanitize($inputs, $fields=NULL, $default_filter = FILTER_SANITIZE_STRING, $trim = true){
		global $filters, $map_fields;
		$input_arr = array('myfield'=>$inputs);
		$field_arr = array('myfield'=>$fields);
		if (!empty($fields)) {
			$options = array_map($map_fields, $field_arr);
			$data = filter_var_array($input_arr, $options);
		} else {
			$data = filter_var_array($input_arr, $default_filter);
		}
		$return = $trim ? array_trim($data) : $data;
	    return $return['myfield'];
	}
	
	function is_phone_exists($phone){
		global $myPDO;
		$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt_edit1 = $myPDO->query("SELECT contact_no FROM commissioners WHERE contact_no='".$phone."'");
		//$stmt_edit2 = $myPDO->query("SELECT mobile FROM clients WHERE mobile=".$phone);
		//$stmt_edit3 = $myPDO->query("SELECT reg_contact_no FROM registrations WHERE reg_contact_no=".$phone);
		//if($stmt_edit1->rowCount() > 0 || $stmt_edit2->rowCount() > 0 || $stmt_edit3->rowCount() > 0) {
		if($stmt_edit1->rowCount() > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	function is_email_exists($email){
		global $myPDO;
		$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt_edit1 = $myPDO->query("SELECT email FROM users WHERE email='".$email."'");
		$stmt_edit2 = $myPDO->query("SELECT reg_email FROM registrations WHERE reg_email='".$email."'");
		if($stmt_edit1->rowCount() > 0 || $stmt_edit2->rowCount() > 0) {
			return true;
		} else {
			return false;
		}
	}
	function sendSMS1($recipient_no, $message){
		global $msgUrl, $msgAuthKey, $msgSenderId, $msgRoute, $msgCountry;
		$response = $msgUrl.'?authkey='.$msgAuthKey.'&mobiles='.$recipient_no.'&message='.str_replace(" ", "+", $message).'&sender='.$msgSenderId.'&route='.$msgRoute.'&country='.$msgCountry.'&response=json';
        $response_output = file_get_contents($response);
		return $response_output;
	}
	
	function sendSMS($recipient_no, $temp_id, $txt_msg=NULL){
		global $fast2sms_url;
		if(ENV == 'PROD'){
			$apiurl = $fast2sms_url.'?mob='.$recipient_no.'&txtmsg='.urlencode($txt_msg).'&tmpid='.$temp_id;
		}else{
			global $fast2sms_auth, $fast2sms_sender;
			$apiurl = $fast2sms_url.'?authorization='.$fast2sms_auth.'&sender_id='.$fast2sms_sender.'&message='.$temp_id.'&variables_values='.urlencode($txt_msg).'&route=dlt&numbers='.$recipient_no;
		}
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $apiurl,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_SSL_VERIFYHOST => 0,
		  CURLOPT_SSL_VERIFYPEER => 0,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
		  return "cURL Error #:" . $err;
		} else {
		  return $response['return'];
		}
	}
	
	function auth_user($user,$pass){
		$return = false;
		if(ENV == 'PROD'){
			//$apiurl = 'http://10.1.109.97:8090/secApp/hello?ssoid=80007002&pass=123&hash=456';
			//$apiurl = 'http://10.1.109.97:8090/secApp/oud?ssoid=67676767&pass=12345';
			$apiurl = 'http://10.1.109.97:8090/secApp/oud?uid='.$user.'&password='.$pass;
			$curl = curl_init();
			curl_setopt_array($curl, array(
			  CURLOPT_URL => $apiurl,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_SSL_VERIFYHOST => 0,
			  CURLOPT_SSL_VERIFYPEER => 0,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  CURLOPT_HTTPHEADER => array(
				"cache-control: no-cache"
			  ),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			/*if ($err) {
			  return "cURL Error #:" . $err;
			} else {
			  return $response['return'];
			}*/
				//$response_output = file_get_contents($response);
			//return $response;
			if($response == md5('success')){
				$return = true;
			}
		}else{
			global $myPDO;
			$hash = get_value_from_id("users","password","username",$user);
			if(password_verify($pass, $hash)){
				$return = true;
			}
		}
		return $return;
	}
	
	function send_mail_function1($to, $subject, $htmlContent, $senderEmail, $senderName, $files = array()){
		$from = $senderName." <".$senderEmail.">";  
		$headers = "From: $from"; 
		
		// Boundary  
		$semi_rand = md5(time());  
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";  
	 
		// Headers for attachment  
		$headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";  
		
		$message ="<html>" ;
		$message .="<head>";
		$message .="</head>";
		$message .="<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0'>";
		$message .="<table width='100%' border='0' cellspacing='0' cellpadding='0' style='font-size:16px;background-color: #fff;'>";
		$message .="  <tr>";
		$message .="    <td align='center'>";
		$message .="      <table width='70%' border='0' cellspacing='0' cellpadding='5'>";
		$message .=			$htmlContent;
		$message .="      </table>";
		$message .="    </td>";
		$message .="  </tr>";
		$message .="</table>";
		$message .="</body></html>";
		
		// Multipart boundary  
		$message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"UTF-8\"\n" . 
		"Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
		
		//if(!empty(array_filter($_FILES['image']['name']))){
		if(!empty($files)){
			$file_count = count($files);
			foreach($files as $key=>$val){
				// File upload path
				$file_name = $_FILES['form_attachment']['name'][$key];
				$tmp = $_FILES['form_attachment']['tmp_name'][$key];
				//get file info
				$file_size = $_FILES['form_attachment']['size'][$key];
				$file_type = $_FILES['form_attachment']['type'][$key];
				
				//read file
				$fp =    fopen($tmp, "r");
				$data =  fread($fp, $file_size); 
				fclose($fp);
				$data = chunk_split(base64_encode($data));
				
				$message .= "--{$mime_boundary}\n";
				$message .= "Content-Type: $file_type; name=\"".$file_name."\"\n" .  
					"Content-Description: ".$file_name."\n" . 
					"Content-Disposition: attachment;\n" . " filename=\"".$file_name."\"; size=".$file_size.";\n" .  
					"Content-Transfer-Encoding: base64\n\n" . $data . "\n\n"; 
			}
		}
		
		$message .= "--{$mime_boundary}--"; 
		$returnpath = "-f" . $senderEmail;
		
		// Send email 
		$mail = @mail($to, $subject, $message, $headers, $returnpath);  
		
		if($mail){ 
			return true; 
		}else{ 
			return false; 
		} 
	}
	//Import the PHPMailer class into the global namespace
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\SMTP;

	function send_mail_function($to, $subject, $htmlContent, $senderEmail, $senderName, $files = array()){
		/**
		 * This example shows making an SMTP connection with authentication.
		 */

		//SMTP needs accurate times, and the PHP time zone MUST be set
		//This should be done in your php.ini, but this is how to do it if you don't have access to that
		//date_default_timezone_set('Etc/UTC');

		require ROOT_DIR_COMMON.'phpmailer_vendor/autoload.php';

		//Create a new PHPMailer instance
		$mail = new PHPMailer();
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();
		//Enable SMTP debugging
		//SMTP::DEBUG_OFF = off (for production use)
		//SMTP::DEBUG_CLIENT = client messages
		//SMTP::DEBUG_SERVER = client and server messages
		$mail->SMTPDebug = SMTP::DEBUG_OFF;
		//$mail->SMTPDebug = 0;
		//Set the hostname of the mail server
		//$mail->Host = 'smtp.gmail.com';
		$mail->Host = (ENV == 'PROD' ? 'smtp.icegate.gov.in' : 'smtp.hostinger.com');
		//Set the SMTP port number - likely to be 25, 465 or 587
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$mail->Port = (ENV == 'PROD' ? 25 : 465);
		if(ENV != 'PROD'){
			//Whether to use SMTP authentication
			$mail->SMTPAuth = true;
		}
		//Username to use for SMTP authentication
		$mail->Username = (ENV == 'PROD' ? '' : 'shreshth@varito.in');
		//Password to use for SMTP authentication
		$mail->Password = (ENV == 'PROD' ? '' : 'Shreshth@2023');
		//Set who the message is to be sent from
		//$mail->setFrom('shreshth.hitm@gmail.com', 'First Last');
		$mail->setFrom($senderEmail, $senderName);
		if(ENV != 'PROD'){
			//Set an alternative reply-to address
			//$mail->addReplyTo('shreshth.hitm@gmail.com', 'First Last');
			$mail->addReplyTo($senderEmail, $senderName);
		}
		//Set who the message is to be sent to
		//$mail->addAddress('info.crossboltitsolutions@gmail.com', 'John Doe');
		$mail->addAddress($to);		//Add NAME as second paramter
		//Set the subject line
		$mail->Subject = $subject;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		///$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
		//Simple mail
		$mail->isHTML(true);                       // Set email format to HTML
		$message ="<html>" ;
		$message .="<head>";
		$message .="</head>";
		$message .="<body topmargin='0' leftmargin='0' rightmargin='0' bottommargin='0'>";
		$message .="<table width='100%' border='0' cellspacing='0' cellpadding='0' style='font-size:16px;background-color: #fff;'>";
		$message .="  <tr>";
		$message .="    <td align='center'>";
		$message .="      <table width='70%' border='0' cellspacing='0' cellpadding='5'>";
		$message .=			$htmlContent;
		$message .="      </table>";
		$message .="    </td>";
		$message .="  </tr>";
		$message .="  <tr style='text-align: center; font-size: 11px;'>This is a system generated mail. Please do not reply to this email ID.<br/>(1) Call our 24-hour Customer Care at 1800*** (2) Email Us at <a href=#>****</a></tr>";
		$message .="</table>";
		$message .="</body></html>";
		$mail->Body    = $message;
		//$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
		//Replace the plain text body with one created manually
		//$mail->AltBody = 'This is a plain-text message body';
		//Attach an image file
		//$mail->addAttachment('phpmailer.png');

		//send the message, check for errors
		if (!$mail->send()) {
			//$mail->ErrorInfo;
			return false;
		} else {
			return true;
		}
	}
	
	function addQueryString($a){
		if (empty($_SERVER['QUERY_STRING'])){
			return '?' . $a;
		}else if (!empty($_SERVER['QUERY_STRING'])){
			return '?' . $_SERVER['QUERY_STRING'] . '&' . $a;
		}
	}
	function report_type_populate($given_type=NULL){
		$return = '<option value="">-Select-</option>';
		$type = array('Monthly', 'Quarterly', 'Annually');
		foreach ($type as $key => $value) {
			//$month_prev = date('m', strtotime("-1 months"));
			$selected = ($given_type == $value ? 'selected' : '');
			$return .= '<option '.$selected.' value="'.$value.'">'.$value.'</option>'."\n";
		}
		return $return;
	}
	function months_populate($given_month){
		$return = '<option value="">-Select-</option>';
		for ($i = 1; $i <= 12; $i++) {
			//$month_prev = date('m', strtotime("-1 months"));
			$month_prev = $given_month;
			$month = strtotime(date('Y').'-'.$i.'-01');
			$selected = (($month_prev == date('m', $month)) ? 'selected' : '');
			$return .= '<option '.$selected.' value="'.date('m', $month).'">'.date('F', $month).'</option>'."\n";
		}
		return $return;
	}
	
	function year_populate($given_year){
		$return = '<option value="">-Select-</option>';
		for ($i = 1; $i <= 10; $i++) {
			//$year_prev = date('Y', strtotime("-1 months"));
			$year_prev = $given_year;
			$month = strtotime((date('Y') - ($i - 1)).'-01-01');
			$selected = (($year_prev == date('Y', $month)) ? 'selected' : '');
			$return .= '<option '.$selected.' value="'.date('Y', $month).'">'.date('Y', $month).'</option>'."\n";
		}
		return $return;
	}
/*function quarter($quarter){
	
}*/
function required_year($r_type, $fy_year, $period=NULL){
	$cur_month = date('n');
	/*if($month_num >= 4){
		$yr = explode("-",$fy_year)[0];
	}else{
		$yr = explode("-",$fy_year)[0] + 1;
	}*/
	$cur_fy = (date('n') >= 4 ? date('Y') : (date('Y') - 1));
	$yr1 = explode("-",$fy_year)[0];
	$yr2 = ($cur_fy == $yr1) ? ((date('n') >= 4) ? $yr1 : $yr1 + 1) : $yr1 + 1;
	if($r_type == "Annually"){
		$mon2 = ($cur_fy == $yr1) ? date('m') : '03' ;
		$date2 = ($cur_fy == $yr1) ? date('d') : '31' ;
		//$req_year1 = (date('n') >= 4 ? date('Y') : date('Y') + 1);
		//$req_year2 = (date('n') >= 4 ? date('Y') : date('Y') + 1);
		$start_date = $yr1.'-04-01';
		//$end_date = '2021-'.date('m').'-'.date('n');
		$end_date = $yr2.'-'.$mon2.'-'.$date2;
	}else {
		if($r_type == "Monthly"){
			//$month = $period;
			$month = ltrim($period, "0");
			$req_yr = $month >= 4 ? $yr1 : $yr1 + 1;
			$month_days = date('t', strtotime(date($req_yr.'-'.$period.'-01')));
			$start_date = $req_yr.'-'.$period.'-01';
			$end_date = $req_yr.'-'.$period.'-'.$month_days;
			//$req_year1 = (date('n') >= 4 ? date('Y') : date('Y') + 1);
			//$req_year2 = (date('n') >= 4 ? date('Y') : date('Y') + 1);
		}else if($r_type == "Quarterly"){
			$month = explode("-",$period);
			$mon_f = date('n', strtotime($month[0]));
			$mon1 = date('m', strtotime($month[0]));
			$mon2 = date('m', strtotime($month[1]));
			//$req_year1 = (date('n') >= 4 ? date('Y') : date('Y') + 1);
			//$req_year2 = (date('n') >= 4 ? date('Y') : date('Y') + 1);
			$req_yr = $mon_f >= 4 ? $yr1 : $yr1 + 1;
			$month_days = date('t', strtotime(date($req_yr.'-'.$mon2.'-01')));
			$yr_f = $mon_f >= 4 ? $yr1 : $yr1 + 1;
			$start_date = $yr_f.'-'.$mon1.'-01';
			$end_date = $yr_f.'-'.$mon2.'-'.$month_days;
		}
	}
	return array($start_date, $end_date);
}
function fy_year_populate($fy_year=NULL){
	//$return = date('n'); For Month without leading zero
	$curr_month = date('n');
	if($curr_month >= 4){
		$curr_year = date('Y');
	}else{
		$curr_year = date('Y') - 1;
	}
	$return = '<option value="">Select</option>';
	for($i = $curr_year; $i >= 2020; $i--){
		$yr_str = $i.'-'.substr(($i+1),-2);
		$selected = ($fy_year == $yr_str ? 'selected' : '');
		$return .= '<option value="'.$yr_str.'" '.$selected.'>'.$yr_str.'</option>';
	}
	return $return;
}
function quarter_populate($quarter=NULL){
	$selected = ($fy_year == $yr_str ? 'selected' : '');
	$quarter_arr = array("Apr-Jun", "Jul-Sep", "Oct-Dec", "Jan-Mar");
	$return = '<option value="">Select Quarter</option>';
	foreach($quarter_arr as $key => $value){
		$selected = ($quarter == $value ? 'selected' : '');
		$return .= '<option value="'.$value.'" '.$selected.'>'.$value.'</option>';
	}
	return $return;
}
/*function months_populate(){
	$return = '<option value="">Select Month</option>
				<option value="Jan">Jan</option>
				<option value="Feb">Feb</option>
				<option value="Mar">Mar</option>
				<option value="Apr">Apr</option>
				<option value="May">May</option>
				<option value="Jun">Jun</option>
				<option value="Jul">Jul</option>
				<option value="Aug">Aug</option>
				<option value="Sep">Sep</option>
				<option value="Oct">Oct</option>
				<option value="Nov">Nov</option>
				<option value="Dec">Dec</option>';
	return $return;
}*/
function generate_gst_period($mode, $period, $fy_year){
	//$period = 'Feb';
	//echo date('F', strtotime($month));
	if($mode == 'Monthly'){
		$month = $period;	//$row_edit1['gst_period']
	}else{
		$month = explode("-",$period)[0];
	}
	$month_num = date('n', strtotime($month));
	if($month_num >= 4){
		$yr = explode("-",$fy_year)[0];
	}else{
		$yr = explode("-",$fy_year)[0] + 1;
	}
	if($mode == 'Monthly'){
		$period = date('m', strtotime($month));
	}else{
		$period = $period;
	}
	$period .= "/".substr($yr, -2);
	return $period;
}

	function code($str,$str_alpha,$str_num){
		if($str == ''){ $str = 'STUD00000'; }
		$str1 = substr($str, 0, $str_alpha);
		$str2 = (int)substr($str, $str_alpha, strlen($str));
		$str2 = $str2 + 1;
		$str2 = str_pad($str2,$str_num,"0",STR_PAD_LEFT);
		return $str1.$str2;
	}
	function code_new($start,$str,$str_alpha,$str_num){
		if($str == ''){ $str = $start.'0000000'; }
		$str1 = substr($str, 0, $str_alpha);
		$str2 = (int)substr($str, $str_alpha, strlen($str));
		$str2 = $str2 + 1;
		$str2 = str_pad($str2,$str_num,"0",STR_PAD_LEFT);
		return $str1.$str2;
	}
	
	function Is_email($user){
		//If the username input string is an e-mail, return true
		if(filter_var($user, FILTER_VALIDATE_EMAIL)) {
			return true;
		} else {
			return false;
		}
	}
function get_value_from_id($table, $col, $con_col, $val, $separator=',') {
	global $myPDO;
	$return = '';
	if(strpos($val,",") > 0) {
		$where=$con_col." in (".$val.")";
	} else {
		if(is_numeric($val)){
			$where=$con_col."=".$val;
		}else{
			$where=$con_col."='".$val."'";
		}
	}
	$sql="select ".$col." from ".$table." where ".$where;
	//$sql="select type from unit where id=3";
	$stmt_edit = $myPDO->query($sql);
	//$result= mysql_query($sql);
	if($stmt_edit->rowCount() > 0) {
		while ($row_edit = $stmt_edit->fetch(PDO::FETCH_ASSOC)) {
			 $return .= strip_tags($row_edit[$col]) . $separator;
		}
		$return = substr($return, 0, strlen($return)-1);
		return $return;
	}
}
function financialYear($date){
	//(date('m')<'04') ? date('Y-04-01',strtotime('-1 year')) : date('Y-04-01');
	$date1 = date("m", strtotime($date));
	if($date1 < '04'){
		$startYear = date('Y-04-01',strtotime('-1 year'));
		$endYear = date('Y-04-01');
	}else{
		$startYear = date('Y-04-01');
		$endYear = date('Y-04-01',strtotime('+1 year'));
	}
	$date2 = date("y", strtotime($startYear))."-".date("y", strtotime($endYear));
	return $date2;
}

function state_populate() {
	global $myPDO;
	$sql_populate = "SELECT id, state_name FROM states WHERE is_active=1 ORDER BY state_name";
	//$sql_populate = mysql_query($sql_populate);
	$return = "<option value=''>--------Select State--------</option>";
	foreach($myPDO->query($sql_populate) as $row){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $row['id'] . "'>" . $row['state_name'] . "</option>";
	}
	return $return;
}

function job_populate() {
	global $myPDO;
	$sql_populate = "SELECT id, job_name FROM job_roles WHERE is_active=1 ORDER BY job_name";
	//$sql_populate = mysql_query($sql_populate);
	$return = "<option value=''>--------Select Job Role--------</option>";
	foreach($myPDO->query($sql_populate) as $row){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $row['id'] . "'>" . $row['job_name'] . "</option>";
	}
	return $return;
}

function centre_populate($centre_id=NULL) {
	global $myPDO;
	/*if($_SESSION['sess_userrole'] == 'manager'){
		$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}*/
	$sql_populate = "SELECT centres.user_id, centres.name FROM centres, users as u WHERE u.id=centres.user_id and u.is_active=1 ORDER BY name ASC";
	//$sql_populate = mysql_query($sql_populate);
	$return = "<option value=''>---Select Consultant Name---</option>";
	foreach($myPDO->query($sql_populate) as $row){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $row['user_id'] . "' ".(($centre_id == $row['user_id']) ? 'selected' : '').">" . $row['name'] . "</option>";
	}
	return $return;
}
function user_role_populate($centre_id=NULL) {
	global $myPDO, $user_roles;
	/*if($_SESSION['sess_userrole'] == 'manager'){
		$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}*/
	//$sql_populate = "SELECT commissioners.user_id, commissioners.name FROM commissioners, users as u WHERE u.id=commissioners.user_id and u.is_active=1 ORDER BY name ASC";
	//$sql_populate = mysql_query($sql_populate);
	/*$sql_populate = array("cc_user","manager","high","tribunal");
	$role_name = array("Chief Commissioner","Commissioner","High Court","Tribunal Court");*/
	
	$return = "<option value=''>---Select Role---</option>";
	foreach($user_roles as $key=>$value){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		//$return .= "<option value='" . $value . "' ".(($centre_id == $value) ? 'selected' : '').">" . $role_name[$key] . "</option>";
		$return .= "<option value='" . $key . "' ".(($centre_id == $key) ? 'selected' : '').">" . $value . "</option>";
	}
	return $return;
}
function user_role_populate_adv($centre_id=NULL) {
	global $myPDO, $user_roles, $get_editable_role;
	//if($_SESSION['sess_userrole'] != 'hqrs'){
		//$user_roles = array($get_editable_role[$_SESSION['sess_userrole']]);
		$centre_id = $get_editable_role[$_SESSION['sess_userrole']];
	//}
	/*if($_SESSION['sess_userrole'] == 'manager'){
		$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}*/
	//$sql_populate = "SELECT commissioners.user_id, commissioners.name FROM commissioners, users as u WHERE u.id=commissioners.user_id and u.is_active=1 ORDER BY name ASC";
	//$sql_populate = mysql_query($sql_populate);
	/*$sql_populate = array("cc_user","manager","high","tribunal");
	$role_name = array("Chief Commissioner","Commissioner","High Court","Tribunal Court");*/
	
	$return = "<option value=''>---Select Role---</option>";
	foreach($user_roles as $key=>$value){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		//$return .= "<option value='" . $value . "' ".(($centre_id == $value) ? 'selected' : '').">" . $role_name[$key] . "</option>";
		$return .= "<option value='" . $key . "' ".(($centre_id == $key) ? 'selected' : '').">" . $value . "</option>";
	}
	return $return;
}
function user_role_filtered_populate($centre_id=NULL) {
	global $myPDO, $user_roles, $user_roles_wo_board, $get_editable_role, $page_name;
	$get_editable_role_arr = explode(',',$get_editable_role[$_SESSION['sess_userrole']]);
	$user_roles_init = ($page_name == 'Transfer Charge' ? $user_roles_wo_board : $user_roles);
	foreach($user_roles as $key=>$value){
		if(!in_array($key, $get_editable_role_arr)){
			unset($user_roles_init[$key]);
		}
	}
	
	$return = "<option value=''>---Select Role---</option>";
	foreach($user_roles_init as $key=>$value){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		//$return .= "<option value='" . $value . "' ".(($centre_id == $value) ? 'selected' : '').">" . $role_name[$key] . "</option>";
		$return .= "<option value='" . $key . "' ".(($centre_id == $key) ? 'selected' : '').">" . $value . "</option>";
	}
	return $return;
}
function formation_populate($centre_id=NULL, $role=NULL, $parent_id=NULL) {
	global $myPDO, $user_roles;
	$sequence_arr = array();
	foreach($user_roles as $key => $val){
		$sequence_arr[] = $key;
	}
	$sequence = "'".implode("','", $sequence_arr)."'";
	$sql_populate = "SELECT * FROM formations WHERE is_active=1";
	$role_name = 'Formation';
	if($role != ''){
		$sql_populate .= " and role='".$role."'";
		$role_name = $user_roles[$role];
	}
	if($parent_id != ''){
		$sql_populate .= " and parent_id='".$parent_id."'";
	}
	if($_SESSION['sess_userrole'] == 'cc_user'){
		$sql_populate .= " and parent_id='".$_SESSION['sess_fid']."'";
	}
	$sql_populate .= " ORDER BY FIELD(role,".$sequence."), formation ASC";
	//$sql_populate = mysql_query($sql_populate);
	$return = "<option value=''>Select ".$role_name."</option>";
	foreach($myPDO->query($sql_populate) as $row){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $row['id'] . "' ".(($centre_id == $row['id']) ? 'selected' : '').">" . $row['formation'] . "</option>";
	}
	return $return;
}
function formation_transfer_populate($centre_id=NULL, $role=NULL, $parent_id=NULL, $exclude_self=NULL, $self_only=NULL, $hide_user_formations=NULL, $hide_unassigned=NULL, $map_user_id='no') {
	global $myPDO, $user_roles;
	$sequence_arr = array();
	foreach($user_roles as $key => $val){
		$sequence_arr[] = $key;
	}
	$sequence = "'".implode("','", $sequence_arr)."'";
	$sql_populate = "SELECT * FROM formations F WHERE is_active=1";
	$role_name = 'Formation';
	if($role != ''){
		$sql_populate .= " and F.role='".$role."'";
		$role_name = $user_roles[$role];
	}
	if($parent_id != ''){
		$sql_populate .= " and F.parent_id='".$parent_id."'";
	}
	if($exclude_self != ''){
		$sql_populate .= " and F.id!='".$exclude_self."'";
	}
	if($self_only != ''){
		$sql_populate .= " and F.id='".$self_only."'";
	}
	if($hide_user_formations != ''){
		$sql_populate1 = "SELECT user_id FROM charges_table WHERE formation_id=".$exclude_self." && charge_status=1";
		$stmt_user = $myPDO->query($sql_populate1);
		$row_user = $stmt_user->fetch(PDO::FETCH_ASSOC);
		$f_arr = array();
		$sql_populate2 = "SELECT formation_id FROM charges_table WHERE user_id=".$row_user['user_id']." && charge_status=1";
		$stmt_formation1 = $myPDO->query($sql_populate2);
		if($stmt_formation1->rowCount() > 0){
			foreach($stmt_formation1 as $rr){
				$f_arr[] = $rr['formation_id'];
			}
			$sql_populate .= " and F.id NOT IN('".implode("','", $f_arr)."')";
		}
	}
	if($hide_unassigned == 'yes'){
		$sql_populate1 = "SELECT F.id FROM charges_table CT INNER JOIN formations F ON F.id=CT.formation_id WHERE F.role='".$role."' && CT.charge_status=1";
		$stmt_user = $myPDO->query($sql_populate1);
		$f_arr1 = array();
		if($stmt_user->rowCount() > 0){
			foreach($stmt_user as $rr){
				$f_arr1[] = $rr['id'];
			}
			$sql_populate .= " and F.id IN('".implode("','", $f_arr1)."')";
		}
	}
	$sql_populate .= " ORDER BY FIELD(role,".$sequence."), formation ASC";
	//print_r($sql_populate); die();
	//$sql_populate = mysql_query($sql_populate);
	$return = "<option value=''>Select ".$role_name."</option>";
	foreach($myPDO->query($sql_populate) as $row){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		if($map_user_id == 'yes'){
			$final_id = get_user_by_formation($row['id'], 'yes');
		}else{
			$final_id = $row['id'];
		}
		$return .= "<option value='" . $final_id . "' ".(($centre_id == $final_id && $map_user_id != 'yes') ? 'selected' : '').">" . $row['formation'] . "</option>";
	}
	return $return;
}
function all_user_populate($centre_id=NULL, $role=NULL, $exclude=NULL, $user_display=NULL) {
	global $myPDO, $user_roles, $page_name;
	$sql_populate = "SELECT c.user_id, c.officer_name, u.username FROM commissioners c, users as u WHERE u.id=c.user_id and  u.is_active=1";
	if($role != ''){
		$sql_populate .= " and u.role='".$role."'";
		$role_name = ' '.$user_roles[$role];
	}
	if($exclude != ''){
		$sql_populate .= " and u.id!='".$exclude."'";
	}
	if($user_display != '' && $user_display == 'other_than_leave'){
		$sql_populate .= " and u.role='".$role."'";
	}
	
	$sql_populate .= " ORDER BY c.officer_name ASC";
	//print_r($sql_populate); die();
	//$sql_populate = mysql_query($sql_populate);
	$return = "<option value=''>Select".$role_name." Name</option>";
	foreach($myPDO->query($sql_populate) as $row){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		if($user_display != '' && $user_display == 'due_to_leave'){
			$sql_populate3 = "SELECT formation_id FROM charges_table WHERE user_id=".$row['user_id']." && charge_status=1";	//The User has its charge to another
			$stmt_formation = $myPDO->query($sql_populate3);
			$row_formation = $stmt_formation->fetch(PDO::FETCH_ASSOC);
			$val = get_value_from_id("formations","formation","id",$row_formation['id']);

		}else{
			$val = $row['officer_name'].($row['username'] != '' ? ' ('.$row['username'].')' : '');
		}
		$return .= "<option value='" . $row['user_id'] . "' ".(($centre_id == $row['user_id']) ? 'selected' : '').">" . $val . "</option>";
	}
	return $return;
}
/*function commissioner_populate($centre_id=NULL, $role=NULL, $parent_id=NULL, $exclude_self=NULL, $self_only=NULL, $user_display=NULL) {
	global $myPDO, $user_roles, $page_name;
	if(($page_name == 'User' && $role == 'cc_user' && $_GET['edit'] != '') || ($page_name == 'Get Comm WRT Role' && $role == 'cc_user')){
		$centre_id = get_value_from_id("commissioners","parent_id","user_id",$centre_id);
	}
	$sql_populate = "SELECT commissioners.user_id, commissioners.name, commissioners.officer_name, u.username FROM commissioners, users as u WHERE u.id=commissioners.user_id and  u.is_active=1";
	$role_name = $user_roles['manager'];
	if($role != ''){
		$sql_populate .= " and u.role='".$role."'";
		$role_name = $user_roles[$role];
	}
	if($parent_id != ''){
		$sql_populate .= " and commissioners.parent_id='".$parent_id."'";
	}
	if($exclude_self != ''){
		//$sql_populate .= " and u.id!='".$exclude_self."'";
	}
	if($self_only != ''){
		$sql_populate .= " and u.id='".$self_only."'";
	}
	$sql_populate .= " ORDER BY ".($user_display == '' ? 'name' : 'username')." ASC";
	//$sql_populate = mysql_query($sql_populate);
	$return = "<option value=''>Select ".$role_name." Name</option>";
	foreach($myPDO->query($sql_populate) as $row){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $row['user_id'] . "' ".(($centre_id == $row['user_id']) ? 'selected' : '').">" . ($user_display == '' ? $row['name'] : $row['username'].($row['officer_name'] != '' ? ' ('.$row['officer_name'].')' : '')) . "</option>";
	}
	return $return;
}
function commissioner_to_transfer_populate($centre_id=NULL, $role=NULL, $exclude_self=NULL, $self_only=NULL, $user_display=NULL) {
	global $myPDO, $user_roles, $page_name;
	if($page_name == 'User' && $role == 'cc_user' && $_GET['edit'] != ''){
		$centre_id = get_value_from_id("commissioners","parent_id","user_id",$centre_id);
	}
	$sql_populate = "SELECT commissioners.user_id, commissioners.name, commissioners.officer_name, u.username FROM commissioners, users as u WHERE u.id=commissioners.user_id and u.is_active=1";
	$role_name = $user_roles['manager'];
	if($role != ''){
		$sql_populate .= " and u.role='".$role."'";
		$role_name = $user_roles[$role];
	}
	if($exclude_self != ''){
		$sql_populate .= " and u.id!='".$exclude_self."'";
	}
	$sql_populate .= " ORDER BY ".($user_display == '' ? 'name' : 'username')." ASC";
	//$sql_populate = mysql_query($sql_populate);
	$return = "<option value=''>Select ".$role_name." Name</option>";
	foreach($myPDO->query($sql_populate) as $row){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $row['user_id'] . "' ".(($centre_id == $row['user_id']) ? 'selected' : '').">" . ($user_display == '' ? $row['name'] : $row['username'].($row['officer_name'] != '' ? ' ('.$row['officer_name'].')' : '')) . "</option>";
	}
	return $return;
}*/
function transfer_reason_populate($centre_id=NULL) {
	global $myPDO, $transfer_reason;
	$return = "<option value=''>---Select Reason---</option>";
	foreach($transfer_reason as $key=>$val){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $key . "' ".(($centre_id == $key) ? 'selected' : '').">" . $val . "</option>";
	}
	return $return;
}
/*function send_transfer_request($user_id, $charge_id, $charge_status, $created_at, $updated_at){
	global $myPDO, $template_id, $sms_mob;
	if($user_id == $charge_id){
		//$myPDO->query("UPDATE charges_table SET charge_status=1, updated_at='".$updated_at."' WHERE user_id=".$charge_id." && charge_id=".$charge_id);
		$myPDO->query("DELETE FROM charges_table WHERE user_id!=".$charge_id." && charge_id=".$charge_id." && charge_status=1");
	}else{
		$sql_populate = "SELECT * FROM charges_table WHERE user_id=".$user_id." && charge_id=".$charge_id;
		$stmt_edit = $myPDO->query($sql_populate);
		if($stmt_edit->rowCount() > 0){
			$row = $stmt_edit->fetch(PDO::FETCH_ASSOC);
			$myPDO->query("UPDATE charges_table SET charge_status=".$charge_status.", updated_at='".$updated_at."' WHERE id=".$row['id']);
		}else{
			$myPDO->query("INSERT INTO charges_table(user_id, charge_id, charge_status, created_at) VALUES(".$user_id.", ".$charge_id.", ".$charge_status.", '".$created_at."')");
		}
	}
	if(ENV == 'PROD'){
		$sms_sender = 'Your charge transfer request has been forwarded to '.get_value_from_id("commissioners","name","user_id",$user_id).' on SAMAY. -CBIC';
		sendSMS(get_value_from_id("commissioners","contact_no","user_id",$charge_id), $template_id['ct_request'], $sms_sender);	//Sender
	}
	if($_SESSION['role'] == ADMIN_ACCESS || ($_SESSION['sess_userrole'] == 'cc_user' && get_value_from_id("users","role","id",$charge_id) == 'manager')){
		//$myPDO->query("UPDATE charges_table SET charge_status=0, updated_at='".$updated_at."' WHERE user_id=".$charge_id." && charge_id=".$charge_id);
		accept_transfer_request($user_id, $charge_id);
	}else{
		if(ENV == 'PROD'){
			sendSMS(get_value_from_id("commissioners","contact_no","user_id",$user_id), $template_id['ct_receive'], $sms_mob['receive']);	//Receiver
		}
	}
	return true;
}
function accept_transfer_request($user_id, $charge_id){
	global $myPDO, $template_id;
	$updated_at = date('Y-m-d H:i:s');
	$myPDO->query("UPDATE charges_table SET charge_status=0, updated_at='".$updated_at."' WHERE user_id=".$charge_id." && charge_id=".$charge_id);
	$myPDO->query("UPDATE charges_table SET charge_status=1, updated_at='".$updated_at."' WHERE user_id=".$user_id." && charge_id=".$charge_id);
	$myPDO->query("DELETE FROM charges_table WHERE user_id!=".$charge_id." && charge_id=".$charge_id." && charge_status=0");
	if(ENV == 'PROD'){
		$mobile = get_value_from_id("commissioners","contact_no","user_id",$user_id).','.get_value_from_id("commissioners","contact_no","user_id",$charge_id);
		$sms = 'Charge successfully transferred to '.get_value_from_id("commissioners","name","user_id",$user_id).' on SAMAY. -CBIC';
		sendSMS($mobile, $template_id['ct_success'], $sms);	//Sender & Receiver both
	}
	return true;
}
function reject_transfer_request($user_id, $charge_id){
	global $myPDO, $template_id, $sms_mob;
	$updated_at = date('Y-m-d H:i:s');
	$myPDO->query("UPDATE charges_table SET reject_status=1, updated_at='".$updated_at."' WHERE user_id=".$user_id." && charge_id=".$charge_id);
	if(ENV == 'PROD'){
		$sms_sender = 'Charge transfer request to '.get_value_from_id("commissioners","name","user_id",$user_id).' on SAMAY has been declined. -CBIC';
		sendSMS(get_value_from_id("commissioners","contact_no","user_id",$charge_id), $template_id['ct_decline_sender'], $sms_sender);	//Sender
		sendSMS(get_value_from_id("commissioners","contact_no","user_id",$user_id), $template_id['ct_decline_receiver'], $sms_mob['decline_receiver']);	//Receiver
	}
	return true;
}*/
function send_transfer_request($user_id, $formation_id, $given_to_formation, $charge_status, $created_at, $updated_at){
	global $myPDO, $template_id, $sms_mob;
	$parent = 0;
	if($_SESSION['role'] == ADMIN_ACCESS || ($_SESSION['sess_userrole'] == 'cc_user' && get_value_from_id("formations","role","id",$formation_id) == 'manager')){
		$myPDO->query("DELETE FROM charges_table WHERE user_id=".$user_id." && formation_id=".$formation_id);
		$parent = 1;
	}
	//$given_to_formation = ($parent == 0 ? $_SESSION['sess_fid'] : 0);
	$given_by_user = ($parent == 0 ? $_SESSION['sess_user_id'] : 0);
	$given_to_formation = ($parent == 0 ? $given_to_formation : 0);
	$myPDO->query("DELETE FROM charges_table WHERE formation_id=".$formation_id." && charge_status=0");	//not deleting with charge_status=1
	$myPDO->query("INSERT INTO charges_table(user_id, formation_id, given_by_user, given_to_formation, charge_status, created_at) VALUES(".$user_id.", ".$formation_id.", ".$given_by_user.", ".$given_to_formation.", 0, '".$created_at."')");
	//Sender's Contact No.
	$contact_sender = get_contact_by_formation($formation_id, '', 'yes');	//Keep this line before deleting old officer
	if(ENV == 'PROD' && $contact_sender != ''){
		$sms_sender = 'Your charge transfer request has been forwarded to '.get_value_from_id("commissioners","officer_name","user_id",$user_id).' on SAMAY. -CBIC';
		sendSMS($contact_sender, $template_id['ct_request'], $sms_sender);	//Sender
	}
	if(ENV == 'PROD'){
		sendSMS(get_value_from_id("commissioners","contact_no","user_id",$user_id), $template_id['ct_receive'], $sms_mob['receive']);	//Receiver
	}
	if($parent == 1){
		//$myPDO->query("DELETE FROM charges_table WHERE formation_id=".$formation_id);
		//$myPDO->query("INSERT INTO charges_table(user_id, formation_id, charge_status, created_at) VALUES(".$user_id.", ".$formation_id.", ".$charge_status.", '".$created_at."')");
		//$myPDO->query("UPDATE charges_table SET charge_status=0, updated_at='".$updated_at."' WHERE user_id=".$charge_id." && charge_id=".$charge_id);
		accept_transfer_request($user_id, $formation_id);
	}
	return true;
}
function accept_transfer_request($user_id, $formation_id){
	global $myPDO, $template_id;
	$updated_at = date('Y-m-d H:i:s');
	$old_contact = get_contact_by_formation($formation_id, '', 'yes');	//Keep this line before deleting old officer
	
	$myPDO->query("DELETE FROM charges_table WHERE user_id!=".$user_id." && formation_id=".$formation_id);
	$myPDO->query("UPDATE charges_table SET charge_status=1, updated_at='".$updated_at."' WHERE user_id=".$user_id." && formation_id=".$formation_id);
	
	if(ENV == 'PROD'){
		$mobile = get_value_from_id("commissioners","contact_no","user_id",$user_id).($old_contact != '' ? ','.$old_contact : '');
		$sms = 'Charge successfully transferred to '.get_value_from_id("formations","formation","id",$formation_id).' on SAMAY. -CBIC';
		sendSMS($mobile, $template_id['ct_success'], $sms);	//Sender & Receiver both
	}
	return true;
}
function reject_transfer_request($user_id, $formation_id){
	global $myPDO, $template_id, $sms_mob;
	$updated_at = date('Y-m-d H:i:s');
	$myPDO->query("UPDATE charges_table SET reject_status=1, updated_at='".$updated_at."' WHERE user_id=".$user_id." && formation_id=".$formation_id);
	if(ENV == 'PROD'){
		$sms_sender = 'Charge transfer request to '.get_value_from_id("formations","formation","id",$formation_id).' on SAMAY has been declined. -CBIC';
		$contact = get_contact_by_formation($formation_id, '', 'yes');
		sendSMS($contact, $template_id['ct_decline_sender'], $sms_sender);	//Sender
		sendSMS(get_value_from_id("commissioners","contact_no","user_id",$user_id), $template_id['ct_decline_receiver'], $sms_mob['decline_receiver']);	//Receiver
	}
	return true;
}
function charges_by_user($user_id){
	global $myPDO, $status_charge_bg, $status_charge_label;
	$sql_populate = $myPDO->query("SELECT * FROM charges_table WHERE user_id=".$user_id." && reject_status=0");
	$i = 0;
	$return = "";
	if($sql_populate->rowCount() > 0){
		foreach($sql_populate as $row){
			$return .= ($i == 0 ? '' : '<br/>')."<i class='fa fa-chevron-right'></i> ".get_value_from_id("formations","formation","id",$row['formation_id']).($row['charge_status'] == 0 ? ' (<span class="text-'.$status_charge_bg[$row['charge_status']].'">'.$status_charge_label[$row['charge_status']].'</span>)' : '');
			$i++;
		}
	}
	return $return;
}
function get_contact_by_formation($formation_id, $contact_type=NULL, $old=NULL){
	global $myPDO;
	if($old != ''){
		$charge_status = 1;	//For officer who previously have charge
	}else{
		$charge_status = 0;	//For officer who is going to take the charge
	}
	if($contact_type != ''){
		$type = $contact_type;	//For officer who previously have charge
	}else{
		$type = "contact_no";	//For officer who is going to take the charge
	}
	$sql_populate = "SELECT user_id FROM charges_table WHERE formation_id=".$formation_id." && charge_status=".$charge_status;
	$stmt_edit = $myPDO->query($sql_populate);
	$row = $stmt_edit->fetch(PDO::FETCH_ASSOC);
	return get_value_from_id("commissioners",$type,"user_id",$row['user_id']);
}
function get_user_by_formation($formation_id, $old=NULL){
	global $myPDO;
	if($old != ''){
		$charge_status = 1;	//For officer who previously have charge
	}else{
		$charge_status = 0;	//For officer who is going to take the charge
	}
	$sql_populate = "SELECT user_id FROM charges_table WHERE formation_id=".$formation_id." && charge_status=".$charge_status;
	$stmt_edit = $myPDO->query($sql_populate);
	$row = $stmt_edit->fetch(PDO::FETCH_ASSOC);
	return (int)$row['user_id'];
}
function coc_populate($centre_id=NULL) {
	global $myPDO;
	$sql_populate = "SELECT * FROM classification_case ORDER BY id ASC";
	$sql_populate = $myPDO->query($sql_populate);
	$return = "<option value=''>---Select Classification---</option>";
	foreach($sql_populate as $row){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $row['id'] . "' ".(($centre_id == $row['id']) ? 'selected' : '').">" . $row['case_class'] . "</option>";
	}
	return $return;
}
function pasc_populate($centre_id=NULL) {
	global $myPDO, $pasc;
	$sql_populate = $pasc;
	$return = "";
	foreach($sql_populate as $key=>$val){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $key . "' ".(($centre_id == $key) ? 'selected' : '').">" . $val . "</option>";
	}
	return $return;
}
function court_populate($centre_id=NULL) {
	global $myPDO, $courts;
	$return = "<option value=''>---Select Court---</option>";
	foreach($courts as $key=>$value){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $key . "' ".(($centre_id == $key) ? 'selected' : '').">" . $value . "</option>";
	}
	return $return;
}
/*function court_assoc_populate($centre_id=NULL) {
	global $myPDO, $courts_assoc;
	//$sql_populate = "SELECT commissioners.user_id, commissioners.name FROM commissioners, users as u WHERE u.id=commissioners.user_id and u.is_active=1 ORDER BY name ASC";
	//$sql_populate = mysql_query($sql_populate);
	$sql_populate = $courts_assoc;
	$return = "<option value=''>---Select Court---</option>";
	foreach($sql_populate as $key=>$value){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $key . "' ".(($centre_id == $key) ? 'selected' : '').">" . $value . "</option>";
	}
	return $return;
}*/
function bench_populate($court, $centre_id=NULL) {
	global $myPDO;
	/*if($_SESSION['sess_userrole'] == 'manager'){
		$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}*/
	//$sql_populate = "SELECT commissioners.user_id, commissioners.name FROM commissioners, users as u WHERE u.id=commissioners.user_id and u.is_active=1 ORDER BY name ASC";
	//$sql_populate = mysql_query($sql_populate);
	$sql_populate = "Select * from benches WHERE court_type='".$court."'";
	$return = "<option value='' selected='selected'>Select Bench</option>";
	foreach($myPDO->query($sql_populate) as $row){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $row['id'] . "' ".(($centre_id == $row['id']) ? 'selected' : '').">" . $row['bench_name'] . "</option>";
	}
	return $return;
}
function comm_order_status_populate($centre_id=NULL,$forwarded=NULL) {
	global $myPDO, $comm_order_status, $user_roles;
	/*if($_SESSION['sess_userrole'] == 'manager'){
		$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}*/
	//$sql_populate = "SELECT commissioners.user_id, commissioners.name FROM commissioners, users as u WHERE u.id=commissioners.user_id and u.is_active=1 ORDER BY name ASC";
	//$sql_populate = mysql_query($sql_populate);
	
	$return = "<option value=''>---Select Status---</option>";
	foreach($comm_order_status as $key=>$value){
		//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		if($key == 3 && $forwarded != ''){
			$overwrite = 'Forwarded to '.$user_roles['cc_user'];
		}else if($key == 3){
			$overwrite = 'Pending at '.$user_roles['cc_user'];
		}else if($key == 1){
			$overwrite = 'Pending at '.$user_roles['manager'];
		}else{
			$overwrite = $value;
		}
		$return .= "<option value='" . $key . "' ".(($centre_id == $value) ? 'selected' : '').">" . $overwrite . "</option>";
	}
	return $return;
}

function cc_order_status_populate($centre_id=NULL,$court=NULL) {
	global $myPDO, $cc_order_status;
	if($court == 'high'){
		array_pop($cc_order_status);
	}
	/*if($_SESSION['sess_userrole'] == 'manager'){
		$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}*/
	//$sql_populate = "SELECT commissioners.user_id, commissioners.name FROM commissioners, users as u WHERE u.id=commissioners.user_id and u.is_active=1 ORDER BY name ASC";
	//$sql_populate = mysql_query($sql_populate);
	$return = "<option value=''>---Select Status---</option>";
	foreach($cc_order_status as $key=>$value){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		//$return .= "<option value='" . $key . "' ".(($centre_id == $value) ? 'selected' : '').">" . ($value == 'Appeal/Petition to be filed in High Court' ? 'Appeal to be filed in High Court' : $value) . "</option>";
		$return .= "<option value='" . $key . "' ".(($centre_id == $value) ? 'selected' : '').">" . $value . "</option>";
	}
	return $return;
}

function comm_after_cc_order_status_populate($centre_id=NULL) {
	global $myPDO, $comm_after_cc_order_status;
	/*if($_SESSION['sess_userrole'] == 'manager'){
		$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}*/
	//$sql_populate = "SELECT commissioners.user_id, commissioners.name FROM commissioners, users as u WHERE u.id=commissioners.user_id and u.is_active=1 ORDER BY name ASC";
	//$sql_populate = mysql_query($sql_populate);
	$return = "<option value=''>---Select Status---</option>";
	foreach($comm_after_cc_order_status as $key=>$value){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $key . "' ".(($centre_id == $key) ? 'selected' : '').">" . $value . "</option>";
	}
	return $return;
}

function comm_after_cc_order_status_same_court_populate($centre_id=NULL,$court=NULL) {
	global $myPDO, $comm_after_cc_order_status_same_court;
	/*if($_SESSION['sess_userrole'] == 'manager'){
		$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}*/
	//$sql_populate = "SELECT commissioners.user_id, commissioners.name FROM commissioners, users as u WHERE u.id=commissioners.user_id and u.is_active=1 ORDER BY name ASC";
	//$sql_populate = mysql_query($sql_populate);
	$return = "<option value=''>---Select Status---</option>";
	foreach($comm_after_cc_order_status_same_court as $key=>$value){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $key . "' ".(($centre_id == $key) ? 'selected' : '').">" . (($court == 'tribunal' && $key == 2) ? 'Application Filed' : $value) . "</option>";
	}
	return $return;
}

function board_order_status_populate($centre_id=NULL) {
	global $myPDO, $board_order_status;
	/*if($_SESSION['sess_userrole'] == 'manager'){
		$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}*/
	//$sql_populate = "SELECT commissioners.user_id, commissioners.name FROM commissioners, users as u WHERE u.id=commissioners.user_id and u.is_active=1 ORDER BY name ASC";
	//$sql_populate = mysql_query($sql_populate);
	$return = "<option value=''>---Select Status---</option>";
	foreach($board_order_status as $key=>$value){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $key . "' ".(($centre_id == $value) ? 'selected' : '').">" . $value . "</option>";
	}
	return $return;
}
function comm_after_board_order_status_populate($centre_id=NULL) {
	global $myPDO, $comm_after_board_order_status;
	$return = "<option value=''>---Select Status---</option>";
	foreach($comm_after_board_order_status as $key=>$value){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		$return .= "<option value='" . $key . "' ".(($centre_id == $value) ? 'selected' : '').">" . $value . "</option>";
	}
	return $return;
}

function crud_order_status($crud, $order_id, $order_status, $old_order_status_id=NULL) {
	global $myPDO;
	if($crud == 'insert'){
		$q_status = "INSERT INTO order_status (order_id, comm_status, comm_date) VALUES (".$order_id.", ".$order_status.", '".date('Y-m-d')."')";
	}else{
		if($old_order_status_id != '' && $old_order_status_id != NULL){
			$q_status = "UPDATE order_status SET cc_status=".$order_status.", old_order_status_id=".$old_order_status_id." WHERE order_id=".$order_id;
		}else{
			$q_status = "UPDATE order_status SET cc_status=".$order_status." WHERE order_id=".$order_id;
		}
	}
	$myPDO->query($q_status);
	$order_status_id = $myPDO->lastInsertId();
	return $order_status_id;
}

function crud_order_activity($order_status_id, $activity_creator_id, $activity_name, $comment) {
	global $myPDO;
	$q_status = "INSERT INTO order_activity (order_status_id, activity_creator_id, activity_name, comment) VALUES (".$order_status_id.",".$activity_creator_id.",'".$activity_name."','".$comment."')";
	$myPDO->query($q_status);
}

function display_order_status($order_id) {
	global $myPDO, $comm_order_status, $cc_order_status, $comm_after_cc_order_status, $user_roles, $courts;
	$final_s = '';
	$list_start = '<li>';
	$list_end = '</li>';
	$editable = 'Y';
	$order_final_status = 'Order pending with the Commissioner';
	$edit_btn = "<a href='javascript:void(0);' class='change_status btn-xs btn-danger' data-toggle='modal' data-target='#changeModal' data-id='".$order_id."' style='cursor: pointer;'>Update <i class='fa fa-pencil' aria-hidden='true'></i></a>";
	$q_status = "SELECT * FROM order_status WHERE order_id=".$order_id;
	$stmt_status = $myPDO->query($q_status);
	if($stmt_status->rowCount() > 0) {
		$row_status = $stmt_status->fetch(PDO::FETCH_ASSOC);
		
		$comm_create = DateTime::createFromFormat('Y-m-d', $row_status['comm_date']);
		$comm_date1 = $comm_create->format('Y-m-d');
		$comm_date = "(".date('d-M-y', strtotime($comm_date1)).")";
		
		if(isset($row_status['cc_date']) && $row_status['cc_date'] != ''){
			$cc_create = DateTime::createFromFormat('Y-m-d', $row_status['cc_date']);
			$cc_date1 = $cc_create->format('Y-m-d');
			$cc_date = '('.date('d-M-y', strtotime($cc_date1)).')';
		}else{
			$cc_date = '';
		}
		if(isset($row_status['proposal_date']) && $row_status['proposal_date'] != ''){
			$proposal_create = DateTime::createFromFormat('Y-m-d', $row_status['proposal_date']);
			$proposal_date1 = $proposal_create->format('Y-m-d');
			$proposal_date = '('.date('d-M-y', strtotime($proposal_date1)).')';
		}else{
			$proposal_date = '';
		}
		if(isset($row_status['board_date']) && $row_status['board_date'] != ''){
			$board_create = DateTime::createFromFormat('Y-m-d', $row_status['board_date']);
			$board_date1 = $board_create->format('Y-m-d');
			$board_date = '('.date('d-M-y', strtotime($board_date1)).')';
		}else{
			$board_date = '';
		}
		
		if($row_status['comm_status'] == 'Pending at Commissioner'){
			$row_status['comm_status'] = 'Pending at '.$user_roles['manager'];
		}
		$comm_s = $list_start.$row_status['comm_status'].$comm_date.$list_end;
		$cc_s = $list_start.$row_status['cc_status'].$cc_date.$list_end;
		$prop_s = (($row_status['proposal_status'] == 2) ? $list_start.'Proposal Forwarded to Board'.$proposal_date.$list_end : '');
		//$prop_s = $list_start.(($row_status['proposal_status'] == 2) ? 'Proposal Forwarded to Board' : 'Proposal under Process').$list_end;
		$board_s = $list_start.$row_status['board_status'].$board_date.$list_end;
		$appeal_s = $list_start.$row_status['appeal_status'].$list_end;
		
		if($row_status['comm_status'] != 'Pending at Chief Commissioner'){
			if($_SESSION['sess_userrole'] != 'manager'){
				$edit_btn = '';
				$editable = 'Y';
			}
			if($row_status['comm_status'] == 'Order Accepted'){
				$order_final_status = 'Order Accepted by the '.$user_roles['manager'];
				$edit_btn = '';	//Edit Button Hidden in every case (No further action needed)
				$editable = 'N';
				$final_s .= $list_start.$row_status['comm_status']." by ".$user_roles['manager'].$comm_date.$list_end;
			}else{
				$final_s .= $comm_s;
			}
		}else{
			if($row_status['comm_status'] == 'Pending at Chief Commissioner'){
				$comm_s = $list_start.'Forwarded to '.$user_roles['cc_user'].$comm_date.$list_end;
				$editable = ($_SESSION['sess_userrole'] != 'manager' ? 'Y' : 'N');
			}
			if($row_status['cc_status'] == 'Order Accepted'){
				$order_final_status = 'Order Accepted by the '.$user_roles['cc_user'];
				//$final_s .= $comm_s.$cc_s;
				$final_s .= $comm_s.$list_start.$row_status['cc_status']." by ".$user_roles['cc_user'].$cc_date.$list_end;
				$edit_btn = '';	//Edit Button Hidden in every case (No further action needed)
				$editable = 'N';
			}else if($row_status['cc_status'] == 'Appeal/Petition to be filed in Supreme Court'){
				$order_final_status = $user_roles['cc_user'].' approved for filing SLP/CA';
				//$final_s .= $comm_s.$cc_s;
				//$final_s .= $comm_s.$list_start.'Action Pending after '.$user_roles['cc_user'].' Decision'.$cc_date.$list_end;
				$final_s .= $comm_s.$list_start.$user_roles['cc_user'].' approved for filing SLP/CA'.$cc_date.$list_end;
				$editable = 'N';
				if($_SESSION['sess_userrole'] != 'manager' && $row_status['proposal_status'] != 2){
					$edit_btn = '';
					$editable = 'Y';
				}
				if($row_status['proposal_status'] == 2){
					if($_SESSION['sess_userrole'] == 'cc_user'){
						$editable = 'N';
					}
					$order_final_status = 'Proposal forwarded to Board for filing Appeal/Petition';
					$final_s .= $prop_s;
					//if($_SESSION['sess_userrole'] == 'manager' || $_SESSION['sess_userrole'] == 'cc_user' || $_SESSION['sess_userrole'] == 'admin'){
					/*if(!($_SESSION['sess_userrole'] == 'high' || $_SESSION['sess_userrole'] == 'tribunal')){
						$edit_btn = '';
					}*/
					if($row_status['board_status'] == 'Appeal/Petition Filed'){
						$order_final_status = 'Appeal/Petition Filed by Board';
						//$final_s .= $board_s;
						$final_s .= $list_start.$row_status['board_status'].' by Board'.$board_date.$list_end;
						if($_SESSION['sess_userrole'] != 'manager'){
							$edit_btn = '';
							$editable = 'N';
						}
						if($row_status['appeal_status'] != 'Appeal Decided'){
							//$order_final_status = 'Appeal Pending with the Commissioner';
							$edit_btn = '';		//Temporary Addition, Remove if above line is uncommented...
							$editable = 'N';
						}
						//$final_s .= $appeal_s;
						if($row_status['appeal_status'] == 'Appeal Decided'){
							$order_final_status = 'Appeal Decided';
							$edit_btn = '';
							$editable = 'N';
						}
					}else if($row_status['board_status'] == 'Board decided not to file SLP or CA'){
						$order_final_status = 'Board decided not to file SLP or CA';
						$final_s .= $board_s;
						$edit_btn = '';	//Edit Button Hidden in every case (No further action needed)
						$editable = 'N';
					}else{
						$order_final_status = 'Order Pending with the Board';
						if(!($_SESSION['sess_userrole'] == 'board')){
							$edit_btn = '';
							$editable = 'N';
						}
					}
				}else if($row_status['proposal_status'] == 1){
					$order_final_status = 'Proposal under Process';
					$final_s .= $prop_s;
					//$editable = 'N';
				}
			}else if($row_status['cc_status'] == 'Appeal/Petition to be filed in High Court' || $row_status['cc_status'] == 'Application to be filed in Tribunal'){
				if($row_status['cc_status'] == 'Appeal/Petition to be filed in High Court'){
					$appeal_str = 'Appeal/Petition';
					//$filing_str = 'SLP/CA';
					$my_court = $courts['high'];
				}else{
					$appeal_str = 'Application';
					//$filing_str = 'Application';
					$my_court = $courts['tribunal'];
				}
				$order_final_status = 'Appeal/Petition to be filed in '.$my_court;
				$final_s .= $comm_s.$list_start.$user_roles['cc_user'].' approved for filing '.$appeal_str.' in '.$my_court.$cc_date.$list_end;
				//$edit_btn = '';	//Edit Button Hidden in every case (No further action needed)
				//$editable = 'N';
				if($_SESSION['sess_userrole'] != 'manager' && $row_status['proposal_status'] != 2){
					$edit_btn = '';
					//$editable = 'N';
				}
				if($row_status['proposal_status'] == 2){
					$edit_btn = '';
					if($_SESSION['sess_userrole'] == 'cc_user'){
						$editable = 'N';
					}
					$order_final_status = 'Appeal/Petition Filed';
					$final_s .= $list_start.$appeal_str.' filed in '.$my_court.$proposal_date.$list_end;
				}else if($row_status['proposal_status'] == 1){
					$order_final_status = 'Proposal under Process';
					$final_s .= $prop_s;
					//$editable = 'N';
				}
			}else{
				$order_final_status = 'Order Pending with the '.$user_roles['cc_user'];
				$final_s .= $comm_s;
				if($_SESSION['sess_userrole'] == 'manager' || $_SESSION['sess_userrole'] == 'board'){
					$edit_btn = '';
					//$editable = 'N';
				}
			}
		}
		if($_SESSION['sess_userrole'] == 'admin' || $_SESSION['sess_userrole'] == 'board'){
			$editable = 'Y';
		}
	}
	//if($_SESSION['sess_userrole'] == 'manager' || $_SESSION['sess_userrole'] == 'cc_user'){
		//$final_s .= '</ul>';
		$final_s_arr = explode('</li><li>',rtrim(ltrim($final_s, "<li>"), "</li>"));
		$final_ss = '<li>'.implode('</li><li>', array_reverse($final_s_arr)).'</li>';
		$final_s = '<div class="wrap_ul"><div class="overlay text-white"><i class="fa fa-eye" title="Expand"></i></div><ul class="orders_status">'.$final_ss.'</ul></div>';
	//}
	//$edit_btn = ($edit_btn != '') ? '<br/>'.$edit_btn : '';
	$edit_btn = ($edit_btn != '') ? $edit_btn : '';
	//$out_arr = array($final_s, $order_final_status.$edit_btn,$editable);
	$out_arr = array($final_s, $edit_btn, $editable);
	return $out_arr;
}

function display_order_messages($order_id){
	global $myPDO;
	$edit_btn = "<a href='javascript:;' class='display_msgs btn-lg text-info' data-toggle='modal' data-target='#display_msgs' data-id='".$order_id."' style='cursor: pointer;position: relative;'><i class='fa fa-envelope' aria-hidden='true'></i>";
	$edit_btn_close = "</a>";
	$edit_btn_new = '<span class="new_span_holder" style="position: absolute; right: -7px; top: -5px;"><span class="new_span" style="padding: 5px;font-size: 9px;font-weight: bold;background: red;color: #fff;border-radius: 50%;">Alert</span></span>';
	if($_SESSION['sess_userrole'] == 'cc_user'){
		$q_status = "SELECT * FROM chat WHERE order_id=".$order_id;
		$stmt_status = $myPDO->query($q_status);
		if($stmt_status->rowCount() > 0) {
			//$row_status = $stmt_status->fetch(PDO::FETCH_ASSOC);
			$return = $edit_btn;
			$q_seen = "SELECT * FROM chat WHERE order_id=".$order_id." && seen_status=0";
			$stmt_seen = $myPDO->query($q_seen);
			if($stmt_seen->rowCount() > 0) {
				$return .= $edit_btn_new;
			}
			$return .= $edit_btn_close;
		}else{
			$return = "-";
		}
	}else{
		$return = $edit_btn.$edit_btn_close;
	}
	return $return;
}

function chat_data($order_id=NULL){
	global $myPDO;
	$msgs = array();
	$current = strtotime(date("Y-m-d"));
	
	if($_SESSION['sess_userrole'] == 'cc_user'){
		$upd_seen = "UPDATE chat SET seen_status=1 WHERE order_id=".$order_id;
		$myPDO->query($upd_seen);
	}
	
	$q_comment = "SELECT *, date(comment_date) AS commentdate FROM chat WHERE order_id = " . $order_id . " GROUP BY date(comment_date) ORDER BY comment_date DESC";
	$i = 0;
	foreach($myPDO->query($q_comment) as $row){
		$j = 0;
		$co_data = array();
		$date = strtotime(date($row['commentdate']));
		$datediff = $current - $date;
		$difference = floor($datediff/(60*60*24));
		if($difference == 0){
			$day = 'today';
		}else if($difference == 1){
			$day = 'yesterday';
		}else{
			$day = date('d-M-y',strtotime($row['commentdate']));
		}
		
		$q_row = "SELECT comment, time(comment_date) AS c_time FROM chat WHERE order_id = " . $order_id . " && date(comment_date)='".$row['commentdate']."' ORDER BY comment_date DESC";
		foreach($myPDO->query($q_row) as $row1){
			$co_data[$j][0] = $row1['comment'];
			$co_data[$j][1] = date('g:i A',strtotime($row1['c_time']));
			$j++;
		}
		$msgs[$i]['day'] = $day;
		$msgs[$i]['comments'] = $co_data;
		$i++;
	}
	return $msgs;
}

function batch_populate() {
	global $myPDO;
	global $batch_limit;
	$empty_batches = array();
	//$sql_populate = "SELECT reg_id FROM registrations rr LEFT JOIN centres c ON rr.centre_id = c.id WHERE c.user_id = ".$_SESSION['sess_user_id'];
	$sql_populate = "SELECT reg_batch, count(*) as count FROM registrations rr LEFT JOIN centres c ON rr.centre_id = c.id WHERE c.user_id = ".$_SESSION['sess_user_id']." group by reg_batch";
	$query_results = $myPDO->query($sql_populate);
	foreach($query_results as $row){
		$empty_batches[$row['reg_batch']] = $row['count'];
	}
	//$num_of_regs = $query_results->rowCount();
	//$unav_batches = (int)($num_of_regs/$batch_limit) + 1;
	$no_of_batches = get_value_from_id("centres","no_of_batches","user_id",$_SESSION['sess_user_id']);
	$return = "<option value=''>---Select Batch---</option>";
	//for($i = $unav_batches; $i <= $no_of_batches; $i++){
	for($i = 1; $i <= $no_of_batches; $i++){
		$emp = (isset($empty_batches[$i])) ? $empty_batches[$i] : 0 ;
		$return .= "<option value='" . $i . "' ".(($emp < $batch_limit) ? '' : 'disabled' )." >Batch " . $i . (($emp < $batch_limit) ? '' : ' (full)' )."</option>";
	}
	return $return;
}
function export_to_excel($label_arr, $widthArr, $numberFormatArr, $rowData, $filename, $title='Worksheet'){
	global $myPDO;
	//include(ROOT_DIR_COMMON."Excel.php");
	require_once ROOT_DIR_COMMON."functions.php";
	require_once ROOT_DIR_COMMON."excel_vendor/autoload.php";
	//use PhpOffice\PhpSpreadsheet\Spreadsheet;
	//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	$rangeTo = chr(65+count($label_arr));
	
	$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
	$object = $spreadsheet->getActiveSheet();
	$object->setTitle($title);
	
	//$object = new Excel();
	//$object->setActiveSheetIndex(0);
	
	$column = 1;
	
	/*foreach($label_arr as $field=>$val){
		$object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $val);
		$column++;
	}*/
	//$count_label = 1;
	foreach($label_arr as $field=>$val){
	//foreach(range('A',$rangeTo) as $val){
		//$object->setCellValue(getColName($count_label).'1', $val);
		$object->setCellValueByColumnAndRow($column, 1, $val);
		$column++;
	}
	foreach($widthArr as $wkey=>$wval){
		//$object->getActiveSheet()->getColumnDimension($wkey)->setWidth($wval);
		$object->getColumnDimension($wkey)->setWidth($wval);
	}
	foreach($numberFormatArr as $nkey=>$nval){
		//$object->getActiveSheet()->getStyle($nkey)->getNumberFormat()->setFormatCode($nval);
		$object->getStyle($nkey)->getNumberFormat()->setFormatCode($nval);
	}
	
	$colL = count($label_arr);
	$rowL = count($rowData);
	for($i = 0; $i < $rowL; $i++){
		for($j = 0; $j < $colL; $j++){
			//$object->getActiveSheet()->setCellValueByColumnAndRow($j, $i+2, $rowData[$i+2][$j]);
			$object->setCellValueByColumnAndRow($j+1, $i+2, $rowData[$i+2][$j]);
		}
	}
	
	//$object->setActiveSheetIndex(0);
	//$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
	//echo "<pre>";
	//print_r($rowData);
	// Write an .xlsx file
	$object_writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
	// Sending headers to force the user to download the file
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="' . $filename . '"');
	header('Cache-Control: max-age=0');
	//$object_writer->save($filename);	//To save file on the server
	$object_writer->save('php://output');
	//print_r($object);die();
	//die();
	/*$ex_file_name = $fileName . '_' . date('d-m-y') . '.xls';
	$object_writer->save(str_replace(__FILE__,'uploads/files/'.$ex_file_name,__FILE__));*/
/*
	header('Content-Type: application/zip');
	header('Content-disposition: attachment; filename='.$zipname);
	header('Content-Length: ' . filesize($zipname));
	readfile($zipname);*/
	
}
/*function export_to_excel1($label_arr, $widthArr, $numberFormatArr, $rowData, $filename){
	global $myPDO;
	include(ROOT_DIR_COMMON."Excel.php");

	$object = new Excel();
	$object->setActiveSheetIndex(0);
	
	$column = 0;
	
	foreach($label_arr as $field=>$val){
		$object->getActiveSheet()->setCellValueByColumnAndRow($column, 1, $val);
		$column++;
	}
	foreach($widthArr as $wkey=>$wval){
		$object->getActiveSheet()->getColumnDimension($wkey)->setWidth($wval);
	}
	foreach($numberFormatArr as $nkey=>$nval){
		$object->getActiveSheet()->getStyle($nkey)->getNumberFormat()->setFormatCode($nval);
	}
	
	$colL = count($label_arr);
	$rowL = count($rowData);
	for($i = 0; $i < $rowL; $i++){
		for($j = 0; $j < $colL; $j++){
			$object->getActiveSheet()->setCellValueByColumnAndRow($j, $i+2, $rowData[$i+2][$j]);
		}
	}
	
	$object->setActiveSheetIndex(0);
	$object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
	//echo "<pre>";
	//print_r($rowData);
	// Sending headers to force the user to download the file
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="' . $filename . '"');
	header('Cache-Control: max-age=0');
	$object_writer->save('php://output');
	die();*/
	/*$ex_file_name = $fileName . '_' . date('d-m-y') . '.xls';
	$object_writer->save(str_replace(__FILE__,'uploads/files/'.$ex_file_name,__FILE__));*/
/*
	header('Content-Type: application/zip');
	header('Content-disposition: attachment; filename='.$zipname);
	header('Content-Length: ' . filesize($zipname));
	readfile($zipname);*/
	
//}

?>