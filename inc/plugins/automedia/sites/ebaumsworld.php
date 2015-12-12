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

function automedia_ebaumsworld($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "567";
		$h = "345";
	}

/**
 *Example:
 *http://www.ebaumsworld.com/video/watch/82078791/
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?ebaumsworld\.com/video/watch/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?ebaumsworld\.com/video/watch/(\d{6,12})/(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed src=\"http://www.ebaumsworld.com/player.swf\" allowScriptAccess=\"always\" flashvars=\"id1=$4\" wmode=\"opaque\" width=\"$w\" height=\"$h\" allowfullscreen=\"true\" /></div>", $message);
  }
	return $message;
}
?>
