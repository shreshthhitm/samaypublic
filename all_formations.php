<?php
include 'init.php';
 
$requiredRole = USER_ACCESS;
if (! isAuthorized($requiredRole) || $_SESSION['sess_userrole'] == 'manager' || $_SESSION['sess_userrole'] == 'board') {
  header('Location: dashboard.php');
}

include ROOT_DIR_COMMON.'header.php';
$role = 'manager';
if(isset($_GET['role']) && $_GET['role'] != ''){
	$role = sanitize($_GET['role'], 'string');
	$page_name = $user_roles[$role];
}else{
	//$page_name = 'manager';
	//header('Location: all_users.php?role=manager');
	echo "<script>\n"; 
	echo "    window.location.href = '".ROOT_URL."/all_formations.php?role=manager';\n"; 
	echo "</script>\n";
}
$s = 0;
$page_name = 'Formation';
if(isset($_POST['submit'])){
	$centre_id = sanitize($_POST['c_id'], 'int');
	$s = 1;
}else{
	/*if($_SESSION['role'] == USER_ACCESS){
		$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}*/
	if($_SESSION['sess_userrole'] == 'cc_user'){
	    //$centre_id = get_hotel_ids_manager($_SESSION['sess_user_id']);
	    $centre_id = $formation_id;
		//$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}
}
if(isset($_POST['btn_active'])){
	$c_id = sanitize($_POST['c_id'], 'int');
	$active = (($_POST['active'] == 1) ? 0 : 1);
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
	$q0 = "UPDATE formations SET is_active=".$active." WHERE id=".$c_id;
	if ($myPDO->query($q0)) {
		//echo "<script type= 'text/javascript'>alert('New Record Inserted Successfully');</script>";
		//$msg="<div class='panel-heading' style='color: green;text-align: center;background: transparent;'>Product Inserted Successfully</div>";
		//$post_status = 0;
		echo "<script>\n"; 
		echo "    window.location.href = 'all_formations.php?update=1';\n"; 
		echo "</script>\n";

	}
}
if(isset($_POST['btn_set_user'])){
	$formation_id = sanitize($_POST['formation_id'], 'int');
	$user_id = sanitize($_POST['user_id'], 'int');
	$formation_role = get_value_from_id("formations","role","id",$formation_id);
	
	$created_at = date('Y-m-d H:i:s');
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
	if(!($user_id == '' || $user_id == 0)){
		$cur_role = get_value_from_id("users","role","id",$user_id);
		if($formation_role != $cur_role){
			//Remove all charges of this specific user himself
			$q_charges = $myPDO->query("SELECT * from charges_table WHERE user_id=".$user_id." && charge_status=1");
			if($q_charges->rowCount() > 0){
				$q_del = "DELETE FROM charges_table WHERE user_id=".$user_id;
				$myPDO->query($q_del);
			}
			$q_role = "UPDATE users SET role='".$formation_role."' WHERE id=".$user_id;
			$myPDO->query($q_role);
		}
		//Override all charges of the user
		$q0 = $myPDO->query("SELECT * from charges_table WHERE formation_id=".$formation_id." && charge_status=1");
		if($q0->rowCount() > 0){
			$row = $q0->fetch(PDO::FETCH_ASSOC);
			$old_contact = get_value_from_id("commissioners","contact_no","user_id",$row['user_id']);
			$q1 = "UPDATE charges_table SET user_id=".$user_id." WHERE formation_id=".$formation_id." && charge_status=1";
		}else{
			$q1 = "INSERT INTO charges_table (formation_id, user_id, charge_status, created_at, updated_at) VALUES(".$formation_id.", ".$user_id.", 1, '".$created_at."', '".$created_at."')";
		}
		if(ENV == 'PROD'){
			$mobile = get_value_from_id("commissioners","contact_no","user_id",$user_id).($old_contact != '' ? ','.$old_contact : '');
			$sms = 'Charge successfully transferred to '.get_value_from_id("formations","formation","id",$formation_id).' on SAMAY. -CBIC';
			sendSMS($mobile, $template_id['ct_success'], $sms);	//Sender & Receiver both
		}
	}else{
		$q1 = "DELETE FROM charges_table WHERE formation_id=".$formation_id;
		//$myPDO->query($q_del);
	}
	
	if ($myPDO->query($q1)) {
		echo "<script>\n"; 
		echo "    window.location.href = 'all_formations.php?".(isset($role) ? "role=".$role."&" : '')."update=1';\n"; 
		echo "</script>\n";

	}
}
?>
		<!-- ============================================================== -->
        <!-- Page Content -->
        <!-- ============================================================== -->
        <div id="page-wrapper" class>
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">View <?=$page_name;?>s</h4>
					</div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /row -->
				<?php if($_SESSION['role'] == ADMIN_ACCESS || $_SESSION['role'] == ADMIN_USER_ACCESS){ ?>
				<div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-info">
							<div class="panel-heading">Search All <?=$page_name;?>s</div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                    <form action="" class="form-horizontal" enctype="multipart/form-data" method="post" data-toggle="validator">
                                        <div class="form-body">
                                            <!--<h3 class="box-title">Person Info</h3>
                                            <hr class="m-t-0 m-b-40">-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Name<span class="red">*</span></label>
                                                        <div class="col-md-9">
															<select id="c_id" name="c_id" class="form-control" required>
																<?php echo formation_populate($centre_id, $role); ?>
															</select>
															 <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                                <div class="col-md-6">
                                                    <div class="form-group "><!-- use class='has-error' for error-->
                                                        <div class="col-md-8">
                                                            <button type="submit" id="submit" name="submit" class="btn btn-success">Search</button>
														</div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                            </div>
                                            <!--/row-->
										</div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<?php } ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="white-box">
                            <h3 class="box-title">View All <?=$page_name;?>s<?php /*if($s == 1){ ?> <span style="font-weight: 400; background: darkgrey; padding: 10px; text-transform: none;">(Displaying results for: <b>"<?php //echo get_value_from_id("hotels","hotel_name","id",$centre_id); ?>"</b>)</span><?php }*/ ?></h3>
                            <!--<p class="text-muted">Add class <code>.table</code></p>-->
                            <div class="table-responsive">
                                <table class="table color-table info-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <?php /*if($s != 0){ ?><th>Hotel Name</th><?php }*/ ?>
											<?php if($_SESSION['sess_userrole'] != 'cc_user'){ ?>
											<th>Role</th>
											<?php } ?>
											<th>Formation Name</th>
											<th>Assign User</th>
                                            <?php /*?><th>Status</th><?php*/ ?>
											<?php if($_SESSION['sess_userrole'] != 'cc_user'){ ?>
                                            <th>Action</th>
											<?php } ?>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									$q = 'SELECT * FROM formations WHERE role="'.$role.'"';
									if($s == 1){
										$q .= ' && id='.$centre_id;
									}
									if($_SESSION['sess_userrole'] == 'cc_user'){
										$q .= ' && parent_id='.$centre_id;
									}
									$i = 1;
									$stmt_noc = $myPDO->query($q);
									/*if($stmt_managers->rowCount() > 0) {*/
    									foreach($stmt_noc as $row){
											$sql1 = "SELECT user_id FROM charges_table WHERE formation_id=".$row['id']." && charge_status=1";
											$stmt1 = $myPDO->query($sql1);
											$user_assigned = '';
											if($stmt1->rowCount() > 0){
												$row1 = $stmt1->fetch(PDO::FETCH_ASSOC);
												$user_assigned = $row1['user_id'];
											}
											/*$is_active = "<form method='post'><input type='hidden' id='c_id' name='c_id' value='{$row['id']}' ><input type='hidden' id='active' name='active' value='{$row['is_active']}' ><button type='submit' name='btn_active' class='label label-".(($row['is_active'] == 1)? "success'>Active" : "danger'>Inactive")."</button></form>";*/
											$user_set = "<form method='post' class='update_user_form'><input type='hidden' id='formation_id' name='formation_id' value='{$row['id']}' ><input type='hidden' class='formation_name' name='formation_name' value='{$row['formation']}' ><select name='user_id' class='form-control m-r-5 user_id' style='width: auto; float: left;'>".all_user_populate($user_assigned)."</select><button type='submit' name='btn_set_user' class='btn btn-xs btn-success hide'><i class='fa fa-check'></i> Update</button></form>";
                                            echo "<tr>
    												<td>{$i}.</td>";
												if($_SESSION['sess_userrole'] != 'cc_user'){
													echo "<td><b>".$user_roles[$row['role']]."</b></td>";
												}
											echo "<td>".$row['formation']."</td>
													<td>".$user_set."</td>";
											/*echo "<td>".$is_active."</td>";*/
												if($_SESSION['sess_userrole'] != 'cc_user'){
													echo "<td><a href=add_formation.php?edit={$row['id']}  title='Update Formation'><i class='ti-pencil-alt'></i></a></td>";
												}
												echo "</tr>";
    										$i++;
    									}
									/*}else{
										echo "<tr><td colspan='6'>No Records Found</td></tr>";
									}*/
									?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
			<!--<div id="mainContainer" class="clearfix"></div>-->
            <?php include 'copyright.php'; ?>
        </div>
        <!-- /#page-wrapper -->
<style type="text/css">
#mainContainer{
    background:Red;
    min-width:850px;
}
.container{
    text-align:center;
    margin:10px .5%;
    padding:10px .5%;
    float:left;
    overflow:visible;
    position:relative;
}
.member {
    /*background: #eee;*/
    position: relative;
    z-index: 1;
    cursor: default;
    border-bottom: solid 1px #000;
	display: flex;
	/*width: 40px;
    height: 50px;
    vertical-align: middle;
    display: table;
    margin: 0 auto;*/
}
.memberTxt{
	background: #eee;
	width: 40px;
    height: 50px;
	margin: 0 auto;
    padding-top: 15px;
}
.member .metaInfo {
    display: none;
    border: solid 1px #000;
    background: #fff;
    position: absolute;
    bottom: 100%;
    left: 50%;
    padding: 5px;
    width: 100px;
}
.member:hover .metaInfo {
    display: block;
}
.member:after {
    display: block;
    position: absolute;
    left: 50%;
    width: 1px;
    height: 20px;
    background: #000;
    content: " ";
    bottom: 100%;
}
</style>
<?php
include ROOT_DIR_COMMON.'footer.php';
?>
<script type="text/javascript">

	function getDistrict(val) {
		$.ajax({
		type: "POST",
		url: "<?php echo ROOT_URL; ?>/get_district.php",
		data:'state_id='+val,
		success: function(data){
					$("#c_id").html(data);
				}
		});
	}

