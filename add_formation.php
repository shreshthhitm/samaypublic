<?php
include 'init.php';
 
$requiredRole = ADMIN_ACCESS;
if (! isAuthorized($requiredRole)) {
  header('Location: dashboard.php');
}

include ROOT_DIR_COMMON.'header.php';

$msg = "";

$page_name = 'Formation';

if(isset($_GET['edit']) && is_numeric($_GET['edit']) && $_GET['edit'] != 0) {
	$edit = sanitize($_GET['edit'], 'int');
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$stmt_edit = $myPDO->query("SELECT * FROM formations WHERE id=".$edit);
	if($stmt_edit->rowCount() > 0){
		$row_edit = $stmt_edit->fetch(PDO::FETCH_ASSOC);
	}else{
		header("location: all_formations.php");
	}
}

if(isset($_POST['submit'])){
	$role = $_POST['role'];
	$formation = sanitize($_POST['formation'], 'string');
	if($_SESSION['sess_userrole'] == 'cc_user'){
		$role = 'manager';
		$parent_id = sanitize($_SESSION['sess_user_id'], 'int');
	}else{
		$role = sanitize($_POST['role'], 'string');
		$parent_id = ($role == 'manager' ? sanitize($_POST['cc_id'], 'int') : 0);
	}
	
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
	if($_POST['c_id'] != ''){
		$c_id = sanitize($_POST['c_id'], 'int');
		$q0 = "UPDATE formations SET role='".$role."', formation='".$formation."', parent_id='".$parent_id."' WHERE id=".$c_id;
		$myPDO->query($q0);
		$inserted = 1;
	}else{
		$q0 = "INSERT INTO formations (role, formation, parent_id) VALUES ('".$role."', '".$formation."', '".$parent_id."')";
		$myPDO->query($q0);
		$inserted = 1;
	}
		
	if ($inserted == 1) {
		echo "<script>\n"; 
		echo "    window.location.href = 'all_formations.php?role=".$role."&".((isset($edit)) ? "update" : "insert" )."=1';\n"; 
		//echo "    window.location.href = 'add_room.php?".((isset($edit)) ? "update" : "insert" )."=1';\n"; 
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
                                                        <label class="control-label col-md-4">Select Group<span class="red">*</span></label>
                                                        <div class="col-md-8">
															<?php
															if(isset($edit)){ ?>
															<input type="hidden" id="role" name="role" value="<?=$row_edit['role'];?>">
															<?php } ?>
															<select class="form-control" <?=(isset($edit) ? 'id="role1" name="role1" disabled readonly' : 'id="role" name="role"');?> required>
                                                                <?php
																if(isset($edit)){
																	echo user_role_populate($row_edit['role']);
																}else{
																	echo user_role_populate();
																}
																?>
                                                            </select> </span>
														</div>
                                                    </div>
                                                </div>
												<div class="col-md-6 show_cc">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Chief Commissioners<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <select id="cc_id" name="cc_id" class="form-control" required>
                                                                <?php
																if(isset($edit)){
																	echo formation_populate($row_edit['parent_id'], 'cc_user');
																}else{
																	echo formation_populate('','cc_user');
																	//echo '<option value="" disabled="disabled" selected="selected">Select Formation*</option>';
																}
																?>
                                                            </select>
															<span class="help-block"></span>
														</div>
                                                    </div>
                                                </div>
											</div>
											<div class="row">
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-4">Formation<span class="red">*</span></label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" placeholder="" id="formation" name="formation" value="<?=(isset($edit) ? $row_edit['formation'] : '');?>" required> <span class="help-block"></span>
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
$(document).on('change', '#role', function(){
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
		<?php if(isset($edit)){ ?>
		var optionValue = $('#role').val();
		<?php }else{ ?>
		var optionValue = $('#role').find("option:selected").attr("value");
		<?php } ?>
		if(optionValue == 'manager'){
			$("#cc_id").attr('required',true);
			$showdiv.slideDown();
		} else{
			$("#cc_id").attr('required',false);
			$showdiv.slideUp();
		}
	//});
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
		});*/
		$(".add_unit #p_unit").autocomplete({
			source: "select_unit.php",
			select: function(event, ui) {
				$(".add_unit #p_unit").val(ui.item.label);
				$("#id_product").val(ui.item.id);
			}
		});
		$(".form-horizontal").submit(function(){
			/*if($("#id_product").val() == ''){
				alert("Please select a 'unit' from the suggestions.");
				return false;
			}*/
			
		});
	});
</script>