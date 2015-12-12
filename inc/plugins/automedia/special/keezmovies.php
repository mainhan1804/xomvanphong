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

function automedia_keezmovies($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://www.keezmovies.com/video/amateur-brunette-blows-hubby-447981
*/
  if(preg_match('<a href=\"(http://)(?:www\.)?keezmovies\.com/video/(.*)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?keezmovies\.com/video/([\w-]+)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<iframe src=\"http://www.keezmovies.com/embed/$4\" frameborder=\"0\" height=\"$h\" width=\"$w\" scrolling=\"no\" name=\"keezmovies_embed_video\"></iframe></div>", $message);
  }
	return $message;
}
