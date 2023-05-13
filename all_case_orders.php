<?php
include 'init.php';
 
$requiredRole = USER_ACCESS;
if (! isAuthorized($requiredRole)) {
  header('Location: dashboard.php');
}

include ROOT_DIR_COMMON.'header.php';

$page_name = 'Order';
$php_self = htmlspecialchars($_SERVER['PHP_SELF']);
if(isset($_GET['type']) && is_numeric($_GET['type']) && $_GET['type'] != 0){
	$type = sanitize($_GET['type'], 'int');
}
if($type > 10){
	echo "<script>\n"; 
	echo "    window.location.href = 'all_case_orders.php?error=y';\n";
	echo "</script>\n";
}
if(isset($_POST['btn_active'])){
	$c_id = $_POST['c_id'];
	$active = (($_POST['active'] == 1) ? 0 : 1);
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
	$q0 = "UPDATE orders SET is_active=".$active." WHERE id=".$c_id;
	if ($myPDO->query($q0)) {
		//echo "<script type= 'text/javascript'>alert('New Record Inserted Successfully');</script>";
		//$msg="<div class='panel-heading' style='color: green;text-align: center;background: transparent;'>Product Inserted Successfully</div>";
		//$post_status = 0;
		echo "<script>\n"; 
		echo "    window.location.href = 'all_case_orders.php?update=1';\n"; 
		echo "</script>\n";

	}
}
if(isset($_POST['submit_update'])){
	$order_id = $_POST['order_id'];
	$update_lims = 0;
	$update_column = $_POST['update_column'];
	$change_select = $_POST['change_select'];
	$upd_query = "UPDATE order_status SET ".$update_column."=".$change_select;
	if($update_column == 'comm_status'){
		$upd_query .= ", comm_date='".date('Y-m-d')."'";
	}
	if($update_column == 'cc_status'){
		$upd_query .= ", cc_date='".date('Y-m-d')."'";
	}
	if($update_column == 'proposal_status'){
		$upd_query .= ", proposal_date='".date('Y-m-d')."'";
		if(get_value_from_id("order_status","cc_status","order_id",$order_id) == 'Appeal/Petition to be filed in Supreme Court'){
			$update_lims = 1;
		}
	}
	if($update_column == 'board_status'){
		$upd_query .= ", board_date='".date('Y-m-d')."'";
	}
	if($update_column == 'appeal_status' && $change_select == 2){
		$appeal_status_create = DateTime::createFromFormat('d-m-Y', $_POST['appeal_decided_date']);
		$appeal_status_date = $appeal_status_create->format('Y-m-d');
		$upd_query .= ", appeal_status_date='".$appeal_status_date."'";
	}
	
	$upd_query .= " WHERE order_id=".$order_id;
	//if($update_lims == 1){
	if($_POST['lims_id'] != ''){
		$myPDO->query("UPDATE orders SET lims=".$_POST['lims_id']." WHERE id=".$order_id);
	}
	if($myPDO->query($upd_query)){
		echo "<script>\n"; 
		echo "    window.location.href = 'all_case_orders.php?type=".(isset($type) ? $type : '')."&update=1';\n";
		echo "</script>\n";
	}	
}

$msg_sent = 0;
if(isset($_POST['submit_comment'])){
	$update_column2 = $_POST['update_column2'];
	$order_id = $_POST['order_id2'];
	$comment = $_POST['comment'];
	$com_query = "INSERT into chat (order_id, creator_id, comment) VALUES (".$order_id.",".$_SESSION['sess_user_id'].",'".$comment."')";
	
	if($myPDO->query($com_query)){
		echo "<script>\n"; 
		echo "    window.location.href = 'all_case_orders.php?type=".(isset($type) ? $type : '')."&sent=1';\n";
		echo "</script>\n";
	}
	$msg_sent = 1;
}

