<?php
/**
 * Plugin Name: AutoMedia 3.0 for MyBB 1.8.*
 * Copyright © 2009-2014 doylecc
 * http://mybbplugins.de.vu
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 */


// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />
		Please make sure IN_MYBB is defined.");
}


/**
 *
 * UserCP Enabled Status
 *
 **/
$plugins->add_hook("usercp_start", "automedia_ucp_status");

function automedia_ucp_status()
{
	global $mybb, $templates, $lang;

	if (!isset($lang->av_ucp_status))
	{
		$lang->load("automedia");
	}

	$auset = htmlspecialchars_uni($mybb->user['automedia_use']);
	$ucpstatus = '';

	if ($auset == "Y")
	{
		eval("\$ucpstatus = \"".$templates->get("automedia_ucpstatus_up")."\";");
	}
	else
	{
		eval("\$ucpstatus = \"".$templates->get("automedia_ucpstatus_down")."\";");
	}
	return $ucpstatus;
}

/**
 *
 * UserCP Settings
 *
 */
$plugins->add_hook("usercp_start", "automedia_usercp");

function automedia_usercp()
{
	global $header, $headerinclude, $usercpnav, $footer, $mybb, $theme, $db, $lang, $templates;

	$av_checked_yes = ' checked="checked"';
	$av_checked_no = '';

	if (!isset($lang->av_ucp_yes))
	{
		$lang->load("automedia");
	}

	if ($mybb->input['action'] == "userautomedia")
	{
		if ($mybb->user['automedia_use'] != 'Y')
		{
			$av_checked_yes = '';
			$av_checked_no = ' checked="checked"';
		}

		add_breadcrumb($lang->nav_usercp, "usercp.php");
		add_breadcrumb("AutoMedia");
		$ucpset = @automedia_ucp_status();
		eval("\$automedia_ucp = \"".$templates->get("automedia_usercp")."\";");
		output_page($automedia_ucp);
	}
	elseif ($mybb->input['action'] == "do_automedia" && $mybb->request_method == "post")
	{
		$uid = (int)$mybb->user['uid'];
		$updated_record = array(
			"automedia_use" => $db->escape_string($mybb->input['automedia'])
		);
		if ($db->update_query('users', $updated_record, "uid='".$uid."'"))
		{
			redirect("usercp.php?action=userautomedia", $lang->av_ucp_submit_success);
		}
	}
	else
	{
		return;
	}
}

// Let other youtube MyCodes still do their work
$plugins->add_hook("parse_message", "automedia_oldyt_run", -1);

function automedia_oldyt_run($message)
{
	global $mybb;

	$message = preg_replace("#\[youtube\]http://((?:de|www)\.)?youtube.com/watch\?v=([A-Za-z0-9\-\_]+)\[/youtube\]#i", '<div class=\'am_embed\'><object width=\'425\' height=\'350\'><param name=\'movie\' value=\'http://www.youtube.com/v/$2\' /><embed src=\'http://www.youtube.com/v/$2\' type=\'application/x-shockwave-flash\' width=\'425\' height=\'350\'></embed></object></div>', $message);
	$message = preg_replace("#\[youtube\]([A-Za-z0-9\-\_]+)\[/youtube\]#i", '<div class=\'am_embed\'><object width=\'425\' height=\'350\'><param name=\'movie\' value=\'http://www.youtube.com/v/$1\' /><embed src=\'http://www.youtube.com/v/$1\' type=\'application/x-shockwave-flash\' width=\'425\' height=\'350\'></embed></object></div>', $message);
	$message = preg_replace("#\[yt\]http://((?:de|www)\.)?youtube.com/watch\?v=([A-Za-z0-9\-\_]+)\[/yt\]#i", '<div class=\'am_embed\'><object width=\'425\' height=\'350\'><param name=\'movie\' value=\'http://www.youtube.com/v/$2\' /><embed src=\'http://www.youtube.com/v/$2\' type=\'application/x-shockwave-flash\' width=\'425\' height=\'350\'></embed></object></div>', $message);
	$message = preg_replace("#\[yt\]([A-Za-z0-9\-\_]+)\[/yt\]#i", '<div class=\'am_embed\'><object width=\'425\' height=\'350\'><param name=\'movie\' value=\'http://www.youtube.com/v/$1\' /><embed src=\'http://www.youtube.com/v/$1\' type=\'application/x-shockwave-flash\' width=\'425\' height=\'350\'></embed></object></div>', $message);

	return $message;
}


