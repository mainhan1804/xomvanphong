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

function automedia_trtube($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "425";
		$h = "350";
	}

/**
 *Example:
 *http://www.trtube.com/-ayna-sen-unutma-beni--97383.html
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?trtube\.com/(.*)(\.html)?\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?trtube\.com/(.*?)-([0-9]{1,8})(\.html)?(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"allowScriptAccess\" value=\"always\" /><param name=\"movie\" value=\"http://www.trtube.com/player/player.swf?file=http://www.trtube.com/video/$4-$5.flv&amp;skin=http://www.trtube.com/player/beelden.zip&amp;image=http://www.trtube.com/resim/$4-$5.jpg\" /><embed src=\"http://www.trtube.com/player/player.swf?file=http://www.trtube.com/video/$4-$5.flv&amp;image=&amp;stretching=exactfit&amp;frontcolor=ffffff&amp;lightcolor=cc9900&amp;screencolor=ffffff&amp;autostart=false&amp;smoothing=true&amp;skin=http://www.trtube.com/player/beelden.zip&amp;image=http://www.trtube.com/resim/$4-$5.jpg\"	type=\"application/x-shockwave-flash\" height=\"$h\" width=\"$w\"></embed></object></div>", $message);
  }
	return $message;
}
?>
