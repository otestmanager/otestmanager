<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>

<script>
function check_all(grid,column)
{
	 grid.getStore().data.each(function (record) {
		record.set(column, true);
	});

}

function uncheck_all(grid,column)
{
	 grid.getStore().data.each(function (record) {
		record.set(column, false);
	});
}

function onCheckChange (column, rowIndex, checked, eOpts) {
	var id_array  = column.id.split('_');
	var grid = Ext.getCmp(id_array[0]+'_'+id_array[1]);

	if(rowIndex == 0){
		if(checked){
			check_all(grid,id_array[2]);
		}else{
			uncheck_all(grid,id_array[2]);
		}
	}else{
		if(checked){
			var check_cnt = 0;
			grid.getStore().data.each(function (record) {
				if(record.data[id_array[2]]){
					check_cnt++;
				}

				if(grid.getStore().getCount()-1 == check_cnt){
					check_all(grid,id_array[2]);
				}
			});
		}else{
			grid.getStore().data.items[0].set(id_array[2], false);
		}
	}
	return;
}

var main_center = {
	region	: 'center',
	xtype	: 'panel',
	autoScroll : true,

	defaults	: {
		anchor: '100%',
		layout: {
			type	: 'vbox',
			align	: 'stretch',
			defaultMargins: {top: 0, right: 0, bottom: 0, left: 0}
		},
		border : false
	},
	items	:[{
		xtype	: 'panel',
		items: [
		{
			xtype	: 'gridpanel',
			title	: '| 프로젝트 알림 설정',
			id		: 'notification_project',
			padding : 5,
			store	: new Ext.data.SimpleStore({
				fields:['name', 'writer', 'member'],
				data:[['- 프로젝트',true,true],[' 추가',true,'n/a'],[' 참여자(추가 / 수정 / 삭제)',true,true],[' 수정',true,true],[' 삭제',true,true]]
			}),
			viewConfig: {
                listeners: {
                    refresh: function(view) {
                        var nodes = view.getNodes();
						var node = nodes[0];
						Ext.fly(node).setStyle('background-color', '#dddddd');

						var cells = Ext.get(nodes[1]).query('td');
						Ext.fly(cells[2]).setText('n/a');
						Ext.fly(cells[2]).setStyle('text-align', 'center');
                    }
                }
            },
			columns: [
				{header: '알림 기능',	dataIndex: 'name',		flex: 1},
				{header: '작성자',		dataIndex: 'writer',	xtype: 'checkcolumn',	width:100, align:'center',
					id: 'notification_project_writer',
					listeners: {
							checkChange: onCheckChange
					}
				},
				{header: '참여자',		dataIndex: 'member',	xtype: 'checkcolumn',	width:100, align:'center',
					id: 'notification_project_member',
					listeners: {
							checkChange: onCheckChange
					}
				}
			]
		},{
			xtype	: 'gridpanel',
			title	: '| 테스트 케이스 알림 설정',
			id		: 'notification_testcase',
			padding : 5,
			store	: new Ext.data.SimpleStore({
				fields:['name', 'writer', 'member'],
				data:[['- 테스트 케이스',true,true],[' 추가',true,'n/a'],[' 실행 담당자(지정 / 취소)',true,true],[' 수정',true,true],[' 실행',true,true],[' 삭제',true,true]]
			}),
			viewConfig: {
                listeners: {
                    refresh: function(view) {
                        var nodes = view.getNodes();
						var node = nodes[0];
						Ext.fly(node).setStyle('background-color', '#dddddd');

						var cells = Ext.get(nodes[1]).query('td');
						Ext.fly(cells[2]).setText('n/a');
						Ext.fly(cells[2]).setStyle('text-align', 'center');
                    }
                }
            },
			columns: [
				{header: '알림 기능',	dataIndex: 'name',		flex: 1},
				{header: '작성자',		dataIndex: 'writer',	xtype: 'checkcolumn',	width:100, align:'center',
					id: 'notification_testcase_writer',
					listeners: {
							checkChange: onCheckChange
					}
				},
				{header: '담당자',		dataIndex: 'member',	xtype: 'checkcolumn',	width:100, align:'center',
					id: 'notification_testcase_member',
					listeners: {
							checkChange: onCheckChange
					}
				}
			]
		},{
			xtype	: 'gridpanel',
			title	: '| 결함 알림 설정',
			id		: 'notification_defect',
			padding : 5,
			store	: new Ext.data.SimpleStore({
				fields:['name', 'writer', 'member'],
				data:[['- 결함',true,true],[' 추가',true,'n/a'],[' 결함 상태 변경',true,true],[' 상태 담당자 지정',true,true],[' 수정',true,true],[' 삭제',true,true]]
			}),
			viewConfig: {
                listeners: {
					checked: function(a,b,c){
						console.log('checked',a,b,c);
					},
                    refresh: function(view) {
                        var nodes = view.getNodes();
						var node = nodes[0];
						Ext.fly(node).setStyle('background-color', '#dddddd');

						var cells = Ext.get(nodes[1]).query('td');
						Ext.fly(cells[2]).setText('n/a');
						Ext.fly(cells[2]).setStyle('text-align', 'center');
                    }
                }
            },
			columns: [
				{header: '알림 기능',	dataIndex: 'name',		flex: 1},
				{header: '작성자',		dataIndex: 'writer',	xtype: 'checkcolumn',	width:100, align:'center',
					id: 'notification_defect_writer',
					listeners: {
							checkChange: onCheckChange
					}
				},
				{header: '담당자',		dataIndex: 'member',	xtype: 'checkcolumn',	width:100, align:'center',
					id: 'notification_defect_member',
					listeners: {
							checkChange: onCheckChange
					}
				}
			]
		}]
	}],
	buttons : [{
		text:Otm.com_save,
		iconCls:'ico-save',
		handler:function(btn){
			Ext.Msg.alert('OTM',Otm.com_save);
		}
	}]
};

Ext.onReady(function(){
	var main_panel = {
		layout		: 'border',
		defaults	: {
			collapsible	: false,
			split		: true,
			bodyStyle	: 'padding:0px'
		},
		items		: [main_center]
	};
	Ext.getCmp('notification').add(main_panel);
	Ext.getCmp('notification').doLayout();
});

</script>