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
	}else if(grid.getSelectionModel().selected.length > 1){
		Ext.Msg.alert("OTM",Otm.com_msg_only_one);
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
		},
		autoLoad : true
	});

	store.on('load',function(data){
		var type = params.co_type;

		if(Ext.getCmp('code_'+type+'_grid'))
		{
			if(data.lastOptions.selItem){
				var select = Ext.getCmp('code_'+type+'_grid').getStore().find('co_seq', data.lastOptions.selItem.co_seq);
				if(select > 0){
					Ext.getCmp('code_'+type+'_grid').getSelectionModel().select(select);
				}
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
	var params = {co_type:type};

	var columns = [];
	switch(type){
		case "tc_id_rule":
			columns = [
				{xtype: 'rownumberer',width: 30,sortable: false},
				{header: Otm.id_rule.tc_id_rule,dataIndex: 'co_name',		flex: 3},
				{header: Otm.com_default_value,	dataIndex: 'co_is_default', width:100, align:'center', renderer: renderer_YorN}
			];

			url = './index.php/Id_rule/id_rule_list/tc_id_rule';
			break;
		case "df_id_rule":
			columns = [
				{xtype: 'rownumberer',width: 30,sortable: false},
				{header: Otm.id_rule.df_id_rule,dataIndex: 'co_name',		flex: 3},
				{header: Otm.com_default_value,	dataIndex: 'co_is_default', width:100, align:'center', renderer: renderer_YorN}
			];

			url = './index.php/Id_rule/id_rule_list/df_id_rule';
			break;
	}

	var store = code_get_store(url,params);
	var grid = {
		region	: 'center',
		layout	: 'fit',
		xtype	: 'gridpanel',
		id		: 'code_'+type+'_grid',
		flex	: 1,
		store	: store,
		verticalScrollerType	: 'paginggridscroller',
		invalidateScrollerOnRefresh	: false,
		selModel:Ext.create('Ext.selection.CheckboxModel'),
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
			deselect: function(item, record, eOpts ){
				var post_type = Ext.getCmp(type+'_save_type').getValue();

				if(post_type=='update')
				{
					Ext.getCmp('code_'+type+'_form').reset();
					Ext.getCmp('code_'+type+'_form').expand();
					Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_add);
				}
			},
			select: function(item, record, eOpts ){

				var Records = Ext.getCmp('code_'+type+'_grid').getSelectionModel().selected.items;
				if(Records.length > 1){
					Ext.getCmp('code_'+type+'_form').reset();
					Ext.getCmp('code_'+type+'_form').expand();
					Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_add);
					return;
				}

				if(record){
					Ext.getCmp(type+'_save_type').setValue('update');

					Ext.getCmp(type+'_sample').setValue(record.data.co_name);
					if(Ext.getCmp(type+'_is_required')){
						Ext.getCmp(type+'_is_required').setValue((record.data.co_is_required=="Y")?true:false);
					}
					if(Ext.getCmp(type+'_is_default')){
						Ext.getCmp(type+'_is_default').setValue((record.data.co_is_default=="Y")?true:false);
					}

					Ext.getCmp('code_'+type+'_form').expand();
					Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_update);

					if(!record.data.co_default_value || record.data.co_default_value.length <= 0) return;

					var id_rule_path = record.data.co_default_value;
						id_rule_path = id_rule_path.split(',');

					Ext.getCmp(type+'_default_value').setValue(record.data.co_default_value);

					var i=0;

					Ext.getCmp(type+'_name').setValue(id_rule_path[i]);
					i++;
					Ext.getCmp(type+'_sp1').setValue(id_rule_path[i]);
					i++;
					Ext.getCmp(type+'_date').setValue(id_rule_path[i]);
					i++;
					Ext.getCmp(type+'_sp2').setValue(id_rule_path[i]);
					i++;
					Ext.getCmp(type+'_number').setValue(id_rule_path[i].length+'');
				}
			}
		},
		tbar	: [
			{xtype: 'button', text: Otm.com_add, iconCls:'ico-add',	handler: function() {
				var post_type = Ext.getCmp(type+'_save_type').getValue();

				if(post_type=='update')
				{
					Ext.getCmp('code_'+type+'_form').reset();
					Ext.getCmp('code_'+type+'_form').expand();
					Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_add);
				}
				Ext.getCmp('code_'+type+'_grid').getSelectionModel().deselectAll();
			}},'-',
			{xtype: 'button', text: Otm.com_remove, iconCls:'ico-remove', handler: function() {

					var Records = Ext.getCmp('code_'+type+'_grid').getSelectionModel().selected.items;
					if(Records.length >= 1){
						for(var i=0; i<Records.length; i++){
							if(Records[i].data['co_is_default'] == 'Y'){
								Ext.MessageBox.show({
									title : Otm.com_unable_delete,
									msg : Otm.id_rule.delete_defaultvalue_msg,
									buttons : Ext.MessageBox.OK,
									icon : Ext.MessageBox.WARNING
								});
								return;
							}
						}
					}else{
						Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
						return;
					}

					Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
						if(bt=='yes'){
							var Records = Ext.getCmp('code_'+type+'_grid').getSelectionModel().selected.items;
							var co_list = Array();

							if(Records.length >= 1){

								var url = './index.php/Id_rule/delete_code';

								for(var i=0; i<Records.length; i++){
									co_list.push(Records[i].data['co_seq']);
								}

								var params = {
									co_list	: Ext.encode(co_list)
								};
								params.co_type = type;
								params.action_type = 'delete';

								Ext.Ajax.request({
									url : url,
									params : params,
									method: 'POST',
									success: function ( result, request ) {
										if(result.responseText=="ok"){
											Ext.getCmp('code_'+type+'_grid').getStore().reload();
											Ext.getCmp('code_'+type+'_form').reset();
										}else{
											var obj = Ext.decode(result.responseText);
											Ext.Msg.alert("OTM",obj.check_msg);
										}
									},
									failure: function ( result, request ) {
										Ext.Msg.alert("OTM",result.responseText);
									}
								});
							}else{
								Ext.Msg.alert("OTM","No Select Data : User");
								return;
							}

							Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_add);
						}else{
							return;
						}
					})
			}}
		]
	};

	return grid;
};

