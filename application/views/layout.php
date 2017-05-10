<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Riesgo Psicosocial</title>
		
		<!--css-->
		<link rel="stylesheet" type="text/css"
			href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		
		<!--css datatable-->
		<link rel="stylesheet" type="text/css"
			href="//cdn.datatables.net/1.10.13/css/dataTables.bootstrap.min.css">
			
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script
			src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		
		<!-- start general custom style -->
		<script>
		//User Roles & Menu
		var company_id = 3; //Replace after use session storage
// 		var user = JSON.parse(sessionStorage.getItem("user"));
// 		if(user){
// 			if (user.username == 'superadmin') {
// 				$("#companies").show();
// 				$("#jobPositions").hide();
// 				$("#randomUsers").hide();
// 				$("#psychosocialTeam").hide();
// 				$("#recomendations").hide();
// 				$("#resultsAnalysis").hide();
// 			} else if (user.admin) {
// 				company_id = user.id;
// 				$("#companies").show();
// 				$("#jobPositions").show();
// 				$("#randomUsers").show();
// 				$("#psychosocialTeam").show();
// 				$("#recomendations").show();
// 				$("#resultsAnalysis").show();
// 			}
// 		}else{
//			window.location.href = '<?php echo base_url('login/'); ?>';
// 			}
		</script>
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
	<script
		src="//cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"></script>

	<!-- end scripts data tables-->
</body>
</html>