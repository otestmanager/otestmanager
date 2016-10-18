<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script>
function userform_grid_select_item(grid_id){
	var grid = Ext.getCmp(grid_id);
	if(grid.getSelectionModel().selected.length >= 1){
		var selItem = grid.getSelectionModel().selected.items[0];
		return selItem;
	}else{
		Ext.Msg.alert("OTM","No Select Data : User Form Item");
	}
}
function set_userform_data(record)
{
	if(record){
		Ext.getCmp('userform_save_type').setValue('update');

		Ext.getCmp('cf_category').setValue({"category":record.data.cf_category});
		Ext.getCmp('cf_name').setValue(record.data.cf_name);
		Ext.getCmp('cf_formtype').setValue(record.data.cf_formtype);

		if(Ext.getCmp('cf_is_required')){
			Ext.getCmp('cf_is_required').setValue((record.data.cf_is_required=="Y")?true:false);
		}
		if(Ext.getCmp('cf_is_display')){
			Ext.getCmp('cf_is_display').setValue((record.data.cf_is_display=="Y")?true:false);
		}
		if(Ext.getCmp('cf_default_value') && record.data.cf_default_value != ""){
			if(record.data.cf_formtype=="datefield"){
				Ext.getCmp('cf_default_value').setValue(new Date(record.data.cf_default_value));
			}else{
				Ext.getCmp('cf_default_value').setValue(record.data.cf_default_value);
			}
		}

		if(record.data.cf_formtype=='combo' || record.data.cf_formtype=='checkbox' || record.data.cf_formtype=='radio'){
			Ext.getCmp('userform_option_grid').getStore().reload({params:{cf_seq:record.data.cf_seq}});
		}

		Ext.getCmp('userform_form_panel').expand();
		Ext.getCmp('userform_form_panel').setTitle(Otm.com_update);
	}
}

var userform_list_store = Ext.create('Ext.data.JsonStore', {
	fields:['cf_seq','cf_name','cf_category','cf_is_required','cf_formtype','cf_default_value','cf_content','writer','regdate','last_writer','last_update'],
    proxy: {
		type: 'ajax',
		url:'./index.php/Userform/userform_list',
		extraParams: {
		},
		actionMethods: {
            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
        },
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
    },
	groupField: 'cf_category'
});

var groupingFeature = Ext.create('Ext.grid.feature.Grouping', {
	groupHeaderTpl: '{[{name} == "ID_TC" ? Otm.tc : Otm.def]} ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})',
	hideGroupedHeader: true,
	startCollapsed: false,
	id: 'restaurantGrouping'
});


