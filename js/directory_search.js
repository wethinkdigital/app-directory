jQuery(function(){
	
	
	var target;
	var clickableurl;
	var returndata;

	jQuery('form.directory.search').submit(function(e){
		e.preventDefault();

		target = jQuery(this).attr('targetid');
		clickableurl = jQuery(this).attr('clickableurl');
		returndata = jQuery(this).attr('return');
		returndata = returndata.split(',');
		console.log(returndata);

		itemsearch(jQuery(this));

	});
	
	function itemsearch(form){
		
		var data = form.serialize();
		var datajson = form.serializeObject();
		jQuery.each(datajson, function(k,v){
			jQuery('input[name="'+k+'"]').val(v);
		});
		//console.log('');
		
		jQuery.ajax({
			type: 'POST',
			url:  directory_search.ajaxurl,
			data: data,
			dataType: 'json',
			async: true,
					
			success: function(result){
				var prototype = jQuery('#'+target+' .prototype');
				jQuery('#'+target+' .rowitem').remove();
				
				if(result.posts.length == 0){
					jQuery('#'+target).append('<tr class="rowitem"><td colspan=99><h3>No results were found</h3><p>Please broaden your search and try again</p></td></tr>');
					return false;
				}

				jQuery.each(result.posts, function(index,postdata){
					
					

					if(prototype.length > 0){
						var row = prototype.clone();
						row.addClass('rowitem clickable');
						
						row.removeClass('prototype');
						row.attr('data-href', clickableurl+'?i='+postdata['ID']);
						jQuery.each(returndata, function(k,v){
							var hasvalue = false;
							if(typeof postdata.meta[v] != 'undefined' && postdata.meta[v] != ''){
								hasvalue = true;
								console.log(k+': '+postdata.meta[v]);
								if(jQuery.isArray(postdata.meta[v])){
									row.html(row.html().replace('['+v+']',postdata.meta[v][0]));
								} else {
									row.html(row.html().replace('['+v+']',postdata.meta[v]));
								}
							} 
							
							if(typeof postdata.groupmeta[v] != 'undefined' && postdata.meta[v] != ''){
								hasvalue = true;
								if(jQuery.isArray(postdata.groupmeta[v])){
									row.html(row.html().replace('['+v+']',postdata.groupmeta[v][0]));
								} else {
									row.html(row.html().replace('['+v+']',postdata.groupmeta[v]));
								}
							}

							if(typeof postdata.authormeta[v] != 'undefined' && postdata.meta[v] != ''){
								hasvalue = true;
								if(jQuery.isArray(postdata.authormeta[v])){
									row.html(row.html().replace('['+v+']',postdata.authormeta[v][0]));
								} else {
									row.html(row.html().replace('['+v+']',postdata.authormeta[v]));
								}
							}
							
							if(!hasvalue) row.html(row.html().replace('class="'+v+'"','class="hide"'));

							
						});
						row.html(row.html().replace(/[\, ]\[.*?\]/g,''));
						row.html(row.html().replace(/\[.*?\]/g,''));
					} else {
						//var promoted = '';
						//if(typeof postdata.meta.promote != 'undefined' && typeof postdata.meta.promote_enabled != 'undefined') promoted = 'promoted';					
						var row = '<tr class="clickable rowitem" data-href="'+clickableurl+'?i='+postdata['ID']+'">';
							jQuery.each(returndata, function(k,v){
								if(typeof postdata.meta[v] == 'undefined') postdata.meta[v] = '';
								row += '<td>'+postdata.meta[v]+'</td>';
							});
						row += '</tr>';
					}
					if(postdata.meta.promote == datajson.industry && postdata.meta.promote_enabled == 'enabled' && postdata.meta.ad_type[0] == 'sponsored'){
						row.addClass('promoted');
					}
					
					if(postdata.meta.ad_type == 'premium') row.addClass('premium');
					
					//console.log(postdata.meta.ad_type[0]);

					jQuery('#'+target).append(row);				
				});
			}
		});
	}


});