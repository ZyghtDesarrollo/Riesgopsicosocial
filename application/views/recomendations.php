<script>
	var user = sessionStorage.getItem('user');
	user = JSON.parse(user);
</script>

<style>
	.video-iframe{
		width: 100%;
		height: 345px;
	}
	
	blockquote{
  		margin: 20px 0;
  		padding-left: 1.5rem;
  		border-left: 5px solid #64b5f6; /* Just change the color value and that's it*/
	}
</style>
<!-- start breadcrumb -->
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb">
			<li><a href="#">Home</a></li>
			<li class="active">Recomendaciones</li>
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
					<th>Categoría</th>
					<th>Título</th>
					<th>Descripción</th>
					<th>URL</th>
					<th>Estado</th>
					<th>Acciones</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

<div class="row" id="row_btn_create">
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
						<label for="record-question-category" class="form-control-label">Categoría</label>
						<select id="record-question-category" name="record-question-category" class="form-control">
							<option value="-1">Seleccione...</opstion>
						</select>
					</div>
					<div class="form-group">
						<label for="record-title" class="form-control-label">Título</label>
						<input type="text" class="form-control" id="record-title"
							name="record-title">
					</div>
					<div class="form-group">
						<label for="record-description" class="form-control-label">Descripción</label>
						<input type="text" class="form-control" id="record-description"
							name="record-description">
					</div>
					<div class="form-group">
						<label for="record-url" class="form-control-label">URL (Video)</label>
						<blockquote>
  							<h5>Agregue sólo URL de videos embebidos.<br>
  							 Si desea agregar un video de <strong>Youtube</strong>, presione "compartir", luego seleccione la pestaña "insertar", copie sólo el enlace que está en "src"</h5>
						</blockquote>
						<input type="text" class="form-control" id="record-url"
							name="record-url">
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
				<p>¿Está seguro que desea eliminar el registro?</p>
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

<!-- start modal for display video -->
<div class="modal fade" tabindex="-1" id="video-modal" role="dialog"
	data-backdrop="static">
	<div class="modal-dialog">
		<!-- start Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Videos recomendado</h4>
			</div>
			<!-- This section (div id="modal-body") will be loaded dynamically -->
			<div id="loading" class="text-center" style="display:none;">
					<img style="width: 200px; height: 200px;" src="<?php echo explode('index.php', base_url())[0]?>assets/imgs/busy.gif" alt="Cargando" />
			</div>
			<div class="modal-body" id="modal-body">
				<h3 id="rHeadTitle"></h3>
				<h4 id="rTitle"></h4>
				<iframe id="rVideo" class="video-iframe" src="" frameborder="0" allowfullscreen></iframe>
				<h4 id="rDescription"></h4>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
		<!-- Modal content-->
	</div>
</div>
<!-- end modal for display video -->

