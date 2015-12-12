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

function automedia_apple_trailers($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "640";
		$h = "380";
	}

/**
 *Example:
 *http://trailers.apple.com/trailers/summit/thethreemusketeers/ 
 */
/*
	Site as iframe:
	if(preg_match('<a href=\"(http://)(?:trailers\.)?apple\.com/trailers/(.*)">isU',$message))
	{
		$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:trailers\.)?apple\.com/trailers/(.*)(\.html)?(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><iframe src=\"http://trailers.apple.com/trailers/$3\" frameborder=0 width=$w height=$h scrolling=auto></iframe></div>", $message);
	} */


	if(preg_match('<a href=\"(http://)(?:trailers\.)?apple\.com/trailers/(.*)/">isU',$message))
	{
		$pattern = "<http://trailers.apple.com/trailers/(.*)\" target>";
		preg_match_all($pattern, $message, $links);
		$link = $links[1];
		foreach ($link as $url)
		{
			$site = htmlspecialchars_uni("http://trailers.apple.com/trailers/$url/includes/automatic.html");
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
			$data = str_replace('window.location.replace', '', $data);
			if($data) {
				preg_match_all('/includes\/(.*)-automatic\" class=\"/isU',$data,$trailer);
				$vid = $trailer[1];
			}
			$limit = 1;
			if(count($vid) == 1)
			{
         $id = $vid[0];
				 $trailersite = htmlspecialchars_uni("http://trailers.apple.com/trailers/$url/includes/$id-automatic");
				if (!function_exists('curl_init') || !$ch = curl_init())
					return false;
				curl_setopt($ch, CURLOPT_URL, $trailersite);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$tdata = utf8_encode(curl_exec($ch));
				if (!$tdata)
					$tdata = 'not available';
				curl_close($ch);

				if($tdata) {
					$aptr = get_avmatch('/<a class=\"movieLink\" href=\"(.*)\?width=/isU',$tdata,$trailer);
					$apcl = array($aptr);
					foreach($apcl as $clip)
					{
						$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:trailers\.)?apple\.com/trailers/(.*)(\.html)?(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object id=\"ImageWindow\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\"  width=\"$w\" height=\"$h\"> <param name=\"src\" value=\"$clip\" /><param name=\"autostart\" value=\"0\" /><embed src=\"$clip\" type=\"video/quicktime \"width=\"$w\" height=\"$h\" autostart=\"false\"></embed></object></div>", $message, $limit);
					}
				}

      } elseif(count($vid) > 1) {      
			foreach ($vid as $id)
			{
				 $trailersite = htmlspecialchars_uni("http://trailers.apple.com/trailers/$url/includes/$id-automatic");
				if (!function_exists('curl_init') || !$ch = curl_init())
					return false;
				curl_setopt($ch, CURLOPT_URL, $trailersite);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 3);
				$tdata = utf8_encode(curl_exec($ch));
				if (!$tdata)
					$tdata = 'not available';
				curl_close($ch);

				if($tdata) {
					$aptr = get_avmatch('/<a class=\"movieLink\" href=\"(.*)\?width=/isU',$tdata,$trailer);
					$apcl = array($aptr);
					foreach($apcl as $clip)
					{
						$mov = "<div class=\"am_embed\"><object id=\"ImageWindow\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\"  width=\"$w\" height=\"$h\"> <param name=\"src\" value=\"$clip\" /><param name=\"autostart\" value=\"0\" /><embed src=\"$clip\" type=\"video/quicktime \"width=\"$w\" height=\"$h\" autostart=\"false\"></embed></object></div>"; 
						$link = "#(\[automedia\]|<a href=\"(http://)?(?:trailers\.)?apple\.com/trailers/(.*)(\.html)?(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i";
					}
					$message = $message.'<br /><br />'.$mov;
				}
			}
		}
	 }	
	}
	return $message;
}
?>
