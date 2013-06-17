<?php

function url_get_contents($url, $timeout=30)
{
	$response = false;
	$fd = fopen($url, 'r');
	
	if($fd)
	{
		stream_set_blocking($fd, true);
		stream_set_timeout($fd, $timeout);
		$data = stream_get_contents($fd);
		$status = stream_get_meta_data($fd);
		
		if(!$status['timed_out'])
		{
			$response = $data;
		}
	}
	
	return $response;
}

$id = (int)$_GET['id'];
echo url_get_contents('http://www.nvivo.es/AJAX/getTickets.php?id=' . $id);

?>