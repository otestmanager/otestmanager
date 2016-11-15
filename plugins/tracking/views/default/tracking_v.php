<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */

include_once $data['skin_dir'].'/locale-'.$data['mb_lang'].'.php';
?>
<script type="text/javascript">

var project_seq = <?=$project_seq?>;
var tracking_select_type = 'common';	/*선택 기준 : 공통 목록, 전체 목록*/
var tracking_view_panel_open = 'open';	/*상세보기 열기/닫기*/
var view_east_south = 'east';			/*상세보기 우측/아래 보기*/
var view_east_south_hidden = false;		/*상세보기 우측/아래 보기 기능 (비)활성화*/

/**
*	Common Function
*/
function getMap(select_cnt, arr)
{
	var return_arr = Array();

	var resultMap = Array();
	for(var i in arr)
	{
		if(!(arr[i] in resultMap))
			resultMap[arr[i]] = [];
		resultMap[arr[i]].push(arr[i]);
	}

	for(var i in resultMap)
	{
		if(resultMap[i].length >= select_cnt){
			return_arr.push(resultMap[i][0]);
		}
	}

	return return_arr;
}

function select_link_items(type)
{
	var target = Ext.getCmp('tracking_view_target').getValue();

	if(target != type){
		return;
	}

	if(target == 'testcase'){
		var temp_arry = Array();
		var select = Ext.getCmp('tracking_testcaseGrid').getSelectionModel().selected;

		if(select.length >= 1){
			var selItem = select.items;
			for(var i=0; i<select.length; i++){
				if(selItem[i].data.result_df_link){
					var fieldvalues = selItem[i].data.result_df_link;
					var arr = fieldvalues.split(',');
					if(arr.length >0){
						for(var j=0; j<arr.length; j++){
							if(arr[j] > 0)
							temp_arry.push(arr[j]);
						}
					}
				}
			}
		}else{
			return;
		}

		if(Ext.getCmp('tracking_select_type').getValue() == 'common'){
			temp_arry = getMap(select.length,temp_arry);
		}else{
		}

		var grid = Ext.getCmp('tracking_defectGrid');
		var map = grid.store.data.map;
		var records = Array();
		if(temp_arry.length > 0){

			var cnt = 1;
			for (var key in map) {
				var tmp_records = Ext.Array.filter(
					grid.store.data.map[key].value,
					function(r) {
						return temp_arry.indexOf(''+r.get('df_seq')) !== -1;
					}
				);
				cnt++;
				for(var i=0; i<tmp_records.length; i++){
					records.push(tmp_records[i]);
				}
			}

			var rowIndex = grid.store.find('df_seq', records[records.length - 1].data.df_seq);

			grid.getPlugin('bufferedrenderer').scrollTo(rowIndex);
			grid.getSelectionModel().select(records);
		}else{
			grid.getSelectionModel().deselectAll()
		}

		if(temp_arry.length > 1){
			Ext.getCmp('tracking_defect_east_panel').removeAll();
		}
		return;
	}else if(target == 'defect'){
		var temp_arry = Array();
		var select = Ext.getCmp('tracking_defectGrid').getSelectionModel().selected;

		if(select.length >= 1){
			var selItem = select.items;
			for(var i=0; i<select.length; i++){
				if(selItem[i].data.result_tl_link){
					var fieldvalues = selItem[i].data.result_tl_link;
					var arr = fieldvalues.split(',');
					for(var j=0; j<arr.length; j++){
						temp_arry.push(arr[j]);
					}
				}
			}
		}

		if(Ext.getCmp('tracking_select_type').getValue() == 'common'){
			temp_arry = getMap(select.length,temp_arry);
		}else{
		}

		var grid = Ext.getCmp('tracking_testcaseGrid');

		if(temp_arry.length > 0){
			var records = Ext.Array.filter(
				grid.store.data.items,
				function(r) {
					return temp_arry.indexOf(''+r.get('tl_seq')) !== -1;
				}
			);
			grid.getSelectionModel().select(records);
		}else{
			grid.getSelectionModel().deselectAll()
		}

		if(temp_arry.length > 1){
			if(Ext.getCmp('tracking_testcase_east_view_panel')){
				Ext.getCmp('tracking_testcase_east_view_panel').removeAll();
			}
		}
		return;
	}else{
	}
}

