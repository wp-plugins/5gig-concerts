// tags5gig for WordPress plugin
function getTickets(id){
	obj = $('#tickets_results_'+id);

    $(obj).slideToggle('fast');
    return false;
}

function send_wp_editor(html) {
	document.domain = window.location.hostname;
    var win = window.dialogArguments || opener || parent || top;
    win.send_to_editor(html);
}

function insert_code(code) {
    send_wp_editor(code);
}

jQuery(document).ready(function($) {

   	function show_results(output, mode)
   	{   		
   		$('#tags5gig-results').html(output);
   	}

    function submit_me() {
        // check if the search string is empty
        if ($('#tags5gig-search').val().length == 0) {
            $('#tags5gig-results').html('');
            return;
        }

        // get the search phrase
        var phrase = $('#tags5gig-search').val();

        // get active radio checkbox
        var mode = $("input[name='tags5gig-radio']:checked").val();

        var lang = $('#tags5gig-lang').val();
       
        $('#tags5gig-results').html('<img src="' + tags5gigSettings.tags5gig_url + '/img/loading.gif" />');
                  
        var apiParams = {
			search: phrase,
			mode: mode,
			lang: lang,
			action: 'tags5gig_ajax'
			};
	     
        $.ajax({
			type: "GET",
			url: tags5gigSettings.ajax_url,
			data: apiParams,
			datatype: "string",
			error: function() {
				$('#tags5gig-results').html('Can not retrieve results');
			},
			success: function(searchReponse) {
	           show_results(searchReponse, mode);
	           if( mode == 2 ){
	           		$('#tags5gig-results A').each(function(){
	           			$(this).click(function(){
	           				t_info = $(this).attr('href').replace(/\#/,'').split(':');
	           				id = t_info[0];
	           				lang = t_info[1];
	           				if( !lang ) lang = 'ES';
	           				
	           				getEventsByVenue( id, lang );
	           				
	           				return false;	
	           			});	
	           		});
	           }	   					
			}
		});
    }
    
    
    $('#tags5gig-submit').click(function() {
        submit_me();
    });

    // check for ENTER or ArrowDown keys
    $('#tags5gig-search').keypress(function(e) {
        if (e.keyCode == 13 || e.keyCode == 40) {
            submit_me();
            return false;
        }

    });

	function getEventsByVenue( id, lang ){
		mode = 4;
		$('#tags5gig-results').html('<img src="' + tags5gigSettings.tags5gig_url + '/img/loading.gif" />');
		var apiParams = {
			search: id,
			mode: mode,
			lang: lang,
			action: 'tags5gig_ajax'
		};
		     
		$.ajax({
			type: "GET",
			url: tags5gigSettings.ajax_url,
			data: apiParams,
			datatype: "string",
			error: function() {
				$('#tags5gig-results').html('Can not retrieve results');
			},
			success: function(searchReponse){
				show_results(searchReponse, mode);
			}
		});	
	}
	

	
	

	mode = 2;
});

