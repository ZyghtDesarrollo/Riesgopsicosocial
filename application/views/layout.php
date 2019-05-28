<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Riesgo Psicosocial</title>

		<script>
			//User Roles & Menu
			var user = JSON.parse(sessionStorage.getItem("user"));

			if (!user) {
				window.location.href = '<?php echo base_url('login/'); ?>';
			}

			var company_id = user.id;
		</script>
		
		<!--css-->
		<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_BOOTSTRAP_CSS; ?>">
		
		<!--css datatable-->
		<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_DATATABLE_BOOTSTRAP_CSS; ?>">
		
		<!--css buttons-->
		<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_BOOTSTRAP_BUTTONS_CSS; ?>">
			
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="<?php echo RESOURCE_JQUERY_LIBRARY; ?>"></script>
		
		<link href="<?php echo RESOURCE_SELECT2_CSS; ?>" rel="stylesheet" />
		<script src="<?php echo RESOURCE_SELECT2_LIBRARY; ?>"></script>
		
		<!-- start general custom style -->
		<style>
			#app {
				width: 100vw;
				overflow-x: hidden;
			}
			
			.navbar-default .navbar-nav>li>a:hover, .navbar-default .navbar-nav>li>a:focus
				{
				background-color: #E3E5E6;
			}
			
			.logout {
					margin-top: 18px;
				}
			.icon-action {
				cursor: pointer;
				font-size: 19px;
			}
			
			.icon-deactivated {
				color: #D9534F;
			}
			
			td > i.glyphicon:hover{
				opacity: 0.5;
				color: red;
			}

            ul.hide-item > li{
                display:none !important;
            }

            .ajaxReplayMessage{ display:none;}
		</style>
		<!-- end general custom style -->
	</head>
	
	<body>
		<div id="app">
		
			<!-- start nav -->
			<?php 
				require_once 'partials/_menu.php';
			?>
			<!-- end nav -->
			
			<!-- start container -->
			<div class="container" style="margin-top: 80px;">
				<?php echo $content; //load dinamically?>
			</div>
			<!-- end container -->
		</div>
	
	<!--  start general custom script -->
	<script>
		function logout() {
			sessionStorage.removeItem("user");
			window.location.href = '<?php echo base_url('login/'); ?>';
		}	
	</script>
	<!-- end general custom script -->

	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="<?php echo RESOURCE_BOOTSTRAP_LIBRARY;?>" integrity="<?php echo RESOURCE_BOOTSTRAP_LIBRARY_INTEGRITY;?>" crossorigin="anonymous"></script>
	<!-- start scripts data tables-->
	<script src="<?php echo RESOURCE_DATATABLE_JQUERY_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_DATATABLE_BOOTSTRAP_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_DATATABLE_BUTTONS_DATATABLE_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_DATATABLE_BUTTONS_BOOTSTRAP_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_DATATABLE_BUTTONS_HTML5_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_DATATABLE_BUTTONS_PRINT_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_DATATABLE_BUTTONS_COLVIS_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_DATATABLE_BUTTONS_PRINT_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_JSZIP_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_PDFMAKE_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_PDFMAKE_FONTS_LIBRARY; ?>"></script>

	<!-- end scripts data tables-->

	<script>
		//User Roles & Menu
		if (user.name == 'superadmin') {
			$("#companies").attr('style','display: block !important');;
		} else {
			$("#jobPositions").attr('style','display: block !important');
			$("#randomUsers").attr('style','display: block !important');
			$("#psychosocialTeam").attr('style','display: block !important');
			$("#recomendations").attr('style','display: block !important');
			$("#resultsAnalysis").attr('style','display: block !important');
            $("#activityLog").attr('style','display: block !important');
            $("#notification").attr('style','display: block !important');
            //$("#billboard").attr('style','display: block !important');
		}
	</script>
</body>
</html>