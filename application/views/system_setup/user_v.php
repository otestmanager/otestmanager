<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script>
function is_default_user(type)
{
	var selItem = select_grid_item();
	var defaultMember = new Array('admin@sta.co.kr');


	for(var i=0;i<defaultMember.length;i++){
		if(defaultMember[i] == selItem.data.mb_email){
			return "no";
		}
	}
	return "";
}

var user_userlist_grid = Ext.create("Ext.grid.Panel",{
	region:'center',
	layout: 'fit',
	title : Otm.com_memberlist,
	id:'user_userlist_grid',
	store: user_store,
	border:false,
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
		{header: Otm.com_name,			dataIndex: 'mb_name',		align:'center', width:100},
		{header: Otm.com_email,			dataIndex: 'mb_email',		flex: 1},
		{header: Otm.com_admin,			dataIndex: 'mb_is_admin',	align:'center', width:80},
		{header: Otm.com_approve,			dataIndex: 'mb_is_approved', align:'center', width:80},
		{header: Otm.com_regist_date,			dataIndex: 'regdate', align:'center', width:150},
		{header: Otm.com_modified,	dataIndex: 'last_update', align:'center',	width:150}
	],
	bbar:Ext.create('Ext.PagingToolbar', {
		store: user_store,
		displayInfo: true
	}),
	listeners:{
		scope:this,
		select: function(smObj, rowIndex, record) {
			set_user_data();
		},
		deselect:function(){
		}
	}
});

