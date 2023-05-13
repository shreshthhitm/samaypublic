<?php
include 'init.php';

$requiredRole = USER_ACCESS;
if (! isAuthorized($requiredRole)) {
  header('Location: dashboard.php');
}

include ROOT_DIR_COMMON.'header.php';

$msg = "";
$php_self = htmlspecialchars($_SERVER['PHP_SELF']);
$page_name = 'Transfer Charge';
/*if(isset($_GET['del']) && is_numeric($_GET['del']) && $_GET['del'] != 0) {
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
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt_edit = $myPDO->query("SELECT *, A.id AS user_main_id FROM users A,commissioners WHERE A.id=commissioners.user_id AND A.id=".$_GET['edit']);
	if($stmt_edit->rowCount() > 0) {
		$row_edit = $stmt_edit->fetch(PDO::FETCH_ASSOC);
	} else {
		header("location: all_users.php?role=manager");
	}
}*/
if(isset($_GET['edit']) && is_numeric($_GET['edit']) && $_GET['edit'] != 0) {
	$edit = htmlspecialchars($_GET['edit']);
}
if(isset($_GET['insert']) && $_GET['insert'] == 1){
	echo "<script>\n"; 
	echo "    window.location.href = '".ROOT_URL."/common/logout.php?err=6".((isset($_GET['transfer']) && $_GET['transfer'] == 1) ? '&transfer=1' : '')."';\n"; 
	echo "</script>\n";
}
if(isset($_POST['submit_form'])){
	$transfer = 0;
	if($_POST['submit_form'] == 'btn_transfer'){
		$user_id = ($_POST['transfer_reason'] == 'other_than_leave' ? $_POST['user_id'] : get_user_by_formation($_POST['user_id'], 'yes'));
		$given_to_formation = ($_POST['transfer_reason'] == 'other_than_leave' ? 0 : $_POST['user_id']);	//In case of due to leave $_POST['user_id'] is a formation id
		$formation_id = (($_SESSION['sess_userrole'] == 'admin' || ($_SESSION['sess_userrole'] == 'cc_user' && isset($_POST['role']) && $_POST['role'] != $_SESSION['sess_userrole'])) ? $_POST['formation_id'] : $_SESSION['sess_fid']);
		$charge_status = (($_SESSION['sess_userrole'] == 'admin' || ($_SESSION['sess_userrole'] == 'cc_user' && isset($_POST['role']) && $_POST['role'] != $_SESSION['sess_userrole'])) ? 1 : 0);
		$created_at = date('Y-m-d H:i:s');
		$updated_at = date('Y-m-d H:i:s');
		$transfer = 1;
		$entry = send_transfer_request($user_id, $formation_id, $given_to_formation, $charge_status, $created_at, $updated_at);
	}else if($_POST['submit_form'] == 'btn_accept'){
		$entry = accept_transfer_request($_SESSION['sess_user_id'], $_POST['formation_id']);
	}else{
		$entry = reject_transfer_request($_SESSION['sess_user_id'], $_POST['formation_id']);
	}
	
	if ($entry) {
		/*sendSMS($contact_no, $template_id['linked']);
		send_mail_function($email, $strSubject_link, $mail_msg_linked, $site_email, $site_name);
		if($password != ''){
			send_mail_function($email, $strSubject_pwd_upd, $mail_msg_pwd_upd, $site_email, $site_name);
		}*/
		//echo "<script type= 'text/javascript'>alert('New Record Inserted Successfully');</script>";
		//$msg="<div class='panel-heading' style='color: green;text-align: center;background: transparent;'>Product Inserted Successfully</div>";
		//$post_status = 0;
		echo "<script>\n";
		if($_POST['submit_form'] != 'btn_reject' || ($_POST['submit_form'] == 'btn_reject' && isset($_POST['force_logout']) && $_POST['force_logout'] == 'yes')){
			if($_SESSION['role'] == ADMIN_ACCESS || ($_SESSION['sess_userrole'] == 'cc_user' && get_value_from_id("users","role","id",$user_id) == 'manager')){
				echo "    window.location.href = '".$php_self."?update=1';\n";
			}else{
				echo "    window.location.href = '".$php_self."?".(isset($edit) ? "edit=".$edit."&update" : "insert" )."=1".($transfer == 1 ? "&transfer=1" : "")."';\n";
			}
		}else{
			echo "    window.location.href = 'dashboard.php';\n";
		}
		echo "</script>\n";

	}else{
		//echo "<script type= 'text/javascript'>alert('Data not successfully Inserted.');</script>";
		$msg="<div class='panel-heading' style='color: red;text-align: center;background: transparent;'>Some error occurred while adding the product</div>";
		//$post_status = 1;
	}
	
	//echo '<meta http-equiv="refresh" content="1;url='.$php_self.'">';
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
                        <h4 class="page-title"><?php if($_SESSION['sess_userrole'] == 'admin'){ echo "Add New ".$page_name; }else{ echo "Request for Charge Transfer"; } ?></h4>
					</div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <!--<a href="https://wrappixel.com/templates/ampleadmin/" target="_blank" class="btn btn-danger pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">Upgrade to Pro
                        </a>-->
                        <!--<ol class="breadcrumb">
                            <li><a href="#">Dashboard</a></li>
                            <li class="active">All Products</li>
                        </ol>-->
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /row -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-info">
                			<div class="panel-heading"><?php if($_SESSION['sess_userrole'] == 'admin'){ echo "Add New ".$page_name; }else{ echo "Request for Charge Transfer"; } ?></div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
									
                                    <form action="" class="form-horizontal" name="<?=(isset($edit) ? '' : 'contact_form');?>" enctype="multipart/form-data" method="post" data-toggle="validator">
                                        <div class="form-body">
                                            <!--<h3 class="box-title">General Information</h3>
											<hr class="m-t-0 m-b-40">-->
									
                                            <div class="row">
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Select Reason<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" id="transfer_reason" name="transfer_reason" <?=(isset($edit) ? 'disabled readonly' : '');?> required>
                                                                <?php
																if(isset($edit)){
																	echo transfer_reason_populate($row_edit['role']);
																}else{
																	echo transfer_reason_populate();
																}
																?>
                                                            </select>
															<span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
												<?php
													if($_SESSION['sess_userrole'] == 'admin' || $_SESSION['sess_userrole'] == 'cc_user'){
												?>
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Role<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <select onChange="getCommsWrtRole(this, this.value);" class="form-control" id="role" name="role" <?=(isset($edit) ? 'disabled readonly' : '');?> required>
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
												<!--<div class="col-md-12 m-b-20 text-center">
													<span class="font-bold text-danger">Note: If you want to transfer <u class="text-muted">BACK</u> the charge to anyone, then select same charge on both sides below!</span>
                                                </div>-->
												<div class="col-md-6 show_cc">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4"><span class="assign_label">Transfer Charge from</span><span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <select onChange="getCommsWrtRole(this, this.value, 'exclude');" id="formation_id" name="formation_id" class="form-control" required>
                                                                <option>--Select--</option>
                                                            </select>
															<span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
												<?php
													}
												?>
												<div class="col-md-6 show_cc">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Assign Charge to<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <select id="user_id" name="user_id" class="form-control" required>
																<?php
																//if($_SESSION['sess_userrole'] == 'admin' || $_SESSION['sess_userrole'] == 'cc_user'){ ?>
																	<option>--Select--</option>
																<?php /*}else{
																	echo commissioner_to_transfer_populate('',$_SESSION["sess_userrole"]);
																}*/ ?>
                                                            </select>
															<span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
											</div>
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12 text-center">
															<!--<input type="hidden" value="<?php //echo $post_status; ?>" id="post_status" name="post_status">-->
                                                            <button type="submit" id="submit" name="submit_form" class="btn btn-success" value="btn_transfer"><i class="fa fa-check"></i> <?php if(isset($edit)){ echo "Update"; }else{ echo "Save"; } ?></button>
															<?php /*if(isset($edit) && $_SESSION['sess_userrole'] == 'admin'){ ?><a href="<?php echo ROOT_URL; ?>/add_user.php?del=<?php echo $edit; ?>" onclick='return confirmDelete()'><button type='button' name='delete' class="btn btn-danger" value='Delete' >Delete</button></a><?php }*/ ?>
                                                            <button type="reset" class="btn btn-default">Reset</button>
															<input type="hidden" name="c_id" value="<?php if(isset($edit)){ echo $row_edit['user_main_id']; }?>"/>
															<input type="hidden" name="not_allowed" id="not_allowed" value=""/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6"> </div>
                                            </div>
                                        </div>
                                    </form>
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
<?php //include 'contact_change_js.php'; ?>
<script type="text/javascript">
var specialKeys = new Array();
specialKeys['push'](8, 46);

function IsNumeric(key) {
    var key_code = key['which'] ? key['which'] : key['keyCode'];
    console['log'](key_code);
    var rt_key = ((key_code >= 48 && key_code <= 57) || specialKeys['indexOf'](key_code) != -1);
    return rt_key;
}

function confirmDelete(){
   return confirm("Are you sure you want to delete this?");
}
$("#transfer_reason").on('change', function(){
	transfer_reason();
});
function transfer_reason(){
	var val = $("#transfer_reason").find("option:selected").val();
	if(val == 'due_to_leave'){
		$(".assign_label").text("Transfer Charge from");
	}else{
		$(".assign_label").text("Assign Charge of");
	}
	if($("#role").length){	//For Admin or CC
		console.log("triggered admin");
		getCommsWrtRole('#role', '');
	}else if($("#formation_id").length){	//For Comm & Board
		console.log("inside com");
		getCommsWrtRole('#formation_id', $('#formation_id').val(), 'exclude');
	}else{
		console.log("inside transfer reason");
		var transfer_reason = $("#transfer_reason").find("option:selected").val();
		$.ajax({
			type: "POST",
			url: "<?php echo ROOT_URL; ?>/get_comms_wrt_role.php",
			data:'userrole=yes&transfer_reason='+transfer_reason,
			success: function(result){
						var data = JSON.parse(result);
						var htmlData = data.popu;
						$("#user_id").html(htmlData);
						check_blank_comm(data.not_allowed, data.authority);
						//check_cross_transfer(data.not_allowed, data.authority);
					}
		});
	}
	$("#user_id").html("<option value=''>Select</option>");
}
function getCommsWrtRole(elem, val, exclude) {
	var role = $("#role").val();
	var transfer_reason = $("#transfer_reason").find("option:selected").val();
	var this_id = $(elem).attr('id');
	var exclude = (exclude == 'exclude' ? val : '');
	if(role != ''){
		$.ajax({
		type: "POST",
		url: "<?php echo ROOT_URL; ?>/get_comms_wrt_role.php",
		//async: false,
		data:'elem='+this_id+'&role='+role+'&exclude='+exclude+'&transfer_reason='+transfer_reason,
		success: function(result){
					var data = JSON.parse(result);
					var htmlData = data.popu;
					if($(elem).attr('id') == 'role'){
						$("#formation_id").html(htmlData);
					}else{
						$("#user_id").html(htmlData);
						check_blank_comm(data.not_allowed, data.authority);
						//check_cross_transfer(data.not_allowed, data.authority);
					}
				}
		});
		$("#user_id").html("<option value=''>Select</option>");
	}else{
		$("#formation_id, #user_id").html("<option value=''>Select</option>");
	}
}


function check_blank_comm(){
	//console.log("here");
	$("form[name='contact_form']").on('submit', function(){
		var valid = false;
		var user_id = '<?=$_SESSION['sess_user_id'];?>';
		var selected_id = $("#user_id").val();
		var selected = $(this).find("#user_id").find('option:selected').text();
		var transfer_reason = $("#transfer_reason").find("option:selected").val();
		if(transfer_reason == 'due_to_leave'){
			if(selected_id != ''){
				$.ajax({
				type: "POST",
				url: "<?php echo ROOT_URL; ?>/check_cc_empty.php",
				async: false,
				data:'formation_id='+selected_id,
				success: function(result){
							if(result == 'true'){
								valid = true;
							}else{
								alert("ALERT: Charge cannot be transferred to \""+selected+"\" (as no user is mapped to aforesaid field formation).");
								valid = false;
							}
						}
				});
			}
		}else{
			if(selected_id ==''){
				valid = false;
				alert("ALERT: Charge cannot be transferred to \""+selected+"\" (as no user is mapped to aforesaid field formation).");
			}else{
				if(selected_id == user_id){
					valid = false;
					alert("Error! The charge for the office: \""+selected+"\" is already with you. Please try selecting other office.!");
				}else{
					valid = true;
				}
			}
		}
		return valid;
	});
}

function check_cross_transfer(val, auth){
	//console.log("here");
	$("form[name='contact_form']").on('submit', function(){
		var valid = false;
		var selected = $("#user_id").val();
		if(selected !='' && selected == val){
			valid = false;
			alert("You can\'t transfer your charge to the officer whose charge is already with you!\nPlease contact "+auth+"!");
		}else{
			valid = true;
		}
		return valid;
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
        text: '<?php if(isset($_GET['insert'])){ echo $page_name.' Assigned Successfully'; }else if(isset($_GET['update'])){ echo ' Updated Successfully'; } ?>',
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
	
</script>