function check_id_rule(type)
{
	var post_action = function(type){};

	var post_type = Ext.getCmp(type+'_save_type').getValue();

	if(post_type=='update')
	{
		post_action = function(params){
			code_update_code(params);
		};

		var selItem = code_grid_select_item('code_'+type+'_grid');
		if(selItem){
			var params = {
				action_type		: post_type,
				co_seq			: selItem.data.co_seq,
				co_type			: type,
				before_name		: selItem.data.co_name,
				co_name			: Ext.getCmp(type+'_sample').getValue(),
				co_default_value: Ext.getCmp(type+'_default_value').getValue()
			};

			if(Ext.getCmp(type+'_is_required')){
				params.co_is_required = Ext.getCmp(type+'_is_required').getValue();
			}
			if(Ext.getCmp(type+'_is_default')){
				params.co_is_default = Ext.getCmp(type+'_is_default').getValue();
			}

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

			var before_save_msg = Otm.com_msg_update;

			if(selItem.data.co_is_default == 'Y' || Ext.getCmp(type+'_is_default').getValue() == true){
				if(type == 'tc_id_rule'){
				}else if(type == 'df_id_rule'){
				}
			}
		}
	}else{

		post_action = function(params){
			code_create_code(params);
		};

		var before_save_msg = Otm.com_msg_add;

		if(Ext.getCmp(type+'_is_default').getValue() == true){
			if(type == 'tc_id_rule'){
			}else if(type == 'df_id_rule'){
			}
		}

		var params = {
			action_type		: post_type,
			co_type			: type,
			co_name			: Ext.getCmp(type+'_sample').getValue(),
			co_default_value: Ext.getCmp(type+'_default_value').getValue()
		};
		if(Ext.getCmp(type+'_is_required')){
			params.co_is_required = Ext.getCmp(type+'_is_required').getValue();
		}
		if(Ext.getCmp(type+'_is_default')){
			params.co_is_default = Ext.getCmp(type+'_is_default').getValue();
		}
	}

	Ext.Ajax.request({
		url		: './index.php/Id_rule/check_id_rule',
		params	: params,
		method	: 'POST',
		success	: function ( result, request ) {

			var confirm_msg = before_save_msg;
			var obj = Ext.decode(result.responseText);

			if(obj.data.check){
			}else{
				params.check = obj.data.check;
				params.check_type = obj.data.check_type;
				confirm_msg = obj.data.check_msg;
			}

			Ext.Msg.confirm('OTM',confirm_msg,function(bt){
				if(bt=='yes'){
					post_action(params);
				}
			});
		},
		failure: function ( result, request ) {
			Ext.Msg.alert("OTM",result.responseText);
		}
	});
}

