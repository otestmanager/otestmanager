<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script>
var group_grouplist_grid = Ext.create("Ext.grid.Panel",{
	region: 'center',
	layout: 'fit',
	title : Otm.com_grouplist,
	id: 'group_grouplist_grid',
	store: group_store,
	verticalScrollerType:'paginggridscroller',
	invalidateScrollerOnRefresh:false,
	viewConfig: {
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
	columns: [
		{xtype: 'rownumberer',width: 30,sortable: false},
		{header: Otm.com_groupname,dataIndex: 'gr_name',		flex: 1},
		{header: Otm.com_member,dataIndex: 'gr_user_cnt', width:100, align:'center'},
		{header: Otm.com_creator,	dataIndex: 'writer', width:100, align:'center'},
		{header: Otm.com_date,	dataIndex: 'regdate',width:80, align:'center', renderer:function(value,index,record){
			if(value){
				var value = value.substr(0,10);
			}else{
				value = '';
			}
			return value;
		}},
		{header: Otm.com_modifiers,	dataIndex: 'last_writer',width:120, align:'center'},
		{header: Otm.com_modified, dataIndex: 'last_update',autoResizeWidth: true, align:'center',
			width:80, sortable: true, renderer:function(value,index,record){
				if(value){
					var value = value.substr(0,10);
					if(value == '0000-00-00') value = '';
				}else{
					value = '';
				}
				return value;
			}
		}
	],
	listeners: {
		select: function(item, record, eOpts ){
			group_form_data_setting(record);
		}
	}
});

function group_form_data_setting(selItem)
{
	if(selItem.data.gr_seq){
		Ext.getCmp("group_user_grid").getSelectionModel().deselectAll();//clearSelections();

		Ext.getCmp('group_groupuser_grid').getStore().reload({params:{gr_seq:selItem.data.gr_seq}});
		Ext.getCmp('group_east_panel').expand();
		Ext.getCmp('group_east_panel').setTitle(Otm.com_group+''+Otm.com_update);
		Ext.getCmp('gr_name').setValue(selItem.data.gr_name);
		Ext.getCmp('gr_content').setValue(selItem.data.gr_content);
		Ext.getCmp('writer').setValue(selItem.data.writer);
		Ext.getCmp('regdate').setValue(selItem.data.regdate);

		Ext.getCmp('last_writer').setValue(selItem.data.last_writer);
		Ext.getCmp('last_update').setValue(selItem.data.last_update);

		Ext.getCmp("group_groupuser_grid").setTitle(selItem.data.gr_name);
		Ext.getCmp('systemsetup_group_east_userlist_panel').setDisabled(false);
	}
}

function group_select_grid_item(){
	var grid = Ext.getCmp("group_grouplist_grid");
	if(grid.getSelectionModel().selected.length >= 1){
		var selItem = grid.getSelectionModel().selected.items[0];
		return selItem;
	}else{
		Ext.Msg.alert("OTM","No Select Data : Group");
	}
}

var center_panel = {
	region	: 'center',
	layout	: 'border',
	title	: Otm.com_group,
	tbar	: [
		{xtype: 'button', text: Otm.com_add, iconCls:'ico-add',	handler: function() {
			Ext.getCmp('systemsetup_group_east_userlist_panel').setDisabled(true);

			Ext.getCmp("group_grouplist_grid").getSelectionModel().deselectAll();

			Ext.getCmp('group_east_panel').expand();
			Ext.getCmp('group_east_panel').setTitle(Otm.com_group+' '+Otm.com_add);
			Ext.getCmp('group_form_panel').reset();
			Ext.getCmp("group_groupuser_grid").setTitle(Otm.com_member);
			Ext.getCmp('group_groupuser_grid').getStore().reload({params:{gr_seq:''}});
		}},'-',
		{xtype: 'button', text: Otm.com_update, iconCls:'ico-update',	handler: function() {
			var selItem = group_select_grid_item();
			group_form_data_setting(selItem);
		}},'-',
		{xtype: 'button', text: Otm.com_remove, iconCls:'ico-remove', handler: function() {
			var selItem = group_select_grid_item();
			if(selItem){
				Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
					if(bt=='yes'){
						Ext.getCmp('systemsetup_group_east_userlist_panel').setDisabled(true);
						Ext.Ajax.request({
							url : './index.php/Group/delete_group',
							params :{
								gr_seq		: selItem.data.gr_seq
							},
							method: 'POST',
							success: function ( result, request ) {
								if(result.responseText=="ok"){
									Ext.getCmp('group_groupuser_grid').getStore().reload({params:{gr_seq:''}});
									Ext.getCmp('group_grouplist_grid').getStore().reload();
									Ext.getCmp('group_east_panel').setTitle(Otm.com_group+' '+Otm.com_add);
									Ext.getCmp('group_form_panel').reset();
									Ext.getCmp("group_groupuser_grid").setTitle(Otm.com_member);
									Ext.getCmp('group_groupuser_grid').getStore().reload({params:{gr_seq:''}});
								}else{
									Ext.Msg.alert("OTM",result.responseText);
								}
							},
							failure: function ( result, request ) {
								Ext.Msg.alert("OTM",result.responseText);
							}
						});
					}else{
						return;
					}
				})
			}
		}},'-',{
			xtype: 'button', text: Otm.com_export, iconCls:'ico-export', handler: function(){
				export_data('group/group_list_export','');
			}
		}
	],
	items	: [group_grouplist_grid]
};

