<?php
###################################
# Plugin AutoMedia 3.0  for MyBB 1.8.*#
# (c) 2012 by doylecc    #
# Website: http://mods.mybb.com/profile/14694 #
###################################


// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />
		Please make sure IN_MYBB is defined.");
}

function automedia_xxxymovies($message)
{
	global $mybb, $width, $height;

	$w = $width;
	$h = $height;

/**
 *Example:
 *http://www.xxxymovies.com/164396/
*/
  $pattern = "<http://www.xxxymovies.com/([0-9]{1,12})/\" target>";
  if(preg_match($pattern, $message))
  {
    preg_match_all($pattern, $message, $links);
    $link = $links[1];
    foreach ($link as $url)
    {
		$site = htmlspecialchars_uni("http://www.xxxymovies.com/".$url."/");
		$data = fetch_remote_file($site);
        if($data) {
          $nrxxx = get_avmatch('~rel="video_src" href="([\w\.\/:-_]+)"~i',$data);
          $vid = array($nrxxx);
        }
        $limit = 1;
        foreach ($vid as $id)
        {
          $n = htmlspecialchars_uni($id);
          $message = preg_replace("#(\[automedia\]|<a href=\"(http://)(?:www\.)?xxxymovies\.com/([0-9]{1,12})/(\[/automedia\]|\" target=\"_blank\">)(.*?)</a>)#i", "<div class=\"am_embed\"><embed src=\"$n\" loop=\"false\" width=\"$w\" height=\"$h\" allowfullscreen=\"true\" allowScriptAccess=\"always\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" /></div>", $message, $limit);
        }
    }
  }
	return $message;
}
