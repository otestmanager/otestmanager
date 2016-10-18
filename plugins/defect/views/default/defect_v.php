<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script type="text/javascript">
var project_seq = '<?=$project_seq?>';
var search_condition = 0;

function defect_send_mail()
{
	var Records = Ext.getCmp("defect_defectGrid").getSelectionModel().selected.items;

	if(Records.length <= 0 ){
		Ext.Msg.alert("OTM",Otm.def +' ' + Otm.com_msg_NotSelectData);
		return;
	}

	var defect_send_mail_grid_store = new Ext.data.SimpleStore({
		fields:['df_seq','df_id','df_subject','df_status','df_severity','df_priority','df_frequency','df_id','df_assign_member','writer_name','otm_testcase_result_tr_seq','tracking_id']
	});

	for(var i=0; i<Records.length; i++){
		defect_send_mail_grid_store.insert(i,Records);
	}

	var defect_send_mail_grid = Ext.create("Ext.grid.Panel",{
		layout	: 'fit',
		title	: Otm.rep_defect_list,
		store	: defect_send_mail_grid_store,
		border	: true,
		forceFit: true,
		columns	: [{
				text: Otm.com_number,autoResizeWidth: true,
				dataIndex: 'df_seq',align:'center',
				hidden:true,
				width:50
			},{
				text: 'ID',autoResizeWidth: true,
				dataIndex: 'df_id',
				width: 80,
				menuDisabled: false
			},{
				text: Otm.com_subject,
				dataIndex: 'df_subject',
				flex:1,
				minWidth:100,
				menuDisabled: false
			},{
				text: Otm.def_status,
				dataIndex: 'df_status',align:'center',autoResizeWidth: true,
				width: 80,
				renderer: function(value, metaData, record, rowIndex, colIndex, store){
					for(var i=0;i<defect_status_store.data.items.length;i++){
						if(defect_status_store.data.items[i].data.pco_seq == value){
							var name = defect_status_store.data.items[i].data.pco_name;
							if(defect_status_store.data.items[i].data.pco_is_required == 'Y'){
								return '<font color=blue>'+name+'</font>';
							}else{
								return name;
							}
						}
					}
					return value;
				}
			},{
				text: Otm.com_user,
				dataIndex: 'df_assign_member',align:'center',autoResizeWidth: true,
				width: 80,
				menuDisabled: false
			},{
				text: Otm.com_creator,
				dataIndex: 'writer_name',align:'center',autoResizeWidth: true,
				width: 80,
				menuDisabled: false
			},{
					text: Otm.com_date, dataIndex: 'regdate',autoResizeWidth: true, align:'center',
					width:80,
					sortable: true, renderer:function(value,index,record){
						if(value){
							var value = value.substr(0,10);
						}else{
							value = '';
						}
						return value;
					}
			}]
	});

	if(Ext.getCmp('defect_send_mail_window')){
		Ext.getCmp('defect_send_mail_window').removeAll();
	}else{
		Ext.create('Ext.window.Window', {
			layout		: 'border',
			title		: Otm.def+Otm.com_mail_alram,
			id			: 'defect_send_mail_window',
			height		: document.body.clientHeight - 100,
			width		: document.body.clientWidth - 100,
			resizable	: true,
			modal		: true,
			constrainHeader: true,
			closable	: false,
			border		: false,
			closeAction	: 'hide',
			items		: [],
			buttons		: [{
				text	: 'Send Mail',
				handler	: function(btn){

					var df_list = Array();
					var user_list = Array();

					/*
						Select Defect
					*/
					var Records = Ext.getCmp("defect_defectGrid").getSelectionModel().selected.items;
					if(Records.length >= 1){
						for(var i=0; i<Records.length; i++){
							df_list.push(Records[i].data['df_seq']);
							project_seq = Records[i].data['otm_project_pr_seq'];
						}
					}else{
						Ext.Msg.alert("OTM",Otm.def +' ' + Otm.com_msg_NotSelectData);
						return;
					}

					/*
						Select User
					*/
					var send_to = Ext.getCmp('defect_send_mail_to').getValue();
					if(send_to){
						var tmp_send_to = send_to.split(';');
						for(var i=0; i<tmp_send_to.length; i++){
							if(tmp_send_to[i] == '') continue;
							user_list.push(tmp_send_to[i]);
						}
					}else{
						Ext.Msg.alert("OTM",Otm.com_msg_select_user);
						return;
					}


					var params = {
							project_seq : project_seq,
							user_list	: Ext.encode(user_list),
							defect_list : Ext.encode(df_list),
							subject		: Ext.getCmp('defect_send_mail_subject').getValue(),
							content		: Ext.getCmp('defect_send_mail_content').getValue()
						};

					Ext.Ajax.request({
						url		: './index.php/Plugin_view/defect/send_mail',
						params	: params,
						method	: 'POST',
						success	: function ( result, request ) {
							if(result.responseText == "ok"){
								Ext.Msg.alert('OTM',Otm.com_msg_mail_sended);
								Ext.getCmp('defect_send_mail_window').hide();
							}else{
								Ext.Msg.alert('OTM',result.responseText);
							}
						},
						failure	: function ( result, request ){
							Ext.Msg.alert('OTM','Fail');
						}
					});
				}
			},{
				text:Otm.com_close,
				handler:function(btn){
					Ext.getCmp('defect_send_mail_window').hide();
				}
			}]
		}).show();
	}

	var defect_send_mail_usergrid_store = Ext.create('Ext.data.Store', {
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
		},
		autoLoad:true
	});
	var defect_send_mail_usergrid = Ext.create("Ext.grid.Panel",{
		region	: 'east',
		layout	: 'fit',
		title	: Otm.pjt+' '+Otm.com_userlist,
		id		:'defect_send_mail_usergrid',
		store	: defect_send_mail_usergrid_store,
		border	: true,
		width	: 200,
		collapsible	: false,
		collapsed	: false,
		animation	: false,
		selModel:Ext.create('Ext.selection.CheckboxModel'),
		viewConfig: {
			listeners: {
				viewready: function(){
					this.store.load({params:{pr_seq:project_seq}});
				}
			}
		},
		columns	: [
			{header: Otm.com_name,			dataIndex: 'mb_name', width:60},
			{header: Otm.com_group,			dataIndex: 'user_group_name',	width:150, hidden:true},
			{header: Otm.com_role,			dataIndex: 'user_role_name',	width:150, hidden:true},
			{header: Otm.com_email,			dataIndex: 'mb_email',		flex: 1},
			{header: Otm.com_admin,			dataIndex: 'mb_is_admin', align:'center',	width:80, hidden:true}
		],
		listeners:{
			scope:this,
			select: function(smObj, record, rowIndex){
				var return_send_to = "";
				var send_to = Ext.getCmp('defect_send_mail_to').getValue();
				if(send_to){
					var tmp_send_to = send_to.split(';');
					var send_to_array = Array();
					for(var i=0; i<tmp_send_to.length; i++){
						if(tmp_send_to[i] == '') continue;

						if(tmp_send_to[i] == record.data.mb_email){
						}else{
							send_to_array.push(tmp_send_to[i]);
						}
					}
					send_to_array.push(record.data.mb_email);

					return_send_to = send_to_array.join(';')+';';
				}else{
					return_send_to = record.data.mb_email+';';
				}
				Ext.getCmp('defect_send_mail_to').setValue(return_send_to);
			},
			deselect: function(smObj, record, rowIndex){
				var return_send_to = "";
				var send_to = Ext.getCmp('defect_send_mail_to').getValue();
				if(send_to){
					var tmp_send_to = send_to.split(';');
					var send_to_array = Array();
					for(var i=0; i<tmp_send_to.length; i++){
						if(tmp_send_to[i] == '') continue;

						if(tmp_send_to[i] == record.data.mb_email){
						}else{
							send_to_array.push(tmp_send_to[i]);
						}
					}

					return_send_to = send_to_array.join(';')+';';
				}else{
					return_send_to = record.data.mb_email+';';
				}
				Ext.getCmp('defect_send_mail_to').setValue(return_send_to);
			}
		}
	});

	var defect_send_mail_form = {
		anchor		: '0',
		fieldLabel	: 'Form',
		xtype		: 'displayfield',
		value		: mb_email
	};
	var defect_send_mail_to = {
		id: 'defect_send_mail_to',
		name:'defect_send_mail_to',
		anchor: '0',
		fieldLabel: 'To (*)',
		allowBlank : false,
		xtype: 'textfield'
	};
	var defect_send_mail_subject = {
		id: 'defect_send_mail_subject',
		name:'defect_send_mail_subject',
		anchor: '0',
		minLength:2,
		fieldLabel: Otm.com_subject+'(*)',
		allowBlank : false,
		xtype: 'textfield'
	};
	var defect_send_mail_content = {
		id: 'defect_send_mail_content',
		name:'defect_send_mail_content',
		anchor: '0',
		fieldLabel: Otm.com_description+'(*)',
		allowBlank : false,
		grow : true,
		growMax: 400,
		growMin: 100,
		xtype: 'textarea'
	};
	var defect_send_mail_writeForm = Ext.create("Ext.form.Panel",{
		region		: 'center',
		collapsible	: false,
		border		: false,
		bodyStyle	: 'padding: 10px;',
		autoScroll	: true,
		labelWidth	: '10',
		items: [defect_send_mail_form,defect_send_mail_to,defect_send_mail_subject,defect_send_mail_content
			,defect_send_mail_grid]
	});

	Ext.getCmp('defect_send_mail_window').add(defect_send_mail_writeForm);
	Ext.getCmp('defect_send_mail_window').add(defect_send_mail_usergrid);
	Ext.getCmp('defect_send_mail_window').show();
}

