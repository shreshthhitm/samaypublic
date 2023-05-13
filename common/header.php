<?php
include_once ROOT_DIR_COMMON.'functions.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo ROOT_URL; ?>/images/favicon.png">
    <title>SAMAY Reporting Module</title>
    <!-- Bootstrap Core CSS -->
    <link href="<?php echo ROOT_URL; ?>/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<!-- Typehead CSS -->
    <link href="<?php echo ROOT_URL; ?>/bower_components/typeahead.js-master/dist/typehead-min.css" rel="stylesheet">
    <!-- Menu CSS -->
    <link href="<?php echo ROOT_URL; ?>/bower_components/sidebar-nav/dist/sidebar-nav.min.css" rel="stylesheet">
	
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<!-- Date picker plugins css -->
    <link href="<?php echo ROOT_URL; ?>/bower_components/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
	<link href="<?php echo ROOT_URL; ?>/bower_components/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet">
    <!-- toast CSS -->
    <link href="<?php echo ROOT_URL; ?>/bower_components/toast-master/css/jquery.toast.css" rel="stylesheet">
    <!-- morris CSS -->
    <link href="<?php echo ROOT_URL; ?>/bower_components/morrisjs/morris.css" rel="stylesheet">
    <!-- chartist CSS -->
    <link href="<?php echo ROOT_URL; ?>/bower_components/chartist-js/dist/chartist.min.css" rel="stylesheet">
    <link href="<?php echo ROOT_URL; ?>/bower_components/chartist-plugin-tooltip-master/dist/chartist-plugin-tooltip.css" rel="stylesheet">
    <!-- animation CSS -->
    <link href="<?php echo ROOT_URL; ?>/css/animate.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo ROOT_URL; ?>/css/style.css" rel="stylesheet">
    <!-- color CSS -->
    <link href="<?php echo ROOT_URL; ?>/css/colors/default.css" id="theme" rel="stylesheet">
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="fix-header">
    <!-- ============================================================== -->
    <!-- Preloader -->
    <!-- ============================================================== -->
    <div class="preloader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" />
        </svg>
    </div>
    <!-- ============================================================== -->
    <!-- Wrapper -->
    <!-- ============================================================== -->
    <div id="wrapper">
        <!-- ============================================================== -->
        <!-- Topbar header - style you can find in pages.scss -->
        <!-- ============================================================== -->
        <nav class="navbar navbar-default navbar-static-top m-b-0">
            <div class="navbar-header">
                <div class="top-left-part">
                    <!-- Logo -->
                    <a class="logo" href="<?php echo ROOT_URL; ?>" style="color: #000; font-size: 18px;">
                        <!-- Logo icon image, you can use font-icon also -->
                        <!--This is dark logo icon--><img src="<?php echo ROOT_URL; ?>/images/logo.png" alt="CBIC" class="" /><!--This is light logo icon-->
						<!--<img src="<?php echo ROOT_URL; ?>/images/logo.png" style="text-align: center;max-height: 70px;" alt="home" class="light-logo" />
                     <b></b>-->
                        <!-- Logo text image you can use text also --><span class="hidden-xs">
                        <!--This is dark logo text--><!--<img src="<?php echo ROOT_URL; ?>/images/admin-text.png" alt="home" class="dark-logo" />--><!--Mantrix<br/>Solutions<br/>Pvt. Ltd.--><!--This is light logo text--><!--<img src="<?php echo ROOT_URL; ?>/images/admin-text-dark.png" alt="home" class="light-logo" />-->
                     </span> </a>
                </div>
                <!-- /Logo -->
				<ul class="nav navbar-top-links navbar-left">
                    <li><a href="javascript:void(0)" class="open-close waves-effect waves-light"><i class="ti-menu"></i></a></li>
				</ul>
				<?php
				$query = $myPDO->query("Select * from users where id=".$user_id);
				$row = $query->fetch();
				?>
                <ul class="nav navbar-top-links navbar-right pull-right">
                    <!--<li>
                        <form role="search" class="app-search hidden-sm hidden-xs m-r-10">
                            <input type="text" placeholder="Search<?php echo ROOT_URL; ?>." class="form-control"> <a href=""><i class="fa fa-search"></i></a> </form>
                    </li>-->
                    <li>
                        <a class="profile-pic" href="#"> <img src="<?php echo ROOT_URL; ?>/images/users/varun.jpg" alt="user-img" width="36" class="img-circle"><b class="hidden-xs"> Welcome, <?=($_SESSION['sess_userrole'] == 'admin' ? 'Administrator' : get_value_from_id("formations","formation","id",$formation_id));?> !</b></a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-header -->
            <!-- /.navbar-top-links -->
            <!-- /.navbar-static-side -->
        </nav>
        <!-- End Top Navigation -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
		
        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav slimscrollsidebar">
                <div class="sidebar-head">
                    <h3><span class="fa-fw open-close"><i class="ti-close ti-menu"></i></span> <span class="hide-menu">Navigation</span></h3>
                </div>
				<div  style="padding: 61px 0 0;"></div>
				<?php if($basename != 'choose_charge.php'){ ?>
                <ul class="nav" id="sidebarnav">
                    <li>
                        <a href="<?php echo ROOT_URL; ?>/dashboard.php" class="waves-effect"><i class="fa fa-tachometer fa-fw" aria-hidden="true"></i>Dashboard</a>
                    </li>
					<li>
                        <a href="<?php echo ROOT_URL; ?>/my_profile.php" class="waves-effect"><i class="fa fa-user fa-fw" aria-hidden="true"></i>My Profile</a>
                    </li>
					<!--<li>
                        <a href="<?php echo ROOT_URL; ?>/profile.php" class="waves-effect"><i class="fa fa-clock-o fa-fw" aria-hidden="true"></i>My Profile</a>
                    </li>
					<li>
                        <a href="<?php echo ROOT_URL; ?>/all_messages.php" class="waves-effect"><i class="fa fa-building fa-fw" aria-hidden="true"></i>All Messages</a>
                    </li>-->
					<?php if($_SESSION['role'] == ADMIN_ACCESS){ ?>
					<li>
						<a href="javascript:void(0)" aria-expanded="false" class="has-arrow waves-effect"><i class="fa fa-legal fa-fw" aria-hidden="true"></i>Benches</a>
						<ul aria-expanded="false" class="collapse first-level">
							<li>
								<a href="<?php echo ROOT_URL; ?>/add_bench.php" class="waves-effect"><i class="fa fa-file fa-fw" aria-hidden="true"></i>Add New</a>
							</li>
							<li>
								<a href="<?php echo ROOT_URL; ?>/all_benches.php" class="waves-effect"><i class="fa fa-files-o fa-fw" aria-hidden="true"></i>View All</a>
							</li>
						</ul>
                    </li>
					<li>
						<a href="javascript:void(0)" aria-expanded="false" class="has-arrow waves-effect"><i class="fa fa-hand-o-right fa-fw" aria-hidden="true"></i>Boards</a>
						<ul aria-expanded="false" class="collapse first-level">
							<li>
								<a href="<?php echo ROOT_URL; ?>/add_board_to_court.php" class="waves-effect"><i class="fa fa-file fa-fw" aria-hidden="true"></i>Add New</a>
							</li>
							<li>
								<a href="<?php echo ROOT_URL; ?>/all_board_to_court.php" class="waves-effect"><i class="fa fa-files-o fa-fw" aria-hidden="true"></i>View All</a>
							</li>
						</ul>
                    </li>
					<!--<li>
                        <a href="<?php echo ROOT_URL; ?>/add_message.php" class="waves-effect"><i class="fa fa-building fa-fw" aria-hidden="true"></i>Add New Message</a>
                    </li>-->
					<?php } ?>
					<?php /*if($_SESSION['role'] == ADMIN_ACCESS){ ?>
					<li>
                        <a href="<?php echo ROOT_URL; ?>/all_events.php" class="waves-effect"><i class="fa fa-building fa-fw" aria-hidden="true"></i>Events</a>
                    </li>
					<li>
                        <a href="<?php echo ROOT_URL; ?>/add_event.php" class="waves-effect"><i class="fa fa-server fa-fw" aria-hidden="true"></i>Add New Event</a>
                    </li>
					<?php }*/ ?>
					<?php /*if($_SESSION['role'] != USER_ACCESS){ ?>
					<!--<li>
						<?php
							$centre_id_for_bank = get_value_from_id("centres","id","user_id",$_SESSION['sess_user_id']);
							try{
							$stmt_edit_for_bank1 = $myPDO->query("SELECT * FROM bankinfo WHERE centre_id=$centre_id_for_bank");
							}catch(PDOException $e){}
						?>
                        <a href="<?php echo ROOT_URL; ?>/add_bank_details.php" class="waves-effect"><i class="fa fa-building fa-fw" aria-hidden="true"></i><?php if($_SESSION['sess_userrole'] == 'manager' && $stmt_edit_for_bank1->rowCount() > 0){ ?>View<?php }else{ ?>Add<?php } ?> Bank Details</a>
                    </li>-->
					<?php }*/ ?>
					<?php if($_SESSION['sess_userrole'] == 'admin' || $_SESSION['sess_userrole'] == 'cc_user'){ ?>
					<li>
						<a href="javascript:void(0)" aria-expanded="false" class="has-arrow waves-effect"><i class="fa fa-bank fa-fw" aria-hidden="true"></i>Formations</a>
						<ul aria-expanded="false" class="collapse first-level">
							<?php if($_SESSION['sess_userrole'] == 'admin'){ ?>
							<li>
								<a href="<?php echo ROOT_URL; ?>/add_formation.php" class="waves-effect"><i class="fa fa-pencil fa-fw" aria-hidden="true"></i>Add Formation</a>
							</li>
							<?php } ?>
							<li>
								<a href="<?php echo ROOT_URL; ?>/all_formations.php?role=manager" class="waves-effect"><i class="fa fa-other fa-fw" aria-hidden="true">CO</i>All <?=$user_roles['manager'];?>s</a>
							</li>
							<?php if($_SESSION['sess_userrole'] == 'admin'){ ?>
							<li>
								<a href="<?php echo ROOT_URL; ?>/all_formations.php?role=cc_user" class="waves-effect"><i class="fa fa-other fa-fw" aria-hidden="true">CC</i>All <?=$user_roles['cc_user'];?>s</a>
							</li>
							<li>
								<a href="<?php echo ROOT_URL; ?>/all_formations.php?role=board" class="waves-effect"><i class="fa fa-other fa-fw" aria-hidden="true">BO</i>All <?=$user_roles['board'];?>s</a>
							</li>
							<?php } ?>
						</ul>
                    </li>
					<li>
						<a href="javascript:void(0)" aria-expanded="false" class="has-arrow waves-effect"><i class="fa fa-users fa-fw" aria-hidden="true"></i>Users</a>
						<ul aria-expanded="false" class="collapse first-level">
							<li>
								<a href="<?php echo ROOT_URL; ?>/add_user.php" class="waves-effect"><i class="fa fa-pencil fa-fw" aria-hidden="true"></i>Add User</a>
							</li>
							<li>
								<a href="<?php echo ROOT_URL; ?>/all_users.php?role=manager" class="waves-effect"><i class="fa fa-other fa-fw" aria-hidden="true">CO</i>All <?=$user_roles['manager'];?>s</a>
							</li>
							<?php if($_SESSION['sess_userrole'] == 'admin'){ ?>
							<li>
								<a href="<?php echo ROOT_URL; ?>/all_users.php?role=cc_user" class="waves-effect"><i class="fa fa-other fa-fw" aria-hidden="true">CC</i>All <?=$user_roles['cc_user'];?>s</a>
							</li>
							<li>
								<a href="<?php echo ROOT_URL; ?>/all_users.php?role=board" class="waves-effect"><i class="fa fa-other fa-fw" aria-hidden="true">BO</i>All <?=$user_roles['board'];?>s</a>
							</li>
							<?php } ?>
						</ul>
                    </li>
					<?php } ?>
					
					<!--<li>
                        <a href="<?php echo ROOT_URL; ?>/add_chat.php" class="waves-effect"><i class="fa fa-server fa-fw" aria-hidden="true"></i>Add New Chat</a>
                    </li>
					<li>
                        <a href="<?php echo ROOT_URL; ?>/all_chats.php" class="waves-effect"><i class="fa fa-building fa-fw" aria-hidden="true"></i>All Chats</a>
                    </li>-->
					
					<?php if($_SESSION['sess_userrole'] == 'manager'){ ?>
					<li>
						<a href="javascript:void(0)" aria-expanded="false" class="has-arrow waves-effect"><i class="fa fa-file-text fa-fw" aria-hidden="true"></i>Orders</a>
						<ul aria-expanded="false" class="collapse first-level">
							<li>
								<a href="<?php echo ROOT_URL; ?>/add_case_order.php" class="waves-effect"><i class="fa fa-file fa-fw" aria-hidden="true"></i>Add New Order</a>
							</li>
					<?php } ?>
							<li>
								<a href="<?php echo ROOT_URL; ?>/all_case_orders.php" class="waves-effect"><i class="fa fa-files-o fa-fw" aria-hidden="true"></i>All Orders</a>
							</li>
					<?php if($_SESSION['sess_userrole'] == 'manager'){ ?>
						</ul>
                    </li>
					<?php } ?>
					<li>
                        <a href="<?php echo ROOT_URL; ?>/transfer_charge.php" class="waves-effect"><i class="fa fa-exchange fa-fw" aria-hidden="true"></i>Transfer <?=(($_SESSION['sess_userrole'] == 'admin' || $_SESSION['sess_userrole'] == 'cc_user') ? '' : 'my ');?>Charge</a>
                    </li>
					<?php if($_SESSION['sess_userrole'] != 'manager'){ ?>
					<li>
                        <a href="<?php echo ROOT_URL; ?>/all_performances.php" class="waves-effect"><i class="fa fa-file-text fa-fw" aria-hidden="true"></i>Performance Report</a>
                    </li>
					<?php } ?>
					<!--<li>
                        <a href="<?php echo ROOT_URL; ?>/export.php" class="waves-effect"><i class="fa fa-download fa-fw" aria-hidden="true"></i>Export Data</a>
                    </li>-->
					<?php if($_SESSION['role'] != USER_ACCESS){ ?>
                    <!--<li>
                        <a href="<?php echo ROOT_URL; ?>/all_products.php" class="waves-effect"><i class="fa fa-user fa-fw" aria-hidden="true"></i>Products</a>
                    </li>
                    <li>
                        <a href="<?php echo ROOT_URL; ?>/add_product.php" class="waves-effect"><i class="mdi mdi-cart-outline fa-fw" aria-hidden="true"></i>Add New Product</a>
                    </li>
					<li>
                        <a href="<?php echo ROOT_URL; ?>/all_invoices.php" class="waves-effect"><i class="fa fa-edit fa-fw" aria-hidden="true"></i>Customer Bills</a>
                    </li>
                    <li>
                        <a href="<?php echo ROOT_URL; ?>/invoice.php" class="waves-effect"><i class="fa fa-table fa-fw" aria-hidden="true"></i>Create Bill</a>
                    </li>
                    <li>
                        <a href="<?php echo ROOT_URL; ?>/print_datewise.php" class="waves-effect"><i class="fa fa-print fa-fw" aria-hidden="true"></i>Print Bills</a>
                    </li>
					<?php } ?>
                    <!--<li>
                        <a href="<?php echo ROOT_URL; ?>/map-google.html" class="waves-effect"><i class="fa fa-globe fa-fw" aria-hidden="true"></i>Google Map</a>
                    </li>
                    <li>
                        <a href="<?php echo ROOT_URL; ?>/blank.html" class="waves-effect"><i class="fa fa-columns fa-fw" aria-hidden="true"></i>Blank Page</a>
                    </li>
                    <li>
                        <a href="<?php echo ROOT_URL; ?>/404.html" class="waves-effect"><i class="fa fa-info-circle fa-fw" aria-hidden="true"></i>Error 404</a>
                    </li>-->

                </ul>
				<?php } ?>
                <div class="center p-20">
                     <a href="<?php echo ROOT_URL.'/common/logout.php'; ?>" class="btn btn-danger m-l-20 waves-effect waves-light">Logout</a>
                 </div>
            </div>
            
        </div>
		
        <!-- ============================================================== -->
        <!-- End Left Sidebar -->
        <!-- ============================================================== -->
