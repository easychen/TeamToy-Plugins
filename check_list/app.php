<?php
/*** 
TeamToy extenstion info block  
##name Checklist 
##folder_name check_list
##author Easy
##email Easychen@qq.com
##reversion 1
##desp 为TODO添加checklist，用于必要步骤和流程的检查。 
##update_url http://tt2net.sinaapp.com/?c=plugin&a=update_package&name=check_list 
##reverison_url http://tt2net.sinaapp.com/?c=plugin&a=latest_reversion&name=check_list 
***/

if( !defined('IN') ) die('bad request');

if( !mysql_query("SHOW COLUMNS FROM `checklist_tpl`",db()) )
{

	$sql = "CREATE TABLE IF NOT EXISTS `checklist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `content` varchar(255) NOT NULL,
  `timeline` datetime NOT NULL,
  `uid` int(11) NOT NULL,
  `is_done` tinyint(1) NOT NULL DEFAULT '0',
  `sub_tid` int(11) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8";

	run_sql( $sql );

	$sql = "CREATE TABLE IF NOT EXISTS  `checklist_tpl` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` VARCHAR( 255 ) NOT NULL ,
`content` TEXT NOT NULL ,
`uid` INT NOT NULL DEFAULT  '0',
`version` INT NOT NULL DEFAULT  '1',
INDEX (  `uid` )
) ENGINE = MYISAM  DEFAULT CHARSET=utf8";

	run_sql( $sql );
}

$plugin_lang = array();

$plugin_lang['zh_cn'] = array
(

	'PL_CHECK_LIST_EDIT' => '编辑CheckList',
	'PL_CHECK_LIST_TEMPLATE' => '模板',
	'PL_CHECK_LIST_ADD_ITEM' => '追加检查项',
	'PL_CHECK_LIST_TEMPLATE_REMOVE' => '删除',
	'PL_CHECK_LIST_TEMPLATE_CREATE' => '创建',
	'PL_CHECK_LIST_TEMPLATE_UPDATE' => '保存修改',
	'PL_CHECK_LIST_TEMPLATE_APPLY' => '追加到TODO',
	'JS_PL_CHECK_LIST_ITEM_REMOVE_CONFIRM' => '检查项移除后将不可恢复，继续？',
	'JS_PL_CHECK_LIST_MARK_TODO_READ_CONFIRM' => '你已经完成了TODO的全部CheckList，要将TODO标记为完成么？',
	'JS_PL_CHECK_LIST_TEMPLATE_REMOVE_CONFIRM' => '确定要删除CheckList模板【%s(%s)】吗？',
	'JS_PL_CHECK_LIST_TEMPLATE' => '编辑CheckList模板',
	'JS_PL_CHECK_LIST_TEMPLATE_UPDATED' => '模板内容已保存',
	'JS_PL_CHECK_LIST_TEMPLATE_NAME' => '请输入模板名称',
	'JS_PL_CHECK_LIST_TEMPLATE_INTRO' => '填写CheckList检查项，每行一条',
	'JS_PL_CHECK_LIST_TID_ERROR' => '错误的TID',
	'PL_CHECK_LIST_TEST' => ''
);

$plugin_lang['zh_tw'] = array
(

	'PL_CHECK_LIST_EDIT' => '編輯CheckList',
	'PL_CHECK_LIST_TEMPLATE' => '模板',
	'PL_CHECK_LIST_ADD_ITEM' => '追加檢查項',	
	'PL_CHECK_LIST_TEMPLATE_REMOVE' => '刪除',
	'PL_CHECK_LIST_TEMPLATE_CREATE' => '創建',
	'PL_CHECK_LIST_TEMPLATE_UPDATE' => '保存修改',
	'PL_CHECK_LIST_TEMPLATE_APPLY' => '追加到TODO',
	'JS_PL_CHECK_LIST_ITEM_REMOVE_CONFIRM' => '檢查項移除後將不可恢復，繼續？ ',
	'JS_PL_CHECK_LIST_MARK_TODO_READ_CONFIRM' => '你已經完成了TODO的全部CheckList，要將TODO標記為完成么？ ',
	'JS_PL_CHECK_LIST_TEMPLATE_REMOVE_CONFIRM' => '確定要刪除CheckList模板【%s(%s)】嗎？ ',
	'JS_PL_CHECK_LIST_TEMPLATE' => '編輯CheckList模板',
	'JS_PL_CHECK_LIST_TEMPLATE_UPDATED' => '模板內容已保存',
	'JS_PL_CHECK_LIST_TEMPLATE_NAME' => '請輸入模板名稱',
	'JS_PL_CHECK_LIST_TEMPLATE_INTRO' => '填寫CheckList檢查項，每行一條',
	'JS_PL_CHECK_LIST_TID_ERROR' => '錯誤的TID',
	'PL_CHECK_LIST_TEST' => ''
);

