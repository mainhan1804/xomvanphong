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

function automedia_divshare($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "560";
		$h = "400";
	}

/**
 *Example:
 *http://www.divshare.com/download/7714880-d76
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?divshare\.com/download/([^\"]*)\">isU',$message))
	{
		$pattern = "<http://www.divshare.com/download/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.divshare.com/download/$url");
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
				$nrdv = get_avmatch('/video_flash_detector.php\?data=(.*)\" name=\"movie\"/isU',$data);
				$vid = array($nrdv);
				$nrdi = get_avmatch('/ class=\"img_thumb\" id=\"(.{6,40}?)\" border=/isU',$data);
				$img = array($nrdi);
			}
			$limit = 1;
			if($vid)
			{
				foreach ($vid as $video_id)
				{
					if(!in_array("ajaxData_img_thumb", $img))
					{
						$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?divshare\.com/download/(.{6,18}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><div id=\"kadoo_video_container_$3\"><object height=\"$h\" width=\"$w\" id=\"video_detector_$3\"><param value=\"http://divshare.com/flash/video_flash_detector.php?data=$video_id\" name=\"movie\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><param name=\"wmode\" value=\"opaque\"></param><embed wmode=\"opaque\" height=\"$h\" width=\"$w\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" src=\"http://divshare.com/flash/video_flash_detector.php?data=$video_id\"></embed></object></div>", $message, $limit);
					}
				}
			}
			if($img)
			{
				foreach ($img as $image_id)
				{
					if($image_id == "ajaxData_img_thumb")
					{
						$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?divshare\.com/download/(.{6,18}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,18,0\" width=\"$w\" height=\"$h\" id=\"divslide\"><param name=\"movie\" value=\"http://www.divshare.com/flash/slide?myId=$3\" /><param name=\"allowFullScreen\" value=\"true\" /><embed src=\"http://www.divshare.com/flash/slide?myId=$3\" width=\"$h\" height=\"$h\" name=\"divslide\" allowfullscreen=\"true\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed></object></div>", $message, $limit);
					}
				}
			}
		} 
	}
	return $message;
}
?>
