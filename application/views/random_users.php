<!-- start breadcrumb -->
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb">
			<li><a href="#">Home</a></li>
			<li class="active">Usuarios aleatorios</li>
		</ol>
	</div>
</div>
<!-- end breadcrumb -->

<div class="row">
	<div class="col-sm-12">
		<table id="example" class="table table-striped table-bordered"
			cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>ID</th>
					<th>Contraseña</th>
					<th>Fecha de creación</th>
					<th>Código de compañía</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-sm-12 text-center">
		<button id="btn-create" class="btn btn-success" data-action="create">Generar Usuarios Aleatorios</button>
	</div>
</div>


<!-- start modal for create / update-->
<div class="modal fade" tabindex="-1" id="form-modal" role="dialog"
	data-backdrop="static">
	<div class="modal-dialog">
		<!-- start Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="title">default</h4>
			</div>
			<div class="modal-body">
				<form id="form-record">
					<div class="form-group">
						<label for="record-quantity" class="form-control-label">Cantidad</label>
						<input type="number" class="form-control" id="record-quantity"
							name="record-quantity">
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn-action" class="btn btn-success"
					data-action="create">Crear</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
		<!-- Modal content-->
	</div>
</div>
<!-- end modal for create / update-->

<!-- start modal for confirm -->
<div class="modal fade" tabindex="-1" id="confirm-modal" role="dialog"
	data-backdrop="static">
	<div class="modal-dialog">
		<!-- start Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Confirmación</h4>
			</div>
			<!-- This section (div id="modal-body") will be loaded dynamically -->
			<div class="modal-body">
				<p>Por favor, presione para efectuar el cambio de estado</p>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn-action-confirm"
					class="btn btn-warning" data-action="">Aceptar</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
		<!-- Modal content-->
	</div>
</div>
<!-- end modal for confirm -->

<!-- start own script-->
<script>
	var table;
	$(document).ready(function() {
		table = $('#example').DataTable({
	    		"select": true,
		    	"language": {
				    "url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Spanish.json"
				},
			   "ajax": {
          			"url": "http://riesgopsicosocial.azurewebsites.net/index.php/api/rrandomuser/list_by_company_id",
          			"type": "GET",
          			"data" : {
              			"company_id" : company_id
              		}
        		},
        		"showRefresh": true,
            	"sAjaxDataProp" : "response",
	            "columns": [
	            	{ 	
	            		"data": "id" 
	            	},
		            { 	
		            	"data": "password" 
		            },
		            { 	
		            	"data": "date" 
		            },
		            { 
		            	"data": "company_id"
		        	},
	            ]
		    });
		} );

		//$('#example').find('.dataTables_filter').append('<button class="btn btn-success mr-xs pull-right" type="button">Crear</button>');

			var btnAction = $("#btn-action");
			//lang var text
			var textEdit = "Editar";
			var textDelete = "Eliminar";
			var textActivate = "Activar";
			var textDeActivate = "Desactivar";
			var textCreate = "Generar";

			//To prepare and display modal (edit, activate, deactivate)
			$('#example').on('click', 'i.icon-action', function (e) {
		        e.preventDefault();
		    
		        var action = $(this).attr("data-action");
		 		var row = $(this).closest('tr');
				var id = row.find('td:eq(0)').text();
				var name = row.find('td:eq(1)').text();
				var rut = row.find('td:eq(2)').text();
				var phone = row.find('td:eq(3)').text();
				var email = row.find('td:eq(4)').text();
				
				switch (action){
					case "edit":
						//set value to form
						$("#record-id").val(id);
						$("#record-name").val(name);
						$("#record-rut").val(rut);
						$("#record-phone").val(phone);
						$("#record-email").val(email);
						$("#title").text(textEdit);
						btnAction.text(textEdit);
						btnAction.attr("data-action", action);
						btnAction.attr("class", "btn btn-warning");
						$('#form-modal').modal('show');
					break;

					case "activate":
						var btn = $("#btn-action-confirm");
						$("#title").text(textActivate);
						btn.text(textActivate);
						btn.attr("data-id", id);
						btn.attr("data-action", action);
						btn.attr("class", "btn btn-warning");
						$('#confirm-modal').modal('show');
					break;

					case "deactivate":
						var btn = $("#btn-action-confirm");
						$("#title").text(textDeActivate);
						btn.text(textDeActivate);
						btn.attr("data-id", id);
						btn.attr("data-action", action);
						btn.attr("class", "btn btn-danger");
						$('#confirm-modal').modal('show');
					break;
				}
		    } );

			//To prepare and display modal (create)
		    $("#btn-create").click(function(){
				$('#form-record')[0].reset();
				$("#title").text(textCreate);
				btnAction.text(textCreate);
				btnAction.attr("class", "btn btn-success");
				btnAction.attr("data-action", "create");
				$('#form-modal').modal('show');
			});

		    //Event trigger (create / edit)
		    btnAction.click(function(e){
		    	e.preventDefault();
		    	var action = $(this).attr("data-action");
		    	var params = {
				    		"amount": $("#record-quantity").val(),
							"company_id" : company_id
						};
				if(action == "edit"){
					params.id = $("#record-id").val();
				}
				processAction(action, params);
		    });

		    //Event trigger (activate / deactivate)
			$("#btn-action-confirm").click(function(e){
		    	e.preventDefault();
		    	var action = $(this).attr("data-action");
		    	var params = {"id" : $(this).attr("data-id") };
				processAction(action, params);
		    });


			//Process actions
			function processAction(action, params){
				var url = "";
				switch (action){

					case "create":
						url = "http://riesgopsicosocial.azurewebsites.net/index.php/api/rrandomuser/generate";
					break;

					case "edit":
						url = "#";
					break;
					
					case "activate":
						url = "#";
					break;

					case "deactivate":
						url="#";
					break;
				}

				//Call to API
				$.post( url, params)  .done(function() {
					if ($('#form-modal').is(':visible')) {
    					$('#form-modal').modal('hide');
					}
					if ($('#confirm-modal').is(':visible')) {
    					$('#confirm-modal').modal('hide');
					}
					
				    table.ajax.reload( null, false );
				  })
				  .fail(function() {
				    //alert( "error" );
				  })
				  .always(function() {
				    //alert( "finished" );
				  });
			}
		</script>
<!-- end own script -->