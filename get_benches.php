<?php
include 'init.php';
include ROOT_DIR_COMMON.'functions.php';
global $courts_assoc;
if(!empty($_POST["court"])) {
	echo bench_populate($_POST["court"], (!empty($_POST['bench_id']) ? $_POST['bench_id'] : ''));
}
?>
