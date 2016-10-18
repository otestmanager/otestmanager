<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<style type="text/css">
.x-grid-row .x-grid-cell-inner {
	white-space: normal;
}
.x-grid-row-over .x-grid-cell-inner {
	font-weight: bold;
	white-space: normal;
}
</style>

<script type="text/javascript">
function plugin_migration(url)
{
	Ext.Ajax.request({
		url : url,
		method: 'POST',
		success: function ( result, request ) {
			if(result.responseText){
				Ext.Msg.alert('OTM',result.responseText);

				Ext.getCmp('plugin_core_info').getLoader().load();
				plugin_store.reload();
			}
		},
		failure: function ( result, request ){

		}
	});
}

var plugin_store = Ext.create('Ext.data.Store', {
	fields	: ['name','current_ver','migration_ver','description'],
	proxy	: {
		type	: 'ajax',
		url		: './index.php/Plugin/plugin_list',
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

function plugin_store_reload()
{
	plugin_store.reload({
		callback:function(){
		}
	});
}


var pluginlist_grid_listener = {
	scope:this,
	select: function(smObj, rowIndex, record) {
	},
	deselect:function(){
	}
};

var pluginlist_grid = {
	xtype : 'gridpanel',
	id	: 'pluginlist_grid',
	store: plugin_store,
	verticalScrollerType:'paginggridscroller',
	invalidateScrollerOnRefresh:false,
	autoScroll : true,
	viewConfig: {
		plugins: [{
			pluginId: 'preview',
			ptype: 'preview',
			bodyField: 'version_info',
			expanded: true
		}],
		listeners: {
			viewready: function(){
			}
		}
	},
	columns: [
		{header: Otm.com_name,	dataIndex: 'name', minWidth:150, width: 150},
		{header: Otm.com_description,	dataIndex: 'description', minWidth:80, flex: 1,renderer:function(value, metaData, record, rowIdx, colIdx, store){
			return value;
		}}
	],
	listeners:pluginlist_grid_listener
};

var center_panel = {
	region	: 'center',
	layout	: 'fit',
	title	: 'Plug-In List',
	tbar	: [
		{xtype: 'button', text: Otm.com_add, iconCls:'ico-add',
		hidden : true,
		handler: function() {
			Ext.getCmp('plugin_eastPanel').setTitle(Otm.com_add);
			if(Ext.getCmp('plugin_eastPanel').collapsed==false){
			}else{
				Ext.getCmp('plugin_eastPanel').expand();
			}

			Ext.getCmp('plugin_form').reset();
			Ext.getCmp('pluginlist_grid').getSelectionModel().deselectAll();
		}}
	],
	items	: [pluginlist_grid]
};

var north_panel = {
	region	: 'north',
	layout	: 'fit',
	title	: 'OTM Core Information',
	id		: 'plugin_core_info',
	height	: 245,
	autoScroll	: true,
	bodyStyle	: 'padding:10px;',
	closable: false,
	plain	: false,
	split	: false,
	scope	: this,
	loader	: {
		autoLoad: true,
		loadMask: true,
		params	: {},
		scripts	: true,
		url		: './index.php/Plugin/core_info',
	},
	items	: [],
	tbar:['->',{
		xtype:'button',
		text:'Database BackUp',
		handler:function(btn){
			var url = './index.php/Otm/otm_database_backup';
			Ext.Ajax.request({
				url : url,
				method: 'POST',
				success: function ( result, request ) {
					if(result.responseText){
						var info = Ext.decode(result.responseText);
						Ext.Msg.alert('OTM',info.data);
					}
				},
				failure: function ( result, request ){
					if(result.responseText){
						var info = Ext.decode(result.responseText);
						Ext.Msg.alert('OTM',info.data);
					}
				}
			});
		}
	}]
};

var east_panel = {
	region		: 'east',
	layout		: 'fit',
	xtype		: 'panel',
	title		: Otm.com_plugin_info,
	id			: 'plugin_eastPanel',
	hidden		: true,
	split		: true,
	collapsible	: true,
	collapsed	: true,
	flex		: 1,
	animate		: false,
	minWidth	: 450,
	bodyStyle	: 'padding:10px;',
	items		: []
};

function plugin_view(plugin_name)
{
	var plugin_view_panel = {
		layout	: 'border',
		id		: plugin_name,
		title	: 'plugin_view_panel',
		bodyStyle	: 'padding:10px;',
		closable: false,
		plain	: false,
		split	: false,
		scope	: this,
		loader	: {
			autoLoad: true,
			loadMask: true,
			params	: {},
			scripts	: true,
			url		: './index.php/Plugin_view/'+plugin_name,
		},
		items	: []
	};

	Ext.getCmp('plugin').add(plugin_view_panel);
	Ext.getCmp('plugin').doLayout(true,false);

}

Ext.onReady(function(){
	var main_panel = {
		layout		: 'border',
		id			: 'system_plugin_main_panel',
		defaults	: {
			collapsible	: false,
			split		: true,
			bodyStyle	: 'padding:0px'
		},
		items		: [north_panel,center_panel,east_panel]
	};
	Ext.getCmp('plugin').add(main_panel);
	Ext.getCmp('plugin').doLayout(true,false);

	plugin_store.reload();
});
</script>