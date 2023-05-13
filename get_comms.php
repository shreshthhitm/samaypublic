<?php
include_once 'init.php';
include_once ROOT_DIR_COMMON.'functions.php';

/*if(!empty($_POST['city_id'])){
	$city_id = $_POST['city_id'];
}else{*/
	//$comm_id = NULL;
//}
if(!empty($_POST["cc_id"])) {
	//global $myPDO;
	$comm_id = (isset($_POST['comm_id'])) ? $_POST['comm_id'] : '';
	$sql_populate = "SELECT user_id, name FROM commissioners WHERE parent_id=".$_POST["cc_id"]." && is_active=1 ORDER BY name ASC";
	echo '<option value="" disabled="disabled" '.((empty($comm_id)) ? 'selected="selected"' : '').'>Select '.$user_roles['manager'].'</option>';
	foreach($myPDO->query($sql_populate) as $row){
	//while ($row = mysql_fetch_row($sql_populate)  or mysql_error()) {
		echo "<option value='" . $row['user_id'] . "' ".(($comm_id == $row['user_id']) ? 'selected' : '').">" . $row['name'] . "</option>";
	}
}
?>
