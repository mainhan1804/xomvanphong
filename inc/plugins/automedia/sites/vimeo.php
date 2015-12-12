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

function automedia_vimeo($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "400";
		$h = "225";
	}

/**
 *Example:
 *http://www.vimeo.com/2464373
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?vimeo\.com/(.*?)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?vimeo\.com/(\d{1,16})(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe src=\"http://player.vimeo.com/video/$3?title=0&amp;byline=0&amp;portrait=0\" width=\"$w\" height=\"$h\" frameborder=\"0\"></iframe></div>", $message);
  }
	return $message;

}
?>
