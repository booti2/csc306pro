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

$schema['buy_together'] = array (
	'permissions' => array ('GET' => 'view_catalog', 'POST' => 'manage_catalog'),
	'modes' => array (
		'delete' => array (
			'permissions' => 'manage_catalog'
		)
	),
);
$schema['tools']['modes']['update_status']['param_permissions']['table_names']['buy_together'] = 'manage_catalog';

?>