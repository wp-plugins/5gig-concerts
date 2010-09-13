<?php

/*
Plugin Name: 5gig Concerts
Plugin URI: http://5gig.com
Description: You can use it to search and seek information about concerts from wordpress editor.
Version: 1.0
Author: Miquel Camps Orteza
Author URI: http://miquelcamps.com/
*/

$tags5gig_url = get_settings('home')."/wp-content/plugins/5gigconcerts";	//URL to the plugin directory
$tags5gig_dir = dirname(__FILE__);	//path to the plugin directory
$tags5gig_cache_dir = $tags5gig_dir . '/cache/';	//path to the plugin directory

require $tags5gig_dir . '/tags5gig-functions.php';

function replaceTags5gig($text) {
	global $tags5gig_dir, $tags5gig_cache_dir;
		
	$nvivo_key = get_option('nvivo_key');
	$maps_api = get_option('maps_api');
	
	if( $nvivo_key ){
	
		$file_pattern = '/\[5gig_(.*?)\](.*?)\[\/5gig_(.*?)\]/';
		if (preg_match_all ($file_pattern, $text, $matches)) {
			for ($i = 0; $i < count($matches[0]); $i++) {
				$events = array();
				$mode = $matches[1][$i];
				$t_id = explode(':', $matches[2][$i]);
				$id = $t_id[0];
				$nvivo_cou = $t_id[1];
				
				if( !$id ) $id = $matches[2][$i];
				if( !$nvivo_cou ) $nvivo_cou = 'ES';
				
				$domain = get5gigDomain( $nvivo_cou );
				$cache_id = md5( $mode . '-' . $id );

					
					switch( $mode ){
						
						case 'event':
							$url = $domain . '/api/request.php?api_key=' . $nvivo_key . '&method=event.getEvent&id=' . $id . '&country_iso=' . $nvivo_cou . '&format=xml';
							$xml = tags5gig_getcache( $cache_id, $url );
							$xml = str_replace('geo:', 'geo', $xml);
							//$xml = str_replace(array('<![CDATA[', ']]>'), '', $xml);
							$xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA );
							if( isset( $xml->event ) ) $events = $xml->event;
							break;
							
						case 'artist':
							$url = $domain . '/api/request.php?api_key=' . $nvivo_key . '&method=artist.getEvents&artist=' . urlencode( $id ) . '&country_iso=' . $nvivo_cou . '&format=xml';

							
							$xml = tags5gig_getcache( $cache_id, $url );
							$xml = str_replace('geo:', 'geo', $xml);
							//$xml = str_replace(array('<![CDATA[', ']]>'), '', $xml);
							$xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA );
							if( isset( $xml->events->event ) ) $events = $xml->events->event;
							break;
							
						case 'venue':
							$url = $domain . '/api/request.php?api_key=' . $nvivo_key . '&method=venue.get&venue_id=' . urlencode( $id ) . '&format=xml';
							$xml = tags5gig_getcache( $cache_id, $url );
							$xml = str_replace('geo:', 'geo', $xml);
							//$xml = str_replace(array('<![CDATA[', ']]>'), '', $xml);
							$xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA );
							$venue = $xml->venue;
							if( $venue ){
								$url = $domain . '/api/request.php?api_key=' . $nvivo_key . '&method=venue.getEvents&venue_id=' . urlencode( $id ) . '&format=xml';
								$xml = file_get_contents( $url );
								$xml = str_replace('geo:', 'geo', $xml);
								//$xml = str_replace(array('<![CDATA[', ']]>'), '', $xml);
								$xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA );
								if( isset( $xml->events->event ) ){
									$events = $xml->events->event;
									for( $k = 0; $k < count( $events ); $k++ ){
										$events[$k]->venue->name = $venue->name;
										$events[$k]->venue->location->city = $venue->location->city;
									}							
								}
							}
							break;
							
						case 'city':
							$url = $domain . '/api/request.php?api_key=' . $nvivo_key . '&method=city.getEvents&city=' . urlencode( $id ) . '&country_iso=' . $nvivo_cou . '&format=xml';
							$xml = tags5gig_getcache( $cache_id, $url );
							$xml = str_replace('geo:', 'geo', $xml);
							//$xml = str_replace(array('<![CDATA[', ']]>'), '', $xml);
							$xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA );
							if( isset( $xml->events->event ) ) $events = $xml->events->event;
							break;
							
					}
					

				
				// remplazar bbcodes por html
				$html = '';
		
				if( $mode == 'event' ){

						$coords = $events->venue->location->geopoint->geolat . ',' . $events->venue->location->geopoint->geolong;
						$price = $events->ticket_price->min;
						
						if( !((int) $price) ) $price = false;
						
						$timestamp = strtotime( $events->startDate );
						$dia = date('d', $timestamp);
						$mes = strftime( '%b', $timestamp );
						
						$date = substr( $events->startDate, 11, 5);
						if( $date == '00:00' ) $date = false;
	

						
						$html .= '<div class="widget_box_5gig widget_box_5gig_event">';
						if( $events->tickets_url ) $html .= '<a href="' . $events->url . '" class="button_ticket">' . __("Tickets", 'tags5gig') . '</a>';
						

						$html .= '<div class="minical"><span class="month_label">' . $mes . '</span><b class="day_label">' . $dia . '</b></div>';

						

						$html .= '<div class="gig_info"><b class="title">' . sprintf( __('%s in %s', 'tags5gig'), $events->name, $events->venue->location->city ) . '</b>';
						
	
						$html .= '<div class="gig_labels">';
						
						if( $price ) $html .= __("Tickets", 'tags5gig') . '<br/>';
						
						if( $date ) $html .= 'Hora<br/>';
						$html .= __("Venue", 'tags5gig') . '</div>';
						$html .= '<div class="gig_details">';
						
						if( $price ) $html .= $events->ticket_price->min . '<br/>';
						if( $date ) $html .= $date . '<br/>';
						
						$html .= $events->venue->name . '<br/>';
						$html .= $events->venue->location->street.'</div>';
						
						if( $maps_api ){
							$img = 'http://maps.google.com/staticmap?center=' . $coords . '&zoom=15&size=270x150&maptype=mobile&markers=' . $coords . ',smallred&key=' . $maps_api . '&sensor=false';
							$html .= '<br style="clear:both"/><div class="map"><a href="' . $events->venue->url . '" target="_blank"><img src="' . $img . '" alt="' . $events->venue->name . '"/></a></div>';
						}
						
						$html .= '</div><div><br style="clear:both"/></div></div>';
	
					
				}else{
					


					foreach( $events as $event ){						
						

						
						$timestamp = strtotime( $event->startDate );
						$dia = date('d', $timestamp);
						$mes = strftime( '%b', $timestamp );
	
						$html .= '<div class="widget_box_5gig" style="' . $css . '">';
						
						if( $event->tickets_url ) $html .= '<a href="' . $event->url . '" class="button_ticket">' . __("Tickets", 'tags5gig') . '</a>';
						
						$html .= '<div class="minical"><span class="month_label">' . $mes . '</span><b class="day_label">' . $dia . '</b></div><div>';
						$html .= '<b class="title">' . sprintf( __('%s in %s', 'tags5gig'), $event->name, $event->venue->location->city ) . '</b>';
						
						$html .= $event->venue->name;
						$html .= '</div></div>';
					}
				}
				$text = str_replace($matches[0][$i], $html, $text);
			}
		}
	}
	return $text;
}