function viewImportWin_defect()
{
	var win = Ext.getCmp('defect_import_window');

	if(win){
		Ext.getCmp('form_file').clearOnSubmit = true;
		Ext.getCmp("defect_uploadPanel").reset();
		Ext.getCmp('form_file').clearOnSubmit = false;
		Ext.getCmp("defect_uploadPanel").update();

		win.show();
		return;
	}

	var checck_id_checkbox = {
		hidden: true,
		xtype: 'radiogroup',	width:'100%',
		id:'import_check_id',
		fieldLabel : 'Check ID',
		columns: 2,
		items: [{
			boxLabel:'Insert All',
			name : 'import_check_id',
			inputValue : false,
			checked:false
		},{
			boxLabel:'Insert and Update',
			name : 'import_check_id',
			inputValue : true,
			checked:true
		}]
	}

	var defect_uploadPanel = new Ext.FormPanel({
		fileUpload: true,
		id:'defect_uploadPanel',
		border:false,
		bodyStyle: 'padding: 5px;',
		labelWidth: 1,
		defaults: {
			allowBlank: false
		},
		items: [{
			height: 10,
			border:false
		},{
			xtype: 'filefield',
			name : 'form_file',
			id : 'form_file',
			clearOnSubmit : false,
			regex: /^.*\.(xls|XLS)$/,
			regexText: 'Only xls files allowed',
			anchor:'100%',
			allowBlank : false,
			reference: 'basicFile'
		},{
			height: 20,
			border:false
		},
			checck_id_checkbox,
		{
			xtype:'label',
			html:'Example : [Max Rows : 1000, Max Columns : 50]'
		}],
		buttons: [pbar,{
			text:Otm.com_save,
			formBind: true,
			iconCls:'ico-save',
			id:'testCaseImportSubmitBtn',
			handler: function(){
				var params = {
							project_seq	: project_seq,
							import_check_id : true,
							update : false
						};
				var chkInfo = Ext.getCmp('import_check_id').items;
				if(chkInfo.items[1].checked){
					params.import_check_id = true;
				}

				defect_uploadPanel_submit(params);
			}
		}]
	});

	var import_window = Ext.create('Ext.window.Window', {
		title: Otm.def+' '+Otm.com_import+'(.xls)',
		id	: 'defect_import_window',
		height: 230,
		width: 650,
		layout: 'fit',
		resizable : true,
		modal : true,
		constrainHeader: true,
		closeAction: 'hide',
		items: [defect_uploadPanel]
	});

	import_window.show();
}

function select_excel_sheet_defect(sheets,params)
{
	if(Ext.getCmp('select_excel_sheet_defect_window')){
		Ext.getCmp('select_excel_sheet_defect_window').removeAll();
	}else{
		Ext.create('Ext.window.Window', {
			title: 'Select Sheet',
			id	:'select_excel_sheet_defect_window',
			height: 130,
			width: 380,
			layout: 'fit',
			resizable : false,
			modal : true,
			constrainHeader: true,
			closeAction: 'hide',
			items: []
		});
	}

	var store = Ext.create('Ext.data.Store', {
		fields:['index', 'name'],
		data:{'items':[]},
		proxy: {
			type: 'memory',
			reader: {
				type: 'json',
				rootProperty: 'items'
			}
		}
	});

	for(var i=0; i<sheets.length; i++)
	{
		store.add({
			index	: i,
			name : sheets[i]
		});
	}

	var combo = Ext.create('Ext.form.ComboBox', {
		id			: 'sheet_name_combo',
		editable	: false,
		width		: '100%',
		fieldLabel	: 'Sheet list',
		displayField: 'name',
		valueField	: 'index',
		store		: store,
		allowBlank	: true,
		queryParam	: 'q',
		queryMode	: 'local'
	});

	var select_excel_sheet_defect = new Ext.FormPanel({
		border:false,
		bodyStyle: 'padding: 5px;',
		labelWidth: 1,
		defaults: {
			allowBlank: false
		},
		items: [{
			height: 10,
			border:false
		},combo],
		buttons: [{
			text:Otm.com_save,
			iconCls:'ico-save',
			handler: function(){
				var value = Ext.getCmp('sheet_name_combo').getValue();
				if(value != null){
					params.sheet_index = value;
					Ext.getCmp('select_excel_sheet_defect_window').hide();
					defect_uploadPanel_submit(params);
				}else{
					Ext.Msg.alert('OTM','Please, Select Sheet.');
				}
			}
		}]
	});

	Ext.getCmp('select_excel_sheet_defect_window').add(select_excel_sheet_defect);
	Ext.getCmp('select_excel_sheet_defect_window').show();
}

