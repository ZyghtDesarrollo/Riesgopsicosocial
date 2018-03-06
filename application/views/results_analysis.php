<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
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
	.modal-body {
    	max-height: calc(100vh - 200px);
    	overflow-y: auto;
	}
	
	tables.table-result {
	    table-layout: fixed;
	}
	
	table.table-result th, table.table-result td{
		text-align: center;
		font-size: 12px;
	}
	
	table.table-result tfoot > tr{
		background-color: #cee1ff;
		font-weight: bold;
	}
</style>

<!-- start breadcrumb -->
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb">
			<li><a href="#">Home</a></li>
			<li class="active">Resultados por puestos de trabajo</li>
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
					<th>ID Puesto</th>
					<th>Puesto</th>
					<th># Encuestas</th>
					<th>Estado</th>
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


<!-- start modal for detail -->
<div class="modal fade" tabindex="-1"  id="detail-modal" role="dialog"
	data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<!-- start Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="title">default</h4>
			</div>
			<div class="modal-body">

				<div id="loading-detail" class="text-center" style="display:none;">
					<img style="width: 200px; height: 200px;" src="<?php echo explode('index.php', base_url())[0]?>assets/imgs/busy.gif" alt="Cargando" />
				</div>
				<div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
				<div id="box-detail" style="padding:0px;"></div>
			</div>
			<div class="panel-body">
				<form id="form-record">
						<div class="form-group">
							<label for="record-recommendation" class="form-control-label">Video sugerido</label>
							<select id="record-recommendation" 
								name="record-recommendation" multiple="multiple" class="form-control-label" style="width: 100%;">
								<option value="-1">Seleccione</option>
							</select>
						</div>
						<input type="hidden" id="record-job-position">
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" id="btn-action" class="btn btn-success"
					data-action="edit"
					data-loading-text="&lt;span&gt;&lt;i class='fa fa-refresh 
					fa-spin'&gt;&lt;/i&gt;&nbsp;&nbsp; Procesando...&lt;span&gt;">Agregar recomendación</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
		<!-- Modal content-->
	</div>
</div>
<!-- end modal for detail -->

<!-- start modal for recommendation-->
<div class="modal fade" tabindex="-1"  id="recommendation-modal" role="dialog"
	data-backdrop="static">
	<div class="modal-dialog modal-lg">
		<!-- start Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="rTitle">default</h4>
			</div>
			<div class="modal-body">

				<div id="loading" class="text-center" style="display:none;">
					<img style="width: 200px; height: 200px;" src="<?php echo explode('index.php', base_url())[0]?>assets/imgs/busy.gif" alt="Cargando" />
				</div>
				<div id="questionary" style="padding:0px;">
					
				</div>
				<form id="form-record">
					<div class="form-group">
						<label for="record-recommendation" class="form-control-label">Video sugerido</label>
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
					data-action="edit"
					data-loading-text="&lt;span&gt;&lt;i class='fa fa-refresh 
					fa-spin'&gt;&lt;/i&gt;&nbsp;&nbsp; Procesando...&lt;span&gt;">Agregar recomendación</button>
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
		<!-- Modal content-->
	</div>
</div>
<!-- end modal for recommendation -->

<!-- start modal for display video -->
<div class="modal fade" tabindex="-1" id="video-modal" role="dialog"
	data-backdrop="static">
	<div class="modal-dialog">
		<!-- start Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Videos recomendados - Puesto de Trabajo: <span id="vJobPosition"></span></h4>
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

