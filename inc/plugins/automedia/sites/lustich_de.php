<?php

###################################
# Plugin AutoMedia 2.0  for MyBB 1.6.*#
# (c) 2011 by doylecc    #
# Website: http://mods.mybb.com/profile/14694 #
###################################

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />
	Please make sure IN_MYBB is defined.");
}

function automedia_lustich_de($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "500";
		$h = "375";
	}

/**
 *Example:
 *http://lustich.de/videos/sportler/fan-verhindert-tor/
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?lustich\.de/videos/(.*)\">isU',$message))
	{
		$pattern = "<http://lustich.de/videos/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://lustich.de/videos/$url");
			//Use cURL and find the video id
			if (!function_exists('curl_init') || !$c = curl_init())
				return false;
				curl_setopt($c, CURLOPT_URL, $site);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($c, CURLOPT_TIMEOUT, 3);
				$data = utf8_encode(curl_exec($c));
				if (!$data)
					$data = 'not available';
				curl_close($c);

				if($data) {
					$nr = get_avmatch('/<link rel=\"image_src\" href=\"http:\/\/data.lustich.de\/videos\/m\/(.*)-/isU',$data);
					$vid = array($nr);
				}
				$limit = 1;
				foreach ($vid as $id)
				{
					$n = htmlspecialchars_uni($id);
					$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?lustich\.de/videos/(.*?)/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"player\" width=\"$w\" height=\"$h\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"><param value=\"true\" name=\"allowfullscreen\"/><param value=\"always\" name=\"allowscriptaccess\"/><param value=\"high\" name=\"quality\"/><param value=\"true\" name=\"cachebusting\"/><param value=\"#000000\" name=\"bgcolor\"/><param name=\"movie\" value=\"http://lustich.de/player/flowplayer.commercial-3.2.5-0.swf\" /><param value=\"config=http://lustich.de/pec-$n.js\" name=\"flashvars\"/><embed src=\"http://lustich.de/player/flowplayer.commercial-3.2.5-0.swf\" type=\"application/x-shockwave-flash\" width=\"$w\" height=\"$h\" allowfullscreen=\"true\" allowscriptaccess=\"always\" cachebusting=\"true\" flashvars=\"config=http://lustich.de/pec-$n.js\" bgcolor=\"#000000\" quality=\"true\"></embed></object></div>", $message, $limit);
				}
		}
	}
	return $message;
}
?>
