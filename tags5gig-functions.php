<?php

	function get5gigDomain( $cou ){
		switch( $cou ){
			default:
			case 1:	
				$domain = 'http://www.nvivo.es';
				break;
			case 2:
				$domain = 'http://www.5gig.co.uk';
				break;
			case 3:
				$domain = 'http://www.5gig.fr';
				break;
			case 4:
				$domain = 'http://www.5gig.it';
				break;
			case 5:
				$domain = 'http://www.5gig.com';
				break;
			case 7:
				$domain = 'http://www.5gig.de';
				break;
			case 8:
				$domain = 'http://www.5gig.nl';
				break;
			case 21:
				$domain = 'http://www.5gig.at';
				break;
			case 29:
				$domain = 'http://www.5gig.be';
				break;
		}
		return $domain;
	}

?>