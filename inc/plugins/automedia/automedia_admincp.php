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


// Reapply template edits
$plugins->add_hook("admin_style_themes_add_commit", "automedia_reapply_template_edits");
$plugins->add_hook("admin_style_themes_import_commit", "automedia_reapply_template_edits");

function automedia_reapply_template_edits()
{
	require_once MYBB_ROOT."/inc/adminfunctions_templates.php";

	find_replace_templatesets("usercp_nav_misc", "#".preg_quote('</a></td></tr><tr><td class="trow1 smalltext"><a href="usercp.php?action=userautomedia" class="usercp_nav_item usercp_nav_options">{$lang->av_ucp_menu}')."#s",'', '',false);
	find_replace_templatesets("usercp_nav_misc", "#".preg_quote('<tr><td class="trow1 smalltext"><a href="usercp.php?action=userautomedia" class="usercp_nav_item usercp_nav_options">AutoMedia</a></td></tr>')."#s",'', '',false);
	find_replace_templatesets("usercp_nav_misc",  "#".preg_quote('{$lang->ucp_nav_view_profile}')."#i", '{$lang->ucp_nav_view_profile}</a></td></tr><tr><td class="trow1 smalltext"><a href="usercp.php?action=userautomedia" class="usercp_nav_item usercp_nav_options">{$lang->av_ucp_menu}');
}


// Settinggroup for peeker
$plugins->add_hook("admin_config_settings_change","automedia_settings_change");

function automedia_settings_change()
{
	global $db, $mybb, $automedia_settings_peeker;

	$result = $db->simple_select("settinggroups", "gid", "name='AutoMedia Global'", array("limit" => 1));
	$group = $db->fetch_array($result);
	$automedia_settings_peeker = ($mybb->input["gid"] == $group["gid"]) && ($mybb->request_method!="post");
}


// Add the peekers
$plugins->add_hook("admin_settings_print_peekers", "automedia_settings_peek");

function automedia_settings_peek(&$peekers)
{
	global $automedia_settings_peeker;

	if ($automedia_settings_peeker)
	{
		// Peeker for adult sites settings
		$peekers[] = 'new Peeker($(".setting_av_adultsites"), $("#row_setting_av_adultguest"),/1/,true)';
		$peekers[] = 'new Peeker($(".setting_av_adultsites"), $("#row_setting_av_adultgroups"),/1/,true)';
		$peekers[] = 'new Peeker($(".setting_av_adultsites"), $("#row_setting_av_adultforums"),/1/,true)';

		// Peeker for embedly settings
		$peekers[] = 'new Peeker($(".setting_av_embedly"), $("#row_setting_av_embedly_key"),/1/,true)';
		$peekers[] = 'new Peeker($(".setting_av_embedly"), $("#row_setting_av_embedly_click"),/1/,true)';
		$peekers[] = 'new Peeker($(".setting_av_embedly"), $("#row_setting_av_embedly_links"),/1/,true)';
		$peekers[] = 'new Peeker($(".setting_av_embedly"), $("#row_setting_av_embedly_card"),/1/,true)';

	}
}


// ACP menu entry
$plugins->add_hook("admin_tools_menu", "automedia_admin_tools_menu");

function automedia_admin_tools_menu(&$sub_menu)
{
	global $lang;

	if (!isset($lang->automedia))
	{
		$lang->load("automedia");
	}

	$sub_menu[] = array('id' => 'automedia', 'title' => $lang->automedia, 'link' => 'index.php?module=tools-automedia');
}


// Set action handler
$plugins->add_hook("admin_tools_action_handler", "automedia_admin_tools_action_handler");

function automedia_admin_tools_action_handler(&$actions)
{
	$actions['automedia'] = array('active' => 'automedia', 'file' => 'automedia');
}


// Admin permissions
$plugins->add_hook("admin_tools_permissions", "automedia_admin_tools_permissions");

