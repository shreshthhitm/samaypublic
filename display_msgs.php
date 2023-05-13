<?php
include 'init.php';
include ROOT_DIR_COMMON.'functions.php';

if(!empty($_POST["order_id"])) {
	$order_id = $_POST["order_id"];
	$order_no = get_value_from_id("orders","order_no","id",$order_id);
	//$msgs = array();
	//$q_order = "SELECT * FROM chat WHERE order_id = " . $_POST["order_id"];
	/*$order_receiving_date = explode(" ",get_value_from_id("orders","order_receiving_date","id",$_POST["order_id"]));
	$r_date = $order_receiving_date[0];*/
	//$stmt_order = $myPDO->query($q_order);
	$msgs = chat_data($order_id);
	/**/
	/*$row_order = $stmt_order->fetch(PDO::FETCH_ASSOC);
	$output_select = '<select id="change_select" name="change_select" class="form-control" required>';
	if($_SESSION['sess_userrole'] == 'manager'){
		if($row_order['board_status'] == 'Appeal Filed'){
			$output_select .= comm_after_board_order_status_populate($row_order['appeal_status']);
			$update_column = 'appeal_status';
		}else if($row_order['cc_status'] == 'Appeal to be filed'){
			$output_select .= comm_after_cc_order_status_populate($row_order['proposal_status']);
			$update_column = 'proposal_status';
		}else{
			$output_select .= comm_order_status_populate($row_order['comm_status']);
			$update_column = 'comm_status';
		}
	}
	if($_SESSION['sess_userrole'] == 'cc_user' || $_SESSION['sess_userrole'] == 'admin'){
		$output_select .= cc_order_status_populate($row_order['cc_status']);
		$update_column = 'cc_status';
	}
	if($_SESSION['sess_userrole'] == 'high' || $_SESSION['sess_userrole'] == 'tribunal'){
		$output_select .= board_order_status_populate($row_order['board_status']);
		$update_column = 'board_status';
	}*/
	//if($_POST["price"] >= $row_order['ws_price']){
		$stat = 'true';
	//}else{
		//$stat = 'false';
	//}
	/*$output_select .= '</select>';
	$arrr = array('stat'=>'true','output'=>$output_select,'update_column'=>$update_column,'order_id'=>$_POST['order_id']);
	if($row_order['board_status'] == 'Appeal Filed'){
		$arrr['r_date'] = $r_date;
	}
	echo json_encode($arrr);
	*/
	$arrr = array('stat'=>$stat,'order_id'=>$order_id,'order_no'=>$order_no,'chats'=>$msgs);
}else{
	$stat = 'false';
	$arrr = array('stat'=>$stat);
}

echo json_encode($arrr);
?>