var east_north_panel = Ext.create("Ext.form.Panel",{
	region	: 'north',
	xtype	: 'panel',
	id		: 'group_form_panel',
	height	: 150,
	bodyStyle	:'padding:10px;',
	collapsible	: false,
	collapsed	: false,
	animation: false,
	defaults: {
		anchor: '100%',
		layout: {
			type: 'hbox',
			defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
		}
	},
	items	:[{
		xtype: 'textfield',
		fieldLabel: Otm.com_groupname+'(*)',
		minLength:2,
		maxLength:50,
		id:'gr_name',
		allowBlank: false
	},{
		xtype: 'textareafield',
		fieldLabel: Otm.com_description,
		id:'gr_content',
		emptyText: 'Textarea value'
	},{
		xtype: 'displayfield',
		fieldLabel: Otm.com_creator,
		hidden:true,
		id:'writer'
	},{
		xtype: 'displayfield',
		fieldLabel: Otm.com_date,
		hidden:true,
		id:'regdate'
	},{
		xtype: 'displayfield',
		fieldLabel: Otm.com_modifiers,
		hidden:true,
		id:'last_writer'
	},{
		xtype: 'displayfield',
		fieldLabel: Otm.com_modified,
		hidden:true,
		id:'last_update'
	}],
	buttons:[{
		text:Otm.com_save,
		iconCls:'ico-save',
		disabled: true,
		formBind: true,
		handler:function(btn){
			var url = './index.php/Group/create_group';
			var params = {
					gr_name		: Ext.getCmp('gr_name').getValue(),
					gr_content	: Ext.getCmp('gr_content').getValue()
				};
			if(Ext.getCmp('writer').getValue()){
				url = './index.php/Group/update_group';

				var selItem = group_select_grid_item();
				if(selItem){
					params.gr_seq = selItem.data.gr_seq;
				}
			}

			Ext.Ajax.request({
				url : url,
				params : params,
				method: 'POST',
				success: function ( result, request ) {
					if(result.responseText=="ok"){
						Ext.getCmp('group_grouplist_grid').getStore().reload();
						Ext.getCmp('group_east_panel').setTitle(Otm.com_group+' '+Otm.com_add);
						Ext.getCmp('group_form_panel').reset();
						Ext.getCmp('group_groupuser_grid').getStore().reload({params:{gr_seq:''}});
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
		text:'Reset',
		hidden:true,
		handler:function(btn){
			var grid = Ext.getCmp("group_grouplist_grid");
			if(grid.getSelectionModel().selected.length >= 1){
				var selItem = group_select_grid_item();
				group_form_data_setting(selItem);
			}else{
				Ext.getCmp('group_form_panel').reset();
				Ext.getCmp('group_east_panel').setTitle(Otm.com_group+' '+Otm.com_add);
				Ext.getCmp('group_groupuser_grid').getStore().reload({params:{gr_seq:''}});
			}
		}
	}]
});

var east_center_panel = {
	region	: 'center',
	layout	: 'fit',
	id		: 'systemsetup_group_east_userlist_panel',
	split	: false,
	disabled	: true,
	collapsible	: false,
	collapsed	: false,
	animation: false,
	xtype	: 'panel',
	items	:[{
		layout		: 'fit',
		xtype		:'tabpanel',
		collapsible	: false,
		activeTab	: 0,
		items: [{
			layout	: 'fit',
			xtype: 'panel',
			title: Otm.com_memberlist,
			border : true,
			listeners:{
				render: function(tab){
				},
				activate : function(tabpanel){

				}
			},
			items:[{
				xtype	: 'panel',
				layout: {
					type	: 'hbox',
					align	: 'stretch'
				},
				anchor	: '100%',
				items	: [{
					flex	: 1,
					xtype : 'gridpanel',
					title : Otm.com_all+' '+Otm.com_member,
					id	: 'group_user_grid',
					store: user_store,
					verticalScrollerType:'paginggridscroller',
					invalidateScrollerOnRefresh:false,
					selModel:Ext.create('Ext.selection.CheckboxModel'),
					viewConfig: {
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
					columns: [
						{xtype: 'rownumberer',width: 30,sortable: false},
						{header: Otm.com_name,	dataIndex: 'mb_name',	autoResizeWidth: true},
						{header: Otm.com_email,	dataIndex: 'mb_email',	flex: 1}
					],
					bbar:Ext.create('Ext.PagingToolbar', {
						store: user_store,
						displayInfo: true
					})
				},{
					layout	: 'vbox',
					xtype	: 'panel',
					border	: false,
					anchor	: '100%',
					lbar: [{ xtype: 'component', flex: 0.5 },
						{
							xtype: 'button',
							iconCls: 'arrow_right',
							handler:function(btn){
								var selItem = group_select_grid_item();
								if(!selItem){
									return;
								}

								var Records = Ext.getCmp("group_user_grid").getSelectionModel().selected.items;
								var userlist = Array();

								if(Records.length >= 1){
									for(var i=0; i<Records.length; i++){
										userlist.push(Records[i].data['mb_email']);
									}

									var params = {
											gr_seq		: selItem.data.gr_seq,
											userlist	: Ext.encode(userlist)
										};

									Ext.Ajax.request({
										url : './index.php/Group/insert_group_user',
										params : params,
										method: 'POST',
										success: function ( result, request ) {
											if(result.responseText=="ok"){
												Ext.getCmp('group_groupuser_grid').getStore().reload({params:params});
												Ext.getCmp('group_grouplist_grid').getStore().reload({
													callback:function(r,b,c,k){
														var store = Ext.getCmp('group_grouplist_grid').getStore();

														for(var i=0;i<store.data.length;i++){
															if(store.data.items[i].data.gr_seq == selItem.data.gr_seq){
																Ext.getCmp('group_grouplist_grid').getSelectionModel().select(i);
															}
														}
													}
												});
												Ext.getCmp('group_form_panel').reset();
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
							}
						},
						{ xtype: 'component', flex: 0.5 }
					]
				},{
					flex	: 1,
					xtype : 'gridpanel',
					title : Otm.com_member,
					id:'group_groupuser_grid',
					store: group_user_store,
					verticalScrollerType:'paginggridscroller',
					invalidateScrollerOnRefresh:false,
					selModel:Ext.create('Ext.selection.CheckboxModel'),
					viewConfig: {
						listeners: {
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
						{header: Otm.com_name,	dataIndex: 'mb_name',	autoResizeWidth: true},
						{header: Otm.com_email,	dataIndex: 'mb_email',	flex: 1}
					],
					tbar	: ['->',{xtype: 'button', iconCls:'ico-remove', text: Otm.com_remove,
						handler: function() {
							var selItem = group_select_grid_item();
							if(!selItem){
								return;
							}
							Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
								if(bt=='yes'){
									var Records = Ext.getCmp("group_groupuser_grid").getSelectionModel().selected.items;
									var userlist = Array();

									if(Records.length >= 1){
										for(var i=0; i<Records.length; i++){
											userlist.push(Records[i].data['mb_email']);
										}
										var params = {
												gr_seq		: selItem.data.gr_seq,
												userlist	: Ext.encode(userlist)
											};
										Ext.Ajax.request({
											url : './index.php/Group/delete_group_user',
											params : params,
											method: 'POST',
											success: function ( result, request ) {
												if(result.responseText=="ok"){
													Ext.getCmp('group_groupuser_grid').getStore().reload({params:params});
													Ext.getCmp('group_grouplist_grid').getStore().reload();
													Ext.getCmp('group_form_panel').reset();
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
					}]
				}]
			}]
		},{
			layout	: 'fit',
			hidden:true,
			xtype: 'panel',
			title: Otm.com_revision_history,
			plain: true,
			scope:this,
			listeners:{
				render: function(tab){
				},
				activate : function(tabpanel){
				}
			},
			items:[{
				xtype:'panel',
				plain: true,
				html:''
			}]
		}]
	}]
};

var east_panel = {
	region	: 'east',
	layout	: 'border',
	xtype	: 'panel',
	title	: Otm.com_group,
	id		: 'group_east_panel',
	split	: true,
	collapsible	: true,
	collapsed	: true,
	flex: 1,
	animation: false,
	minWidth : 450,
	items	:[east_north_panel,east_center_panel]
};

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
	Ext.getCmp('group').add(main_panel);
	Ext.getCmp('group').doLayout();
});

</script>
