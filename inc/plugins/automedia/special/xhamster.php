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

function automedia_xhamster($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://xhamster.com/movies/823722/billie_piper_secret_diary_of_a_call_girl_03.html
*/
	if(preg_match('<a href=\"(http://)(?:www\.)?xhamster\.com/movies/(.*?)\">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?xhamster\.com/movies/(\d{1,10}?)/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe width=\"$w\" height=\"$h\" src=\"http://xhamster.com/xembed.php?video=$4\" frameborder=\"0\" scrolling=\"no\"></iframe></div>", $message);
	}
	return $message;
}
