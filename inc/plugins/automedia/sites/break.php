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

function automedia_break($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "464";
		$h = "384";
	}

/**
 *Example:
 *http://www.break.com/index/kid-freaks-out-on-first-roller-coaster-ride.html
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?break\.com/[^\"]*?\w+\.html\">isU',$message))
	{
		$pattern = "<http://www.break.com/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.break.com/$url");
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
				$nrbreak = get_avmatch('/http:\/\/embed\.break\.com\/([0-9]{1,12}?)/isU',$data);
				$vid = array($nrbreak);
			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = htmlspecialchars_uni($id);
				$message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?break\.com/(.*?)\.html(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"><param name=\"flashvars\" value=\"playerversion=12\"></param><param name=\"movie\" value=\"http://embed.break.com/$n/\" /><param name=\"allowScriptAccess\" value=\"always\" /><embed flashvars=\"playerversion=12\" src=\"http://embed.break.com/$n/\" width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" allowScriptAccess=\"always\"></embed></object></div>", $message, $limit);
			}
		}
	}
	return $message;
}
?>
