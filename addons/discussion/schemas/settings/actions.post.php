<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/


if ( !defined('AREA') ) { die('Access denied'); }

/**
 * Check if mod_rewrite is active and clean up templates cache
 */
function fn_settings_actions_addons_discussion_home_page_testimonials(&$new_value, $old_value)
{
	$dicussion = array();
	$discussion['type'] = $new_value;
	$discussion['object_type'] = 'E';
	$discussion['object_id'] = 0;

	if (PRODUCT_TYPE == 'ULTIMATE') {
		if (!defined('COMPANY_ID')) {
			$discussion['for_all_companies'] = 1;
		} else {
			$discussion['company_id'] = COMPANY_ID;
		}
	}
	
	if (function_exists('fn_update_discussion')) {
		fn_update_discussion($discussion);
	}

	return true;
}

function fn_settings_actions_addons_discussion_company_discussion_type(&$new_value, $old_value)
{
	db_query('UPDATE ?:discussion SET type = ?s WHERE object_type = ?s', $new_value, 'M');
}

?>