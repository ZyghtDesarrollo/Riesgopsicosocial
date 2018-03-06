<nav class="navbar navbar-default navbar-fixed-top">
    <div class="pull-right">
        <div class="col-sm-12"><span><strong>C&oacute;digo de compa&ntilde;&iacute;a: </strong></span>
            <script>if(user.code){ document.write(user.code)}else{document.write('SA')};</script>&nbsp;&nbsp;
			<span class="logout"><a href="javascript:logout();" style="text-decoration: none; cursor: pointer;">Cerrar Sesi&oacute;n</a></span>
        </div>
    </div>
	<div class="container">
		<ul class="nav navbar-nav hide-item">
			<li id="companies"><a href="<?php echo base_url('companies'); ?>">Compa&ntilde;&iacute;as</a></li>
			<li id="jobPositions"><a href="<?php echo base_url('job_positions'); ?>">Puestos de Trabajo</a></li>
			<li id="randomUsers"><a href="<?php echo base_url('random_users'); ?>">Usuarios Aleatorios</a></li>
			<li id="psychosocialTeam"><a href="<?php echo base_url('psychosocial_team'); ?>">Comit&eacute; de Aplicaci&oacute;n</a></li>

			<li class="dropdown" style="display:none;" id="resultsAnalysis">
	        	<a href="#" id="resultsAnalysisToggle" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">An&aacute;lisis de Resultados <span class="caret"></span></a>
	          	<ul class="dropdown-menu" aria-labelledby="resultsAnalysisToggle">
                    <li><a href="<?php echo base_url('results_analysis/get_global_result'); ?>">Resultado Global</a></li>
                    <li><a href="<?php echo base_url('results_analysis/get_results'); ?>">Resultados por Puestos de Trabajo</a></li>
		            <li><a href="<?php echo base_url('results_analysis/questionary'); ?>">Ver Cuestionarios</a></li>
		            <li><a href="results_analysis">Revisar Cuestionarios</a></li>
          		</ul>
        	</li>

            <li class="dropdown" style="display:none;" id="activityLog">
                <a href="#" id="activityLogToggle" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Registro de Actividades<span class="caret"></span></a>
                <ul class="dropdown-menu" aria-labelledby="activityLogToggle">
                    <li><a href="<?php echo base_url('activity_log'); ?>">Historial de Actividades</a></li>
                    <li><a href="<?php echo base_url('billboard'); ?>">Bit&aacute;cora de Proceso</a></li>
                    <li><a href="<?php echo base_url('activity_log/activity_log_summary'); ?>">Reporte de Cuestionarios</a></li>
                    <li><a href="<?php echo base_url('activity_log/recommendation_log_summary'); ?>">Reporte de Visualizaci&oacute;n de Videos</a></li>
                </ul>
            </li>

            <li id="recomendations"><a href="<?php echo base_url('recomendations'); ?>">Videos</a></li>
            <li id="notification"><a href="<?php echo base_url('notification'); ?>">Notificaciones</a></li>
            <li id="billboard"><a href="<?php echo base_url('billboard'); ?>">Bit&aacute;cora de Proceso</a></li>
		</ul>
	</div>
</nav>
