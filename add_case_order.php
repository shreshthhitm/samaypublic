<?php
include 'init.php';

$requiredRole = USER_ACCESS;
if (! isAuthorized($requiredRole)) {
  header('Location: dashboard.php');
}

include ROOT_DIR_COMMON.'header.php';

$msg = "";
$php_self = sanitize($_SERVER['PHP_SELF'], 'url');
$page_name = 'Case Order';
if(isset($_GET['del']) && is_numeric($_GET['del']) && $_GET['del'] != 0) {
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$del_query = "UPDATE orders SET is_active=0 WHERE id=".$_GET['del'];
	if($myPDO->query($del_query)) {
		echo "<script>\n"; 
		echo "    window.location.href = '".ROOT_URL."/all_case_orders.php?delete=1';\n"; 
		echo "</script>\n";
	} else {
		header("location: all_case_orders.php");
	}
}
if(isset($_GET['edit']) && is_numeric($_GET['edit']) && $_GET['edit'] != 0) {
	$edit = sanitize($_GET['edit'], 'int');
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt_edit = $myPDO->query("SELECT * FROM orders WHERE id=".$edit);
	if($stmt_edit->rowCount() > 0) {
		$row_edit = $stmt_edit->fetch(PDO::FETCH_ASSOC);
		$order_commence_create = DateTime::createFromFormat('Y-m-d H:i:s', $row_edit['order_commence_date']);
		$order_commence_date = $order_commence_create->format('d-m-Y');
		$order_receiving_create = DateTime::createFromFormat('Y-m-d H:i:s', $row_edit['order_receiving_date']);
		$order_receiving_date = $order_receiving_create->format('d-m-Y');
	} else {
		header("location: all_case_orders.php");
	}
}

