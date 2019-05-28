<style>
	.question{
		text-align: "justify";
	}
	.selected-answere{
		font-weight: bold;
	}
	.answere-box{
		margin-left: 20px;
	}
	
	.questionary-panel{
		display: none;
	}
</style>


<!-- start breadcrumb -->
<div class="row">
	<div class="col-sm-12">
		<ol class="breadcrumb">
			<li><a href="#">Home</a></li>
			<li class="active">Cuestionarios</li>
		</ol>
	</div>
</div>
<!-- end breadcrumb -->

<div class="row" id="questionary-type"></div>

<div class="row">
	<div class="col-sm-12" id="questionary"></div>
</div>

<!-- start own script-->
<script>
	function showPanel(panelId){
		$(panelId).closest("div.questionary-panel").addClass("show").siblings().removeClass("show");;
	}
	

	$(document).ready(function() {
		var url = "<?php echo base_url('api/rquestionary/initialdata');?>";
		//Call to API
		$.get(url)
			.done(function(response) {
				var panel = '';
				var questionary_type = '';
				var questionaries = null;
				$.each(response, function(i, obj){
					questionary_type += '<div class="col-sm-3">'
											+'<p><i class="glyphicon glyphicon-chevron-right"></i> <a href="#" onclick="showPanel(\'#panel-'+i+'\');">'+obj.name+'</a></p>'
										+'</div>';
					$("#questionary-type").html(questionary_type);
				});
				$.each(response, function(i, obj){
					panel += '<div id="panel-'+i+'" class="questionary-panel panel panel-default">'
				  		  	+'<div class="panel-body">';
					questionaries = response;
					panel += '<h2 class="text-center">'+obj.name+'</h2>';
					$.each(obj.categories, function(i, obj_category){
						panel += '<h4>'+obj_category.title+'</h4>';
						$.each(obj_category.questions, function(y, obj_question){
							panel += '<p class="question">'
			    			+'<ol start='+(y+1)+'>'
			    				+'<li>'+obj_question.title+'</li>'
							+'</ol>'
							
							+'<div class="answere-box">'
								+'<ol type="a">';
									$.each(obj_question.options, function(i, obj_options){
										panel += '<li>'+obj_options.title+'</li>';
										//<li class="selected-answere">Algunas veces <i class="glyphicon glyphicon-ok"></i></li>
									});
									panel+='</ol>'
				    	   		+'<p></p>'
						 		+'<hr>'
							+'</div>';
						});
						
					});
					panel += '</div>'
						+'</div>';	
				});

				$("#questionary").html(panel);
			})
			.fail(function() {
				//alert( "error" );
			})
			.always(function() {
				//alert( "finished" );
			});
		});
		</script>
<!-- end own script -->