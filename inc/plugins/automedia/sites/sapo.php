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

function automedia_sapo($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "400";
		$h = "350";
	}

/**
 *Examples:
 *http://videos.sapo.pt/h3zC1Cvy8Q28vEsBD2YW or http://futebol.videos.sapo.pt/38UEoigpbOyZjyPu4qwn
 */

  if(preg_match('<a href=\"(http://)(?:videos\.)?sapo\.pt/(.*)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:videos\.)?sapo\.pt/(.{1,30}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"allowFullScreen\" value=\"true\" /><param name=\"movie\" value=\"http://rd3.videos.sapo.pt/play?file=http://rd3.videos.sapo.pt/$4/mov/1\" /><embed src=\"http://rd3.videos.sapo.pt/play?file=http://rd3.videos.sapo.pt/$4/mov/1\"	type=\"application/x-shockwave-flash\" allowFullScreen=\"true\" height=\"$h\" width=\"$w\"></embed></object></div>", $message);
  }
  if(preg_match('<a href=\"(http://)(?:futebol\.)(?:videos\.)?sapo\.pt/(.*)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:futebol\.)(?:videos\.)?sapo\.pt/(.{1,30}?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"allowFullScreen\" value=\"true\" /><param name=\"movie\" value=\"http://futebol.videos.sapo.pt/play-bwin?file=http://futebol.videos.sapo.pt/$4/mov/1\" /><embed src=\"http://futebol.videos.sapo.pt/play-bwin?file=http://futebol.videos.sapo.pt/$4/mov/1\"	type=\"application/x-shockwave-flash\" allowFullScreen=\"true\" height=\"$h\" width=\"$w\"></embed></object></div>", $message);
  }
	return $message;
}
?>