function confirmDelete(){
   return confirm("Are you sure you want to delete this?");
}
$(".update_user_form .user_id").on('change', function(){
	$(this).parent().find('button[type="submit"]').removeClass('hide');
});
$(".update_user_form").on('submit', function(){
	var valid = false;
	var user_id = $(this).find(".user_id").find('option:selected').val();
	var user = $(this).find(".user_id").find('option:selected').text();
	var upd1 = $(this).find(".formation_name").val();
	var formation_id = $(this).find("#formation_id").val();
	if(confirmUpdate(user, upd1)){
		if(checkCharges(formation_id, user_id) == true){
			valid = true;
		}else{
			//alert("ALERT! You have selected the user with different role. Please first assign the current charges of the selected user to somebody else and then try again!");
			valid = confirm("ALERT! You have selected the user with different role. Please click OK to update otherwise click Cancel.");
		}
	}
	return valid;
});
function checkCharges(formation_id, user_id) {
	var valid = false;
	$.ajax({
		type: "POST",
		url: "<?php echo ROOT_URL; ?>/check_charges.php",
		data:'formation_id='+formation_id+'&user_id='+user_id,
		async: false,
		success: function(data){
					if(data == "true"){
						valid = true;
					}
				}
	});
	return valid;
}
function confirmUpdate(upd, upd1){
	return confirm("Are you sure you want to assign: "+upd+" to "+upd1+"?");
}
				/*$(".enq_status").on('change', function(){
					update_inquiry($(this).find(':selected').text(),$(this).parent().find(".enq_id").val());
				});
				function update_inquiry(val,id){
					var valid = confirmUpdate(val);
					console.log(val);
					console.log(id);
					if(valid){
						//$("form[name='update_inquiry_form_"+id+"']").submit();
						$("form[name='update_inquiry_form_"+id+"']").find("button").click();
					}else{
						return false;
					}
				}*/

	<?php if(isset($_GET['delete']) && $_GET['delete'] == 1 && isset($_SERVER['HTTP_REFERER'])){ ?>
		$.toast({
			text: '1 Record Deleted',
			heading: 'Deleted',
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
	<?php if((isset($_GET['insert']) && $_GET['insert'] == 1 && isset($_SERVER['HTTP_REFERER'])) || (isset($_GET['update']) && $_GET['update'] == 1 && isset($_SERVER['HTTP_REFERER']))){ ?>
	$.toast({
        text: '<?php if(isset($_GET['insert'])){ echo $page_name.' Added Successfully'; }else if(isset($_GET['update'])){ echo $page_name.' Updated Successfully'; } ?>',
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