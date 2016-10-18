<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script type="text/javascript">
function get_select_project_tree_node(){
	var project_treePanel = Ext.getCmp('project_treePanel');
	var node = project_treePanel.getSelectionModel().getSelection();
	return node;
}

/**
	project members
*/
var projectsetup_user_store = Ext.create('Ext.data.Store', {
	fields:['pm_seq','rp_seq','mb_seq','mb_email','mb_name','mb_pw','mb_tel','mb_is_admin','mb_is_approved','writer','regdate','last_writer','last_update','user_group_name','user_role_name'],
    proxy: {
		type	: 'ajax',
		url		: './index.php/Project_setup/project_userlist',
		actionMethods : {
            create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
        },
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
    }
});
var projectsetup_userlist_grid = Ext.create("Ext.grid.Panel",{
	region:'center',
	layout: 'fit',
	title : Otm.pjt+' '+Otm.com_userlist,
	id:'projectsetup_userlist_grid',
	store: projectsetup_user_store,
	border:false,
	verticalScrollerType:'paginggridscroller',
	invalidateScrollerOnRefresh:true,
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
				var node = get_select_project_tree_node();
				if(node && node[0].data.pr_seq){
	               this.store.load({params:{pr_seq:node[0].data.pr_seq}});
				}
            }
		}
	},
	columns: [
		{xtype: 'rownumberer',width: 30,sortable: false},
		{header: Otm.com_name,			dataIndex: 'mb_name', autoResizeWidth: true},//flex: 1,
		{header: Otm.com_group,			dataIndex: 'user_group_name',	width:150},
		{header: Otm.com_role,			dataIndex: 'user_role_name',	width:150},
		{header: Otm.com_email,			dataIndex: 'mb_email',		flex: 1},
		{header: Otm.com_admin,			dataIndex: 'mb_is_admin', align:'center',	width:80}
	],
	listeners:{
		scope:this,
		select: projectsetup_userlist_grid_select
	}
});

/**
	system users
*/
var node = get_select_project_tree_node();
var projectsetup_system_user_store = Ext.create('Ext.data.Store', {
	fields:['mb_email','mb_name','mb_pw','mb_tel','mb_is_admin','mb_is_approved','writer','regdate','last_writer','last_update','user_group_name'],
	pageSize: 50,
	proxy: {
		type	: 'ajax',
		url		: './index.php/User/userlist',
		actionMethods : {
			create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
		},
		extraParams: {pr_seq : node[0].data.pr_seq},
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
	}
});

