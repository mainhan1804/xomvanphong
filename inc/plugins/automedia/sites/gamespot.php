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

function automedia_gamespot($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "460";
		$h = "391";
	}

/**
 *Example:
 *http://www.gamespot.com/xbox360/driving/forzamotorsport3/video/6237914/forza-motorsport-3-video-review
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?gamespot\.com/(.*?)/video/([0-9]{1,9}?)/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?gamespot\.com/(.*?)/video/([0-9]{1,9}?)/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed id=\"mymovie\" width=\"$w\" height=\"$h\" flashvars=\"playerMode=embedded&amp;movieAspect=4.3&amp;flavor=EmbeddedPlayerVersion&amp;skin=http://image.com.com/gamespot/images/cne_flash/production/media_player/proteus/one/skins/gamespot.png&amp;paramsURI=http://www.gamespot.com/pages/video_player/xml.php?id=$5&amp;mode=embedded&amp;width=$w&amp;height=$h/\" wmode=\"transparent\" allowscriptaccess=\"always\" quality=\"high\" name=\"mymovie\" style=\"\" src=\"http://image.com.com/gamespot/images/cne_flash/production/media_player/proteus/one/proteus2.swf\" type=\"application/x-shockwave-flash\"></embed></div>", $message);
  }	
	return $message;
}
?>