// Lookup
function get_avmatch($regex, $content)
{
	preg_match($regex, $content, $matches);
	return $matches[1];
}


// Embed the Media Files
$plugins->add_hook("parse_message", "automedia_run", -1);

function automedia_run($message)
{
	global $db, $mybb, $cache, $automedia, $width, $height;

	// Spare myshoutbox
	if ($mybb->input['action'] == 'show_shouts' || $mybb->input['action'] == 'full_shoutbox' || THIS_SCRIPT == 'shoutbox.php' || THIS_SCRIPT == 'pspshoutbox.php' || (isset($mybb->input['view_mode']) && $mybb->input['view_mode'] == 'window')) return;

/**
 * Get the settings for width and height
 **/
	$width = (int)$mybb->settings['av_width'];
	$height = (int)$mybb->settings['av_height'];

	if ($width < 10 || $width > 1200)
	{
		$width = "480";
	}

	if ($height < 10 || $height > 1000)
	{
		$height = "360";
	}

	// Add new MyCode for disabling embedding
	$message = preg_replace("#\[amoff\](<a href=\")(http://)(.*?)\" target=\"_blank\">(.*?)(</a>)\[/amoff\]#i", '<a class=\'amoff\' href=\'${2}${3}\' id= \'am\' target=\'_blank\'>${4}</a>', $message);
	$message = preg_replace("#\[amoff\](http://)(.*?)\[/amoff\]#i", '<a class=\'amoff\' href=\'${1}${2}\' id= \'am\' target=\'_blank\'>${1}${2}</a>', $message);
	// Handle [amquote] tags from older plugin versions
	$message = str_replace(array('[amquote]', '[/amquote]'), '', $message);

/**
 * Apply the permissions
 **/
	/**
	 * Get the settings for the forums
	 **/
	if ($mybb->settings['av_forums'] != -1)
	{
		if ($mybb->settings['av_forums'] == '') return;

		global $fid;

		if (isset($fid))
		{
			$avfid = (int)$fid;
		}
		else
		{
			$avfid = $mybb->get_input('fid', 1);
			if ($mybb->version > "1.8.0")
			{
				$avfid = $mybb->get_input('fid', MyBB::INPUT_INT);
			}
		}

		// Find the set fid's in settings
		$fids = explode(',', $mybb->settings['av_forums']);
		if (!in_array($avfid, $fids)) return;
	}

/**
 *Get the settings for the usergroups
 **/
	// Find the excluded groups in settings
	if ($mybb->settings['av_groups'] != '' && $mybb->usergroup['cancp'] != 1)
	{
		if (is_member($mybb->settings['av_groups']) || $mybb->settings['av_groups'] == -1) return;
	}

	// AutoMedia not disabled in settings?
	if ($mybb->settings['av_enable'] != 0)
	{
		// Embedding disabled in quotes?
		if ($mybb->settings['av_quote'] != 1)
		{
			$message = preg_replace("#(<blockquote\b[^>]*>(?>(?:[^<]++|<(?!\/?blockquote\b[^>]*>))+|(?R))*<\/blockquote>)#isU", "<amquotes>$1</amquotes>", $message);
			$noembedpattern = '#<amquotes>(.*?)<\/amquotes>#si';
			$noembed = array();
			preg_match_all($noembedpattern, $message, $quotes);
			foreach ($quotes[1] as $quote)
			{
				$noembed[] = $quote;
			}
		}
		// Embedding not disabled by using MyCode?
		if (!preg_match('/<a class=\"amoff\" href=\"(.*)\" id=\"am\" target=\"_blank\">/isU',$message))
		{
			// AutoMedia allowed for guests in settings or disabled in UCP?
			if ($mybb->user['uid'] != 0 && $mybb->user['automedia_use'] != 'N' ||
				$mybb->user['uid'] == 0 && $mybb->settings['av_guest'] == 1)
			{
				/**
				* Embed the files/providers not included in Embedly and Embera first
				**/
				$sitecache = $cache->read('automedia');
				if (is_array($sitecache))
				{
					foreach ($sitecache as $key => $sites)
					{
						if ($sites['class'] == "site")
						{
							$site = htmlspecialchars_uni($sites['name']);
							$file = MYBB_ROOT."inc/plugins/automedia/mediasites/{$site}.php";
							if (file_exists($file))
							{
								require_once($file);
								$fctn = "automedia_".$site;
								$message = $fctn($message);
							}
						}
					}
				}
				// Use the oEmbed API
				if (isset($mybb->settings['av_embera']) && $mybb->settings['av_embera'] == 1 && version_compare(PHP_VERSION, '5.3.0', '>='))
				{
					preg_match_all("/<a href=\"(https?:\/\/[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))\" target/i", $message, $post_links);
					$am_urls = $post_links[1];
					if (!empty($am_urls))
					{
						// Blacklist sites e.g.: array('Instagram', 'Github') - if empty all providers are allowed
						$denied_providers = array();
						// Exclude non SSL providers for forums using HTTPS to avoid mixed content
						if (automedia_secure())
						{
							$nohttps = array('FunnyOrDie', 'Hq23', 'Clyp', 'Dipity', 'Edocr', 'Hulu', 'MobyPicture', 'VideoFork', 'VideoJug', 'YFrog');
							// Add the non SSL providers to blacklist
							$denied_embed = array_merge($denied_providers,$nohttps);
						}
						else
						{
							$denied_embed = $denied_providers;
						}
						// Use the Embera library for embedding
						require_once MYBB_ROOT.'inc/plugins/automedia/Lib/Embera/Autoload.php';

						$config = array(
							'params' => array(
								'width' => $width,
								'height' => $height
							),
							'fake' => array(
								'width' => $width,
								'height' => $height
							),
							// Exclude providers
							'deny' => $denied_embed
						);
						$embera = new \Embera\Embera($config);

						foreach ($am_urls as $am_url)
						{
							// Embera embed
							$embed_url = $embera->autoEmbed($am_url);

							if ($embed_url != $am_url)
							{
								// Force https embedding if forum uses SSL
								if (automedia_secure())
								{
									$embed_url = str_replace('src="http://', 'src="//', $embed_url);
								}
								$message = str_replace('<a href="'.$am_url.'" target="_blank">', '<!-- automedia_start --><div class="am_embed">'.$embed_url.'</div><!-- automedia_end -->', $message);
								$message = preg_replace("#</div><!-- automedia_end -->(.*?)</a>#i", '</div><!-- automedia_end -->', $message);
							}
						}
					}
				}
			}
		// Add embed css class to links for embed.ly embedding
		$message = str_replace("<a href=", '<a class=\'oembed\' href=', $message);
		}

		// Embedding disabled in quotes?
		if ($mybb->settings['av_quote'] != 1)
		{
			$noembedendpattern = '#<amquotes>(.*?)<\/amquotes>#si';
			preg_match_all($noembedendpattern, $message, $endquotes);
			$i = 0;
			foreach ($endquotes[1] as $endquote)
			{
				$message = str_replace($endquote, $noembed[$i], $message);
				$i++;
			}
			$message = str_replace(array('<amquotes>', '</amquotes>'), '', $message);
		}
	}
	return $message;
}


