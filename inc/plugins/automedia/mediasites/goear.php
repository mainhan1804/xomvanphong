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

function automedia_goear($message)
{
	global $mybb, $width, $height;

	$w = "580";
	$h = "115";

/**
 *Example:
 *http://www.goear.com/listen/dacf88d/pokerface-pokerface
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?goear\.com/listen(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?goear\.com/listen/([0-9a-f]{5,10}?)/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe width=\"$w\" height=\"$h\" src=\"http://www.goear.com/embed/sound/$4\" marginheight=\"0\" align=\"top\" scrolling=\"no\" frameborder=\"0\" hspace=\"0\" vspace=\"0\" allowfullscreen></iframe></div>", $message);
  }
	return $message;
}
