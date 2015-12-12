<?php

###################################
# Plugin AutoMedia 3.0  for MyBB 1.8.*#
# (c) 2011-2014 by doylecc    #
# Website: http://mods.mybb.com/profile/14694 #
###################################

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />
	Please make sure IN_MYBB is defined.");
}

function automedia_windows_media($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://www.mediacollege.com/video/format/windows-media/streaming/videofilename.wmv or http://www.sound-emotion.com/sound-emotioncom/wmamusic/baroqueloop90z.wma
 */

  if(preg_match('<a href=\"(http://)?(www.)?(.*)\.wm[va]\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?([\/\w \.-\? &=]*?)/([\w/ \?=&;%\.-]+\.wm[va])(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"ImageWindow\" classid=\"clsid:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" type=\"application/x-oleobject\" width=\"$w\" height=\"$h\"><param name=\"src\" value=\"$2$3$4/$5\" /><param name=\"autostart\" value=\"false\" /><paran name=\"ShowControls\" value=\"true\" \><param name=\"ShowStatusBar\" value=\"false\" /><embed name=\"MediaPlayer\" src=\"$2$3$4/$5\" type=\"application/x-mplayer2\" width=\"$w\" height=\"$h\" autostart=\"false\" ShowControls=\"1\" ShowStatusBar=\"0\"></embed></object></div>", $message);
  }
	return $message;
}

