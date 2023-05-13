<?php
include 'init.php';
include ROOT_DIR_COMMON.'functions.php';
$autoTypeNo = $_GET['type'];
//$invoice_type = $_GET['invoice_type'];
//echo $autoTypeNo."<br/>";
$query = '%'.$_GET['term'].'%'; // add % for LIKE query later
$comm_id = $_GET['comm_id'];
$cc_id = $_GET['cc_id'];
// do query

/*if($invoice_type == 'sale'){
	$not_allowed = 2;
}else{
	$not_allowed = 1;
}*/
/*if($autoTypeNo == 'code'){
	$stmt = $myPDO->prepare('SELECT id, code, name, price, qty FROM products WHERE code LIKE :query AND is_active=1');
}else if($autoTypeNo == 'name'){*/
	$q_cust = 'SELECT O.id AS o_id, F.id AS f_id, O.order_no, O.party FROM orders O INNER JOIN formations F ON F.id=O.formation_id WHERE O.is_active=1';
	/*if(isset($_GET['invoice_type']) && $_GET['invoice_type'] != ''){
		$q_cust .= 'order_no!='.$not_allowed." && ";
	}*/
	if(!empty($comm_id)){
		$q_cust .= ' && F.id='.$comm_id;
	}else if(!empty($cc_id)){
		$q_cust .= ' && F.parent_id='.$cc_id;
	}
	if($autoTypeNo == 'order_no'){
		$q_cust .= ' && O.order_no LIKE :query';
	}else if($autoTypeNo == 'party'){
		$q_cust .= ' && O.party LIKE :query';
	}
	$stmt = $myPDO->prepare($q_cust);
//}

$stmt->bindParam(':query', $query, PDO::PARAM_STR);
$stmt->execute();


// populate results
$results = array();
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
	/*if(isset($_GET['page']) && $_GET['page'] != ''){
		$results['value'] = $row['order_no'];
		$results['label'] = $row['order_no'];
		$results['id'] = $row['id'];
	/*}else{
		if(!empty($row['cust_state_id'])){
			$cust_state = get_value_from_id("states", "state_name", "id", $row['cust_state_id']);
		}else{
			$cust_state = '';
		}*/
		$results[] = $row['o_id']."|".$row['order_no']."|".$row['party'];
	//}
}
//$results = array('S24_4620|1961 Chevrolet Impala|32.33','S24_4620|1961 Chevrolet Impala|32.33','S24_4620|1961 Chevrolet Impala|32.33');
/*if(isset($_GET['page']) && $_GET['page'] != ''){
	echo json_encode([$results]);
}else{*/
	echo json_encode($results);
//}
?>