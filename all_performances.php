<?php
include 'init.php';

 
$requiredRole = USER_ACCESS;
if (! isAuthorized($requiredRole) || $_SESSION['sess_userrole'] == 'manager') {
  header('Location: dashboard.php');
}
$php_self = htmlspecialchars($_SERVER['PHP_SELF']);
if(isset($_POST['form_submit'])){
	include ROOT_DIR_COMMON.'functions.php';
	$label_arr1 = json_decode($_POST['label_arr'], true);
	$widthArr1 = json_decode($_POST['widthArr'], true);
	$numberFormatArr1 = json_decode($_POST['numberFormatArr'], true);
	$rowData1 = json_decode($_POST['rowData'], true);
	$filename1 = "Performance_Report_".date('dmy_His').".xlsx";
	//echo $_POST['rowTest'];
	//print_r($label_arr1);
	//echo "Exporting";
	export_to_excel($label_arr1, $widthArr1, $numberFormatArr1, $rowData1, $filename1);
}

include ROOT_DIR_COMMON.'header.php';
if($_SESSION['sess_userrole'] == 'admin' || $_SESSION['sess_userrole'] == 'board'){
	//$page_name = $user_roles['cc_user'];
	$page_name = 'Performance Report';
	//$order_end_date = 'IFNULL(OS.cc_date,CURDATE())';	//ROUND(AVG(DATEDIFF(IFNULL(OS.cc_date,CURDATE()),date(O.order_receiving_date))), 2)
	//$order_end_date = 'IF(OS.proposal_status = 2,OS.comm_date,IFNULL(OS.cc_date,CURDATE()))';	
	$order_end_date = 'IF(OS.comm_status = 2,OS.comm_date, IF(OS.cc_status = 1, OS.cc_date, IF(OS.proposal_status = 2, OS.proposal_date, IFNULL(OS.proposal_date,CURDATE()))))';
	//ROUND(AVG(DATEDIFF(IFNULL(OS.cc_date,CURDATE()),date(O.order_receiving_date))), 2)
	//ROUND(AVG(DATEDIFF(IF(OS.comm_status = 2,OS.comm_date,IFNULL(OS.cc_date,CURDATE())),date(O.order_receiving_date))), 2)
}else if($_SESSION['sess_userrole'] == 'cc_user'){
	$page_name = $user_roles['manager'];
	$order_end_date = 'OS.comm_date';
}else{
	header('Location: dashboard.php');
}
$show_all_courts = 0;
if($_SESSION['sess_userrole'] == 'admin' || $_SESSION['sess_userrole'] == 'cc_user'){
	$show_all_courts = 1;
}
$s = 0;
if(isset($_POST['submit'])){
	//$centre_id = $_POST['c_id'];
	$s = 1;
	$r_type = (isset($_POST['r_type']) ? $_POST['r_type'] : '');
	$month = (isset($_POST['month']) ? $_POST['month'] : '');
	$quarter = htmlspecialchars(isset($_POST['quarter']) ? $_POST['quarter'] : '');
	//$req_year = 
	$year = (isset($_POST['year']) ? $_POST['year'] : '');
	$period = ((isset($_POST['r_type']) && $_POST['r_type'] != "Annually") ? ($_POST['r_type'] == "Quarterly" ? $quarter : $month ) : '');
}else{
	/*if($_SESSION['sess_userrole'] == 'cc_user'){
		$centre_id = $_SESSION['sess_user_id'];
	}else{
		$centre_id = '';
	}*/
	$r_type = 'Annually';
	$month = date('m', strtotime("-1 months"));
	$quarter = '';
	//$req_year1 = (date('n') >= 4 ? date('Y') : date('Y') + 1);
	//$req_year2 = (date('n') >= 4 ? date('Y') : date('Y') + 1);
	$year = (date('n') >= 4 ? date('Y').'-'.substr((date('Y') + 1),-2) : (date('Y') - 1).'-'.substr(date('Y'),-2));
}
//echo date('m', strtotime('Apr'));
if($r_type == 'Quarterly'){
	$page_heading_date = $quarter.' of '.$year;
}else if($r_type == 'Annually'){
	$page_heading_date = "Year ".$year;
}else{
	$yy = explode("-",$year)[0];
	$mo = ltrim($period, "0");
	$re_yr = $mo >= 4 ? $yy : $yy + 1;
	$page_heading_date = date('F Y',strtotime(date($re_yr.'-'.$month.'-01')));
}
$page_heading = $page_name.' for <span class="text-danger">'.$page_heading_date.'</span>';
/*if(isset($_GET['role']) && $_GET['role'] != ''){
	$role = $_GET['role'];
	$page_name = $user_roles[$role];
}else{
	//$page_name = 'manager';
	header('Location: dashboard.php');
}*/

