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

function automedia_goear($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "470";
		$h = "353";
	}

/**
 *Example:
 *http://www.goear.com/listen/dacf88d/pokerface-pokerface
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?goear\.com/listen(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?goear\.com/listen/([0-9a-f]{5,10}?)/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width= \"353\" height=\"132\"><embed src=\"http://www.goear.com/files/external.swf?file=$4\" type=\"application/x-shockwave-flash\" wmode=\"transparent\" quality=\"high\" width=\"353\" height=\"132\"></embed></object></div>", $message);
  } 
	return $message;
}
?>
