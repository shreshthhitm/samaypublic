<?php
include 'init.php';

 
$requiredRole = USER_ACCESS;
if (! isAuthorized($requiredRole) || $_SESSION['sess_userrole'] == 'manager' || $_SESSION['sess_userrole'] == 'board') {
  header('Location: dashboard.php');
}

include ROOT_DIR_COMMON.'header.php';

$msg = "";
$php_self = htmlspecialchars($_SERVER['PHP_SELF']);
$page_name = 'User';
if(isset($_GET['del']) && is_numeric($_GET['del']) && $_GET['del'] != 0) {
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$del_query1 = "UPDATE commissioners SET contact_no='' WHERE user_id=".$_GET['del'];
	$myPDO->query($del_query1);
	$del_query = "UPDATE users SET is_active=0 WHERE id=".$_GET['del'];
	if($myPDO->query($del_query)) {
		echo "<script>\n"; 
		echo "    window.location.href = '".ROOT_URL."/all_users.php?delete=1';\n"; 
		echo "</script>\n";
	} else {
		header("location: all_users.php?role=".$_GET['role']);
	}
}
if(isset($_GET['edit']) && is_numeric($_GET['edit']) && $_GET['edit'] != 0) {
	$edit = htmlspecialchars($_GET['edit']);
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt_edit = $myPDO->query("SELECT *, A.id AS user_main_id FROM users A,commissioners WHERE A.id=commissioners.user_id AND A.id=".$edit);
	if($stmt_edit->rowCount() > 0) {
		$row_edit = $stmt_edit->fetch(PDO::FETCH_ASSOC);
	} else {
		header("location: all_users.php?role=manager");
	}
}

