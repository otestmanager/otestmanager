<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script type="text/javascript">

function role_store_reload()
{
	role_store.reload({
		callback:function(){
		}
	});
}

function set_role_data(){
	if(Ext.getCmp("rolelist_grid").getSelectionModel().selected.length >= 1){
		Ext.getCmp("role_eastPanel").setTitle(Otm.com_update);
		if(Ext.getCmp("role_eastPanel").collapsed==false){
		}else{
			Ext.getCmp("role_eastPanel").expand();
		}

		var selItem = Ext.getCmp("rolelist_grid").getSelectionModel().selected.items[0];

		selItem.data.role_seq = selItem.data.rp_seq;
		selItem.data.role_name = selItem.data.rp_name;


		Ext.getCmp("system_role_main_panel").mask("Loading...");


		Ext.Ajax.request({
			url : './index.php/Role/get_permission',
			params :{
				rp_seq			: selItem.data.rp_seq
			},
			method: 'POST',
			success: function ( result, request ) {
				if(result.responseText){
					var permission_data = Ext.decode(result.responseText);

					for(var i=0;i<permission_data.data.length;i++){
						if(Ext.getCmp(permission_data.data[i].pmi_name)){
							if(permission_data.data[i].pmi_value=='1'){
								Ext.getCmp(permission_data.data[i].pmi_name).setValue(1);
							}else{
								Ext.getCmp(permission_data.data[i].pmi_name).setValue(0);
							}
						}
					}

					Ext.getCmp("system_role_main_panel").unmask();
				}
			},
			failure: function ( result, request ) {
				Ext.Msg.alert("OTM",result.responseText);
			}
		});
		setTimeout(function(){
			if (selItem) {
				Ext.getCmp("role_form").loadRecord(selItem);
			}
		},300);
	}else{
		Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
		return;
	}
}


var rolelist_grid_listener = {
	scope:this,
	select: function(smObj, rowIndex, record) {
		set_role_data();
	},
	deselect:function(){
	}
}

var rolelist_grid = {
	layout: 'fit',
	xtype : 'gridpanel',
	id	: 'rolelist_grid',
	store: role_store,
	verticalScrollerType:'paginggridscroller',
	invalidateScrollerOnRefresh:false,
	autoScroll : true,
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
	columns: [
		{xtype: 'rownumberer',width: 30,sortable: false},
		{header: Otm.com_role+' '+Otm.com_name,	dataIndex: 'rp_name', minWidth:150, flex: 1},
		{header: Otm.com_creator,	dataIndex: 'writer', width:150, align:'center'},
		{header: Otm.com_date,	dataIndex: 'regdate', width:150, align:'center'}
	],
	listeners:rolelist_grid_listener
};

