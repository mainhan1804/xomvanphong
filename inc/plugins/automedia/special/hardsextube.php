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

function automedia_hardsextube($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://www.hardsextube.com/video/3431679/doctor-helped-with-her-big-titts
*/
  if(preg_match('<a href=\"(http://)(?:www\.)?hardsextube\.com/video/(.*?)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?hardsextube\.com/video/([0-9]{1,16})/?(.*?)(\W?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe width=\"$w\" height=\"$h\" src=\"//www.hardsextube.com/embed/$4/\" frameborder=\"0\" allowfullscreen></iframe></div>", $message);
  }
	return $message;
}
