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
	var riskanalysis_discussion_result_store = Ext.create('Ext.data.Store', {
		fields:['ri_seq', 'riskarea', 'ri_subject', {type:'int',name:'final_point'}, 'risk_req_cnt', 'risk_tc_cnt'],
		pageSize: 50,
		proxy: {
			type	: 'ajax',
			//url		: './index.php/Plugin_view/riskanalysis/riskanalysis_list',
			url		: './index.php/Plugin_view/riskanalysis/riskitem_discussion_result',			
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
		//grouper: {
		//	sortProperty: 'ora_rank',
			//groupFn: function (record) {
			//	return record.get('ora_riskarea');
			//}
		//},
		//remoteGroup:true,
		//remoteSort: true,
		//sortInfo: {field: 'final_point', direction: 'ASC'},
		sorters: [{
			property: 'final_point',
			direction: 'DESC'
		}],
		sortRoot: 'final_point',
		sortOnLoad: true,
		remoteSort: false,

		//groupField: 'riskarea',
		autoLoad:true
	});

	var riskanalysis_discussion_result_center_panel =  {
		region	: 'center',
		layout	: 'fit',
		xtype	: 'gridpanel',
		id		: 'riskanalysis_discussion_result_list',
		multiSelect: false,
		store	: riskanalysis_discussion_result_store,
		features: [{
			ftype: 'grouping',
			startCollapsed: false,
			enableNoGroups:true,
			depthToIndent:20,
			groupHeaderTpl: '{name} ({rows.length} Item{[values.rows.length > 1 ? "s" : ""]})'
		}],
		columns	: [
			{header: '순위',			dataIndex: 'riskarea',align:'center',	width:50},
			{header: '리스크 아이템',	dataIndex: 'ri_subject',	flex: 1,	minWidth:200},
			{header: '점수',			dataIndex: 'final_point',align:'center',	width:100},
			{header: '연결 요구사항',	dataIndex: 'risk_req_cnt',	align:'center',	width:100},
			{header: '연결 TC',			dataIndex: 'risk_tc_cnt',	align:'center',	width:100}
		],
		listeners:{
			scope:this,
			select: function(smObj, record, rowIndex){
				//Ext.getCmp('riskanalysis_discussion_result_east_panel').removeAll();
				var riskanalysis_discussion_result_east_panel = Ext.getCmp('riskanalysis_discussion_result_east_panel');

				if(Ext.getCmp("riskanalysis_discussion_result_list").getSelectionModel().selected.length > 1){
					riskanalysis_discussion_result_east_panel.collapse();
					return;
				}

				//riskanalysis_discussion_result_east_panel.setTitle(Otm.com_view);
				if(riskanalysis_discussion_result_east_panel.collapsed==false){
				}else{
					riskanalysis_discussion_result_east_panel.expand();
				}

				//var obj ={
				//	target : 'riskanalysis_discussion_result_east_panel',
				//	df_seq : record.data.df_seq,
				//	pr_seq : record.data.otm_project_pr_seq
				//};

				return;
			}
		}
	};


	Ext.onReady(function(){
		var main_panel = {
			layout		: 'border',
			defaults	: {
				collapsible	: false,
				split		: false,
				bodyStyle	: 'padding:0px'
			},
			title		: '협의결과',
			items		: [riskanalysis_discussion_result_center_panel]
		};

		Ext.getCmp('riskanalysis_discussion_result').add(main_panel);
		Ext.getCmp('riskanalysis_discussion_result').doLayout(true,false);
	});

</script>