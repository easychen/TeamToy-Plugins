<?php
//liqiping@baixing.net
//
//注：由于SAE的限制，在新浪云上这个插件不能运行

/***
TeamToy extenstion info block
##name OpenIdAuth
##folder_name openid_auth
##author QipingLi
##email liqiping@baixing.net
##reversion 1
##desp 使用OpenId用于用户的登录(由于SAE的限制，在新浪云上这个插件不能运行)
***/
if( !defined('IN') ) die('bad request');

$plugin_lang = array();
$plugin_lang['zh_cn'] = array
(
	'PL_OPENID_AUTH_SETTINGS_TITLE' => 'OpenId认证设置',
	'PL_OPENID_AUTH_EMAIL_PATTEN' => '接受的Email(正则表达式)',
	'PL_OPENID_AUTH_SETTINGS_UPDATED' => '设置已保存',
	'PL_OPENID_AUTH_LINK' => '使用OpenId登录',
);

$plugin_lang['zh_tw'] = array
(
	'PL_OPENID_AUTH_SETTINGS_TITLE' => 'OpenId認證設置',
	'PL_OPENID_AUTH_EMAIL_PATTEN' => '接受的Email(正則表達式)',
	'PL_OPENID_AUTH_SETTINGS_UPDATED' => '設置已保存',
	'PL_OPENID_AUTH_LINK' => '使用OpenId登錄',
);

$plugin_lang['us_en'] = array
(
	'PL_OPENID_AUTH_SETTINGS_TITLE' => 'OpenId Auth Settings',
	'PL_OPENID_AUTH_EMAIL_PATTEN' => 'Accept Email Pattern(regular expression)',
	'PL_OPENID_AUTH_SETTINGS_UPDATED' => 'Settings updated',
	'PL_OPENID_AUTH_LINK' => 'Login using OpenId',
);

plugin_append_lang( $plugin_lang );

define ('OPENID_LIBS', dirname ( __FILE__ ) . DIRECTORY_SEPARATOR . 'lib');
require_once(OPENID_LIBS . '/OpenId.php');
require_once(AROOT . 'model' . DS . 'api.function.php');

// 添加OPENID配置菜单
add_action( 'UI_USERMENU_ADMIN_LAST', 'openid_auth_menu_list');
function openid_auth_menu_list()
{
	?><li><a href="javascript:show_float_box( '<?=__('PL_OPENID_AUTH_SETTINGS_TITLE')?>' , '?c=plugin&a=OPENID_AUTH' );void(0);"><?=__('PL_OPENID_AUTH_SETTINGS_TITLE')?></a></li>
<?php
}

add_action('PLUGIN_OPENID_AUTH', 'plugin_openid_auth');
function  plugin_openid_auth()
{
	$data['openid_auth_email_pattern'] = kget('openid_auth_email_pattern');
	render($data, 'ajax', 'plugin', 'openid_auth');
}

add_action('PLUGIN_OPENID_AUTH_SAVE', 'plugin_openid_auth_save');
function  plugin_openid_auth_save()
{
	$openid_auth_email_pattern = z(t(v('openid_auth_email_pattern')));
	kset('openid_auth_email_pattern', $openid_auth_email_pattern);

	ajax_echo( __('PL_OPENID_AUTH_SETTINGS_UPDATED').'<script>setTimeout( close_float_box, 500)</script>');
}

// 添加使用OpenId登录链接
add_action('CTRL_SESSION_STARTED', 'openid_auth_link');
function openid_auth_link()
{
	//只在登录界面使用
	if (g('c') == 'guest' && g('a') == 'index') {
			?>
			<script>
				window.onload = function() {
					$('button.login').parent().after('<a href="?c=plugin&a=openid_auth_do"><?= __('PL_OPENID_AUTH_LINK')?></a>');
				};
			</script>
	<?
	}
}

// OpenId不需要check_login
add_filter('CTRL_PLUGIN_LOGIN_FILTER', 'openid_not_check');

function openid_not_check() {
	return array('openid_auth_do');
}


add_action('PLUGIN_OPENID_AUTH_DO', 'openid_auth_do');

function openid_auth_do() {
	if(!is_login()) {
		$userEmail = OpenId::getUserEmail();
		$userName = $userEmail['userName'];
		$email = $userEmail['email'];

		$genPassword = hash('md5', 'XFAGAGArere' . $email);

		if (!(is_email_accepted($email))) die('该邮箱不能登录本系统！');
		if(try_login($email, $genPassword)) {
			//如果用户已经被注册过，直接登录
			forward('?c=dashboard');
		} else {
			//否则，先注册用户再登录
			register($email, $userName, $genPassword);
			try_login($email, $genPassword);
			forward('?c=dashboard');
		}
	}
}

function is_email_accepted($email) {
	$pattern = '#' . kget('openid_auth_email_pattern') . '#';
	return preg_match($pattern, $email);
}

function try_login($email, $password) {
	$user = get_full_info_by_email_password($email, $password);
	if($user) {
		session_set_cookie_params(c('session_time'));
		@session_start();
		$token = session_id();
		$_SESSION['token'] = $token;
		$_SESSION['uid'] = $user[ 'id' ];
		$_SESSION['uname'] = $user['name'];
		$_SESSION['email'] = $user['email'];
		$_SESSION['level'] = $user['level'];
		if(strlen( $user['groups']) > 0) {
			$user['groups'] = explode('|', trim($user['groups'] , '|')) ;
			$_SESSION['groups'] = $user['groups'];
		}
		return true;
	} else {
		return false;
	}
}

function register($email, $userName, $passwd) {
	$dsql = array();

	$dsql[] = "'" . s($userName) . "'";
	$dsql[] = "'" . s(pinyin(strtolower($userName))) . "'";
	$dsql[] = "'" . s($email) . "'";
	$dsql[] = "'" . s(md5($passwd)) . "'";
	$dsql[] = "'" . s(date("Y-m-d H:i:s")) . "'";

	$sql = "REPLACE INTO `user` ( `name` , `pinyin` , `email` , `password` , `timeline` ) VALUES ( " . join( ' , ' , $dsql ) . " )";

	run_sql($sql);

	if(db_errno() != 0) {
		die('DATABASE_ERROR' . db_error());
	}
}
