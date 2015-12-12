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

function automedia_streetfire($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "670";
		$h = "377";
	}

/**
 *Example:
 *http://www.streetfire.net/video/1500-HP-Camaro-Street-NRE_703469.htm
 */

	if(preg_match('<a href=\"(http://)?(www.)?streetfire\.net\/video\/(.*?)\.htm\">isU',$message))
	{
		$pattern = "<http://www.streetfire.net/video/(.*).htm\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://www.streetfire.net/video/$url.htm");
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
					$nr = get_avmatch('/flashvars=\'video=(.*)\' allowfullscreen=/isU',$data);
					$vid = array($nr);
				}
				$limit = 1;
				foreach ($vid as $id)
				{
					$n = htmlspecialchars_uni($id);
          $message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(www.)?streetfire\.net\/video\/(.*?)([0-9]{6,12})\.htm(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" id=\"SFID016554228495806456\"><param name=\"movie\" value=\"http://www.streetfire.net/flash/SPlayer.swf\" type=\"application/x-shockwave-flash\" /><param name=\"allowScriptAccess\" value=\"always\" /><param name=\"wmode\" value=\"transparent\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"FlashVars\" value=\"video=$n\"/><embed src=\"http://www.streetfire.net/flash/SPlayer.swf\" flashvars=\"video=$n\" allowfullscreen=\"true\" wmode=\"transparent\" width=\"$w\" height=\"$h\" allowscriptaccess=\"always\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" /></object></div>", $message, $limit);
        }
		}
	}
	return $message;
}
?>
