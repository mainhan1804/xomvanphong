<?php
/*
 * firstpreview.php
 *
 * Copyright 2014 doylecc
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *
 */
// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

// Add Hooks
$plugins->add_hook("forumdisplay_end", "firstpreview_show");
$plugins->add_hook("search_results_end", "firstpreview_show");
$plugins->add_hook("forumdisplay_thread", "firstpreview_insert");
$plugins->add_hook("search_results_thread", "firstpreview_insert");
$plugins->add_hook("xmlhttp", "firstpreview_ajax");
$plugins->add_hook("private_end", "firstpreview_pm");

global $mybb;
if(isset($mybb->settings['firstpreview_last']) && $mybb->settings['firstpreview_last'] != 0)
{
	$plugins->add_hook("index_end", "firstpreview_show");
}

// Plugin Info
function firstpreview_info()
{

	$firstpreview_info = array(
		"name"			=> "First Post/PM  Preview",
		"description"	=> "Shows the pm in message list, the first post of a thread on forum list and search results as popup",
		"website"		=> "http://mybbplugins.de.vu",
		"author"		=> "doylecc",
		"authorsite"	=> "http://mybbplugins.de.vu",
		"version"		=> "1.2",
		"guid" 			=> "",
		"compatibility" => "16*,18*"
	);

	return $firstpreview_info;
}

// Install plugin
function firstpreview_install()
{
	global $db;

	// DELETE ALL SETTINGS FIRST TO AVOID DUPLICATES
	$query = $db->simple_select('settinggroups','gid','name="firstpreview"');
	$fp = $db->fetch_array($query);
	$db->delete_query('settinggroups',"gid='".$fp['gid']."'");
	$db->delete_query('settings',"gid='".$fp['gid']."'");

	/**
	* Now add the settings
	**/
	$query_add = $db->simple_select("settinggroups", "COUNT(*) as rows");
	$rows = $db->fetch_field($query_add, "rows");

	// Add the settinggroup
	$firstpreview_group = array(
		"name" => "firstpreview",
		"title" => "First Post/PM Preview",
		"description" => "Settings of the First Preview Plugins",
		"disporder" => $rows+1,
		"isdefault" => 0
	);
	$db->insert_query("settinggroups", $firstpreview_group);
	$gid = $db->insert_id();

	// Add the settings for the first preview plugin settinggroup
	$firstpreview_1 = array(
		"name" => "firstpreview_length",
		"title" => "Max. characters of the preview ",
		"description" => "The message is cut off after the max. character setting is reached. (0 = no limitation)",
		"optionscode" => "text",
		"value" => "0",
		"disporder" => 1,
		"gid" => (int)$gid
		);
	$db->insert_query("settings", $firstpreview_1);

	$firstpreview_2 = array(
		"name" => "firstpreview_html",
		"title" => "Preview in HTML",
		"description" => "Show preview as HTML (Yes) or plain text (No)",
		"optionscode" => "yesno",
		"value" => 1,
		"disporder" => 2,
		"gid" => (int)$gid
		);
	$db->insert_query("settings", $firstpreview_2);

	$firstpreview_3 = array(
		"name" => "firstpreview_last",
		"title" => "Last post preview",
		"description" => "Also show a preview of the last post in forum list and index.",
		"optionscode" => "yesno",
		"value" => 0,
		"disporder" => 3,
		"gid" => (int)$gid
		);
	$db->insert_query("settings", $firstpreview_3);

	$firstpreview_4 = array(
		"name" => "firstpreview_bg",
		"title" => "Background color of the preview",
		"description" => "Background color of the preview window as Hex code without semicolon (Default: #aaaaaa)",
		"optionscode" => "text",
		"value" => '#aaaaaa',
		"disporder" => 4,
		"gid" => (int)$gid
		);
	$db->insert_query("settings", $firstpreview_4);

	$firstpreview_5 = array(
		"name" => "firstpreview_close",
		"title" => "Close Button",
		"description" => "Show close button in preview window",
		"optionscode" => "yesno",
		"value" => 0,
		"disporder" => 5,
		"gid" => (int)$gid
		);
	$db->insert_query("settings", $firstpreview_5);

	rebuild_settings();
}

