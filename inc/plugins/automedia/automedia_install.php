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


// Plugin installed?
function automedia_is_installed()
{
	global $db;

	if ($db->table_exists('automedia'))
	{
		return true;
	}
		return false;
}


// Install the Plugin
function automedia_install()
{
	global $db, $mybb, $lang, $cache;

	if ($db->field_exists('automedia_use', 'users'))
	{
		$db->drop_column("users", "automedia_use");
	}

	if ($db->table_exists('automedia'))
	{
		$db->drop_table('automedia');
	}

	// Add the templates
	automedia_templates_add();

    $collation = $db->build_create_table_collation();

	// Create sites table
	$db->write_query("
		CREATE TABLE ".TABLE_PREFIX."automedia (
			`amid` int(10) unsigned NOT NULL auto_increment,
			`name` varchar(255) NOT NULL,
			`class` varchar(255) NOT NULL,
			PRIMARY KEY (amid)
		) ENGINE=MyISAM{$collation};
	");


	// DELETE ALL POSSIBLE FORMER PLUGIN SETTINGS TO AVOID DUPLICATES
	$query = $db->simple_select('settinggroups','gid','name="AutoMedia Sites"');
	$ams = $db->fetch_array($query);
	$db->delete_query('settinggroups',"gid='".$ams['gid']."'");
	$query2 = $db->simple_select('settinggroups','gid','name="AutoMedia Global"');
	$amg = $db->fetch_array($query2);
	$db->delete_query('settinggroups',"gid='".$amg['gid']."'");
	$query3 = $db->simple_select('settinggroups','gid','name="AutoMedia"');
	$am = $db->fetch_array($query3);
	$db->delete_query('settinggroups',"gid='".$am['gid']."'");
	$db->delete_query('settings',"gid='".$ams['gid']."'");
	$db->delete_query('settings',"gid='".$amg['gid']."'");
	$db->delete_query('settings',"gid='".$am['gid']."'");

/**
 *
 * Add Settings
 *
 **/

	// If MyBB version >= 1.8.1 use numeric optionscode
	$optionscode = 'numeric';
	// Else still use text optionscode
	if ($mybb->version < "1.8.1")
	{
		$optionscode = 'text';
	}

	$query = $db->simple_select("settinggroups", "COUNT(*) as rows");
	$rows = $db->fetch_field($query, "rows");

	// Add Settinggroup for Global Settings
	$automedia_group = array(
		"name" => "AutoMedia Global",
		"title" => $db->escape_string($lang->av_group_global_title),
		"description" => $db->escape_string($lang->av_group_global_descr),
		"disporder" => $rows+1,
		"isdefault" => 0
	);
	$db->insert_query("settinggroups", $automedia_group);
	$gid2 = $db->insert_id();

	// Add Settings for Global Settinggroup
	$automedia_1 = array(
		"sid" => NULL,
		"name" => "av_enable",
		"title" => $db->escape_string($lang->av_enable_title),
		"description" => $db->escape_string($lang->av_enable_descr),
		"optionscode" => "yesno",
		"value" => 1,
		"disporder" => 1,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_1);

	$automedia_2 = array(
		"sid" => NULL,
		"name" => "av_guest",
		"title" => $db->escape_string($lang->av_guest_title),
		"description" => $db->escape_string($lang->av_guest_descr),
		"optionscode" => "yesno",
		"value" => 1,
		"disporder" => 2,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_2);

	$automedia_3 = array(
		"sid" => NULL,
		"name" => "av_groups",
		"title" => $db->escape_string($lang->av_groups_title),
		"description" => $db->escape_string($lang->av_groups_descr),
		"optionscode" => "groupselect",
		"value" => '',
		"disporder" => 3,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_3);

	$automedia_4 = array(
		"sid" => NULL,
		"name" => "av_forums",
		"title" => $db->escape_string($lang->av_forums_title),
		"description" => $db->escape_string($lang->av_forums_descr),
		"optionscode" => "forumselect",
		"value" => "-1",
		"disporder" => 4,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_4);

	$automedia_5= array(
		"sid" => NULL,
		"name" => "av_adultsites",
		"title" => $db->escape_string($lang->av_adultsites_title),
		"description" => $db->escape_string($lang->av_adultsites_descr),
		"optionscode" => "yesno",
		"value" => 0,
		"disporder" => 5,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_5);

	$automedia_6 = array(
		"sid" => NULL,
		"name" => "av_adultguest",
		"title" => $db->escape_string($lang->av_adultguest_title),
		"description" => $db->escape_string($lang->av_adultguest_descr),
		"optionscode" => "yesno",
		"value" => 0,
		"disporder" => 6,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_6);

	$automedia_7 = array(
		"sid" => NULL,
		"name" => "av_adultgroups",
		"title" => $db->escape_string($lang->av_adultgroups_title),
		"description" => $db->escape_string($lang->av_adultgroups_descr),
		"optionscode" => "groupselect",
		"value" => "-1",
		"disporder" => 7,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_7);

	$automedia_8 = array(
		"sid" => NULL,
		"name" => "av_adultforums",
		"title" => $db->escape_string($lang->av_adultforums_title),
		"description" => $db->escape_string($lang->av_adultforums_descr),
		"optionscode" => "forumselect",
		"value" => "-1",
		"disporder" => 8,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_8);

	$automedia_9 = array(
		"sid" => NULL,
		"name" => "av_signature",
		"title" => $db->escape_string($lang->av_signature_title),
		"description" => $db->escape_string($lang->av_signature_descr),
		"optionscode" => "yesno",
		"value" => 0,
		"disporder" => 9,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_9);

	// setting if admins only, admins and mods or all users can embed flash files
	$automedia_10 = array(
		"sid" => NULL,
		"name" => "av_flashadmin",
		"title" => $db->escape_string($lang->av_flashadmin_title),
		"description" => $db->escape_string($lang->av_flashadmin_descr),
		"optionscode" => "radio
admin=".$db->escape_string($lang->av_flashadmin_admins)."
mods=".$db->escape_string($lang->av_flashadmin_mods)."
all=".$db->escape_string($lang->av_flashadmin_all)."",

		"value" => "all",
		"disporder" => 10,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_10);

	// add setting for max width
	$automedia_11 = array(
		"sid" => NULL,
		"name" => "av_width",
		"title" => $db->escape_string($lang->av_width_title),
		"description" => $db->escape_string($lang->av_width_descr),
		"optionscode" => $optionscode,
		"value" => "480",
		"disporder" => 11,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_11);

	// add setting for max height
	$automedia_12 = array(
		"sid" => NULL,
		"name" => "av_height",
		"title" => $db->escape_string($lang->av_height_title),
		"description" => $db->escape_string($lang->av_height_descr),
		"optionscode" => $optionscode,
		"value" => "360",
		"disporder" => 12,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_12);

	$automedia_13 = array(
		"sid" => NULL,
		"name" => "av_embera",
		"title" => $db->escape_string($lang->av_embera_title),
		"description" => $db->escape_string($lang->av_embera_descr),
		"optionscode" => "yesno",
		"value" => 1,
		"disporder" => 13,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_13);

	$automedia_14 = array(
		"sid" => NULL,
		"name" => "av_embedly",
		"title" => $db->escape_string($lang->av_embedly_title),
		"description" => $db->escape_string($lang->av_embedly_descr),
		"optionscode" => "yesno",
		"value" => 0,
		"disporder" => 14,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_14);

	$automedia_15 = array(
		"sid" => NULL,
		"name" => "av_embedly_key",
		"title" => $db->escape_string($lang->av_embedly_key_title),
		"description" => $db->escape_string($lang->av_embedly_key_descr),
		"optionscode" => "text",
		"value" => "",
		"disporder" => 15,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_15);

	$automedia_16 = array(
		"sid" => NULL,
		"name" => "av_embedly_click",
		"title" => $db->escape_string($lang->av_embedly_click_title),
		"description" => $db->escape_string($lang->av_embedly_click_descr),
		"optionscode" => "select\nembed=".$db->escape_string($lang->av_embedly_click_embed)."\nbutton=".$db->escape_string($lang->av_embedly_click_button)."\nmodal=".$db->escape_string($lang->av_embedly_click_modal)."",
		"value" => "embed",
		"disporder" => 16,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_16);

	$automedia_17 = array(
		"sid" => NULL,
		"name" => "av_embedly_links",
		"title" => $db->escape_string($lang->av_embedly_links_title),
		"description" => $db->escape_string($lang->av_embedly_links_descr),
		"optionscode" => "yesno",
		"value" => 0,
		"disporder" => 17,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_17);

	$automedia_18 = array(
		"sid" => NULL,
		"name" => "av_embedly_card",
		"title" => $db->escape_string($lang->av_embedly_card_title),
		"description" => $db->escape_string($lang->av_embedly_card_descr),
		"optionscode" => "yesno",
		"value" => 1,
		"disporder" => 18,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_18);

	$automedia_19 = array(
		"sid" => NULL,
		"name" => "av_codebuttons",
		"title" => $db->escape_string($lang->av_codebuttons_title),
		"description" => $db->escape_string($lang->av_codebuttons_descr),
		"optionscode" => "yesno",
		"value" => 1,
		"disporder" => 19,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_19);

	$automedia_20 = array(
		"sid" => NULL,
		"name" => "av_quote",
		"title" => $db->escape_string($lang->av_quote_title),
		"description" => $db->escape_string($lang->av_quote_descr),
		"optionscode" => "yesno",
		"value" => 1,
		"disporder" => 20,
		"gid" => (int)$gid2
		);
	$db->insert_query("settings", $automedia_20);

	// Add users setting
	if (!$db->field_exists("automedia_use", "users"))
	{
		$db->add_column('users', 'automedia_use', 'VARCHAR(1) NOT NULL DEFAULT "Y"');
	}

	// Refresh settings.php
	rebuild_settings();

}


// Uninstall the Plugin
function automedia_uninstall()
{
	global $db, $cache;

	// Remove the extra column
	if ($db->field_exists('automedia_use', 'users'))
	{
		$db->drop_column("users", "automedia_use");;
	}
	// Delete table automedia
	if ($db->table_exists('automedia'))
	{
		$db->drop_table('automedia');
	}

	// DELETE ALL SETTINGS
	$query = $db->simple_select('settinggroups','gid','name="AutoMedia Sites"');
	$ams = $db->fetch_array($query);
	$db->delete_query('settinggroups',"gid='".$ams['gid']."'");
	$query2 = $db->simple_select('settinggroups','gid','name="AutoMedia Global"');
	$amg = $db->fetch_array($query2);
	$db->delete_query('settinggroups',"gid='".$amg['gid']."'");
	$query3 = $db->simple_select('settinggroups','gid','name="AutoMedia"');
	$am = $db->fetch_array($query3);
	$db->delete_query('settinggroups',"gid='".$am['gid']."'");
	$db->delete_query('settings',"gid='".$ams['gid']."'");
	$db->delete_query('settings',"gid='".$amg['gid']."'");
	$db->delete_query('settings',"gid='".$am['gid']."'");

	// Refresh settings.php
	rebuild_settings();

	// Delete cache
	if (is_object($cache->handler))
	{
		$cache->handler->delete('automedia');
	}
	// Delete database cache
	$db->delete_query("datacache", "title='automedia'");

	// Delete the templates and templategroup
	$db->delete_query("templategroups", "prefix = 'automedia'");
	$db->delete_query("templates", "title LIKE 'automedia_%'");
	// Delete old template
	$db->delete_query("templates", "title = 'usercp_automedia'");


/**
 *
 * Delete [amquote], [amoff] and [ampl] tags
 *
 **/
	// Delete [amquote], [/amquote]
	$query_amquote_open = $db->simple_select('posts','*','message like "%[amquote]%"');
	$result_amquote_open = $db->num_rows($query_amquote_open);
	if ($result_amquote_open > 0)
	{
		$query = $db->query("UPDATE ".TABLE_PREFIX."posts SET message = replace(message, '[amquote]', '')");
	}
	$query_amquote_close = $db->simple_select('posts','*','message like "%[/amquote]%"');
	$result_amquote_close = $db->num_rows($query_amquote_close);
	if ($result_amquote_close > 0)
	{
		$query = $db->query("UPDATE ".TABLE_PREFIX."posts SET message = replace(message, '[/amquote]', '')");
	}
	// Delete [amoff], [/amoff]
	$query_amoff_open = $db->simple_select('posts','*','message like "%[amoff]%"');
	$result_amoff_open = $db->num_rows($query_amoff_open);
	if ($result_amoff_open > 0)
	{
		$query = $db->query("UPDATE ".TABLE_PREFIX."posts SET message = replace(message, '[amoff]', '')");
	}
	$query_amoff_close = $db->simple_select('posts','*','message like "%[/amoff]%"');
	$result_amoff_close = $db->num_rows($query_amoff_close);
	if ($result_amoff_close > 0)
	{
		$query = $db->query("UPDATE ".TABLE_PREFIX."posts SET message = replace(message, '[/amoff]', '')");
	}
	// Delete [ampl], [/ampl]
	$query_ampl_open = $db->simple_select('posts','*','message like "%[ampl]%"');
	$result_ampl_open = $db->num_rows($query_ampl_open);
	if ($result_ampl_open > 0)
	{
		$query = $db->query("UPDATE ".TABLE_PREFIX."posts SET message = replace(message, '[ampl]', '')");
	}
	$query_ampl_close = $db->simple_select('posts','*','message like "%[/ampl]%"');
	$result_ampl_close = $db->num_rows($query_ampl_close);
	if ($result_ampl_close > 0)
	{
		$query = $db->query("UPDATE ".TABLE_PREFIX."posts SET message = replace(message, '[/ampl]', '')");
	}

/**
 *
 * Delete MyCodes
 *
 **/
	// Delete MyCode to parse [amquote] tags
	$amquoteresult = $db->simple_select('mycode', 'cid', "title = 'AutoMedia Quotes (AutoMedia Plugin)'", array('limit' => 1));
	$amquotegroup = $db->fetch_array($amquoteresult);
	if (!empty($amquotegroup['cid']))
	{
		$db->delete_query('mycode',"cid='".$amquotegroup['cid']."'");
		$cache->update_mycode();
	}
	// Delete MyCode to parse [amoff] tags
	$amoffresult = $db->simple_select('mycode', 'cid', "title = 'AutoMedia Links (AutoMedia Plugin)'", array('limit' => 1));
	$amoffgroup = $db->fetch_array($amoffresult);

	if (!empty($amoffgroup['cid']))
	{
		$db->delete_query('mycode',"cid='".$amoffgroup['cid']."'");
		$cache->update_mycode();
	}
}


//Activate the Plugin
function automedia_activate()
{
	global $db, $mybb, $lang, $cache;

	change_admin_permission('tools','automedia', 1);

	// Find and activate custom modules
	$folder1 = MYBB_ROOT."inc/plugins/automedia/mediasites/";
	$folder2 = MYBB_ROOT."inc/plugins/automedia/special/";
	if (is_dir($folder1))
	{
		$mediafiles1 = scandir($folder1);

		foreach ($mediafiles1 as $sites1)
		{ // Fetch all files in the folder
			$siteinfo1 = pathinfo($folder1."/".$sites1);
			if ($sites1 != "." && $sites1 != "..")
			{
				$filetype1 = "php";
				// We need only php files
				if ($siteinfo1['extension'] == $filetype1)
				{
					$media1 = str_replace(".php", "", $sites1);
					$check1 = file_get_contents($folder1.$siteinfo1['basename']);
					if (preg_match('"function automedia_"isU', $check1))
					{
						// Is the module already installed?
						$query_ex = $db->simple_select('automedia', 'name', "name='".htmlspecialchars_uni($media1)."'");
						$modactive = $db->fetch_array($query_ex);
						if (!$modactive)
						{
							// add site
							$automedia_site1 = array(
								"name" => htmlspecialchars_uni($media1),
								"class" => "site",
							);
							$db->insert_query("automedia", $automedia_site1);
						}
					}
				}
			}
		}
	}
	if (is_dir($folder2))
	{
		$mediafiles2 = scandir($folder2);

		foreach ($mediafiles2 as $sites2)
		{ // Fetch all files in the folder
			$siteinfo2 = pathinfo($folder2."/".$sites2);
			if ($sites2 != "." && $sites2 != "..")
			{
				$filetype2 = "php";
				// We need only php files
				if ($siteinfo2['extension'] == $filetype2)
				{
					$media2 = str_replace(".php", "", $sites2);
					$check2 = file_get_contents($folder2.$siteinfo2['basename']);
					if (preg_match('"function automedia_"isU', $check2))
					{
						// Is the module already installed?
						$query_ex2 = $db->simple_select('automedia', 'name', "name='".htmlspecialchars_uni($media2)."'");
						$modactive2 = $db->fetch_array($query_ex2);
						if (!$modactive2)
						{
							// add site
							$automedia_site2 = array(
								"name" => htmlspecialchars_uni($media2),
								"class" => "special",
							);
							$db->insert_query("automedia", $automedia_site2);
						}
					}
				}
			}
		}
	}

	// Update cache
	automedia_cache();

/**
 * Edit templates
 *
 **/
	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

	find_replace_templatesets("usercp_nav_misc", "#".preg_quote('<tr><td class="trow1 smalltext"><a href="usercp.php?action=userautomedia" class="usercp_nav_item usercp_nav_options">{$lang->av_ucp_menu}</a></td></tr>')."#s",'', '',false);
	find_replace_templatesets("usercp_nav_misc", "#".preg_quote('<tr><td class="trow1 smalltext"><a href="usercp.php?action=userautomedia" class="usercp_nav_item usercp_nav_options">AutoMedia</a></td></tr>')."#s",'', '',false);
	// delete template editings by a former beta version
	find_replace_templatesets('usercp_editsig', '#\n{\$amsigpreview}<br /><br />#', '', false);

	find_replace_templatesets("usercp_nav_misc",  "#".preg_quote('{$lang->ucp_nav_view_profile}')."#i", '{$lang->ucp_nav_view_profile}</a></td></tr><tr><td class="trow1 smalltext"><a href="usercp.php?action=userautomedia" class="usercp_nav_item usercp_nav_options">{$lang->av_ucp_menu}');


	// If we are upgrading...add the new templates
	$query_tpl = $db->simple_select('templategroups','*','prefix="automedia"');
	$result_template = $db->num_rows($query_tpl);

	if (!$result_template)
	{
		automedia_templates_add();
	}

}


// Deactivate the Plugin
function automedia_deactivate()
{
	global $db, $mybb, $cache;

	change_admin_permission('tools','automedia', -1);
	automedia_cache(true);

/**
 *
 * Restore templates
 *
 **/
	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

	find_replace_templatesets("usercp_nav_misc", "#".preg_quote('</a></td></tr><tr><td class="trow1 smalltext"><a href="usercp.php?action=userautomedia" class="usercp_nav_item usercp_nav_options">{$lang->av_ucp_menu}')."#s",'', '',false);
	find_replace_templatesets("usercp_nav_misc", "#".preg_quote('<tr><td class="trow1 smalltext"><a href="usercp.php?action=userautomedia" class="usercp_nav_item usercp_nav_options">AutoMedia</a></td></tr>')."#s",'', '',false);

	// Delete master templates for upgrade
	$db->delete_query("templategroups", "prefix = 'automedia'");
	$db->delete_query("templates", "title LIKE 'automedia_%' AND sid='-2'");
}


/**
 * Add the templates.
 *
 */
function automedia_templates_add()
{
	global $db, $lang;

	$lang->load("automedia");

/**
 * Add templategroup and templates
 *
 **/
	$templategrouparray = array(
		'prefix' => 'automedia',
		'title'  => $db->escape_string($lang->av_templategroup),
		'isdefault' => 1
	);
	$db->insert_query("templategroups", $templategrouparray);

	$template_1 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_usercp",
		"template"	=> $db->escape_string('<html>
			<head>
				<title>{$mybb->settings[bbname]} - {$lang->av_ucp_title}</title>
				{$headerinclude}
			</head>
			<body>
				{$header}
				<form action="usercp.php" method="post">
				<table width="100%" border="0" align="center">
					<tr>
						{$usercpnav}
						<td valign="top">
							<table border="0" cellspacing="{$theme[borderwidth]}" cellpadding="{$theme[tablespace]}" class="tborder">
								<tr>
									<td class="thead" colspan="3"><strong>{$lang->av_ucp_title}</strong></td>
								</tr>
								<tr>
									<td align="center" class="trow1" width="60%">
										{$lang->av_ucp_label}
									</td>
									<td class="trow1" width="20%">
										<input type="radio" name="automedia" value="Y"{$av_checked_yes} />{$lang->av_ucp_yes}<br />
										<input type="radio" name="automedia" value="N"{$av_checked_no} />{$lang->av_ucp_no}
									</td>
									<td align="center" class="trow1" width="20%">
										<div>{$ucpset}</div>
									</td>
								</tr>
							</table>
							<br />
							<div align="center">
							<input type="hidden" name="action" value="do_automedia" />
							<input type="submit" class="button" name="submit" value="{$lang->av_ucp_submit}" />
							</div>
						</td>
					</tr>
				</table>
				</form>
				{$footer}
			</body>
		</html>
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_1);

	$template_2 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_head",
		"template"	=> $db->escape_string('
			<script type="text/javascript" src="{$mybb->asset_url}/jscripts/automedia/build/mediaelement-and-player.min.js?ver='.AUTOMEDIA_VER.'"></script>
			<link rel="stylesheet" href="{$mybb->asset_url}/jscripts/automedia/build/mediaelementplayer.css?ver='.AUTOMEDIA_VER.'" />
			<style type="text/css">
				.am_embed{text-align:center;margin: auto auto;width: 550px;}
				.twitter_embed{width: 550px;}
			</style>
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_2);

	$template_3 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_head_embedly",
		"template"	=> $db->escape_string('
			<script src="https://cdn.embed.ly/jquery.embedly-3.1.2.min.js" type="text/javascript"></script>
			<script type="text/javascript">
				<!--
				// Allow Embedly embedding only from the following sites, e.g. ["Twitter","YouTube","Vimeo"] (leave empty for embedding media from all available sites)
				embedly_allowed_providers = [];

				// Embed following site previews with embed.ly even when link embedding is disabled
				embedly_allowed_links = [
					"Wikipedia",
					"Imdb",
					"Whosay",
					"Twitter",
					"Instagram",
					"Github"
				];
				-->
			</script>
			<style type="text/css">
				#bg_embed{position:fixed;top:0;left:0;height:100%;width:100%;background:rgba(0,0,0,0.5);z-index:29000;display:none}
				#video-modal{border:1px solid #fff;box-shadow:0 2px 7px #292929;-moz-box-shadow:0 2px 7px #292929;-webkit-box-shadow:0 2px 7px #292929;border-radius:10px;-moz-border-radius:10px;-webkit-border-radius:10px;display:none;z-index:29999;padding:20px}
			</style>
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_3);

	$template_4 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_embedly_modal_card",
		"template"	=> $db->escape_string('
				<script type="text/javascript">
					<!--
					embedly_links = {$mybb->settings[\'av_embedly_links\']};
					$.embedly.defaults = {
						key:"{$mybb->settings[\'av_embedly_key\']}",
						query: {
							maxwidth:{$mybb->settings[\'av_width\']},
							maxheight:{$mybb->settings[\'av_height\']},
						}
					}

					$(".oembed").embedly({
						display: function(edata, elem){
							if (edata.invalid !== true && edata.type !== "error" ) {
								if (embedly_allowed_providers.length > 0 && $.inArray(""+edata.provider_name+"", embedly_allowed_providers) === -1 ) return false;
								if (embedly_links === 1 || $.inArray(""+edata.provider_name+"", embedly_allowed_links) !== -1 ) {
									if (edata.provider_name === "Twitter" && $(this).attr("href").match(/status/i) || edata.provider_name !== "Twitter") {
										$(this).addClass("am_embedly");
									}
								} else if ((edata.type !== "link" && embedly_links !== 1)) {
									$(this).addClass("am_embedly");
								}
							}
						}
					});

					$(document).on("click", ".am_embedly", function(){
						var url = $(this).attr("href");
						$("#video-modal").hide();
						$("#video-modal").remove();
						$(this).after("<div id=\'bg_embed\'></div><div id=\'video-modal\' style=\'display: none; text-align: center; width: {$modalwidth}px; border: 2px solid; border-radius: 7px;\' tabindex=\'-1\' role=\'dialog\'><div class=\'modal-header tcat\'><h3></h3></div><div class=\'modal-body trow1\' style=\'padding: 20px; border-radius: 7px;\'><p></p></div></div>");

						$("#video-modal").on("hide", function(){
							$("#video-modal .modal-body").html("");
						});

						$.embedly.oembed(url).progress(function(data){
							if (data.invalid !== true && data.type !== "error") {
								if (embedly_allowed_providers.length > 0 && $.inArray(""+data.provider_name+"", embedly_allowed_providers) === -1 ) return false;
								$("#bg_embed").fadeIn(500, function() {
									$("#video-modal .modal-header h3").html(data.title);
									$("#video-modal .modal-body").html("<a class=\'embedly-card\'  href=\'"+url+"\'>"+data.title+"</a>");
									$("#video-modal").show();
									$("#video-modal").centerInClient({ container: window, forceAbsolute: true });
								});
							}
						});
						return false;
					});

					$(document).on("click", "#video-modal, #bg_embed", function(){
						$("#video-modal").hide();
						$("#video-modal").remove();
						$("#bg_embed").hide();
					});

					-->
				</script>
				<script type="text/javascript" async src="//cdn.embedly.com/widgets/platform.js?ver='.AUTOMEDIA_VER.'" charset="UTF-8"></script>
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_4);

	$template_5 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_embedly_button_card",
		"template"	=> $db->escape_string('
				<script type="text/javascript">
					<!--
					embedly_links = {$mybb->settings[\'av_embedly_links\']};
					$(".oembed").embedly({
						key:"{$mybb->settings[\'av_embedly_key\']}",
						query: {
							maxwidth:{$mybb->settings[\'av_width\']},
							maxheight:{$mybb->settings[\'av_height\']},
						},
						display: function(data, elem){
							if (data.invalid !== true && data.type !== "error" ) {
								if (embedly_allowed_providers.length > 0 && $.inArray(""+data.provider_name+"", embedly_allowed_providers) === -1 ) return false;
								if (data.type !== "link" || (data.type === "link" && embedly_links === 1) || $.inArray(""+data.provider_name+"", embedly_allowed_links) !== -1 ) {
									if (data.provider_name === "Twitter" && $(elem).attr("href").match(/status/i) || data.provider_name !== "Twitter") {
										$(this).prepend("<span class=\'embed_show\' style=\'margin: 10px;\'><input type=\'button\' class=\'button\' value=\'{$lang->av_click}\' /></span>");
									}
								}
								$(".embed_show").on("click", function(){
									$(this).parent().html("<a class=\'embedly-card\' href=\'"+$(this).parent().attr("href")+"\'>"+data.title+"</a>");
									return false;
								});
							}
						}
					});
					-->
				</script>
				<script type="text/javascript" async src="//cdn.embedly.com/widgets/platform.js?ver='.AUTOMEDIA_VER.'" charset="UTF-8"></script>
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_5);

	$template_6 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_embedly_direct_card",
		"template"	=> $db->escape_string('
				<script type="text/javascript">
					<!--
					embedly_links = {$mybb->settings[\'av_embedly_links\']};
					$(".oembed").embedly({
						key:"{$mybb->settings[\'av_embedly_key\']}",
						query: {
							maxwidth:{$mybb->settings[\'av_width\']},
							maxheight:{$mybb->settings[\'av_height\']},
							method:"after",
						},
						 display: function(data, elem) {
							if (data.invalid !== true && data.type !== "error" ) {
								if (embedly_allowed_providers.length > 0 && $.inArray(""+data.provider_name+"", embedly_allowed_providers) === -1 ) return false;
								cardtype = "article";
								if (data.type === "video") {
									cardtype = "video";
								} else if  (data.type === "photo") {
									cardtype = "image";
								} else if  (data.type === "rich") {
									cardtype = "rich";
								}
								if (data.type !== "link" || (data.type === "link" && embedly_links === 1) || $.inArray(""+data.provider_name+"", embedly_allowed_links) !== -1 ) {
									if (data.provider_name === "Twitter" && $(elem).attr("href").match(/status/i) || data.provider_name !== "Twitter") {
										$(elem).html("<a class=\'embedly-card\' data-card-type=\'"+cardtype+"\' href=\'"+$(this).attr("href")+"\'>"+data.title+"</a>");
									}
								}
							}
						}
					});
					-->
				</script>
				<script type="text/javascript" async src="//cdn.embedly.com/widgets/platform.js?ver='.AUTOMEDIA_VER.'" charset="UTF-8"></script>
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_6);

	$template_7 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_codebuttons",
		"template"	=> $db->escape_string('
			<br />
			<img id="amoff" src="{$mybb->asset_url}/images/amoff.png" width="28" height="28" alt="{$amoff}" title="{$amoff}" />&nbsp;&nbsp;
			<img id="ampl" src="{$mybb->asset_url}/images/ampl.png" width="28" height="28" alt="{$ampl}" title="{$ampl}" /><br /><br />
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_7);

	$template_8 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_codebuttons_private",
		"template"	=> $db->escape_string('
			<br />
			<img id="amoff" src="{$mybb->asset_url}/images/amoff.png" width="28" height="28" alt="{$amoff}" title="{$amoff}" />&nbsp;&nbsp;
			<img id="ampl" src="{$mybb->asset_url}/images/ampl.png" width="28" height="28" alt="{$ampl}" title="{$ampl}" /><br /><br />
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_8);

	$template_9 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_codebuttons_footer",
		"template"	=> $db->escape_string('
			<script type="text/javascript" src="{$mybb->asset_url}/jscripts/automedia/rangyinputs_jquery.min.js?ver='.AUTOMEDIA_VER.'"></script>
			<script type="text/javascript">
			<!--
			jQuery(document).ready(function($)
			{
				$("#amoff").on("click", function() {
					if(!MyBBEditor) {
						$("#message, #signature").surroundSelectedText("[amoff]", "[/amoff]");
					} else {
						MyBBEditor.insert(\'[amoff]\', \'[/amoff]\');
					}
				});
				$("#ampl").on("click", function() {
					if(!MyBBEditor) {
						$("#message, #signature").surroundSelectedText("[ampl]", "[/ampl]");
					} else {
						MyBBEditor.insert(\'[ampl]\', \'[/ampl]\');
					}
				});
			});
			-->
			</script>
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_9);

	$template_10 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_videocount",
		"template"	=> $db->escape_string('
			<div style="color:#FF0000"><strong><u>{$lang->av_vidcount} {$mybb->settings[\'maxpostvideos\']}</u></strong></div>
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_10);

	$template_11 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_ucpstatus_up",
		"template"	=> $db->escape_string('
			&nbsp;<b>{$lang->av_ucp_status}</b><br /> <img src="{$mybb->asset_url}/images/icons/thumbsup.png" alt="{$lang->av_ucp_yes}" />'
		),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW	);
	$db->insert_query("templates", $template_11);

	$template_12 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_ucpstatus_down",
		"template"	=> $db->escape_string('
			&nbsp;<b>{$lang->av_ucp_status}</b><br /> <img src="{$mybb->asset_url}/images/icons/thumbsdown.png" alt="{$lang->av_ucp_no}" />'
		),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_12);

	$template_13 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_embedly_modal",
		"template"	=> $db->escape_string('
				<script type="text/javascript">
					<!--
					embedly_links = {$mybb->settings[\'av_embedly_links\']};
					$.embedly.defaults = {
						key:"{$mybb->settings[\'av_embedly_key\']}",
						query: {
							maxwidth:{$mybb->settings[\'av_width\']},
							maxheight:{$mybb->settings[\'av_height\']},
						}
					}

					$(".oembed").embedly({
						display: function(edata, elem){
							if (edata.invalid !== true && edata.type !== "error" ) {
								if (embedly_allowed_providers.length > 0 && $.inArray(""+edata.provider_name+"", embedly_allowed_providers) === -1 ) return false;
								if (embedly_links === 1 || $.inArray(""+edata.provider_name+"", embedly_allowed_links) !== -1 ) {
									if (edata.provider_name === "Twitter" && $(this).attr("href").match(/status/i) || edata.provider_name !== "Twitter") {
										$(this).addClass("am_embedly");
									}
								} else if ((edata.type !== "link" && embedly_links !== 1)) {
									$(this).addClass("am_embedly");
								}
							}
						}
					});

					$(document).on("click", ".am_embedly", function(){
						var url = $(this).attr("href");
						$("#video-modal").hide();
						$("#video-modal").remove();
						$(this).after("<div id=\'bg_embed\'></div><div id=\'video-modal\' style=\'display: none; text-align: center; width: {$modalwidth}px; border: 2px solid; border-radius: 7px;\' tabindex=\'-1\' role=\'dialog\'><div class=\'modal-header tcat\'><h3></h3></div><div class=\'modal-body trow1\' style=\'padding: 20px; border-radius: 7px;\'><p></p></div></div>");

						$("#video-modal").on("hide", function(){
							$("#video-modal .modal-body").html("");
						});

						$.embedly.oembed(url).progress(function(data){
							if (data.invalid !== true && data.type !== "error") {
								if (embedly_allowed_providers.length > 0 && $.inArray(""+data.provider_name+"", embedly_allowed_providers) === -1 ) return false;
								$("#bg_embed").fadeIn(500, function() {
									if (data.type === "video" || data.type === "rich") {
										$("#video-modal .modal-header h3").html(data.title);
										$("#video-modal .modal-body").html(data.html);
										$("#video-modal").show();
									} else if (data.type === "photo") {
										$("#video-modal .modal-header h3").html(data.title);
										$("#video-modal").html("<div style=\'text-align: center;\'><div><a href=\'"+url+"\' target=\'_blank\'><h3>"+data.url+"</h3></a></div><img src=\'"+data.url+"\' width=\'"+data.width+"\' height=\'auto\' alt=\'"+data.provider_name+"\' /> <div style=\'padding: 20px;\'>"+data.description+"</div></div>");
										$("#video-modal").show();
									} else if (data.type === "link" && embedly_links === 1 || $.inArray(""+data.provider_name+"", embedly_allowed_links) !== -1  ) {
										if (data.provider_name === "Twitter" && url.match(/status/i) || data.provider_name !== "Twitter") {
											if (typeof data.thumbnail_url !== "undefined") {
												$("#video-modal .modal-header h3").html(data.title);
												$("#video-modal .modal-body").html("<img src=\'"+data.thumbnail_url+"\' width=\'200px\' height=\'auto\' alt=\'"+data.provider_name+"\' /> <div style=\'padding: 20px;\'>"+data.description+"</div><div style=\'text-align: center; font-weight: bold;\'><a href=\'"+data.url+"\' target=\'_blank\'>"+data.url+"</a></div>");
												$("#video-modal").show();
											} else if (typeof data.thumbnail_url === "undefined" && typeof data.description !== "undefined") {
												$("#video-modal .modal-header h3").html(data.title);
												$("#video-modal .modal-body").html("<div style=\'padding: 20px;\'>"+data.description+"</div><div style=\'text-align: center; font-weight: bold;\'><a href=\'"+data.url+"\' target=\'_blank\'>"+data.url+"</a></div>");
												$("#video-modal").show();
											} else {
												$("#video-modal .modal-header h3").html(data.title);
												$("#video-modal .modal-body").html("<div style=\'text-align: center; font-weight: bold; padding: 20px;\'><a href=\'"+data.url+"\' target=\'_blank\'>"+data.url+"</a></div>");
												$("#video-modal").show();
											}
										}
									}
								});
							}
						});
						return false;
					});

					$(document).on("click", "#video-modal, #bg_embed", function(){
						$("#video-modal").hide();
						$("#video-modal").remove();
						$("#bg_embed").hide();
					});

					-->
				</script>
				<script type="text/javascript" async src="//cdn.embedly.com/widgets/platform.js?ver='.AUTOMEDIA_VER.'" charset="UTF-8"></script>
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_13);

	$template_14 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_embedly_button",
		"template"	=> $db->escape_string('
				<script type="text/javascript">
					<!--
					embedly_links = {$mybb->settings[\'av_embedly_links\']};
					$.embedly.defaults = {
						key:"{$mybb->settings[\'av_embedly_key\']}",
						query: {
							maxwidth:{$mybb->settings[\'av_width\']},
							maxheight:{$mybb->settings[\'av_height\']},
						}
					}

					$(".oembed").embedly({
						display: function(data, elem){
							if (data.invalid !== true && data.type !== "error" ) {
								if (embedly_allowed_providers.length > 0 && $.inArray(""+data.provider_name+"", embedly_allowed_providers) === -1 ) return false;
								if (data.type !== "link" || (data.type === "link" && embedly_links === 1) || $.inArray(""+data.provider_name+"", embedly_allowed_links) !== -1 ) {
									$(this).prepend("<span class=\'embed_show\' style=\'margin: 10px;\'><input type=\'button\' class=\'button\' value=\'{$lang->av_click}\' /></span>");
								}
								$(".embed_show").on("click", function(){
									if (data.type === "video" || data.type === "rich") {
										$(this).parent().html("<div class=\'am_embed\'>" + data.html + "</div>");
									} else if (data.type === "photo") {
										$(this).parent().html("<div style=\'text-align: center;\'><div><h3>"+data.title+"</h3></div><img src=\'"+data.url+"\' width=\'"+data.width+"\' height=\'auto\' alt=\'"+data.provider_name+"\' /> <div style=\'padding: 20px;\'>"+data.description+"</div></div>");
									} else if (data.provider_name === "Twitter" && $(elem).attr("href").match(/status/i) || data.provider_name !== "Twitter") {
										$(this).parent().html("<img src=\'"+data.thumbnail_url+"\' width=\'"+thumbnail_width+"\' height=\'auto\' alt=\'"+data.provider_name+"\' /> <div style=\'padding: 20px;\'>"+data.description+"</div><div style=\'text-align: center; font-weight: bold;\'><a href=\'"+data.url+"\' target=\'_blank\'>"+data.url+"</a></div>");
									}
									return false;
								});
							}
						}
					});
					-->
				</script>
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_14);

	$template_15 = array(
		"tid" 		=> "NULL",
		"title"		=> "automedia_embedly_direct",
		"template"	=> $db->escape_string('
				<script type="text/javascript">
					<!--
					embedly_links = {$mybb->settings[\'av_embedly_links\']};
					$(".oembed").embedly({
						key:"{$mybb->settings[\'av_embedly_key\']}",
						query: {
							maxwidth:{$mybb->settings[\'av_width\']},
							maxheight:{$mybb->settings[\'av_height\']},
							method:"after",
						},
						 display: function(data, elem) {
							if (data.invalid !== true && data.type !== "error" ) {
								if (embedly_allowed_providers.length > 0 && $.inArray(""+data.provider_name+"", embedly_allowed_providers) === -1 ) return false;
								if (data.type === "video" || data.type === "rich") {
									$(elem).html("<div class=\'am_embed\'>" + data.html + "</div>");
								} else if (data.type === "photo") {
									$(elem).html("<div style=\'text-align: center;\'><div><h3>"+data.title+"</h3></div><img src=\'"+data.url+"\' width=\'"+data.width+"\' height=\'auto\' alt=\'"+data.provider_name+"\' /> <div style=\'padding: 20px;\'>"+data.description+"</div></div>");
								} else if (data.type === "link" && embedly_links === 1 || $.inArray(""+data.provider_name+"", embedly_allowed_links) !== -1 ) {
									if (data.provider_name === "Twitter" && $(elem).attr("href").match(/status/i) || data.provider_name !== "Twitter") {
										$(elem).html("<img src=\'"+data.thumbnail_url+"\' width=\'"+data.thumbnail_width+"\' height=\'auto\' alt=\'"+data.provider_name+"\' /> <div style=\'padding: 20px;\'>"+data.description+"</div><div style=\text-align: center; font-weight: bold;\'><a href=\'"+data.url+"\' target=\'_blank\'>"+data.url+"</a></div>");
									}
								}
							}
						}
					});
					-->
				</script>
		'),
		"sid"	=> -2,
		'version'	=> AUTOMEDIA_VER,
		'dateline'	=> TIME_NOW
	);
	$db->insert_query("templates", $template_15);
}