var center_panel = {
	region	: 'center',
	layout	: 'fit',
	title	: Otm.com_role_auth,
	tbar	: [
		{xtype: 'button', text: Otm.com_add, iconCls:'ico-add',	handler: function() {
			Ext.getCmp("role_eastPanel").setTitle(Otm.com_add);
			if(Ext.getCmp("role_eastPanel").collapsed==false){
			}else{
				Ext.getCmp("role_eastPanel").expand();
			}

			Ext.getCmp("role_form").reset();
			Ext.getCmp("rolelist_grid").getSelectionModel().deselectAll();
		}},'-',
		{xtype: 'button', text: Otm.com_update, iconCls:'ico-update', hidden:true, handler: function() {
			set_role_data();
		}},
		{xtype: 'button', text: Otm.com_remove, iconCls:'ico-remove', handler: function() {
			if(Ext.getCmp("rolelist_grid").getSelectionModel().selected.length >= 1){
				var selItem = Ext.getCmp("rolelist_grid").getSelectionModel().selected.items[0];
				if(selItem.data.rp_seq == 1){
					Ext.Msg.alert('OTM',Otm.com_msg_delete_default_value);
					return;
				}

				Ext.Msg.confirm('OTM',Otm.com_msg_isdelete_allProject, function (btn) {
					if(btn == "yes") {

						Ext.Ajax.request({
							url : './index.php/Role/delete_role',
							params :{
								rp_seq			: selItem.data.rp_seq
							},
							method: 'POST',
							success: function ( result, request ) {
								if(result.responseText){
									role_store_reload('del');
									if(Ext.getCmp("role_eastPanel").collapsed==false){
										Ext.getCmp("role_eastPanel").setTitle("");
										Ext.getCmp("role_eastPanel").collapse();
										Ext.getCmp("role_form").reset();
									}
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
			}else{
				Ext.Msg.alert("OTM","No Select Data");
				return;
			}
		}}
	],
	items	: [rolelist_grid]
};

var east_panel = {
	region	: 'east',
	layout	: 'fit',
	xtype	: 'panel',
	title	: Otm.com_add,
	id		: 'role_eastPanel',
	split	: true,
	collapsible	: false,
	collapsed	: false,
	flex: 1,
	animate: false,
	minWidth : 450,
	items	:[Ext.create("Ext.form.Panel",{
		xtype		: 'panel',
		autoScroll	: true,
		id			: 'role_form',
		collapsible	: false,
		collapsed	: false,
		animation	: false,
		border		: false,
		bodyStyle	: "padding:10px;",
		margin		: 5,
		items	:[{
			xtype:'hiddenfield',
			id:'role_seq'
		},{
			xtype: 'textfield',
			fieldLabel: Otm.com_role+' '+Otm.com_name+'(*)',
			id:'role_name',
			minLength:2,
			maxLength:50,
			allowBlank: false
		},{
			xtype	: 'fieldset',
			title	: Otm.pjt,
			defaults	: {
				anchor: '100%',
				layout: {
					type: 'hbox',
					defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
				},
				margin	: '5 5 5 5'
			},
			items	: [{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'project_edit',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_update
				},{
					flex: 1,
					id:'project_delete',
					xtype: 'checkboxfield',
					hidden:true,
					fieldLabel: Otm.com_remove
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'project_status',
					xtype: 'checkboxfield',
					hidden:true,
					fieldLabel: 'Close/Reopen'
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex	: 1,
					xtype	: 'label',
					width	: '100%',
					style	: 'color:red;',
					text	: Otm.com_msg_project_update_auth
				}]
			}]
		},{
			xtype	: 'fieldset',
			title	: Otm.def,
			defaults	: {
				anchor: '100%',
				layout: {
					type: 'hbox',
					defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
				},
				margin	: '5 5 5 5'
			},
			items	: [{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'defect_view',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_view
				},{
					flex: 1,
					id:'defect_add',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_add
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'defect_edit',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_update
				},{
					flex: 1,
					id:'defect_delete',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_remove
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'defect_view_all',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_all+' '+Otm.com_view
				},{
					flex: 1,
					id:'defect_edit_all',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_all+' '+Otm.com_update
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'defect_delete_all',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_all+' '+Otm.com_remove
				},{
					flex: 1,
					id:'defect_assign',
					xtype: 'checkboxfield',
					fieldLabel: Otm.def_assignment
				}]
			}]
		},{
			xtype	: 'fieldset',
			title	: Otm.tc,
			defaults	: {
				anchor: '100%',
				layout: {
					type: 'hbox',
					defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
				},
				margin	: '5 5 5 5'
			},
			items	: [{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'tc_view',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_view+"("+Otm.com_user+")"
				},{
					flex: 1,
					id:'tc_add',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_add
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'tc_edit',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_update
				},{
					flex: 1,
					id:'tc_delete',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_remove
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'tc_view_all',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_all+' '+Otm.com_view
				},{
					flex: 1,
					id:'tc_edit_all',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_all+' '+Otm.com_update
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'tc_delete_all',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_all+' '+Otm.com_remove
				},{
					flex: 1,
					id:'tc_result',
					xtype: 'checkboxfield',
					fieldLabel: Otm.tc_execution
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex	: 1,
					xtype	: 'label',
					width	: '100%',
					style	: 'color:red;',
					text	: Otm.com_msg_tc_view_auth
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex	: 1,
					xtype	: 'label',
					width	: '100%',
					style	: 'color:red;',
					text	: Otm.com_msg_tc_allview_auth
				}]
			}]
		},{
			xtype	: 'fieldset',
			title	: Otm.rep,
			items	: [{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					id:'report_view',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_view
				}]
			}]
		},{
			xtype	: 'fieldset',
			title	: Otm.comtc,
			hidden	: true,
			defaults	: {
				anchor: '100%',
				layout: {
					type: 'hbox',
					defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
				},
				margin	: '5 5 5 5'
			},
			items	: [{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'com_tc_view',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_view
				},{
					flex: 1,
					id:'com_tc_add',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_add
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'com_tc_edit',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_update
				},{
					flex: 1,
					id:'com_tc_delete',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_remove
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'com_tc_view_all',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_all+' '+Otm.com_view
				},{
					flex: 1,
					id:'com_tc_edit_all',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_all+' '+Otm.com_update
				}]
			},{
				xtype : 'fieldcontainer',
				combineErrors: true,
				msgTarget: 'side',
				defaults: {
					hideLabel: false
				},
				items : [{
					flex: 1,
					id:'com_tc_delete_all',
					xtype: 'checkboxfield',
					fieldLabel: Otm.com_all+' '+Otm.com_remove
				}]
			}]
		}],
		buttons:[{
			text:Otm.com_save,
			id: 'role_save_btn',
			iconCls:'ico-save',
			disabled: true,
			formBind: true,
			handler:function(btn){

				if(Ext.getCmp('role_seq').getValue() == 1){
					Ext.Msg.alert('OTM',Otm.com_msg_update_default_value);
					return;
				}

				var permission_info = new Array();
				permission_info.push({category:'ID_PROJECT',id:"project_edit",value:Ext.getCmp('project_edit').getValue()});
				permission_info.push({category:'ID_PROJECT',id:"project_delete",value:Ext.getCmp('project_delete').getValue()});
				permission_info.push({category:'ID_PROJECT',id:"project_status",value:Ext.getCmp('project_status').getValue()});

				permission_info.push({category:'ID_DEFECT',id:"defect_view",value:Ext.getCmp('defect_view').getValue()});
				permission_info.push({category:'ID_DEFECT',id:"defect_add",value:Ext.getCmp('defect_add').getValue()});
				permission_info.push({category:'ID_DEFECT',id:"defect_edit",value:Ext.getCmp('defect_edit').getValue()});
				permission_info.push({category:'ID_DEFECT',id:"defect_delete",value:Ext.getCmp('defect_delete').getValue()});
				permission_info.push({category:'ID_DEFECT',id:"defect_view_all",value:Ext.getCmp('defect_view_all').getValue()});
				permission_info.push({category:'ID_DEFECT',id:"defect_edit_all",value:Ext.getCmp('defect_edit_all').getValue()});
				permission_info.push({category:'ID_DEFECT',id:"defect_delete_all",value:Ext.getCmp('defect_delete_all').getValue()});
				permission_info.push({category:'ID_DEFECT',id:"defect_assign",value:Ext.getCmp('defect_assign').getValue()});

				permission_info.push({category:'ID_TC',id:"tc_view",value:Ext.getCmp('tc_view').getValue()});
				permission_info.push({category:'ID_TC',id:"tc_add",value:Ext.getCmp('tc_add').getValue()});
				permission_info.push({category:'ID_TC',id:"tc_edit",value:Ext.getCmp('tc_edit').getValue()});
				permission_info.push({category:'ID_TC',id:"tc_delete",value:Ext.getCmp('tc_delete').getValue()});
				permission_info.push({category:'ID_TC',id:"tc_view_all",value:Ext.getCmp('tc_view_all').getValue()});
				permission_info.push({category:'ID_TC',id:"tc_edit_all",value:Ext.getCmp('tc_edit_all').getValue()});
				permission_info.push({category:'ID_TC',id:"tc_delete_all",value:Ext.getCmp('tc_delete_all').getValue()});
				permission_info.push({category:'ID_TC',id:"tc_result",value:Ext.getCmp('tc_result').getValue()});

				permission_info.push({category:'ID_REPORT',id:"report_view",value:Ext.getCmp('report_view').getValue()});

				permission_info.push({category:'ID_COMTC',id:"com_tc_view",value:Ext.getCmp('com_tc_view').getValue()});
				permission_info.push({category:'ID_COMTC',id:"com_tc_add",value:Ext.getCmp('com_tc_add').getValue()});
				permission_info.push({category:'ID_COMTC',id:"com_tc_edit",value:Ext.getCmp('com_tc_edit').getValue()});
				permission_info.push({category:'ID_COMTC',id:"com_tc_delete",value:Ext.getCmp('com_tc_delete').getValue()});
				permission_info.push({category:'ID_COMTC',id:"com_tc_view_all",value:Ext.getCmp('com_tc_view_all').getValue()});
				permission_info.push({category:'ID_COMTC',id:"com_tc_edit_all",value:Ext.getCmp('com_tc_edit_all').getValue()});
				permission_info.push({category:'ID_COMTC',id:"com_tc_delete_all",value:Ext.getCmp('com_tc_delete_all').getValue()});


				var URL = './index.php/Role/create_role';
				if(Ext.getCmp('role_seq').getValue()){
					URL = './index.php/Role/update_role';
					Ext.Msg.confirm('OTM',Otm.com_msg_isupdate_allProject, function (btn) {
						if(btn == "yes") {
							Ext.Ajax.request({
								url : URL,
								params :{
									role_seq				: Ext.getCmp('role_seq').getValue(),
									role_name				: Ext.getCmp('role_name').getValue(),
									permission				: Ext.encode(permission_info)
								},
								method: 'POST',
								success: function ( result, request ) {
									if(result.responseText == "ok"){
										role_store_reload('add',result.responseText);
										Ext.getCmp("role_form").reset();
										Ext.getCmp("role_eastPanel").setTitle(Otm.com_add);
										Ext.Msg.alert("OTM",Otm.com_msg_save);
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
					});
				}else{
					Ext.Ajax.request({
						url : URL,
						params :{
							role_seq				: Ext.getCmp('role_seq').getValue(),
							role_name				: Ext.getCmp('role_name').getValue(),
							permission				: Ext.encode(permission_info)
						},
						method: 'POST',
						success: function ( result, request ) {
							if(result.responseText == "ok"){
								role_store_reload('add',result.responseText);
								Ext.getCmp("role_form").reset();
								Ext.getCmp("role_eastPanel").setTitle(Otm.com_add);
								Ext.Msg.alert("OTM",Otm.com_msg_save);
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
		},{
			text:Otm.com_reset,
			iconCls:'ico-reset',
			hidden:true,
			handler:function(btn){
				if(Ext.getCmp("role_seq").getValue()){
					set_role_data();
				}else{
					Ext.getCmp("role_form").reset();
				}
			}
		}]
	})]
};

Ext.onReady(function(){
	var main_panel = {
		layout		: 'border',
		id			: 'system_role_main_panel',
		defaults	: {
			collapsible	: false,
			split		: true,
			bodyStyle	: 'padding:0px'
		},
		items		: [center_panel,east_panel]
	};
	Ext.getCmp('role').add(main_panel);
	Ext.getCmp('role').doLayout(true,false);

	role_store.reload();
});
</script>