function get_select_data()
{
	var temp_tc_arry = Array();
	var select_tc = Ext.getCmp('tracking_testcaseGrid').getSelectionModel().selected;
	if(select_tc.length >= 1){
		var selItem = select_tc.items;
		for(var i=0; i<select_tc.length; i++){
			temp_tc_arry.push(selItem[i].data.tl_seq);
		}
	}

	var temp_df_arry = Array();
	var select_df = Ext.getCmp('tracking_defectGrid').getSelectionModel().selected;
	if(select_df.length >= 1){
		var selItem = select_df.items;
		for(var i=0; i<select_df.length; i++){
			temp_df_arry.push(selItem[i].data.df_seq);
		}
	}

	var result_value = '';
	var result_msg = '';
	if(Ext.getCmp('tracking_testcase_east_result')){
		var select_result = Ext.getCmp('tracking_testcase_east_result').getChecked();
		result_value  = select_result[0].inputValue;
		result_msg	= Ext.getCmp('tracking_testcase_east_result_content').getValue();
	}


	var obj = {
		tl_seq		: Ext.encode(temp_tc_arry),
		df_seq		: Ext.encode(temp_df_arry),
		result_value: result_value,
		result_msg	: result_msg
	};

	return obj;
}

function set_link(obj)
{
	Ext.Msg.confirm('OTM',Otm.tracking.msg_connect_confirm,function(bt){
		if(bt=='yes'){
			Ext.Ajax.request({
				url : './index.php/Plugin_view/tracking/set_link',
				params :{
					pr_seq : project_seq,
					tl_seq : obj.tl_seq,
					df_seq : obj.df_seq,
					result_value : obj.result_value,
					result_msg : obj.result_msg
				},
				method: 'POST',
				success: function ( result, request ) {
					if(result.responseText){
						var params = {
							params:{
								project_seq : project_seq,
								tcplan		: 'tcplan_'+Ext.getCmp('tracking_testcase_plan_combo').getValue()
							}
						};
						Ext.getCmp('tracking_testcaseGrid').getStore().proxy.extraParams.tcplan = 'tcplan_'+Ext.getCmp('tracking_testcase_plan_combo').getValue();
						Ext.getCmp('tracking_testcaseGrid').getStore().load(params);

						Ext.getCmp('tracking_defectGrid').getStore().load();

						if(Ext.getCmp('tracking_testcase_east_execute_panel')){
							Ext.getCmp('tracking_testcase_east_execute_panel').reset();
						}

						Ext.getCmp('tracking_defect_east_panel').removeAll();
						if(Ext.getCmp('tracking_testcase_east_view_panel'))
							Ext.getCmp('tracking_testcase_east_view_panel').removeAll();
					}
				},
				failure: function ( result, request ) {
					Ext.getCmp('dashboard').unmask();
					Ext.Msg.alert('OTM','DataBase Select Error');
				}
			});
		}else{
			return;
		}
	});
}

function set_unlink(obj)
{
	Ext.Msg.confirm('OTM',Otm.tracking.msg_disconnect_confirm,function(bt){
		if(bt=='yes'){
		Ext.Ajax.request({
			url : './index.php/Plugin_view/tracking/set_unlink',
			params :{
				pr_seq : project_seq,
				tl_seq : obj.tl_seq,
				df_seq : obj.df_seq
			},
			method: 'POST',
			success: function ( result, request ) {
				if(result.responseText){
					var params = {
						params:{
							project_seq : project_seq,
							tcplan		: 'tcplan_'+Ext.getCmp('tracking_testcase_plan_combo').getValue()
						}
					};
					Ext.getCmp('tracking_testcaseGrid').getStore().proxy.extraParams.tcplan = 'tcplan_'+Ext.getCmp('tracking_testcase_plan_combo').getValue();
					Ext.getCmp('tracking_testcaseGrid').getStore().load(params);

					Ext.getCmp('tracking_defectGrid').getStore().load();//.loadPage(1);

					if(Ext.getCmp('tracking_testcase_east_execute_panel')){
						Ext.getCmp('tracking_testcase_east_execute_panel').reset();
					}

					Ext.getCmp('tracking_defect_east_panel').removeAll();
					if(Ext.getCmp('tracking_testcase_east_view_panel'))
						Ext.getCmp('tracking_testcase_east_view_panel').removeAll();
				}
			},
			failure: function ( result, request ) {
				Ext.getCmp('dashboard').unmask();
				Ext.Msg.alert('OTM','DataBase Select Error');
			}
		});
		}
	});
}
/**
*	Common Function
*		END
*/


