<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_FONTS_CSS;?>">
<!-- start breadcrumb -->
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb">
			<li><a href="#">Home</a></li>
			<li class="active">Reporte de Visualizaci&oacute;n de Videos</li>
		</ol>
	</div>
</div>
<!-- end breadcrumb -->

<div class="row">
    <div class="col-sm-12">
        <table id="recommendation_log_summary" class="table table-striped table-bordered"
               cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>Video</th>
                <th>Usuarios Aleatorios</th>
                <th>Total de Visualizaciones</th>
            </tr>
            </thead>
        </table>
    </div>
</div>


<!-- start own script-->
<script>
    var recommendation_summary_table;
	$(document).ready(function() {
        recommendation_summary_table = $('#recommendation_log_summary').DataTable({
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
				"url": "<?php echo base_url('api/rrecommendation/list_recommendation_views_summary_by_company_id');?>",
                "type": "GET",
                "data" : {
                    "company_id" : company_id,
                }

            },
            "initComplete": function( settings, json ) {
                recommendation_summary_table.buttons().container()
                    .appendTo( $('#recommendation_log_summary_wrapper .col-sm-6:eq(0)'));
                $("#activity_log_filter").append("&nbsp;&nbsp;<button id='refresh' "
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
                    "data": "recommendation_name"
                },
                {
                    "data": "unique_random_users"
                },
                {
                    "data": "amount_of_views",
                }
            ]
        });
		} );
		</script>
<!-- end own script -->