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

function automedia_twitvid($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "425";
		$h = "344";
	}

/**
 *Example:  
 *http://www.twitvid.com/BBF3D
 */


  if(preg_match('<a href=\"(http://)?(www.)?twitvid\.com/(.*)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?twitvid\.com/)(.{5}?)((.*?)\" target=\"_blank\">)?((.*?)\[/automedia\]|(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://www.twitvid.com/player/$4\" /><param name=\"allowscriptaccess\" value=\"always\" /><param name=\"allowFullScreen\" value=\"true\" /><embed type=\"application/x-shockwave-flash\" src=\"http://www.twitvid.com/player/$4\" quality=\"high\" allowscriptaccess=\"always\" allowNetworking=\"all\" allowfullscreen=\"true\" wmode=\"transparent\" height=\"$h\" width=\"$w\"></embed></object></div>", $message);
  }
	return $message;
}
?>
