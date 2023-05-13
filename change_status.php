<?php
include 'init.php';
include ROOT_DIR_COMMON.'functions.php';

if(!empty($_POST["order_id"])) {
	$q_order = "SELECT * FROM order_status WHERE order_id = " . $_POST["order_id"];
	$order_receiving_date = explode(" ",get_value_from_id("orders","order_receiving_date","id",$_POST["order_id"]));
	$r_date = $order_receiving_date[0];
	$stmt_order = $myPDO->query($q_order);
	$row_order = $stmt_order->fetch(PDO::FETCH_ASSOC);
	$courtt = get_value_from_id("orders","court","id",$row_order['order_id']);
	$output_select = '<select id="change_select" name="change_select" class="form-control" required>';
	if($_SESSION['sess_userrole'] == 'manager'){
		if($row_order['board_status'] == 'Appeal/Petition Filed'){
			/*$output_select .= comm_after_board_order_status_populate($row_order['appeal_status']);
			$update_column = 'appeal_status';*/
		}else if($row_order['cc_status'] == 'Appeal/Petition to be filed in Supreme Court'){
			$output_select .= comm_after_cc_order_status_populate($row_order['proposal_status']);
			$update_column = 'proposal_status';
		}else if($row_order['cc_status'] == 'Appeal/Petition to be filed in High Court' || $row_order['cc_status'] == 'Application to be filed in Tribunal'){
			if($row_order['cc_status'] == 'Appeal/Petition to be filed in High Court'){
				$output_select .= comm_after_cc_order_status_same_court_populate($row_order['proposal_status']);
			}else if($row_order['cc_status'] == 'Application to be filed in Tribunal'){
				$output_select .= comm_after_cc_order_status_same_court_populate($row_order['proposal_status'],'tribunal');
			}
			$update_column = 'proposal_status';
		}else{
			$output_select .= comm_order_status_populate($row_order['comm_status'],'forwarded');
			$update_column = 'comm_status';
		}
	}
	if($_SESSION['sess_userrole'] == 'cc_user' || $_SESSION['sess_userrole'] == 'admin'){
		$output_select .= cc_order_status_populate($row_order['cc_status'],$courtt);
		$update_column = 'cc_status';
	}
	if($_SESSION['sess_userrole'] == 'board'){
		$output_select .= board_order_status_populate($row_order['board_status']);
		$update_column = 'board_status';
	}
	//if($_POST["price"] >= $row_order['ws_price']){
		$stat = 'true';
	//}else{
		$stat = 'false';
	//}
	$output_select .= '</select>';
	$arrr = array('stat'=>'true','output'=>$output_select,'update_column'=>$update_column,'order_id'=>$_POST['order_id']);
	if($row_order['board_status'] == 'Appeal Filed'){
		$arrr['r_date'] = $r_date;
	}
	if($update_column == 'proposal_status' && $row_order['cc_status'] == 'Appeal/Petition to be filed in Supreme Court'){
		$arrr['lims'] = 'y';
	}else{
		$arrr['lims'] = 'n';
	}
	echo json_encode($arrr);
	
}
?>