if(isset($_POST['submit_form'])){
	$_SESSION['verify_otp'] = 0;
	$_SESSION['verify_otp_email'] = 0;
	$c_name = $_POST['c_name'];
	$officer_name = $_POST['officer_name'];
	//$op_name = $_POST['op_name'];
	//$c_addr = $_POST['c_addr'];
	$email = $_POST['email'];
	$contact_no = $_POST['form_phone2'];
	//$no_of_batches = $_POST['no_of_batches'];
	$username = $_POST['username'];
	
	if($_SESSION['sess_userrole'] == 'cc_user'){
		$role = 'manager';
		//$formation_id = $_SESSION['sess_user_id'];
	}else{
		$role = $_POST['role'];
		//$formation_id = $_POST['formation_id'];
	}
	$formation_id = $_POST['formation_id'];
	$created_at = date('Y-m-d H:i:s');
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
	if($_POST['c_id'] != ''){
		$c_id = $_POST['c_id'];
		$q0 = "UPDATE users SET username='".$username."', email='".$email."'";
		$q0 .= " WHERE id=".$c_id;
		$myPDO->query($q0);
		$name_upd = 0;
		$q = "UPDATE commissioners SET officer_name='".$officer_name."' WHERE user_id=".$c_id;
	}else{
	
		//$q = "INSERT INTO products (code, name, prod_desc, unit, qty, price, date) VALUES (?,?,?,?,?,?,?)";
		$q0 = "INSERT INTO users (username, email, role, is_active) VALUES ('".$username."', '".$email."', '".$role."',1)";
		$myPDO->query($q0);
		$last_id = $myPDO->lastInsertId();
		$q = "INSERT INTO commissioners (user_id, officer_name, contact_no) VALUES (".$last_id.", '".$officer_name."', '".$contact_no."')";
		//echo $q;
		//$myPDO->query($q);
		$myPDO->query("INSERT INTO charges_table(formation_id, user_id, charge_status, created_at) VALUES(".$formation_id.", ".$last_id.", 1, '".$created_at."')");
	}
		
	if ($myPDO->query($q)) {
		$sms_msg = (ENV == 'PROD' ? $sms_mob['linked'] : '');
		sendSMS($contact_no, $template_id['linked'], $sms_msg);
		send_mail_function($email, $strSubject_link, $mail_msg_linked, $site_email, $site_name);
		if($password != ''){
			//send_mail_function($email, $strSubject_pwd_upd, $mail_msg_pwd_upd, $site_email, $site_name);
		}
		//echo "<script type= 'text/javascript'>alert('New Record Inserted Successfully');</script>";
		//$msg="<div class='panel-heading' style='color: green;text-align: center;background: transparent;'>Product Inserted Successfully</div>";
		//$post_status = 0;
		echo "<script>\n"; 
		echo "    window.location.href = '".$php_self."?".((isset($edit)) ? "edit=".$edit."&update" : "insert" )."=1".((isset($edit) && $password != '') ? "&pwd=y" : "")."';\n"; 
		echo "</script>\n";

	}else{
		//echo "<script type= 'text/javascript'>alert('Data not successfully Inserted.');</script>";
		$msg="<div class='panel-heading' style='color: red;text-align: center;background: transparent;'>Some error occurred while adding the product</div>";
		//$post_status = 1;
	}
	
	//echo '<meta http-equiv="refresh" content="1;url='.$_SERVER['PHP_SELF'].'">';
}
//error_reporting(E_ALL); ini_set('display_errors',1);
?>
		<!-- ============================================================== -->
        <!-- Page Content -->
        <!-- ============================================================== -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title"><?php if(isset($edit)){ echo "Edit"; }else{ echo "Add New"; } ?> <?=$page_name;?></h4>
					</div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-info">
                            <div class="panel-heading"><?php if(isset($edit)){ echo "Edit"; }else{ echo "Add New"; } ?> <?=$page_name;?></div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
									
                                    <form action="" class="form-horizontal" name="<?=(isset($edit) ? '' : 'contact_form');?>" enctype="multipart/form-data" method="post" data-toggle="validator">
                                        <div class="form-body">
                                            <h3 class="box-title">General Information</h3>
											<hr class="m-t-0 m-b-40">
									
                                            <div class="row">
												<?php
													if($_SESSION['sess_userrole'] == 'admin'|| $_SESSION['sess_userrole'] == 'cc_user'){
												?>
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Role<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <select onChange="getFormations(this.value);" class="form-control" id="role" name="role" <?=(isset($edit) ? 'disabled readonly' : '');?>>
                                                                <?php
																if(isset($edit)){
																	echo user_role_filtered_populate($row_edit['role']);
																}else{
																	echo user_role_filtered_populate();
																}
																?>
                                                            </select>
															<span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
												<?php if(!isset($edit)){ ?>
												<div class="col-md-6 show_cc">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Office Name<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <select id="formation_id" name="formation_id" class="form-control" required>
                                                                <?php
																if(isset($edit)){
																	echo formation_populate($row_edit['formation_id'], $row_edit['role']);
																}else{
																	//echo formation_populate('','cc_user');
																	echo '<option value="" disabled="disabled" selected="selected">Select Formation*</option>';
																}
																?>
                                                            </select>
															<span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
												<?php
													}else{ ?>
												<div class="col-md-6 show_cc">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Charges<span class="red"></span></label>
                                                        <div class="col-md-8">
                                                            <div class="" style="width: 100%; border: 1px solid gray; padding: 7px 12px;"><?=(!empty(html_entity_decode(charges_by_user($edit))) ? html_entity_decode(charges_by_user($edit)) : '<span class="text-danger">No Charge Assigned</span>');?></div>
															<span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
												<?php
													}												
												}
												?>
											</div>
											<div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Officer's Name<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" value="<?php if(isset($edit)){ echo $row_edit['officer_name']; } ?>" placeholder="" id="officer_name" name="officer_name" required> <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">SSO-ID<span class="red"></span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" value="<?php if(isset($edit)){ echo $row_edit['username']; } ?>" placeholder="" onkeypress="return IsNumeric(event);" maxlength="8" id="username" name="username"> <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Email<span class="red">*</span></label>
                                                        <div class="col-md-8">
															<input type="email" class="form-control" value="<?php if(isset($edit)){ echo $row_edit['email']; } ?>" placeholder="" id="email" name="email" required> <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
                                                <!--/span-->                                                
                                            </div>
                                            <!--/row-->
                                            <div class="row">
												<?php if(!isset($edit)){ ?>
												<div class="col-md-6">
													<div class="formError"></div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Contact No.<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <!--<input type="text" class="form-control" placeholder="dd/mm/yyyy"> -->
															<input type="hidden" name="form_phone2" id="form_phone2" value="">
															<input type="text" class="form-control" value="<?php if(isset($edit)){ echo $row_edit['contact_no']; } ?>" placeholder="" id="form_phone" name="form_phone" minlength="10" maxlength="10" onkeypress='return IsNumberOnly(evt)' required>
															<button type="button" class="btn btn-primary otpBtn enterOtpBtn" onclick="sendOTP(this,'sms','yes');" name="enterOtpBtn" style="/*position: relative; margin-top: 10px; margin-right: -7px; float: right;*/ right: -100%;">Validate with OTP</button> <span class="help-block"></span>
                                                        </div>
														<div class="col-md-offset-4 col-md-8 sendOtpError"></div>
                                                    </div>
                                                </div>                                                
                                                <!--/span-->
												<?php } ?>
												<?php
													/*if($_SESSION['sess_userrole'] == 'admin'){
												?>
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Password<span class="red">*</span></label>
                                                        <div class="col-md-8">
															<input type="password" class="form-control" placeholder="" id="password" name="password" <?php if(isset($edit)){}else{ echo "required"; } ?>><?=(isset($edit) ? "Leave blank if you don't want to change password." : "");?> <span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                </div>                                               
                                                <!--/span-->
												<?php }*/ ?>
                                            </div>
                                            <!--/row-->
											
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12 text-center">
															<!--<input type="hidden" value="<?php //echo $post_status; ?>" id="post_status" name="post_status">-->
                                                            <button type="submit" id="submit" name="submit_form" class="btn btn-success"><i class="fa fa-check"></i> <?php if(isset($edit)){ echo "Update"; }else{ echo "Save"; } ?></button>
															<?php if(isset($edit) && $_SESSION['sess_userrole'] == 'admin'){ /*?><a href="<?php echo ROOT_URL; ?>/add_user.php?del=<?php echo $edit; ?>" onclick='return confirmDelete()'><button type='button' name='delete' class="btn btn-danger" value='Delete' >Delete</button></a><?php*/ } ?>
                                                            <button type="reset" class="btn btn-default">Reset</button>
															<input type="hidden" name="c_id" value="<?php if(isset($edit)){ echo $row_edit['user_main_id']; }?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6"> </div>
                                            </div>
                                        </div>
                                    </form>
									
									
									
									<?php if(isset($edit)){ ?>
                                    <form name="contact_form" action="" class="form-horizontal" enctype="multipart/form-data" method="post">
                                        <div class="form-body">
                                            <h3 class="box-title">Contact Information</h3>
                                            <hr class="m-t-0 m-b-40">
                                           
											<div class="row">
											   <div class="col-md-12">
													<div class="formError"></div>
                                                    <div class="form-group">
                                                        <label class="control-label col-md-2">Contact No.<span class="red">*</span></label>
                                                        <div class="col-md-4">
                                                            <!--<input type="text" class="form-control" placeholder="dd/mm/yyyy"> -->
															<input type="text" class="form-control" value="<?php if(isset($edit)){ echo $row_edit['contact_no']; } ?>" placeholder="" id="form_phone" name="form_phone" minlength="10" maxlength="10" onkeypress="return IsNumberOnly(event);" required />
															<button type="button" class="btn btn-primary otpBtn enterOtpBtn" onclick="sendOTP(this,'sms','no');" name="enterOtpBtn" style="/*position: relative; margin-top: 10px; margin-right: -7px; float: right;*/ right: -100%;">Validate with OTP</button>
                                                        </div>
														<div class="col-md-offset-2 col-md-8 sendOtpError"></div>
                                                    </div>
                                                </div>                                                
                                            </div>
                                            <!--/row-->
											
                                        
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12 text-center">
															<!--<input type="hidden" value="<?php //echo $post_status; ?>" id="post_status" name="post_status">-->
                                                            <button type="submit" id="submit_mobile" name="submit_mobile" class="btn btn-success"><i class="fa fa-check"></i> <?php if(isset($edit)){ echo "Update"; }else{ echo "Save"; } ?></button>
															
                                                            <button type="reset" class="btn btn-default">Reset</button>
															<input type="hidden" name="user_id" value="<?php if(isset($edit)){ echo $row_edit['user_main_id']; }?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6"> </div>
                                            </div>
                                        </div>
                                    </form>
									<?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
            <?php include 'copyright.php'; ?>
        </div>
        <!-- /#page-wrapper -->

<?php
include ROOT_DIR_COMMON.'footer.php';
include 'otpModal.php';
?>

<!--<link rel="stylesheet" href="<?php echo ROOT_URL; ?>/css/jquery-ui.css">
<script src="<?php echo ROOT_URL; ?>/js/jquery-ui.js" ></script>-->
<?php include 'contact_change_js.php'; ?>
<script type="text/javascript">
var specialKeys = new Array();
specialKeys['push'](8, 46);

function IsNumeric(key) {
    var key_code = key['which'] ? key['which'] : key['keyCode'];
    console['log'](key_code);
    var rt_key = ((key_code >= 48 && key_code <= 57) || specialKeys['indexOf'](key_code) != -1);
    return rt_key;
}
function IsNumber(key) {
    var key_code = key['which'] ? key['which'] : key['keyCode'];
    //console['log'](key_code);
    var rt_key = ((key_code >= 48 && key_code <= 57) || specialKeys['indexOf'](key_code) != -1);
    return rt_key;
}
function IsNumberOnly(key1) {
    var key_code_int = key1['which'] ? key1['which'] : key1['keyCode'];
    //console['log'](key_code);
    var rt_key_int = (key_code_int >= 48 && key_code_int <= 57);
    return rt_key_int;
}

function confirmDelete(){
   return confirm("Are you sure you want to delete this?");
}
function getFormations(val) {
	$.ajax({
	type: "POST",
	url: "<?php echo ROOT_URL; ?>/get_formations.php",
	data:'role='+val+'&id=<?=(isset($edit) ? $row_edit['formation_id'] : '');?>',
	success: function(data){
				$("#formation_id").html(data);
			}
	});
}
/*function show_checkboxes(){
	var checkbox_val = $("#role").parent().find(".sub_admin_exists");
	if($("#role").val() == "cc_user"){
		checkbox_val.prop('checked', true);
	}else{
		checkbox_val.prop('checked', false);
	}
	show_div(checkbox_val);
}*/
	
/*$(document).on('change', '#role', function(){
	//show_checkboxes();
	show_div();
});
$(window).on('load', function() {
	show_div();
});
function show_div(){
	var $showdiv = $(".show_cc");
	//$showdiv.slideToggle();
	//$('#role').find("option:selected").each(function(){
		var optionValue = $('#role').find("option:selected").attr("value");
		if(optionValue == 'manager'){
			$("#cc_id").attr('required',true);
			$showdiv.slideDown();
		} else{
			$("#cc_id").attr('required',false);
			$showdiv.slideUp();
		}
	//});
}*/
/*$('input.show_div')['on']('click', function() {
	show_div($(this));
});*/

	<?php if((isset($_GET['insert']) && $_GET['insert'] == 1 && isset($_SERVER['HTTP_REFERER'])) || (isset($_GET['update']) && $_GET['update'] == 1 && isset($_SERVER['HTTP_REFERER']))){ ?>
	$.toast({
        text: '<?php if(isset($_GET['insert'])){ echo $page_name.' Added Successfully'; }else if(isset($_GET['update'])){ if(isset($_GET['pwd']) && $_GET['pwd'] == 'y'){ echo 'Password'; }else if(isset($_GET['ph']) && $_GET['ph'] == 'y'){ echo 'Your phone have been'; }else{ echo $page_name; } echo ' Updated Successfully'; } ?>',
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
	$(document).ready(function(){
		// Init Twitter Typeahead.js
		var substringMatcher = function(strs) {
			return function findMatches(q, cb) {
				var matches, substrRegex;

				matches = [];

				substrRegex = new RegExp(q, 'i');

				$.each(strs, function(i, str) {
					if (substrRegex.test(str)) {
						matches.push({
							value: str
						});
					}
				});

				cb(matches);
			};
		};

		/*$('.add_unit #p_unit').typeahead({
			source: function (query, process) {
				console.log("shreshth");
				$.getJson('select_unit.php', {
					'query': query
				}, function(data) {
					console.log(data);
					process(data);
				});
			}
		});*/
		/*$('.add_unit .typeahead').typeahead({
		  name: 'type',
		  remote: 'select_unit.php?query=%QUERY'
		});*/
	});
	
	$(document).ready(function(){
		/*$('.add_unit #p_unit').typeahead({
			name: 'type',
			value: 'id',
			remote: 'select_unit.php?query=%QUERY',
			/*minLength: 3, */
			/*limit: 10
		});
		$(".add_unit #p_unit").autocomplete({
			source: "select_unit.php",
			select: function(event, ui) {
				$(".add_unit #p_unit").val(ui.item.label);
				$("#id_product").val(ui.item.id);
			}
		});*/
		$(".form-horizontal").submit(function(){
			/*if($("#id_product").val() == ''){
				alert("Please select a 'unit' from the suggestions.");
				return false;
			}*/
			
		});
	});
</script>
