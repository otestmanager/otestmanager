<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script>
function get_select_project_tree_node(){
	var project_treePanel = Ext.getCmp('project_treePanel');
	var node = project_treePanel.getSelectionModel().getSelection();
	return node;
}

function code_grid_select_item(grid_id){
	var grid = Ext.getCmp(grid_id);
	if(grid.getSelectionModel().selected.length >= 1){
		var selItem = grid.getSelectionModel().selected.items[0];
		return selItem;
	}else{
		//Ext.Msg.alert("OTM","No Select Data");
		Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
		return null;
	}
}

function code_get_store(url,params){
	var node = get_select_project_tree_node();
	params.pr_seq = node[0].data.pr_seq;

	var store = Ext.create('Ext.data.Store', {
		fields:['otm_project_pr_seq','pco_seq','pco_type','pco_name','pco_is_required','pco_is_default','pco_position','pco_default_value','pco_color','pco_is_use'],

		proxy: {
			type	: 'ajax',
			url		: url,
			extraParams: params,
			actionMethods: {
				create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
			},
			reader: {
				type: 'json',
				totalProperty: 'totalCount',
				rootProperty: 'data'
			}
		}
	});

	return store;
};

var renderer_YorN = function(value, metaData, record, rowIndex, colIndex, store){
						return (value === 'Y')?'O':'';
					};

