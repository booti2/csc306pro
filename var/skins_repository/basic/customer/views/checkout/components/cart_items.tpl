{capture name="cartbox"}
{if $mode == "checkout"}
	{if $cart.coupons|floatval}<input type="hidden" name="c_id" value="" />{/if}
	{hook name="checkout:form_data"}
	{/hook}
{/if}

<div id="cart_items">
<table class="table top" width="100%" cellpadding="0" cellspacing="0" border="0">
{if $cart_products}

{assign var="prods" value=false}

<tr>
	<th colspan="2" class="left">{$lang.product}</th>
	<th class="right">{$lang.unit_price}</th>
	<th class="quantity-cell">{$lang.quantity}</th>
	<th class="right">{$lang.total_price}</th>
</tr>				
{foreach from=$cart_products item="product" key="key" name="cart_products"}
{assign var="obj_id" value=$product.object_id|default:$key}
{hook name="checkout:items_list"}
{if !$cart.products.$key.extra.parent}
<tr>
	<td valign="top" class="product-image-cell">
	{if $mode == "cart" || $show_images}
	<div class="product-image cm-reload-{$obj_id}" id="product_image_update_{$obj_id}">
		{hook name="checkout:product_icon"}
		<a href="{"products.view?product_id=`$product.product_id`"|fn_url}">
		{include file="common_templates/image.tpl" obj_id=$key images=$product.main_pair object_type="product" show_thumbnail="Y" image_width=$settings.Thumbnails.product_cart_thumbnail_width image_height=$settings.Thumbnails.product_cart_thumbnail_height}</a>
		{/hook}
	<!--product_image_update_{$obj_id}--></div>
	</td>
	<td width="50%" valign="top" class="product-description">
	<a href="{"products.view?product_id=`$product.product_id`"|fn_url}" class="product-title">{$product.product|unescape}</a>{if !$product.exclude_from_calculate}<a class="{$ajax_class} icon-delete-big" href="{"checkout.delete?cart_id=`$key`&amp;redirect_mode=`$mode`"|fn_url}" rev="cart_items,checkout_totals,cart_status*,checkout_steps,checkout_cart" title="{$lang.remove}"></a>{/if}	
		<p class="sku{if !$product.product_code} hidden{/if}" id="sku_{$key}">
			{$lang.sku}: <span class="cm-reload-{$obj_id}" id="product_code_update_{$obj_id}">{$product.product_code}<!--product_code_update_{$obj_id}--></span>
		</p>
		{if $product.product_options}
			<div class="cm-reload-{$obj_id} options" id="options_update_{$obj_id}">
			{include file="views/products/components/product_options.tpl" product_options=$product.product_options product=$product name="cart_products" id=$key location="cart" disable_ids=$disable_ids form_name="checkout_form"}
			<!--options_update_{$obj_id}--></div>
		{/if}
	{/if}
		{assign var="name" value="product_options_$key"}
		{capture name=$name}

		{capture name="product_info_update"}
		{hook name="checkout:product_info"}
			{if $product.exclude_from_calculate}
				<strong><span class="price">{$lang.free}</span></strong>
			{elseif $product.discount|floatval || ($product.taxes && $settings.General.tax_calculation != "subtotal")}
				{if $product.discount|floatval}
					{assign var="price_info_title" value=$lang.discount}
				{else}
					{assign var="price_info_title" value=$lang.taxes}
				{/if}
				<p><a rev="discount_{$key}" class="cm-dialog-opener cm-dialog-auto-size">{$price_info_title}</a></p>

				<div class="product-options hidden" id="discount_{$key}" title="{$price_info_title}">
				<table class="table" cellpadding="0" cellspacing="0" border="0">
				<tr>
					<th>{$lang.price}</th>
					<th>{$lang.quantity}</th>
					{if $product.discount|floatval}<th>{$lang.discount}</th>{/if}
					{if $product.taxes && $settings.General.tax_calculation != "subtotal"}<th>{$lang.tax}</th>{/if}
					<th>{$lang.subtotal}</th>
				</tr>
				<tr>
					<td>{include file="common_templates/price.tpl" value=$product.original_price span_id="original_price_`$key`" class="none"}</td>
					<td class="center">{$product.amount}</td>
					{if $product.discount|floatval}<td>{include file="common_templates/price.tpl" value=$product.discount span_id="discount_subtotal_`$key`" class="none"}</td>{/if}
					{if $product.taxes && $settings.General.tax_calculation != "subtotal"}<td>{include file="common_templates/price.tpl" value=$product.tax_summary.total span_id="tax_subtotal_`$key`" class="none"}</td>{/if}
					<td>{include file="common_templates/price.tpl" span_id="product_subtotal_2_`$key`" value=$product.display_subtotal class="none"}</td>
				</tr>
				<tr class="table-footer">
					<td colspan="5">&nbsp;</td>
				</tr>
				</table>
				</div>
			{/if}
			{include file="views/companies/components/product_company_data.tpl" company_name=$product.company_name company_id=$product.company_id}
		{/hook}
		{/capture}
		{if $smarty.capture.product_info_update|trim}
			<div class="cm-reload-{$obj_id}" id="product_info_update_{$obj_id}">
				{$smarty.capture.product_info_update}
			<!--product_info_update_{$obj_id}--></div>
		{/if}
		{/capture}
		
		{if $smarty.capture.$name|trim}
		<div class="clear"></div>
		<a id="sw_options_{$key}" class="cm-combo-on cm-combination"><i>{$lang.text_click_here}</i></a>

		<div id="options_{$key}" class="product-options hidden">
			<span class="light-block-arrow"></span>
			{$smarty.capture.$name}
		</div>
		{/if}
	</td>
	<td class="right price-cell cm-reload-{$obj_id}" id="price_display_update_{$obj_id}">
	{include file="common_templates/price.tpl" value=$product.display_price span_id="product_price_`$key`" class="sub-price"}
	<!--price_display_update_{$obj_id}--></td>
	<td class="quantity-cell center{if $product.is_edp == "Y" || $product.exclude_from_calculate} quantity-disabled{/if}">
		{if $use_ajax == true && $cart.amount != 1}
			{assign var="ajax_class" value="cm-ajax"}
		{/if}
		
		<div class="quantity cm-reload-{$obj_id}{if $settings.Appearance.quantity_changer == "Y"} changer{/if}" id="quantity_update_{$obj_id}">
			<input type="hidden" name="cart_products[{$key}][product_id]" value="{$product.product_id}" />
			{if $product.exclude_from_calculate}<input type="hidden" name="cart_products[{$key}][extra][exclude_from_calculate]" value="{$product.exclude_from_calculate}" />{/if}

			<label for="amount_{$key}"></label>
			{if $product.is_edp == "Y" || $product.exclude_from_calculate}
				{$product.amount}
			{else}
				{if $settings.Appearance.quantity_changer == "Y"}
					<div class="center valign cm-value-changer">
					<a class="cm-increase"></a>
				{/if}
				<input type="text" size="3" id="amount_{$key}" name="cart_products[{$key}][amount]" value="{$product.amount}" class="input-text-short cm-amount"{if $product.qty_step > 1} data-ca-step="{$product.qty_step}"{/if} />
				{if $settings.Appearance.quantity_changer == "Y"}
					<a class="cm-decrease"></a>
					</div>
				{/if}
			{/if}
			{if $product.is_edp == "Y" || $product.exclude_from_calculate}
				<input type="hidden" name="cart_products[{$key}][amount]" value="{$product.amount}" />
			{/if}
			{if $product.is_edp == "Y"}
				<input type="hidden" name="cart_products[{$key}][is_edp]" value="Y" />
			{/if}
		<!--quantity_update_{$obj_id}--></div>
	</td>
	<td class="right price-cell cm-reload-{$obj_id}" id="price_subtotal_update_{$obj_id}">
		{include file="common_templates/price.tpl" value=$product.display_subtotal span_id="product_subtotal_`$key`" class="price"}
		{if $product.zero_price_action == "A"}
			<input type="hidden" name="cart_products[{$key}][price]" value="{$product.base_price}" />
		{/if}
	<!--price_subtotal_update_{$obj_id}--></td>
</tr>
{/if}
{/hook}
{/foreach}
{/if}

{hook name="checkout:extra_list"}
{/hook}

</table>
<!--cart_items--></div>

{/capture}
{include file="common_templates/mainbox_cart.tpl" title=$lang.cart_items content=$smarty.capture.cartbox}
