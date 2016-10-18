<script type="text/javascript">
var mb_email = "<?=$this->session->userdata('mb_email')?>";
var mb_name = "<?=$this->session->userdata('mb_name')?>";
var mb_is_admin = "<?=$this->session->userdata('mb_is_admin')?>";
var dashboard_key = 1;

function project_store_reload(type,seq)
{
	Ext.getCmp('projectGrid').getStore().reload({
		callback:function(){
			if(seq && (type=="add_view" || type=="edit_view")){
				for(var i=0;i<projectlist_store.data.length;i++){
					if(projectlist_store.data.items[i].data.pr_seq == seq){
						Ext.getCmp("projectGrid").getSelectionModel().select(i);
					}
				}
			}
		}
	});
	Ext.getCmp('project_treePanel').getStore().load();
}

function get_plugin_testcase(item){

	Ext.getCmp("acc").mask(Otm.com_msg_processing_data);

	var title = item.parentNode.data.text;
	var tabPanel = Ext.getCmp('main_tab');
	var tabId = 'testcase';

	var tabIndex = tabPanel.items.findIndex('id', tabId);

	if(Ext.getCmp(tabId) && tabIndex != -1){
		tabPanel.remove(Ext.getCmp(tabId));
	}

	var tcplan = item.get('id');

	if(Ext.getCmp(tabId) && tabIndex != -1){
		tabPanel.setActiveTab(tabIndex);
		var params  = {
			project_seq : item.data.pr_seq,
			tcplan		: tcplan
		};
		Ext.getCmp('testcase_treegrid').getStore().load({params:params});
	}else{
		var url = './index.php/Plugin_view/'+tabId;

		tabPanel.add({
			layout:'fit',
			xtype: 'panel',
			title: title,
			id: tabId,
			pr_seq:item.data.pr_seq,
			closable: true,
			plain: true,
			scope:this,
			loader: {
				autoLoad:true,
				loadMask: true,
				params:{
					project_seq : item.data.pr_seq,
					tcplan		: tcplan
				},
				scripts: true,
				url : url
			},
			listeners:{
				render: function(tab){
				},
				activate : function(tabpanel){
				}
			}
		}).show();
	}
}

function get_tabpanel(item){
	var before_pr = Ext.getCmp('before_select_project_tree').getValue();
	if(before_pr){
		Ext.getCmp('before_select_project_tree').setValue(item.get('pr_seq'));

		var tabPanel = Ext.getCmp('main_tab');
		var tab_cnt = tabPanel.items.length;

			var default_tab_cnt = 1 + dashboard_key;
			// 기본 Tab Panel 추가
			//default_tab_cnt = 2;

			for(var i=tab_cnt-1; i>=default_tab_cnt; i--){
			tabPanel.remove(tabPanel.items.getAt(i));
		}
		tabPanel.doLayout();
	}else{
		Ext.getCmp('before_select_project_tree').setValue(item.get('pr_seq'));
	}

	var title = item.get('text');
	var tabPanel = Ext.getCmp('main_tab');
	var tabId = item.get('type');
	var params  = {project_seq:item.data.pr_seq};

	if(tabId == 'testcase_plan'){
		get_plugin_testcase(item);
		return;
	}

	var tabIndex = tabPanel.items.findIndex('id', tabId);

	if(Ext.getCmp(tabId) && tabIndex != -1){
		tabPanel.setActiveTab(tabIndex);
	}else{
		var url = './index.php/Plugin_view/'+tabId;

		var tabId_array  = tabId.split('_');
		if(tabId_array.length >1 && tabId_array[0] !== '' && tabId_array[1] !== ''){
			url = './index.php/Plugin_view/'+tabId_array[0];
			params.subpage = tabId.replace(tabId_array[0]+"_", "");
		}

		if(tabId == "project_setup_main"
			|| tabId == "project_setup_user"
			|| tabId == "project_setup_id_rule"
			|| tabId == "project_setup_code"
			|| tabId == "project_setup_userform"
			|| tabId == "project_setup_workflow"){
			url = './index.php/Project_setup/'+tabId;
		}

		var tabId_array  = tabId.split('_');
		if(tabId_array[0] == 'project' && tabId_array[1] == 'setup'){
			url = './index.php/Project_setup/'+tabId;
		}

		tabPanel.add({
			layout:'fit',
			xtype: 'panel',
			title: title,
			id: tabId,
			pr_seq:item.data.pr_seq,
			closable: true,
			plain: true,
			scope:this,
			loader: {
				autoLoad:true,
				loadMask: true,
				params:params,
				scripts: true,
				url : url
			},
			listeners:{
				render: function(tab){
				},
				activate : function(tabpanel){

				}
			}
		}).show();
	}
}

