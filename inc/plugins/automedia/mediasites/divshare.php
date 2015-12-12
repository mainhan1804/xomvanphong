<?php

###################################
# Plugin AutoMedia 3.0  for MyBB 1.8.*#
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

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://www.divshare.com/download/7714880-d76
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?divshare\.com/download/([^\"]*)\">isU',$message))
	{
		$pattern = "<http://www.divshare.com/download/([-\w]+)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.divshare.com/download/$url");
			//Find the video id
			$data = utf8_encode(fetch_remote_file($site));

			if($data) {
				$nrdv = get_avmatch('/data=([-\w =]*)&/isU', $data);
				$vid = array($nrdv);
				$nrdi = get_avmatch('/ class=\"img_thumb\" id=\"([-\w =]{6,40}?)\" border=/isU',$data);
				$img = array($nrdi);
			}
			$limit = 1;
			if($vid)
			{
				foreach ($vid as $video_id)
				{
					if(!in_array("ajaxData_img_thumb", $img))
					{
						$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?divshare\.com/download/([-\w]{6,18}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><div id=\"kadoo_video_container_$3\"><object height=\"$h\" width=\"$w\" id=\"video_detector_$3\"><param value=\"http://divshare.com/flash/video_flash_detector.php?data=$video_id&amp;autoplay=default&amp;id=$3\" name=\"movie\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><param name=\"wmode\" value=\"opaque\"></param><embed wmode=\"opaque\" height=\"$h\" width=\"$w\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" src=\"http://divshare.com/flash/video_flash_detector.php?data=$video_id&amp;autoplay=default&amp;id=$3\"></embed></object></div>", $message, $limit);
					}
				}
			}
			if($img)
			{
				foreach ($img as $image_id)
				{
					if($image_id == "ajaxData_img_thumb")
					{
						$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?divshare\.com/download/([-\w]{6,18}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,18,0\" width=\"$w\" height=\"$h\" id=\"divslide\"><param name=\"movie\" value=\"http://www.divshare.com/flash/slide?myId=$3\" /><param name=\"allowFullScreen\" value=\"true\" /><embed src=\"http://www.divshare.com/flash/slide?myId=$3\" width=\"$h\" height=\"$h\" name=\"divslide\" allowfullscreen=\"true\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed></object></div>", $message, $limit);
					}
				}
			}
		}
	}
	return $message;
}