// Uninstall plugin
function firstpreview_uninstall()
{
	global $db;

	// DELETE ALL PLUGIN SETTINGS
	$query = $db->simple_select('settinggroups','gid','name="firstpreview"');
	$fp = $db->fetch_array($query);
	$db->delete_query('settinggroups',"gid='".$fp['gid']."'");
	$db->delete_query('settings',"gid='".$fp['gid']."'");

	rebuild_settings();
}

// Plugin installed check
function firstpreview_is_installed()
{
	global $db;

	$query = $db->simple_select('settinggroups','*','name="firstpreview"');
	$installed = $db->fetch_array($query);

	if($installed)
	{
		return true;
	}
		return false;
}

// Activate plugin
function firstpreview_activate()
{
	global $db;

	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	// Undo old template edits to avoid duplicate entries
	find_replace_templatesets("forumdisplay_thread", "#".preg_quote(' class="last_preview" id="ltid_{$inline_edit_tid}"')."#s",'', '',false);
	find_replace_templatesets("search_results_threads_thread", "#".preg_quote(' class="last_preview" id="ltid_{$inline_edit_tid}"')."#s",'', '',false);
	find_replace_templatesets("forumbit_depth2_forum_lastpost", "#".preg_quote(' class="last_preview" id="ltid_{$lastpost_data[\'lastposttid\']}"')."#s",'', '',false);
	find_replace_templatesets("private_messagebit", "#".preg_quote('<div class="modal_firstpost"></div><a class="pmprev" id="pmid_{$message[\'pmid\']}"')."#s",'<a', '',false);
	find_replace_templatesets("private_messagebit", "#".preg_quote('<a class="pmprev" id="{$message')."#s",'<a class="pmprev" id="pmid_{$message', '',false);

	// Edit templates
	find_replace_templatesets("forumdisplay_thread", "#".preg_quote('<a href="{$thread[\'lastpostlink\']}"').'#s', "<a href=\"{\$thread['lastpostlink']}\" class=\"last_preview\" id=\"ltid_{\$inline_edit_tid}\"");
	find_replace_templatesets("search_results_threads_thread", "#".preg_quote('<a href="{$thread[\'lastpostlink\']}"').'#s', "<a href=\"{\$thread['lastpostlink']}\" class=\"last_preview\" id=\"ltid_{\$inline_edit_tid}\"");
	find_replace_templatesets("forumbit_depth2_forum_lastpost", "#".preg_quote('<a href="{$lastpost_link}"').'#s', "<a href=\"{\$lastpost_link}\" class=\"last_preview\" id=\"ltid_{\$lastpost_data['lastposttid']}\"");
	find_replace_templatesets("private_messagebit", "#".preg_quote('{$msgprefix}<a').'#s', "{\$msgprefix}<div class=\"modal_firstpost\"></div><a class=\"pmprev\" id=\"pmid_{\$message['pmid']}\"");

	// Upgrade to 1.1
	$query = $db->simple_select("settinggroups", "gid", "name='firstpreview'");
	$fpgid = $db->fetch_array($query);
	if($fpgid)
	{
		$gid = $fpgid['gid'];
	}
	$query_2 = $db->simple_select("settings", "*", "name='firstpreview_last'");
	$result = $db->num_rows($query_2);
	if(!$result)
	{
		$firstpreview_3 = array(
			"name" => "firstpreview_last",
			"title" => "Last post preview",
			"description" => "Also show a preview of the last post in forum list and index.",
			"optionscode" => "yesno",
			"value" => 0,
			"disporder" => 3,
			"gid" => (int)$gid
			);
		$db->insert_query("settings", $firstpreview_3);
	}
	// Upgrade to 1.1.1
	$query_3 = $db->simple_select("settings", "*", "name='firstpreview_bg'");
	$result_2 = $db->num_rows($query_3);
	if(!$result_2)
	{
		$firstpreview_4 = array(
			"name" => "firstpreview_bg",
			"title" => "Background color of the preview",
			"description" => "Background color of the preview window as Hex code without semicolon (Default: #aaaaaa)",
			"optionscode" => "text",
			"value" => '#aaaaaa',
			"disporder" => 4,
			"gid" => (int)$gid
			);
		$db->insert_query("settings", $firstpreview_4);
	}
	// Upgrade to 1.1.2
	$query_4 = $db->simple_select("settings", "*", "name='firstpreview_close'");
	$result_3 = $db->num_rows($query_4);
	if(!$result_3)
	{
		$firstpreview_5 = array(
			"name" => "firstpreview_close",
			"title" => "Close Button",
			"description" => "Show close button in preview window",
			"optionscode" => "yesno",
			"value" => 0,
			"disporder" => 5,
			"gid" => (int)$gid
			);
		$db->insert_query("settings", $firstpreview_5);
	}
	rebuild_settings();
}

