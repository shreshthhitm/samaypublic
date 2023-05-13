<?php
define('ROOT_DIR', dirname(__FILE__));
define('ROOT_URL', substr($_SERVER['PHP_SELF'], 0, - (strlen($_SERVER['SCRIPT_FILENAME']) - strlen(ROOT_DIR))));
define('ROOT_DIR_COMMON', dirname(__FILE__).'/common/');
define('ROOT_DIR_ADMIN', dirname(__FILE__).'/admin/');
define('ROOT_DIR_IMAGES', dirname(__FILE__).'/images/');
define('ROOT_DIR_JS', dirname(__FILE__).'/js/');
define('ROOT_DIR_CSS', dirname(__FILE__).'/css/');

define('ADMIN_ACCESS', 1);
define('ADMIN_USER_ACCESS', 2);
define('USER_ACCESS', 3);

define('ENV', 'TEST');	//TEST or PROD as per your requirement

define('DB_HOST', ENV == 'PROD' ? "10.1.104.206" : "127.0.0.1");
define('DB_NAME', ENV == 'PROD' ? "samay" : "u485293523_samayuat");
define('DB_USER', ENV == 'PROD' ? "u_80007004" : "u485293523_samayuat");
define('DB_PASS', ENV == 'PROD' ? "G5hJ23#b" : "Samay@1234");
$siteurl = '';
$siteurl .= isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http';
$siteurl .= '://'.$_SERVER['SERVER_NAME'];
define('BASEURL', $siteurl);
if(in_array( $_SERVER['HTTP_HOST'], array( 'localhost', '127.0.0.1' ) )){
    $siteurl .= '/samay-uat';
}else{
    $siteurl .= (ENV == 'PROD' ? '' : '/samay-uat');
}
define('SITEURL', $siteurl);
if(ENV == 'PROD'){
    error_reporting(0);
}else{
	error_reporting(E_ALL);
}
session_start();
//include 'common/functions.php';
//Write a custom function to check user role.
	function isAuthorized($requiredRole = NULL) {
		if (session_id() == '') {
			return FALSE;
		}
		if (isset($_SESSION['role'])) {
			if ($_SESSION['role'] == ADMIN_ACCESS) {
				return TRUE; // Administrator has access to every page/functionality.
			}
			else if ($requiredRole < $_SESSION['role']) {
				return FALSE;
			}
			else{
				return TRUE;
			}
		}
		return FALSE;
	}
	

?>