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

function automedia_mtv_trailers($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "512";
		$h = "319";
	}

/**
 *Examples:
 *http://www.mtv.com/videos/?vid=430678 or http://www.mtv.com/videos/movie-trailers/430678/jennifers-body.jhtml
 */

  if(preg_match('<a href=\"(http://)?(?:www\.)?mtv\.com/videos/\?vid=(.*)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?mtv\.com/videos/\?vid=(\d{1,12})(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed src=\"http://media.mtvnservices.com/mgid:uma:video:mtv.com:$3\" width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" flashVars=\"configParams=vid%3D$3%26uri%3Dmgid%3Auma%3Avideo%3Amtv.com%3A$3\" allowscriptaccess=\"always\" allowfullscreen=\"true\" base=\".\"></embed></div>", $message);
  }
  if(preg_match('<a href=\"(http://)?(?:www\.)?mtv\.com/videos/movie-trailers/(.*)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?mtv\.com/videos/movie-trailers/(\d{1,12})/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed src=\"http://media.mtvnservices.com/mgid:uma:video:mtv.com:$3\" width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" flashVars=\"configParams=vid%3D$3%26uri%3Dmgid%3Auma%3Avideo%3Amtv.com%3A$3\" allowscriptaccess=\"always\" allowfullscreen=\"true\" base=\".\"></embed></div>", $message);
  }
	return $message;

}
?>
