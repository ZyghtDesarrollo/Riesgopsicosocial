<script>
	var questionaries = null;

	function findAnswer(id, answers) {
		response = false;

		$.each(answers, function(i, obj){
			if (id == obj.question_option_id) {
				response = true;
			}
		});

		return response;	
	}

	function getQuestionary(questionaryType, questionaries, answers) {
		questionary = (questionaryType == "Cuestionario Breve") ? questionaries[0] : questionaries[1];

		var panel = '';

		panel += '<div id="panel-'+ questionary.id +'" class="questionary-panel panel panel-default">'
	  		  	+'<div class="panel-body">';

		panel += '<h2 class="text-center">'+ questionary.name +'</h2>';

		$.each(questionary.categories, function(i, obj_category){
			panel += '<h4>'+obj_category.title+'</h4>';

			$.each(obj_category.questions, function(y, obj_question){
				panel += '<p class="question">'
	    			+ '<ol start='+(y+1)+'>' + '<li>'+obj_question.title+'</li>' + '</ol>'
					+ '<div class="answere-box">'
						+'<ol type="a">';

				$.each(obj_question.options, function(i, obj_options){
					if (findAnswer(obj_options.id, answers) == false) {
						panel += '<li>' + obj_options.title + '</li>';
					} else {
						panel += '<li class="selected-answere">' + obj_options.title + ' <i class="glyphicon glyphicon-ok"></i></li>';
					}
				});

				panel += '</ol>'
	    	   		+ '<p></p>'
			 		+ '<hr>'
					+ '</div>';
			});						
		});

		panel += '</div>'
			+'</div>';	

		return panel;
	}
</script>

<style>
	.selected-answere{
        font-weight: bold;
    }	
	
	.video-iframe{
		width: 100%;
		height: 345px;
	}
	
	td > i.glyphicon:hover{
		opacity: 0.5;
		color: red;
	}

	.modal-body{
		height: 500px;
		overflow-y: auto;
	}
</style>

<!-- start breadcrumb -->
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb">
			<li><a href="#">Home</a></li>
			<li class="active">Revisión de cuestionarios</li>
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
					<th>Tipo de cuestionario</th>
					<th>Puesto de trabajo</th>
					<th>Código de compañía</th>
					<th>Estado</th>
					<th>Fecha de creación</th>
					<th>Acciones</th>
				</tr>
			</thead>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-sm-12 text-center" style="display:none;">
		<button id="btn-create" class="btn btn-success" data-action="create">Crear</button>
	</div>
</div>


<!-- start modal for create / update-->
<div class="modal fade" tabindex="-1" id="form-modal" role="dialog"
	data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<!-- start Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="title">default</h4>
			</div>
			<div class="modal-body">
				<div id="questionary" style="padding:0px;"></div>
				<form id="form-record">
					<div class="form-group">
						<label for="record-recommendation" class="form-control-label">Recomendación (Video sugerido)</label>
						<select id="record-recommendation"
							name="record-recommendation" multiple="multiple" class="form-control-label" style="width: 100%;">
							<option value="-1">Seleccione</option>
						</select>
					</div>
					<input type="hidden" id="record-id">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn-action" class="btn btn-success"
					data-action="create"
					data-loading-text="&lt;span&gt;&lt;i class='fa fa-refresh 
					fa-spin'&gt;&lt;/i&gt;&nbsp;&nbsp; Procesando...&lt;span&gt;">Crear</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
		<!-- Modal content-->
	</div>
</div>
<!-- end modal for create / update-->

<!-- start modal for confirm -->
<div class="modal fade" tabindex="-1" id="video-modal" role="dialog"
	data-backdrop="static">
	<div class="modal-dialog">
		<!-- start Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Videos recomendados</h4>
			</div>
			<!-- This section (div id="modal-body") will be loaded dynamically -->
			<div id="loading" class="text-center" style="display:none;">
					<img style="width: 200px; height: 200px;" src="<?php echo explode('index.php', base_url())[0]?>assets/imgs/busy.gif" alt="Cargando" />
			</div>
			<div class="modal-body" id="modal-body"></div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
		<!-- Modal content-->
	</div>
