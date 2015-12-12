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

function automedia_tinypic($message)
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
 *http://tinypic.com/r/zjf407/5 or http://tinypic.com/player.php?v=zjf407&s=5
 */

  if(preg_match('<a href=\"(http://)?tinypic\.com/r/(.*?)\">isU', $message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?tinypic\.com/r/(\w{2,10})/(\d{1,2})(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" src=\"http://v$4.tinypic.com/player.swf?file=$3&amp;s=$4\" /></div>", $message);
  }
  if(preg_match('<a href=\"(http://)?tinypic\.com/player\.php(.*?)\">isU', $message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?tinypic\.com/player\.php\?v=(\w{2,10})\&amp;s=(\d{1,2})(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" src=\"http://v$4.tinypic.com/player.swf?file=$3&amp;s=$4\" /></div>", $message);
  }

	return $message;
}
?>
