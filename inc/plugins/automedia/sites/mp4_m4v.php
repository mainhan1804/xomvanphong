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

function automedia_mp4_m4v($message)
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
 *http://spacem.at/movie/audio/podcast-2006-08-03-68914.m4v or http://medien.wdr.de/m/1251018000/maus/wdr_fernsehen_die_maus_20090823.mp4
 */

  if(preg_match('<a href=\"(http://)?(www.)?(.*)\.(?:mp4|m4v)(.*?)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?(.*)/([\w/ &;%\.-]+\.(?:mp4|m4v))(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"flowplayer\" width=\"$w\" height=\"$h\" data=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.7.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.7.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":{\"url\":\"$2$3$4/$5\",\"autoPlay\":false}}' /></object></div>", $message);
  }
	return $message;
}
?>
