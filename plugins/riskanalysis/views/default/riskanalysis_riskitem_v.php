<?php
/**
 * @copyright Copyright STA
 * Created on 2016. 04.
 * @author STA <otm@sta.co.kr>
 */

include_once($data['skin_dir'].'/'.$data['module_directory'].'_common.php');
?>
<script type="text/javascript">
var project_seq = '<?=$project_seq?>';

function viewImportWin_riskitem()
{
	var win = Ext.getCmp('riskitem_import_window');

	if(win){
		Ext.getCmp('form_file').clearOnSubmit = true;
		Ext.getCmp("riskitem_uploadPanel").reset();
		Ext.getCmp('form_file').clearOnSubmit = false;
		Ext.getCmp("riskitem_uploadPanel").update();

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

	var riskitem_uploadPanel = new Ext.FormPanel({
		fileUpload: true,
		id:'riskitem_uploadPanel',
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
				//var chkInfo = Ext.getCmp('import_check_id').items;
				//if(chkInfo.items[1].checked){
					params.import_check_id = true;
				//}

				riskitem_uploadPanel_submit(params);
			}
		}]
	});

	var import_window = Ext.create('Ext.window.Window', {
		title: Otm.com_import+'(.xls)',
		id	: 'riskitem_import_window',
		height: 230,
		width: 650,
		layout: 'fit',
		resizable : true,
		modal : true,
		constrainHeader: true,
		closeAction: 'hide',
		items: [riskitem_uploadPanel]
	});

	import_window.show();
}

function select_excel_sheet_riskitem(sheets,params)
{
	if(Ext.getCmp('select_excel_sheet_riskitem_window')){
		Ext.getCmp('select_excel_sheet_riskitem_window').removeAll();
	}else{
		Ext.create('Ext.window.Window', {
			title: 'Select Sheet',
			id	:'select_excel_sheet_riskitem_window',
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

	var select_excel_sheet_riskitem = new Ext.FormPanel({
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
					Ext.getCmp('select_excel_sheet_riskitem_window').hide();
					riskitem_uploadPanel_submit(params);
				}else{
					Ext.Msg.alert('OTM','Please, Select Sheet.');
				}
			}
		}]
	});

	Ext.getCmp('select_excel_sheet_riskitem_window').add(select_excel_sheet_riskitem);
	Ext.getCmp('select_excel_sheet_riskitem_window').show();
}

function riskitem_uploadPanel_submit(params)
{
	if(Ext.getCmp("riskitem_uploadPanel").getForm().isValid()){
		top.myUpdateProgress(1,'Data Loadding...');

		var URL = "./index.php/Import/plugin/riskanalysis/import_riskitem";

		Ext.getCmp("riskitem_uploadPanel").mask(Otm.com_msg_processing_data);

		Ext.getCmp("riskitem_uploadPanel").getForm().submit({
			url: URL,
			method:'POST',
			params: params,
			success: function(form, action){
				top.myUpdateProgress(100,'End');

				var obj = Ext.decode(action.response.responseText);
				if(obj.sheet_count && obj.sheet_count>1)
				{
					Ext.getCmp("riskitem_uploadPanel").unmask();

					select_excel_sheet_riskitem(obj.sheet_names,params);
					return;
				}

				Ext.getCmp("riskanalysis_riskitemGrid").getStore().reload();

				Ext.getCmp("riskitem_uploadPanel").unmask();
				Ext.getCmp("riskitem_import_window").hide();
			},
			failure: function(form, action){
				var obj = Ext.decode(action.response.responseText);

				var msg = Ext.decode(obj.msg);
				if(msg.over){
					Ext.Msg.alert('OTM',msg.over);

					top.myUpdateProgress(100,'End');
					Ext.getCmp("riskitem_uploadPanel").unmask();
					return;
				}else if(msg.duplicate_id){
					Ext.Msg.confirm('OTM',Otm.com_msg_duplicate_id,function(bt){
						if(bt=='yes'){

							var params = {
								project_seq	: project_seq,
								import_check_id : true,
								update : true
							};

							riskitem_uploadPanel_submit(params);

						}else{
							Ext.getCmp("riskitem_import_window").hide();
							return;
						}
					})

				}else{
					Ext.Msg.alert('OTM',msg.msg);
				}

				top.myUpdateProgress(100,'End');
				Ext.getCmp("riskitem_uploadPanel").unmask();
			}
		});
	}
}