// Deactivate plugin
function firstpreview_deactivate()
{
	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";
	// Remove old edits if we are upgrading
	find_replace_templatesets("forumdisplay_thread", "#".preg_quote('<div id="modal_firstpost"></div>')."#s",'', '',false);
	find_replace_templatesets("search_results_threads_thread", "#".preg_quote('<div id="modal_firstpost"></div>')."#s",'', '',false);
	find_replace_templatesets("private_messagebit", "#".preg_quote('<a class="pmprev" id="{$message')."#s",'<a class="pmprev" id="pmid_{$message', '',false);
	find_replace_templatesets("forumdisplay_thread", "#".preg_quote('<div class="modal_firstpost"></div>')."#s",'', '',false);
	find_replace_templatesets("search_results_threads_thread", "#".preg_quote('<div class="modal_firstpost"></div>')."#s",'', '',false);
	find_replace_templatesets("forumbit_depth2_forum", "#".preg_quote('<div class="modal_firstpost"></div>')."#s",'', '',false);

	// Undo template edits
	find_replace_templatesets("forumdisplay_thread", "#".preg_quote(' class="last_preview" id="ltid_{$inline_edit_tid}"')."#s",'', '',false);
	find_replace_templatesets("search_results_threads_thread", "#".preg_quote(' class="last_preview" id="ltid_{$inline_edit_tid}"')."#s",'', '',false);
	find_replace_templatesets("forumbit_depth2_forum_lastpost", "#".preg_quote(' class="last_preview" id="ltid_{$lastpost_data[\'lastposttid\']}"')."#s",'', '',false);
	find_replace_templatesets("private_messagebit", "#".preg_quote('<div class="modal_firstpost"></div><a class="pmprev" id="pmid_{$message[\'pmid\']}"')."#s",'<a', '',false);
}

// Insert code into page
function firstpreview_show()
{
	global $headerinclude, $header;

	$headerinclude .= firstpreview_insert();
	$header = '<div class="modal_firstpost"></div><div class="arrow-down"></div>'.$header;
}

