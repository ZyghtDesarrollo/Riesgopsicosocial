<style>
	.question{
		text-align: "justify";
	}
	.selected-answere{
		font-weight: bold;
	}
	.answere-box{
		margin-left: 20px;
	}
	
	.questionary-panel{
		display: none;
	}
</style>


<!-- start breadcrumb -->
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb">
			<li><a href="#">Home</a></li>
			<li class="active">Respuestas</li>
		</ol>
	</div>
</div>
<!-- end breadcrumb -->

<div class="row" id="questionary-type"></div>

<div class="row">
	<div class="col-sm-12" id="questionary"></div>
</div>

<div class="row">
	<div class="col-sm-12">
		<table id="example" class="table table-striped table-bordered"
			cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>ID</th>
					<th>Puesto de trabajo</th>
					<th>Código de compañía</th>
					<th>Estado</th>
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
	function showPanel(panelId){
		$(panelId).closest("div.questionary-panel").addClass("show").siblings().removeClass("show");
	}
	
	var table;
	$(document).ready(function() {
		var url = "http://riesgopsicosocial.azurewebsites.net/index.php/api/rquestionary/initialdata";
		//Call to API
		$.get(url)
			.done(function(response) {
				var panel = '';
				var questionary_type = '';
				var questionaries = null;
				$.each(response, function(i, obj){
					questionary_type += '<div class="col-sm-3">'
											+'<p><i class="glyphicon glyphicon-chevron-right"></i> <a href="#" onclick="showPanel(\'#panel-'+i+'\');">'+obj.name+'</a></p>'
										+'</div>';
					$("#questionary-type").html(questionary_type);
				});
				$.each(response, function(i, obj){
					panel += '<div id="panel-'+i+'" class="questionary-panel panel panel-default">'
				  		  	+'<div class="panel-body">';
					questionaries = response;
					panel += '<h2 class="text-center">'+obj.name+'</h2>';
					$.each(obj.categories, function(i, obj_category){
						panel += '<h3>'+obj_category.title+'</h3>';
						$.each(obj_category.questions, function(y, obj_question){
							panel += '<p class="question">'
			    			+'<ol start='+(y+1)+'>'
			    				+'<li>'+obj_question.title+'</li>'
							+'</ol>'
							
							+'<div class="answere-box">'
								+'<ol type="a">';
									$.each(obj_question.options, function(i, obj_options){
										panel += '<li>'+obj_options.title+'</li>';
										//<li class="selected-answere">Algunas veces <i class="glyphicon glyphicon-ok"></i></li>
									});
									panel+='</ol>'
				    	   		+'<p></p>'
						 		+'<hr>'
							+'</div>';
						});
						
					});
					panel += '</div>'
						+'</div>';	
				});

				$("#questionary").html(panel);
			})
			.fail(function() {
				//alert( "error" );
			})
			.always(function() {
				//alert( "finished" );
			});

		table = $('#example').DataTable({
	    		"select": true,
		    	"language": {
				    "url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Spanish.json"
				},
			   "ajax": {
          			"url": "http://riesgopsicosocial.azurewebsites.net/index.php/api/rjobposition/list_by_company_id?company_id=3",
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
		            	"data": "position" 
		            },
		            { 
		            	"data": "company_id"
		        	},
		            { 	
		            	"data": "active",
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
             				return data == '1' ? 'Activo' : 'Inactivo';
          				}
				    },
				    { 	//icons options
        				targets : [4],
          					render : function (data, type, row) {
          						var iconSwitch = '&nbsp;&nbsp;<i class="glyphicon glyphicon-off icon-action icon-deactivated" data-action="activate" aria-hidden="true"></i>';
          						if(data.active == 1){
          							iconSwitch = '&nbsp;&nbsp;<i class="glyphicon glyphicon-off icon-action" data-action="deactivate" aria-hidden="true" style="color : green"></i>';
          						}
          					return '<i class="glyphicon glyphicon-edit icon-action" data-action="edit" aria-hidden="true"></i>'+iconSwitch;
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
				var state = row.find('td:eq(3)').text();
				
				switch (action){
					case "edit":
						//set value to form
						$("#record-id").val(id);
						$("#record-name").val(name);
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
							"position" :  $("#record-name").val(),
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
						url = "http://riesgopsicosocial.azurewebsites.net/index.php/api/rjobposition/add";
					break;

					case "edit":
						url = "http://riesgopsicosocial.azurewebsites.net/index.php/api/rjobposition/edit";
					break;
					
					case "activate":
						url = "http://riesgopsicosocial.azurewebsites.net/index.php/api/rjobposition/activate";
					break;

					case "deactivate":
						url="http://riesgopsicosocial.azurewebsites.net/index.php/api/rjobposition/deactivate";
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