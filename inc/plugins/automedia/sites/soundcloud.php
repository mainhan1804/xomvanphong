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

function automedia_soundcloud($message)
{
	global $mybb, $db, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "480";
		$h = "360";
	}

/**
 *Example:
 *http://soundcloud.com/skreamizm/skream-future-funk-teaser
 */

	if(preg_match('<a href=\"(http://)?soundcloud\.com/(.*?)">isU',$message))
	{
		$pattern = "<http://soundcloud.com/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://soundcloud.com/$url");
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
				$nrsstream = get_avmatch('/data-sc-track=\"(\d{5,12})\">/isU',$data);
				$vid = array($nrsstream);
			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = $db->escape_string($id);
				$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)?soundcloud\.com/([\w|\-]+)/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object height=\"81\" width=\"100%\"> <param name=\"movie\" value=\"http://player.soundcloud.com/player.swf?url=http://api.soundcloud.com/tracks/$n&amp;secret_url=false\"></param> <param name=\"allowscriptaccess\" value=\"always\"></param> <embed allowscriptaccess=\"always\" height=\"81\" src=\"http://player.soundcloud.com/player.swf?url=http://api.soundcloud.com/tracks/$n&amp;secret_url=false\" type=\"application/x-shockwave-flash\" width=\"100%\"></embed> </object></div>", $message, $limit);
			}
		}
	}
	return $message;
}
?>
