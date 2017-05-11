<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <!--css-->
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

		<style type="text/css">
			#app {
				width: 100vw;
				overflow-x: hidden;
			}
		</style>
		<title>Riesgo Psicosocial</title>
	</head>
	<body>

		<div id="app">
			<div class="container" style="margin-top:80px;">
				<style type="text/css">
				.flex-center {
	               align-items: center;
	               display: flex;
	               justify-content: center;
			    }
				</style>

				<div class="row flex-center">
					<div class="col-md-4">
						<div class="panel panel-default">
							<div class="panel-heading">
						    	<h3 class="panel-title">Autenticación</h3>
						  	</div>
						  	<form id="form-login">
							  	<div class="panel-body">
						          	<div class="form-group">
							            <label for="password" class="form-control-label">Contraseña</label>
							            <input type="password" class="form-control" id="password" name="password">
						          	</div>
						          	<div class="form-group">
							            <label for="code" class="form-control-label">Código</label>
							            <input type="text" class="form-control" id="code" name="code">
						          	</div>
						          	<input type="hidden" id="record-id">
							  	</div>
							  	<div class="panel-footer text-center">
							  		<button id="btn-login" class="btn btn-success">Aceptar</button>
							  		<button class="btn btn-default" type="reset">Limpiar</button>
							  	</div>
						  	</form>
						</div>
					</div>
				</div>
			</div>	
		</div>
		

		<!-- start own script-->
		<script>
			$("#btn-login").click(function(e){
				e.preventDefault();

				var params = $("#form-login").serialize();
				var url = "http://riesgopsicosocial.azurewebsites.net/index.php/api/rcompany/login";
				//Call to API
				$.post(url, params)
					.done(function(data) {
						sessionStorage.setItem("user", JSON.stringify(data.user));

						if (data.user.name == 'superadmin') {
							window.location.href = "<?php echo base_url('companies/'); ?>";
						} else {
							window.location.href = "<?php echo base_url('job_positions/'); ?>";
						}
					})
					.fail(function() {
						alert("Por favor verifique los datos ingresados");
					})
					.always(function() {
						//alert( "finished" );
					});
			});
		</script>
		<!-- end own script -->

	    <!-- Include all compiled plugins (below), or include individual files as needed -->
	    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	</body>
</html>
