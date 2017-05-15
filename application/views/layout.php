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
		<link rel="stylesheet" type="text/css"
			href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		
		<!--css datatable-->
		<link rel="stylesheet" type="text/css"
			href="//cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
			
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script
			src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		
		<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
		<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
		
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
	<script
		src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<!-- start scripts data tables-->
	<script src="//cdn.datatables.net/1.10.13/js/jquery.dataTables.js"></script>
	<script src="//cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>

	<!-- end scripts data tables-->

	<script>
		//User Roles & Menu
		if (user.name == 'superadmin') {
			$("#companies").show();
			$("#jobPositions").hide();
			$("#randomUsers").hide();
			$("#psychosocialTeam").hide();
			$("#recomendations").show();
			$("#resultsAnalysis").hide();
		} else {
			$("#companies").hide();
			$("#jobPositions").show();
			$("#randomUsers").show();
			$("#psychosocialTeam").show();
			$("#recomendations").show();
			$("#resultsAnalysis").show();
		}
	</script>
</body>
</html>