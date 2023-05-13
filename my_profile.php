<?php
include 'init.php';

$requiredRole = USER_ACCESS;
if (! isAuthorized($requiredRole)) {
  header('Location: dashboard.php');
}

include ROOT_DIR_COMMON.'header.php';
$page_name = 'Profile';
$php_self = sanitize($_SERVER['PHP_SELF'], 'url');
$username = sanitize(get_value_from_id('users', 'username', 'id', $user_id), 'string');
$email = sanitize(get_value_from_id('users', 'email', 'id', $user_id, $separator=','), 'email');
if($_SESSION['sess_userrole'] == 'admin'){
		//$centre = $company_name;
		//$address = $company_address;
		$role = 'admin';
		$contact = $company_phone;
		$person = $contact_person;
}else{
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt_info = $myPDO->query("SELECT * FROM users,commissioners WHERE users.id=commissioners.user_id AND users.id=".$user_id);
	if($stmt_info->rowCount() > 0) {
		$row_info = $stmt_info->fetch(PDO::FETCH_ASSOC);
		//$centre = $row_info['name'];
		//$address = $row_info['centre_address'];
		$role = $user_roles[$userrole];
		$contact = $row_info['contact_no'];
		$person = $row_info['officer_name'];
	} else {
		header("location: dashboard.php");
	}
}

