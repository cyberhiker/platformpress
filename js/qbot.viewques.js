jQuery(document).ready(function($) {

	//localStorage.clear();
	jQuery(".write-comment-action").click(function(){
		jQuery(this).parent().parent().find("form.qbot-comment-form").toggle();
		
	});
    jQuery(".unlock").click(function(){
        jQuery(this).hide(function(){
			jQuery(".fb-like").show()
		});
    });
	
	$('#qbotform').validate({	
		ignore: "",
		rules: {
			'qbotanswercontent': {
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
	
	jQuery('#qbotform input[type="submit"]').on('click',function(){
		content = tinymce.get('qbotanswercontent').getContent();
		$("#qbotanswercontent").val(content);
	});	
	
});