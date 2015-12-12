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

function automedia_blib($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "550";
		$h = "339";
	}

/**
 *Example:
 *http://blip.tv/quilty/quilty-blocks-a-go-go-rolling-stone-5469580
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?blip\.tv/(.*)-(\w{1,10})\">isU',$message))
	{
		$pattern = "<http://blip.tv/(.*)-(\w{1,10})\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[2];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://blip.tv/episode/get_share_actions/$url?no_wrap=1");
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
				$nrblip = get_avmatch('/http:\/\/blip.tv\/play\/(.*).html/isU',$data);
				$vid = array($nrblip);
			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = htmlspecialchars_uni($id);
				$message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?blip\.tv/(.*)-(\w{1,10})(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe src=\"http://blip.tv/play/$n.html\" width=\"$w\" height=\"$h\" frameborder=\"0\" allowfullscreen></iframe><embed type=\"application/x-shockwave-flash\" src=\"http://a.blip.tv/api.swf#$n\" style=\"display:none\"></embed></div>", $message, $limit);
			}
		}
	}
	return $message;
}
?>
