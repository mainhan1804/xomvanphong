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

function automedia_youtube($message)
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
 *Examples:
 *http://www.youtube.com/watch?v=K2oLoBpFmho or http://www.youtube.com/watch?v=cSB2TpeY-2E&feature=related or http://youtu.be/t2EmCBDKlRo
 */

	if(preg_match('<a href=\"(http://)(?:www\.)?youtube.com/watch\?v=(.{11})">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)?(www.)?youtube.com/watch\?(.*?)v=)([\w_-]{11})((\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe width=\"$w\" height=\"$h\" src=\"http://www.youtube.com/embed/$6?wmode=opaque\" frameborder=\"0\" allowfullscreen></iframe></div>", $message);
	}
	if(preg_match('<a href=\"(http://)(?:www\.)?youtu.be/(.*?)">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)?(www.)?youtu.be/)([\w_-]{11}?)((.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe width=\"$w\" height=\"$h\" src=\"http://www.youtube.com/embed/$5?wmode=opaque\" frameborder=\"0\" allowfullscreen></iframe></div>", $message);
	}
	//Playlist
	if(preg_match('<a href=\"(http://)(?:www\.)?youtube.com/watch\?v=(.*?)list=(\w{14,22})(.*?)">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)?(www.)?youtube.com/watch\?(.*?)v=)([\w_-]{11})([&;=\w]*?)&amp;list=(\w{14,22})((.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe width=\"$w\" height=\"$h\" src=\"http://www.youtube.com/embed/$6?list=$8\" frameborder=\"0\" allowfullscreen></iframe></div>", $message);
	}
	if(preg_match('<a href=\"(http://)(?:www\.)?youtube.com/watch\?(.*?)">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)?(www.)?youtube.com/watch\?(.*?)v=)([\w_-]{11})((.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><div class=\"am_embed\"><iframe width=\"$w\" height=\"$h\" src=\"http://www.youtube.com/embed/$6?wmode=opaque\" frameborder=\"0\" allowfullscreen></iframe></div>", $message);
	}

	//HTTPS
	if(preg_match('<a href=\"(https://)(?:www\.)?youtube.com/watch\?v=(.{11})">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(https://)?(www.)?youtube.com/watch\?(.*?)v=)([\w_-]{11})((\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe width=\"$w\" height=\"$h\" src=\"http://www.youtube.com/embed/$6?wmode=opaque\" frameborder=\"0\" allowfullscreen></iframe></div>", $message);
	}
	if(preg_match('<a href=\"(https://)(?:www\.)?youtu.be/(.*?)">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(https://)?(www.)?youtu.be/)([\w_-]{11}?)((.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe width=\"$w\" height=\"$h\" src=\"http://www.youtube.com/embed/$5?wmode=opaque\" frameborder=\"0\" allowfullscreen></iframe></div>", $message);
	}
	if(preg_match('<a href=\"(https://)(?:www\.)?youtube.com/watch\?v=(.*?)list=(\w{14,22})">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(https://)?(www.)?youtube.com/watch\?(.*?)v=)([\w_-]{11})&amp;([&;=\w]*?)&amp;list=(\w{14,22})((.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe width=\"$w\" height=\"$h\" src=\"http://www.youtube.com/embed/$6?list=$8\" frameborder=\"0\" allowfullscreen></iframe></div>", $message);
	}
	if(preg_match('<a href=\"(https://)(?:www\.)?youtube.com/watch\?(.*?)">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(https://)?(www.)?youtube.com/watch\?(.*?)v=)(\w{11})((.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><div class=\"am_embed\"><iframe width=\"$w\" height=\"$h\" src=\"http://www.youtube.com/embed/$6?wmode=opaque\" frameborder=\"0\" allowfullscreen></iframe></div>", $message);
	}
	return $message;

}
?>
