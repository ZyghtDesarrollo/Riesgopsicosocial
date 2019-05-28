<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_FONTS_CSS;?>">
<script src="<?php echo RESOURCE_GRAPHICS_GENERATION;?>"></script>
<script src="<?php echo RESOURCE_GRAPHICS_EXPORT;?>"></script>
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
			<li class="active">Resultado global</li>
		</ol>
	</div>
</div>
<!-- end breadcrumb -->

<div class="row">
    <div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">Resultado total del Centro de Trabajo</div>
        <div class="panel-body">
            <div id="loading-detail" class="text-center" style="display:none;">
                <img style="width: 200px; height: 200px;" src="<?php echo explode('index.php', base_url())[0]?>assets/imgs/busy.gif" alt="Cargando" />
            </div>
            <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div><div class="row">
			<div class="col-sm-12">
				<table id="box-detail-table" class="table table-striped table-bordered"
					cellspacing="0" width="100%">
					<tr>
						<th>Nº</th>
					</tr>
				</table>
			</div>
        </div>
        <div class="panel-footer"></div>
    </div>
    </div>
</div>

<!-- start own script-->
<script>
	var exportable_results;
	var exportable_results_columns = [];
	var exportable_results_column_defs = [];
	var exportable_results_data_object = [];
	function renderRiskData(data, type, row) {
		if(data == 'B') return -1;
		if(data == 'A') return 1;
		return 0;
	}
	
	$(document).ready(function() {
    		//Start load recommendations
			$.get("<?php echo base_url('api/rrecommendation/list_actives_by_company_id');?>", {"company_id" : company_id})
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


            loadChart();
		} );

    function loadChart(){
        $("#container").empty();
        $("#box-detail-table").empty();
        $("#loading-detail").show();


        $.get( "<?php echo base_url('api/rquestionary/list_category_results_by_job_position_id');?>", { job_position_id: -1, company_id : user.id})
            .done(function(data) {
                $("#loading-detail").hide();
                //var for highchart
                var categories = [];
                var serie_high = [];
                var serie_medium = [];
                var serie_low = [];
				var header_column = {title: "Nº", data:0};
				exportable_results_columns.push(header_column);
                $(data.response.head).each(function(i, e){
                    categories.push(e.title);
					var category_column = {title: e.title, data:e.categoryData}
					exportable_results_columns.push(category_column);
					var risk_column = {title: 'Nivel de Riesgo', data:e.riskData}
					exportable_results_columns.push(risk_column);
					var risk_column_def = {targets: [e.riskData], render: renderRiskData}
					exportable_results_column_defs.push(risk_column_def);
                });
				
                var cont = 1;
                var obj = data.response.rows;
                for (var key in obj){
					var data_row = [];
					data_row[0] = (cont<10) ? '0'+cont : cont;
                    var value = obj[key];
                    for(var t = 0 ; t < value.length; t++){
                        data_row[t+1] = value[t];
                    }
					cont++;
					exportable_results_data_object.push(data_row);
                }
				
                var percent = data.response.percent;
				var total_risk_high = [];
				total_risk_high[0] = "Riesgo Alto";
				var total_risk_medium = [];
				total_risk_medium[0] = "Riesgo Medio";
				var total_risk_low = [];
				total_risk_low[0] = "Riesgo Bajo";
				var current_key = 1;
                for (var key in percent){
                    var val = percent[key];
					total_risk_high[current_key] = +val.risk_high.toFixed(2);
					total_risk_medium[current_key] = +val.risk_medium.toFixed(2);
					total_risk_low[current_key] = +val.risk_low.toFixed(2);
					total_risk_high[current_key+1] = '';
					total_risk_medium[current_key+1] = '';
					total_risk_low[current_key+1] = '';
					current_key+=2;
                    serie_high.push(parseFloat(val.risk_high.toFixed(2)));
                    serie_medium.push(parseFloat(val.risk_medium.toFixed(2)));
                    serie_low.push(parseFloat(val.risk_low.toFixed(2)));
                }
				exportable_results_data_object.push(total_risk_high);
				exportable_results_data_object.push(total_risk_medium);
				exportable_results_data_object.push(total_risk_low);
				
				exportable_results = $('#box-detail-table').DataTable({
					buttons: [{
						extend: 'excel',
						exportOptions: {
						columns: ':visible'
						},
						text:      '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
						titleAttr: 'Excel'
					}],
					"dom": '<"row"<"div-filtros"><"col-lg-4"l><"col-lg-4"p><"col-lg-4"B>>tir',
					"paging": false,
					"select": false,
					"searching": false,
					'responsive' : true,
					"language": {
					"url": "<?php echo RESOURCE_DATATABLE_LANGUAGE; ?>"
					},
					"initComplete": function( settings, json ) {
						exportable_results.buttons().container()
					},
					"showRefresh": false,
					"columns": exportable_results_columns,
					"data": exportable_results_data_object,
					"columnDefs" : exportable_results_column_defs
					});
				  
                render_chart(cont-1, categories, serie_high, serie_medium, serie_low);
            })
            .fail(function() {
                //alert( "error" );
            })
            .always(function() {
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
				        },						
						series: {
							dataLabels: {
								enabled: true
							}
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