var group_store = Ext.create('Ext.data.Store', {
	fields:['gr_seq','gr_name',{name:'gr_user_cnt',type:'integer'},'gr_content','writer','regdate','last_writer','last_update'],
	proxy: {
		type	: 'ajax',
		url		: './index.php/Group/grouplist',
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
	,autoLoad:true
});

var user_searchField_store = new Ext.data.SimpleStore({
	 fields:['Key', 'Name']
	,data:[['mb_name', Otm.com_name],['mb_email', Otm.com_email],['mb_group', Otm.com_group]]
});

var user_searchForm = Ext.create("Ext.form.Panel",{
	id : 'user_searchForm',
	xtype: 'container',
	layout: 'hbox',
	border:false,
	margin: '0 10px 0 0',
	items:[{
		xtype: 'combo',
		width:'100px',
		name: 'user_sfl',id: 'user_sfl',
		editable: false,
		displayField: 'Name',
		valueField:'Key',
		store:user_searchField_store,
		minChars: 0,
		allowBlank : false,
		queryParam: 'q',
		queryMode: 'local',
		value:'mb_name',
		listeners : {
			scope:this,
			select:function(combo, record, index ){
				switch(record[0].data.Key){
					case "mb_name":
					case "mb_email":
						Ext.getCmp("user_stx").show(true);
						Ext.getCmp("user_group").hide(true);
						Ext.getCmp("user_search_btn").show(true);
					break;
					case "mb_group":
						Ext.getCmp("user_stx").hide(true);
						Ext.getCmp("user_group").show(true);
						Ext.getCmp("user_search_btn").hide(true);
					break;
				}
			}
		}
	},{
		xtype: 'textfield',
		style:'margin-left:3px;',
		name: 'user_stx',id: 'user_stx',
		flex: 1,
		allowBlank: false,
		hidden:false,
		enableKeyEvents: true,
		listeners: {
            keyup: function (string, e) {
				if(e.keyCode == 13){
					var sfl = Ext.getCmp("user_sfl").getValue();
					var stx = Ext.getCmp("user_stx").getValue();

					var node = get_select_project_tree_node();
					var params = {
							pr_seq		: node[0].data.pr_seq,
							'searchField':sfl,'searchText':stx
						};

					if(sfl && stx){
						projectsetup_system_user_store.loadPage(1,{
							params :params
						});
					}else{
						if(sfl){
							Ext.Msg.alert("OTM",Otm.com_msg_please_search_keyword);
							Ext.getCmp("user_stx").focus();
						}
					}
				}
			}
		}
	},{
		xtype:'combo',
		id:'user_group',
		style:'margin-left:3px;',
		store: group_store,
		valueField:'gr_seq',
		hidden:true,
		displayField:'gr_name',
		mode: 'local',
		width:200,
		typeAhead     : false,
		editable      : false,
		forceSelection: false,
		selectOnFocus : false,
		triggerAction : 'all',
		listeners : {
			scope:this,
			select:function(combo, record, index ){
				var sfl = Ext.getCmp("user_sfl").getValue();
				var group = Ext.getCmp("user_group").getValue();
				var node = get_select_project_tree_node();

				projectsetup_system_user_store.loadPage(1,{params:{pr_seq:node[0].data.pr_seq,'searchType':sfl,'group':group}});
			}
		}
	},{
		xtype:'button',
		style:'margin-left:3px;',
		text:Otm.com_search,
		id:'user_search_btn',
		iconCls:'ico-search',
		handler:function(btn){
			var sfl = Ext.getCmp("user_sfl").getValue();
			var stx = Ext.getCmp("user_stx").getValue();

			var node = get_select_project_tree_node();
			var params = {
					pr_seq		: node[0].data.pr_seq,
					'searchField':sfl,'searchText':stx
				};

			if(sfl && stx){
				projectsetup_system_user_store.loadPage(1,{
					params :params
				});
			}else{
				if(sfl){
					Ext.Msg.alert("OTM",Otm.com_msg_please_search_keyword);
					Ext.getCmp("user_stx").focus();
				}
			}
		}
	},{
		xtype:'button',
		style:'margin-left:3px;',
		text:Otm.com_reset,
		iconCls:'ico-reset',
		handler:function(btn){
			user_searchForm.reset();
			Ext.getCmp("user_stx").show(true);
			Ext.getCmp("user_group").hide(true);
			Ext.getCmp("user_search_btn").show(true);

			var node = get_select_project_tree_node();
			var params = {
					pr_seq		: node[0].data.pr_seq
				};

			projectsetup_system_user_store.loadPage(1,{
				params: params
			});

		}
	}]
});

var projectsetup_system_userlist_grid = Ext.create("Ext.grid.Panel",{
	region:'north',
	flex: 1,
	title : Otm.com_all+' '+Otm.com_userlist,
	id:'projectsetup_system_userlist_grid',
	store: projectsetup_system_user_store,
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
				var node = get_select_project_tree_node();
				if(node && node[0].data.pr_seq){
	               this.store.load({params:{pr_seq:node[0].data.pr_seq}});
				}
			}
		}
	},
	border:false,
	columns: [
		{xtype: 'rownumberer',width: 30,sortable: false},
		{header: Otm.com_name,			dataIndex: 'mb_name',		width:100},
		{header: Otm.com_group,			dataIndex: 'user_group_name',	flex: 1},
		{header: Otm.com_email,			dataIndex: 'mb_email',		flex: 1},
		{header: Otm.com_admin,			dataIndex: 'mb_is_admin', align:'center',	width:50}
	],
	bbar:Ext.create('Ext.PagingToolbar', {
		store: projectsetup_system_user_store,
		displayInfo: true,
		preprendButtons: false,
		baseParams : {pr_seq : 2},
		listeners: {
			afterrender: function() {
				this.child('#refresh').hide();
			}
		}
	}),
	tbar:[user_searchForm]
});

/**
	system role
*/
var projectsetup_system_role_store = Ext.create('Ext.data.Store', {
	fields:['rp_seq','rp_name','writer','regdate','last_writer','last_update'],
	proxy: {
		type	: 'ajax',
		url		: './index.php/Role/role_list',
		actionMethods : {
			create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
		},
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data',
			idProperty:'rp_seq'
		}
	}
});

var projectsetup_system_role_grid = Ext.create("Ext.grid.Panel",{
	region:'center',
	title : Otm.com_role,
	id:'projectsetup_system_role_grid',
	store: projectsetup_system_role_store,
	border:false,
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
		Ext.create('Ext.grid.RowNumberer'),
		{header: Otm.com_role,	dataIndex: 'rp_name',	flex: 1}
	]
});


function projectsetup_select_grid_item(){
	var grid = Ext.getCmp("projectsetup_userlist_grid");
	if(grid.getSelectionModel().selected.length >= 1){
		var selItem = grid.getSelectionModel().selected.items[0];
		return selItem;
	}else{
	}
}

function projectsetup_user_reset(){
	Ext.getCmp('projectsetup_system_userlist_grid').enable(true);
	Ext.getCmp('projectsetup_system_userlist_grid').getSelectionModel().deselectAll(true);
	Ext.getCmp('projectsetup_system_role_grid').getSelectionModel().deselectAll(true);
	Ext.getCmp('projectsetup_userlist_grid').getSelectionModel().deselectAll(true);
}

