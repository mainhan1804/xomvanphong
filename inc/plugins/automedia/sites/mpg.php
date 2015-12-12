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

function automedia_mpg($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "500";
		$h = "360";
	}

/**
 *Example:  
 *http://www.indymedia.ie/attachments/apr2004/downhillmass.mpg
 */

  if(preg_match('<a href=\"(http://)?(www.)?(.*)\.mpe?g\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?(.*)\.(.*)/([\w/ &;%\.-]+\.mpe?g)(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"ImageWindow\" classid=\"clsid:CLSID:05589FA1-C356-11CE-BF01-00AA0055595A\" width=\"$w\" height=\"$h\"><param name=\"src\" value=\"$2$3$4.$5/$6\" /><param name=\"autostart\" value=\"0\" /><embed src=\"$2$3$4.$5/$6\" type=\"video/mpeg\" width=\"$w\" height=\"$h\" autostart=\"false\"></embed></object></div>", $message);
  }
	return $message;
}
?>
