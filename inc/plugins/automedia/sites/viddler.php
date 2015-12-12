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

function automedia_viddler($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "545";
		$h = "339";
	}

/**
 *Example:
 *http://www.viddler.com/explore/engadget/videos/3196/ 
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?viddler\.com/explore/(.*)/videos/(:?\w{1,12})/\">isU',$message))
	{
		$pattern = "<http://www.viddler.com/explore/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.viddler.com/explore/$url");
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
				$nrv = get_avmatch('/http:\/\/www\.viddler\.com\/player\/(.*)\//isU',$data);
				$vid = array($nrv);
			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = htmlspecialchars_uni($id);
				$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?viddler\.com/explore/(.*?)/videos/(?:\w{1,12})/(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" width=\"$w\" height=\"$h\" id=\"viddler_$n\"><param name=\"movie\" value=\"http://www.viddler.com/player/$n/\" /><param name=\"allowScriptAccess\" value=\"always\" /><param name=\"allowFullScreen\" value=\"true\" /><embed src=\"http://www.viddler.com/player/$n/\" width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" allowScriptAccess=\"always\" allowFullScreen=\"true\" name=\"viddler_$n\"></embed></object></div>", $message, $limit);
			}
		}
	}
	return $message;
}
?>
