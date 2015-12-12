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

function automedia_porn8($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://www.porn8.com/videos/66145
*/
  if(preg_match('<a href=\"(http://)(?:www\.)?porn8\.com/videos/(.*?)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?porn8\.com/videos/([0-9]{1,15})(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe scrolling=\"no\" width=\"$w\" height=\"$h\" src=\"http://www.porn8.com/videos/embed/$4\" frameborder=\"0\"></iframe></div>", $message);
  }
	return $message;
}
