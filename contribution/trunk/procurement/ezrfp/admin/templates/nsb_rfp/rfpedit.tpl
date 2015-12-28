<form method="post" action="{www_dir}{index}/rfp/rfpedit/{action_value}/{rfp_id}/" >

<h1>{intl-head_line}</h1>

<!-- BEGIN error_message_tpl -->
<h3 class="error">{intl-error_parsing_xml}:</h3>
<textarea class="box" name="InvalidContents" cols="40" rows="5" wrap="soft">{rfp_invalid_contents}</textarea>
<!-- END error_message_tpl -->

<hr noshade="noshade" size="4" />


<p class="boxtext">{intl-rfp_name}:</p>
<input class="box" type="text" name="Name" size="40" value="{rfp_name}" />
<br />

<p class="boxtext">{intl-project_estimate}:</p>
<input class="box" type="text" name="ProjectEstimate" size="40" value="{rfp_project_estimate}" />
<br />
<br />

<!-- BEGIN urltranslator_tpl --> 
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
        <td valign="top">
        <p class="boxtext">{intl-rfp_urltranslator}:</p>
        <input class="halfbox" type="text" name="Urltranslator" size="20" value="{rfp_urltranslator}" />
        <input type="hidden" name="UrltranslatorEnabled" value="1" />
        </td>
        <td valign="top">
        <p class="boxtext">{intl-rfp_url}:</p><span class="halfbox">{rfp_url}{intl-rfp_nourl}</span>	
        </td>
</tr>
</table>
<br />
<!-- END urltranslator_tpl -->

<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
	<td valign="top">

	<span class="boxtext">{intl-category}: <br /></span>
	
	<select name="CategoryID">

	<!-- BEGIN value_tpl -->
	<option value="{option_value}" {selected}>{option_level}{option_name}</option>
	<!-- END value_tpl -->
	
	</select>

<!--
	<p class="boxtext">{intl-rfp_topic}:</p>

	<select name="TopicID">

	<option value="0">{intl-no_topic}</option>
	
	<!-- BEGIN topic_item_tpl -->
	<option value="{topic_id}" {selected}>{topic_name}</option>
	<!-- END topic_item_tpl -->
	
	</select>
-->
	</td>	
	<td>&nbsp;</td>
	<td valign="top">
	<span class="boxtext">{intl-additional_category}:<br /></span>
	
	<select multiple size="{num_select_categories}" name="CategoryArray[]">
	
	<!-- BEGIN multiple_value_tpl -->
	<option value="{option_value}" {multiple_selected}>{option_level}{option_name}</option>
	<!-- END multiple_value_tpl -->
	
	</select>
	</td>
</tr>
</table>

<span class="boxtext">{intl-contents}: <br /></span>
<textarea class="box" name="Contents[]" cols="40" rows="20" wrap="soft">{rfp_contents_0}</textarea>


<input type="hidden" name="Keywords[]" value="{rfp_keywords}" />

<!--
<p class="boxtext">{intl-intro}:</p>
<textarea class="box" name="Contents[]" cols="40" rows="5" wrap="soft">{rfp_contents_0}</textarea>
<br /><br />

<input type="hidden" name="Contents[]" value="{rfp_contents_0}" />

<p class="boxtext">{intl-contents}:</p>
<textarea class="box" name="Contents[]" cols="40" rows="20" wrap="soft">{rfp_contents_1}</textarea>

<br /><br />

<p class="boxtext">{intl-link_text}:</p>
<input class="box" type="text" name="LinkText" size="20" value="{link_text}" />

<p class="boxtext">{intl-keywords}:</p>
<input class="box" type="text" name="Keywords" size="40" value="{rfp_keywords}" />
<br />
-->

<table width="78%" cellpaddning="0" cellspacing="0" border="0">
<tr>
	<td valign="top" colspan="0">

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td colspan="5">
	<p class="boxtext">{intl-publish_date}:</p>
	</td>
