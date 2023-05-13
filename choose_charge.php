<?php
	include_once 'init.php';
    include_once ROOT_DIR_COMMON.'functions.php';
	if(isset($_SESSION['role']) && $_SESSION['role'] != ''){
		header('Location: dashboard.php');
	}
	$charge_exist = 0;
	//$_SESSION['sess_user_id'] = $_GET['user'];
	$qq = "SELECT * FROM charges_table WHERE user_id=".$_SESSION['sess_user_id']." && charge_status=1";
	$stmt_charges = $myPDO->query($qq);
	if($stmt_charges->rowCount() > 1){	//If they have only 1 charge then it will not jump to this page, hence compared to greater than 1
		//$row_edit = $stmt_charges->fetch(PDO::FETCH_ASSOC);
		$charge_exist = 1;
	}
	if(isset($_POST['submit_form'])){
		$formation_id = $_POST['formation_id'];
		$q = 'SELECT * FROM users WHERE id='.$_SESSION['sess_user_id'];
		$stmt_edit = $myPDO->query($q);
		$row = $stmt_edit->fetch(PDO::FETCH_ASSOC);
		$row_charges = $stmt_charges->fetch();
		session_regenerate_id();
		$_SESSION['sess_user_id'] = $row['id'];
		$_SESSION['sess_username'] = $row['username'];
		$_SESSION['sess_userrole'] = $row['role'];
		$_SESSION['sess_fid'] = $formation_id;
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
	}
	include_once ROOT_DIR_COMMON.'header.php';
	$page_name = "Select Charge";
