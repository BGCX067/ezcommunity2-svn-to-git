<form method="post" action="/trade/productedit/optionedit/">

<h1>{intl-option_edit}: {product_name}</h1>

<hr noshade="noshade" size="4" />

<p class="boxtext">{intl-title}:</p>
<input type="text" size="40" name="OptionName" value="{name_value}"/>
	
<p class="boxtext">{intl-description}:</p>
<textarea rows="5" cols="40" name="Description">{description_value}</textarea>
<br /><br />
	
<h2>{intl-options}</h2>
<table table cellspacing="0" cellpadding="4" border="0">
<tr>
	<th>&nbsp;</th>
	<th colspan="{value_count}">{intl-value_description}:</th>
	<th>{intl-main_price}:</th>
	<!-- BEGIN group_item_tpl -->
	<th>{price_group_name}:</th>
	<!-- END group_item_tpl -->
	<th>&nbsp;</th>
</tr>
<tr>
	<th valign="bottom">{intl-value_header}:</th>
	<!-- BEGIN value_description_item_tpl -->
	<td>
	<div class="check"><input type="checkbox" name="OptionDescriptionDelete[]" value="{value_description_index}" />{intl-delete}</div>
	<input type="text" size="8" name="OptionValueDescription[{value_description_index}]" value="{option_description_value}" />
	</td>
	<!-- END value_description_item_tpl -->
	<td>&nbsp;</td>
	<td colspan="{group_count}">&nbsp;</td>
</tr>
	<!-- BEGIN option_item_tpl -->
<tr>
	<th>
	{intl-value} {value_pos}:
	</th>
	<!-- BEGIN value_item_tpl -->
	<td>
	<input type="text" size="8" name="OptionValue[{value_index}][]" value="{option_value}" />
	</td>
	<!-- END value_item_tpl -->
	<td>
	<input type="text" size="6" name="OptionMainPrice[]" value="{main_price_value}" />
	</td>
	<!-- BEGIN option_price_item_tpl -->
	<td>
	<input type="text" size="6" name="OptionPrice[{value_index}][{price_group}]" value="{price_value}" />
	</td>
	<!-- END option_price_item_tpl -->
	<td>
	<div class="check"><input type="checkbox" name="OptionDelete[]" value="{value_index}" />{intl-delete}</div>
	</td>
</tr>
	<!-- END option_item_tpl -->
</table>
<br />

<hr noshade="noshade" size="4" />

<input class="stdbutton" type="submit" name="Delete" value="{intl-delete_value}" />	

<hr noshade="noshade" size="4" />

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<input class="stdbutton" type="submit" name="NewValue" value="{intl-new_value}" />	
	</td>
	<td>&nbsp;</td>
	<td>
	<input class="stdbutton" type="submit" name="NewDescription" value="{intl-new_description}" />	
	</td>
</tr>
</table>

<input type="hidden" name="ValueCount" value="{value_count}" />

<hr noshade="noshade" size="4" />

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td>
	<input type="hidden" name="ProductID" value="{product_id}" />
	<input type="hidden" name="OptionID" value="{option_id}" />
	<input class="okbutton" type="submit" name="OK" value="{intl-ok}" />
	</td>
	<td>&nbsp;</td>
	<td>
	<input class="okbutton" type="submit" name="Abort" value="{intl-abort}" />	
	</td>
	</form>
</tr>
</table>



