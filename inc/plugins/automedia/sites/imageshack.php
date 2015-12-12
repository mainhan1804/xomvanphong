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

function automedia_imageshack($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "500";
		$h = "360";
	}

/**
 *Example:  
 *http://imageshack.us/clip/my-videos/687/9hz.mp4/
 */

  if(preg_match('<a href=\"(http://)?imageshack\.us/clip/(.*?)\.mp4/\">isU', $message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?\imageshack\.us/clip/my-videos/(\d{2,5}?)/(.{2,5}?)\.mp4/(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed src=\"http://img$3.imageshack.us/flvplayer.swf?f=P$4\" width=\"$w\" height=\"$h\" allowFullScreen=\"true\" wmode=\"transparent\" type=\"application/x-shockwave-flash\"/></div>", $message);
  }
	return $message;
}
?>
