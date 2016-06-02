	jQuery(document).ready(function($) {
			$('#qbotform').validate({	
			ignore: "",
			rules: {
				question_title: {
					required: true,
					maxlength:150,
					minlength: 3
					
				},
				question_slug: {
					required: true,
					maxlength:150,
					minlength: 3
				},
				question_description: {
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
		
		jQuery('.slug').slugify('#question_title');
		
		jQuery('#qbotform input[type="submit"]').on('click',function(){
			content = tinymce.get('question_description').getContent();
			$("#question_description").val(content);
		});
		
	});