function automedia_admin_tools_permissions(&$admin_permissions)
{
	global $lang;
	if (!isset($lang->can_view_automedia))
	{
		$lang->load("automedia");
	}
	$admin_permissions['automedia'] = $lang->can_view_automedia;
}


// Show installed modules in ACP
$plugins->add_hook("admin_load", "automedia_admin");

function automedia_admin()
{
	global $db, $lang, $mybb, $page, $cache, $run_module, $action_file;
	if (!isset($lang->automedia_modules))
	{
		$lang->load("automedia");
	}

	if ($page->active_action != 'automedia')
	{
		return false;
	}

	if ($run_module == 'tools' && $action_file == 'automedia')
	{
		$page->add_breadcrumb_item($lang->automedia, 'index.php?module=tools-automedia');

		// Show site modules
		if ($mybb->input['action'] == "" || !$mybb->input['action'])
		{
			$page->add_breadcrumb_item($lang->automedia_modules);
			$page->output_header($lang->automedia_modules.' - '.$lang->automedia_modules);

			$sub_tabs['automedia'] = array(
				'title'			=> $lang->automedia_modules,
				'link'			=> 'index.php?module=tools-automedia',
				'description'	=> $lang->automedia_modules_description1
			);
			if ($mybb->settings['av_adultsites'] == 1)
			{
				$sub_tabs['special'] = array(
					'title'=>$lang->automedia_adult,
					'link'=>'index.php?module=tools-automedia&amp;action=adult',
					'description'=>$lang->automedia_adult_description1
				);
			}
			$page->output_nav_tabs($sub_tabs, 'automedia');

			$aotable = new Table;
			$aotable->construct_header('#');
			$aotable->construct_header($lang->automedia_oembed_desc);
			if (isset($mybb->settings['av_embera']) && $mybb->settings['av_embera'] == 1)
			{
				$aotable->construct_cell('<img src="styles/default/images/icons/success.png" width="16px" height="16px" alt="OK" />');
				$aotable->construct_cell($lang->automedia_modules_embera);
				$aotable->construct_row();
			}
			if (isset($mybb->settings['av_embedly']) && $mybb->settings['av_embedly'] == 1 && !empty($mybb->settings['av_embedly_key']) && $mybb->settings['av_embedly_key'] != "")
			{
				$aotable->construct_cell('<img src="styles/default/images/icons/success.png" width="16px" height="16px" alt="'.$lang->automedia_modules_success.'" />');
				$aotable->construct_cell($lang->automedia_modules_embedly);
				$aotable->construct_row();
			}
			$aotable->output($lang->automedia_oembed);

			$amtable = new Table;
			$amtable->construct_header('#');
			$amtable->construct_header($lang->automedia_modules_description2);
			$amtable->construct_header('<div style="text-align: center;">'.$lang->automedia_modules_status.'</div>');
			$amtable->construct_header('<div style="text-align: center;">'.$lang->automedia_modules_options.':</div>');

			$folder = MYBB_ROOT."inc/plugins/automedia/mediasites/";
			if (is_dir($folder))
			{
				$mediafiles = scandir($folder);
				$mediatitles = str_replace(".php", "", $mediafiles);
				$query = $db->simple_select('automedia', 'name', "class='site'");
				// Find missing files for active modules
				while ($missing = $db->fetch_array($query))
				{
					if (!in_array($missing['name'], $mediatitles))
					{
						$missingfile = ucfirst(htmlspecialchars_uni($missing['name']));
						$amtable->construct_cell('<strong>!</strong>');
						$amtable->construct_cell('<strong>'.$missingfile.'</strong> (<a href="'.$sub_tabs['automedia']['link'].'&amp;action=deactivate&amp;site='.urlencode($missing['name']).'&amp;my_post_key='.$mybb->post_code.'"><strong>'.$lang->automedia_modules_deactivate.'</strong></a>)');
						$amtable->construct_cell($lang->automedia_modules_notfound.' '.$folder.''.htmlspecialchars_uni($missing['name']).'.php', array('colspan' => '2'));
						$amtable->construct_row();
					}
				}

				$i = 1;
				foreach ($mediafiles as $sites)
				{ // Fetch all files in the folder
					$siteinfo = pathinfo($folder."/".$sites);
					if ($sites != "." && $sites != "..")
					{
						$filetype = "php";
						// We need only php files
						if ($siteinfo['extension'] == $filetype)
						{
							$site = str_replace(".php", "", $sites);
							$media = ucfirst(htmlspecialchars_uni($site));
							$check = file_get_contents($folder.$siteinfo['basename']);
							if (preg_match('"function automedia_"isU', $check))
							{
								$amtable->construct_cell($i);
								$amtable->construct_cell('<a href="'.$sub_tabs['automedia']['link'].'&amp;action=showsite&amp;site='.urlencode($site).'&amp;my_post_key='.$mybb->post_code.'"><strong>'.$media.'</strong></a>');
								$query2 = $db->simple_select('automedia', '*', "name='".htmlspecialchars_uni($site)."'");
								$active = $db->fetch_array($query2);
								if ($active && $active['class'] == "site")
								{
									$amtable->construct_cell('<div style="text-align: center;"><img src="'.$mybb->asset_url.'/images/mod-on.png" width="32" height="32" alt="'.$lang->automedia_modules_success.'" />');
									$amtable->construct_cell('<div style="text-align: center;"><a href="'.$sub_tabs['automedia']['link'].'&amp;action=deactivate&amp;site='.urlencode($site).'&amp;my_post_key='.$mybb->post_code.'"><strong>'.$lang->automedia_modules_deactivate.'</strong></a></div>');
								} else {
									$amtable->construct_cell('<div style="text-align: center;"><img src="'.$mybb->asset_url.'/images/mod-off.png" width="32" height="32" alt="'.$lang->automedia_modules_fail.'" />');
									$amtable->construct_cell('<div style="text-align: center;"><a href="'.$sub_tabs['automedia']['link'].'&amp;action=activate&amp;site='.urlencode($site).'&amp;my_post_key='.$mybb->post_code.'"><strong>'.$lang->automedia_modules_activate.'</strong></a></div>');
								}
								$amtable->construct_row();
								$i++;
							}
						}
					}
				}
				if ($amtable->num_rows() == 0)
				{
					$amtable->construct_cell($lang->automedia_modules, array('colspan' => '4'));
					$amtable->construct_row();
				}
			}
			else
			{
				$amtable->construct_cell($lang->automedia_modules_missing_sitesfolder, array('colspan' => '4'));
				$amtable->construct_row();
			}

			$amtable->output($lang->automedia_modules);
			echo '<div style="text-align: center;">
			<a href="'.$sub_tabs['automedia']['link'].'&amp;action=activateallsites&amp;my_post_key='.$mybb->post_code.'"><span style="border: 3px double #0F5C8E;	padding: 3px;	background: #fff url(images/submit_bg.png) repeat-x top;	color: #0F5C8E;	margin-right: 3px;">'.$lang->automedia_modules_activateall.'</span></a>
			</div>';
			$page->output_footer();
		}

		// Show special modules
		if ($mybb->input['action'] == "adult" && $mybb->settings['av_adultsites'] == 1)
		{
			$page->add_breadcrumb_item($lang->automedia_adult);
			$page->output_header($lang->automedia_modules.' - '.$lang->automedia_adult);

			$sub_tabs['automedia'] = array(
				'title'			=> $lang->automedia_modules,
				'link'			=> 'index.php?module=tools-automedia',
				'description'	=> $lang->automedia_modules
			);
			if ($mybb->settings['av_adultsites'] == 1)
			{
				$sub_tabs['special'] = array(
					'title'=>$lang->automedia_adult,
					'link'=>'index.php?module=tools-automedia&amp;action=adult',
					'description'=>$lang->automedia_adult_description1
				);
			}
			$page->output_nav_tabs($sub_tabs, 'special');
			$amtable = new Table;
			$amtable->construct_header('#');
			$amtable->construct_header($lang->automedia_modules_description2);
			$amtable->construct_header('<div style="text-align: center;">'.$lang->automedia_modules_status.'</div>');
			$amtable->construct_header('<div style="text-align: center;">'.$lang->automedia_modules_options.':</div>');

			$folder = MYBB_ROOT."inc/plugins/automedia/special/";
			if (is_dir($folder))
			{
				$mediafiles = scandir($folder);
				$mediatitles = str_replace(".php", "", $mediafiles);
				$query = $db->simple_select('automedia', 'name', "class='special'");
				// Find missing files for active modules
				while ($missing = $db->fetch_array($query))
				{
					if (!in_array($missing['name'], $mediatitles))
					{
						$missingfile = ucfirst(htmlspecialchars_uni($missing['name']));
						$amtable->construct_cell('<strong>!</strong>');
						$amtable->construct_cell('<strong>'.$missingfile.'</strong> (<a href="'.$sub_tabs['automedia']['link'].'&amp;action=adultdeactivate&amp;site='.urlencode($missing['name']).'&amp;my_post_key='.$mybb->post_code.'"><strong>'.$lang->automedia_modules_deactivate.'</strong></a>)');
						$amtable->construct_cell($lang->automedia_modules_notfound.' '.$folder.''.htmlspecialchars_uni($missing['name']).'.php', array('colspan' => '2'));
						$amtable->construct_row();
					}
				}

				$i = 1;
				foreach ($mediafiles as $sites)
				{ // Fetch all files in the folder
					$siteinfo = pathinfo($folder."/".$sites);
					if ($sites != "." && $sites != "..")
					{
						$filetype = "php";
						// We need only php files
						if ($siteinfo['extension'] == $filetype)
						{
							$site = str_replace(".php", "", $sites);
							$media = ucfirst(htmlspecialchars_uni($site));
							$check = file_get_contents($folder.$siteinfo['basename']);
							if (preg_match('"function automedia_"isU', $check))
							{
								$amtable->construct_cell($i);
								$amtable->construct_cell('<a href="'.$sub_tabs['automedia']['link'].'&amp;action=showspecial&amp;site='.urlencode($site).'&amp;my_post_key='.$mybb->post_code.'"><strong>'.$media.'</strong></a>');
								$query = $db->simple_select('automedia', '*', "name='".htmlspecialchars_uni($site)."'");
								$active = $db->fetch_array($query);
								if ($active && $active['class'] == "special")
								{
									$amtable->construct_cell('<div style="text-align: center;"><img src="'.$mybb->asset_url.'/images/mod-on.png" width="32" height="32" alt="'.$lang->automedia_modules_success.'" />');
									$amtable->construct_cell('<div style="text-align: center;"><a href="'.$sub_tabs['automedia']['link'].'&amp;action=adultdeactivate&amp;site='.urlencode($site).'&amp;my_post_key='.$mybb->post_code.'"><strong>'.$lang->automedia_modules_deactivate.'</strong></a></div>');
								} else {
									$amtable->construct_cell('<div style="text-align: center;"><img src="'.$mybb->asset_url.'/images/mod-off.png" width="32" height="32" alt="'.$lang->automedia_modules_fail.'" />');
									$amtable->construct_cell('<div style="text-align: center;"><a href="'.$sub_tabs['automedia']['link'].'&amp;action=adultactivate&amp;site='.urlencode($site).'&amp;my_post_key='.$mybb->post_code.'"><strong>'.$lang->automedia_modules_activate.'</strong></a></div>');
								}
								$amtable->construct_row();
								$i++;
							}
						}
					}
				}
				if ($amtable->num_rows() == 0)
				{
					$amtable->construct_cell($lang->automedia_adult, array('colspan' => '4'));
					$amtable->construct_row();
				}
			}
			else
			{
				$amtable->construct_cell($lang->automedia_modules_missing_specialfolder, array('colspan' => '4'));
				$amtable->construct_row();
			}

			$amtable->output($lang->automedia_modules);
			echo '<div style="text-align: center;">
			<a href="'.$sub_tabs['automedia']['link'].'&amp;action=activateallspecial&amp;my_post_key='.$mybb->post_code.'"><span style="border: 3px double #0F5C8E;	padding: 3px;	background: #fff url(images/submit_bg.png) repeat-x top;	color: #0F5C8E;	margin-right: 3px;">'.$lang->automedia_modules_activateall.'</span></a>
			</div>';
			$page->output_footer();
		}

		// Activate site module
		if ($mybb->input['action'] == 'activate')
		{
			if (!verify_post_check($mybb->input['my_post_key']))
			{
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=tools-automedia");
			}
			else
			{
				$site = htmlspecialchars_uni($mybb->input['site']);
				$query_act1 = $db->simple_select('automedia', '*', "name='".$site."'");
				$active1 = $db->fetch_array($query_act1);
				if (!$active1)
				{
					$automedia_site = array(
						"name" => $site,
						"class" => "site",
					);
					$db->insert_query("automedia", $automedia_site);
					automedia_cache();

					$mybb->input['module'] = $lang->av_plugin_title;
					$mybb->input['action'] = $lang->automedia_modules_active." ";
					log_admin_action(ucfirst($site));

					flash_message($lang->automedia_modules_active, 'success');
					admin_redirect("index.php?module=tools-automedia");
				}
				else
				{
					flash_message($lang->automedia_modules_notfound,'error');
				}
			}
			exit();
		}

		// Activate special module
		if ($mybb->input['action'] == 'adultactivate')
		{
			if (!verify_post_check($mybb->input['my_post_key']))
			{
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=tools-automedia&action=adult");
			}
			else
			{
				$site = htmlspecialchars_uni($mybb->input['site']);
				$query_act2 = $db->simple_select('automedia', '*', "name='".$site."'");
				$active2 = $db->fetch_array($query_act2);
				if (!$active2)
				{
					$automedia_special = array(
						"name" => $site,
						"class" => "special",
					);
					$db->insert_query("automedia", $automedia_special);
					automedia_cache();

					$mybb->input['module'] = $lang->av_plugin_title;
					$mybb->input['action'] = $lang->automedia_modules_active." ";
					log_admin_action(ucfirst($site));

					flash_message($lang->automedia_modules_active, 'success');
					admin_redirect("index.php?module=tools-automedia&action=adult");
				}
				else
				{
					flash_message($lang->automedia_modules_notfound,'error');
				}
			}
			exit();
		}

		// Deactivate site module
		if ($mybb->input['action'] == 'deactivate')
		{
			if (!verify_post_check($mybb->input['my_post_key']))
			{
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=tools-automedia");
			}
			else
			{
				$site = htmlspecialchars_uni($mybb->input['site']);
				$query_del1 = $db->simple_select('automedia', '*', "name='".$site."'");
				$delete1 = $db->fetch_array($query_del1);
				if ($delete1['name'] == $site)
				{
					$db->delete_query('automedia', "name='{$site}'");
					automedia_cache();

					$mybb->input['module'] = $lang->av_plugin_title;
					$mybb->input['action'] = $lang->automedia_modules_deleted." ";
					log_admin_action(ucfirst($site));

					flash_message($lang->automedia_modules_deleted, 'success');
					admin_redirect("index.php?module=tools-automedia");
				}
				else
				{
					flash_message($lang->automedia_modules_notfound,'error');
				}
			}
			exit();
		}

		// Deactivate special module
		if ($mybb->input['action'] == 'adultdeactivate')
		{
			if (!verify_post_check($mybb->input['my_post_key']))
			{
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=tools-automedia&action=adult");
			}
			else
			{
				$site = htmlspecialchars_uni($mybb->input['site']);
				$query_del2 = $db->simple_select('automedia', '*', "name='".$site."'");
				$delete2 = $db->fetch_array($query_del2);
				if ($delete2['name'] == $site)
				{
					$db->delete_query('automedia', "name='{$site}'");
					automedia_cache();

					$mybb->input['module'] = $lang->av_plugin_title;
					$mybb->input['action'] = $lang->automedia_modules_deleted." ";
					log_admin_action(ucfirst($site));

					flash_message($lang->automedia_modules_deleted, 'success');
					admin_redirect("index.php?module=tools-automedia&action=adult");
				}
				else
				{
					flash_message($lang->automedia_modules_notfound,'error');
				}
			}
			exit();
		}

		// Activate all site modules
		if ($mybb->input['action'] == 'activateallsites')
		{
			if (!verify_post_check($mybb->input['my_post_key']))
			{
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=tools-automedia");
			}
			else
			{
				$folder1 = MYBB_ROOT."inc/plugins/automedia/mediasites/";
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
									$query_ex = $db->simple_select('automedia', 'name', "name='".htmlspecialchars_uni($media1)."'");
									$modactive = $db->fetch_array($query_ex);
									if (!$modactive)
									{
										// activate site
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
					automedia_cache();
				}
			}
			admin_redirect("index.php?module=tools-automedia");
			exit();
		}

		// Activate all special modules
		if ($mybb->input['action'] == 'activateallspecial')
		{
			if (!verify_post_check($mybb->input['my_post_key']))
			{
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=tools-automedia");
			}
			else
			{
				$folder2 = MYBB_ROOT."inc/plugins/automedia/special/";
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
					automedia_cache();
				}
			}
			admin_redirect("index.php?module=tools-automedia&action=adult");
			exit();
		}

		// Show site module code
		if ($mybb->input['action'] == 'showsite')
		{
			if (!verify_post_check($mybb->input['my_post_key']))
			{
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=tools-automedia");
			}
			else
			{
			$site = htmlspecialchars_uni($mybb->input['site']);
			$page->add_breadcrumb_item($lang->automedia_modules_embedcode);
			$page->output_header($lang->automedia_modules_showcode);

			$sub_tabs['automedia'] = array(
				'title'			=> $lang->automedia_modules,
				'link'			=> 'index.php?module=tools-automedia',
				'description'	=> $lang->automedia_modules
			);
			if ($mybb->settings['av_adultsites'] == 1)
			{
				$sub_tabs['special'] = array(
					'title'=>$lang->automedia_adult,
					'link'=>'index.php?module=tools-automedia&amp;action=adult',
					'description'=>$lang->automedia_adult_description1
				);
			}
			$sub_tabs['embedcode'] = array(
				'title'			=> $lang->automedia_modules_embedcode,
				'link'			=> 'index.php?module=tools-automedia&amp;action=showsite&amp;site='.urlencode($site).'&amp;my_post_key='.$mybb->post_code.'',
				'description'	=> $lang->automedia_modules_viewcode
			);
			$page->output_nav_tabs($sub_tabs, 'embedcode');
			$amtable = new Table;
			$amtable->construct_header(ucfirst($site).' '.$lang->automedia_modules_embedcode.':');

			$codefile = MYBB_ROOT."inc/plugins/automedia/mediasites/".$site.".php";
			if (is_file($codefile))
			{
				$embedcode = file_get_contents($codefile);
				$showcode = @highlight_string($embedcode, true);
				$amtable->construct_cell($showcode);
			}
			$amtable->construct_row();
			$amtable->output($lang->automedia_modules_showcode);
			$page->output_footer();
			}
			exit();
		}


		// Show special module code
		if ($mybb->input['action'] == 'showspecial')
		{
			if (!verify_post_check($mybb->input['my_post_key']))
			{
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=tools-automedia");
			}
			else
			{
				$site = htmlspecialchars_uni($mybb->input['site']);
				$page->add_breadcrumb_item($lang->automedia_modules_embedcode);
				$page->output_header($lang->automedia_modules_showcode);

				$sub_tabs['automedia'] = array(
					'title'			=> $lang->automedia_modules,
					'link'			=> 'index.php?module=tools-automedia',
					'description'	=> $lang->automedia_modules
				);
				if ($mybb->settings['av_adultsites'] == 1)
				{
					$sub_tabs['special'] = array(
						'title'=>$lang->automedia_adult,
						'link'=>'index.php?module=tools-automedia&amp;action=adult',
						'description'=>$lang->automedia_adult_description1
					);
				}
				$sub_tabs['embedcode'] = array(
					'title'			=> $lang->automedia_modules_embedcode,
					'link'			=> 'index.php?module=tools-automedia&amp;action=showspecial&amp;site='.urlencode($site).'&amp;my_post_key='.$mybb->post_code.'',
					'description'	=> $lang->automedia_modules_viewcode
				);
				$page->output_nav_tabs($sub_tabs, 'embedcode');
				$amtable = new Table;
				$amtable->construct_header(ucfirst($site).' '.$lang->automedia_modules_embedcode.':');

				$codefile = MYBB_ROOT."inc/plugins/automedia/special/".$site.".php";
				if (is_file($codefile))
				{
					$embedcode = file_get_contents($codefile);
					$showcode = @highlight_string($embedcode, true);
					$amtable->construct_cell($showcode);
				}
				$amtable->construct_row();
				$amtable->output($lang->automedia_modules_showcode);
				$page->output_footer();
			}
			exit();
		}

		// Reapply template edits
		if ($mybb->input['action'] == "templateedits")
		{
			if (!verify_post_check($mybb->input['my_post_key']))
			{
				flash_message($lang->invalid_post_verify_key2, 'error');
				admin_redirect("index.php?module=config-plugins");
			}
			else
			{
				automedia_reapply_template_edits();
				admin_redirect("index.php?module=config-plugins");
			}
			exit();
		}
	}
}