function defect_uploadPanel_submit(params)
{
	if(Ext.getCmp("defect_uploadPanel").getForm().isValid()){
		top.myUpdateProgress(1,'Data Loadding...');

		var URL = "./index.php/Import/plugin/defect/import_defect";

		Ext.getCmp("defect_uploadPanel").mask(Otm.com_msg_processing_data);

		Ext.getCmp("defect_uploadPanel").getForm().submit({
			url: URL,
			method:'POST',
			params: params,
			success: function(form, action){
				top.myUpdateProgress(100,'End');

				var obj = Ext.decode(action.response.responseText);
				if(obj.sheet_count && obj.sheet_count>1)
				{
					Ext.getCmp("defect_uploadPanel").unmask();

					select_excel_sheet_defect(obj.sheet_names,params);
					return;
				}

				Ext.getCmp("defect_defectGrid").getStore().reload();

				Ext.getCmp("defect_uploadPanel").unmask();
				Ext.getCmp("defect_import_window").hide();
			},
			failure: function(form, action){
				var obj = Ext.decode(action.response.responseText);

				var msg = Ext.decode(obj.msg);
				if(msg.over){
					Ext.Msg.alert('OTM',msg.over);

					top.myUpdateProgress(100,'End');
					Ext.getCmp("defect_uploadPanel").unmask();
					return;
				}else if(msg.duplicate_id){
					Ext.Msg.confirm('OTM',Otm.com_msg_duplicate_id,function(bt){
						if(bt=='yes'){

							var params = {
								project_seq	: project_seq,
								import_check_id : true,
								update : true
							};

							defect_uploadPanel_submit(params);

						}else{
							Ext.getCmp("defect_import_window").hide();
							return;
						}
					})

				}else{
					Ext.Msg.alert('OTM',msg.msg);
				}

				top.myUpdateProgress(100,'End');
				Ext.getCmp("defect_uploadPanel").unmask();
			}
		});
	}
}

