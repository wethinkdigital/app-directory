jQuery(function(){
	
	var autosave = 'false';
	
	
	jQuery('form.directory.update').submit(function(e){
		tinyMCE.triggerSave();
		e.preventDefault();
		console.log('form submitted');
		var validates = dirvalidates(jQuery(this));
		if(validates == 'true'){

			jQuery(this).find('[type="submit"]').button('loading').addClass('saving');
    		jQuery('.invalidmsg').html('');
			submitForm(jQuery(this),autosave);
			return true;
		} else {
			jQuery('.invalidmsg').html('<div class="alert alert-danger">You did not complete some required fields - please check the form below and try again.</div>');
		}
	});

	jQuery('form.directory.update input, form.directory.update textarea, form.directory.update select').change(function(){
		tinyMCE.triggerSave();
		console.log('changes made');
		var form = jQuery(this).closest('form');
		autosave = form.attr('autosave');
		if (typeof autosave !== 'undefined' && autosave == 'true') {
			console.log('autosaving');
			var validates = dirvalidates(jQuery(this));
			if(validates == 'true'){
    			jQuery('.invalidmsg').html('');
				submitForm(form,autosave);
			} else {
				jQuery('.invalidmsg').html('<div class="alert alert-danger">You did not complete some required fields - please check the form below and try again.</div>');
			}
		}
	});	
		

	function submitForm(form,autosave){
		
		console.log(form);
				
		var data = new FormData(form[0]);
		//var data = form.serialize();

		
		// Is this required? Seems to work without...

		/*
		jQuery.each(jQuery('input[type="file"]')[0].files, function(i,file){
			data.append(jQuery(this).attr('name'),file);
		});
		*/

				
		jQuery.ajax({
			type: 'POST',
			url:  directory_update.ajaxurl,
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
					
			success: function(result){
				form.find('[type="submit"]').button('reset').removeClass('saving');
				if(autosave == 'true'){
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