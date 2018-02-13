<script>
	var questionaries = null;

	function findAnswer(id, answers) {
		response = false;

		$.each(answers, function(i, obj){
			if (id == obj.question_option_id) {
				response = true;
				return false;
			}
		});

		return response;	
	}

	function getQuestionary(questionaryType, questionaries, answers) {
		questionary = (questionaryType == "Cuestionario Breve") ? questionaries[0] : questionaries[1];

		var panel = '';
		var finalBox = '';
		var item = 0;
		var factor = 0;
		var total_ponderation = 0;
		var points = 0;
		var cont = 0;

		panel += '<div id="panel-'+ questionary.id +'" class="questionary-panel panel panel-default">'
	  		  	+'<div class="panel-body">';

		panel += '<h2 class="text-center">'+ questionary.name +'</h2>';

		$.each(questionary.categories, function(i, obj_category){
			factor = 4;
			item = 0;
			total_ponderation = 0;
			
			panel += '<div class="alert alert-info category">'+obj_category.title+'</div>';
			
			$.each(obj_category.questions, function(y, obj_question){
				item++;
				panel += '<ol start='+(y+1)+'>' + '<li class="question">'+obj_question.title+'</li>' + '</ol>'
					+ '<div class="answere-box">';
					//panel += '<ul><li class="open-answer"></li></ul>';
					
					panel +='<ol type="a">';
					
					if(!obj_question.hasOwnProperty('options') && answers[cont] != undefined){
						panel += '<li>' + answers[cont].open_answer + '</li>';
					}else{
						$.each(obj_question.options, function(i, obj_options){
							//console.log(JSON.stringify(obj_options));
		
							if (findAnswer(obj_options.id, answers) == false) {
								panel += '<li>' + obj_options.title + '</li>';
							} else {
								total_ponderation += obj_options.ponderation;
								panel += '<li class="selected-answere">' + obj_options.title + ' <i class="glyphicon glyphicon-ok"></i></li>';
							}
						});
					}
				cont++;

				panel += '</ol>'
			 		+ '<hr>'
					+ '</div>';
			});

			factor *= item;
			points = (total_ponderation / factor)*100;
			finalBox += '<div class="panel panel-default">';
			finalBox +='<div class="panel-heading"><strong>'+obj_category.title+'</strong></div>';
			finalBox += '<div class="panel-body">';
			finalBox += '<div class="alert alert-info">Puntaje '+obj_category.title+': <strong>('+total_ponderation+'/ '+factor+')*100 = '
			+points.toFixed(2)+'</strong></div>';
			finalBox +='</div></div>';
		});

		panel += '</div>'
			+'</div>';	

		return panel+finalBox;
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
	
	.modal-body{
		height: 500px;
		overflow-y: auto;
	}
	
	.open-answer{
		list-style-type: none; 
		text-align: justify;
	}
	
	.my-select{
		width: 100%;
	}
	
	.my-label{
    	font-weight: normal !important;
	}
	
	.category{
		font-size: 20px;
	}
	
	.question{
		font-size: 18px;
	}
</style>

<!-- start breadcrumb -->
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb">
			<li><a href="#">Home</a></li>
			<li class="active">Análisis de resultados</li>
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
					<th>Puesto</th>
					<th># Encuestas</th>
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
<div class="modal fade" tabindex="-1"  id="form-modal" role="dialog"
	data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<!-- start Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="title">default</h4>
			</div>
			<div class="modal-body">

				<div id="loading" class="text-center" style="display:none;">
					<img style="width: 200px; height: 200px;" src="<?php echo explode('index.php', base_url())[0]?>assets/imgs/busy.gif" alt="Cargando" />
				</div>

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

<!-- start modal for display video -->
<div class="modal fade" tabindex="-1" id="video-modal" role="dialog"
	data-backdrop="static">
	<div class="modal-dialog">
		<!-- start Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Videos recomendados - Cuestionario ID: <span id="vQuestionaryId"></span></h4>
			</div>
			<!-- This section (div id="modal-body") will be loaded dynamically -->
			<div id="loading-video" class="text-center" style="display:none;">
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
<!-- end modal for display video -->

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


// 		$('#form-modal').on('shown', function() {
// 			alert("hola");
// 			$('.my-select').select2({
// 				language: "es",
// 				 placeholder: {
// 					    id: '-1', // the value of the option
// 					    text: 'Seleccione2'
// 					}
// 			});
// 		});

		$('#record-recommendation').select2({
			language: "es",
			placeholder: {
				    id: '-1', // the value of the option
				    text: 'Seleccione'
				}
		});
		
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
		            	"data": "name" 
		            },
		            { 
		            	"data": "position"
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
        				targets : [3],
          					render : function (data, type, row) {
              							var state = 'No revisado';
              							if(data.has_recommendation){
											state = 'Revisado';
                      					}
          						return state;
          				}
				    },
				    { 	//icons action options
        				targets : [5],
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
				var temp = '';
				$.each(data.response, function(index, obj){
					if(temp !== obj.question_category_title){
						temp = obj.question_category_title;
						if(index > 0)
							$("#record-recommendation").append('</optgroup>');
						$("#record-recommendation").append('<optgroup label="'+ obj.question_category_title +'">');
					}
					$("#record-recommendation").append('<option value="'+obj.id+'">'+obj.title+'</option>');
					if(index === data.response.length - 1)
						$("#record-recommendation").append('</optgroup>');
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
						
						$("#questionary").empty();
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

						$("#record-recommendation").val('').trigger('change');
						$.get( "<?php echo base_url('api/rrecommendation/list_by_questionary_completion_id');?>", {"qc_id": id})
						.done(function(data) {
							var selected = [];
							$(data.response).each(function(i, element){
								selected.push(element.id);
							});
							$("#record-recommendation").val(selected).trigger('change');
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
						$("#vQuestionaryId").text(id);
						$("#loading-video").show();
						$("#modal-body").empty();
						$.get( "<?php echo base_url('api/rrecommendation/list_by_questionary_completion_id');?>", {"qc_id": id})
							.done(function(data) {
								$("#loading-video").hide();
								var iframes = '';
								var temp = '';
								$(data.response).each(function(i, element){
									if(temp !== element.qc_title){
										iframes  +='<div class="alert alert-info"><span style="font-size:18px;">'+element.qc_title+'</span></div>';//Category
										temp = element.qc_title;
									}
										iframes  +='<h4>'+element.title+'</h4>';//Title
										iframes += '<iframe class="video-iframe" src="'+ element.link +'" frameborder="0" allowfullscreen></iframe>';
										iframes  +='<h5>'+element.description+'</h5><hr>';
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