</div>
<!-- end modal for confirm -->

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
	$('#record-recommendation').select2({
	  placeholder: {
		    id: '-1', // the value of the option
		    text: 'Seleccione'
		}
	});
	var table;
	$(document).ready(function() {
		table = $('#example').DataTable({
	    		"select": true,
		    	"language": {
				    "url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Spanish.json"
				},
			   "ajax": {
          			"url": "<?php echo base_url('api/rquestionary/list_completions_by_company_id');?>",
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
		            	"data": "name" 
		            },
		            { 
		            	"data": "position"
		        	},
		            { 
		            	"data": "company_id"
		        	},
		        	{
						"data": null,
						"className": "center",
		                "defaultContent": ''
				    },
		            { 	
		            	"data": "created_at",
		            },
		            {
		            	"data": null,
		                "className": "center",
		                "defaultContent": ''
		                	//'<i class="glyphicon glyphicon-zoom-in icon-action" data-action="showDetail" data-id="2" aria-hidden="true"></i>'
		            }
	            ],
	            "columnDefs" : [
	            	{ 	//icons action options
        				targets : [4],
          					render : function (data, type, row) {
              							var state = 'No revisado';
              							if(data.has_recommendation){
											state = 'Revisado';
                      					}
          						return state;
          				}
				    },
				    { 	//icons action options
        				targets : [6],
          					render : function (data, type, row) {
              							var icons = '<i class="glyphicon glyphicon-zoom-in icon-action"'
              										+' data-action="detail" data-questionary="#panel-'+data.questionary_id+'"' 
              										+' aria-hidden="true" title="Revisar"></i>&nbsp;&nbsp';
		          						var videoIcon = '<i class="glyphicon glyphicon-film icon-action"'
		          										+' data-action="showVideo" aria-hidden="true" title="Ver video"></i>';
              							if(data.has_recommendation){
											icons += videoIcon;
                      					}
          						return icons;
          				}
				    }
				]
		    });

	    	//Start load questionaries
	    	var url = "http://riesgopsicosocial.azurewebsites.net/index.php/api/rquestionary/initialdata";
			//Call to API
			$.get(url)
				.done(function(response) {
					questionaries = response;
			});
    		//End load quesrionaries

    		//Start load recommendations
			$.get("http://riesgopsicosocial.azurewebsites.net/index.php/api/rrecommendation/list_actives", {"company_id" : company_id})
			.done(function(data) {
				$.each(data.response, function(index, obj){
					$("#record-recommendation").append('<option value="'+obj.id+'">'+obj.title+'</option>');
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
				var questionaryType = row.find('td:eq(1)').text();
				var jobPosition = row.find('td:eq(2)').text();
				var code = row.find('td:eq(3)').text();
				var createdAt = row.find('td:eq(4)').text();

				switch (action) {
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

					case "detail":
						$("#record-id").val(id);
						$("#record-name").val(name);
						$("#title").text('Revisión de cuestionario ID: '+id);

						btnAction.text('Agregar recomendación');
						btnAction.attr("data-action", 'edit');
						btnAction.attr("class", "btn btn-warning");

						$("#loading").show();
						$.get( "<?php echo base_url('api/rquestionary/list_answers_by_id');?>", {"questionary_completion_id": id})
							.done(function(data) {
								$("#loading").hide();

								result = getQuestionary(questionaryType, questionaries, data.response);

								$("#questionary").html(result);

//								$("#modal-body").html(iframes);								
							})
							.fail(function() {
								//alert( "error" );
							})
							.always(function() {
								//alert( "finished" );
							});

						$('#form-modal').modal('show');
					break;

					case "showVideo":
						$("#loading").show();
						$("#modal-body").empty();
						$.get( "<?php echo base_url('api/rrecommendation/list_by_questionary_completion_id');?>", {"qc_id": id})
							.done(function(data) {
								$("#loading").hide();
								var iframes = '';
								$(data.response).each(function(i, element){
									console.log(JSON.stringify(element));
										iframes  +='<h4>'+element.title+'</h4>';//Title
										iframes += '<iframe class="video-iframe" src="'+ element.link +'" frameborder="0" allowfullscreen></iframe>';
										iframes  +='<h5>'+element.description+'</h5>'
								});
								$("#modal-body").html(iframes);
								
						  	})
						  	.fail(function() {
						    //alert( "error" );
						  	})
						  	.always(function() {
						    //alert( "finished" );
						  	});
						$('#video-modal').modal('show');
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
							"recommendations" :  $("#record-recommendation").val()
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
				var ajaxPost = true;
				var url = "";
				switch (action){

					case "create":
						url = "#";
					break;

					case "edit":
						url = "<?php echo base_url('api/rquestionary/add_recommendations');?>";
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