?>
		<!-- ============================================================== -->
        <!-- Page Content -->
        <!-- ============================================================== -->
        <div id="page-wrapper" class="print_page">
            <div class="container-fluid">
                <div class="row bg-title">
                    <div class="col-xs-12">
                        <h4 class="page-title"><?=$page_name;?></h4> </div>
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
				<?php //if($_SESSION['role'] == ADMIN_ACCESS || $_SESSION['role'] == ADMIN_USER_ACCESS){ ?>
				<div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-info">
							<div class="panel-heading">Search <?=$page_name;?></div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                    <form action="" class="form-horizontal" enctype="multipart/form-data" method="post" data-toggle="validator">
                                        <div class="form-body">
                                            <!--<h3 class="box-title">Person Info</h3>
                                            <hr class="m-t-0 m-b-40">-->
                                            <div class="row">
                                                <?php /*?><div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Name<span class="red">*</span></label>
                                                        <div class="col-md-9">
															<select id="c_id" name="c_id" class="form-control" required>
																<?php echo commissioner_populate($centre_id, $role); ?>
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
                                                <!--/span--><?php */ ?>
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-6">Report Type<span class="red">*</span></label>
                                                        <div class="col-md-6">
															<select id="r_type" name="r_type" class="form-control" required>
																<?php echo report_type_populate($r_type); ?>
															</select>
															 <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-6">Select Month<span class="red">*</span></label>
                                                        <div class="col-md-6">
															<select id="month" name="month" class="form-control" required>
																<?php echo months_populate($month); ?>
															</select>
															 <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-6">Select Quarter<span class="red">*</span></label>
                                                        <div class="col-md-6">
															<select id="quarter" name="quarter" class="form-control" required>
																<?php echo quarter_populate($quarter); ?>
															</select>
															 <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
												<div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-6">Select Year<span class="red">*</span></label>
                                                        <div class="col-md-6">
															<select id="year" name="year" class="form-control" required>
																<?php
																	//echo year_populate($year);
																	echo fy_year_populate($year);
																?>
															</select>
															 <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group"><!-- use class='has-error' for error-->
                                                        <div class="col-12 text-center">
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
				<?php //} ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="panel panel-info printableArea" id="printableArea">
							<div class="panel-heading">
								<div class="row">
									<div class="col-xs-6">
										<?=$page_heading;?>
									</div>
									<?php if($_SESSION['sess_userrole'] != 'manager'){ ?>
									<div class="col-xs-6 text-right nc_holder">
										<button id="export_all" name="export_all" class="btn btn-danger right_header_btn export_all print-hide" onclick="export_excel();"><i class="fa fa-download" aria-hidden="true"></i> Export in Excel</button><!--data-toggle='modal' data-target='#newBatchModal'-->
										<button id="print1" class="btn btn-danger right_header_btn print-hide" type="button"><i class="fa fa-print" aria-hidden="true"></i> Print</button>
									</div>
									<?php } ?>
								</div>
							</div>
                            <div class="panel-wrapper">
								<div class="panel-body">
									<div class="table-responsive">
										<table class="table compact_table">
											<thead>
												<tr>
												<?php
												$label_arr = array(
																'sl_no'=>'Sl. No.',
																'user_type'=>$page_name,
															);
												?>
													<th>#</th>
													<th><?=$page_name?></th>
													<?php
														if($show_all_courts == 1){
															echo "<th>Court</th>";
															$label_arr['court'] = 'Court';
														}
														$label_arr['orders'] = 'No. of Orders';
														$label_arr['avg_dur'] = 'Average Duration';
													?>
													<th>No. of Orders</th>
													<!--<th>Bank Info</th>
													<th>Contact Person</th>-->											
													<?php /* ?><th>Status</th><?php */ ?>
													<th>Average Duration</th>
												</tr>
											</thead>
											<tbody>
											<?php
											if($r_type == "Annually"){
												$req_year = required_year($r_type, $year);
											}else{
												$req_year = required_year($r_type, $year, $period);
											}
											
											//print_r($req_year);
											//echo date('n');
											//echo 'gfdhg'.date('n', strtotime(date('Y-m-d')));
											$start_date = $req_year[0];
											$end_date = $req_year[1];
											
											//$month_days = date('t', strtotime(date($req_year.'-'.$month.'-01')));
											//echo $month_days;
											//$month = date('m', strtotime("-1 month"));
											
											//$start_date = $req_year.'-'.$month.'-01';
											//$end_date = '2021-'.date('m').'-'.date('n');
											//$end_date = $req_year.'-'.$month.'-'.$month_days;	//cal_days_in_month(CAL_GREGORIAN,date('m', strtotime("-1 month")),date('Y'));
											//echo $end_date;
											$order_receiving_date = 'date(O.order_receiving_date)';
											//$q0 = "SELECT *, A.id AS user_main_id FROM users A INNER JOIN commissioners C ON C.user_id = A.id INNER JOIN charges_table CT ON CT.user_id=A.id INNER JOIN formations F ON F.id = CT.formation_id WHERE A.is_active=1 && CT.charge_status=1";
											$q0 = "SELECT * FROM formations WHERE 1=1";
											if($_SESSION['sess_userrole'] == 'cc_user'){
												$q0 .= " && parent_id=".$_SESSION['sess_fid'];
											}else{
												$q0 .= " && role='cc_user'";
											}
											//echo $q0; die();
											$i = 1;
											$courts_len = count($courts);
											$rowData = array();	//For Excel
											$erow = 2;
											foreach($myPDO->query($q0) as $row0){
												$c_name = $row0['formation'];
												echo "<tr>
														<td ".(($show_all_courts == 1) ? 'rowspan='.$courts_len : '').">{$i}</td>
														<td ".(($show_all_courts == 1) ? 'rowspan='.$courts_len : '').">{$c_name}</td>";
												$court_counter = 1;
												
												foreach($courts as $court_key=>$court_val){
													$row_arr = array();		//Initialize row for excel
													if($court_counter == 1){
														$row_arr[] = $i;
														$row_arr[] = $c_name;
													}else{
														$row_arr[] = '';
														$row_arr[] = '';
													}
													
													$q = "SELECT O.court, COUNT(O.id) AS num_of_orders, ROUND(AVG(DATEDIFF(".$order_end_date.",".$order_receiving_date.")), 2) AS avg_days FROM orders O INNER JOIN order_status OS ON O.id=OS.order_id INNER JOIN formations F ON F.id=O.formation_id WHERE date(O.order_receiving_date)>='".$start_date."' && date(O.order_receiving_date)<='".$end_date."' && O.is_active=1";
													//echo date('m',(strtotime("-2 month")))."<br/>";
													//echo date('t');	//Calendar days in current month
													//echo cal_days_in_month(CAL_GREGORIAN,date('m'),date('Y')); //Calendar days in a given month
													$groupby = '';
													if($_SESSION['sess_userrole'] == 'admin'){
														$q .= " && O.court='".$court_key."' && F.parent_id=".$row0['id']." GROUP BY F.parent_id, O.court";
													}else if($_SESSION['sess_userrole'] == 'board'){
														$mapped_court = get_value_from_id("boards","court_type","formation_id",$formation_id);
														$q .= " && O.court='".$mapped_court."' && F.parent_id=".$row0['id']." GROUP BY F.parent_id";
													}else if($_SESSION['sess_userrole'] == 'cc_user'){
														$q .= " && O.court='".$court_key."' && F.id=".$row0['id']." GROUP BY F.id, O.court";
													}
													//echo $q."<br/><br/>";
													//die();
													$matched = 0;
													if($mapped_court == $court_key){
														continue;
														$matched = 1;
													}
													$stmt_perform = $myPDO->query($q);
													if($stmt_perform->rowCount() > 0) {
														$row = $stmt_perform->fetch(PDO::FETCH_ASSOC);
													}else{
														$row['court'] = $court_key;
														$row['num_of_orders'] = 0;
														$row['avg_days'] = '-';
													}
													//foreach($myPDO->query($q) as $row){
														$no_of_orders = $row['num_of_orders'];
														$avg_days = (($row['avg_days'] != '-') ? (($row['avg_days'] >= 1) ? $row['avg_days'].' days' : 0) : 'NA');
														//$avg_days = $row['avg_days'];
														if($show_all_courts == 1){
															echo "<td>{$courts[$row['court']]}</td>";
															$row_arr[] = $courts[$row['court']];
														}
														
														echo "	<td>$no_of_orders</td>
																<td>{$avg_days}</td>";
														if($matched == 0){
															echo "</tr><tr>";
														}
														$row_arr[] = $no_of_orders;
														$row_arr[] = $avg_days;
													//}
													$court_counter = 0;
													$rowData[$erow] = $row_arr;
													$erow++;
												}
												echo "</tr>";
												$i++;
											}
											//echo "<pre>";
											//print_r($rowData);
											if($show_all_courts == 1){
												$widthArr = array(
																'B'=>15,
																'C'=>20,
																'D'=>25,
																'E'=>25,
															);
											}else{
												$widthArr = array(
																'B'=>15,
																'C'=>25,
																'D'=>25,
															);
											}
											$numberFormatArr = array(
															//'B'=>'0',
														);
											?>
											</tbody>
										</table>
										<form name="report" id="report" action="<?=$php_self;?>" method="post" style="display: none;">
											<input type="hidden" name="label_arr" value='<?=json_encode($label_arr);?>'>
											<input type="hidden" name="width_arr" value='<?=json_encode($width_arr);?>'>
											<input type="hidden" name="numberFormatArr" value='<?=json_encode($numberFormatArr);?>'>
											<input type="hidden" name="rowData" value='<?=json_encode($rowData);?>'>
											<input type="hidden" name="form_submit" value='Hi'>
											<!--<button type="submit" name="submit" class="btn btn-xs btn-info" value=""><i class="fa fa-eye" aria-hidden="true"></i> View</button>-->
											<input type="submit" name="submit_report" value="Submit">
										</form>
									</div>
								</div>
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
<script src="<?php echo ROOT_URL; ?>/js/jquery.PrintArea.js"></script>
<script type="text/javascript">
		$("#print, #print1").on("click", function() {
            var mode = 'iframe'; //popup
            var close = mode == "popup";
            var options = {
                mode: mode,
				popTitle: 'Performance_Report_<?=date('dmy_His');?>',
                popClose: close,
            };
            $(".print-hide").hide();
            $("div.printableArea").printArea(options);
            $(".print-hide").show();
        });
