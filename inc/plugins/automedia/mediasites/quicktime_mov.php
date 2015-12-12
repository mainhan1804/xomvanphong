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

function automedia_quicktime_mov($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://movies.apple.com/media/us/ipad/2011/tours/apple-ipad2-feature-us-20110302_r848-9cie.mov
 */

  if(preg_match('<a href=\"(http://)?(www.)?(.*)\.mov\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?([\/\w \.-\? &=]*?)/([\w/ \?=&;%\.-]+\.mov)(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" width=\"$w\" height=\"$h\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"$2$3$4/$5\" /><param name=\"autoplay\" value=\"false\" /><param name=\"controller\" value=\"true\" /><embed src=\"$2$3$4/$5\" width=\"$w\" height=\"$h\" autoplay=\"false\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object></div>", $message);
  }
	return $message;
}
