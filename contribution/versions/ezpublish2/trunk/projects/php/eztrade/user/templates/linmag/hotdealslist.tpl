<table width="100%" cellspacing="0" cellpadding="2" border="0">
<tr>
	<td class="menuhead" colspan="{hotdeal_columns}">{intl-hot_deals}</td>
</tr>

<!-- BEGIN product_list_tpl -->

<!-- BEGIN product_tpl -->
{begin_tr}
	<td class="menutext">
	<a class="menutext" href="{www_dir}{index}/trade/productview/{product_id}/{category_id}/"><b>{product_name}</b></a><br />
	<!-- BEGIN product_image_tpl -->
	<a href="{www_dir}{index}/trade/productview/{product_id}/{category_id}/"><img src="{www_dir}{product_image_path}" border="0" width="{product_image_width}" height="{product_image_height}" alt="{product_image_caption}"/></a>
	<!-- END product_image_tpl -->
	<div><span class="menutext">{product_intro_text}</span></div>
<!-- BEGIN price_tpl -->
	<span class="pris">{product_price}</span>
<!-- END price_tpl -->
	</td>
{end_tr}

<!-- END product_tpl -->

<tr>
	<td class="menuspacer" colspan="{hotdeal_columns}">&nbsp;</td>
</tr>


<!-- END product_list_tpl -->

</table>