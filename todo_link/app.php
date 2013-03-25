<?php
/*** 
TeamToy extenstion info block
##name TODO Link
##folder_name todo_link
##author Easy
##email Easychen@qq.com
##reversion 1
##desp 将TODO标题中的链接显示到TODO下方 
##update_url http://tt2net.sinaapp.com/?c=plugin&a=update_package&name=todo_link
##reverison_url http://tt2net.sinaapp.com/?c=plugin&a=latest_reversion&name=todo_link
***/
if( !defined('IN') ) die('bad request');


add_action( 'UI_TODO_DETAIL_TITLE_AFTER' , 'plugin_todo_link_show' );
add_action( 'UI_TODO_DETAIL_CENTER_TITLE_AFTER' , 'plugin_todo_link_show' );
add_action( 'UI_FEED_DETAIL_TITLE_AFTER' , 'plugin_todo_link_show' );
function plugin_todo_link_show( $data )
{
	if( $links = find_links( $data['content'] ) )
	{
		?>
		<div class="todo_elink">
		<?php foreach( $links as $link ): ?>
		<a href="<?=t($link)?>" target="_blank"><?=t($link)?>&nbsp;<i class="icon-external-link"></i></a>
		<?php endforeach; ?>
		</div>
		<?php
	}
}

add_action( 'UI_HEAD' ,'plugin_todo_link_css');
function plugin_todo_link_css()
{
	?>
	<style type="text/css">
	.todo_elink
	{
		white-space: wrap;
		word-wrap: break-word;
		word-break: break-all;
	}
	</style>
	<?php
}