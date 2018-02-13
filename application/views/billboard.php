<link href="<?php echo config_item('assets_path');?>/css/font-awesome/font-awesome.min.css" rel="stylesheet">
<link href="<?php echo config_item('assets_path');?>/css/animate.css" rel="stylesheet" />
<link href="<?php echo config_item('assets_path');?>/css/summernote/summernote.css" rel="stylesheet" />
<link href="<?php echo config_item('assets_path');?>/css/summernote/summernote-bs3.css" rel="stylesheet" />

<!-- start breadcrumb -->
<div class="row">
    <div class="col-sm-12">
        <ol class="breadcrumb">
            <li><a href="#">Home</a></li>
            <li class="active">Bitácora de proceso</li>
        </ol>
    </div>
</div>
<!-- end breadcrumb -->

<div class="wrapper wrapper-content">

    <div class="row">
        <div class="col-lg-12">
            <div class="ajaxReplayMessage"></div>
            <div class="ibox float-e-margins">
                <div class="ibox-content no-padding">

                    <div class="summernote">
                        <h3>Cronograma de jornada</h3>
                        dummy text of the printing and typesetting industry. <strong>Lorem Ipsum has been the industry's</strong> standard dummy text ever since the 1500s,
                        when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic
                        typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with
                        <br/>
                        <br/>
                        <ul>
                            <li>Remaining essentially unchanged</li>
                            <li>Make a type specimen book</li>
                            <li>Unknown printer</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 pull-right">
            <button class="btn btn-default center-block" id="btnPublish" data-flag="false" name="btnPublish" data-loading-text="&lt;span&gt;
            &nbsp;&nbsp;Procesando...&lt;span&gt;">Publicar</button>
        </div>
        <div class="col-md-6 pull-left">
            <button class="btn btn-success center-block" id="btnSave" name="btnSave" data-loading-text="&lt;span&gt;
            &nbsp;&nbsp;Enviando...&lt;span&gt;">Guardar</button>
        </div>
    </div>
</div>

<script src="<?php echo config_item('assets_path');?>/js/metisMenu/jquery.metisMenu.js"></script>
<script src="<?php echo config_item('assets_path');?>/js/slimscroll/jquery.slimscroll.min.js"></script>
<script src="<?php echo config_item('assets_path');?>/js/summernote/summernote.min.js"></script>

<!-- start own script-->
<script>
    var billboard_id = 0;

    $(document).ready(function(){
        $('.summernote').summernote({
            //height: 300,                 // set editor height
            minHeight: 300,             // set minimum height of editor
            maxHeight: null,             // set maximum height of editor
            focus: true                  // set focus to editable area after initializing summernote
        });

        var company_id = 0;

        if(user.id){
            company_id = user.id;
        }

        <?php
        if(!empty($billboard)) {
            echo 'billboard_id ='.$billboard->id.';';
            $output = str_replace(array("\r\n", "\r"), "\n", $billboard->content);
            $lines = explode("\n", $output);
            $new_lines = array();

            foreach ($lines as $i => $line) {
                if(!empty($line))
                    $new_lines[] = trim($line);
            }

            echo  "$('.summernote').summernote('code', '".addslashes(implode($new_lines))."');";
            if($billboard->published){
                echo "$('#btnPublish').html('Ocultar');";
            }else{
                echo "$('#btnPublish').html('Publicar');";
                echo "$('#btnPublish').attr('data-flag', 'true')";
            }

        }
        ?>

    });

    $('#btnSave').click(function(){
        //$(".summernote").summernote("code", "your text");
        var btn = $(this);
        btn.button("loading");
        var params = {content : $('.summernote').summernote('code'), company_id : company_id};
        $.post( 'billboard/save', params )
            .done(function(resp) {
                showAlert(resp.type, resp.message);
            })
            .fail(function(resp) {
                showAlert(resp.type, resp.message);
            })
            .always(function() {
                btn.button('reset');
            });
    });

    $('#btnPublish').click(function(){
        var btn = $(this);
        btn.button("loading");
        var params = {billboard_id : billboard_id, publish : btn.attr('data-flag')};
        $.post( 'billboard/publish', params )
            .done(function(resp) {
                showAlert(resp.type, resp.message);
                btn.button('reset');
                setTimeout(function(){
                    if(resp.data.published ){
                        btn.text("Ocultar");
                        btn.attr('data-flag', "false");
                    }else{
                        btn.text("Publicar");
                        btn.attr('data-flag', "true");
                    }
                }, 1000);
            })
            .fail(function(resp) {
                showAlert(resp.type, resp.message);
            })
    });

    function showAlert(type, message){
        $("html,body").animate({ scrollTop: 0 }, "slow");
        $('.ajaxReplayMessage').html("<div class='alert alert-"+type+"'><button type='button' class='close' data-dismiss='alert'>×</button><strong>"+message+"</strong></div>");
        $('.ajaxReplayMessage').fadeIn();
    }
</script>
<!-- end own script -->