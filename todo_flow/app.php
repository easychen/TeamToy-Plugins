<?php
/*** 
TeamToy extenstion info block
##name TODO Flow
##folder_name todo_flow
##author Easy
##email Easychen@qq.com
##reversion 1
##desp TODO Flow 以瀑布流的形式显示全体成员的最近TODO。 
##update_url http://tt2net.sinaapp.com/?c=plugin&a=update_package&name=todo_flow 
##reverison_url http://tt2net.sinaapp.com/?c=plugin&a=latest_reversion&name=todo_flow 
***/
// todo flow
// a flow view of all todos
if( !defined('IN') ) die('bad request');

$plugin_lang = array();
$plugin_lang['zh_cn'] = array
(
	'PL_TODO_FLOW_TITLE' => '团队TODO一览',
	'PL_TODO_FLOW_TODO_TIME' => '最后活动时间 - %s',
	'PL_TODO_FLOW_NO_TODO_NOW' => '暂无TODO',
	'PL_TODO_FLOW_TEST' => ''
);

$plugin_lang['zh_tw'] = array
(
'PL_TODO_FLOW_TITLE' => '團隊TODO一覽',
'PL_TODO_FLOW_TODO_TIME' => '最後活動時間- %s',
'PL_TODO_FLOW_NO_TODO_NOW' => '暫無TODO',
'PL_TODO_FLOW_TEST' => ''
);

$plugin_lang['us_en'] = array
(
	'PL_TODO_FLOW_TITLE' => 'TODO Flow',
	'PL_TODO_FLOW_TODO_TIME' => 'Last active at %s',
	'PL_TODO_FLOW_NO_TODO_NOW' => 'No TODO',
	'PL_TODO_FLOW_TEST' => ''
);


plugin_append_lang( $plugin_lang );

add_action( 'UI_NAVLIST_LAST' , 'todo_flow_icon' );
function todo_flow_icon()
{
	?><li <?php if( g('c') == 'plugin' && g('a') == 'todo_flow' ): ?>class="active"<?php endif; ?>><a href="?c=plugin&a=todo_flow" title="<?=__('PL_TODO_FLOW_TITLE')?>" >
	<div><img src="plugin/todo_flow/appicon.png"/></div></a>
	</li>
	<?php
}

add_action( 'PLUGIN_TODO_FLOW' , 'todo_flow_view' );
function todo_flow_view()
{
	$data['top'] = $data['top_title'] = __('PL_TODO_FLOW_TITLE');
	$data['uids'] = get_data("SELECT `id` FROM `user` WHERE `is_closed` = 0 AND `level` > 0 ");
	$data['js'][] = 'jquery.masonry.min.js';
	return render( $data , 'web' , 'plugin' , 'todo_flow' );
}

add_action( 'PLUGIN_TODO_FLOW_ITEM' , 'todo_flow_item' );
function todo_flow_item()
{
	$uid = intval(z(v(t('uid'))));
	if( $uid < 0 ) return ajax_echo('BAD UID');

	$params = array();
	$params['uid'] = $uid;
	$params['ord'] = 'desc';
	$params['by'] = 'last_action_at';
	$params['count'] = '20';
	
	if($content = send_request( 'todo_list' ,  $params , token()  ))
	{
			$data = json_decode($content , 1);
			$data['user'] = get_user_info_by_id( $uid );

			if( isset($data['data']) )
			foreach( $data['data'] as $k => $v )
			{
				if( $v['is_follow'] == 1 ) unset( $data['data'][$k] );
			}
			return render( $data , 'ajax' , 'plugin' , 'todo_flow' );
	}
}