if(isset($_POST['submit'])){
	$c_id = ($_SESSION['sess_userrole'] == 'manager' ? $_SESSION['sess_fid'] : $_POST['c_id']);
	$order_no = sanitize($_POST['order_no'], 'string');
	$party = sanitize($_POST['party'], 'string');
	//$order_title = $_POST['order_title'];
	$order_title = '';
	$cnr = sanitize($_POST['cnr'], 'int');
	$cnr = (isset($cnr) && !empty($cnr)) ? $cnr : 0;
	$coc_id = sanitize($_POST['coc_id'], 'int');
	//$possibility = $_POST['possibility'];
	$possibility = 1;
	$court = sanitize($_POST['court']);
	$bench_id = sanitize($_POST['bench_id'], 'int');
	$order_status = $_POST['order_status'];
	$order_commence_create = DateTime::createFromFormat('d-m-Y', sanitize($_POST['order_commence_date']));
	$order_commence_date = $order_commence_create->format('Y-m-d');
	$order_receiving_create = DateTime::createFromFormat('d-m-Y', sanitize($_POST['order_receiving_date']));
	$order_receiving_date = $order_receiving_create->format('Y-m-d');
	//$p_date = date('Y-m-d H:i:s');
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
	$saved = 0;
	if($_POST['order_id'] != ''){
		$order_id = sanitize($_POST['order_id'], 'int');
		//$q = "UPDATE orders SET order_no='".$order_no."', order_title='".$order_title."', party='".$party."', order_commence_date='".$order_commence_date."', court='".$court."', order_receiving_date='".$order_receiving_date."', order_status=".$order_status." WHERE id=".$order_id;
		$q = "UPDATE orders SET order_no='".$order_no."', party='".$party."', cnr='".$cnr."', coc_id=".$coc_id.", possibility=".$possibility.", order_commence_date='".$order_commence_date."', court='".$court."', bench_id='".$bench_id."', order_receiving_date='".$order_receiving_date."' WHERE id=".$order_id;
		//$q = "UPDATE orders SET order_no='".$order_no."', party='".$party."', court='".$court."' WHERE id=".$order_id;
		$myPDO->query($q);
		$saved = 1;
	}else{
		
		//$q = "INSERT INTO products (code, name, prod_desc, unit, qty, price, date) VALUES (?,?,?,?,?,?,?)";
		$q = "INSERT INTO orders (formation_id, order_no, order_title, party, cnr, coc_id, possibility, order_commence_date, court, bench_id, order_receiving_date, order_status) VALUES (".$c_id.",'".$order_no."', '".$order_title."', '".$party."', '".$cnr."', ".$coc_id.", ".$possibility.", '".$order_commence_date."', '".$court."', '".$bench_id."', '".$order_receiving_date."', ".$order_status.")";
		echo $q;
		$myPDO->query($q);
		$order_id = $myPDO->lastInsertId();
		$order_status_id = crud_order_status('insert',$order_id, $order_status);
		//crud_order_activity($order_status_id, $_SESSION['sess_fid'], $comm_order_status[$order_status], $comm_order_status[$order_status]);
		//$query = $myPDO->prepare($q);
		//echo $q;
		//$query->execute(array($p_code, $p_name, $p_desc, $p_unit, $p_qty, $p_price, $p_date));
		//$myPDO->query($q);
		$saved = 1;
	}
		
	if ($saved == 1) {
		echo "<script>\n"; 
		echo "    window.location.href = '".$php_self."?".((isset($edit)) ? "update" : "insert" )."=1';\n"; 
		echo "</script>\n";

	}
	else{
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
								/*$stmt = $myPDO->query("SELECT code FROM products ORDER BY id DESC LIMIT 1");
								$row = $stmt->fetch(PDO::FETCH_NUM);
								$str = $row[0];
								//echo $str;
								$new_p_code = code($str,4,4);*/
								//echo $new_p_code;
							?>
							<div class="panel-heading"><?php if(isset($edit)){ echo "Edit"; }else{ echo "Add New"; } ?> <?=$page_name;?></div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                    <form action="" class="form-horizontal" enctype="multipart/form-data" method="post" data-toggle="validator">
                                        <div class="form-body">
                                            <!--<h3 class="box-title">Person Info</h3>
                                            <hr class="m-t-0 m-b-40">-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
													<?php if($_SESSION['sess_userrole'] == 'manager'){ /*?>
														<input type="text" class="form-control" value="<?php echo get_value_from_id("commissioners","name","user_id",$_SESSION['sess_user_id']); ?>" placeholder="" readonly id="c_name" name="c_name" required>
														<?php */ ?>
														<input type="hidden" id="c_id" name="c_id" value="<?php echo $_SESSION['sess_fid']; ?>" >
													<?php }else if($_SESSION['role'] == ADMIN_ACCESS || $_SESSION['sess_userrole'] == 'cc_user'){ ?>
														<label class="control-label col-md-4">Commissioner<span class="red">*</span></label>
														<div class="col-md-8">
															<select id="c_id" name="c_id" class="form-control" <?=((isset($edit)) ? 'readonly disabled' : '');?> required>
																<?php
																if(isset($edit)){
																	echo formation_populate($row_edit['user_id'],'manager');
																}else{
																	echo formation_populate('','manager');
																}
																?>
															</select>
															<span class="help-block"></span>
														</div>
													<?php } ?>
                                                    </div>
                                                </div>
                                                <!--/span-->
											</div>
                                            <!--/row-->
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group "><!-- use class='has-error' for error-->
                                                        <label class="control-label col-md-4">Appeal/Petition No.<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" value="<?php if(isset($edit)){ echo $row_edit['order_no']; } ?>" placeholder="" id="order_no" name="order_no" required> <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
                                                <!--/span-->
												<div class="col-md-6">
                                                    <div class="form-group "><!-- use class='has-error' for error-->
                                                        <label class="control-label col-md-4">Classification of Case<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <select id="coc_id" name="coc_id" class="form-control" required>
															<?php
															if(isset($edit)){
																echo coc_populate($row_edit['coc_id']);
															}else{
																echo coc_populate();
															}
															?>
															</select>
															<span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
                                                <!--/span-->
												<div class="col-md-6">
                                                    <div class="form-group "><!-- use class='has-error' for error-->
                                                        <label class="control-label col-md-4">Party<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" value="<?php if(isset($edit)){ echo $row_edit['party']; } ?>" placeholder="" id="party" name="party" maxlength="20" required> <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
                                            
                                                <?php /* ?><div class="col-md-6">
                                                    <div class="form-group "><!-- use class='has-error' for error-->
                                                        <label class="control-label col-md-4">Order Title<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" value="<?php if(isset($edit)){ echo $row_edit['order_title']; } ?>" placeholder="" id="order_title" name="order_title" required> <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
                                                <!--/span--><?php */?>
                                                <div class="col-md-6">
                                                    <div class="form-group "><!-- use class='has-error' for error-->
                                                        <label class="control-label col-md-4">Date of Order<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control mydatepicker order_commence_date" value="<?php if(isset($edit)){ echo $order_commence_date; } ?>" placeholder="" id="order_commence_date" name="order_commence_date" autocomplete="off" required <?php /*=(isset($edit) ? 'readonly disabled' : '');*/?>> <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
                                            
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Court<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <!--<input type="text" class="form-control" placeholder="dd/mm/yyyy"> -->
															<select id="court" name="court" class="form-control" required>
															<?php
															if(isset($edit)){
																echo court_populate($row_edit['court']);
															}else{
																echo court_populate();
															}
															?>
															</select>
															<span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Bench<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <!--<input type="text" class="form-control" placeholder="dd/mm/yyyy"> -->
															<select id="bench_id" name="bench_id" class="form-control" required>
															<?php
															if(isset($edit)){
																echo bench_populate($row_edit['court'], $row_edit['bench_id']);
															}else{
																echo '<option value="" selected="selected">Select Bench</option>';
															}
															?>
															</select>
															<span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
													<div class="form-group "><!-- use class='has-error' for error-->
														<label class="control-label col-md-4">Date of <span class="second_date_label"></span> of Order<span class="red">*</span></label>
														<div class="col-md-8">
															<input type="text" class="form-control mydatepicker order_receiving_date" value="<?php if(isset($edit)){ echo $order_receiving_date; } ?>" placeholder="" id="order_receiving_date" name="order_receiving_date" autocomplete="off" required <?php /*=(isset($edit) ? 'readonly disabled' : '');*/?>> <span class="help-block"></span>
														</div>
													</div>
                                                </div>
                                                <!--/span-->
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Order Status<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <!--<input type="text" class="form-control" placeholder="dd/mm/yyyy"> -->
															<select id="order_status" name="order_status" class="form-control" required <?=(isset($edit) ? 'readonly disabled' : '');?>>
															<?php
															if(isset($edit)){
																echo comm_order_status_populate($row_edit['order_status']);
															}else{
																echo comm_order_status_populate();
															}
															?>
															</select>
															<span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--/span-->
												<?php /*?><div class="col-md-6">
                                                    <div class="form-group "><!-- use class='has-error' for error-->
                                                        <label class="control-label col-md-4">Possibility of Approaching Supreme Court<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <select id="possibility" name="possibility" class="form-control" required>
															<?php
															if(isset($edit)){
																echo pasc_populate($row_edit['possibility']);
															}else{
																echo pasc_populate();
															}
															?>
															</select>
															<span class="help-block"></span>
														</div>
                                                    </div>
                                                </div><?php */?>
												<div class="col-md-6 cnr_col hide">
                                                    <div class="form-group "><!-- use class='has-error' for error-->
                                                        <label class="control-label col-md-4">CNR No.<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" value="<?php if(isset($edit)){ echo $row_edit['cnr']; } ?>" placeholder="Enter 10 digit numeric ID" id="cnr" name="cnr" minlength="10" maxlength="10" onkeypress="return IsNumberOnly(event);" <?=((isset($edit) && $row_edit['court'] == 'High Court') ? 'required' : '');?>> <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
                                                <!--/span-->
                                            </div>
                                            <!--/row-->
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-offset-3 col-md-9">
															<!--<input type="hidden" value="<?php //echo $post_status; ?>" id="post_status" name="post_status">-->
                                                            <button type="submit" id="submit" name="submit" class="btn btn-success"><i class="fa fa-check"></i> <?php if(isset($edit)){ echo "Update"; }else{ echo "Save"; } ?></button>
															<?php if(isset($edit) && $userrole == 'admin'){ ?><a href="<?php echo ROOT_URL; ?>/add_case_order.php?del=<?php echo $edit; ?>" onclick='return confirmDelete()'><button type='button' name='delete' class="btn btn-danger" value='Delete' >Delete</button></a><?php } ?>
                                                            <button type="reset" class="btn btn-default">Cancel</button>
															<input type="hidden" name="order_id" value="<?php if(isset($edit)){ echo $row_edit['id']; }?>"/>
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

?>

<!--<link rel="stylesheet" href="<?php echo ROOT_URL; ?>/css/jquery-ui.css">
<script src="<?php echo ROOT_URL; ?>/js/jquery-ui.js" ></script>-->
<script type="text/javascript">
function IsNumberOnly(key1) {
	var key_code_int = key1['which'] ? key1['which'] : key1['keyCode'];
	//console['log'](key_code);
	var rt_key_int = (key_code_int >= 48 && key_code_int <= 57);
	return rt_key_int;
}
$("#party").on('keypress', function(){
	if($(this).val().length == 19){
		$(this).siblings('.help-block').addClass('text-danger font-bold').html('Character Limit: 20');
	}
});
show_second_date();
$(document).on('change', '#court', function(){
	show_second_date();
});
//CNR to be mandatory in case of high courts only
function show_second_date(){
	//$(".cnr_col").addClass('hide');
	$('#court').find("option:selected").each(function(){
		var optionValue = $(this).attr("value");
		if(optionValue == 'tribunal'){
			$(".second_date_label").html('Receipt');
			$(".cnr_col input").removeAttr('required');
			$(".cnr_col").addClass('hide');
		} else{
			$(".second_date_label").html('Uploading');
			if(optionValue == 'high'){
				$(".cnr_col input").attr('required','true');
				$(".cnr_col").removeClass('hide');
			}
		}
		if(optionValue == ''){
			$("#bench_id").html('<option value="" selected="selected">Select Bench</option>');
		}else{
			//console.log(optionValue);
			$.ajax({
				type: "POST",
				url: "get_benches.php",
				data:'court='+optionValue+'&bench_id=<?=(isset($edit) ? $row_edit['bench_id'] : '');?>',
				success: function(data){
							$("#bench_id").html(data);
						}
			});
		}
	});
}
function confirmDelete(){
   return confirm("Are you sure you want to delete this?");
}
	<?php if((isset($_GET['insert']) && $_GET['insert'] == 1 && isset($_SERVER['HTTP_REFERER'])) || (isset($_GET['update']) && $_GET['update'] == 1 && isset($_SERVER['HTTP_REFERER']))){ ?>
	$.toast({
        text: '<?php if(isset($_GET['insert'])){ echo $page_name.' added Successfully'; }else if(isset($_GET['update'])){ echo $page_name.' Updated Successfully'; } ?>',
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
	$('.mydatepicker.order_commence_date').datepicker({
		format: "dd-mm-yyyy",
		autoclose: true,
		todayHighlight: true,
		endDate: new Date(),
		/*onSelect: function (dateStr) {
			//var start = $('#order_commence_date').val(this.value);
			//$(".mydatepicker.order_receiving_date").datepicker().datepicker("startDate", new Date(start));
			//console.log("Mydate: "+$('#order_commence_date').val());
			$("#order_receiving_date").datepicker('option', 'minDate', dateStr);
		}*/
	}).on('changeDate', function (selected) {
		var minDate = new Date(selected.date.valueOf());
		$('#order_receiving_date').datepicker('setStartDate', minDate);
		//$('#order_receiving_date').datepicker('setDate', minDate); // <--THIS IS THE LINE ADDED
	});
	
	/*$(".mydatepicker.order_commence_date").datepicker().datepicker("setDate", new Date("<?=((isset($edit)) ? date('m/d/Y',strtotime($row_edit['order_commence_date'])) : date('m/d/Y'));?>"));
	//$(".mydatepicker.order_receiving_date").datepicker().datepicker("setDate", new Date("<?=((isset($edit)) ? date('m/d/Y',strtotime($row_edit['order_receiving_date'])) : date('m/d/Y'));?>"));
	$('.order_commence_date, .order_receiving_date').on('load change keyup blur', function () {
		console.log("Mydate: "+$('#order_commence_date').val());
		//var currDate = new Date();
		//var todayDate = new Date().getDate();
		//console.log(currDate);
		//var endD= new Date(new Date().setDate(todayDate - 15));
		var commence_date = $("#order_commence_date").val();
		console.log(commence_date);
		//var endD = new Date(new Date().setDate(commence_date));
		//var endD = new Date(2018, 11, 24);
		var from = commence_date.split("-")
		var endD = new Date(from[2], from[1] - 1, from[0]);
		console.log(endD);
		$('.mydatepicker.order_receiving_date').datepicker({
			format: "dd-mm-yyyy",
			autoclose: true,
			todayHighlight: true,
			startDate : endD,
			//endDate : currDate
		});
	});*/
	$('.mydatepicker.order_receiving_date').datepicker({
		format: "dd-mm-yyyy",
		autoclose: true,
		//todayHighlight: true,
		<?php if(isset($edit)){ ?>
		startDate: '<?=$order_commence_date;?>',
		<?php } ?>
		endDate: new Date(),
	}).on('changeDate', function (selected) {
		var endDate = new Date(selected.date.valueOf());
		$('#order_commence_date').datepicker('setEndDate', endDate);
		//$('#order_receiving_date').datepicker('setDate', minDate); // <--THIS IS THE LINE ADDED
	});
</script>