</tr>
<tr>
	<td>
	<span class="small">{intl-month}:</span>
	</td>
	<td>
	<span class="small">{intl-day}:</span>
	</td>
	<td>
	<span class="small">{intl-year}:</span>
	</td>

	<td>
	<span class="small">{intl-hour}:</span>
	</td>
	<td>
	<span class="small">{intl-minute}:</span>
	</td>
</tr>
<tr>
	<td>
	<input type="text" size="2" name="StartMonth" value="{start_month}" />&nbsp;&nbsp;
	</td>
	<td>
	<input type="text" size="2" name="StartDay" value="{start_day}" />&nbsp;&nbsp;
	</td>
	<td>
	<input type="text" size="4" name="StartYear" value="{start_year}" />&nbsp;&nbsp;
	</td>
	<td>
	<input type="text" size="2" name="StartHour" value="{start_hour}" />&nbsp;&nbsp;
	</td>
	<td>
	<input type="text" size="2" name="StartMinute" value="{start_minute}" />
	</td>
</tr>
</table>

<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td colspan="5">
	<br />
	<p class="boxtext">{intl-responce_due_date}:</p>
	</td>
</tr>
<tr>
	<td>
	<span class="small">{intl-month}:</span>
	</td>
	<td>
	<span class="small">{intl-day}:</span>
	</td>
	<td>
	<span class="small">{intl-year}:</span>
	</td>

	<td>
	<span class="small">{intl-hour}:</span>
	</td>
	<td>
	<span class="small">{intl-minute}:</span>
	</td>
</tr>
<tr>
	<td>
	<input type="text" size="2" name="StopMonth" value="{stop_month}" />&nbsp;&nbsp;
	</td>
	<td>
	<input type="text" size="2" name="StopDay" value="{stop_day}" />&nbsp;&nbsp;
	</td>
	<td>
	<input type="text" size="4" name="StopYear" value="{stop_year}" />&nbsp;&nbsp;
	</td>
	<td>
	<input type="text" size="2" name="StopHour" value="{stop_hour}" />&nbsp;&nbsp;
	</td>
	<td>
	<input type="text" size="2" name="StopMinute" value="{stop_minute}" />
	</td>
</tr>
</table>

</td>
	<td valign="top">
	<!-- BEGIN publish_dates_tpl -->

	<p class="boxtext">{intl-created}:</p><span class="p">{created_date}</span>

	<!-- BEGIN published_tpl -->
	<p class="boxtext">{intl-published}:</p><span class="p">{published_date}</span>
	<!-- END published_tpl -->
	<!-- BEGIN un_published_tpl -->
	<p class="boxtext">{intl-un_published}</p>
	<!-- END un_published_tpl -->

	<p class="boxtext">{intl-modified}:</p><span class="p">{modified_date}</span>
        <p class="boxtext">{intl-responce_due_date}:</p><span class="p">{responce_due_date}</span>

	<!-- END publish_dates_tpl -->

	</td>
</tr>

<input type="hidden" name="GroupArray[]" value="0" />
<input type="hidden" name="WriteGroupArray[]" value="1,3" />

<!--
<tr>
	<td colspan="2">&nbsp;
	<br />

<table width="100%" cellspacing="0" cellpadding="0" border="0">

<tr>
        <td align="top">
	<input type="hidden" name="GroupArray[]" value="0" />

        <p class="boxtext">{intl-groups}:</p>
        <select name="GroupArray[]" size="3" multiple>
	        <option value="0" {all_selected}>{intl-all}</option>
        </select>

        <!-- BEGIN group_item_tpl -->
        <option value="{group_id}" {selected}>{group_name}</option>
        <!-- END group_item_tpl -->

        <option value="1" >Administrators</option>
        <option value="3" selected>Bidders</option>
        <option value="2" >Anonymous</option>
        <option value="4" >Holders</option>

        </td>

        <td>&nbsp;</td>
        <td>

	 <input type="hidden" name="WriteGroupArray[]" value="1,3" />


        <p class="boxtext">{intl-groups_write}:</p>
		
        <select name="WriteGroupArray[]" size="3" multiple>
        <option value="0" {all_write_selected}>{intl-all}</option>
        
        <option value="1" selected>Administrators</option>
        <option value="3" selected>Bidders</option>
        <option value="4" >Holders</option>
        <option value="2" >Anonymous</option>

        </select>
        </td>

        <!-- BEGIN category_owner_tpl -->
        <option value="{module_owner_id}" {is_selected}>{module_owner_name}</option>
        <!-- END category_owner_tpl -->

