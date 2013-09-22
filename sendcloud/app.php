<?php
/*** 
TeamToy extenstion info block  
##name SendCloud邮件发送 
##folder_name sendcloud
##author Easy
##email Easychen@qq.com
##reversion 1
##desp 通过调用sendcloud进行邮件通知。 
##update_url http://tt2net.sinaapp.com/?c=plugin&a=update_package&name=sendcloud 
##reverison_url http://tt2net.sinaapp.com/?c=plugin&a=latest_reversion&name=sendcloud 
***/

if( !defined('IN') ) die('bad request');


$plugin_lang = array();
$plugin_lang['zh_cn'] = array
(

	'PL_SENDCLOUD_SETTINGS' => 'SendCloud设置',
	'PL_SENDCLOUD_SETTINGS_CANNOT_EMPTY' => '设置内容不能为空',
	'PL_SENDCLOUD_SETTINGS_UPDATED' => '设置已保存',
	'JS_PL_SENDCLOUD_NOTICE_ON' => '你将会收到邮件通知',
	'JS_PL_SENDCLOUD_NOTICE_OFF' => '你将不再收到邮件通知',
	
	'JS_PL_SENDCLOUD_TEST_MAIL_SENT' => '已经向%s发送了邮件，请登入邮箱检查。如果邮件在垃圾箱，请将发件人加入白名单。',
	'JS_PL_SENDCLOUD_TEST_MAIL_ERROR' => '发送失败，请检查配置项是否填写完整，错误信息%s',
	'JS_PL_SENDCLOUD_SENDING_MAIL' => '正在发送队列中的邮件-剩余%s封',

	'PL_SENDCLOUD_TEST_MAIL_TITLE' => '来自TeamToy的测试邮件%s',
	'PL_SENDCLOUD_TEST_MAIL_CONTENT' => '如果您收到这封邮件说明您在SMTP中的邮件配置是正确的；如果您在垃圾邮箱找到这封邮件，请将发件人加入白名单。',


	'PL_SENDCLOUD_NOTICE_MAIL_TITLE' => '%s邮件通知 - %s',
	'PL_SENDCLOUD_NOTICE_MAIL_CONTENT_POSTFIX' => '- <a href="%s">点击这里查看详情</a>',
	'PL_SENDCLOUD_TITLE_SHORT' => '邮件通知',

	'PL_SENDCLOUD_USER' => 'SendCloud用户名',
	'PL_SENDCLOUD_KEY' => 'SendCloud Key',
	'PL_SENDCLOUD_FROM' => '邮件中显示的发送邮箱',
	'PL_SENDCLOUD_URL' => 'SendCloud URL',
	'PL_SENDCLOUD_SEND_TO_NOT_ONLINE' => '只给不在线的同学发邮件',
	'PL_SENDCLOUD_ON' => '启用',
	'PL_SENDCLOUD_SEND_TEST_MAIL' => '发送测试邮件',


	'PL_SENDCLOUD_TEST' => ''
);


plugin_append_lang( $plugin_lang );

// 添加邮件设置菜单
add_action( 'UI_USERMENU_ADMIN_LAST' , 'sendcloud_menu_list');
function sendcloud_menu_list()
{
	?><li><a href="javascript:show_float_box( '<?=__('PL_SENDCLOUD_SETTINGS')?>' , '?c=plugin&a=sendcloud' );void(0);"><?=__('PL_SENDCLOUD_SETTINGS')?></a></li>
	<?php 	 	
} 

add_action( 'PLUGIN_SENDCLOUD' , 'plugin_sendcloud');
function  plugin_sendcloud()
{

	$data['sendcloud_on'] = kget('sendcloud_on');
	$data['sendcloud_user'] = kget('sendcloud_user');
	$data['sendcloud_key'] = kget('sendcloud_key');
	$data['sendcloud_from'] = kget('sendcloud_from');
	$data['sendcloud_url'] = kget('sendcloud_url');
	
	return render( $data , 'ajax' , 'plugin' , 'sendcloud' ); 
}

add_action( 'PLUGIN_SENDCLOUD_UPDATE' , 'plugin_sendcloud_update');
function  plugin_sendcloud_update()
{
	$sendcloud_on = intval(t(v('sendcloud_on')));
	$sendcloud_user = z(t(v('sendcloud_user')));
	$sendcloud_key = z(t(v('sendcloud_key')));
	$sendcloud_from = z(t(v('sendcloud_from')));
	$sendcloud_url = z(t(v('sendcloud_url')));
	
	if( strlen( $sendcloud_user ) < 1 
		|| strlen( $sendcloud_key ) < 1 
		|| strlen( $sendcloud_from ) < 1 
		|| strlen( $sendcloud_url ) < 1 

	) return ajax_echo( __('PL_SENDCLOUD_SETTINGS_CANNOT_EMPTY') );

	kset('sendcloud_on' , $sendcloud_on);	
	kset('sendcloud_user' , $sendcloud_user);	
	kset('sendcloud_key' , $sendcloud_key);	
	kset('sendcloud_from' , $sendcloud_from);	
	kset('sendcloud_url' , $sendcloud_url);	
	
	return ajax_echo( __('PL_SENDCLOUD_SETTINGS_UPDATED') . '<script>setTimeout( close_float_box, 500)</script>');

}



add_action( 'SEND_NOTICE_AFTER' , 'send_notice_mail' );
function send_notice_mail( $data )
{
	if( intval(kget('sendcloud_on')) != 1 ) return false;
	// 未设置，或者设置为接受

	// 检查是否在线
	// 只有不在线的时候发送邮件通知
	$send = true;

	/*
	if( intval( kget('mqueue_not_online') ) == 1 && is_online( $data['uid'] ) )
	$send = false;
	*/


	if( $send )
	{
		$user = get_user_info_by_id( $data['uid'] );
		$dd = array();
		$dd['to'] = $email = $user['email'];
		$dd['subject'] = __( 'PL_SENDCLOUD_NOTICE_MAIL_TITLE',array( c('site_name') , mb_strimwidth( $data['content'] , 0 , 20 , '...' , 'UTF-8' ) ) );

		$dd['body'] = $data['content'] .__('PL_SENDCLOUD_NOTICE_MAIL_CONTENT_POSTFIX',c('site_url') . '/?c=inbox');
		
		sendcloud_mail( $dd['to'] , $dd['subject'] , $dd['body'] );
	}

	
}

function sendcloud_mail( $to , $subject , $content , $bcc = false )
{
	$to = str_replace( ',', ';', $to);

	$option = array
	(
		'api_user' => kget('sendcloud_user'),
		'api_key' => kget('sendcloud_key'),
		'from' => kget('sendcloud_from'),
		'to' => $to,
		'subject' => $subject,
		'html' => $content
	);

	if( $bcc !== false ) $option['bcc'] = $bcc;

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
	curl_setopt($ch, CURLOPT_URL, kget('sendcloud_url'));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $option );
	curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, 3 );

  	$result = curl_exec($ch);
  	curl_close($ch);

  	$ret = json_decode( $result , 1 );
  	kset( 'last-sendcloud-info' , $result );
  	return strtolower($ret['message']) == 'success';
	
}