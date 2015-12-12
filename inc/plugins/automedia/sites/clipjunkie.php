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

function automedia_clipjunkie($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "450";
		$h = "350";
	}

/**
 *Example:
 *http://www.clipjunkie.com/Meerkat-Tribal-Council-vid5797.html
 */

  if(preg_match('<a href=\"(http://)?(?:www\.)?clipjunkie\.com/(.*)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?clipjunkie\.com/(.*?)-vid([0-9]{4})\.html(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\" data=\"http://www.clipjunkie.com/flvplayer/flvplayer.swf?flv=http://videos.clipjunkie.com/videos/$3-vid$4.flv&amp;themes=http://www.clipjunkie.com/flvplayer/themes.xml&amp;playList=http://www.clipjunkie.com/playlist.php&amp;config=http://www.clipjunkie.com/skin/config.xml\" type=\"application/x-shockwave-flash\" ><param name=\"movie\" value=\"http://www.clipjunkie.com/flvplayer/flvplayer.swf?flv=http://videos.clipjunkie.com/videos/$3-vid$4.flv&amp;themes=http://www.clipjunkie.com/flvplayer/themes.xml&amp;playList=http://www.clipjunkie.com/playlist.php&amp;config=http://www.clipjunkie.com/skin/config.xml\" /><param name=\"allowFullScreen\" value=\"true\" /><param name=\"autoStart\" value=\"0\" /><param name=\"allowScriptAccess\" value=\"always\" /></object></div>", $message);
  }
	return $message;

}
?>
