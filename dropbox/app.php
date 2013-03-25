<?php
/*** 
TeamToy extenstion info block
##name 网盘文件插件
##folder_name dropbox
##author Easy
##email Easychen@qq.com
##reversion 1
##desp 允许在评论中添加 Dropbox、微盘的文件连接
##update_url http://tt2net.sinaapp.com/?c=plugin&a=update_package&name=dropbox
##reverison_url http://tt2net.sinaapp.com/?c=plugin&a=latest_reversion&name=dropbox
***/

if( !defined('IN') ) die('bad request');

$plugin_lang = array();

$plugin_lang['zh_cn'] = array
(

	'PL_DROPBOX_TITLE' => 'DropBox设置',
	'PL_DROPBOX_TOOLBAR' => '附件',
	'PL_DROPBOX_SETTINGS_CANNOT_EMPTY' => '设置内容不能为空',
	'PL_DROPBOX_SETTINGS_UPDATED' => '设置已保存',
	'PL_DROPBOX_TEST' => ''
);

$plugin_lang['zh_tw'] = array
(

	'PL_DROPBOX_TITLE' => 'DropBox設置',
	'PL_DROPBOX_TOOLBAR' => '附件',
	'PL_DROPBOX_SETTINGS_CANNOT_EMPTY' => '設置內容不能為空',
	'PL_DROPBOX_SETTINGS_UPDATED' => '設置已保存',
	'PL_DROPBOX_TEST' => ''
);

$plugin_lang['us_en'] = array
(

	'PL_DROPBOX_TITLE' => 'DropBox Settings',
	'PL_DROPBOX_TOOLBAR' => 'Attach files',
	'PL_DROPBOX_SETTINGS_CANNOT_EMPTY' => 'Settings can\'t be empty',
	'PL_DROPBOX_SETTINGS_UPDATED' => 'Settings updated',
	'PL_DROPBOX_TEST' => ''
);

plugin_append_lang( $plugin_lang );

add_action( 'UI_HEAD' ,'dropbox_js');
function dropbox_js()
{
	?>
	<style type="text/css">
	#todo_comment_toolbar
	{
		background-color: #FAFAFA;
		padding:5px;
		display:none;
	}
	</style>
	<?php
	if( intval(kget('dropbox_on')) == 1 && strlen( kget('dropbox_akey') ) > 5 )
	{	
	?>
	<script type="text/javascript" src="https://www.dropbox.com/static/api/1/dropbox.js" id="dropboxjs" data-app-key="<?=kget('dropbox_akey')?>"></script>
	<script type="text/javascript">
	function show_dropbox()
	{
		Dropbox.choose(
		{
			linkType:'preview', 
			success:function( files )
			{
				$('#comment_text').val( $('#comment_text').val() + ' ' + files[0].link );
				$('#todo_comment_toolbar').slideUp();
			}
		});	
	}

	var vwin = null;

	function show_vdisk()
	{
		vwin = window.open('http://vshare.sinaapp.com/share/','new','width=600,height=600, menubar=no, toolbar=no,location=no,directories=no,status=no,scrollbars=yes,resizable=yes');
	}

	function vdisk_file_selected( link )
	{
		alert( link );
		vwin.close();
		$('#comment_text').val( $('#comment_text').val() + ' ' + link );
		$('#todo_comment_toolbar').slideUp();
	}

	function JS_TODO_DETAIL_CALLBACK()
	{
		$('#comment_text').unbind( 'focus');
		$('#comment_text').bind( 'focus' , function()
		{
			$('#todo_comment_toolbar').slideDown();
		});
	}

	</script>
	<?php
	};
}



add_action( 'UI_TODO_DETAIL_COMMENTBOX_TOOLBAR' , 'dropbox_show_link' );
function dropbox_show_link( $data )
{
	?>
	<div id="todo_comment_toolbar"><i class="icon-hdd"></i>&nbsp;<?=__('PL_DROPBOX_TOOLBAR')?> <a href="javascript:show_dropbox();void(0);">DropBox</a><!-- | <a href="javascript:show_vdisk();void(0);">微盘</a> --></div>
	<?php
}

// 添加邮件设置菜单
add_action( 'UI_USERMENU_ADMIN_LAST' , 'dropbox_menu_list');
function dropbox_menu_list()
{
	?><li><a href="javascript:show_float_box( '<?=__('PL_DROPBOX_TITLE')?>' , '?c=plugin&a=dropBox' );void(0);"><?=__('PL_DROPBOX_TITLE')?></a></li>
	<?php 	 	
} 

add_action( 'PLUGIN_DROPBOX' , 'plugin_dropbox');
function  plugin_dropbox()
{

	$data['dropbox_akey'] = kget('dropbox_akey');
	$data['dropbox_on'] = kget('dropbox_on');
	
	return render( $data , 'ajax' , 'plugin' , 'dropbox' ); 
}

add_action( 'PLUGIN_DROPBOX_SAVE' , 'plugin_dropbox_save');
function  plugin_dropbox_save()
{
	$dropbox_on = intval(t(v('dropbox_on')));
	$dropbox_akey = z(t(v('dropbox_akey')));
	

	if( strlen( $dropbox_akey ) < 1 )
		 return ajax_echo(__('PL_DROPBOX_SETTINGS_CANNOT_EMPTY'));

	kset('dropbox_on' , $dropbox_on);	
	kset('dropbox_akey' , $dropbox_akey);	
	

	return ajax_echo(__('PL_DROPBOX_SETTINGS_UPDATED').'<script>setTimeout( close_float_box, 500)</script>');

}

