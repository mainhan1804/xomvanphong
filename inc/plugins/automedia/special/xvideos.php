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

function automedia_xvideos($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://www.xvideos.com/video221033/daisy_marie_is_so_cute
*/
  if(preg_match('<a href=\"(http://)(?:www\.)?xvideos\.com/video(?:\d{1,12})/(.*?)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?xvideos\.com/video([0-9]{1,12})/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", '<div class=\'am_embed\'><iframe src="http://flashservice.xvideos.com/embedframe/$4" frameborder=0 width=510 height=400 scrolling=no></iframe></div>', $message);
  }
	return $message;
}
