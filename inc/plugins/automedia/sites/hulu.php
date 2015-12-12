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

function automedia_hulu($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "512";
		$h = "350";
	}

/**
 *Examples:
 *http://www.hulu.com/watch/277495/miss-universe-evening-gown-competition  or http://www.hulu.com/watch/91202/divorcing-jack
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?hulu\.com/watch/(.*)\">isU',$message))
	{
		$pattern = "<http://www.hulu.com/watch/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.hulu.com/watch/$url");
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
						$nrh = get_avmatch('/<param name=\"movie\" value=\"http:\/\/www\.hulu\.com\/embed\/(.*)\"><\/param>/isU',$data);
						$vid = array($nrh);
					}
					$limit = 1;
					foreach ($vid as $id)
					{
						$n = htmlspecialchars_uni($id);
						$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?hulu\.com/watch/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://hulu.com/embed/$n\" /><param name=\"allowFullScreen\" value=\"true\" /><embed  wmode=\"transparent\" src=\"http://hulu.com/embed/$n\" type=\"application/x-shockwave-flash\" width=\"$w\" height=\"$h\"></embed></object></div>", $message, $limit);
					}
		}
	}
	return $message;
}
?>
