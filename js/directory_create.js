jQuery(function(){
	
	var autosave;

	jQuery('form.directory.create').submit(function(e){
		//tinyMCE.triggerSave();
		e.preventDefault();
		submitForm(jQuery(this));
	});
	
	function submitForm(form){
		
		//var data = form.serialize();
		var data = new FormData(form[0]);

		
		jQuery.ajax({
			type: 'POST',
			url:  directory_create.ajaxurl,
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
					
			success: function(result){
				if(result.redirect){
					window.document.location(result.redirect);
				}
				if(result.message){
					form.find('.message').html(result.message).show();
					form.siblings('.message').html(result.message).show();
				}
				if(result.formafter){
					var formafter = result.formafter;
					if (formafter.toLowerCase().indexOf('fade') >= 0){
						form.fadeOut();
					}
					if (formafter.toLowerCase().indexOf('hide') >= 0){
						form.hide();
					}
					if (formafter.toLowerCase().indexOf('collapse') >= 0){
						form.animate({
							height: '0px'
						}, 1200, function(){
							form.hide();
						});
					}
				}
				if(result.post_author == 0){
					jQuery.each(result, function(k,v){
						jQuery('#register_prompt').find('[name="'+k+'"]').val(v);				
						jQuery('#register_prompt').find('[name="'+k+'"]').html(v);				
					});
					jQuery('#register_prompt').fadeIn();
				}
				form.trigger("reset");
			}
		});
	}


});