var search_form = {
		region	: 'north',
		split	: false,
		collapsible	: false,
		collapsed	: false,
		animation: false,
		xtype	: 'fieldset',
		title	: 'Search',
		height	: 80,
		defaults: {
			labelWidth: 50,
			anchor: '100%',
			layout: {
				type: 'hbox',
				defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
			}
		},
		items: [{
			xtype: 'fieldcontainer',
			combineErrors: false,
			defaults: {
				hideLabel: false
			},
			items: [{
				flex: 1,
				xtype			: 'combo',
				mode			: 'local',
				triggerAction	: 'all',
				forceSelection	: true,
				id				: 'user_search_status',
				editable		: false,
				fieldLabel		: Otm.com_status,
				displayField	: 'name',
				valueField		: 'value',
				value			: '0',
				queryMode		: 'local',
				store			: Ext.create('Ext.data.Store', {
					fields : ['name', 'value'],
					data   : [
						{name : Otm.com_all,		value: '0'},
						{name : Otm.com_approve,		value: 'Y'},
						{name : Otm.com_unapproved,	value: 'N'}
					]
				}),
				listeners: {
					scope : this,
					select:function(combo,record,index){

						var params = {
								status		: Ext.getCmp("user_search_status").getValue()
							};

						if(Ext.getCmp("user_search_searchfield").getValue() != null && Ext.getCmp("user_search_searchtext").getValue() != ""){
							params.group = Ext.getCmp("user_search_group").getValue();
							params.searchField = Ext.getCmp("user_search_searchfield").getValue();
							params.searchText = Ext.getCmp("user_search_searchtext").getValue();
						}

						Ext.getCmp('user_userlist_grid').getStore().loadPage(1,{
							params: params
						});
					}
				}
			},{
				xtype: 'displayfield',
				margin: '0 0 0 30',
				value: ''
			},{
				flex: 1,
				xtype			: 'combo',
				mode			: 'local',
				hidden			:true,
				triggerAction	: 'all',
				forceSelection	: true,
				editable		: false,
				fieldLabel		: Otm.com_group,
				id				: 'user_search_group',
				displayField	: 'gr_name',
				valueField		: 'gr_seq',
				store			: group_store
			}]
		},
		{
			xtype : 'fieldcontainer',
			combineErrors: true,
			msgTarget: 'side',
			fieldLabel: Otm.com_member,
			defaults: {
				hideLabel: true
			},
			items : [{
				flex: 1,
				xtype:          'combo',
				mode:           'local',
				triggerAction:  'all',
				forceSelection: true,
				editable:       false,
				id				: 'user_search_searchfield',
				displayField:   'name',
				valueField:     'value',
				queryMode: 'local',
				store:          Ext.create('Ext.data.Store', {
					fields : ['name', 'value'],
					data   : [
						{name : Otm.com_name,   value: 'mb_name'},
						{name : Otm.com_email,  value: 'mb_email'}
					]
				})
			},{
				xtype: 'displayfield',
				margin: '0 0 0 10',
				value: ''
			},
			{
				xtype	: 'textfield',
				flex	: 1,
				id		: 'user_search_searchtext',
				listeners	: {
					'render' : function(cmp) {
						cmp.getEl().on('keypress', function(e) {
							if (e.getKey() == e.ENTER) {
								if(Ext.getCmp("user_search_searchfield").getValue() == null){
									Ext.Msg.alert("OTM",Otm.com_msg_please_search_con);
									return;
								}
								if(Ext.getCmp("user_search_searchtext").getValue() == ""){
									Ext.Msg.alert("OTM",Otm.com_msg_please_search_word);
									Ext.getCmp("user_search_searchtext").focus();
									return;
								}

								Ext.getCmp('user_userlist_grid').getStore().loadPage(1,{
									params: {
										status		: Ext.getCmp("user_search_status").getValue(),
										group		: Ext.getCmp("user_search_group").getValue() ,
										searchField	: Ext.getCmp("user_search_searchfield").getValue(),
										searchText	: Ext.getCmp("user_search_searchtext").getValue(),
										start:0,page:1,limit:50
									}
								});
							}
						});
					}
				}
			},{
				xtype: 'displayfield',
				margin: '0 0 0 10',
				value: ''
			},
			{
				xtype: 'button',
				flex : 1,
				width : 50,
				text:Otm.com_search,
				iconCls:'ico-search',
				margins: '5',
				handler:function(btn){
					if(Ext.getCmp("user_search_searchfield").getValue() == null){
						Ext.Msg.alert("OTM",Otm.com_msg_please_search_con);
						return;
					}
					if(Ext.getCmp("user_search_searchtext").getValue() == ""){
						Ext.Msg.alert("OTM",Otm.com_msg_please_search_word);
						Ext.getCmp("user_search_searchtext").focus();
						return;
					}

					Ext.getCmp('user_userlist_grid').getStore().loadPage(1,{
						params: {
							status		: Ext.getCmp("user_search_status").getValue(),
							group		: Ext.getCmp("user_search_group").getValue() ,
							searchField	: Ext.getCmp("user_search_searchfield").getValue(),
							searchText	: Ext.getCmp("user_search_searchtext").getValue()
						}
					});
				}
			},{
				xtype: 'displayfield',
				margin: '0 0 0 10',
				value: ''
			},{
				xtype: 'button',
				width : 80,
				text:Otm.com_reset,
				iconCls:'ico-reset',
				handler:function(btn){
					Ext.getCmp("user_search_searchfield").setValue('');
					Ext.getCmp("user_search_searchtext").setValue('')
					Ext.getCmp("user_search_status").setValue('0');

					Ext.getCmp('user_userlist_grid').getStore().loadPage(1,{
						params: {
							status		: Ext.getCmp("user_search_status").getValue(),
							group		: Ext.getCmp("user_search_group").getValue() ,
							searchField	: Ext.getCmp("user_search_searchfield").getValue(),
							searchText	: Ext.getCmp("user_search_searchtext").getValue()
						}
					});
				}
			}]
		}]
	};


