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

function automedia_pornhub($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://www.pornhub.com/view_video.php?viewkey=395131153
*/
  if(preg_match('<a href=\"(http://)(?:www\.)?pornhub\.com/view_video\.php(.*?)\">isU',$message))
  {
		$message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?pornhub\.com/view_video\.php\?viewkey=([0-9]{1,14})(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe src=\"http://www.pornhub.com/embed/$3\" frameborder=\"0\" width=\"$w\" height=\"$h\" scrolling=\"no\"></iframe></div>", $message);
  }
	return $message;
}