function getProjectForm(){

	var project_seq = {
		id: 'project_seq',
		anchor: '100%',
		allowBlank : false,
		xtype: 'hiddenfield'
	};
	var subjectForm = {
		id: 'project_name',
		anchor: '100%',
		minLength:2,
		maxLength:100,
		fieldLabel: Otm.pjt_name+'(*)',
		allowBlank : false,
		xtype: 'textfield',
		listeners: {
			afterrender: function(fld) {
				fld.focus(false, 500);
			}
		}
	};

	var startDateForm = {
		id: 'project_startdate',
		fieldLabel: Otm.com_start_date+'(*)',
		format:'Y-m-d',editable: false,
		allowBlank : false,
		endDateField: 'project_enddate',
		vtype: 'daterange',
		xtype: 'datefield'
	};
	var endDateForm = {
		id: 'project_enddate',
		fieldLabel: Otm.com_end_date,
		format:"Y-m-d",editable: false,
		allowBlank : true,
		startDateField: 'project_startdate',
		vtype: 'daterange',
		xtype: 'datefield'
	};
	var descriptionForm = {
		id: 'project_description',
		anchor: '100%',
		fieldLabel: Otm.com_description,
		allowBlank : true,
		height:'100',
		xtype: 'textarea'
	};

	var projectForm = Ext.create("Ext.form.Panel",{
		id:'projectForm',
		border:false,
		anchor:'100%',
		items:[{
			xtype:'fieldset',
			title:Otm.pjt_info,
			width:'100%',anchor:'100%',flex:1,
			items:[project_seq,subjectForm,startDateForm,endDateForm,descriptionForm]
		}]
	});

	var projectWriteForm = Ext.create("Ext.form.Panel",{
		id : 'projectWriteForm',
		region:'center',
		collapsible: false,border:false,
		bodyStyle: 'padding: 10px;',
		autoScroll: true,
		labelWidth:'10',
		items: [projectForm],
		buttons:[{
			text:Otm.com_save,
			disabled: true,
			formBind: true,
			id:'projectWriteForm_saveBtn',
			iconCls:'ico-save',
			handler:function(btn){
				this.disable(true);
				var URL = './index.php/Otm/create_project';
				if(Ext.getCmp('project_seq').getValue()){
					URL = './index.php/Otm/update_project';
				}

				Ext.Ajax.request({
					url : URL,
					params :{
						project_seq				: Ext.getCmp('project_seq').getValue(),
						project_name			: Ext.getCmp('project_name').getValue(),
						project_startdate		: Ext.getCmp('project_startdate').getValue(),
						project_enddate			: Ext.getCmp('project_enddate').getValue(),
						project_description		: Ext.getCmp('project_description').getValue()
					},
					method: 'POST',
					success: function ( result, request ) {
						Ext.getCmp("projectWriteForm_saveBtn").disable(false);
						if(result.responseText){
							var info = Ext.decode(result.responseText);
							if(info.msg == "Duplicate"){
								Ext.Msg.alert("OTM",Otm.com_msg_duplicate_data);
								Ext.getCmp("projectWriteForm_saveBtn").disable(false);
								return;
							}else{
								project_store_reload('add_view',result.responseText);
								projectWriteForm.reset();
							}
						}
					},
					failure: function ( result, request ) {
						Ext.getCmp("projectWriteForm_saveBtn").disable(false);
					}
				});
			}
		},{
			text:'Reset',
			hidden:true,
			handler:function(btn){
				if(Ext.getCmp("projectGrid").getSelectionModel().selected.length >= 1){
					var selItem = Ext.getCmp("projectGrid").getSelectionModel().selected.items[0];
					Ext.getCmp("dashboard").mask(Otm.com_msg_processing_data);
					Ext.Ajax.request({
						url : './index.php/Otm/project_view',
						params :{project_seq : selItem.data.pr_seq},
						method: 'POST',
						success: function ( result, request ) {
							Ext.getCmp("dashboard").unmask();

							if(result.responseText){
								var project_info = Ext.decode(result.responseText);

								selItem.data.project_name			= project_info.data.pr_name;
								selItem.data.project_startdate		= project_info.data.pr_startdate.substr(0,10);
								selItem.data.project_enddate		= project_info.data.pr_enddate.substr(0,10);
								selItem.data.project_description	= project_info.data.pr_description;
								selItem.data.project_seq			= project_info.data.pr_seq;

								Ext.getCmp("projectForm").loadRecord(selItem);
							}
						},
						failure: function ( result, request ) {
							alert("fail");
						}
					});
				}else{
					projectWriteForm.reset();
				}

			}
		}]
	});

	return projectWriteForm;
};

var rowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
		clicksToMoveEditor: 1,
		autoCancel: false
	});

var cellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
		clicksToMoveEditor: 1,
		autoCancel: false
	});

function addProjectBtnListener(btn)
{
	var projectEastPanel = Ext.getCmp("projectEastPanel");
	Ext.getCmp("projectGrid").getSelectionModel().deselectAll();
	if(projectEastPanel.collapsed==false){
	}else{
		projectEastPanel.expand();
	}
	projectEastPanel.update("");

	projectEastPanel.removeAll();
	projectEastPanel.add(getProjectForm());
};

function editProjectBtnListener(btn)
{
	var projectEastPanel = Ext.getCmp("projectEastPanel");
	if(Ext.getCmp("projectGrid").getSelectionModel().selected.length >= 1){
		var selItem = Ext.getCmp("projectGrid").getSelectionModel().selected.items[0];

		projectEastPanel.removeAll();
		projectEastPanel.update("");

		projectEastPanel.add(getProjectForm());

		Ext.Ajax.request({
			url : './index.php/Otm/project_view',
			params :{project_seq : selItem.data.pr_seq},
			method: 'POST',
			success: function ( result, request ) {
				if(result.responseText){
					var project_info = Ext.decode(result.responseText);

					selItem.data.project_name			= project_info.data.pr_name;
					selItem.data.project_startdate		= project_info.data.pr_startdate.substr(0,10);
					selItem.data.project_enddate		= project_info.data.pr_enddate.substr(0,10);
					selItem.data.project_description	= project_info.data.pr_description;
					selItem.data.project_seq			= project_info.data.pr_seq;

					Ext.getCmp("projectForm").loadRecord(selItem);
				}
			},
			failure: function ( result, request ) {
			}
		});
	}else{
		Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
	}
};

function deleteProjectBtnListener(btn)
{
	if(Ext.getCmp("projectGrid").getSelectionModel().selected.length >= 1){

		var selItem = Ext.getCmp("projectGrid").getSelectionModel().selected.items[0];
		if(mb_is_admin == 'Y' || mb_email == selItem.data.writer){
		}else{
			Ext.Msg.alert("OTM",Otm.com_remove+Otm.com_msg_noRole+"<br>"+Otm.com_msg_youneed_auth);
			return;
		}

		Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
			if(bt=='yes'){

				Ext.Ajax.request({
					url : './index.php/Otm/delete_project',
					params :{
						project_seq			: selItem.data.pr_seq
					},
					method: 'POST',
					success: function ( result, request ) {
						if(result.responseText){
							project_store_reload('del');
							var projectEastPanel = Ext.getCmp("projectEastPanel");
							projectEastPanel.body.dom.textContent="";
							projectEastPanel.collapse();
						}
					},
					failure: function ( result, request ) {
					}
				});

			}else{
				return;
			}
		});
	}else{
		Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
	}
};

