<?php
include 'init.php';
 
if(!isset($_SESSION['role']) || $_SESSION['role'] == ''){
		header('Location: index.php?err=4');
	}

include ROOT_DIR_COMMON.'header.php';

if(isset($_POST['submit_confirm'])){
	$q = "DELETE FROM charges_table WHERE user_id=".$_POST['user_id']." && formation_id=".$_SESSION['sess_fid']." && reject_status=1";
	$entry = $myPDO->query($q);
	
	if ($entry) {
		/*sendSMS($contact_no, $template_id['linked']);
		send_mail_function($email, $strSubject_link, $mail_msg_linked, $site_email, $site_name);*/
		echo "<script>\n";
		echo "    window.location.href = 'dashboard.php';\n";
		echo "</script>\n";

	}else{
		//echo "<script type= 'text/javascript'>alert('Data not successfully Inserted.');</script>";
		$msg="<div class='panel-heading' style='color: red;text-align: center;background: transparent;'>Some error occurred while adding the product</div>";
		//$post_status = 1;
	}
	
	//echo '<meta http-equiv="refresh" content="1;url='.$_SERVER['PHP_SELF'].'">';
}
?>
<style type="text/css">
.col-first .white-box.analytics-info{
	margin-top: 0;
}
</style>
        <!-- ============================================================== -->
        <!-- Page Content -->
        <!-- ============================================================== -->
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">Dashboard</h4> </div>
                    <!--<div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                        <a href="<?php echo ROOT_URL.'/common/logout.php'; ?>" class="btn btn-danger pull-right m-l-20 hidden-xs hidden-sm waves-effect waves-light">Logout</a>
                        <ol class="breadcrumb">
                            <li><a href="#">Dashboard</a></li>
                        </ol>
                    </div>-->
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
                <!-- ============================================================== -->
                <!-- Different data widgets -->
                <!-- ============================================================== -->
                <!-- .row -->
				<?php
					$order_receiving_date = 'date(O.order_receiving_date)';
					$today_date = "'".date('Y-m-d')."'";
					$dash_query_rem = "Select COALESCE(SUM(IF(DATEDIFF(".$today_date.",".$order_receiving_date.") >= 20, 1, 0)), 0) AS delay_orders from orders O INNER JOIN order_status OS ON O.id=OS.order_id INNER JOIN formations F ON F.id=O.formation_id WHERE ";
					
					$dash_query_start = "Select * from orders O INNER JOIN order_status OS ON O.id=OS.order_id INNER JOIN formations F ON F.id=O.formation_id WHERE ";
					$user_id = $_SESSION['sess_user_id'];
					$formation_id = $_SESSION['sess_fid'];
					
					$user_q = '';
					if($userrole == 'board'){
						$mapped_court = get_value_from_id("boards","court_type","formation_id",$formation_id);
						$user_q = "O.court='".$mapped_court."' && ";
					}
					if($userrole == 'cc_user'){
						$user_q = "F.parent_id=".$formation_id." && ";
					}
					if($userrole == 'manager'){
						$user_q = "F.id=".$formation_id." && ";
					}
					if($userrole != 'admin'){
						$user_q .= "O.is_active=1 && ";
					}
					$dash_query_start .= $user_q;
					
					$query_rem = $dash_query_rem.$user_q."(".$dashboard_query[1]." OR ".$dashboard_query[3]." OR ".$dashboard_query[5];
					if(!($userrole == 'cc_user' || $userrole == 'manager')){
						$query_rem .= " OR ".$dashboard_query[6];
					}
					$query_rem .= ")";
					//echo $query_rem;
				?>
                <div class="row">
					<?php
					$col_first = 0;
					if($_SESSION['role'] != ADMIN_ACCESS){
						$sql_populate1 = "SELECT * FROM charges_table WHERE user_id=".$user_id." && formation_id!=".$formation_id." && charge_status=0 && reject_status=0";
						$stmt_edit1 = $myPDO->query($sql_populate1);
						if($stmt_edit1->rowCount() > 0){
							foreach($stmt_edit1 as $row_charge){
								$comm_name3 = get_value_from_id("formations","formation","id",$row_charge['formation_id']);
						?>
					<div class="col-xs-12 <?=($col_first == 0 ? 'col-first' : '');?>">
                        <div class="row white-box analytics-info" >
                            <h3 class="box-title col-xs-8"><i class="fa fa-hand-o-right" aria-hidden="true"></i> Take charge from <span class="text-danger"><?=$comm_name3;?></span></h3>
							<ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-success"></i>--> 
									<form class="" action="transfer_charge.php" method="post">
										<input type="hidden" name="formation_id" value="<?=$row_charge['formation_id'];?>" />
										<button type="submit" name="submit_form" class="btn btn-success" value="btn_accept"><i class="fa fa-check"></i> Accept</button>
										<button type="submit" name="submit_form" class="btn btn-danger" value="btn_reject"><i class="fa fa-close"></i> Reject</button>
									</form>
								</li>
                            </ul>
                        </div>
                    </div>
					<?php
							}
							$col_first = 1;
						}
						
						$sql_populate2 = "SELECT * FROM charges_table WHERE given_by_user=".$user_id." && formation_id=".$formation_id." && charge_status=0";
						$stmt_edit2 = $myPDO->query($sql_populate2);
						if($stmt_edit2->rowCount() > 0){
							foreach($stmt_edit2 as $row_charge){
								$comm_name4 = ($row_charge['given_to_formation'] !='' ? get_value_from_id("formations","formation","id",$row_charge['given_to_formation']) : ($row_charge['given_by_user'] != '' ? get_value_from_id("commissioners","officer_name","user_id",$row_charge['given_by_user']).($row['username'] != '' ? ' ('.$row['username'].')' : '') : ''));
						?>
					<div class="col-xs-12  <?=($col_first == 0 ? 'col-first' : '');?>">
                        <div class="row white-box analytics-info" >
                            <h3 class="box-title col-xs-8"><i class="fa fa-info-circle" aria-hidden="true"></i> Charge transfer request to <span class="text-danger"><?=$comm_name4;?></span> <?=($row_charge['reject_status'] == 0 ? 'on SAMAY is pending' : 'has been declined');?>.</h3>
							<ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash"></div>
                                </li>-->
								<?php if($row_charge['reject_status'] == 1){ ?>
                                <li class="text-right"><!--<i class="ti-arrow-up text-success"></i>--> 
									<form class="" action="" method="post">
										<input type="hidden" name="user_id" value="<?=$row_charge['user_id'];?>" />
										<button type="submit" name="submit_confirm" class="btn btn-success" value="btn_accept"><i class="fa fa-check"></i> OK</button>
									</form>
								</li>
								<?php } ?>
                            </ul>
                        </div>
                    </div>
					<?php
							}
							$col_first = 1;
						}
					} ?>
                    <?php if($_SESSION['role'] == ADMIN_ACCESS){ ?>
					<div class="col-xs-12  <?=($col_first == 0 ? 'col-first' : '');?>">
						<div class="col-xs-12 bg-white p-t-20 p-b-20" >
							<!--<div class="row white-box analytics-info" >
								<h3 class="box-title text-success col-xs-8"><i class="fa fa-tags" aria-hidden="true"></i> Total Formations</h3>
								<?php
								$query = $myPDO->query("Select * from formations");
								//$row = $query->fetch();
								$num_of_products = $query->rowCount();
								?>
								<ul class="list-inline two-part col-xs-4">
									<li class="text-right"><span class="counter text-purple"><?php echo $num_of_products; ?></span></li>
								</ul>
							</div>-->
							<div class="row white-box analytics-info" style="background: #fff;">
								<h2 class="box-title text-success text-center m-b-20" style="font-size: 24px;"><i class="fa fa-tags" aria-hidden="true"></i> Total Formations <span class="counter text-purple m-l-20"><?=(get_value_from_id("formations","count(*)","1","1") - get_value_from_id("formations","count(*)","role","board"));?></span></h2>
							</div>
							<div class="row">
								<?php
								$col_offset = 0;
								//$color_box_arr = array("danger","","","");
								foreach($user_roles_wo_board as $ukey=>$uval){
								?>
								<div class="col-xs-12 col-sm-6 col-md-4 <?=($col_offset == 0 ? 'col-md-offset-2' : '');?>">
									<div class="row white-box analytics-info inner-analytics m-b-10" style="background: #fff;border-radius: 10px;border: 2px solid #aaa;" onclick="window.location.href='all_formations.php?role=<?=$ukey;?>';" >
										<h3 class="box-title text-center text-success"><i class="fa fa-check" aria-hidden="true"></i> Total <?=$uval;?></h3>
										<ul class="list-inline text-center">
											<li><span class="counter text-purple"><?=get_value_from_id("formations","count(*)","role",$ukey);?></span></li>
										</ul>
									</div>
								</div>
								<?php $col_offset++;
								} ?>
							</div>
						</div>						
                    </div>
					<?php $col_first = 1;
					}
					?>
                    <!--<div class="col-lg-4 col-sm-6 col-xs-12">-->
					<div class="col-xs-12  <?=($col_first == 0 ? 'col-first' : '');?>">
                        <div class="row white-box analytics-info" onclick="window.location.href='all_case_orders.php?type=10';" >
                            <h3 class="box-title text-danger col-xs-8"><i class="fa fa-edit" aria-hidden="true"></i> Orders Pending beyond 20 Days</h3>
							<?php
							//echo $query_rem;
							$query_inv = $myPDO->query($query_rem);
							$row_of_inv = $query_inv->fetch(PDO::FETCH_ASSOC);
							$num_of_inv = $row_of_inv['delay_orders'];
							?>
                            <ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash2"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-purple"></i>--> <span class="counter text-purple"><?php echo $num_of_inv; ?></span></li>
                            </ul>
                        </div>
                    </div>
					<div class="col-xs-12">
                        <div class="row white-box analytics-info" onclick="window.location.href='all_case_orders.php?type=5';" >
                            <h3 class="box-title text-danger col-xs-8"><i class="fa fa-edit" aria-hidden="true"></i> Orders Pending with the <?=$user_roles['manager'];?></h3>
							<?php
							
							$query_reg = $dash_query_start.$dashboard_query[5];
							//echo "Pending: ".$query_reg;
							$query_inv = $myPDO->query($query_reg);
							$num_of_inv = $query_inv->rowCount();
							?>
                            <ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash2"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-purple"></i>--> <span class="counter text-purple"><?php echo $num_of_inv; ?></span></li>
                            </ul>
                        </div>
                    </div>
					<div class="col-xs-12">
                        <div class="row white-box analytics-info" onclick="window.location.href='all_case_orders.php?type=4';" >
                            <h3 class="box-title text-success col-xs-8"><i class="fa fa-edit" aria-hidden="true"></i> Orders Accepted by <?=$user_roles['manager'];?>/<?=$user_roles['cc_user'];?>/Board</h3>
							<?php
							
							$query_reg = $dash_query_start.$dashboard_query[4];
							$query_inv = $myPDO->query($query_reg);
							$num_of_inv = $query_inv->rowCount();
							?>
                            <ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash2"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-purple"></i>--> <span class="counter text-purple"><?php echo $num_of_inv; ?></span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="row white-box analytics-info" onclick="window.location.href='all_case_orders.php?type=1';" >
                            <h3 class="box-title text-<?=(($_SESSION['sess_userrole'] == 'cc_user') ? 'danger' : 'info');?> col-xs-8"><i class="fa fa-edit" aria-hidden="true"></i> Orders Pending at <?=$user_roles['cc_user'];?></h3>
							<?php
							
							$query_reg = $dash_query_start.$dashboard_query[1];
							$query_inv = $myPDO->query($query_reg);
							$num_of_inv = $query_inv->rowCount();
							?>
                            <ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash2"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-purple"></i>--> <span class="counter text-purple"><?php echo $num_of_inv; ?></span></li>
                            </ul>
                        </div>
                    </div>
					
					<div class="col-xs-12">
                        <div class="row white-box analytics-info" onclick="window.location.href='all_case_orders.php?type=3';" >
                            <h3 class="box-title text-warning col-xs-8"><i class="fa fa-edit" aria-hidden="true"></i> Action Pending after <?=$user_roles['cc_user'];?> Decision</h3>
							<?php
							
							$query_reg = $dash_query_start.$dashboard_query[3];
							$query_inv = $myPDO->query($query_reg);
							$num_of_inv = $query_inv->rowCount();
							?>
                            <ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash2"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-purple"></i>--> <span class="counter text-purple"><?php echo $num_of_inv; ?></span></li>
                            </ul>
                        </div>
                    </div>
					
					
					<div class="col-xs-12">
                        <div class="row white-box analytics-info" onclick="window.location.href='all_case_orders.php?type=6';" >
                            <h3 class="box-title text-info col-xs-8"><i class="fa fa-edit" aria-hidden="true"></i> Proposal forwarded to Board for filing Appeal/Petition</h3>
							<?php
							
							$query_reg = $dash_query_start.$dashboard_query[6];
							$query_inv = $myPDO->query($query_reg);
							$num_of_inv = $query_inv->rowCount();
							?>
                            <ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash2"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-purple"></i>--> <span class="counter text-purple"><?php echo $num_of_inv; ?></span></li>
                            </ul>
                        </div>
                    </div>
					<div class="col-xs-12">
                        <div class="row white-box analytics-info" onclick="window.location.href='all_case_orders.php?type=2';" >
                            <h3 class="box-title text-danger col-xs-8"><i class="fa fa-edit" aria-hidden="true"></i> Appeal/Petition Filed in High Court</h3>
							<?php
							
							$query_reg = $dash_query_start.$dashboard_query[2];
							$query_inv = $myPDO->query($query_reg);
							$num_of_inv = $query_inv->rowCount();
							?>
                            <ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash2"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-purple"></i>--> <span class="counter text-purple"><?php echo $num_of_inv; ?></span></li>
                            </ul>
                        </div>
                    </div>
					<?php
					if(!($_SESSION['sess_userrole'] == 'board' && get_value_from_id("boards","court_type","formation_id",$formation_id) == 'high')){
					?>
					<div class="col-xs-12">
                        <div class="row white-box analytics-info" onclick="window.location.href='all_case_orders.php?type=9';" >
                            <h3 class="box-title text-danger col-xs-8"><i class="fa fa-edit" aria-hidden="true"></i> Application Filed in Tribunal</h3>
							<?php
							
							$query_reg = $dash_query_start.$dashboard_query[9];
							$query_inv = $myPDO->query($query_reg);
							$num_of_inv = $query_inv->rowCount();
							?>
                            <ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash2"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-purple"></i>--> <span class="counter text-purple"><?php echo $num_of_inv; ?></span></li>
                            </ul>
                        </div>
                    </div>
					<?php
					}
					?>
					<div class="col-xs-12">
                        <div class="row white-box analytics-info" onclick="window.location.href='all_case_orders.php?type=7';" >
                            <h3 class="box-title text-danger col-xs-8"><i class="fa fa-edit" aria-hidden="true"></i> Appeal/Petition Filed in Supreme Court</h3>
							<?php
							
							$query_reg = $dash_query_start.$dashboard_query[7];
							$query_inv = $myPDO->query($query_reg);
							$num_of_inv = $query_inv->rowCount();
							?>
                            <ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash2"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-purple"></i>--> <span class="counter text-purple"><?php echo $num_of_inv; ?></span></li>
                            </ul>
                        </div>
                    </div>
					<?php /*?><div class="col-xs-12">
                        <div class="row white-box analytics-info" onclick="window.location.href='all_case_orders.php?type=8';" >
                            <h3 class="box-title text-success col-xs-8"><i class="fa fa-edit" aria-hidden="true"></i> Appeal Decided by Supreme Court</h3>
							<?php
							
							$query_reg = $dash_query_start.$dashboard_query[8];
							$query_inv = $myPDO->query($query_reg);
							$num_of_inv = $query_inv->rowCount();
							?>
                            <ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash2"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-purple"></i>--> <span class="counter text-purple"><?php echo $num_of_inv; ?></span></li>
                            </ul>
                        </div>
                    </div><?php */?>
					<div class="col-xs-12">
                        <div class="row white-box analytics-info" onclick="window.location.href='all_case_orders.php';" >
                            <h3 class="box-title col-xs-8"><i class="fa fa-edit" aria-hidden="true"></i> Total Orders Registered</h3>
							<?php
							
							$query_reg = $dash_query_start.$dashboard_query[0];
							$query_inv = $myPDO->query($query_reg);
							$num_of_inv = $query_inv->rowCount();
							?>
                            <ul class="list-inline two-part col-xs-4">
                                <!--<li>
                                    <div id="sparklinedash2"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-purple"></i>--> <span class="counter text-purple"><?php echo $num_of_inv; ?></span></li>
                            </ul>
                        </div>
                    </div>
					<?php /*if($_SESSION['role'] == ADMIN_ACCESS){ ?>
                    <div class="col-lg-4 col-sm-6 col-xs-12">
                        <div class="white-box analytics-info" onclick="" >
                            <h3 class="box-title"><i class="fa fa-money" aria-hidden="true"></i>&nbsp; Total Contribution</h3>
							<?php
							$active_event_id = get_value_from_id("events", "id", "is_active","1");
							$que = "";
							if($_SESSION['sess_userrole'] == 'manager'){
								$que .= "Select E.event_id, sum(E.event_amt) AS total_amt from event_regs E LEFT JOIN registrations R ON E.reg_id=R.id WHERE E.event_id=$active_event_id AND E.is_paid=1 AND R.centre_id=$centre_id GROUP BY R.id";
							}else{
								$que .= "Select E.event_id, sum(E.event_amt) AS total_amt from event_regs E LEFT JOIN registrations R ON E.reg_id=R.id WHERE E.event_id=$active_event_id AND E.is_paid=1 GROUP BY E.reg_id";
							}
							try{
								$query_amt = $myPDO->query($que);
							}catch(PDOException $e){}
							//$row = $query->fetch();
							$tot_amt = 0;
							/*foreach($query_amt as $row1){
								$tot_amt += $row1['total_amt'];
							}*/
							//$tot_amt = $query_amt->fetch(PDO::FETCH_ASSOC);
							//$num_of_cust = $query_cust->rowCount();
							/*?>
                            <ul class="list-inline two-part">
                                <!--<li>
                                    <div id="sparklinedash3"></div>
                                </li>-->
                                <li class="text-right"><!--<i class="ti-arrow-up text-info"></i>--><i class="fa fa-inr" aria-hidden="true" style="color: #41b3f9;"></i> <span class="counter text-info"><?php echo $tot_amt; //echo $tot_amt['total_amt']; ?></span></li>
                            </ul>
                        </div>
                    </div>
					<?php } */?>
                </div>
                <!--/.row -->
                <!--row -->
                <!-- /.row -->
                <!--<div class="row">
                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                        <div class="white-box">
                            <h3 class="box-title">Products Yearly Sales</h3>
                            <ul class="list-inline text-right">
                                <li>
                                    <h5><i class="fa fa-circle m-r-5 text-info"></i>Mac</h5> </li>
                                <li>
                                    <h5><i class="fa fa-circle m-r-5 text-inverse"></i>Windows</h5> </li>
                            </ul>
                            <div id="ct-visits" style="height: 405px;"></div>
                        </div>
                    </div>
                </div>-->
                <!-- ============================================================== -->
                <!-- table -->
                <!-- ============================================================== -->
                <!--<div class="row">
                    <div class="col-md-12 col-lg-12 col-sm-12">
                        <div class="white-box">
                            <div class="col-md-3 col-sm-4 col-xs-6 pull-right">
                                <select class="form-control pull-right row b-none">
                                    <option>March 2017</option>
                                    <option>April 2017</option>
                                    <option>May 2017</option>
                                    <option>June 2017</option>
                                    <option>July 2017</option>
                                </select>
                            </div>
                            <h3 class="box-title">Recent sales</h3>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>NAME</th>
                                            <th>STATUS</th>
                                            <th>DATE</th>
                                            <th>PRICE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td class="txt-oflo">Elite admin</td>
                                            <td>SALE</td>
                                            <td class="txt-oflo">April 18, 2017</td>
                                            <td><span class="text-success">$24</span></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td class="txt-oflo">Real Homes WP Theme</td>
                                            <td>EXTENDED</td>
                                            <td class="txt-oflo">April 19, 2017</td>
                                            <td><span class="text-info">$1250</span></td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td class="txt-oflo">Ample Admin</td>
                                            <td>EXTENDED</td>
                                            <td class="txt-oflo">April 19, 2017</td>
                                            <td><span class="text-info">$1250</span></td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td class="txt-oflo">Medical Pro WP Theme</td>
                                            <td>TAX</td>
                                            <td class="txt-oflo">April 20, 2017</td>
                                            <td><span class="text-danger">-$24</span></td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td class="txt-oflo">Hosting press html</td>
                                            <td>SALE</td>
                                            <td class="txt-oflo">April 21, 2017</td>
                                            <td><span class="text-success">$24</span></td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td class="txt-oflo">Digital Agency PSD</td>
                                            <td>SALE</td>
                                            <td class="txt-oflo">April 23, 2017</td>
                                            <td><span class="text-danger">-$14</span></td>
                                        </tr>
                                        <tr>
                                            <td>7</td>
                                            <td class="txt-oflo">Helping Hands WP Theme</td>
                                            <td>MEMBER</td>
                                            <td class="txt-oflo">April 22, 2017</td>
                                            <td><span class="text-success">$64</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>-->
                <!-- ============================================================== -->
                <!-- chat-listing & recent comments -->
                <!-- ============================================================== -->
                <?php /* ?><div class="row">
                    <!-- .col -->
                    <div class="col-md-12 col-lg-8 col-sm-12">
                        <div class="white-box">
                            <h3 class="box-title">Recent Comments</h3>
                            <div class="comment-center p-t-10">
                                <div class="comment-body">
                                    <div class="user-img"> <img src="./images/users/pawandeep.jpg" alt="user" class="img-circle">
                                    </div>
                                    <div class="mail-contnet">
                                        <h5>Pavan kumar</h5><span class="time">10:20 AM   20  may 2016</span>
                                        <br/><span class="mail-desc">Donec ac condimentum massa. Etiam pellentesque pretium lacus. Phasellus ultricies dictum suscipit. Aenean commodo dui pellentesque molestie feugiat. Aenean commodo dui pellentesque molestie feugiat</span> <a href="javacript:void(0)" class="btn btn btn-rounded btn-default btn-outline m-r-5"><i class="ti-check text-success m-r-5"></i>Approve</a><a href="javacript:void(0)" class="btn-rounded btn btn-default btn-outline"><i class="ti-close text-danger m-r-5"></i> Reject</a>
                                    </div>
                                </div>
                                <div class="comment-body">
                                    <div class="user-img"> <img src="./images/users/sonu.jpg" alt="user" class="img-circle">
                                    </div>
                                    <div class="mail-contnet">
                                        <h5>Sonu Nigam</h5><span class="time">10:20 AM   20  may 2016</span>
                                        <br/><span class="mail-desc">Donec ac condimentum massa. Etiam pellentesque pretium lacus. Phasellus ultricies dictum suscipit. Aenean commodo dui pellentesque molestie feugiat. Aenean commodo dui pellentesque molestie feugiat</span>
                                    </div>
                                </div>
                                <div class="comment-body b-none">
                                    <div class="user-img"> <img src="./images/users/arijit.jpg" alt="user" class="img-circle">
                                    </div>
                                    <div class="mail-contnet">
                                        <h5>Arijit singh</h5><span class="time">10:20 AM   20  may 2016</span>
                                        <br/><span class="mail-desc">Donec ac condimentum massa. Etiam pellentesque pretium lacus. Phasellus ultricies dictum suscipit. Aenean commodo dui pellentesque molestie feugiat. Aenean commodo dui pellentesque molestie feugiat</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="panel">
                            <div class="sk-chat-widgets">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        CHAT LISTING
                                    </div>
                                    <div class="panel-body">
                                        <ul class="chatonline">
                                            <li>
                                                <div class="call-chat">
                                                    <button class="btn btn-success btn-circle btn-lg" type="button"><i class="fa fa-phone"></i></button>
                                                    <button class="btn btn-info btn-circle btn-lg" type="button"><i class="fa fa-comments-o"></i></button>
                                                </div>
                                                <a href="javascript:void(0)"><img src="./images/users/varun.jpg" alt="user-img" class="img-circle"> <span>Varun Dhavan <small class="text-success">online</small></span></a>
                                            </li>
                                            <li>
                                                <div class="call-chat">
                                                    <button class="btn btn-success btn-circle btn-lg" type="button"><i class="fa fa-phone"></i></button>
                                                    <button class="btn btn-info btn-circle btn-lg" type="button"><i class="fa fa-comments-o"></i></button>
                                                </div>
                                                <a href="javascript:void(0)"><img src="./images/users/genu.jpg" alt="user-img" class="img-circle"> <span>Genelia Deshmukh <small class="text-warning">Away</small></span></a>
                                            </li>
                                            <li>
                                                <div class="call-chat">
                                                    <button class="btn btn-success btn-circle btn-lg" type="button"><i class="fa fa-phone"></i></button>
                                                    <button class="btn btn-info btn-circle btn-lg" type="button"><i class="fa fa-comments-o"></i></button>
                                                </div>
                                                <a href="javascript:void(0)"><img src="./images/users/ritesh.jpg" alt="user-img" class="img-circle"> <span>Ritesh Deshmukh <small class="text-danger">Busy</small></span></a>
                                            </li>
                                            <li>
                                                <div class="call-chat">
                                                    <button class="btn btn-success btn-circle btn-lg" type="button"><i class="fa fa-phone"></i></button>
                                                    <button class="btn btn-info btn-circle btn-lg" type="button"><i class="fa fa-comments-o"></i></button>
                                                </div>
                                                <a href="javascript:void(0)"><img src="./images/users/arijit.jpg" alt="user-img" class="img-circle"> <span>Arijit Sinh <small class="text-muted">Offline</small></span></a>
                                            </li>
                                            <li>
                                                <div class="call-chat">
                                                    <button class="btn btn-success btn-circle btn-lg" type="button"><i class="fa fa-phone"></i></button>
                                                    <button class="btn btn-info btn-circle btn-lg" type="button"><i class="fa fa-comments-o"></i></button>
                                                </div>
                                                <a href="javascript:void(0)"><img src="./images/users/govinda.jpg" alt="user-img" class="img-circle"> <span>Govinda Star <small class="text-success">online</small></span></a>
                                            </li>
                                            <li>
                                                <div class="call-chat">
                                                    <button class="btn btn-success btn-circle btn-lg" type="button"><i class="fa fa-phone"></i></button>
                                                    <button class="btn btn-info btn-circle btn-lg" type="button"><i class="fa fa-comments-o"></i></button>
                                                </div>
                                                <a href="javascript:void(0)"><img src="./images/users/hritik.jpg" alt="user-img" class="img-circle"> <span>John Abraham<small class="text-success">online</small></span></a>
                                            </li>
                                            <li>
                                                <div class="call-chat">
                                                    <button class="btn btn-success btn-circle btn-lg" type="button"><i class="fa fa-phone"></i></button>
                                                    <button class="btn btn-info btn-circle btn-lg" type="button"><i class="fa fa-comments-o"></i></button>
                                                </div>
                                                <a href="javascript:void(0)"><img src="./images/users/varun.jpg" alt="user-img" class="img-circle"> <span>Varun Dhavan <small class="text-success">online</small></span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.col -->
                </div><?php */ ?>
            </div>
            <!-- /.container-fluid -->
            <?php include 'copyright.php'; ?>
        </div>
        <!-- ============================================================== -->
        <!-- End Page Content -->
        <!-- ============================================================== -->

		<?php
		include ROOT_DIR_COMMON.'footer.php';
		?>