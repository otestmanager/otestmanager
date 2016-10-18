<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
 include_once($data['skin_dir'].'/locale-'.$data['mb_lang'].'.php');
?>
<script type="text/javascript">
var project_seq = '<?=$project_seq?>';

var requirement_customform_store = Ext.create('Ext.data.Store', {
	fields:['item'],
	proxy: {
		type	: 'ajax',
		url		: './index.php/Project_setup/userform_list',
		extraParams: {
			pr_seq		: project_seq,
			pc_category : 'ID_REQ',
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
var project_user_store = Ext.create('Ext.data.Store', {
	fields:['pm_seq','mb_email','mb_name'],
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
	autoLoad:false
});

function requirement_save(type)
{
	var URL = "./index.php/Plugin_view/requirement/create_requirement";
	if(Ext.getCmp("req_seqForm").getValue() >= 1){
		URL = "./index.php/Plugin_view/requirement/update_requirement";
	}
	var req_seq = Ext.getCmp("req_seqForm").getValue();

	var user_customform_result = new Array();
	var commit_info = Ext.getCmp("requirement_writeForm").getForm().getValues();
	for(var i=0;i<customform_seq.length;i++){
		user_customform_result.push({
			name	: customform_seq[i].name,
			seq		: customform_seq[i].seq,
			type	: customform_seq[i].type,
			value	: eval("commit_info.custom_"+customform_seq[i].seq)
		});
	}

	var select = Ext.getCmp("requirement_requirementGrid").getSelectionModel().selected;

	if(Ext.getCmp("requirement_writeForm").getForm().isValid()){

		var params = {
				pr_seq	: project_seq,
				custom_form : Ext.encode(user_customform_result)
			};

		Ext.getCmp("requirement_writeForm").getForm().submit({
			url: URL,
			method:'POST',
			params: params,
			success: function(rsp, o){
				var info = Ext.decode(o.response.responseText);

				requirement_store.reload({
					callback:function(){
						if(!req_seq){
							Ext.getCmp("requirement_requirementGrid").getSelectionModel().select(0);
						}else{
							for(var i=0;i<requirement_store.data.length;i++){
								if(requirement_store.data.items[i].data.req_seq == req_seq){
									Ext.getCmp("requirement_requirementGrid").getSelectionModel().select(i);
								}
							}
						}
					}
				});
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

function get_requirement_view_panel(obj)
{
	var target_panel = obj.target;

	Ext.getCmp(target_panel).update('');

	Ext.Ajax.request({
		url : "./index.php/Plugin_view/requirement/requirement_info",
		params : obj,
		method: 'POST',
		success: function ( result, request ) {
			if(result.responseText){
				var requirement_info = _getCustomform_view(requirement_customform_store,result.responseText);
				
				//console.log(requirement_info);
				
				//requirement_info.data.regdate = requirement_info.data.regdate.substr(0,10);

				var printFile = _common_fileView('defectGrid',Ext.decode(requirement_info.data.fileform));
				requirement_info.data.fileform = printFile;

				requirement_info.data.req_history = _history_view(Ext.decode(requirement_info.data.req_history));

				var default_fieldset = {
					xtype		: 'fieldset',
					collapsible	: false,
					collapsed	: false,
					border		: false,
					items		: [{
						xtype		: 'displayfield',
						fieldLabel	: Otm.com_creator,
						value		: requirement_info.data.writer
					},{
						xtype		: 'displayfield',
						fieldLabel	: Otm.com_date,
						value		: requirement_info.data.regdate.substr(0,10)
					},{xtype : 'menuseparator',width : '100%'}]
				};

				var content_fieldset = {
						xtype		: 'fieldset',
						collapsible	: false,
						collapsed	: false,
						border		: false,
						items		: [{
							xtype		: 'displayfield',
							fieldLabel	: Otm.com_subject,
							value		: requirement_info.data.req_subject
						},{
							xtype		: 'displayfield',
							fieldLabel	: '중요도',
							value		: requirement_info.data.req_priority
						},{
							xtype		: 'displayfield',
							fieldLabel	: '난이도',
							value		: requirement_info.data.req_difficulty
						},{
							xtype		: 'displayfield',
							fieldLabel	: '수용여부',
							value		: requirement_info.data.req_accept
						},{
							xtype		: 'displayfield', multiline	: true,
							fieldLabel	: Otm.com_description,
							value		: requirement_info.data.req_description
						}]
					};

				var userform_fieldset = {
					xtype		: 'fieldset',
					collapsible	: false,
					collapsed	: false,
					border		: false,
					items		: [{
						border		: false,
						html		: requirement_info.data.user_form
					},{xtype : 'menuseparator',width : '100%'}]
				};

				var attached_file_fieldset = {
					xtype		: 'fieldset',
					title		: Otm.com_attached_file,
					collapsible	: false,
					collapsed	: false,
					//border		: false,
					items		: [{
						border		: false,
						html		: requirement_info.data.fileform
					},{xtype : 'menuseparator',width : '100%'}]
				};

				var history_fieldset = {
					xtype		:'fieldset',
					title		: Otm.com_revision_history,
					collapsible	: true,
					collapsed	: false,
					items		: [{
						border	: false,
						html	: requirement_info.data.req_history
					}]
				};

				var view_form = {
					xtype		: 'form',
					collapsible	: false,
					border		: false,
					bodyStyle	: 'padding: 10px;',
					autoScroll	: true,
					items	: [
						default_fieldset,
						content_fieldset,
						userform_fieldset,
						attached_file_fieldset
						,						history_fieldset
					]
				};

				Ext.getCmp(target_panel).removeAll();
				Ext.getCmp(target_panel).add({
					region	: 'center', layout:'fit', xtype:'panel',
					animation: false, autoScroll: true,
					items : [view_form]
				});
			}
		},
		failure: function ( result, request ) {
			Ext.Msg.alert("OTM","DataBase Select Error");
		}
	});
}

function get_requirement_write_form(obj)
{
	Ext.getCmp('requirement_east_panel').removeAll();
	var requirement_seqForm = {
		id: 'req_seqForm',
		name: 'req_seqForm',
		anchor: '100%',
		allowBlank : true,
		xtype: 'hiddenfield'
	};
	var requirement_subjectForm = {
		id: 'req_subjectForm',
		name:'req_subjectForm',
		anchor: '0',
		minLength:2,
		maxLength:100,
		fieldLabel: Otm.com_subject+'(*)',
		allowBlank : false,
		xtype: 'textfield'
	};

	var requirement_priorityForm = {
		id: 'req_priorityForm',
		name:'req_priorityForm',
		anchor: '0',
		minLength:2,
		maxLength:100,
		fieldLabel: '중요도',
		xtype: 'textfield'
	};

	var requirement_difficultyForm = {
		id: 'req_difficultyForm',
		name:'req_difficultyForm',
		anchor: '0',
		minLength:2,
		maxLength:100,
		fieldLabel: '난이도',
		xtype: 'textfield'
	};

	var requirement_acceptForm = {
		id: 'req_acceptForm',
		name:'req_acceptForm',
		anchor: '0',
		minLength:2,
		maxLength:100,
		fieldLabel: '수용여부',
		xtype: 'textfield'
	};

	var requirement_descriptionForm = {
		id: 'req_descriptionForm',
		name:'req_descriptionForm',
		anchor: '0',
		fieldLabel: Otm.com_description,
		//allowBlank : false,
		grow : true,
		growMax: 400,
		growMin: 100,
		xtype: 'textarea'
	};
	var requirement_fileForm = {
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
	var requirement_customForm = {
		xtype:'panel',
		id:'requirement_customForm',
		border:false,
		width:'100%'
	};
	
	/*
	if(obj.action_type == 'edit'){
		var URL = './index.php/Plugin_view/requirement/requirement_info';		
		Ext.Ajax.request({
			url : URL,
			params : {
				req_seq : record.data.req_seq,
				pr_seq : record.data.otm_project_pr_seq
				action_type : obj.action_type
			},
			method: 'POST',
			success: function ( result, request ) {
				if(result.responseText){
					var requirement_info = Ext.decode(result.responseText);
					var selItem = Ext.getCmp('requirement_requirementGrid').getSelectionModel().selected.items[0];
				}
				requirement_customform_store.load({
					callback: function(r,options,success){				
						Ext.getCmp('requirement_customForm').add(_setCustomform('ID_REQ',r));

						selItem.data.req_subject			= requirement_info.data[0].req_subject;
						selItem.data.req_description		= requirement_info.data[0].req_description;

						Ext.getCmp('requirement_writeForm').loadRecord(selItem);						
					}
				});	
				
			},
			failure: function ( result, request ){
				Ext.Msg.alert("OTM","Error");				
			}
		});
	}else{
		requirement_customform_store.load({
			callback: function(r,options,success){				
				Ext.getCmp('requirement_customForm').add(_setCustomform('ID_REQ',r));
			}
		});
	}
	*/


	var requirement_writeForm = Ext.create("Ext.form.Panel",{
		region		: 'center',
		id			: 'requirement_writeForm',
		collapsible	: false,
		border		: false,
		bodyStyle	: 'padding: 10px;',
		autoScroll	: true,
		labelWidth	: '10',
		items: [
			requirement_seqForm,
			requirement_subjectForm,
			requirement_priorityForm,
			requirement_difficultyForm,
			requirement_acceptForm,
			requirement_descriptionForm,
			requirement_customForm,
			requirement_fileForm
		],
		buttons:[{
			text:Otm.com_save,
			id		: 'requirement_writeBtn',
			disabled: true,
			formBind: true,
			iconCls:'ico-save',
			handler:function(btn){
				requirement_save('save');
			}
		}]
	});

	
	requirement_customform_store.load({
		callback: function(r,options,success){				
			Ext.getCmp('requirement_customForm').add(_setCustomform('ID_REQ',r));
			var requirement_east_panel = Ext.getCmp('requirement_east_panel');
			requirement_east_panel.add(requirement_writeForm);
			requirement_east_panel.doLayout(true,false);

			if(Ext.getCmp("requirement_requirementGrid").getSelectionModel().selected.length >= 1){
				Ext.getCmp('requirement_writeForm').reset();

				Ext.Ajax.request({
					url : './index.php/Plugin_view/requirement/requirement_info',
					params :{
						req_seq : Ext.getCmp("requirement_requirementGrid").getSelectionModel().selected.items[0].data.req_seq
					},
					method: 'POST',
					success: function ( result, request ) {
						var selItem = Ext.getCmp("requirement_requirementGrid").getSelectionModel().selected.items[0];

						if(result.responseText){
							var req_info = Ext.decode(result.responseText);

							var default_fieldset = {
								xtype		: 'fieldset',
								collapsible	: false,
								collapsed	: false,
								border		: false,
								items		: [{
									xtype		: 'displayfield',
									fieldLabel	: Otm.com_creator,
									value		: req_info.data.writer
								},{
									xtype		: 'displayfield',
									fieldLabel	: Otm.com_date,
									value		: req_info.data.regdate.substr(0,10)
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

							Ext.getCmp('requirement_east_panel').add(view_form);

							selItem.data.req_seqForm = req_info.data.req_seq;
							selItem.data.req_subjectForm = req_info.data.req_subject;
							selItem.data.req_descriptionForm = req_info.data.req_description;
							selItem.data.req_priorityForm = req_info.data.req_priority;
							selItem.data.req_difficultyForm = req_info.data.req_difficulty;
							selItem.data.req_acceptForm = req_info.data.req_accept;

							var df_customform = req_info.data.df_customform;
							_setCustomform_userdata(customform_seq,df_customform);

							Ext.getCmp('requirement_writeForm').loadRecord(selItem);
						}
					},
					failure: function ( result, request ) {
						Ext.Msg.alert("OTM","DataBase Select Error");
					}
				});

			}else{

			}
		}
	});
}

function requirement_write_click(btn)
{
	var requirement_main_panel = Ext.getCmp("requirement_main_panel");
	var requirement_east_panel = Ext.getCmp('requirement_east_panel');

	var obj = {
		action_type : 'add'
	}

	if(btn.id == "requirement_add_btn"){
		Ext.getCmp("requirement_requirementGrid").getSelectionModel().deselectAll();
		requirement_east_panel.setTitle(Otm.com_add);
	}else if(btn.id == "requirement_edit_btn"){
		switch(Ext.getCmp('requirement_requirementGrid').getSelectionModel().selected.length){
			case 0:
				Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
				return;
			break;
			case 1:
				requirement_east_panel.setTitle(Otm.com_update);
				obj.action_type = 'edit';
			break;
			default:
				Ext.Msg.alert("OTM",Otm.com_msg_only_one);
				return;
			break;
		}
	}	

	
	if(requirement_east_panel.collapsed==false){
	}else{
		requirement_east_panel.setWidth(requirement_main_panel.getWidth()/2);
		requirement_east_panel.expand();
	}

	get_requirement_write_form(obj);
}
function requirement_delete_click(btn)
{

	if(Ext.getCmp("requirement_requirementGrid").getSelectionModel().selected.length >= 1){

		var req_list = Array();
		var Records = Ext.getCmp("requirement_requirementGrid").getSelectionModel().selected.items;

		for(var i=0; i<Records.length; i++){
			req_list.push(Records[i].data['req_seq']);
		}

		Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
			if(bt=='yes'){
				var params = {
					pr_seq	: project_seq,
					req_list		: Ext.encode(req_list),
					writer		: Ext.getCmp("requirement_requirementGrid").getSelectionModel().selected.items[0].data.writer
				};
				var url = './index.php/Plugin_view/requirement/delete_requirement';
				Ext.Ajax.request({
					url : url,
					params :params,
					method: 'POST',
					success: function ( result, request ) {
						if(result.responseText){
							//var selItem = Ext.getCmp('requirement_requirementGrid').getSelectionModel().getSelection();
							//Ext.getCmp('requirement_requirementGrid').getStore().remove(selItem);
							requirement_store.reload();

							var requirement_east_panel = Ext.getCmp('requirement_east_panel');
							requirement_east_panel.collapse();
							requirement_east_panel.removeAll();
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
		Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
	}

	/*
	switch(Ext.getCmp('requirement_requirementGrid').getSelectionModel().selected.length){
		case 0:
			Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
			return;
		break;		
		default:
			Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
				if(bt=='yes'){
					var selItem = Ext.getCmp('requirement_requirementGrid').getSelectionModel().getSelection();
					Ext.getCmp('requirement_requirementGrid').getStore().remove(selItem);
					
					var requirement_east_panel = Ext.getCmp('requirement_east_panel');
					requirement_east_panel.collapse();
					requirement_east_panel.removeAll();
				}else{
					return;
				}
			});
		break;
	}
	*/
}
function get_requirement_assign_form(btn)
{
	//var requirement_main_panel = Ext.getCmp("requirement_main_panel");
	var requirement_east_panel = Ext.getCmp('requirement_east_panel');

	requirement_east_panel.setTitle(Otm.tc_assign_persion);
	requirement_east_panel.expand();
	requirement_east_panel.removeAll();
	requirement_east_panel.update();

	var requirement_project_user_grid = Ext.create("Ext.grid.Panel",{
		region	: 'center',
		title	: Otm.pjt+' '+Otm.com_memberlist,
		id		: 'requirement_project_user_grid',
		store	: project_user_store,
		verticalScrollerType:'paginggridscroller',
		invalidateScrollerOnRefresh:true,
		viewConfig: {
			listeners: {
				refresh: function(dataView) {
					Ext.each(dataView.panel.columns, function(column) {
					if (column.autoResizeWidth)
						column.autoSize();
					});
				},
				viewready: function(){
					this.store.load({params:{pr_seq:project_seq}});
				}
			}
		},
		columns: [
			Ext.create('Ext.grid.RowNumberer'),
			{header: Otm.com_name,			dataIndex: 'mb_name',	flex: 1, minWidth:100 },
			{header: Otm.com_group,			dataIndex: 'user_group_name',	minWidth:50, autoResizeWidth:true},
			{header: Otm.com_role,			dataIndex: 'user_role_name',	minWidth:50, autoResizeWidth:true},
			{header: Otm.com_email,			dataIndex: 'mb_email',		minWidth:50, autoResizeWidth:true}
		],
		buttons:[{
			text:Otm.com_save,
			iconCls:'ico-save',
			handler:function(btn){
				var params = {};

				//요구사항 선택 확인
				var Records = Ext.getCmp('requirement_requirementGrid').getSelectionModel().selected.items;
				var req_list = Array();

				if(Records.length >= 1){
					for(var i=0; i<Records.length; i++){
							req_list.push(Records[i].data['req_seq']);
					}
					params.req_list = Ext.encode(req_list);
				}else{
					Ext.Msg.alert('OTM',Otm.com_msg_select_items_assign);
					return;
				}

				//담당자 선택 확인
				var selItem = Ext.getCmp('requirement_project_user_grid').getSelectionModel().selected.items[0];
				if(selItem){
					params.assign_to = selItem.data.mb_email;
				}else{
					Ext.Msg.alert('OTM',Otm.com_msg_responsible_person);
					return;
				}

				Ext.Ajax.request({
					url : './index.php/Plugin_view/requirement/assign_requirement',
					params : params,
					method: 'POST',
					success: function ( result, request ) {
						//var obj = Ext.decode(result.responseText);
						//var node = Ext.decode(obj.data);					
						requirement_east_panel.removeAll();
						requirement_east_panel.add(get_requirement_assign_form());
						requirement_store.reload();

						return;
					},
					failure: function ( result, request ){
					}
				});
			}
		}]
	});
	
	requirement_east_panel.add(requirement_project_user_grid);	
	requirement_east_panel.doLayout(true,false);
}

function requirement_center_click(smObj, record, rowIndex)
{
	Ext.getCmp('requirement_east_panel').removeAll();
	var requirement_east_panel = Ext.getCmp('requirement_east_panel');

	if(Ext.getCmp("requirement_requirementGrid").getSelectionModel().selected.length > 1){
		requirement_east_panel.collapse();
		return;
	}

	requirement_east_panel.setTitle(Otm.com_view);
	if(requirement_east_panel.collapsed==false){
	}else{
		requirement_east_panel.expand();
	}

	var obj ={
		target : 'requirement_east_panel',
		req_seq : record.data.req_seq,
		pr_seq : record.data.otm_project_pr_seq
	};
	get_requirement_view_panel(obj);

	return;
}

var requirement_addBtn = {
	xtype	: 'button',
	text	: Otm.com_add,
	id		: 'requirement_add_btn',
	iconCls	: 'ico-add',
	handler	: requirement_write_click
}
var requirement_editBtn = {
	xtype	: 'button',
	text	: Otm.com_update,
	id		: 'requirement_edit_btn',
	iconCls	: 'ico-update',
	handler	: requirement_write_click
}
var requirement_deleteBtn = {
	xtype	: 'button',
	text	: Otm.com_remove,
	id		: 'requirement_delete_btn',
	iconCls	: 'ico-remove',
	handler	: requirement_delete_click
}
var requirement_assignBtn = {
	xtype	: 'button',
	text	: Otm.tc_assign_persion,
	id		: 'requirement_assign_btn',
	iconCls	: 'ico-man',
	handler	: get_requirement_assign_form
}

var requirement_exportBtn = {
	xtype	: 'button',
	iconCls	: 'ico-export',
	text	: Otm.com_export,
	disabled: true,
	handler	: function (btn){
		//export_data('plugin/requirement/export','project_seq=<?=$project_seq?>');
	}
};
var requirement_importBtn = {
	xtype	: 'button', text: Otm.com_import,
	iconCls	:'ico-import',
	disabled: true,
	handler	: function (btn){
	}
};

var requirement_east_panel = {
	region		: 'east',
	layout		: 'border',
	id			: 'requirement_east_panel',
	xtype		: 'panel',
	minWidth	: 200,
	flex		: 1,
	collapsible	: true,
	split		: true,
	autoScroll	: true,
	collapsed	: true
}

var requirement_store = Ext.create('Ext.data.Store', {
	fields:[
		'req_subject','req_priority','req_difficulty','req_accept','req_assign','writer','regdate'
	],
	pageSize: 50,
	proxy: {
		type	: 'ajax',
		url		: './index.php/Plugin_view/requirement/requirement_list',
		extraParams: {
			pr_seq : project_seq
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


var requirement_center_panel =  {
	region	: 'center',
	layout	: 'fit',
	xtype	: 'gridpanel',
	id		: 'requirement_requirementGrid',
	multiSelect: true,
	store	: requirement_store,
	columns	: [
		{header: Otm.requirement.subject,	dataIndex: 'req_subject',		minWidth:300,		flex: 3},
		//{header: 'ID',					dataIndex: 'req_id',		minWidth:80},
		{header: '중요도',					dataIndex: 'req_priority',		minWidth:50},
		{header: '난이도',					dataIndex: 'req_difficulty',		minWidth:50},
		{header: '수용여부',				dataIndex: 'req_accept',		minWidth:50},

		{header: '담당자',				dataIndex: 'req_assign',		minWidth:50},

		{header: Otm.com_creator,			dataIndex: 'writer',		minWidth:80},
		{header: Otm.com_date,				dataIndex: 'regdate',		minWidth:80}
	],
	tbar:[requirement_addBtn,'-',requirement_editBtn,'-',requirement_deleteBtn,'-',requirement_assignBtn,'-',requirement_exportBtn,'-',requirement_importBtn],
	listeners:{
		scope:this,
		select: requirement_center_click
	}
}		

Ext.onReady(function(){
	var main_panel = {
		layout		: 'border',
		id			: 'requirement_main_panel',
		defaults	: {
			collapsible	: false,
			split		: false,
			bodyStyle	: 'padding:0px'
		},
		items		: [requirement_center_panel,requirement_east_panel]
	};

	Ext.getCmp('requirement').add(main_panel);
	Ext.getCmp('requirement').doLayout(true,false);
	requirement_store.load();
});
</script>