var addProjectBtn = {
	xtype:'button',
	text:Otm.com_add,
	iconCls:'ico-add',
	handler:addProjectBtnListener
};
var editProjectBtn = {
	xtype:'button',
	text:Otm.com_update,
	iconCls:'ico-update',
	handler:editProjectBtnListener
};
var deleteProjectBtn = {
	xtype:'button',
	text:Otm.com_remove,
	iconCls:'ico-remove',
	handler:deleteProjectBtnListener
};
var exportProjectBtn = {
	xtype:'button',
	text:Otm.com_export,
	iconCls:'ico-export',
	handler: function (btn){
		export_data('otm/project_list_export');
	}
};

var projectTplMarkup = [
	'<div style=padding:20px;>',
		'<div style="font-weight:bold;word-break:break-all;">{pr_name}</div>',
		'<div style="border-top:1px solid gray;"></div>',
		'<div style="padding:10px;line-height:20px;">',

			'<div style="float:left;width:50%">',
				'<div style="float:left;width:40%">'+Otm.com_start_date+' : </div>',
				'<div style="float:right;width:60%">{pr_startdate}</div>',
			'</div>',
			'<div style="float:right;width:50%">',
				'<div style="float:left;width:40%">'+Otm.com_end_date+' : </div>',
				'<div style="float:right;width:60%">{pr_enddate}</div>',
			'</div>',
			'<div style="float:left;width:50%">',
				'<div style="float:left;width:40%">'+Otm.com_creator+' : </div>',
				'<div style="float:right;width:60%">{writer}</div>',
			'</div>',
			'<div style="float:right;width:50%">',
				'<div style="float:left;width:40%">'+Otm.com_date+' : </div>',
				'<div style="float:right;width:60%">{regdate}</div>',
			'</div>',
			'<div style="float:left;width:50%">',
				'<div style="float:left;width:40%">'+Otm.com_modifiers+' : </div>',
				'<div style="float:right;width:60%">{last_writer}</div>',
			'</div>',
			'<div style="float:right;width:50%">',
				'<div style="float:left;width:40%">'+Otm.com_modified+' : </div>',
				'<div style="float:right;width:60%">{last_update}</div>',
			'</div>',
		'</div>',

		'<div style="clear:both;"></div>',

		'<div style="border-top:1px solid gray;margin-top:20px;"></div>',
		'<div style="padding:10px;word-break:break-all;">{pr_description}</div>',
	'</div>'
];
var projectTpl = new Ext.Template(projectTplMarkup);

function projectViewForm()
{
}

var projectGrid_listener = {
	scope:this,
	select: function(smObj, record, rowIndex) {
		var projectEastPanel = Ext.getCmp("projectEastPanel");
		if(projectEastPanel.collapsed==false){
		}else{
			projectEastPanel.expand();
		}

		projectEastPanel.update("");
		projectEastPanel.removeAll();

		Ext.Ajax.request({
			url : './index.php/Otm/project_view',
			params :{project_seq : record.data.pr_seq},
			method: 'POST',
			success: function ( result, request ) {
				if(result.responseText){
					var project_info = Ext.decode(result.responseText);

					if(project_info.data.last_writer == null){
						project_info.data.last_writer = "";
					}
					if(project_info.data.last_update== null){
						project_info.data.last_update="";
					}
					if(project_info.data.pr_enddate == "0000-00-00 00:00:00"){
						project_info.data.pr_enddate = "";
					}
					if(project_info.data.pr_enddate){
						project_info.data.pr_enddate = project_info.data.pr_enddate.substr(0,10);
					}
					project_info.data.pr_startdate = project_info.data.pr_startdate.substr(0,10);
					project_info.data.pr_description = project_info.data.pr_description.replace(/\n/gi,'<br/>').replace(/\\n/gi,'<br/>');

					projectTpl.overwrite(Ext.getCmp("projectEastPanel").body, project_info.data);
				}
			},
			failure: function ( result, request ) {
				Ext.Msg.alert("OTM","DataBase Select Error");
			}
		});
	},
	deselect:function(){
	}
};

