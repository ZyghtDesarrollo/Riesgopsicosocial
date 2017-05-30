<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container">
		<ul class="nav navbar-nav">
			<li id="companies"><a href="<?php echo base_url('companies'); ?>">Compañías</a></li>
			<li id="jobPositions"><a href="<?php echo base_url('job_positions'); ?>">Puestos de trabajo</a></li>
			<li id="randomUsers"><a href="<?php echo base_url('random_users'); ?>">Usuarios aleatorios</a></li>
			<li id="psychosocialTeam"><a href="<?php echo base_url('psychosocial_team'); ?>">Equipo psicosocial</a></li>
			<li id=""><a href="<?php echo base_url('results_analysis/get_results'); ?>">Análisis de Resultados</a></li>
			
			<li class="dropdown" style="display:none;">
	        	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Análisis de Resultados <span class="caret"></span></a>
	          	<ul class="dropdown-menu">
		            <li><a href="<?php echo base_url('results_analysis/questionary'); ?>">Ver cuestionarios</a></li>
		            <li><a href="results_analysis">Ver respuestas</a></li>
          		</ul>
        	</li>
			<li id="recomendations"><a href="<?php echo base_url('recomendations'); ?>">Recomendaciones</a></li>
		</ul>
		<span class="pull-right logout"><a href="javascript:logout();"
			style="text-decoration: none; cursor: pointer;">Cerrar Sesión</a></span>
	</div>
</nav>
