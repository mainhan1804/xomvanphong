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

function automedia_liveleak($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "450";
		$h = "370";
	}

/**
 *Example:
 *http://www.liveleak.com/view?i=6b0_1264510631
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?liveleak\.com/view(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?liveleak\.com/view\?i=(\w{1,25}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://www.liveleak.com/e/$4\" /><param name=\"wmode\" value=\"transparent\" /><param name=\"allowscriptaccess\" value=\"always\" /><embed src=\"http://www.liveleak.com/e/$4\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" allowscriptaccess=\"always\" width=\"$w\" height=\"$h\"></embed></object></div>", $message);
  } 
	return $message;
}
?>
