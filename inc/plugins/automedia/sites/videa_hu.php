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

function automedia_videa_hu($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "448";
		$h = "366";
	}

/**
 *Example:
 *http://videa.hu/videok/sport/forma-1-2008-brazil-nagydij-alonso-elozes-eso-vQjWxcm8oYBLh68a
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?videa\.hu/videok/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?videa\.hu/videok/(.*?)-(\w{16}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://videa.hu/flvplayer.swf?v=$5\" /><param name=\"allowscriptaccess\" value=\"always\" /><param name=\"allowFullScreen\" value=\"true\" /><embed width=\"$w\" height=\"$h\" src=\"http://videa.hu/flvplayer.swf?v=$5\" allowscriptaccess=\"always\" allowfullscreen=\"true\" type=\"application/x-shockwave-flash\" /></object></div>", $message);
  }
	return $message;
}
?>
