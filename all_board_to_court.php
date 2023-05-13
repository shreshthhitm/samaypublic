<?php
include 'init.php';
 
$requiredRole = ADMIN_ACCESS;
if (! isAuthorized($requiredRole)) {
  header('Location: dashboard.php');
}

include ROOT_DIR_COMMON.'header.php';

$s = 0;
$page_name = 'Board to Court Mapping';
if(isset($_POST['submit'])){
	$centre_id = $_POST['c_id'];
	$s = 1;
}else{
	/*if($_SESSION['role'] == USER_ACCESS){
		$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}*/
	if($_SESSION['role'] == ADMIN_USER_ACCESS || $_SESSION['role'] == USER_ACCESS){
	    $centre_id = get_hotel_ids_manager($_SESSION['sess_user_id']);
		//$centre_id = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
	}
}
if(isset($_POST['btn_active'])){
	$c_id = $_POST['c_id'];
	$active = (($_POST['active'] == 1) ? 0 : 1);
	$myPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // <== add this line
	$q0 = "UPDATE benches SET is_active=".$active." WHERE id=".$c_id;
	if ($myPDO->query($q0)) {
		//echo "<script type= 'text/javascript'>alert('New Record Inserted Successfully');</script>";
		//$msg="<div class='panel-heading' style='color: green;text-align: center;background: transparent;'>Product Inserted Successfully</div>";
		//$post_status = 0;
		echo "<script>\n"; 
		echo "    window.location.href = 'all_benches.php?update=1';\n"; 
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
                        <h4 class="page-title">View <?=$page_name;?></h4>
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
				<?php /*if($_SESSION['role'] == ADMIN_ACCESS || $_SESSION['role'] == ADMIN_USER_ACCESS){ ?>
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
												<div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label col-md-3">PG<span class="red">*</span></label>
                                                        <div class="col-md-9">
															<select id="c_id" name="c_id" class="form-control" required>
																<?php
																	if($s == 1){
																        echo hotel_populate($centre_id);
																    }else{
																        echo hotel_populate();
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
				<?php }*/ ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="white-box">
                            <h3 class="box-title">View All <?=$page_name;?><?php if($s == 1){ ?> <span style="font-weight: 400; background: darkgrey; padding: 10px; text-transform: none;">(Displaying results for: <b>"<?php //echo get_value_from_id("hotels","hotel_name","id",$centre_id); ?>"</b>)</span><?php } ?></h3>
                            <!--<p class="text-muted">Add class <code>.table</code></p>-->
                            <div class="table-responsive">
                                <table class="table color-table info-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Board Name</th>
                                            <th>Court</th>
											<th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
									<?php
									$q = 'SELECT * FROM boards';
									$i = 1;
									$stmt_bench = $myPDO->query($q);
									if($stmt_bench->rowCount() > 0) {
    									foreach($stmt_bench as $row){
											$is_active = "<form method='post'><input type='hidden' id='c_id' name='c_id' value='{$row['id']}' ><input type='hidden' id='active' name='active' value='{$row['is_active']}' ><button type='submit' name='btn_active' class='label label-".(($row['is_active'] == 1)? "success'>Active" : "danger'>Inactive")."</button></form>";
                                            echo "<tr>
    												<td>{$i}.</td>
													<td>".get_value_from_id("formations","formation","id",$row['formation_id'])."</td>
													<td>".$courts[$row['court_type']]."</td>
													<td><a href=add_board_to_court.php?edit={$row['id']}  title='Update Bench'><i class='ti-pencil-alt'></i></a></td>
												</tr>";
    										$i++;
    									}
									}else{
										echo '<tr><td colspan="4"><h2 class="text-center">No Records Found.</h2></td></tr>';
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
var members = [
    {memberId : 1, parentId:null, amount:200, otherInfo:"blah"},
    {memberId : 2, parentId:1, amount:300, otherInfo:"blah1"},
    {memberId : 3, parentId:1, amount:400, otherInfo:"blah2"},
    {memberId : 4, parentId:3, amount:500, otherInfo:"blah3"},
    {memberId : 6, parentId:1, amount:600, otherInfo:"blah4"},
    {memberId : 9, parentId:4, amount:700, otherInfo:"blah5"},
    {memberId : 12, parentId:2, amount:800, otherInfo:"blah6"},
    {memberId : 5, parentId:2, amount:900, otherInfo:"blah7"},
    {memberId : 13, parentId:2, amount:0, otherInfo:"blah8"},
    {memberId : 14, parentId:2, amount:800, otherInfo:"blah9"},
    {memberId : 55, parentId:2, amount:250, otherInfo:"blah10"},
    {memberId : 56, parentId:3, amount:10, otherInfo:"blah11"},
    {memberId : 57, parentId:3, amount:990, otherInfo:"blah12"},
    {memberId : 58, parentId:3, amount:400, otherInfo:"blah13"},
    {memberId : 59, parentId:6, amount:123, otherInfo:"blah14"},
    {memberId : 54, parentId:6, amount:321, otherInfo:"blah15"},
    {memberId : 53, parentId:56, amount:10000, otherInfo:"blah7"},
    {memberId : 52, parentId:2, amount:47, otherInfo:"blah17"},
    {memberId : 51, parentId:6, amount:534, otherInfo:"blah18"},
    {memberId : 50, parentId:9, amount:55943, otherInfo:"blah19"},
    {memberId : 22, parentId:9, amount:2, otherInfo:"blah27"},
    {memberId : 33, parentId:12, amount:-10, otherInfo:"blah677"}

];
var testImgSrc = "http://0.gravatar.com/avatar/06005cd2700c136d09e71838645d36ff?s=69&d=wavatar";
(function heya( parentId ){
    // This is slow and iterates over each object everytime.
    // Removing each item from the array before re-iterating 
    // may be faster for large datasets.
    for(var i = 0; i < members.length; i++){
        var member = members[i];
        if(member.parentId === parentId){
            var parent = parentId ? $("#containerFor" + parentId) : $("#mainContainer"),
                memberId = member.memberId,
                    metaInfo = "<img src='"+testImgSrc+"'/>" + member.otherInfo + " ($" + member.amount + ")";
            parent.append("<div class='container' id='containerFor" + memberId + "'><div class='member'><div class='memberTxt'>" + memberId + "<div class='metaInfo'>" + metaInfo + "</div></div></div></div>");
            heya(memberId);
        } 
    }
 }( null ));

// makes it pretty:
// recursivley resizes all children to fit within the parent.
var pretty = function(){
    var self = $(this),
        children = self.children(".container"),
        // subtract 4% for margin/padding/borders.
        width = (100/children.length) - 1;
    children
        .css("width", width + "%")
        .each(pretty);

};
$("#mainContainer").each(pretty);
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
				function confirmUpdate(upd){
					return confirm("Are you sure you want to update inquiry to "+upd+"?");
				}
				$(".enq_status").on('change', function(){
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