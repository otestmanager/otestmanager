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
							_setCustomform_userdata(customform_seq,df_customform);

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
				var riskanalysis_riskitem_east_panel = Ext.getCmp('riskanalysis_riskitem_east_panel');

				riskanalysis_riskitem_east_panel.setTitle(Otm.com_view);
				if(riskanalysis_riskitem_east_panel.collapsed==false){
				}else{
					riskanalysis_riskitem_east_panel.expand();
				}

				var obj ={
					target : 'riskanalysis_riskitem_form',
					ri_seq : record.data.ri_seq,
					pr_seq : record.data.otm_project_pr_seq
				};

				riskitem_view_panel(obj);
				
				return;
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

										var riskanalysis_riskitem_east_panel = Ext.getCmp('riskanalysis_riskitem_east_panel');
										riskanalysis_riskitem_east_panel.collapse();
										form_reset();
										//riskanalysis_riskitem_east_panel.removeAll();
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
			},'-',{
				text	: Otm.com_up,
				iconCls	: 'ico-up',
				disabled: true,
				handler	: function(btn){

				}
			},'-',{
				text	: Otm.com_down,
				iconCls	: 'ico-down',
				disabled: true,
				handler	: function(btn){
				}
			},'-',{
				xtype	: 'button',
				text	: Otm.com_import,
				iconCls	: 'ico-import',
				disabled: true,
				handler	: function (btn){

				}
			},'-',{
				xtype	: 'button',
				text	: Otm.com_export,
				iconCls	: 'ico-export',
				disabled: true,
				handler	: function (btn){
					//export_data('otm/riskitem_list_export');
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
		]
	};

	var riskanalysis_riskitem_requirement_link_grid =  {
		region	: 'east',
		layout	: 'fit',
		xtype	: 'gridpanel',
		id		: 'riskanalysis_riskitem_requirement_link_grid',
		title	: '연결된 요구사항',
		flex		: 1,
		store	: riskanalysis_riskitem_requirement_link_store,
		columns	: [
			{header: '요구사항명',		dataIndex: 'req_subject',		flex: 1}
			//,{header: 'ID',		dataIndex: 'oreq_id',	flex: 1, width:50, minWidth:100}
		],
		lbar	: [{
			xtype	: 'button',
			iconCls	: 'arrow_right',
			tooltip	: '선택된 요구사항이 선택된 리스크아이템과 연결됩니다.',
			handler	: function(){
				alert('link');
				var ri_seq = 0;

				var Records = Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.items;
				if(Records.length > 1){
					return;
				}else{
					ri_seq = Records[0].data['ri_seq'];
				}

				var params = {
					ri_seq: ri_seq
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
						riskanalysis_riskitem_requirement_unlink_store.load({params:{'ri_seq':ri_seq}});
						riskanalysis_riskitem_requirement_link_store.load({params:{'ri_seq':ri_seq}});

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

	var riskanalysis_riskitem_tc_store = Ext.create('Ext.data.TreeStore', {
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
				tcplan		: 0
			},
			reader: {
				type: 'json',
				totalProperty: 'totalCount',
				rootProperty: 'data'
			}
		},
		folderSort: true
	});

	var riskanalysis_riskitem_tc_grid =  {
		//region	: 'center',
		layout	: 'fit',
		xtype	: 'gridpanel',
		title	: '테스트 케이스',
		split		: true,
		collapsible	: true,
		collapsed	: false,
		flex		: 1,
		store	: riskanalysis_riskitem_tc_store,
		columns	: [
			{header: '테스트케이스명',		dataIndex: 'oreq_name',		flex: 1,	minWidth:200},
			{header: 'ID',		dataIndex: 'oreq_id',	flex: 1, width:50, minWidth:100}
		],
		tbar	: [{
			xtype	: 'button',
			text	: Otm.com_add,
			iconCls	:'ico-add',
			handler	: function (btn){
				Ext.create('Ext.window.Window', {
					title	: '테스트 케이스 추가',
					height	: 600,
					width	: 400,
					layout	: 'form',
					modal	: true,
					constrainHeader: true,
					items	: [{
						xtype	: 'textfield'
					}],
					buttons:[{
						text:Otm.com_save,
						//formBind: true,
						iconCls:'ico-save',
						handler:function(btn){
							//this.close();
						}
					}]
				}).show();
			}
		}]
	};


	var tab_panel = {//Ext.create('Ext.tab.Panel', {
		region		: 'south',
		//layout		: 'border',
		xtype		: 'tabpanel',
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
			}
		}
	//});
	};

	//---------------------------
	var riskanalysis_riskitem_east_panel = {
		region		: 'east',
		layout		: 'border',
		id			: 'riskanalysis_riskitem_east_panel',
		split		: true,
		collapsible	: true,
		collapsed	: true,
		flex		: 1,
		animation	: false,
		//autoScroll	: true,
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