$s = 0;
$date_set = 0;
if(isset($_REQUEST['submit'])){
	$cc_id = (isset($_REQUEST['c_id'])) ? $_REQUEST['c_id'] : '';
	$comm_id = (isset($_REQUEST['comm_id'])) ? $_REQUEST['comm_id'] : '';
	$order_no = htmlspecialchars((isset($_REQUEST['order_no'])) ? $_REQUEST['order_no'] : '');
	$party = htmlspecialchars((isset($_REQUEST['party'])) ? $_REQUEST['party'] : '');
	$orderId = (isset($_REQUEST['orderId'])) ? $_REQUEST['orderId'] : '';
	$samay_id = htmlspecialchars((isset($_REQUEST['samay_id'])) ? ltrim($_REQUEST['samay_id'], 'S') : '');
	//$state_id = get_value_from_id('districts','state_id','id',$centre_id);
	if(!empty($_REQUEST['start']) || !empty($_REQUEST['end'])){
		$start_date = htmlspecialchars($_REQUEST['start']);
		$end_date = htmlspecialchars($_REQUEST['end']);
		
		$start_date1 = DateTime::createFromFormat('d-m-Y', $start_date);
		$search_start = $start_date1->format('Y-m-d');
		
		$end_date1 = DateTime::createFromFormat('d-m-Y', $end_date);
		$search_end = $end_date1->format('Y-m-d');
		$date_set = 1;
	}
	$s = 1;
}else{
	if($_SESSION['sess_userrole'] == 'cc_user'){
		$cc_id = htmlspecialchars($_SESSION['sess_fid']);
	}else{
		$cc_id = '';
	}
	if($_SESSION['sess_userrole'] == 'manager'){
		$comm_id = htmlspecialchars($_SESSION['sess_fid']);
		//$cc_id = get_value_from_id("commissioners","parent_id","user_id",$comm_id);
		$cc_id = htmlspecialchars(get_value_from_id("formations","parent_id","id",$_SESSION['sess_fid']));
	}else{
		$comm_id = '';
	}
	/*if($_SESSION['role'] == USER_ACCESS){
		$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}*/
}
?>
		<!-- ============================================================== -->
        <!-- Page Content -->
        <!-- ============================================================== -->
        <div id="page-wrapper" class>
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
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
				<?php //if(!($_SESSION['sess_userrole'] == 'manager' || $_SESSION['sess_userrole'] == 'cc_user')){ ?>
				<div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-info">
							<div class="panel-heading">Search All <?=$page_name;?>s</div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                    <form action="" class="form-horizontal search_form" enctype="multipart/form-data" method="get" data-toggle="validator">
                                        <div class="form-body">
                                            <!--<h3 class="box-title">Person Info</h3>
                                            <hr class="m-t-0 m-b-40">-->
                                            <div class="row">
                                                <?php
												if(!($_SESSION['sess_userrole'] == 'cc_user' || $_SESSION['sess_userrole'] == 'manager')){
												?>
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">CC Name<span class="red"></span></label>
                                                        <div class="col-md-8">
															<select onChange="getComm(this.value);" id="c_id" name="c_id" class="form-control">
																<?php
																	echo formation_populate($cc_id, 'cc_user');
																?>
															</select>
														</div>
                                                    </div>
                                                </div>
												<?php
												}else{ ?>
													<input type="hidden" class="c_id" id="c_id" name="c_id" value="<?=$cc_id;?>" />
												<?php
												}
												?>
												<?php
												if($_SESSION['sess_userrole'] != 'manager'){
												?>
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Commissioner<span class="red"></span></label>
                                                        <div class="col-md-8">
															<select id="comm_id" name="comm_id" class="form-control">
																<?php
																	if($s == 1){
																        echo formation_populate($comm_id, 'manager');
																    }else{
																        //echo state_populate();
																		echo '<option value="" disabled="disabled" selected="selected">Select '.$user_roles['manager'].'</option>';
																    }
																?>
															</select>
														</div>
                                                    </div>
                                                </div>
												<?php
												}else{ ?>
													<input type="hidden" class="comm_id" id="comm_id" name="comm_id" value="<?=$comm_id;?>" />
												<?php
												}
												?>
												<div class="col-md-6">
													<div class="form-group">
                                                        <label class="control-label col-md-4">Select Date Range<span class="red"></span></label>
														<div class="col-md-8">
															<div class="input-group input-daterange" id="date-range">
																<input type="text" class="form-control" name="start" value="<?=($s == 1 ? $start_date : '');?>" placeholder="dd-mm-yyyy" autocomplete="off">
																<span class="input-group-addon bg-info b-0 text-white">to</span>
																<input type="text" class="form-control" name="end" value="<?=($s == 1 ? $end_date : '');?>" placeholder="dd-mm-yyyy" autocomplete="off">
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Appeal/Petition No.<span class="red"></span></label>
                                                        <div class="col-md-8">
															<input type="text" class="form-control autocomplete_txt ui-autocomplete-input autocomplete_order_no" autocomplete="off" value="<?=($s == 1 ? $order_no : '');?>" placeholder="Appeal/Petition No." id="order_no" name="order_no">
															 <span class="help-block text-danger"><small>Type & wait to select from suggestions</small></span>
														</div>
                                                    </div>
                                                </div>
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Party Name<span class="red"></span></label>
                                                        <div class="col-md-8">
															<input type="text" class="form-control autocomplete_txt ui-autocomplete-input autocomplete_party" autocomplete="off" value="<?php if($s == 1){ echo $party; } ?>" placeholder="Party" id="party" name="party">
															 <span class="help-block text-danger"><small>Type & wait to select from suggestions</small></span>
														</div>
                                                    </div>
                                                </div>
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">SAMAY ID<span class="red"></span></label>
                                                        <div class="col-md-8">
															<input type="text" class="form-control" value="S<?php if($s == 1){ echo $samay_id; } ?>" placeholder="SAMAY ID" id="samay_id" name="samay_id">
															 <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
												<div class="col-md-6 text-right">
													<div class="form-group "><!-- use class='has-error' for error-->
														<!--<div class="col-md-12">-->
															<button type="reset" name="reset" value="" class="btn btn-info" />Reset</button>
															<button type="submit" id="submit" name="submit" class="btn btn-success">Search</button>
															<input type="hidden" class="orderId" id="orderId" name="orderId" value="" />
														<!--</div>-->
													</div>
												</div>
                                                <!--/span-->
                                                
                                                <!--/span--><?php  ?>
												
                                            </div>
                                            <!--/row-->
										</div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<?php //} ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="white-box">
                            <h3 class="box-title">View <?=$page_name;?>s<?=((isset($type) && $type != 0) ? ': <i style="text-decoration: underline;">'.$dashboard_status[$type].'</i>' : '');?></h3>
                            <!--<p class="text-muted">Add class <code>.table</code></p>-->
                            <div class="table-responsive">
								<?php
									$table_cols = 7;
									$table_cols = ($_SESSION['sess_userrole'] == 'manager' ? $table_cols : ($table_cols+1));
									$table_cols = (($_SESSION['sess_userrole'] == 'cc_user' || $_SESSION['sess_userrole'] == 'board') ? ($table_cols+1) : $table_cols);
								?>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Sl. No.</th>
                                            <th>Samay ID<br/>Classification of Case</th>
                                            <?=(($_SESSION['sess_userrole'] == 'manager') ? '' : '<th>'.$user_roles['manager'].'</th>');?>
                                            <th>Appeal/Petition No.<br/>Party Name</th>
                                            <!--<th>Title</th>-->
											<th>Date</th>
											<th>Court<br/>(CNR)</th>
											<th>LIMBS ID</th>
											<th>Progress</th>
											<!--<th>Update Status</th>-->
											<?=(($_SESSION['sess_userrole'] == 'board') ? '<th>Messages</th>' : (($_SESSION['sess_userrole'] == 'cc_user') ? '<th>Notifications</th>' : ''));?>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									if (isset($_GET["page"])) {
										$pn = $_GET["page"];
									} else {
										$pn = 1;
									}
									$limit = $display_orders_limit;
									$start = $limit*($pn - 1);
									//echo $pn.','.$limit.','.$start;
									$limit_str = " LIMIT ".$start.", ".$limit;
									
									$q = 'SELECT *, O.id AS o_id, OS.id AS os_id, O.is_active as o_active FROM orders O INNER JOIN order_status OS ON O.id=OS.order_id INNER JOIN formations F ON F.id=O.formation_id';
									$order_receiving_date = 'date(O.order_receiving_date)';
									$today_date = "'".date('Y-m-d')."'";
									$dash_query_rem = "Select *, O.id AS o_id, OS.id AS os_id FROM orders O INNER JOIN order_status OS ON O.id=OS.order_id INNER JOIN formations F ON F.id=O.formation_id WHERE DATEDIFF(".$today_date.",".$order_receiving_date.") >= 20 && ";
									$where = ' WHERE';
									$concat = '';
									if(isset($type) && $type != 0){
										if($type == 10){
											$q = $dash_query_rem."(".$dashboard_query[1]." OR ".$dashboard_query[3]." OR ".$dashboard_query[5];
											if(!($userrole == 'cc_user' || $userrole == 'manager')){
												$q .= " OR ".$dashboard_query[6];
											}
											$q .= ")";
										}else{
											//$q .=  $where." order_status=".$type;
											$q .=  $where." ".$dashboard_query[$type];
										}
										$concat = ' &&';
									}else{
										$concat = $where;
									}
									if($_SESSION['sess_userrole'] == 'board'){
										$mapped_court = get_value_from_id("boards","court_type","formation_id",$formation_id);
										$q .= $concat." O.court='".$mapped_court."'";
									}else if($_SESSION['sess_userrole'] == 'cc_user'){
										$q .= $concat." F.parent_id=".$_SESSION['sess_fid'];
									}else if($_SESSION['sess_userrole'] == 'manager'){
										$q .= $concat." O.formation_id=".$_SESSION['sess_fid'];
									}else{
										$q .= $concat." 1=1";
									}
									//if(!($_SESSION['sess_userrole'] == 'manager' || $_SESSION['sess_userrole'] == 'cc_user')){
										/*if(!empty($comm_id)){
											$q .= ' && C.user_id='.$comm_id;
										}else if(!empty($cc_id)){
											$q .= ' && C.parent_id='.$cc_id;
										}*/
										if(!empty($comm_id)){
											$q .= ' && O.formation_id='.$comm_id;
										}else if(!empty($cc_id)){
											$q .= ' && F.parent_id='.$cc_id;
										}
									//}
									//echo $search_start;
									if($date_set == 1){
										//echo $start."<br/>".$end."<br/>";
										if($search_start == $search_end){
											$q .= ' && date(O.order_commence_date)="'.$search_start.'"';
										}else{
											$q .= ' && date(O.order_commence_date)>="'.$search_start.'" && date(O.order_commence_date)<="'.$search_end.'"';
										}
									}
									if($s == 1 && $party !=''){
										$q .= ' && O.party LIKE "%'.$party.'%"';
									}
									if($s == 1 && $orderId !=''){
										$q .= ' && O.id='.$orderId;
									}
									if($s == 1 && $samay_id !=''){
										$q .= ' && O.id='.$samay_id;
									}
									if($userrole != 'admin'){
										$q .= ' && O.is_active=1';
									}
									$q_all = $q;
									$q .= ' ORDER BY O.id DESC'.$limit_str;
									//echo $q;
									$totalRecords = $myPDO->query($q_all)->rowCount();
									$totalPages = ceil($totalRecords / $limit);
									//echo $totalRecords.','.$totalPages;
									//$i = 1;
									$i = $start+1;
									$final_stmt = $myPDO->query($q);
									if($final_stmt->rowCount() > 0){
										foreach($final_stmt as $row){
											//$comm_name = ($_SESSION['sess_userrole'] == 'manager') ? '' : '<td>'.get_value_from_id("commissioners","name","user_id",$row['user_id']).'</td>';
											if($userrole == 'admin'){
												$o_active = "<form method='post'><input type='hidden' id='c_id' name='c_id' value='{$row['id']}' ><input type='hidden' id='active' name='active' value='{$row['o_active']}' ><button type='submit' name='btn_active' class='label label-".(($row['o_active'] == 1)? "success'>Active" : "danger'>Inactive")."</button></form>";
											}
											$comm_name = ($_SESSION['sess_userrole'] == 'manager') ? '' : '<td>'.$row['formation'].'</td>';
											$second_date_head = (($row['court'] == 'high') ? 'Upload Date' : 'Receipt Date' );
											$display_order_status = display_order_status($row['o_id']);
											$final_status = "<b>".$display_order_status[1]."</b>";
											
											if($display_order_status[1] == 'Appeal Decided'){
												$final_status .= "<br/><u><i>Date:</i></u> <b>".date('d-M-y', strtotime($row['appeal_status_date']))."</b>";
											}
											$row_order_no = "";
											//if($_SESSION['sess_userrole'] == 'cc_user'){
											if($display_order_status[2] == 'Y' && $_SESSION['sess_userrole'] != 'manager'){
												$row_order_no .= "<a href='add_case_order.php?edit=".$row['o_id']."'>";
											}
											$row_order_no .= "<b>{$row['order_no']}</b>";
											//if($_SESSION['sess_userrole'] == 'cc_user'){
											if($display_order_status[2] == 'Y' && $_SESSION['sess_userrole'] != 'manager'){
												$row_order_no .= " <i class='fa fa-pencil'></i></a>";
											}
											$row_order_no .= "<br/>{$row['party']}";
											echo "<tr>
													<td>{$i}</td>
													<td><b>S{$row['o_id']}</b><br/>".get_value_from_id("classification_case","case_class","id",$row['coc_id']).($userrole == 'admin' ? "<br/>".$o_active : '')."</td>
													{$comm_name}
													<td>{$row_order_no}</td>
													<!--<td>{$row['order_title']}</td>-->
													<td class='td_date'><span><b>Date of Order:</b> ".date('d-M-y', strtotime($row['order_commence_date']))."</span><br/>
													<span><b>".$second_date_head.":</b> ".date('d-M-y', strtotime($row['order_receiving_date']))."</span></td>
													<td>{$courts[$row['court']]}".($row['bench_id'] > 0 ? '<br/>'.get_value_from_id("benches","bench_name","id",$row['bench_id']) : '').($row['court'] == 'high' ? '<br/><b>'.$row['cnr'].'</b>' : '')."</td>
													<td>".($row['lims'] != 0 ? '<b>'.$row['lims'].'</b>' : '-')."</td>
													<td>{$display_order_status[0]}{$final_status}</td>"./*.
													<td>{$row['order_status']}</td>"./*.
													<td><span class='label ".(($row['is_active'] == 1)? "label-success label-rouded'>Active" : "label-danger label-rouded'>Inactive")."</span></td>".*/"
													<!--<td>".$final_status."</td>-->";
											?>
											<?=(($_SESSION['sess_userrole'] == 'high' || $_SESSION['sess_userrole'] == 'tribunal' || $_SESSION['sess_userrole'] == 'cc_user') ? '<td>'.display_order_messages($row['o_id']).'</td>' : '');?>
											<?php
											/*echo '<form action="progress.php" method="post">
													<input type="hidden" name="order_id" value="'.$row['o_id'].'">
													<button type="submit" name="submit" class="btn btn-xs btn-info" value=""><i class="fa fa-eye" aria-hidden="true"></i> View</button>
												</form>';*/
											echo "<!--<a href=add_case_order.php?edit={$row['o_id']}><button type='button' class='btn btn-info btn-outline btn-circle btn-lg m-r-5'><i class='ti-pencil-alt'></i></button></a></td>
													<td><a href=add_case_order.php?del={$row['o_id']} onclick='return confirmDelete();'><button type='button' class='btn btn-danger btn-delete'><i class='ti-trash'></i></button></a></td>-->
												</tr>";
										$i++;
										}
									}else{
										echo '<tr><td colspan="'.$table_cols.'"><h2 class="text-center">No Records Found with the selected criteria!!!</h2></td></tr>';
									}
									?>
                                    </tbody>
                                </table>
                            </div>
							<?php if($totalPages > 1){ ?>
							<div class="pagination_container"> 			
								<div class="pagination"> 			
									<?php
									//$queryString = '?';
									if ($pn > 1) {
										$prev_page = addToUrl($current_url,'page',$pn-1);
										?>
										<a class="previous-page" id="prev-page" href="<?=$prev_page;?>" title="Previous Page"><span>❮ Prev</span></a>
									<?php
									}
									if (($pn - 1) > 1) {
										$first_page = addToUrl($current_url,'page',1);
										?>
										<a href='<?=$first_page;?>'><div class='page-a-link'> 1 </div></a>
										<div class='page-before-after'>...</div>
									<?php 
									}

									for ($i = ($pn - 1); $i <= ($pn + 1); $i ++) {
										if ($i < 1)
											continue;
										if ($i > $totalPages)
											break;
										if ($i == $pn) {
											$class = "active";
										} else {
											$class = "page-a-link";
										}
										$middle_page = addToUrl($current_url,'page',$i);
										?>
										<a href='<?=$middle_page;?>'>  <div class='<?php echo $class; ?>'><?php echo $i; ?></div> </a>
										<?php 
									}

									if (($totalPages - ($pn + 1)) >= 1) {
										?>
										<div class='page-before-after'>...</div>
									<?php 
									}
									if (($totalPages - ($pn + 1)) > 0) {
										if ($pn == $totalPages) {
											$class = "active";
										} else {
											$class = "page-a-link";
										}
										$last_page = addToUrl($current_url,'page',$totalPages);
										?>
										<a href='<?=$last_page;?>'><div class='<?php echo $class; ?>'><?php echo $totalPages; ?></div></a> 
										<?php 
									}
									if ($pn < $totalPages) {
										$next_page = addToUrl($current_url,'page',$pn+1);
										?>
												<a class="next" id="next-page"
													href="<?=$next_page;?>"
													title="Next Page"><span>Next ❯</span></a> 
										<?php
									}
									?>
								</div>
							</div>
							<?php } ?>
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
			
			<div class="modal fade" id="changeModal" >
				<div class="modal-dialog printableArea" id="printableArea">
					<div class="modal-content">
						<!-- Modal Header -->
						<div class="modal-header">
							<div class="row register-form">
								<h4 class="modal-title text-center">Update Status</h4>
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							</div>
						</div>
						<div class="modal-body">
							<div class="error"></div>
							<div class="success"></div>
							
							<form class="form-horizontal" action="" method="post" name="change_status_form" enctype="multipart/form-data" data-toggle="validator">
								<div class="form-group row">
									<div class="col-md-12">
										
									</div>
								</div>
								<div class="form-group row row_change_status">
									<label class="control-label col-xs-6">
										<span class="label_span">Change Status</span><span class="red">*</span>:
									</label><?php /*?><input type="text" class="form-control mydatepicker appeal_decided_date" value="" placeholder="" id="appeal_decided_date" name="appeal_decided_date" autocomplete="off" required><?*/?>
									<div class="col-xs-6 select_change_status">
										
									</div>
								</div>
								<div class="form-group row print_row">
									<div class="col-md-12 text-center">
										<input type="hidden" name="update_column" id="update_column">
										<input type="hidden" name="order_id" id="order_id">
										<button type="submit" id="submit_update" name="submit_update" class="btn btn-success" onclick="return confirmStatusUpdate();">Update</button>
										<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			
			<div class="modal fade" id="display_msgs" >
				<div class="modal-dialog printableArea" id="printableArea">
					<div class="modal-content">
						<!-- Modal Header -->
						<div class="modal-header">
							<div class="row register-form">
								<h4 class="modal-title text-center font-bold"><?=(($_SESSION['sess_userrole'] == 'cc_user') ? 'Notifications' : 'Messages')."<br/>(Order No.: <span class='text-danger'></span>)";?></h4>
								<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							</div>
						</div>
						<div class="modal-body">
							<div class="error"></div>
							<div class="success"></div>
							<?php
							if($_SESSION['sess_userrole'] != 'cc_user'){
							?>
							<form class="form-horizontal" action="" method="post" name="otp" enctype="multipart/form-data" novalidate="novalidate" >
								<div class="form-group row row_comment">
									<?php /*?><label class="control-label col-xs-4">
										<span class="label_span">Message</span><span class="red">*</span>:
									</label><input type="text" class="form-control mydatepicker appeal_decided_date" value="" placeholder="" id="appeal_decided_date" name="appeal_decided_date" autocomplete="off" required><?*/?>
									<div class="col-xs-12 comment_div">
										<textarea name="comment" id="comment" rows="1" placeholder="Enter your comment..." required></textarea>
										<input type="hidden" name="update_column2" id="update_column2">
										<input type="hidden" name="order_id2" id="order_id2">
										<button type="submit" id="submit_comment" name="submit_comment" class="btn btn-success send-button" alt="Send"><i class="fa fa-paper-plane"></i></button>
										<!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
									</div>
								</div>
								
							</form>
							<?php
							}
							?>
							<div class="form-group row chats_row">
								<div class="col-md-12">
									
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
            <?php include 'copyright.php'; ?>
        </div>
        <!-- /#page-wrapper -->

