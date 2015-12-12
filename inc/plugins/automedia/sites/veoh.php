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

function automedia_veoh($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "410";
		$h = "341";
	}

/**
 *Example:
 *http://www.veoh.com/watch/v210913612rARtR84
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?veoh.com/watch/(.*)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?veoh\.com/watch/)(\w{8,25})((.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\" id=\"veohFlashPlayer\" name=\"veohFlashPlayer\"><param name=\"movie\" value=\"http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1186&mp;permalinkId=$3&amp;player=videodetailsembedded&amp;videoAutoPlay=0&amp;id=anonymous\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1186&amp;permalinkId=$3&amp;player=videodetailsembedded&amp;videoAutoPlay=0&amp;id=anonymous\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"$w\" height=\"$h\" id=\"veohFlashPlayerEmbed\" name=\"veohFlashPlayerEmbed\"></embed></object></div>", $message);
  }
	return $message;

}
?>
