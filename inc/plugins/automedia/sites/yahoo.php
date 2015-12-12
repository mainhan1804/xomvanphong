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

function automedia_yahoo($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "576";
		$h = "324";
	}

/**
 *Example:
 *http://animalvideos.yahoo.com/?vid=26479478&cid=24721185  or http://video.yahoo.com/tlc-25331195/cakeboss-25344983/tool-cake-for-dino-26479086.html
 */

	if(preg_match('<a href=\"(http://)(.*?)?yahoo\.com/\?vid=(.*?)\">isU',$message))
	{
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)(.*?)\.?yahoo\.com/\?vid=([0-9]{6,12})(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://d.yimg.com/nl/cbe/paas/player.swf\"></param><param name=\"flashVars\" value=\"shareUrl=http://$3.yahoo.com/?vid=$4&amp;startScreenCarouselUI=hide&ampvid=$4&amp;\"></param><param name=\"allowfullscreen\" value=\"true\"></param><param name=\"wmode\" value=\"transparent\"></param><embed width=\"$w\" height=\"$h\" allowFullScreen=\"true\" src=\"http://d.yimg.com/nl/cbe/paas/player.swf\" type=\"application/x-shockwave-flash\" flashvars=\"shareUrl=http://$3.yahoo.com/?vid=$4&amp;startScreenCarouselUI=hide&amp;vid=$4&amp;\"></embed></object></div>", $message);
  }
	if(preg_match('<a href=\"(http://)video\.?yahoo\.com/(.*?)\.html\">isU',$message))
	{
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)(video\.)?yahoo\.com/(.*?)-([0-9]{6,12})\.html(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://d.yimg.com/nl/vyc/site/player.swf\"></param><param name=\"wmode\" value=\"opaque\"></param><param node=\"allowFullScreen\" value=\"true\"></param><param name=\"flashVars\" \"value=\"vid=$5&amp;lang=en-US\"></param><embed width=\"$w\" height=\"$h\" allowFullScreen=\"true\" src=\"http://d.yimg.com/nl/vyc/site/player.swf\" type=\"application/x-shockwave-flash\" flashvars=\"vid=$5&amp;autoPlay=false&amp;volume=100&amp;enableFullScreen=1&amp;lang=en-US\"></embed></object></div>", $message);
  }

	return $message;
}
?>
