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

function automedia_yfrog($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "500";
		$h = "360";
	}

/**
 *Example:  
 *http://yfrog.com/56800z
 */


  if(preg_match('<a href=\"(http://)?yfrog\.com/(.*?)z+\">isU', $message))
  {
		$pattern = "<http://yfrog.com/(.*)z+\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://yfrog.com/${url}z");
			//Use cURL and find the video id
			if (!function_exists('curl_init') || !$c = curl_init())
				return false;
				curl_setopt($c, CURLOPT_URL, $site);
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($c, CURLOPT_TIMEOUT, 3);
				$data = utf8_encode(curl_exec($c));
				if (!$data)
					$data = 'not available';
				curl_close($c);

				if($data) {
					$nr = get_avmatch('/<input class=\"readonly\" value=\"(.*?)\.mp4\" readonly=\"readonly\" \/>/isU',$data);
					$vid = array($nr);
				}
				$limit = 1;
				print_r($vid);
				foreach ($vid as $id)
				{
					$n = htmlspecialchars_uni($id);
          $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?yfrog\.com/(\w{2,10})z+(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"flowplayer\" width=\"$w\" height=\"$h\" data=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.7.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"{$mybb->settings['bburl']}/inc/plugins/automedia/mediaplayer/flowplayer-3.2.7.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":{\"url\":\"$n.mp4\",\"autoPlay\":false}}' /></object></div>", $message, $limit);
        }
	   }
  }
	return $message;
}
?>
