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

function automedia_movshare($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "720";
		$h = "420";
	}

/**
 *Example:
 *http://www.movshare.net/video/w2rqkllu4sm97
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?movshare\.net/video/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?movshare\.net/video/(\w{5,18}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe style=\"overflow: hidden; border: 0; width: ${w}px; height: ${h}px\" src=\"http://www.movshare.net/embed/$4/?width=$w&amp;height=$h\" scrolling= \"no\"></iframe></div>", $message);
  }
	return $message;
}
?>
