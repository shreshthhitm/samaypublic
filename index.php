<?php
	include_once 'init.php';
    include_once ROOT_DIR_COMMON.'functions.php';
	$php_self = sanitize($_SERVER['PHP_SELF'], 'url');
	if(isset($_SESSION['role']) && $_SESSION['role'] != ''){
		header('Location: dashboard.php');
	}
	if(isset($_GET['user_id']) && $_GET['user_id'] != "" && isset($_GET['key']) && $_GET['key'] != ""){
        $user_id = sanitize($_GET['user_id'], 'int');
        $active_key = sanitize($_GET['key'], 'string');
        $myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$stmt_edit = $myPDO->query("SELECT * FROM users WHERE id='$user_id' AND active_key = '$active_key'");
        if($stmt_edit->rowCount() == 0) {
          header("Location: index.php?err=5");
        }
    }else if(isset($_GET['user_id']) || isset($_GET['key'])){
		header("Location: index.php?err=5");
	}
	
	$username = "";
	$password = "";
 
	if(isset($_POST['username']) && isset($_POST['password'])){
		$username = $_POST['username'];
		//$password = md5($_POST['password']);
		$password = $_POST['password'];

		$check_email = Is_email($username);
		if($check_email){
			// email & password combination
			//$query = mysql_query("SELECT * FROM `users` WHERE `email` = '$name' AND `password` = '$pass'");
			//$q = 'SELECT * FROM users WHERE email=? AND password=?';
			$q = 'SELECT * FROM users WHERE email=?';
			$query = $myPDO->prepare($q);
			//$result = $query->execute(array($username,$password));
			$result = $query->execute(array($username));
		} else {
			// username & password combination
			//$query = mysql_query("SELECT * FROM `users` WHERE `username` = '$name' AND `password` = '$pass'");
			//$q = 'SELECT * FROM users WHERE username=? AND password=?';
			$q = 'SELECT * FROM users WHERE username=?';
			$query = $myPDO->prepare($q);
			//$result = $query->execute(array($username,$password));
			$result = $query->execute(array($username));
		}
		
		//$result = $myPDO->query($q);
		//print_r($query->rowCount());
		if($query->rowCount() == 0){
			header('Location: index.php?err=1');
		}else{
			$row = $query->fetch();
			
			if($row['is_active'] == 0){
				header('Location: index.php?err=3');
			}else if($row['is_active'] == 1 && auth_user($row['username'],$password)){
				$stmt_charges = $myPDO->query("SELECT * FROM charges_table WHERE user_id=".$row['id']." && charge_status=1");
				if($row['role'] == "admin" || $stmt_charges->rowCount() == 1){
					$row_charges = $stmt_charges->fetch();
					session_regenerate_id();
					$_SESSION['sess_user_id'] = $row['id'];
					$_SESSION['sess_username'] = $row['username'];
					$_SESSION['sess_userrole'] = $row['role'];
					$_SESSION['sess_fid'] = $row_charges['formation_id'];
					if($row['role'] == "admin"){
						$_SESSION['role'] = ADMIN_ACCESS; // Admin (Any one of three)
					}
					if($row['role'] == "admin_user"){
						$_SESSION['role'] = ADMIN_USER_ACCESS; // User Admin (Any one of three)
					}
					if($row['role'] == "manager"){
						$_SESSION['role'] = USER_ACCESS; // User (Any one of three)
					}
					if($row['role'] == "cc_user"){
						$_SESSION['role'] = USER_ACCESS; // User (Any one of three)
					}
					if($row['role'] == "board"){
						$_SESSION['role'] = USER_ACCESS; // User (Any one of three)
					}
					
					//echo $_SESSION['sess_userrole'];
					session_write_close();

					//if( $_SESSION['sess_userrole'] == "admin"){
					if( isset($_SESSION['sess_userrole'])){
						header('Location: dashboard.php');
					}
				}else{
					$_SESSION['sess_user_id'] = $row['id'];
					header('Location: choose_charge.php');
				}
			}else{
				header('Location: index.php?err=1');
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="./images/favicon.png">
    <title>SAMAY Reporting Module</title>
    <!-- Bootstrap Core CSS -->
    <link href="bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Menu CSS -->
    <link href="./bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
    <!-- animation CSS -->
    <link href="css/animate.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
    <!-- color CSS -->
    <link href="css/colors/default.css" id="theme" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="fix-header">
    <!-- ============================================================== -->
    <!-- Preloader -->
    <!-- ============================================================== -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>
    <!-- ============================================================== -->
    <!-- Wrapper -->
    <!-- ============================================================== -->
    <div id="wrapper">
        
        <!-- ============================================================== -->
        <!-- Page Content -->
        <!-- ============================================================== -->
        <div id="login_page">
			<!--
				you can substitue the span of reauth email for a input with the email and
				include the remember me checkbox
				-->
			<div class="container">
				<div class="card card-container">
					<!-- <img class="profile-img-card" src="//lh3.googleusercontent.com/-6V8xOA6M7BA/AAAAAAAAAAI/AAAAAAAAAAA/rzlHcD0KYwo/photo.jpg?sz=120" alt="" /> -->
					<img id="profile-img" class="profile-img-card" src="./images/logo.png" />
					<p id="profile-name" class="profile-name-card"></p>
					<?php
						$error_id = isset($_GET['err']) ? (int)$_GET['err'] : 0;

						if ($error_id >= 1) {
							echo '<p class="text-danger">'.$errors[$error_id].'</p>';
						}
				   ?>  
					<form class="form-signin" method="POST">
						<span id="reauth-email" class="reauth-email"></span>
						<!--<input type="email" id="inputEmail" class="form-control" placeholder="Username" required autofocus>-->
						<input type="text" id="inputEmail" class="form-control" placeholder="Username" name="username" required>
						<div class="" style="position: relative;">
							<input type="password" id="inputPassword" class="form-control" placeholder="Password" name="password" required>
							<button type="button" id="showPassword" class="btn show_password m-0" data-show="0"><i class="fa fa-eye" aria-hidden="true"></i></button>
						</div>
						<!--<div id="remember" class="checkbox">
							<label>
								<input type="checkbox" value="remember-me"> Remember me
							</label>
						</div>-->
						<button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Sign in</button>
					</form><!-- /form -->
					<!--<a href='javascript:;' class='forgot-password' data-toggle='modal' data-target='#forgotPasswordModal' data-id='".$order_id."' style='cursor: pointer;position: relative;'>
						Forgot password?
					</a>-->
				</div><!-- /card-container -->
			</div><!-- /container -->
			
			<div class="modal fade" id="forgotPasswordModal" >
				<div class="modal-dialog printableArea" id="printableArea">
					<div class="modal-content">
						<!-- Modal Header -->
						<div class="modal-header">
							<div class="row register-form">
								<h4 class="modal-title text-center">Forgot Password</h4>
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							</div>
						</div>
						<div class="modal-body">
							<div class="alert alert-danger font-bold error"></div>
							<div class="alert alert-success success"></div>
							
							<form class="form-horizontal forgot_form" name="forgot_form" action="" method="post" enctype="multipart/form-data" novalidate="novalidate" >
								<div class="form-group row">
									<div class="col-md-12">
										
									</div>
								</div>
								<div class="form-group row row_change_status">
									<label class="control-label col-xs-4">
										<span class="label_span">Email-ID</span><span class="red">*</span>:
									</label>
									<div class="col-xs-8">
										<input name="forgot_email" placeholder="" class="form-control" type="email" required="" autofocus>
									</div>
								</div>
								<div class="form-group row print_row">
									<div class="col-md-12 text-center">
										<button type="submit" id="submit_forgot" name="submit_forgot" class="btn btn-success" >Submit</button>
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php if(isset($user_id) && $user_id != "" && isset($key) && $key != ''){ ?>
			<div class="modal fade" id="resetPasswordModal" >
				<div class="modal-dialog printableArea" id="printableArea">
					<div class="modal-content">
						<!-- Modal Header -->
						<div class="modal-header">
							<div class="row register-form">
								<h4 class="modal-title text-center">Reset Password</h4>
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							</div>
						</div>
						<div class="modal-body">
							<div class="alert alert-danger font-bold error"></div>
							<div class="alert alert-success font-bold success"></div>
							
							<form class="form-horizontal reset_form" name="reset_form" action="" method="post" enctype="multipart/form-data" novalidate="novalidate" >
								<div class="form-group row">
									<div class="col-md-12">
										
									</div>
								</div>
								<div class="form-group row row_change_status">
									<label class="control-label col-xs-4">
										<span class="label_span">Password</span><span class="red">*</span>:
									</label>
									<div class="col-xs-8">
										<input name="form_password" id="form_password" placeholder="" class="form-control" type="password" required="" autofocus>
									</div>
								</div>
								<div class="form-group row row_change_status">
									<label class="control-label col-xs-4">
										<span class="label_span">Confirm Password</span><span class="red">*</span>:
									</label>
									<div class="col-xs-8">
										<input name="form_password1" id="form_password1" placeholder="" class="form-control" type="password" required="" autofocus>
									</div>
								</div>
								<div class="form-group row print_row">
									<div class="col-md-12 text-center">
										<input type="hidden" name="user_id" id="user_id" value="<?=$user_id;?>">
										<input type="hidden" name="key" id="key" value="<?=$key;?>">
										<button type="submit" id="submit_forgot" name="submit_forgot" class="btn btn-success" >Submit</button>
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
            <?php include_once 'copyright.php'; ?>
        </div>
        <!-- ============================================================== -->
        <!-- End Page Content -->
        <!-- ============================================================== -->
    </div>
    <!-- /#wrapper -->
    <!-- jQuery -->
    <script src="./bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <script src="bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Menu Plugin JavaScript -->
    <script src="./bower_components/sidebar-nav/dist/sidebar-nav.min.js"></script>
    <!--slimscroll JavaScript -->
    <script src="js/jquery.slimscroll.js"></script>
    <!--Wave Effects -->
    <script src="js/waves.js"></script>
    <!-- Custom Theme JavaScript -->
    <script src="js/custom.min.js"></script>
    <script src="js/my_custom.js"></script>
	<script type="text/javascript">
		/*$(document).ready(function() {
			if(window.location.href.indexOf('#forgotPasswordModal') != -1) {
				$('#forgotPasswordModal').modal('show');
			}
		});*/
		$(".show_password").on("click", function(){
			var showValue = $(this).attr("data-show");
			if(showValue == 0){
				$(this).attr({'data-show': 1});
				$(this).prev().attr({'type': 'text'});
				$(this).html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
			}else if(showValue == 1){
				$(this).attr({'data-show': 0});
				$(this).prev().attr({'type': 'password'});
				$(this).html('<i class="fa fa-eye" aria-hidden="true"></i>');
			}
		});
		<?php if(isset($user_id) && $user_id != "" && isset($key) && $key != ''){ ?>
			$('#resetPasswordModal').modal('show');
		<?php } ?>
		$('.modal').on('shown.bs.modal', function () {
			$(this).find('input[type!="hidden"]:first').focus();
		});
		$(".error").hide();
		$(".success").hide();
		$("form[name='forgot_form']").on('submit', function (e) {
			e.preventDefault();
			$(".error").hide();
			$(".success").hide();
			var email_input = $("input[name=forgot_email]");
			email_input.focus();
			if(email_input.val() != ''){
				$.ajax({
					type: "POST",
					url: "forgot_pwd_ajax.php",
					data:{
						email: email_input.val()
					},
					success: function(data){
						if(data == 1){
							$('#forgotPasswordModal .modal-body .success').html('A Password Reset Link has been sent to your email-id. Please check your email!');
							$("form[name='forgot_form']")[0].reset();
							$("#forgotPasswordModal .modal-body .success").show();
							window.setTimeout(function() {
								window.location.href = '<?=$php_self;?>';
							}, 2500);
						}else if(data == 0){
							$('#forgotPasswordModal .modal-body .error').html('Invalid email id. Please enter valid email id.');
							$("#forgotPasswordModal .modal-body .error").show();
						}
					}
				});
			}
		});
		
		var isConfPassResetForm = 0;
		$("form[name='reset_form']").on('submit', function(e){
			e.preventDefault();
			$(".error").hide();
			$(".success").hide();
			var pass = $('input[name="form_password"]').val();
			var confpass = $('input[name="form_password1"]').val();
			var key = $('input[name="key"]').val();

			//just to make sure once again during submit
			//if both are true then only allow submit
			if(pass == confpass){
				isConfPassResetForm = 1;
				//$("#resetPasswordModal .modal-body .error").hide();
			}
			
			if(isConfPassResetForm == 1){
				resetPasswordForm(this);
			}else{
				if(isConfPassResetForm == 0){
					$('#resetPasswordModal .modal-body .error').html('Passwords do not match!');
					$("#resetPasswordModal .modal-body .error").show();
				}
				return false;
			}
		});
		
		function resetPasswordForm(resetElem){
			$(".error").hide();
			$(".success").hide();
			var url = "reset_new_password.php";       
			if($('#form_password').val() != ''){
				$.ajax({
				type: "POST",
				url: url,
				data: $(resetElem).serialize(),
				  success: function(dataResult) {
					var dataResult = JSON.parse(dataResult);
					var link = '';
					if(dataResult.statusCode == 'success'){
					  /*if(dataResult.message == 'client'){
						  link = '../client-login/#signin';
					  }else if(dataResult.message == 'prof_client'){
						  link = '../professional-login/#signin';
					  }else{
						  link = '../login';
					  }*/
					  link = dataResult.link;
					  $("#resetPasswordModal .modal-body .success").html('Your Password reset successfully. Please wait while we redirect you to the login url! or click <a href="'+link+'">here</a>');
					  $("form[name='reset_form']")[0].reset();
					  $("#resetPasswordModal .modal-body .success").show();
					  window.setTimeout(function() {
						window.location.href = link;
					  }, 2500);
					}else{
					  $("#resetPasswordModal .modal-body .error").html('Password reset failed. Enter again.');
					  $("#resetPasswordModal .modal-body .error").show();
					}
				  }
				});
			}
			return false;
		}
	</script>
</body>

</html>
