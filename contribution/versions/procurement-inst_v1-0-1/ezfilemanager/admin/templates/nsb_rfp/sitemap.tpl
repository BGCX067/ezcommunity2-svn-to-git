<h1>{intl-site_map}</h1>

<br />

<table width="100%" cellpadding="2" cellspacing="0" border="0">
<!-- BEGIN category_value_tpl -->
<tr>
	<td>
	{option_level}
	<img src="{www_dir}/admin/images/folder.gif" height="16" width="16" border="0" alt="" align="top" />&nbsp;
	<a href="{www_dir}{index}/filemanager/{option_value}">{option_name}</a>&nbsp;
	<a href="{www_dir}{index}/filemanager/edit/{category_id}" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezsmc{category_id}-red','','{www_dir}/admin/images/redigerminimrk.gif',1)"><img name="ezsmc{category_id}-red" border="0" src="{www_dir}/admin/images/redigermini.gif" width="16" height="16" align="top">
	</td>
</tr>
<!-- END category_value_tpl -->

<!-- BEGIN file_value_tpl -->
<tr>
	<td>
	{option_level}
	<img src="{www_dir}/admin/images/document.gif" height="16" width="16" border="0" alt="" align="top" />&nbsp;
	<a href="{www_dir}{index}/filemanager/{option_value}">{option_name}</a>&nbsp;
	<a href="{www_dir}{index}/filemanager/edit/{file_id}" onMouseOut="MM_swapImgRestore()" onMouseOver="MM_swapImage('ezsmd{file_id}-red','','{www_dir}/admin/images/redigerminimrk.gif',1)"><img name="ezsmd{file_id}-red" border="0" src="{www_dir}/admin/images/redigermini.gif" width="16" height="16" align="top">

	</td>
</tr>
<!-- END file_value_tpl -->

<!-- BEGIN value_tpl -->

<!-- END value_tpl -->
</table>
<br />