<?php
include ROOT_DIR_COMMON.'footer.php';
?>
<style type="text/css">
.comment_div{
	position: relative;
	padding: 15px 25px 0;
}
.comment_div textarea{
	width: 100%;
    border-radius: 22px;
    resize: none;
    padding: 5px 15px;
    line-height: 32px;
    font-size: 18px;
	overflow: hidden;
}
.send-button{
	position: absolute;
	bottom: 9px;
	right: 18px;
	height: 38px;
	border-radius: 50%;
	width: 38px;
    padding: 6px;
    font-size: 16px;
}
.msg_row{
	margin: 10px;
}
.msg_row_head{
	text-align: center;
	text-transform: capitalize;
	padding: 5px 10px;
	background: #2f323e;
	color: #fff;
	border-radius: 5px;
	margin: 20px auto 10px;
	font-weight: bold;
	display: table;
}
.msg_inner_row{
	background: #edf1f5;
	padding: 10px 20px;
	border-radius: 10px;
	margin-bottom: 5px;
	position: relative;
}
.msg_inner_row_msg{
	overflow-wrap: break-word;
    white-space: pre-wrap;
}
.msg_inner_row_space{
	width: 56px;
	display: inline-block;
    width: 54px;
    vertical-align: middle;
}
.msg_inner_time{
	position: absolute;
	right: 10px;
	bottom: 5px;
	font-size: 11px;
	float: right;
}
.td_date span{
	white-space: nowrap;
}
.wrap_ul{
	position: relative;
	padding-right: 30px;
}
.wrap_ul .overlay{
	position: absolute;
	width: 26px;
    padding: 3px;
    text-align: center;
    bottom: -2px;
    top: auto;
    right: 0;
    /*background: linear-gradient(0deg, #76767647, #cdcdcd54, transparent);*/
	background: #ff6683;
    border-radius: 5px;
	cursor: pointer;
}
.orders_status{
	max-height: 40px;
	overflow: hidden;
	transition: all 1s ease;
}
.expand .orders_status{
	max-height: 300px;
}
</style>
<script type="text/javascript">
$(".wrap_ul .overlay").click(function () {
	var wrap_ul_elem = $(this).parent();
	var eye_elem = $(this).parent().find(".fa");
	if($(wrap_ul_elem).hasClass("expand")){
		$(eye_elem).removeClass("fa-eye-slash").addClass("fa-eye").attr("title","Expand");
		$(wrap_ul_elem).removeClass('expand');
	}else{
		//$(ul_elem).css('overflow','auto');
		$(eye_elem).removeClass("fa-eye").addClass("fa-eye-slash").attr("title","Close");
		$(wrap_ul_elem).addClass('expand');
	}
});
function IsNumberOnly(key1) {
	var key_code_int = key1['which'] ? key1['which'] : key1['keyCode'];
	//console['log'](key_code);
	var rt_key_int = (key_code_int >= 48 && key_code_int <= 57);
	return rt_key_int;
}
function confirmDelete(){
   return confirm("Are you sure you want to delete this?");
}
function confirmStatusUpdate(){
	return confirm("Are you sure you want to change the Order Status?");
}
$('#date-range').datepicker({
	/*locale: {
	  format: 'YYYY-MM-DD'
	},
	toggleActive: true*/
	format: "dd-mm-yyyy",
	autoclose: true,
	todayHighlight: true,
});
<?php $countCC = 0; ?>
function getComm(val) {
	
	<?php
	$countCC++;
	//if((isset($_REQUEST['submit']) && $cc_id == '') || (!isset($_REQUEST['submit']))){
	?>
		//console.log(<?=$countCC;?>);
		//$("#order_no").val('');
		$("#orderId").val('');
	<?php //} ?>
	if(val == ''){
		$("#comm_id").html('<option value="" selected="selected">Select <?=$user_roles['manager'];?></option>');
	}else{
		$.ajax({
			type: "POST",
			url: "get_formations.php",
			data:'cc_id='+val+'&comm_id=<?=(isset($_REQUEST['submit'])) ? $comm_id : '';?>',
			success: function(data){
						$("#comm_id").html(data);
					}
		});
	}
}
<?php
if(isset($_REQUEST['submit']) || $_SESSION['sess_userrole'] == 'cc_user' || $_SESSION['sess_userrole'] == 'manager'){ ?>
	getComm(<?=$cc_id;?>);
<?php } ?>
	$("button[name=reset]").on('click', function(){
		var selected_c_id = $("#c_id option:selected").attr("value");
		console.log(selected_c_id);
		$("#c_id option[value="+selected_c_id+"]").removeAttr('selected');
		$("#c_id option:first").attr('selected', 'selected');
		$("#comm_id").html('<option value="" selected="selected">Select <?=$user_roles['manager'];?></option>');
	});
