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

function automedia_dailymotion($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "560";
		$h = "420";
	}

/**
 *Example:
 *http://www.dailymotion.com/video/xa8h5c_dbo-frusty-offizielles-video_music
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?dailymotion.com/(.*?)">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)?(www.)?dailymotion.com/(.*?)/?video/)(.{3,8}?)_((.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe frameborder=\"0\" width=\"$w\" height=\"$h\" src=\"http://www.dailymotion.com/embed/video/$6?width=$w\"></iframe></div>", $message);
	}
	return $message;

}
?>