// Code for forumdisplay and search results
function firstpreview_insert()
{
	global $mybb, $fpermissions;

	$firstpreview = '';
	if ($fpermissions['canviewthreads'] != 0 || THIS_SCRIPT == "search.php" && $mybb->input['action'] == "results" || THIS_SCRIPT == "index.php")
	{
		// Add jQuery and noConflict for MyBB 1.6.*
		$jquery = '';
		$noconflict = '';
		if($mybb->version < "1.7.0")
		{
			$jquery = '<script type="text/javascript">
//<![CDATA[
if (!window.jQuery)
{
	document.write(unescape("%3Cscript src=\"http://code.jquery.com/jquery-latest.min.js\" type=\"text/javascript\"%3E%3C/script%3E"));
}
//]]>
</script>';
			$noconflict = 'jQuery.noConflict();';
		}
		// Background color
		$bg_color = '#aaaaaa';
		if (isset($mybb->settings['firstpreview_bg']) && preg_match('/^#([0-9a-f]{1,6})$/i', $mybb->settings['firstpreview_bg']))
		{
			$bg_color = htmlspecialchars_uni($mybb->settings['firstpreview_bg']);
		}
		// Close button
		$close_preview = '#close_preview{display:none;cursor:pointer;background:#000;color:#fff;float:right;font-size:1em;font-weight:bold;text-align:center;width:20px;height:20px;border-radius:5px}';
		if (isset($mybb->settings['firstpreview_close']) && $mybb->settings['firstpreview_close'] == 1)
		{
			$close_preview = '#close_preview{cursor:pointer;background:#000;color:#fff;float:right;font-size:1em;font-weight:bold;text-align:center;width:20px;height:20px;border-radius:5px}';
		}

		$firstpreview = '
		<!-- start: first_preview_plugin -->
		<style type="text/css">
		.modal_firstpost{text-align:left;border-radius:7px;-moz-border-radius:7px;-webkit-border-radius:7px;border:1px solid '.$bg_color.';display:none;position:absolute;z-index:29000;width:390px;height:180px;overflow:hidden}
		.fpreview{z-index:29001;width:390px;height:180px;overflow:auto;background:'.$bg_color.'}
		.arrow-down{display:none;position:absolute;z-index:28999;width:0;height:0;border-left:20px solid transparent;border-right:20px solid transparent;border-top:20px solid '.$bg_color.'}
		.prev_content{padding:10px;height:auto;word-wrap:break-word;-webkit-hyphens:auto;-moz-hyphens:auto;-ms-hyphens:auto;-o-hyphens:auto;hyphens:auto;background:none}
		'.$close_preview.'
		</style>
		'.$jquery.'
		<script type="text/javascript">
		//<![CDATA[
		'.$noconflict.'
		if(use_xmlhttprequest == 1) {
			jQuery(document).ready(function(e){e(".subject_old, .subject_new").on("touchenter mouseenter",function(){id=e(this).attr("id");tid=id.replace(/[^\d.]/g,"");var t=e(this).offset().left;var n=e(this).offset().top-200;showPost=setTimeout(function(){e.ajax({url:"xmlhttp.php?tid="+tid+"&firstpost=1",type:"post",complete:function(t){e(".modal_firstpost").html(t.responseText)}});e(".modal_firstpost").fadeIn("slow");e(".modal_firstpost").css("top",n);e(".modal_firstpost").css("left",t);e(".arrow-down").fadeIn("slow");e(".arrow-down").css("top",n+180);e(".arrow-down").css("left",t+20);},1500)});e(".subject_old,.subject_new").on("mouseleave touchleave touchend",function(){clearTimeout(showPost);});e(".modal_firstpost").on("mouseleave touchmove",function(){e(".modal_firstpost").fadeOut("slow");e(".arrow-down").fadeOut("fast")});e(".modal_firstpost").on("click", "#close_preview", function(){e(".modal_firstpost").fadeOut("slow");e(".arrow-down").fadeOut("fast")})});
			var lastpreview = '.$mybb->settings['firstpreview_last'].';
			if(lastpreview == 1) {
				jQuery(document).ready(function(e){e(".last_preview").on("touchenter mouseenter",function(){id=e(this).attr("id");tid=id.replace(/[^\d.]/g,"");var t=e(this).parent().offset().left-340;var n=e(this).offset().top-200;showLast=setTimeout(function(){e.ajax({url:"xmlhttp.php?tid="+tid+"&lastpost=1",type:"post",complete:function(t){e(".modal_firstpost").html(t.responseText)}});e(".modal_firstpost").fadeIn("slow");e(".modal_firstpost").css("top",n);e(".modal_firstpost").css("left",t);e(".arrow-down").fadeIn("slow");e(".arrow-down").css("top",n+180);e(".arrow-down").css("left",t+340);},2500)});e(".last_preview").on("mouseleave touchleave touchend",function(){clearTimeout(showLast);});e(".modal_firstpost").on("mouseleave touchmove",function(){e(".modal_firstpost").fadeOut("slow");e(".arrow-down").fadeOut("fast")});e(".modal_firstpost").on("click", "#close_preview", function(){e(".modal_firstpost").fadeOut("slow");e(".arrow-down").fadeOut("fast")})});
			}
		}
		//]]>
		</script>
		<!-- end: first_preview_plugin -->
		';
	}
	return $firstpreview;
}

