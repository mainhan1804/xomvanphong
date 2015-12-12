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

function automedia_bofunk($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "446";
		$h = "370";
	}

/**
 *Example:
 *http://www.bofunk.com/video/9966/chick_gives_boyfriend_a_snow_job.html
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?bofunk\.com/[^\"]*?\w+\.html\">isU',$message))
	{
		$pattern = "<http://www.bofunk.com/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.bofunk.com/$url");
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
				$nrbofunk = get_avmatch('/<embed src=\"http:\/\/www\.bofunk\.com\/e\/(.*)\" quality=\"high\" /isU',$data);
				$vid = array($nrbofunk);
			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = htmlspecialchars_uni($id);
				$message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?bofunk\.com/video/(.*?)\.html(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed src=\"http://www.bofunk.com/e/$n\" quality=\"high\" bgcolor=\"#000000\" width=\"$w\" height=\"$h\" name=\"flvplayer\" align=\"middle\" allowFullScreen=\"true\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed></div>", $message, $limit);
			}
		}
	}
	return $message;
}
?>
