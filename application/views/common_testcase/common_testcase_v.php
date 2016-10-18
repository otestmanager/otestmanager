<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script type="text/javascript">
	function get_select_comtc_testcase()
	{
		var selItem = Ext.getCmp("comtc_treegrid").getSelectionModel().selected.items[0];

		if(selItem){
			return selItem.data;
		}else{
			return '';
		}
	}
	var mask = {
		start : function(){
			Ext.getBody().mask(Otm.com_msg_processing_data);
		},
		stop : function(){
			Ext.getBody().unmask();
		}
	}

	/**
	* Add, Update, Delete Button
	*/
	var add_comtc_suite_btn = {
		xtype:'button',
		text:Otm.tc_suite+' '+Otm.com_add,
		iconCls:'ico-add',
		handler:function (btn){
			var obj = {
				form_type : 'suite',
				action_type : 'add'
			};
			Ext.getCmp('comtc_east_panel').add(get_comtc_write_form(obj));
		}
	};

	var add_comtc_case_btn = {
		xtype:'button',
		text:Otm.tc+' '+Otm.com_add,
		iconCls:'ico-add',
		handler:function (btn){
			var obj = {
				form_type : 'case',
				action_type : 'add'
			};
			Ext.getCmp('comtc_east_panel').add(get_comtc_write_form(obj));
		}
	};
	var edit_comtc_testcase_btn = {
		xtype:'button',
		text:Otm.com_update,
		iconCls:'ico-update',
		handler:function (btn){
			var node = Ext.getCmp("comtc_treegrid").getSelectionModel().selected;

			for(var i=0;i<node.length;i++){
				if(node.keys[i] == "root"){
					Ext.Msg.alert("OTM",Otm.com_msg_root_cannot_modify);
					return;
				}
			}

			if(get_select_comtc_testcase() == ''){
				Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
				return;
			}

			if(Ext.getCmp("comtc_treegrid").getSelectionModel().selected.length > 1){
				Ext.Msg.alert('OTM',Otm.com_msg_only_one);
				return;
			}

			if(Ext.getCmp("comtc_treegrid").getSelectionModel().selected.length >= 1){
				var obj = {
					form_type : (get_select_comtc_testcase().type == 'folder')?'suite':'case',
					action_type : 'edit'
				};
				Ext.getCmp('comtc_east_panel').add(get_comtc_write_form(obj));
			}
		}
	};
	var delete_comtc_testcase_btn = {
		xtype:'button',
		text:Otm.com_remove,
		iconCls:'ico-remove',
		handler:function (btn){
			if(get_select_comtc_testcase() == ''){
				Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
				return;
			}
			var node = Ext.getCmp("comtc_treegrid").getSelectionModel().selected;
			for(var i=0;i<node.length;i++){
				if(node.keys[i] == "root"){
					Ext.Msg.alert("OTM",Otm.com_msg_root_cannot_delete);
					return;
				}
			}

			if(Ext.getCmp("comtc_treegrid").getSelectionModel().selected.length >= 1){
				Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
					if(bt=='yes'){
						var Records = Ext.getCmp("comtc_treegrid").getSelectionModel().selected.items;
						var list = Array();

						if(Records.length >= 1){
							for(var i=0; i<Records.length; i++){
								list.push(Records[i].data['id']);
							}

							var params = {
									version : get_select_comtc_product().seq,
									list : Ext.encode(list)
								};
						}

						Ext.Ajax.request({
							url : './index.php/Com_testcase/delete_testcase',
							params :params,
							method: 'POST',
							success: function ( result, request ) {
								Records.cmd = "delete";
								if(result.responseText){
									NodeReload(Records);
									Ext.getCmp('comtc_treegrid').getSelectionModel().deselectAll();
									Ext.getCmp('comtc_east_panel').removeAll();
									Ext.getCmp('comtc_east_panel').update('');
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
				Ext.Msg.alert('OTM',Otm.com_msg_only_one);
				return;
			}
		}
	};

	var export_comtc_testcase_btn = {
		xtype:'button',
		text:Otm.com_export,
		iconCls:'ico-export',
		handler: function (btn){
			if(get_select_comtc_product() == ''){
				Ext.Msg.alert('OTM',Otm.com_msg_NotVersion);
				return;
			}else{
				var type = get_select_comtc_product().type;
				if(type && type == 'version'){
					var params = 'seq='+get_select_comtc_product().seq;

					export_data('com_testcase/comtc_list_export',params);
				}else{
					Ext.Msg.alert('OTM',Otm.com_msg_NotVersion);
				}
			}
		}
	};

	var import_comtc_testcase_btn = {
		xtype: 'button',
		text: Otm.com_import,
		iconCls:'ico-import',
		/*action_type:'tc_add',*/
		handler:function (btn){
			if(get_select_comtc_product() == ''){
				Ext.Msg.alert('OTM',Otm.com_msg_NotVersion);
				return;
			}else{
				var type = get_select_comtc_product().type;
				if(type && type == 'version'){
					var params = 'seq='+get_select_comtc_product().seq;
					viewImportComTCWin();
				}else{
					Ext.Msg.alert('OTM',Otm.com_msg_NotVersion);
				}
			}
		}
	};

	/**
	* TreeGrid
	*/
	var comtc_testcase_tree_store = Ext.create('Ext.data.TreeStore', {
		root: {
			text: 'Root',
			expanded: true,
			expandable: false,
			children: []
		},
		proxy: {
			type: 'ajax',
			url:'./index.php/Com_testcase/testcase_tree_list',
			reader: {
				type: 'json',
				totalProperty: 'totalCount',
				rootProperty: 'data'
			}
		},
		folderSort: true
	});

	comtc_testcase_tree_store.on('load',function(){
		Ext.getCmp("acc").unmask();
		Ext.getCmp('comtc_treegrid').getSelectionModel().deselectAll();
	});

	var comtc_treegrid = Ext.create('Ext.tree.Panel', {
		id: 'comtc_treegrid',
		width: 500,
		height: 300,
		enableDD: true,
		useArrows: true,
		rootVisible: true,
		root: {
			nodeType: 'async',
            text: 'Root',
			draggable: false
        },
		store: comtc_testcase_tree_store,
		multiSelect: true,
		singleExpand: false,
		animate: false,
		listeners:{
			itemclick : function(view,rec,item,index,eventObj) {
				var node = Ext.getCmp("comtc_treegrid").getSelectionModel().selected;

				if(node.length == 1){
					if(rec.id == "root"){
						return;
					}
					var obj = {
						form_type : (rec.get('type') == 'folder')?'suite':'case',
						id : rec.get('id')
					};
					get_comtc_view_form(obj);
				}else if(node.length > 1 || node.length == 0){
					Ext.getCmp('comtc_east_panel').removeAll();
					Ext.getCmp('comtc_east_panel').update("");
				}
			},
			deselect:function(view,rec,index,eventObj){
				Ext.getCmp('comtc_east_panel').removeAll();
				Ext.getCmp('comtc_east_panel').update("");
			}
		},
		viewConfig: {
			plugins : {
				ptype: 'treeviewdragdrop'
			},
			listeners: {
				beforedrop: function (node, data) {
				},
				drop: function (node, data, dropRec, dropPosition) {
					var list = Array();
					var select_node = data.records;
					for(var i=0; i<select_node.length; i++){
						list.push(select_node[i].data['id']);
					}
					var target_id = dropRec.data.id;
					var target_type = dropRec.data.type;

					var params = {
						target_id	: target_id,
						target_type : target_type,
						position	: dropPosition,
						select_id	: Ext.encode(list)
					};
					mask.start();
					Ext.Ajax.request({
						url : './index.php/Com_testcase/move_testcase',
						params : params,
						method: 'POST',
						success: function ( result, request ) {
							if(result.responseText){
								Ext.getCmp('comtc_east_panel').removeAll();
								Ext.getCmp('comtc_east_panel').update("");
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
		columns: [{
				xtype: 'treecolumn',
				text: Otm.com_subject, dataIndex: 'text',
				minWidth:150, flex: 1, sortable: true
			},{
				text: Otm.com_id, dataIndex: 'out_id',
				width:100, sortable: true,align:'center'
			},{
				text: Otm.com_creator, dataIndex: 'writer_name',
				width:100, sortable: true,align:'center'
			},{
				text: Otm.com_modifiers, dataIndex: 'last_writer_name',
				width:120, sortable: true,align:'center'
			},{
				text: Otm.com_modified, dataIndex: 'last_update',
				width:150, sortable: true,align:'center',renderer:function(value,index,record){
					if(value){
						if(value == '0000-00-00 00:00:00'){
							return '';
						}
						value = value.substr(0,10);
					}else{
						value = '';
					}
					return value;
				}
			}],
		bbar:tree_OpenClose_Btn('comtc_treegrid')
	});
	comtc_treegrid.getRootNode().expand();


	/**
	* Center Panel
	*/
	var comtc_center_panel =  {
		layout	: 'fit',
		region	: 'center',
		items	: [comtc_treegrid],
		tbar	: [add_comtc_suite_btn,'-',add_comtc_case_btn,'-',edit_comtc_testcase_btn,'-',delete_comtc_testcase_btn,'-',export_comtc_testcase_btn,'-',import_comtc_testcase_btn]
	};

	/**
	* East Form Panel
	*/
	function get_comtc_write_form(obj)
	{
		if(obj.action_type == 'add'){
			if(get_select_comtc_product() == '' || get_select_comtc_product().type == 'product'){
				Ext.Msg.alert('OTM',Otm.com_msg_NotVersion_add);
				return;
			}else{
				if(get_select_comtc_testcase().type == 'case' || get_select_comtc_testcase().type == 'file'){
					Ext.Msg.alert('OTM',Otm.com_msg_testcase_inadd);
					return;
				}
			}
		}else{
			if(get_select_comtc_testcase() == ''){
				Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
				return;
			}
		}

		Ext.getCmp('comtc_east_panel').removeAll();
		Ext.getCmp('comtc_east_panel').update("");

		var comtc_east_panel = Ext.getCmp("comtc_east_panel");
		if(comtc_east_panel.collapsed==false){
		}else{
			comtc_east_panel.expand();
		}

		var items = [];
		switch(obj.form_type){
			case 'suite':
				var subject = {
					anchor: '100%',
					fieldLabel: Otm.tc_suite+' '+Otm.com_name+'(*)',
					id: 'comtc_suite_name',
					minLength:2,
					maxLength:100,
					allowBlank : false,
					xtype: 'textfield'
				};
				var description = {
					anchor: '100%',
					fieldLabel: Otm.com_description,
					id: 'comtc_suite_description',
					allowBlank : true,
					height:'100',
					xtype: 'textarea'
				};

				items = [subject,description];

				break;
			case 'case':
				var subject = {
					anchor: '100%',
					fieldLabel: Otm.tc+' '+Otm.com_name+'(*)',
					id: 'comtc_case_name',
					minLength:2,
					maxLength:100,
					allowBlank : false,
					xtype: 'textfield'
				};
				var precondition = {
					anchor: '100%',
					fieldLabel: Otm.tc_precondition,
					id: 'comtc_case_precondition',
					allowBlank : true,
					height:'100',
					xtype: 'textarea'
				};
				var testdata = {
					anchor: '100%',
					fieldLabel: Otm.tc_testdata,
					id: 'comtc_case_testdata',
					allowBlank : true,
					height:'100',
					xtype: 'textarea'
				};
				var procedure = {
					anchor: '100%',
					fieldLabel: Otm.tc_action_performed,
					id: 'comtc_case_procedure',
					allowBlank : true,
					height:'100',
					xtype: 'textarea'
				};
				var expected_result = {
					anchor: '100%',
					fieldLabel: Otm.tc_expected_result,
					id: 'comtc_case_expected_result',
					allowBlank : true,
					height:'100',
					xtype: 'textarea'
				};
				var description = {
					anchor: '100%',
					fieldLabel: Otm.tc_remarks,
					id: 'comtc_case_description',
					allowBlank : true,
					height:'100',
					xtype: 'textarea'
				};

				items = [subject,precondition,testdata,procedure,expected_result,description];
				break;
		}

		var comtc_testcase_write_form = Ext.create("Ext.form.Panel",{
			id : 'comtc_testcase_write_form',
			border:false,
			bodyStyle: 'padding: 10px;',
			autoScroll: true,
			labelWidth:'10',
			items: items,
			buttons:[{
				text:Otm.com_save,
				iconCls:'ico-save',
				disabled: true,
				formBind: true,
				handler:function(btn){
					var form = this.up('form').getForm();
					if(form.isValid()) {
						var URL = './index.php/Com_testcase/create_testcase';
						var action_type = "add";

						switch(obj.form_type){
							case 'suite':
								var params = {
									type			: obj.form_type,
									v_seq			: get_select_comtc_product().seq,
									pid				: (get_select_comtc_testcase() != '')?get_select_comtc_testcase().id:'',
									ct_subject		: form.findField("comtc_suite_name").getValue(),
									ct_description	: form.findField("comtc_suite_description").getValue()
								};
								break;
							case 'case':
								var params = {
									type				: obj.form_type,
									v_seq				: get_select_comtc_product().seq,
									pid					: (get_select_comtc_testcase() != '')?get_select_comtc_testcase().id:'',
									ct_subject			: form.findField("comtc_case_name").getValue(),
									ct_precondition		: form.findField("comtc_case_precondition").getValue(),
									ct_testdata			: form.findField("comtc_case_testdata").getValue(),
									ct_procedure		: form.findField("comtc_case_procedure").getValue(),
									ct_expected_result	: form.findField("comtc_case_expected_result").getValue(),
									ct_description		: form.findField("comtc_case_description").getValue()
								};
								break;
						}

						if(obj.action_type == 'edit'){
							action_type = "update";
							var URL = './index.php/Com_testcase/update_testcase';
							params.seq = get_select_comtc_testcase().seq;
							params.pid = get_select_comtc_testcase().pid;
							params.out_id = (get_select_comtc_testcase() != '')?get_select_comtc_testcase().out_id:'';
						}

						Ext.Ajax.request({
							url : URL,
							params : params,
							method: 'POST',
							success: function ( result, request ) {
								var obj = Ext.decode(result.responseText);
								var node = Ext.decode(obj.data);

								node.cmd = action_type;
								node.target_grid = "comtc_treegrid";
								NodeReload(node);


								var nodeInfo = {
									form_type : (node.type == 'folder')?'suite':'case',
									id : node.id
								}
								get_comtc_view_form(nodeInfo);
								return;
							},
							failure: function ( result, request ){
							}
						});

					}
				}
			},{
				text:Otm.com_reset,
				iconCls:'ico-reset',
				hidden:true,
				handler:function(btn){
					Ext.getCmp("comtc_testcase_write_form").reset();
				}
			}]
		});

		if(obj.action_type == 'edit'){
			var URL = './index.php/Com_testcase/get_testcase_info';
				Ext.Ajax.request({
				url : URL,
				params : {
					id : get_select_comtc_testcase().id
				},
				method: 'POST',
				success: function ( result, request ) {
					if(result.responseText){
						var testcase_info = Ext.decode(result.responseText);
						var selItem = Ext.getCmp("comtc_treegrid").getSelectionModel().selected.items[0];

						switch(obj.form_type){
							case 'suite':
								selItem.data.comtc_suite_name			= testcase_info.data.ct_subject;
								selItem.data.comtc_suite_description	= testcase_info.data.ct_description;
								break;
							case 'case':
								selItem.data.comtc_case_name			= testcase_info.data.ct_subject;
								selItem.data.comtc_case_precondition	= testcase_info.data.ct_precondition;
								selItem.data.comtc_case_testdata		= testcase_info.data.ct_testdata;
								selItem.data.comtc_case_procedure		= testcase_info.data.ct_procedure;
								selItem.data.comtc_case_expected_result	= testcase_info.data.ct_expected_result;
								selItem.data.comtc_case_description		= testcase_info.data.ct_description;
							break;
						}

						Ext.getCmp("comtc_testcase_write_form").loadRecord(selItem);
					}
				},
				failure: function ( result, request ){
				}
			});
		}

		return comtc_testcase_write_form;
	}

	function get_comtc_view_form(obj)
	{
		Ext.getCmp('comtc_east_panel').removeAll();
		Ext.getCmp('comtc_east_panel').update("");

		var comtc_east_panel = Ext.getCmp("comtc_east_panel");
		if(comtc_east_panel.collapsed==false){
		}else{
			comtc_east_panel.expand();
		}

		switch(obj.form_type){
			case 'suite':
				var comtc_testcase_TplMarkup = [
					'<div style=padding:20px;>',
						'<div style="font-weight:bold;">['+Otm.tc_suite+']</div>',
						'<div style="margin-top:20px;"></div>',

						'<div style="font-weight:bold;word-break:break-all;">{ct_subject}</div>',
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
							'<div style="float:left;width:50%">',
								'<div style="float:left;width:40%">'+Otm.com_modifiers+' : </div>',
								'<div style="float:right;width:60%">{last_writer_name}</div>',
							'</div>',
							'<div style="float:right;width:50%">',
								'<div style="float:left;width:40%">'+Otm.com_modified+' : </div>',
								'<div style="float:right;width:60%">{last_update}</div>',
							'</div>',
						'</div>',

						'<div style="clear:both;"></div>',
						'<div style="border-top:1px solid gray;margin-top:20px;"></div>',

						'<div style="padding:10px;word-break:break-all;"><pre>{ct_description}</pre></div>',
					'</div>'
				];

				break;
			case 'case':
				var comtc_testcase_TplMarkup = [
					'<div style=padding:20px;>',
						'<div style="font-weight:bold;">['+Otm.tc+']</div>',
						'<div style="margin-top:20px;"></div>',

						'<div style="font-weight:bold;word-break:break-all;">{ct_subject}</div>',
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
							'<div style="float:left;width:50%">',
								'<div style="float:left;width:40%">'+Otm.com_modifiers+' : </div>',
								'<div style="float:right;width:60%">{last_writer_name}</div>',
							'</div>',
							'<div style="float:right;width:50%">',
								'<div style="float:left;width:40%">'+Otm.com_modified+' : </div>',
								'<div style="float:right;width:60%">{last_update}</div>',
							'</div>',
						'</div>',

						'<div style="clear:both;"></div>',
						'<div style="border-top:1px solid gray;margin-top:20px;"></div>',

						'<div style="padding:1px;"><pre><b>['+Otm.tc_precondition+']</b></pre></div>',
						'<div style="padding:1px;word-break:break-all;"><pre>{ct_precondition}</pre></div>',
						'<div style="padding:1px;"><pre><b>['+Otm.tc_testdata+']</b></pre></div>',
						'<div style="padding:1px;word-break:break-all;"><pre>{ct_testdata}</pre></div>',
						'<div style="padding:1px;"><pre><b>['+Otm.tc_action_performed+']</b></pre></div>',
						'<div style="padding:1px;word-break:break-all;"><pre>{ct_procedure}</pre></div>',
						'<div style="padding:1px;"><pre><b>['+Otm.tc_expected_result+']</b></pre></div>',
						'<div style="padding:1px;word-break:break-all;"><pre>{ct_expected_result}</pre></div>',
						'<div style="padding:1px;"><pre><b>['+Otm.tc_remarks+']</b></pre></div>',
						'<div style="padding:1px;word-break:break-all;"><pre>{ct_description}</pre></div>',
					'</div>'
				];
				break;
		}

		var comtc_testcase_Tpl = new Ext.Template(comtc_testcase_TplMarkup);


		Ext.Ajax.request({
			url : './index.php/Com_testcase/get_testcase_info',
			params :{id : obj.id},
			method: 'POST',
			success: function ( result, request ) {
				if(result.responseText){
					var info = Ext.decode(result.responseText);
					if(info.data.last_update == '0000-00-00 00:00:00'){
						info.data.last_update = '';
					}
					if(info.data.last_writer_name == null){
						info.data.last_writer_name="";
					}

					comtc_testcase_Tpl.overwrite(Ext.getCmp("comtc_east_panel").body, info.data);
				}
			},
			failure: function ( result, request ) {
				Ext.getCmp("dashboard").unmask();
				Ext.Msg.alert("OTM","DataBase Select Error");
			}
		});
	}

	function viewImportComTCWin()
	{
		var win = Ext.getCmp('comtc_import_window');
		if(win){
			Ext.getCmp("com_testcase_uploadPanel").reset();
			Ext.getCmp("com_testcase_uploadPanel").update();
			win.show();
			return;
		}
		var tmp_store = Ext.create('Ext.data.Store', {
			fields:['location', 'ct_id', 'ct_subject', 'ct_precondition', 'ct_testdata', 'ct_procedure', 'ct_expected_result', 'ct_description'],
			proxy: {
				type: 'memory',
				reader: {
					type: 'json',
					rootProperty: 'data'
				}
			}
		});

		var column = [
			Ext.create('Ext.grid.RowNumberer'),
			{header: 'Location',  dataIndex: 'location'},
			{header: 'TC ID',  dataIndex: 'ct_id'},
			{header: Otm.tc+' '+Otm.com_name+'(*)', dataIndex: 'ct_subject'},
			{header: Otm.tc_precondition, dataIndex: 'ct_precondition', align:'center'},
			{header: Otm.tc_testdata, dataIndex: 'ct_testdata', align:'center'},
			{header: Otm.tc_action_performed, dataIndex: 'ct_procedure', align:'center'},
			{header: Otm.tc_expected_result, dataIndex: 'ct_expected_result', align:'center'},
			{header: Otm.tc_remarks, dataIndex: 'ct_description', align:'center'}
		];

		var sample_grid = Ext.create("Ext.grid.Panel",{
			border	: true,
			forceFit: true,
			height	: 200,
			store	: tmp_store,
			columns	: column
		});


		var checck_id_checkbox = {
			xtype: 'radiogroup',	width:'100%',
			id:'comtc_import_check_id',
			fieldLabel : 'Check ID',
			columns: 2,
			items: [{
				boxLabel:'Insert All',
				name : 'comtc_import_check_id',
				inputValue : false,
				checked:true
			},{
				boxLabel:'Insert and Update',
				name : 'comtc_import_check_id',
				inputValue : true,
				checked:false
			}]
		}

		var com_testcase_uploadPanel = new Ext.FormPanel({
			fileUpload: true,
			id:'com_testcase_uploadPanel',
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
			},
				sample_grid,
			],
			buttons: [pbar,{
				text:Otm.com_save,
				formBind: true,
				iconCls:'ico-save',
				id:'comtc_testCaseImportSubmitBtn',
				handler: function(){
					if(Ext.getCmp("com_testcase_uploadPanel").getForm().isValid()){
						top.myUpdateProgress(1,'Data Loadding...');
						var params = {
									version	: get_select_comtc_product().seq,
									import_check_id : false
								};
						var chkInfo = Ext.getCmp('comtc_import_check_id').items;
						if(chkInfo.items[1].checked){
							params.import_check_id = true;
						}

						var URL = "./index.php/Import/com_testcase";

						Ext.getCmp("com_testcase_uploadPanel").mask(Otm.com_msg_processing_data);

						Ext.getCmp("com_testcase_uploadPanel").getForm().submit({
							url: URL,
							method:'POST',
							params : params,
							success: function(form, action){
								top.myUpdateProgress(100,'End');

								Ext.getCmp('comtc_treegrid').getStore().load({params:{v_seq:get_select_comtc_product().seq}});

								Ext.getCmp("com_testcase_uploadPanel").unmask();
								Ext.getCmp("comtc_import_window").hide();
							},
							failure: function(form, action){
								top.myUpdateProgress(100,'End');

								Ext.getCmp("com_testcase_uploadPanel").unmask();
								var obj = Ext.decode(action.response.responseText);
								Ext.Msg.alert('OTM',obj.msg);
							}
						});
					}
				}
			}]
		});

		var import_window = new Ext.Window({
			title: Otm.tc+' '+Otm.com_import+'(.xls)',
			id	: 'comtc_import_window',
			height: 230,
			width: 650,
			layout: 'fit',
			resizable : true,
			modal : true,
			constrainHeader: true,
			closeAction: 'hide',
			items: [com_testcase_uploadPanel]
		});
		import_window.show();
	}

	/**
	* East Panel
	*/
	var comtc_east_panel = {
		region	: 'east',
		layout	: 'fit',
		split	: true,
		collapsible	: true,
		collapsed	: true,
		flex: 1,
		animation: false,
		autoScroll: true,
		minWidth: 420,
		maxWidth: 600,
		id:'comtc_east_panel',
		items:[]
	};

	Ext.onReady(function(){
		var main_panel = {
			layout		: 'border',
			defaults	: {
				collapsible	: false,
				split		: true,
				bodyStyle	: 'padding:0px'
			},
			items		: [comtc_center_panel,comtc_east_panel]
		};
		Ext.getCmp('comtc_main').add(main_panel);
		Ext.getCmp('comtc_main').doLayout();

		Ext.Msg.alert('OTM',Otm.comtc_msg_unsupport_service);
	});
</script>