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

function automedia_mtv_music($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "512";
		$h = "319";
	}

/**
 *Example:
 *http://www.mtv.com/videos/portugal-the-man/666251/got-it-all.jhtml#id=1518072  or http://www.mtv.de/videos/20653889-lady-gaga-bad-romance.html
 */

  if(preg_match('<a href=\"(http://)?(?:www\.)?mtv\.de/videos/(.*)\">isU',$message))
  {
		$pattern = "<http://www.mtv.de/videos/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.mtv.de/videos/$url");
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
					$nr = get_avmatch('/new SWFObject\(\"http:\/\/media.mtvnservices.com\/mgid:uma:video:mtv.de:([0-9]{1,10})\"/isU',$data);
					$vid = array($nr);
				}
				$limit = 1;
				foreach ($vid as $id)
				{
					$n = htmlspecialchars_uni($id);
          $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?mtv\.de/videos/(\d{1,12})(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed src=\"http://media.mtvnservices.com/mgid:uma:video:mtv.de:$n\" width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" allowFullScreen=\"true\" allowScriptAccess=\"always\" base=\".\" flashVars=\"\"></embed></div>", $message, $limit);
        }
    }
  }
  
  if(preg_match('<a href=\"(http://)?(?:www\.)?mtv\.com/videos/(.*)\">isU',$message))
  {
    $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?mtv\.com/videos/(.*?)/(\d{1,12})/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed src=\"http://media.mtvnservices.com/mgid:uma:video:mtv.com:$4\" width=\"$w\" height=\"$h\" type=\"application/x-shockwave-flash\" flashVars=\"configParams=vid%3D$4%26uri%3Dmgid%3Auma%3Avideo%3Amtv.com%3A$4\" allowscriptaccess=\"always\" allowfullscreen=\"true\" base=\".\"></embed></div>", $message);
  }
	return $message;

}
?>