function get_grid(type){
	var url = '';
	var params = {};

	var columns = [];
	switch(type){
		case "status":
			columns = [
				Ext.create('Ext.grid.RowNumberer'),
				{header: Otm.def_status,	dataIndex: 'pco_name',		flex: 1},
				{header: Otm.com_default_value,	dataIndex: 'pco_is_default',width:100, align:'center', renderer: renderer_YorN},
				{header: Otm.com_complete+' '+Otm.def_status,dataIndex: 'pco_is_required',width:100, align:'center'},
				{header: Otm.com_sort,	dataIndex: 'pco_position',	width:50, hidden:true}
			];
			url = './index.php/Project_setup/code_list/status';
			break;
		case "severity":
			columns = [
				Ext.create('Ext.grid.RowNumberer'),
				{header: Otm.def_severity,	dataIndex: 'pco_name',		flex: 1},
				{header: Otm.com_default_value,	dataIndex: 'pco_is_default',width:100, align:'center', renderer: renderer_YorN},
				{header: Otm.com_sort,	dataIndex: 'pco_position',	width:50, hidden:true}
			];

			url = './index.php/Project_setup/code_list/severity';
			break;
		case "priority":
			columns = [
				Ext.create('Ext.grid.RowNumberer'),
				{header: Otm.def_priority,dataIndex: 'pco_name',		flex: 1},
				{header: Otm.com_default_value,	dataIndex: 'pco_is_default',width:100, align:'center', renderer: renderer_YorN},
				{header: Otm.com_sort,	dataIndex: 'pco_position',	width:50, hidden:true}
			];

			url = './index.php/Project_setup/code_list/priority';
			break;
		case "frequency":
			columns = [
				Ext.create('Ext.grid.RowNumberer'),
				{header: Otm.def_frequency,dataIndex: 'pco_name',		flex: 1},
				{header: Otm.com_default_value,	dataIndex: 'pco_is_default',width:100, align:'center', renderer: renderer_YorN},
				{header: Otm.com_sort,	dataIndex: 'pco_position',	width:50, hidden:true}
			];

			url = './index.php/Project_setup/code_list/frequency';
			break;
		case "tc_item":
			columns = [
				Ext.create('Ext.grid.RowNumberer'),
				{header: Otm.tc_input_item,		dataIndex: 'pc_name',			flex: 1},
				{header: Otm.com_usage,				dataIndex: 'pc_is_use',			width:100, align:'center'},
				{header: Otm.com_mandatory,				dataIndex: 'pc_is_required',	width:100, align:'center'},
				{header: Otm.com_sort,			dataIndex: 'pco_position',		width:100, hidden:true}
			];

			url = './index.php/Project_setup/userform_list';
			params.pc_category = 'TC_ITEM';
			break;
		case "tc_result":
			columns = [
				Ext.create('Ext.grid.RowNumberer'),
				{header: Otm.tc_execution_result,dataIndex: 'pco_name',		flex: 1},
				{header: Otm.com_default_value,	dataIndex: 'pco_is_default',width:100, align:'center', renderer: renderer_YorN},
				{header: Otm.com_sort,	dataIndex: 'pco_position',	width:50, hidden:true}
			];

			url = './index.php/Project_setup/code_list/tc_result';
			break;
	}

	var store = code_get_store(url,params);

	var pro_code_select_action = false;

	var grid = {
		region	: 'center',
		layout	: 'fit',
		xtype	: 'gridpanel',
		id		: 'code_'+type+'_grid',
		flex	: 1,
		store	: store,
		viewConfig	: {
			listeners: {
				refresh: function(dataView) {
					Ext.each(dataView.panel.columns, function(column) {
					if (column.autoResizeWidth)
						column.autoSize();
					});
				},
				viewready: function(){
				   this.store.load();
				}
			}
		},
		columns: columns,
		listeners: {
			select: function(item, record, eOpts ){
				if(record){
					Ext.getCmp(type+'_save_type').setValue('update');

					if(type == 'tc_item'){
						Ext.getCmp(type+'_name').setValue(record.data.pc_name);
						if(Ext.getCmp(type+'_is_required')){
							Ext.getCmp(type+'_is_required').setValue((record.data.pc_is_required=="Y")?true:false);
						}
						if(Ext.getCmp(type+'_is_use')){
							Ext.getCmp(type+'_is_use').setValue((record.data.pc_is_use=="Y")?true:false);
						}
					}else{
						Ext.getCmp(type+'_name').setValue(record.data.pco_name);
						if(Ext.getCmp(type+'_is_required')){
							Ext.getCmp(type+'_is_required').setValue((record.data.pco_is_required=="Y")?true:false);
						}
						if(Ext.getCmp(type+'_is_default')){
							Ext.getCmp(type+'_is_default').setValue((record.data.pco_is_default=="Y")?true:false);
						}
					}

					Ext.getCmp('code_'+type+'_form').expand();
					Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_update);
				}
			}
		},
		tbar	: [
			{xtype: 'button', text: Otm.com_add, iconCls:'ico-add',	handler: function() {
				Ext.getCmp('code_'+type+'_form').reset();
				Ext.getCmp('code_'+type+'_form').expand();
				Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_add);
			}},
			{xtype: 'button', text: Otm.com_update,iconCls:'ico-update', hidden:true, handler: function() {
				var selItem = code_grid_select_item('code_'+type+'_grid');
				if(selItem){
					Ext.getCmp(type+'_save_type').setValue('update');

					if(type == 'tc_item'){

						Ext.getCmp(type+'_name').setValue(selItem.data.pc_name);
						if(Ext.getCmp(type+'_is_required')){
							Ext.getCmp(type+'_is_required').setValue((selItem.data.pc_is_required=="Y")?true:false);
						}
						if(Ext.getCmp(type+'_is_use')){
							Ext.getCmp(type+'_is_use').setValue((selItem.data.pc_is_use=="Y")?true:false);
						}

					}else{

						Ext.getCmp(type+'_name').setValue(selItem.data.pco_name);
						if(Ext.getCmp(type+'_is_required')){
							Ext.getCmp(type+'_is_required').setValue((selItem.data.pco_is_required=="Y")?true:false);
						}
						if(Ext.getCmp(type+'_is_default')){
							Ext.getCmp(type+'_is_default').setValue((selItem.data.pco_is_default=="Y")?true:false);
						}
					}

					Ext.getCmp('code_'+type+'_form').expand();
					Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_update);
				}
			}},'-',
			{xtype: 'button', text: Otm.com_remove,iconCls:'ico-remove', handler: function() {
				var selItem = code_grid_select_item('code_'+type+'_grid');

				if(type == 'status' || type == 'severity' || type == 'priority' || type == 'frequency' || type == 'tc_result'){
					if(selItem && selItem.data.pco_is_default == 'Y'){
						Ext.Msg.alert('OTM','기본값은 삭제할 수 없습니다.');
						return;
					}
				}

				if(selItem){
					Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
						if(bt=='yes'){
							var Records = Ext.getCmp('code_'+type+'_grid').getSelectionModel().selected.items;
							var pco_list = Array();

							if(Records.length >= 1){

								if(type == 'tc_item'){

									var url = './index.php/Project_setup/delete_userform';
									var check_default = 0;

									for(var i=0; i<Records.length; i++){
										if(Records[i].data['pc_content'] != 'default'){
											pco_list.push(Records[i].data['pc_seq']);
										}else{
											check_default++;
										}
									}
									if(check_default > 0){
										Ext.Msg.alert("OTM",Otm.com_msg_delete_default_value+' 1~5');
									}


									var node = get_select_project_tree_node();
									var params = {
										pc_list: Ext.encode(pco_list)
									};

								}else{
									var url = './index.php/Project_setup/delete_code';

									for(var i=0; i<Records.length; i++){
										pco_list.push(Records[i].data['pco_seq']);
									}

									var node = get_select_project_tree_node();
									var params = {
										pr_seq	: node[0].data.pr_seq,
										pco_list: Ext.encode(pco_list)
									};
								}

								Ext.Ajax.request({
									url : url,
									params : params,
									method: 'POST',
									success: function ( result, request ) {
										if(result.responseText=="ok"){
											Ext.getCmp('code_'+type+'_grid').getStore().reload();
											Ext.getCmp('code_'+type+'_form').reset();
										}else{
											Ext.Msg.alert("OTM",result.responseText);
										}
									},
									failure: function ( result, request ) {
										alert("fail");
									}
								});
							}else{
								Ext.Msg.alert("OTM","No Select Data : User");
							}

							Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_add);
						}else{
							return;
						}
					})
				}else{
					Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
				}
			}},{
				xtype: 'tbseparator'
			},{
				text:Otm.com_up,
				iconCls:'ico-up',
				handler:function(btn){
					var node = get_select_project_tree_node();
					var selItem = code_grid_select_item('code_'+type+'_grid');
					if(type=='tc_item'){
						if(selItem){
							var sort_num = 0;
							var seq_arr = new Array();
							for(var i=0;i<store.data.items.length;i++){
								seq_arr.push(store.data.items[i].data.pc_seq);
							}

							var except_seq_arr = new Array();
							for(var i=0;i<seq_arr.length;i++){
								if(seq_arr[i] !=  selItem.data.pc_seq){
									except_seq_arr.push(seq_arr[i]);
								}else{
									sort_num = i-1;
								}
							}
							if(sort_num < 0){
								return;
							}else{
								var k=0;
								var final_arr = new Array();
								for(var i=0;i<seq_arr.length;i++){
									if(i==sort_num){
										final_arr.push(seq_arr[k+1]);
									}else{
										final_arr.push(except_seq_arr[k]);
										k++;
									}
								}

								var params = {userform_list:Ext.encode(final_arr)};
								Ext.Ajax.request({
									url		: './index.php/Project_setup/update_sort_list',
									params	: params,
									method	: 'POST',
									success	: function ( result, request ) {
										pro_code_select_action = true;
										store.reload({
											callback:function(){
												if(pro_code_select_action){
													Ext.getCmp('code_'+type+'_grid').getSelectionModel().select(sort_num);
													pro_code_select_action = false;
												}
											}
										});
									},
									failure: function ( result, request ) {
										Ext.Msg.alert("OTM",result.responseText);
									}
								});
							}
						}
						return;
					}

					if(selItem){
						var sort_num = 0;
						var seq_arr = new Array();
						for(var i=0;i<store.data.items.length;i++){
							seq_arr.push(store.data.items[i].data.pco_seq);
						}

						var except_seq_arr = new Array();
						for(var i=0;i<seq_arr.length;i++){
							if(seq_arr[i] !=  selItem.data.pco_seq){
								except_seq_arr.push(seq_arr[i]);
							}else{
								sort_num = i-1;
							}
						}
						if(sort_num < 0){
							return;
						}else{
							var k=0;
							var final_arr = new Array();
							for(var i=0;i<seq_arr.length;i++){
								if(i==sort_num){
									final_arr.push(seq_arr[k+1]);
								}else{
									final_arr.push(except_seq_arr[k]);
									k++;
								}
							}

							var params = {pr_seq:node[0].data.pr_seq, pco_type:type, pco_list:Ext.encode(final_arr)};

							Ext.Ajax.request({
								url		: './index.php/Project_setup/update_sort_code',
								params	: params,
								method	: 'POST',
								success	: function ( result, request ) {
									pro_code_select_action = true;
									store.reload({
										callback:function(){
											if(pro_code_select_action){
												Ext.getCmp('code_'+type+'_grid').getSelectionModel().select(sort_num);
												pro_code_select_action = false;
											}
										}
									});
								},
								failure: function ( result, request ) {
									Ext.Msg.alert("OTM",result.responseText);
								}
							});
						}
					}
				}
			},{
				xtype: 'tbseparator'
			},{
				text:Otm.com_down,
				iconCls:'ico-down',
				handler:function(btn){
					var node = get_select_project_tree_node();

					var selItem = code_grid_select_item('code_'+type+'_grid');

					if(type=='tc_item'){
						if(selItem){
							var sort_num = 0;
							var seq_arr = new Array();
							for(var i=0;i<store.data.items.length;i++){
								seq_arr.push(store.data.items[i].data.pc_seq);
							}

							var except_seq_arr = new Array();
							for(var i=0;i<seq_arr.length;i++){
								if(seq_arr[i] !=  selItem.data.pc_seq){
									except_seq_arr.push(seq_arr[i]);
								}else{
									sort_num = i+1;
								}
							}

							if(sort_num >= seq_arr.length){
								return;
							}else{
								var k=0;
								var final_arr = new Array();
								for(var i=0;i<seq_arr.length;i++){
									if(i==sort_num){
										final_arr.push(seq_arr[k-1]);
									}else{
										final_arr.push(except_seq_arr[k]);
										k++;
									}
								}

								var params = {userform_list:Ext.encode(final_arr)};
								Ext.Ajax.request({
									url		: './index.php/Project_setup/update_sort_list',
									params	: params,
									method	: 'POST',
									success	: function ( result, request ) {
										pro_code_select_action = true;
										store.reload({
											callback:function(){
												if(pro_code_select_action){
													Ext.getCmp('code_'+type+'_grid').getSelectionModel().select(sort_num);
													pro_code_select_action = false;
												}
											}
										});
									},
									failure: function ( result, request ) {
										Ext.Msg.alert("OTM",result.responseText);
									}
								});
							}
						}

						return;
					}

					if(selItem){
						var sort_num = 0;
						var seq_arr = new Array();
						for(var i=0;i<store.data.items.length;i++){
							seq_arr.push(store.data.items[i].data.pco_seq);
						}

						var except_seq_arr = new Array();
						for(var i=0;i<seq_arr.length;i++){
							if(seq_arr[i] !=  selItem.data.pco_seq){
								except_seq_arr.push(seq_arr[i]);
							}else{
								sort_num = i+1;
							}
						}

						if(sort_num >= seq_arr.length){
							return;
						}else{
							var k=0;
							var final_arr = new Array();
							for(var i=0;i<seq_arr.length;i++){
								if(i==sort_num){
									final_arr.push(seq_arr[k-1]);
								}else{
									final_arr.push(except_seq_arr[k]);
									k++;
								}
							}

							var params = {pr_seq:node[0].data.pr_seq, pco_type:type, pco_list:Ext.encode(final_arr)};
							Ext.Ajax.request({
								url		: './index.php/Project_setup/update_sort_code',
								params	: params,
								method	: 'POST',
								success	: function ( result, request ) {
									pro_code_select_action = true;
									store.reload({
										callback:function(){
											if(pro_code_select_action){
												Ext.getCmp('code_'+type+'_grid').getSelectionModel().select(sort_num);
												pro_code_select_action = false;
											}
										}
									});
								},
								failure: function ( result, request ) {
									Ext.Msg.alert("OTM",result.responseText);
								}
							});
						}
					}
				}
			}
		]
	};

	return grid;
};

