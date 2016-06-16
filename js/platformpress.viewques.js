jQuery(document).ready(function($) {

	//localStorage.clear();
	jQuery(".write-comment-action").click(function(){
		jQuery(this).parent().parent().find("form.platformpress-comment-form").toggle();
		
	});
    jQuery(".unlock").click(function(){
        jQuery(this).hide(function(){
			jQuery(".fb-like").show()
		});
    });
	
	$('#platformpressform').validate({	
		ignore: "",
		rules: {
			'platformpressremarkcontent': {
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
	
	jQuery('#platformpressform input[type="submit"]').on('click',function(){
		content = tinymce.get('platformpressremarkcontent').getContent();
		$("#platformpressremarkcontent").val(content);
	});	
	
});