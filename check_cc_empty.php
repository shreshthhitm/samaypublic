<?php
include_once 'init.php';
include_once ROOT_DIR_COMMON.'functions.php';
$user_id = get_user_by_formation($_POST['formation_id'], 'yes');
if(!empty($user_id)){
	echo "true";
}else{
	echo "false";
}
?>