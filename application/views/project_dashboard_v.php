<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script type="text/javascript">

	var search_node_array = Array();
	var search_node_total = 0;

	function search_node(node,key,value,throw_key,throw_value)
	{
		//init
		search_node_array = Array();
		search_node_total = 0;

		try {
			search_node_process(node,key,value,throw_key,throw_value);
		} catch(e) {

		} finally {
			return Array(search_node_array, search_node_total);
		}
	}

	function search_node_process(node,key,value,throw_key,throw_value)
	{
		if(throw_key && throw_value && node.data[throw_key] && node.data[throw_key] == throw_value){

		}else{
			search_node_total++;

			if(node.data[key] == value){
				search_node_array.push(node);
			}else{

			}
		}

		for(var i=0; i<node.childNodes.length; i++){
			search_node_process(node.childNodes[i],key,value,throw_key,throw_value);
		}
	}

	function reload_project_tree(node_id)
	{
		if(node_id == 'root'){
			Ext.getCmp('project_treePanel').getStore().load();
			return;
		}

		var node = Ext.getCmp('project_treePanel').getStore().getRootNode().findChild('id', node_id, true);
		if(node){
			Ext.getCmp('project_treePanel').getStore().load({node:node});
		}
	}

	function project_group_Form(data)
	{
		var project_group_WriteForm = Ext.create("Ext.form.Panel",{
			region:'center',
			collapsible: false,border:false,
			bodyStyle: 'padding: 10px;',
			autoScroll: true,
			labelWidth:'10',
			items: [{
				id: 'project_group_seq',
				name: 'project_group_seq',
				anchor: '100%',
				allowBlank : false,
				xtype: 'hiddenfield',
				value : (data && data.seq)?data.seq:''
			},{
				id: 'project_group_name',
				name: 'project_group_name',
				anchor: '100%',
				minLength:2,
				maxLength:100,
				fieldLabel: Otm.group+' '+Otm.com_name+'(*)',
				allowBlank : false,
				xtype: 'textfield',
				value : (data && data.text)?data.text:'',
				listeners: {
					afterrender: function(fld) {
						fld.focus(false, 500);
					}
				}
			}],
			buttons:[{
				text:Otm.com_save,
				disabled: true,
				formBind: true,
				iconCls:'ico-save',
				handler:function(btn){
					var select_node = 'root';
					var selItem = Ext.getCmp("project_dashboard_treegrid").getSelectionModel().selected.items[0];
					if(selItem){
						select_node = selItem.data.id;
					}else{
						selItem = Ext.getCmp('project_dashboard_treegrid').getStore().getRootNode();
					}

					var url = './index.php/Otm/create_project_group';
					if(data && data.seq && data.seq > 0){
						url = './index.php/Otm/update_project_group';
					}

					Ext.Ajax.request({
						url : url,
						params : {
							node	: select_node,
							pg_name	: Ext.getCmp('project_group_name').getValue()
						},
						method: 'POST',
						success: function ( result, request ) {
							if(result.responseText){
								if(data && data.seq && data.seq > 0){
									var node_id = selItem.parentNode.data.id;
									reload_project_tree(node_id);
									Ext.getCmp("project_dashboard_treegrid").getStore().load({node:selItem.parentNode});

									selItem.data.text = Ext.getCmp('project_group_name').getValue();
									var tmp_selItem = selItem;
									Ext.getCmp("project_dashboard_treegrid").getSelectionModel().deselectAll();

									//var task = new Ext.util.DelayedTask(function(){
										Ext.getCmp("project_dashboard_treegrid").getSelectionModel().select(tmp_selItem);
									//});

									// Wait 500ms before calling our function. If the user presses another key
									// during that 500ms, it will be cancelled and we'll wait another 500ms.

									//task.delay(3000);
									return;
								}

								Ext.getCmp("project_dashboard_treegrid").getStore().load({node:selItem});
								project_group_WriteForm.reset();
								reload_project_tree(selItem.data.id);
							}
						},
						failure: function ( result, request ){
						}
					});

				}
			}]
		});

		return project_group_WriteForm;
	};

