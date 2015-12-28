<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="no" lang="no">

<head>
	<title>{intl-header}: {site_url}</title>

	<link REL="shortcut icon" HREF="http://{site_url}/favicon.ico" TYPE="image/x-icon">
	<link rel="stylesheet" type="text/css" href="{www_dir}/admin/templates/{site_style}/style.css" />
	<link rel="stylesheet" type="text/css" href="/design/nsb/w3cstyle.css" />

	<meta http-equiv="Content-Type" content="text/html; charset={charset}"/>

<script language="JavaScript1.2">
<!--//

        function MM_swapImgRestore()
        {
                var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
        }

        function MM_preloadImages()
        {
                var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
                var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
                if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
        }

        function MM_findObj(n, d)
        {
                var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
                d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
                if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
                for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document); return x;
        }

        function MM_swapImage()
        {
                var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
                if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
        }


        function verify( msg, url )
        {
        if ( confirm( msg ) )
        {
            this.location = url;
        }
        }

        function popup ( url, target )
        {
            numbers = "width=500, height=400, left=4, top=4, toolbar=1, statusbar=0, scrollbars=1, resizable=1";
            newWin = window.open ( url, target, numbers );
            return false;
        }

        function SwitchCharset()
        {
            CharsetSwitch.submit();
        }


//-->
</script>

</head>

<body bgcolor="#ffffff" topmargin="6" marginheight="6" leftmargin="6" marginwidth="6">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td class="repeaty" width="1" background="{www_dir}/admin/images/{site_style}/top-l02.gif" valign="top" align="left"><img src="{www_dir}/admin/images/{site_style}/top-l01.gif" width="10" height="10" border="0" alt="" /><br /></td>
    <td class="repeatx" width="50%" background="{www_dir}/admin/images/{site_style}/top-m01.gif" valign="absmiddle" bgcolor="#ffffff" align="left"><img src="{www_dir}/admin/images/{site_style}/1x1.gif" width="120" height="6" border="0" alt="" /><br /></td>
    <td class="repeatx" width="50%" background="{www_dir}/admin/images/{site_style}/top-m01.gif" valign="absmiddle" bgcolor="#ffffff" align="left"><img src="{www_dir}/admin/images/{site_style}/1x1.gif" width="120" height="6" border="0" alt="" /><br /></td>
    <td class="repeaty" width="1" background="{www_dir}/admin/images/{site_style}/top-r02.gif" valign="top" align="left"><img src="{www_dir}/admin/images/{site_style}/top-r01.gif" width="10" height="10" border="0" /><br /></td>
</tr>
<tr>
    <td class="repeaty" width="1" background="{www_dir}/admin/images/{site_style}/top-l02.gif" valign="top" align="left"><img src="{www_dir}/admin/images/{site_style}/1x1.gif" width="10" height="10" border="0" alt="" /><br /></td>
    <td class="repeatx" colspan="2" width="98%" valign="absmiddle" bgcolor="#ffffff" align="left">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td width="2%" class="tdmini"> &nbsp;
	 <a href="http://{site_url}/" target="_vblank" style="font-weight:bold;font-size:16px;">Site</a> <br /><br />
	<a href="http://{admin_site_url}/" target="_vblank" style="font-weight:bold;font-size:16px;">Administration</a> <br /><br />
	</td>
	<td width="1%"><img src="{www_dir}/admin/images/1x1.gif" width="10" height="10" border="0" alt="" /></td>
<!--
	<td width="71%" valign="top">
	<form action="{charset_submit_url}" method="post" name="CharsetSwitch">
-->
	<!-- BEGIN charset_switch_tpl -->
	<select name="page_charset" onchange="SwitchCharset()">
            <!-- BEGIN charset_switch_item_tpl --> 
	    <option value="{charset_code}" {charset_selected}>{charset_description}</option>
	    <!-- END charset_switch_item_tpl -->
        </select>
        <input type="submit" class="stdbutton" value="Set" />
        <!-- END charset_switch_tpl -->
<!--
	</form>
	</td>
