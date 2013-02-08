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

// 添加顶部导航按钮
add_action( 'UI_USERMENU_BOTTOM' , 'stoken_user_menu' );
function stoken_user_menu()
{
	?><li><a href="javascript:show_float_box( 'SimpleToken' , '?c=plugin&a=simple_token' );void(0);">通过Token访问数据</a></li>
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
