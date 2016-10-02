jQuery(function(){
		
	var target;
	var clickableurl;
	var returndata;

	jQuery('form.directory.searchuser').submit(function(e){
		e.preventDefault();

		target = jQuery(this).attr('targetid');
		clickableurl = jQuery(this).attr('clickableurl');
		returndata = jQuery(this).attr('return');
		returndata = returndata.split(',');
		console.log(returndata);

		usersearch(jQuery(this));

	});
	
	function usersearch(form){
		
		console.log('running user search');
		
		var data = form.serialize();
		var datajson = form.serializeObject();
		jQuery.each(datajson, function(k,v){
			jQuery('input[name="'+k+'"]').val(v);
		});
		//console.log('');
		
		jQuery.ajax({
			type: 'POST',
			url:  directory_search_user.ajaxurl,
			data: data,
			dataType: 'json',
			async: true,
					
			success: function(users){
				var prototype = jQuery('#'+target+' .prototype');
				jQuery('#'+target+' .rowitem').remove();
				
				console.log('found '+users.length+' users');
				
				if(users.length == 0){
					jQuery('#'+target).append('<tr class="rowitem"><td colspan=99><h3>No results were found</h3><p>Please broaden your search and try again</p></td></tr>');
					return false;
				}

				jQuery.each(users, function(index,userdata){
					
					console.log(userdata.data.meta.nickname);

					if(prototype.length > 0){
						var row = prototype.clone();
						row.addClass('rowitem clickable');
						
						row.removeClass('prototype');
						row.attr('data-href', clickableurl+'?i='+userdata['ID']);
						jQuery.each(returndata, function(k,v){
							var hasvalue = false;
							if(typeof userdata.data.meta[v] != 'undefined' && userdata.data.meta[v] != ''){
								hasvalue = true;
								console.log(k+': '+userdata.data.meta[v]);
								if(jQuery.isArray(userdata.data.meta[v])){
									row.html(row.html().replace('['+v+']',userdata.data.meta[v][0]));
								} else {
									row.html(row.html().replace('['+v+']',userdata.data.meta[v]));
								}
							} 
							
							
							if(!hasvalue) row.html(row.html().replace('class="'+v+'"','class="hide"'));

							
						});
						row.html(row.html().replace(/[\, ]\[.*?\]/g,''));
						row.html(row.html().replace(/\[.*?\]/g,''));
					} else {
			
						var row = '<tr class="clickable rowitem" data-href="'+clickableurl+'?i='+userdata['ID']+'">';
							jQuery.each(returndata, function(k,v){
								if(typeof userdata.data.meta[v] == 'undefined') userdata.data.meta[v] = '';
								row += '<td>'+userdata.data.meta[v]+'</td>';
							});
						row += '</tr>';
					}

								
					//console.log(userdata.data.meta.ad_type[0]);

					jQuery('#'+target).append(row);				
				});
			}
		});
	}


});