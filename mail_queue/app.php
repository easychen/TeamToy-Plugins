<?php
/*** 
TeamToy extenstion info block  
##name 邮件队列 
##folder_name mail_queue
##author Easy
##email Easychen@qq.com
##reversion 1
##desp 通过ajax模拟邮件队列，解决群发邮件时php卡死的问题。支持smtp。 
##update_url http://tt2net.sinaapp.com/?c=plugin&a=update_package&name=mail_queque 
##reverison_url http://tt2net.sinaapp.com/?c=plugin&a=latest_reversion&name=mail_queque 
***/

if( !defined('IN') ) die('bad request');

// 检查并创建数据库
if( !mysql_query("SHOW COLUMNS FROM `mail_queue`",db()) )
{
	// table not exists
	// create it
	run_sql("CREATE TABLE IF NOT EXISTS `mail_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(256) NOT NULL,
  `data` text NOT NULL,
  `timeline` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; ");

	if( $data = get_data("SELECT `id` FROM `user` ") )
	{
		$sql = "REPLACE INTO `keyvalue` ( `key` , `value` ) VALUES ";
		foreach( $data as $item )
		{
			$v[] = "( 'mqueue_usettings_" . intval($item['id']) . "' , 1 )";
		}

		$sql = $sql . join( ' , ' , $v );
		run_sql( $sql );
	}

	kset( 'mqueue_not_online' , 1 );
}


$plugin_lang = array();
$plugin_lang['zh_cn'] = array
(

	'PL_MAIL_QUEUE_SETTINGS' => '邮件发送设置',
	'PL_MAIL_QUEUE_SETTINGS_CANNOT_EMPTY' => '设置内容不能为空',
	'PL_MAIL_QUEUE_SETTINGS_UPDATED' => '设置已保存',
	'JS_PL_MAIL_QUEUE_NOTICE_ON' => '你将会收到邮件通知',
	'JS_PL_MAIL_QUEUE_NOTICE_OFF' => '你将不再收到邮件通知',
	
	'JS_PL_MAIL_QUEUE_TEST_MAIL_SENT' => '已经向%s发送了邮件，请登入邮箱检查。如果邮件在垃圾箱，请将发件人加入白名单。',
	'JS_PL_MAIL_QUEUE_TEST_MAIL_ERROR' => '发送失败，请检查配置项是否填写完整，错误信息%s',
	'JS_PL_MAIL_QUEUE_SENDING_MAIL' => '正在发送队列中的邮件-剩余%s封',

	'PL_MAIL_QUEUE_TEST_MAIL_TITLE' => '来自TeamToy的测试邮件%s',
	'PL_MAIL_QUEUE_TEST_MAIL_CONTENT' => '如果您收到这封邮件说明您在SMTP中的邮件配置是正确的；如果您在垃圾邮箱找到这封邮件，请将发件人加入白名单。',


	'PL_MAIL_QUEUE_NOTICE_MAIL_TITLE' => '%s邮件通知 - %s',
	'PL_MAIL_QUEUE_NOTICE_MAIL_CONTENT_POSTFIX' => '- <a href="%s">点击这里查看详情</a>',
	'PL_MAIL_QUEUE_TITLE_SHORT' => '邮件通知',

	'PL_MAIL_QUEUE_SMTP_SERVER' => 'SMTP服务器',
	'PL_MAIL_QUEUE_SMTP_PORT' => '端口',
	'PL_MAIL_QUEUE_SMTP_USERNAME' => '用户名',
	'PL_MAIL_QUEUE_SMTP_PASSWORD' => '密码',
	'PL_MAIL_QUEUE_SEND_TO_NOT_ONLINE' => '只给不在线的同学发邮件',
	'PL_MAIL_QUEUE_ACTIVE' => '启用',
	'PL_MAIL_QUEUE_SEND_TEST_MAIL' => '发送测试邮件',


	'PL_MAIL_QUEUE_TEST' => ''
);

$plugin_lang['zh_tw'] = array
(

'PL_MAIL_QUEUE_SETTINGS' => '郵件發送設置',
'PL_MAIL_QUEUE_SETTINGS_CANNOT_EMPTY' => '設置內容不能為空',
'PL_MAIL_QUEUE_SETTINGS_UPDATED' => '設置已保存',
'JS_PL_MAIL_QUEUE_NOTICE_ON' => '你將會收到郵件通知',
'JS_PL_MAIL_QUEUE_NOTICE_OFF' => '你將不再收到郵件通知',

'JS_PL_MAIL_QUEUE_TEST_MAIL_SENT' => '已經向%s發送了郵件，請登入郵箱檢查。如果郵件在垃圾箱，請將發件人加入白名單。 ',
'JS_PL_MAIL_QUEUE_TEST_MAIL_ERROR' => '發送失敗，請檢查配置項是否填寫完整，錯誤信息%s',
'JS_PL_MAIL_QUEUE_SENDING_MAIL' => '正在發送隊列中的郵件-剩餘%s封',

'PL_MAIL_QUEUE_TEST_MAIL_TITLE' => '來自TeamToy的測試郵件%s',
'PL_MAIL_QUEUE_TEST_MAIL_CONTENT' => '如果您收到這封郵件說明您在SMTP中的郵件配置是正確的；如果您在垃圾郵箱找到這封郵件，請將發件人加入白名單。 ',


'PL_MAIL_QUEUE_NOTICE_MAIL_TITLE' => '%s郵件通知- %s',
'PL_MAIL_QUEUE_NOTICE_MAIL_CONTENT_POSTFIX' => '- <a href="%s">點擊這裡查看詳​​情</a>',
'PL_MAIL_QUEUE_TITLE_SHORT' => '郵件通知',

'PL_MAIL_QUEUE_SMTP_SERVER' => 'SMTP服務器',
'PL_MAIL_QUEUE_SMTP_PORT' => '端口',
'PL_MAIL_QUEUE_SMTP_USERNAME' => '用戶名',
'PL_MAIL_QUEUE_SMTP_PASSWORD' => '密碼',
'PL_MAIL_QUEUE_SEND_TO_NOT_ONLINE' => '只給不在線的同學發郵件',
'PL_MAIL_QUEUE_ACTIVE' => '啟用',
'PL_MAIL_QUEUE_SEND_TEST_MAIL' => '發送測試郵件',


'PL_MAIL_QUEUE_TEST' => ''
);

$plugin_lang['us_en'] = array
(

'PL_MAIL_QUEUE_SETTINGS' => 'Mail Settings',
'PL_MAIL_QUEUE_SETTINGS_CANNOT_EMPTY' => 'Settings can\'t be empty',
'PL_MAIL_QUEUE_SETTINGS_UPDATED' => 'Settings updated',
'JS_PL_MAIL_QUEUE_NOTICE_ON' => 'You will receive notification mail',
'JS_PL_MAIL_QUEUE_NOTICE_OFF' => 'You will not receive notification mail anymore',

'JS_PL_MAIL_QUEUE_TEST_MAIL_SENT' => 'Sent mail to %s, check it now.If you find it in trashbox, add the sender to your whitelist',
'JS_PL_MAIL_QUEUE_TEST_MAIL_ERROR' => 'Send mail error, check settings again , error message - %s',
'JS_PL_MAIL_QUEUE_SENDING_MAIL' => 'Sending mail - %s left',

'PL_MAIL_QUEUE_TEST_MAIL_TITLE' => 'Test mail form TeamToy[%s]',
'PL_MAIL_QUEUE_TEST_MAIL_CONTENT' => 'If you see this means your SMTP settings is right, If you find this in trashbox, add the sender to your whitelist',


'PL_MAIL_QUEUE_NOTICE_MAIL_TITLE' => '%s notification- %s',
'PL_MAIL_QUEUE_NOTICE_MAIL_CONTENT_POSTFIX' => '- <a href="%s">Click for more</a>',
'PL_MAIL_QUEUE_TITLE_SHORT' => 'Mail notification',

'PL_MAIL_QUEUE_SMTP_SERVER' => 'SMTP Server',
'PL_MAIL_QUEUE_SMTP_PORT' => 'Port',
'PL_MAIL_QUEUE_SMTP_USERNAME' => 'Username',
'PL_MAIL_QUEUE_SMTP_PASSWORD' => 'Password',
'PL_MAIL_QUEUE_SEND_TO_NOT_ONLINE' => 'Send to user not online only',
'PL_MAIL_QUEUE_ACTIVE' => 'Active',
'PL_MAIL_QUEUE_SEND_TEST_MAIL' => 'Send test mail',


'PL_MAIL_QUEUE_TEST' => ''
);


plugin_append_lang( $plugin_lang );

// 添加邮件设置菜单
add_action( 'UI_USERMENU_ADMIN_LAST' , 'mail_queue_menu_list');
function mail_queue_menu_list()
{
	?><li><a href="javascript:show_float_box( '<?=__('PL_MAIL_QUEUE_SETTINGS')?>' , '?c=plugin&a=mail_queue' );void(0);"><?=__('PL_MAIL_QUEUE_SETTINGS')?></a></li>
	<?php 	 	
} 

add_action( 'PLUGIN_MAIL_QUEUE' , 'plugin_mail_queue');
function  plugin_mail_queue()
{

	$data['mqueue_on'] = kget('mqueue_on');
	$data['mqueue_server'] = kget('mqueue_server');
	$data['mqueue_port'] = kget('mqueue_port');
	$data['mqueue_username'] = kget('mqueue_username');
	$data['mqueue_password'] = kget('mqueue_password');
	$data['mqueue_not_online'] = kget('mqueue_not_online');
	return render( $data , 'ajax' , 'plugin' , 'mail_queue' ); 
}

add_action( 'PLUGIN_MAIL_QUEUE_SAVE' , 'plugin_mail_queue_save');
function  plugin_mail_queue_save()
{
	$mqueue_on = intval(t(v('mqueue_on')));
	$mqueue_server = z(t(v('mqueue_server')));
	$mqueue_port = z(t(v('mqueue_port')));
	$mqueue_username = z(t(v('mqueue_username')));
	$mqueue_password = z(t(v('mqueue_password')));
	$mqueue_not_online = intval(t(v('mqueue_not_online')));

	if( strlen( $mqueue_server ) < 1 
		|| strlen( $mqueue_port ) < 1 
		|| strlen( $mqueue_username ) < 1 
		|| strlen( $mqueue_password ) < 1 

	) return ajax_echo( __('PL_MAIL_QUEUE_SETTINGS_CANNOT_EMPTY') );

	kset('mqueue_on' , $mqueue_on);	
	kset('mqueue_server' , $mqueue_server);	
	kset('mqueue_port' , $mqueue_port);	
	kset('mqueue_username' , $mqueue_username);	
	kset('mqueue_password' , $mqueue_password);	
	kset('mqueue_not_online' , $mqueue_not_online);	

	return ajax_echo( __('PL_MAIL_QUEUE_SETTINGS_UPDATED') . '<script>setTimeout( close_float_box, 500)</script>');

}

add_action( 'UI_INBOX_LIST_BEFORE' , 'mail_queue_css' );
function  mail_queue_css()
{
	?>
	<style type="text/css">
	#mqueue_settings.on
	{
		background-color:rgb(153, 153, 153);
	}
	</style>
	<?php
}

add_action( 'UI_INBOX_SCRIPT_LAST' , 'mail_queue_js' );
function mail_queue_js()
{
	if( intval(kget('mqueue_on')) != 1 ) return false;
	?>
	function mail_settings_toggle()
	{
		if( $('#mqueue_settings').hasClass('on') )
		{
			$.post( '?c=plugin&a=mset&on=0' , {} , function()
			{
				noty(
				{
					text:__('JS_PL_MAIL_QUEUE_NOTICE_OFF'),
					timeout:1000,
					layout:'topRight',
					type:'warning'
				});
				$('#mqueue_settings').removeClass('on');
			});
		}
		else
		{
			$.post( '?c=plugin&a=mset&on=1' , {} , function()
			{
				noty(
				{
					text:__('JS_PL_MAIL_QUEUE_NOTICE_ON'),
					timeout:1000,
					layout:'topRight',
					type:'success'
				});
				$('#mqueue_settings').addClass('on');
			});
		}
	}
	<?php
}

add_action( 'UI_COMMON_SCRIPT' , 'check_mail_script' );
function check_mail_script()
{
	if( intval(kget('mqueue_on')) != 1 ) return false;
	?>
	var sending_mail = false;
	var mail_noty = null ;

	function mqueue_test()
	{
		var url = '?c=plugin&a=test_mail' ;
		var params = {};

		var params = {};
		$.each( $('#mqueue_form').serializeArray(), function(index,value) 
		{
			params[value.name] = value.value;
		});

		$.post( url , params , function( data )
		{
			var data_obj = $.parseJSON( data );
			if( data_obj.err_code == 0 )
			{
				mail_noty = noty(
				{
						text:__('JS_PL_MAIL_QUEUE_TEST_MAIL_SENT',[data_obj.data.mail_sent]),
						layout:'topRight',
				});
			}
			else
			{
				return alert(__('JS_PL_MAIL_QUEUE_TEST_MAIL_ERROR'));
			}
		});	
	}


	function check_mail()
	{
		var url = '?c=plugin&a=check_mail' ;
	
		var params = {};
		$.post( url , params , function( data )
		{
			var data_obj = $.parseJSON( data );
			if( data_obj.err_code == 0 )
			{
				if( data_obj.data.to_send && parseInt( data_obj.data.to_send ) > 0 )
				{
					if( mail_noty != null )
					{
						mail_noty.setText( __('JS_PL_MAIL_QUEUE_SENDING_MAIL',parseInt( data_obj.data.to_send )));
					}
					else
					mail_noty = noty(
					{
						text: __('JS_PL_MAIL_QUEUE_SENDING_MAIL',parseInt( data_obj.data.to_send )),
						layout:'topRight',
					});

					sending_mail = true;
					check_mail();
				}
				else
				{
					if( sending_mail )
					{
						sending_mail = false;
						mail_noty.close();
					}
				}
			}
		});	
	}

	setTimeout( check_mail , 9000 );
	setInterval( check_mail , 120000 );

	<?php
}

// test_mail

add_action( 'PLUGIN_TEST_MAIL' , 'plugin_test_mail' );
function plugin_test_mail()
{
	include_once( AROOT .'model' . DS . 'api.function.php');
	include_once( AROOT .'controller' . DS . 'api.class.php');
	
	$mqueue_on = intval(t(v('mqueue_on')));
	$mqueue_server = z(t(v('mqueue_server')));
	$mqueue_port = z(t(v('mqueue_port')));
	$mqueue_username = z(t(v('mqueue_username')));
	$mqueue_password = z(t(v('mqueue_password')));
	

	if( strlen( $mqueue_server ) < 1 
		|| strlen( $mqueue_port ) < 1 
		|| strlen( $mqueue_username ) < 1 
		|| strlen( $mqueue_password ) < 1 

	)
	return apiController::send_error( LR_API_ARGS_ERROR , 'SMTP ARGS ERROR ' );


	if($user = get_user_info_by_id( uid() ))
	{
		session_write_close();
		
		if(phpmailer_send_mail( $user['email'] , __('PL_MAIL_QUEUE_TEST_MAIL_TITLE',date("Y-m-d H:i")) , __('PL_MAIL_QUEUE_TEST_MAIL_CONTENT') 
				, $mqueue_username , $mqueue_server , $mqueue_port 
				, $mqueue_username , $mqueue_password  ))
		{
			return apiController::send_result( array( 'mail_sent' => $user['email'] ) );
		}
	}

	return apiController::send_error( 200010 , 'SMTP ERROR - ' . g('LP_MAILER_ERROR') );
	
}

add_action( 'PLUGIN_CHECK_MAIL' , 'plugin_check_mail' );
function plugin_check_mail()
{
	if( intval(kget('mqueue_on')) != 1 ) return false;
	$sql = "SELECT * FROM `mail_queue` WHERE `timeline` > '" . date("Y-m-d H:i:s" , strtotime( "-1 hour" ) ) . "' LIMIT 1";
	if( $line = get_line( $sql ) )
	{
		session_write_close();
		$info = unserialize( $line['data'] );
		if(phpmailer_send_mail( $info['to'] , $info['subject'] , $info['body'] 
			, kget('mqueue_username') , kget('mqueue_server') , kget('mqueue_port') 
			, kget('mqueue_username') , kget('mqueue_password')  ))
		{
			$sql = "DELETE FROM `mail_queue` WHERE `id` = '" . intval( $line['id'] ) . "' LIMIT 1";
		}
		else
		{
			$sql = "UPDATE `mail_queue` SET `timeline` = '" . date("Y-m-d H:i:s" , strtotime("-2 hours")) . "' LIMIT 1 ";
		}

		run_sql( $sql );
	}

	include_once( AROOT .'controller' . DS . 'api.class.php');
	if( db_errno() != 0  ) apiController::send_error( LR_API_DB_ERROR , 'DATABASE ERROR ' . db_error() );
	return apiController::send_result( array('to_send'=>get_var("SELECT COUNT(*) FROM `mail_queue` WHERE `timeline` > '" . date("Y-m-d H:i:s" , strtotime( "-1 hour" ) ) . "' ")) );
}


add_action( 'SEND_NOTICE_AFTER' , 'send_notice_mail' );
function send_notice_mail( $data )
{
	if( intval(kget('mqueue_on')) != 1 ) return false;
	if( intval(kget('mqueue_usettings_'.$data['uid'])) == 1  )
	{
		// 未设置，或者设置为接受

		// 检查是否在线
		// 只有不在线的时候发送邮件通知
		$send = true;

		if( intval( kget('mqueue_not_online') ) == 1 && is_online( $data['uid'] ) )
		$send = false;



		if( $send )
		{
			$user = get_user_info_by_id( $data['uid'] );
			$dd = array();
			$dd['to'] = $email = $user['email'];
			$dd['subject'] = __( 'PL_MAIL_QUEUE_NOTICE_MAIL_TITLE',array( c('site_name') , mb_strimwidth( $data['content'] , 0 , 20 , '...' , 'UTF-8' ) ) );

			$dd['body'] = $data['content'] .__('PL_MAIL_QUEUE_NOTICE_MAIL_CONTENT_POSTFIX',c('site_url') . '/?c=inbox');
			
			$sql = "INSERT INTO `mail_queue` ( `email` , `data` , `timeline` ) VALUES ( '" . s( $email ) . "' , '" . s(serialize($dd)) . "' , '" . s(date("Y-m-d H:i:s")) . "' )";
			run_sql( $sql );
		}

		

	}

	
}

add_action( 'PLUGIN_MSET' , 'plugin_mset' );
function plugin_mset()
{
	if( intval(v('on')) == 1 ) kset(  'mqueue_usettings_'.uid() , 1 );
	else kset(  'mqueue_usettings_'.uid() , 0 );
}

add_action( 'UI_INBOX_SETTINGS_LAST' , 'mail_queue_inbox_icon');
function mail_queue_inbox_icon()
{
	if( intval(kget('mqueue_on')) == 1 )
	{
		if( intval(kget('mqueue_usettings_'.uid())) == 1 )
		{
			?>
			<li id="mqueue_settings" class="on"><a href="javascript:mail_settings_toggle();void(0);" title="<?=__('PL_MAIL_QUEUE_TITLE_SHORT')?>" ><img src="<?=image('settings.btn.email.png')?>"/></a></li>
			<?php
		}
		else
		{
			?>
			<li id="mqueue_settings" ><a href="javascript:mail_settings_toggle();void(0);" title="<?=__('PL_MAIL_QUEUE_TITLE_SHORT')?>" ><img src="<?=image('settings.btn.email.png')?>"/></a></li>
			<?php
		}
	}
}