var order_no = $("#order_no").val();
$(document)['on']('focus', '.autocomplete_txt', function() {
    //var type = $(this)['data']('type');
    var type = $(this).attr('id');
	//var type_name = '';
    if (type == 'order_no') {
        autoTypeNo = 1;
		type_name = 'order_no';
    }
    if (type == 'party') {
        autoTypeNo = 2;
		//autoTypeNo = 0;
		type_name = 'party';
    }
	///var type_name = 'order_no';
	//var invoice_type = $("#bill_type").val();
	var cc_id = $("#c_id").val();
	var comm_id = $("#comm_id").val();
	//+'&comm_id=<?=(isset($_REQUEST['submit'])) ? $comm_id : '';?>',
	//var autoTypeNo = 1;
    $(this)['autocomplete']({
        source: function(request, response) {
			$.ajax({
				url : 'get_order_title_2.php',
				dataType: "json",
				//method: 'post',
				data: {
					term: request.term,
					type: type_name,
					//invoice_type: invoice_type
					cc_id: cc_id,
					comm_id: comm_id
				},
				success: function( data ) {
					response( $.map( data, function( item ) {
						//console.log(data);
						var code = item.split("|");
							return {
								label: code[autoTypeNo],
								value: code[autoTypeNo],
								data : item
							}
						}));
					}
				});
        },
        autoFocus: true,
        minLength: 1,
        select: function(event, ui) {
            var product = ui['item']['data']['split']('|');
            
			console.log("product"+product);
            //id_arr = $(this)['attr']('id');
			//console.log("id_arr"+id_arr);
            //id = id_arr['split']('_');
            //element_id = id[id['length'] - 1];
			$("#orderId").val(product[0]);
			
        }
    });
});