// Embed Adult Site Videos
$plugins->add_hook("parse_message", "automedia_adult_run", -1);

function automedia_adult_run($message)
{
	global $db, $mybb, $cache, $automedia_adult, $width, $height;

	// Spare myshoutbox
	if ($mybb->input['action'] == 'show_shouts' || $mybb->input['action'] == 'full_shoutbox' || THIS_SCRIPT == 'shoutbox.php' || THIS_SCRIPT == 'pspshoutbox.php') return;

	/**
	 * Get the settings for width and height
	 **/
	$width = (int)$mybb->settings['av_width'];
	$height = (int)$mybb->settings['av_height'];

	if ($width < 10 || $width > 1200)
	{
		$width = "480";
	}

	if ($height < 10 || $height > 1000)
	{
		$height = "360";
	}

	// Add new MyCode for disabling embedding
	$message = preg_replace("#\[amoff\](<a href=\")(http://)(.*?)\" target=\"_blank\">(.*?)(</a>)\[/amoff\]#i", '<a class=\'amoff\' href=\'${2}${3}\' id= \'am\' target=\'_blank\'>${4}</a>', $message);
	$message = preg_replace("#\[amoff\](http://)(.*?)\[/amoff\]#i", '<a class=\'amoff\' href=\'${1}${2}\' id= \'am\' target=\'_blank\'>${1}${2}</a>', $message);
	// Handle [amquote] tags from older plugin versions
	$message = str_replace(array('[amquote]', '[/amquote]'), '', $message);

/**
 * Apply the permissions
 **/
	/**
	 * Get the settings for the forums
	 **/
	if ($mybb->settings['av_adultforums'] != -1)
	{
		if ($mybb->settings['av_adultforums'] == '') return;

		global $fid;

		if (isset($fid))
		{
			$avfid = (int)$fid;
		}
		else
		{
			$avfid = $mybb->get_input('fid', 1);
			if ($mybb->version > "1.8.0")
			{
				$avfid = $mybb->get_input('fid', MyBB::INPUT_INT);
			}
		}

		// Find the set fid's in settings
		$fids = explode(',', $mybb->settings['av_adultforums']);
		if (!in_array($avfid, $fids)) return;
	}

/**
 *Get the settings for the usergroups
 **/
	// Find the excluded groups in settings
	if ($mybb->settings['av_adultgroups'] != -1 || $mybb->usergroup['cancp'] != 1)
	{
		if ($mybb->settings['av_adultgroups'] == '' || !is_member($mybb->settings['av_adultgroups'])) return;
	}

	// Adultsites enabled?
	if ($mybb->settings['av_adultsites'] != 0)
	{
		// Embedding disabled in quotes?
		if ($mybb->settings['av_quote'] != 1)
		{
			$message = preg_replace("#(<blockquote\b[^>]*>(?>(?:[^<]++|<(?!\/?blockquote\b[^>]*>))+|(?R))*<\/blockquote>)#isU", "<amquotes>$1</amquotes>", $message);
			$noembedpattern = '#<amquotes>(.*?)<\/amquotes>#si';
			$noembed = array();
			preg_match_all($noembedpattern, $message, $quotes);
			foreach ($quotes[1] as $quote)
			{
				$noembed[] = $quote;
			}
		}
		// Has the user AutoMedia enabled in User CP?
		if ($mybb->user['automedia_use'] != 'N')
		{
			// Embedding not disabled by using MyCode?
			if (!preg_match('/<a class=\"amoff\" href=\"(.*)\" id=\"am\" target=\"_blank\">/isU',$message))
			{
				// Adultsites allowed for guests in settings?
				if ($mybb->settings['av_adultguest'] != 0 || ($mybb->user['uid'] != 0))
				{
					/**
					* Embed the files
					**/
					$sitecache = $cache->read('automedia');
					if (is_array($sitecache))
					{
						foreach ($sitecache as $key => $sites)
						{
							if ($sites['class'] == "special")
							{
								$site = htmlspecialchars_uni($sites['name']);
								$file = MYBB_ROOT."inc/plugins/automedia/special/{$site}.php";
								if (file_exists($file))
								{
									require_once($file);
									$fctn = "automedia_".$site;
									$message = $fctn($message);
								}
							}
						}
					}
				}
			}
		}

		// Embedding disabled in quotes?
		if ($mybb->settings['av_quote'] != 1)
		{
			$noembedendpattern = '#<amquotes>(.*?)<\/amquotes>#si';
			preg_match_all($noembedendpattern, $message, $endquotes);
			$i = 0;
			foreach ($endquotes[1] as $endquote)
			{
				$message = str_replace($endquote, $noembed[$i], $message);
				$i++;
			}
		}
	}
	return $message;
}