var riskitem_customform_store = Ext.create('Ext.data.Store', {
	fields:['pc_seq','pc_name','pc_is_required','pc_formtype','pc_default_value','pc_content','pc_is_use'],
	proxy: {
		type	: 'ajax',
		url		: './index.php/Project_setup/userform_list',
		extraParams: {
			pr_seq		: project_seq,
			pc_category : 'ID_RISK',
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

function form_reset()
{
	Ext.getCmp('riskanalysis_riskitem_form').removeAll();
	riskanalysis_riskitem_requirement_unlink_store.removeAll();
	riskanalysis_riskitem_requirement_link_store.removeAll();

	var riskitem_seqForm = {
		id: 'riskitem_seqForm',
		name: 'riskitem_seqForm',
		anchor: '100%',
		allowBlank : true,
		xtype: 'hiddenfield'
	};
	var riskitem_subjectForm = {
		id: 'riskitem_subjectForm',
		name:'riskitem_subjectForm',
		anchor: '95%',
		minLength:2,
		maxLength:100,
		fieldLabel: Otm.com_subject+'(*)',
		allowBlank : false,
		xtype: 'textfield'
	};
	var riskitem_descriptionForm = {
		id: 'riskitem_descriptionForm',
		name:'riskitem_descriptionForm',
		anchor: '95%',
		fieldLabel: Otm.com_description+'(*)',
		allowBlank : false,
		grow : true,
		growMax: 400,
		growMin: 100,
		xtype: 'textarea'
	};

	var riskitem_fileForm = {
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
	var riskitem_CustomForm = {
		xtype:'panel',
		id:'riskitem_customForm',
		border:false,
		//width:'95%'
		anchor: '95%'
	};
	var riskitem_writeForm = Ext.create("Ext.form.Panel",{
		id			: 'riskitem_writeForm',
		collapsible	: false,
		border		: false,
		bodyStyle	: 'padding: 10px;',
		labelWidth	: '10',
		items: [riskitem_seqForm,riskitem_subjectForm,riskitem_descriptionForm,riskitem_CustomForm,riskitem_fileForm]
	});

	var temp_riskitem_writeForm = Ext.create("Ext.form.Panel",{
		border		: false,
		autoScroll	: true,
		items: [riskitem_writeForm],
		buttons:[{
		//tbar:[{
			text:Otm.com_save,
			disabled: true,
			formBind: true,
			iconCls:'ico-save',
			handler:function(btn){
				riskitem_save('save');
			}
		}]
	});



	riskitem_customform_store.load({
		callback: function(r,options,success){
			tmp_customform = _setCustomform('ID_RISK',r);

			Ext.getCmp("riskitem_customForm").add(tmp_customform);
			Ext.getCmp('riskanalysis_riskitem_form').add(temp_riskitem_writeForm);

			if(Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.length >= 1){
				riskitem_writeForm.reset();
				
				Ext.Ajax.request({
					url : "./index.php/Plugin_view/riskanalysis/view_riskitem",
					params :{
						ri_seq : Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.items[0].data.ri_seq
					},
					method: 'POST',
					success: function ( result, request ) {
						var selItem = Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.items[0];

						
						if(result.responseText){
							var riskitem_info = Ext.decode(result.responseText);

							var default_fieldset = {
								xtype		: 'fieldset',
								collapsible	: false,
								collapsed	: false,
								border		: false,
								items		: [{
									xtype		: 'displayfield',
									fieldLabel	: 'seq',
									hidden		: true,
									value		: riskitem_info.data.ri_seq
								},{
									xtype		: 'displayfield',
									fieldLabel	: Otm.com_creator,
									value		: riskitem_info.data.writer
								},{
									xtype		: 'displayfield',
									fieldLabel	: Otm.com_date,
									value		: riskitem_info.data.regdate.substr(0,10)
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

							Ext.getCmp('riskanalysis_riskitem_form').add(view_form);

							selItem.data.riskitem_seqForm = riskitem_info.data.ri_seq;
							selItem.data.riskitem_subjectForm = riskitem_info.data.ri_subject;
							selItem.data.riskitem_descriptionForm = riskitem_info.data.ri_description;

							var df_customform = riskitem_info.data.df_customform;
							//_setCustomform_userdata(customform_seq,df_customform);

							riskitem_writeForm.loadRecord(selItem);
						}
						
					},
					failure: function ( result, request ) {
						Ext.Msg.alert("OTM","DataBase Select Error");
					}
				});
				

			}else{
				riskitem_writeForm.reset();
			}
		}
	});
}

	/**
	* Center Panel
	*/
	var riskanalysis_riskitem_store = Ext.create('Ext.data.Store', {
		fields:['ri_seq','ri_subject','link_req_cnt','link_tc_cnt'],
		//pageSize: 50,
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/riskanalysis/riskitem_list',
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
		autoLoad:true
	});

	var riskanalysis_riskitem_center_panel =  {
		region	: 'center',
		layout	: 'fit',
		xtype	: 'gridpanel',
		id		: 'riskanalysis_riskitemGrid',
		multiSelect: true,
		store	: riskanalysis_riskitem_store,
		columns	: [
			{header: Otm.riskanalysis.item,	dataIndex: 'ri_subject',	flex: 1,	minWidth:150},
			{header: '연결 요구사항',		dataIndex: 'link_req_cnt',	align:'center',	width:80},
			{header: '연결TC',				dataIndex: 'link_tc_cnt',	align:'center',	width:80}
		],
		listeners:{
			scope:this,
			select: function(smObj, record, rowIndex){
				//console.log('select');
				var riskanalysis_riskitem_east_panel = Ext.getCmp('riskanalysis_riskitem_east_panel');

				riskanalysis_riskitem_east_panel.setTitle(Otm.com_view);
				//if(riskanalysis_riskitem_east_panel.collapsed==false){
				//}else{
				//	riskanalysis_riskitem_east_panel.expand();
				//}

				var obj ={
					target : 'riskanalysis_riskitem_form',
					ri_seq : record.data.ri_seq,
					pr_seq : record.data.otm_project_pr_seq
				};

				riskitem_view_panel(obj);
				
				return;
			},
			deselect: function(smObj, record, rowIndex){
				//console.log('deselect');
				//Ext.getCmp('riskanalysis_riskitem_east_panel').collapse();
				//var obj ={
				//	target : 'riskanalysis_riskitem_form'
				//};

				//riskitem_view_panel(obj);
			}
		},
		tbar	: [{
				xtype	: 'button',
				text	: Otm.com_add,
				iconCls	:'ico-add',
				handler	: function (btn){
					var riskanalysis_riskitem_east_panel = Ext.getCmp('riskanalysis_riskitem_east_panel');
					riskanalysis_riskitem_east_panel.setTitle(Otm.com_add);

					if(riskanalysis_riskitem_east_panel.collapsed==false){
					}else{
						riskanalysis_riskitem_east_panel.expand();
					}

					Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().deselectAll();
					form_reset();
				}
			},'-',{
				xtype	: 'button',
				text	: Otm.com_update,
				iconCls	:'ico-update',
				handler	: function (btn){
					
					if(Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.length > 1){
						Ext.Msg.alert('OTM',Otm.com_msg_only_one);
						return;
					}

					if(Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.length == 1){
						var riskanalysis_riskitem_east_panel = Ext.getCmp('riskanalysis_riskitem_east_panel');
						riskanalysis_riskitem_east_panel.setTitle(Otm.com_update);
						if(riskanalysis_riskitem_east_panel.collapsed==false){
						}else{
							riskanalysis_riskitem_east_panel.expand();
						}
						form_reset();
					}else{
						Ext.Msg.alert('OTM',Otm.def +' ' + Otm.com_msg_NotSelectData);
						return;
					}
					
				}
			},'-',{
				xtype	: 'button',
				text	: Otm.com_remove,
				iconCls	:'ico-remove',
				handler	: function (btn){
					if(Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.length >= 1){

					var ri_list = Array();
					var Records = Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.items;

					for(var i=0; i<Records.length; i++){
						ri_list.push(Records[i].data['ri_seq']);
					}

					Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
						if(bt=='yes'){
							var params = {
								pr_seq	: project_seq,
								ri_list		: Ext.encode(ri_list),
								writer		: Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.items[0].data.writer
							};

							Ext.Ajax.request({
								url		: './index.php/Plugin_view/riskanalysis/delete_riskitem',
								params	: params,
								method	: 'POST',
								success	: function ( result, request ) {
									if(result.responseText=="1"){
										riskanalysis_riskitem_store.reload();

										//var riskanalysis_riskitem_east_panel = Ext.getCmp('riskanalysis_riskitem_east_panel');
										//riskanalysis_riskitem_east_panel.collapse();
										//form_reset();
										//riskanalysis_riskitem_east_panel.removeAll();
										Ext.getCmp('riskanalysis_riskitem_form').removeAll();
										riskanalysis_riskitem_requirement_unlink_store.removeAll();
										riskanalysis_riskitem_requirement_link_store.removeAll();

										//Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().select(0);
									}else{
										Ext.Msg.alert("OTM",result.responseText);
									}
								},
								failure: function ( result, request ) {
									//alert("fail");
									Ext.Msg.alert("OTM",'Fail');
								}
							});
						}else{
							return;
						}
					});
				}else{
					Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
				}
				}
			},
				
			{
				text	: Otm.com_up,
				iconCls	: 'ico-up',
				hidden	: true,
				disabled: true,
				handler	: function(btn){

				}
			},{
				text	: Otm.com_down,
				iconCls	: 'ico-down',
				hidden	: true,
				disabled: true,
				handler	: function(btn){
				}
			},
				
			'-',{
				xtype	: 'button',
				text	: Otm.com_export,
				iconCls	: 'ico-export',
				//disabled: true,
				handler	: function (btn){
					//export_data('otm/riskitem_list_export');
					export_data('plugin/riskanalysis/export_riskitem','project_seq='+project_seq);
				}
			},'-',{
				xtype	: 'button',
				text	: Otm.com_import,
				iconCls	: 'ico-import',
				//disabled: true,
				handler	: function (btn){
					viewImportWin_riskitem();
				}
			}]
	};


	/**
	* Eest Panel
	*/

	var riskanalysis_riskitem_form =  {
		region	: 'center',
		xtype	: 'panel',
		layout  : 'fit',
		//title	: '리스크 아이템',
		id		: 'riskanalysis_riskitem_form',
		flex	: 1,
		//bodyStyle: 'padding: 10px;',
		//border	: true,
		//autoScroll: true,
		items	: []
	};


	/*
	*	Requirement mapping tab panel 
	*	- unlink grid panel
	*	- linked grid panel
	*/
	//요구사항 연결 목록
	var riskanalysis_riskitem_requirement_unlink_store = Ext.create('Ext.data.Store', {
		fields:['req_seq', 'req_subject'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/riskanalysis/riskanalysis_requirement',
			extraParams: {
				pr_seq	: project_seq,
				ri_seq	: '',
				type	: 'unlink'
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

	var riskanalysis_riskitem_requirement_link_store = Ext.create('Ext.data.Store', {
		fields:['req_seq', 'req_subject', 'rrl_seq'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/riskanalysis/riskanalysis_requirement',
			extraParams: {
				pr_seq	: project_seq,
				ri_seq	: '',
				type	: 'link'
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

	var riskanalysis_riskitem_requirement_unlink_grid =  {
		region	: 'center',
		xtype	: 'gridpanel',
		id		: 'riskanalysis_riskitem_requirement_unlink_grid',
		title	: '연결안된 요구사항',
		store	: riskanalysis_riskitem_requirement_unlink_store,
		columns	: [
			{header: '요구사항명',		dataIndex: 'req_subject',		flex: 1}
			//,{header: 'ID',		dataIndex: 'oreq_id',	flex: 1, width:50, minWidth:100}
		],
		tbar	: [{
			xtype	: 'button',
			//iconCls	: 'arrow_right',
			iconCls	: 'ico-link',
			text	: '연결',
			tooltip	: '리스크아이템과 연결합니다.',
			handler	: function(){				
				var ri_seq = 0;

				var Records = Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.items;
				if(Records.length > 1){
					return;
				}else{
					//console.log(Records);
					ri_seq = Records[0].data['ri_seq'];
				}

				var params = {
					ri_seq	: ri_seq,
					type	: 'link'
				};
				var req_list = Array();
				
				var Records = Ext.getCmp('riskanalysis_riskitem_requirement_unlink_grid').getSelectionModel().selected.items;
				if(Records.length >= 1){
					for(var i=0; i<Records.length; i++){
						req_list.push(Records[i].data['req_seq']);
					}
					params.req_list = Ext.encode(req_list);
				}else{
					Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
					return;
				}

				Ext.Ajax.request({
					url : './index.php/Plugin_view/riskanalysis/riskitem_requirement_link',
					params : params,
					method: 'POST',
					success: function ( result, request ) {
						//if(result.responseText){
						//}
						//riskanalysis_riskitem_requirement_unlink_store.load({params:{'ri_seq':ri_seq}});
						//riskanalysis_riskitem_requirement_link_store.load({params:{'ri_seq':ri_seq}});
						riskanalysis_riskitem_store.reload({
							callback:function(){

								if(!ri_seq){
									//Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().select(0);
									Ext.getCmp('riskanalysis_riskitem_east_panel').collapse();
								}else{
									for(var i=0;i<riskanalysis_riskitem_store.data.length;i++){
										//console.log('callback',i, riskanalysis_riskitem_store.data.items[i].data.ri_seq , ri_seq);
										if(riskanalysis_riskitem_store.data.items[i].data.ri_seq == ri_seq){
											ri_seq = 0;
											Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().select(i);
										}
									}
								}
							}
							
						});
					},
					failure: function ( result, request ){						
					}
				});
			}
		}]
	};

	var riskanalysis_riskitem_requirement_link_grid =  {
		region	: 'east',
		layout	: 'fit',
		xtype	: 'gridpanel',
		id		: 'riskanalysis_riskitem_requirement_link_grid',
		title	: '연결된 요구사항',
		flex	: 1,
		store	: riskanalysis_riskitem_requirement_link_store,
		columns	: [
			{header: '요구사항명',		dataIndex: 'req_subject',		flex: 1}
			//,{header: 'ID',		dataIndex: 'oreq_id',	flex: 1, width:50, minWidth:100}
		],
		tbar	: [{
			xtype	: 'button',
			iconCls	: 'ico-unlink',
			text	: '연결 해제',
			tooltip	: '리스크아이템과 연결을 해제합니다.',
			handler	: function(){
				var ri_seq = 0;

				var Records = Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.items;
				if(Records.length > 1){
					return;
				}else{
					ri_seq = Records[0].data['ri_seq'];
				}

				var params = {
					ri_seq	: ri_seq,
					type	: 'unlink'
				};
				var req_list = Array();
				
				var Records = Ext.getCmp('riskanalysis_riskitem_requirement_link_grid').getSelectionModel().selected.items;
				if(Records.length >= 1){
					for(var i=0; i<Records.length; i++){
						req_list.push(Records[i].data['otm_requirement_req_seq']);
					}
					params.req_list = Ext.encode(req_list);
				}else{
					Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
					return;
				}

				Ext.Ajax.request({
					url : './index.php/Plugin_view/riskanalysis/riskitem_requirement_link',
					params : params,
					method: 'POST',
					success: function ( result, request ) {
						//if(result.responseText){
						//}
						//riskanalysis_riskitem_requirement_unlink_store.load({params:{'ri_seq':ri_seq}});
						//riskanalysis_riskitem_requirement_link_store.load({params:{'ri_seq':ri_seq}});
						riskanalysis_riskitem_store.reload({
							callback:function(){
								for(var i=0;i<riskanalysis_riskitem_store.data.length;i++){
									if(riskanalysis_riskitem_store.data.items[i].data.ri_seq == ri_seq){
										Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().select(i);
									}
								}
							}
						});
					},
					failure: function ( result, request ){						
					}
				});
			}
		}]
	};

	var riskanalysis_riskitem_requirement_tabpanel =  {				
		layout	: 'fit',
		title	: '요구사항',
		items	: [{					
			layout	: 'border',
			xtype	: 'panel',
			items: [
				riskanalysis_riskitem_requirement_unlink_grid,
				riskanalysis_riskitem_requirement_link_grid					
			] 				
		}]
	};
	/*
	*	Requirement mapping tab panel
	*	END
	*/


	/*
	*	Testcase mapping tab panel 
	*	- testcase backlog tree panel
	*/
	function riskanalysis_testcase_link(type)
	{
		var ri_seq = 0;

		var select_risk = Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.items;

		if(select_risk.length == 1){
			ri_seq = select_risk[0].data['ri_seq'];

			var params = {
				type			: type,
				pr_seq			: project_seq,
				ri_seq			: select_risk[0].data['ri_seq']
			};
		}else{
			return;
		}

		var URL = './index.php/Plugin_view/riskanalysis/riskitem_testcase_link';
		var select_tc = Ext.getCmp('riskanalysis_riskitem_tc_grid').getSelectionModel().selected.items[0];

		if(type == 'add_link'){				
			if(select_tc){
				params.pid = select_tc.data.id;
				params.tc_seq = (select_tc.data.tc_seq)?select_tc.data.tc_seq:0;
			}

			var action_type = 'add';

		}else if(type == 'link'){
			if(select_tc){
				if(select_tc.data.id == 'root'){
					Ext.Msg.alert("OTM",'Root에는 연결할 수 없습니다.');
					return;
				}else{
					params.pid = select_tc.data.id;
					params.tc_seq = select_tc.data.tc_seq;
				}
			}else{			
				Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
				return;
			}

			var action_type = 'update';
			
			if(select_tc.data.link_seq != ''){
				if(select_risk[0].data['ri_seq'] ==	select_tc.data.link_seq){
					Ext.Msg.alert('OTM','연결된 테스트 케이스 입니다.');
				}else{
					Ext.Msg.alert('OTM','다른 테스트 케이스와 연결되어있습니다.');
				}
				return;
			}
		}else if(type == 'unlink'){
			if(select_tc){
				if(select_tc.data.id == 'root'){
					return;
				}else{
					params.pid = select_tc.data.id;
					params.tc_seq = select_tc.data.tc_seq;
				}
			}else{			
				Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
				return;
			}

			var action_type = 'update';

			if(select_tc.data.link_seq != ''){
				if(select_risk[0].data['ri_seq'] ==	select_tc.data.link_seq){
				}else{
					Ext.Msg.alert('OTM','다른 테스트 케이스와 연결되어있습니다.');
					return;
				}
			}else{
				Ext.Msg.alert('OTM','연결된 테스트 케이스가 없습니다.');
				return;
			}
		}


		//console.log(URL, params);
		
		//riskanalysis_riskitem_tc_store.load();
		//Ext.getCmp('riskanalysis_riskitem_tc_grid').getStore().load({params:{node:'root'}});
		//Ext.getCmp('riskanalysis_riskitem_tc_grid').getRootNode().reload();

		Ext.Ajax.request({
			url : URL,
			params : params,
			method: 'POST',
			success: function ( result, request ) {
				//console.log(result, request);
				//return;
				//if(result.responseText){
				var obj =  Ext.decode(result.responseText);
				if(obj.data.msg && obj.data.msg == 'over_num'){
					Ext.Msg.alert('OTM',Otm.id_rule.over_id_number_msg);
					return;
				}else if(obj.data.msg){
					Ext.Msg.alert('OTM',obj.data.msg);
					return;
				}
				
				/*
				var node = Ext.decode(obj.data);
				node.cmd = action_type;
				node.target_grid = "riskanalysis_riskitem_tc_grid";
				
				//console.log(node);

				NodeReload(node);
				*/

				riskanalysis_riskitem_store.reload({
					callback:function(){
						for(var i=0;i<riskanalysis_riskitem_store.data.length;i++){
							if(riskanalysis_riskitem_store.data.items[i].data.ri_seq == ri_seq){
								Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().select(i);
							}
						}
					}
				});
				return;
			},
			failure: function ( result, request ){						
			}
		});
	
	}

	var riskanalysis_riskitem_tc_store = Ext.create('Ext.data.TreeStore', {
		/*root: {
			text: 'Root',
			//id: 'tc_0',
			//expanded: true,
			expandable: false,
			children: []
		},*/
		proxy: {
			type: 'ajax',
			//url:'./index.php/Plugin_view/testcase/testcase_tree_list',
			url	: './index.php/Plugin_view/riskanalysis/riskanalysis_testcase_list',
			extraParams: {
				pr_seq : project_seq,
				ri_seq : 0
			},
			reader: {
				type: 'json',
				totalProperty: 'totalCount',
				rootProperty: 'data'
			}
		},
		folderSort: true
	});

	riskanalysis_riskitem_tc_store.addListener('beforeload',function(thisStore){
		/*
		console.log('brforload');
		console.log(thisStore);
		console.log(thisStore.lastOptions);
		console.log(thisStore.lastOptions.params);
		thisStore.lastOptions.params.ri_seq = 5;
		thisStore.proxy.extraParams.ri_seq = 5;
		*/

		var tabPanel = Ext.getCmp('riskanalysis_tab_panel');
		if(tabPanel.activeTab.id == 'riskanalysis_riskitem_tc_grid')
		{
			//console.log('111 : ',tabPanel);

			//riskanalysis_riskitem_tc_store.getRootNode().removeAll();
			/*
			riskanalysis_riskitem_tc_store.setRootNode({
			  id: 'tc_0',
			  text: 'root'
			  // other configs in root
			});
			*/

			//riskanalysis_riskitem_tc_store.reload();//{params:{'ri_seq':obj.ri_seq}});
			//riskanalysis_riskitem_tc_grid.getRootNode().load();
		}else{
			//console.log(tabPanel);
			return false;
		}

		var select_risk = Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.items;
		if(select_risk.length == 1){			
			thisStore.lastOptions.params.ri_seq = select_risk[0].data['ri_seq'];
			thisStore.proxy.extraParams.ri_seq = select_risk[0].data['ri_seq'];
			return true;
		}else{
			return false;
		}
	});


	var riskanalysis_riskitem_tc_grid = Ext.create('Ext.tree.Panel', {
		//layout	: 'fit',
		title	: '테스트 케이스',
		id: 'riskanalysis_riskitem_tc_grid',
		width: 500,
		height: 300,
		enableDD: true,
		useArrows: true,
		rootVisible: true,
		store: riskanalysis_riskitem_tc_store,
		root: {
			//nodeType: 'async',
            text: 'Root',
			//id:'tc_0',
            draggable: false
        },
		multiSelect: true,
		singleExpand: false,
		animate: false,		
		viewConfig: {
			plugins : {
				ptype: 'treeviewdragdrop',
				ddGroup: 'dd_testcase'
			},
			listeners: {
				beforedrop: function (node, data) {
				},
				drop: function (node, data, dropRec, dropPosition) {
					/*
					var list = Array();
					var select_node = data.records;
					var url = './index.php/Plugin_view/testcase/move_testcase';

					if(temp_tcplan[0] == 'backlog'){
						for(var i=0; i<select_node.length; i++){
							list.push(select_node[i].data['id']);
						}
					}else{
						url = './index.php/Plugin_view/testcase/move_testcase_target';
						for(var i=0; i<select_node.length; i++){
							list.push({
								'tl_seq':select_node[i].data['tl_seq'],
								'tl_inp_pid':select_node[i].data['tl_inp_pid'],
							});
						}
					}

					var target_id = dropRec.data.id;
					var target_tl_seq = dropRec.data.tl_seq;
					var target_type = dropRec.data.type;
					var target_tc_seq = dropRec.data.tc_seq;

					var params = {
						pr_seq	: project_seq,
						tc_plan : temp_tcplan[1],
						target_tc_seq : target_tc_seq,
						target_id : target_id,
						select_id : Ext.encode(list),
						position : dropPosition,
						target_type : target_type
					};

					mask.start();
					Ext.Ajax.request({
						url : url,
						params : params,
						method: 'POST',
						success: function ( result, request ) {
							if(result.responseText){
								Ext.getCmp('testcase_east_panel').removeAll();
								mask.stop();
							}
						},
						failure: function ( result, request ){
							mask.stop();
						}
					});
					*/
				}
			}
		},
		columns: [{
				xtype: 'treecolumn',
				text: Otm.com_subject, dataIndex: 'text',
				minWidth:150, flex: 1, sortable: true
			},{
				text: Otm.com_id, dataIndex: 'out_id',
				width:80, sortable: true, align:'center'
			},{
				text: '연결 정보<br>(<img src="resource/css/icon/bullet_green.gif" />,<img src="resource/css/icon/bullet_black.gif" />)', dataIndex: 'link_seq',
				width:70, sortable: true, align:'center',
				tooltip	: '녹색 : 선택된 리스크 아이템과 연결 <br>검정 : 다른 리스크 아이템과 연결',
				renderer:function(val){
					if(val){

						var select_risk = Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.items;
						if(select_risk.length == 1){			
							if(select_risk[0].data['ri_seq'] == val){
								var img = '<img src="resource/css/icon/bullet_green.gif" />';
								return img;
								return '*'+val;
							}else{
								var img = '<img src="resource/css/icon/bullet_black.gif" />';
								return img;
								return val;
							}
						}else{
							
						}
						
					}
				}
			},{
				hidden:true,
				text: 'disabled', dataIndex: 'disabled',
				width:100, sortable: true, align:'center'
			}],
		tbar:[{
			xtype	: 'button',
			iconCls	: 'ico-add',
			text	: '등록',
			tooltip	: '리스크아이템을 스윗으로 추가합니다.',
			handler	: function(){
				//alert('등록 후 연결');
				riskanalysis_testcase_link('add_link');
			}
		},'-',{
			xtype	: 'button',
			iconCls	: 'ico-link',
			text	: '연결',
			tooltip	: '리스크아이템과 스윗을 연결합니다.',
			handler	: function(){
				//alert('연결');
				riskanalysis_testcase_link('link');
			}
		},'-',{
			xtype	: 'button',
			iconCls	: 'ico-unlink',
			text	: '연결 해제',
			tooltip	: '리스크아이템과 스윗 연결을 해제합니다.',
			handler	: function(){
				//alert('연결');
				riskanalysis_testcase_link('unlink');
			}
		}]
	});
	//riskanalysis_riskitem_tc_grid.getRootNode().expand();
	/*
	*	Testcase mapping tab panel 
	*	END
	*/

	var tab_panel = {
		region		: 'south',
		xtype		: 'tabpanel',
		id			: 'riskanalysis_tab_panel',
		//split		: true,
		//collapsible	: true,
		//collapsed	: false,
		flex		: 1,
		deferredRender: false,
		activeTab	: 0,
		//plain		: true,
		border		: true,
		items		: [			
			riskanalysis_riskitem_requirement_tabpanel,
			riskanalysis_riskitem_tc_grid
		],
		listeners: {
			tabchange : function(tabPanel, newCard, oldCard, eOpts ) {
				//tabPanel.activeTab.id
				//console.log(tabPanel, newCard, oldCard, eOpts);
				if(tabPanel.activeTab.id == 'riskanalysis_riskitem_tc_grid')
				{
					riskanalysis_riskitem_tc_grid.getRootNode().expand();
					//var select_risk = Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.items;
					//if(select_risk.length == 1){			
						//thisStore.lastOptions.params.ri_seq = select_risk[0].data['ri_seq'];
						//thisStore.proxy.extraParams.ri_seq = select_risk[0].data['ri_seq'];

						//riskanalysis_riskitem_tc_store.reload({params:{'ri_seq':select_risk[0].data['ri_seq']}});
					//}
				}
			}
		}
		
	};

	var riskanalysis_riskitem_east_panel = {
		region		: 'east',
		layout		: 'border',
		id			: 'riskanalysis_riskitem_east_panel',
		split		: true,
		collapsible	: true,
		collapsed	: true,
		flex		: 1,
		animation	: false,
		minWidth	: 420,
		maxWidth	: 600,
		items		: [riskanalysis_riskitem_form, tab_panel]
	};


	Ext.onReady(function(){
		var main_panel = {
			layout		: 'border',
			id			: 'riskanalysis_riskitem_main',
			defaults	: {
				collapsible	: false,
				split		: false,
				bodyStyle	: 'padding:0px'
			},
			items		: [riskanalysis_riskitem_center_panel,riskanalysis_riskitem_east_panel]
		};
		Ext.getCmp('riskanalysis_riskitem').removeAll();

		Ext.getCmp('riskanalysis_riskitem').add(main_panel);
		Ext.getCmp('riskanalysis_riskitem').doLayout(true,false);

		form_reset();
	});
</script>