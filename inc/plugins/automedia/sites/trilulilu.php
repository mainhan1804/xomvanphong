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

function automedia_trilulilu($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "448";
		$h = "386";
	}

/**
 *Example:
 *http://www.trilulilu.ro/ronaldo22/2b370258cd84e9
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?trilulilu\.ro/(.*)/([0-9a-f]*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?trilulilu\.ro/(.{3,50}?)/([0-9a-f]*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://embed.trilulilu.ro/video/$4/$5.swf\" /><param name=\"allowFullScreen\" value=\"true\" /><param name=\"allowscriptaccess\" value=\"always\" /><embed src=\"http://embed.trilulilu.ro/video/$4/$5.swf\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"$w\" height=\"$h\"></embed></object></div>", $message);
  }
	return $message;
}
?>