// Embedding disabled in signatures
$plugins->add_hook("postbit", "automedia_hide");
$plugins->add_hook("postbit_prev", "automedia_hide");
$plugins->add_hook("postbit_pm", "automedia_hide");

function automedia_hide(&$post)
{
	global $mybb, $settings, $automedia;

	if ($mybb->settings['av_signature'] != 1)
	{
		global $lang;
		if (!isset($lang->av_sigreplace))
		{
			if (isset($lang))
			{
				$lang->load("automedia");
			}
			else
			{
				$GLOBALS['lang']->load("automedia");
				$lang = $GLOBALS['lang'];
			}
		}
		$post['signature'] = preg_replace("!<div class=\'am_embed\'>(.*?)</div>!i", "{$lang->av_sigreplace}", $post['signature']);
		$post['signature'] = preg_replace("!<div class=\"am_embed\">(.*?)</div>!i", "{$lang->av_sigreplace}", $post['signature']);
		$post['signature'] = preg_replace("!<object(.*?)</object>!i", "{$lang->av_sigreplace}", $post['signature']);
		$post['signature'] = preg_replace("!<embed(.*?)</embed>!i", "{$lang->av_sigreplace}", $post['signature']);
		$post['signature'] = str_replace("class='oembed' ", "", $post['signature']);
		$post['signature'] = str_replace("class=\"oembed\" ", "", $post['signature']);
	}
}


