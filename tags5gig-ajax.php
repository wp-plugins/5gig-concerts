<?php

require_once('../../../wp-config.php');
  
$tags5gig_path = dirname(__FILE__);

$nvivo_key = get_option('nvivo_key');

$lang = $_GET['lang'];
if( !$lang ) $lang = 'ES';
	 
  
if ($_GET['search']) {
	
	
$domain = get5gigDomain( $lang );
	  
	  	
	 	switch ($_GET['mode']){
	 		
/////////////////////////////////////////////////////////////////
	 		
	 		case 1:

  	 		
	  	 	$url = $domain . '/api/request.php?api_key=' . $nvivo_key . '&method=artist.getEvents&artist=' . urlencode( $_GET['search'] ) . '&country_iso=' . $lang;
  	 		$xml = @simplexml_load_file( $url, 'SimpleXMLElement', LIBXML_NOCDATA );




  	 		if( isset( $xml->events->event ) ){
  	 			echo __("Click on one of the gigs to add it to your post", 'tags5gig'), ':<br/><br/><table>';
  	 			foreach( $xml->events->event as $event ){
  	 				$date = date('d/m/Y', strtotime( $event->startDate ) );
  	 				echo '<tr>
	  	 				<td><a href="javascript:insert_code(\'[5gig_event]', $event->id, ':', $lang, '[/5gig_event]\')">', $event->venue->name, '</a></td>
	  	 				<td>', $event->venue->location->city, '</td>
	  	 				<td style="color:#666">', $date, '</td>
	  	 				</tr>';
  	 			}
  	 			echo '</table>';
  	 			
  	 		}else{
  	 			echo __("No results found", 'tags5gig'), '<br/><br/>';	

  	 		}
  	 		
			echo '<br/><a href="javascript:insert_code(\'[5gig_artist]', $_GET['search'], ':', $lang, '[/5gig_artist]\')" style="font-weight:bold">', __("Add list with upcoming gigs", 'tags5gig'), '</a><br/><br/>';
  	 		
  	 		die();
  	 		break;

/////////////////////////////////////////////////////////////////
	 		
	 		
	 			
	 		
		  	case 2:
  	 		$url = $domain . '/api/request.php?api_key=' . $nvivo_key . '&method=venue.find&venue_name=' . urlencode( $_GET['search'] ) . '&country_iso=' . $lang;
  	 		$xml = @simplexml_load_file( $url, 'SimpleXMLElement', LIBXML_NOCDATA );

  	 		if( strstr($xml->error, "No venues") ){
  	 			
  	 		}

  	 		if( isset( $xml->venues->venue[0] ) ){
  	 			echo 'Selecciona una sala para ver sus conciertos:<br/><br/><table cellpadding="5">';
  	 			foreach( $xml->venues->venue as $venue ){
  	 				//echo '<span style="float:right"></span>';
  	 				echo '<tr>
  	 					<td><a href="#', $venue->id, ':', $lang, '">', $venue->name, '</a></td>
	  	 				<td style="color:#666">', $venue->location->city, '</td>
	  	 				</tr>';
  	 			}
  	 			echo '</table>';
  	 		}else{
  	 			
  	 			echo __("No results found", 'tags5gig');
  	 			
  	 		}
  	 		
  	 		die();
  	 		

  	 		
  	 		break;	
  	 		
  	 		
/////////////////////////////////////////////////////////////////
	 		
		  	case 3:

  	 		
  	 		$url = $domain . '/api/request.php?api_key=' . $nvivo_key . '&method=city.getEvents&city=' . urlencode( $_GET['search'] ) . '&country_iso=' . $lang;
  	 		$xml = @simplexml_load_file( $url, 'SimpleXMLElement', LIBXML_NOCDATA );

  	 		if( strstr($xml->error, "doesn't exist") ){
  	 			die( __("No results found", 'tags5gig') );
  	 		}

  	 		if( isset( $xml->events->event ) ){
  	 			echo __("Click on one of the gigs to add it to your post", 'tags5gig'), ':<br/><br/><table>';
  	 			foreach( $xml->events->event as $event ){
  	 				
  	 				$date = date('d/m/Y', strtotime( $event->startDate ) );

  	 				echo '<tr>
  	 						<td><a href="javascript:insert_code(\'[5gig_event]', $event->id, ':', $lang, '[/5gig_event]\')">', $event->name, '</a></td>
		  	 				<td style="color:#666">', $date, '</td>
		  	 			</tr>';
  	 			}
  	 			
  	 			echo '</table>';

  	 		}else{
  	 			echo 'sin conciertos<br/><br/>';
  	 			
  	 		}
  	 		
			echo '<br/><a href="javascript:insert_code(\'[5gig_city]', $_GET['search'], ':', $lang, '[/5gig_city]\')" style="font-weight:bold">', __("Add list with upcoming gigs", 'tags5gig'), '</a><br/><br/>';
  	 		
  	 		die();
  	 		
  	 		break;


/////////////////////////////////////////////////////////////////

			case 4:
  	 		$url = $domain . '/api/request.php?api_key=' . $nvivo_key . '&method=venue.getEvents&venue_id=' . urlencode( $_GET['search'] ) . '&country_iso=' . $lang;
  	 		$xml = @simplexml_load_file( $url, 'SimpleXMLElement', LIBXML_NOCDATA );

  	 		if( strstr($xml->error, "doesn't exist") ){
  	 			die('la sala no existe');
  	 		}

  	 		if( isset( $xml->events->event ) ){
  	 			echo __("Click on one of the gigs to add it to your post", 'tags5gig'), ':<br/><br/><table>';
  	 			foreach( $xml->events->event as $event ){
  	 				
  	 				$date = date('d/m/Y', strtotime( $event->startDate ) );
  	 				
  	 				echo '<tr>
  	 					<td><a href="javascript:insert_code(\'[5gig_event]', $event->id, ':', $lang, '[/5gig_event]\')">', $event->name, '</a></td>
  	 					<td style="color:#666">', $date, '</td>
  	 					</tr>';
  	 			}
  	 			echo '</table>';
  	 		}
  	 		
			echo '<br/><a href="javascript:insert_code(\'[5gig_venue]', $_GET['search'], ':', $lang, '[/5gig_venue]\')" style="font-weight:bold">', __("Add list with upcoming gigs", 'tags5gig'), '</a><br/><br/>';
  	 		
  	 		die();
			
			
			break;
  	 		
  	 		
/////////////////////////////////////////////////////////////////
			
		}
		
		
		
		
		
		
		
		
}


die('No results found');

  


?>