<!-- start own script-->
<script>
	var table;
	$(document).ready(function() {
		$('#record-recommendation').select2({
			language: "es",
			placeholder: {
				    id: '-1', // the value of the option
				    text: 'Seleccione'
				}
		});
		
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
				    "url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Spanish.json"
				},
			   "ajax": {
          			"url": "<?php echo base_url('api/rquestionary/list_job_position_completions_by_company_id');?>",
          			"type": "GET",
          			"data" : {
              			"company_id" : company_id
              		}
       
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
	            		"data": "position_id" 
	            	},
	            	{ 	
	            		"data": "position" 
	            	},
		            { 	
		            	"data": "quantity" 
		            },
		            {
		            	"data": null,
		                "className": "center",
		                "defaultContent": ''
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
        				targets : [4],
          					render : function (data, type, row) {
              							var icons = '<i class="glyphicon glyphicon-zoom-in icon-action"'
              										+' data-action="detail" data-position_id="'+data.position_id+'" data-company-id="'+ company_id +'"'
              										+' aria-hidden="true" title="Detalle"></i>&nbsp;&nbsp';
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

    		//Start load recommendations
			$.get("http://riesgopsicosocial.azurewebsites.net/index.php/api/rrecommendation/list_actives_by_company_id", {"company_id" : company_id})
			.done(function(data) {
				var temp = '';
				$.each(data.response, function(index, obj){
					if(temp !== obj.question_category_id){
						temp = obj.question_category_id;
						if(index > 0)
							$("#record-recommendation").append('</optgroup>');
						$("#record-recommendation").append('<optgroup label="'+ obj.question_category_title +'">');
					}
					$("#record-recommendation").append('<option value="'+obj.id+'">'+obj.title+'</option>');
				});
				$("#record-recommendation").append('</optgroup>');
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
				var jobPosition = row.find('td:eq(1)').text();

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
						$("#record-job-position").val(id);
						$("#title").text('Resultados por puesto de trabajo: '+jobPosition);
						$("#container").empty();
						$("#box-detail").empty();
						$("#loading-detail").show();

						//Start load selected recommendation
						$("#record-recommendation").val('').trigger('change');
						$.get( "<?php echo base_url('api/rrecommendation/list_by_job_position_id');?>", {"job_position_id": id})
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
						//End load selected recommendation

						$.get( "<?php echo base_url('api/rquestionary/list_category_results_by_job_position_id');?>", {"job_position_id": id, company_id : user.id})
							.done(function(data) {
								$("#loading-detail").hide();
								//var for highchart
								var categories = [];
								var serie_high = [];
								var serie_medium = [];
								var serie_low = [];	
								var cont = 1;
								var content = '<div class="table-responsive">';
								content += '<table class="table table-striped table-hover table-result">'
											+'<thead>'
												+'<tr><th>Nº</th>';
								
								$(data.response.head).each(function(i, e){
									content += '<th>'+e.title+'</th><th>Nivel de Riesgo</th>';
									categories.push(e.title);
								});

								content += '<tr>'
											+'</thead>'
											+'<tbody>';

									  var obj = data.response.rows;
									  for (var key in obj){
										  content += '<tr><td>'+ cont++ +'</td>';
									    var value = obj[key];
									    for(var t = 0 ; t < value.length; t++){
									    	content +='<td>'+value[t]+'</td>';
									    }
									    content += '</tr>';
									  }

									 content +='<tfoot><tr>'
										 		+'<td colspan="2"><strong>Riesgo Alto<br>Riesgo Medio<br>Riesgo Bajo</strong></td>';
									 
									var percent = data.response.percent;
									var flag = false;
									for (var key in percent){
										var val = percent[key];
										if(flag){
											content += '<td></td>';
										}else{
											flag = true;
										}
									    content +='<td><strong>'+val.risk_high+'<br>'+val.risk_medium+'<br>'+val.risk_low+'</strong></td>';
									    serie_high.push(val.risk_high);
										serie_medium.push(val.risk_medium);
										serie_low.push(val.risk_low);
									 }
									content += '</tr>';
									content +='</tr></tfoot></tbody>';
								+'</table>'
							+'</div>';
								render_chart(cont-1, categories, serie_high, serie_medium, serie_low)
								$("#box-detail").html(content);
							})
							.fail(function() {
								//alert( "error" );
							})
							.always(function() {
								//alert( "finished" );
							});

						$('#detail-modal').modal('show');
					break;
					
					case "recommendation":
						$("#rTitle").text('Videos sugeridos por puesto de trabajo: '+jobPosition);
						$('#recommendation-modal').modal('show');
					break;

					case "showVideo":
						$("#vJobPosition").text(jobPosition);
						$("#loading-video").show();
						$("#modal-body").empty();
						$.get( "<?php echo base_url('api/rrecommendation/list_by_job_position_id');?>", {"job_position_id": id})
							.done(function(data) {
								$("#loading-video").hide();
								var iframes = '';
								var temp = '';
								$(data.response).each(function(i, element){
									if(temp !== element.question_category_id){
										iframes  +='<div class="alert alert-info"><span style="font-size:18px;">'+element.question_category_title+'</span></div>';//Category
										temp = element.question_category_id;
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

			$('#video-modal').on('hidden.bs.modal', function () {
				$("#modal-body").empty();
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
							"recommendations_id" :  $("#record-recommendation").val()
						};
				if(action == "edit"){
					btnAction.button("loading");
					params.job_position_id = $("#record-job-position").val();
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
						url = "<?php echo base_url('api/rrecommendation/associate_by_job_position');?>";
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
					  btnAction.button("reset");
				    //alert( "finished" );
				  });
			}	

			function render_chart(workers, categories, serie_high, serie_medium, serie_low){
				Highcharts.chart('container', {
				    chart: {
				        type: 'column'
				    },
				    colors: ['#D9534F', '#F0AD4E', '#5CB85C'],
				    title: {
				        text: 'Gráfico de las prevalencias (porcentajes) de trabajadores en cada nivel de riesgo'
					        +' en una unidad de '+ workers +' trabajadores'
				    },
				    xAxis: {
				        categories: categories//['Exigencias psicológicas', 'Trabajo activo y desarrollo de habilidades', 'Apoyo social en la empresa', 'Compensaciones', 'Doble presencia']
				    },
				    yAxis: {
				        min: 0,
						max: 100,
						tickInterval: 10,
				        title: {
				            text: 'Porcentajes'
				        }
				    },
				    tooltip: {
				        pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.0f}%)<br/>',
				        shared: true
				    },
				    plotOptions: {
				        column: {
				            stacking: 'percent'
				        }
				    },
				    series: [{
				        name: 'Alto',
				        data: serie_high //[5, 3, 4, 7, 2]
				    }, {
				        name: 'Medio',
				        data: serie_medium //[2, 2, 3, 2, 1]
				    }, {
				        name: 'Bajo',
				        data: serie_low//[3, 4, 4, 2, 5]
				    }],
				    credits:{
						enabled: false
						}
				});
			}
		</script>
<!-- end own script -->