// Update settings language in ACP
$plugins->add_hook("admin_config_settings_start", "automedia_settings_lang");

function automedia_settings_lang()
{
	global $mybb, $db, $lang;
	// Load language strings in plugin function
	if (!isset($lang->av_group_global_descr))
	{
		$lang->load("automedia");
	}

	// Get settings language string
	$query = $db->simple_select("settinggroups", "*", "name='AutoMedia Global'");
	$amgroup = $db->fetch_array($query);

	if ($amgroup['description'] != $lang->av_group_global_descr)
	{
		// Update setting group
		$updated_record_gr = array(
			"title" => $db->escape_string($lang->av_group_global_title),
			"description" => $db->escape_string($lang->av_group_global_descr)
				);
		$db->update_query('settinggroups', $updated_record_gr, "name='AutoMedia Global'");

		// Update settings
		$updated_record1 = array(
			"title" => $db->escape_string($lang->av_enable_title),
			"description" => $db->escape_string($lang->av_enable_descr)
				);
		$db->update_query('settings', $updated_record1, "name='av_enable'");

		$updated_record2 = array(
			"title" => $db->escape_string($lang->av_guest_title),
			"description" => $db->escape_string($lang->av_guest_descr)
				);
		$db->update_query('settings', $updated_record2, "name='av_guest'");

		$updated_record3 = array(
			"title" => $db->escape_string($lang->av_groups_title),
			"description" => $db->escape_string($lang->av_groups_descr)
				);
		$db->update_query('settings', $updated_record3, "name='av_groups'");

		$updated_record4 = array(
			"title" => $db->escape_string($lang->av_forums_title),
			"description" => $db->escape_string($lang->av_forums_descr)
				);
		$db->update_query('settings', $updated_record4, "name='av_forums'");

		$updated_record5 = array(
			"title" => $db->escape_string($lang->av_adultsites_title),
			"description" => $db->escape_string($lang->av_adultsites_descr)
				);
		$db->update_query('settings', $updated_record5, "name='av_adultsites'");

		$updated_record6 = array(
			"title" => $db->escape_string($lang->av_adultguest_title),
			"description" => $db->escape_string($lang->av_adultguest_descr)
				);
		$db->update_query('settings', $updated_record6, "name='av_adultguest'");

		$updated_record7 = array(
			"title" => $db->escape_string($lang->av_adultgroups_title),
			"description" => $db->escape_string($lang->av_adultgroups_descr)
				);
		$db->update_query('settings', $updated_record7, "name='av_adultgroups'");

		$updated_record8 = array(
			"title" => $db->escape_string($lang->av_adultforums_title),
			"description" => $db->escape_string($lang->av_adultforums_descr)
				);
		$db->update_query('settings', $updated_record8, "name='av_adultforums'");

		$updated_record9 = array(
			"title" => $db->escape_string($lang->av_signature_title),
			"description" => $db->escape_string($lang->av_signature_descr)
				);
		$db->update_query('settings', $updated_record9, "name='av_signature'");

		$updated_record10 = array(
			"title" => $db->escape_string($lang->av_flashadmin_title),
			"description" => $db->escape_string($lang->av_flashadmin_descr),
			"optionscode" => "radio
admin=".$db->escape_string($lang->av_flashadmin_admins)."
mods=".$db->escape_string($lang->av_flashadmin_mods)."
all=".$db->escape_string($lang->av_flashadmin_all).""
				);
		$db->update_query('settings', $updated_record10, "name='av_flashadmin'");

		$updated_record11 = array(
			"title" => $db->escape_string($lang->av_width_title),
			"description" => $db->escape_string($lang->av_width_descr)
				);
		$db->update_query('settings', $updated_record11, "name='av_width'");

		$updated_record12 = array(
			"title" => $db->escape_string($lang->av_height_title),
			"description" => $db->escape_string($lang->av_height_descr)
				);
		$db->update_query('settings', $updated_record12, "name='av_height'");

		$updated_record13 = array(
			"title" => $db->escape_string($lang->av_embera_title),
			"description" => $db->escape_string($lang->av_embera_descr)
				);
		$db->update_query('settings', $updated_record13, "name='av_embera'");

		$updated_record14 = array(
			"title" => $db->escape_string($lang->av_embedly_title),
			"description" => $db->escape_string($lang->av_embedly_descr)
				);
		$db->update_query('settings', $updated_record14, "name='av_embedly'");

		$updated_record15 = array(
			"title" => $db->escape_string($lang->av_embedly_key_title),
			"description" => $db->escape_string($lang->av_embedly_key_descr)
				);
		$db->update_query('settings', $updated_record15, "name='av_embedly_key'");

		$updated_record16 = array(
			"title" => $db->escape_string($lang->av_embedly_click_title),
			"description" => $db->escape_string($lang->av_embedly_click_descr),
			"optionscode" => "select\nembed=".$db->escape_string($lang->av_embedly_click_embed)."\nbutton=".$db->escape_string($lang->av_embedly_click_button)."\nmodal=".$db->escape_string($lang->av_embedly_click_modal)."",
				);
		$db->update_query('settings', $updated_record16, "name='av_embedly_click'");

		$updated_record17 = array(
			"title" => $db->escape_string($lang->av_embedly_links_title),
			"description" => $db->escape_string($lang->av_embedly_links_descr)
				);
		$db->update_query('settings', $updated_record17, "name='av_embedly_links'");

		$updated_record18 = array(
			"title" => $db->escape_string($lang->av_embedly_card_title),
			"description" => $db->escape_string($lang->av_embedly_card_descr)
				);
		$db->update_query('settings', $updated_record18, "name='av_embedly_card'");

		$updated_record19 = array(
			"title" => $db->escape_string($lang->av_codebuttons_title),
			"description" => $db->escape_string($lang->av_codebuttons_descr)
				);
		$db->update_query('settings', $updated_record19, "name='av_codebuttons'");

		$updated_record20 = array(
			"title" => $db->escape_string($lang->av_quote_title),
			"description" => $db->escape_string($lang->av_quote_descr)
				);
		$db->update_query('settings', $updated_record20, "name='av_quote'");

		rebuild_settings();
	}
}
