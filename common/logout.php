<?php
   //session_start();
	include '../init.php';
	include ROOT_DIR_COMMON.'functions.php';
	if(session_destroy()) {
	   if(isset($_GET['err']) && $_GET['err'] == 6){
			echo "<script>\n"; 
			echo "    alert('".((isset($_GET['transfer']) && $_GET['transfer'] == 1) ? addslashes($transfer_noti[1]) : addslashes($transfer_noti[2]))."');\n"; 
			echo "    window.location.href = '../index.php?err=".$_GET['err']."';\n"; 
			echo "</script>\n";
		}else{
			header("Location: ../index.php");
		}
	}
?>