<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bitácora de proceso</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="<?php echo RESOURCE_JQUERY_MOBILE_CSS;?>">
	
	<script src="<?php echo RESOURCE_JQUERY_LIBRARY;?>"></script>
	<script src="<?php echo RESOURCE_JQUERY_MOBILE_LIBRARY;?>"></script>
	<script src="<?php echo RESOURCE_YOUTUBE_RESIZER_LIBRARY;?>"></script>
</head>

<body>

<div data-role="page">

    <div data-role="header">
        <h4>Bitácora</h4>
    </div>

    <div role="main" class="ui-content">

        <?php if(!empty($billboard)) {echo $billboard->content;}else{?>
            <strong><span style="color:darkred">No hay contenido disponible para mostrar</span></strong>
        <?php }?>

    </div>

    <div data-role="footer">
        <h4>SUSESO 2017</h4>
    </div><!-- /footer -->

</div><!-- /page -->

</body>
</html>