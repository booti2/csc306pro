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

if (!defined('AREA') ) {
	if (!empty($_REQUEST['montant'])) {
	
		require './init_payment.php';

		$_order_id = ltrim($_REQUEST['reference'], '0');
		$order_id = (strpos($_order_id, '_')) ? substr($_order_id, 0, strpos($_order_id, '_')) : $_order_id;
		$order_info = fn_get_order_info($order_id);
		$processor_data = fn_get_payment_method_data($order_info['payment_id']);
		$_amount = (strpos($_REQUEST['montant'], ".") ? $order_info['total'] : round($order_info['total'])) . $processor_data['params']['currency'];
		$data = $processor_data['params']['merchant_id'] . '*' . $_REQUEST['date'] . '*' . $_amount . '*' . $_REQUEST['reference'] . '*' . $processor_data['params']['payment_desc'] . '*3.0*' . $_REQUEST['code-retour'] . '*' . $_REQUEST['cvx'] . '*' . $_REQUEST['vld'] . '*' . $_REQUEST['brand'] . '*' . $_REQUEST['status3ds'] . '*' . $_REQUEST['numauto'] . '*********';
		$_key = $processor_data['params']['key'];
		$_mac = fn_cmcic_hmac_sha1($_key, $data);

		$pp_response = array();

		if ($_mac == fn_strtolower($_REQUEST['MAC']) && ($_REQUEST['code-retour'] == 'payetest' || $_REQUEST['code-retour'] == 'paiement')) {
			$pp_response['order_status'] = 'P';
			$pp_response['reason_text'] = fn_get_lang_var('approved');
			if ($_REQUEST['code-retour'] == 'payetest') {
				$pp_response['reason_text'] .= '; ' . fn_get_lang_var('test_transaction');
			}
		} else {
			$pp_response['order_status'] = 'F';
			$pp_response['reason_text'] = fn_get_lang_var('declined');
		}

		if (fn_check_payment_script('cmcic.php', $order_id)) {
			fn_finish_payment($order_id, $pp_response);
		}

		echo "Pragma: no-cache \nContent-type: text/plain \nVersion: 1 \n" . ($pp_response['order_status'] == "P" ? "OK" : "Document Falsifie");
		exit;
	} else {
		die('Access denied');
	}
}

if (defined('PAYMENT_NOTIFICATION')) {
	if ($mode == 'notify') {
		fn_order_placement_routines($_REQUEST['order_id']);
	}
} else {
	if ( !defined('AREA') ) { die('Access denied'); }

	$_post_test = array (
		'CM' => 'https://paiement.creditmutuel.fr/test/paiement.cgi',
		'CIC' => 'https://ssl.paiement.cic-banques.fr/test/paiement.cgi',
		'OBC' => 'https://ssl.paiement.banque-obc.fr/test/paiement.cgi',
	);
	$_post = array (
		'CM' => 'https://paiement.creditmutuel.fr/paiement.cgi',
		'CIC' => 'https://ssl.paiement.cic-banques.fr/paiement.cgi',
		'OBC' => 'https://ssl.paiement.banque-obc.fr/paiement.cgi',
	);

	$soc = $processor_data['params']['societe'];
	$return_url = htmlentities(Registry::get('config.current_location') . "/$index_script?dispatch=payment_notification.notify&payment=cmcic&order_id=$order_id");
	$post_url = ($processor_data['params']['mode'] == 'test') ? $_post_test[$processor_data['params']['bank']] : $_post[$processor_data['params']['bank']];
	$_amount = $order_info['total'] . $processor_data['params']['currency'];
	$_date = htmlentities(date("d/m/Y:H:i:s"));
	$_order_id = ($order_info['repaid']) ? ($order_id .'_'. $order_info['repaid']) : $order_id;
	$_refernce = substr("000000000000", strlen($_order_id)) . $_order_id;

	$data = $processor_data['params']['merchant_id'] . '*' . $_date . '*' . $_amount . '*' . $_refernce . '*' . $processor_data['params']['payment_desc'] . '*3.0*' . $processor_data['params']['language'] . '*' . $soc . '*' . $order_info['email'] . '**********';
	$_pass = $processor_data['params']['password'];
	$_key = $processor_data['params']['key'];
	$_mac = fn_cmcic_hmac_sha1($_key, $data);

echo <<<EOT
<html>
<body onLoad="javascript: document.process.submit();">
<form action="{$post_url}" method="post" name="process" target="_top">
	<input type="hidden" name="version"        value="3.0">
	<input type="hidden" name="TPE"            value="{$processor_data['params']['merchant_id']}">
	<input type="hidden" name="date"           value="{$_date}">
	<input type="hidden" name="montant"        value="{$_amount}">
	<input type="hidden" name="reference"      value="{$_refernce}">
	<input type="hidden" name="MAC"            value="{$_mac}">
	<input type="hidden" name="url_retour"     value="{$return_url}">
	<input type="hidden" name="url_retour_ok"  value="{$return_url}">
	<input type="hidden" name="url_retour_err" value="{$return_url}">
	<input type="hidden" name="lgue"           value="{$processor_data['params']['language']}">
	<input type="hidden" name="societe"        value="{$soc}">
	<input type="hidden" name="texte-libre"    value="{$processor_data['params']['payment_desc']}">
	<input type="hidden" name="mail"           value="{$order_info['email']}" />
EOT;
$msg = fn_get_lang_var('text_cc_processor_connection');
$msg = str_replace('[processor]', 'Payment server', $msg);
echo <<<EOT
	</form>
 </body>
</html>
EOT;
exit;
}

function fn_cmcic_get_usable_key($key)
{
	$hexStrKey = substr($key, 0, 38);
	$hexFinal = '' . substr($key, 38, 2) . '00';
	$cca0 = ord($hexFinal);
	if ($cca0 > 70 && $cca0 < 97) {
		$hexStrKey .= chr($cca0-23) . substr($hexFinal, 1, 1);
	} else {
		if (substr($hexFinal, 1, 1) == 'M') {
			$hexStrKey .= substr($hexFinal, 0, 1) . '0';
		} else {
			$hexStrKey .= substr($hexFinal, 0, 2);
		}
	}

	return pack('H*', $hexStrKey);
}

function fn_cmcic_hmac_sha1 ($key, $data)
{
	$key = fn_cmcic_get_usable_key($key);

	$length = 64; // block length for SHA1
	if (strlen($key) > $length) {
		$key = pack('H*',sha1($key));
	}
	$key  = str_pad($key, $length, chr(0x00));
	$ipad = str_pad('', $length, chr(0x36));
	$opad = str_pad('', $length, chr(0x5c));
	$k_ipad = $key ^ $ipad;
	$k_opad = $key ^ $opad;

	return fn_strtolower(sha1($k_opad . pack('H*', sha1($k_ipad . $data))));
}
?>