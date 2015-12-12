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

function automedia_metacafe($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "600";
		$h = "338";
	}

/**
 *Example:
 *http://www.metacafe.com/watch/7120307/thq_asilva_final/  or http://www.metacafe.com/watch/sy-28995355001/beyonce_sweet_dreams_official_music_video/
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?metacafe\.com/watch/(.*?)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?metacafe\.com/watch/(.{2}?)(-?)(\d{3,16})/(\w*?)(/?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\" data=\"http://www.metacafe.com/fplayer/$3$4$5/$6.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"http://www.metacafe.com/fplayer/$3$4$5/$6.swf\" /></object></div>", $message);
  }
	return $message;

}
?>
