<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script>
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
	store: status_store,
	verticalScrollerType:'paginggridscroller',
	invalidateScrollerOnRefresh:false,
	disableSelection:true,
	viewConfig: {
	},
	columns: [
		{header: 'Current status',	dataIndex: 'co_name',	flex: 1}
	]
};

var center_panel = {
	region	: 'center',
	border		: false,
	bodyStyle	: 'padding:15px',
	title	: Otm.com_def_lifecycle,
	items	: [{
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
		store			: role_store,
		listeners: {
			select : function(combo,records,eOpts ){
				status_store.reload({
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
				var changeRecords = status_store.getModifiedRecords();
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
									rp_seq		: role_value,
									co_seq_from	: changeRecords[i].data.co_seq,
									co_seq_to	: field.replace(/_/gi,''),
									dw_value	: rec[field]
								});
							}
						}
					}
				}

				if(isAction){
					Ext.Ajax.request({
						scope : this,
						url:'./index.php/Code/update_workflow',
						method : 'POST',
						params :{
							workflow_data	: Ext.encode(newObj)
						},
						success: function ( result, request ) {
							if(result.responseText){
								status_store.reload({
									params:{rp_seq:Ext.getCmp("role_form_combo").getValue()},
									callback: function(r,options,success){
										status_setValue(options);
										Ext.Msg.alert("OTM",Otm.com_msg_save);
									}
								});
							}
						},
						failure: function ( result, request ) {
							Ext.Msg.alert("OTM",result.responseText);
						}
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
			status_store.reload();
		}
	}]
};
function status_setValue(r)
{
	header_info = r.request._rawRequest.options.scope.reader.rawData.head;

	var group_column = {
		header: 'New statuses allowed', flex: 1, columns: []
	};
	for(var i=0;i<status_store.data.length;i++){
		if(i==0){
			for(var j=0;j<header_info.length;j++){
				var file_name = status_store.data.items[i].data;

				group_column.columns.push({
					header: status_store.data.items[j].data.co_name,
					dataIndex: ''+header_info[j].value_field+'',
					id: file_name.co_type+'_'+file_name.co_seq+''+header_info[j].value_field,
					xtype:'checkcolumn'
				});
			}
		}
	}
	workflowlist_grid.columns.push(group_column);
}


Ext.onReady(function(){
	role_store.reload({
		callback: function(r,options,success){
			status_store.reload({
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
					Ext.getCmp('workflow').add(main_panel);
					Ext.getCmp('workflow').doLayout();

					var recordSelected = Ext.getCmp("role_form_combo").getStore().getAt(0);
					Ext.getCmp("role_form_combo").setValue(recordSelected.get('rp_seq'));
					status_store.reload({
						params:{rp_seq:recordSelected.data.rp_seq},
						callback: function(r,options,success){
							status_setValue(options);
						}
					});
				}
			});
		}
	});

});
</script>