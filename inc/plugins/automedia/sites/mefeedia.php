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

function automedia_mefeedia($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "560";
		$h = "365";
	}

/**
 *Example:
 *http://www.mefeedia.com/video/33461653
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?mefeedia\.com/([video|tv|game|music|movie|news|watch]+)/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?mefeedia\.com/([video|tv|game|music|movie|news|watch]+)/([0-9]{1,12}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe scrolling=\"no\" frameborder=\"0\" width=\"$w\" height=\"$h\" src=\"http://www.mefeedia.com/$4/$5?iframe=1&amp;w=$w&amp;h=$h&amp;autoplay=0' allowfullscreen\"></iframe></div>", $message);
  }
	return $message;
}
?>