function tags5gig_header(){
	global $tags5gig_url;
	echo "<link rel=\"stylesheet\" href=\"{$tags5gig_url}/tags5gig.css\" type=\"text/css\" media=\"all\" />\n";
}

function draw_tags5gig() {
?>

<input type="text" id="tags5gig-search" name="tags5gig-search" size="17" autocomplete="off" />
<?=__("en", 'tags5gig')?>: 
<select name="tags5gig-lang" id="tags5gig-lang">
	<option value="ES">Spain</option>
	<option value="GB">Great Britain</option>
	<option value="FR">France</option>
	<option value="IT">Italy</option>
	<option value="US">United States</option>
	<option value="DE">Deutschland</option>
	<option value="NL">Netherlands</option>
	<option value="AT">Austria</option>
	<option value="BE">Belgium</option>
</select>
<input id="tags5gig-submit" class="button" type="button" value="<?=__("Search", 'tags5gig')?>"  /> <br /><br />
<input name="tags5gig-radio" id="op_artist" type="radio" checked="" value="1" /><label for="op_artist"> <?=__("Artist", 'tags5gig')?> </label>
<input name="tags5gig-radio" id="op_venue" type="radio" value="2"/><label for="op_venue"> <?=__("Venue", 'tags5gig')?> </label>
<input name="tags5gig-radio" id="op_city" type="radio" value="3"/><label for="op_city"> <?=__("City", 'tags5gig')?> </label> <br /><br /><br />

<div id="tags5gig-results"></div>

<style>
#tags5gig-results TD{padding:5px 20px 5px 0;border-bottom:1px #efefef solid}
	#tags5gig-results A{text-decoration:none}
</style>
	
<?php			
}

