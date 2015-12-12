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

function automedia_clipmoon($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "460";
		$h = "357";
	}

/**
 *Example:
 *http://www.clipmoon.com/videos/2190311/lazy-cats.html
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?clipmoon\.com/videos/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?clipmoon\.com/videos/([0-9a-f]{1,10}?)/(.*?)\.html(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed src=\"http://www.clipmoon.com/flvplayer.swf\" FlashVars=\"config=http://www.clipmoon.com/flvplayer.php?viewkey=$4&amp;external=no\" quality=\"high\" bgcolor=\"#000000\" wmode=\"transparent\" width=\"$w\" height=\"$h\" loop=\"false\" align=\"middle\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"  scale=\"exactfit\"></embed></div>", $message);
  } 
	return $message;
}
?>