?>
		<!-- ============================================================== -->
        <!-- Page Content -->
        <!-- ============================================================== -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title"><?=$page_name;?></h4>
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
                <?php /* ?><div class="row">
                    <div class="col-sm-12">
                        <div class="white-box">
                            <h3 class="box-title">View Products</h3>
                            <!--<p class="text-muted">Add class <code>.table</code></p>-->
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Sl. No.</th>
                                            <th>Product Code</th>
                                            <th>Name</th>
                                            <th>Qty</th>
											<th>Unit</th>
											<th>Price</th>
											<th rowspan="2">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									$q = 'SELECT * FROM products';
									$i = 1;
									foreach($myPDO->query($q) as $row){
									
                                        echo "<tr>
												<td>{$i}</td>
												<td>{$row['code']}</td>
												<td>{$row['name']}</td>
												<td>{$row['qty']}</td>
												<td>{$row['unit']}</td>
												<td>{$row['price']}</td>
												<td><a href=?id={$row['id']}>Edit</a></td>
												<td><a href=?id={$row['id']}>Delete</a></td>
												 
											</tr>";
                                    $i++;
									}
									?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div><?php */ ?>
                <!-- /.row -->
				<div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-info">
                            <?php //if($msg!=''){ echo $msg; }
								/*$insert = array(
									1=>"Product Saved Successfully!",
									2=>"Some Error Occurred!!!",
								  );*/
								  /*$msg1="<div class='panel-heading' style='color: green;text-align: center;background: transparent;'>Product Inserted Successfully</div>";
								  $msg2="<div class='panel-heading' style='color: red;text-align: center;background: transparent;'>Some error occurred while adding the product</div>";

								$insert_msg_id = isset($_GET['insert']) ? (int)$_GET['insert'] : 0;

								if ($insert_msg_id == 1) {
									echo $msg1;
								}elseif ($insert_msg_id == 2) {
									echo $msg2;
								}*/
								//$stmt = $myPDO->query("SELECT code FROM products ORDER BY id DESC LIMIT 1");
								//$row = $stmt->fetch(PDO::FETCH_NUM);
								//$str = $row[0];
								//echo $str;
								//$new_p_code = code($str,4,4);
								//echo $new_p_code;
							?>
							<?php
							$no_charge = 0;
							$sql_populate3 = "SELECT * FROM charges_table WHERE user_id=".$_SESSION['sess_user_id'];	//The User has its charge to another
							$stmt_edit3 = $myPDO->query($sql_populate3);
							if($stmt_edit3->rowCount() > 0){
								if($charge_exist == 1){
								?>
								<div class="panel-heading text-center">Select one of the following charges to continue:-</div>
								<div class="panel-wrapper collapse in" aria-expanded="true">
									<div class="panel-body">
										<?php
										foreach($myPDO->query($qq) as $row){
											$username = get_value_from_id("users","username","id",$row['formation_id']);
											$comm_name = get_value_from_id("formations","formation","id",$row['formation_id']);
										?>
										<form action="" class="form-horizontal" name="<?=(isset($_GET['edit']) ? '' : 'contact_form');?>" enctype="multipart/form-data" method="post" data-toggle="validator">
											<div class="form-actions">
												<div class="row">
													<div class="col-md-12">
														<div class="form-group">
															<div class="col-md-12 text-center">
																<!--<input type="hidden" value="<?php //echo $post_status; ?>" id="post_status" name="post_status">-->
																<button type="submit" id="submit" name="submit_form" class="btn btn-success" value="btn_accept"><i class="fa fa-arrow-right"></i> <?=$comm_name;?></button>
																<input type="hidden" name="formation_id" value="<?=$row['formation_id'];?>" />
															</div>
														</div>
													</div>
													<div class="col-md-6"> </div>
												</div>
											</div>
										</form>
										<?php
										}
										?>
									</div>
								</div>
								<?php }
								
								$sql_populate1 = "SELECT * FROM charges_table WHERE user_id=".$_SESSION['sess_user_id']." && charge_status=0 && reject_status=0";	//The User has to take charge from someone
								$stmt_edit1 = $myPDO->query($sql_populate1);
								$sql_populate2 = "SELECT * FROM charges_table WHERE user_id!=".$_SESSION['sess_user_id']." && formation_id=".$_SESSION['sess_user_id']." && charge_status=1";	//The User has its charge to nobody
								$stmt_edit2 = $myPDO->query($sql_populate2);
								if($stmt_edit1->rowCount() > 0){
									foreach($stmt_edit1 as $row_charge){
										$comm_name2 = get_value_from_id("formations","formation","id",$row_charge['formation_id']);
									?>
								<div class="panel-heading text-center">You have pending charges to continue, please accept to continue:-</div>
								<div class="panel-wrapper collapse in" aria-expanded="true">
									<div class="panel-body">
										<div class="text-center p-10" style="width: 100%; max-width: 500px; margin: 0 auto; border: 1px solid #333; border-radius: 5px;">
											<div class="text-danger font-bold m-b-10"><?=$comm_name2;?></div>
											<form class="" action="transfer_charge.php" method="post">
												<input type="hidden" name="formation_id" value="<?=$row_charge['formation_id'];?>" />
												<input type="hidden" name="force_logout" value="yes" />
												<button type="submit" name="submit_form" class="btn btn-success" value="btn_accept"><i class="fa fa-check"></i> Accept</button>
												<button type="submit" name="submit_form" class="btn btn-danger" value="btn_reject"><i class="fa fa-close"></i> Reject</button>
											</form>
										</div>
									</div>
								</div>
									<?php }
								}
								$sql_populate_reject = "SELECT * FROM charges_table WHERE user_id=".$_SESSION['sess_user_id']." && reject_status=1";	//The User has its charge to another
								$stmt_reject = $myPDO->query($sql_populate_reject);
								if($stmt_reject->rowCount() > 0){
									$no_charge = 1;
								}
							}else{
								$no_charge = 1;
							}
							if($no_charge == 1){
							?>
							<div class="panel-heading text-center">Sorry, you have no charge to continue at this time!</div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
									<?php
									/*if($stmt_edit3->rowCount() > 0){
										foreach($stmt_edit3 as $row_charge){
									?>
									<div class="text-center p-b-20">It is assigned to <b><?=get_value_from_id("commissioners","name","user_id",$row_charge['user_id']);?></b>.</div>
										<?php }
									}*/ ?>
									<div class="text-center">Please contact the administrator or corresponding authority!</div>
                                </div>
                            </div>
							<?php
							} ?>
							<div class="text-center p-b-20"><span class="text-danger">*</span> If you are not seeing any of the charges assigned to you, please try logout and then log in again!</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.container-fluid -->
            <?php include_once 'copyright.php'; ?>
        </div>
        <!-- /#page-wrapper -->

<?php
include_once ROOT_DIR_COMMON.'footer.php';
include_once 'otpModal.php';
?>