var defect_code_store = Ext.create('Ext.data.Store', {
	fields:['pco_seq', 'pco_type','pco_name','pco_is_required','pco_is_default','pco_default_value'],
	proxy: {
		type	: 'ajax',
		url		: './index.php/Plugin_view/defect/code_list',
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

var defect_store = Ext.create('Ext.data.Store', {
	fields:['df_seq','df_id','df_subject','df_status','df_severity','df_priority','df_frequency','df_id','df_assign_member','writer_name','otm_testcase_result_tr_seq','tracking_id'],
	pageSize: 50,
	proxy: {
		type	: 'ajax',
		url		: './index.php/Plugin_view/defect/defect_list',
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

var defect_contact_store = Ext.create('Ext.data.Store', {
	fields:['mb_email','mb_name'],
	proxy: {
		type	: 'ajax',
		url		: './index.php/Project_setup/project_userlist',
		extraParams: {
			pr_seq : <?=$project_seq?>
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

var defect_customform_store = Ext.create('Ext.data.Store', {
	fields:['pc_seq','pc_name','pc_is_required','pc_formtype','pc_default_value','pc_content','pc_is_use'],
	proxy: {
		type	: 'ajax',
		url		: './index.php/Project_setup/userform_list',
		extraParams: {
			pr_seq		: <?=$project_seq?>,
			pc_category : 'ID_DEFECT',
			pc_is_use	: 'Y'
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

var blank_store = Ext.create('Ext.data.Store', {
	fields:['pco_seq', 'pco_name'],
    data:{'items':[]},
    proxy: {
        type: 'memory',
        reader: {
            type: 'json',
            rootProperty: 'items'
        }
    }
});

var defect_severity_store = Ext.create('Ext.data.Store', {
	fields:['pco_seq', 'pco_name'],
	data:{'items':[]},
	proxy: {
		type: 'memory',
		reader: {
			type: 'json',
			rootProperty: 'items'
		}
	}
});

var defect_priority_store = Ext.create('Ext.data.Store', {
	fields:['pco_seq', 'pco_name'],
	data:{'items':[]},
	proxy: {
		type: 'memory',
		reader: {
			type: 'json',
			rootProperty: 'items'
		}
	}
});

var defect_frequency_store = Ext.create('Ext.data.Store', {
	fields:['pco_seq', 'pco_name'],
	data:{'items':[]},
	proxy: {
		type: 'memory',
		reader: {
			type: 'json',
			rootProperty: 'items'
		}
	}
});

var defect_status_store = Ext.create('Ext.data.Store', {
	fields:['pco_seq', 'pco_name','pco_is_required'],
	data:{'items':[]},
	proxy: {
		type: 'memory',
		reader: {
			type: 'json',
			rootProperty: 'items'
		}
	}
});

var defect_searchField_store = new Ext.data.SimpleStore({
	 fields:['Key', 'Name']
	,data:[['subject', Otm.com_subject],['description', Otm.com_description],['writer', Otm.com_creator],['charge', Otm.com_user],['regdate', Otm.com_date],['status', Otm.com_status]]
});

function defect_addBtnListener(btn)
{
	var defect_EastPanel = Ext.getCmp('defect_EastPanel');
	defect_EastPanel.setTitle(Otm.com_add);

	Ext.getCmp("defect_defectGrid").getSelectionModel().deselectAll();
	if(defect_EastPanel.collapsed==false){
	}else{
		defect_EastPanel.expand();
	}

	form_reset();
}

function defect_editBtnListener(btn)
{
	if(Ext.getCmp("defect_defectGrid").getSelectionModel().selected.length > 1){
		Ext.Msg.alert('OTM',Otm.com_msg_only_one);
		return;
	}

	if(Ext.getCmp("defect_defectGrid").getSelectionModel().selected.length == 1){

		if(check_role('defect_edit_all')){
		}else if(check_role('defect_edit')){
			var selected = Ext.getCmp('defect_defectGrid').getSelectionModel().selected.items[0].data;
			if(selected.writer != mb_email && selected.dc_to != mb_email ){
				Ext.Msg.alert('OTM',Otm.def+' '+Otm.com_update+Otm.com_msg_noRole);
				return;
			}
		}else{
			Ext.Msg.alert('OTM',Otm.tc+' '+Otm.com_update+Otm.com_msg_noRole);
			return;
		}

		var defect_EastPanel = Ext.getCmp('defect_EastPanel');
		defect_EastPanel.setTitle(Otm.com_update);
		if(defect_EastPanel.collapsed==false){
		}else{
			defect_EastPanel.expand();
		}
		form_reset();
	}else{
		Ext.Msg.alert('OTM',Otm.def +' ' + Otm.com_msg_NotSelectData);
		return;
	}
}

function defect_deleteBtnListener(btn)
{
	if(Ext.getCmp("defect_defectGrid").getSelectionModel().selected.length >= 1){

		var df_list = Array();
		var Records = Ext.getCmp("defect_defectGrid").getSelectionModel().selected.items;

		for(var i=0; i<Records.length; i++){
			df_list.push(Records[i].data['df_seq']);
		}

		Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
			if(bt=='yes'){
				var params = {
					project_seq	: <?php print $project_seq?>,
					df_list		: Ext.encode(df_list),
					writer		: Ext.getCmp("defect_defectGrid").getSelectionModel().selected.items[0].data.writer
				};
				var url = './index.php/Plugin_view/defect/delete_defect';
				Ext.Ajax.request({
					url : url,
					params :params,
					method: 'POST',
					success: function ( result, request ) {
						if(result.responseText=="1"){
							defect_store.reload();

							var defect_EastPanel = Ext.getCmp('defect_EastPanel');
							defect_EastPanel.collapse();
							defect_EastPanel.removeAll();
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
		});
	}else{
		Ext.Msg.alert("OTM",Otm.def +' ' + Otm.com_msg_NotSelectData);
	}
}

function defect_defectGrid_select(smObj, record, rowIndex)
{
	Ext.getCmp('defect_EastPanel').removeAll();
	var defect_EastPanel = Ext.getCmp('defect_EastPanel');

	if(Ext.getCmp("defect_defectGrid").getSelectionModel().selected.length > 1){
		defect_EastPanel.collapse();
		return;
	}

	defect_EastPanel.setTitle(Otm.com_view);
	if(defect_EastPanel.collapsed==false){
	}else{
		defect_EastPanel.expand();
	}

	var obj ={
		target : 'defect_EastPanel',
		df_seq : record.data.df_seq,
		pr_seq : record.data.otm_project_pr_seq
	};
	get_defect_view_panel(obj);

	return;
}

var assign_role = false;
if((member_role_store && member_role_store['defect_assign']) || mb_is_admin == 'Y'){
}else{
	assign_role = true;
};

function form_reset()
{
	Ext.getCmp('defect_EastPanel').removeAll();

	var defect_seqForm = {
		id: 'defect_seqForm',
		name: 'defect_seqForm',
		anchor: '100%',
		allowBlank : true,
		xtype: 'hiddenfield'
	};
	var defect_subjectForm = {
		id: 'defect_subjectForm',
		name:'defect_subjectForm',
		anchor: '0',
		minLength:2,
		maxLength:100,
		fieldLabel: Otm.com_subject+'(*)',
		allowBlank : false,
		xtype: 'textfield'
	};
	var defect_descriptionForm = {
		id: 'defect_descriptionForm',
		name:'defect_descriptionForm',
		anchor: '0',
		fieldLabel: Otm.com_description+'(*)',
		allowBlank : false,
		grow : true,
		growMax: 400,
		growMin: 100,
		xtype: 'textarea'
	};
	var defect_severityForm = Ext.create('Ext.form.ComboBox', {
		id:'defect_severityForm',
		name:'defect_severityForm',
		editable: false,
		fieldLabel: Otm.def_severity,
		displayField: 'pco_name',
		valueField:'pco_seq',
		store: defect_severity_store,
		value: severity_value,
		minChars: 0,
		allowBlank : true,
		queryParam: 'q',
		queryMode: 'local'
	});
	var defect_priorityForm = Ext.create('Ext.form.ComboBox', {
		id:'defect_priorityForm',name:'defect_priorityForm',
		editable: false,
		fieldLabel: Otm.def_priority,
		displayField: 'pco_name',
		valueField:'pco_seq',
		store: defect_priority_store,
		value: priority_value,
		minChars: 0,
		allowBlank : true,
		queryParam: 'q',
		queryMode: 'local'
	});
	var defect_frequencyForm = Ext.create('Ext.form.ComboBox', {
		id:'defect_frequencyForm',name:'defect_frequencyForm',
		editable: false,
		fieldLabel: Otm.def_frequency,
		displayField: 'pco_name',
		valueField:'pco_seq',
		store: defect_frequency_store,
		value: frequency_value,
		minChars: 0,
		allowBlank : true,
		queryParam: 'q',
		queryMode: 'local'
	});

	var defect_statusForm = Ext.create('Ext.form.ComboBox', {
		id:'defect_statusForm',name:'defect_statusForm',
		editable: false,
		fieldLabel: Otm.def_status,
		displayField: 'pco_name',
		valueField:'pco_seq',
		store: defect_status_store,
		value: status_value,
		minChars: 0,
		allowBlank : true,
		queryParam: 'q',
		queryMode: 'local'
	});

	var defect_contactForm = {
		id:'defect_assign_member',name:'defect_assign_member',
		xtype:'combo',
		editable: false,
		fieldLabel: Otm.com_user,
		displayField: 'mb_name',
		valueField:'mb_email',
		action_type : 'defect_assign',
		disabled : assign_role,
		store: defect_contact_store,
		minChars: 0,
		allowBlank : true,
		queryParam: 'q',
		queryMode: 'local'
	};

	var defect_dateForm = {
		layout: 'hbox',
		xtype: 'fieldcontainer',
		fieldLabel: Otm.com_start_date,
		combineErrors: false,
		items: [{
			xtype: 'datefield',
			id:'defect_start_date',name:'defect_start_date',
			width: 90,
			format:"Y-m-d",editable: false,
			endDateField: 'defect_end_date',vtype: 'daterange',
			allowBlank: true
		},{
			xtype: 'displayfield',
			style:'padding-left:30px;',
			value: Otm.com_end_date
		},{
			xtype: 'datefield',
			id:'defect_end_date',name:'defect_end_date',
			bodyStyle:'padding-left:30px;',
			startDateField: 'defect_start_date',vtype: 'daterange',
			format:"Y-m-d",editable: false,
			width: 90,
			allowBlank: true
		}]
	};
	var defect_fileForm = {
		xtype:'panel',
		border:false,
		items:[{
			layout:'hbox',
			xtype: 'fieldcontainer',
			fieldLabel: Otm.com_attached_file,
			combineErrors: false,
			defaults: {
				hideLabel: true
			},
			items: [{
				xtype: 'filefield',
				name : 'form_file[]',
				allowBlank : true,
				reference: 'basicFile'
			},{
				xtype:'panel',
				border:false,width:5
			},{
				xtype:'button',
				bodyStyle:'padding-left:10px;background-color:white;',
				text:Otm.com_add,
				handler:function(btn){
					Ext.getCmp("addFileFormPanel").add({
						xtype: 'filefield',
						name : 'form_file[]',
						fieldLabel:Otm.com_attached_file,
						reference: 'basicFile'
					});
				}
			}]
		},{
			layout:'vbox',
			border:false,
			id:'addFileFormPanel'
		}]
	};
	var defectCustomForm = {
		xtype:'panel',
		id:'defect_customForm',
		border:false,
		width:'100%'
	};
	var defect_writeForm = Ext.create("Ext.form.Panel",{
		region		: 'center',
		id			: 'defect_writeForm',
		collapsible	: false,
		border		: false,
		bodyStyle	: 'padding: 10px;',
		autoScroll	: true,
		labelWidth	: '10',
		items: [defect_seqForm,defect_subjectForm,defect_descriptionForm,defect_severityForm,defect_priorityForm,defect_frequencyForm,defect_statusForm,defect_contactForm,defect_dateForm,defectCustomForm,defect_fileForm],
		buttons:[{
			text:Otm.com_save,
			disabled: true,
			formBind: true,
			iconCls:'ico-save',
			handler:function(btn){
				defect_save('save');
			}
		}]
	});

	defect_customform_store.load({
		callback: function(r,options,success){
			tmp_customform = _setCustomform('ID_DEFECT',r);

			Ext.getCmp("defect_customForm").add(tmp_customform);
			Ext.getCmp('defect_EastPanel').add(defect_writeForm);

			for(var i=0;i<r.length;i++){
				var add_chk = true;
				for(var key in defect_defectGrid_column){
					if(defect_defectGrid_column[key].dataIndex == "_"+r[i].data.pc_seq){
						add_chk = false;
						break;
					}
				}
				if(add_chk){
					if(r[i].data.pc_is_display == 'Y'){
						defect_searchField_store.add({'Key': r[i].data.pc_seq, 'Name': r[i].data.pc_name});

						defect_defectGrid_column.push({
							 header: r[i].data.pc_name,  dataIndex: "_"+r[i].data.pc_seq, align:'center'
						});
					}
				}
			}
			Ext.getCmp('defect_defectGrid').reconfigure(undefined,defect_defectGrid_column);

			if(Ext.getCmp("defect_defectGrid").getSelectionModel().selected.length >= 1){
				defect_writeForm.reset();

				Ext.Ajax.request({
					url : "./index.php/Plugin_view/defect/view_defect",
					params :{
						df_seq : Ext.getCmp("defect_defectGrid").getSelectionModel().selected.items[0].data.df_seq
					},
					method: 'POST',
					success: function ( result, request ) {
						var selItem = Ext.getCmp("defect_defectGrid").getSelectionModel().selected.items[0];

						if(result.responseText){
							var defect_info = Ext.decode(result.responseText);

							var default_fieldset = {
								xtype		: 'fieldset',
								collapsible	: false,
								collapsed	: false,
								border		: false,
								items		: [{
									xtype		: 'displayfield',
									fieldLabel	: 'ID',
									value		: defect_info.data.df_id
								},{
									xtype		: 'displayfield',
									fieldLabel	: Otm.com_creator,
									value		: defect_info.data.writer
								},{
									xtype		: 'displayfield',
									fieldLabel	: Otm.com_date,
									value		: defect_info.data.regdate.substr(0,10)
								},{xtype : 'menuseparator',width : '100%'}]
							};
							var view_form = {
								region		: 'north',
								xtype		: 'form',
								collapsible : false,
								border		: false,
								bodyStyle	: 'padding: 10px;',
								autoScroll	: true,
								items	: [default_fieldset]
							};

							Ext.getCmp('defect_EastPanel').add(view_form);

							selItem.data.defect_seqForm = defect_info.data.df_seq;
							selItem.data.defect_subjectForm = defect_info.data.df_subject;
							selItem.data.defect_descriptionForm = defect_info.data.df_description;
							selItem.data.defect_start_date = defect_info.data.dc_start_date;
							selItem.data.defect_end_date = defect_info.data.dc_end_date;

							selItem.data.defect_assign_member = defect_info.data.dc_to_seq;

							var df_customform = defect_info.data.df_customform;
							_setCustomform_userdata(customform_seq,df_customform);

							var temp_status_store = Ext.create('Ext.data.Store', {
								fields:['pco_seq', 'pco_name'],
								proxy: {
									type: 'memory',
									reader: {
										type: 'json',
										rootProperty: 'items'
									}
								}
							});

							if(mb_is_admin == 'Y'){
								temp_status_store = defect_status_store;
							}else{
								var record = defect_status_store.findRecord('pco_seq', defect_info.data.status_seq);
								temp_status_store.add({
									pco_seq	: record.get('pco_seq'),
									pco_name : record.get('pco_name')
								});

								for(var i=0; i<defect_workflow.length;i++){
									if(defect_workflow[i].from_status == defect_info.data.status_seq
										&& defect_workflow[i].from_status != defect_workflow[i].to_status)
									{
										var record = defect_status_store.findRecord('pco_seq', defect_workflow[i].to_status);

										temp_status_store.add({
											pco_seq	: record.get('pco_seq'),
											pco_name : record.get('pco_name')
										});
									}
								}
							}

							Ext.getCmp("defect_statusForm").bindStore(temp_status_store);

							if(defect_info.data.status_name){
								Ext.getCmp("defect_statusForm").setValue(defect_info.data.status_seq);
							}
							if(defect_info.data.severity_name){
								Ext.getCmp("defect_severityForm").setValue(defect_info.data.df_severity);
							}
							if(defect_info.data.priority_name){
								Ext.getCmp("defect_priorityForm").setValue(defect_info.data.df_priority);
							}
							if(defect_info.data.frequency_name){
								Ext.getCmp("defect_frequencyForm").setValue(defect_info.data.df_frequency);
							}

							if(defect_info.data.dc_to_seq >0){
								selItem.data.d_assign_member = defect_info.data.dc_to_seq;
							}
							defect_writeForm.loadRecord(selItem);
						}
					},
					failure: function ( result, request ) {
						Ext.Msg.alert("OTM","DataBase Select Error");
					}
				});

			}else{
				defect_writeForm.reset();

				var temp_status_store = Ext.create('Ext.data.Store', {
					fields:['pco_seq', 'pco_name'],
					proxy: {
						type: 'memory',
						reader: {
							type: 'json',
							rootProperty: 'items'
						}
					}
				});
				if(mb_is_admin == 'Y'){
					temp_status_store = defect_status_store;
				}else{
					var record = defect_status_store.findRecord('pco_seq', status_value);

					temp_status_store.add({
						pco_seq	: record.get('pco_seq'),
						pco_name : record.get('pco_name')
					});

					for(var i=0; i<defect_workflow.length;i++){
						if(defect_workflow[i].from_status == status_value
							&& defect_workflow[i].from_status != defect_workflow[i].to_status)
						{
							var record = defect_status_store.findRecord('pco_seq', defect_workflow[i].to_status);

							temp_status_store.add({
								pco_seq	: record.get('pco_seq'),
								pco_name : record.get('pco_name')
							});
						}
					}
				}

				Ext.getCmp("defect_statusForm").bindStore(temp_status_store);

				Ext.getCmp("defect_statusForm").setValue(status_value);
				Ext.getCmp("defect_severityForm").setValue(severity_value);
				Ext.getCmp("defect_priorityForm").setValue(priority_value);
				Ext.getCmp("defect_frequencyForm").setValue(frequency_value);
			}
		}
	});
}

defect_save = function(saveType){

	var URL = "./index.php/Plugin_view/defect/create_defect";
	if(Ext.getCmp("defect_seqForm").getValue() >= 1){
		URL = "./index.php/Plugin_view/defect/update_defect";
	}
	var df_seq = Ext.getCmp("defect_seqForm").getValue();

	var user_customform_result = new Array();
	var commit_info = Ext.getCmp("defect_writeForm").getForm().getValues();
	for(var i=0;i<customform_seq.length;i++){
		user_customform_result.push({
			name	: customform_seq[i].name,
			seq		: customform_seq[i].seq,
			type	: customform_seq[i].type,
			value	: eval("commit_info.custom_"+customform_seq[i].seq)
		});
	}

	var select = Ext.getCmp("defect_defectGrid").getSelectionModel().selected;

	if(Ext.getCmp("defect_writeForm").getForm().isValid()){

		var params = {
				writer : (select.items[0] && select.items[0].data.writer)?select.items[0].data.writer:'',
				project_seq	: <?php print $project_seq?>,
				custom_form : Ext.encode(user_customform_result)
			};

		if(assign_role){
			params.defect_assign_member = Ext.getCmp('defect_assign_member').getValue();
		}

		Ext.getCmp("defect_writeForm").getForm().submit({
			url: URL,
			method:'POST',
			params: params,
			success: function(rsp, o){
				var info = Ext.decode(o.response.responseText);
				if(info.data && info.data.msg && info.data.msg == 'over_num'){
					Ext.Msg.alert('OTM',Otm.id_rule.over_id_number_msg);
					return;
				}else if(info.data && info.data.msg && info.data.msg == 'empty'){
					Ext.Msg.alert('OTM','Please, set defect id rule.');
					return;
				}

				defect_store.reload({
					callback:function(){
						if(!df_seq){
							Ext.getCmp("defect_defectGrid").getSelectionModel().select(0);
						}else{
							for(var i=0;i<defect_store.data.length;i++){
								if(defect_store.data.items[i].data.df_seq == df_seq){
									Ext.getCmp("defect_defectGrid").getSelectionModel().select(i);
								}
							}
						}
					}
				})
				return;
			},
			failure: function(rsp, result, r){
				var rep = Ext.decode(result.response.responseText);
				if(rep && rep.msg){
					Ext.Msg.alert('OTM',rep.msg);
				}
				return;
			}
		});
	}
}

var status_value,severity_value,priority_value,frequency_value;
var customform_seq = new Array();
var fileCnt = 0;

function set_add_searchForm(btn,is_return)
{
	search_condition++;

	var form_name = 'search_Form_'+search_condition;
	var sfl = 'sfl_'+search_condition;
	var stx = 'stx_'+search_condition;
	var sop = 'sop_'+search_condition;
	var search_start_date = 'search_start_date_'+search_condition;
	var search_end_date = 'search_end_date_'+search_condition;
	var search_status = 'search_status_'+search_condition;
	var search_userform_combo = 'search_userform_combo_'+search_condition;

	var search_Form = {
		xtype: 'container',
		layout: 'hbox',
		id : form_name,
		border:false,
		margin: '5px px 0 30px;',
		items:[{
			xtype:'combo',width:'50px',
			name:sop,id:sop,
			hidden : (search_condition == 1 || is_return==true)?true:false,
			editable: false,
			displayField: 'pco_name',
			valueField:'pco_seq',
			store: ['AND','OR'],
			value:'AND',
			allowBlank : true,
			queryParam: 'q',
			queryMode: 'local'
		},{
			xtype: 'combo',
			style:(search_condition == 1)?'margin-left:53px;':'margin-left:3px;',
			width:'100px',
			name: sfl,id: sfl,
			condition_value : search_condition,
			editable: false,
			displayField: 'Name',
			valueField:'Key',
			store:defect_searchField_store,
			minChars: 0,
			allowBlank : false,
			queryParam: 'q',
			queryMode: 'local',
			value:'subject',
			listeners: {
				select: function( combo, records, eOpts ) {
					if(records){
						Ext.getCmp("sop_"+this.condition_value).setDisabled(false);

						if(combo.lastValue == "subject" || combo.lastValue == "description" || combo.lastValue == "sub_des" || combo.lastValue == "writer" || combo.lastValue == "charge"){
							Ext.getCmp("stx_"+this.condition_value).setVisible(true);
							Ext.getCmp("search_start_date_"+this.condition_value).setVisible(false);
							Ext.getCmp("search_end_date_"+this.condition_value).setVisible(false);
							Ext.getCmp("search_status_"+this.condition_value).setVisible(false);//search_userform_combo
							Ext.getCmp("search_userform_combo_"+this.condition_value).setVisible(false);
						}else if(combo.lastValue == "regdate"){
							Ext.getCmp("stx_"+this.condition_value).setVisible(false);
							Ext.getCmp("search_start_date_"+this.condition_value).setVisible(true);
							Ext.getCmp("search_end_date_"+this.condition_value).setVisible(true);
							Ext.getCmp("search_status_"+this.condition_value).setVisible(false);
							Ext.getCmp("search_userform_combo_"+this.condition_value).setVisible(false);
						}else if(combo.lastValue == "status"){
							Ext.getCmp("search_status_"+this.condition_value).clearValue();
							Ext.getCmp("search_status_"+this.condition_value).bindStore(defect_status_store);

							Ext.getCmp("stx_"+this.condition_value).setVisible(false);
							Ext.getCmp("search_start_date_"+this.condition_value).setVisible(false);
							Ext.getCmp("search_end_date_"+this.condition_value).setVisible(false);
							Ext.getCmp("search_status_"+this.condition_value).setVisible(true);
							Ext.getCmp("search_userform_combo_"+this.condition_value).setVisible(false);
						}else{
							for(var i=0;i<defect_customform_store.data.length;i++){
								if(combo.lastValue == defect_customform_store.data.items[i].data.pc_seq){
									switch(defect_customform_store.data.items[i].data.pc_formtype){
										case "textfield":
										case "textarea":
											Ext.getCmp("stx_"+this.condition_value).setVisible(true);
											Ext.getCmp("search_start_date_"+this.condition_value).setVisible(false);
											Ext.getCmp("search_end_date_"+this.condition_value).setVisible(false);
											Ext.getCmp("search_status_"+this.condition_value).setVisible(false);
											Ext.getCmp("search_userform_combo_"+this.condition_value).setVisible(false);
										break;
										case "combo":
										case "radio":
											var tmp_store = Ext.create('Ext.data.Store', {
												fields:['name', 'is_required'],
												data : [],
												proxy: {
													type: 'memory',
													reader: {
														type: 'json'
													}
												}
											});
											tmp_store.add(Ext.decode(defect_customform_store.data.items[i].data.pc_content));

											Ext.getCmp("search_userform_combo_"+this.condition_value).clearValue();
											Ext.getCmp("search_userform_combo_"+this.condition_value).bindStore(tmp_store);

											Ext.getCmp("stx_"+this.condition_value).setVisible(false);
											Ext.getCmp("search_start_date_"+this.condition_value).setVisible(false);
											Ext.getCmp("search_end_date_"+this.condition_value).setVisible(false);
											Ext.getCmp("search_status_"+this.condition_value).setVisible(false);
											Ext.getCmp("search_userform_combo_"+this.condition_value).setVisible(true);
										break;
										case "datefield":
											Ext.getCmp("stx_"+this.condition_value).setVisible(false);
											Ext.getCmp("search_start_date_"+this.condition_value).setVisible(true);
											Ext.getCmp("search_end_date_"+this.condition_value).setVisible(true);
											Ext.getCmp("search_status_"+this.condition_value).setVisible(false);
											Ext.getCmp("search_userform_combo_"+this.condition_value).setVisible(false);
										break;
										default:
										break;
									}
									break;
								}
							}
						}
					}
				}
			}
		},{
			xtype: 'textfield',
			style:'margin-left:3px;',
			name: stx,id: stx,
			width: 243,
			allowBlank: false,
			enableKeyEvents: true
		},{
			xtype: 'datefield',hidden:true,style:'margin-left:3px;',
			name:search_start_date,id:search_start_date,
			emptyText: 'From',width: 120,
			format:"Y-m-d",editable: false,
			vtype: 'daterange',
			allowBlank: true
		},{
			xtype: 'datefield',hidden:true,style:'margin-left:3px;',
			name:search_end_date,id:search_end_date,
			emptyText: 'To',width: 120,
			format:"Y-m-d",editable: false,
			vtype: 'daterange',
			allowBlank: true
		},{
			xtype: 'combo',style:'margin-left:3px;',hidden:true,
			name: search_status,id: search_status,
			editable: false,
			displayField: 'pco_name',
			valueField:'pco_seq',
			store:defect_status_store,
			width: 243,minChars: 0,
			queryParam: 'q',queryMode: 'local',allowBlank : false
		},{
			xtype: 'combo',style:'margin-left:3px;',hidden:true,
			name: search_userform_combo,id: search_userform_combo,
			editable: false,
			displayField: 'name',
			valueField:'name',
			store:blank_store,
			width: 243,minChars: 0,
			queryParam: 'q',queryMode: 'local',allowBlank : false
		},{
			xtype:'button',
			text:Otm.com_search_condition_del,
			hidden : (search_condition == 1 || is_return==true)?true:false,
			style:'margin-left:3px;',
			handler:function(btn){
				Ext.getCmp("searchForm").remove(form_name);
			}
		}]
	}
	if(is_return){
		return search_Form;
	}else{
		Ext.getCmp("searchForm").add(search_Form);
	}
}

defect_store.on('beforeload',function(storeObj,option){
	var search_array = new Array();
	for(var i=search_condition;i>0;i--){
		var sfl = 'sfl_'+i;
		var stx = 'stx_'+i;
		var sop = 'sop_'+i;
		var search_start_date = 'search_start_date_'+i;
		var search_end_date = 'search_end_date_'+i;
		var search_status = 'search_status_'+i;
		var search_userform_combo = 'search_userform_combo_'+i;

		if(typeof Ext.getCmp(sfl) != 'undefined'){
			search_array.push({
				sfl						: Ext.getCmp(sfl).getValue(),
				stx						: Ext.getCmp(stx).getValue(),
				sop						: Ext.getCmp(sop).getValue(),
				search_start_date		: Ext.getCmp(search_start_date).getValue(),
				search_end_date			: Ext.getCmp(search_end_date).getValue(),
				search_status			: Ext.getCmp(search_status).getValue(),
				search_userform_combo	: Ext.getCmp(search_userform_combo).getValue()
			});
		}
	}

	if(search_array.length > 0){
		 option.setParams({'search_array':Ext.encode(search_array)});
	}
});

var defect_addBtn = {
	xtype:'button',
	text:Otm.com_add,
	action_type:'defect_add',
	iconCls:'ico-add',
	handler:defect_addBtnListener
}
var defect_editBtn = {
	xtype:'button',
	isPermissionControl:true,
	id:'defect_defect_editBtn',
	text:Otm.com_update,
	iconCls:'ico-update',
	action_type:'defect_edit',
	handler:defect_editBtnListener
}
var defect_deleteBtn = {
	xtype:'button',
	isPermissionControl:true,
	id:'defect_defect_deleteBtn',
	text:Otm.com_remove,
	iconCls:'ico-remove',
	action_type:'defect_delete',
	handler:defect_deleteBtnListener
}
var exportBtn = {
	xtype:'button',
	iconCls:'ico-export',
	action_type:'defect_view_all',
	text:Otm.com_export,
	handler: function (btn){
		export_data('plugin/defect/defect_list_export','project_seq=<?=$project_seq?>');
	}
};
var importBtn = {
	xtype: 'button', text: Otm.com_import,
	iconCls:'ico-import',
	action_type:'defect_add',
	handler:function (btn){
		viewImportWin_defect();
	}
};
var defect_mantisBtn = {
	xtype:'button',
	isPermissionControl:true,
	id:'defect_defect_mantisBtn',
	text:'Get Mantis Data',
	action_type:'defect_add',
	handler:function(btn){
		Ext.Ajax.request({
			url : './index.php/Plugin_view/defect/mantis_connect?name=mc_project_get_issues',
			params :{ project_seq : <?=$project_seq?>},
			method: 'POST',
			success: function ( result, request ) {
				if(result.responseText == "ok"){
					defect_store.reload();
					Ext.Msg.alert('OTM','Mantis Issue Insert Finish!');
				}
			},
			failure: function ( result, request ){
			}
		});
	}
};

var defect_redmineBtn = {
	xtype:'button',
	isPermissionControl:true,
	id:'defect_defect_redmineBtn',
	text:'Get Redmine Data',
	action_type:'defect_add',
	handler:function(btn){
	}
};

var defect_send_mail_btn = {
	xtype	: 'button',
	text	: Otm.com_mail_alram,
	hidden	: (mb_is_admin == 'Y')?false:true,
	handler	: function(btn){
		defect_send_mail();
	}
};

/*
 if you want to ues mantis or redmine connect that input button to 'defect_defectGrid_tbar'.
	- defect_mantisBtn
	- defect_redmineBtn
*/

var defect_defectGrid_tbar = [defect_addBtn,'-',defect_editBtn,'-',defect_deleteBtn,'-',exportBtn,'-',importBtn,'-',defect_send_mail_btn];

var defect_defectGrid_listener = {
	scope:this,
	select: defect_defectGrid_select
};

var defect_defectGrid_column = [{
		text: Otm.com_number,autoResizeWidth: true,
		dataIndex: 'df_seq',align:'center',
		hidden:true,
		width:50,
	},{
		text: 'ID',autoResizeWidth: true,
		dataIndex: 'df_id',
		width: 80
	},{
		text: Otm.com_subject,
		dataIndex: 'df_subject',
		flex:1,
		minWidth:300,
		width: 500
	},{
		text: Otm.com_tc_link_cnt,
		dataIndex: 'tracking_id',align:'center',autoResizeWidth: true,
		width: 50,
		renderer: function(value, metaData, record, rowIndex, colIndex, store){
			if(value){
				var id_array  = value.split(',');
				return id_array.length;
			}else{
				return '';
			}
		}
	},{
		text: Otm.tc_plan, hidden:false,
		dataIndex: 'tp_subject',align:'center',autoResizeWidth: true,
		width: 50
	},{
		text: Otm.com_user,
		dataIndex: 'df_assign_member',align:'center',autoResizeWidth: true,
		width: 50
	},{
		text: Otm.com_creator,
		dataIndex: 'writer_name',align:'center',autoResizeWidth: true,
		width: 50
	},{
		text: Otm.def_status,
		dataIndex: 'df_status',align:'center',autoResizeWidth: true,
		width: 80,
		renderer: function(value, metaData, record, rowIndex, colIndex, store){
			for(var i=0;i<defect_status_store.data.items.length;i++){
				if(defect_status_store.data.items[i].data.pco_seq == value){
					var name = defect_status_store.data.items[i].data.pco_name;
					if(defect_status_store.data.items[i].data.pco_is_required == 'Y'){
						return '<font color=blue>'+name+'</font>';
					}else{
						return name;
					}
				}
			}
			return value;
		}
	},{
		text: Otm.def_severity,autoResizeWidth: true,
		dataIndex: 'df_severity',align:'center',
		width: 80,
		renderer: function(value, metaData, record, rowIndex, colIndex, store){
			for(var i=0;i<defect_severity_store.data.items.length;i++){
				if(defect_severity_store.data.items[i].data.pco_seq == value){
					return defect_severity_store.data.items[i].data.pco_name;
				}
			}
			return value;
		}
	},{
		text: Otm.def_priority,autoResizeWidth: true,
		dataIndex: 'df_priority',align:'center',
		width: 80,
		renderer: function(value, metaData, record, rowIndex, colIndex, store){
			for(var i=0;i<defect_priority_store.data.items.length;i++){
				if(defect_priority_store.data.items[i].data.pco_seq == value){
					return defect_priority_store.data.items[i].data.pco_name;
				}
			}
			return value;
		}
	},{
		text: Otm.def_frequency,autoResizeWidth: true,
		dataIndex: 'df_frequency',align:'center',
		width:80,
		renderer: function(value, metaData, record, rowIndex, colIndex, store){
			for(var i=0;i<defect_frequency_store.data.items.length;i++){
				if(defect_frequency_store.data.items[i].data.pco_seq == value){
					return defect_frequency_store.data.items[i].data.pco_name;
				}
			}
			return value;
		}
	},{
		text: Otm.com_date, dataIndex: 'regdate',autoResizeWidth: true, align:'center',
		width:80, sortable: true, renderer:function(value,index,record){
			if(value){
				var value = value.substr(0,10);
			}else{
				value = '';
			}
			return value;
		}
}];

var defect_defectGrid = Ext.create("Ext.grid.Panel",{
	region:'center',
	id:'defect_defectGrid',
	store:defect_store,
	selModel:Ext.create('Ext.selection.CheckboxModel'),
	border:false,
	forceFit: true,
	columns:defect_defectGrid_column,
	tbar:defect_defectGrid_tbar,
	bbar:Ext.create('Ext.PagingToolbar', {
		id:'defect_page',
		store: defect_store,
		displayInfo: true
	}),
	listeners:defect_defectGrid_listener
});

var defect_subCenterPanel = {
	layout		: 'border',
	id			: 'defect_subCenterPanel',
	defaults	: {
		collapsible	: false,
		split		: true,
		bodyStyle	: 'padding:0px'
	},
	items:[{
		region		: 'center',
		layout		: 'fit',
		collapsible	: false,
		items		: [defect_defectGrid]
	},{
		region		: 'east',
		layout		: 'border',
		id			: 'defect_EastPanel',
		title		: Otm.com_add,
		split		: true,
		collapsible	: true,
		collapsed	: true,
		flex		: 1,
		animation	: false,
		autoScroll	: true,
		minWidth	: 420,
		maxWidth	: 600,
		items		: []
	},{
		region		: 'north',
		layout		: 'border',
		title		: 'Search',
		split		: true,border:false,
		collapsible	: true,
		collapsed	: true,
		flex		: 1,
		autoScroll	: false,
		minHeight	: 150,
		maxHeight	: 150,
		items		: [{
			xtype:'form',
			region:'center',border:false,
			id : 'search_field_center',
			autoScroll	: true,
			items:[{
				id : 'searchForm',
				xtype: 'container',
				layout: 'vbox',
				autoScroll	: true,
				border:false,
				margin: '0 10px 0 0',
				items:[]
			}]
		},{
			xtype:'panel',
			region:'west',border:false,
			bodyStyle : 'padding:5px;',
			minWidth	: 150,
			maxWidth	: 150,
			items:[{
				xtype:'button',style:'width:100%',
				text:Otm.com_search_condition_add,iconCls:'ico-add',
				handler:function(btn){
					set_add_searchForm();
				}
			},{
				xtype:'button',style:'margin-top:5px;width:100%;height:50px;',
				text:Otm.com_search,iconCls:'ico-search',
				handler:function(btn){
					var search_array = new Array();
					for(var i=search_condition;i>0;i--){
						var sfl = 'sfl_'+i;
						var stx = 'stx_'+i;
						var sop = 'sop_'+i;
						var search_start_date = 'search_start_date_'+i;
						var search_end_date = 'search_end_date_'+i;
						var search_status = 'search_status_'+i;
						var search_userform_combo = 'search_userform_combo_'+i;

						if(typeof Ext.getCmp(sfl) != 'undefined'){
							search_array.push({
								sfl						: Ext.getCmp(sfl).getValue(),
								stx						: Ext.getCmp(stx).getValue(),
								sop						: Ext.getCmp(sop).getValue(),
								search_start_date		: Ext.getCmp(search_start_date).getValue(),
								search_end_date			: Ext.getCmp(search_end_date).getValue(),
								search_status			: Ext.getCmp(search_status).getValue(),
								search_userform_combo	: Ext.getCmp(search_userform_combo).getValue()
							});
						}
					}

					defect_store.loadPage(1,{
						params :{
							search_arr : Ext.encode(search_array)
						}
					});
				}
			},{
				xtype:'button',style:'margin-top:5px;width:100%',
				text:Otm.com_reset,iconCls:'ico-reset',
				handler:function(btn){
					Ext.getCmp("searchForm").removeAll();

					search_condition = 0;
					set_add_searchForm();
				}
			}]
		}]
	}]
};

var tmp_customform;
Ext.onReady(function(){

	Ext.getCmp('defect').add(defect_subCenterPanel);
	Ext.getCmp('defect').doLayout(true,false);
	defect_contact_store.load();

	defect_code_store.load({
		callback: function(r,options,success){
			defect_store.reload();

			for(var i=0;i<r.length;i++){
				switch(r[i].data.pco_type){
					case "status":
						defect_status_store.add({
							pco_seq	: r[i].data.pco_seq,
							pco_name : r[i].data.pco_name,
							pco_is_required : r[i].data.pco_is_required
						})
						if(r[i].data.pco_is_default=="Y"){
							status_value = r[i].data.pco_seq;
						}
					break;
					case "severity":
						defect_severity_store.add({
							pco_seq	: r[i].data.pco_seq,
							pco_name : r[i].data.pco_name
						})
						if(r[i].data.pco_is_default=="Y"){
							severity_value = r[i].data.pco_seq;
						}
					break;
					case "priority":
						defect_priority_store.add({
							pco_seq	: r[i].data.pco_seq,
							pco_name : r[i].data.pco_name
						})
						if(r[i].data.pco_is_default=="Y"){
							priority_value = r[i].data.pco_seq;
						}
					break;
					case "frequency":
						defect_frequency_store.add({
							pco_seq	: r[i].data.pco_seq,
							pco_name : r[i].data.pco_name
						})
						if(r[i].data.pco_is_default=="Y"){
							frequency_value = r[i].data.pco_seq;
						}
					break;
				}
			}

			set_add_searchForm();

			form_reset();
		}
	});
});

var testcase_customform_store = Ext.create('Ext.data.Store', {
	fields:['pc_seq','pc_name','pc_is_required','pc_formtype','pc_default_value','pc_content','pc_is_use'],
	proxy: {
		type	: 'ajax',
		url		: './index.php/Project_setup/userform_list',
		extraParams: {
			pr_seq		: <?=$project_seq?>,
			pc_category : 'ID_TC',
			pc_is_use	: 'Y'
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
	autoLoad:true
});
</script>