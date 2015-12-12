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

function automedia_sevenload($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "500";
		$h = "314";
	}

/**
 *Example:
 *http://de.sevenload.com/videos/ZWyfb8l-Kill-Bill-Teil-1-2-in-einer-Minute
 */

  if(preg_match('<a href=\"(http://)((?:en|tr|de|www)\.)?sevenload.com/(.*?)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)((?:en|tr|de|www))\.?sevenload.com/([0-9a-z\-\/]*?)/(\w{5,10}?)-(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object type=\"application/x-shockwave-flash\" data=\"http://$3.sevenload.com/pl/$5/${w}x${h}/swf\" width=\"$w\" height=\"$h\"><param name=\"allowFullscreen\" value=\"true\" /><param name=\"allowScriptAccess\" value=\"always\" /> <param name=\"movie\" value=\"http://$3.sevenload.com/pl/$5/${w}x${h}/swf\" /></object></div>", $message);
  }
	return $message;
}
?>
