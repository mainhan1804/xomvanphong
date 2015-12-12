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

function automedia_quicktime_mov($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "500";
		$h = "360";
	}

/**
 *Example:
 *http://movies.apple.com/media/us/ipad/2011/tours/apple-ipad2-feature-us-20110302_r848-9cie.mov
 */

  if(preg_match('<a href=\"(http://)?(www.)?(.*)\.mov\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?(.*)/([\w/ &;%\.-]+\.mov)(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" width=\"$w\" height=\"$h\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"$2$3$4/$5$6\" /><param name=\"autoplay\" value=\"true\" /><param name=\"controller\" value=\"false\" /><embed src=\"$2$3$4/$5$6\" width=\"$w\" height=\"$h\" autoplay=\"true\" controller=\"false\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object></div>", $message);
  }
	return $message;
}
?>