var userformlist_grid = {
	layout: 'fit',
	xtype : 'gridpanel',
	id	: 'userform_list_grid',
	store: userform_list_store,
	verticalScrollerType:'paginggridscroller',
	invalidateScrollerOnRefresh:false,
	autoScroll : true,
	//features: [groupingFeature],
	viewConfig: {
		listeners: {
			// auto resize column width
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
	features: [{
        ftype: 'grouping',
        startCollapsed: false,
        groupHeaderTpl: '{rows.length} Item{[values.rows.length > 1 ? "s" : ""]}'
    }],
	columns: [
		{xtype: 'rownumberer',width: 30,sortable: false},
		{header: Otm.com_category,		dataIndex: 'cf_category',	width:100,
			renderer: function(value, metaData, record, rowIndex, colIndex, store){

				for(var i=0; i<plugin_category_store.length; i++){
					if(plugin_category_store[i].inputValue == value){
						return plugin_category_store[i].boxLabel;
					}
				}
				return value;
				/*
				if(value == "ID_TC"){
					return Otm.tc;
				}else if(value == "ID_DEFECT"){
					return Otm.def;
				}else if(value == "ID_REQ"){
					return Otm.requirement;
				}else{
					return value;
				}*/

				if(value == "ID_TC"){
					return Otm.tc;
				}else if(value == "ID_DEFECT"){
					return Otm.def;
				}else if(value == "ID_REQ"){
					return Otm.requirement;
				}else{
					return value;
				}
			}
		},
		{header: Otm.com_user_defined_form+' '+Otm.com_name,dataIndex: 'cf_name', minWidth:150, flex: 1},
		{header: Otm.com_form_type,		dataIndex: 'cf_formtype',		width:100, align:'center'},
		{header: Otm.com_default_value,		dataIndex: 'cf_default_value',	width:100},
		{header: Otm.com_mandatory,		dataIndex: 'cf_is_required',	width:100, align:'center'},
		{header: Otm.com_creator,		dataIndex: 'writer',			width:100, align:'center'},
		{header: Otm.com_date,		dataIndex: 'regdate',			width:130, align:'center'}
	],
	listeners: {
		select: function(item, record, eOpts ){
			if(record){
				set_userform_data(record);
			}
		}
	}
};
var sys_userform_select_action = false;
function sort_list(params, sort_num)
{
	Ext.Ajax.request({
		url		: './index.php/Userform/update_sort_list',
		params	: params,
		method	: 'POST',
		success	: function ( result, request ) {
			sys_userform_select_action = true;

			userform_list_store.reload({
				callback:function(){
					if(sys_userform_select_action){
						Ext.getCmp('userform_list_grid').getSelectionModel().select(sort_num);
						sys_userform_select_action = false;
					}
				}
			});
		},
		failure: function ( result, request ) {
			Ext.Msg.alert("OTM",result.responseText);
		}
	});
}

var center_panel = {
	region	: 'center',
	layout:'fit',
	title	: Otm.com_user_defined_form,
	tbar	: [
		{xtype: 'button', text: Otm.com_add, iconCls:'ico-add',	handler: function() {
			Ext.getCmp('userform_form_panel').reset();
			Ext.getCmp('userform_form_panel').expand();
			Ext.getCmp('userform_form_panel').setTitle(Otm.com_add);
		}},'-',
		{xtype: 'button', text: Otm.com_update, iconCls:'ico-update',	handler: function() {

			var selItem = userform_grid_select_item('userform_list_grid');
			if(selItem){
				set_userform_data(selItem);
			}
		}},'-',
		{xtype: 'button', text: Otm.com_remove, iconCls:'ico-remove', handler: function() {

			var selItem = userform_grid_select_item('userform_list_grid');
				if(selItem){
					Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
						if(bt=='yes'){
							var Records = Ext.getCmp('userform_list_grid').getSelectionModel().selected.items;
							var cf_list = Array();

							if(Records.length >= 1){
								for(var i=0; i<Records.length; i++){
									cf_list.push(Records[i].data['cf_seq']);
								}

								var params = {
										cf_list	: Ext.encode(cf_list)
									};

								Ext.Ajax.request({
									url : './index.php/Userform/delete_userform',
									params : params,
									method: 'POST',
									success: function ( result, request ) {
										if(result.responseText=="ok"){
											Ext.getCmp('userform_list_grid').getStore().reload();
											Ext.getCmp('userform_form_panel').reset();
											Ext.getCmp('userform_form_panel').expand();
											Ext.getCmp('userform_form_panel').setTitle(Otm.com_add);
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
						}else{
							return;
						}
					})
				}
		}},'->',
		{
			text:Otm.com_up,
			iconCls:'ico-up',
			handler:function(btn){
				var selItem = userform_grid_select_item('userform_list_grid');
				if(selItem){
					var sort_num = 0;
					var seq_arr = new Array();
					for(var i=0;i<userform_list_store.data.items.length;i++){
						seq_arr.push(userform_list_store.data.items[i].data.cf_seq);
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
						sort_list(params, sort_num);
					}
				}
			}
		},'-',
		{
			text:Otm.com_down,
			iconCls:'ico-down',
			handler:function(btn){
				var selItem = userform_grid_select_item('userform_list_grid');
				if(selItem){
					var sort_num = 0;
					var seq_arr = new Array();
					for(var i=0;i<userform_list_store.data.items.length;i++){
						seq_arr.push(userform_list_store.data.items[i].data.cf_seq);
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
						sort_list(params, sort_num);
					}
				}
			}
		}
	],
	items	: [userformlist_grid]
};

function  columnsEditor(type){
	return ({
		xtype: type,
		allowBlank:false,
		listeners: ({
			render: function(c) {
			  c.getEl().on({
				keydown:function(e){
					if(!e.isSpecialKey()) return;
					var v=e.getKey();
					if(v != 27) return; //ESC key

					var grid = Ext.getCmp("pastePanel");
					if(!grid) return;
					var cell = grid.selModel.getSelectedCell();
					if(!cell) return;

					var record = grid.getStore().getAt(cell[0]);
					var columnName = grid.getColumnModel().getColumnAt(cell[1]).dataIndex;

					var cellvalue = record.data[columnName];

					var col_value = this.getValue();
						col_value = col_value.replace(/\n/g, '<br>');

					record.set(columnName, col_value);
				},
				scope: c
			  });
			}
		 })
	});
};

function userform_set_from_panel_draw(formtype){

	var textform = {
		xtype	: 'fieldset',
		title	: 'Option',
		items	: [{
			xtype: 'textfield',
			fieldLabel: Otm.com_default_value,
			maxLength:100,
			id:'cf_default_value'
		},{
			flex: 1,
			xtype: 'checkboxfield',
			fieldLabel: Otm.com_mandatory,
			id:'cf_is_required'
		},{
			flex: 1,
			xtype: 'checkboxfield',
			fieldLabel: Otm.com_display_list,
			id:'cf_is_display'
		}]
	};
	var dateform = {
		xtype	: 'fieldset',
		title	: 'Option',
		items	: [{
			xtype: 'datefield',
			format: 'Y-m-d',editable: false,
			fieldLabel: Otm.com_default_value,
			id:'cf_default_value'
		},{
			flex: 1,
			xtype: 'checkboxfield',
			fieldLabel: Otm.com_mandatory,
			id:'cf_is_required'
		},{
			flex: 1,
			xtype: 'checkboxfield',
			fieldLabel: Otm.com_display_list,
			id:'cf_is_display',
			hidden:(Ext.getCmp("cf_category").getValue().category=="ID_TC")?true:false
		}]
	};

	var userform_option_store = Ext.create('Ext.data.Store', {
		fields:['name','is_required'],
		proxy: {
			type: 'ajax',
			url:'./index.php/Userform/option_list',
			extraParams: {
			},
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

	var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToMoveEditor: 1,
        autoCancel: false
    });

	var comboform = {
		xtype	: 'fieldset',
		title	: 'Option',
		items	: [{
		layout	: 'fit',
		xtype	: 'gridpanel',
		title	: Otm.com_detailed_data,
		id		: 'userform_option_grid',
		store	: userform_option_store,
		verticalScrollerType:'paginggridscroller',
		invalidateScrollerOnRefresh:false,
		plugins: [cellEditing],
		viewConfig: {
			listeners: {
				// auto resize column width
				refresh: function(dataView) {
					Ext.each(dataView.panel.columns, function(column) {
					if (column.autoResizeWidth)
						column.autoSize();
					});
				},
				viewready: function(){
				}
			}
		},
		columns: [
			Ext.create('Ext.grid.RowNumberer'),
			{header: Otm.com_detailed_data,	dataIndex: 'name',	flex: 1, editor: columnsEditor('textfield')},
			{header: Otm.com_default_value,		dataIndex: 'is_required',	width:100,
				xtype: 'checkcolumn',
				listeners: {
					checkchange: function(column,rowIndex,checked) {
						var items = Ext.getCmp('userform_option_grid').getStore().data.items;
						if(checked == true){
							for(var i=0; i<items.length; i++){
								if(i==rowIndex){
								}else{
									items[i].set('is_required',false);
								}
							}
						}else{
							items[rowIndex].set('is_required',true);
							Ext.Msg.alert("OTM",Otm.com_msg_default_mandatory);
						}
					}
				}
			}
		],
		tbar	: [
			{xtype: 'button', text: Otm.com_add, iconCls:'ico-add',	handler: function() {
				var storeInfo = Ext.getCmp('userform_option_grid').getStore();
				storeInfo.add({name: '', is_required: false});

			}},'-',
			{xtype: 'button', text: Otm.com_remove, iconCls:'ico-remove', handler: function() {
				var grid = Ext.getCmp("userform_option_grid");
				if(grid.getSelectionModel().selected.length >= 1){
					Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
						if(bt=='yes'){
							var selItem = grid.getSelectionModel().selected.items[0];
							var storeInfo = Ext.getCmp('userform_option_grid').getStore();
							storeInfo.remove(selItem);
						}else{
							return;
						}
					});
				}else{
					Ext.Msg.alert("OTM","No Select Option Data");
				}
			}}
		],
		fbar	: [{xtype:'label',width:'100%',style:'color:red;',text:'*'+Otm.com_msg_default_detailed_data}]
	},{
			flex: 1,
			xtype: 'checkboxfield',
			fieldLabel: Otm.com_mandatory,
			id:'cf_is_required'
		},{
			flex: 1,
			xtype: 'checkboxfield',
			fieldLabel: Otm.com_display_list,
			id:'cf_is_display',
			hidden:(Ext.getCmp("cf_category").getValue().category=="ID_TC")?true:false
		}]
	};

	var treeform = {
		xtype	: 'fieldset',
		title	: 'Option',
		items	: [{
			layout	: 'fit',
			xtype	: 'treepanel',
			id		:'kkkkk',
			height: 300,
			useArrows: true,
			rootVisible: true,
			store: [],
			multiSelect: false,
			singleExpand: false,
			animate: false,
			tbar:[{
				xtype:'textfield',
				id:'aaa'
			},'-',{
				xtype:'button',
				text: Otm.com_add,
				iconCls:'ico-add',
				handler:function(btn){
					var target = Ext.getCmp("kkkkk").selModel.getSelection()[0] || Ext.getCmp("kkkkk").getRootNode();

					node = {
						text: Ext.getCmp("aaa").getValue()
					};

					node = target.appendChild(node);

					// User might want to see what they've just added!
					if (!target.isExpanded()) {
						target.expand(false, function() {
						});
					}
					this.selModel.select(node);
					inputField.reset();
				}
			},'-',{
				xtype:'button',
				text: Otm.com_remove,
				iconCls:'ico-remove',
				handler:function(btn){
					var target = Ext.getCmp("kkkkk").selModel.getSelection()[0]
					target.remove().destroy();
				}
			}]
		},{
			flex: 1,
			xtype: 'checkboxfield',
			fieldLabel: Otm.com_mandatory,
			id:'cf_is_required'
		},{
			flex: 1,
			xtype: 'checkboxfield',
			fieldLabel: Otm.com_display_list,
			id:'cf_is_display',
			hidden:(Ext.getCmp("cf_category").getValue().category=="ID_TC")?true:false
		}]
	}

	Ext.getCmp('userform_option_form_panel').removeAll();
	switch(formtype){
		case 'textfield':
		case 'textarea':
			Ext.getCmp('userform_option_form_panel').add(textform);
			break;
		case 'combo':
		case 'checkbox':
		case 'radio':
			Ext.getCmp('userform_option_form_panel').add(comboform);
			break;
		case 'datefield':
			Ext.getCmp('userform_option_form_panel').add(dateform);
			break;
		case 'tree':
			Ext.getCmp('userform_option_form_panel').add(treeform);
			break;
		default:
			break;
	}
	Ext.getCmp('userform_option_form_panel').doLayout();
}

var userform_category_radiogroup = Ext.create("Ext.form.RadioGroup",{
	xtype: 'radiogroup',
    fieldLabel: Otm.com_category,
	id:'cf_category',
    columns: 3,
    vertical: true,
    items: plugin_category_store,
	/*items: [{
		boxLabel: Otm.tc,			name: 'category', inputValue: 'ID_TC'
	},{
		boxLabel: Otm.def,			name: 'category', inputValue: 'ID_DEFECT', checked: true
	},{
		boxLabel: Otm.requirement,	name: 'category', inputValue: 'ID_REQ'
	}],*/
	listeners:{
		change: function(radiogroup, value) {
			if(value.category=='ID_TC'){
				if(typeof Ext.getCmp("cf_is_display") == 'undefined'){
				}else{
					Ext.getCmp("cf_is_display").setHidden(true);
				}
			}else{
				if(typeof Ext.getCmp("cf_is_display") == 'undefined'){
				}else{
					Ext.getCmp("cf_is_display").setHidden(false);
				}
			}
		}
	}
});

var east_panel = Ext.create("Ext.form.Panel",{
	region	: 'east',
	xtype	: 'panel',
	title	: Otm.com_user_defined_form+' / '+Otm.com_add+' / '+Otm.com_update,
	id		: 'userform_form_panel',
	split	: true,
	collapsible	: true,
	collapsed	: true,
	flex: 1,
	animate: false,
	minWidth : 450,
	defaults	: {
		margin: 5
	},
	items	:[{
		xtype: 'hiddenfield',
		id:'userform_save_type'
	},
		userform_category_radiogroup,
	{
		xtype: 'textfield',
		fieldLabel: Otm.com_user_defined_form+' '+Otm.com_name+'(*)',
		minLength:2,
		maxLength:100,
		id:'cf_name',
		allowBlank: false
	},{
		xtype			: 'combo',
		triggerAction	: 'all',
		forceSelection	: true,
		editable		: false,
		fieldLabel		: Otm.com_form_type+'(*)',
		id				: 'cf_formtype',
		displayField	: 'name',
		valueField		: 'value',
		allowBlank		: false,
		store			: Ext.create('Ext.data.Store', {
			fields : ['name', 'value'],
			data   : [
				{name : 'textfield',value: 'textfield'},
				{name : 'textarea',	value: 'textarea'},
				{name : 'combo',	value: 'combo'},
				/*{name : 'checkbox',	value: 'checkbox'},*/
				{name : 'radio',	value: 'radio'},
				{name : 'datefield',value: 'datefield'}
			]
		}),
		listeners: {
			select: function( combo, records, eOpts ) {
			},
			change: function( combo, select, beforeSelect ) {
				userform_set_from_panel_draw(select);
			}
		}
	},{
		xtype	: 'panel',
		id		: 'userform_option_form_panel',
		border	: false
	}],
	buttons : [{
		text:Otm.com_save,
		iconCls:'ico-save',
		disabled: true,
		formBind: true,
		handler:function(btn){
			var formtype = Ext.getCmp('cf_formtype').getValue();
			var params = {
				cf_name			: Ext.getCmp('cf_name').getValue(),
				cf_category		: Ext.getCmp('cf_category').getValue().category,
				cf_formtype		: Ext.getCmp('cf_formtype').getValue()
			};
			if(Ext.getCmp('cf_is_required')){
				params.cf_is_required	= Ext.getCmp('cf_is_required').getValue();
			}
			if(Ext.getCmp('cf_default_value')){
				params.cf_default_value	= Ext.getCmp('cf_default_value').getValue();
			}
			if(Ext.getCmp('cf_content')){
				params.cf_content		= Ext.getCmp('cf_content').getValue();
			}
			if(Ext.getCmp('cf_is_display')){
				params.cf_is_display	= Ext.getCmp('cf_is_display').getValue();
			}

			var option_data = new Array();
			if(formtype=='combo' || formtype=='checkbox' || formtype=='radio'){
				var items = Ext.getCmp('userform_option_grid').getStore().data.items;
				var check_default_value = false;
				for(var i=0; i<items.length; i++){
					if(items[i].data.is_required == true){
						check_default_value = true;
					}
					if(items[i].data.name == ""){
						Ext.Msg.alert('OTM',Otm.com_msg_opt_detailed_mandatory);
						return;
					}else{
						var item = {
							name: items[i].data.name,
							is_required: (items[i].data.is_required)?'Y':'N'
						};
						option_data.push(item);
					}
				}

				if(check_default_value == false){
					Ext.Msg.alert("OTM",Otm.com_msg_default_mandatory);
					return;
				}

				params.cf_default_value = "";
				params.cf_content = Ext.encode(option_data);
			}

			var url = './index.php/Userform/create_userform';

			if(Ext.getCmp('userform_save_type').getValue()=="update"){

				var selItem = userform_grid_select_item('userform_list_grid');
				if(selItem){
					params.cf_seq = selItem.data.cf_seq
					url = './index.php/Userform/update_userform';
				}else{
					return;
				}
			}

			Ext.Ajax.request({
				url		: url,
				params	: params,
				method	: 'POST',
				success	: function ( result, request ) {
					if(result.responseText=="ok"){
						Ext.getCmp('userform_list_grid').getStore().reload();
						Ext.getCmp('userform_form_panel').reset();
						Ext.getCmp('userform_form_panel').setTitle(Otm.com_add);
					}else{
						Ext.Msg.alert("OTM",result.responseText);
					}
				},
				failure: function ( result, request ) {
					Ext.Msg.alert("OTM",result.responseText);
				}
			});
		}
	},{
		text:Otm.com_reset,
		iconCls:'ico-reset',
		hidden:true,
		handler:function(btn){
			if(Ext.getCmp('userform_save_type').getValue() == "update"){
				var selItem = userform_grid_select_item('userform_list_grid');
				if(selItem){
					set_userform_data(selItem);
				}
			}else{
				Ext.getCmp('userform_form_panel').reset();
				Ext.getCmp('userform_form_panel').setTitle(Otm.com_add);
			}
		}
	}]
});


Ext.onReady(function(){
	var main_panel = {
		layout		: 'border',
		defaults	: {
			collapsible	: false,
			split		: true,
			bodyStyle	: 'padding:0px'
		},
		items		: [center_panel,east_panel]
	};
	Ext.getCmp('userform').add(main_panel);
	Ext.getCmp('userform').doLayout();
});

</script>