</tr>
</table>
<br />

-->
<tr>
	<td colspan="4">
        	<span class="boxtext">{intl-rfp_author}:<br />{selected}<br /></span>
        	<select multiple size="10" name="ContentsWriterID[]">
        		<!-- BEGIN author_item_tpl -->
        		<option value="{author_id}" {mario}>{option_level}{author_name} {mario}</option>
        		<!-- END author_item_tpl -->
        	</select>
<!--
<br /><br />
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
        <td valign="top">
        <p class="boxtext">{intl-new_author_name}:</p>
        <input class="halfbox" type="text" name="NewAuthorName" size="20" value="" />
        </td>
        <td valign="top">
        <p class="boxtext">{intl-new_author_email}:</p>
        <input class="halfbox" type="text" name="NewAuthorEmail" size="20" value="" />
        </td>

        <td valign="top">
        <p class="boxtext">{intl-rfp_author}:</p>
        <p>{author_text} {author_email}</p>
        </td>
</tr>
</table>
-->

<!-- BEGIN author_pending_information_tpl -->
&nbsp; <!-- END author_pending_information_tpl -->

</td>
</tr>

<!--
<tr>
	<td colspan="2">
	<p class="boxtext">{intl-log_message}:</p>
<textarea class="box" name="LogMessage" cols="40" rows="3" wrap="soft"></textarea>
<br />
<input class="stdbutton" type="submit" name="Log" value="{intl-log_history}" />

	</td>
</tr>
<tr>
        <td>&nbsp;</td>
</tr>
-->

<tr>
        <td colspan="4"><input type="hidden" name="LogMessage[]" value="" /></td>
</tr>

        <!-- BEGIN rfp_pending_tpl -->
<tr>

	<td>
	<p>{intl-rfp_is_pending}</p>
	</td>
	<td colspan="3">&nbsp;</td>
</tr>
        <!-- END rfp_pending_tpl -->
<tr>
	<td colspan="1">
	<input type="checkbox" name="IsPublished" {rfp_is_published} />
	<span class="boxtext">{intl-rfp_is_published}</span><br />
	</td>

        <td colspan="2">
	<input type="checkbox" name="Discuss" {discuss_rfp} />
	<span class="boxtext">{intl-discuss_rfp}</span><br />
	</td>
</tr>
</table>
<!--
<br />
-->
<hr noshade="noshade" size="4" />

<table cellspacing="0" cellpadding="0" border="0">
<tr>
    <td>
        <select name="ItemToAdd">
        <option value="File">{intl-files}</option>
<!--
        <option value="Image">{intl-pictures}</option>
        <option value="Media">{intl-media}</option>
        <option value="File">{intl-files}</option>
        <option value="Attribute">{intl-attributes}</option>
        <option value="Form">{intl-forms}</option>
-->
        </select>
    </td>
    <td>
        <input class="stdbutton" type="submit" name="AddItem" value="{intl-add_item}" />
    </td>
<!--
    <td>&nbsp;&nbsp;&nbsp;</td>
-->
    <td>
        <input class="stdbutton" type="submit" name="Preview" value="{intl-preview}" />
    <td>
<tr>
</table>
<hr noshade="noshade" size="4" />
<span>
<table cellspacing="0" cellpadding="0" border="0">
<tr>
	<td><input class="okbutton" type="submit" value="{intl-ok}" />
	</td>
	</form>
	<form method="post" action="{www_dir}{index}/rfp/rfpedit/cancel/{rfp_id}/">
	<td>
	<input class="okbutton" type="submit" value="{intl-cancel}" />	
	</td>
	</form>
</tr>
</table>


