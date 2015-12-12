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

function automedia_ustream($message)
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
 *http://www.ustream.tv/channel/pix-wpa-01 or http://www.ustream.tv/recorded/10919116 
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?ustream\.tv/recorded/(.*?)">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?ustream\.tv/recorded/(\d{4,12})(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" width=\"$w\" height=\"$h\"><param name=\"flashvars\" value=\loc=%2F&amp;autoplay=false&amp;vid=$3&amp;locale=en_US&amp;hasticket=false&amp;v3=1\"/><param name=\"bgcolor\" value=\"#000000\"/><param name=\"allowfullscreen\" value=\"true\"/><param name=\"allowscriptaccess\" value=\"always\"/><param name=\"src\" value=\"http://www.ustream.tv/flash/viewer.swf\" /><embed flashvars=\"loc=%2F&amp;autoplay=false&amp;vid=$3&amp;locale=en_US&amp;hasticket=false&amp;v3=1\" width=\"$w\" height=\"$h\" bgcolor=\"#000000\" allowfullscreen=\"true\" allowscriptaccess=\"always\" src=\"http://www.ustream.tv/flash/viewer.swf\" type=\"application/x-shockwave-flash\" /></object></div>", $message);
	}

	if(preg_match('<a href=\"(http://)(?:www\.)?ustream\.tv/channel/(.*?)">isU',$message))
	{
		$pattern = "<http://www.ustream.tv/channel/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.ustream.tv/channel/$url");
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
				$nrustream = get_avmatch('/cid=([0-9]{1,12}?)/isU',$data);
				$vid = array($nrustream);
			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = $db->escape_string($id);
				$message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?ustream\.tv/channel/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" width=\"$w\" height=\"$h\"><param name=\"flashvars\" value=\"autoplay=false&amp;brand=embed&amp;cid=$n&amp;v3=1\"/><param name=\"bgcolor\" value=\"#000000\"/><param name=\"allowfullscreen\" value=\"true\"/><param name=\"allowscriptaccess\" value=\"always\"/><param name=\"src\" value=\"http://www.ustream.tv/flash/viewer.swf\"/><embed flashvars=\"autoplay=false&amp;brand=embed&amp;cid=$n&amp;v3=1\" width=\"$w\" height=\"$h\" bgcolor=\"#000000\" allowfullscreen=\"true\" allowscriptaccess=\"always\" src=\"http://www.ustream.tv/flash/viewer.swf\" type=\"application/x-shockwave-flash\" /></object></div>", $message, $limit);
			}
		}
	}
	return $message;
}
?>
