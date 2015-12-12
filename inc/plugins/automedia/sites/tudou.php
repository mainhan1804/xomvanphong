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

function automedia_tudou($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "420";
		$h = "320";
	}

/**
 *Example:
 *http://www.tudou.com/programs/view/Cp9PT_6LGKg/
 */

  if(preg_match('<a href=\"(http://)?(?:www\.)?tudou\.com/(.*)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?tudou\.com/(?:programs/view/|v/)(.{1,12}?)(/?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://www.tudou.com/v/$3\" /><param name=\"allowFullScreen\" value=\"true\" /><param name=\"allowscriptaccess\" value=\"always\" /><param name=\"wmode\" value=\"opaque\" /><embed src=\"http://www.tudou.com/v/$3\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" wmode=\"opaque\" width=\"$w\" height=\"$h\"></embed></object></div>", $message);
  }
	return $message;

}
?>
