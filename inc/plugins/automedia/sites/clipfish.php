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

function automedia_clipfish($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "464";
		$h = "384";
	}

/**
 *Example:
 *http://www.clipfish.de/video/3122529/millennium-force/
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?clipfish.de(.*)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?clipfish\.de)(.*?)/video/(\d{3,12})/((.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\" width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://www.clipfish.de/cfng/flash/clipfish_player_3.swf?as=0&amp;vid=$4&amp;r=1\" /><param name=\"bgcolor\" value=\"#ffffff\" /><param name=\"allowFullScreen\" value=\"true\" /><embed src=\"http://www.clipfish.de/cfng/flash/clipfish_player_3.swf?as=0&amp;vid=$4&r=1\" quality=\"high\" bgcolor=\"#990000\" width=\"$w\" height=\"$h\" name=\"player\" align=\"middle\" allowFullScreen=\"true\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"></embed></object></div>", $message);
  }
	return $message;

}
?>
