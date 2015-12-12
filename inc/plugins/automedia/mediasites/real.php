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

function automedia_real($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://darfhurl.euro.real.com/darf/?prod=rn_video&amp;filename=p6/RealOne-Europe-20090826-152142-rn_gratis_recording-LeSalondecoiffure-p6EP.rm
 */

  if(preg_match('<a href=\"(http://)?(www.)?(.*)\.(?:ra|rm|ram|rpm|rv|smil)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?([\/\w \.-\? &=]*?)/([\w/ \?=&;%\.-]+\.(?:ra|rm|ram|rpm|rv|smil))(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"RVOCX\" classid=\"clsid:CFCDAA03-8BE4-11CF-B84B-0020AFBBCCFA\" width=\"$w\" height=\"$h\"><param name=\"controls\" value=\"ImageWindow\" /><param name=\"autostart\" value=\"false\" /><param name=\"src\" value=\"$2$3$4/$5\" /><embed src=\"$2$3$4/$5\" type=\"audio/x-pn-realaudio-plugin\" controls=\"ImageWindow\" width=\"$w\" height=\"$h\" autostart=\"false\"></embed></object></div>", $message);
  }
	return $message;
}
