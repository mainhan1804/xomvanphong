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

function automedia_godtube($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "640";
		$h = "390";
	}

/**
 *Example:
 *http://http://www.godtube.com/watch/?v=F9FC1FNU
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?godtube\.com/watch/\?v=([A-Za-z0-9]+)">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)?(www.)?godtube\.com/watch/\?v=([A-Za-z0-9]+)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object height=\"$h\" width=\"$w\" type=\"application/x-shockwave-flash\" data=\"http://media.salemwebnetwork.com/godtube/resource/mediaplayer/5.6/player.swf\"><param name=\"movie\" value=\"http://media.salemwebnetwork.com/godtube/resource/mediaplayer/5.6/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"allowacriptaccess\"value=\"always\" /><param name=\"wmode\" value=\"opaque\" /><param name=\"flashvars\" value=\"file=http://www.godtube.com/resource/mediaplayer/$5.file&amp;image=http://www.godtube.com/resource/mediaplayer/$5.jpg&amp;screencolor=000000&amp;type=video&amp;autostart=false&amp;playonce=true&amp;skin=http://media.salemwebnetwork.com/godtube/resource/mediaplayer/skin/default/videoskin.swf&amp;logo.file=undefinedtheme/default/media/embed-logo.png&amp;logo.link=http://www.godtube.com/watch/?v=$5&amp;logo.position=top-left&amp;logo.hide=false&amp;controlbar.position=over\" /></object></div>", $message);
	}
	return $message;

}
?>