function projectsetup_userlist_grid_select()
{
	var selItem = projectsetup_select_grid_item();
	if(selItem){
		Ext.getCmp('projectsetup_east_panel').expand();

		var role_seq = selItem.data.rp_seq.split(",");

		var user_grid = Ext.getCmp('projectsetup_system_userlist_grid');
		var user_store = user_grid.getStore();

		user_store.reload({
			scope:this,
			callback:function() {
				for(var i=0; i<role_seq.length; i++){
					var mb_email = selItem.data.mb_email;
					var rowIndex = user_store.find('mb_email', mb_email);
					user_grid.getView().select(rowIndex,true);
				}
			}
		})

		var role_grid = Ext.getCmp('projectsetup_system_role_grid');
		var role_store = role_grid.getStore();
		role_store.reload({
			scope:this,
			callback:function() {
				for(var i=0; i<role_seq.length; i++){
					var rp_seq = role_seq[i].replace(/(^\s*)|(\s*$)/gi, "");

					var rowIndex = role_store.find('rp_seq', rp_seq);
					role_grid.getView().select(rowIndex,true);
				}
			}
		})
	}else{
		Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
	}
}

var projectsetup_center_panel = {
	region	: 'center',
	layout	: 'fit',
	xtype	: 'panel',
	layout	: 'border',
	title	: Otm.com_member,
	split	: false,
	collapsible	: false,
	collapsed	: false,
	animation: false,
	tbar	: [
		{xtype: 'button', text: Otm.com_remove, iconCls:'ico-remove', handler: function() {
			var selItem = projectsetup_select_grid_item();
			if(selItem){
				Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
					if(bt=='yes'){
						var url = './index.php/Project_setup/delete_user';
						Ext.Ajax.request({
							url : url,
							params :{
								pm_seq		: selItem.data.pm_seq
							},
							method: 'POST',
							success: function ( result, request ) {
								if(result.responseText=="ok"){
									Ext.getCmp('projectsetup_userlist_grid').getStore().reload();
									Ext.getCmp('projectsetup_system_userlist_grid').getStore().reload();

									projectsetup_user_reset();
								}else{
									Ext.Msg.alert("OTM",result.responseText);
								}
							},
							failure: function ( result, request ) {
								alert("fail");
							}
						});
					}else{
						return;
					}
				})
			}else{
				Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
			}
		}},
		{xtype: 'button', text: 'Export',	hidden:true, handler: function() {
		}},
	],
	items	: [projectsetup_userlist_grid]
};

var projectsetup_east_panel = Ext.create("Ext.form.Panel",{
	region	: 'east',
	layout	: 'border',
	xtype	: 'panel',
	id		: 'projectsetup_east_panel',
	defaults	: {
		collapsible	: false,
		split		: true,
		bodyStyle	: 'padding:0px'
	},
	flex: 1,
	animation: false,
	items	:[projectsetup_system_userlist_grid,projectsetup_system_role_grid],
	buttons:[{
		text:Otm.com_save,
		iconCls:'ico-save',
		handler:function(btn){

			var userlist = Array();
			var rolelist = Array();

			var selItem = projectsetup_select_grid_item();
			if(selItem){
			}else{
				var Records = Ext.getCmp("projectsetup_system_userlist_grid").getSelectionModel().selected.items;
				if(Records.length >= 1){
					for(var i=0; i<Records.length; i++){
						userlist.push(Records[i].data['mb_email']);
					}
				}else{
					Ext.Msg.alert("OTM",Otm.com_msg_select_user);
					return;
				}
			}

			var Records = Ext.getCmp("projectsetup_system_role_grid").getSelectionModel().selected.items;
			if(Records.length >= 1){
				for(var i=0; i<Records.length; i++){
					rolelist.push(Records[i].data['rp_seq']);
				}
			}else{
				Ext.Msg.alert("OTM",Otm.com_msg_select_role);
				return;
			}

			var node = get_select_project_tree_node();

			var url = './index.php/Project_setup/create_user';
			var params = {
					pr_seq		: node[0].data.pr_seq,
					userlist	: Ext.encode(userlist),
					rolelist	: Ext.encode(rolelist)
				};

			if(selItem){
				url = './index.php/Project_setup/update_user';
				params.pm_seq	= selItem.data.pm_seq;
			}

			Ext.Ajax.request({
				url		: url,
				params	: params,
				method	: 'POST',
				success	: function ( result, request ) {
					if(result.responseText=="ok"){
						Ext.getCmp('projectsetup_userlist_grid').getStore().reload();
						Ext.getCmp('projectsetup_system_userlist_grid').getStore().reload();

						projectsetup_user_reset();
					}else{
						Ext.Msg.alert("OTM",result.responseText);
					}
				},
				failure: function ( result, request ) {
					alert("fail");
				}
			});
		}
	},{
		text:Otm.com_reset,
		iconCls:'ico-reset',
		hidden:true,
		handler:function(btn){
			projectsetup_user_reset();
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
		items		: [projectsetup_center_panel,projectsetup_east_panel]
	};
	Ext.getCmp('project_setup_user').add(main_panel);
	Ext.getCmp('project_setup_user').doLayout();
});
</script>