/**
*	Store Load
*/
var defect_customform_store = Ext.create('Ext.data.Store', {
	fields:['pc_seq','pc_name','pc_is_required','pc_formtype','pc_default_value','pc_content','pc_is_use'],
    proxy: {
		type	: 'ajax',
		url		: './index.php/Project_setup/userform_list',
		extraParams: {
			pr_seq		: project_seq,
			pc_category : 'ID_DEFECT',
			pc_is_use	: 'Y'
		},
		actionMethods : {create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'},
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
    },
	autoLoad:true
});

var testcase_customform_store = Ext.create('Ext.data.Store', {
	fields:['pc_seq','pc_name','pc_is_required','pc_formtype','pc_default_value','pc_content','pc_is_use'],
    proxy: {
		type	: 'ajax',
		url		: './index.php/Project_setup/userform_list',
		extraParams: {
			pr_seq		: project_seq,
			pc_category : 'ID_TC',
			pc_is_use	: 'Y'
		},
		actionMethods : {create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'},
		reader: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
    },
	autoLoad:true
});

var tracking_testcase_plan_combo_store = Ext.create('Ext.data.Store', {
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

tracking_testcase_plan_combo_store.on('load',function(){
	if(tracking_testcase_plan_combo_store.getCount() < 1){
		Ext.Msg.alert('OTM',Otm.tracking.msg_select_plan);
	}
});


var tracking_defect_store = Ext.create('Ext.data.BufferedStore',{
	pageSize : 100000,
	autoLoad : false,
	fields:['df_seq','df_id','df_subject','df_status','df_severity','df_priority','df_frequency','df_id','df_assign_member','writer_name','otm_testcase_result_tr_seq','result_tl_link','status_name','severity_name','priority_name','frequency_name'],
	proxy: {
		loadmask		: false,
		type			: 'ajax',
		url				: './index.php/Plugin_view/tracking/defect_list',
		extraParams		: {
			project_seq : project_seq
		},
		actionMethods	: {
			create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
		},
		reader			: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
	}
});

tracking_defect_store.on('load',function(){
	if(Ext.getCmp('tracking_defect_east_panel'))
		Ext.getCmp('tracking_defect_east_panel').removeAll();
});

var tracking_testcase_store = Ext.create('Ext.data.BufferedStore',{
	pageSize : 100000,
	fields:['tc_seq','location','tc_id','subject','writer_name','regdate','result_df_link','tracking_id'],
	proxy	: {
		loadmask		: false,
		type			: 'ajax',
		url				: './index.php/Plugin_view/tracking/testcase_list',
		extraParams		: {
			project_seq : project_seq,
			tcplan		: ''
		},
		actionMethods	: {create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'},
		reader			: {
			type: 'json',
			totalProperty: 'totalCount',
			rootProperty: 'data'
		}
	},
	autoLoad: false
});

tracking_testcase_store.on('load',function(){
	if(Ext.getCmp('tracking_testcase_east_view_panel'))
		Ext.getCmp('tracking_testcase_east_view_panel').removeAll();
});
/**
*	Store Load
*		END
*/

/**
*	Grid Columns
*/
var tracking_defectGrid_column = [{
		text: Otm.com_number,
		dataIndex: 'df_seq',align:'center',
		hidden:true,
		width:50,
	},{
		text: 'ID',
		dataIndex: 'df_id',
		width: 50
	},{
		text: Otm.com_subject,
		dataIndex: 'df_subject',
		flex:1,
		width: 500
	},{
		text: 'TC '+Otm.tracking.link_count,
		dataIndex: 'tracking_id',align:'center',autoResizeWidth: true,
		width: 50,
		renderer: function(value, metaData, record, rowIndex, colIndex, store){
			if(value){
				var id_array  = value.split(',');
				return id_array.length;
			}else{
				return '';
			}
		}
	},{
		text: Otm.com_user,
		dataIndex: 'df_assign_member',align:'center',
		width: 50
	},{
		text: Otm.com_creator,
		dataIndex: 'writer_name',align:'center',
		width: 50
	},{
		text: Otm.def_status,
		dataIndex: 'status_name',
		width: 80,
		renderer: function(value, metaData, record, rowIndex, colIndex, store){

			return value;
		}
	},{
		text: Otm.def_severity,
		dataIndex: 'severity_name',
		width: 80,
		renderer: function(value, metaData, record, rowIndex, colIndex, store){

			return value;
		}
	},{
		text: Otm.def_priority,
		dataIndex: 'priority_name',
		width: 80,
		renderer: function(value, metaData, record, rowIndex, colIndex, store){

			return value;
		}
	},{
		text: Otm.def_frequency,
		dataIndex: 'frequency_name',
		width:80,
		renderer: function(value, metaData, record, rowIndex, colIndex, store){

			return value;
		}
	},{
		text: Otm.com_date, dataIndex: 'regdate', align:'center',
		width:80, sortable: true, renderer:function(value,index,record){
			if(value){
				var value = value.substr(0,10);
			}else{
				value = '';
			}
			return value;
		}
	}
];

/**
*	TestCase Columns
*/
var tracking_testcaseGrid_column = [
	{
		text: Otm.com_number,
		dataIndex: 'tc_seq',align:'center',
		hidden:true,
		width:50,
	},{
		text: 'location',
		dataIndex: 'location',
		width: 50
	},{
		text: 'ID',
		dataIndex: 'tc_id',
		width: 50
	},{
		text: Otm.com_subject,
		dataIndex: 'subject',
		flex:1,
		width: 500
	},{
		text: Otm.def+' '+Otm.tracking.link_count,
		dataIndex: 'tracking_id',align:'center',
		width: 50,
		renderer: function(value, metaData, record, rowIndex, colIndex, store){
			if(value){
				var id_array  = value.split(',');
				return id_array.length;
			}else{
				return '';
			}
		}
	},{
		text: Otm.com_creator,
		dataIndex: 'writer_name',align:'center',
		width: 50
	},{
		text: Otm.com_date, dataIndex: 'regdate', align:'center',
		width:80, sortable: true, renderer:function(value,index,record){
			if(value){
				var value = value.substr(0,10);
			}else{
				value = '';
			}
			return value;
		}
	}
];

defect_customform_store.load({
	callback: function(r,options,success){
		for(var i=0;i<r.length;i++){
			var add_chk = true;
			for(var key in tracking_defectGrid_column){
				if(tracking_defectGrid_column[key].dataIndex == "_"+r[i].data.pc_seq){
					add_chk = false;
					break;
				}
			}
			if(add_chk){
				if(r[i].data.pc_is_display == 'Y'){
					tracking_defectGrid_column.push({
						 header: r[i].data.pc_name,  dataIndex: "_"+r[i].data.pc_seq, align:'center'
					});
				}
			}
		}

		Ext.getCmp('tracking_defectGrid').reconfigure(undefined,tracking_defectGrid_column);
	}
});

testcase_customform_store.load({
	callback: function(r,options,success){
		for(var i=0;i<r.length;i++){
			var add_chk = true;
			for(var key in tracking_testcaseGrid_column){
				if(tracking_testcaseGrid_column[key].dataIndex == "_"+r[i].data.pc_seq){
					add_chk = false;
					break;
				}
			}
			if(add_chk){
				if(r[i].data.pc_is_display == 'Y'){
					tracking_testcaseGrid_column.push({
						 header: r[i].data.pc_name,  dataIndex: "_"+r[i].data.pc_seq, align:'center'
					});
				}
			}
		}

		Ext.getCmp('tracking_testcaseGrid').reconfigure(undefined,tracking_testcaseGrid_column);
	}
});
/**
*	Grid Columns
*		END
*/

/**
*	TestCase Function
*/
function get_tracking_testcase_view_form(obj)
{
	if(Ext.getCmp('tracking_testcase_east_panel')){
	}else{
		return;
	}

	if(Ext.getCmp('tracking_testcaseGrid').getSelectionModel().selected.length > 1){
		if(Ext.getCmp('tracking_testcase_east_view_panel')){
			Ext.getCmp('tracking_testcase_east_view_panel').removeAll();
		}
		Ext.getCmp('tracking_testcase_east_panel').collapse();
		return;
	}

	if(Ext.getCmp('tracking_testcase_east_view_panel')){
		Ext.getCmp('tracking_testcase_east_view_panel').removeAll('');
	}else{
		Ext.getCmp('tracking_testcase_east_panel').add({
			region	: 'center', layout:'fit', xtype:'panel',
			animation: false, autoScroll: true,
			id:'tracking_testcase_east_view_panel'
		});
	}

	var tracking_testcase_east_panel = Ext.getCmp('tracking_testcase_east_panel');
	if(tracking_testcase_east_panel.collapsed==false){
	}else{
		tracking_testcase_east_panel.expand();
	}

	if(Ext.getCmp('tracking_testcase_east_execute_panel')){
	}else{
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
							name : 'tracking_testcase_east_result_value',
							checked:(code.data[i].pco_is_default=='Y')?true:false
						});
					}
					var Records = Ext.getCmp('tracking_testcaseGrid').getSelectionModel().selected.items;

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

					Ext.getCmp('tracking_testcase_east_panel').add({
						region: 'south', xtype: 'form',
						title: Otm.tc_execution, id:'tracking_testcase_east_execute_panel',
						animate: false, autoScroll: false, collapsible: false, collapsed: false, animCollapse: false, disabled:form_disable,
						height: 150, frame:true, bodyStyle:'padding:5px;',
						items:[{
								xtype		: 'radiogroup',
								fieldLabel	: Otm.tc_execution_result,
								id			: 'tracking_testcase_east_result',
								anchor		:'100%',
								columns		: 2,
								allowBlank	: false,
								items		: checkbox
							},{
								layout		:'fit',
								xtype		:'textarea',
								id			:'tracking_testcase_east_result_content',
								anchor		:'100%',
								height		: 50
							}
						]
					});
				}
			},
			failure: function ( result, request ) {
				Ext.getCmp('dashboard').unmask();
				Ext.Msg.alert('OTM','DataBase Select Error');
			}
		});
	}

	obj.target = 'tracking_testcase_east_view_panel';
	get_testcase_view_panel(obj);
	return;
}