function confirmDelete(){
   return confirm("Are you sure you want to delete this?");
}
function export_excel(){
	console.log("Submitting Form");
   $("form[name='report']").submit();
}
function show_r_type(){
	$('#r_type').find("option:selected").each(function(){
		var optionValue = $(this).attr("value");
		if(optionValue == 'Quarterly'){
			$("#month").closest('.form-group').parent('div').removeAttr('required').hide();
			$("#quarter").closest('.form-group').parent('div').attr('required','true').show();
			$("#submit").closest('.form-group').parent('div').removeClass('col-md-4').addClass('col-md-12');
		} else if(optionValue == 'Annually'){
			$("#month").closest('.form-group').parent('div').removeAttr('required').hide();
			$("#quarter").closest('.form-group').parent('div').removeAttr('required').hide();
			$("#submit").closest('.form-group').parent('div').removeClass('col-md-12').addClass('col-md-4');
		} else {
			$("#month").closest('.form-group').parent('div').attr('required','true').show();
			$("#quarter").closest('.form-group').parent('div').removeAttr('required').hide();
			$("#submit").closest('.form-group').parent('div').removeClass('col-md-4').addClass('col-md-12');
		}
	});
}
$(document).on('change', '#r_type', function(){
	show_r_type();
});
show_r_type();

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