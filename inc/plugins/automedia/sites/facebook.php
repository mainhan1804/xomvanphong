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

function automedia_facebook($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "480";
		$h = "360";
	}

/**
 *Example:
 *http://www.facebook.com/video/video.php?v=102771843083725
 */

  if(preg_match('<a href=\"(http://)?(www.)?facebook\.com/video/(.*?)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?facebook\.com/video/video\.php\?v=([0-9a-z]{7,18})(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width:=\"$w\" height=\"$h\"><param name=\"allowFullscreen\" value=\"true\" /><param name=\"movie\" value=\"http://www.facebook.com/v/$4\" /><embed src=\"http://www.facebook.com/v/$4\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\" width=\"$w\" height=\"$h\"></embed></object></div>", $message);
  }
	return $message;
}
?>
