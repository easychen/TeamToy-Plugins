<?php
/*** 
TeamToy extenstion info block  
##name Basic认证 
##folder_name basic_auth
##author Easy
##email Easychen@qq.com
##reversion 1
##desp 给TeamToy添加Basic认证，所有人需要先通过才能访问API和登入页面，提升安全性 
##update_url http://tt2net.sinaapp.com/?c=plugin&a=update_package&name=basic_auth 
##reverison_url http://tt2net.sinaapp.com/?c=plugin&a=latest_reversion&name=basic_auth 
***/

if( !defined('IN') ) die('bad request');

$plugin_lang = array();
$plugin_lang['zh_cn'] = array
(
	'PL_BASIC_AUTH_SETTINGS_TITLE' => 'Basic认证设置',
	'PL_BASIC_AUTH_PASSWORD_NOT_SAME' => '两次输入的密码不一致，请重新输入',
	'PL_BASIC_AUTH_SETTINGS_UPDATED' => '设置已保存',
	'PL_BASIC_AUTH_USERNAME' => '用户名',
	'PL_BASIC_AUTH_PASSWORD' => '密码',
	'PL_BASIC_AUTH_PASSWORD_REPEAT' => '重复密码',
	'PL_BASIC_AUTH_ACTIVE' => '启用'
);

$plugin_lang['zh_tw'] = array
(
	'PL_BASIC_AUTH_SETTINGS_TITLE' => 'Basic認證設置',
	'PL_BASIC_AUTH_PASSWORD_NOT_SAME' => '兩次輸入的密碼不一致，請重新輸入',
	'PL_BASIC_AUTH_SETTINGS_UPDATED' => '設置已保存',
	'PL_BASIC_AUTH_USERNAME' => '用戶名',
	'PL_BASIC_AUTH_PASSWORD' => '密碼',
	'PL_BASIC_AUTH_PASSWORD_REPEAT' => '重複密碼',
	'PL_BASIC_AUTH_ACTIVE' => '啟用'
);

$plugin_lang['us_en'] = array
(
	'PL_BASIC_AUTH_SETTINGS_TITLE' => 'Basic Settings',
	'PL_BASIC_AUTH_PASSWORD_NOT_SAME' => 'Password not same , retry please',
	'PL_BASIC_AUTH_SETTINGS_UPDATED' => 'Settings updated',
	'PL_BASIC_AUTH_USERNAME' => 'User',
	'PL_BASIC_AUTH_PASSWORD' => 'Password',
	'PL_BASIC_AUTH_PASSWORD_REPEAT' => 'Repeat',
	'PL_BASIC_AUTH_ACTIVE' => 'Active'
);

plugin_append_lang( $plugin_lang );


// 添加邮件设置菜单
add_action( 'UI_USERMENU_ADMIN_LAST' , 'basic_auth_menu_list');
function basic_auth_menu_list()
{
	?><li><a href="javascript:show_float_box( '<?=__('PL_BASIC_AUTH_SETTINGS_TITLE')?>' , '?c=plugin&a=basic_auth' );void(0);"><?=__('PL_BASIC_AUTH_SETTINGS_TITLE')?></a></li>
	<?php 	 	
} 

add_action( 'PLUGIN_BASIC_AUTH' , 'plugin_basic_auth');
function  plugin_basic_auth()
{
	$data['bauth_username'] = kget('bauth_username');
	$data['bauth_password'] = kget('bauth_password');
	$data['bauth_on'] = kget('bauth_on');
	
	return render( $data , 'ajax' , 'plugin' , 'basic_auth' ); 
}

if( intval(kget('bauth_on')) == 1 )
	add_filter( 'UPLOAD_CURL_SETTINGS' , 'plugin_basic_curl' );

function plugin_basic_curl( $ch )
{
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC); 
	curl_setopt($ch, CURLOPT_USERPWD, kget('bauth_username').":".kget('bauth_password')); 	
	
	return $ch;
}

add_action( 'PLUGIN_BASIC_AUTH_SAVE' , 'plugin_basic_auth_save');
function  plugin_basic_auth_save()
{
	if( z(t(v('bauth_password'))) != z(t(v('bauth_password2'))) ) 
	return ajax_echo(__('PL_BASIC_AUTH_PASSWORD_NOT_SAME'));
	
	$bauth_username = z(t(v('bauth_username')));
	$bauth_password = z(t(v('bauth_password')));
	$bauth_on = intval(t(v('bauth_on')));
	

	kset('bauth_username' , $bauth_username);	
	kset('bauth_password' , $bauth_password);	
	kset('bauth_on' , $bauth_on);		

	return ajax_echo( __('PL_BASIC_AUTH_SETTINGS_UPDATED').'<script>setTimeout( close_float_box, 500)</script>');

}

add_action( 'CTRL_SESSION_STARTED' , 'basic_auth_do' );
function basic_auth_do()
{
	if( !is_login() )
	{
		if( intval(kget('bauth_on')) == 1 )
		{
			if( !isset($_SERVER['PHP_AUTH_USER']) )
			{
				//
				header('WWW-Authenticate: Basic realm="'.c('site_name').'"');
				header('HTTP/1.0 401 Unauthorized');
				echo 'Members Only';
				exit;
			}
			else
			{
				if( t($_SERVER['PHP_AUTH_USER']) != t(kget('bauth_username'))
				 || t($_SERVER['PHP_AUTH_PW']) != t(kget('bauth_password'))
				)
				{
					echo 'Bad username or password. Close browser and try again';
					exit;
				}
			}
		}
		
	}
}


