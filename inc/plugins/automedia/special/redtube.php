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

function automedia_redtube($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://www.redtube.com/12528
*/
	if(preg_match('<a href=\"(http://)(?:www\.)?redtube\.com/(?:\d{1,10})\">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?redtube\.com/([0-9]{1,10})(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe src=\"http://embed.redtube.com/?id=$4&bgcolor=000000\" frameborder=\"0\" width=\"$w\" height=\"$h\" scrolling=\"no\"></iframe></div>", $message);
	}
	return $message;
}
