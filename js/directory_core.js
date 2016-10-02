jQuery(function(){
	
	// clickable rows
	jQuery('body').on('click', '.clickable', function () {  
		window.document.location = jQuery(this).attr('data-href');
	});
	
	jQuery('.datepicker').datepicker({
		dateFormat : 'd M yy',
		minDate: 0
	});

	
});


// Show / hide questions based on dependency


jQuery(function(){
	
	showHide();
	jQuery('input, select, textarea').change(function(){
		showHide();
	});
	
	function showHide(){
		jQuery('input, select, textarea').each(function(){

			if(typeof jQuery(this).attr('dependency') != 'undefined'){

				var deps = jQuery(this).attr('dependency').split(',');
				var show = 'false';
				
				jQuery.each(deps, function(index, dep){

					dep = dep.split(':');
					
					// show this question if it's dependency parent isn't used
					if(jQuery('[name="'+dep[0]+'"]').length == 0 && jQuery(':checkbox[name="'+dep[0]+'[]"]').length == 0){
						show = 'true';
					}

					// show this question if dependency params are met
					if(dep[1].startsWith('!')){

						dep[1] = dep[1].replace(/!/,'');
						console.log(dep);

						if(jQuery(':checkbox[name="'+dep[0]+'[]"][value="'+dep[1]+'"]').is(':checked')){} else {
							show = 'true';
						}
						

					} else {

						if(jQuery('[name="'+dep[0]+'"]').val() == dep[1] || jQuery(':checkbox[name="'+dep[0]+'[]"][value="'+dep[1]+'"]').is(':checked')){
							show = 'true';
						}

					}
					
					
				});
				
				if(show == 'true'){
					jQuery(this).closest('.question').show();					
				} else {
					jQuery(this).closest('.question').hide();					
				}

			}


		});
	}

	//forceVals();
	jQuery('input, select, textarea').change(function(){

		if(typeof jQuery(this).attr('force') != 'undefined'){

			var force = jQuery(this).attr('force').split('@');
			// this question has the correct force value
			if(jQuery(this).val() == force[0] || jQuery(this).val() == force[0] && jQuery(this).is(':checked')){
				var forceTarget = force[1].split(':');
				jQuery('[name="'+forceTarget[0]+'"]').val(forceTarget[1]);
				jQuery('[name="'+forceTarget[0]+'[]"][value="'+forceTarget[1]+'"]').attr('checked','checked');
			}
			var show = 'false';

		}

	});

});




/*
	
foreach field
 if has a dependency, check that dependency is met then validate
	
*/

function dirvalidates(form){
	
	
	var result = 'true';
	form.find('input, textarea, select, radio, file').each(function(){
		if(typeof jQuery(this).attr('req') != 'undefined'){
			if(jQuery(this).attr('req') != ''){


	
					if(typeof jQuery(this).attr('reqdep') != 'undefined'){
					var reqdep = jQuery(this).attr('reqdep');
			

					// checks if form state matches to invoke required fields
					if(form.find('*[name="'+reqdep+'"]').val() == jQuery(this).attr('req')){
						
						if(jQuery(this).is(':checkbox')){
							var name = jQuery(this).attr('name');
							if(jQuery('input[name="'+name+'"]:checked').length > 0){
								jQuery(this).closest('fieldset').removeClass('invalid');					
							} else {
								console.log(name+' is empty');
								jQuery(this).closest('fieldset').addClass('invalid');	
								result = 'false';
							}
						} else if(jQuery(this).val() == ''){
							jQuery(this).addClass('invalid');
							result = 'false';
						} else {
							jQuery(this).removeClass('invalid');			
						}
						

					}
					
				}

				
				
			}
			
			if(jQuery(this).attr('req') == 'save'){
				
						if(jQuery(this).is(':checkbox')){
							var name = jQuery(this).attr('name');
							if(jQuery('input[name="'+name+'"]:checked').length > 0){
								jQuery(this).closest('fieldset').removeClass('invalid');					
							} else {
								console.log(name+' is empty');
								jQuery(this).closest('fieldset').addClass('invalid');	
								result = 'false';
							}
						} else if(jQuery(this).val() == ''){
							jQuery(this).addClass('invalid');
							result = 'false';
						} else {
							jQuery(this).removeClass('invalid');			
						}

				
			}
		}
	});
	
	if(form.find('.invalid').length > 0) result = 'false';

	console.log('validation result: '+result);
	return result;
}