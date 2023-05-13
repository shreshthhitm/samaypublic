<?php
include 'init.php';
include ROOT_DIR_COMMON.'functions.php';

/*if(!empty($_POST['city_id'])){
	$city_id = $_POST['city_id'];
}else{*/
	//$comm_id = NULL;
//}
$not_allowed = '';
$authority = 'Admin/'.$user_roles['cc_user'];
$page_name = 'Get Comm WRT Role';
if(!empty($_POST["role"])) {
	//global $myPDO;
	//if($_POST['role'] == 'role'){}else{
		//$popu = all_user_populate2('',$_POST["role"], (($_SESSION['sess_userrole'] == 'cc_user' && $_POST['role'] == 'manager') ? $_SESSION['sess_user_id'] : ''), $_POST["exclude"],((isset($_POST['elem']) && $_POST['elem'] == 'role' && $_POST['role'] == 'cc_user' && $_SESSION['sess_userrole'] == 'cc_user') ? $_SESSION['sess_user_id'] : ''), (isset($_POST['transfer_reason']) ? (($_POST['transfer_reason'] == 'due_to_leave' || $_POST["exclude"] == '') ? '' : $_POST['transfer_reason']) : ''));
		if(isset($_POST['elem']) && $_POST['elem'] == 'role'){
			$popu = formation_transfer_populate('',$_POST["role"], (($_SESSION['sess_userrole'] == 'cc_user' && $_POST['role'] == 'manager') ? $_SESSION['sess_fid'] : ''), $_POST["exclude"],((isset($_POST['elem']) && $_POST['elem'] == 'role' && $_POST['role'] == 'cc_user' && $_SESSION['sess_userrole'] == 'cc_user') ? $_SESSION['sess_fid'] : ''), '', (isset($_POST['transfer_reason']) && $_POST['transfer_reason'] == 'due_to_leave' && $_POST['role'] == 'manager' ? 'yes' : ''));
			//$not_allowed = 'case 1';
		}else{
			//$not_allowed = 'else';
			if($_POST['transfer_reason'] == 'due_to_leave'){
				$popu = formation_transfer_populate('',$_POST["role"], ($_POST['role'] == $_SESSION['sess_userrole'] ? '' : $_SESSION['sess_fid']), $_POST["exclude"],((isset($_POST['elem']) && $_POST['elem'] != 'role' && $_POST['elem'] == 'manager' && $_SESSION['sess_userrole'] == 'cc_user') ? $_SESSION['sess_fid'] : ''), ((isset($_POST["exclude"]) && $_SESSION['sess_userrole'] != 'admin' && $_POST['role'] == $_SESSION['sess_userrole']) ? 'yes' : ''), '', 'no');
			}else{
				$exclude_user_id = '';
				$sql_populate3 = "SELECT * FROM charges_table WHERE formation_id=".$_POST['exclude']." && charge_status=1";
				$stmt_formation = $myPDO->query($sql_populate3);
				if($stmt_formation->rowCount() > 0){
					$row_formation = $stmt_formation->fetch(PDO::FETCH_ASSOC);
					$exclude_user_id = $row_formation['user_id'];
				}
				$popu = all_user_populate('',$_POST["role"], $exclude_user_id, (isset($_POST['transfer_reason']) ? $_POST['transfer_reason'] : ''));
			}
		}
	//}
}else if(!empty($_POST["userrole"])) {
	//global $myPDO;
	//if($_POST['role'] == 'role'){}else{
		//$popu = commissioner_to_transfer_populate('',$_POST["userrole"], '', '', (isset($_POST['transfer_reason']) ? ($_POST['transfer_reason'] == 'due_to_leave' ? '' : $_POST['transfer_reason']) : ''));
		$exclude_user_id = '';
		
		$userrole = $_SESSION['sess_userrole'];
		$formation_id = $_SESSION['sess_fid'];
		$parent_formation = get_value_from_id("formations","parent_id","id",$_SESSION['sess_fid']);
		if($_POST['transfer_reason'] == 'due_to_leave'){
			$popu = formation_transfer_populate('',$userrole, ($userrole == 'manager' ? $parent_formation : ''), $formation_id,'', 'yes', '', 'no');
		}else{
			//$not_allowed = 'hi';
			$exclude_user_id = $_SESSION['sess_user_id'];
			$popu = all_user_populate('',$userrole, $exclude_user_id, (isset($_POST['transfer_reason']) ? ($_POST['transfer_reason'] == 'due_to_leave' ? '' : $_POST['transfer_reason']) : ''));
		}
	//}
	
}
	/*$ss = $myPDO->query("SELECT formation_id FROM charges_table WHERE user_id=".$_SESSION['sess_user_id']." && charge_status=1");
	$row = $ss->fetch(PDO::FETCH_ASSOC);
	$not_allowed = $row['formation_id'];*/
echo json_encode(array(
					'popu' => $popu,
					'not_allowed' => $not_allowed,
					'authority' => $authority,
				), JSON_UNESCAPED_SLASHES);
?>