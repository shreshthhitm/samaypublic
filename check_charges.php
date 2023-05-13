<?php
include 'init.php';
include ROOT_DIR_COMMON.'functions.php';
$formation_id = $_POST['formation_id'];
$user_id = $_POST['user_id'];
if(!empty($formation_id) && !empty($user_id)){
	$formation_role = get_value_from_id("formations","role","id",$formation_id);
	$cur_role = get_value_from_id("users","role","id",$user_id);
	$q_charges = $myPDO->query("SELECT * from charges_table WHERE user_id=".$user_id." && charge_status=1");
	if($formation_role != $cur_role && $q_charges->rowCount() > 0){
		echo "false";
	}else{
		echo "true";
	}
}
?>