function tracking_testcase_panel()
{
	var tracking_testcase_plan_combo = Ext.create('Ext.form.ComboBox', {
		anchor			: '100%',
		fieldLabel		: Otm.tracking.plan_select,
		id				: 'tracking_testcase_plan_combo',
		triggerAction	: 'all',
		forceSelection	: true,
		editable		: false,
		displayField	: 'text',
		valueField		: 'tp_seq',
		queryMode		: 'local',
		store			: tracking_testcase_plan_combo_store,
		listeners: {
			select: function(combo, record, index) {
				var params = {
					params:{
						project_seq : project_seq,
						tcplan		: 'tcplan_'+combo.getValue()
					}
				};
				Ext.getCmp('tracking_testcaseGrid').getStore().proxy.extraParams.tcplan = 'tcplan_'+combo.getValue();
				Ext.getCmp('tracking_testcaseGrid').getStore().load(params);
			}
		}
	});

	var tracking_testcaseGrid_listener = {
		scope	:this,
		deselect: function(smObj, record, rowIndex) {
			select_link_items('testcase');
			if(Ext.getCmp('tracking_testcase_east_view_panel'))
				Ext.getCmp('tracking_testcase_east_view_panel').removeAll()
		},
		select	: function(smObj, record, rowIndex) {
			select_link_items('testcase');

			if(Ext.getCmp('tracking_view_panel_open').getValue() == 'close') return;

			var obj = {
				form_type : (record.get('type') == 'folder')?'suite':'case',
				id : record.get('id'),
				pid : record.get('pid'),
				tl_seq : record.get('tl_seq'),
				pr_seq : project_seq
			};

			get_tracking_testcase_view_form(obj);
		}
	};

	var tracking_testcaseGrid = Ext.create("Ext.grid.Panel",{
		region		: 'center',
		id			: 'tracking_testcaseGrid',
		store		: tracking_testcase_store,
		plugins: [{
			ptype: 'bufferedrenderer',
			pluginId: 'bufferedrenderer',
			variableRowHeight: true
		}],
		border		: false,
		forceFit	: true,
		selModel	: Ext.create('Ext.selection.CheckboxModel'),
		columns		: tracking_testcaseGrid_column,
		tbar		: [tracking_testcase_plan_combo],
		listeners	: tracking_testcaseGrid_listener
	});

	var tracking_testcase_center_panel = {
		region		: 'center',
		layout		: 'fit',
		xtype		: 'panel',
		title		: Otm.tc+' '+Otm.tracking.list,
		id			: 'tracking_testcase_center_panel',
		animation	: false,
		autoScroll	: true,
		items		: [tracking_testcaseGrid]
	};

	var tracking_testcase_east_panel = {
		region		: view_east_south,
		layout		: 'border',
		xtype		: 'panel',
		title		: Otm.tc+' '+Otm.tracking.detail_view,
		id			: 'tracking_testcase_east_panel',
		hidden		: (tracking_view_panel_open=='open')?false:true,
		split		: false,
		collapsible	: true,
		collapsed	: true,
		flex		: 1,
		animation	: false,
		autoScroll	: true,
		items:[]
	};

	var panel = {
		region		: 'center',
		layout		: 'border',
		id			: 'tracking_testcase_panel',
		collapsible	: false,
		plain		: true,
		animation	: false,
		autoScroll	: false,
		items: [tracking_testcase_center_panel, tracking_testcase_east_panel]
	};

	return panel;
}
/**
*	TestCase Function
*		END
*/


