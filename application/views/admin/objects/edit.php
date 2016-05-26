<script type="text/javascript" charset="utf-8">

    var tinyLoaded = true;
	$(document).ready(function() {

		function resetCaps(text) {
			var sourceText = text, clearedText, allTags, clearedHTM;
		  
		  function capitalizeFirstLetter(string) {
		      return string.charAt(0).toUpperCase() + string.slice(1);
		  }
		  
		  var re = /(<([^>]+)>)/ig;
		  var allTags = sourceText.match(re);
		  clearedText = sourceText.replace(re, '@@@').split('@@@').map(function(item){
		  	
		    var sourceSub = item, clearedSub;
		    var reSigns = /(\.|!)/ig;
		  	var allSigns = item.match(reSigns);
		    
		    clearedSub = sourceSub.replace(reSigns, '###').split('###').map(function(subItem){
		    	return (subItem.length >= 3) ? subItem.replace(subItem.trim(), capitalizeFirstLetter(subItem.trim().toLowerCase())) : subItem;
		    }).reduce(function(result, current, index){
		      result = result || "";
		      return result + allSigns[index - 1] + current;
		    });
		    
		    
		  	return clearedSub;
		  }).reduce(function(result, current, index){
		  	result = result || "";
		  	return result + allTags[index - 1] + current;
		  });
		  
		  return clearedText;
		}

		// try {

		// 		$('.tiny').tinymce({
		// 			//selector: "textarea .tiny",
		// 			theme: "modern",
		// 			image_advtab: true,
		// 			width: '100%',
		// 			verify_html : false,
		// 			toolbar_items_size: 'small',
		// 			plugins: ["visualblocks visualchars code fullscreen"],
		// 		});

		// } catch (e) {

		// }

		var editor =new nicEditor({
                    iconsPath:'/images/nicEditorIcons.gif'
           }).panelInstance('tiny');

		$('#edit_form').submit(function(e){
			e.preventDefault();

			nicEditors.findEditor('tiny').saveContent();

			$.post('/khbackend/objects/save/<?=$object->id?>', $(this).serialize(), function(json){
				if (json.code == 200) {
					$('.modal-body .alert-error').hide('slow');
					$('#myModal').modal('hide');
					reload_row(<?=$object->id?>);
				} else if (json.errors) {
					$('.modal-body .alert-error').html(json.errors).show('slow');
				}
			}, 'json');


		});

		$('.js-correct').click(function(e){

			e.preventDefault();

			nicEditors.findEditor('tiny').saveContent();


			nicEditors.findEditor('tiny').setContent(resetCaps($('#tiny').val()));

			$('.title').val(resetCaps($('.title').val()));


		})
	});
</script>

<form action="" class="form-horizontal" id="edit_form">
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<h3 id="myModalLabel">Редактирование объявления</h3>
</div>
<div class="modal-body">
		<div class="alert alert-error hide"></div>

		<input type="text" name="title" style="width:100%" value="<?=$object->title?>" class="title" required />
		<br /><br />
		<textarea name="user_text"  style="width:500px; height:250px;"  class="tiny input-xlarge" id="tiny"><?=$object->user_text?></textarea>
</div>
<div class="modal-footer">
	<button class="btn btn-warning fl js-correct">Исправить регистр</button>
	<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	<input type="submit" class="btn btn-primary" value="Save changes" />
</div>
</form>