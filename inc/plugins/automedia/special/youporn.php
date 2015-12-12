<?php
###################################
# Plugin AutoMedia 3.0  for MyBB 1.8*#
# (c) 2011-2014 by doylecc    #
# Website: http://mods.mybb.com/profile/14694 #
###################################


// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />
		Please make sure IN_MYBB is defined.");
}

function automedia_youporn($message)
{
	global $mybb, $db, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://www.youporn.com/watch/9347303/blonde-whore-enjoys-three-dicks-at-a-time-telsev/
*/
	if(preg_match('<a href=\"(http://)?www\.youporn\.com/watch/(.*?)\">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)?www\.youporn\.com/watch/([\d]{1,12})/([\w-]+)/(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe src='http://www.youporn.com/embed/$4/$5/' frameborder=0 height='$h' width='$w' scrolling=no name='yp_embed_video'></iframe></div>", $message);
	}
	return $message;
}
