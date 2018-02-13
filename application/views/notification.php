<!-- start breadcrumb -->
<div class="row">
    <div class="col-sm-12">
        <ol class="breadcrumb">
            <li><a href="#">Home</a></li>
            <li class="active">Notificación</li>
        </ol>
    </div>
</div>
<!-- end breadcrumb -->

<div class="row">
    <div class="col-sm-12">
        <div class="ajaxReplayMessage"></div>
        <div class="panel panel-default">
            <div class="panel-heading">Notificación Push</div>
            <div class="panel-body">
                <form id="notificationForm">
                    <div class="form-group">
                        <label for="title">Título</label>
                        <input type="text" class="form-control" required="required" id="title" name="title">
                    </div>
                    <div class="form-group">
                        <label for="comment">Comentarios</label>
                        <textarea class="form-control" rows="5" id="comment" required="required" id="comment" name="comment"></textarea>
                    </div>
                    <div><button class="btn btn-success center-block" type="button" id="btnSend" data-loading-text="&lt;span&gt;<i class='fa fa-refresh fa-spin'>
				</i>&nbsp;&nbsp;Enviando...&lt;span&gt;">Enviar</button></div>
                    <input type="hidden" value="" id="company_id" name="company_id">
                </form>
            </div>
        </div>
    </div>
</div>

<!-- start own script-->
<script>
    $(document).ready(function(){
        if(user.code){
            $('#company_id').val(user.code);
        }
    });

    $('#btnSend').click(function(){
        var btn = $(this);
        btn.button("loading");
        $.post( 'notification/send_to_all', $('#notificationForm').serialize())
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


    function showAlert(type, message){
        $("html,body").animate({ scrollTop: 0 }, "slow");
        $('.ajaxReplayMessage').html("<div class='alert alert-"+type+"'><button type='button' class='close' data-dismiss='alert'>×</button><strong>"+message+"</strong></div>");
        $('.ajaxReplayMessage').fadeIn();
    }

</script>
<!-- end own script -->