if(isset($_POST['submit_profile'])){
	//$c_name = $_POST['c_name'];
	//$op_name = $_POST['op_name'];
	//$c_addr = $_POST['c_addr'];
	$email = sanitize($_POST['email'], 'email');
	$form_phone2 = sanitize($_POST['form_phone2'], 'string');
	$contact_no = (isset($form_phone2) && $form_phone2 != '') ? $form_phone2 : sanitize($_POST['form_phone'], 'string');
	//$no_of_batches = $_POST['no_of_batches'];
	//$username = $email;
	$password = sanitize($_POST['password'], 'string');
	$user_id = sanitize($_SESSION['sess_user_id'], 'int');
	
	//$c_date = date('Y-m-d H:i:s');
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
	
	$q0 = "UPDATE users SET email='".$email."'";
	if($password != ''){
		//$q0 .= ", password='".md5($password)."'";
	}
	//$q0 .= ", role='manager'";
	$q0 .= " WHERE id=".$user_id;
	$myPDO->query($q0);
	
	$saved = 0;
	if($_SESSION['sess_userrole'] == 'admin'){
		$old_contact_no = get_value_from_id("settings","option_value","option_name","admin_phone");
		//$q_name = "UPDATE settings SET option_value='".$c_name."' WHERE option_name='admin_name'";
		//$myPDO->query($q_name);
		$q_phone = "UPDATE settings SET option_value='".$contact_no."' WHERE option_name='admin_phone'";
		$myPDO->query($q_phone);
		$saved = 1;
	}else{
		$old_contact_no = get_value_from_id("commissioners","contact_no","user_id",$user_id);
		//$q = "UPDATE commissioners SET name='".$c_name."', contact_no='".$contact_no."' WHERE user_id=".$user_id;
		$q = "UPDATE commissioners SET contact_no='".$contact_no."' WHERE user_id=".$user_id;
		$myPDO->query($q);
		$saved = 1;
	}
		
	if ($saved == 1) {
		if(isset($form_phone2) && $form_phone2 != ''){
			sendSMS($contact_no, $template_id['linked'], $sms_mob['linked']);
			sendSMS($old_contact_no, $template_id['delinked'], $sms_mob['delinked']);
			//send_mail_function($email, $strSubject_link, $mail_msg_linked, $site_email, $site_name);
			//send_mail_function($email, $strSubject_delink, $mail_msg_delinked, $site_email, $site_name);
			send_mail_function($email, $strSubject_mobile_upd, $mail_msg_mobile_upd, $site_email, $site_name);
		}
		if($password != ''){
			//send_mail_function($email, $strSubject_pwd_upd, $mail_msg_pwd_upd, $site_email, $site_name);
		}
		//echo "<script type= 'text/javascript'>alert('New Record Inserted Successfully');</script>";
		//$msg="<div class='panel-heading' style='color: green;text-align: center;background: transparent;'>Product Inserted Successfully</div>";
		//$post_status = 0;
		echo "<script>\n"; 
		echo "    window.location.href = '".$php_self."?update=1';\n"; 
		echo "</script>\n";

	}
	else{
		//echo "<script type= 'text/javascript'>alert('Data not successfully Inserted.');</script>";
		$msg="<div class='panel-heading' style='color: red;text-align: center;background: transparent;'>Some error occurred while adding the product</div>";
		//$post_status = 1;
	}
	
	//echo '<meta http-equiv="refresh" content="1;url='.$_SERVER['PHP_SELF'].'">';
}
?>
		<!-- ============================================================== -->
        <!-- Page Content -->
        <!-- ============================================================== -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">Profile page</h4> </div>
                    </div>
                <!-- /.row -->
                <!-- .row -->
                <div class="row">
                    <div class="col-md-12 col-xs-12">
                        <div class="white-box">
                            <form action="" class="form-horizontal form-material" name="contact_form" enctype="multipart/form-data" method="post" data-toggle="validator">
                                <div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-12">Role</label>
											<div class="col-md-12">
												<input type="text" disabled placeholder="" class="form-control form-control-line" value="<?=ucfirst($role);?>"> </div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label class="col-md-12">SSO-ID</label>
											<div class="col-md-12">
												<input type="text" disabled placeholder="" class="form-control form-control-line" value="<?=ucfirst($username);?>"> </div>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="c_name" class="col-md-12">Name<span class="red">*</span></label>
											<div class="col-md-12">
												<input type="text" placeholder="" class="form-control form-control-line" name="c_name" id="c_name" value="<?=$person;?>" readonly disabled> </div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="email" class="col-md-12">Email<span class="red">*</span></label>
											<div class="col-md-12">
												<input type="email" placeholder="email" class="form-control form-control-line" name="email" id="email" value="<?=$email;?>"> </div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="mobile" class="col-md-12">Mobile<span class="red">*</span></label>
											<div class="col-md-12">
												<input type="hidden" name="form_phone2" id="form_phone2" value="">
												<input type="text" placeholder="" class="form-control form-control-line" name="form_phone" id="form_phone" value="<?=$contact;?>" minlength="10" maxlength="10" onkeypress='return event.charCode >= 48 && event.charCode <= 57' required>
												<!--<button type="button" class="btn btn-primary otpBtn enterOtpBtn" onclick="sendOTP(this,'sms','yes');" name="enterOtpBtn">Send OTP</button>-->
											</div>
											<div class="col-md-8 sendOtpError"></div>
										</div>
									</div>
									<div class="col-md-6">
										<!--<div class="form-group">
											<label class="col-md-12">Phone No</label>
											<div class="col-md-12">
												<input type="text" placeholder="123 456 7890" class="form-control form-control-line"> </div>
										</div>-->
										
									</div>
								</div>
								<div class="form-group">
                                    					<div class="col-sm-12 text-center">
										<input type="hidden" name="user_id" value="<?=$_SESSION['sess_user_id'];?>">
                                        <button type="submit" id="submit" name="submit_profile" class="btn btn-success"><i class="fa fa-check"></i> Update Profile</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
			<?php include 'copyright.php'; ?>
        </div>
        <!-- ============================================================== -->
        <!-- End Page Content -->
        <!-- ============================================================== -->
		
		<?php
		include ROOT_DIR_COMMON.'footer.php';
		include 'otpModal.php';
		?>

<?php include 'contact_change_js.php'; ?>
<script type="text/javascript">	
	<?php if((isset($_GET['insert']) && $_GET['insert'] == 1 && isset($_SERVER['HTTP_REFERER'])) || (isset($_GET['update']) && $_GET['update'] == 1 && isset($_SERVER['HTTP_REFERER']))){ ?>
	$.toast({
        text: '<?php if(isset($_GET['insert'])){ echo $page_name.' Inserted Successfully'; }else if(isset($_GET['update'])){ echo $page_name.' Updated Successfully'; } ?>',
        heading: 'Success',
        showHideTransition: 'slide',
        allowToastClose: true,
        hideAfter: 3000,
        loader: true,
        loaderBg: '#6dce8e',
        stack: 5,
        position: 'top-center',
        bgColor: '#148d3d',
        textColor: '#fff',
        textAlign: 'left',
        icon: false,
        beforeShow: function () {},
        afterShown: function () {},
        beforeHide: function () {},
        afterHidden: function () {}
    });
	<?php } ?>
</script>
