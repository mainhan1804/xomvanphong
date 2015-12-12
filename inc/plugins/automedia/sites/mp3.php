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

function automedia_mp3($message)
{
	global $mybb;

/**
 * Flash Mp3 Player
 * http://flash-mp3-player.net/players/
 *
 * Example:
 * http://www.birding.dk/galleri/stemmermp3/Luscinia%20megarhynchos%201.mp3
 * http://flash-mp3-player.net/medias/another_world.mp3
 */

	//If we have a playlist
	if(preg_match('<\[ampl\](.*?)\[\/ampl\]>isU',$message))
	{
		//Add separator
		$message = str_replace(array('.mp3http', '.mp3,http'), '.mp3|http', $message);
	}
	//Build and embed playlist
	$message = preg_replace("#\[ampl\]([^<>\"']+?)\[/ampl\]#i", "<div class=\"am_embed\"><object type=\"application/x-shockwave-flash\" data=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/player_mp3_multi.swf\" width=\"300\" height=\"100\"><param name=\"movie\" value=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/player_mp3_multi.swf\" /><param name=\"bgcolor\" value=\"#ffffff\" /><param name=\"FlashVars\" value=\"mp3=$1&amp;width=300&amp;height=100&amp;showvolume=1&amp;showinfo=1\" /></object></div>", $message);

	//If we have single mp3 file
	if(preg_match('<a href=\"(http://)?(www.)?(.*)\.mp3\">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?(.*)\.(.*)/([\w/ &;%\.-]+\.mp3)(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#iU", "<div class=\"am_embed\"><object type=\"application/x-shockwave-flash\" data=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/player_mp3_maxi.swf\" width=\"300\" height=\"30\"><param name=\"movie\" value=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/player_mp3_maxi.swf\" /><param name=\"bgcolor\" value=\"#ffffff\" /><param name=\"FlashVars\" value=\"mp3=$2$3$4.$5/$6&amp;width=300&amp;height=30&amp;showstop=1&amp;showinfo=1&amp;showvolume=1\" /></object></div>", $message);
	}

	return $message;
}
?>
