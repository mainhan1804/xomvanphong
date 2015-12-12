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

function automedia_collegehumor($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "600";
		$h = "338";
	}

/**
 *Example:
 *http://www.collegehumor.com/video/5507133/hardly-working-pony-hole
 */

  if(preg_match('<a href=\"(http://)?(?:www\.)?collegehumor\.com/video(.*)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?collegehumor\.com/video/(\d{1,12})(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"ch$3\" type=\"application/x-shockwave-flash\" data=\"http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id=$3&amp;use_node_id=true&amp;fullscreen=1\" width=\"$w\" height=\"$h\"><param name=\"allowfullscreen\" value=\"true\"/><param name=\"wmode\" value=\"transparent\"/><param name=\"allowScriptAccess\" value=\"always\"/><param name=\"movie\" quality=\"best\" value=\"http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id=$3&amp;use_node_id=true&amp;fullscreen=1\"/><embed src=\"http://www.collegehumor.com/moogaloop/moogaloop.swf?clip_id=$3&amp;use_node_id=true&amp;fullscreen=1\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" width=\"$w\" height=\"$h\" allowScriptAccess=\"always\"></embed></object></div>", $message);
  }
	return $message;

}
?>
