function check_list_add()
{
	$('#check_list_add_box').show();
	$('#edit_links').hide();
}

function checklist_reorder( tid , ordstring )
{
	var url = '?c=plugin&a=checklist_reorder' ;
	var params = { 'tid' : tid  , 'ord' : ordstring };
	$.post( url , params , function( data )
	{
		// console.log( data );
		var data_obj = $.parseJSON( data );
		 
		if( data_obj.err_code == 0 )
		{
			
		}
		else
		{
			alert('API调用错误，请稍后再试。错误号'+data_obj.err_code + ' 错误信息 ' + data_obj.message);
		}

		done();
		

	} );

	doing();
}

function check_list_save( tid , text )
{

	var url = '?c=plugin&a=checklist_add' ;
	var params = { 'tid' : tid  , 'text' : text };
	$.post( url , params , function( data )
	{
		var data_obj = $.parseJSON( data );
		 
		if( data_obj.err_code == 0 )
		{
			$('#checklist_list').removeClass('empty');
			$('#checklist_list').append( $(data_obj.data.html) );
			bind_checklist();
			count_checklist();
			$('#checklist_text').val('');
			$('#checklist_text').focus();

		}
		else
		{
			alert('API调用错误，请稍后再试。错误号'+data_obj.err_code + ' 错误信息 ' + data_obj.message);
		}

		done();
		

	} );

	doing();

	
}

function count_checklist()
{
	$('#check_count').text( '['+ $('#checklist_list li.checked').length + '/' + $('#checklist_list li').length +']' );
}

function bind_checklist()
{
	$('#checklist_list a.close').unbind('click');
	$('#checklist_list a.close').bind('click',function()
	{
		if( confirm('检查项移除后将不可恢复，继续？') )
		{
			var url = '?c=plugin&a=checklist_remove' ;
			var ckid = $(this).attr('ckid');
			var params = { 'ckid' : ckid  };
			$.post( url , params , function( data )
			{
				var data_obj = $.parseJSON( data );
			 
				if( data_obj.err_code == 0 )
					$('#checklist_list li[ckid='+ckid+']').remove();
				else
					alert('API调用错误，请稍后再试。错误号'+data_obj.err_code + ' 错误信息 ' + data_obj.message);
				
				count_checklist();
				done();
			});
			doing();
		}
	});

	$('#checklist_list a.ing').unbind('click');
	$('#checklist_list a.ing').bind('click',function()
	{
		var ckid = $(this).attr('ckid');
		if( parseInt($.cookie( 'ckitem-'+ckid )) == 1 )
		{
			$.cookie( 'ckitem-'+ckid , 0 );
			$('#checklist_list li[ckid='+ckid+']').removeClass('doing');
		}
		else
		{
			$.cookie( 'ckitem-'+ckid , 1 );
			$('#checklist_list li[ckid='+ckid+']').addClass('doing');
		}
	});



	$('#checklist_list input[type=checkbox]').unbind('change');
	$('#checklist_list input[type=checkbox]').bind('change',function()
	{
		var action = '';
		if( $(this).is(':checked') ) action = 'done' ;
		else action = 'reopen';

		var url = '?c=plugin&a=checklist_'+action ;
			var ckid = $(this).attr('ckid');
			var params = { 'ckid' : ckid  };
			$.post( url , params , function( data )
			{
				var data_obj = $.parseJSON( data );
			 
				if( data_obj.err_code == 0 )
				{
					if( action == 'done' )
					{
						$('#checklist_list li[ckid='+ckid+']').removeClass('doing');
						$.cookie( 'ckitem-'+ckid , 0 );
						$('#checklist_list li[ckid='+ ckid +']').addClass('checked');
						if( $('#checklist_list li').length == $('#checklist_list li.checked').length )
						{
							if( confirm('你已经完成了TODO的全部CheckList，要将TODO标记为完成么？') )
							{
								mark_todo_done( parseInt( $('#checklist_list').attr('tid') ) );
							}
						}	
					} 
					else $('#checklist_list li[ckid='+ ckid +']').removeClass('checked');
				}
				else
					alert('API调用错误，请稍后再试。错误号'+data_obj.err_code + ' 错误信息 ' + data_obj.message);
				
				count_checklist();
				done();
			});
			doing();
	});

	$('#checklist_list li').each(function()
	{
		if( parseInt($.cookie('ckitem-'+$(this).attr('ckid'))) == 1 ) $(this).addClass('doing');
	});
}

