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

function automedia_myvideo($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "470";
		$h = "406";
	}

/**
 *Example:
 *http://www.myvideo.de/watch/8252167/Nicki_Minaj_Rihanna_Fly
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?myvideo\.de/watch/(.*?)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?myvideo\.de/watch/)(\d{2,12})/(\w*?)((\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" data=\"http://www.myvideo.de/movie/$3/$4\"> <param name=\"movie\" value=\"http://www.myvideo.de/movie/$3/$4\" />	<param name=\"AllowFullscreen\" value=\"false\" /></object></div>", $message);
  }
	return $message;

}
?>
