<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<?php if(empty($printToPdf)): ?>
<!-- start breadcrumb -->
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb">
			<li><a href="#">Home</a></li>
			<li class="active">Reporte de Evaluación Psicosocial</li>
		</ol>
	</div>
</div>
<div class="row">
	<div class="col-sm-10">
		&nbsp;
	</div>
	<div class="col-sm-2">
		<a href="<?php echo base_url('results_analysis/get_pdf_report'); ?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Exportar a PDF</a>
	</div>
</div>
<!-- end breadcrumb -->
<?php endif; ?>

<div class="row">
	<div class="col-sm-12">
		<h3>Identificación del Centro de Trabajo</h3>
		<table class="table table-bordered" cellspacing="0" width="100%">
			<tbody>
				<tr>
					<th class="col-xs-8">Nombre</th>
                    <td class="col-xs-4"><?php echo $description["company_name"];?></td>
				</tr>
				<tr>
					<th class="col-xs-8">RUT</th>
                    <td class="col-xs-4"><?php echo $description["company_rut"];?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-sm-12">
		<h3>Descripción de la Muestra y el Instrumento</h3>
		<table class="table table-bordered" cellspacing="0" width="100%">
			<tbody>
				<tr>
					<th class="col-xs-8">Instrumento</th>
                    <td class="col-xs-4"><?php echo $description["evaluation_instrument"];?></td>
				</tr>
				<tr>
					<th class="col-xs-8">Método de Aplicación</th>
                    <td class="col-xs-4"><?php echo $description["evaluation_methodology"];?></td>
				</tr>
				<tr>
					<th class="col-xs-8">Fecha de Inicio de la Encuesta</th>
                    <td class="col-xs-4"><?php echo date('d/m/Y', strtotime($description["evaluation_start_date"]));?></td>
				</tr>
				<tr>
					<th class="col-xs-8">Fecha de Fin de la Encuesta</th>
                    <td class="col-xs-4"><?php echo date('d/m/Y', strtotime($description["evaluation_end_date"]));?></td>
				</tr>
				<tr>
					<th class="col-xs-8">Total de Trabajadores en el Centro de Trabajo</th>
                    <td class="col-xs-4"><?php echo $description["evaluation_total_workers"];?></td>
					<?php $total_workers = empty($description["evaluation_total_workers"]) ? 1 : $description["evaluation_total_workers"]; ?>
				</tr>
				<tr>
					<th class="col-xs-8">Total de Trabajadores que contestaron la Encuesta</th>
                    <td class="col-xs-4"><?php echo $description["evaluation_total_answers"];?> (<?php echo round(100*$description["evaluation_total_answers"]/$total_workers , 2);?>%)</td>
					<?php $total_answers = empty($description["evaluation_total_answers"]) ? 1 : $description["evaluation_total_answers"]; ?>
				</tr>
			</tbody>
		</table>
		<?php if(!empty($description["evaluation_total_answers_by_sex"])): ?>
		<h4>Trabajadores por Sexo</h4>
		<table class="table table-striped table-bordered" cellspacing="0" width="100%">
			<tbody>
				<?php foreach($description["evaluation_total_answers_by_sex"] as $key => $value) :?>
				<tr>
                    <td class="col-xs-8"><?php echo $key;?></td>
                    <td class="col-xs-4"><?php echo $value;?> (<?php echo round(100*$value/$total_answers, 2);?>%)</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
		<?php if(!empty($description["evaluation_total_answers_by_age"])): ?>
		<h4>Trabajadores por Edad</h4>
		<table class="table table-striped table-bordered" cellspacing="0" width="100%">
			<tbody>
				<?php foreach($description["evaluation_total_answers_by_age"] as $key => $value) :?>
				<tr>
                    <td class="col-xs-8"><?php echo $key;?></td>
                    <td class="col-xs-4"><?php echo $value;?> (<?php echo round(100*$value/$total_answers, 2);?>%)</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
		<?php if(!empty($description["evaluation_total_answers_by_job_position"])): ?>
		<h4>Trabajadores por Puesto de Trabajo</h4>
		<table class="table table-striped table-bordered" cellspacing="0" width="100%">
			<tbody>
				<?php foreach($description["evaluation_total_answers_by_job_position"] as $key => $value) :?>
				<tr>
                    <td class="col-xs-8"><?php echo $key;?></td>
                    <td class="col-xs-4"><?php echo $value;?> (<?php echo round(100*$value/$total_answers, 2);?>%)</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
	</div>
	<div class="col-sm-12">
		<h3>Resultados Globales</h3>
		<table class="table table-bordered" cellspacing="0" width="100%">
			<tbody>
				<tr>
					<th class="col-xs-4">Puntaje Total de Riesgo</th>
                    <td class="col-xs-8"><?php echo $global["total"]["risk_score"];?></td>
				</tr>
				<tr>
					<th class="col-xs-4">Calificación Total de Riesgo</th>
                    <td class="col-xs-8"><?php echo $global["total"]["risk_label"];?></td>
				</tr>
				<tr>
					<th class="col-xs-4">Fecha de Próxima Evaluación </th>
                    <td class="col-xs-8"><?php echo $global["update_rule"];?></td>
				</tr>
			</tbody>
		</table>
		<?php if(!empty($global["sex"])): ?>
		<h4>Comparación de Puntaje por Sexo</h4>
		<table class="table table-striped table-bordered" cellspacing="0" width="100%">
			<tbody>
				<?php foreach($global["sex"] as $key => $value) :?>
				<tr>
                    <td class="col-xs-4"><?php echo $key;?></td>
                    <td class="col-xs-8"><?php echo (($value["risk_score"]>0) ? "+" : ""). $value["risk_score"] ." (". $value["risk_label"].")";?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
		<?php if(!empty($global["age"])): ?>
		<h4>Comparación de Puntaje por Edad</h4>
		<table class="table table-striped table-bordered" cellspacing="0" width="100%">
			<tbody>
				<?php foreach($global["age"] as $key => $value) :?>
				<tr>
                    <td class="col-xs-4"><?php echo $key;?></td>
                    <td class="col-xs-8"><?php echo (($value["risk_score"]>0) ? "+" : ""). $value["risk_score"] ." (". $value["risk_label"].")";?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
	</div>
	<div class="col-sm-12">
		<?php if(!empty($job_position)): ?>
		<h3>Resultados por Puesto de Trabajo</h3>
		<?php foreach($job_position as $job_position_value => $job_position_array) :?>
		<h4>Puesto de Trabajo: <?php echo $job_position_value;?></h4>
		<table class="table table-bordered" cellspacing="0" width="100%">
			<tbody>
				<tr>
					<th class="col-xs-4">Puntaje de Riesgo</th>
                    <td class="col-xs-8"><?php echo $job_position_array["total"]["risk_score"];?></td>
				</tr>
				<tr>
					<th class="col-xs-4">Clasificación de Riesgo</th>
                    <td class="col-xs-8"><?php echo $job_position_array["total"]["risk_label"];?></td>
				</tr>
			</tbody>
		</table>
		<?php if(!empty($job_position_array["sex"])): ?>
		<h5>Comparación de Puntaje por Sexo</h5>
		<table class="table table-striped table-bordered" cellspacing="0" width="100%">
			<tbody>
				<?php foreach($job_position_array["sex"] as $key => $value) :?>
				<tr>
                    <td class="col-xs-4"><?php echo $key;?></td>
                    <td class="col-xs-8"><?php echo (($value["risk_score"]>0) ? "+" : ""). $value["risk_score"] ." (". $value["risk_label"].")";?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
		<?php if(!empty($job_position_array["age"])): ?>
		<h5>Comparación de Puntaje por Edad</h5>
		<table class="table table-striped table-bordered" cellspacing="0" width="100%">
			<tbody>
				<?php foreach($job_position_array["age"] as $key => $value) :?>
				<tr>
                    <td class="col-xs-4"><?php echo $key;?></td>
                    <td class="col-xs-8"><?php echo (($value["risk_score"]>0) ? "+" : ""). $value["risk_score"] ." (". $value["risk_label"].")";?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>
	<div class="col-sm-12">
		<?php if(!empty($dimension)): ?>
		<h3>Resultados por Dimensión</h3>
		<?php foreach($dimension as $dimension_value => $dimension_array) :?>
		<h4>Dimensión: <?php echo $dimension_value;?></h4>
		<table class="table table-bordered" cellspacing="0" width="100%">
			<tbody>
				<tr>
					<th class="col-xs-4">Puntaje de Riesgo</th>
                    <td class="col-xs-8"><?php echo $dimension_array["total"]["risk_score"];?></td>
				</tr>
				<tr>
					<th class="col-xs-4">Clasificación de Riesgo</th>
                    <td class="col-xs-8"><?php echo $dimension_array["total"]["risk_label"];?></td>
				</tr>
			</tbody>
		</table>
		<?php if(!empty($dimension_array["sex"])): ?>
		<h5>Comparación de Puntaje por Sexo</h5>
		<table class="table table-striped table-bordered" cellspacing="0" width="100%">
			<tbody>
				<?php foreach($dimension_array["sex"] as $key => $value) :?>
				<tr>
                    <td class="col-xs-4"><?php echo $key;?></td>
                    <td class="col-xs-8"><?php echo (($value["risk_score"]>0) ? "+" : ""). $value["risk_score"] ." (". $value["risk_label"].")";?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
		<?php if(!empty($dimension_array["age"])): ?>
		<h5>Comparación de Puntaje por Edad</h5>
		<table class="table table-striped table-bordered" cellspacing="0" width="100%">
			<tbody>
				<?php foreach($dimension_array["age"] as $key => $value) :?>
				<tr>
                    <td class="col-xs-4"><?php echo $key;?></td>
                    <td class="col-xs-8"><?php echo (($value["risk_score"]>0) ? "+" : ""). $value["risk_score"] ." (". $value["risk_label"].")";?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>

