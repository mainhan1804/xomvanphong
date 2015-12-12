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

function automedia_megavideo($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "640";
		$h = "344";
	}

/**
 *Examples:
 *http://www.megavideo.com/?v=CZRADM47 or http://www.megavideo.com/?d=36FFEO0Q
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?megavideo\.com/(?:\?[^\"]*?v=|v/)(\w{8})\">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?megavideo\.com/(?:\?[^\"]*?v=|v/)(\w{8})(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" data=\"http://www.megavideo.com/v/$3.0.0\"> <param name=\"movie\" value=\"http://www.megavideo.com/v/$3.0.0\" /></object></div>", $message);
	}
	if(preg_match('<a href=\"(http://)(?:www\.)?megavideo\.com/(?:\?[^\"]*?d=|d/)(\w{8})\">isU',$message))
	{
		$pattern = "<http://www.megavideo.com/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.megavideo.com/$url");
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
				$nrmega = get_avmatch('/flashvars\.v = \"(\w{8})\"/isU',$data);
				$vid = array($nrmega);
			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = htmlspecialchars_uni($id);
				$message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?megavideo\.com/(?:\?[^\"]*?d=|d/)(\w{8})(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" data=\"http://www.megavideo.com/v/$n.0.0\"> <param name=\"movie\" value=\"http://www.megavideo.com/v/$n.0.0\" /></object></div>", $message, $limit);
			}
		}
	}
	return $message;
}
?>
