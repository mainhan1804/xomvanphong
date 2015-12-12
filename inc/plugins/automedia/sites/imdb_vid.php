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

function automedia_imdb_vid($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "640";
		$h = "480";
	}

/**
 *Example:
 *http://www.imdb.com/video/imdb/vi14943257/
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?imdb\.com/video/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?imdb\.com/video/(\w*?)/vi([0-9]{1,12}?)/(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe src=\"http://www.imdb.com/video/$4/vi$5/player\" width=\"$w\" height=\"$h\" name=\"IMDB Video\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" frameborder=\"0\"></iframe></div>", $message);
  }
	return $message;
}
?>