// Get the first/last post of the thread
function firstpreview_ajax()
{
	global $mybb, $db, $lang, $charset;

	// Get the first post
	if (isset($mybb->input['firstpost']) && $mybb->input['firstpost'] == 1 && $mybb->request_method == "post")
	{
		$thread = get_thread((int)$mybb->input['tid']);
		$permissions = forum_permissions($thread['fid']);

		require_once MYBB_ROOT."inc/class_parser.php";
		$parser = new postParser;
		$post = get_post($thread['firstpost']);
		$forum = get_forum($thread['fid']);
		$user = get_user($post['uid']);
		$thread['subject'] = htmlspecialchars_uni($parser->parse_badwords($thread['subject']));
		$threaddate = my_date($mybb->settings['dateformat'], $thread['dateline']);
		$threadtime = my_date($mybb->settings['timeformat'], $thread['dateline']);
		$threadposted = ' ('.$threaddate.', '.$threadtime.')';

		$parser_options['allow_html'] = $forum['allowhtml'];
		$parser_options['allow_mycode'] = $forum['allowmycode'];
		$parser_options['allow_smilies'] = $forum['allowsmilies'];
		$parser_options['allow_imgcode'] = $forum['allowimgcode'];
		$parser_options['allow_videocode'] = $forum['allowvideocode'];
		$parser_options['filter_badwords'] = 1;
		$id = 0;
		$post['message'] = $parser->parse_message($post['message'], $parser_options);

		if (isset($mybb->settings['firstpreview_html']) && $mybb->settings['firstpreview_html'] != 1)
		{
			$post['message'] = strip_tags($post['message'], "<br><p><ul><ol><li>");
		}
		if (!empty($mybb->settings['firstpreview_length']) && $mybb->settings['firstpreview_length'] != "0" && my_strlen($post['message']) > (int)$mybb->settings['firstpreview_length'])
		{
			$post['message'] = my_substr($post['message'], 0, (int)$mybb->settings['firstpreview_length']).'...';
		}

		if (isset($permissions['canviewthreads']) && $permissions['canviewthreads'] == 1)
		{
			$preview = "<div class=\"fpreview\"><span id=\"close_preview\">&#10060;</span>
			<div class=\"thead\" style=\"text-align:center; font-weight:bold; min-height:20px;\">".$thread['subject']."</div>
			<div class=\"tcat\" style=\"padding-left:10px; height: 10%;\">".build_profile_link(format_name(htmlspecialchars_uni($post['username']), (int)$user['usergroup'], (int)$user['displaygroup']), (int)$post['uid'])."<span class=\"smalltext\">".$threadposted."</span></div>
			<div class=\"prev_content\">".$post['message']."</div>
			</div>";
		}
		else
		{
			$lang->load("messages");
			$preview = "<div class=\"fpreview\"><span id=\"close_preview\">&#10060;</span><div class=\"prev_content\" style=\"text-align:center;\">".$lang->error_nopermission_user_ajax."</div></div>";
		}

		header("Content-type: text/plain; charset={$charset}");
		echo $preview;
		exit;
	}
	// Get the last post
	if (isset($mybb->settings['firstpreview_last']) && $mybb->settings['firstpreview_last'] != 0 && isset($mybb->input['lastpost']) && $mybb->input['lastpost'] == 1 && $mybb->request_method == "post")
	{
		$thread = get_thread((int)$mybb->input['tid']);
		$tid = (int)$thread['tid'];
		$permissions = forum_permissions($thread['fid']);

		require_once MYBB_ROOT."inc/class_parser.php";
		$parser = new postParser;
		$lastposter = (int)$thread['lastposteruid'];
		$lastposttime = (int)$thread['lastpost'];
		$query = $db->simple_select('posts', '*', "uid = '".$lastposter."' AND dateline = '".$lastposttime."' AND tid = '".$tid."'");
		$post = $db->fetch_array($query);
		$forum = get_forum($thread['fid']);
		$user = get_user($post['uid']);
		$thread['subject'] = htmlspecialchars_uni($parser->parse_badwords($thread['subject']));
		$lastdate = my_date($mybb->settings['dateformat'], $lastposttime);
		$lasttime = my_date($mybb->settings['timeformat'], $lastposttime);
		$lastposted = ' ('.$lastdate.', '.$lasttime.')';

		$parser_options['allow_html'] = $forum['allowhtml'];
		$parser_options['allow_mycode'] = $forum['allowmycode'];
		$parser_options['allow_smilies'] = $forum['allowsmilies'];
		$parser_options['allow_imgcode'] = $forum['allowimgcode'];
		$parser_options['allow_videocode'] = $forum['allowvideocode'];
		$parser_options['filter_badwords'] = 1;
		$id = 0;

		$post['message'] = $parser->parse_message($post['message'], $parser_options);

		if (isset($mybb->settings['firstpreview_html']) && $mybb->settings['firstpreview_html'] != 1)
		{
			$post['message'] = strip_tags($post['message'], "<br><p><ul><ol><li>");
		}
		if (!empty($mybb->settings['firstpreview_length']) && $mybb->settings['firstpreview_length'] != "0" && my_strlen($post['message']) > (int)$mybb->settings['firstpreview_length'])
		{
			$post['message'] = my_substr($post['message'], 0, (int)$mybb->settings['firstpreview_length']).'...';
		}

		if (isset($permissions['canviewthreads']) && $permissions['canviewthreads'] == 1)
		{
			$lang->load("forumdisplay");
			$preview = "<div class=\"fpreview\"><span id=\"close_preview\">&#10060;</span>
			<div class=\"thead\" style=\"text-align:center; font-weight:bold; min-height:20px;\">".$thread['subject']."</div>
			<div class=\"tcat\" style=\"padding-left:10px; padding-right:10px;\">".build_profile_link(format_name(htmlspecialchars_uni($post['username']), (int)$user['usergroup'], (int)$user['displaygroup']), (int)$post['uid'])."<span class=\"smalltext\">".$lastposted."<span class=\"float_right\"><strong>".$lang->lastpost."</strong></span></span></div>
			<div class=\"prev_content\">".$post['message']."</div>
			</div>";
		}
		else
		{
			$lang->load("messages");
			$preview = "<div class=\"fpreview\"><span id=\"close_preview\">&#10060;</span><div class=\"prev_content\" style=\"text-align:center;\">".$lang->error_nopermission_user_ajax."</div></div>";
		}

		header("Content-type: text/plain; charset={$charset}");
		echo $preview;
		exit;
	}
}

