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

function automedia_funnyordie($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "512";
		$h = "328";
	}

/**
 *Example:
 *http://www.funnyordie.com/videos/25a35c4142/brendan-fraser-hand-clap-remix
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?funnyordie\.(com|co\.uk)/videos/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?funnyordie\.(com|co\.uk)/videos/([0-9a-f]{1,15}?)/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" id=\"ordie_player_$5\"><param name=\"movie\" value=\"http://player.ordienetworks.com/flash/fodplayer.swf\" /><param name=\"flashvars\" value=\"key=$5\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"allowscriptaccess\" value=\"always\" /><embed width=\"$w\" height=\"$h\" flashvars=\"key=$5\" allowfullscreen=\"true\" allowscriptaccess=\"always\" quality=\"high\" src=\"http://player.ordienetworks.com/flash/fodplayer.swf\" name=\"ordie_player_$5\" type=\"application/x-shockwave-flash\"></embed></object></div>", $message);
  } 
	return $message;
}
?>
