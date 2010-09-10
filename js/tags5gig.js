// tags5gig for WordPress plugin

function send_wp_editor(html) {
    var win = window.dialogArguments || opener || parent || top;
    win.send_to_editor(html);

    // alternatively
    // tinyMCE.execCommand("mceInsertContent", false, html);
}

function insert_link(html_link) {
    if ((typeof tinyMCE != "undefined") && (edt = tinyMCE.getInstanceById('content')) && !edt.isHidden()) {
        var sel = edt.selection.getSel();
        //sel.toString()
        if (sel) {
            var link = '<a href="' + html_link + '" title="' + sel + '">' + sel + '</a>';

            send_wp_editor(link);
        }
    }
    return false;
}

function insert_image(link, src, title) {
    var size = document.getElementById('img_size').value;
    var img = '<a href="' + link + '"><img src="' + src + size + '.jpg" alt="' + title + '" title="' + title + '" hspace="5" border="0" /></a>';

    send_wp_editor(img);
}


var videoid = 0;

function insert_video() {
    var video = '<object type="application/x-shockwave-flash" width="425" height="344" data="http://www.youtube.com/v/' + videoid + '&amp;rel=0&amp;fs=1"><param name="movie" value="http://www.youtube.com/v/' + videoid + '&amp;rel=0&amp;fs=1"></param><param name="allowFullScreen" value="true"></param><param name="wmode" value="transparent" /></object>';

    send_wp_editor(video);
}

function insert_map() {
    var maphtml = '<img src="' + updateImage() + '" alt="" />';

    send_wp_editor(maphtml);

}


function insert_code(code) {
    send_wp_editor(code);

}




function show_video(ytfile, yttitle, ytdesc, ytviews, ytrating) {
  
  videoid=ytfile;	
	var link='<span style="padding: 2px"><object type="application/x-shockwave-flash" width="425" height="344" data="http://www.youtube.com/v/'+ytfile+'&amp;rel=0&amp;fs=1"><param name="movie" value="http://www.youtube.com/v/'+ytfile+'&amp;rel=0&amp;fs=1"></param><param name="allowFullScreen" value="true"></param><param name="wmode" value="transparent" /></object></span>';
  var data='<h4>'+yttitle+'</h4><p><a href="http://www.youtube.com/watch?v='+ytfile+'">link</a></p><p>'+ytdesc+'</p><p><strong>Views:</strong> '+ytviews+'</p><p><strong>Rating: </strong>'+ytrating+'</p>';
  var button='<br /><p><input class="button" type="button" value="Add Video" onclick="insert_video();" ></p><p>(you may need to go from Visual to HTML mode and back to see the video object)</p>';
	
	jQuery('#tags5gig-youtube-preview').html(link);
	jQuery('#tags5gig-youtube-data').html(data+button);
	jQuery('#tags5gig-youtube-holder').fadeIn();
}


// setup everything when document is ready
jQuery(document).ready(function($) {

    // initialize the variables
//    var search_timeout = undefined;
//    var last_mode = undefined;
 //   var last_search = undefined;

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


        /*
        if ((jQuery.trim(phrase) == last_search) && last_mode == mode) {
            return;
        }
 				last_mode = mode;
        last_search = phrase;      
        */
       
        $('#tags5gig-results').html('<img src="' + tags5gigSettings.tags5gig_url + '/img/loading.gif" />');
                  


        // create the query
 //       var query = tags5gigSettings.tags5gig_url + '/tags5gig-ajax.php?search=' + escape(phrase) + '&mode=' + mode + '&lang=' + lang;

        //var cached = $.jCache.getItem(query);
        
        //if (cached){
        //		alert( query );
        //		show_results(cached, mode);
        //}
        //else
       	//{		
	        var apiParams = {
						search: phrase,
						mode: mode,
						lang: lang
					};
	     
	       $.ajax({
						type: "GET",
						url: tags5gigSettings.tags5gig_url + "/tags5gig-ajax.php",
						data: apiParams,
						datatype: "string",
						error: function() {
							$('#tags5gig-results').html('Can not retrieve results');
						},
						success: function(searchReponse) {
						
	           show_results(searchReponse, mode);
	           //$.jCache.setItem(query, searchReponse);
	           
	           
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
				//}

    }
    
    // measure time
  	// var startTime=new Date();
    // search button click event
    // var endTime=new Date();
	  // var responseTime=(endTime.getTime()-startTime.getTime());
    
    
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

   // if (parseInt(tags5gigSettings.tags5gig_interactive))

    // automatically refresh the view
    /*
    $('#tags5gig-search').keyup(function(e) {
        if (search_timeout != undefined) {
            clearTimeout(search_timeout);
        }
        if ($('#tags5gig-search').val().length < 3) {            
            return;
        }

        search_timeout = setTimeout(function() {
            search_timeout = undefined;
            submit_me();
        },
        700);
    });
	*/




function getEventsByVenue( id, lang ){

	mode = 4;
	
	$('#tags5gig-results').html('<img src="' + tags5gigSettings.tags5gig_url + '/img/loading.gif" />');
	
	//var query = tags5gigSettings.tags5gig_url + '/tags5gig-ajax.php?search=' + id + '&mode=4';	
        //var cached = $.jCache.getItem(query);
        
        //if (cached)
        //		show_results(cached, mode);
        //else
       	//{		
	        var apiParams = {
						search: id,
						mode: mode,
						lang: lang
					};
	     
	       $.ajax({
						type: "GET",
						url: tags5gigSettings.tags5gig_url + "/tags5gig-ajax.php",
						data: apiParams,
						datatype: "string",
						error: function() {
							$('#tags5gig-results').html('Can not retrieve results');
						},
						success: function(searchReponse) {
						
	           show_results(searchReponse, mode);
	          // $.jCache.setItem(query, searchReponse);
	   					
						}
					});	
				}
		mode = 2;
//}


});


