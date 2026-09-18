<?php


error_reporting(E_ALL);
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');

include('../include/session.php');
//print_r($session);exit;
?>
<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="Swamy">
	<meta name="theme-color" content="#0c8bf1" />
	<meta name="keywords" content="">
	<link href="<?php echo SECURE_PATH; ?>vendor/images/baseline_local_activity_black_36dp.png" sizes="16X16" rel="shortcut icon" type="image/png" />

	<?php //echo "Session file loaded successfully"; exit; ?>

	<title>Admin</title>
	<?php $session->commonUserCSS(); ?>
	<?php $session->commonUserJS(); ?>

	
</head>

<body id="page-top">

	<div class="preloader">
		<div class="loader"></div>
	</div>

	<nav id="sidebar">
		<div class="sidebar-header">
			<a href="dashboard.html">
				<h2 class="m-0"><i class="baseline-local_activity icon-image-preview icon-white icon-36"></i> Admin</h2>
			</a>
		</div>
		<ul class="side-menu accordion" id="accordionSidebar">
			<?php
			if ($session->userlevel == 9) {
			?>
				<li class="nav-item">
					<a class="nav-link" onClick="setState('content-page','<?php echo SECURE_PATH ?>projects_list/','getLayout=true')"><i class="icon fa fa-user" aria-hidden="true"></i>
						Projects </a>
				</li>

				<!-- <li class="nav-item">
					<a class="nav-link" onClick="setState('content-page','<?php echo SECURE_PATH ?>view_project/','getLayout=true')"><i class="icon fa fa-users" aria-hidden="true"></i>
						View Project</a>
				</li>

				<li class="nav-item">
					<a class="nav-link" onClick="setState('content-page','<?php echo SECURE_PATH ?>add_project_new/','getLayout=true')"><i class="icon fa fa-user" aria-hidden="true"></i>
						Add Project New</a>
				</li>

				<li class="nav-item">
					<a class="nav-link" onClick="setState('content-page','<?php echo SECURE_PATH ?>view_project_new/','getLayout=true')"><i class="icon fa fa-users" aria-hidden="true"></i>
						View Project New</a>
				</li> -->


			<?php
			}
			?>
		</ul>

	</nav>

	<div class="menu-overlay"></div>

	<main class="main-wrapper">
		<header class="header-area">
			<nav class="navbar navbar-expand navbar-light bg-admin">
				<div class="container">
					<button class="btn btn-light btn-circle text-theme order-1 order-sm-0" id="sidebarCollapse">
						<i class="material-icons text-theme md-18">more_vert</i>
					</button>
					<!-- Navbar Search -->
					<form class="form-inline mr-auto mr-0 ml-md-3">
						<div class="has-search">
							<span class="material-icons md-24 form-control-feedback">search</span>
							<input type="text" class="form-control" placeholder="Search...">
						</div>
					</form>

					<!-- Navbar -->
					<ul class="navbar-nav ml-auto ml-md-0">

						<li class="nav-item dropdown no-arrow">
							<a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<i class="material-icons">notifications</i>
								<span class="badge badge-danger">9+</span>
							</a>
							<div class="dropdown-menu notification dropdown-menu-right" aria-labelledby="alertsDropdown">
								<ul>
									<li>
										<div class="drop-title">Your Notifications</div>
									</li>
									<li>
										<div class="notification-center">
											<a href="#">
												<div class="btn btn-danger btn-circle shadow"><i class="material-icons">notifications_none</i></div>
												<div class="notification-contnet">
													<h5>Luanch Admin</h5> <span class="mail-desc">Just see the my new
														admin!</span> <span class="time">9:30 AM</span>
												</div>
											</a>
											<a href="#">
												<div class="btn btn-success btn-circle"><i class="material-icons">notifications_none</i></div>
												<div class="notification-contnet">
													<h5>Event today</h5> <span class="mail-desc">Just a reminder that
														you have event</span> <span class="time">9:10 AM</span>
												</div>
											</a>
											<a href="#">
												<div class="btn btn-info btn-circle"><i class="material-icons">notifications_none</i></div>
												<div class="notification-contnet">
													<h5>Settings</h5> <span class="mail-desc">You can customize this
														template as you want</span> <span class="time">9:08 AM</span>
												</div>
											</a>
											<a href="#">
												<div class="btn btn-primary btn-circle"><i class="material-icons">notifications_none</i></div>
												<div class="notification-contnet">
													<h5>Pavan kumar</h5> <span class="mail-desc">Just see the my
														admin!</span>
													<span class="time">9:02 AM</span>
												</div>
											</a>
										</div>
									</li>
									<li>
										<a class="pt-2 nav-link text-center" href="javascript:void(0);"> All
											notifications</a>
									</li>
								</ul>
							</div>
						</li>
						<li class="nav-item dropdown no-arrow">
							<a class="nav-link dropdown-toggle pt-1" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<img src="<?php echo SECURE_PATH; ?>vendor/images/user-profile.png" width="35" alt="profile-user" class="rounded-circle">
								<div class="d-none d-xl-inline-block">Rajan</div>
							</a>
							<div class="dropdown-menu dropdown-menu-right logout" aria-labelledby="userDropdown">
								<a class="dropdown-item" href="#"><i class="material-icons">settings</i> Settings</a>
								<a class="dropdown-item" href="#"><i class="material-icons">style</i> Activity Log</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" onClick="setState('content','<?php echo SECURE_PATH; ?>login_process.php','userLogout=true')" data-toggle="modal" data-target="#logoutModal"><i class="material-icons">exit_to_app</i>
									Logout</a>
							</div>
						</li>
					</ul>
				</div>
			</nav>
		</header>
		<div class="content-wrapper">
			<!-- Content Area-->
			<section class="content-area">
				<div class="container" id="content-page">

				</div>
			</section>
			<!-- End Cards-->

			<!-- Scroll to Top Button-->
			<a class="scroll-to-top rounded" href="#page-top">
				<i class="material-icons">navigation</i>
			</a>
		</div>
		<footer class="footer border-top py-3 text-center">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<div class="copy-rights">
							<span class="text-dark">&copy; 2019 <a href="#" target="_blank">Admin</a> All rights
								reserved.</span>
						</div>
					</div>
				</div>
			</div>
		</footer>
	</main>

	<script type="text/javascript">
		$(".side-menu li").click(function() {
			$(".side-menu li").removeClass("active");
			$(this).addClass("active");
		});


		$(document).ready(function() {
			var i = 0;
			var colors = ["2196F3", "EC407A", "4CAF50", "FF7043", "795548", "607D8B", "f44336"];
			$(".side-menu li").each(function() {
				$(this).find("a i").css({
					"font-size": "16px",
					"color": "#" + colors[i]
				});
				i++;
				if (i > colors.length) {
					i = 0;
				}
			});
		});

		function onlyAlphabets(evt, t) {

			try {

				evt = (evt) ? evt : window.event;
				var charCode = (evt.which) ? evt.which : evt.keyCode;
				var ctrlDown = evt.ctrlKey || evt.metaKey // Mac support
				if ((charCode > 64 && charCode < 91) || (charCode > 96 && charCode < 123) || charCode == 32 || charCode == 8 || charCode == 37 || charCode == 39 || (ctrlDown && charCode == 86))

					return true;

				else

					return false;

			} catch (err) {

				console.log(err);

			}

		}

		function scrollThis(id, off) {
			$('html,body').animate({
				scrollTop: $("#" + id).offset().top - $(window).height() / off
			}, 1000);
		}

		function isMobile(evt, ref, len) {
			evt = (evt) ? evt : window.event;
			var charCode = (evt.which) ? evt.which : evt.keyCode;
			if (charCode == 8 || charCode == 9) {
				return true;
			}

			var ctrlDown = evt.ctrlKey || evt.metaKey // Mac support

			if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46 || charCode == 8 || charCode == 37 || charCode == 39 || (ctrlDown && charCode == 86)) {
				return false;
			} else if (ref.val().length >= len) {

				return false;
			}



			if (parseInt(ref.val().charAt(0)) < 7) {
				$('#mobile_error').html(" * Invalid Mobile Number");
				return false;
			}

			$('#mobile_error').html("");
			return true;
		}

		function isNumber(evt, ref, len) {
			evt = (evt) ? evt : window.event;
			var charCode = (evt.which) ? evt.which : evt.keyCode;
			if (charCode == 8 || charCode == 9) {
				return true;
			}
			var ctrlDown = evt.ctrlKey || evt.metaKey // Mac support

			if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46 || charCode == 8 || charCode == 37 || charCode == 39 || (ctrlDown && charCode == 86)) {
				return false;
			} else if (ref.val().length >= len) {
				return false;
			}
			return true;
		}
	</script>


</body>

</html>