<?php
include_once 'init.php';
include_once ROOT_DIR_COMMON.'functions.php';
if(!empty($_POST['role'])) {
	//global $myPDO;
	echo formation_populate((!empty($_POST['id']) ? $_POST['id'] : ''), $_POST['role']);
}
if(!empty($_POST["cc_id"])) {
	//global $myPDO;
	$comm_id = (isset($_POST['comm_id'])) ? htmlspecialchars($_POST['comm_id']) : '';
	echo formation_populate((!empty($comm_id) ? $comm_id : ''), 'manager', htmlspecialchars($_POST["cc_id"]));
}
?>