$('.change_status').on('click', function() {
    var id = $(this).data('id');
	$(".date_row").remove();
	$(".lims_row").remove();
	var date_input = '<div class="form-group row date_row">';
		date_input += '		<label class="control-label col-xs-6">';
		date_input += '			Appeal Date<span class="red">*</span>:';
		date_input += '		</label>';
		date_input += '		<div class="col-xs-6">';
		date_input += '			<input type="text" class="form-control mydatepicker appeal_decided_date" placeholder="" id="appeal_decided_date" name="appeal_decided_date" autocomplete="off" required>';
		date_input += '		</div>';
		date_input += '	</div>';
		
	var lims_input = '<div class="form-group row lims_row">';
		lims_input += '		<label class="control-label col-xs-6">';
		lims_input += '			LIMBS ID<span class="red"></span>:';
		lims_input += '		</label>';
		lims_input += '		<div class="col-xs-6">';
		lims_input += '			<input type="text" class="form-control lims_id" placeholder="" id="lims_id" name="lims_id" minlength="8" maxlength="8" onkeypress="return IsNumberOnly(event);" autocomplete="off">';
		lims_input += '		</div>';
		lims_input += '	</div>';
	$("#update_column").val();
	$.ajax({
			url : 'change_status.php',
			type : 'POST',
			//crossDomain: true,
			async: false,
			data : 'order_id='+id,
			success: function(data){
						var result = JSON.parse(data);
						
						if(result.stat == 'false'){
							//arr_invalid.push(sl_no);
							//console.log(arr_invalid);
						}else{
							$(".select_change_status").html(result.output);
							if(result.update_column == 'proposal_status'){
								$(".label_span").html('Change Status');
								if(result.lims == 'y'){
									$(".label_span").html('File Proceed to Board');
									$(lims_input).insertAfter(".row_change_status");
								}
								show_div_lims();
							}
							if(result.update_column == 'appeal_status'){
								$(date_input).insertAfter(".row_change_status");
								var currDate = new Date();
								///console.log(currDate);
								var todayDate = new Date().getDate();
								///console.log(todayDate);
								//var endD= new Date(new Date().setDate(todayDate - 15));
								var from = result.r_date.split("-")
								///console.log(from);
								var endD1 = new Date(from[0], from[1] - 1, from[2]);
								///console.log("END: "+endD1);
								$('#appeal_decided_date').datepicker({
									format: "dd-mm-yyyy",
									autoclose: true,
									//todayHighlight: true,
									startDate : endD1,
									//endDate : currDate
								});
								$('#appeal_decided_date').datepicker('setDate', endD1);
								//$(".mydatepicker").datepicker().datepicker("setDate", new Date("<?=date('m/d/Y');?>"));
								show_div();
							}
							
							$("#update_column").val(result.update_column);
							$("#order_id").val(result.order_id);
							
						}
						/*$("#stateGstinId").val(result.id);
						$("#hotel_gst_address").html(result.address);
						$("#hotel_gstin").val(result.gstin);*/
					}
	});
});
$(document).on('change', '#change_select', function(){
	//show_checkboxes();
	show_div();
	show_div_lims();
});
$(window).on('load', function() {
	show_div();
	show_div_lims();
});
function show_div(){
	var $showdiv = $(".date_row");
	//$showdiv.slideToggle();
	//$('#change_select').find("option:selected").each(function(){
		var optionValue = $('#change_select').find("option:selected").attr("value");
		if(optionValue == 2){
			$("#appeal_decided_date").attr('required',true);
			$showdiv.slideDown();
		} else{
			$("#appeal_decided_date").attr('required',false);
			$showdiv.slideUp();
		}
	//});
}

