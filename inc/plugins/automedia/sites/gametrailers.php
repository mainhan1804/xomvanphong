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

function automedia_gametrailers($message)
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
 *http://www.gametrailers.com/video/nissan-gtr-need-for/55935
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?gametrailers\.com/(.*?)/([0-9]{1,9}?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?gametrailers\.com/(.*?)/([0-9]{1,9}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed src=\"http://media.mtvnservices.com/mgid:moses:video:gametrailers.com:$5\" width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" allowFullScreen=\"true\" allowScriptAccess=\"always\" base=\".\" flashVars=\"\"></embed></div>", $message);
  }
	return $message;
}
?>
