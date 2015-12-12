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

function automedia_flv($message)
{
	global $mybb, $db, $post, $postrow, $pmid, $memprofile, $width, $height;

/**
 *Example:
 *www.gugelproductions.de/blog/wp-content/fltest.flv
*/
	if(preg_match('<a href=\"(http://)?(www.)?(.*)\.flv\">isU',$message))
	{
		if(THIS_SCRIPT=="private.php")
		{
			$priv = intval($pmid); 
			$query  = $db->simple_select("privatemessages", "fromid", "pmid='$priv'");
			$privuid = $db->fetch_array($query);
			$puid = intval($privuid['fromid']);
		}
		else if(THIS_SCRIPT=="usercp.php")
		{
			$puid = intval($mybb->user['uid']);
		}
		else if(THIS_SCRIPT=="member.php")
		{
			$puid = intval($memprofile['uid']);
		}
    else if(THIS_SCRIPT=="printthread.php")
    {
      $puid = intval($postrow['uid']);
    }	
		else
		{
			$puid = intval($post['uid']);
		}
		//Get the posters usergroup
		$permissions = user_permissions($puid);

		switch($mybb->settings['av_flashadmin'])
		{
			case "admin":
			if($permissions['cancp'] == 1) {
				$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?(.*)/([\w/ &;%\.-]+\.flv)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"flowplayer\" width=\"$width\" height=\"$height\" data=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.7.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.7.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":{\"url\":\"$2$3$4/$5\",\"autoPlay\":false}}' /></object></div>", $message);
			}
			break;
			case "mods":
			if($permissions['cancp'] == 1 || $permissions['canmodcp'] == 1)	{
				$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?(.*)/([\w/ &;%\.-]+\.flv)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"flowplayer\" width=\"$width\" height=\"$height\" data=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.7.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.7.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":{\"url\":\"$2$3$4/$5\",\"autoPlay\":false}}' /></object></div>", $message);
			}
			break;
			case "all":
				$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?(.*)/([\w/ &;%\.-]+\.flv)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"flowplayer\" width=\"$width\" height=\"$height\" data=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.7.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.7.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":{\"url\":\"$2$3$4/$5\",\"autoPlay\":false}}' /></object></div>", $message);
			break;
		}
	}
	return $message;
}
?>
