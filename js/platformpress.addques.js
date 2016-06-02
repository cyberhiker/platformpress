	jQuery(document).ready(function($) {
			$('#platformpressform').validate({	
			ignore: "",
			rules: {
				plank_title: {
					required: true,
					maxlength:150,
					minlength: 3
					
				},
				plank_slug: {
					required: true,
					maxlength:150,
					minlength: 3
				},
				plank_description: {
					required: true,
					minlength: 3
				},
			},
			highlight: function(element) {
				$(element).parent().parent().addClass('form-invalid');
			},		
			unhighlight: function(element) {
				$(element).parent().parent().removeClass('form-invalid');
			},		
		});
		
		jQuery('.slug').slugify('#plank_title');
		
		jQuery('#platformpressform input[type="submit"]').on('click',function(){
			content = tinymce.get('plank_description').getContent();
			$("#plank_description").val(content);
		});
		
	});