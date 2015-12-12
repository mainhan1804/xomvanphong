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

function automedia_avi($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "520";
		$h = "400";
	}

/**
 *Example:
 *http://www.hydro-kosmos.de/video/vfrogy.avi
 */

  if(preg_match('<a href=\"(http://)?(www.)?(.*)\.avi\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?(.*)\.(.*)/([\w/ &;%\.-]+\.avi)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object classid=\"clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616\" width=\"$w\" height=\"$h\" codebase=\"http://go.divx.com/plugin/DivXBrowserPlugin.cab\"><param name=\"custommode\" value=\"none\" /><param name=\"autoPlay\" value=\"false\" /><param name=\"src\" value=\"$2$3$4.$5/$6\" /><embed type=\"video/divx\" src=\"$2$3$4.$5/$6\" custommode=\"none\" width=\"$w\" height=\"$h\" autoPlay=\"false\"  pluginspage=\"http://go.divx.com/plugin/download/\"></embed></object><br />No video? <a href=\"http://www.divx.com/software/divx-plus/web-player\" target=\"_blank\">Download</a> the DivX Plus Web Player.</div>", $message);
  }
	return $message;
}
?>
