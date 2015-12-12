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

function automedia_myspace($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "425";
		$h = "360";
	}

/**
 *Example:
 *http://www.myspace.com/video/kavkavstheweb/ready-to-rumpel-kavka-vs-the-web-folge-20/62365647 or http://www.myspace.com/video/vid/62365647
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?myspace\.com/video/(.*?)\">isU',$message))
	{
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.myspace)\.com/video/(.*?)/(\d{1,12})(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\" ><param name=\"allowFullScreen\" value=\"true\"/><param name=\"wmode\" value=\"transparent\"/><param name=\"movie\" value=\"http://mediaservices.myspace.com/services/media/embed.aspx/m=$4,t=1,mt=video\"/><embed src=\"http://mediaservices.myspace.com/services/media/embed.aspx/m=$4,t=1,mt=video\" width=\"$w\" height=\"$h\" allowFullScreen=\"true\" type=\"application/x-shockwave-flash\" wmode=\"transparent\"></embed></object></div>", $message);
  }
	return $message;
}
?>