/**
*	Defect Function
*/
function get_tracking_defect_view_form(obj)
{
	if(Ext.getCmp('tracking_defect_east_panel')){
	}else{
		return;
	}

	Ext.getCmp('tracking_defect_east_panel').removeAll();
	if(Ext.getCmp('tracking_defectGrid').getSelectionModel().selected.length > 1){
		Ext.getCmp('tracking_defect_east_panel').collapse();
		return;
	}else{
		obj.target = 'tracking_defect_east_panel';
		get_defect_view_panel(obj);

		Ext.getCmp('tracking_defect_east_panel').expand();
	}
}

function tracking_defect_panel()
{
	var tracking_defectGrid_listener = {
		scope:this,
		deselect: function(smObj, record, rowIndex) {
			select_link_items('defect');
			Ext.getCmp('tracking_defect_east_panel').removeAll();
		},
		select	: function(smObj, record, rowIndex) {
			select_link_items('defect');

			if(Ext.getCmp('tracking_view_panel_open').getValue() == 'close') return;

			var obj = {
				df_seq : record.data.df_seq,
				pr_seq : record.data.otm_project_pr_seq
			};

			get_tracking_defect_view_form(obj);
		}
	};

	var tracking_defectGrid = Ext.create("Ext.grid.Panel",{
		region		: 'center',
		id			: 'tracking_defectGrid',
		store		: tracking_defect_store,
		plugins: [{
			ptype: 'bufferedrenderer',
			pluginId: 'bufferedrenderer',
			variableRowHeight: true
		}],
		border		: false,
		forceFit	: true,
		selModel	: Ext.create('Ext.selection.CheckboxModel'),
		columns		: tracking_defectGrid_column,
		listeners	: tracking_defectGrid_listener
	});

	var tracking_defect_center_panel = {
		region		: 'center',
		layout		: 'fit',
		xtype		: 'panel',
		title		: Otm.def+' '+Otm.tracking.list,
		id			: 'tracking_defect_center_panel',
		animation	: false,
		autoScroll	: true,
		items		: [tracking_defectGrid]
	};

	var tracking_defect_east_panel = {
		region		: view_east_south,
		layout		: 'fit',
		xtype		: 'panel',
		title		: Otm.def+' '+Otm.tracking.detail_view,
		id			: 'tracking_defect_east_panel',
		hidden		: (tracking_view_panel_open=='open')?false:true,
		split		: false,
		collapsible	: true,
		collapsed	: true,
		flex		: 1,
		animation	: false,
		autoScroll	: true,
		items:[]
	};

	var panel = {
		region		: 'center',
		layout		: 'border',
		id			: 'tracking_defect_panel',
		collapsible	: false,
		plain		: true,
		animation	: false,
		autoScroll	: false,
		items: [tracking_defect_center_panel, tracking_defect_east_panel]
	};

	return panel;
}
/**
*	Defect Function
*		END
*/