// Disable embedding in threadreview while making a new reply
$plugins->add_hook("newreply_end", "automedia_threadreview");

function automedia_threadreview()
{
	global $threadreview, $lang;

	if (!isset($lang->av_threadpreview))
	{
		$lang->load("automedia");
	}

	$threadreview = preg_replace("!<div class=\'am_embed\'>(.*?)</div>!i", "{$lang->av_threadpreview}", $threadreview);
	$threadreview = preg_replace("!<div class=\"am_embed\">(.*?)</div>!i", "{$lang->av_threadpreview}", $threadreview);
	$threadreview = str_replace("class='oembed' ", "", $threadreview);
	$threadreview = str_replace("class=\"oembed\" ", "", $threadreview);

	return $threadreview;
}


// Message in User CP signature preview and profile if embedding in signatures is disabled
$plugins->add_hook("pre_output_page", "automedia_sigpreview");

function automedia_sigpreview($page)
{
	global $mybb, $settings, $amsigpreview, $templates, $footer, $am_embedly_script, $am_embedly_head;

	if (THIS_SCRIPT == "usercp.php" || THIS_SCRIPT == "member.php")
	{
		if ($mybb->settings['av_signature'] != 1)
		{
			global $lang;
			if (isset($lang))
			{
				if (!isset($lang->av_sigreplace))
				{
					$lang->load("automedia");
				}
			}
			else
			{
				$GLOBALS['lang']->load("automedia");
				$lang = $GLOBALS['lang'];
				$lang->load("automedia");
			}
			$sigreplace = $lang->av_sigreplace;

			$page = preg_replace("!<div class=\'am_embed\'>(.*?)</div>!i", "{$sigreplace}", $page);
			$page = preg_replace("!<div class=\"am_embed\">(.*?)</div>!i", "{$sigreplace}", $page);
			$page = str_replace("class='oembed' ", "", $page);
			$page = str_replace("class=\"oembed\" ", "", $page);
		}
	}

	// Load embedly scripts only if the page contains links
	if (my_strpos($page, "class='oembed'") !== false)
	{
		$page = str_replace("<!--embedlyhead-->", $am_embedly_head, $page);
		$page = str_replace("<!--embedlyfooter-->", $am_embedly_script, $page);
	}

	return $page;
}

// Load embed.ly and HTML5 mediaplayer javascript
$plugins->add_hook("global_end", "automedia_embedly");
$plugins->add_hook("archive_thread_end", "automedia_embedly");
$plugins->add_hook("printthread_end", "automedia_embedly");

