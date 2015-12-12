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

function automedia_traileraddict($message)
{
	global $mybb, $width, $height;

	if($mybb->settings['av_sizeall'] == 1)
	{
		$w = $width;
		$h = $height; 
	} else {
		$w = "650";
		$h = "388";
	}

/**
 *Example:
 *http://www.traileraddict.com/trailer/inglorious-basterds/interview-quentin-tarantino-ii
 */

  if(preg_match('<a href=\"(http://)(?:www\.)?traileraddict\.com/(?:trailer|clip)/(.*)\">isU',$message))
	{
    $pattern = "<http://www.traileraddict.com/(.*)\" target>";
    preg_match_all($pattern, $message, $links);
    $link = $links[1];
    foreach ($link as $url)
    {
      $site = htmlspecialchars_uni("http://www.traileraddict.com/$url");
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
			$nrt = get_avmatch('/<param name=\"movie\" value=\"http:\/\/www\.traileraddict\.com\/emb\/(.*)\"><\/param>/isU',$data);
			$vid = array($nrt);
			}
			$limit = 1;
			foreach ($vid as $id)
			{
				$n = htmlspecialchars_uni($id);
				$message = preg_replace("#(\[automedia\]|<a href=\"(http://)?(?:www\.)?traileraddict\.com/(?:trailer|clip)/(.*?)(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><object width=\"$w\" height=\"$h\"><param name=\"movie\" value=\"http://www.traileraddict.com/emd/$n\" /><param name=\"allowscriptaccess\" value=\"always\" /><param name=\"wmode\" value=\"transparent\" /><param name=\"allowFullScreen\" value=\"true\" /><embed  src=\"http://www.traileraddict.com/emd/$n\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" wmode=\"transparent\"  width=\"$w\" height=\"$h\" allowFullScreen=\"true\"></embed></object></div>", $message, $limit);
			}
		}
  }
	return $message;
}
?>