<!-- start own script-->
<script>
	var table;
	$(document).ready(function() {
		$.fn.dataTable.ext.buttons.reload = {
			    text: 'Reload',
			    action: function ( e, dt, node, config ) {
			        dt.ajax.reload();
			    }
			};
		
		table = $('#example').DataTable({
	    		"select": true,
		    	"language": {
				    "url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Spanish.json"
				},
			   "ajax": {
          			"url": "http://riesgopsicosocial.azurewebsites.net/index.php/api/rrecommendation/list_actives_by_company_id",
          			"type": "GET",
          			"data" : {
              			"company_id" : company_id
              		},
              		buttons: [
              	        'reload'
              	    ]
       
        		},
        		"initComplete": function( settings, json ) {
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
	            		"data": "question_category_title" 
	            	},
		            { 	
		            	"data": "title" 
		            },
		            { 
		            	"data": "description"
		        	},
		            { 	
		            	"data": "link"
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
        				targets : [5],
          					render : function (data, type, row) {
             				return data == '1' ? 'Activo' : 'Inactivo';
          				}
				    },
				    { 	//icons options
        				targets : [6],
          					render : function (data, type, row) {
              					//For logic elimination
//           						var iconSwitch = '&nbsp;&nbsp;<i class="glyphicon glyphicon-off icon-action icon-deactivated" data-action="activate" aria-hidden="true"></i>';
//           						if(data.active == 1){
//           							iconSwitch = '&nbsp;&nbsp;<i class="glyphicon glyphicon-off icon-action" data-action="deactivate" aria-hidden="true" style="color : green"></i>';
//           						}

							var iconVideo = '';
							var iconEdit = '';
							var iconTrash = '';
							var hideOption = '';

							if(!data.link){
								hideOption = 'style="visibility:hidden;"';
							}

							if (user.name !== 'superadmin') {
								iconVideo = '<i class="glyphicon glyphicon-film icon-action"'
									+' data-action="showVideo" aria-hidden="true" title="Ver video" ' + hideOption + '></i>&nbsp;&nbsp;';
								iconEdit = '<i class="glyphicon glyphicon-edit icon-action" data-action="edit" aria-hidden="true"></i>';
								iconTrash ='&nbsp;&nbsp;<i class="glyphicon glyphicon glyphicon-trash icon-action icon-deactivated" data-action="deactivate" aria-hidden="true""></i>';
							}

          					return iconVideo + iconEdit + iconTrash;
          				}
				    }
				]
		    });
		});


		//$('#example').find('.dataTables_filter').append('<button class="btn btn-success mr-xs pull-right" type="button">Crear</button>');

			var btnAction = $("#btn-action");
			//lang var text
			var textEdit = "Editar";
			var textDelete = "Eliminar";
			var textActivate = "Activar";
			var textDeActivate = "Eliminar";
			var textCreate = "Crear";

			//To prepare and display modal (edit, activate, deactivate)
			$('#example').on('click', 'i.icon-action', function (e) {
		        e.preventDefault();
		    
		        var action = $(this).attr("data-action");
		 		var row = $(this).closest('tr');
				var id = row.find('td:eq(0)').text();
				var qc_title = row.find('td:eq(1)').text();
				var title = row.find('td:eq(2)').text();
				var description = row.find('td:eq(3)').text();
				var url = row.find('td:eq(4)').text();;
				
				switch (action){
					case "edit":
						//set value to form
						$("#record-id").val(id);
						$("#record-question-category option").filter(function () {
						    return $(this).text() === qc_title;
						}).prop("selected", true);
						$("#record-title").val(title);
						$("#record-description").val(description);
						$("#record-url").val(url);
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

					case "showVideo":
						$("#rVideo").attr("src", url);
						$("#rHeadTitle").text("");
						$("#rTitle").text("");
						$("#rDescription").text("");
						$("#rHeadTitle").html('<div class="alert alert-info"><span style="font-size:18px;">'+qc_title+'</span></div>');
						$("#rTitle").text(title);
						$("#rDescription").text(description);
						$('#video-modal').modal('show');
					break;
				}
		    } );

			$('#video-modal').on('hidden.bs.modal', function () {
				$("#rVideo").attr("src", "");
			});

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
				    		"company_id" : company_id,
				    		"questionCategoryId": $("#record-question-category").val(),
							"title" :  $("#record-title").val(),
							"description" :  $("#record-description").val(),
							"link" :  $("#record-url").val()
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
						url = "http://riesgopsicosocial.azurewebsites.net/index.php/api/rrecommendation/add";
					break;

					case "edit":
						url = "http://riesgopsicosocial.azurewebsites.net/index.php/api/rrecommendation/edit";
					break;
					
					case "activate":
						url = "http://riesgopsicosocial.azurewebsites.net/index.php/api/rrecommendation/activate";
					break;

					case "deactivate":
						url="http://riesgopsicosocial.azurewebsites.net/index.php/api/rrecommendation/deactivate";
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

			//Start load recommendations
			$.get("http://riesgopsicosocial.azurewebsites.net/index.php/api/rquestioncategory/list_actives")
			.done(function(data) {
				$.each(data.response, function(index, obj){
					$("#record-question-category").append('<option value="'+obj.id+'">'+obj.title+'</option>');
				});
		  	})
		  	.fail(function(e) {
		    	console.log(e);
		  	})
		  	.always(function() {
		  		//console.log(JSON.stringify(companies));
		    	//alert( "finished" );
			});
			//End load recommendations
		</script>
<!-- end own script -->