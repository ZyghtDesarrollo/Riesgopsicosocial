<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bitácora de proceso</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css" />
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
    <script src="https://cdn.rawgit.com/skipser/youtube-autoresize/master/youtube-autoresizer.js"></script>
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