/**
*	Center Panel
*/
var tracking_center_panel = {
	region		: 'center',
	layout		: 'border',
	id			: 'tracking_center_panel',
	collapsible	: false,
	plain		: true,
	animation	: false,
	autoScroll	: false,
	items: [tracking_testcase_panel()]
};

/**
*	East Panel
*/
var tracking_east_panel = {
	region		: 'east',
	layout		: 'border',
	id			: 'tracking_east_panel',
	split		: false,
	collapsible	: false,
	collapsed	: false,
	flex		: 1,
	animation	: false,
	autoScroll	: false,
	items:[tracking_defect_panel()]
};


Ext.onReady(function(){

	var main_panel = {
		layout		: 'border',
		id			: 'tracking_main_panel',
		defaults	: {
			collapsible	: false,
			split		: true,
			bodyStyle	: 'padding:0px'
		},
		items		: [tracking_center_panel,tracking_east_panel],
		tbar : [
			{
				id			: 'tracking_view_target',
				xtype		: 'combo',
				width		: 130,
				editable	: false,
				displayField: 'Name',
				valueField	: 'Key',
				store		: new Ext.data.SimpleStore({
					fields:['Key', 'Name'],
					data:[['testcase', Otm.tracking.base_tc],['defect', Otm.tracking.base_def]]
				}),
				minChars	: 0,
				allowBlank	: false,
				queryParam	: 'q',
				queryMode	: 'local',
				value		: 'testcase',
				listeners:{
					select	: function(combo,event,eOpts)
					{
						Ext.getCmp('tracking_center_panel').removeAll();
						Ext.getCmp('tracking_east_panel').removeAll();

						switch(combo.getValue())
						{
							case "testcase":
								Ext.getCmp('tracking_center_panel').add(tracking_testcase_panel());
								Ext.getCmp('tracking_east_panel').add(tracking_defect_panel());
								break;
							case "defect":
								Ext.getCmp('tracking_center_panel').add(tracking_defect_panel());
								Ext.getCmp('tracking_east_panel').add(tracking_testcase_panel());
								break;
						}

						if(Ext.getCmp('tracking_testcaseGrid').getStore().proxy.extraParams.tcplan){
							var tp_seq = Ext.getCmp('tracking_testcaseGrid').getStore().proxy.extraParams.tcplan.split('_');
							Ext.getCmp('tracking_testcase_plan_combo').setValue(tp_seq);
						}
					},
					blur	: function(combo,event,eOpts)
					{
						if(combo.lastSelection.length != 1){
						}
					},
					focus	: function(combo,event,eOpts)
					{
					}
				}
			},{
				id			: 'tracking_select_type',
				xtype		: 'combo',
				width		: 130,
				editable	: false,
				displayField: 'Name',
				valueField	: 'Key',
				store		: new Ext.data.SimpleStore({
					fields:['Key', 'Name'],
					data:[['common', Otm.tracking.list_com],['all', Otm.tracking.list_all]]
				}),
				minChars	: 0,
				allowBlank	: false,
				queryParam	: 'q',
				queryMode	: 'local',
				value		: tracking_select_type,
				listeners:{
					select	: function(combo,event,eOpts)
					{
						tracking_select_type = combo.getValue();

						var target = Ext.getCmp('tracking_view_target').getValue();
						select_link_items(target);
					}
				}
			},{
				hidden		: view_east_south_hidden,
				id			: 'view_east_south',
				xtype		: 'combo',
				width		: 130,
				editable	: false,
				displayField: 'Name',
				valueField	: 'Key',
				store		: new Ext.data.SimpleStore({
					fields:['Key', 'Name'],
					data:[['east', Otm.tracking.detail_view_right],['south', Otm.tracking.detail_view_bottom]]
				}),
				minChars	: 0,
				allowBlank	: false,
				queryParam	: 'q',
				queryMode	: 'local',
				value		: view_east_south,
				listeners:{
					select	: function(combo,event,eOpts)
					{
						view_east_south = combo.getValue();
						switch(combo.getValue())
						{
							case "east":
								Ext.getCmp('tracking_testcase_east_panel').setRegion('east');
								Ext.getCmp('tracking_defect_east_panel').setRegion('east');

								Ext.getCmp('tracking_testcase_panel').doLayout();
								Ext.getCmp('tracking_defect_panel').doLayout();
								break;
							case "south":
								Ext.getCmp('tracking_testcase_east_panel').setRegion('south');
								Ext.getCmp('tracking_defect_east_panel').setRegion('south');

								Ext.getCmp('tracking_testcase_panel').doLayout();
								Ext.getCmp('tracking_defect_panel').doLayout();
								break;
						}
					}
				}
			}
		],
		bbar : [
			{
				id			: 'tracking_view_panel_open',
				xtype		: 'combo',
				width		: 130,
				editable	: false,
				displayField: 'Name',
				valueField	: 'Key',
				store		: new Ext.data.SimpleStore({
					fields:['Key', 'Name'],
					data:[['open', Otm.com_default_panel_open],['close', Otm.com_default_panel_close]]
				}),
				minChars	: 0,
				allowBlank	: false,
				queryParam	: 'q',
				queryMode	: 'local',
				value		: 'open',
				listeners:{
					select	: function(combo,event,eOpts)
					{
						tracking_view_panel_open = combo.getValue();
						switch(combo.getValue())
						{
							case "open":
								Ext.getCmp('tracking_testcase_east_panel').show();
								Ext.getCmp('tracking_defect_east_panel').show();
								break;
							case "close":
								Ext.getCmp('tracking_testcase_east_panel').hide();
								Ext.getCmp('tracking_defect_east_panel').hide();
								break;
						}
					}
				}
			},'->',{
				xtype	: 'button',
				style	: 'margin-left:3px;',
				text	: Otm.tracking.link,
				iconCls	: 'ico-link',
				disabled: (check_role('defect_edit_all') && check_role('tc_edit_all'))?false:true,
				handler	: function(btn){
					var obj = get_select_data();

					if(Ext.decode(obj.tl_seq).length < 1){
						Ext.Msg.alert('OTM',Otm.tracking.msg_select_tc);
						return;
					}else if(Ext.decode(obj.df_seq).length < 1){
						Ext.Msg.alert('OTM',Otm.tracking.msg_select_df);
						return;
					}

					if(!obj.result_msg || obj.result_msg == ''){
						Ext.Msg.confirm('OTM',Otm.tracking.msg_no_descriotion_connect_confirm,function(bt){
							if(bt=='yes'){
								set_link(obj);
							}else{
								return;
							}
						});
					}else{
						set_link(obj);
					}
				}
			},{
				xtype	: 'button',
				style	: 'margin-left:3px;',
				text	: Otm.tracking.unlink,
				iconCls	: 'ico-unlink',
				disabled: (check_role('defect_edit_all') && check_role('tc_edit_all'))?false:true,
				handler	: function(btn){
					var obj = get_select_data();

					if(Ext.decode(obj.tl_seq).length < 1){
						Ext.Msg.alert('OTM',Otm.tracking.msg_select_disconnect_tc);
						return;
					}else if(Ext.decode(obj.df_seq).length < 1){
						Ext.Msg.alert('OTM',Otm.tracking.msg_select_disconnect_df);
						return;
					}

					set_unlink(obj);
				}
			}
		]
	};
	Ext.getCmp('tracking').add(main_panel);
	Ext.getCmp('tracking').doLayout();

	tracking_defect_store.loadPage(1);
});
</script>