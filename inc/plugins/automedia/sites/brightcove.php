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

function automedia_brightcove($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "520";
		$h = "420";
	}

/**
 *Example:  
 *http://link.brightcove.com/services/player/bcpid62612523001?bctid=777446479001
 */

  if(preg_match('<a href=\"(http://)?(link.)?brightcove\.com/(.*)\">isU',$message))
  {
   $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(link.)?brightcove\.com/services/(?:link|player)/bcpid(\d+)\?(.*?)bctid=(\d+)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"flashObj\" width=\"$w\" height=\"$h\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,47,0\"><param name=\"movie\" value=\"http://c.brightcove.com/services/viewer/federated_f9?isVid=1\" /><param name=\"bgcolor\" value=\"#FFFFFF\" /><param name=\"flashVars\" value=\"videoId=$6&playerID=$4&domain=embed&dynamicStreaming=true\" /><param name=\"base\" value=\"http://admin.brightcove.com\" /><param name=\"seamlesstabbing\" value=\"false\" /><param name=\"allowFullScreen\" value=\"true\" /><param name=\"swLiveConnect\" value=\"true\" /><param name=\"allowScriptAccess\" value=\"always\" /><embed src=\"http://c.brightcove.com/services/viewer/federated_f9?isVid=1\" bgcolor=\"#FFFFFF\" flashVars=\"videoID=$6&playerID=$4&domain=embed&dynamicStreaming=true\" base=\"http://admin.brightcove.com\" name=\"flashObj\" width=\"$w\" height=\"$h\" seamlesstabbing=\"false\" type=\"application/x-shockwave-flash\" allowFullScreen=\"true\" swLiveConnect=\"true\" allowScriptAccess=\"always\" pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\"></embed></object></div>", $message);
  }
	return $message;
}
?>
