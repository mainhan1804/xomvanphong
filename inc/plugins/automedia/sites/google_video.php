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

function automedia_google_video($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "400";
		$h = "326";
	}

/**
 *Example:
 *http://video.google.com/videoplay?docid=-8368239920596130207
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?video.google.(com|co\.uk)/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?video.google.(com|co\.uk)/videoplay\?docid=)(\W?)(\d{15,20})(\[/automedia\]|\" target=\"_blank\">(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\" id=\"VideoPlayback\" type=\"application/x-shockwave-flash\" data=\"http://video.google.com/googleplayer.swf?docId=$4$5&amp;hl=en\"><param name=\"movie\" value=\"http://video.google.com/googleplayer.swf?docId=$4$5&amp;hl=en\" /></object></div>", $message);
  }
	return $message;

}
?>
