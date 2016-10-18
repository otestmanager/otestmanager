<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script>
function code_grid_select_item(grid_id){
	var grid = Ext.getCmp(grid_id);
	if(grid.getSelectionModel().selected.length >= 1){
		var selItem = grid.getSelectionModel().selected.items[0];
		return selItem;
	}else{
		Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
	}
}

function code_get_store(url,params){
	var store = Ext.create('Ext.data.Store', {
		fields:['co_seq','co_type','co_name','co_is_required','co_is_default','co_position','co_default_value','co_color'],
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
				{xtype: 'rownumberer',width: 30,sortable: false},
				{header: Otm.def_status,	dataIndex: 'co_name',		flex: 1},
				{header: Otm.com_default_value,	dataIndex: 'co_is_default',	width:100, align:'center', renderer: renderer_YorN},
				{header: Otm.com_complete+' '+Otm.com_status,dataIndex: 'co_is_required',	width:100, align:'center'}
			];

			url = './index.php/Code/code_list/status';
			break;
		case "severity":
			columns = [
				{xtype: 'rownumberer',width: 30,sortable: false},
				{header: Otm.def_severity,	dataIndex: 'co_name',		flex: 1},
				{header: Otm.com_default_value,	dataIndex: 'co_is_default',	width:100, align:'center', renderer: renderer_YorN}
			];

			url = './index.php/Code/code_list/severity';
			break;
		case "priority":
			columns = [
				{xtype: 'rownumberer',width: 30,sortable: false},
				{header: Otm.def_priority,dataIndex: 'co_name',		flex: 1},
				{header: Otm.com_default_value,	dataIndex: 'co_is_default',	width:100, align:'center', renderer: renderer_YorN}
			];

			url = './index.php/Code/code_list/priority';
			break;
		case "frequency":
			columns = [
				{xtype: 'rownumberer',width: 30,sortable: false},
				{header: Otm.def_frequency,dataIndex: 'co_name',		flex: 1},
				{header: Otm.com_default_value,	dataIndex: 'co_is_default', width:100, align:'center', renderer: renderer_YorN}
			];

			url = './index.php/Code/code_list/frequency';
			break;
		case "tc_item":
			columns = [
				Ext.create('Ext.grid.RowNumberer'),
				{header: Otm.tc_input_item,		dataIndex: 'cf_name',			flex: 1},
				{header: Otm.com_mandatory,				dataIndex: 'cf_is_required',	width:100, align:'center'},
				{header: Otm.com_sort,			dataIndex: 'cf_position',		width:100, hidden:true}
			];

			url = './index.php/Userform/userform_list';

			params.cf_category = 'TC_ITEM';
			break;
		case "tc_result":
			columns = [
				{xtype: 'rownumberer',width: 30,sortable: false},
				{header: Otm.tc_execution_result,dataIndex: 'co_name',		flex: 1},
				{header: Otm.com_default_value,	dataIndex: 'co_is_default', width:100, align:'center', renderer: renderer_YorN}
			];

			url = './index.php/Code/code_list/tc_result';
			break;
	}

	var store = code_get_store(url,params);

	var sys_code_select_action = false;

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

						Ext.getCmp(type+'_name').setValue(record.data.cf_name);
						if(Ext.getCmp(type+'_is_required')){
							Ext.getCmp(type+'_is_required').setValue((record.data.cf_is_required=="Y")?true:false);
						}
						if(Ext.getCmp(type+'_is_use')){
							Ext.getCmp(type+'_is_use').setValue((record.data.cf_is_use=="Y")?true:false);
						}

					}else{

						Ext.getCmp(type+'_name').setValue(record.data.co_name);
						if(Ext.getCmp(type+'_is_required')){
							Ext.getCmp(type+'_is_required').setValue((record.data.co_is_required=="Y")?true:false);
						}
						if(Ext.getCmp(type+'_is_default')){
							Ext.getCmp(type+'_is_default').setValue((record.data.co_is_default=="Y")?true:false);
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
			{xtype: 'button', text: Otm.com_update, iconCls:'ico-update',	hidden:true, handler: function() {
				var selItem = code_grid_select_item('code_'+type+'_grid');
				if(selItem){
					Ext.getCmp(type+'_save_type').setValue('update');

					if(type == 'tc_item'){

						Ext.getCmp(type+'_name').setValue(selItem.data.cf_name);
						if(Ext.getCmp(type+'_is_required')){
							Ext.getCmp(type+'_is_required').setValue((selItem.data.cf_is_required=="Y")?true:false);
						}
						if(Ext.getCmp(type+'_is_use')){
							Ext.getCmp(type+'_is_use').setValue((selItem.data.cf_is_use=="Y")?true:false);
						}

					}else{

						Ext.getCmp(type+'_name').setValue(selItem.data.co_name);
						if(Ext.getCmp(type+'_is_required')){
							Ext.getCmp(type+'_is_required').setValue((selItem.data.co_is_required=="Y")?true:false);
						}
						if(Ext.getCmp(type+'_is_default')){
							Ext.getCmp(type+'_is_default').setValue((selItem.data.co_is_default=="Y")?true:false);
						}

					}

					Ext.getCmp('code_'+type+'_form').expand();
					Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_update);
				}
			}},'-',
			{xtype: 'button', text: Otm.com_remove, iconCls:'ico-remove', handler: function() {

				var selItem = code_grid_select_item('code_'+type+'_grid');

				if(type == 'status' || type == 'severity' || type == 'priority' || type == 'frequency' || type == 'tc_result'){
					if(selItem.data.co_is_default == 'Y'){
						Ext.Msg.alert('OTM',Otm.com_msg_delete_default_value);
						return;
					}
				}

				if(selItem){
					Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
						if(bt=='yes'){
							var Records = Ext.getCmp('code_'+type+'_grid').getSelectionModel().selected.items;
							var co_list = Array();

							if(Records.length >= 1){

								if(type == 'tc_item'){

									var url = './index.php/Userform/delete_userform';
									var check_default = 0;

									for(var i=0; i<Records.length; i++){
										if(Records[i].data['cf_content'] != 'default'){
											co_list.push(Records[i].data['cf_seq']);
										}else{
											check_default++;
										}
									}

									if(check_default > 0){
										Ext.Msg.alert("OTM",Otm.com_msg_delete_default_value+' 1~5');
									}

									var params = {
										cf_list: Ext.encode(co_list)
									};

								}else{
									var url = './index.php/Code/delete_code';

									for(var i=0; i<Records.length; i++){
										co_list.push(Records[i].data['co_seq']);
									}

									var params = {
										co_list	: Ext.encode(co_list)
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
										Ext.Msg.alert("OTM",result.responseText);
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
					var selItem = code_grid_select_item('code_'+type+'_grid');
					if(type=='tc_item'){
						if(selItem){
							var sort_num = 0;
							var seq_arr = new Array();
							for(var i=0;i<store.data.items.length;i++){
								seq_arr.push(store.data.items[i].data.cf_seq);
							}

							var except_seq_arr = new Array();
							for(var i=0;i<seq_arr.length;i++){
								if(seq_arr[i] !=  selItem.data.cf_seq){
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
									url		: './index.php/Userform/update_sort_list',
									params	: params,
									method	: 'POST',
									success	: function ( result, request ) {
										sys_code_select_action = true;
										store.reload({
											callback:function(){
												if(sys_code_select_action){
													Ext.getCmp('code_'+type+'_grid').getSelectionModel().select(sort_num);
													sys_code_select_action = false;
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
							seq_arr.push(store.data.items[i].data.co_seq);
						}

						var except_seq_arr = new Array();
						for(var i=0;i<seq_arr.length;i++){
							if(seq_arr[i] !=  selItem.data.co_seq){
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

							var params = {co_type:type, co_list:Ext.encode(final_arr)};

							Ext.Ajax.request({
								url		: './index.php/Code/update_sort_code',
								params	: params,
								method	: 'POST',
								success	: function ( result, request ) {

									sys_code_select_action = true;

									store.reload({
										callback:function(){
											if(sys_code_select_action){
												Ext.getCmp('code_'+type+'_grid').getSelectionModel().select(sort_num);
												sys_code_select_action = false;
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
					var selItem = code_grid_select_item('code_'+type+'_grid');
					if(type=='tc_item'){
						if(selItem){
							var sort_num = 0;
							var seq_arr = new Array();
							for(var i=0;i<store.data.items.length;i++){
								seq_arr.push(store.data.items[i].data.cf_seq);
							}

							var except_seq_arr = new Array();
							for(var i=0;i<seq_arr.length;i++){
								if(seq_arr[i] !=  selItem.data.cf_seq){
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
									url		: './index.php/Userform/update_sort_list',
									params	: params,
									method	: 'POST',
									success	: function ( result, request ) {
										sys_code_select_action = true;

										store.reload({
											callback:function(){
												if(sys_code_select_action){
													Ext.getCmp('code_'+type+'_grid').getSelectionModel().select(sort_num);
													sys_code_select_action = false;
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
							seq_arr.push(store.data.items[i].data.co_seq);
						}

						var except_seq_arr = new Array();
						for(var i=0;i<seq_arr.length;i++){
							if(seq_arr[i] !=  selItem.data.co_seq){
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

							var params = {co_type:type, co_list:Ext.encode(final_arr)};
							Ext.Ajax.request({
								url		: './index.php/Code/update_sort_code',
								params	: params,
								method	: 'POST',
								success	: function ( result, request ) {
									sys_code_select_action = true;

									store.reload({
										callback:function(){
											if(sys_code_select_action){
												Ext.getCmp('code_'+type+'_grid').getSelectionModel().select(sort_num);
												sys_code_select_action = false;
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

		var params = {co_type:type, co_name:Ext.getCmp(type+'_name').getValue()};
		if(Ext.getCmp(type+'_is_required')){
			params.co_is_required = Ext.getCmp(type+'_is_required').getValue();
		}
		if(Ext.getCmp(type+'_is_default')){
			params.co_is_default = Ext.getCmp(type+'_is_default').getValue();
		}

		var url = './index.php/Code/create_code';

		if(type == 'tc_item'){
			url = './index.php/Userform/create_userform';
			params.cf_category			= 'TC_ITEM';
			params.cf_formtype			= 'textarea';
			params.cf_name				= Ext.getCmp(type+'_name').getValue();
			params.cf_is_use		= Ext.getCmp(type+'_is_use').getValue();
			params.cf_is_required		= Ext.getCmp(type+'_is_required').getValue();
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
				Ext.Msg.alert("OTM",result.responseText);
			}
		});
}

function code_update_code(type){
	var selItem = code_grid_select_item('code_'+type+'_grid');
	if(selItem){
		var params = {
			co_seq	: selItem.data.co_seq,
			co_type	: type,
			co_name	: Ext.getCmp(type+'_name').getValue()
		};
		if(Ext.getCmp(type+'_is_required')){
			params.co_is_required = Ext.getCmp(type+'_is_required').getValue();
		}
		if(Ext.getCmp(type+'_is_default')){
			params.co_is_default = Ext.getCmp(type+'_is_default').getValue();
		}

		if(type == 'status' || type == 'severity' || type == 'priority' || type == 'frequency' || type == 'tc_result'){
			var check_default = 0;
			var store = Ext.getCmp('code_'+type+'_grid').getStore().data.items;

			for(var i=0; i<store.length; i++){
				if(selItem.data.co_seq == store[i].data.co_seq){
					if(Ext.getCmp(type+'_is_default').getValue() == true){
						check_default++;
					}
				}else{
					if(store[i].data.co_is_default == 'Y'){
						check_default++;
					}
				}
			}

			if(check_default == 0){
				Ext.Msg.alert('OTM',Otm.com_msg_should_default_value);
				return;
			}
		}

		var url = './index.php/Code/update_code';

		if(type == 'tc_item'){
			params = {};

			url = './index.php/Userform/update_userform';
			params.cf_category			= 'TC_ITEM';
			params.cf_formtype			= 'textarea';
			params.cf_seq				= selItem.data.cf_seq;

			params.cf_name				= Ext.getCmp(type+'_name').getValue();
			params.cf_is_use			= Ext.getCmp(type+'_is_use').getValue();
			params.cf_is_required		= Ext.getCmp(type+'_is_required').getValue();
			params.cf_content			= selItem.data.cf_content;
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
				Ext.Msg.alert("OTM",result.responseText);
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
				id: type+'_save_type'
			},{
				xtype: 'textfield',
				fieldLabel: Otm.def_status+'(*)',
				minLength:2,
				maxLength:50,
				id	:  type+'_name',
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
						fieldLabel: Otm.com_complete+' '+Otm.com_status,
						id	:  type+'_is_required'
					},{
						xtype: 'displayfield',
						margin: '0 0 0 30',
						value: ''
					},{
						flex: 1,
						xtype: 'checkboxfield',
						fieldLabel: Otm.com_default_value,
						id	:  type+'_is_default'
					}
				]
			}];
			break;
		case "severity":
			title = Otm.def+' '+Otm.def_severity+' / '+Otm.com_add;

			items = [{
				xtype: 'hiddenfield',
				id: type+'_save_type'
			},{
				xtype: 'textfield',
				fieldLabel: Otm.def_severity+'(*)',
				minLength:2,
				maxLength:50,
				id	:  type+'_name',
				allowBlank: false
			},{
				xtype: 'checkboxfield',
				fieldLabel: Otm.com_default_value,
				id	:  type+'_is_default'
			}];
			break;
		case "priority":
			title = Otm.def+' '+Otm.def_priority+' / '+Otm.com_add;

			items = [{
				xtype: 'hiddenfield',
				id: type+'_save_type'
			},{
				xtype: 'textfield',
				fieldLabel: Otm.def_priority+'(*)',
				minLength:2,
				maxLength:50,
				id	:  type+'_name',
				allowBlank: false
			},{
				xtype: 'checkboxfield',
				fieldLabel: Otm.com_default_value,
				id	:  type+'_is_default'
			}];
			break;
		case "frequency":
			title = Otm.def+' '+Otm.def_frequency+' / '+Otm.com_add;

			items = [{
				xtype: 'hiddenfield',
				id: type+'_save_type'
			},{
				xtype: 'textfield',
				fieldLabel: Otm.def_frequency+'(*)',
				minLength:2,
				maxLength:50,
				id	:  type+'_name',
				allowBlank: false
			},{
				xtype: 'checkboxfield',
				fieldLabel: Otm.com_default_value,
				id	:  type+'_is_default'
			}];
			break;
		case "tc_item":
			title = Otm.tc_input_item+' / '+Otm.com_add;

			items = [{
				xtype: 'hiddenfield',
				id: type+'_save_type'
			},{
				xtype: 'textfield',
				fieldLabel: Otm.tc_input_item+'(*)',
				minLength:2,maxLength:100,
				id	:  type+'_name',
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
						id	:  type+'_is_use',
						hidden :true,
						checked: true
					},{
						xtype: 'displayfield',
						margin: '0 0 0 30',
						value: ''
					},{
						flex: 1,
						xtype: 'checkboxfield',
						fieldLabel: Otm.com_mandatory,
						id	:  type+'_is_required',
						checked: true
					}
				]
			}];
			break;
		case "tc_result":
			title = Otm.tc_execution_result_item+' / '+Otm.com_add;
			items = [{
				xtype: 'hiddenfield',
				id: type+'_save_type'
			},{
				xtype: 'textfield',
				fieldLabel: Otm.tc_execution_result_item+'(*)',
				minLength:2,
				maxLength:50,
				id	:  type+'_name',
				allowBlank: false
			},{
				xtype: 'checkboxfield',
				fieldLabel: Otm.com_default_value,
				id	: type+'_is_default'
			}];
			break;
	}

	title = Otm.com_add;
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
			iconCls:'ico-reset',
			hidden:true,
			handler:function(btn){
				if(Ext.getCmp(type+'_save_type').getValue() == "update"){
					var record = Ext.getCmp('code_'+type+'_grid').getSelectionModel().selected.items[0];
					if(record){
						Ext.getCmp(type+'_save_type').setValue('update');

						Ext.getCmp(type+'_name').setValue(record.data.co_name);
						if(Ext.getCmp(type+'_is_required')){
							Ext.getCmp(type+'_is_required').setValue((record.data.co_is_required=="Y")?true:false);
						}
						if(Ext.getCmp(type+'_is_default')){
							Ext.getCmp(type+'_is_default').setValue((record.data.co_is_default=="Y")?true:false);
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
	Ext.getCmp('code').add(main_panel);
	Ext.getCmp('code').doLayout();
});
</script>