var menuBtn = {
	xtype:'button',
	text:'Menu Button'
};

/*
var dashboard = {
	layout	:'fit',
	xtype	: 'panel',
	title	: Otm.pjt+' '+Otm.dashboard,
	id		: 'project_dashboard',
	closable: false,
	plain	: true,
	scope	: this,
	loader	: {
		autoLoad: true,
		loadMask: true,
		scripts	: true,
		url		: './index.php/Otm/project_dashboard'
	},
	listeners:{
		render: function(tab){
		},
		activate : function(tabpanel){
		}
	}
};
*/

var center_panel_items = [{
	layout	:'fit',
	xtype	: 'panel',
	title	: Otm.pjt+' '+Otm.dashboard,
	id		: 'project_dashboard',
	closable: false,
	plain	: true,
	scope	: this,
	loader	: {
		autoLoad: true,
		loadMask: true,
		scripts	: true,
		url		: './index.php/Otm/project_dashboard'
	},
	listeners:{
		render: function(tab){
		},
		activate : function(tabpanel){
		}
	}
}];

var center_panel = Ext.create('Ext.tab.Panel', {
	region	: 'center',
	layout	: 'fit',
	id		: 'main_tab',
	collapsible	: false,
	deferredRender: false,
	activeTab	: dashboard_key,
	plain		: true,
	items		: [],//center_panel_items,
	listeners: {
		tabchange : function(tabPanel, newCard, oldCard, eOpts ) {
			switch(tabPanel.activeTab.id){
				case "user":
				case "group":
				case "role":
				case "code":
				case "workflow":
				case "userform":
					var record = setup_store.getNodeById(tabPanel.activeTab.id);
					Ext.getCmp("systemSetUp_treepanel").getSelectionModel().select(record);
				break;
				case "defect":
					var record = project_tree_store.getNodeById('defect_'+tabPanel.activeTab.pr_seq);
					Ext.getCmp("project_treePanel").getSelectionModel().select(record);
				break;
				case "project_setup_main":
					var record = project_tree_store.getNodeById('setup_'+tabPanel.activeTab.pr_seq);
					Ext.getCmp("project_treePanel").getSelectionModel().select(record);
				break;
				case "project_setup_user":
					var record = project_tree_store.getNodeById('setup_user_'+tabPanel.activeTab.pr_seq);
					Ext.getCmp("project_treePanel").getSelectionModel().select(record);
				break;
				case "project_setup_code":
					var record = project_tree_store.getNodeById('setup_code_'+tabPanel.activeTab.pr_seq);
					Ext.getCmp("project_treePanel").getSelectionModel().select(record);
				break;
				case "project_setup_userform":
					var record = project_tree_store.getNodeById('setup_userform_'+tabPanel.activeTab.pr_seq);
					Ext.getCmp("project_treePanel").getSelectionModel().select(record);
				break;
			}
		}
	}
});

function get_select_comtc_product()
{
	var selItem = Ext.getCmp("comtc_acc_tree").getSelectionModel().selected.items[0];
	if(selItem){
		return selItem.data;
	}else{
		return '';
	}
}

