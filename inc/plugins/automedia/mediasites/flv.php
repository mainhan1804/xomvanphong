<?php
###################################
# Plugin AutoMedia 3.0  for MyBB 1.8.*#
# (c) 2011-2014 by doylecc    #
# Website: http://mods.mybb.com/profile/14694 #
###################################


// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />
		Please make sure IN_MYBB is defined.");
}

function automedia_flv($message)
{
	global $mybb, $width, $height;

/**
 *Example:
 *www.gugelproductions.de/blog/wp-content/fltest.flv
*/
	if (preg_match('<a href=\"(http://)?(www.)?(.*)\.flv\">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?([\/\w \.-\? &=]*?)/([\w/ \?=&;%\.-]+\.flv)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"flowplayer\" width=\"$width\" height=\"$height\" data=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.18.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.18.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":{\"url\":\"$2$3$4/$5\",\"autoPlay\":false}}' /></object></div>", $message);
	}
	return $message;
}