function code_create_code(type){
		var node = get_select_project_tree_node();
		var params = {pr_seq:node[0].data.pr_seq, pco_type:type, pco_name:Ext.getCmp(type+'_name').getValue()};
		if(Ext.getCmp(type+'_is_required')){
			params.pco_is_required = Ext.getCmp(type+'_is_required').getValue();
		}
		if(Ext.getCmp(type+'_is_default')){
			params.pco_is_default = Ext.getCmp(type+'_is_default').getValue();
		}

		var url = './index.php/Project_setup/create_code';

		if(type == 'tc_item'){
			url = './index.php/Project_setup/create_userform';
			params.otm_project_pr_seq	= node[0].data.pr_seq;
			params.pc_category			= 'TC_ITEM';
			params.pc_formtype			= 'textarea';
			params.pc_name				= Ext.getCmp(type+'_name').getValue();
			params.pc_is_use		= Ext.getCmp(type+'_is_use').getValue();
			params.pc_is_required		= Ext.getCmp(type+'_is_required').getValue();
		}

		Ext.Ajax.request({
			url		: url,
			params	: params,
			method	: 'POST',
			success	: function ( result, request ) {
				if(result.responseText=="ok"){
					Ext.getCmp('code_'+type+'_grid').getStore().reload();
					Ext.getCmp('code_'+type+'_form').reset();
					Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_add);
				}else{
					Ext.Msg.alert("OTM",result.responseText);
				}
			},
			failure: function ( result, request ) {
				alert("fail");
			}
		});
}