// Get the pm for preview
function firstpreview_pm()
{
	global $mybb, $db, $charset, $headerinclude, $header;

	$header = '<div class="arrow-down"></div>'.$header;

	// Add jQuery and noConflict for MyBB 1.6.*
	$jquery = '';
	$noconflict = '';
	if($mybb->version < "1.7.0")
	{
		$jquery = '<script type="text/javascript">
//<![CDATA[
if (!window.jQuery)
{
document.write(unescape("%3Cscript src=\"http://code.jquery.com/jquery-latest.min.js\" type=\"text/javascript\"%3E%3C/script%3E"));
}
//]]>
</script>';
		$noconflict = 'jQuery.noConflict();';
	}

	// Background color
	$bg_color = '#aaaaaa';
	if (isset($mybb->settings['firstpreview_bg']) && preg_match('/^#([0-9a-f]{1,6})$/i', $mybb->settings['firstpreview_bg']))
	{
		$bg_color = htmlspecialchars_uni($mybb->settings['firstpreview_bg']);
	}

	// Close button
	$close_preview = '#close_preview{display:none;cursor:pointer;background:#000;color:#fff;float:right;font-size:1em;font-weight:bold;text-align:center;width:20px;height:20px;border-radius:5px}';
	if (isset($mybb->settings['firstpreview_close']) && $mybb->settings['firstpreview_close'] == 1)
	{
		$close_preview = '#close_preview{cursor:pointer;background:#000;color:#fff;float:right;font-size:1em;font-weight:bold;text-align:center;width:20px;height:20px;border-radius:5px}';
	}

	// Insert the code
	$headerinclude .= '
	<!-- start: first_preview_plugin -->
	<style type="text/css">
	.modal_firstpost{text-align:left;border-radius:7px;-moz-border-radius:7px;-webkit-border-radius:7px;border:1px solid '.$bgcolor.';display:none;position:absolute;z-index:29000;width:390px;height:180px;overflow:hidden}
	.fpreview{z-index:29001;width:390px;height:180px;overflow:auto;background:'.$bg_color.'}
	.arrow-down{display:none;position:absolute;z-index:28999;width:0;height:0;border-left:20px solid transparent;border-right:20px solid transparent;border-top:20px solid '.$bg_color.'}
	.prev_content{padding:10px;height:auto;word-wrap:break-word;-webkit-hyphens:auto;-moz-hyphens:auto;-ms-hyphens:auto;-o-hyphens:auto;hyphens:auto;background:none}
	'.$close_preview.'
	</style>
	'.$jquery.'
	<script type="text/javascript">
	//<![CDATA[
	'.$noconflict.'
	<!--
	if(use_xmlhttprequest == 1) {
		jQuery(document).ready(function(e){e(".pmprev").on("touchenter mouseenter",function(){id=e(this).attr("id");pmid=id.replace(/[^\d.]/g,"");var t=e(this).offset().left;var n=e(this).offset().top-200;showPost=setTimeout(function(){e.ajax({url:"private.php?pmid="+pmid+"&firstpm=1",type:"post",complete:function(t){e(".modal_firstpost").html(t.responseText)}});e(".modal_firstpost").fadeIn("slow");e(".modal_firstpost").css("top",n);e(".modal_firstpost").css("left",t);e(".arrow-down").fadeIn("slow");e(".arrow-down").css("top",n+180);e(".arrow-down").css("left",t+20);},1500)});e(".pmprev").on("mouseleave touchleave touchend",function(){clearTimeout(showPost);});e(".modal_firstpost").on("mouseleave touchmove",function(){e(".modal_firstpost").fadeOut("slow");e(".arrow-down").fadeOut("fast")});e(".modal_firstpost").on("click", "#close_preview", function(){e(".modal_firstpost").fadeOut("slow");e(".arrow-down").fadeOut("fast")})});
	}
	//]]>
	</script>
	<!-- end: first_preview_plugin -->
	';
	// Get the pm preview
	if (isset($mybb->input['firstpm']) && $mybb->input['firstpm'] == 1 && $mybb->request_method == "post")
	{
		$pmid = (int)$mybb->input['pmid'];

		$query = $db->simple_select('privatemessages', '*', "pmid = '".$pmid."'");
		$pm = $db->fetch_array($query);
		// Load the users own messages only
		if ($pm['uid'] != $mybb->user['uid']) return;

		require_once MYBB_ROOT."inc/class_parser.php";
		$parser = new postParser;

		$pm['subject'] = htmlspecialchars_uni($parser->parse_badwords($pm['subject']));
		$user = get_user($pm['fromid']);

		$idtype = 'pmid';
		$parser_options['allow_html'] = $mybb->settings['pmsallowhtml'];
		$parser_options['allow_mycode'] = $mybb->settings['pmsallowmycode'];
		$parser_options['allow_smilies'] = $mybb->settings['pmsallowsmilies'];
		$parser_options['allow_imgcode'] = $mybb->settings['pmsallowimgcode'];
		$parser_options['allow_videocode'] = $mybb->settings['pmsallowvideocode'];
		$parser_options['me_username'] = $user['username'];
		$parser_options['filter_badwords'] = 1;
		$id = $pmid;
		$pm['message'] = $parser->parse_message($pm['message'], $parser_options);
		$pmdate = my_date($mybb->settings['dateformat'], $pm['dateline']);
		$pmtime = my_date($mybb->settings['timeformat'], $pm['dateline']);
		$pmsent = ' ('.$pmdate.', '.$pmtime.')';

		if (isset($mybb->settings['firstpreview_html']) && $mybb->settings['firstpreview_html'] != 1)
		{
			$pm['message'] = strip_tags($pm['message'], "<br><p><ul><ol><li>");
		}
		if (!empty($mybb->settings['firstpreview_length']) && $mybb->settings['firstpreview_length'] != "0" && my_strlen($pm['message']) > (int)$mybb->settings['firstpreview_length'])
		{
			$pm['message'] = preg_replace("!<a([^>]+)>!isU", "", $pm['message']);
			$pm['message'] = str_replace("</a>", "", $pm['message']);
			$pm['message'] = my_substr($pm['message'], 0, (int)$mybb->settings['firstpreview_length']).'...<p><a href="private.php?action=read&amp;pmid='.(int)$pm['pmid'].'">more</a></p>';
		}
		$preview = "<div class=\"fpreview\"><span id=\"close_preview\">&#10060;</span>
		<div class=\"thead\" style=\"text-align:center; font-weight:bold; min-height:20px;\">".$pm['subject']."</div>
		<div class=\"tcat\" style=\"padding-left:10px;\">".build_profile_link(format_name(htmlspecialchars_uni($user['username']), (int)$user['usergroup'], (int)$user['displaygroup']), (int)$pm['fromid'])."<span class=\"smalltext\">".$pmsent."</span></div>
		<div class=\"prev_content\">".$pm['message']."</div>
		</div>";

		header("Content-type: text/plain; charset={$charset}");
		echo $preview;
		exit;
	}
}
