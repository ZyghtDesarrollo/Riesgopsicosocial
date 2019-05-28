<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">

		<!--css-->
		<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_BOOTSTRAP_CSS; ?>">
		
		<!--css datatable-->
		<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_DATATABLE_BOOTSTRAP_CSS; ?>">
		
		<!--css buttons-->
		<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_BOOTSTRAP_BUTTONS_CSS; ?>">
			
		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="<?php echo RESOURCE_JQUERY_LIBRARY; ?>"></script>

		<style type="text/css">
			#app {
				width: 100vw;
				overflow-x: hidden;
			}
			.navbar-default .navbar-nav > li > a:hover, .navbar-default .navbar-nav > li > a:focus {
			    background-color: #E3E5E6;
			}
			.logout{
				margin-top: 18px;
			}
		</style>
		<title>API Test</title>
	</head>
	<body>
		<div id="app">
			<nav class="navbar navbar-default navbar-fixed-top">
				<div class="container">
					<ul class="nav navbar-nav">
						<li id="companies"><a href="<?php echo base_url('companies/'); ?>">Centros de Trabajo</a></li>
						<li id="users"><a href="<?php echo base_url('users/'); ?>">Usuarios</a></li>
						<li id="travels" class="active"><a href="<?php echo base_url('travels/'); ?>">Trayectos</a></li> 
					</ul>
					<span class="pull-right logout"><a href="javascript:logout();" style="text-decoration: none; cursor: pointer;">Cerrar Sesión</a></span>
				</div>	
			</nav>

			<div class="container" style="margin-top:80px;">
				<style type="text/css">
				.icon-action{
					cursor: pointer;
					font-size: 19px;
				}
				.icon-deactivated{
					color: #D9534F;
				}
				</style>
				<div class="row">
					<div class="col-sm-12">
					<ol class="breadcrumb">
						<li><a href="#">Home</a></li>
		                <li class="active">Trayectos</li>
					</ol>                                  
				</div>
				</div>
				<div class="row">
					<div class="col-sm-12">
						<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
					        <thead>
					            <tr>
					                <th>ID</th>
					                <th>Usuario</th>
					                <th>Fecha</th>
					                <th>Acciones</th>
					            </tr>
					        </thead>
				    	</table>
					</div>
				</div>

				<div class="row" style="display:none;">
					<div class="col-sm-12 text-center">
						<button id="btn-create" class="btn btn-success" data-action="create">Crear</button>
					</div>
				</div>
			</div>	
		</div>
		
		<!-- start own script-->
		<script>
			var table;
			$(document).ready(function() {
				// User Roles & Menu
				var user = JSON.parse(sessionStorage.getItem("user"));
				if (user.username == 'superadmin') {
					$("#companies").show();
					$("#users").show();
					$("#travels").hide();
				} else if (user.admin) {
					$("#companies").hide();
					$("#users").show();
					$("#travels").show();
				}

				endpoint = "<?php echo base_url('api/rtravel/list');?>";
				if (user.company_id) {
					endpoint += "?company_id=" + user.company_id;
				}

				table = $('#example').DataTable({
		    		"select": true,
			    	"language": {
						"url": "<?php echo RESOURCE_DATATABLE_LANGUAGE; ?>"
					},
				   "ajax": {
	          			"url": endpoint,
	          			"type": "GET"
	        		},
	        		"showRefresh": true,
	            	"sAjaxDataProp" : "response",
		            "columns": [
		            	{ 	
		            		"data": "id" 
		            	},
			            { 	
			            	"data": "appuser" 
			            },
			            { 
			            	"data": "date"
			        	},
			            {
			            	"data": null,
			                "className": "center",
			                "defaultContent": ''
			                	//+'&nbsp;&nbsp;<i class="glyphicon glyphicon-trash icon-action" data-action="delete" data-id="2" aria-hidden="true"></i>'
			            }
		            ],
		            
		            "columnDefs" : [
	        			{ 	//param active
	        				targets : [3],
	          					render : function (data, type, row) {
	             				return '<i class="glyphicon glyphicon-download-alt icon-action" data-action="download" aria-hidden="true"></i>';
	          				}
					    }
					]
			    });
			} );


			//To prepare and display modal (edit, activate, deactivate)
			$('#example').on('click', 'i.icon-action', function (e) {
		        e.preventDefault();
		 		var row = $(this).closest('tr');
				var id = row.find('td:eq(0)').text();
    			window.location.href = '<?php echo base_url('api/rtravel/download_logs?travel_id=');?>'+id;

		    } );
		
			function logout() {
				sessionStorage.removeItem("user");
				window.location.href = '<?php echo base_url('login/'); ?>';
			}		
		</script>
		<!-- end own script -->


	<!-- Include all compiled plugins (below), or include individual files as needed -->
	<script src="<?php echo RESOURCE_BOOTSTRAP_LIBRARY;?>"></script>
	<!-- start scripts data tables-->
	<script src="<?php echo RESOURCE_DATATABLE_JQUERY_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_DATATABLE_BOOTSTRAP_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_DATATABLE_BUTTONS_DATATABLE_LIBRARY; ?>"></script>
	<script src="<?php echo RESOURCE_DATATABLE_BUTTONS_BOOTSTRAP_LIBRARY; ?>"></script>
	<!-- end scripts data tables-->
	
	</body>
</html>