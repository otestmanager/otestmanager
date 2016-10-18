<?php
/**
 * @copyright Copyright STA
 * Created on 2016. 04.
 * @author STA <otm@sta.co.kr>
 */

include_once($data['skin_dir'].'/'.$data['module_directory'].'_common.php');
?>
<script type="text/javascript">
	/**
	* Center Panel
	*/
	var riskanalysis_discussion_store = Ext.create('Ext.data.Store', {
		//fields:['ora_name', 'ora_status'],
		fields:['ri_seq','ri_subject','link_req_cnt','link_tc_cnt'],
		pageSize: 50,
		proxy: {
			type	: 'ajax',
			//url		: './index.php/Plugin_view/riskanalysis/riskanalysis_list',
			//url		: './index.php/Plugin_view/riskanalysis/riskitem_list',
			url		: './index.php/Plugin_view/riskanalysis/riskitem_discussion',			
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

	var likelihood_store = Ext.create('Ext.data.Store', {		
		fields:['pco_seq','pco_type','pco_name','pco_is_required','pco_is_default','pco_position','pco_default_value','pco_color','pco_is_use'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/riskanalysis/code_list',
			extraParams: {pr_seq : project_seq, type : 'likelihood'},
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

	var impact_store = Ext.create('Ext.data.Store', {		
		fields:['pco_seq','pco_type','pco_name','pco_is_required','pco_is_default','pco_position','pco_default_value','pco_color','pco_is_use'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/riskanalysis/code_list',
			extraParams: {pr_seq : project_seq, type : 'impact'},
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

	var riskpoint_store = Ext.create('Ext.data.Store', {
		//fields:['ora_code_seq','ora_code_name'],
		fields:['pco_seq','pco_type','pco_name','pco_is_required','pco_is_default','pco_position','pco_default_value','pco_color','pco_is_use'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/riskanalysis/code_list',//'./index.php/Plugin_view/riskanalysis/riskpoint_list',
			extraParams: {pr_seq : project_seq, type : 'riskpoint'},
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

	riskpoint_store.on('load',function(){
		//console.log('riskpoint_store load');
		likelihood_store.load();	
	});


	/*
	var likelihood_columns = [{
		header: '팩터1',		dataIndex: 'orf_1',	align:'center',	width:100,
			editor:{
				xtype: 'combobox',
				store: riskpoint_store,
				displayField: 'pco_name',
				valueField: 'pco_seq'
			}
		},
		{header: '팩터2',		dataIndex: 'orf_2',	align:'center',	width:100,
			editor:{
				xtype: 'combobox',
				store: riskpoint_store,
				displayField: 'pco_name',
				valueField: 'pco_seq'
			}
		}];
	var impact_columns = [{
		header: '팩터3',		dataIndex: 'orf_3',	align:'center',	width:100,
			editor:{
				xtype: 'combobox',
				store: riskpoint_store,
				displayField: 'pco_name',
				valueField: 'pco_seq'
			}
		},
		{header: '팩터4',		dataIndex: 'orf_4',	align:'center',	width:100,
			editor:{
				xtype: 'combobox',
				store: riskpoint_store,
				displayField: 'pco_name',
				valueField: 'pco_seq'
			}
		}];
	*/

	var likelihood_columns = new Array();
	var impact_columns = new Array();

	likelihood_store.on('load',function(storeObj,option){

		//console.log('likelihood_store load');
		impact_store.load();
				
		for(var i=0; i<option.length; i++){
			likelihood_columns.push({
				header: option[i].data.pco_name, dataIndex: option[i].data.pco_type+'_'+option[i].data.pco_seq,	align:'center',	width:100,
					editor:{
						xtype: 'combobox',
						queryParam: 'q',
						queryMode: 'local',
						editable: false,
						store: riskpoint_store,
						displayField: 'pco_name',
						valueField: 'pco_seq'
					},
					renderer: function(value, label, storeItem) {
						riskpoint_store.findBy(function(record) {
							if (record.get('pco_seq') === value) {
								value = record.get('pco_name');
								return true; // findby
							}                        
						});    
						return value;						
					}
				});
		}
	});

	impact_store.on('load',function(storeObj,option){
		//console.log('impact_store load');

		for(var i=0; i<option.length; i++){
			impact_columns.push({
				header: option[i].data.pco_name, dataIndex: option[i].data.pco_type+'_'+option[i].data.pco_seq,	align:'center',	width:100,
					editor:{
						xtype: 'combobox',
						queryParam: 'q',
						queryMode: 'local',
						editable: false,
						store: riskpoint_store,
						displayField: 'pco_name',
						valueField: 'pco_seq'
					},
					renderer: function(value, label, storeItem) {
						riskpoint_store.findBy(function(record) {
							if (record.get('pco_seq') === value) {
								value = record.get('pco_name');
								return true; // findby
							}                        
						});    
						return value;						
					}
				});
		}
		
		//console.log(riskpoint_store.data.length);
		if(likelihood_columns.length < 1 || impact_columns.length < 1 || riskpoint_store.data.length < 1){
			Ext.Msg.alert('OTM','설정 값이 없습니다.');
			return;
		}

		var riskanalysis_discussion_center_panel =  {
			region	: 'center',
			layout	: 'fit',
			xtype	: 'gridpanel',
			id		: 'riskanalysis_discussion_list',
			multiSelect: true,
			store	: riskanalysis_discussion_store,
			plugins: [
				Ext.create('Ext.grid.plugin.CellEditing', {
					clicksToEdit: 1
				})
			],
			columns	: [
				{header: '리스크 아이템',		dataIndex: 'ri_subject',	locked:true,	flex: 1,	minWidth:80},
				{header: '리스크 팩터(likelihood)', dataIndex: '',locked:true, align:'center', sortable: true,
					columns:likelihood_columns
				},
				{header: '리스크 팩터(impact)', dataIndex: '',locked:true, align:'center', sortable: true,
					columns:impact_columns
				}
			],
			listeners:{
				scope:this,
				select: function(smObj, record, rowIndex){
				}
			},
			tbar	: [{
				xtype	: 'button',
				text:Otm.com_save,				
				iconCls:'ico-save',
				handler:function(btn){
					//console.log(riskanalysis_discussion_store);
					//console.log(riskanalysis_discussion_store.data.items);
					var update_items = new Array();
					var store_items = riskanalysis_discussion_store.data.items;
					for(var i=0; i<store_items.length; i++){
						if(store_items[i].dirty){
							update_items.push(store_items[i].data);
						}
					}
				
					if(update_items.length < 1){
						Ext.Msg.alert('OTM','변경된 값이 없습니다.');
						return;
					}else{
						console.log(Ext.encode(update_items));
						Ext.Ajax.request({
							url : './index.php/Plugin_view/riskanalysis/riskitem_discussion_save',
							params :{
								pr_seq		: project_seq,
								save_list	: Ext.encode(update_items)
							},
							method: 'POST',
							success: function ( result, request ) {
								Ext.Msg.alert('OTM','SAVE DATA');							
							},
							failure: function ( result, request ) {
								
							}
						});
					}
				}			
			}]
		};

		Ext.getCmp('riskanalysis_discussion_main_panel').add(riskanalysis_discussion_center_panel);
	
	});


	Ext.onReady(function(){
		var main_panel = {
			layout		: 'border',
			defaults	: {
				collapsible	: false,
				split		: false,
				bodyStyle	: 'padding:0px'
			},
			title		: '분석협의',
			id			: 'riskanalysis_discussion_main_panel',
			items		: []//[riskanalysis_discussion_center_panel]
		};

		Ext.getCmp('riskanalysis_discussion').add(main_panel);
		Ext.getCmp('riskanalysis_discussion').doLayout(true,false);
	});

</script>