function code_update_code(type){
	var selItem = code_grid_select_item('code_'+type+'_grid');
	if(selItem){
		var node = get_select_project_tree_node();
		var params = {
			pr_seq		: node[0].data.pr_seq,
			pco_seq		: selItem.data.pco_seq,
			pco_type	: type,
			pco_name	: Ext.getCmp(type+'_name').getValue()
		};
		if(Ext.getCmp(type+'_is_required')){
			params.pco_is_required = Ext.getCmp(type+'_is_required').getValue();
		}
		if(Ext.getCmp(type+'_is_default')){
			params.pco_is_default = Ext.getCmp(type+'_is_default').getValue();
		}

		if(type == 'status' || type == 'severity' || type == 'priority' || type == 'frequency' || type == 'tc_result'){
			var check_default = 0;
			var store = Ext.getCmp('code_'+type+'_grid').getStore().data.items;
			for(var i=0; i<store.length; i++){
				if(selItem.data.pco_seq == store[i].data.pco_seq){
					if(Ext.getCmp(type+'_is_default').getValue() == true){
						check_default++;
					}
				}else{
					if(store[i].data.pco_is_default == 'Y'){
						check_default++;
					}
				}
			}
			if(check_default == 0){
				Ext.Msg.alert('OTM',Otm.com_msg_should_default_value);
				return;
			}
		}

		var url = './index.php/Project_setup/update_code';

		if(type == 'tc_item'){
			params = {};

			url = './index.php/Project_setup/update_userform';
			params.otm_project_pr_seq	= node[0].data.pr_seq;
			params.pc_category			= 'TC_ITEM';
			params.pc_formtype			= 'textarea';
			params.pc_seq				= selItem.data.pc_seq;

			params.pc_name				= Ext.getCmp(type+'_name').getValue();
			params.pc_is_use			= Ext.getCmp(type+'_is_use').getValue();
			params.pc_is_required		= Ext.getCmp(type+'_is_required').getValue();
			params.pc_content			= selItem.data.pc_content;
		}
		Ext.Ajax.request({
			url		: url,
			params	: params,
			method	: 'POST',
			success	: function ( result, request ) {
				if(result.responseText=="ok"){
					Ext.getCmp('code_'+type+'_grid').getStore().reload();
					Ext.getCmp('code_'+type+'_form').reset();
					Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_add);
				}else{
					Ext.Msg.alert("OTM",result.responseText);
				}
			},
			failure: function ( result, request ) {
				alert("fail");
			}
		});
	}
}

