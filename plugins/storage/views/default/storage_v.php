<?php
/**
 * @copyright Copyright STA
 * Created on 2016. 04.
 * @author STA <otm@sta.co.kr>
 */

include_once($data['skin_dir'].'/locale-'.$data['mb_lang'].'.php');
?>
<script type="text/javascript">
	var project_seq = '<?=$project_seq?>';
	var mb_email = '<?=$mb_email?>';
	var mb_name = '<?=$mb_name?>';
	var mb_is_admin= '<?=$mb_is_admin?>';

	/**
	* Center Panel
	*/
	var storage_store = Ext.create('Ext.data.Store', {
		fields:[
			'of_seq', 'of_name'
		],
		pageSize: 50,
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/storage/storage_list',
			extraParams: {
				pr_seq : project_seq,
				node : 'root'
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

	var storage_center_panel =  {
		region	: 'center',
		layout	: 'fit',
		xtype	: 'gridpanel',
		id		: 'storage_list',
		multiSelect: true,
		store	: storage_store,
		viewConfig: {
			plugins: {
				ptype: 'gridviewdragdrop',
				//dragGroup: 'storage_list',
				dropGroup: 'dd_storage_grid',
				dragGroup: 'dd_storage_tree',
				ddGroup: 'dd_storage_grid'
			}
		},
		columns	: [
			{header: Otm.com_name,		dataIndex: 'of_source',		flex: 1,	minWidth:200,
				renderer: function(value, metaData, record, rowIndex, colIndex, store){
					var name = value.split('.');
					var type = name[name.length - 1];
					var icon = 'etc';
					switch(type)
					{
						case "doc":
						case "docx":
							icon = 'docx';
							break;
						case "ppt":
						case "pptx":
							icon = 'pptx';
							break;
						case "csv":
						case "xls":
						case "xlsx":
							icon = 'xlsx';
							break;
						case "jpg":
						case "jpeg":
							icon = 'jpeg';
							break;
						case "gif":
							icon = 'gif';
							break;
						case "pdf":
							icon = 'pdf';
							break;
						case "zip":
							icon = 'zip';
							break;
						default:
							break;
					}

					return '<img src="./resource/css/icon/thumb/'+icon+'.gif" height="12px" borer="0" /> '+value;
				}
			},
			{header: Otm.storage.type,		dataIndex: 'of_source',	align:'center',	width:80,
				renderer: function(value, metaData, record, rowIndex, colIndex, store){
					var name = value.split('.');
					var type = name[name.length - 1];
					return type;
				}},
			{header: Otm.storage.size,		dataIndex: 'of_filesize',	align:'center',	width:100,
				renderer: function(value, metaData, record, rowIndex, colIndex, store){
					var bytes = value;
					var sizes = [ 'n/a', 'bytes', 'KB', 'MB', 'GB', 'TB'];
					var i = +Math.floor(Math.log(bytes) / Math.log(1024));
					return  (bytes / Math.pow(1024, i)).toFixed( i ? 1 : 0 ) + ' ' + sizes[ isNaN( bytes ) ? 0 : i+1 ];
				}},
			{header: Otm.storage.reguser,	dataIndex: 'writer',	align:'center',	width:100},
			{header: Otm.storage.regdate,	dataIndex: 'regdate',	align:'center',	width:100},
			{header: Otm.storage.download,	dataIndex: 'of_file',	align:'center',	width:100,
				renderer: function(value, metaData, record, rowIndex, colIndex, store){
					return  '<a href=javascript:_common_fileDownload("'+record.data.otm_project_pr_seq+'","'+record.data.otm_category+'","'+record.data.target_seq+'","'+record.data.of_no+'");>Download</a>';
				}
			}
		],
		tbar	: ['->',{
				xtype	: 'button',
				text	: Otm.com_add,
				id		: 'psp_write_btn',
				iconCls	:'ico-add',
				//action:'',
				handler	: function (btn){
					var selItem = Ext.getCmp("storage_folder_tree").getSelectionModel().selected.items[0];
					if(selItem){
						if(selItem.data.id == 'root'){
							//return;
						}
					}else{
						return;
					}

					var file_upload_window = Ext.getCmp('storage_file_upload');
					if(file_upload_window){
						Ext.getCmp("storage_file_upload_form").reset();
						file_upload_window.show();
						return;
					}

					var fileForm = Ext.create("Ext.form.Panel",{
						id		: 'storage_file_upload_form',
						border	: false,
						autoScroll	: true,
						items	: [{
							layout		:'hbox',
							xtype		: 'fieldcontainer',
							fieldLabel	: Otm.com_attached_file,
							combineErrors: false,
							defaults	: {
								hideLabel: true
							},
							items		: [{
								xtype	: 'filefield',
								name	: 'form_file[]',
								allowBlank : true,
								reference: 'basicFile'
							},{
								xtype	: 'panel', border : false, width : 5
							},{
								xtype:'button',
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
					});

					Ext.create('Ext.window.Window', {
						title		: 'File Upload',
						id			: 'storage_file_upload',
						height		: 200,
						width		: 350,
						resizable	: true,
						modal		: true,
						constrainHeader: true,
						closable	: false,
						border		: false,
						closeAction	: 'hide',
						//autoScroll	: true,
						layout		: 'fit',
						items		: [fileForm],
						buttons		: [{
							text	: 'Upload',
							handler	: function(btn){

								var select_node = 'root';
								var selItem = Ext.getCmp("storage_folder_tree").getSelectionModel().selected.items[0];
								if(selItem){
									select_node = selItem.data.id;
								}else{
									//selItem = Ext.getCmp('storage_folder_tree').getStore().getRootNode();
								}

								Ext.getCmp("storage_file_upload_form").getForm().submit({
									url: './index.php/Plugin_view/storage/file_upload',
									method:'POST',
									params: {
										pr_seq	: project_seq,
										node : select_node
									},
									success: function(rsp, o){
										Ext.getCmp('storage_list').getStore().reload({params:{node:select_node}});

										Ext.getCmp("storage_file_upload_form").reset();
										Ext.getCmp('storage_file_upload').hide();
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
						},{
							text:Otm.com_close,
							handler:function(btn){
								Ext.getCmp('storage_file_upload').hide();
							}
						}]
					}).show();

				}
			},'-',{
				xtype	: 'button',
				text	: Otm.com_remove,
				id		: 'psp_delete_btn',
				iconCls	:'ico-remove',
				//action:'',
				handler	: function (btn){
					var Records = Ext.getCmp("storage_list").getSelectionModel().selected.items;

					if(Records.length > 0 ){

						var target_seq = 0;
						var of_no = '';

						for(var i=0; i<Records.length; i++){
							target_seq = Records[i].data.target_seq;

							if(i !== 0){
								of_no += ',';
							}
							of_no += Records[i].data.of_no;
						}

						var params = {
							pr_seq		: project_seq,
							target_seq	: target_seq,
							of_no		: of_no
						};

						Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
							if(bt=='yes')
							{

								Ext.Ajax.request({
									url : './index.php/Plugin_view/storage/delete_file',
									params : params,
									method: 'POST',
									success: function ( result, request ) {
										if(result.responseText){
											Ext.getCmp("storage_list").getStore().load();
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
						Ext.Msg.alert('OTM',Otm.com_msg_NotSelectData);
						return;
					}

				}
			}],
		bbar: new Ext.PagingToolbar({
			store: storage_store,
			displayInfo: true,
			listeners: {
				beforechange: function() {
					var select_node = 'root';
					var selItem = Ext.getCmp("storage_folder_tree").getSelectionModel().selected.items[0];
					if(selItem){
						select_node = selItem.data.id;
					}
					var proxy = storage_store.getProxy();
					proxy.setExtraParam('node', select_node);
				}
			}
		})
	};


	/**
	* West Panel
	*/

	function storage_form(btn)
	{
		var select_node = 'root';
		var selItem = Ext.getCmp("storage_folder_tree").getSelectionModel().selected.items[0];
		if(selItem){
			select_node = selItem.data.id;
		}else{
			selItem = Ext.getCmp('storage_folder_tree').getStore().getRootNode();
		}

		var storage_name = '';

		if(btn.action == 'storage_add'){
		}else if(btn.action == 'storage_update'){
			if(select_node == 'root'){
				return;
			}
			storage_name = selItem.data.text;
		}

		var storage_form_window = Ext.getCmp('storage_form_window');
		if(storage_form_window){
			storage_form_window.show();
			Ext.getCmp('storage_form_action').setValue(btn.action);
			Ext.getCmp('storage_name').setValue(storage_name);

			Ext.getCmp('storage_permission_grid').getStore().reload({params:{node	: select_node,	action	: btn.action}});
			return;
		}

		var role_store = Ext.create('Ext.data.Store', {
			fields:['rp_seq','rp_name','writer','regdate','permission_data'],
			proxy: {
				type: 'ajax',
				url		: './index.php/Plugin_view/storage/storage_permissioin_list',
				extraParams: {
					pr_seq	: project_seq,
					node	: select_node,
					action	: btn.action
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

		var center_panel = {
			region	: 'center',
			layout	: 'fit',
			title	: Otm.com_role_auth,
			items	: [{
				layout: 'fit',
				xtype : 'gridpanel',
				id	: 'storage_permission_grid',
				store: role_store,
				verticalScrollerType:'paginggridscroller',
				invalidateScrollerOnRefresh:false,
				autoScroll : true,
				viewConfig: {
					listeners: {
						viewready: function(){
						   this.store.load();
						}
					}
				},
				columns: [
					{xtype: 'rownumberer',width: 30,sortable: false},
					{header: Otm.com_role+' '+Otm.com_name,	dataIndex: 'rp_name', minWidth:150, flex: 1},
					{header: 'Read',	dataIndex: 'psp_read', xtype: 'checkcolumn', width:80, align:'center',
						listeners: {
							checkChange: function (column, rowIndex, checked, eOpts) {
								var record = role_store.getAt(rowIndex);
								if(checked){
								}else{
									record.set('psp_write', checked);
									record.set('psp_delete', checked);
								}
							}
						}
					},
					{header: 'Write',	dataIndex: 'psp_write', xtype: 'checkcolumn', width:80, align:'center',
						listeners: {
							checkChange: function (column, rowIndex, checked, eOpts) {
								var record = role_store.getAt(rowIndex);
								if(checked){
									record.set('psp_read', checked);
								}
							}
						}},
					{header: 'Delete',	dataIndex: 'psp_delete', xtype: 'checkcolumn', width:80, align:'center',
						listeners: {
							checkChange: function (column, rowIndex, checked, eOpts) {
								var record = role_store.getAt(rowIndex);
								if(checked){
									record.set('psp_read', checked);
								}
							}
						}}
				]
			}]
		};

		Ext.create('Ext.window.Window', {
			layout		: 'border',
			title		: 'Storage Folder Add/Update',
			id			: 'storage_form_window',
			height		: 350,
			width		: 600,
			resizable	: true,
			modal		: true,
			constrainHeader: true,
			closable	: false,
			border		: false,
			closeAction	: 'hide',
			items		: [{
				region	: 'north',
				items	: [{
					xtype	: 'hiddenfield',
					id		: 'storage_form_action',
					name	: 'storage_form_action',
					value	: btn.action
				},{
					xtype		: 'textfield',
					fieldLabel	: Otm.com_name+'(*)',
					id			: 'storage_name',
					name		: 'storage_name',
					value		: storage_name,
					anchor		: '100%',
					minLength	: 2,
					maxLength	: 100
				}]
				},
				center_panel
			],
			buttons		: [{
				text	: 'Save',
				handler	: function(){
					var name = Ext.getCmp('storage_name').getValue();
					if(name && name != '' && name.length > 1){
					}else{
						Ext.Msg.alert('OTM', Otm.com_msg_default_mandatory, function(){
							Ext.getCmp('storage_name').focus();
							return true;
						});
						return;
					}

					var storage_form_action = Ext.getCmp('storage_form_action').getValue();

					var url = './index.php/Plugin_view/storage/create_folder';
					if(storage_form_action == 'storage_update'){
						url = './index.php/Plugin_view/storage/update_folder';
					}

					var store_items = Ext.getCmp('storage_permission_grid').getStore().data.items;
					var permitions = Array();
					for(var i=0; i<store_items.length; i++){
						var item = {};
						item.rp_seq = store_items[i].data.rp_seq;
						item.psp_read = store_items[i].data.psp_read;
						item.psp_write = store_items[i].data.psp_write;
						item.psp_delete = store_items[i].data.psp_delete;

						permitions.push(item);
					}

					var select_node = 'root';
					var selItem = Ext.getCmp("storage_folder_tree").getSelectionModel().selected.items[0];
					if(selItem){
						select_node = selItem.data.id;
					}else{
						selItem = Ext.getCmp('storage_folder_tree').getStore().getRootNode();
					}

					var params = {
							pr_seq		: project_seq,
							node		: select_node,
							ops_subject	: name,
							permitions	: Ext.encode(permitions)
						};

					Ext.Ajax.request({
						url : url,
						params : params,
						method: 'POST',
						success: function ( result, request ) {
							if(result.responseText){
								var info = Ext.decode(result.responseText);
								info.permission = Ext.decode(info.permission);

								if(info.permission.psp_write && info.permission.psp_write > 0){
									Ext.getCmp('psp_write_btn').enable(true);
								}else{
									Ext.getCmp('psp_write_btn').disable(true);
								}

								if(info.permission.psp_delete && info.permission.psp_delete > 0){
									Ext.getCmp('psp_delete_btn').enable(true);
								}else{
									Ext.getCmp('psp_delete_btn').disable(true);
								}

								if(mb_is_admin == 'Y'){
									Ext.getCmp('psp_write_btn').enable(true);
									Ext.getCmp('psp_delete_btn').enable(true);
								}

								if(storage_form_action == 'storage_update'){
									var node = Ext.getCmp('storage_folder_tree').getStore().getRootNode().findChild('id', info.ops_seq, true);
									if(node){
										node.set('text', name);
										node.set('psp_write', info.permission.psp_write);
										node.set('psp_delete', info.permission.psp_delete);

										Ext.getCmp("storage_folder_tree").getSelectionModel().select(node);
										Ext.getCmp('storage_form_window').hide();
									}
									return;
								}

								selItem.insertChild(selItem.childNodes.length, {
									id		: info.ops_seq,
									text	: name,
									ops_seq	: info.ops_seq,
									psp_write	: info.permission.psp_write,
									psp_delete	: info.permission.psp_delete,
									leaf	: false
								});

								selItem.expand(true);

								var node = Ext.getCmp('storage_folder_tree').getStore().getRootNode().findChild('id', info.ops_seq, true);
								if(node){
									Ext.getCmp("storage_folder_tree").getSelectionModel().select(node);
								}

								Ext.getCmp('storage_form_window').hide();
							}
						},
						failure: function ( result, request ){
						}
					});

				}
			},{
				text	: Otm.com_close,
				handler	: function(btn){
					Ext.getCmp('storage_form_window').hide();
				}
			}]
		}).show();

		return;
	}

	var storage_tree_store = Ext.create('Ext.data.TreeStore', {
		root: {
			text: 'Storage',
			ops_seq : 0,
			iconCls:'ico-storage',
			expanded: true,
			draggable: false,
			children: []
		},
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/storage/storage_tree_list',
			extraParams: {
				pr_seq		: project_seq
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

	var storage_folder_tree = {
		xtype		: 'treepanel',
		id			: 'storage_folder_tree',
		rootVisible	: true,
		lines		: false,
		animate		: false,
		store		: storage_tree_store,
		enableDD	: true,
		viewConfig	: {
			plugins : {
				ptype: 'treeviewdragdrop',
				appendOnly: false,
				ddGroup: 'dd_storage_tree',
				dragGroup: 'dd_storage_tree',
				dropGroup: 'dd_storage_tree'
			},
			listeners: {
				beforedrop: function (node, data, dropRec, dropPosition) {
					var select_node = data.records;
					var target_node = dropRec.data;

					if(data.view.xtype == 'gridview'){

						if(dropPosition == "append"){

							var list = Array();
							var select_node = data.records;

							for(var i=0; i<select_node.length; i++){
								var info = {
									target_seq : select_node[i].data['target_seq'],
										of_no : select_node[i].data['of_no']
								};
								list.push(info);
							}

							var target_id = dropRec.data.id;
							var target_ops_seq = dropRec.data.id;

							var params = {
								pr_seq	: project_seq,
								target_seq : target_ops_seq,
								select_id : Ext.encode(list)
							};

							Ext.Ajax.request({
								url : './index.php/Plugin_view/storage/move_file',
								params : params,
								method: 'POST',
								success: function ( result, request ) {
									if(result.responseText){
										Ext.getCmp('storage_list').getStore().reload();
									}
								},
								failure: function ( result, request ){
								}
							});
						}

						return false;
					}

					if(dropPosition == "append"){

					}else{
						if(data.view.xtype == 'gridview'){
							return false;
						}
						if(target_node.id == 'root'){
							return false;
						}
						if(select_node.type != target_node.type){
							return false;
						}
					}
				},
				drop: function (node, data, dropRec, dropPosition) {
					var list = Array();
					var select_node = data.records;

					for(var i=0; i<select_node.length; i++){
						list.push(select_node[i].data['id']);
					}

					var target_id = dropRec.data.id;
					var target_ops_seq = dropRec.data.ops_seq;

					var params = {
						pr_seq	: project_seq,
						target_ops_seq : target_ops_seq,
						target_id : target_id,
						select_id : Ext.encode(list),
						position : dropPosition
					};

					Ext.Ajax.request({
						url : './index.php/Plugin_view/storage/move_folder',
						params : params,
						method: 'POST',
						success: function ( result, request ) {
							if(result.responseText){

							}
						},
						failure: function ( result, request ){
						}
					});
				}
			}
		},
		listeners	: {
			select	: function(view,rec,item,index,eventObj) {
				if(mb_is_admin == 'Y' || rec.data.id == 'root'){
					Ext.getCmp('psp_write_btn').enable(true);
					Ext.getCmp('psp_delete_btn').enable(true);
				}else{
					if(rec.data.psp_write && rec.data.psp_write > 0){
						Ext.getCmp('psp_write_btn').enable(true);
					}else{
						Ext.getCmp('psp_write_btn').disable(true);
					}

					if(rec.data.psp_delete && rec.data.psp_delete > 0){
						Ext.getCmp('psp_delete_btn').enable(true);
					}else{
						Ext.getCmp('psp_delete_btn').disable(true);
					}
				}

				Ext.getCmp('storage_list').getStore().load({params:{node:rec.data.id}});
			}
		}
	};

	var storage_west_panel = {
		region		: 'west',
		layout		: 'fit',
		split		: true,
		collapsible	: false,
		collapsed	: false,
		animation	: false,
		autoScroll	: true,
		width		: 200,
		minWidth	: 150,
		maxWidth	: 600,
		items		: [storage_folder_tree],
		tbar		: [{
			xtype	: 'button',
			text	: Otm.com_add,
			id		: 'storage_add',
			iconCls	: 'ico-add',
			action	:'storage_add',
			handler	:function (btn){
				storage_form(btn);
				return;
			}
		},'-',{
			xtype: 'button',
			text: Otm.com_update,
			iconCls:'ico-update',
			action:'storage_update',
			handler:function (btn){
				var selItem = Ext.getCmp("storage_folder_tree").getSelectionModel().selected.items[0];
				
				if(selItem){
					if(mb_is_admin == 'Y' || selItem.data.writer == mb_email){
						storage_form(btn);
					}else{
						Ext.Msg.alert("OTM",Otm.com_update+Otm.com_msg_noRole+"<br>"+Otm.com_msg_youneed_auth);
						return;
					}
				}else{
					return;
				}
			}
		},'-',{
			xtype: 'button',
			text: Otm.com_remove,
			iconCls:'ico-remove',
			handler:function (btn){
				var selItem = Ext.getCmp("storage_folder_tree").getSelectionModel().selected.items[0];				
				if(selItem){
					if(mb_is_admin == 'Y' || selItem.data.writer == mb_email){

					}else{
						Ext.Msg.alert("OTM",Otm.com_remove+Otm.com_msg_noRole + "<br>" + Otm.com_msg_youneed_auth);
						return;
					}

					Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm + "<br>" + Otm.storage.msg_delete_folder,function(bt){
						if(bt=='yes')
						{
							Ext.Ajax.request({
								url : './index.php/Plugin_view/storage/delete_folder',
								params : {
									pr_seq		: project_seq,
									node		: selItem.data.id
								},
								method: 'POST',
								success: function ( result, request ) {
									if(result.responseText){
										Ext.getCmp("storage_folder_tree").getStore().load();
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
					return;
				}
			}
		}]
	};


	Ext.onReady(function(){
		var main_panel = {
			layout		: 'border',
			defaults	: {
				collapsible	: false,
				split		: false,
				bodyStyle	: 'padding:0px'
			},
			items		: [storage_center_panel,storage_west_panel]
		};

		Ext.getCmp('storage').add(main_panel);
		Ext.getCmp('storage').doLayout(true,false);
		Ext.getCmp('storage_folder_tree').getSelectionModel().select(0);
	});
</script>