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

function automedia_swf($message)
{
	global $mybb, $width, $height;

/**
 *Example:
 *http://www.arcadecabin.com/games/crazy-taxi.swf
*/
	if(preg_match('<a href=\"(http://)?(www.)?(.*)\.swf\">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?([\/\w \.-\? &=]*?)/([\w/ \?=&;%\.-]+\.swf)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object classid=\"CLSID:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://active.macromedia.com/flash2/cabs/swflash.cab#version=4,0,0,0\" width=\"$width\" height=\"$height\"><param name=\"movie\" value=\"$2$3$4/$5\" /><param name=\"menu\" value=\"true\" /><param name=\"autostart\" value=\"0\" /><embed src=\"$2$3$4/$5\" width=\"$width\" height=\"$height\" type=\"application/x-shockwave-flash\" menu=\"false\" autostart=\"false\"></embed></object></div>", $message);
	}
	return $message;
}
