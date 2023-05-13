<?php
include 'init.php';
 
$requiredRole = ADMIN_ACCESS;
if (! isAuthorized($requiredRole)) {
  header('Location: dashboard.php');
}

include ROOT_DIR_COMMON.'header.php';

$msg = "";

$page_name = 'Bench';
if(isset($_GET['del']) && is_numeric($_GET['del']) && $_GET['del'] != 0) {
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$del_query = "UPDATE benches SET is_active=0 WHERE id=".$_GET['del'];
	if($myPDO->query($del_query)) {
		echo "<script>\n"; 
		echo "    window.location.href = '".ROOT_URL."/all_benches.php?delete=1';\n"; 
		echo "</script>\n";
	} else {
		header("location: all_benches.php");
	}
}
if(isset($_GET['edit']) && is_numeric($_GET['edit']) && $_GET['edit'] != 0) {
	$edit = sanitize($_GET['edit'], 'int');
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt_edit = $myPDO->query("SELECT * FROM benches WHERE id=".$edit);
	if($stmt_edit->rowCount() > 0){
		$row_edit = $stmt_edit->fetch(PDO::FETCH_ASSOC);
	}else{
		header("location: all_benches.php");
	}
}

if(isset($_POST['submit'])){
	$court_type = sanitize($_POST['court_type'], 'string');
	$bench_name = sanitize($_POST['bench_name'], 'string');
	
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
	if($_POST['c_id'] != ''){
		$c_id = sanitize($_POST['c_id'], 'int');
		$q0 = "UPDATE benches SET court_type='".$court_type."', bench_name='".$bench_name."' WHERE id=".$c_id;
		$myPDO->query($q0);
		$inserted = 1;
	}else{
		$q0 = "INSERT INTO benches (court_type, bench_name) VALUES ('".$court_type."', '".$bench_name."')";
		$myPDO->query($q0);
		$inserted = 1;
	}
		
	if ($inserted == 1) {
		echo "<script>\n"; 
		//echo "    window.location.href = 'all_benches.php?".((isset($edit)) ? "update" : "insert" )."=1';\n"; 
		echo "    window.location.href = 'add_bench.php?".((isset($edit)) ? "update" : "insert" )."=1';\n"; 
		echo "</script>\n";
	}
	else{
		$msg="<div class='panel-heading' style='color: red;text-align: center;background: transparent;'>Some error occurred while adding the record.</div>";
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
                        <h4 class="page-title"><?php if(isset($edit)){ echo "Edit"; }else{ echo "Add New"; } echo " ".$page_name; ?></h4>
					</div>
                    <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <!-- /row -->
				<div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-info">
							<div class="panel-heading"><?php if(isset($edit)){ echo "Edit"; }else{ echo "Add New"; } echo " ".$page_name; ?></div>
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                    <form action="" class="form-horizontal" name="promo_form" enctype="multipart/form-data" method="post" data-toggle="validator">
                                        <div class="form-body">
                                            <!--<h3 class="box-title">Person Info</h3>
                                            <hr class="m-t-0 m-b-40">-->
											<div class="row">
												<?php /*?>
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">PG<span class="red">*</span></label>
                                                        <div class="col-md-9">
														<?php if($_SESSION['role'] == USER_ACCESS){ ?>
                                                            <input type="text" class="form-control" value="<?php echo get_value_from_id("centres","name","user_id",$_SESSION['sess_user_id']); ?>" placeholder="" readonly id="c_name" name="c_name" required>
															<input type="hidden" id="c_id" name="c_id" value="<?php echo get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']); ?>" >
														<?php }else if($_SESSION['role'] == ADMIN_ACCESS || $_SESSION['role'] == ADMIN_USER_ACCESS){ ?>
															<input type="hidden" id="room_id" name="room_id" value="<?php if(isset($edit)){ echo $row_edit['id']; } ?>" >
															<select id="c_id" name="c_id" class="form-control" required>
																<?php
																if(isset($edit)){
																	echo hotel_populate($row_edit['hotel_id']);
																}else{
																	echo hotel_populate();
																}
																?>
															</select>
														<?php } ?>
															 <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
                                                <!--/span-->
												
												<div class="col-md-12">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">Subject<span class="red">*</span></label>
                                                        <div class="col-md-6">
															<input type="text" class="form-control" value="<?php if(isset($edit)){ echo $row_edit['subject']; } ?>" placeholder="" id="subject" name="subject" required> <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
												<?php */ ?>
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Court<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <!--<input type="text" class="form-control" placeholder="dd/mm/yyyy"> -->
															<select id="court_type" name="court_type" class="form-control" required>
															<?php
															if(isset($edit)){
																echo court_populate($row_edit['court_type']);
															}else{
																echo court_populate();
															}
															?>
															</select>
															<span class="help-block"></span>
                                                        </div>
                                                    </div>
                                                </div>
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Bench Name<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" placeholder="" id="bench_name" name="bench_name" value="<?=(isset($edit) ? $row_edit['bench_name'] : '');?>" required> <span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
											</div>
                                            <!--/row-->
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-offset-3 col-md-9">
															<!--<input type="hidden" value="<?php //echo $post_status; ?>" id="post_status" name="post_status">-->
                                                            <button type="submit" id="submit" name="submit" class="btn btn-success"><i class="fa fa-check"></i> <?php if(isset($edit)){ echo "Update"; }else{ echo "Save"; } ?></button>
															<?php /*if(isset($edit)){ ?><a href="<?php echo ROOT_URL; ?>/add_promo.php?del=<?php echo $edit; ?>" onclick='return confirmDelete()'><button type='button' name='delete' class="btn btn-danger" value='Delete' >Delete</button></a><?php }*/ ?>
                                                            <button type="reset" class="btn btn-default">Reset</button>
                                                            <input type="hidden" name="c_id" value="<?php if(isset($edit)){ echo $edit; }?>"/>
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
$("form[name='promo_form']").on('submit', function (e) {
    //e.preventDefault();
	//$("#promo_value").change(function(){
		var selectedType = $("#promo_type").children("option:selected").val();
		var promo_value = $("#promo_value").val();
		if(selectedType == 'per' && (promo_value <= 0 || promo_value > 100)){
			console.log("alert");
			$("#promo_value").siblings('.help-block').html("Percentage value should be between 0 and 100!").css("color","#de2828");
			return false;
		}/*else{
			return true;
			//$(this).submit();
		}*/
	//});
});
var specialKeys = new Array();
specialKeys['push'](8, 46);
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