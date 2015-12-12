<?php

###################################
# Plugin AutoMedia 3.0  for MyBB 1.8.*#
# (c) 2011-2014 by doylecc    #
# Website: http://mods.mybb.com/profile/14694 #
###################################

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />
	Please make sure IN_MYBB is defined.");
}

function automedia_twitter($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *https://twitter.com/davidlabrava/status/531551695348961280
 */

  if(preg_match('<a href=\"(https?://)(?:www\.)?twitter\.com/([\w\d-_]+)/status/([0-9]+)([\w\d-_\/]*?)" target>i',$message))
  {
		$pattern = "<twitter.com/([\w\d-_]+)/status/([0-9]+)([\w\d-_\/]*?)\" target>";
		preg_match_all($pattern, $message, $links);
		$ids = $links[2];
		foreach ($ids as $id)
		{
			$url ="https://api.twitter.com/1/statuses/oembed.json?id=".urlencode($id);
			$data = json_decode(file_get_contents($url));
			if ($data)
			{
				$message = preg_replace("#(\[automedia\]|(<a href=\")?(https?://)(?:www\.)?twitter\.com/([\w\d-_]+)/status/([0-9]+)(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed twitter_embed\">".$data->html."</div>", $message, 1);
			}
		}
  }
	return $message;
}
