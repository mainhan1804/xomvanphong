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

function automedia_clipshack($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "430";
		$h = "370";
	}

/**
 *Example:
 *http://www.clipshack.com/Clip.aspx?key=78303BB426C9EF24
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?clipshack\.com/Clip\.aspx\?key=(.*?)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?clipshack\.com/Clip\.aspx\?key=([0-9a-f]{12,20})(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object type=\"application/x-shockwave-flash\" data=\"http://www.clipshack.com/player.swf?key=$3\" width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://www.clipshack.com/player.swf?key=$3\" /></object></div>", $message);
  }
	return $message;
}
?>
