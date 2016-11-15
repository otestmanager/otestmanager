<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */

include_once "./plugins/defect/views/default/store.php";
?>
<script type="text/javascript">
	var status_value,severity_value,priority_value,frequency_value;
	var customform_seq = new Array();
	var tmp_customform = "";
	var is_customform_add = true;
	var fileCnt = 0;

	var project_seq = '<?=$project_seq?>';
	var tcplan = '<?=$tcplan?>';
	var temp_tcplan = tcplan.split('_');

	var testcase_customform_store = Ext.create('Ext.data.Store', {
		fields:['pc_category','pc_seq','pc_name','pc_is_required','pc_formtype','pc_default_value','pc_content','pc_is_use'],
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

	var mask = {
		start : function(){
			Ext.getBody().mask(Otm.com_msg_processing_data);
		},
		stop : function(){
			Ext.getBody().unmask();
		}
	}

	function get_select_testcase_plan()
	{
		var selItem = Ext.getCmp('project_treePanel').getSelectionModel().selected.items[0];
		if(selItem){
			return selItem.data;
		}else{
			return '';
		}
	}

	function get_select_testcase()
	{
		var selItem = Ext.getCmp('testcase_treegrid').getSelectionModel().selected.items[0];

		if(selItem){
			return selItem.data;
		}else{
			return '';
		}
	}

	var tc_inputitem_store = Ext.create('Ext.data.Store', {
		fields:['pc_seq', 'pc_name'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/testcase/input_item_list',
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
		autoLoad:true
	});

	function testcase_plan_form_window(obj){
		var win = Ext.getCmp('testcase_plan_form_window');
		if(win){
			win.close();
		}

		var select_seq = {
			xtype: 'hiddenfield', anchor: '100%', allowBlank : false,
			id: 'select_seq'
		};

		var subject = {
			xtype: 'textfield', anchor: '100%', allowBlank : false,
			minLength:2,maxLength:100,
			fieldLabel: Otm.tc_plan+' '+Otm.com_name+'(*)', id: 'testcase_plan_name'
		};

		var description = {
			xtype: 'textarea', anchor: '100%', allowBlank : true,
			fieldLabel: Otm.com_description, id: 'testcase_plan_description',
			height:'100'
		};

		var startdate = Ext.create('Ext.form.DateField', {
			anchor: '100%', format: 'Y-m-d', editable: false,
			fieldLabel: Otm.com_start_date, id: 'testcase_plan_startdate'
		});

		var enddate = Ext.create('Ext.form.DateField', {
			anchor: '100%', format: 'Y-m-d', editable: false,
			fieldLabel: Otm.com_end_date, id: 'testcase_plan_enddate'
		});

		var status = Ext.create('Ext.form.ComboBox', {
			anchor: '100%',
			fieldLabel: Otm.tc_status,
			id: 'testcase_plan_status',
			triggerAction	: 'all',
			forceSelection	: true,
			editable		: false,
			displayField	: 'name',
			valueField		: 'value',
			value	:'play',
			allowBlank		: false,
			store			: Ext.create('Ext.data.Store', {
				fields : ['name', 'value'],
				data   : [
					{name : Otm.com_progress, value: 'play'},
					{name : Otm.com_standby, value: 'push'},
					{name : Otm.com_completed, value: 'stop'}
				]
			})
		});

		if(obj.type == 'edit_plan'){
			select_seq.value = get_select_testcase_plan().tp_seq;
			subject.value = get_select_testcase_plan().text;
			description.value = get_select_testcase_plan().description;
			startdate.setValue(get_select_testcase_plan().startdate.substr(0,10));
			enddate.setValue(get_select_testcase_plan().enddate.substr(0,10));
			status.setValue(get_select_testcase_plan().status);
		}

		var testcase_plan_write_form = Ext.create('Ext.form.Panel',{
			id : 'testcase_plan_write_form',
			border:false,
			bodyStyle: 'padding: 10px;',
			autoScroll: true,
			labelWidth:'10',
			items: [select_seq,subject,description,startdate,enddate,status],
			buttons:[{
				text:Otm.com_save,
				iconCls:'ico-save',
				disabled: true,
				formBind: true,
				handler:function(btn){
					var form = this.up('form').getForm();
					if(form.isValid()) {

						var URL = './index.php/Plugin_view/testcase/create_plan';
						var params = {
							project_seq		: project_seq,
							tp_subject		: form.findField('testcase_plan_name').getValue(),
							tp_description	: form.findField('testcase_plan_description').getValue(),
							tp_startdate	: form.findField('testcase_plan_startdate').getValue(),
							tp_enddate		: form.findField('testcase_plan_enddate').getValue(),
							tp_status		: form.findField('testcase_plan_status').getValue()
						};

						if(obj.type == 'edit_plan'){
							URL = './index.php/Plugin_view/testcase/update_plan';
							params.tp_seq = form.findField('select_seq').getValue();
						}
						mask.start();
						Ext.Ajax.request({
							url : URL,
							params : params,
							method: 'POST',
							success: function ( result, request ) {
								if(result.responseText){
									var node = Ext.getCmp('project_treePanel').getStore().getNodeById('testcase_'+project_seq);
									if(node){
										Ext.getCmp('project_treePanel').getStore().load({node:node});
									}

									var win = Ext.getCmp('testcase_plan_form_window');
									if(win){
										win.close();
									}
									mask.stop();
								}
							},
							failure: function ( result, request ){
								mask.stop();
							}
						});

					}
				}
			},{
				text:Otm.com_reset,
				iconCls:'ico-reset',
				handler:function(btn){
					Ext.getCmp('testcase_plan_write_form').reset();
				}
			}]
		});

		Ext.create('Ext.window.Window', {
			title: obj.title,
			id	: 'testcase_plan_form_window',
			height: 300,
			width: 400,
			layout: 'fit',
			resizable : false,
			modal : true,
			constrainHeader: true,
			items: testcase_plan_write_form
		}).show('',function(){
			Ext.getCmp('testcase_plan_name').focus();
		});
	}

	function select_excel_sheet_testcase(sheets,params)
	{
		if(Ext.getCmp('select_excel_sheet_testcase_window')){
			Ext.getCmp('select_excel_sheet_testcase_window').removeAll();
		}else{
			Ext.create('Ext.window.Window', {
				title: 'Select Sheet',
				id	:'select_excel_sheet_testcase_window',
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


		var select_excel_sheet_testcase = new Ext.FormPanel({
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
					console.log(value);
					if(value != null){
						params.sheet_index = value;
						Ext.getCmp('select_excel_sheet_testcase_window').hide();
						testcase_uploadPanel_submit(params);
					}else{
						Ext.Msg.alert('OTM','Please, Select Sheet.');
					}
				}
			}]
		});

		Ext.getCmp('select_excel_sheet_testcase_window').add(select_excel_sheet_testcase);
		Ext.getCmp('select_excel_sheet_testcase_window').show();
	}

	function testcase_uploadPanel_submit(params)
	{
		if(Ext.getCmp("testcase_uploadPanel").getForm().isValid()){
			top.myUpdateProgress(1,'Data Loadding...');

			var URL = "./index.php/Import/plugin/testcase/import_testcase";

			Ext.getCmp("testcase_uploadPanel").mask(Otm.com_msg_processing_data);

			Ext.getCmp("testcase_uploadPanel").getForm().submit({
				url: URL,
				method:'POST',
				params: params,
				success: function(form, action){
					top.myUpdateProgress(100,'End');

					var obj = Ext.decode(action.response.responseText);
					if(obj.sheet_count && obj.sheet_count>1)
					{
						Ext.getCmp("testcase_uploadPanel").unmask();

						select_excel_sheet_testcase(obj.sheet_names,params);
						return;
					}

					Ext.getCmp('testcase_treegrid').getRootNode().removeAll();
					Ext.getCmp('testcase_treegrid').getStore().load({params:{tcplan:get_select_testcase_plan().id}});

					Ext.getCmp("testcase_uploadPanel").unmask();
					Ext.getCmp("testcase_import_window").hide();
				},
				failure: function(form, action){
					var obj = Ext.decode(action.response.responseText);

					var msg = Ext.decode(obj.msg);
					if(msg.over){
						Ext.Msg.alert('OTM 1',msg.over);

						top.myUpdateProgress(100,'End');
						Ext.getCmp("testcase_uploadPanel").unmask();
						return;
					}else if(msg.duplicate_id){
						Ext.Msg.confirm('OTM',Otm.com_msg_duplicate_id+'<br>'+msg.duplicate_id,function(bt){
							if(bt=='yes'){
								var params = {
									project_seq	: get_select_testcase_plan().pr_seq,
									import_check_id : true,
									update : true
								};

								testcase_uploadPanel_submit(params);
							}else{
								Ext.getCmp("testcase_import_window").hide();
								return;
							}
						})
					}else{
						Ext.Msg.alert('OTM 2',obj.msg);
					}

					top.myUpdateProgress(100,'End');
					Ext.getCmp("testcase_uploadPanel").unmask();
				}
			});
		}
	}

	function viewImportWin()
	{
		var win = Ext.getCmp('testcase_import_window');

		var column = [{
			text: 'Location',
			align:'center'
		},{
			text: 'TC ID',
			align:'center'
		},{
			text: Otm.tc+' '+Otm.com_name,
			align:'center'
		}]
		for(var i=0;i<testcase_customform_store.data.items.length;i++){
			column.push({
				 header: testcase_customform_store.data.items[i].data.pc_name,  align:'center'
			});
		}

		if(win){
			Ext.getCmp('form_file').clearOnSubmit = true;
			Ext.getCmp("testcase_uploadPanel").reset();
			Ext.getCmp('form_file').clearOnSubmit = false;
			Ext.getCmp("testcase_uploadPanel").update();

			Ext.getCmp("sample_grid").reconfigure(undefined,column);

			win.show();
			return;
		}

		var sample_grid = Ext.create("Ext.grid.Panel",{
			id:'sample_grid',
			border:true,
			forceFit: true,
			columns:column
		});

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

		var testcase_uploadPanel = new Ext.FormPanel({
			fileUpload: true,
			id:'testcase_uploadPanel',
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
			},sample_grid],
			buttons: [pbar,{
				text:Otm.com_save,
				formBind: true,
				iconCls:'ico-save',
				id:'testCaseImportSubmitBtn',
				handler: function(){

					var params = {
								project_seq	: get_select_testcase_plan().pr_seq,
								import_check_id : true,
								update : false
							};
					var chkInfo = Ext.getCmp('import_check_id').items;
					if(chkInfo.items[1].checked){
						params.import_check_id = true;
					}

					testcase_uploadPanel_submit(params);
				}
			}]
		});

		var import_window = Ext.create('Ext.window.Window', {
			title: Otm.tc+' '+Otm.com_import+'(.xls)',
			id	: 'testcase_import_window',
			height: 230,
			width: 650,
			layout: 'fit',
			resizable : true,
			modal : true,
			constrainHeader: true,
			closeAction: 'hide',
			items: [testcase_uploadPanel]
		});

		import_window.show();
	}


	/**
	* Tree 열기
	*/
	function getSliderBar(panelId){
		var sliderBar = new Ext.Slider({
			width: 100,	increment: 1,	minValue: 0,value:0,
			maxValue: 10,
			listeners:{
				change : function(slider, thumb, newValue, oldValue){
					var selectValue = newValue.value;
					var rootNode = Ext.getCmp(panelId);

					var path = rootNode.items.items[0].node;
					var path = rootNode.items.items[0].node;

					Ext.getCmp(panelId).getRootNode().expand(true);
				}
			}
		});
		return sliderBar;
	}

	/**
	 * 담당자 취소
	 */
	function assign_cancel()
	{
		if(get_select_testcase().text=='Root'){
			Ext.Msg.alert('OTM',Otm.com_msg_select_items_assign);
			return;
		}

		var params = {
			tp_seq			: get_select_testcase_plan().tp_seq,
			deadline_date	: '',
			assign_to		: ''
		};

		var Records = Ext.getCmp('testcase_treegrid').getSelectionModel().selected.items;
		var tl_seq_list = Array();

		if(Records.length >= 1){
			for(var i=0; i<Records.length; i++){
					tl_seq_list.push(Records[i].data['tl_seq']);
			}
			params.tl_seq_list = Ext.encode(tl_seq_list);
		}else{
			Ext.Msg.alert('OTM',Otm.com_msg_select_items_assign);
			return;
		}

		Ext.Msg.confirm('OTM',Otm.com_msg_assign_cancel,function(bt){
			if(bt=='yes'){
				Ext.Ajax.request({
					url : './index.php/Plugin_view/testcase/assign_testcase',
					params : params,
					method: 'POST',
					success: function ( result, request ) {
						var obj = Ext.decode(result.responseText);
						var node = Ext.decode(obj.data);

						node.cmd = 'update';
						node.target_grid = "testcase_treegrid";
						NodeReload(node);
						return;
					},
					failure: function ( result, request ){
						Ext.Msg.alert('OTM', 'Fail :'+result.responseText);
					}
				});
			}
		});
	}

	/**
	* Add, Update, Delete menu Button
	*/
	var menu = Ext.create('Ext.menu.Menu', {
		style: {
			overflow: 'visible'
		},
		items: [{
				text: Otm.tc_plan+' '+Otm.com_add,
				iconCls:'ico-add',
				action_type:'tc_add',
				listeners : {
					click: function(btn, e, eOpts) {
						var data = {
							type : 'add_plan',
							title : btn.text
						};
						testcase_plan_form_window(data);
					}
				}
			},'-',{
				text: Otm.tc_plan+' '+Otm.com_update,
				iconCls:'ico-update',
				action_type:'tc_edit_all',
				listeners : {
					click: function(btn, e, eOpts) {
						var node = Ext.getCmp('project_treePanel').getSelectionModel().getSelection();
						if(!node[0] || !node[0].data.tp_seq){
							Ext.Msg.alert('OTM',Otm.com_msg_select_plan);
							return;
						}

						if(node[0].id == 'backlog_'+project_seq){
							Ext.Msg.alert('OTM',Otm.com_msg_cannot_edit_backlog);
							return;
						}

						var data = {
							type : 'edit_plan',
							title : btn.text
						};
						testcase_plan_form_window(data);
					}
				}
			},'-',{
				text: Otm.tc_plan+' '+Otm.com_remove,
				iconCls:'ico-remove',
				action_type:'tc_delete_all',
				tooltip: Otm.com_msg_plan_alldata_delete,
				listeners : {
					click: function(btn, e, eOpts) {
						var node = Ext.getCmp('project_treePanel').getSelectionModel().getSelection();
						if(!node[0] || !node[0].data.tp_seq){
							Ext.Msg.alert('OTM',Otm.com_msg_select_plan);
							return;
						}

						var plan_type = node[0].id.split('_');

						if(plan_type[0] == 'backlog'){
							Ext.Msg.alert('OTM',Otm.com_msg_cannot_del_backlog);
							return;
						}else if(plan_type[0] != 'tcplan'){
							return;
						}

						Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
							if(bt=='yes'){
								var params = {
									tp_seq : plan_type[1]
								};

								mask.start();
								Ext.Ajax.request({
									url : './index.php/Plugin_view/testcase/delete_plan',
									params :params,
									method: 'POST',
									success: function ( result, request ) {
										if(result.responseText){
											var node = Ext.getCmp('project_treePanel').getStore().getNodeById('testcase_'+project_seq);
											if(node){
												Ext.getCmp('project_treePanel').getStore().load({node:node});
											}

											Ext.getCmp('testcase_treegrid').getStore().load({params:{tcplan:'backlog_'+project_seq}});
											Ext.getCmp('testcase_treegrid').getSelectionModel().deselectAll();

											Ext.getCmp('testcase_east_panel').removeAll();

											Ext.getCmp('project_treePanel').getSelectionModel().deselectAll();

											mask.stop();
										}
									},
									failure: function ( result, request ) {
										mask.stop();
									}
								});

							}else{
								return;
							}
						});
					}
				}
			}]
	});

	var testcase_toolbar = Ext.create('Ext.toolbar.Toolbar');
		testcase_toolbar.suspendLayout = true;
		testcase_toolbar.add({
				text: Otm.tc_plan+' '+Otm.com_management,
				tooltip: Otm.com_msg_cannot_fun_plan,
				menu: menu
			});
		testcase_toolbar.add('-');
		testcase_toolbar.add({
			xtype: 'button',
			text: Otm.tc_suite+' '+Otm.com_add,
			iconCls:'ico-add',
			action_type:'tc_add',
			tooltip: Otm.com_msg_suite_tc_add_info,
			handler:function (btn){
				var obj = {
					form_type : 'suite',
					action_type : 'add'
				};
				get_testcase_write_form(obj);
			}
		});
		testcase_toolbar.add('-');
		testcase_toolbar.add({
			xtype: 'button',
			text: Otm.tc+' '+Otm.com_add,
			iconCls:'ico-add',
			action_type:'tc_add',
			tooltip: Otm.com_msg_suite_tc_add_info,
			handler:function (btn){
				var obj = {
					form_type : 'case',
					action_type : 'add'
				};
				get_testcase_write_form(obj);
			}
		});
		testcase_toolbar.add('-');
		testcase_toolbar.add({
			xtype: 'button',
			text: Otm.com_update,
			iconCls:'ico-update',
			action_type:'tc_edit',
			tooltip: Otm.com_msg_select_item_change,
			handler:function (btn){
				if(get_select_testcase().id == 'root'){
					return;
				}

				if(get_select_testcase() == ''){
					Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
					return;
				}

				if(Ext.getCmp('testcase_treegrid').getSelectionModel().selected.length > 1){
					Ext.Msg.alert('OTM',Otm.com_msg_only_one);
					return;
				}

				if(Ext.getCmp('testcase_treegrid').getSelectionModel().selected.length == 1){
					if(check_role('tc_edit_all')){
					}else if(check_role('tc_edit')){
						if(get_select_testcase().writer != mb_email){
							Ext.Msg.alert('OTM',Otm.tc+' '+Otm.com_update+Otm.com_msg_noRole);
							return;
						}
					}else{
						Ext.Msg.alert('OTM',Otm.tc+' '+Otm.com_update+Otm.com_msg_noRole);
						return;
					}

					var obj = {
						form_type : (get_select_testcase().type == 'folder')?'suite':'case',
						action_type : 'edit'
					};
					get_testcase_write_form(obj);
					return;
				}
			}
		});
		testcase_toolbar.add('-');
		testcase_toolbar.add({
			xtype: 'button', text: Otm.com_remove,
			iconCls:'ico-remove',
			action_type:'tc_delete',
			tooltip: Otm.com_msg_select_item_delete,
			handler:function (btn){

				if(get_select_testcase() == ''){
					Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
					return;
				}
				if(get_select_testcase().text=='Root'){
					Ext.Msg.alert('OTM',Otm.com_msg_root_cannot_delete);
					return;
				}

				if(Ext.getCmp('testcase_treegrid').getSelectionModel().selected.length >= 1){
					Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
						if(bt=='yes'){
							var Records = Ext.getCmp('testcase_treegrid').getSelectionModel().selected.items;
							var list = Array();
							var writer_list = Array();
							var params = {};

							if(Records.length >= 1){
								params.pr_seq = Records[0].data['pr_seq'];
								params.tp_seq = Records[0].data['tp_seq'];

								for(var i=0; i<Records.length; i++){
									list.push(Records[i].data['id']);
									writer_list.push(Records[i].data['writer']);
								}

								params.list = Ext.encode(list);
								params.writer = Ext.encode(writer_list);
							}

							mask.start();
							Ext.Ajax.request({
								url : './index.php/Plugin_view/testcase/delete_testcase',
								params :params,
								method: 'POST',
								success: function ( result, request ) {
									Records.cmd = "delete";
									if(result.responseText){
										var info = Ext.decode(result.responseText);
										if(info && info.msg && info.msg != ''){
											mask.stop();
											Ext.Msg.alert('OTM',info.msg);
											return;
										}

										mask.stop();
										NodeReload(Records);
										Ext.getCmp('testcase_treegrid').getSelectionModel().deselectAll();
										Ext.getCmp('testcase_east_panel').removeAll();
									}
								},
								failure: function ( result, request ) {
									Ext.Msg.alert('OTM',request.result.msg);
									mask.stop();
								}
							});
						}else{
							return;
						}
					});
				}else{
					Ext.Msg.alert('OTM',Otm.com_msg_only_one);
					return;
				}
			}
		});

		if(temp_tcplan[0] == 'backlog'){
			testcase_toolbar.add('-');
			testcase_toolbar.add({
				xtype: 'button', text: Otm.tc_plan_copy,
				iconCls:'ico-copy',
				action_type:'tc_add',
				disabled: (temp_tcplan[0] == 'backlog')?false:true,
				tooltip: Otm.com_msg_can_use_backlog,
				handler:function (btn){
					var obj = {
						form_type : (get_select_testcase().type == 'folder')?'suite':'case'
					};

					Ext.getCmp('testcase_east_panel').add(testcase_target_treegrid(obj));
				}
			});
		}

		if(temp_tcplan[0] != 'backlog'){
			testcase_toolbar.add('-');
			testcase_toolbar.add({
				xtype: 'button', text: Otm.tc_assign_persion,
				iconCls:'ico-man',
				action_type:'tc_edit_all',
				disabled: (temp_tcplan[0] == 'backlog')?true:false,
				tooltip: Otm.com_msg_plan_use_suite,
				handler:function (btn){
					Ext.getCmp('testcase_east_panel').add(get_testcase_assign_form());
				}
			});

			testcase_toolbar.add('-');
			testcase_toolbar.add({
				xtype: 'button', text: Otm.tc_assign_persion_cancel,
				iconCls:'ico-unman',
				action_type:'tc_edit_all',
				disabled: (temp_tcplan[0] == 'backlog')?true:false,
				handler:function (btn){
					assign_cancel();
				}
			});
		}

		testcase_toolbar.add('-');
		testcase_toolbar.add({
			xtype: 'button', text: Otm.com_export,
			iconCls:'ico-export',
			action_type:'tc_view_all',
			handler:function (btn){
				var node = Ext.getCmp('project_treePanel').getSelectionModel().getSelection();
				var plan_type = node[0].id.split('_');

				if(node){
					var params = 'project_seq='+get_select_testcase_plan().pr_seq+'&tcplan='+get_select_testcase_plan().id;
					export_data('plugin/testcase/testcase_list_export',params);
				}else{
					Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
				}
			}
		});


		if(temp_tcplan[0] == 'backlog'){
			testcase_toolbar.add('-');
			testcase_toolbar.add({
				xtype: 'button', text: Otm.com_import,
				iconCls:'ico-import',
				action_type:'tc_add',
				handler:function (btn){
					var node = Ext.getCmp('project_treePanel').getSelectionModel().getSelection();
					if(!node[0]){
						Ext.Msg.alert('OTM',Otm.com_msg_select_plan);
						return;
					}

					var plan_type = node[0].id.split('_');

					if(plan_type[0] == "backlog"){
						viewImportWin();
					}
				}
			});
		}

	var comtc_product_store = Ext.create('Ext.data.Store', {
		fields:['seq','text'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Com_testcase/product_tree_list',
			extraParams: {
				node:'root'
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

	var comtc_versioin_store = Ext.create('Ext.data.Store', {
		fields:['seq','text'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Com_testcase/product_tree_list',
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

	var comtc_copy_menu = Ext.create('Ext.menu.Menu', {
			style: {
				overflow: 'visible'
			},
			items: [{
					xtype:'combo',
					id: 'comtc_product_combo',
					hideLabel: true,
					store: comtc_product_store,
					displayField: 'text',
					valueField:'seq',
					typeAhead: true,
					queryMode: 'local',
					triggerAction: 'all',
					emptyText: 'Select a product...',
					selectOnFocus: true,
					width: 135,
					indent: true,
					listeners: {
						select: function( combo, records, eOpts ) {
							Ext.getCmp('comtc_versioin_combo').setValue('');
							comtc_versioin_store.load({params:{node:records[0].get('seq')+'_comproduct'}});
							Ext.getCmp('comtc_versioin_combo').setDisabled(false);
						}
					}
				},'-',{
					xtype:'combo',
					id: 'comtc_versioin_combo',
					hideLabel: true,
					store: comtc_versioin_store,
					displayField: 'text',
					valueField:'seq',
					disabled: true,
					typeAhead: true,
					queryMode: 'local',
					triggerAction: 'all',
					emptyText: 'Select a version...',
					selectOnFocus: true,
					width: 135,
					indent: true
				},'-',{
					text: Otm.com_import,
					iconCls:'ico-import',
					action_type:'tc_add',
					listeners : {
						click: function(btn, e, eOpts) {
							var product_seq = Ext.getCmp('comtc_product_combo').getValue();
							var version_seq = Ext.getCmp('comtc_versioin_combo').getValue();
							if(product_seq && version_seq){

								Ext.Msg.confirm('OTM',Otm.com_msg_overwritten_commontc,function(bt){
									if(bt=='yes'){

										var URL = './index.php/Plugin_view/testcase/copy_comtestcase';
										var params = {
											project_seq : project_seq,
											p_seq	: product_seq,
											v_seq	: version_seq
										};

										mask.start();
										Ext.Ajax.request({
											url : URL,
											params : params,
											method: 'POST',
											success: function ( result, request ) {
												if(result.responseText){
													Ext.getCmp('testcase_treegrid').getStore().load({params:{tcplan:get_select_testcase_plan().id}});
													Ext.getCmp('testcase_treegrid').getSelectionModel().deselectAll();
													Ext.getCmp('testcase_east_panel').removeAll();
													mask.stop();
												}
											},
											failure: function ( result, request ){
												mask.stop();
											}
										});
									}else{
										return;
									}
								});
							}else{
								Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
								return;
							}
						}
					}
				}]
		});

	if(temp_tcplan[0] == 'backlog'){
		testcase_toolbar.add('-');
		testcase_toolbar.add({
			text: Otm.comtc+' '+Otm.com_import,
			iconCls:'ico-import',
			action_type:'tc_add',
			menu: comtc_copy_menu
		});
	}

	/**
	* TreeGrid
	*/
	var testcase_tree_store = Ext.create('Ext.data.TreeStore', {
		root: {
			text: 'Root',
			//expanded: true,
			expandable: false,
			children: []
		},
		proxy: {
			type: 'ajax',
			url:'./index.php/Plugin_view/testcase/testcase_tree_list',
			extraParams: {
				project_seq : project_seq,
				tcplan		: tcplan
			},
			reader: {
				type: 'json',
				totalProperty: 'totalCount',
				rootProperty: 'data'
			}
		},
		folderSort: true
	});

	testcase_tree_store.on('load',function(){
		Ext.getCmp('acc').unmask();
	});

	var tree_grid_columns = [];
	if(temp_tcplan[0] == 'backlog'){
		tree_grid_columns = [{
				xtype: 'treecolumn',
				text: Otm.com_subject, dataIndex: 'text',
				minWidth:150, flex: 1, sortable: true
			},{
				text: Otm.com_id, dataIndex: 'out_id',
				width:100, sortable: true, align:'center'
			},{
				text: Otm.com_creator, dataIndex: 'writer_name',
				width:100, sortable: true, align:'center'
			},{
				text: Otm.com_date, dataIndex: 'regdate', align:'center',
				width:120, sortable: true, renderer:function(value,index,record){
					if(value){
						var value = value.substr(0,10);
					}else{
						value = '';
					}
					return value;
				}
			}];
	}else{
		tree_grid_columns = [{
				xtype: 'treecolumn',
				text: Otm.com_subject, dataIndex: 'text',
				minWidth:150, flex: 1, sortable: true
			},{
				text: Otm.com_id, dataIndex: 'out_id',
				width:100, sortable: true, align:'center'
			},{
				text: Otm.com_user, dataIndex: 'assign_name',
				width:100, sortable: true, align:'center'
			},{
				text: Otm.tc_deadline, dataIndex: 'deadline_date', align:'center',
				width:80, sortable: true, renderer:function(value,index,record){
					if(value){
						var value = value.substr(0,10);
					}else{
						value = '';
					}

					if(value == '0000-00-00') value = '';
					return value;
				}
			},{
				text: Otm.tc_execution_result, dataIndex: 'result_value', align:'center',
				width:120, sortable: true, align:'center'
			},{
				text: Otm.tc_execution_user, dataIndex: 'result_writer_name', align:'center',
				width:120, sortable: true, align:'center'
			},{
				text: '실행일', dataIndex: 'result_date', align:'center',
				width:80, sortable: true, renderer:function(value,index,record){
					if(value){
						var value = value.substr(0,10);
					}else{
						value = '';
					}
					if(value == '0000-00-00') value = '';
					return value;
				}
			}];
	}

	var testcase_treegrid = Ext.create('Ext.tree.Panel', {
		id: 'testcase_treegrid',
		width: 500,
		height: 300,
		enableDD: true,
		useArrows: true,
		rootVisible: true,
		store: testcase_tree_store,
		root: {
			nodeType: 'async',
            text: 'Root',
            draggable: false
        },
		multiSelect: true,
		singleExpand: false,
		animate: false,
		listeners:{
			itemdblclick:function(view,rec,index,eventObj) {
				if(Ext.getCmp('testcase_target_treegrid') || Ext.getCmp('testcase_project_user_grid')){
					return;
				}
				var obj = {
					form_type : (rec.get('type') == 'folder')?'suite':'case',
					id : rec.get('id'),
					pid : rec.get('pid'),
					tl_seq : rec.get('tl_seq')
				};

				if(get_select_testcase().id == 'root'){
					return;
				}
				get_testcase_view_form(obj);
			},
			select : function(view,rec,index,eventObj) {
				if(Ext.getCmp('testcase_target_treegrid') || Ext.getCmp('testcase_project_user_grid')){
					return;
				}
				var obj = {
					form_type : (rec.get('type') == 'folder')?'suite':'case',
					id : rec.get('id'),
					pid : rec.get('pid'),
					tl_seq : rec.get('tl_seq')
				};

				if(get_select_testcase().id == 'root'){
					return;
				}
				get_testcase_view_form(obj);
			}
		},
		viewConfig: {
			plugins : {
				ptype: 'treeviewdragdrop',
				ddGroup: 'dd_testcase'
			},
			listeners: {
				beforedrop: function (node, data) {
				},
				drop: function (node, data, dropRec, dropPosition) {
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
				}
			}
		},
		columns: tree_grid_columns,
		tbar:[{
			xtype: 'segmentedbutton',
			id:'cookie_testcase_east_panel',
			items: [{
				text: Otm.com_default_panel_open,
				value:'open'
			},{
				text: Otm.com_default_panel_close,
				value:'close'
			}],
			listeners : {
                toggle : function (btn, button, isPressed, eOpts ) {
					if(button.value){
						set_JSCookie('cookie_testcase_east_panel', button.value, 1);
					}else{
						set_JSCookie('cookie_testcase_east_panel', button.value, 1);
					}
                }
            }
		}],
		bbar:tree_OpenClose_Btn('testcase_treegrid')
	});
	testcase_treegrid.getRootNode().expand();


	/**
	* Center Panel
	*/
	var testcase_center_panel =  {
		layout	: 'fit',
		region	: 'center',
		items	: [testcase_treegrid],
		tbar	: testcase_toolbar
	};

	/**
	* East Form Panel
	*/
	function get_testcase_write_form(obj)
	{
		if(obj.action_type == 'add'){
			if(get_select_testcase_plan() == '' || get_select_testcase_plan().type != 'testcase_plan'){
				Ext.Msg.alert('OTM',Otm.com_msg_plan_select_plan);
				Ext.getCmp('testcase_east_panel').removeAll();
				return;
			}else{
				if(get_select_testcase().type == 'file'){
					Ext.Msg.alert('OTM',Otm.com_msg_testcase_inadd);
					Ext.getCmp('testcase_east_panel').removeAll();
					return false;
				}
			}
		}else{
			if(get_select_testcase() == ''){
				Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
				Ext.getCmp('testcase_east_panel').removeAll();
				return;
			}
		}

		Ext.getCmp('testcase_east_panel').removeAll();

		var testcase_east_panel = Ext.getCmp('testcase_east_panel');
		if(testcase_east_panel.collapsed==false){
		}else{
			testcase_east_panel.expand();
		}

		var items = [];
		switch(obj.form_type){
			case 'suite':
				var subject = {
					xtype: 'textfield', anchor: '100%', allowBlank : false,
					minLength:2,maxLength:100,
					fieldLabel: Otm.tc_suite+' '+Otm.com_name+'(*)',
					id: 'testcase_suite_name',
					name: 'testcase_suite_name'
					};
				var description = {
					xtype: 'textarea', anchor: '100%', allowBlank : true,
					fieldLabel: Otm.com_description,
					id: 'testcase_suite_description',
					name: 'testcase_suite_description',
					height:'100'
					};

				items = [subject,description];
				break;
			case 'case':
				var subject = {
					xtype: 'textfield', anchor: '100%',allowBlank : false,
					minLength:2,maxLength:100,
					fieldLabel: Otm.tc+' '+Otm.com_name+'(*)',
					id: 'testcase_case_name',
					name: 'testcase_case_name'
					};
				var precondition = {
					xtype: 'textarea', anchor: '100%', allowBlank : true,
					fieldLabel: Otm.tc_precondition,
					id: 'testcase_case_precondition',
					name: 'testcase_case_precondition',
					height:'100'
					,hidden:true
					};
				var testdata = {
					xtype: 'textarea', anchor: '100%', allowBlank : true,
					fieldLabel: Otm.tc_testdata,
					id: 'testcase_case_testdata',
					name: 'testcase_case_testdata',
					height:'100'
					,hidden:true
					};
				var procedure = {
					xtype: 'textarea', anchor: '100%', allowBlank : true,
					fieldLabel: Otm.tc_action_performed,
					id: 'testcase_case_procedure',
					name: 'testcase_case_procedure',
					height:'100'
					,hidden:true
					};
				var expected_result = {
					xtype: 'textarea', anchor: '100%', allowBlank : true,
					fieldLabel: Otm.tc_expected_result,
					id: 'testcase_case_expected_result',
					name: 'testcase_case_expected_result',
					height:'100'
					,hidden:true
					};
				var description = {
					xtype: 'textarea', anchor: '100%', allowBlank : true,
					fieldLabel: Otm.tc_remarks,
					id: 'testcase_case_description',
					name: 'testcase_case_description',
					height:'100'
					,hidden:true
					};
				var tc_item_form = {
					xtype:'panel',
					id: 'tc_item_form',
					border:false,
					items:[]
				};
				var tc_custom_form = {
					xtype:'panel',
					id: 'tc_custom_form',
					border:false,
					items:[]
				}
				var tc_fileForm = {
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
							xtype: 'filefield',//
							name : 'form_file[]',
							allowBlank : true,
							reference: 'basicFile'
						},{
							xtype:'panel',
							border:false,width:5
						},{
							xtype:'button',
							bodyStyle:'padding-left:10px;background-color:white;',
							text:'Add',
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
				}

				items = [subject,precondition,testdata,procedure,expected_result,description,tc_item_form,tc_custom_form,tc_fileForm];
		}

		var testcase_write_form = Ext.create('Ext.form.Panel',{
			region		: 'center',
			id			: 'testcase_write_form',
			border		: false,
			bodyStyle	: 'padding: 10px;',
			autoScroll	: true,
			labelWidth	: '10',
			items		: items,
			buttons:[{
				text:Otm.com_save,
				iconCls:'ico-save',
				formBind: true,
				handler:function(btn){
					var form = Ext.getCmp("testcase_write_form").getForm();
					if(form.isValid()) {
						var URL = './index.php/Plugin_view/testcase/create_testcase';
						var action_type = "add";

						var params = {
							writer	: get_select_testcase().writer,
							pr_seq	: project_seq,
							tp_seq	: get_select_testcase_plan().tp_seq,
							tc_seq	: get_select_testcase().tc_seq,
							tl_seq	: get_select_testcase().tl_seq,
							type	: obj.form_type,
							pid		: (get_select_testcase() != '')?get_select_testcase().id:''
						};

						if(obj.form_type == 'suite'){
							params.tc_subject = form.findField('testcase_suite_name').getValue();
							params.tc_description = form.findField('testcase_suite_description').getValue();

						}else if(obj.form_type == 'case'){
							params.tc_subject = form.findField('testcase_case_name').getValue();
							params.tc_precondition = form.findField('testcase_case_precondition').getValue();
							params.tc_testdata = form.findField('testcase_case_testdata').getValue();
							params.tc_procedure = form.findField('testcase_case_procedure').getValue();
							params.tc_expected_result = form.findField('testcase_case_expected_result').getValue();
							params.tc_description = form.findField('testcase_case_description').getValue();

							var user_customform_result = new Array();
							var commit_info = testcase_write_form.getForm().getValues();
							for(var i=0;i<customform_seq.length;i++){
								user_customform_result.push({
									name	: customform_seq[i].name,
									seq		: customform_seq[i].seq,
									type	: customform_seq[i].type,
									value	: eval("commit_info.custom_"+customform_seq[i].seq)
								});
							}
							params.custom_form  = Ext.encode(user_customform_result);
						}

						if(obj.action_type == 'edit'){
							action_type = "update";
							var URL = './index.php/Plugin_view/testcase/update_testcase';
							params.seq = get_select_testcase().seq;
							params.pid = get_select_testcase().pid;
						}

						form.submit({
							url: URL,
							method:'POST',
							params: params,
							waitMsg: Otm.com_msg_processing_data,
							success: function(form, action) {
								var obj = Ext.decode(action.response.responseText);
								if(obj.data.msg && obj.data.msg == 'over_num'){
									Ext.Msg.alert('OTM',Otm.id_rule.over_id_number_msg);
									return;
								}

								var node = Ext.decode(obj.data);

								node.cmd = action_type;
								node.target_grid = "testcase_treegrid";

								NodeReload(node);
								Ext.getCmp('testcase_write_form').reset();

								if(action_type == 'update'){
									rec = get_select_testcase();

									var obj = {
										form_type : (rec.type == 'folder')?'suite':'case',
										id : rec.id,
										pid : rec.pid,
										tl_seq : rec.tl_seq
									};
									get_testcase_view_form(obj);
								}

								return;
							},
							failure: function(form, action) {
								Ext.Msg.alert('OTM',action.result.msg);
							}
						});
						return;
					}
				}
			},{
				text:Otm.com_reset,
				iconCls:'ico-reset',
				hidden: true,
				handler:function(btn){
					Ext.getCmp('testcase_write_form').reset();
				}
			}]
		});

		if(obj.action_type == 'edit'){
			var URL = './index.php/Plugin_view/testcase/get_testcase_info';
			mask.start();
			Ext.Ajax.request({
				url : URL,
				params : {
					id : get_select_testcase().id,
					pr_seq : project_seq,
					tl_seq : (get_select_testcase().tl_seq)?get_select_testcase().tl_seq:'',
					action_type : obj.action_type
				},
				method: 'POST',
				success: function ( result, request ) {
					if(result.responseText){
						var testcase_info = Ext.decode(result.responseText);
						var selItem = Ext.getCmp('testcase_treegrid').getSelectionModel().selected.items[0];

						var default_fieldset = {
							region		: 'north',
							xtype		: 'fieldset',
							collapsible	: false,
							collapsed	: false,
							border		: false,
							items		: [{
								xtype		: 'displayfield',
								fieldLabel	: Otm.tc_plan,
								value		: testcase_info.data.plan_name
							},{
								xtype		: 'displayfield',
								fieldLabel	: 'ID',
								value		: testcase_info.data.tc_out_id
							},{
								xtype		: 'displayfield',
								fieldLabel	: Otm.com_creator,
								value		: testcase_info.data.writer_name
							},{
								xtype		: 'displayfield',
								fieldLabel	: Otm.com_date,
								value		: testcase_info.data.regdate.substr(0,10)
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

						Ext.getCmp('testcase_east_panel').add(view_form);

						switch(obj.form_type){
							case 'suite':
								selItem.data.testcase_suite_name		= testcase_info.data.tc_subject;
								selItem.data.testcase_suite_description	= testcase_info.data.tc_description;

								Ext.getCmp('testcase_write_form').loadRecord(selItem);
								Ext.getCmp('testcase_east_panel').add(testcase_write_form);
								mask.stop();
								break;
							case 'case':
								selItem.data.testcase_case_name				= testcase_info.data.tc_subject;
								selItem.data.testcase_case_precondition		= testcase_info.data.tc_precondition;
								selItem.data.testcase_case_testdata			= testcase_info.data.tc_testdata;
								selItem.data.testcase_case_procedure		= testcase_info.data.tc_procedure;
								selItem.data.testcase_case_expected_result	= testcase_info.data.tc_expected_result;
								selItem.data.testcase_case_description		= testcase_info.data.tc_description;



								testcase_customform_store.load({
									callback: function(r,options,success){
										Ext.getCmp('tc_item_form').add(_setCustomform('TC_ITEM',r));
										Ext.getCmp('tc_custom_form').add(_setCustomform('ID_TC',r));

										var tc_customform = testcase_info.data.df_customform;
										_setCustomform_userdata(customform_seq,tc_customform);

										Ext.getCmp('testcase_east_panel').add(testcase_write_form);
										Ext.getCmp('testcase_write_form').loadRecord(selItem);
										mask.stop();
									}
								});
							break;
						}
					}
				},
				failure: function ( result, request ){
					mask.stop();
				}
			});
		}else{
			if(obj.form_type == 'case'){
				testcase_customform_store.load({
					callback: function(r,options,success){
						Ext.getCmp('tc_item_form').add(_setCustomform('TC_ITEM',r));
						Ext.getCmp('tc_custom_form').add(_setCustomform('ID_TC',r));

						Ext.getCmp('testcase_east_panel').add(testcase_write_form);
					}
				});
			}else{
				Ext.getCmp('testcase_east_panel').add(testcase_write_form);
			}
		}
	}

	function get_testcase_view_form(obj)
	{
		if(get_JSCookie('cookie_testcase_east_panel') != "open"){
			Ext.getCmp('testcase_east_panel').removeAll();
			Ext.getCmp('testcase_east_panel').collapse();
			return;
		}

		if(Ext.getCmp('testcase_treegrid').getSelectionModel().selected.length > 1){
			Ext.getCmp('testcase_east_panel').removeAll();
			Ext.getCmp('testcase_east_panel').collapse();
			return;
		}

		Ext.getCmp('testcase_east_panel').removeAll();
		Ext.getCmp('testcase_east_panel').add({
			region	: 'center', layout:'fit', xtype:'panel',
			animation: false, autoScroll: true,
			id:'testcase_east_view_panel'
		});

		var testcase_east_panel = Ext.getCmp('testcase_east_panel');
		if(testcase_east_panel.collapsed==false){
		}else{
			testcase_east_panel.expand();
		}

		obj.target = 'testcase_east_view_panel';
		obj.pr_seq = project_seq;
		get_testcase_view_panel(obj);

		if(obj.form_type == 'suite') return;

		var plan_type = tcplan.split('_');
		
		if(plan_type[0] != 'backlog'){
			Ext.Ajax.request({
				url : './index.php/Plugin_view/testcase/get_project_code_tc_result',
				params :{
					pr_seq : project_seq
				},
				method: 'POST',
				success: function ( result, request ) {
					if(result.responseText){
						var code = Ext.decode(result.responseText);					

						var checkbox = [];
						for(var i=0; i<code.data.length; i++){
							checkbox.push({
								boxLabel: code.data[i].pco_name,
								inputValue: code.data[i].pco_seq,
								name : 'testcase_east_result_value',
								checked:(code.data[i].pco_is_default=='Y')?true:false
							});
						}
						var Records = Ext.getCmp('testcase_treegrid').getSelectionModel().selected.items;

						var form_disable = false;
						if(Records[0].data.deadline_date != null && Records[0].data.assign_to != null){
							var deadline_date = Records[0].data.deadline_date.substr(0,10);

							if("<?=date('Y-m-d')?>" > deadline_date || Records[0].data.assign_to != login_user_email){
								form_disable = true;
							}
						}

						if(mb_is_admin == 'Y'){
							form_disable = false;
						}

						var resultPanel = new Ext.FormPanel({
							region: 'south', xtype: 'form',
							title: Otm.tc_execution, id:'testcase_east_execute_panel',
							animate: false, autoScroll: false, collapsible: false, collapsed: false, animCollapse: false, disabled:form_disable,
							height: 250, frame:true, bodyStyle:'padding:5px;',
							items:[{
									xtype: 'radiogroup',
									fieldLabel: Otm.tc_execution_result,
									id : 'testcase_east_result',
									anchor:'100%',
									columns: 2,
									allowBlank: false,
									items: checkbox
								},{
									layout:'fit',
									xtype:'textarea',
									id:'testcase_east_result_content',
									anchor:'100%',
									height: 120
								}
							],
							buttons: [{
								text   : Otm.com_save+' ('+Otm.tc_execution+')',
								iconCls:'ico-save',
								action_type : 'tc_result',
								handler: function() {

									var content = Ext.getCmp('testcase_east_result_content').getValue();
									if(!content || content == ''){
										Ext.Msg.confirm('OTM',Otm.com_msg_execution_description_save,function(bt){
											if(bt=='yes'){
												var select_result = Ext.getCmp('testcase_east_result').getChecked();
												var tc_id = obj.id;
												var tl_id = obj.tl_seq;
												var params = {
													id			: obj.id,
													tl_seq		: obj.tl_seq,
													result_value: select_result[0].inputValue,
													result_text : select_result[0].boxLabel,
													content		: Ext.getCmp('testcase_east_result_content').getValue()
												};

												if(resultPanel.getForm().isValid()){
													resultPanel.getForm().submit({
														url : './index.php/Plugin_view/testcase/create_execute_result',
														params: params,
														waitMsg: Otm.com_msg_processing_data,
														success: function(form, action){

															var obj2 = Ext.decode(action.response.responseText);
															var node = Ext.decode(obj2.data);

															node.cmd = 'update';
															node.target_grid = "testcase_treegrid";

															NodeReload(node);
															Ext.getCmp('testcase_east_execute_panel').reset();

															get_testcase_view_form(obj);
															return;
														},
														failure: function (result, request) {
															Ext.Msg.alert('Error',request.result.msg);
														}
													});
												}
											}else{
												return;
											}
										});
									}else{

										var select_result = Ext.getCmp('testcase_east_result').getChecked();
										var tc_id = obj.id;
										var tl_id = obj.tl_seq;
										var params = {
											id			: obj.id,
											tl_seq		: obj.tl_seq,
											result_value: select_result[0].inputValue,
											result_text : select_result[0].boxLabel,
											content		: Ext.getCmp('testcase_east_result_content').getValue()
										};

										if(resultPanel.getForm().isValid()){
											resultPanel.getForm().submit({
												url : './index.php/Plugin_view/testcase/create_execute_result',
												params: params,
												waitMsg: Otm.com_msg_processing_data,
												success: function(form, action){

													var obj2 = Ext.decode(action.response.responseText);
													var node = Ext.decode(obj2.data);


													node.cmd = 'update';
													node.target_grid = "testcase_treegrid";

													NodeReload(node);
													Ext.getCmp('testcase_east_execute_panel').reset();

													get_testcase_view_form(obj);
													return;
												},
												failure: function (result, request) {
													Ext.Msg.alert('Error',request.result.msg);
												}
											});
										}
									}
								}
							}]
						});
						Ext.getCmp('testcase_east_panel').add(
							resultPanel
						);
					}
				},
				failure: function ( result, request ) {
					Ext.getCmp('dashboard').unmask();
					Ext.Msg.alert('OTM','DataBase Select Error');
				}
			});
		}

		return;
	}

	function get_testcase_assign_form(obj)
	{
		Ext.getCmp('testcase_east_panel').removeAll();

		var testcase_east_panel = Ext.getCmp('testcase_east_panel');
		if(testcase_east_panel.collapsed==false){
		}else{
			testcase_east_panel.expand();
		}

		var items = [];
		var subject = {
			xtype: 'textfield', anchor: '100%', allowBlank : false,
			fieldLabel: Otm.com_subject+'(*)', id: 'testcase_assign_subject'
			};
		var description = {
			xtype: 'textarea', anchor: '100%',
			fieldLabel: Otm.com_description, id: 'testcase_assign_description',
			height:'100'
			};
		var deadline_date = Ext.create('Ext.form.DateField', {
			anchor: '100%', format: 'Y-m-d', editable: false, allowBlank : false,
			minValue: new Date(),
			fieldLabel: Otm.tc_deadline, id: 'testcase_assign_deadline_date'
		});

		var project_user_store = Ext.create('Ext.data.Store', {
			fields:['rp_seq','mb_seq','mb_email','mb_name','mb_pw','mb_tel','mb_is_admin','mb_is_approved','writer','regdate','last_writer','last_update','user_group_name','user_role_name'],
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

		var testcase_project_user_grid = Ext.create("Ext.grid.Panel",{
			region:'center',
			layout: 'fit',
			title : Otm.pjt+' '+Otm.com_memberlist,
			id:'testcase_project_user_grid',
			store: project_user_store,
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
				{header: Otm.com_name,			dataIndex: 'mb_name',	flex: 1},
				{header: Otm.com_group,			dataIndex: 'user_group_name',	width:100},
				{header: Otm.com_role,			dataIndex: 'user_role_name',	width:100},
				{header: Otm.com_email,			dataIndex: 'mb_email',		width:100}
			],
			tbar:[deadline_date,'->',
				{
				xtype:'button',
				text: Otm.tc_assign_persion +' '+ Otm.com_close,
				iconCls: 'ico-cancel',
				handler:function(btn){
					Ext.getCmp('testcase_east_panel').removeAll();
					Ext.getCmp('testcase_east_panel').collapse();
				}
			}],
			buttons:[{
				text:Otm.com_save,
				iconCls:'ico-save',
				handler:function(btn){
						var deadline_date =  Ext.getCmp('testcase_assign_deadline_date').getValue();

						if(!deadline_date || deadline_date == ''){
							Ext.Msg.alert('OTM',Otm.com_msg_deadline);
							return;
						}

						if(get_select_testcase().text=='Root'){
							Ext.Msg.alert('OTM',Otm.com_msg_select_items_assign);
							return;
						}

						var params = {
							tp_seq			: get_select_testcase_plan().tp_seq,
							deadline_date	: deadline_date
						};

						var Records = Ext.getCmp('testcase_treegrid').getSelectionModel().selected.items;
						var tl_seq_list = Array();

						if(Records.length >= 1){
							for(var i=0; i<Records.length; i++){
									tl_seq_list.push(Records[i].data['tl_seq']);
							}
							params.tl_seq_list = Ext.encode(tl_seq_list);
						}else{
							Ext.Msg.alert('OTM',Otm.com_msg_select_items_assign);
							return;
						}

						var selItem = Ext.getCmp('testcase_project_user_grid').getSelectionModel().selected.items[0];
						if(selItem){
							params.assign_to = selItem.data.mb_email;
						}else{
							Ext.Msg.alert('OTM',Otm.com_msg_responsible_person);
							return;
						}

						mask.start();
						Ext.Ajax.request({
							url : './index.php/Plugin_view/testcase/assign_testcase',
							params : params,
							method: 'POST',
							success: function ( result, request ) {
								var obj = Ext.decode(result.responseText);
								var node = Ext.decode(obj.data);

								node.cmd = 'update';
								node.target_grid = "testcase_treegrid";
								NodeReload(node);

								mask.stop();
								Ext.getCmp('testcase_east_panel').removeAll();

								Ext.getCmp('testcase_east_panel').add(get_testcase_assign_form());
								return;
							},
							failure: function ( result, request ){
								mask.stop();
							}
						});
				}
			},{
				text:Otm.com_reset,
				iconCls:'ico-reset',
				hidden:true,
				handler:function(btn){
				}
			}]
		});

		return testcase_project_user_grid;
	}

	function testcase_target_treegrid(obj)
	{
		Ext.getCmp('testcase_east_panel').removeAll();

		var testcase_east_panel = Ext.getCmp('testcase_east_panel');
		if(testcase_east_panel.collapsed==false){
		}else{
			testcase_east_panel.expand();
		}

		var testcase_plan_combo_store = Ext.create('Ext.data.Store', {
			fields:['pr_seq','tp_seq','text'],
			proxy: {
				type	: 'ajax',
				url		: './index.php/Plugin_view/testcase/plan_list',
				extraParams: {
					pr_seq : project_seq
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

		var testcase_plan_combo = Ext.create('Ext.form.ComboBox', {
			anchor: '100%',
			fieldLabel: Otm.tc_plan,
			id: 'testcase_plan_combo',
			triggerAction	: 'all',
			forceSelection	: true,
			editable		: false,
			displayField	: 'text',
			valueField		: 'tp_seq',
			allowBlank		: false,
			queryMode		: 'local',
			store			: testcase_plan_combo_store,
			listeners: {
				select: function(combo, record, index) {
					var params = {
						params:{
							project_seq : project_seq,
							tcplan		: 'tcplan_'+combo.getValue()
						}
					};
					Ext.getCmp('testcase_target_treegrid').getStore().proxy.extraParams.tcplan = 'tcplan_'+combo.getValue();
					Ext.getCmp('testcase_target_treegrid').getStore().load(params);
				}
			}
		});


		/**
		* TreeGrid
		*/
		var testcase_tree_store = Ext.create('Ext.data.TreeStore', {
			root: {
				text: 'Root',
				expanded: true,
				children: []
			},
			proxy: {
				type: 'ajax',
				url:'./index.php/Plugin_view/testcase/testcase_tree_list',
				extraParams: {
					project_seq : project_seq,
					tcplan		: ''
				},
				reader: {
					type: 'json',
					totalProperty: 'totalCount',
					rootProperty: 'data'
				}
			},
			folderSort: true,
			autoLoad:false
		});

		var testcase_target_treegrid = Ext.create('Ext.tree.Panel', {
			region	: 'center',
			id: 'testcase_target_treegrid',
			width: 500,
			height: 300,
			useArrows: true,
			rootVisible: true,
			store: testcase_tree_store,
			multiSelect: true,
			singleExpand: false,
			listeners:{
				itemclick : function(view,rec,item,index,eventObj) {
				}
			},
			columns: [{
				xtype: 'treecolumn',
				text: Otm.com_subject, dataIndex: 'text',
				minWidth:150, flex: 1, sortable: true
			},{
				text: Otm.com_id, dataIndex: 'out_id',
				width:100, sortable: true, align:'center'
			},{
				text: Otm.com_creator, dataIndex: 'writer_name',
				width:100, sortable: true, align:'center'
			},{
				text: Otm.com_date, dataIndex: 'regdate', align:'center',
				width:120, sortable: true, renderer:function(value,index,record){
					if(value){
						var value = value.substr(0,10);
					}else{
						value = '';
					}
					return value;
				}
			}],
			tbar: [testcase_plan_combo,'->',
				{
				xtype:'button',
				text: Otm.tc_plan_copy +' '+ Otm.com_close,
				iconCls: 'ico-cancel',
				handler:function(btn){
					Ext.getCmp('testcase_east_panel').removeAll();
					Ext.getCmp('testcase_east_panel').collapse();
				}
			}],
			lbar: [{ xtype: 'component', flex: 0.5 },
				{
					xtype: 'button',
					iconCls: 'arrow_right',
					tooltip: Otm.com_msg_plan_in_testcase,
					handler:function(btn){
						var tp_seq =  Ext.getCmp('testcase_plan_combo').getValue();
						if(tp_seq == '' || tp_seq <= 0){
							Ext.Msg.alert('OTM',Otm.com_msg_select_plan);
							return;
						}

						var params = {
							tp_seq: tp_seq
						};
						var tc_seq_list = Array();
						var tc_id_list = Array();

						var Records = Ext.getCmp('testcase_treegrid').getSelectionModel().selected.items;
						if(Records.length >= 1){
							for(var i=0; i<Records.length; i++){
								tc_seq_list.push(Records[i].data['tc_seq']);
								tc_id_list.push(Records[i].data['id']);
							}
							params.tc_seq_list = Ext.encode(tc_seq_list);
							params.tc_id_list = Ext.encode(tc_id_list);
						}else{
							Ext.Msg.alert('OTM',Otm.com_msg_copy_selecttc);
							return;
						}

						var selItem = Ext.getCmp('testcase_target_treegrid').getSelectionModel().selected.items[0];
						if(selItem && selItem.id != 'root'){
							params.pid = selItem.data.id;
						}else{
							params.pid = 'tc_0';
						}

						mask.start();
						Ext.Ajax.request({
							url : './index.php/Plugin_view/testcase/create_testcase_link',
							params : params,
							method: 'POST',
							success: function ( result, request ) {
								if(result.responseText){

									Ext.Ajax.request({
										url : './index.php/Plugin_view/testcase/testcase_tree_list',
										params : {
											project_seq :  <?=$project_seq?>,
											tcplan : 'tcplan_'+tp_seq,
											node:params.pid
										},
										method: 'POST',
										success: function ( result, request ) {

											var obj = Ext.decode(result.responseText);

											for(var i=0;i<obj.length;i++){
												var node = obj[i];
												node.cmd = 'add';
												node.target_grid = 'testcase_target_treegrid';
												NodeReload(node);
											}
										}
									});

									mask.stop();
								}
							},
							failure: function ( result, request ){
								mask.stop();
							}
						});
					}
				},
				{ xtype: 'component', flex: 0.5 }
			],
			bbar:tree_OpenClose_Btn('testcase_target_treegrid')
		});

		return testcase_target_treegrid;
	}

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
		autoLoad:true
	});

	function testcase_defect_view(df_seq)
	{
		Ext.Ajax.request({
			url : "./index.php/Plugin_view/defect/view_defect",
			params :{
				df_seq : df_seq,
				pr_seq : project_seq
			},
			method: 'POST',
			success: function ( result, request ) {

				if(result.responseText){
					var defect_info = _getCustomform_view(defect_customform_store,result.responseText);

					defect_info.data.df_description = defect_info.data.df_description.replace(/\n/gi,'<br/>').replace(/\\n/gi,'<br/>');

					if(!defect_info.data.dc_to){
						defect_info.data.dc_to = "";
					}

					defect_info.data.dc_start_date = defect_info.data.dc_start_date.substr(0,10);
					defect_info.data.dc_end_date = defect_info.data.dc_end_date.substr(0,10);

					if(defect_info.data.dc_start_date == '0000-00-00')
						defect_info.data.dc_start_date = '';
					if(defect_info.data.dc_end_date == '0000-00-00')
						defect_info.data.dc_end_date = '';

					var printFile = _common_fileView('defect_defectGrid',Ext.decode(defect_info.data.fileform));

					defect_info.data.fileform = printFile;

					defect_info.data.defect_history = _history_view(Ext.decode(defect_info.data.defect_history));

					var doc_width = document.body.clientWidth - 100;
					var doc_height = document.body.clientHeight - 50;

					Ext.create('Ext.window.Window', {
						title: Otm.def+' '+Otm.com_view,
						id:'testcase_defect_view',
						height: doc_height,
						width: doc_width,
						constrain: true,
						minWidth:500,
						layout: 'border',
						collapsible: false,
						bodyStyle: 'background:white;padding: 10px;',
						autoScroll: true,
						modal : true,
						constrainHeader: true,
						items: [],
						buttons:[{
							text:Otm.com_close,
							handler:function(btn){
								Ext.getCmp("testcase_defect_view").close();
							}
						}]
					}).show('',function(){
						defect_defectTpl.overwrite(Ext.getCmp("testcase_defect_view").body, defect_info.data);
					});
				}
			},
			failure: function ( result, request ) {
				Ext.Msg.alert("OTM","DataBase Select Error");
			}
		});
	}

	function testcase_defect_write(data)
	{
		var testcase_defect_seqForm = {
			id: 'defect_seqForm',
			name: 'defect_seqForm',
			anchor: '100%',
			allowBlank : true,
			xtype: 'hiddenfield'
		};
		var testcase_defect_subjectForm = {
			id: 'defect_subjectForm',
			name:'defect_subjectForm',
			anchor: '0',
			fieldLabel: Otm.com_subject+'(*)',
			allowBlank : false,
			minLength:2,
			maxLength:100,
			xtype: 'textfield'
		};

		var testcase_defect_descriptionForm = {
			id: 'defect_descriptionForm',
			name:'defect_descriptionForm',
			anchor: '0',
			fieldLabel: Otm.com_description+'(*)',
			allowBlank : false,
			grow : true,
			growMax: 400,
			growMin: 100,
			xtype: 'textarea'
		}

		var testcase_defect_severityForm = Ext.create('Ext.form.ComboBox', {
			id:'defect_severityForm',
			name:'defect_severityForm',
			editable: false,
			fieldLabel: Otm.def_severity,
			displayField: 'pco_name',
			valueField:'pco_seq',
			store: defect_severity_store,
			minChars: 0,
			allowBlank : true,
			queryParam: 'q',
			queryMode: 'local'
		});
		var testcase_defect_priorityForm = Ext.create('Ext.form.ComboBox', {
			id:'defect_priorityForm',name:'defect_priorityForm',
			editable: false,
			fieldLabel: Otm.def_priority,
			displayField: 'pco_name',
			valueField:'pco_seq',
			store: defect_priority_store,
			minChars: 0,
			allowBlank : true,
			queryParam: 'q',
			queryMode: 'local'
		});
		var testcase_defect_frequencyForm = Ext.create('Ext.form.ComboBox', {
			id:'defect_frequencyForm',name:'defect_frequencyForm',
			editable: false,
			fieldLabel: Otm.def_frequency,
			displayField: 'pco_name',
			valueField:'pco_seq',
			store: defect_frequency_store,
			minChars: 0,
			allowBlank : true,
			queryParam: 'q',
			queryMode: 'local'
		});

		var testcase_defect_statusForm = Ext.create('Ext.form.ComboBox', {
			id:'defect_statusForm',name:'defect_statusForm',
			editable: false,
			fieldLabel: Otm.def_status,
			displayField: 'pco_name',
			valueField:'pco_seq',
			store: defect_status_store,
			minChars: 0,
			allowBlank : true,
			queryParam: 'q',
			queryMode: 'local'
		});

		var assign_role = false;
		if((member_role_store && member_role_store['defect_assign']) || mb_is_admin == 'Y'){
		}else{
			assign_role = true;
		};

		var testcase_defect_contactForm = {
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
		}
		var testcase_defect_dateForm = {
			layout: 'hbox',
			xtype: 'fieldcontainer',
			fieldLabel: Otm.com_start_date,
			combineErrors: false,
			items: [{
				xtype: 'datefield',
				id:'defect_start_date',name:'defect_start_date',
				width: 90,
				format:"Y-m-d",editable: false,
				allowBlank: true
			},{
				xtype: 'displayfield',
				style:'padding-left:30px;',
				value: Otm.com_end_date
			},{
				xtype: 'datefield',
				id:'defect_end_date',name:'defect_end_date',
				bodyStyle:'padding-left:30px;',
				format:"Y-m-d",editable: false,
				width: 90,
				allowBlank: true
			}]
		}
		var testcase_defect_customForm = {
			xtype:'panel',
			id:'testcase_defect_customForm',
			border:false,
			width:'100%'
		}
		var testcase_defect_fileForm = {
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

		var testcase_defect_writeForm = Ext.create("Ext.form.Panel",{
			id : 'testcase_defect_writeForm',
			region:'center',
			collapsible: false,border:false,
			bodyStyle: 'padding: 10px;',
			autoScroll: true,
			labelWidth:'10',
			items: [testcase_defect_seqForm,testcase_defect_subjectForm,testcase_defect_descriptionForm,testcase_defect_severityForm,testcase_defect_priorityForm,testcase_defect_frequencyForm,testcase_defect_statusForm,testcase_defect_contactForm,testcase_defect_dateForm,testcase_defect_customForm,testcase_defect_fileForm],
			buttons:[{
				text:Otm.com_save,
				iconCls:'ico-save',
				disabled: true,
				formBind: true,
				handler:function(btn){
					testcase_defect_save(data);
				}
			},{
				text:Otm.com_reset,
				iconCls:'ico-reset',
				hidden:true,
				handler:function(btn){
					testcase_defect_writeForm.reset();
					Ext.getCmp("defect_statusForm").setValue(status_value);
					Ext.getCmp("defect_severityForm").setValue(severity_value);
					Ext.getCmp("defect_priorityForm").setValue(priority_value);
					Ext.getCmp("defect_frequencyForm").setValue(frequency_value);
				}
			}]
		});

		var doc_width = document.body.clientWidth - 100;
		var doc_height = document.body.clientHeight - 50;

		var testcase_defect_writer_window = Ext.create('Ext.window.Window', {
			title: Otm.def+' '+Otm.com_add,
			id: 'testcase_defect_write_window',
			height: doc_height,
			constrain: true,
			width: doc_width,
			layout: 'border',
			modal : true,
			constrainHeader: true,
			items: [testcase_defect_writeForm]
		});

		defect_customform_store.load({
			callback: function(r,options,success){
				Ext.getCmp("testcase_defect_customForm").add(_setCustomform('ID_DEFECT',r));
				testcase_defect_writer_window.show();
				Ext.getCmp("defect_statusForm").setValue(status_value);
				Ext.getCmp("defect_severityForm").setValue(severity_value);
				Ext.getCmp("defect_priorityForm").setValue(priority_value);
				Ext.getCmp("defect_frequencyForm").setValue(frequency_value);
				Ext.getCmp('defect_subjectForm').focus();
			}
		});
	}

	function testcase_defect_save(tr_seq)
	{
		var tr_seq = tr_seq.split('_');

		var URL = "./index.php/Plugin_view/defect/create_defect";

		var defect_user_customform_result = new Array();
		var commit_info = Ext.getCmp("testcase_defect_writeForm").getForm().getValues();
		for(var i=0;i<customform_seq.length;i++){
			defect_user_customform_result.push({
				name	: customform_seq[i].name,
				seq		: customform_seq[i].seq,
				type	: customform_seq[i].type,
				value	: eval("commit_info.custom_"+customform_seq[i].seq)
			});
		}

		Ext.getCmp("testcase_defect_write_window").mask(Otm.com_msg_processing_data);

		var params = {
					project_seq	: project_seq,
					tr_seq		: tr_seq[1],
					custom_form : Ext.encode(defect_user_customform_result)
				};

		if(Ext.getCmp("testcase_defect_writeForm").getForm().isValid()){
			Ext.getCmp("testcase_defect_writeForm").getForm().submit({
				url: URL,
				method:'POST',
				params: params,
				waitMsg: Otm.com_msg_processing_data,
				success: function(rsp, o){
					Ext.getCmp("testcase_defect_write_window").unmask();

					var info = Ext.decode(o.response.responseText);
					if(info.data.msg && info.data.msg == 'over_num'){
						Ext.Msg.alert('OTM',Otm.id_rule.over_id_number_msg);
						return;
					}

					Ext.getCmp("testcase_defect_write_window").close();

					var select_tc = get_select_testcase().id;
					var store = Ext.getCmp('testcase_treegrid').getStore();
					for(var i=0;i<store.data.length;i++){
						if(store.data.items[i].data.id == select_tc){
							Ext.getCmp('testcase_treegrid').getSelectionModel().deselectAll();
							Ext.getCmp("testcase_treegrid").getSelectionModel().select(i);
						}
					}
				},
				failure: function(rsp, result, r){
					Ext.getCmp("testcase_defect_write_window").unmask();
					var rep = Ext.decode(result.response.responseText);
					if(rep && rep.msg){
						Ext.Msg.alert('OTM',rep.msg);
					}
					return;
				}
			});
		}
	}

	/**
	* East Panel
	*/
	var testcase_east_panel = {
		region		: 'east',
		layout		: 'border',
		split		: true,
		collapsible	: true,
		collapsed	: true,
		flex		: 1,
		animation	: false,
		autoScroll	: true,
		minWidth	: 420,
		maxWidth	: 600,
		id			: 'testcase_east_panel',
		items		: []
	};

	Ext.onReady(function(){
		var main_panel = {
			layout		: 'border',
			defaults	: {
				collapsible	: false,
				split		: true,
				bodyStyle	: 'padding:0px'
			},
			items		: [testcase_center_panel,testcase_east_panel]
		};
		Ext.getCmp('testcase').add(main_panel);
		Ext.getCmp('testcase').doLayout(true,false);

		testcase_customform_store.load({
			callback: function(r,options,success){
				for(var i=0;i<r.length;i++){
					var add_chk = true;
					for(var key in tree_grid_columns){
						if(tree_grid_columns[key].dataIndex == "_"+r[i].data.pc_seq){
							add_chk = false;
							break;
						}
					}
					if(add_chk){
						if(r[i].data.pc_is_display == 'Y'){
							tree_grid_columns.push({
								 header: r[i].data.pc_name,  dataIndex: "_"+r[i].data.pc_seq, align:'center'
							});
						}
					}
				}
				Ext.getCmp('testcase_treegrid').reconfigure(undefined,tree_grid_columns);
			}
		});

		defect_contact_store.load();
		defect_code_store.load({
			callback: function(r,options,success){
				for(var i=0;i<r.length;i++){
					switch(r[i].data.pco_type){
						case "status":
							defect_status_store.add({
								pco_seq	: r[i].data.pco_seq,
								pco_name : r[i].data.pco_name
							});
							if(r[i].data.pco_is_default=="Y"){
								status_value = r[i].data.pco_seq;
							}
						break;
						case "severity":
							defect_severity_store.add({
								pco_seq	: r[i].data.pco_seq,
								pco_name : r[i].data.pco_name
							});
							if(r[i].data.pco_is_default=="Y"){
								severity_value = r[i].data.pco_seq;
							}
						break;
						case "priority":
							defect_priority_store.add({
								pco_seq	: r[i].data.pco_seq,
								pco_name : r[i].data.pco_name
							});
							if(r[i].data.pco_is_default=="Y"){
								priority_value = r[i].data.pco_seq;
							}
						break;
						case "frequency":
							defect_frequency_store.add({
								pco_seq	: r[i].data.pco_seq,
								pco_name : r[i].data.pco_name
							});
							if(r[i].data.pco_is_default=="Y"){
								frequency_value = r[i].data.pco_seq;
							}
						break;
					}
				}

				var detail_view_value = get_JSCookie('cookie_testcase_east_panel');
				if(!detail_view_value || detail_view_value=="open"){
					Ext.getCmp("cookie_testcase_east_panel").getComponent(0).setPressed(true);
				}else{
					Ext.getCmp("cookie_testcase_east_panel").getComponent(1).setPressed(true);
				}
			}
		});
	});
</script>