function comtc_product_form_window(obj){
	var win = Ext.getCmp('comtc_product_form_window');
	if(win){
		win.close();
	}

	var select_value = '';
	var subject_value = '';
	var description_value = '';

	switch(obj.type){
		case 'add_product':
			var subject_fieldLabel = Otm.comtc_productname+'(*)';
			var subject_id = 'comtc_group_name';

			var description_fieldLabel = Otm.com_description;
			var description_id = 'comtc_group_description';

			break;
		case 'add_version':

			if(get_select_comtc_product().type != 'product'){
				Ext.Msg.alert('OTM',Otm.com_msg_please_choose_products);
				return;
			}

			var subject_fieldLabel = Otm.comtc_versionname+'(*)';
			var subject_id = 'comtc_version_name';

			var description_fieldLabel = Otm.com_description;
			var description_id = 'comtc_version_description';

			select_value = get_select_comtc_product().seq;

			break;
		case 'edit_product':
			var subject_fieldLabel = Otm.comtc_productname+'(*)';
			var subject_id = 'comtc_group_name';

			var description_fieldLabel = Otm.com_description;
			var description_id = 'comtc_group_description';

			select_value = get_select_comtc_product().seq;
			subject_value = get_select_comtc_product().text;
			description_value = get_select_comtc_product().description;

			break;
		case 'edit_version':
			var subject_fieldLabel = Otm.comtc_versionname+'(*)';
			var subject_id = 'comtc_version_name';

			var description_fieldLabel = Otm.com_description;
			var description_id = 'comtc_version_description';

			select_value = get_select_comtc_product().seq;
			subject_value = get_select_comtc_product().text;
			description_value = get_select_comtc_product().description;

			break;
	}

	var select_seq = {
		id: 'select_seq',
		anchor: '100%',
		allowBlank : false,
		xtype: 'hiddenfield',
		value: select_value
	};
	var subject = {
		anchor: '100%',
		fieldLabel: subject_fieldLabel,
		id: subject_id,
		minLength:2,
		maxLength:100,
		allowBlank : false,
		xtype: 'textfield',
		value: subject_value
	};
	var description = {
		anchor: '100%',
		fieldLabel: description_fieldLabel,
		id: description_id,
		allowBlank : true,
		height:'100',
		xtype: 'textarea',
		value: description_value
	};

	var comtc_product_write_form = Ext.create("Ext.form.Panel",{
		id : 'comtc_product_write_form',
		border:false,
		bodyStyle: 'padding: 10px;',
		autoScroll: true,
		labelWidth:'10',
		items: [select_seq,subject,description],
		buttons:[{
			text:Otm.com_save,
			disabled: true,
			formBind: true,
			handler:function(btn){

				var form = this.up('form').getForm();
				if(form.isValid()) {

					switch(obj.type){
						case 'add_product':
							var URL = './index.php/Com_testcase/create_product';
							var params = {
								p_subject		: form.findField("comtc_group_name").getValue(),
								p_description	: form.findField("comtc_group_description").getValue()
							};

							break;
						case 'add_version':
							var URL = './index.php/Com_testcase/create_version';
							var params = {
								p_seq					: form.findField("select_seq").getValue(),
								v_version_name			: form.findField("comtc_version_name").getValue(),
								v_version_descriptioin	: form.findField("comtc_version_description").getValue()
							};
							break;
						case 'edit_product':
							var URL = './index.php/Com_testcase/update_product';
							var params = {
								p_seq			: form.findField("select_seq").getValue(),
								p_subject		: form.findField("comtc_group_name").getValue(),
								p_description	: form.findField("comtc_group_description").getValue()
							};
							break;
						case 'edit_version':
							var URL = './index.php/Com_testcase/update_version';
							var params = {
								v_seq					: form.findField("select_seq").getValue(),
								v_version_name			: form.findField("comtc_version_name").getValue(),
								v_version_descriptioin	: form.findField("comtc_version_description").getValue()
							};
							break;
					}

					Ext.Ajax.request({
						url : URL,
						params : params,
						method: 'POST',
						success: function ( result, request ) {
							if(result.responseText){
								var info = result.responseText.split('_');

								if((info[1] != 'comversion') && (info[1] != 'comproduct')){
									Ext.Msg.alert('OTM',result.responseText);
									return;
								}

								comtc_acc_tree_store.load();
								Ext.getCmp("comtc_acc_tree").getSelectionModel().deselectAll();
								var win = Ext.getCmp('comtc_product_form_window');
								if(win){
									win.close();
								}
							}
						},
						failure: function ( result, request ){
						}
					});
				}
			}
		},{
			text:'Reset',
			hidden:true,
			handler:function(btn){
				Ext.getCmp("comtc_product_write_form").reset();
			}
		}]
	});

	Ext.create('Ext.window.Window', {
		title: obj.title,
		id	: 'comtc_product_form_window',
		height: 220,
		width: 400,
		layout: 'fit',
		resizable : false,
		modal : true,
		constrainHeader: true,
		items: comtc_product_write_form
	}).show();
}

