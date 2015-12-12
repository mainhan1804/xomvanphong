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

function automedia_videozer($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "550";
		$h = "380";
	}

/**
 *Example:
 *http://www.videozer.com/video/wOxYS
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?videozer\.com/video/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?videozer\.com/video/(\w{2,10}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"player\" width=\"$w\" height=\"$h\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" ><param name=\"movie\" value=\"http://www.videozer.com/embed/$4\" ></param><param name=\"allowFullScreen\" value=\"true\" ></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.videozer.com/embed/$4\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"$w\" height=\"$h\"></embed></object></div>", $message);
  }
	return $message;
}
?>