function project_copy_form_win(selItem)
{

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

	plugin_store.load({
		callback: function(r,options,success){
			var pr_seq = selItem.data.seq;

			var file_chk = false;
			for(var i=0;i<r.length;i++){
				if(r[i].raw.service_id == 'storage' && r[i].raw.ishidden != 'true'){
					file_chk = true;
				}
			}

			if(selItem){
				temp_text = '['+Otm.copy+'] '+selItem.data.text;
				temp_description = selItem.data.pr_description.replace(/<br\s*[\/]?>/gi, "\n");
				//selItem.data.pr_description = selItem.data.pr_description.replace(/<br\s*[\/]?>/gi, "\n");

				if(selItem.data.pr_startdate == "0000-00-00 00:00:00"){
					selItem.data.pr_startdate = "";
				}
				if(selItem.data.pr_startdate){
					selItem.data.pr_startdate = selItem.data.pr_startdate.substr(0,10);
				}
				if(selItem.data.pr_enddate == "0000-00-00 00:00:00"){
					selItem.data.pr_enddate = "";
				}
				if(selItem.data.pr_enddate){
					selItem.data.pr_enddate = selItem.data.pr_enddate.substr(0,10);
				}
			}

			if(typeof Ext.getCmp('project_copy_window') == "undefined"){
				var copy_projectForm = Ext.create("Ext.form.Panel",{
					id:'copy_projectForm',
					border:false,
					style:'padding:10px;',
					anchor:'100%',
					items:[{
						xtype:'fieldset',
						title:Otm.pjt_info,
						width:'100%',anchor:'100%',flex:1,
						items:[{
							id: 'select_project_seq',
							allowBlank : false,	xtype: 'hiddenfield',
							value : pr_seq,
						},{
							id: 'copy_project_name',
							anchor: '100%',	minLength:2,maxLength:100,
							fieldLabel: Otm.pjt_name+'(*)',
							allowBlank : false,	xtype: 'textfield',
							value : temp_text,//(data && data.text)?data.text:'',
							listeners: {
								afterrender: function(fld) {
									fld.focus(false, 500);
								}
							}
						},{
							id: 'copy_project_startdate',
							fieldLabel: Otm.com_start_date+'(*)',
							format:'Y-m-d',editable: false,
							allowBlank : false,
							vtype: 'daterange',	xtype: 'datefield',
							value : selItem.data.pr_startdate
						},{
							id: 'copy_project_enddate',
							fieldLabel: Otm.com_end_date,
							format:"Y-m-d",editable: false,
							allowBlank : true,
							vtype: 'daterange',
							xtype: 'datefield',
							value : selItem.data.pr_enddate//(data && data.pr_enddate)?data.pr_enddate:''
						},{
							id: 'copy_project_description',
							anchor: '100%',
							fieldLabel: Otm.com_description,
							allowBlank : true,
							height:100,
							xtype: 'textarea',
							value : temp_description
						}]
					}]
				});

				var project_group_treegrid_store = Ext.create('Ext.data.TreeStore', {
					proxy: {
						type: 'ajax',
						url:'./index.php/Otm/project_dashboard_group_list',
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
					autoLoad:true
				});

				var project_group_treegrid = Ext.create('Ext.tree.Panel', {
					id			: 'project_group_treegrid',
					animate		: false,
					enableDD	: true,
					rootVisible	: true,
					store		: project_group_treegrid_store,//project_dashboard_tree_store
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
						xtype: 'treecolumn',
						text: Otm.pjt+' '+Otm.com_group, dataIndex: 'text',
						minWidth:150, flex: 1,
						renderer:function(value,index,record){
							if(record.data.type == "group"){
								return record.data.text;
							}
						}
					}]
				});

				var copy_item_checkbox = {
					xtype: 'container',
					layout: 'hbox',
					margin: '0 0 10',
					style:'padding:10px;',
					items:[{
						xtype: 'fieldset',
						flex: 1,
						title: 'Copy Items',
						defaultType: 'checkbox',
						layout: 'anchor',
						id:'copy_item_checkbox',
						defaults: {
							anchor: '100%',
							hideEmptyLabel: true
						},
						items: [{
							checked: true,
							boxLabel: Otm.tc + ' ('+Otm.com_msg_project_copy_not_item+')',
							name: 'copy_tc_chk'
						}, {
							checked: true,
							boxLabel: Otm.def,
							name: 'copy_def_chk'
						},{
							checked: true,
							hidden : (file_chk==false)?true:false,//true
							boxLabel: Otm.file_doc,
							name: 'copy_filedoc_chk'//보이게 할지 말지를 판단해야 한다.
						},{
							checked: true,
							disabled:true,
							boxLabel: Otm.pjt+' '+Otm.pjt_member,
							name: 'copy_pjtmem_chk'
						}, {
							checked: true,
							disabled:true,
							boxLabel: Otm.id_rule.project_id_rule,
							name: 'copy_pjtidrule_chk'
						}, {
							checked: true,disabled:true,
							boxLabel: Otm.pjt+' '+Otm.com_code,
							name: 'copy_pjtcode_chk'
						},{
							checked: true,disabled:true,
							boxLabel: Otm.pjt+' '+Otm.com_user_defined_form,
							name: 'copy_pjtuserform_chk'
						}, {
							checked: true,disabled:true,
							boxLabel: Otm.com_def_lifecycle,
							name: 'copy_pjtlifecycle_chk'
						}]
					}]
				}


				var project_copy_window = Ext.create('Ext.window.Window', {
					title: Otm.pjt+' '+Otm.copy,
					id	: 'project_copy_window',
					height: document.body.clientHeight-100,
					width: document.body.clientWidth-100,
					layout: 'border',
					defaults	: {
						collapsible	: false,
						split		: true,
						bodyStyle	: 'padding:0px'
					},
					resizable : true,
					modal : true,
					constrainHeader: true,
					closeAction: 'hide',
					items: [{
						region		: 'north',
						minHeight	: 100,autoScroll: true,
						maxHeight	: 230,
						height:230,
						collapsed	: false,
						layout		: 'fit',
						collapsible	: false,
						items		: [copy_projectForm]
					},{
						region		: 'center',
						layout		: 'fit',
						collapsible	: false,
						items		: [project_group_treegrid]
					},{
						region		: 'east',
						layout		: 'fit',
						id			: 'project_copy_window_east',
						collapsible	: false,
						minWidth	: 100,
						maxWidth	: (document.body.clientWidth-100)/2,
						width		: (document.body.clientWidth-100)/2,
						items		: [copy_item_checkbox]
					}],
					buttons:[{
						text:Otm.com_save,
						handler:function(btn){
							var URL = './index.php/Otm/copy_project';
							var select_group_seq = "";

							if(Ext.getCmp("project_group_treegrid").getSelectionModel().selected.length > 0){
								select_group_seq = Ext.getCmp("project_group_treegrid").getSelectionModel().selected.items[0].data.seq;
							}else{
								Ext.Msg.alert("OTM",Otm.com_msg_NotSelectGroup);
								return;
							}

							var params = {
								target_pr_seq		: Ext.getCmp("select_project_seq").getValue(),
								select_group_seq	: select_group_seq,
								copy_subject		: Ext.getCmp("copy_project_name").getValue(),
								copy_startdate		: Ext.getCmp('copy_project_startdate').getValue(),
								copy_enddate		: Ext.getCmp('copy_project_enddate').getValue(),
								copy_description	: Ext.getCmp('copy_project_description').getValue()
							}

							params['select_group_seq'] = select_group_seq;
							params['copy_subject'] = Ext.getCmp("copy_project_name").getValue();
							params['copy_startdate'] = Ext.getCmp('copy_project_startdate').getValue();
							params['copy_enddate'] = Ext.getCmp('copy_project_enddate').getValue();
							params['copy_description'] = Ext.getCmp('copy_project_description').getValue();

							//console.log(params);
							//console.log(selItem);
							//return;
							for(var i=0;i<Ext.getCmp("copy_item_checkbox").items.length;i++){
								params[Ext.getCmp("copy_item_checkbox").items.items[i].name] = Ext.getCmp("copy_item_checkbox").items.items[i].checked;
							}


							Ext.Ajax.request({
								url : URL,
								params :params,
								method: 'POST',
								success: function ( result, request ) {
									if(result.responseText){
										if(result.responseText){
											Ext.Msg.alert("OTM",Otm.com_saved);
											project_copy_window.close();

											//var parentNode = Ext.getCmp('project_dashboard_treegrid').getSelectionModel().getSelection()[0].parentNode;
											//var rootNode = Ext.getCmp('project_dashboard_treegrid').getStore().getRootNode();
											if (selItem){
												Ext.getCmp("project_dashboard_treegrid").getSelectionModel().deselectAll();
												Ext.getCmp('project_treePanel').getStore().load();
												Ext.getCmp("project_dashboard_treegrid").getStore().load();
											}
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
							project_copy_window.close();
						}
					}]
				});
			}else{
				Ext.getCmp("select_project_seq").setValue(pr_seq);
				Ext.getCmp("copy_project_name").setValue(temp_text);
				Ext.getCmp("copy_project_startdate").setValue(selItem.data.pr_startdate);
				Ext.getCmp("copy_project_enddate").setValue(selItem.data.pr_enddate);
				Ext.getCmp("copy_project_description").setValue(temp_description);

				Ext.getCmp('project_group_treegrid').getStore().reload();
				for(var i=0;i<Ext.getCmp("copy_item_checkbox").items.length;i++){
					Ext.getCmp("copy_item_checkbox").items.items[i].reset();
				}

			}
			Ext.getCmp("project_group_treegrid").getSelectionModel().select(0);
			Ext.getCmp('project_copy_window').show();
		}
	});





}

	function project_Form(data)
	{
		if(data){
			if(data.pr_startdate == "0000-00-00 00:00:00"){
				data.pr_startdate = "";
			}
			if(data.pr_startdate){
				data.pr_startdate = data.pr_startdate.substr(0,10);
			}
			if(data.pr_enddate == "0000-00-00 00:00:00"){
				data.pr_enddate = "";
			}
			if(data.pr_enddate){
				data.pr_enddate = data.pr_enddate.substr(0,10);
			}
		}

		var project_seq = {
			id: 'project_seq',
			anchor: '100%',
			allowBlank : false,
			xtype: 'hiddenfield',
			value : (data && data.seq)?data.seq:''
		};
		var subjectForm = {
			id: 'project_name',
			anchor: '100%',
			minLength:2,
			maxLength:100,
			fieldLabel: Otm.pjt_name+'(*)',
			allowBlank : false,
			xtype: 'textfield',
			value : (data && data.text)?data.text:'',
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
			xtype: 'datefield',
			value : (data && data.pr_startdate)?data.pr_startdate:''
		};
		var endDateForm = {
			id: 'project_enddate',
			fieldLabel: Otm.com_end_date,
			format:"Y-m-d",editable: false,
			allowBlank : true,
			startDateField: 'project_startdate',
			vtype: 'daterange',
			xtype: 'datefield',
			value : (data && data.pr_enddate)?data.pr_enddate:''
		};
		var descriptionForm = {
			id: 'project_description',
			anchor: '100%',
			fieldLabel: Otm.com_description,
			allowBlank : true,
			height:'100',
			xtype: 'textarea',
			value : (data && data.pr_description)?data.pr_description.replace(/<br\s*[\/]?>/gi, "\n"):''
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
				iconCls:'ico-save',
				handler:function(btn){
					var pg_seq = 0;
					var selItem = Ext.getCmp("project_dashboard_treegrid").getSelectionModel().selected.items[0];
					if(selItem){
						if(selItem.data.type == 'group'){
							pg_seq = selItem.data.seq;
						}
					}else{
						selItem = Ext.getCmp('project_dashboard_treegrid').getStore().getRootNode();
					}

					var URL = './index.php/Otm/create_project';
					if(Ext.getCmp('project_seq').getValue()){
						URL = './index.php/Otm/update_project';
					}

					var text = Ext.getCmp('project_name').getValue();
					var pr_startdate = Ext.getCmp('project_startdate').getValue();
					var pr_enddate = Ext.getCmp('project_enddate').getValue();

					Ext.Ajax.request({
						url : URL,
						params :{
							pg_seq					: pg_seq,
							project_seq				: Ext.getCmp('project_seq').getValue(),
							project_name			: Ext.getCmp('project_name').getValue(),
							project_startdate		: Ext.getCmp('project_startdate').getValue(),
							project_enddate			: Ext.getCmp('project_enddate').getValue(),
							project_description		: Ext.getCmp('project_description').getValue()
						},
						method: 'POST',
						success: function ( result, request ) {
							if(result.responseText){
								var info = Ext.decode(result.responseText);
								if(info.msg == "Duplicate"){
									Ext.Msg.alert("OTM",Otm.com_msg_duplicate_data);
									return;
								}else{

									if(Ext.getCmp('project_seq').getValue()){

										if (selItem){
											//console.log(selItem, selItem.parentNode);
											if(selItem.parentNode){
												var parentNode = selItem.parentNode;
											}else{
												var node = Ext.getCmp('project_dashboard_treegrid').getStore().getRootNode().findChild('id', selItem.data.id, true);
												var parentNode = node.parentNode;
											}

											var node_id = parentNode.data.id;
											reload_project_tree(node_id);
											Ext.getCmp("project_dashboard_treegrid").getStore().load({node:parentNode});

											selItem.data.text = Ext.getCmp('project_name').getValue();
											selItem.data.pr_startdate = Ext.util.Format.date(Ext.getCmp('project_startdate').getValue(),'Y-m-d');
											selItem.data.pr_enddate = Ext.util.Format.date(Ext.getCmp('project_enddate').getValue(),'Y-m-d');
											var tmp_selItem = selItem;
											Ext.getCmp("project_dashboard_treegrid").getSelectionModel().deselectAll();
											Ext.getCmp("project_dashboard_treegrid").getSelectionModel().select(selItem);

											return;

											selItem.set('text', text);
											//selItem.set('pr_startdate', pr_startdate);
											//selItem.set('pr_enddate', pr_enddate);

											//if(node_id == 'root'){
											//	Ext.getCmp("project_dashboard_treegrid").getStore().load();
											//}else{
											//	Ext.getCmp("project_dashboard_treegrid").getStore().load({node:node_id});
											//}
											//console.log(selItem, selItem.parentNode.data.id);
											Ext.getCmp("project_dashboard_treegrid").getSelectionModel().deselectAll();
											Ext.getCmp("project_dashboard_treegrid").getSelectionModel().select(selItem);
										}
									}else{
										if (selItem){
											Ext.getCmp("project_dashboard_treegrid").getStore().load({node:selItem});
											reload_project_tree(selItem.data.id);
										}
										//projectWriteForm.reset();
									}
									//projectWriteForm.reset();
								}
							}
						},
						failure: function ( result, request ) {
						}
					});
				}
			}]
		});

		return projectWriteForm;
	};

	var add_project_group_Btn = {
		xtype	: 'button',
		text	: Otm.group+' '+Otm.com_add,
		iconCls	: 'ico-add',
		handler	: function(btn){

			var selItem = Ext.getCmp("project_dashboard_treegrid").getSelectionModel().selected.items[0];
			if(selItem && selItem.data.type !== 'group'){
				Ext.Msg.alert('OTM',Otm.com_msg_NotSelectGroup);
				return;
			}

			var projectEastPanel = Ext.getCmp("project_dashboard_EastPanel");
			projectEastPanel.update("");
			projectEastPanel.removeAll();

			if(projectEastPanel.collapsed==false){
			}else{
				projectEastPanel.expand();
			}

			projectEastPanel.add(project_group_Form());
			return;
		}
	};

	var add_project_Btn = {
		xtype	: 'button',
		text	: Otm.pjt+' '+Otm.com_add,
		iconCls	: 'ico-add',
		handler	: function(btn){

			var selItem = Ext.getCmp("project_dashboard_treegrid").getSelectionModel().selected.items[0];
			if(selItem){
				if(selItem.data.type == 'group'){
				}else if(selItem.data.type == 'project'){
					return;
				}
			}

			var projectEastPanel = Ext.getCmp("project_dashboard_EastPanel");
			projectEastPanel.update("");
			projectEastPanel.removeAll();

			if(projectEastPanel.collapsed==false){
			}else{
				projectEastPanel.expand();
			}

			projectEastPanel.add(project_Form());
			return;
		}
	};

	var update_Btn = {
		xtype	: 'button',
		text	: Otm.com_update,
		iconCls	: 'ico-update',
		handler	: function(btn){
			var selItem = Ext.getCmp("project_dashboard_treegrid").getSelectionModel().selected.items[0];
			if(selItem){
				select_node = selItem.data.id;
			}else{
				return;
			}

			if(select_node == 'root'){
				return;
			}

			var form = {};

			if(selItem.data.type == 'group'){
				form = project_group_Form(selItem.data);
			}else if(selItem.data.type == 'project'){
				form = project_Form(selItem.data);
			}

			var projectEastPanel = Ext.getCmp("project_dashboard_EastPanel");
			projectEastPanel.update("");
			projectEastPanel.removeAll();

			if(projectEastPanel.collapsed==false){
			}else{
				projectEastPanel.expand();
			}

			projectEastPanel.add(form);
		}
	};

	var delete_Btn = {
		xtype	: 'button',
		text	: Otm.com_remove,
		iconCls	: 'ico-remove',
		handler	: function(btn){
			var project_dashboard_treegrid = Ext.getCmp("project_dashboard_treegrid");
			var selItem = project_dashboard_treegrid.getSelectionModel().selected.items[0];
			if(selItem){
				select_node = selItem.data.id;
			}else{
				return;
			}

			if(select_node == 'root'){
				return;
			}

			var parentNode = project_dashboard_treegrid.getSelectionModel().getSelection()[0].parentNode;

			if(selItem.data.type == 'group'){

				if(mb_is_admin == 'Y' || mb_email == selItem.data.writer){
				}else{
					var search = search_node(selItem,'writer',mb_email,'type','group')
					if(search[0].length != search[1]){
						Ext.Msg.alert("OTM",Otm.com_remove+Otm.com_msg_noRole+"<br>"+Otm.com_msg_youneed_auth);
						return;
					}
				}

				Ext.Msg.confirm('OTM',Otm.com_msg_DeleteGroup,function(bt){
					if(bt=='yes')
					{
						Ext.Ajax.request({
							url : './index.php/Otm/delete_project_group',
							params : {
								node	: select_node
							},
							method: 'POST',
							success: function ( result, request ) {
								if(result.responseText){
									project_dashboard_treegrid.getStore().load({node:parentNode});
									reload_project_tree(parentNode.data.id);
								}
							},
							failure: function ( result, request ){
							}
						});
					}else{
						return;
					}
				});

			}else{

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
								project_seq			: selItem.data.seq
							},
							method: 'POST',
							success: function ( result, request ) {
								if(result.responseText){
									project_dashboard_treegrid.getStore().load({node:parentNode});
									reload_project_tree(parentNode.data.id);
								}
							},
							failure: function ( result, request ) {
							}
						});

					}else{
						return;
					}
				});

			}

			var projectEastPanel = Ext.getCmp("project_dashboard_EastPanel");
			projectEastPanel.update("");
			projectEastPanel.removeAll();
			projectEastPanel.collapse();
		}
	};

	var copy_project_Btn = {
		xtype	: 'button',
		text	: Otm.pjt+' '+Otm.copy,
		iconCls	: 'ico-copy',
		handler	: function(btn){
			//project_dashboard_tree_store,project_dashboard_treegrid
			var selItem = Ext.getCmp("project_dashboard_treegrid").getSelectionModel().selected.items[0];
			if(selItem && selItem.data.seq && selItem.data.type=="project"){
				project_copy_form_win(selItem);
				/*if(Ext.getCmp('project_copy_window')){
					Ext.getCmp('project_copy_window').removeAll();
				}else{
					project_copy_form_win(selItem);
				}*/
			}else{
				Ext.Msg.alert("OTM",Otm.com_msg_notselect_project_copy);
			}
		}
	};

	var export_project_Btn = {
		xtype	: 'button',
		text	: Otm.pjt+' '+Otm.com_export,
		iconCls	: 'ico-export',
		handler	: function (btn){
			export_data('otm/project_list_export');
		}
	};

	var project_dashboard_tree_store = Ext.create('Ext.data.TreeStore', {
		proxy: {
			type: 'ajax',
			url:'./index.php/Otm/project_dashboard_list',
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

	var project_dashboard_treegrid = Ext.create('Ext.tree.Panel', {
		id			: 'project_dashboard_treegrid',
		width		: 500,
		height		: 300,
		animate		: false,
		enableDD	: true,
		rootVisible	: true,
		store		: project_dashboard_tree_store,
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
		listeners	:{
			itemclick : function(view,rec,item,index,eventObj) {
			},
			itemdblclick:function(view,rec,index,eventObj) {
				return;
			},
			select : function(smObj, record, rowIndex){
				var projectEastPanel = Ext.getCmp("project_dashboard_EastPanel");
				projectEastPanel.update("");
				projectEastPanel.removeAll();

				if(record.data.id == 'root'){
					projectEastPanel.collapse();
					return;
				}

				if(projectEastPanel.collapsed==false){
				}else{
					projectEastPanel.expand();
				}

				if(record.data.type !== 'project'){
					if(record.data.last_writer == null){
						record.data.last_writer = "";
					}
					if(record.data.last_update== null){
						record.data.last_update="";
					}
					if(record.data.regdate){
						record.data.regdate = record.data.regdate.substr(0,10);
					}

					var project_groupTplMarkup = [
						'<div style=padding:20px;>',
							'<div style="font-weight:bold;word-break:break-all;">{text}</div>',
							'<div style="border-top:1px solid gray;"></div>',
							'<div style="padding:10px;line-height:20px;">',
								'<div style="float:left;width:50%">',
									'<div style="float:left;width:40%">'+Otm.com_creator+' : </div>',
									'<div style="float:right;width:60%">{writer_name}</div>',
								'</div>',
								'<div style="float:right;width:50%">',
									'<div style="float:left;width:40%">'+Otm.com_date+' : </div>',
									'<div style="float:right;width:60%">{regdate}</div>',
								'</div>',
							'</div>',
							'<div style="clear:both;"></div>',
							'<div style="border-top:1px solid gray;"></div>',
						'</div>'
					];
					var project_groupTpl = new Ext.Template(project_groupTplMarkup);
					project_groupTpl.overwrite(projectEastPanel.body, record.data);
					return;
				}else{

				}

				Ext.Ajax.request({
					url : './index.php/Otm/project_view',
					params :{project_seq : record.data.seq},
					method: 'POST',
					success: function ( result, request ) {
						if(result.responseText){
							var project_info = Ext.decode(result.responseText);

							if(project_info.data.last_writer == null){
								project_info.data.last_writer = "";
							}

							if(project_info.data.last_update== null){
								project_info.data.last_update="";
							}else{
								project_info.data.last_update = project_info.data.last_update.substr(0,10);
							}

							if(project_info.data.pr_enddate == "0000-00-00 00:00:00"){
								project_info.data.pr_enddate = "";
							}
							if(project_info.data.pr_enddate){
								project_info.data.pr_enddate = project_info.data.pr_enddate.substr(0,10);
							}

							if(project_info.data.pr_startdate == "0000-00-00 00:00:00"){
								project_info.data.pr_startdate = "";
							}
							if(project_info.data.pr_startdate){
								project_info.data.pr_startdate = project_info.data.pr_startdate.substr(0,10);
							}

							project_info.data.regdate = project_info.data.regdate.substr(0,10);
							project_info.data.pr_description = project_info.data.pr_description.replace(/\n/gi,'<br/>').replace(/\\n/gi,'<br/>');

							projectTpl.overwrite(projectEastPanel.body, project_info.data);

							var selItem = Ext.getCmp("project_dashboard_treegrid").getSelectionModel().selected.items[0];
							selItem.data.pr_description = project_info.data.pr_description;
						}
					},
					failure: function ( result, request ) {
						Ext.Msg.alert("OTM","DataBase Select Error");
					}
				});
			}
		},
		viewConfig	: {
			plugins : {
				ptype: 'treeviewdragdrop',
				ddGroup: 'dd_project_dashboard'
			},
			listeners: {
				beforedrop: function (node, data, dropRec, dropPosition) {
					var select_node = data.records[0].data;
					var target_node = dropRec.data;

					if(mb_is_admin == 'Y' || mb_email == select_node.writer){
					}else{
						Ext.Msg.alert("OTM",Otm.com_msg_noRole+"<br>"+Otm.com_msg_youneed_auth);
						return;
					}

					if(dropPosition == "append"){

					}else{
						if(target_node.id == 'root'){
							return false;
						}
						if(select_node.type != target_node.type){
							return false;
						}
					}
				},//end beforedrop
				drop: function (node, data, dropRec, dropPosition) {
					var list = Array();
					var select_node = data.records;

					for(var i=0; i<select_node.length; i++){
						list.push(select_node[i].data['id']);
					}

					var target_id = dropRec.data.id;
					var target_ops_seq = dropRec.data.ops_seq;

					var params = {
						target_id : target_id,
						select_id : Ext.encode(list),
						position : dropPosition
					};

					Ext.Ajax.request({
						url : './index.php/Otm/move_project_group',
						params : params,
						method: 'POST',
						success: function ( result, request ) {
							if(result.responseText){
								Ext.getCmp("project_dashboard_treegrid").getSelectionModel().select(select_node);
								reload_project_tree('root');
							}
						},
						failure: function ( result, request ){
						}
					});
				}//end drop
			}
		},
		tbar	: [add_project_group_Btn,'-',add_project_Btn,'-',update_Btn,'-',delete_Btn,'-',copy_project_Btn,'-',export_project_Btn],
		columns: [{
				xtype		: 'treecolumn',
				dataIndex	: 'pg_seq',
				hidden		: true
			},{
				xtype: 'treecolumn',
				text: Otm.pjt_name, dataIndex: 'text',
				minWidth:150, flex: 1
			},
			{text: Otm.def_cnt,	dataIndex: 'defect_cnt', align:'center',	width:80},
			{header: Otm.com_complete+'<br>'+Otm.def_cnt,	dataIndex: 'defect_cnt_close', align:'center',	width:80},
			{header: Otm.com_complete+' '+Otm.def+'<br> / '+Otm.def,hidden:false,	dataIndex: '', align:'center',	width:100,
				renderer:function(value,index,record){
					if(record.data.leaf == true){
						var b = new Ext.ProgressBar({
							cls:'left-align'
						});

						var reqY = record.data.defect_cnt_close / record.data.defect_cnt;
						var ratio = 0;
						if(reqY > 0){
							ratio = Math.round(100*reqY);
						}
						ratio = '&nbsp;&nbsp;&nbsp;'+ratio+'%';
						b.updateProgress(reqY, ratio);

						return Ext.DomHelper.markup(b.getRenderTree());
					}
				}
			},
			{header: Otm.pjt_member,	dataIndex: 'user_cnt', align:'center', width:90},
			{header: Otm.com_start_date,	dataIndex: 'pr_startdate', align:'center', width:80, renderer:function(value,index,record){
				var value = value.substr(0,10);
				return value;
			}},
			{header: Otm.com_end_date,	dataIndex: 'pr_enddate', align:'center',	width:80, renderer:function(value,index,record){
				if(value){
					var value = value.substr(0,10);
					if(value == "0000-00-00"){
						value = "";
					}
				}
				return value;
			}},
			{header: Otm.pjt_creator,	dataIndex: 'writer_name', align:'center',		 width:100}
		]
	});
	project_dashboard_treegrid.getRootNode().expand();


	var project_dashboard_panel = {
		layout	: 'border',
		items : [{
			layout		: 'fit',
			region		: 'center',
			items		: [project_dashboard_treegrid]
		},{
			region		: 'east',
			layout		: 'fit',
			xtype		: 'panel',
			id			:'project_dashboard_EastPanel',
			split		: true,
			collapsible	: true,
			collapsed	: true,
			flex		: 1,
			minSize		: 100,
			maxSize		: 600,
			autoScroll	: true,
			animation	: false,
			items:[]
		}]
	};

	Ext.onReady(function(){
		Ext.getCmp('project_dashboard').add(project_dashboard_panel);
		Ext.getCmp('project_dashboard').doLayout(true,false);
	});
</script>