var comtc_acc_tree_store = Ext.create('Ext.data.TreeStore', {
	root: {
		text: 'Root',
		expanded: true,
		children: []
	},
	proxy: {
		type: 'ajax',
		url:'./index.php/Com_testcase/product_tree_list',
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
	},
	folderSort: true
});

var system_setup_tree_store = Ext.create('Ext.data.TreeStore', {
	root: {
		text: 'Root',
		expanded: true,
		children: []
	},
	proxy: {
		type: 'ajax',
		url:'./index.php/Project_setup/system_setup_tree_list',
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

var setup_store = Ext.create('Ext.data.TreeStore', {
    root: {
        text: 'Root',
        expanded: true,
        children: [
            {text: Otm.com_member,		id:'user',		type:'user', leaf: false,children:[]},
			{text: Otm.com_group,			id:'group',			type:'group', leaf: false,children:[]},
			{text: Otm.com_role_auth,	id:'role',			type:'role', leaf: false,children:[]},
			{text: Otm.com_code,			id:'code',			type:'code', leaf: false,children:[]},
			{text: Otm.com_def_lifecycle,		id:'workflow',		type:'workflow', leaf: false,children:[]},
			{text: Otm.com_user_defined_form,	id:'userform',		type:'userform', leaf: false,children:[]}
        ]
    }
});

var accordion_items = [
	{
		layout	: 'fit',
		xtype	: 'panel',
		title	: Otm.pjt,
		id		: 'project_acc',
		items	: [{
			xtype: 'treepanel',
			rootVisible: false,
			pr_seq	: 0,
			id:'project_treePanel',
			animate: false,
			listeners: {
				itemclick : function(view,rec,item,index,eventObj) {
					if(rec.get('type')=='project') {
						return;
					}else if(rec.get('type')=='group') {
						return;
					}else if(rec.get('type')=='testcase') {
						return;
					}else if(rec.get('type')=='testcase_plan') {

					}
					get_tabpanel(rec);
				}
			},
			store: project_tree_store
		},{
			xtype	: 'hiddenfield',
			id		: 'before_select_project_tree'
		}]
	},{
		layout	: 'fit',
		xtype	: 'panel',
		hidden	: false,
		title	: Otm.comtc,
		id		: 'comtc_acc',
		hidden	: (mb_is_admin=='Y')?false:true,
		items	: [{
			xtype: 'treepanel',
			id:'comtc_acc_tree',
			rootVisible: false,
			useArrows: true,
			store: comtc_acc_tree_store,
			animate: false,
			listeners:{
				itemclick : function(view,rec,item,index,eventObj) {

					var tabPanel = Ext.getCmp('main_tab');
					var activeTab = tabPanel.getActiveTab();
					if(activeTab.id != 'comtc_main'){
						tabPanel.setActiveItem(1);
					}

					if(rec.get('type')=='version'){
						Ext.getCmp("acc").mask(Otm.com_msg_processing_data);
						Ext.getCmp('comtc_treegrid').getStore().load({params:{v_seq:rec.get('seq')}});
					}
				}
			}
		}],
		listeners: OTM.listeners,
		tbar	: [{
			xtype:'button',
			text: Otm.comtc_products+' '+Otm.com_add,
			iconCls:'ico-add',
			handler:function(btn){
				var data = {
					type : 'add_product',
					title : btn.text
				};
				comtc_product_form_window(data);
			}
		},'-',{
			xtype:'button',
			text:Otm.comtc_version+' '+Otm.com_add,
			iconCls:'ico-add',
			handler:function(btn){
				var data = {
					type : 'add_version',
					title : btn.text
				};
				comtc_product_form_window(data);
			}
		},'-',{
			xtype:'button',
			text:Otm.com_update,
			iconCls:'ico-update',
			handler:function(btn){
				if(get_select_comtc_product() == ''){
					Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
					return;
				}else{
					var data = {
						type : 'edit_'+get_select_comtc_product().type,
						title : btn.text
					};
					comtc_product_form_window(data);
				}
			}
		},'-',{
			xtype:'button',
			text:Otm.com_remove,
			iconCls:'ico-remove',
			handler:function(btn){
				if(get_select_comtc_product() == ''){
					Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
					return;
				}else{
					var type = get_select_comtc_product().type;
					var URL = './index.php/Com_testcase/delete_product';
					var msg = Otm.com_msg_product_del_alldata;

					if(type == 'version'){
						URL = './index.php/Com_testcase/delete_version';
						msg = Otm.com_msg_version_del_alldata;
					}

					Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm+'<br>'+msg,function(bt){
						if(bt=='yes'){
							var params = {
								seq		: get_select_comtc_product().seq
							};

							Ext.getCmp("acc").mask(Otm.com_msg_processing_data);
							Ext.Ajax.request({
								url : URL,
								params : params,
								method: 'POST',
								success: function ( result, request ) {
									if(result.responseText){
										comtc_acc_tree_store.load();
										Ext.getCmp("comtc_acc_tree").getSelectionModel().deselectAll();

										Ext.getCmp('comtc_east_panel').removeAll();
										Ext.getCmp("comtc_east_panel").update("");
										Ext.getCmp("comtc_treegrid").getStore().load();
										Ext.getCmp("comtc_treegrid").getSelectionModel().deselectAll();
									}
								},
								failure: function ( result, request ){
								}
							});
						}else{
							return;
						}
					});
				}
			}
		}]
	},{
		layout	: 'fit',
		xtype	: 'panel',
		title	: Otm.com_system_setup,
		id		: 'sys_setup_acc',
		hidden	: (mb_is_admin=='Y')?false:true,
		items	: [{
			xtype: 'treepanel',
			width: '95%',
			id:'systemSetUp_treepanel',
			rootVisible: false,
			useArrows: true,
			animate: false,
			listeners: OTM.listeners,
			store: system_setup_tree_store
		}]
	}];

var west_panel = new Ext.Panel({
	region	: 'west',
	split	: true,
	width	: 250,
	minSize	: 100,
	maxSize	: 300,
	layout	: 'accordion',
	xtype	: 'panel',
	id		: 'acc',
	animate: false,
	items: accordion_items,
	listeners:OTM.listeners
});

Ext.onReady(function(){

	Ext.create('Ext.container.Viewport', {
		renderTo	: Ext.getBody(),
		id			: 'OTM_HOME',
		layout		: 'border',
		defaults	: {
			collapsible	: true,
			split		: true,
			bodyStyle	: 'padding:0px'
		},
		items		: [
			head_layout,
		{
			title		: 'Footer',
			region		: 'south',
			xtype		: 'box',
			id			: 'footer',
			split		: false,
			collapsible	: false,
			height		: 30,
			titleAlign	: 'center',
			html		: '<h1>'+Otm.com_msg_company_info+'</h1>'
		},west_panel,{
			title		: 'right',
			region		: 'east',
			hidden		: true,
			margins		: '5 0 0 0',
			cmargins	: '5 5 0 0',
			width		: 175,
			minSize		: 100,
			maxSize		: 250
		},
		center_panel
		]
	});

	var tabPanel = Ext.getCmp('main_tab');

	tabPanel.add({
		layout	:'fit',
		xtype	: 'panel',
		title	: Otm.pjt+' '+Otm.dashboard,
		id		: 'project_dashboard',
		closable: false,
		plain	: true,
		scope	: this,
		loader	: {
			autoLoad: true,
			loadMask: true,
			scripts	: true,
			url		: './index.php/Otm/project_dashboard'
		},
		listeners:{
			render: function(tab){
			},
			activate : function(tabpanel){
			}
		}
	}).show();


	tabPanel.add({
		layout	:'fit',
		xtype	: 'panel',
		title	: Otm.def_dashboard,
		id		: 'defect_dashboard',
		closable: false,
		plain	: true,
		scope	: this,
		loader	: {
			autoLoad: true,
			loadMask: true,
			scripts	: true,
			url		: './index.php/Plugin_view/defect/defect_dashboard'
		},
		listeners:{
			render: function(tab){
			},
			activate : function(tabpanel){
			}
		}
	}).show();

});
</script>