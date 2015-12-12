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

function automedia_photobucket($message)
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
 *http://s171.photobucket.com/albums/s172/yeah/?action=view&current=vows.mp4 or http://s171.photobucket.com/albums/s172/yeah/avatars/?action=view&current=303b5732.pbw
 */

  if(preg_match('<a href=\"(http://)([si](\w{1,5}))\.photobucket\.com/albums/((?:[\%\w-]{1,50}/){1,10})(?:\?[^\"]*?current=)?([\%\w-]{1,50}\.(?:pbr|flv|mp4))\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)([si](\w{1,5}))\.photobucket\.com/albums/((?:[\%\w-]{1,50}/){1,10})(?:\?[^\"]*?current=)?([\%\w-]{1,50}\.(?:pbr|flv|mp4))(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" allowFullscreen=\"true\" allowNetworking=\"all\" wmode=\"transparent\" src=\"http://static.photobucket.com/player.swf\" flashvars=\"file=http://$4.photobucket.com/albums/$5$6\"></div>", $message);
  }
  if(preg_match('<a href=\"(http://)([si](\w{1,5}))\.photobucket\.com/albums/((?:[\%\w-]{1,50}/){1,10})(?:\?[^\"]*?current=)?([\%\w-]{1,50}\.(?:pbw))\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)([si](\w{1,5}))\.photobucket\.com/albums/((?:[\%\w-]{1,50}/){1,10})(?:\?[^\"]*?current=)?([\%\w-]{1,50}\.(?:pbw))(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed type=\"application/x-shockwave-flash\" wmode=\"transparent\" src=\"http://w$4.photobucket.com/pbwidget.swf?pbwurl=http://s$4.photobucket.com/albums/$5$6\" height=\"$h\" width=\"$w\"></div>", $message);
  }
	return $message;

}
?>
