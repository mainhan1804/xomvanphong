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

function automedia_vh1($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "512";
		$h = "288";
	}

/**
 *Example:
 *http://www.vh1.com/video/evanescence/689706/what-you-want.jhtml#id=1670257
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?vh1\.com/video/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?vh1\.com/video/([\w|\-]+)/(\d{2,8}?)/([\w|\-]+)\.jhtml\#id=(\d{2,10}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed src=\"http://media.mtvnservices.com/mgid:uma:video:vh1.com:$5/cp~id%3D$7%26vid%3D$5%26uri%3Dmgid%3Auma%3Avideo%3Avh1.com%3A$5\" width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" allowFullScreen=\"true\" allowScriptAccess=\"always\" base=\".\"></embed></div>", $message);
  }
	return $message;
}
?>
