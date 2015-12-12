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

function automedia_gamecreds($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "640";
		$h = "360";
	}

/**
 *Example:
 *http://www.gamecreds.com/video/pimp-map-part-F/QMk1z
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?gamecreds\.com/video/(.*?)">isU',$message))
  {
		$pattern = "<http://www.gamecreds.com/video/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.gamecreds.com/video/$url");
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
				$nrg = get_avmatch('/<param name=\'FlashVars\' value=\'vidID=(.*)%26vidID2=/isU',$data);
				$vid = array($nrg);
			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = htmlspecialchars_uni($id);
        $message = preg_replace("#(\[automedia\]|(<a href=\")?(http://)(?:www\.)?gamecreds\.com/video/([\w|\-]+)/(\w*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://st1.gamecreds.com/images/swf/player-embed.swf\"></param><param name=\"FlashVars\" value=\"vidID=$n&amp;vidID2=F/$5&amp;uVid=http://www.gamecreds.com/video/$4/$5&amp;cUrl0=http://www.gamecreds.com/&amp;uImg=http://st3.gamecreds.com/images2/videos/F/$5.jpg\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"bgcolor\" value=\"#000000\"></param><embed type=\"application/x-shockwave-flash\" src=\"http://st1.gamecreds.com/images/swf/player-embed.swf\" FlashVars=\"vidID=$n&amp:vidID2=F/$5&amp;uVid=http://www.gamecreds.com/video/$4/$5&amp;cUrl0=http://www.gamecreds.com/&amp;uImg=http://st3.gamecreds.com/images2/videos/F/$5.jpg\" width=\"$w\" height=\"$h\" allowfullscreen=\"true\" bgcolor=\"#000000\"></embed></object></div>", $message, $limit);
      } 
    }
  }
	return $message;
}
?>