function code_create_code(params){
	var type = params.co_type;

	Ext.Ajax.request({
		url		: './index.php/Id_rule/create_code',
		params	: params,
		method	: 'POST',
		success	: function ( result, request ) {
			if(result.responseText=="ok"){
				Ext.getCmp('code_'+type+'_form').reset();
				Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_add);

				Ext.getCmp('code_'+type+'_grid').getStore().reload({
				   callback: function(records, operation, success) {
					 Ext.getCmp('code_'+type+'_grid').getSelectionModel().select(Ext.getCmp('code_'+type+'_grid').getStore().data.length-1)
				   }
				});
			}else{
				Ext.Msg.alert("OTM",result.responseText);
			}
		},
		failure: function ( result, request ) {
			Ext.Msg.alert("OTM",result.responseText);
		}
	});
}

function code_update_code(params){
	var type = params.co_type;

	Ext.Ajax.request({
		url		: './index.php/Id_rule/update_code',
		params	: params,
		method	: 'POST',
		success	: function ( result, request ) {
			if(result.responseText=="ok"){
				Ext.getCmp('code_'+type+'_form').reset();
				Ext.getCmp('code_'+type+'_form').setTitle(Otm.com_add);

				if(Ext.getCmp('code_'+type+'_grid').getSelectionModel().selected.length >= 1){
					params.selItem = Ext.getCmp('code_'+type+'_grid').getSelectionModel().selected.items[0].data;
				}
				Ext.getCmp('code_'+type+'_grid').getStore().reload(params);
			}else{
				Ext.Msg.alert("OTM",result.responseText);
			}
		},
		failure: function ( result, request ) {
			Ext.Msg.alert("OTM",result.responseText);
		}
	});
}

function change_rule_display(obj){
	var type = obj.findParentByType('panel').type;
	var view = Ext.getCmp(type+'_sample');
	var text = Ext.getCmp(type+'_name').getValue();
	var date = Ext.getCmp(type+'_date').getValue();
	var sp1 = Ext.getCmp(type+'_sp1').getValue();
	var sp2 = Ext.getCmp(type+'_sp2').getValue();

	var number = Ext.getCmp(type+'_number').getValue();
	var num_text = '';
	for(var i=0; i<number; i++){
		num_text += '#';
	}

	var tc_id_rule = '';
	var tc_id_rule2 = '';

	if(type==='df_id_rule'){

	}

	Ext.getCmp(type+'_default_value').setValue(tc_id_rule + text +','+ sp1 +','+ date +','+ sp2 +','+ num_text);

	var tmp_text = tc_id_rule2 + text + sp1 + date + sp2 + num_text;

	view.setValue('');
	view.setValue(tmp_text);
}