-->
	<td width="1%"><img src="{www_dir}/admin/images/1x1.gif" width="10" height="10" border="0" alt="" /></td>						
	<td width="1%">
	<span class="top">{intl-ezpublish_version}:</span><br />
	<span class="topusername">{ezpublish_version}</span><br />
	<img src="{www_dir}/admin/images/1x1.gif" width="50" height="10" border="0" alt="" /><br />
	</td>	
	<td width="1%"><img src="{www_dir}/admin/images/1x1.gif" width="10" height="10" border="0" alt="" /></td>
	<td width="1%">
	<span class="top">{intl-site_url}:</span><br />
	<span class="topusername">{site_url}</span><br />
	<img src="{www_dir}/admin/images/1x1.gif" width="100" height="10" border="0" alt="" /><br />
	</td>
	<td width="1%"><img src="{www_dir}/admin/images/1x1.gif" width="10" height="10" border="0" alt="" /></td>
	<td width="1%">
	<span class="top">{intl-user_name}:</span><br />
	<span class="topusername">{first_name}&nbsp;{last_name}</span><br />
	<img src="{www_dir}/admin/images/1x1.gif" width="80" height="10" border="0" alt="" /><br />
	</td>
	<td width="1%"><img src="{www_dir}/admin/images/1x1.gif" width="10" height="10" border="0" alt="" /></td>
	<td width="1%">
	<span class="top">{intl-ip_address}:</span><br />
	<span class="topusername">{ip_address}</span><br />
	<img src="{www_dir}/admin/images/1x1.gif" width="80" height="10" border="0" alt="" /><br />
	</td>
	<td width="1%"><img src="{www_dir}/admin/images/1x1.gif" width="10" height="10" border="0" alt="" /></td>
	<td width="1%" valign="top">

	<img src="{www_dir}/admin/images/{site_style}/top-arrow.gif" width="10" height="13" border="0" alt="" />&nbsp;<a class="top" href="{www_dir}{index}/user/settings?RefURL={ref_url}">{intl-user_settings}</a><br />

	<img src="{www_dir}/admin/images/{site_style}/top-arrow.gif" width="10" height="13" border="0" alt="" />&nbsp;<a class="top" href="{www_dir}{index}/user/passwordchange/">{intl-change_user_info}</a><br />

	<img src="{www_dir}/admin/images/1x1.gif" width="120" height="1" border="0" alt="" /><br />
	</td>
	<td width="1%"><img src="{www_dir}/admin/images/1x1.gif" width="10" height="10" border="0" alt="" /></td>
	<td width="1%" align="right">
	<a  href="{www_dir}{index}/user/login/logout/"><img src="{www_dir}/admin/images/{site_style}/top-logout.gif" width="35" height="40" border="0" alt="" /></a>
	</td>
</tr>
</table>

<!-- BEGIN module_list_tpl -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<!-- BEGIN module_item_tpl -->
<!--
	<td align="center"><a href="{www_dir}{index}/module/{module_action}/{ez_module_name}?RefURL={ref_url}"><img src="{www_dir}/{ez_dir_name}/admin/images/module_icon.gif" width="32" height="32" border="0" alt="{module_name}" /></a></td>
-->
	<td align="center"><a href="{www_dir}{index}/module/{module_action}/{ez_module_name}?RefURL={ref_url}"><img src="{www_dir}/{ez_dir_name}/admin/images/module_icon.gif" width="32" height="32" border="0" alt="{module_name}" /></a></td>
<!-- END module_item_tpl -->
<!-- BEGIN module_control_tpl -->
	<td>&nbsp;&nbsp;</td>
	<td align="left">
	<img src="{www_dir}/admin/images/{site_style}/top-arrow.gif" width="10" height="13" border="0" alt="" />&nbsp;<a class="top" href="{www_dir}{index}/module/activate/all?RefURL={ref_url}">{intl-all}</a><br />
	<img src="{www_dir}/admin/images/{site_style}/top-arrow.gif" width="10" height="13" border="0" alt="" />&nbsp;<a class="top" href="{www_dir}{index}/module/activate/none?RefURL={ref_url}">{intl-none}</a>
	</td>
<!-- END module_control_tpl -->
z</tr>
</table>
<!-- END module_list_tpl -->
	
	</td>
    <td class="repeaty" width="%" background="{www_dir}/admin/images/{site_style}/top-r02.gif" valign="top" align="left"><img src="{www_dir}/admin/images/{site_style}/1x1.gif" width="10" height="10" border="0" alt="" /><br /></td>
</tr>
<tr>
    <td class="repeaty" width="1" valign="top" align="left"><img src="{www_dir}/admin/images/{site_style}/top-l03.gif" width="10" height="10" border="0" alt="" /><br /></td>
    <td class="repeatx" width="50%" background="{www_dir}/admin/images/{site_style}/top-m02.gif" valign="absmiddle" align="left" bgcolor="#ffffff"><img src="{www_dir}/admin/images/{site_style}/1x1.gif" width="120" height="6" border="0" alt="" /><br /></td>
    <td class="repeatx" width="50%" background="{www_dir}/admin/images/{site_style}/top-m02.gif" valign="absmiddle" align="left" bgcolor="#ffffff"><img src="{www_dir}/admin/images/{site_style}/1x1.gif" width="120" height="6" border="0" alt="" /><br /></td>
    <td class="repeaty" width="1" valign="top" align="left"><img src="{www_dir}/admin/images/{site_style}/top-r03.gif" width="10" height="10" border="0" alt="" /><br /></td>
</tr>
<tr>
	<td colspan="4" class="tdmini"><img src="{www_dir}/admin/images/{site_style}/1x1.gif" width="6" height="6" border="0" alt="" /><br /></td>
</tr>
</table>


<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
<!-- BEGIN menu_tpl -->

	<td width="1%" valign="top">

<!-- END menu_tpl -->

<!-- Menues: Start -->