function modify_menu_tags5gig(){
	add_options_page( 'tags5gig', '5gig Concerts', 7, __FILE__, 'admin_tags5gig_options' );
	add_meta_box( 'tags5gig', __("Search gigs", 'tags5gig'), 'draw_tags5gig', 'post', 'normal', 'high' );
	add_meta_box( 'tags5gig', __("Search gigs", 'tags5gig'), 'draw_tags5gig', 'page', 'normal', 'high' );
				
}

function set_tags5gig_options(){
	add_option('nvivo_key','');
}

function unset_tags5gig_options(){
	delete_option('nvivo_key');
}

function update_tags5gig_options(){

	if( count( $_POST ) ){
		update_option('nvivo_key', $_REQUEST['nvivo_key']);
		update_option('maps_api', $_REQUEST['maps_api']);
	}

	?><div id="message" class="updated fade"><p><?=__("Saved successfully", 'tags5gig')?></p></div>
<?php
}

function scripts_action(){
	global $tags5gig_url;
	wp_enqueue_script('jquery');		 	
	wp_enqueue_script('tags5gig', $tags5gig_url.'/js/tags5gig.js', array('jquery'));
	wp_localize_script('tags5gig', 'tags5gigSettings', array('tags5gig_url' => $tags5gig_url)); 	
}

function tags5gig_init_locale(){
	$locale = get_locale();
	$mofile = dirname(__FILE__) . "/locale/".$locale.".mo";
	
	if( file_exists( $mofile ) )
		load_textdomain('tags5gig', $mofile);
}

function tags5gig_getcache( $id, $url ){
	global $tags5gig_cache_dir;
	$cache_dir = $tags5gig_cache_dir . $id;
	
	if( file_exists( $cache_dir ) && time() < ( filemtime( $cache_dir ) + ( 3600 * 24 )	) ){ // cache 1 dias
		$xml = file_get_contents( $cache_dir );
	}else{
		$xml = file_get_contents( $url );
		$handler = fopen($cache_dir, 'w');
		fwrite($handler, $xml );
		fclose($handler);
	}
	return $xml;
}

function admin_tags5gig_options(){
	global $tags5gig_dir, $tags5gig_cache_dir;
	
	if( $_REQUEST['submit'] )
		update_tags5gig_options();
	
?>
<div class="wrap"><h2><?=__('Configuration', 'tags5gig')?></h2>
<br/>
<form method="post">
	<?php if( !is_writable( $tags5gig_cache_dir ) ): ?>
		<b style="color:red"><?=sprintf( __("Directory %s needs write-access", 'tags5gig'), $tags5gig_cache_dir)?></b><br/><br/>
	<?php endif ?>
	
	<b>5gig API key</b><br/>
	<?=__('This code is necessary to query our database', 'tags5gig')?>
	<br/><br/>
	<input size="40" name="nvivo_key" value="<?=get_option('nvivo_key')?>"/> <a href="http://nvivo.3scale.net/plans" target="_blank"><?=__('Get an API key', 'tags5gig')?></a>
	<br/><br/><br/>
	
	<b>google API key</b><br/>
	<?=__('This code is necessary to show a google map of the gigs', 'tags5gig')?>
	<br/><br/>
	<input size="40" name="maps_api" value="<?=get_option('maps_api')?>"/> <a href="http://code.google.com/intl/es/apis/maps/signup.html" target="_blank"><?=__('Get an API key', 'tags5gig')?></a><br/><br/><br/>
	<input type="submit" name="submit" class="button" value="<?=__('Save changes', 'tags5gig')?>"/>
</form>

<?php
}

register_activation_hook(__FILE__,'set_tags5gig_options');
register_deactivation_hook(__FILE__,'unset_tags5gig_options');

add_action('init', 'tags5gig_init_locale');
add_filter('the_content', 'replaceTags5gig', 1);
add_action('wp_head', 'tags5gig_header');
add_action('admin_menu','modify_menu_tags5gig');
add_action('admin_print_scripts-post.php', 'scripts_action');
add_action('admin_print_scripts-page.php', 'scripts_action');
add_action('admin_print_scripts-post-new.php', 'scripts_action');
add_action('admin_print_scripts-page-new.php', 'scripts_action');

?>