function set_user_data()
{
	var selItem = select_grid_item();
	if(selItem){
		Ext.getCmp('user_form_panel').expand();
		Ext.getCmp('user_form_panel').setTitle(Otm.com_member+' '+Otm.com_update);
		Ext.getCmp('mb_email').disable();

		Ext.getCmp('mb_pw').setFieldLabel(Otm.com_pw);
		Ext.getCmp('mb_pw_re').setFieldLabel(Otm.com_pwok);
		Ext.getCmp('mb_pw').allowBlank = true;
		Ext.getCmp('mb_pw_re').allowBlank = true;

		Ext.getCmp('mb_email').setValue(selItem.data.mb_email);
		Ext.getCmp('mb_pw').setValue("");
		Ext.getCmp('mb_pw_re').setValue("");

		Ext.getCmp('mb_name').setValue(selItem.data.mb_name);
		Ext.getCmp('mb_tel').setValue(selItem.data.mb_tel);
		Ext.getCmp('mb_is_admin').setValue((selItem.data.mb_is_admin=="Y")?true:false);
		Ext.getCmp('mb_is_approved').setValue((selItem.data.mb_is_approved=="Y")?true:false);
		Ext.getCmp('mb_memo').setValue(selItem.data.mb_memo);

		var returnValue = is_default_user('edit');
		if(returnValue == "no"){
			Ext.getCmp('mb_is_admin').disable();
			Ext.getCmp('mb_is_approved').disable();
		}else{
			Ext.getCmp('mb_is_admin').enable();
			Ext.getCmp('mb_is_approved').enable();
		}

		Ext.getCmp("user_project_panel").getStore().proxy.extraParams = {
			mb_email : selItem.data.mb_email
		};

		Ext.getCmp("user_project_panel").getStore().load();
		Ext.getCmp("user_project_panel").getSelectionModel().deselectAll();
	}
}

function select_grid_item(){
	var grid = Ext.getCmp("user_userlist_grid");
	if(grid.getSelectionModel().selected.length >= 1){
		var selItem = grid.getSelectionModel().selected.items[0];
		return selItem;
	}else{
		Ext.Msg.alert("OTM","No Select Data");
	}
}

var center_panel = {
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
		{xtype: 'button', text: Otm.com_add, iconCls:'ico-add',	handler: function() {
			Ext.getCmp("user_userlist_grid").getSelectionModel().deselectAll();

			Ext.getCmp('user_form_panel').expand();
			Ext.getCmp('user_form_panel').reset();
			Ext.getCmp('user_form_panel').setTitle(Otm.com_member+' '+Otm.com_add);

			Ext.getCmp('mb_pw').setFieldLabel(Otm.com_pw+'(*)');
			Ext.getCmp('mb_pw_re').setFieldLabel(Otm.com_pwok+'(*)');
			Ext.getCmp('mb_pw').allowBlank = false;
			Ext.getCmp('mb_pw_re').allowBlank = false;

			Ext.getCmp('mb_email').enable();
		}},'-',
		{xtype: 'button', text: Otm.com_update, iconCls:'ico-update',	handler: function() {
			set_user_data();
		}},'-',
		{xtype: 'button', text: Otm.com_remove, iconCls:'ico-remove', handler: function() {
			var returnValue = is_default_user('del');

			if(returnValue == "no"){
				Ext.Msg.alert("OTM",Otm.com_msg_default_user_notdel);
				return;
			}else{
				Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
					if(bt=='yes'){
						var selItem = select_grid_item();
						if(selItem){

							var url = './index.php/User/delete_user';
							Ext.Ajax.request({
								url : url,
								params :{
									mb_email		: selItem.data.mb_email,
									mb_pw			: selItem.data.mb_pw,
									mb_pw_re		: selItem.data.mb_pw_re
								},
								method: 'POST',
								success: function ( result, request ) {
									if(result.responseText=="ok"){
										Ext.getCmp('user_userlist_grid').getStore().reload();
										Ext.getCmp('user_form_panel').reset();
										Ext.getCmp('user_form_panel').setTitle(Otm.com_member+' '+Otm.com_add);
										Ext.getCmp('mb_email').enable();
									}else{
										Ext.Msg.alert("OTM",result.responseText);
									}
								},
								failure: function ( result, request ) {
									Ext.Msg.alert("OTM",result.responseText);
								}
							});
						}
					}else{
						return;
					}
				});
			}
		}}
	],
	items	: [search_form,user_userlist_grid]
};