$plugin_lang['us_en'] = array
(

	'PL_CHECK_LIST_EDIT' => 'Edit CheckList',
	'PL_CHECK_LIST_TEMPLATE' => 'Teamplate',
	'PL_CHECK_LIST_ADD_ITEM' => 'Append items',	
	'PL_CHECK_LIST_TEMPLATE_REMOVE' => 'Remove',
	'PL_CHECK_LIST_TEMPLATE_CREATE' => 'Create',
	'PL_CHECK_LIST_TEMPLATE_UPDATE' => 'Save',
	'PL_CHECK_LIST_TEMPLATE_APPLY' => 'Apply to TODO',
	'JS_PL_CHECK_LIST_ITEM_REMOVE_CONFIRM' => 'Item will be remove, continue? ',
	'JS_PL_CHECK_LIST_MARK_TODO_READ_CONFIRM' => 'All check items finished, mark TODO as done?',
	'JS_PL_CHECK_LIST_TEMPLATE_REMOVE_CONFIRM' => 'Remove CheckList template[%s(%s)]?',
	'JS_PL_CHECK_LIST_TEMPLATE' => 'Edite Template',
	'JS_PL_CHECK_LIST_TEMPLATE_UPDATED' => 'Template updated',
	'JS_PL_CHECK_LIST_TEMPLATE_NAME' => 'Input template name',
	'JS_PL_CHECK_LIST_TEMPLATE_INTRO' => 'Add check items here,one line each',
	'JS_PL_CHECK_LIST_TID_ERROR' => 'Bad TID',
	'PL_CHECK_LIST_TEST' => ''
);

plugin_append_lang( $plugin_lang );

add_action( 'UI_TODO_DETAIL_COMMENTBOX_BEFORE' , 'check_list_area' );
function check_list_area( $data )
{
	echo render_html( $data , dirname(__FILE__) . DS .'view' . DS . 'check_list_area.tpl.html' );
}

add_action( 'UI_HEAD' , 'check_list_js' );
function check_list_js()
{
	echo '<script type="text/javascript" src="plugin/check_list/jquery-sortable-min.js"></script>';
	echo '<script type="text/javascript" src="plugin/check_list/app.js"></script>';
	echo '<link href="plugin/check_list/app.css" rel="stylesheet">';

}

add_action( 'PLUGIN_CHECK_LIST_TPL_SHOW' , 'plugin_check_list_tpl_show' );
function plugin_check_list_tpl_show()
{
	$data['tpls'] = get_data("SELECT * FROM `checklist_tpl` WHERE `uid` = 0 OR `uid` = '" . intval( uid() ) . "' ");
	return render( $data , 'ajax' , 'plugin' , 'check_list' ); 
}