function get_form(type){
	var title = '';
	var items = [];
	switch(type){
		case "status":
			title = Otm.def+' '+Otm.def_status+' / '+Otm.com_add;
			items = [{
				xtype: 'hiddenfield',
				id:'status_save_type'
			},{
				xtype: 'textfield',
				fieldLabel: Otm.def_status+'(*)',
				minLength:2,maxLength:100,
				id	: 'status_name',
				allowBlank: false
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
						flex: 1,
						xtype: 'checkboxfield',
						fieldLabel: Otm.com_complete+' '+Otm.def_status,
						id	: 'status_is_required'
					},{
						xtype: 'displayfield',
						margin: '0 0 0 30',
						value: ''
					},{
						flex: 1,
						xtype: 'checkboxfield',
						fieldLabel: Otm.com_default_value,
						id	: 'status_is_default'
					}
				]
			}];
			break;
		case "severity":
			title = Otm.def+' '+Otm.def_severity+' / '+Otm.com_add;
			items = [{
				xtype: 'hiddenfield',
				id:'severity_save_type'
			},{
				xtype: 'textfield',
				fieldLabel: Otm.def_severity+'(*)',
				minLength:2,maxLength:100,
				id	: 'severity_name',
				allowBlank: false
			},{
				xtype: 'checkboxfield',
				fieldLabel: Otm.com_default_value,
				id	: 'severity_is_default'
			}];
			break;
		case "priority":
			title = Otm.def+' '+Otm.def_priority+' / '+Otm.com_add;
			items = [{
				xtype: 'hiddenfield',
				id:'priority_save_type'
			},{
				xtype: 'textfield',
				fieldLabel: Otm.def_priority+'(*)',
				minLength:2,maxLength:100,
				id	: 'priority_name',
				allowBlank: false
			},{
				xtype: 'checkboxfield',
				fieldLabel: Otm.com_default_value,
				id	: 'priority_is_default'
			}];
			break;
		case "frequency":
			title = Otm.def+' '+Otm.def_frequency+' / '+Otm.com_add;
			items = [{
				xtype: 'hiddenfield',
				id:'frequency_save_type'
			},{
				xtype: 'textfield',
				fieldLabel: Otm.def_frequency+'(*)',
				minLength:2,maxLength:100,
				id	: 'frequency_name',
				allowBlank: false
			},{
				xtype: 'checkboxfield',
				fieldLabel: Otm.com_default_value,
				id	: 'frequency_is_default'
			}];
			break;
		case "tc_item":
			title = Otm.tc_input_item+' / '+Otm.com_add;

			items = [{
				xtype: 'hiddenfield',
				id:'tc_item_save_type'
			},{
				xtype: 'textfield',
				fieldLabel: Otm.tc_input_item+'(*)',
				minLength:2,maxLength:100,
				id	: 'tc_item_name',
				allowBlank: false
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
						flex: 1,
						xtype: 'checkboxfield',
						fieldLabel: Otm.com_usage,
						id	: 'tc_item_is_use',
						checked: true
					},{
						xtype: 'displayfield',
						margin: '0 0 0 30',
						value: ''
					},{
						flex: 1,
						xtype: 'checkboxfield',
						fieldLabel: Otm.com_mandatory,
						id	: 'tc_item_is_required',
						checked: true
					}
				]
			}];
			break;
		case "tc_result":
			title = Otm.tc_execution_result_item_all+' / '+Otm.com_add;
			items = [{
				xtype: 'hiddenfield',
				id:'tc_result_save_type'
			},{
				xtype: 'textfield',
				fieldLabel: Otm.tc_execution_result_item+'(*)',
				minLength:2,maxLength:100,
				id	: 'tc_result_name',
				allowBlank: false
			},{
				xtype: 'checkboxfield',
				fieldLabel: Otm.com_default_value,
				id	: 'tc_result_is_default'
			}];
			break;
	}

	var form_id = 'code_'+type+'_form';

	var form = Ext.create("Ext.form.Panel",{
		region		: 'east',
		xtype		: 'panel',
		title		: title,
		id			: form_id,
		flex		: 1,
		autoScroll	: true,
		collapsible	: true,
		collapsed	: false,
		animation	: false,
		border		: false,
		defaults	: {
			anchor: '100%',
			layout: {
				type: 'hbox',
				defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
			},
			margin: 5
		},
		items	: items,
		buttons : [{
			text:Otm.com_save,
			iconCls:'ico-save',
			disabled: true,
			formBind: true,
			handler:function(btn){
				if(Ext.getCmp(type+'_save_type').getValue()=='update'){
					code_update_code(type);
				}else{
					code_create_code(type);
				}
			}
		},{
			text:Otm.com_reset,
			hidden:true,
			handler:function(btn){
				if(Ext.getCmp(type+'_save_type').getValue() == "update"){
					var record = Ext.getCmp('code_'+type+'_grid').getSelectionModel().selected.items[0];
					if(record){
						Ext.getCmp(type+'_save_type').setValue('update');

						Ext.getCmp(type+'_name').setValue(record.data.pco_name);
						if(Ext.getCmp(type+'_is_required')){
							Ext.getCmp(type+'_is_required').setValue((record.data.pco_is_required=="Y")?true:false);
						}
						if(Ext.getCmp(type+'_is_default')){
							Ext.getCmp(type+'_is_default').setValue((record.data.pco_is_default=="Y")?true:false);
						}

						Ext.getCmp('code_'+type+'_form').expand();
						Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_update);
					}

				}else{
					Ext.getCmp(form_id).reset();
				}
			}
		}]
	});

	return form;
};

