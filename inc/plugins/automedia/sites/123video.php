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

function automedia_123video($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "420";
		$h = "339";
	}

/**
 *Example:
 *http://www.123video.nl/playvideos.asp?MovieID=614229
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?123video\.nl/playvideos(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?123video\.nl/playvideos\.asp\?MovieID=)(\d{2,8})((.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://www.123video.nl/123video_share.swf?mediaSrc=$4\" /><param name=\"quality\" value=\"high\" /><embed src=\"http://www.123video.nl/123video_share.swf?mediaSrc=$4\" quality=\"high\" width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" /></object></div>", $message);
  }
	return $message;
}
?>
