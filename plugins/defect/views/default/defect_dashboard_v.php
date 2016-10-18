<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script type="text/javascript">
/*
	Defect Info Data Update
*/
function defect_dashboard_form_reset(obj)
{
	var defect_dashboard_severity_store = Ext.create('Ext.data.Store', {
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
	var defect_dashboard_priority_store = Ext.create('Ext.data.Store', {
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
	var defect_dashboard_frequency_store = Ext.create('Ext.data.Store', {
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

	var defect_dashboard_status_store = Ext.create('Ext.data.Store', {
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

	var defect_dashboard_code_store = Ext.create('Ext.data.Store', {
		fields:['pco_seq', 'pco_type','pco_name','pco_is_required','pco_is_default','pco_default_value'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/defect/code_list',
			extraParams: {
				project_seq : obj.pr_seq
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

	defect_dashboard_code_store.load({
		callback: function(r,options,success){

			for(var i=0;i<r.length;i++){
				switch(r[i].data.pco_type){
					case "status":
						defect_dashboard_status_store.add({
							pco_seq	: r[i].data.pco_seq,
							pco_name : r[i].data.pco_name,
							pco_is_required : r[i].data.pco_is_required
						})
						if(r[i].data.pco_is_default=="Y"){
							status_value = r[i].data.pco_seq;
						}
					break;
					case "severity":
						defect_dashboard_severity_store.add({
							pco_seq	: r[i].data.pco_seq,
							pco_name : r[i].data.pco_name
						})
						if(r[i].data.pco_is_default=="Y"){
							severity_value = r[i].data.pco_seq;
						}
					break;
					case "priority":
						defect_dashboard_priority_store.add({
							pco_seq	: r[i].data.pco_seq,
							pco_name : r[i].data.pco_name
						})
						if(r[i].data.pco_is_default=="Y"){
							priority_value = r[i].data.pco_seq;
						}
					break;
					case "frequency":
						defect_dashboard_frequency_store.add({
							pco_seq	: r[i].data.pco_seq,
							pco_name : r[i].data.pco_name
						})
						if(r[i].data.pco_is_default=="Y"){
							frequency_value = r[i].data.pco_seq;
						}
					break;
				}
			}
		}
	});

	var defect_dashboard_contact_store = Ext.create('Ext.data.Store', {
		fields:['mb_email','mb_name'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Project_setup/project_userlist',
			extraParams: {
				pr_seq : obj.pr_seq
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
	defect_dashboard_contact_store.load({
		callback:function(){
			defect_dashboard_contact_store.sort("mb_name","asc");
		}
	});

	Ext.getCmp('defect_dashboard_EastPanel').removeAll();

	var defect_dashboard_seqForm = {
		id: 'defect_dashboard_seqForm',
		name: 'defect_dashboard_seqForm',
		anchor: '100%',
		allowBlank : true,
		xtype: 'hiddenfield'
	};

	var defect_dashboard_subjectForm = {
		id: 'defect_dashboard_subjectForm',
		name:'defect_dashboard_subjectForm',
		anchor: '0',
		minLength:2,
		maxLength:100,
		fieldLabel: Otm.com_subject+'(*)',
		allowBlank : false,
		xtype: 'textfield'
	};

	var defect_dashboard_descriptionForm = {
		id: 'defect_dashboard_descriptionForm',
		name:'defect_dashboard_descriptionForm',
		anchor: '0',
		fieldLabel: Otm.com_description+'(*)',
		allowBlank : false,
		grow : true,
		growMax: 400,
		growMin: 100,
		xtype: 'textarea'
	};

	var defect_dashboard_severityForm = Ext.create('Ext.form.ComboBox', {
		id:'defect_dashboard_severityForm',
		name:'defect_dashboard_severityForm',
		editable: false,
		fieldLabel: Otm.def_severity,
		displayField: 'pco_name',
		valueField:'pco_seq',
		store: defect_dashboard_severity_store,
		//value: severity_value,
		minChars: 0,
		allowBlank : true,
		queryParam: 'q',
		queryMode: 'local'
	});

	var defect_dashboard_priorityForm = Ext.create('Ext.form.ComboBox', {
		id:'defect_dashboard_priorityForm',name:'defect_dashboard_priorityForm',
		editable: false,
		fieldLabel: Otm.def_priority,
		displayField: 'pco_name',
		valueField:'pco_seq',
		store: defect_dashboard_priority_store,
		minChars: 0,
		allowBlank : true,
		queryParam: 'q',
		queryMode: 'local'
	});

	var defect_dashboard_frequencyForm = Ext.create('Ext.form.ComboBox', {
		id:'defect_dashboard_frequencyForm',name:'defect_dashboard_frequencyForm',
		editable: false,
		fieldLabel: Otm.def_frequency,
		displayField: 'pco_name',
		valueField:'pco_seq',
		store: defect_dashboard_frequency_store,
		minChars: 0,
		allowBlank : true,
		queryParam: 'q',
		queryMode: 'local'
	});

	var defect_dashboard_statusForm = Ext.create('Ext.form.ComboBox', {
		id:'defect_dashboard_statusForm',name:'defect_dashboard_statusForm',
		editable: false,
		fieldLabel: Otm.def_status,
		displayField: 'pco_name',
		valueField:'pco_seq',
		store: defect_dashboard_status_store,
		minChars: 0,
		allowBlank : true,
		queryParam: 'q',
		queryMode: 'local'
	});

	var defect_dashboard_contactForm = {
		id:'defect_dashboard_assign_member',name:'defect_dashboard_assign_member',
		xtype:'combo',
		editable: false,
		fieldLabel: Otm.com_user,
		displayField: 'mb_name',
		valueField:'mb_email',
		action_type : 'defect_dashboard_assign',
		store: defect_dashboard_contact_store,
		minChars: 0,
		allowBlank : true,
		queryParam: 'q',
		queryMode: 'local'
	};

	var defect_dashboard_dateForm = {
		layout: 'hbox',
		xtype: 'fieldcontainer',
		fieldLabel: Otm.com_start_date,
		combineErrors: false,
		items: [{
			xtype: 'datefield',
			id:'defect_dashboard_start_date',name:'defect_dashboard_start_date',
			width: 90,
			format:"Y-m-d",editable: false,
			endDateField: 'defect_dashboard_end_date',vtype: 'daterange',
			allowBlank: true,value:'<?=date("Y-m-d")?>'
		},{
			xtype: 'displayfield',
			style:'padding-left:30px;',
			value: Otm.com_end_date
		},{
			xtype: 'datefield',
			id:'defect_dashboard_end_date',name:'defect_dashboard_end_date',
			bodyStyle:'padding-left:30px;',
			startDateField: 'defect_dashboard_start_date',vtype: 'daterange',
			format:"Y-m-d",editable: false,
			width: 90,
			allowBlank: true
		}]
	};

	var defect_dashboard_fileForm = {
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
					Ext.getCmp("defect_dashboard_addFileFormPanel").add({
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
			id:'defect_dashboard_addFileFormPanel'
		}]
	};

	var defectCustomForm = {
		xtype:'panel',
		id:'defect_dashboard_customForm',
		border:false,
		width:'100%'
	};

	var defect_dashboard_writeForm = Ext.create("Ext.form.Panel",{
		region		: 'center',
		id			: 'defect_dashboard_writeForm',
		collapsible	: false,
		border		: false,
		bodyStyle	: 'padding: 10px;',
		autoScroll	: true,
		labelWidth	: '10',
		items: [defect_dashboard_seqForm],
		buttons:[{
			text:Otm.com_save,
			disabled: true,
			formBind: true,
			iconCls:'ico-save',
			handler:function(btn){
				defect_dashboard_save(obj);
			}
		}]
	});

	/*
		Add Component
	*/
	defect_dashboard_writeForm.add(defect_dashboard_subjectForm);
	defect_dashboard_writeForm.add(defect_dashboard_descriptionForm);
	defect_dashboard_writeForm.add(defect_dashboard_severityForm);
	defect_dashboard_writeForm.add(defect_dashboard_priorityForm);
	defect_dashboard_writeForm.add(defect_dashboard_frequencyForm);
	defect_dashboard_writeForm.add(defect_dashboard_statusForm);
	defect_dashboard_writeForm.add(defect_dashboard_contactForm);
	defect_dashboard_writeForm.add(defect_dashboard_dateForm);
	defect_dashboard_writeForm.add(defect_dashboard_fileForm);

	for(var ii=0;ii<defect_dashboard_customform_store.data.items.length;ii++){
		formInfo = defect_dashboard_customform_store.data.items[ii].data;
		defect_dashboard_writeForm.add(_getCustomfield(formInfo,'defect_dashboard'));
	}
	/*
		End : Add Component
	*/

	Ext.getCmp('defect_dashboard_EastPanel').add(defect_dashboard_writeForm);
	Ext.getCmp('defect_dashboard_editBtn').setDisabled(true);

	if(Ext.getCmp('defect_dashboard_defectGrid').getSelectionModel().selected.length >= 1){
		defect_dashboard_writeForm.reset();

		Ext.Ajax.request({
			url : "./index.php/Plugin_view/defect/view_defect",
			params :{
				df_seq : Ext.getCmp('defect_dashboard_defectGrid').getSelectionModel().selected.items[0].data.df_seq
			},
			method: 'POST',
			success: function ( result, request ) {
				var selItem = Ext.getCmp('defect_dashboard_defectGrid').getSelectionModel().selected.items[0];

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

					Ext.getCmp('defect_dashboard_EastPanel').add(view_form);

					selItem.data.defect_dashboard_seqForm = defect_info.data.df_seq;
					selItem.data.defect_dashboard_subjectForm = defect_info.data.df_subject;
					selItem.data.defect_dashboard_descriptionForm = defect_info.data.df_description;
					selItem.data.defect_dashboard_start_date = defect_info.data.dc_start_date;
					selItem.data.defect_dashboard_end_date = defect_info.data.dc_end_date;

					selItem.data.defect_dashboard_assign_member = defect_info.data.dc_to_seq;

					var df_customform = defect_info.data.df_customform;
					_setCustomform_userdata(customform_seq,df_customform,'defect_dashboard');

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
						temp_status_store = defect_dashboard_status_store;
					}else{
						temp_status_store = defect_dashboard_status_store;
					}

					Ext.getCmp("defect_dashboard_statusForm").bindStore(temp_status_store);

					if(defect_info.data.status_name){
						Ext.getCmp("defect_dashboard_statusForm").setValue(defect_info.data.status_seq);
					}

					if(defect_info.data.severity_name){
						Ext.getCmp("defect_dashboard_severityForm").setValue(defect_info.data.df_severity);
					}
					if(defect_info.data.priority_name){
						Ext.getCmp("defect_dashboard_priorityForm").setValue(defect_info.data.df_priority);
					}
					if(defect_info.data.frequency_name){
						Ext.getCmp("defect_dashboard_frequencyForm").setValue(defect_info.data.df_frequency);
					}

					if(defect_info.data.dc_to_seq >0){
						selItem.data.d_assign_member = defect_info.data.dc_to_seq;
					}
					defect_dashboard_writeForm.loadRecord(selItem);
				}
			},
			failure: function ( result, request ) {
				Ext.Msg.alert("OTM","DataBase Select Error");
			}
		});
	}
}

function defect_dashboard_save(obj)
{
	var URL = "";
	if(Ext.getCmp("defect_dashboard_seqForm").getValue() >= 1){
		URL = "./index.php/Plugin_view/defect/update_defect_dashboard";
	}else{
		Ext.Msg.alert('OTM','Empty defect_dashboard_seqForm');
	}

	var df_seq = Ext.getCmp("defect_dashboard_seqForm").getValue();

	var user_customform_result = new Array();
	var commit_info = Ext.getCmp("defect_dashboard_writeForm").getForm().getValues();
	for(var i=0;i<customform_seq.length;i++){
		if(Ext.getCmp('defect_dashboard'+'custom_'+customform_seq[i].seq)){
			user_customform_result.push({
				name	: customform_seq[i].name,
				seq		: customform_seq[i].seq,
				type	: customform_seq[i].type,
				value	: eval("commit_info.defect_dashboardcustom_"+customform_seq[i].seq)
			});
		}
	}

	var select = Ext.getCmp("defect_dashboard_defectGrid").getSelectionModel().selected;

	if(Ext.getCmp("defect_dashboard_writeForm").getForm().isValid()){
		var params = {
				writer : (select.items[0] && select.items[0].data.writer)?select.items[0].data.writer:'',
				project_seq	: obj.pr_seq,
				custom_form : Ext.encode(user_customform_result)
			};

		Ext.getCmp("defect_dashboard_writeForm").getForm().submit({
			url: URL,
			method:'POST',
			params: params,
			waitMsg: Otm.com_loading,
			success: function(rsp, o){
				var info = Ext.decode(o.response.responseText);
				if(info.data && info.data.msg && info.data.msg == 'over_num'){
					Ext.Msg.alert('OTM',Otm.id_rule.over_id_number_msg);
					return;
				}

				defect_dashboard_defectGrid_store.reload({
					callback:function(){
						if(!df_seq){
							Ext.getCmp("defect_dashboard_defectGrid").getSelectionModel().select(0);
						}else{
							for(var i=0;i<defect_dashboard_defectGrid_store.data.length;i++){
								if(defect_dashboard_defectGrid_store.data.items[i].data.df_seq == df_seq){
									Ext.getCmp("defect_dashboard_defectGrid").getSelectionModel().select(i);
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
/*
	End : Defect Info Data Update
*/


/*
	Defect View
*/
function draw_defect_dashboard_view(obj)
{
	/*
		Get Defect Info Data
	*/
	var target_panel = obj.target;

	Ext.getCmp(target_panel).update('');

	Ext.Ajax.request({
		url : "./index.php/Plugin_view/defect/view_defect",
		params : obj,
		method: 'POST',
		success: function ( result, request ) {
			if(result.responseText){
				var defect_info = _getCustomform_view(defect_dashboard_customform_store,result.responseText);

				if(!defect_info.data.dc_to){
					defect_info.data.dc_to = "";
				}
				if(defect_info.data.dc_start_date){
					defect_info.data.dc_start_date = defect_info.data.dc_start_date.substr(0,10);
				}else{
					defect_info.data.dc_start_date="";
				}
				if(defect_info.data.dc_end_date){
					defect_info.data.dc_end_date = defect_info.data.dc_end_date.substr(0,10);
				}else{
					defect_info.data.dc_end_date ="";
				}

				if(defect_info.data.dc_start_date == '0000-00-00')
					defect_info.data.dc_start_date = '';
				if(defect_info.data.dc_end_date == '0000-00-00')
					defect_info.data.dc_end_date = '';

				defect_info.data.regdate = defect_info.data.regdate.substr(0,10);

				var printFile = _common_fileView('defectGrid',Ext.decode(defect_info.data.fileform));
				defect_info.data.fileform = printFile;

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

				var content_fieldset = {
						xtype		: 'fieldset',
						collapsible	: false,
						collapsed	: false,
						border		: false,
						items		: [{
							xtype		: 'displayfield',
							fieldLabel	: Otm.com_subject,
							value		: defect_info.data.df_subject
						},{
							xtype		: 'displayfield', multiline	: true,
							fieldLabel	: Otm.com_description,
							value		: defect_info.data.df_description
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.def_severity,
							value		: defect_info.data.severity_name
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.def_priority,
							value		: defect_info.data.priority_name
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.def_frequency,
							value		: defect_info.data.frequency_name
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.def_status,
							value		: defect_info.data.status_name
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.com_user,
							value		: defect_info.data.dc_to
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.com_start_date,
							value		: defect_info.data.dc_start_date
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.com_end_date,
							value		: defect_info.data.dc_end_date
						}]
					};

				var userform_fieldset = {
					xtype		: 'fieldset',
					collapsible	: false,
					collapsed	: false,
					border		: false,
					items		: [{
						border		: false,
						html		: defect_info.data.user_form
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
						html		: defect_info.data.fileform
					},{xtype : 'menuseparator',width : '100%'}]
				};

				var racking_result_grid = {};

				if(defect_info.data.tc_result.length > 0){
					var tracking_result_store = Ext.create('Ext.data.Store', {
						fields	: ['pco_name', 'tr_description','writer_name','regdate','df_id'],
						data:{'items':[]},
						proxy: {
							type: 'memory',
							reader: {
								type: 'json',
								rootProperty: 'items'
							}
						}
					});

					for(var i=0; i<defect_info.data.tc_result.length; i++)
					{
						var r = defect_info.data.tc_result;

						tracking_result_store.add({
							pco_seq						: r[i].pco_seq,
							pco_name					: r[i].pco_name,
							tr_description				: r[i].tr_description.replace(/\n/g, '<br>'),
							writer_name					: r[i].writer_name,
							regdate						: r[i].regdate,
							df_seq						: r[i].df_seq,
							tc_seq						: r[i].tc_seq,
							tc_out_id					: r[i].tc_out_id,
							tc_subject					: r[i].tc_subject,
							tr_seq						: r[i].tr_seq,
							otm_testcase_link_tl_seq	: r[i].otm_testcase_link_tl_seq
						});
					}

					var racking_result_grid = new Ext.grid.GridPanel({
						layout		: 'fit',
						store		: tracking_result_store,
						border		: true,
						forceFit	: true,
						autoWidth	: true,
						columns		: [{
								text: Otm.com_number,
								dataIndex: 'df_seq',align:'center',
								hidden:true,
								width:50
							},{
								text: Otm.tc_execution_result,
								dataIndex: 'pco_name',
								width: 50
							},{
								text: Otm.tc_execution+' '+Otm.com_description,
								dataIndex: 'tr_description',
								flex:1,
								width: 150
							},{
								text: Otm.tc_execution_user,
								dataIndex: 'writer_name',
								width: 50
							},{
								text: Otm.tc_execution_regdate,
								dataIndex: 'regdate',
								width: 80,
								renderer:function(value,index,record){
									if(value){
										var value = value.substr(0,10);
									}else{
										value = '';
									}
									return value;
								}
							},{
								text: 'TC ID',
								dataIndex: 'tc_out_id',align:'center',
								width: 80,
								renderer: function(value, metaData, record, rowIndex, colIndex, store){
									if(value){
										if(mb_is_admin == 'Y' || (member_role_store && member_role_store['tc_view'])){
											return value;
											//return '<a href=javascript:popup_view("testcase","'+record.data.otm_testcase_link_tl_seq+'");>'+value+'</a>';
										}else{
											return '<span style="color:red;">['+Otm.tc+' '+Otm.com_view+Otm.com_msg_noRole+']</span>';
										}
									}else{
										return '';
									}
								}
							}
						]
					});
				}

				var racking_result_fieldset = {
					xtype		: 'fieldset',
					title		: Otm.tc+' '+Otm.com_linklist,
					collapsible	: false,
					collapsed	: false,
					style		: 'padding:5px',
					bodyStyle	: 'padding:5px',
					items		: [racking_result_grid]
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
						racking_result_fieldset,
						attached_file_fieldset
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

function get_defect_dashboard_view_panel(obj)
{
	/*
		Get Project Custom Data
	*/
	if(typeof(defect_dashboard_customform_store) !== 'undefined'){
		if(defect_dashboard_customform_store.pr_seq && obj.pr_seq === defect_dashboard_customform_store.pr_seq){
			draw_defect_dashboard_view(obj);
		}else{
			var temp_defect_customform_store = Ext.create('Ext.data.Store', {
				fields:['pc_seq','pc_name','pc_is_required','pc_formtype','pc_default_value','pc_content','pc_is_use'],
				proxy: {
					type	: 'ajax',
					url		: './index.php/Project_setup/userform_list',
					extraParams: {
						pr_seq		: obj.pr_seq,
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
				autoLoad:true
			});

			temp_defect_customform_store.on('load',function(storeObj,option){
				storeObj.pr_seq = obj.pr_seq;
				defect_dashboard_customform_store.pr_seq = obj.pr_seq;
				defect_dashboard_customform_store.data = storeObj.data;

				draw_defect_dashboard_view(obj);
			});
			return;
		}
	}else{
		var temp_defect_customform_store = Ext.create('Ext.data.Store', {
			fields:['pc_seq','pc_name','pc_is_required','pc_formtype','pc_default_value','pc_content','pc_is_use'],
			proxy: {
				type	: 'ajax',
				url		: './index.php/Project_setup/userform_list',
				extraParams: {
					pr_seq		: obj.pr_seq,
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
			autoLoad:true
		});

		temp_defect_customform_store.on('load',function(storeObj,option){
			storeObj.pr_seq = obj.pr_seq;
			defect_dashboard_customform_store = storeObj;
			defect_dashboard_customform_store.pr_seq = obj.pr_seq;
			defect_dashboard_customform_store.data = storeObj.data;

			draw_defect_dashboard_view(obj);
		});
		return;
	}
}
/*
	End : Defect View
*/


var defect_dashboard_defectGrid_store = Ext.create('Ext.data.Store', {
	fields	: ['df_seq','otm_project_pr_seq','pr_name','df_id','df_subject','df_status','df_severity','df_priority','df_frequency','df_id','df_assign_member','writer_name','otm_testcase_result_tr_seq','tracking_id','regdate','df_cnt'],
	pageSize: 50,
	proxy	: {
		type		: 'ajax',
		url			: './index.php/Plugin_view/defect/defect_dashboard_list',
		extraParams	: {
			search_array : ''
		},
		actionMethods: {
			create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
		},
		reader		: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
	},
	autoLoad	: true
});

defect_dashboard_defectGrid_store.on('beforeload',function(storeObj,option){
	var search_array = new Array();
	var defect_list_option = Ext.getCmp("defect_list_option").getValue();

	if(Ext.getCmp('sfl_defect_dashboard').getValue() == 'regdate'){
		search_array.push({
			sfl						: Ext.getCmp('sfl_defect_dashboard').getValue(),
			search_start_date		: Ext.getCmp('search_start_date_defect_dashboard').getValue(),
			search_end_date			: Ext.getCmp('search_end_date_defect_dashboard').getValue()
		});
	}else{
		search_array.push({
			sfl						: Ext.getCmp('sfl_defect_dashboard').getValue(),
			stx						: Ext.getCmp('stx_defect_dashboard').getValue()
		});
	}

	if(search_array.length > 0){
		 option.setParams({'search_array':Ext.encode(search_array),'defect_list_option':defect_list_option});
	}
});

var defect_dashboard_defectGrid_column = [{
		text: Otm.com_number,
		dataIndex: 'df_seq',align:'center',
		hidden:true,
		width:50
	},{
		xtype: 'rownumberer',width: 30,sortable: false
	},{
		text: Otm.pjt_name,
		dataIndex: 'pr_name',
		width: 80,
		menuDisabled: false
	},{
		text: 'ID',
		dataIndex: 'df_id',
		width: 80,
		menuDisabled: false
	},{
		text: Otm.com_subject,
		dataIndex: 'df_subject',
		flex:1,
		minWidth:300,
		width: 500,
		menuDisabled: false
	},{
		text: Otm.com_user,
		dataIndex: 'df_assign_member',align:'center',
		width: 50,
		menuDisabled: false
	},{
		text: Otm.com_creator,
		dataIndex: 'writer_name',align:'center',
		width: 50,
		menuDisabled: false
	},{
		text: Otm.def_status,
		dataIndex: 'df_status_name',
		align:'center',
		width: 80,
		menuDisabled: false,
		filter: {
			type: 'list'
		}
	},{
		text: Otm.def_severity,
		dataIndex: 'df_severity_name',align:'center',
		width: 80,
		menuDisabled: false,
		filter: {
			type: 'list'
		}
	},{
		text: Otm.def_priority,
		dataIndex: 'df_priority_name',align:'center',
		width: 80,
		menuDisabled: false,
		filter: {
			type: 'list'
		}
	},{
		text: Otm.def_frequency,
		dataIndex: 'df_frequency_name',align:'center',
		width:80,
		menuDisabled: false,
		filter: {
			type: 'list'
		}
	},{
		text: Otm.com_date, dataIndex: 'regdate', align:'center',
		width:80,
		menuDisabled: false,
		sortable: true, renderer:function(value,index,record){
			if(value){
				var value = value.substr(0,10);
			}else{
				value = '';
			}
			return value;
		}
	}];

var defect_dashboard_defectGrid = Ext.create("Ext.grid.Panel",{
	region	: 'center',
	id		: 'defect_dashboard_defectGrid',
	store	: defect_dashboard_defectGrid_store,
	border	: false,
	forceFit: true,
	plugins	: 'gridfilters',
	columns	: defect_dashboard_defectGrid_column,
	tbar	: [{
		xtype		: 'radiogroup',
		id : 'defect_list_option',
		items: [
			{boxLabel: Otm.def_allview,		width:150,	name: 'defect_list_option', inputValue: 'all', checked: true},
			{boxLabel: Otm.def_writed_defect,	width:100,	name: 'defect_list_option', inputValue: 'writer'},
			{boxLabel: Otm.def_assigned_defect,width:100,	name: 'defect_list_option', inputValue: 'assign'}
		],
		listeners:{
			change: function(radiogroup, value) {
				Ext.getCmp('defect_dashboard_defectGrid').getStore().loadPage(1,{
					params :{
						defect_list_option:value.defect_list_option
					}
				});
			}
		}
	}],
	bbar	: Ext.create('Ext.PagingToolbar', {
		id		:	'defect_dashboard_defectGrid_page',
		store	: defect_dashboard_defectGrid_store,
		displayInfo: true
	}),
	listeners:{
		scope:this,
		select: function(smObj, record, rowIndex) {
			Ext.getCmp('defect_dashboard_EastPanel').removeAll();

			var defect_dashboard_EastPanel = Ext.getCmp('defect_dashboard_EastPanel');
			defect_dashboard_EastPanel.setTitle(Otm.com_view);
			if(defect_dashboard_EastPanel.collapsed==false){
			}else{
				defect_dashboard_EastPanel.expand();
			}

			var obj ={
				target : 'defect_dashboard_EastPanel',
				df_seq : record.data.df_seq,
				pr_seq : record.data.otm_project_pr_seq
			};
			get_defect_dashboard_view_panel(obj);
			Ext.getCmp('defect_dashboard_editBtn').setDisabled(false);
			return;
		}
	}
});

var defect_dashboard_panel = {
	layout	: 'border',
	tbar	: [{
			xtype		: 'combo',
			width		: '100px',
			name		: 'sfl_defect_dashboard',
			id			: 'sfl_defect_dashboard',
			editable	: false,
			displayField: 'Name',
			valueField	: 'Key',
			store		:	new Ext.data.SimpleStore({
				 fields:['Key', 'Name']
				,data:[['regdate', Otm.com_date],['status', Otm.com_status]]
			}),
			minChars	: 0,
			queryParam	: 'q',
			queryMode	: 'local',
			value		: 'regdate',
			listeners	: {
				select	: function( combo, records, eOpts ) {
					if(records){
						if(combo.lastValue == "regdate"){
							Ext.getCmp("stx_defect_dashboard").setVisible(false);
							Ext.getCmp("search_start_date_defect_dashboard").setVisible(true);
							Ext.getCmp("search_end_date_defect_dashboard").setVisible(true);
						}else if(combo.lastValue == "status"){
							Ext.getCmp("stx_defect_dashboard").setVisible(true);
							Ext.getCmp("search_start_date_defect_dashboard").setVisible(false);
							Ext.getCmp("search_end_date_defect_dashboard").setVisible(false);
						}
					}
				}
			}
		},{
			xtype		: 'textfield',
			style		:'margin-left:3px;',
			name		: 'stx_defect_dashboard',
			id			: 'stx_defect_dashboard',
			width		: 243,
			hidden		: true,
			allowBlank	: false,
			enableKeyEvents: true
		},{
			xtype		: 'datefield',
			style		: 'margin-left:3px;',
			name		: 'search_start_date_defect_dashboard',
			id			: 'search_start_date_defect_dashboard',
			endDateField: 'search_end_date_defect_dashboard',
			emptyText	: 'From',
			width		: 120,
			format		: "Y-m-d",
			editable	: false,
			vtype		: 'daterange',
			allowBlank	: true
		},{
			xtype		: 'datefield',
			style		: 'margin-left:3px;',
			name		: 'search_end_date_defect_dashboard',
			id			: 'search_end_date_defect_dashboard',
			startDateField: 'search_start_date_defect_dashboard',
			emptyText	: 'To',
			width		: 120,
			format		: "Y-m-d",
			editable	: false,
			vtype		: 'daterange',
			allowBlank	: true
		},{
			xtype		: 'button',
			style		: 'margin:0 0 0 5px;',
			text		: Otm.com_search,
			iconCls		: 'ico-search',
			handler		: function(btn){
				var search_array = new Array();
				if(Ext.getCmp('sfl_defect_dashboard').getValue() == 'regdate'){
					search_array.push({
						sfl						: Ext.getCmp('sfl_defect_dashboard').getValue(),
						search_start_date		: Ext.getCmp('search_start_date_defect_dashboard').getValue(),
						search_end_date			: Ext.getCmp('search_end_date_defect_dashboard').getValue()
					});
				}else{
					search_array.push({
						sfl						: Ext.getCmp('sfl_defect_dashboard').getValue(),
						stx						: Ext.getCmp('stx_defect_dashboard').getValue()
					});
				}

				Ext.getCmp('defect_dashboard_defectGrid').getStore().loadPage(1,{
					params :{
						search_array:Ext.encode(search_array)
					}
				});
			}
		},{
			xtype		: 'button',
			style		: 'margin:0 0 0 5px;',
			text		: Otm.com_reset,
			iconCls		: 'ico-reset',
			handler		: function(btn){
				Ext.getCmp("stx_defect_dashboard").setValue("");
				Ext.getCmp("search_start_date_defect_dashboard").setValue("");
				Ext.getCmp("search_end_date_defect_dashboard").setValue("");
			}
		}],
	items : [defect_dashboard_defectGrid,{
		region		: 'east',
		layout		: 'border',
		xtype		: 'panel',
		id			: 'defect_dashboard_EastPanel',
		flex		: 1,
		minSize		: 100,
		maxSize		: 600,
		split		: true,
		collapsible	: true,
		collapsed	: true,
		animation	: false,
		autoScroll	: true,
		tbar		: [{
			xtype	: 'button',
			id		: 'defect_dashboard_editBtn',
			text	: Otm.com_update,
			iconCls	: 'ico-update',
			handler	: function(btn){
				if(Ext.getCmp("defect_dashboard_defectGrid").getSelectionModel().selected.length > 1){
					Ext.Msg.alert('OTM',Otm.com_msg_only_one);
					return;
				}

				if(Ext.getCmp("defect_dashboard_defectGrid").getSelectionModel().selected.length == 1){

					var selected = Ext.getCmp('defect_dashboard_defectGrid').getSelectionModel().selected.items[0].data;
					if(selected.writer != mb_email && selected.dc_to != mb_email ){
						Ext.Msg.alert('OTM',Otm.def+' '+Otm.com_update+Otm.com_msg_noRole);
						return;
					}

					var defect_dashboard_EastPanel = Ext.getCmp('defect_dashboard_EastPanel');
					defect_dashboard_EastPanel.setTitle(Otm.com_update);

					/*
						Set update Form
					*/
					var obj = {pr_seq : selected.otm_project_pr_seq};
					defect_dashboard_form_reset(obj);

				}else{
					Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
					return;
				}
			}
		}],
		items:[]
	}]
};

Ext.onReady(function(){
	Ext.getCmp('defect_dashboard').add(defect_dashboard_panel);
	Ext.getCmp('defect_dashboard').doLayout(true,false);
});
</script>