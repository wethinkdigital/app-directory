jQuery(function(){

	console.log('directory_login loaded');
    
    jQuery('form#login').submit(function(e){
	    e.preventDefault();
	    
	    var data = jQuery(this).serialize();
	    
	    jQuery.ajax({
			type: 'POST',
			url:  directory_login.ajaxurl,
			data: data,
			dataType: 'json',
			async: true,
					
			success: function(result){
				console.log(result);
				if(!result.loggedin) jQuery('form#login').find('.message').html(result.message);
				
				if(result.redirect) { 
					if(window.location.pathname+window.location.search == result.redirect){
						location.reload();
					} else {
						window.document.location = result.redirect;						
					}
				}
				
				else if	(result.roles[0] == 'recruiter' || result.roles[0] == 'recruiter_admin') { window.document.location = '/recruiter-dashboard'; }
				else if	(result.roles[0] == 'advertiser' ) { window.document.location = '/advertiser-dashboard'; }
				else if	(result.roles[0] == 'candidate' ) { window.document.location = '/candidate-profile'; }
				else if	(result.roles[0] == 'administrator' ) { window.document.location = '/sysadmin-dashboard'; }
				else 	{ window.document.location = '/wp-admin'; }


			}
		});
    });

});