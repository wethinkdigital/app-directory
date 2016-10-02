jQuery(function(){

	var autosave = null
	
	jQuery('form.directory.updateuser').submit(function(e){
		if(typeof tinyMCE != 'undefined' )tinyMCE.triggerSave();
		e.preventDefault();
		jQuery(this).find('[type="submit"]').button('loading').addClass('saving');
		console.log('updating user');
		submitForm(jQuery(this));
	});

	jQuery('form.directory.updateuser input, form.directory.updateuser textarea, form.directory.updateuser select').change(function(){
		if(typeof tinyMCE != 'undefined' ) tinyMCE.triggerSave();
		var form = jQuery(this).closest('form');
		if(form.attr('autosave') == 'true'){
			jQuery(this).find('[type="submit"]').button('loading').addClass('saving');
			submitForm(form);
		}
	});	
	
		
	function submitForm(form){
		
		//tinyMCE.triggerSave();
		//var data = form.serialize();
		
		var data = new FormData(form[0]);

		
		jQuery.ajax({
			type: 'POST',
			url:  directory_update_user.ajaxurl,
			data: data,
			cache: false,
			contentType: false,
			processData: false,
					
			success: function(result){
				form.find('[type="submit"]').button('reset').removeClass('saving');
				if(autosave){
					jQuery.each(result, function(k,v){
						jQuery('.preview').find('span#'+k).html(v);				
					});
				}
				if(result.redirect){
					window.document.location(result.redirect);
				}
				if(result.message){
					form.find('.message').html(result.message);
				}
			}
		});
	}


});