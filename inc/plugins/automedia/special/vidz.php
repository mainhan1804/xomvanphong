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

function automedia_vidz($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "640";
		$h = "472";
	}

/**
 *Example:
 *http://latex.vidz.com/video/Pink_Ink_scene_1/vidz_porn_videos_blowjob_boots_brunette_doggystyle_gonzo_kissing_latex_long-hair_on-top_pussy-licking_shaved_smalltits_tattoo/?s=5244&n=111&p=-59
*/
  if(preg_match('<a href=\"(http://)?(.*?)\.vidz\.com/video/(.*?)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)?(.*?)\.vidz\.com/video/(.*?)s=([0-9]{1,8})(.*?)(\-?)([0-9]{1,5})(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\" ><param name=\"quality\" value=\"high\" /><param name=\"bgcolor\" value=\"#000000\" /><param name=\"allowScriptAccess\" value=\"always\" /><param name=\"movie\" value=\"http://webdata.vidz.com/demo/swf/FlashPlayerV2.swf\" /><param name=\"allowFullScreen\" value=\"true\" /><param name=\"flashvars\" value=\"id_scene=$6&amp;id_niche=$8$9&amp;type=free\" /><embed src=\"http://webdata.vidz.com/demo/swf/FlashPlayerV2.swf\" allowscriptaccess=\"always\" width=\"$w\" height=\"$h\" menu=\"false\" quality=\"high\" bgcolor=\"#000000\" allowfullscreen=\"true\" flashvars=\"id_scene=$6&amp;id_niche=$8$9&amp;type=free\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" /></object></div>", $message);
  }
	return $message;
}
?>