function get_form(type){
	var title = Otm.com_add;
	var items = [];
	switch(type){
		case "tc_id_rule":
			items = [{
				xtype		: 'hiddenfield',
				id			: type+'_save_type'
			},{
				xtype		: 'displayfield',
				fieldLabel	: Otm.id_rule.tc_id_rule,
				emptyText	: Otm.id_rule.temp_display,
				id			: type+'_sample',
				height		: 50,
				shrinkWrap	: 0,
				overflowY	: 'scroll',
				resizable	: false
			}];
			break;
		case "df_id_rule":
			var combo_store = code_get_store('./index.php/Id_rule/id_rule_list/tc_id_rule',{xtype:'combo'});
			items = [{
				xtype		: 'hiddenfield',
				id			: type+'_save_type'
			},{
				xtype		: 'displayfield',
				fieldLabel	: Otm.id_rule.df_id_rule,
				emptyText	: Otm.id_rule.temp_display,
				id			: type+'_sample',
				height		: 50,
				shrinkWrap	: 0,
				overflowY	: 'scroll',
				resizable	: false
			},{
				hidden			: true,
				xtype			: 'radiogroup',
				columns			: 2,
				fieldLabel		: Otm.id_rule.tc_id_rule,
				id				: type+'_tc_id_rule_radio',
				items			: [
					{
						boxLabel	: 'use',
						name		: type+'_tc_id_rule_radio',
						inputValue	: '',
						checked		: true
					},{
						boxLabel	: 'not use',
						name		: type+'_tc_id_rule_radio',
						inputValue	: 'TC_ID',
						checked		: false
					}
				],
				listeners: {
					change: change_rule_display
				}
			}];
			break;
	}

	items.push({
				xtype		: 'textfield',
				fieldLabel	: Otm.id_rule.fixed_value,
				emptyText	: Otm.id_rule.fixed_value_msg,
				id			: type+'_name',
				maxLength	: 30,
				maskRe		: /^[^,#]*$/,
				listeners	: {
					change: change_rule_display
				}
			});

	items.push({
				xtype			: 'combo',
				fieldLabel		: Otm.id_rule.separator_select,
				emptyText		: Otm.id_rule.separator_select,
				id				: type+'_sp1',
				triggerAction	: 'all',
				forceSelection	: true,
				editable		: false,
				displayField	: 'name',
				valueField		: 'value',
				value			: '',
				store: Ext.create('Ext.data.Store', {
					fields : ['name', 'value'],
					data   : [
						{name: Otm.id_rule.no_select, value: ''},
						{name: '-', value: '-'},
						{name: '_', value: '_'}
					]
				}),
				listeners: {
					change: change_rule_display
				}
			});

	items.push({
				xtype			: 'combo',
				fieldLabel		: Otm.id_rule.date_type,
				emptyText		: Otm.id_rule.date_type_select,
				id				: type+'_date',
				triggerAction	: 'all',
				forceSelection	: true,
				editable		: false,
				displayField	: 'name',
				valueField		: 'value',
				value			: '',
				store: Ext.create('Ext.data.Store', {
					fields : ['name', 'value'],
					data   : [
						{name: Otm.id_rule.no_select, value: ''},
						{name: 'yyyy-mm-dd', value: 'yyyy-mm-dd'},
						{name: 'yy-mm-dd', value: 'yy-mm-dd'},
						{name: 'mm-dd', value: 'mm-dd'},
						{name: 'yyyy-mm-dd DayOfWeek', value: 'yyyy-mm-dd DayOfWeek'},
						{name: 'yy-mm-dd DayOfWeek', value: 'yy-mm-dd DayOfWeek'},
						{name: 'mm-dd DayOfWeek', value: 'mm-dd DayOfWeek'}
					]
				}),
				listeners: {
					change: change_rule_display
				}
			});

	items.push({
				xtype			: 'combo',
				fieldLabel		: Otm.id_rule.separator_select,
				emptyText		: Otm.id_rule.separator_select,
				id				: type+'_sp2',
				triggerAction	: 'all',
				forceSelection	: true,
				editable		: false,
				displayField	: 'name',
				valueField		: 'value',
				value			: '',
				store: Ext.create('Ext.data.Store', {
					fields : ['name', 'value'],
					data   : [
						{name: Otm.id_rule.no_select, value: ''},
						{name: '-', value: '-'},
						{name: '_', value: '_'}
					]
				}),
				listeners: {
					change: change_rule_display
				}
			});

	items.push({
				xtype			: 'combo',
				fieldLabel		: Otm.id_rule.number_type,
				emptyText		: Otm.id_rule.mumber_type_select,
				id				: type+'_number',
				triggerAction	: 'all',
				forceSelection	: true,
				editable		: false,
				displayField	: 'name',
				valueField		: 'value',
				value			: '3',
				store: Ext.create('Ext.data.Store', {
					fields : ['name', 'value'],
					data   : [
						{name: '3', value: '3'},
						{name: '4', value: '4'},
						{name: '5', value: '5'},
						{name: '6', value: '6'}
					]
				}),
				listeners: {
					change: change_rule_display
				}
			});

	items.push({
				xtype: 'checkboxfield',
				fieldLabel: Otm.com_default_value,
				id	: type+'_is_default'
			});

	items.push({xtype : 'hiddenfield', id : type+'_default_value'});

	var form_id = 'code_'+type+'_form';

	var form = Ext.create("Ext.form.Panel",{
		region		: 'east',
		xtype		: 'panel',
		title		: title,
		id			: form_id,
		type		: type,
		flex		: 1,
		autoScroll	: true,
		collapsible	: true,
		collapsed	: false,
		animation	: false,
		border		: false,
		fieldDefaults: {
			labelAlign:'left',
			labelWidth:155
		},
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
				check_id_rule(type);
			}
		}]
	});

	return form;
};

var tc_id_rule_grid = get_grid('tc_id_rule');
var tc_id_rule_form_panel = get_form('tc_id_rule');

var df_id_rule_grid = get_grid('df_id_rule');
var df_id_rule_form_panel = get_form('df_id_rule');

var tc_id_rule_panel = {
	layout	: 'border',
	xtype	: 'panel',
	title	: Otm.id_rule.tc_id_rule,
	flex	: 1,
	height	: 330,
	items	:[tc_id_rule_grid,tc_id_rule_form_panel]
};

var df_id_rule_panel = {
	layout	: 'border',
	xtype	: 'panel',
	title	: Otm.id_rule.df_id_rule,
	flex	: 1,
	height	: 350,
	items	:[df_id_rule_grid,df_id_rule_form_panel]
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
	items	:[tc_id_rule_panel,df_id_rule_panel]
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
	Ext.getCmp('id_rule').add(main_panel);
	Ext.getCmp('id_rule').doLayout();
});

</script>