function automedia_embedly()
{
	global $mybb, $headerinclude, $footer, $lang, $templates, $am_embedly_script, $am_embedly_head;

	$am_head = '';
	$am_head_embedly = '';
	$am_embedly_script = '';
	$this_scripts = array('usercp.php', 'showthread.php', 'private.php', 'newthread.php', 'newreply.php', 'editpost.php', 'calendar.php', 'portal.php', 'modcp.php', 'printthread.php');

	// Don't load the templates everywhere
	if (!in_array(THIS_SCRIPT, $this_scripts) && !defined("IN_ARCHIVE")) return;

	/**
	 * Get the settings for the forums
	 **/
	if ($mybb->settings['av_forums'] != -1)
	{
		global $fid;

		if (isset($fid))
		{
			$avfid = (int)$fid;
		}
		else
		{
			$avfid = $mybb->get_input('fid', 1);
			if ($mybb->version > "1.8.0")
			{
				$avfid = $mybb->get_input('fid', MyBB::INPUT_INT);
			}
		}

		// Find the set fid's in settings
		$fids = explode(',', $mybb->settings['av_forums']);
		if (!in_array($avfid, $fids)) return;
	}

/**
 *Get the settings for the usergroups
 **/
	// Find the excluded groups in settings
	if ($mybb->settings['av_groups'] != '' && $mybb->usergroup['cancp'] != 1)
	{
		if (is_member($mybb->settings['av_groups']) || $mybb->settings['av_groups'] == -1) return;
	}

	// Check settings and permissions
	if ($mybb->settings['av_enable'] == 1 && $mybb->user['uid'] != 0 && $mybb->user['automedia_use'] != 'N' ||
		$mybb->settings['av_enable'] == 1 && $mybb->user['uid'] == 0 && $mybb->settings['av_guest'] == 1)
	{
		eval("\$am_head = \"".$templates->get("automedia_head")."\";");
		$headerinclude .= $am_head;

		// Embed.ly jQuery
		if ($mybb->settings['av_embedly'] == 1 && !empty($mybb->settings['av_embedly_key']) && $mybb->settings['av_embedly_key'] != "")
		{
			if (!isset($lang->av_click))
			{
				$lang->load("automedia");
			}
			// Set sanitized variables
			$mybb->settings['av_embedly_key'] = htmlspecialchars_uni($mybb->settings['av_embedly_key']);
			$mybb->settings['av_width'] = (int)$mybb->settings['av_width'];
			$mybb->settings['av_height'] = (int)$mybb->settings['av_height'];
			$modalwidth = (int)$mybb->settings['av_width'] + 50;

			// Add script to headerinclude
			eval("\$am_embedly_head = \"".$templates->get("automedia_head_embedly")."\";");

			if ($mybb->settings['av_embedly_click'] == 'modal')
			{
				// Show media as modal popup
				if ($mybb->settings['av_embedly_card'] != 1)
				{
					eval("\$am_embedly_script = \"".$templates->get("automedia_embedly_modal")."\";");
				}
				else
				{
					eval("\$am_embedly_script = \"".$templates->get("automedia_embedly_modal_card")."\";");
				}

			} // Show click button
			elseif ($mybb->settings['av_embedly_click'] == 'button')
			{
				if ($mybb->settings['av_embedly_card'] != 1)
				{
					eval("\$am_embedly_script = \"".$templates->get("automedia_embedly_button")."\";");
				}
				else
				{
					eval("\$am_embedly_script = \"".$templates->get("automedia_embedly_button_card")."\";");
				}
			}
			// Show media immediately
			else
			{
				if ($mybb->settings['av_embedly_card'] != 1)
				{
					eval("\$am_embedly_script = \"".$templates->get("automedia_embedly_direct")."\";");
				}
				else
				{
					eval("\$am_embedly_script = \"".$templates->get("automedia_embedly_direct_card")."\";");
				}
			}
			$headerinclude = $headerinclude.'<!--embedlyhead-->';
			$footer = $footer.'<!--embedlyfooter-->';
			// Add embedly scripts in archive mode
			if (defined('IN_ARCHIVE'))
			{
				echo('<script type="text/javascript" src="'.$mybb->asset_url.'/jscripts/jquery.js?ver=1800"></script>
					<script type="text/javascript" src="'.$mybb->asset_url.'/jscripts/jquery.plugins.min.js?ver=1800"></script>');
				echo($am_embedly_head);
				echo($am_embedly_script);
			}
			// Add embedly scripts in printthread
			if (THIS_SCRIPT == 'printthread.php')
			{
				global $postrows;
				$postrows = $postrows.'
				<script type="text/javascript" src="'.$mybb->asset_url.'/jscripts/jquery.js?ver=1800"></script>
				<script type="text/javascript" src="'.$mybb->asset_url.'/jscripts/jquery.plugins.min.js?ver=1800"></script>'
				.$am_embedly_head.$am_embedly_script;
			}
		}
	}
}

// Use MyBB's maxpostvideos settings
$plugins->add_hook("postbit", "automedia_count");
$plugins->add_hook("postbit_prev", "automedia_count");
$plugins->add_hook("postbit_pm", "automedia_count");
$plugins->add_hook("archive_thread_post", "automedia_count");
$plugins->add_hook("printthread_post", "automedia_count");

function automedia_count(&$post)
{
	global $mybb, $settings, $automedia, $templates;

	if (defined('IN_ARCHIVE'))
	{
		global $post;
	}

	$am_video_count = '';

	// Print version
	if (THIS_SCRIPT == 'printthread.php')
	{
		global $postrow;

		// Get the permissions of the user who is making this post or thread
		$permissions = user_permissions($postrow['uid']);

		// Check if this post contains more videos than the forum allows
		if ($mybb->settings['maxpostvideos'] != 0 && $permissions['cancp'] != 1)
		{
			// And count the number of all videos in the message.
			$automedia_count = substr_count($postrow['message'], "am_embed");
			$vids_count = substr_count($postrow['message'], "video_embed");
			$all_count = $automedia_count + $vids_count;
			if ($all_count > $mybb->settings['maxpostvideos'])
			{
				global $lang;
				if (!isset($lang->av_vidcount))
				{
					$lang->load("automedia");
				}
				// Throw back a message if over the count as well as the maximum number of videos per post.
				eval("\$am_video_count = \"".$templates->get("automedia_videocount")."\";");
				$postrow['message'] = $am_video_count;
			}
		}
	}
	else
	{
		// Get the permissions of the user who is making this post or thread
		$permissions = user_permissions($post['uid']);

		// Check if this post contains more videos than the forum allows
		if ((!isset($post['savedraft']) || $post['savedraft'] != 1) && $mybb->settings['maxpostvideos'] != 0 && $permissions['cancp'] != 1)
		{
			// And count the number of all videos in the message.
			$automedia_count = substr_count($post['message'], "am_embed");
			$vids_count = substr_count($post['message'], "video_embed");
			$all_count = $automedia_count + $vids_count;
			if ($all_count > $mybb->settings['maxpostvideos'])
			{
				global $lang;
				if (!isset($lang->av_vidcount))
				{
					$lang->load("automedia");
				}
				// Throw back a message if over the count as well as the maximum number of videos per post.
				eval("\$am_video_count = \"".$templates->get("automedia_videocount")."\";");
				$post['message'] = $am_video_count;
			}
		}
	}
}


// Show codebuttons for disabling embedding and mp3-playlist MyCode
$plugins->add_hook("pre_output_page", "automedia_codebutton");

function automedia_codebutton($page)
{
	global $mybb, $templates, $am_codebuttons, $am_codebuttons_footer, $amoff, $ampl;

	if ($mybb->settings['av_codebuttons'] == 1)
	{
		global $lang;
		if (!isset($lang->av_amoff))
		{
			if (isset($lang))
			{
				$lang->load("automedia");
			}
			else
			{
				$GLOBALS['lang']->load("automedia");
				$lang = $GLOBALS['lang'];
			}
		}
		$amoff = $lang->av_amoff;
		$ampl = $lang->av_ampl;
		$am_codebuttons = '';
		$am_codebuttons_footer = '';

		if (THIS_SCRIPT == "newthread.php" || THIS_SCRIPT == "usercp.php" && $mybb->input['action'] == "editsig" || THIS_SCRIPT == "calendar.php" || (THIS_SCRIPT == "showthread.php" &&  $mybb->settings['quickreply'] == 1) || THIS_SCRIPT == "newreply.php" || THIS_SCRIPT == "editpost.php" || THIS_SCRIPT == "modcp.php")
		{
			eval("\$am_codebuttons = \"".$templates->get("automedia_codebuttons")."\";");
			$page = str_replace('</textarea>', '</textarea>'.$am_codebuttons.'', $page);
			eval("\$am_codebuttons_footer = \"".$templates->get("automedia_codebuttons_footer")."\";");
			$replace_body = array('</body>' => ''.$am_codebuttons_footer.'
</body>');
			$page = strtr($page, $replace_body);
		}
		if (THIS_SCRIPT == "private.php" && $mybb->input['action'] == "send")
		{
			eval("\$am_codebuttons = \"".$templates->get("automedia_codebuttons_private")."\";");
			$page = str_replace('<label><input type="checkbox" class="checkbox" name="options[signature]"', ''.$am_codebuttons.'
<label><input type="checkbox" class="checkbox" name="options[signature]"', $page);

			eval("\$am_codebuttons_footer = \"".$templates->get("automedia_codebuttons_footer")."\";");
			$replace_body = array('</body>' => ''.$am_codebuttons_footer.'
</body>');
			$page = strtr($page, $replace_body);
		}
		return $page;
	}
}

// Check if forum uses SSL
function automedia_secure()
{
	return
		(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| $_SERVER['SERVER_PORT'] == 443;
}


// Use this hooks and functions only if permissions are set accordingly
if ($GLOBALS['mybb']->settings['av_flashadmin'] == "admin" || $GLOBALS['mybb']->settings['av_flashadmin'] == "mods")
{
	// Check flash embed permissions
	$plugins->add_hook("private_end", "automedia_flash");
	$plugins->add_hook("editpost_end", "automedia_flash");
	$plugins->add_hook("newreply_end", "automedia_flash");
	$plugins->add_hook("newthread_end", "automedia_flash");
	$plugins->add_hook("xmlhttp", "automedia_flash");

	function automedia_flash()
	{
		global $mybb;

		// Get message for quick edit
		if($mybb->input['do'] == "update_post")
		{
			$message = (string)$mybb->input['value'];
		}
		else
		{
			$message = $mybb->input['message'];
		}

		$permissions = user_permissions((int)$mybb->user['uid']);

		switch($mybb->settings['av_flashadmin'])
		{
			case "admin":
			if ($permissions['cancp'] != 1)
			{
				$message = preg_replace('#(http://)?(www.)?(.*)\.flv#i', '[amoff]$1$2$3.flv[/amoff]', $message);
				$message = preg_replace('#(http://)?(www.)?(.*)\.swf#i', '[amoff]$1$2$3.swf[/amoff]', $message);
			}
			break;
			case "mods":
			if ($permissions['cancp'] != 1 && $permissions['canmodcp'] != 1)
			{
				$message = preg_replace('#(http://)?(www.)?(.*)\.flv#i', '[amoff]$1$2$3.flv[/amoff]', $message);
				$message = preg_replace('#(http://)?(www.)?(.*)\.swf#i', '[amoff]$1$2$3.swf[/amoff]', $message);
			}
			break;
		}

		return $message;
	}


	// Insert [amoff] tags for flash files if user has no permission
	$plugins->add_hook("newreply_do_newreply_start", "automedia_insert_post", -10);
	$plugins->add_hook("newreply_start", "automedia_insert_post", -10);
	$plugins->add_hook("newthread_do_newthread_start", "automedia_insert_post", -10);
	$plugins->add_hook("newthread_start", "automedia_insert_post", -10);
	$plugins->add_hook("editpost_do_editpost_start", "automedia_insert_post", -10);
	$plugins->add_hook("editpost_start", "automedia_insert_post", -10);
	$plugins->add_hook("private_send_do_send", "automedia_insert_post", -10);
	$plugins->add_hook("private_send_start", "automedia_insert_post", -10);

	function automedia_insert_post()
	{
		global $mybb;
		$replace = "automedia_flash";
		$new_message = $replace($message);
		if (!empty($new_message))
		{
			$mybb->input['message'] = $replace($message);
		}
	}

	// Insert [amoff] tags for flash files in quick edit if user has no permission
	function automedia_quickedit()
	{
		global $mybb;

		if($mybb->input['do'] == "update_post")
		{
			$replace = "automedia_flash";
			$new_message = $replace($message);
			if (!empty($new_message))
			{
				$mybb->input['value'] = $replace($message);
			}
		}
	}
}