var user_form_panel = Ext.create("Ext.form.Panel",{
	xtype	: 'fit',
	title	: Otm.com_member+' / '+Otm.com_add+' / '+Otm.com_update,
	id		: 'user_form_panel',
	flex	: 1,
	bodyStyle:'padding:10px;',
	animation: false,
	autoScroll: true,
	minWidth : 450,
	items	:[{
		xtype: 'textfield',
		fieldLabel: Otm.com_email+'(*)',
		width:'100%',
		minLength:5,
		maxLength:100,
		vtype:'email',
		id:'mb_email',
		allowBlank: false
	},{
		xtype: 'textfield',
		fieldLabel: Otm.com_pw,
		id:'mb_pw',
		minLength:6,
		maxLength:30,
		width:'100%',
		inputType: 'password',
		allowBlank: true
	},{
		xtype: 'textfield',
		fieldLabel: Otm.com_pwok,
		id:'mb_pw_re',
		minLength:6,
		maxLength:30,
		width:'100%',
		inputType: 'password',
		allowBlank: true
	},{
		xtype: 'textfield',
		fieldLabel: Otm.com_name+'(*)',
		minLength:2,
		maxLength:30,
		id:'mb_name',width:'100%',
		allowBlank: false
	},{
		xtype: 'textfield',
		fieldLabel: Otm.com_contact_number,
		width:'100%',
		maxLength:30,
		id:'mb_tel'
	},{
		xtype : 'fieldcontainer',
		combineErrors: true,
		msgTarget: 'side',
		width:'100%',
		defaults: {
			hideLabel: false
		},
		items : [{
				flex: 1,
				xtype: 'checkboxfield',
				id:'mb_is_admin',
				fieldLabel: Otm.com_admin
			},{
				flex: 1,
				xtype: 'checkboxfield',
				id:'mb_is_approved',
				fieldLabel: Otm.com_approve
			}
		]
	},{
		xtype: 'textareafield',
		fieldLabel: Otm.com_description,
		id:'mb_memo',
		width:'100%',
		emptyText: 'Textarea value'
	}],
	buttons:[{
		text:Otm.com_save,
		iconCls:'ico-save',
		disabled: true,
		formBind: true,
		handler:function(btn){
			var url = './index.php/User/create_user';
			if(Ext.getCmp('mb_email').disabled){
				url = './index.php/User/update_user';
			}

			Ext.Ajax.request({
				url : url,
				params :{
					mb_email		: Ext.getCmp('mb_email').getValue(),
					mb_pw			: Ext.getCmp('mb_pw').getValue(),
					mb_pw_re		: Ext.getCmp('mb_pw_re').getValue(),
					mb_name			: Ext.getCmp('mb_name').getValue(),
					mb_tel			: Ext.getCmp('mb_tel').getValue(),
					mb_is_admin		: Ext.getCmp('mb_is_admin').getValue(),
					mb_is_approved	: Ext.getCmp('mb_is_approved').getValue(),
					mb_memo			: Ext.getCmp('mb_memo').getValue()
				},
				method: 'POST',
				success: function ( result, request ) {
					if(result.responseText=="ok"){
						Ext.getCmp('user_userlist_grid').getStore().reload();
						Ext.getCmp('user_form_panel').reset();
						Ext.getCmp('user_form_panel').setTitle(Otm.com_member+' '+Otm.com_add);
						Ext.getCmp('mb_email').enable();
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
			if(Ext.getCmp('mb_email').disabled){
				Ext.getCmp('user_form_panel').setTitle(Otm.com_member+' '+Otm.com_update);
				set_user_data();
			}else{
				Ext.getCmp('user_form_panel').setTitle(Otm.com_member+' '+Otm.com_add);
				Ext.getCmp('user_form_panel').reset();
			}
		}
	}]
});

function set_project_user_update(select_pr_seq,pm_seq,rp_seq)
{
	var set_params = {
		select_pr_seq : select_pr_seq,
		pm_seq : pm_seq,
		rp_seq : rp_seq
	};

	if(typeof Ext.getCmp('project_user_window') == "undefined"){

		var project_role_store = Ext.create('Ext.data.Store', {
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

		var project_role_grid = Ext.create("Ext.grid.Panel",{
			region:'center',
			title : Otm.com_role,
			id:'project_role_grid',
			store: project_role_store,
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

		var project_user_window = Ext.create('Ext.window.Window', {
			title	: Otm.pjt+' '+Otm.com_join+'(user name)',
			id		: 'project_user_window',
			height	: 500,
			width	: 500,
			layout	: 'border',
			defaults	: {
				collapsible	: false,
				split		: true,
				bodyStyle	: 'padding:0px'
			},
			resizable : true,
			modal	: true,
			constrainHeader: true,
			closeAction	: 'hide',
			set_params	: set_params,
			items		: [project_role_grid],
			buttons		:[{
				text:Otm.com_save,
				handler:function(btn){
					var set_params = Ext.getCmp('project_user_window').set_params;

					var select_pr_seq = set_params.select_pr_seq;
					var pm_seq = set_params.pm_seq;
					var rp_seq = set_params.rp_seq;

					var userlist = Array();
					var rolelist = Array();

					var grid = Ext.getCmp("user_userlist_grid");
					var selItem = grid.getSelectionModel().selected.items[0];
					userlist.push(selItem.data.mb_email);

					var role_selection = Ext.getCmp("project_role_grid").getSelectionModel().selected;
					if(Ext.getCmp("project_role_grid").getSelectionModel().selected.length >=1){
						for(var i=0;i<Ext.getCmp("project_role_grid").getSelectionModel().selected.length;i++){
							rolelist.push(Ext.getCmp("project_role_grid").getSelectionModel().selected.items[i].data.rp_seq);
						}
					}else{
						Ext.Msg.alert("OTM",Otm.com_msg_select_role);
						return;
					}

					var params = {
						pr_seq		: select_pr_seq,
						userlist	: Ext.encode(userlist),
						rolelist	: Ext.encode(rolelist)
					};

					var url = './index.php/Project_setup/create_user';
					if(pm_seq){
						url = './index.php/Project_setup/update_user';
						params.pm_seq	= pm_seq;
					}

					Ext.Ajax.request({
						url : url,
						params :params,
						method: 'POST',
						success: function ( result, request ) {
							if(result.responseText){
								if(result.responseText){
									set_user_data();
									project_user_window.close();
								}
							}
						},
						failure: function ( result, request ) {
						}
					});
				}
			},{
				text:Otm.com_close,
				handler:function(btn){
					project_user_window.close();
				}
			}]
		});
	}else{
		Ext.getCmp('project_user_window').set_params = set_params;
	}

	Ext.getCmp('project_user_window').show(undefined,function(){
		var grid = Ext.getCmp("user_userlist_grid");
		if(grid.getSelectionModel().selected.length >= 1){
			var role_grid = Ext.getCmp('project_role_grid');
			var role_store = role_grid.getStore();

			if(rp_seq){
				var role_seq = rp_seq.split(",");

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
				role_grid.getSelectionModel().deselectAll();
			}


			var selItem = grid.getSelectionModel().selected.items[0];
			var newTitle = Otm.pjt+' '+Otm.com_join+'('+selItem.data.mb_name+')';

			Ext.getCmp('project_user_window').setTitle(newTitle);
		}else{
			Ext.Msg.alert("OTM","No Select Data");
		}
	});
}

function set_project_user_del(pm_seq)
{
	if(pm_seq){
		Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
			if(bt=='yes'){
				var url = './index.php/Project_setup/delete_user';
				Ext.Ajax.request({
					url : url,
					params :{
						pm_seq		: pm_seq
					},
					method: 'POST',
					success: function ( result, request ) {
						if(result.responseText=="ok"){
							set_user_data();
						}else{
							Ext.Msg.alert("OTM",result.responseText);
						}
					},
					failure: function ( result, request ) {
						Ext.Msg.alert("OTM","Fail.");
					}
				});
			}else{
				return;
			}
		})
	}else{
		Ext.Msg.alert("OTM","Fail");
	}
}


function get_user_project_panel()
{
	var user_project_panel_store = Ext.create('Ext.data.TreeStore', {
		proxy: {
			type: 'ajax',
			url:'./index.php/Otm/user_project_include_info',
			extraParams: {
			},
			actionMethods : {
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

	var user_project_panel = Ext.create('Ext.tree.Panel', {
		id			: 'user_project_panel',
		width		: 500,
		height		: 300,
		animate		: false,
		enableDD	: true,
		rootVisible	: true,
		store		: user_project_panel_store,
		root: {
			text		: Otm.group,
			seq			: 0,
			type		: 'group',
			draggable	: false,
			expanded	: true,
			pr_startdate : '',
			pr_enddate : '',
			writer_name : '',
			user_cnt : '',
			defect_cnt : '',
			defect_cnt_close : '',
			writer_name : '',
			writer : '',
			regdate : '',
			last_writer : '',
			last_update : ''
		},
		columns: [{
				xtype		: 'treecolumn',
				dataIndex	: 'pg_seq',
				hidden		: true
			},{
				xtype: 'treecolumn',
				text: Otm.pjt_name, dataIndex: 'text',
				minWidth:150, flex: 1
			},{
				text: Otm.com_role, dataIndex: 'rp_name',
				minWidth:150
			},{
				text: 'rp_seq', dataIndex: 'rp_seq',
				minWidth:150,hidden		: true
			},{
				dataIndex: '',	minWidth:150,
				renderer:function(value,index,record){
					if(record.data.type=='project'){
						if(record.data.mb_email){
							if(!record.data.rp_name){
								return "<input type=button style='border:1px solid gray;font-size:10px;' value='"+Otm.pjt+" "+Otm.com_join+"' onclick=\"set_project_user_update('"+record.data.seq+"')\"/>";
							}else{
								return "<input type=button style='border:1px solid gray;font-size:10px;' value='"+Otm.com_update+"' onclick=\"set_project_user_update('"+record.data.seq+"','"+record.data.pm_seq+"','"+record.data.rp_seq+"')\"/> <input type=button style='border:1px solid gray;font-size:10px;' value='"+Otm.pjt+" "+Otm.com_nonattendance+"' onclick=\"set_project_user_del('"+record.data.pm_seq+"')\"/>";
							}
						}
					}else{
						return "";
					}
				}
			}
		],
		listeners:{
			scope:this,
			select: function(smObj, rowIndex, record) {
			},
			deselect:function(){
			}
		}
	});
	user_project_panel.getRootNode().expand();

	var user_project_border_panel = {
		title		: Otm.com_member+' '+Otm.pjt_info,
		border		: false,
		layout		: 'border',
		items : [{
			layout		: 'fit',
			region		: 'center',
			items		: [user_project_panel]
		}]
	}

	return user_project_border_panel;
}

var east_panel = Ext.create('Ext.tab.Panel', {
	region	: 'east',
	xtype	: 'fit',
	minWidth : 100,
	width:(document.body.clientWidth-100)/2,
	maxWidth : (document.body.clientWidth-100)/2,
	collapsible	: false,collapsed	: false,
	plain: true,
	items : [user_form_panel,get_user_project_panel()]
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
	Ext.getCmp('user').add(main_panel);
	Ext.getCmp('user').doLayout();
});
</script>
