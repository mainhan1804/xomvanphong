<?php

###################################
# Plugin AutoMedia 3.0  for MyBB 1.6.*#
# (c) 2011-2014 by doylecc    #
# Website: http://mods.mybb.com/profile/14694 #
###################################

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />
	Please make sure IN_MYBB is defined.");
}

function automedia_mp4_m4v($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;


/**
 *Example:
 *http://spacem.at/movie/audio/podcast-2006-08-03-68914.m4v or http://medien.wdr.de/m/1251018000/maus/wdr_fernsehen_die_maus_20090823.mp4
 */

	if (preg_match('<a href=\"(http://)?(www.)?(.*)\.(?:mp4|m4v|mp4v|webm)(.*?)\">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?([\/\w \.-\? &=]*?)/([\w/ \?=&;%\.-]+\.)((?:mp4|m4v|mp4v|webm))(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\">
			<video controls=\"controls\" width=\"$w\" height=\"$h\">
				<source src=\"$2$3$4/$5$6\" type=\"video/$6\" />
				<object type=\"application/x-shockwave-flash\" data=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.18.swf\" width=\"$w\" height=\"$h\">
					<param name=\"movie\" value=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.18.swf\" />
					<param name=\"allowFullScreen\" value=\"true\" />
					<param name=\"wmode\" value=\"transparent\" />
					<param name=\"flashVars\" value=\"config={'playlist':[{'url':'$2$3$4/$5','autoPlay':false}]}\" />
				</object>
			</video>
		</div>", $message);
	}
	if (preg_match('<a href=\"(http://)?(www.)?(.*)\.(?:ogv)(.*?)\">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?([\/\w \.-\? &=]*?)/([\w/ \?=&;%\.-]+\.)((?:ogv))(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\">
			<video controls=\"controls\" width=\"$w\" height=\"$h\">
				<source src=\"$2$3$4/$5\" type=\"video/ogg\" />
				<object type=\"application/x-shockwave-flash\" data=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.18.swf\" width=\"$w\" height=\"$h\">
					<param name=\"movie\" value=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.18.swf\" />
					<param name=\"allowFullScreen\" value=\"true\" />
					<param name=\"wmode\" value=\"transparent\" />
					<param name=\"flashVars\" value=\"config={'playlist':[{'url':'$2$3$4/$5$6','autoPlay':false}]}\" />
				</object>
			</video>
		</div>", $message);
	}
	return $message;
}
