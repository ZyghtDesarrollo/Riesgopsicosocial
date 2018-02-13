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
			<li class="active">Resultado global</li>
		</ol>
	</div>
</div>
<!-- end breadcrumb -->

<div class="row">
    <div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">Resultado total de la compañía</div>
        <div class="panel-body">
            <div id="loading-detail" class="text-center" style="display:none;">
                <img style="width: 200px; height: 200px;" src="<?php echo explode('index.php', base_url())[0]?>assets/imgs/busy.gif" alt="Cargando" />
            </div>
            <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
            <div id="box-detail" style="padding:0px;"></div>
        </div>
        <div class="panel-footer"></div>
    </div>
    </div>
</div>

<!-- start own script-->
<script>
	$(document).ready(function() {
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


            loadChart();
		} );

    function loadChart(){
        $("#container").empty();
        $("#box-detail").empty();
        $("#loading-detail").show();


        $.get( "<?php echo base_url('api/rquestionary/list_category_results_by_job_position_id');?>", { job_position_id: -1, company_id : user.id})
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