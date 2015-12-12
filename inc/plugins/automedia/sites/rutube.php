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

function automedia_rutube($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "470";
		$h = "353";
	}

/**
 * Old embed code
 * Example:
 *http://rutube.ru/tracks/2370874.html?v=73ae0fe7d944c85caa4392d062fd9377
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?rutube\.ru/tracks/(.*)">isU',$message))
  {
		$pattern = "<http://rutube.ru/tracks/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://rutube.ru/tracks/$url");
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
				$nrbreak = get_avmatch('/http:\/\/video\.rutube\.ru\/([0-9a-f]{20,50}?)/isU',$data);
				$vid = array($nrbreak);

			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = htmlspecialchars_uni($id);
				$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?rutube\.ru/tracks/([0-9]{3,12}?)\.html(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://video.rutube.ru/$n\" /><param name=\"wmode\" value=\"window\" /><param name=\"allowFullScreen\" value=\"true\" /><embed src=\"http://video.rutube.ru/$n\" type=\"application/x-shockwave-flash\" wmode=\"window\" width=\"$w\" height=\"$h\" allowfullscreen=\"true\"></embed></object></div>", $message, $limit);
			}
		}
	}
/**
 * New Embed Code
 * Example:
 *http://rutube.ru/video/73ae0fe7d944c85caa4392d062fd9377/
 */
	if(preg_match('<a href=\"(http://)(?:www\.)?rutube\.ru/video/(.*)">isU',$message))
	{
		$pattern = "<http://rutube.ru/video/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://rutube.ru/video/$url");
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
				$nrbreak = get_avmatch('/http:\/\/video\.rutube\.ru\/([0-9a-f]{20,50}?)/isU',$data);
				$vid = array($nrbreak);

			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = htmlspecialchars_uni($id);
				$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?rutube\.ru/video/([0-9a-f]{20,50}?)/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://video.rutube.ru/$n\" /><param name=\"wmode\" value=\"window\" /><param name=\"allowFullScreen\" value=\"true\" /><embed src=\"http://video.rutube.ru/$n\" type=\"application/x-shockwave-flash\" wmode=\"window\" width=\"$w\" height=\"$h\" allowfullscreen=\"true\"></embed></object></div>", $message, $limit);
			}
		}
	}
	return $message;
}
?>
