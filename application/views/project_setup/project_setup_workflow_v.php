<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script>

var projectsetup_role_store = Ext.create('Ext.data.Store', {
	fields:['rp_seq','rp_name','writer','regdate','permission_data'],
	proxy: {
		type: 'ajax',
		url:'./index.php/Role/role_list',
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
	autoLoad:false
});

var projectsetup_status_store = Ext.create('Ext.data.Store', {
	fields:['pco_seq','pco_name','val'],
	proxy: {
		type: 'ajax',
		url:'./index.php/Project_setup/code_list_workflow/status',
		extraParams: {
			project_seq : <?=$project_seq?>
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
	autoLoad:false
});

var header_info="";
var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
	clicksToMoveEditor: 1,
	autoCancel: false
});

var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
	clicksToMoveEditor: 1,
	autoCancel: false
});

var workflowlist_grid = {
	layout: 'fit',
	xtype : 'gridpanel',
	id:'workflowlist_grid',
	store: projectsetup_status_store,
	verticalScrollerType:'paginggridscroller',
	invalidateScrollerOnRefresh:false,
	disableSelection:true,
	viewConfig: {
	},
	columns: [
		{header: 'Current status', dataIndex: 'pco_name', flex: 1}
	]
};

var center_panel = {
	region : 'center',
	border : false,
	bodyStyle : 'padding:15px',
	title : Otm.com_def_lifecycle,
	items : [{
		xtype			: 'combo',
		mode			: 'local',
		triggerAction	: 'all',
		forceSelection	: true,
		editable		: false,
		allowBlank		: false,
		fieldLabel		: Otm.com_role,
		id				: 'role_form_combo',
		displayField	: 'rp_name',
		valueField		: 'rp_seq',
		queryMode		: 'local',
		store			: projectsetup_role_store,
		listeners: {
			select : function(combo,records,eOpts ){
				projectsetup_status_store.reload({
					params:{rp_seq:combo.getValue()},
					callback: function(r,options,success){
						status_setValue(options);
					}
				});
			}
		}
	},{
		xtype:'panel',border:false,height:20
	},workflowlist_grid],
	buttons:[{
		text:Otm.com_save,
		iconCls:'ico-save',
		formBind: true,
		handler:function(btn){
			var role_value = Ext.getCmp("role_form_combo").getValue();
			if(role_value){
				var changeRecords = projectsetup_status_store.getModifiedRecords();
				var isAction = false;
				var newObj=new Array();

				for(var i=0;i<changeRecords.length;i++){
					for(var j=0;j<header_info.length;j++){
						var rec = changeRecords[i].data;
						var mod = changeRecords[i].modified;
						var field = header_info[j]['value_field'];

						if(typeof mod[field] != "undefined"){
							if(rec[field] != mod[field]){
								isAction = true;
								newObj.push({
									project_seq : <?=$project_seq?>,
									rp_seq		: role_value,
									pco_seq_from: changeRecords[i].data.pco_seq,
									pco_seq_to	: field.replace(/_/gi,''),
									pdw_value	: rec[field]
								});
							}
						}
					}
				}

				if(isAction){
					Ext.Ajax.request({
						scope : this,
						url:'./index.php/Project_setup/update_workflow',
						method : 'POST',
						params :{
							workflow_data	: Ext.encode(newObj)
						},
						success: function ( result, request ) {
							if(result.responseText){
								projectsetup_status_store.reload({
									params:{rp_seq:Ext.getCmp("role_form_combo").getValue()},
									callback: function(r,options,success){
										status_setValue(options);
									}
								});
							}
						},
						failure: function ( result, request ) {}
					});
				}else{
					Ext.Msg.alert("OTM",Otm.com_msg_NotChanged);
				}
			}else{
				Ext.Msg.alert("OTM",Otm.com_msg_NotSelectRole);
			}
		}
	},{
		text:Otm.com_reset,
		iconCls:'ico-reset',
		hidden:true,
		handler:function(btn){
			projectsetup_status_store.reload();
		}
	}]
};

function status_setValue(r)
{
	header_info = r.request._rawRequest.options.scope.reader.rawData.head;

	var group_column = {
		header: 'New statuses allowed', flex: 1, columns: []
	};
	for(var i=0;i<projectsetup_status_store.data.length;i++){
		if(i==0){
			for(var j=0;j<header_info.length;j++){
				var file_name = projectsetup_status_store.data.items[i].data;

				group_column.columns.push({
					header: projectsetup_status_store.data.items[j].data.pco_name,
					dataIndex: ''+header_info[j].value_field+'',
					id: file_name.pco_type+'_'+file_name.pco_seq+''+header_info[j].value_field,
					xtype:'checkcolumn'
				});

			}
		}
	}
	workflowlist_grid.columns.push(group_column);
}


Ext.onReady(function(){
	projectsetup_role_store.reload();
	projectsetup_status_store.reload({
		callback: function(r,options,success){
			status_setValue(options);
			var main_panel = {
				layout		: 'border',
				border		: false,
				defaults	: {
					collapsible	: false,
					split		: true,
					bodyStyle	: 'padding:0px'
				},
				items		: [center_panel]
			};
			Ext.getCmp('project_setup_workflow').add(main_panel);
			Ext.getCmp('project_setup_workflow').doLayout();

			var recordSelected = Ext.getCmp("role_form_combo").getStore().getAt(0);
			Ext.getCmp("role_form_combo").setValue(recordSelected.get('rp_seq'));
			projectsetup_status_store.reload({
				params:{rp_seq:recordSelected.data.rp_seq},
				callback: function(r,options,success){
					status_setValue(options);
				}
			});
		}
	});
});
</script>