<?php
/*** 
TeamToy extenstion info block
##name SimpleToken
##folder_name simple_token
##author Easy
##email Easychen@qq.com
##reversion 1
##desp SimpleToken让用户可以创建一个当前账户的永不过期的Token，并在调用API的时候使用。SimpleToekn支持json和jsonp两种输出。 
##update_url http://tt2net.sinaapp.com/?c=plugin&a=update_package&name=stoken 
##reverison_url http://tt2net.sinaapp.com/?c=plugin&a=latest_reversion&name=stoken 
***/

if( !defined('IN') ) die('bad request');

// 创建方便外部引用的长期Token，可手工关闭和重置

// 检查并创建数据库
if( !mysql_query("SHOW COLUMNS FROM `stoken`",db()) )
{
	// table not exists
	// create it
	run_sql("CREATE TABLE IF NOT EXISTS `stoken` (
  `uid` int(11) NOT NULL,
  `token` varchar(32) NOT NULL,
  `on` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");

}

$plugin_lang = array();
$plugin_lang['zh_cn'] = array
(
	'PL_SIMPLE_TOKEN_MENU' => 'Simple Token API',
	'PL_SIMPLE_TOKEN_TITLE' => 'Simple Token',
	'PL_SIMPLE_TOKEN_EXPLAIN' => '<p>SimpleToken是一个永不过期的Token，通过它你可以直接用URL访问TeamToy数据。</p>
<p>为保证安全，请在不使用时关闭此功能。</p>',
	'PL_SIMPLE_TOKEN_TO_ACTIVE' => '启用Token',
	'PL_SIMPLE_TOKEN_STOPPED' => 'Token %s (已停用)',
	'PL_SIMPLE_TOKEN_ACTIVE' => 'Token %s ',
	'PL_SIMPLE_TOKEN_RESET' => '重置Token',
	'PL_SIMPLE_TOKEN_STOP' => '停用Token',
	'PL_SIMPLE_TOKEN_DOC_TODOLIST' => '<a href="?c=api&a=todo_list&stoken=%s" target="new">获取我的TODO List</a>',
	'PL_SIMPLE_TOKEN_DOC_UNREAD_MESSAGE' => '<a href="?c=api&a=user_unread&stoken=%s" target="new">获取我的未读消息</a>',
	'PL_SIMPLE_TOKEN_DOC_MEMBERS' => '<a href="?c=api&a=team_members&stoken=%s" target="new">获取成员联系信息</a>',
	'PL_SIMPLE_TOKEN_DOC_FEED' => '<a href="?c=api&a=feed_list&stoken=%s" target="new">获取最新团队动态</a>',
	'PL_SIMPLE_TOKEN_DOC_MORE' => '<a href="%s/apidoc.html" target="_blank">查看更多API</a><br/>↑将文档中的\'token\'变量改为\'stoken\'即可',
	'PL_SIMPLE_TOKEN_CREATE_ERROR' => '创建失败，请稍后再试',
	'PL_SIMPLE_TOKEN_RESET_CONFIRM' => '确定要重置Token么？之前使用了接口的程序可能因此失效。',
	
	'PL_SIMPLE_TOKEN_TEST' => ''
);

$plugin_lang['zh_tw'] = array
(
'PL_SIMPLE_TOKEN_MENU' => 'Simple Token API',
'PL_SIMPLE_TOKEN_TITLE' => 'Simple Token',
'PL_SIMPLE_TOKEN_EXPLAIN' => '<p>SimpleToken是一個永不過期的Token，通過它你可以直接用URL訪問TeamToy數據。 </p>
<p>為保證安全，請在不使用時關閉此功能。 </p>',
'PL_SIMPLE_TOKEN_TO_ACTIVE' => '啟用Token',
'PL_SIMPLE_TOKEN_STOPPED' => 'Token %s (已停用)',
'PL_SIMPLE_TOKEN_ACTIVE' => 'Token %s ',
'PL_SIMPLE_TOKEN_RESET' => '重置Token',
'PL_SIMPLE_TOKEN_STOP' => '停用Token',
'PL_SIMPLE_TOKEN_DOC_TODOLIST' => '<a href="?c=api&a=todo_list&stoken=%s" target="new">獲取我的TODO List</a>',
'PL_SIMPLE_TOKEN_DOC_UNREAD_MESSAGE' => '<a href="?c=api&a=user_unread&stoken=%s" target="new">獲取我的未讀消息</a>',
'PL_SIMPLE_TOKEN_DOC_MEMBERS' => '<a href="?c=api&a=team_members&stoken=%s" target="new">獲取成員聯繫信息</a>',
'PL_SIMPLE_TOKEN_DOC_FEED' => '<a href="?c=api&a=feed_list&stoken=%s" target="new">獲取最新團隊動態</a>',
'PL_SIMPLE_TOKEN_DOC_MORE' => '<a href="%s/apidoc.html" target="_blank">查看更多API</a><br/>↑將文檔中的\'token\'變量改為\'stoken\'即可',
'PL_SIMPLE_TOKEN_CREATE_ERROR' => '創建失敗，請稍後再試',
'PL_SIMPLE_TOKEN_RESET_CONFIRM' => '確定要重置Token麼？之前使用了接口的程序可能因此失效。 ',

'PL_SIMPLE_TOKEN_TEST' => ''
);

$plugin_lang['us_en'] = array
(
'PL_SIMPLE_TOKEN_MENU' => 'Simple Token API',
'PL_SIMPLE_TOKEN_TITLE' => 'Simple Token',
'PL_SIMPLE_TOKEN_EXPLAIN' => '<p> SimpleToken is a token never expires, through which you can directly access TeamToy data via URL  </ p>
<p>Please turn this feature off when not in use to ensure safety</ p> ',
'PL_SIMPLE_TOKEN_TO_ACTIVE' => 'Enable Token',
'PL_SIMPLE_TOKEN_STOPPED' => 'Token %s (disabled)',
'PL_SIMPLE_TOKEN_ACTIVE' => 'Token %s',
'PL_SIMPLE_TOKEN_RESET' => 'Reset Token',
'PL_SIMPLE_TOKEN_STOP' => 'Disable Token',
'PL_SIMPLE_TOKEN_DOC_TODOLIST' => '<a href="?c=api&a=todo_list&stoken=%s" target="new">Get my TODO List </ a>',
'PL_SIMPLE_TOKEN_DOC_UNREAD_MESSAGE' => '<a href="?c=api&a=user_unread&stoken=%s" target="new"> Get my unread messages </ a>',
'PL_SIMPLE_TOKEN_DOC_MEMBERS' => '<a href="?c=api&a=team_members&stoken=%s" target="new"> Get members\' contact information </ a>',
'PL_SIMPLE_TOKEN_DOC_FEED' => '<a href="?c=api&a=feed_list&stoken=%s" target="new"> Get team feeds</ a>',
'PL_SIMPLE_TOKEN_DOC_MORE' => '<a href="%s/apidoc.html" target="_blank">More API </ a> ↑ change the \' token \'variable to \'stoken \' in document  ',
'PL_SIMPLE_TOKEN_CREATE_ERROR' => 'Create failed, please try again later',
'PL_SIMPLE_TOKEN_RESET_CONFIRM' => 'Reset Token now? Old code using this token may not available ',

'PL_SIMPLE_TOKEN_TEST' =>''
);

plugin_append_lang( $plugin_lang );

// 添加顶部导航按钮
add_action( 'UI_USERMENU_BOTTOM' , 'stoken_user_menu' );
function stoken_user_menu()
{
	?><li><a href="javascript:show_float_box( '<?=__('PL_SIMPLE_TOKEN_TITLE')?>' , '?c=plugin&a=simple_token' );void(0);"><?=__('PL_SIMPLE_TOKEN_MENU')?></a></li>
	<?php 	
}


// 添加显示页面的逻辑
add_action( 'PLUGIN_SIMPLE_TOKEN' , 'plugin_simple_token');
function plugin_simple_token()
{
	$do = z(t(v('do')));

	switch( $do )
	{
		case 'create':
		case 'refresh':
			$new_token = substr( md5( uid() . time("Y h j G") . rand( 1 , 9999 ) ) , 0 , rand( 9 , 20 ) );
			$new_token = uid() . substr( md5( $new_token ) , 0 , 10 );
			$sql = "REPLACE INTO `stoken` ( `uid` , `token` , `on` ) VALUES ( '" . intval( uid() ) . "' , '" . s($new_token) . "' , '1' )";
			run_sql( $sql );
			if( db_errno() == 0 ) return ajax_echo('done');
			else return ajax_echo('error');
			break;
		
		case 'close':
			$sql = "UPDATE `stoken` SET `on` = '0' WHERE `uid` = '" . intval(uid()) .  "' LIMIT 1";
			run_sql( $sql );
			if( db_errno() == 0 ) return ajax_echo('done');
			else return ajax_echo('error');
			break;
		case 'reopen':
			$sql = "UPDATE `stoken` SET `on` = '1' WHERE `uid` = '" . intval(uid()) .  "' LIMIT 1";
			run_sql( $sql );
			if( db_errno() == 0 ) return ajax_echo('done');
			else return ajax_echo('error');
			break;
		
		default:
			$data['tinfo'] = get_line( "SELECT * FROM `stoken` WHERE `uid` = '" . intval(uid()) . "' LIMIT 1" );
	render( $data , 'ajax' , 'plugin' , 'simple_token' ); 

	}
}


// 添加API hook，完成业务逻辑
add_filter('API_LOGIN_ACTION_FILTER', 'stoken_api_login');
function stoken_api_login($data)
{
	$stoken = z(t(v('stoken')));
	if( (!in_array( g('a') , $data )) && (strlen($stoken) > 0) )
	{
		if( $uid = get_var("SELECT `uid` FROM `stoken` WHERE `token` = '" . s($stoken) . "' AND `on` = '1' LIMIT 1") )
		{
			$user = get_user_info_by_id( $uid );
			if( $user['level'] < 1 || $user['is_closed'] == 1 )
				return apiController::send_error( LR_API_USER_CLOSED , 'USER CLOSED BY ADMIN' );
			session_set_cookie_params( c('session_time') );
			@session_start();
            $token = session_id();
            // $_SESSION[ 'token' ] = $stoken; <- 加上这行stoken可以变成token
            $_SESSION[ 'uid' ] = $user[ 'id' ];
            $_SESSION[ 'uname' ] = $user['name'];
            $_SESSION[ 'email' ] = $user[ 'email' ];
			$_SESSION[ 'level' ] = $user['level'];
			$data[] = g('a');
		} 
	}

	return $data;
}

add_filter( 'API_'.g('a').'_OUTPUT_FILTER' , 'stoken_jsonp' );
function stoken_jsonp( $data )
{
	$jsonp = z(t(v('jsonp')));
	if( strlen($jsonp) > 0 )
	{
		$str = $jsonp . '(' . json_encode( $data ) . ');';
		ajax_echo( $str );
		exit;
	}
	return $data;
}
