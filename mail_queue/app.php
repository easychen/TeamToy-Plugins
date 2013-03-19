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

// 添加邮件设置菜单
add_action( 'UI_USERMENU_ADMIN_LAST' , 'mail_queue_menu_list');
function mail_queue_menu_list()
{
	?><li><a href="javascript:show_float_box( '邮件发送设置' , '?c=plugin&a=mail_queue' );void(0);">邮件发送设置</a></li>
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

	) return ajax_echo('设置内容不能为空');

	kset('mqueue_on' , $mqueue_on);	
	kset('mqueue_server' , $mqueue_server);	
	kset('mqueue_port' , $mqueue_port);	
	kset('mqueue_username' , $mqueue_username);	
	kset('mqueue_password' , $mqueue_password);	
	kset('mqueue_not_online' , $mqueue_not_online);	

	return ajax_echo('设置已保存<script>setTimeout( close_float_box, 500)</script>');

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
					text:'你将不再收到邮件通知',
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
					text:'你将会收到邮件通知',
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
						text:'已经向'+ data_obj.data.mail_sent +'发送了邮件，请登入邮箱检查。如果邮件在垃圾箱，请将发件人加入白名单。',
						layout:'topRight',
				});
			}
			else
			{
				return alert('发送失败，请检查配置项是否填写完整，错误信息'+data_obj.err_msg);
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
						mail_noty.setText('正在发送队列中的邮件-剩余'+parseInt( data_obj.data.to_send )+'封');
					}
					else
					mail_noty = noty(
					{
						text:'正在发送队列中的邮件-剩余'+parseInt( data_obj.data.to_send )+'封',
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
		
		if(phpmailer_send_mail( $user['email'] , '来自TeamToy的测试邮件 '.date("Y-m-d H:i") , '如果您收到这封邮件说明您在SMTP中的邮件配置是正确的；如果您在垃圾邮箱找到这封邮件，请将发件人加入白名单。' 
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
			$dd['subject'] = c('site_name').'邮件通知 - ' . mb_strimwidth( $data['content'] , 0 , 20 , '...' , 'UTF-8' );
			$dd['body'] = $data['content'] . ' - <a href="' . c('site_url') . '/?c=inbox">点击这里查看详情</a>';
			
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
			<li id="mqueue_settings" class="on"><a href="javascript:mail_settings_toggle();void(0);" title="邮件通知" ><img src="<?=image('settings.btn.email.png')?>"/></a></li>
			<?php
		}
		else
		{
			?>
			<li id="mqueue_settings" ><a href="javascript:mail_settings_toggle();void(0);" title="邮件通知" ><img src="<?=image('settings.btn.email.png')?>"/></a></li>
			<?php
		}
	}
}