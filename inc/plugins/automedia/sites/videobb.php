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

function automedia_videobb($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "640";
		$h = "505";
	}

/**
 *Example:
 *http://www.videobb.com/watch_video.php?v=P0gLCRfBySKB or http://www.videobb.com/video/P0gLCRfBySKB
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?videobb.com/video/([A-Za-z0-9]+)">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)?(www.)?videobb.com/video/([A-Za-z0-9]{3,16})(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"player\" width=\"$w\" height=\"$h\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" ><param name=\"movie\" value=\"http://www.videobb.com/e/$5\" ></param><param name=\"allowFullScreen\" value=\"true\" ></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.videobb.com/e/$5\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"$w\" height=\"$h\"></embed></object></div>", $message);
	}
	if(preg_match('<a href=\"(http://)(?:www\.)?videobb.com/watch_video.php\?v=([A-Za-z0-9]+)">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)?(www.)?videobb.com/watch_video.php\?v=([A-Za-z0-9]{3,16})(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"player\" width=\"$w\" height=\"$h\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" ><param name=\"movie\" value=\"http://www.videobb.com/e/$5\" ></param><param name=\"allowFullScreen\" value=\"true\" ></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.videobb.com/e/$5\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"$w\" height=\"$h\"></embed></object></div>", $message);
	}
	return $message;

}
?>
