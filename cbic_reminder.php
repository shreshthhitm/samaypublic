<?php
include 'init.php';
include ROOT_DIR_COMMON.'functions.php';

$high = get_value_from_id("boards","formation_id","court_type","high");
//$high = get_value_from_id("formations","id","role","high");
$high_user = get_user_by_formation($high, 'yes');
$high_contact = get_value_from_id("commissioners","contact_no","user_id",$high_user);
$high_email = get_value_from_id("users","email","id",$high_user);

$tribunal = get_value_from_id("boards","formation_id","court_type","tribunal");
//$tribunal = get_value_from_id("formations","id","role","tribunal");
$tribunal_user = get_user_by_formation($tribunal, 'yes');
$tribunal_contact = get_value_from_id("commissioners","contact_no","user_id",$tribunal_user);
$tribunal_email = get_value_from_id("users","email","id",$tribunal_user);

$today_date = "'".date('Y-m-d')."'";
//echo $today_date;
$order_receiving_date = 'date(O.order_receiving_date)';
$dash_query_start = "Select *, DATEDIFF(".$today_date.",".$order_receiving_date.") AS total_delay from orders O INNER JOIN order_status OS ON O.id=OS.order_id INNER JOIN formations F ON F.id=O.formation_id WHERE O.is_active=1 && O.possibility=1 && (";
/*$user_id = $_SESSION['sess_user_id'];
$userrole = $_SESSION['sess_userrole'];
$user_q = '';
if($userrole == 'high'){
	$user_q = "O.court='high' && ";
}
if($userrole == 'tribunal'){
	$user_q = "O.court='tribunal' && ";
}
if($userrole == 'cc_user'){
	$user_q = "C.parent_id=".$user_id." && ";
}
if($userrole == 'manager'){
	$user_q = "O.user_id=".$user_id." && ";
}
$dash_query_start .= $user_q;*/
$query_rem = $dash_query_start.$dashboard_query[1]." OR ".$dashboard_query[3]." OR ".$dashboard_query[5]." OR ".$dashboard_query[6].")";
//echo $query_rem; die();
foreach($myPDO->query($query_rem) as $row){
	//echo "<pre>";
	//print_r($row);
	$send_flag = 0;
	$contact_arr = array();
	$email_arr = array();
	//$sms1 = 'Order No. '.$row['order_no'].' Dated '.date('d-M-y', strtotime($row['order_receiving_date'])).' is pending for processing';
	///$sms1 = 'Order No. '.$row['order_no'].' Dated '.date('d-M-y', strtotime($row['order_receiving_date'])).' is pending for processing';
	$sms1 = 'Order No. '.$row['order_no'].' dated '.date('d-M-y', strtotime($row['order_receiving_date'])).' in the case of '.$row['party'].' is pending for processing';
	$comm_user = get_user_by_formation($row['formation_id'], 'yes');
	$comm_contact = get_value_from_id("commissioners","contact_no","user_id",$comm_user);
	$comm_email = get_value_from_id("users","email","id",$comm_user);
	
	$cc_idd = get_value_from_id("formations","parent_id","id",$row['formation_id']);
	$cc_contact = get_value_from_id("commissioners","contact_no","user_id",$cc_idd);
	$cc_email = get_value_from_id("users","email","id",$cc_idd);
	
	$contact_arr[] = $comm_contact;
	$contact_arr[] = $cc_contact;
	$email_arr[] = $comm_email;
	$email_arr[] = $cc_email;
	$template_id1 = $template_id['remfinal'];
	$sms = 'ALERT! '.$sms1.' from '.$row['total_delay'].' days. -CBIC';
	
	if($row['total_delay'] >= 30){
		if($row['court'] == 'tribunal'){
			$contact_arr[] = $tribunal_contact;
			$email_arr[] = $tribunal_email;
		}else{
			$contact_arr[] = $high_contact;
			$email_arr[] = $high_email;
		}
		
		$case = 'Case2';
		//$template_id1 = $template_id['rem35days'];
		//$var_values = $row['order_no'];
		$send_flag = 1;
	}else if($row['total_delay'] >= 10 && $row['total_delay'] < 30){
		$case = 'Case3';
		//$var_values = $row['order_no'].'|'.$row['total_delay'];
		$send_flag = 1;
	}
	if($send_flag == 1){
		//echo "<br/>".$case."<br/>";
		$mobile = implode(',',$contact_arr);
		//$mobile = '919027074744,919458677614';
		/*$mobile = '917017679328,918279974192';
		echo $sms.'<br/>';
		echo $mobile.'<br/>';*/
		$email = implode(',',$email_arr);
		$strSubject_delay = "CBIC SAMAY | Case Reminder";
		$message = '<tr><th style="padding: 0;"><h3 style="background-color: #0073b1;padding: 20px 0;color: #fff;margin: 0;">Case Reminder | CBIC Samay</h3></th></tr>';
		$message .= '<tr><td style="background-color: #edf0f3;padding: 15px;">You have a case pending. Please see the details below:-<br/><b>'.$sms.'</b></td></tr>';
		//sendSMS($mobile, $template_id1, $var_values);
		sendSMS($mobile, $template_id1, $sms);
		//send_mail_function($email, $strSubject_delay, $message, $site_email, $site_name);
		foreach($email_arr as $em){
			send_mail_function($em, $strSubject_delay, $message, $site_email, $site_name);
		}
		foreach($contact_arr as $mob){
			$items_q = "INSERT INTO reminders(text_msg,contact_no) VALUES ('".$sms."','".$mob."')";
			$myPDO->query($items_q);
		}
	}
}

$query_ch_rem = "select * from charges_table where charge_status=0 && reject_status=0";
foreach($myPDO->query($query_ch_rem) as $row){
	sendSMS(get_value_from_id("commissioners","contact_no","user_id",$row['user_id']), $template_id['ct_rem'], $sms_mob['charge_reminder']);
}
?>
