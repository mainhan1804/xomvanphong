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

function automedia_bbc_iplayer($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "640";
		$h = "395";
	}

/**
 *Example:
 *http://www.bbc.co.uk/iplayer/episode/b007jpl9/Death_on_the_Nile_Episode_2/
 */

  if(preg_match('<a href=\"(http://)?(?:www\.)?bbc\.co\.uk/iplayer/(?:page/item|episode)/(.*)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?bbc\.co\.uk/iplayer/(?:page/item|episode)/([a-z0-9]{8})/(\w*?)/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed type=\"application/x-shockwave-flash\" src=\"http://www.bbc.co.uk/emp/9player.swf?revision=10344_10570\" style=\"\" id=\"bbc_emp_embed_bip-play-emp\" name=\"bbc_emp_embed_bip-play-emp\" bgcolor=\"#000000\" quality=\"high\" wmode=\"default\" allowfullscreen=\"true\" allowscriptaccess=\"always\" flashvars=\"embedReferer=&amp;embedPageUrl=http://www.bbc.co.uk/iplayer/episode/$3/$4/?t=00m01s&amp;domId=bip-play-emp&amp;config=http://www.bbc.co.uk/emp/iplayer/config.xml&amp;playlist=http://www.bbc.co.uk/iplayer/playlist/$3&amp;holdingImage=http://node2.bbcimg.co.uk/iplayer/images/episode/$3_640_360.jpg&amp;config_settings_bitrateFloor=0&amp;config_settings_bitrateCeiling=2500&amp;config_settings_transportHeight=35&amp;config_settings_cueItem=b00ldy1k:875&amp;config_settings_showPopoutCta=false&amp;config_messages_diagnosticsMessageBody=Insufficient bandwidth to stream this programme. Try downloading instead, or see our diagnostics page.&config_settings_language=en&guidance=unset\" width=\"$w\" height=\"$h\" /></div>", $message);
  }
	return $message;
}
?>
