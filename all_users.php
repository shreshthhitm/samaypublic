<?php
include 'init.php';

$requiredRole = USER_ACCESS;
if (! isAuthorized($requiredRole)) {
  header('Location: dashboard.php');
}

include ROOT_DIR_COMMON.'header.php';
$role = 'manager';
if(isset($_GET['role']) && $_GET['role'] != ''){
	$role = htmlspecialchars($_GET['role']);
	$page_name = $user_roles[$role];
}else{
	//$page_name = 'manager';
	//header('Location: all_users.php?role=manager');
	echo "<script>\n"; 
	echo "    window.location.href = '".ROOT_URL."/all_users.php?role=manager';\n"; 
	echo "</script>\n";
}
$s = 0;
if(isset($_POST['submit'])){
	$centre_id = htmlspecialchars($_POST['c_id']);
	$s = 1;
}else{
	if($_SESSION['sess_userrole'] == 'cc_user'){
		$centre_id = htmlspecialchars($_SESSION['sess_fid']);
	}else{
		$centre_id = '';
	}
}
if(isset($_POST['btn_active'])){
	$c_id = htmlspecialchars($_POST['c_id']);
	$active = (($_POST['active'] == 1) ? 0 : 1);
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
	$q0 = "UPDATE users SET is_active=".$active." WHERE id=".$c_id;
	if ($myPDO->query($q0)) {
		//echo "<script type= 'text/javascript'>alert('New Record Inserted Successfully');</script>";
		//$msg="<div class='panel-heading' style='color: green;text-align: center;background: transparent;'>Product Inserted Successfully</div>";
		//$post_status = 0;
		echo "<script>\n"; 
		echo "    window.location.href = 'all_users.php?role=".$role."&update=1';\n"; 
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
                    <div class="col-xs-12">
                        <h4 class="page-title">All <?=$page_name;?>s</h4> </div>
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
                            <h3 class="box-title">View <?=$page_name;?> List</h3>
                            <!--<p class="text-muted">Add class <code>.table</code></p>-->
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?=$page_name;?></th>
											<th>Charge(s)</th>
											<th>Email</th>
											<th>SSO-ID</th>
											<th>Contact No.</th>
											<th>Status</th>
											<th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									$q = 'SELECT *, U.id AS user_main_id, U.is_active AS user_is_active FROM users U INNER JOIN commissioners C ON C.user_id = U.id';
									if($_SESSION['sess_userrole'] != 'admin' || $s == 1){
										$q .= ' INNER JOIN charges_table CT ON CT.user_id=U.id INNER JOIN formations F ON F.id = CT.formation_id WHERE 1=1 && CT.charge_status=1';
									}
									/*if($_SESSION['role'] == USER_ACCESS || $s == 1){
										$q .= ' && B.id='.$centre_id;
									}*/
									if($_SESSION['sess_userrole'] == 'cc_user'){
										$q .= ' && F.parent_id='.$centre_id;
									}
									if($s == 1){
										$q .= ' && F.id='.$centre_id;
									}
									$q .= ' AND U.role="'.$role.'"';
									if($_SESSION['sess_userrole'] != 'admin'){
										$q .= ' GROUP BY U.id';
									}
									$q .= ' ORDER BY U.id DESC';
									//echo $q;
									$i = 1;
									foreach($myPDO->query($q) as $row){
										/*$query = $myPDO->query("Select * from orders WHERE user_id=".$row['user_main_id']);
										$no_of_orders = $query->rowCount();*/
										/*$bank_query = $myPDO->query("SELECT * FROM bankinfo WHERE centre_id=".$row['id']);
										$bankinfo_exists = $bank_query->rowCount();
										if($bankinfo_exists == 0){
											$bank_status = "<a href=add_bank_details.php?add={$row['id']}><i class='ti-pencil-alt'></i> Add</a>";
										}else{
											$bankinfo_id = get_value_from_id("bankinfo","id","centre_id",$row['id']);
											$bank_status = "<a href=add_bank_details.php?edit={$bankinfo_id}><i class='ti-eye'></i> View</a>";
										}*/
										$is_active = "<form method='post'><input type='hidden' id='c_id' name='c_id' value='{$row['user_main_id']}' ><input type='hidden' id='active' name='active' value='{$row['user_is_active']}' ><button type='submit' name='btn_active' class='label label-".(($row['user_is_active'] == 1)? "success'>Active" : "danger'>Inactive")."</button></form>";
                                        echo "<tr>
												<td>{$i}</td>
												<td>{$row['officer_name']}</td>
												<td>".charges_by_user($row['user_main_id'])."</td>
												<td>{$row['email']}</td>
												<td>{$row['username']}</td>
												<td>{$row['contact_no']}</td>
												<td>".$is_active."</td>
												<td><a href=add_user.php?edit={$row['user_main_id']}><i class='ti-pencil-alt'></i></a>";
											if($_SESSION['sess_userrole'] == 'admin'){
												/*echo "	<a href=add_user.php?del={$row['user_main_id']}&role={$role} onclick='return confirmDelete();'><button type='button' class='btn btn-info btn-outline btn-circle btn-lg m-r-5 btn-delete'><i class='ti-trash'></i></button></a>";*/
											}
											echo "</td>";
											echo "</tr>";
										$i++;
									}
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
            <?php include 'copyright.php'; ?>
        </div>
        <!-- /#page-wrapper -->

<?php
include ROOT_DIR_COMMON.'footer.php';
?>
<script type="text/javascript">
function confirmDelete(){
   return confirm("Are you sure you want to delete this?");
}

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
</script>