var status_grid = get_grid('status');
var status_form_panel = get_form('status');

var severity_grid = get_grid('severity');
var severity_form_panel = get_form('severity');

var priority_grid = get_grid('priority');
var priority_form_panel = get_form('priority');

var frequency_grid = get_grid('frequency');
var frequency_form_panel = get_form('frequency');

var tc_item_grid = get_grid('tc_item');
var tc_item_form_panel = get_form('tc_item');

var tc_result_grid = get_grid('tc_result');
var tc_result_form_panel = get_form('tc_result');

var status_panel = {
	layout	: 'border',
	xtype	: 'panel',
	title	: Otm.def+' '+Otm.def_status,
	flex	: 1,
	height	: 210,
	items	: [status_grid,status_form_panel]
};
var severity_panel = {
	layout	: 'border',
	xtype	: 'panel',
	title	: Otm.def+' '+Otm.def_severity,
	flex	: 1,
	height	: 200,
	items	:[severity_grid,severity_form_panel]
};
var priority_panel = {
	layout	: 'border',
	xtype	: 'panel',
	title	: Otm.def+' '+Otm.def_priority,
	flex	: 1,
	height	: 200,
	items	:[priority_grid,priority_form_panel]
};
var frequency_panel = {
	layout	: 'border',
	xtype	: 'panel',
	title	: Otm.def+' '+Otm.def_frequency,
	flex	: 1,
	height	: 200,
	items	:[frequency_grid,frequency_form_panel]
};
var tc_item_panel = {
	layout	: 'border',
	xtype	: 'panel',
	title	: Otm.tc_input_item + ' <font color="red">('+Otm.com_msg_delete_default_value+' 1~5)</font>',
	flex	: 1,
	height	: 200,
	items	:[tc_item_grid,tc_item_form_panel]
};
var tc_result_panel = {
	layout	: 'border',
	xtype	: 'panel',
	title	: Otm.tc_execution_result_item_all,
	flex	: 1,
	height	: 200,
	items	:[tc_result_grid,tc_result_form_panel]
};

var main_center = {
	region	: 'center',
	xtype	: 'panel',
	autoScroll : true,
	defaults	: {
		anchor: '100%',
		layout: {
			type	: 'hbox',
			align	: 'stretch',
			defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
		},
		margin: 5
	},
	items	:[status_panel,severity_panel,priority_panel,frequency_panel,tc_item_panel,tc_result_panel]
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
	Ext.getCmp('project_setup_code').add(main_panel);
	Ext.getCmp('project_setup_code').doLayout();
});

</script>