add_action(  'PLUGIN_CHECK_LIST_TPL_APPLY' , 'plugin_check_list_tpl_apply');
function plugin_check_list_tpl_apply()
{
	$tid = intval(v('tid'));
	if( $tid < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

	$todoid = intval(v('todoid'));
	if( $tid < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );


	$sql = "SELECT `content` FROM `checklist_tpl` WHERE `id` = '" . intval( $tid ) . "' LIMIT 1";
	if( $content = get_var( $sql ) )
	{
		$lines = explode( "\n" , trim( $content ) );
		if( is_array( $lines ) )
		{
			$sql = "INSERT INTO `checklist` ( `tid` , `title` , `content` , `timeline` , `uid` ) VALUES  ";
			foreach( $lines as $line )
			{
				$line = trim($line);
				$isql[] = "( '" 
		. intval($todoid) . "' , '" . s($line) . "' , '" . s($line) . "'  , NOW() , '" . intval(uid()) . "' )";
			}

			if( isset( $isql ) ) $sql = $sql . join( ' , ' , $isql );
			run_sql( $sql );
			
		}
		return render( array( 'code' => 0 , 'data' =>  array( 'tid' => $tid , 'content' => $content ) ) , 'rest' );
	}
	else
		return render( array( 'code' => 100002 , 'message' => 'can not read data' ) , 'rest' );
}



add_action(  'PLUGIN_CHECK_LIST_TPL_REMOVE' , 'plugin_check_list_tpl_remove');
function plugin_check_list_tpl_remove()
{
	if( !is_admin() ) return render( array( 'code' => 100002 , 'message' => 'ONLY ADMIN CAN DO THIS' ) , 'rest' );

	$tid = intval(v('tid'));
	if( $tid < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

	$sql = "DELETE FROM `checklist_tpl` WHERE `id` = '" . intval( $tid ) . "' LIMIT 1";
	run_sql($sql);

	if( db_errno() == 0 )
		return render( array( 'code' => 0 , 'data' =>  array( 'tid' => $tid ) ) , 'rest' );
	else
		return render( array( 'code' => 100002 , 'message' => 'can not read data' ) , 'rest' );
}



add_action(  'PLUGIN_CHECK_LIST_TPL_UPDATE' , 'plugin_check_list_tpl_update');
function plugin_check_list_tpl_update()
{
	if( !is_admin() ) return render( array( 'code' => 100002 , 'message' => 'ONLY ADMIN CAN DO THIS' ) , 'rest' );

	$tid = intval(v('tid'));
	if( $tid < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

	$content = z(t(v('content')));

	$sql = "UPDATE `checklist_tpl` SET `content` = '" . s($content) . "' WHERE `id` = '" . intval( $tid ) . "' LIMIT 1";
	run_sql($sql);
	if( db_errno() == 0 )
		return render( array( 'code' => 0 , 'data' =>  array( 'tid' => $tid , 'content'=>$content) ) , 'rest' );
	else
		return render( array( 'code' => 100002 , 'message' => 'can not read data' ) , 'rest' );

}

add_action(  'PLUGIN_CHECK_LIST_TPL_CREATE' , 'plugin_check_list_tpl_create');
function plugin_check_list_tpl_create()
{
	if( !is_admin() ) return render( array( 'code' => 100002 , 'message' => 'ONLY ADMIN CAN DO THIS' ) , 'rest' );

	$title = z(t(v('title')));
	if( strlen( $title ) < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

	$content = z(t(v('content')));
	
	$sql = "INSERT INTO `checklist_tpl` ( `title` , `content` ) VALUES ( '" . s( $title ) . "' , '" . s($content) . "' ) ";
	run_sql($sql);
	
	if( db_errno() == 0 )
		return render( array( 'code' => 0 , 'data' =>  array( 'tid' => last_id() , 'title' => $title , 'content'=>$content) ) , 'rest' );
	else
		return render( array( 'code' => 100002 , 'message' => 'can not read data' ) , 'rest' );
}

add_action( 'PLUGIN_CHECK_LIST_TPL_DETAIL' , 'plugin_check_list_tpl_detail' );
function plugin_check_list_tpl_detail()
{
	$tid = intval(v('tid'));
	if( $tid < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

	$sql = "SELECT `content` FROM `checklist_tpl` WHERE `id` = '" . intval( $tid ) . "' AND ( `uid` = 0 OR `uid` = '" . intval( uid() ) . "') LIMIT 1";
	$content = get_var( $sql );
	if( db_errno() == 0 )
		return render( array( 'code' => 0 , 'data' =>  array('content'=>$content) ) , 'rest' );
	else
		return render( array( 'code' => 100002 , 'message' => 'can not read data' ) , 'rest' );
}


add_action( 'PLUGIN_CHECKLIST_ADD' , 'plugin_checklist_add' );
function plugin_checklist_add()
{
	$text = z(t(v('text')));
	if( strlen( $text ) < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

	$tid = intval(v('tid'));
	if( $tid < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

	$params = array();
	$params['text'] = $text;
	$params['tid'] = $tid;

	if($content = send_request( 'checklist_add' ,  $params , token()  ))
	{
		$data = json_decode($content , 1);
		if( $data['err_code'] == 0 )
		{
			return render( array( 'code' => 0 , 'data' =>  
				array( 'html' => render_html( array( 'item' => $data['data'] ) , dirname(__FILE__) . DS .'view' . DS . 'check_list_item.tpl.html'  ) ) ) , 'rest' );
		}
		else
			return render( array( 'code' => 100002 , 'message' => 'can not save data' ) , 'rest' );
		//return render( array( 'code' => 0 , 'data' => $data['data'] ) , 'rest' );
	}

	return render( array( 'code' => 100001 , 'message' => 'can not get api content' ) , 'rest' );
}

add_action( 'PLUGIN_CHECKLIST_REORDER' , 'plugin_checklist_reorder' );
function plugin_checklist_reorder()
{
	$ord = z(t(v('ord')));
	if( strlen( $ord ) < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

	$tid = intval(v('tid'));
	if( $tid < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

	$params = array();
	$params['ord'] = $ord;
	$params['tid'] = $tid;

	if($content = send_request( 'checklist_reorder' ,  $params , token()  ))
	{
		$data = json_decode($content , 1);
		if( $data['err_code'] == 0 )
		{
			return render( array( 'code' => 0 , 'data' =>  $data['data'] ) , 'rest' );
		}
		else
			return render( array( 'code' => 100002 , 'message' => 'can not save data' ) , 'rest' );
		//return render( array( 'code' => 0 , 'data' => $data['data'] ) , 'rest' );
	}

	return render( array( 'code' => 100001 , 'message' => 'can not get api content' ) , 'rest' );
}

add_action( 'PLUGIN_CHECKLIST_DONE' , 'plugin_checklist_done' );
function plugin_checklist_done()
{
	return ck_request( 'done' );
}

add_action( 'PLUGIN_CHECKLIST_REOPEN' , 'plugin_checklist_reopen' );
function plugin_checklist_reopen()
{
	return ck_request( 'reopen' );
}

add_action( 'PLUGIN_CHECKLIST_REMOVE' , 'plugin_checklist_remove' );
function plugin_checklist_remove()
{
	return ck_request( 'remove' );
}

function ck_request( $type = 'done' )
{
	$ckid = intval(v('ckid'));
	if( $ckid < 1 ) return render( array( 'code' => 100002 , 'message' => 'bad args' ) , 'rest' );

	$params = array();
	$params['ckid'] = $ckid;

	if( $type == 'done' ) $action = 'checklist_done';
	if( $type == 'remove' ) $action = 'checklist_remove';
	if( $type == 'reopen' ) $action = 'checklist_reopen';

	if($content = send_request( $action ,  $params , token()  ))
	{
		$data = json_decode($content , 1);
		if( $data['err_code'] == 0 )
		{
			return render( array( 'code' => 0 , 'data' =>  $data['data'] ) , 'rest' );
		}
		else
			return render( array( 'code' => 100002 , 'message' => 'can not save data' ) , 'rest' );
		//return render( array( 'code' => 0 , 'data' => $data['data'] ) , 'rest' );
	}

	return render( array( 'code' => 100001 , 'message' => 'can not get api content' ) , 'rest' );
}


add_action( 'API_CHECKLIST_REORDER' , 'api_checklist_reorder' );
function api_checklist_reorder()
{
	$ordstring = z(t(v('ord')));
	$ords_array = explode( '|' , $ordstring );
	if( !is_array($ords_array) ) return apiController::send_error(  LR_API_ARGS_ERROR , 'ORDSTRING CAN\'T EMPTY' );
		
	$tid = intval(v('tid'));
	if( intval( $tid ) < 1 ) return apiController::send_error( LR_API_ARGS_ERROR , 'TID NOT EXISTS' );

	$i = 1;
	foreach( $ords_array as $item )
	{
		$sql = "UPDATE `checklist` SET `order` = '" . intval( $i ) . "' WHERE `tid` = '" . intval( $tid ) . "' AND `id` = '" . intval( $item ) . "' LIMIT 1 ";
		run_sql( $sql );
		$i++;
	}

	if( db_errno() != 0 )
		return apiController::send_error( LR_API_DB_ERROR , 'DATABASE ERROR ' . mysql_error() );
	else
		return apiController::send_result( array('msg'=>'ok') );


}


add_action( 'API_CHECKLIST_DONE' , 'api_checklist_done' );
function api_checklist_done()
{
	return ck_set( 'done');
}

add_action( 'API_CHECKLIST_REOPEN' , 'api_checklist_reopen' );
function api_checklist_reopen()
{
	return ck_set( 'reopen');
}

add_action( 'API_CHECKLIST_REMOVE' , 'api_checklist_remove' );
function api_checklist_remove()
{
	return ck_set( 'remove');
}

function ck_set( $type = 'done' )
{
	$ckid = intval(v('ckid'));
	if( intval( $ckid ) < 1 ) return apiController::send_error( LR_API_ARGS_ERROR , 'CKID NOT EXISTS' );

	// check user
	$ckinfo = get_line( "SELECT * FROM `checklist` WHERE `id` = '" . intval( $ckid ) . "' LIMIT 1" );
	if( uid() != $ckinfo['uid'] )
		return apiController::send_error( LR_API_FORBIDDEN , 'ONLY OWNER CAN REMOVE ' );

	if( $type == 'done' )
		$sql = "UPDATE `checklist` SET `is_done` = 1 WHERE `uid` = '" . intval(uid()) . "' AND `id` = '" . intval( $ckid ) . "' LIMIT 1";
	
	if( $type == 'reopen' )
		$sql = "UPDATE `checklist` SET `is_done` = 0 WHERE `uid` = '" . intval(uid()) . "' AND `id` = '" . intval( $ckid ) . "' LIMIT 1";
	
	if( $type == 'remove' )
		$sql = "DELETE FROM `checklist` WHERE `uid` = '" . intval(uid()) . "' AND `id` = '" . intval( $ckid ) . "' LIMIT 1";
	
//$sql = "DELETE FROM `checklist` WHERE `uid` = '" . intval(uid()) . "' AND `id` = '" . intval( $ckid ) . "' LIMIT 1";
	run_sql( $sql );

	if( db_errno() != 0 )
		return apiController::send_error( LR_API_DB_ERROR , 'DATABASE ERROR ' . mysql_error() );
	else
		return apiController::send_result( $ckinfo ); 
}






add_action( 'API_CHECKLIST_ADD' , 'api_checklist_add' );
function api_checklist_add()
{
	$content = z(t(v('text')));
	if( !not_empty($content) ) return apiController::send_error(  LR_API_ARGS_ERROR , 'TEXT CAN\'T EMPTY' );
		
	$tid = intval(v('tid'));
	if( intval( $tid ) < 1 ) return apiController::send_error( LR_API_ARGS_ERROR , 'TID NOT EXISTS' );

	// check user
	$tinfo = get_todo_info_by_id( $tid );
	if( (intval($tinfo['details']['is_public']) == 0) && (uid() != $tinfo['owner_uid']) )
		return apiController::send_error( LR_API_FORBIDDEN , 'ONLY PUBLIC TODO CAN ADD CHECKLIST BY OTHERS' );

	$sql = "INSERT INTO `checklist` ( `tid` , `title` , `content` , `timeline` , `uid` ) VALUES ( '" 
		. intval($tid) . "' , '" . s($content) . "' , '" . s($content) . "'  , NOW() , '" . intval(uid()) . "' ) ";

	run_sql( $sql );

	if( db_errno() != 0 )
		return apiController::send_error( LR_API_DB_ERROR , 'DATABASE ERROR ' . mysql_error() );
	else
		return apiController::send_result( get_line("SELECT * FROM `checklist` WHERE `id` = '" . intval(last_id()) . "' LIMIT 1" , db()) ); 

}

// 为user profile 接口追加css数据
add_filter( 'API_TODO_DETAIL_OUTPUT_FILTER' , 'api_check_list_detail'  );
function api_check_list_detail( $data )
{
	$tid = intval($data['tid']);
	$data['checklists'] = get_data("SELECT * FROM `checklist` WHERE `tid` = '" . intval($tid) . "' ORDER BY `order` DESC , `id` ASC LIMIT 100");
	return $data;
}

add_filter( 'API_TODO_ASSIGN_OUTPUT_FILTER' , 'api_check_list_assign'   ); // todo_assign
function api_check_list_assign( $data )
{
	$sql = "UPDATE `checklist` SET `uid` = '" . intval( $data['owner_uid'] ) . "' WHERE `tid` = '" . intval($data['tid']) . "' LIMIT 100";
	run_sql( $sql );
	return $data;
}




