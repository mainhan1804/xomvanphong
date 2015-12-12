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

function automedia_blog_tv($message)
{
	global $mybb, $db, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "445";
		$h = "374";
	}

/**
 *Examples:
 *http://www.blogtv.com/Shows/1059925/Ze_vYeNGbWFEZu7xYe7&pos=ancr or http://www.blogtv.com/People/DennyMarco or http://www.blogtv.com/channel/Music/most_viewed/views/Zu_HZePDaePvZ23tZP&pos=ancr
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?blogtv\.com/Shows/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?blogtv\.com/Shows/(\d*)/(\w*)(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed width=\"$w\" height=\"$h\" src=\"http://www.blogtv.com/vb/$5\" type=\"application/x-shockwave-flash\" allowFullScreen=\"true\"></embed></div>", $message);
  }

  if(preg_match('<a href=\"(http://)(?:www\.)?blogtv\.com/channel/(.*?)">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?blogtv\.com/channel/(.*?)/(\w*)(&amp;pos=ancr)?(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed width=\"$w\" height=\"$h\" src=\"http://www.blogtv.com/vb/$5\" type=\"application/x-shockwave-flash\" allowFullScreen=\"true\"></embed></div>", $message);
  }


  if(preg_match('<a href=\"(http://)(?:www\.)?blogtv\.com/People/(.*?)">isU',$message))
	{
		$pattern = "<http://www.blogtv.com/People/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.blogtv.com/People/$url");
			//Use cURL and find the video id
			if (!function_exists('curl_init') || !$c = curl_init())
				return false;
			curl_setopt($c, CURLOPT_URL, $site);
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_TIMEOUT, 3);
			curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/5.0");
			$data = utf8_encode(curl_exec($c));
			if (!$data)
				$data = 'not available';
			curl_close($c);

			if($data) {
				$nrwat = get_avmatch('/http:\/\/www.blogtv.com\/livesdk\/(.*)\"/isU',$data);
				$vid = array($nrwat);
			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = $db->escape_string($id);
				$message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?blogtv\.com/People/(\w*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed width=\"$w\" height=\"$h\" src=\"http://www.blogtv.com/livesdk/$n\" type=\"application/x-shockwave-flash\" allowfullscreen=\"true\"></embed></div>", $message, $limit);
			}
		}
	}
	return $message;
}
?>
