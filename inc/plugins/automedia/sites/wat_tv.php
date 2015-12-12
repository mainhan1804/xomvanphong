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

function automedia_wat_tv($message)
{
	global $mybb, $db, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "480";
		$h = "320";
	}

/**
 *Example:
 *http://www.wat.tv/video/the-pretty-reckless-just-tonight-360an_2zicp_.html
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?wat\.tv/video/(.*?)\.html">isU',$message))
	{
		$pattern = "<http://www.wat.tv/video/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.wat.tv/video/$url");
			//Use cURL and find the video id
			if (!function_exists('curl_init') || !$c = curl_init())
				return false;
			curl_setopt($c, CURLOPT_URL, $site);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_TIMEOUT, 3);
			curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0");
			$data = utf8_encode(curl_exec($c));
			if (!$data)
				$data = 'not available';
			curl_close($c);

			if($data) {
				$nrwat = get_avmatch('/<meta property=\"og:video\" content=\"(.*)\"/isU',$data);
				$vid = array($nrwat);
			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = $db->escape_string($id);
				$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?wat\.tv/video/(.*?)\.html(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"480\" height=\"270\"><param name=\"movie\" value=\"$n\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowScriptAccess\" value=\"always\"></param><embed src=\"$n\" type=\"application/x-shockwave-flash\"  allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"480\" height=\"270\"></embed></object></div>", $message, $limit);
			}
		}
	}
	return $message;
}
?>
