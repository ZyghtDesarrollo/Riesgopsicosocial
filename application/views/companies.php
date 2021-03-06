<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_FONTS_CSS;?>">
<!-- start breadcrumb -->
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb">
			<li><a href="#">Home</a></li>
			<li class="active">Centros de Trabajo</li>
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
					<th>Nombre</th>
					<th>Código</th>
					<th>RUT</th>
					<th>Correo electrónico</th>
                    <th>Estado</th>
                    <th>Usuarios Aleatorios</th>
                    <th>Respuestas</th>
                    <th>Riesgo</th>
                    <th>Nivel de Riesgo</th>
					<th>Acciones</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-sm-12 text-center">
		<button id="btn-create" class="btn btn-success" data-action="create">Crear</button>
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
						<label for="record-name" class="form-control-label">Nombre</label>
						<input type="text" class="form-control" id="record-name"
							name="record-name">
					</div>
					<div class="form-group">
						<label for="record-code" class="form-control-label">Código</label>
						<input type="text" class="form-control" id="record-code"
							name="record-code">
					</div>
					<div class="form-group">
						<label for="record-rut" class="form-control-label">RUT</label>
						<input type="text" class="form-control" id="record-rut"
							name="record-rut">
					</div>
					<div class="form-group">
						<label for="record-email" class="form-control-label">Correo electrónico</label>
						<input type="text" class="form-control" id="record-email"
							name="record-email">
					</div>
					<div class="form-group">
						<label for="record-password" class="form-control-label">Contraseña</label>
						<input type="password" class="form-control" id="record-password"
							name="record-password">
					</div>
					<input type="hidden" id="record-id">
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
                    buttons: [{
                        extend: 'excel',
                        exportOptions: {
                            columns: ':visible'
                        },
                        text:      '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
                        titleAttr: 'Excel'
                    }],
		    		"select": true,
			    	"language": {
						"url": "<?php echo RESOURCE_DATATABLE_LANGUAGE; ?>"
					},
					"ajax": {
					   "url": "<?php echo base_url('api/rcompany/list');?>",
					   "type": "GET"
	        		},
	        		"initComplete": function( settings, json ) {
                        table.buttons().container()
                            .appendTo( $('#example_wrapper .col-sm-6:eq(0)'));
	        			$("#example_filter").append("&nbsp;&nbsp;<button id='refresh' "
	                			+"class='btn btn-button' "
	                			+"data-loading-text='Actualizando...'><span class='glyphicon glyphicon-refresh'></span></button>");
	        			$("#refresh").click(function(){
	        				table.ajax.reload( null, false );
	        				var $this = $(this);
	        				  $this.button('loading');
	        				    setTimeout(function() {
	        				       $this.button('reset');
	        				   }, 3000);
	            		});
	        		  },
	        		"showRefresh": true,
	            	"sAjaxDataProp" : "response",
		            "columns": [
		            	{ 	
		            		"data": "id" 
		            	},
			            { 	
			            	"data": "name" 
			            },
			            { 
			            	"data": "code"
			        	},
			        	{
							"data": "rut"
				        },
				        {
							"data": "email"
						},
                        {
                            "data": "active",
                        },
                        {
                            "data": "total_workers",
                        },
                        {
                            "data": "total_answers",
                        },
                        {
                            "data": "company_risk",
                        },
                        {
                            "data": "company_risk_name",
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
                            targets : [5],
                            render : function (data, type, row) {
                                return data == '1' ? 'Activo' : 'Inactivo';
                            }
                        },
                        { 	//param active
                            targets : [8],
                            render : function (data, type, row) {
                                if(<?php echo MEDIUM_RISK_THRESHOLD; ?> > data )
                                {
                                    return '<b><span style="color : green">'+data+'</span></b>';
                                }
                                else if(<?php echo HIGH_RISK_THRESHOLD; ?> <= data )
                                {
                                    return '<span style="color : red">'+data+'</span>';
                                }
                                return '<span style="color : black">'+data+'</span>';
                            }
                        },
                        { 	//param active
                            targets : [9],
                            render : function (data, type, row) {
                                if('<?php echo LOW_RISK_NAME; ?>' == data )
                                {
                                    return '<b><span style="color : green">'+data+'</span></b>';
                                }
                                else if('<?php echo HIGH_RISK_NAME; ?>' == data )
                                {
                                    return '<span style="color : red">'+data+'</span>';
                                }
                                return '<span style="color : black">'+data+'</span>';
                            }
                        },
					    { 	//icons options
	        				targets : [10],
	          					render : function (data, type, row) {
									var actionBar = '<i class="glyphicon glyphicon-edit icon-action" data-action="edit" title="Editar" aria-hidden="true"></i>';
									actionBar = actionBar + '&nbsp;&nbsp;<a title="Exportar a PDF" style="color:inherit; text-decoration:inherit;" href="<?php echo base_url('results_analysis/get_pdf_report/'); ?>/'+data.id+'"><i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i></a>';
	          						var iconSwitch = '&nbsp;&nbsp;<i class="glyphicon glyphicon-off icon-action icon-deactivated" title="Activar" data-action="activate" aria-hidden="true"></i>';
	          						if(data.active == 1){
	          							iconSwitch = '&nbsp;&nbsp;<i class="glyphicon glyphicon-off icon-action" title="Desactivar" data-action="deactivate" aria-hidden="true" style="color : green"></i>';
	          						}
	          					return actionBar+iconSwitch;
	          				}
					    }
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
			var textCreate = "Crear";

			//To prepare and display modal (edit, activate, deactivate)
			$('#example').on('click', 'i.icon-action', function (e) {
		        e.preventDefault();
		    
		        var action = $(this).attr("data-action");
		 		var row = $(this).closest('tr');
				var id = row.find('td:eq(0)').text();
				var name = row.find('td:eq(1)').text();
				var code = row.find('td:eq(2)').text();
				var rut = row.find('td:eq(3)').text();
				var email = row.find('td:eq(4)').text();
				
				switch (action){
					case "edit":
						//set value to form
						$("#record-id").val(id);
						$("#record-name").val(name);
						$("#record-code").val(code);
						$("#record-rut").val(rut);
						$("#record-email").val(email);
						$("#record-password").val('');
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
							"name" :  $("#record-name").val(),
							"code" : $("#record-code").val(),
							"rut" : $("#record-rut").val(),
							"email" : $("#record-email").val(),
							"password" : $("#record-password").val()
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
						url = "<?php echo base_url('api/rcompany/add');?>";
					break;

					case "edit":
						url = "<?php echo base_url('api/rcompany/edit');?>";
					break;
					
					case "activate":
						url = "<?php echo base_url('api/rcompany/activate');?>";
					break;

					case "deactivate":
						url = "<?php echo base_url('api/rcompany/deactivate');?>";
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