function show_div_lims(){
	var $showdiv = $(".lims_row");
	//$showdiv.slideToggle();
	//$('#change_select').find("option:selected").each(function(){
		var optionValue = $('#change_select').find("option:selected").attr("value");
		if(optionValue == 2){
			//$("#lims_id").attr('required',true);
			$showdiv.slideDown();
		} else{
			//$("#lims_id").attr('required',false);
			$showdiv.slideUp();
		}
	//});
}

$('.display_msgs').click(function() {
	var elem = $(this);
    var id = elem.data('id');
	console.log(id)
	$("#order_id2").val(id);
	$.ajax({
			url : 'display_msgs.php',
			type : 'POST',
			//crossDomain: true,
			async: false,
			data : 'order_id='+id,
			success: function(data){
						var result = JSON.parse(data);
						//console.log(result);
						$("#display_msgs .modal-title .text-danger").html(result.order_no);
						$(elem).find(".new_span_holder").hide();
						var html_msg = '';
						for(var i = 0; i < result.chats.length; i++){
							html_msg += '<div class="msg_row">';
							html_msg += '<div class="msg_row_head">'+result.chats[i].day+'</div>';
							for(var j = 0; j < result.chats[i].comments.length; j++){
								//console.log(result.chats[i].comments[j]);
								html_msg += '<div class="msg_inner_row">';
								html_msg += '	<span class="msg_inner_row_msg">'+result.chats[i].comments[j][0]+'</span>';
								html_msg += '	<span class="msg_inner_row_space"></span>';
								html_msg += '<div class="msg_inner_time">'+result.chats[i].comments[j][1]+'</div>';
								html_msg += '</div>';
							}
							html_msg += '</div>';
						}
						//$(html_msg).insertAfter($("#display_msgs .chats_row"));
						$("#display_msgs .chats_row .col-md-12").html(html_msg);
					}
	});
});
	/*$('.mydatepicker').datepicker({
		format: "dd-mm-yyyy",
		autoclose: true,
		todayHighlight: true,
	});
	$(".mydatepicker.appeal_decided_date").datepicker().datepicker("setDate", new Date("<?=((isset($_GET['edit'])) ? date('m/d/Y',strtotime($row_edit['appeal_decided_date'])) : date('m/d/Y'));?>"));*/

	<?php
	$pageWasRefreshed = isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0';
	if((isset($_GET['sent']) && $_GET['sent'] == 1 && isset($_SERVER['HTTP_REFERER'])) && !$pageWasRefreshed){ ?>
	$.toast({
			text: 'Message Sent',
			heading: 'Success',
			showHideTransition: 'slide',
			allowToastClose: true,
			hideAfter: 2000,
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
	<?php if((isset($_GET['insert']) && $_GET['insert'] == 1 && isset($_SERVER['HTTP_REFERER'])) || (isset($_GET['update']) && $_GET['update'] == 1 && isset($_SERVER['HTTP_REFERER'])) || (isset($_GET['error']) && $_GET['error'] == 'y' && isset($_SERVER['HTTP_REFERER']))){ ?>
	$.toast({
        text: '<?php if(isset($_GET['insert'])){ echo $page_name.' added Successfully'; }else if(isset($_GET['update'])){ echo $page_name.' Updated Successfully'; }else if(isset($_GET['error'])){ echo 'Do not play with me!'; } ?>',
        heading: '<?=(isset($_GET['error']) ? 'Stop!' : 'Success');?>',
        showHideTransition: 'slide',
        allowToastClose: true,
        hideAfter: 3000,
        loader: true,
        loaderBg: '<?=(isset($_GET['error']) ? '#e16572' : '#6dce8e');?>',
        stack: 5,
        position: 'top-center',
        bgColor: '<?=(isset($_GET['error']) ? '#f64e60' : '#148d3d');?>',
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