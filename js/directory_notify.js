jQuery(function(){
	

	console.log('notify script queued');
	
	var successmessage = 'default alert';
	
	jQuery('form.directory.notify').submit(function(e){
		var form = jQuery(this);
		e.preventDefault();
		console.log('notify form submitted');
		
		var data = jQuery(this).serialize();
		var datajson = jQuery(this).serializeObject();
		console.log(datajson);


		if(jQuery(this).find('input[name="successmessage"]').length > 0){
			successmessage = jQuery(this).find('input[name="successmessage"]').val();
		}


		
		jQuery.ajax({
			type: 'POST',
			url:  directory_notify.ajaxurl,
			data: data,
			dataType: 'json',
			async: true,
					
			success: function(result){
				console.log('ajax complete');
				form[0].reset();
				form.find('.alert').remove();
				form.append('<div class="alert alert-success" style="text-align: left; margin-top: 10px;">'+successmessage+'</p>');
			}
		});
	});

});