function ck_edit_toggle()
{
	if( $('#ck_edit_icon').hasClass('edit') )
	{
		$('#ck_edit_icon').removeClass('edit');
		$('#checklist_list').removeClass('edit');
	}
	else
	{
		$('#ck_edit_icon').addClass('edit');
		$('#checklist_list').addClass('edit');
		cklist_sortable();
	}
}

function cklist_sortable()
{
	$('#checklist_list').sortable
	({
		group:'cklist',
	  	handle: 'a.move',
	  	itemSelector:'li',
	  	onDrop:function( item, targetContainer, _super )
	  	{
	  		var neword = [];
	  		$('#checklist_list li').each(function()
	  		{
	  			neword.unshift( $(this).attr('ckid') );
	  		});
	  		if( neword.length > 1 )
	  			checklist_reorder( parseInt($('#checklist_list').attr('tid')) , neword.join('|') );

	  		_super(item);  		
	  	}
	});
}

function check_list_tpl_show( todoid )
{
	show_float_box( 'CheckList模板' , '?c=plugin&a=check_list_tpl_show&todoid='+todoid);
}

function check_list_tpl_apply( todoid )
{	
	var tid = parseInt( $('#tpl_select option:selected').attr('value') );
	if( tid  < 1 || isNaN(tid) )
	{
		alert('tid错误');
		return false;
	} 


	var url = '?c=plugin&a=check_list_tpl_apply' ;
	var params = { 'tid':tid,'todoid':todoid  };
	$.post( url , params , function( data )
	{
			var data_obj = $.parseJSON( data );
			if( data_obj.err_code == 0 )
			{
				done();
				$('#tpl_text').val('');
				show_todo_detail(todoid);
				close_float_box();

			}
	});
	doing();
}

function check_list_tpl_remove(title)
{
	var tid = parseInt( $('#tpl_select option:selected').attr('value') );
	if( tid  < 1 || isNaN(tid) )
	{
		alert('tid错误');
		return false;
	} 
	if( confirm('确定要删除CheckList模板【'+title+'('+tid+')】吗？') )
	{
		var url = '?c=plugin&a=check_list_tpl_remove' ;
		var params = { 'tid':tid,'content':$('#tpl_text').val()  };
		$.post( url , params , function( data )
		{
				var data_obj = $.parseJSON( data );
				if( data_obj.err_code == 0 )
				{
					done();
					$('#tpl_text').val('');
					$('#tpl_select option:selected').remove();
				}
		});
		doing();
	}
	
}



function check_list_tpl_update()
{
	var tid = parseInt( $('#tpl_select option:selected').attr('value') );
	if( tid  < 1 || isNaN(tid) )
	{
		alert('tid错误');
		return false;
	} 
	var url = '?c=plugin&a=check_list_tpl_update' ;
	var params = { 'tid':tid,'content':$('#tpl_text').val()  };
	$.post( url , params , function( data )
	{
			var data_obj = $.parseJSON( data );
			if( data_obj.err_code == 0 )
			{
				done();
				noty(
				{
					text:'模板内容已保存',
					timeout:1000,
					layout:'topRight'
				});

				$('#ckltpl_apply_btn').show();
				$('#ckltpl_update_btn').hide('btn');
			}
	});
	doing();
}

function cktpll_changed()
{
	$('#ckltpl_apply_btn').hide();
	$('#ckltpl_update_btn').show();
}

function check_list_tpl_create()
{
	var tname = prompt('请输入模板名称');
	if( tname != null && tname.length > 0  )
	{
		var url = '?c=plugin&a=check_list_tpl_create' ;
		var params = { 'title':tname,'content' : '填写CheckList检查项，每行一条'  };
		$.post( url , params , function( data )
		{
			var data_obj = $.parseJSON( data );
			if( data_obj.err_code == 0 )
			{
				$('#tpl_select option').attr('selected',false);
				$('#tpl_select').append( $('<option value="' + data_obj.data.tid + '" selected="selected">'+ data_obj.data.title +'</option>') );
				check_list_tpl_set2text(data_obj.data.tid);
			}
		});
	}
}

function check_list_tpl_set2text( tid )
{
	if( tid < 1 ) return false;

	var url = '?c=plugin&a=check_list_tpl_detail' ;
	var params = { 'tid':tid  };

	$.post( url , params , function( data )
	{
		// console.log( data );
		var data_obj = $.parseJSON( data );
		 
		if( data_obj.err_code == 0 )
		{
			$('#tpl_text').val(data_obj.data.content);
		}
		else
		{
			alert('API调用错误，请稍后再试。错误号'+data_obj.err_code + ' 错误信息 ' + data_obj.message);
		}

		done();
		

	} );

	doing();
}

