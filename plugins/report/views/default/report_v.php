<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
 include_once($data['skin_dir'].'/locale-'.$data['mb_lang'].'.php');
$today = date("Y-m-d");
?>

<script type="text/javascript">
var colorArr = ["#00b8bf","#0080c0","#ff8000","#ff0000","#8600a8","#00ff00","#80ff00","#0000ff","#809609", "#0e518d", "#8d0e1b", "#e67904","#ffca20","#910f75","#82b638","#a66011"];
var colorArray = Array(0x00b8bf,0x0080c0,0xff8000,0xff0000,0x8600a8,0x00ff00,0x80ff00,0x0000ff,0x0000ff,0x809609,0x0e518d,0x8d0e1b,0xe67904,0xffca20,0x910f75,0x82b638,0xa66011);

var sdate = "";
var edate = "";

function get_line_chart(store,column)
{
	var chart_series = new Array();
	for(var i=1;i<store.config.fields.length;i++){
		chart_series.push({
			type	: 'line',
			fill	: true,
			axis	: 'left',
			xField	: store.config.fields[0],
			yField	: store.config.fields[i],
			title	: column[i].header
		})
	}

	var chart = Ext.create('Ext.chart.Chart', {
		border:false,
		theme: 'Category1',
		legend: {
			position: 'bottom'
		},
		axes: [{
			type: 'numeric',
			position: 'left',
			minimum: 0
		}, {
			type: 'category',
			position: 'bottom'
		}],
		series: chart_series,
		store: store
	});
	return chart;
}

function get_pie_chart(store)
{
	return Ext.create('Ext.panel.Panel', {
		border:false,
		layout: 'fit',
		items: [{
			xtype: 'polar',
			insetPadding: 50,
			innerPadding: 20,
			series: [{
				type: 'pie',
				angleField: 'cnt',
				label: {
					field: 'name',
					calloutLine: {
						length: 60,
						width: 3
					}
				},
				highlight: true,
				tooltip: {
					trackMouse: true,
					renderer: function(storeItem, item) {
						this.setHtml('Name : '+storeItem.get('name')+'<br>Count : '+storeItem.get('cnt'));
					}
				},
				getLegendColor: function(index) {
					return colorArr[index%15];
				},
				renderer: function(sprite, record, attr, index, store) {
					return Ext.apply(attr, {
						fill: colorArr[index%15]
					});
				}
			}],
			store: store
		}]
	});
}
function get_bar_chart(store)
{
	return Ext.create('Ext.panel.Panel', {
		layout: 'fit',
		items: [{
			xtype: 'cartesian',
			legend: {
				position: 'bottom'
			},
			axes: [{
				type: 'numeric',
				position: 'left',
				minimum: 0
			}, {
				type: 'category',
				position: 'bottom'
			}],
			series: {
				type: 'bar',
				axis: 'left',
				xField: 'name',
				yField: 'cnt',
				label: {
					field: 'cnt',
					display: 'insideEnd'
				}
			},
			store: store
		}]
	});
}

function get_bar_chart2(store)
{
	return Ext.create('Ext.panel.Panel', {
		layout: 'fit',
		items: [{
			xtype: 'cartesian',
			axes: [{
				type: 'numeric',
				position: 'left',
				fields: ['total_cnt','open_cnt','close_cnt'],
				minimum: 0
			}, {
				type: 'category',
				position: 'bottom'
			}],			
			series: [{				
				type: 'bar',
				axis: 'left',
				xField: 'name',
				yField: 'total_cnt',
				style: {
					lineWidth: 2,
					maxBarWidth: 30,
					stroke: 'dodgerblue',
					opacity: 0.6
				},
				label: {
					field: 'total_cnt',
					display: 'insideEnd'
				},
				tooltip: {
					trackMouse: true,
					renderer: function(storeItem, item) {
						this.setHtml('Total Defect Count : '+storeItem.get('total_cnt')+'<br>Open Defect Count : '+storeItem.get('open_cnt')+'<br>Close Defect Count : '+storeItem.get('close_cnt'));
					}
				}
			},{
				type: 'line',	
				axis: 'left',
				xField: 'name',
				yField: 'close_cnt'
			}],
			store: store
		}]
	});
}

function get_html_grid_win(title,columns,datas)
{
	var win = Ext.getCmp('html_grid_win');
	if(win){
		win.close();
	}
	

	var colspan = 1;
	var rowspan = 1;
	var repeat = 0;
	var colsColumn = new Array();
	var data_index = new Array();

	var subColumn = new Array();

	for(var i=0;i<columns.length;i++){
		if(columns[i].columns){
			rowspan=2;
			break;
		}
	}
	

	var innerHTML = "<table width=100% border=1><tr>";
	for(var i=1;i<columns.length;i++){
		if(columns[i].columns){
			repeat++;
			colsColumn = columns[i].columns;

			colspan = columns[i].columns.length;
			for(var j=0;j<colsColumn.length;j++){
				subColumn.push(colsColumn[j].header);
				data_index.push(colsColumn[j].dataIndex);
			}
			innerHTML += "<td colspan='"+colspan+"'>"+columns[i].header+"</td>";
		}else{
			data_index.push(columns[i].dataIndex);
			innerHTML += "<td rowspan='"+rowspan+"'>"+columns[i].header+"</td>";
		}
	}
	innerHTML += "</tr>";


	for(var i=0;i<subColumn.length;i++){
		innerHTML += "<td>"+subColumn[i]+"</td>";
	}

	innerHTML += "</tr>";

	var printValue="";
	for(var i=0;i<datas.length;i++){
		innerHTML += "<tr>";
		for(var j=0;j<data_index.length;j++){
			printValue = "";
			printValue = eval("datas[i]."+data_index[j]);
			if(printValue != null && printValue != 0){
				innerHTML += "<td>"+printValue+"</td>";
			}else{
				innerHTML += "<td></td>";
			}
		}
		innerHTML += "</tr>";
	}
	innerHTML += "</table>";

	Ext.create('Ext.window.Window', {
		title:title,
		id	: 'html_grid_win',
		height: document.body.clientHeight-100,
		width: document.body.clientWidth-100,
		bodyStyle:'padding:20px;',
		layout: 'fit',
		resizable : true,
		autoScroll:true,
		modal : true,
		constrainHeader: true,
		html : innerHTML,
		buttons:[{
			text:Otm.com_close,
			iconCls:'ico-close',
			handler:function(btn){
				Ext.getCmp('html_grid_win').close();
			}
		}]
	}).show('',function(){
	});
}

function get_plantestcase_result_summary_panel(add_panel_id,tp_seq)
{
	var plantestcase_result_summary_tc_result_store = Ext.create('Ext.data.Store', {
		fields:['name','field1','field2'],
		id:'plantestcase_result_summary_tc_result_store',
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_report_plan_result_summary',
			extraParams: {
				project_seq : <?=$project_seq?>,
				tp_seq : tp_seq
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

	plantestcase_result_summary_tc_result_store.load({
		callback: function(r,options,success){
			Ext.getCmp(add_panel_id).removeAll();

			var columns = this.proxy.reader.rawData.columns;
			var datas = this.proxy.reader.rawData.data;
			var data = [];

			for(var i=0; i<columns[0].subColumn.length; i++){
				var cnt_value = 0;					
				if(datas[0]){					
					cnt_value = datas[0][Object.keys(datas[0])[i+1]];
				}
				data.push({
					name : columns[0].subColumn[i].name,
					cnt : cnt_value
				});
			}
			
			var chart_store = Ext.create('Ext.data.JsonStore', {
				fields:['name', 'cnt'],
				data: data
			});

			var column = new Array();
			column.push({header:'TestCase Count',dataIndex:'tc_cnt',align:'center', width:100});
			if(columns !== null){
				for(var i=0;i<columns.length;i++){
					if(columns[i].subColumn.length == 0){
					}else{
						var subColumn = new Array();
						for(var j=0;j<columns[i].subColumn.length;j++){
							var data_index = '_'+columns[i].subColumn[j].dataIndex;
							column.push({header:columns[i].subColumn[j].name,dataIndex:data_index,align:'center', width:80,renderer: function(value, label, storeItem) {
								if(value > 0){
									return value;
								}
								return 0;
							}});
						}
					}
				};
			}

			var plantestcase_result_summary_grid = Ext.create("Ext.grid.Panel",{
				id:'plantestcase_result_summary_grid',
				store:this,
				autoScroll: true,
				border:false,
				columns:column
			});

			var plantestcase_result_summary_tcresult_panel =  Ext.create("Ext.panel.Panel",{
				id:'plantestcase_result_summary_tcresult_panel',border:true,
				title:Otm.rep_tc_result_summary,
				layout:'border',
				defaults: {
					collapsible: true,
					split: false
				},
				items:[{
					region:'center',collapsible: false,border:false,
					layout:'fit',
					items:[get_pie_chart(chart_store)]
				},{
					region:'south',
					layout:'fit',autoScroll: true,
					items:[plantestcase_result_summary_grid]
				}]
			});

			Ext.getCmp(add_panel_id).add(plantestcase_result_summary_tcresult_panel);
			Ext.getCmp(add_panel_id).doLayout(true,false);
		}
	});
}

//테스트 진척도 -> 실행결과 요약의 결함 상태현황
function get_plantestcase_result_summary_defect_status_panel(add_panel_id,tp_seq)
{
	var plantestcase_result_summary_defect_status_store = Ext.create('Ext.data.Store', {
		fields:['name','field1','field2'],
		id:'plantestcase_result_summary_defect_status_store',
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_report_defect_status',
			extraParams: {
				project_seq : <?=$project_seq?>,
				tp_seq : tp_seq
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

	plantestcase_result_summary_defect_status_store.load({
		callback: function(r,options,success){
			Ext.getCmp(add_panel_id).removeAll();

			var columns = this.proxy.reader.rawData.columns;
			var datas = this.proxy.reader.rawData.data;
			var data = [];


			for(var i=1; i<columns.length; i++){
				data.push({
					name : columns[i].name,
					cnt : datas[0][Object.keys(datas[0])[i]]
				});
			}

			var chart_store = Ext.create('Ext.data.JsonStore', {
				fields:['name', 'cnt'],
				data: data
			});

			var column = new Array();
			for(var i=0;i<columns.length;i++){
				var column_name = columns[i].name;
				if(i==0){
					var column_dataIndex = columns[i].dataIndex;
				}else{
					var column_dataIndex = 'val_'+columns[i].dataIndex;
				}

				column.push({
					header: column_name, align:'center',
					dataIndex: column_dataIndex,
					flex: 1
				});
			}

			var plantestcase_result_summary_defect_status_grid = Ext.create("Ext.grid.Panel",{
				id:'plantestcase_result_summary_defect_status_grid',
				store:this,
				autoScroll: true,
				border:false,
				columns:column
			});

			var plantestcase_result_summary_defect_status_panel =  Ext.create("Ext.panel.Panel",{
				id:'plantestcase_result_summary_defect_status_panel',border:true,
				title:Otm.rep_defect_status_info,
				layout:'border',
				defaults: {
					collapsible: true,
					split: false
				},
				items:[{
					region:'center',collapsible: false,border:false,
					layout:'fit',
					items:[get_bar_chart(chart_store)]
				},{
					region:'south',
					layout:'fit',autoScroll: true,
					items:[plantestcase_result_summary_defect_status_grid]
				}]
			});

			Ext.getCmp(add_panel_id).add(plantestcase_result_summary_defect_status_panel);
			Ext.getCmp(add_panel_id).doLayout(true,false);
		}
	})
}

function get_alltestcase_result_panel(add_panel_id)
{
	var plan_tc_result_grid_store = Ext.create('Ext.data.Store', {
		fields:['location','tc_name','last_result','tc_id','num1','num2'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_plan_tc_result_list',
			extraParams: {
				pr_seq : <?=$project_seq?>
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
	plan_tc_result_grid_store.load({
		callback: function(r,options,success){
			var columns = this.proxy.reader.rawData.columns;
			var datas = this.proxy.reader.rawData.data;

			var column = new Array();
			column.push({xtype: 'rownumberer',width: 30,sortable: false,locked:true});
			column.push({header: Otm.tc_suite, dataIndex: 'location',locked:true, align:'left', sortable: true,width: 150});
			column.push({header: Otm.tc+' '+Otm.com_id, dataIndex: 'tc_out_id',locked:true, align:'center', sortable: true,width: 100});
			column.push({header: Otm.tc, dataIndex: 'subject',locked:true, align:'left', sortable: true, minWidth:200, flex:1});
			column.push({header: Otm.rep_defect_conn_number, dataIndex: 'df_cnt',locked:true, align:'center', sortable: true,
				columns:[
					{header:Otm.rep_open_defect,width:80,align:'center',dataIndex: 'df_cnt',renderer: function(value, label, storeItem) {
						if(value){
							return "<a href=javascript:get_show_defect_list('all','"+storeItem.data.tc_seq+"','all')><span style=color:black>"+value+"</span></a>";
						}else{
							return "";
						}
					}},
					{header:Otm.rep_close_defect,width:80,align:'center',dataIndex: 'close_cnt',renderer: function(value, label, storeItem) {
						if(value){
							return "<a href=javascript:get_show_defect_list('all','"+storeItem.data.tc_seq+"','close')><span style=color:black>"+value+"</span></a>";
						}else{
							return "";
						}
					}}
				]
			});
			column.push({header: Otm.rep_final_executed_result, dataIndex: 'last_result',locked:true, align:'center', sortable: true,width: 100,renderer: function(value, label, storeItem) {
				/*if(storeItem.data.group_pco_seq){
					var pco_group = storeItem.data.group_pco_seq.split(",");
					var check=0;
					for(var i=0;i<pco_group.length;i++){
						if(storeItem.data.pco_seq != pco_group[i]){
							check = 1;
							break;
						}
					}
					if(check==1){
						return "<a href=javascript:get_show_testcase_result_list('"+storeItem.data.tc_seq+"','')><span style=color:red>"+value+"</span></a>";
					}else{
						return "<a href=javascript:get_show_testcase_result_list('"+storeItem.data.tc_seq+"','')><span style=color:black>"+value+"</span></a>";
					}
				}else{
					return "";
				}*/
				if(!value){
					return "";
				}else{
					return "<a href=javascript:get_show_testcase_result_list('"+storeItem.data.tc_seq+"','')><span style=color:black>"+value+"</span></a>";
				}
			}});

			if(columns !== null){
				for(var i=0;i<columns.length;i++){

					if(columns[i].subColumn.length == 0){
					}else{
						var subColumn = new Array();
						for(var j=0;j<columns[i].subColumn.length;j++){
							var data_index = '_'+columns[i].plan_seq+'_'+columns[i].subColumn[j].dataIndex;
							subColumn.push({header:columns[i].subColumn[j].name,dataIndex:data_index,align:'center', width:50,renderer: function(value, label, storeItem) {
								if(value > 0){
									return value;
								}
								return '';
							}});
						}
						column.push({header:columns[i].plan_name, sortable: true,width: 100,columns:subColumn});//,
					}
				};
			}

			var plan_tc_result_grid = Ext.create("Ext.grid.Panel",{
				flex:1,
				id:'plan_tc_result_grid',
				store:this,
				autoScroll: true,
				border:false,
				columns:column,
				tbar:['->',{
					xtype:'button',
					text:Otm.rep_data_table,
					handler:function(btn){
						get_html_grid_win(Ext.getCmp(add_panel_id).title,column,datas);
					}
				},'-',{
					xtype:'button',
					iconCls:'ico-export',
					text:Otm.com_export,
					handler:function(btn){
						export_data('plugin/report/report_export_v1','project_seq=<?=$project_seq?>&report_type=plan_tc_result_grid');
					}
				}]
			});

			Ext.getCmp(add_panel_id).add(plan_tc_result_grid);
			Ext.getCmp(add_panel_id).doLayout(true,false);
		}
	});
}

function get_plantestcase_result_summary(add_panel_id)
{
	var plantestcase_result_summary_plan_store = Ext.create('Ext.data.Store', {
		fields:['pr_seq','tp_seq','text'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/testcase/plan_list',
			extraParams: {
				pr_seq : <?=$project_seq?>
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
	var rec = new Array();
	rec.push({
		tp_seq:'0',
		text:Otm.com_all+''+Otm.tc_plan
	});
	plantestcase_result_summary_plan_store.load({
		callback: function(r,options,success){
			plantestcase_result_summary_plan_store.insert(0,rec);
			plantestcase_result_summary_plan_combo.setValue('0');
		}
	});

	var plantestcase_result_summary_plan_combo = Ext.create('Ext.form.ComboBox', {
		anchor: '100%',
		fieldLabel: Otm.tc_plan,
		id: 'plantestcase_result_summary_plan_combo',
		triggerAction	: 'all',
		forceSelection	: true,
		editable		: false,
		displayField	: 'text',
		valueField		: 'tp_seq',
		allowBlank		: false,
		queryMode		: 'local',
		store			: plantestcase_result_summary_plan_store,
		listeners: {
			select: function(combo, record, index) {
				get_plantestcase_result_summary_panel('plantestcase_result_summary_1',combo.getValue());
				get_plantestcase_result_summary_defect_status_panel('plantestcase_result_summary_2',combo.getValue());
			}
		}
	});
	var plantestcase_result_summary_panel = Ext.create("Ext.panel.Panel",{
		border:false,
		id:'plantestcase_result_summary_panel',
		layout: 'vbox',
		pack: 'start',
		align: 'stretch',
		items:[{
			xtype:'panel',
			width:'100%',border:false,
			flex:1,layout:'fit',
			items:[{
				layout: {
					type: 'hbox',
					pack: 'start',
					align: 'stretch'
				},
				border:false,
				items:[{
					xtype:'panel',layout:'fit',flex:1,border:false,bodyStyle:'padding:5px',id:'plantestcase_result_summary_1',
					items:[get_plantestcase_result_summary_panel('plantestcase_result_summary_1')]
				},{
					xtype:'panel',layout:'fit',flex:1,border:false,bodyStyle:'padding:5px',id:'plantestcase_result_summary_2',
					items:[get_plantestcase_result_summary_defect_status_panel('plantestcase_result_summary_2')]
				}]

			}]
		}],
		tbar:[plantestcase_result_summary_plan_combo]
	});
	return plantestcase_result_summary_panel;
}

function get_plantestcase_result_panel(add_panel_id)
{
	var plan_testcase_result_combo_store = Ext.create('Ext.data.Store', {
		fields:['pr_seq','tp_seq','text'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/testcase/plan_list',
			extraParams: {
				pr_seq : <?=$project_seq?>
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

	var plan_testcase_result_combo = Ext.create('Ext.form.ComboBox', {
		anchor: '100%',
		fieldLabel: Otm.tc_plan,
		id: 'plan_testcase_result_combo',
		triggerAction	: 'all',
		forceSelection	: true,
		editable		: false,
		displayField	: 'text',
		valueField		: 'tp_seq',
		allowBlank		: false,
		queryMode		: 'local',
		emptyText		: Otm.com_msg_select_plan,
		store			: plan_testcase_result_combo_store,
		listeners: {
			change: function(combo, record, index) {
				Ext.getCmp('plan_testcase_result_grid').getStore().reload({
					params:{
						pr_seq		: <?=$project_seq?>,
						tp_seq		: combo.getValue()
					}
				});
			}
		}
	});

	var plan_testcase_result_column = Ext.create('Ext.data.Store', {
		fields:['location','tc_name','tc_id','num1','num2'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_plan_testcase_result_column',
			extraParams: {
				pr_seq : <?=$project_seq?>,
				tp_seq : 0
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

	plan_testcase_result_column.load({
		callback: function(r,options,success){
			var plan_testcase_result_store = Ext.create('Ext.data.Store', {
				fields:['location','tc_name','tc_id'],
				proxy: {
					type	: 'ajax',
					url		: './index.php/Plugin_view/report/get_plan_testcase_result_list',
					extraParams: {
						pr_seq : <?=$project_seq?>,
						tp_seq : 0
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

			var columns = this.proxy.reader.rawData.columns;
			var column = new Array();
			column.push({xtype: 'rownumberer',width: 30,sortable: false});
			column.push({header: Otm.tc_suite, dataIndex: 'location', align:'left', sortable: true,minWidth:150,width:200});
			column.push({header: Otm.tc+' '+Otm.com_id, dataIndex: 'tc_out_id', align:'center', sortable: true, minWidth:100,width:150});
			column.push({header: Otm.tc, dataIndex: 'subject', align:'left', sortable: true, minWidth:300,flex:1});

			column.push({header: Otm.rep_final_executed_result, dataIndex: 'df_cnt', align:'center', sortable: true,
				columns:[
					{header:Otm.rep_open_defect,width:80,align:'center',dataIndex: 'df_cnt',renderer: function(value, label, storeItem) {
						if(value){
							return "<a href=javascript:get_show_defect_list('plan','"+storeItem.data.tc_seq+"','all')><span style=color:black>"+value+"</span></a>";
						}else{
							return "";
						}
					}},
					{header:Otm.rep_close_defect,width:80,align:'center',dataIndex: 'close_cnt',renderer: function(value, label, storeItem) {
						if(value){
							return "<a href=javascript:get_show_defect_list('plan','"+storeItem.data.tc_seq+"','close')><span style=color:black>"+value+"</span></a>";
						}else{
							return "";
						}
					}}
				]
			});
			column.push({header: Otm.rep_final_executed_result, dataIndex: 'last_result', align:'center', sortable: true,width: 100,renderer: function(value, label, storeItem) {
				/*if(storeItem.data.group_pco_seq){
					var pco_group = storeItem.data.group_pco_seq.split(",");
					var check=0;
					for(var i=0;i<pco_group.length;i++){
						if(storeItem.data.pco_seq != pco_group[i]){
							check = 1;
							break;
						}
					}
					var tp_seq = Ext.getCmp("plan_testcase_result_combo").getValue();
					if(check==1){
						return "<a href=javascript:get_show_testcase_result_list('"+storeItem.data.tc_seq+"','"+tp_seq+"')><span style=color:red>"+value+"</span></a>";
					}else{
						return "<a href=javascript:get_show_testcase_result_list('"+storeItem.data.tc_seq+"','"+tp_seq+"')><span style=color:black>"+value+"</span></a>";
					}

				}else{
					return "";
				}*/
				if(!value){
					return "";
				}else{
					return "<a href=javascript:get_show_testcase_result_list('"+storeItem.data.tc_seq+"','')><span style=color:black>"+value+"</span></a>";
				}
			}});

			if(columns !== null){
				for(var i=0;i<columns.length;i++){

					if(columns[i].subColumn.length == 0){
					}else{
						var subColumn = new Array();
						for(var j=0;j<columns[i].subColumn.length;j++){
							var data_index = '_'+columns[i].subColumn[j].dataIndex;
							subColumn.push({header:columns[i].subColumn[j].name,dataIndex:data_index,align:'center', width:50,renderer: function(value, label, storeItem) {
								if(value > 0){
									return value;
								}
								return '';
							}});
						}
						column.push({header:columns[i].plan_name, sortable: true,width: 100,columns:subColumn});//,
					}
				};
			}

			var plan_testcase_result_grid = Ext.create("Ext.grid.Panel",{
				flex:1,
				id:'plan_testcase_result_grid',
				store:plan_testcase_result_store,
				autoScroll: true,
				border:false,
				columns:column,
				tbar:[plan_testcase_result_combo,'->',{
					xtype:'button',
					text:Otm.rep_data_table,
					handler:function(btn){
						var datas = "";
						if(plan_testcase_result_store.data.length > 0){
							datas = plan_testcase_result_store.proxy.reader.rawData.data;
						}

						get_html_grid_win(Ext.getCmp(add_panel_id).title,column,datas);
					}
				},'-',{
					xtype:'button',
					iconCls:'ico-export',
					text:Otm.com_export,
					handler:function(btn){
						var tp_seq = Ext.getCmp("plan_testcase_result_combo").getValue();
						export_data('plugin/report/report_export_v1','project_seq=<?=$project_seq?>&report_type=plan_testcase_result_grid&tp_seq='+tp_seq);
					}
				}]
			});

			Ext.getCmp(add_panel_id).add(plan_testcase_result_grid);
			Ext.getCmp(add_panel_id).doLayout(true,false);

			plan_testcase_result_combo_store.load({
				callback: function(r,options,success){
					if(r.length >= 1){
						Ext.getCmp("plan_testcase_result_combo").setValue(r[r.length-1].data.tp_seq);
					}
				}
			});
		}
	});
}

//결함 대쉬보드 -> 결함 상태 정보
function get_defect_status_panel(add_panel_id,tp_seq,start_date,end_date)
{
	var defect_status_store = Ext.create('Ext.data.Store', {
		fields:['name','field1','field2'],
		id:'defect_status_store',
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_report_defect_status',
			extraParams: {
				project_seq : <?=$project_seq?>,
				tp_seq : tp_seq,
				start_date : start_date,
				end_date : end_date
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

	defect_status_store.load({
		callback: function(r,options,success){
			Ext.getCmp(add_panel_id).removeAll();

			var columns = this.proxy.reader.rawData.columns;
			var datas = this.proxy.reader.rawData.data;
			var data = [];

			for(var i=1; i<columns.length; i++){
				data.push({
					name : columns[i].name,
					cnt : datas[0][Object.keys(datas[0])[i]]
				});
			}

			var chart_store = Ext.create('Ext.data.JsonStore', {
				fields:['name', 'cnt'],
				data: data
			});

			var column = new Array();
			for(var i=0;i<columns.length;i++){
				var column_name = columns[i].name;
				if(i==0){
					var column_dataIndex = columns[i].dataIndex;
				}else{
					var column_dataIndex = 'val_'+columns[i].dataIndex;
				}

				column.push({
					header: column_name, align:'center',
					dataIndex: column_dataIndex,
					flex: 1
				});
			}

			var defect_status_grid = Ext.create("Ext.grid.Panel",{
				id:'defect_status_grid',
				store:this,
				autoScroll: true,
				border:false,
				columns:column
			});

			var defect_status_panel =  Ext.create("Ext.panel.Panel",{
				id:'defect_status_panel',border:true,
				title:Otm.rep_defect_status_info,
				layout:'border',
				defaults: {
					collapsible: true,
					split: false
				},
				items:[{
					region:'center',collapsible: false,border:false,
					layout:'fit',
					items:[get_bar_chart(chart_store)]
				},{
					region:'south',
					layout:'fit',autoScroll: true,
					items:[defect_status_grid]
				}]
			});

			Ext.getCmp(add_panel_id).add(defect_status_panel);
			Ext.getCmp(add_panel_id).doLayout(true,false);
		}
	})
}

//결함 대쉬보드 -> 결함 심각도 정보
function get_defect_severity_panel(add_panel_id,tp_seq,start_date,end_date)
{
	var defect_severity_store = Ext.create('Ext.data.Store', {
		fields:['name','field1','field2'],
		id:'defect_severity_store',
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_report_defect_severity',
			extraParams: {
				project_seq : <?=$project_seq?>,
				tp_seq : tp_seq,
				start_date : start_date,
				end_date : end_date
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
	defect_severity_store.load({
		callback: function(r,options,success){
			Ext.getCmp(add_panel_id).removeAll();

			var columns = this.proxy.reader.rawData.columns;
			var datas = this.proxy.reader.rawData.data;
			var data = [];

			for(var i=1; i<columns.length; i++){
				data.push({
					name : columns[i].name,
					cnt : datas[0][Object.keys(datas[0])[i]]
				});
			}

			var chart_store = Ext.create('Ext.data.JsonStore', {
				fields:['name', 'cnt'],
				data: data
			});

			var column = new Array();
			for(var i=0;i<columns.length;i++){
				var column_name = columns[i].name;
				if(i==0){
					var column_dataIndex = columns[i].dataIndex;
				}else{
					var column_dataIndex = 'val_'+columns[i].dataIndex;
				}

				column.push({
					header: column_name, align:'center',
					dataIndex: column_dataIndex,
					flex: 1
				});
			}

			var defect_severity_grid = Ext.create("Ext.grid.Panel",{
				id:'defect_severity_grid',
				store:this,
				autoScroll: true,
				border:false,
				columns:column
			});

			var defect_severity_panel =  Ext.create("Ext.panel.Panel",{
				id:'defect_severity_panel',border:true,
				title:Otm.rep_defect_severity_info,
				layout:'border',
				defaults: {
					collapsible: true,
					split: false
				},
				items:[{
					region:'center',collapsible: false,border:false,
					layout:'fit',
					items:[get_bar_chart(chart_store)]
				},{
					region:'south',
					layout:'fit',autoScroll: true,
					items:[defect_severity_grid]
				}]
			});

			Ext.getCmp(add_panel_id).add(defect_severity_panel);
			Ext.getCmp(add_panel_id).doLayout(true,false);
		}
	});
}

//결함 대쉬보드 -> 우선순위
function get_defect_priority_panel(add_panel_id,tp_seq,start_date,end_date)
{
	var defect_priority_store = Ext.create('Ext.data.Store', {
		fields:['name','field1','field2'],
		id:'defect_priority_store',
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_report_defect_priority',
			extraParams: {
				project_seq : <?=$project_seq?>,
				tp_seq : tp_seq,
				start_date : start_date,
				end_date : end_date
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
	defect_priority_store.load({
		callback: function(r,options,success){
			Ext.getCmp(add_panel_id).removeAll();

			var columns = this.proxy.reader.rawData.columns;
			var datas = this.proxy.reader.rawData.data;
			var data = [];

			for(var i=1; i<columns.length; i++){
				data.push({
					name : columns[i].name,
					cnt : datas[0][Object.keys(datas[0])[i]]
				});
			}

			var chart_store = Ext.create('Ext.data.JsonStore', {
				fields:['name', 'cnt'],
				data: data
			});

			var column = new Array();
			for(var i=0;i<columns.length;i++){
				var column_name = columns[i].name;
				if(i==0){
					var column_dataIndex = columns[i].dataIndex;
				}else{
					var column_dataIndex = 'val_'+columns[i].dataIndex;
				}

				column.push({
					header: column_name, align:'center',
					dataIndex: column_dataIndex,
					flex: 1
				});
			}

			var defect_priority_grid = Ext.create("Ext.grid.Panel",{
				id:'defect_priority_grid',
				store:this,
				autoScroll: true,
				border:false,
				columns:column
			});

			var defect_priority_panel =  Ext.create("Ext.panel.Panel",{
				id:'defect_priority_panel',border:true,
				title:Otm.rep_defect_priority_info,
				layout:'border',
				defaults: {
					collapsible: true,
					split: false
				},
				items:[{
					region:'center',collapsible: false,border:false,
					layout:'fit',
					items:[get_bar_chart(chart_store)]
				},{
					region:'south',
					layout:'fit',autoScroll: true,
					items:[defect_priority_grid]
				}]
			});

			Ext.getCmp(add_panel_id).add(defect_priority_panel);
			Ext.getCmp(add_panel_id).doLayout(true,false);
		}
	});
}

//결함 대쉬보드 -> 재현빈도
function get_defect_frequency_panel(add_panel_id,tp_seq,start_date,end_date)
{
	var defect_frequency_store = Ext.create('Ext.data.Store', {
		fields:['name','field1','field2'],
		id:'defect_frequency_store',
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_report_defect_frequency',
			extraParams: {
				project_seq : <?=$project_seq?>,
				tp_seq : tp_seq,
				start_date : start_date,
				end_date : end_date
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
	defect_frequency_store.load({
		callback: function(r,options,success){
			Ext.getCmp(add_panel_id).removeAll();

			var columns = this.proxy.reader.rawData.columns;
			var datas = this.proxy.reader.rawData.data;
			var data = [];

			for(var i=1; i<columns.length; i++){
				data.push({
					name : columns[i].name,
					cnt : datas[0][Object.keys(datas[0])[i]]
				});
			}

			var chart_store = Ext.create('Ext.data.JsonStore', {
				fields:['name', 'cnt'],
				data: data
			});

			var column = new Array();
			for(var i=0;i<columns.length;i++){
				var column_name = columns[i].name;
				if(i==0){
					var column_dataIndex = columns[i].dataIndex;
				}else{
					var column_dataIndex = 'val_'+columns[i].dataIndex;
				}

				column.push({
					header: column_name, align:'center',
					dataIndex: column_dataIndex,
					flex: 1
				});
			}

			var defect_frequency_grid = Ext.create("Ext.grid.Panel",{
				id:'defect_frequency_grid',
				store:this,
				autoScroll: true,
				border:false,
				columns:column
			});

			var defect_frequency_panel =  Ext.create("Ext.panel.Panel",{
				id:'defect_frequency_panel',border:true,
				title:Otm.rep_defect_frequency_info,
				layout:'border',
				defaults: {
					collapsible: true,
					split: false
				},
				items:[{
					region:'center',collapsible: false,border:false,
					layout:'fit',
					items:[get_bar_chart(chart_store)]
				},{
					region:'south',
					layout:'fit',autoScroll: true,
					items:[defect_frequency_grid]
				}]
			});

			Ext.getCmp(add_panel_id).add(defect_frequency_panel);
			Ext.getCmp(add_panel_id).doLayout(true,false);
		}
	});
}

/*Risk 영역*/
function get_risk_defect_summary(add_panel_id)
{
	var risk_defect_summary_plan_store = Ext.create('Ext.data.Store', {
		fields:['pr_seq','tp_seq','text'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/testcase/plan_list',
			extraParams: {
				pr_seq : <?=$project_seq?>
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
	var risk_defect_summary_plan_combo = Ext.create('Ext.form.ComboBox', {
		anchor: '100%',
		fieldLabel: Otm.tc_plan,
		id: 'risk_defect_summary_plan_combo',
		triggerAction	: 'all',
		forceSelection	: true,
		editable		: false,
		displayField	: 'text',
		valueField		: 'tp_seq',
		allowBlank		: false,
		queryMode		: 'local',
		store			: risk_defect_summary_plan_store,
		listeners: {			
			change : function(combo,newValue,oldValue,e){
				risk_defect_summary_store.load({params:{pr_seq:'<?=$project_seq?>',tp_seq:newValue}});
			}
		}
	});
	risk_defect_summary_plan_store.load({
		callback: function(r,options,success){			
			risk_defect_summary_plan_combo.setValue(r[0].data.tp_seq);
		}
	});
	

	var risk_defect_summary_store = Ext.create('Ext.data.Store', {
		fields:['name','field1','field2'],
		id:'risk_defect_summary_store',
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_risk_defect_summary',
			extraParams: {
				pr_seq : <?=$project_seq?>
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

	var column = new Array();
	column.push({xtype: 'rownumberer',width: 30,sortable: false});
	column.push({header: Otm.report.risarea, dataIndex: 'name', align:'left', sortable: true, width: 150});
	column.push({header: Otm.com_all+''+Otm.def, dataIndex: 'total_cnt', align:'center', sortable: false, width: 150});
	column.push({header: Otm.rep_open_defect, dataIndex: 'open_cnt', align:'center', sortable: false, width: 150});
	column.push({header: Otm.rep_close_defect, dataIndex: 'close_cnt', align:'center', sortable: false, width: 150});
	

	var risk_defect_summary_grid = Ext.create("Ext.grid.Panel",{
		id:'risk_defect_summary_grid',
		store:risk_defect_summary_store,
		autoScroll: true,
		border:false,
		columns:column
	});

	var risk_defect_summary_panel = Ext.create("Ext.panel.Panel",{
		border:false,
		id:'risk_defect_summary_panel',
		layout: 'vbox',
		pack: 'start',
		align: 'stretch',
		items:[{
			xtype:'panel',
			width:'100%',border:false,
			flex:2,layout:'fit',bodyStyle:'padding:20px',
			items:[get_bar_chart2(risk_defect_summary_store)]
		},{
			xtype:'panel',layout:'fit',
			width:'100%',border:true,
			flex:1,
			items:[risk_defect_summary_grid]
		}],
		tbar:[risk_defect_summary_plan_combo,'->',{
			xtype:'button',
			text:Otm.rep_data_table,
			handler:function(btn){
				var datas = risk_defect_summary_store.proxy.reader.rawData.data;				
				get_html_grid_win(Ext.getCmp(add_panel_id).title,column,datas);
			}
		},'-',{
			xtype:'button',
			text:Otm.com_export,
			handler:function(btn){
				var tp_seq = Ext.getCmp("risk_defect_summary_plan_combo").getValue();
				export_data('plugin/report/report_export_v1','project_seq=<?=$project_seq?>&report_type=risk_defect_summary_grid&tp_seq='+tp_seq);
			}
		}]	
	});	
	
	return risk_defect_summary_panel;	
}

function get_risk_tcresult_summary(add_panel_id)
{
	var risk_tcresult_summary_plan_store = Ext.create('Ext.data.Store', {
		fields:['pr_seq','tp_seq','text'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/testcase/plan_list',
			extraParams: {
				pr_seq : <?=$project_seq?>
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
	var risk_tcresult_summary_plan_combo = Ext.create('Ext.form.ComboBox', {
		anchor: '100%',
		fieldLabel: Otm.tc_plan,
		id: 'risk_tcresult_summary_plan_combo',
		triggerAction	: 'all',
		forceSelection	: true,
		editable		: false,
		displayField	: 'text',
		valueField		: 'tp_seq',
		allowBlank		: false,
		queryMode		: 'local',
		store			: risk_tcresult_summary_plan_store,
		listeners: {			
			change : function(combo,newValue,oldValue,e){
				risk_tcresult_summary_store.load({params:{pr_seq:'<?=$project_seq?>',tp_seq:newValue}});
			}
		}
	});
	risk_tcresult_summary_plan_store.load({
		callback: function(r,options,success){			
			risk_tcresult_summary_plan_combo.setValue(r[0].data.tp_seq);
		}
	});


	var risk_tcresult_summary_store = Ext.create('Ext.data.Store', {
		fields:['name','field1','field2'],
		id:'risk_tcresult_summary_store',
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_risk_tcresult_summary',
			extraParams: {
				pr_seq : <?=$project_seq?>
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

	var column = new Array();
	column.push({xtype: 'rownumberer',width: 30,sortable: false});
	column.push({header: Otm.report.risarea, dataIndex: 'name', align:'left', sortable: true, width: 150});
	column.push({header: Otm.com_link+''+Otm.tc, dataIndex: 'total_cnt', align:'left', sortable: true, width: 150});
	column.push({header: Otm.tc_execution+''+Otm.tc, dataIndex: 'close_cnt', align:'left', sortable: true, width: 150});

	

	var risk_tcresult_summary_grid = Ext.create("Ext.grid.Panel",{
		id:'risk_tcresult_summary_grid',
		store:risk_tcresult_summary_store,
		autoScroll: true,
		border:false,
		columns:column
	});

	var risk_tcresult_summary_panel = Ext.create("Ext.panel.Panel",{
		border:false,
		id:'risk_tcresult_summary_panel',
		layout: 'vbox',
		pack: 'start',
		align: 'stretch',
		items:[{
			xtype:'panel',
			width:'100%',border:false,
			flex:2,layout:'fit',bodyStyle:'padding:20px',
			items:[get_bar_chart2(risk_tcresult_summary_store)]
		},{
			xtype:'panel',layout:'fit',
			width:'100%',border:true,
			flex:1,
			items:[risk_tcresult_summary_grid]
		}],
		tbar:[risk_tcresult_summary_plan_combo,'->',{
			xtype:'button',
			text:Otm.rep_data_table,
			handler:function(btn){
				var datas = risk_tcresult_summary_store.proxy.reader.rawData.data;				
				get_html_grid_win(Ext.getCmp(add_panel_id).title,column,datas);
			}
		},'-',{
			xtype:'button',
			text:Otm.com_export,
			handler:function(btn){
				var tp_seq = Ext.getCmp("risk_tcresult_summary_plan_combo").getValue();
				export_data('plugin/report/report_export_v1','project_seq=<?=$project_seq?>&report_type=risk_tcresult_summary_grid&tp_seq='+tp_seq);
			}
		}]
	});
	
	return risk_tcresult_summary_panel;
}

function get_risk_defect_info(add_panel_id)
{
	var risk_defect_info_plan_store = Ext.create('Ext.data.Store', {
		fields:['pr_seq','tp_seq','text'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/testcase/plan_list',
			extraParams: {
				pr_seq : <?=$project_seq?>
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
	var risk_defect_info_plan_combo = Ext.create('Ext.form.ComboBox', {
		anchor: '100%',
		fieldLabel: Otm.tc_plan,
		id: 'risk_defect_info_plan_combo',
		triggerAction	: 'all',
		forceSelection	: true,
		editable		: false,
		displayField	: 'text',
		valueField		: 'tp_seq',
		allowBlank		: false,
		queryMode		: 'local',
		store			: risk_defect_info_plan_store,
		listeners: {			
			change : function(combo,newValue,oldValue,e){
				risk_defect_into_store.load({params:{pr_seq:'<?=$project_seq?>',tp_seq:newValue}});
			}
		}
	});
	risk_defect_info_plan_store.load({
		callback: function(r,options,success){			
			risk_defect_info_plan_combo.setValue(r[0].data.tp_seq);
		}
	});


	var risk_defect_into_store = Ext.create('Ext.data.Store', {
		fields:['name','field1','field2'],
		id:'risk_defect_into_store',
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_risk_defect_info',
			extraParams: {
				pr_seq : <?=$project_seq?>
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
		groupField: 'risk_area',
		autoLoad:false
	});

	var column = new Array();
	column.push({xtype: 'rownumberer',width: 30,sortable: false});
	column.push({header: Otm.def, dataIndex: 'df_subject', align:'left', sortable: true, flex:1});
	column.push({header: Otm.com_status, dataIndex: 'df_status', align:'center', sortable: true, width: 80});
	column.push({header: Otm.com_user, dataIndex: 'df_author', align:'center', sortable: true, width: 80});
	column.push({header: Otm.com_creator, dataIndex: 'df_writer', align:'center', sortable: true, width: 80});
	column.push({header: Otm.com_date, dataIndex: 'df_regdate', align:'center', sortable: true, width: 80});


	var risk_defect_into_panel = {
		layout: 'fit',
		border:false,
		id:'risk_defect_into_panel',
		xtype : 'grid',
		store:risk_defect_into_store,		
		features: [{
			ftype: 'grouping',
			startCollapsed: false,
			groupHeaderTpl: '{name} {rows.length} Item{[values.rows.length > 1 ? "s" : ""]}'
		}],
		columns:column,
		tbar:[risk_defect_info_plan_combo,'->',{
			xtype:'button',
			text:Otm.rep_data_table,
			handler:function(btn){
				var datas = "";
				if(risk_defect_into_store.data.length > 0){
					datas = risk_defect_into_store.proxy.reader.rawData.data;
				}
				get_html_grid_win(Ext.getCmp(add_panel_id).title,column,datas);
			}
		},'-',{
			xtype:'button',
			text:Otm.com_export,
			handler:function(btn){
				var tp_seq = Ext.getCmp("risk_defect_info_plan_combo").getValue();
				export_data('plugin/report/report_export_v1','project_seq=<?=$project_seq?>&report_type=risk_defect_into_panel&tp_seq='+tp_seq);
			}
		}]
	};
	
	return risk_defect_into_panel;
}




function get_defect_dashboard_panel(add_panel_id)
{
	var defect_dashboard_plan_store = Ext.create('Ext.data.Store', {
		fields:['pr_seq','tp_seq','text'],
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/testcase/plan_list',
			extraParams: {
				pr_seq : <?=$project_seq?>
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
	var rec = new Array();
	rec.push({
		tp_seq:'0',
		text:Otm.com_all+''+Otm.tc_plan
	});
	defect_dashboard_plan_store.load({
		callback: function(r,options,success){
			defect_dashboard_plan_store.insert(0,rec);
			defect_dashboard_plan_combo.setValue('0');
		}
	});

	var defect_dashboard_plan_combo = Ext.create('Ext.form.ComboBox', {
		anchor: '100%',
		fieldLabel: Otm.tc_plan,
		id: 'defect_dashboard_plan_combo',
		triggerAction	: 'all',
		forceSelection	: true,
		editable		: false,
		displayField	: 'text',
		valueField		: 'tp_seq',
		allowBlank		: false,
		queryMode		: 'local',
		store			: defect_dashboard_plan_store,
		listeners: {
			select: function(combo, record, index) {
				var start_date = Ext.getCmp("defect_dashboard_sdate").getValue();
				var end_date = Ext.getCmp("defect_dashboard_edate").getValue();

				get_defect_status_panel('defect_dashboard_panel_1',combo.getValue(),start_date,end_date);
				get_defect_severity_panel('defect_dashboard_panel_2',combo.getValue(),start_date,end_date);
				get_defect_priority_panel('defect_dashboard_panel_3',combo.getValue(),start_date,end_date);
				get_defect_frequency_panel('defect_dashboard_panel_4',combo.getValue(),start_date,end_date);
			}
		}
	});

	var defect_dashboard_dateRange = {			
			style:'margin-left:100px;',
			xtype: 'fieldcontainer',
			fieldLabel: Otm.com_period,
			combineErrors: true,
			msgTarget : 'side',
			layout: 'hbox',
			defaults: {
				flex: 1,
				hideLabel: true
			},
			items: [{
				xtype		: 'datefield',vtype: 'daterange', 
				id			: 'defect_dashboard_sdate',
				name		: 'defect_dashboard_sdate',
				endDateField: 'defect_dashboard_edate',
				fieldLabel	: 'Start',
				format		:"Y-m-d", editable: false,
				value		: sdate,
				allowBlank	: false
			},{
				xtype		: 'datefield',vtype: 'daterange', 
				id			: 'defect_dashboard_edate',
				name		: 'defect_dashboard_edate',
				startDateField: 'defect_dashboard_sdate',
				format		: "Y-m-d",editable: false,
				value		: edate,
				fieldLabel	: 'End',
				allowBlank	: false
			},{
				xtype:'button',
				style:'margin-left:3px;',
				text:Otm.com_search,
				iconCls:'ico-search',
				handler:function(btn){	
					var plan_combo = Ext.getCmp("defect_dashboard_plan_combo").getValue();
					var start_date = Ext.getCmp("defect_dashboard_sdate").getValue();
					var end_date = Ext.getCmp("defect_dashboard_edate").getValue();

					get_defect_status_panel('defect_dashboard_panel_1',plan_combo,start_date,end_date);
					get_defect_severity_panel('defect_dashboard_panel_2',plan_combo,start_date,end_date);
					get_defect_priority_panel('defect_dashboard_panel_3',plan_combo,start_date,end_date);
					get_defect_frequency_panel('defect_dashboard_panel_4',plan_combo,start_date,end_date);	
				}
			}]
		}
	
	var defect_dashboard_panel = Ext.create("Ext.panel.Panel",{
		border:false,
		id:'defect_dashboard_panel',
		layout: 'vbox',
		pack: 'start',
		align: 'stretch',
		items:[{
			xtype:'panel',
			width:'100%',border:false,
			flex:1,layout:'fit',
			items:[{
				layout: {
					type: 'hbox',
					pack: 'start',
					align: 'stretch'
				},
				border:false,
				items: [{
					xtype:'panel',layout:'fit',flex:1,border:false,id:'defect_dashboard_panel_1',bodyStyle:'padding:5px',
					items:[get_defect_status_panel('defect_dashboard_panel_1','',sdate,edate)]
				},{
					xtype:'panel',layout:'fit',flex:1,border:false,id:'defect_dashboard_panel_2',bodyStyle:'padding:5px',
					items:[get_defect_severity_panel('defect_dashboard_panel_2','',sdate,edate)]
				}]
			}]
		},{
			xtype:'panel',layout:'fit',
			width:'100%',border:false,
			flex:1,
			items:[{
				layout: {
					type: 'hbox',
					pack: 'start',
					align: 'stretch'
				},
				border:false,
				items: [{
					xtype:'panel',layout:'fit',flex:1,border:false,id:'defect_dashboard_panel_3',bodyStyle:'padding:5px',
					items:[get_defect_priority_panel('defect_dashboard_panel_3','',sdate,edate)]
				},{
					xtype:'panel',layout:'fit',flex:1,border:false,id:'defect_dashboard_panel_4',bodyStyle:'padding:5px',
					items:[get_defect_frequency_panel('defect_dashboard_panel_4','',sdate,edate)]
				}]
			}]
		}],
		tbar:[defect_dashboard_plan_combo,defect_dashboard_dateRange]
	});
	return defect_dashboard_panel;
}

//누적결함 S-커브
function get_defect_scurve_grid(add_panel_id,data_unit)
{
	var defect_scurve_store = Ext.create('Ext.data.Store', {
		fields:['name','field1','field2'],
		id:'defect_scurve_store',
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_defect_scurve_list',
			extraParams: {
				pr_seq : <?=$project_seq?>,
				sdate : sdate,
				edate : edate,
				data_unit : data_unit
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

	var column = new Array();
	column.push({xtype: 'rownumberer',width: 30,sortable: false});
	column.push({header: Otm.com_period, dataIndex: 'name', align:'left', sortable: true, width: 150});
	column.push({header: Otm.rep_open_defect, dataIndex: 'field1', align:'center', sortable: false, width: 150});
	column.push({header: Otm.rep_close_defect, dataIndex: 'field2', align:'center', sortable: false, width: 150});

	var chart_column = new Array();
	chart_column.push({header: Otm.com_period, dataIndex: 'name', align:'left', sortable: true, width: 150});
	chart_column.push({header: Otm.rep_open_defect, dataIndex: 'field1', align:'center', sortable: false, width: 150});
	chart_column.push({header: Otm.rep_close_defect, dataIndex: 'field2', align:'center', sortable: false, width: 150});

	var defect_scurve_grid = Ext.create("Ext.grid.Panel",{
		id:'defect_scurve_grid',
		store:defect_scurve_store,
		autoScroll: true,
		border:false,
		columns:column
	});

	var defect_scurve_panel = Ext.create("Ext.panel.Panel",{
		border:false,
		id:'defect_scurve_panel',
		layout: 'vbox',
		pack: 'start',
		align: 'stretch',
		items:[{
			xtype:'panel',
			width:'100%',border:false,
			flex:2,layout:'fit',bodyStyle:'padding:20px',
			items:[get_line_chart(defect_scurve_store,chart_column)]
		},{
			xtype:'panel',layout:'fit',
			width:'100%',border:true,
			flex:1,
			items:[defect_scurve_grid]
		}],
		tbar:[{
			xtype: 'radiogroup',
			id:'scurve_data_unit',
            fieldLabel: Otm.rep_data_unit,
			width:'500',
			columns: 4,
            items: [
                {boxLabel: Otm.com_year, style:'padding-left:20px;', name: 'scurve_data_unit', inputValue: 'year'},
                {boxLabel: Otm.com_month, style:'padding-left:20px;', name: 'scurve_data_unit', inputValue: 'month'},
                {boxLabel: Otm.com_week, style:'padding-left:20px;', name: 'scurve_data_unit', inputValue: 'week'},
                {boxLabel: Otm.com_day, style:'padding-left:20px;', name: 'scurve_data_unit', inputValue: 'day'}
            ]
		},{
			style:'margin-left:100px;',
			xtype: 'fieldcontainer',
			fieldLabel: Otm.com_period,
			combineErrors: true,
			msgTarget : 'side',
			layout: 'hbox',
			defaults: {
				flex: 1,
				hideLabel: true
			},
			items: [{
				xtype		: 'datefield',vtype: 'daterange',
				id			: 'scurve_sdate',
				name		: 'scurve_sdate',
				endDateField: 'scurve_edate',
				fieldLabel	: 'Start',
				format		:"Y-m-d", editable: false,
				value		: sdate,
				allowBlank	: false
			},{
				xtype		: 'datefield',vtype: 'daterange',
				id			: 'scurve_edate',
				name		: 'scurve_edate',
				startDateField: 'scurve_sdate',
				format		: "Y-m-d",editable: false,
				value		: edate,
				fieldLabel	: 'End',
				allowBlank	: false
			},{
				xtype:'button',
				style:'margin-left:3px;',
				text:Otm.com_search,
				iconCls:'ico-search',
				handler:function(btn){
					defect_scurve_store.reload({
						params:{
							pr_seq : <?=$project_seq?>,
							sdate : Ext.getCmp("scurve_sdate").getValue(),
							edate : Ext.getCmp("scurve_edate").getValue(),
							data_unit : Ext.getCmp("scurve_data_unit").getValue().scurve_data_unit
						}
					});
				}
			}]
		},'->',{
			xtype:'button',
			text:Otm.rep_data_table,
			handler:function(btn){
				var datas = "";
				if(defect_scurve_store.data.length > 0){
					datas = defect_scurve_store.proxy.reader.rawData.data;
				}
				get_html_grid_win(Ext.getCmp(add_panel_id).title,column,datas);
			}
		},'-',{
			xtype:'button',
			iconCls:'ico-export',
			text:Otm.com_export,
			handler:function(btn){
				var select_data_unit = Ext.getCmp("scurve_data_unit").getValue();
				var tmp_sdate = new Date(Ext.getCmp("scurve_sdate").getValue());
				var tmp_edate = new Date(Ext.getCmp("scurve_edate").getValue());

				sdate_year = tmp_sdate.getFullYear();
				sdate_month = ((""+(tmp_sdate.getMonth()+1)).length ==1)?"0"+(tmp_sdate.getMonth()+1):tmp_sdate.getMonth()+1;
				sdate_day = ((""+tmp_sdate.getDate()).length ==1)?"0"+tmp_sdate.getDate():tmp_sdate.getDate();
				sdate = sdate_year+"-"+sdate_month+"-"+sdate_day;

				edate_year = tmp_edate.getFullYear();
				edate_month = ((""+(tmp_edate.getMonth()+1)).length ==1)?"0"+(tmp_edate.getMonth()+1):tmp_edate.getMonth()+1;
				edate_day = ((""+tmp_edate.getDate()).length ==1)?"0"+tmp_edate.getDate():tmp_edate.getDate();
				edate = edate_year+"-"+edate_month+"-"+edate_day;

				export_data('plugin/report/report_export_v1','project_seq=<?=$project_seq?>&report_type=defect_scurve_grid&data_unit='+select_data_unit.scurve_data_unit+'&sdate='+sdate+'&edate='+edate);
			}
		}]
	});
	var data_unit_group = Ext.getCmp("scurve_data_unit");
	data_unit_group.setValue({scurve_data_unit: data_unit});

	return defect_scurve_panel;
}

//스윗별 결함 분포도
function get_suite_defect_distribution_grid(add_panel_id)
{
	var suite_defect_distribution_plan_store = Ext.create('Ext.data.Store', {
		fields:['pr_seq','tp_seq','text'],
		id:'suite_defect_distribution_plan_store',
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/testcase/plan_list',
			extraParams: {
				pr_seq : <?=$project_seq?>
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

	var suite_defect_distribution_plan_combo = Ext.create('Ext.form.ComboBox', {
		anchor: '100%',
		fieldLabel: Otm.tc_plan,
		id: 'suite_defect_distribution_plan_combo',
		triggerAction	: 'all',
		forceSelection	: true,
		editable		: false,
		displayField	: 'text',
		valueField		: 'tp_seq',
		allowBlank		: false,
		queryMode		: 'local',
		emptyText		: Otm.com_msg_select_plan,
		store			: suite_defect_distribution_plan_store,
		listeners: {
			change: function(combo, record, index) {
				Ext.getCmp('suite_defect_distribution_grid').getStore().reload({
					params:{
						pr_seq		: <?=$project_seq?>,
						tp_seq		: combo.getValue()
					}
				});
			}
		}
	});
	suite_defect_distribution_plan_store.load({
		callback: function(r,options,success){
			if(r.length >= 1){
				Ext.getCmp("suite_defect_distribution_plan_combo").setValue(r[r.length-1].data.tp_seq);
			}
		}
	});

	var suite_defect_distribution_store = Ext.create('Ext.data.Store', {
		fields:['name','cnt'],
		id:'suite_defect_distribution_store',
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_suite_defect_distribution_list',
			extraParams: {
				pr_seq : <?=$project_seq?>
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

	var datas = "";

	suite_defect_distribution_store.load({
		callback: function(r,options,success){
			datas = this.proxy.reader.rawData.data;
		}
	});

	var column = new Array();
	column.push({xtype: 'rownumberer',width: 30,sortable: false});
	column.push({header: Otm.tc_suite, dataIndex: 'name', align:'left', sortable: true, width: 350});
	column.push({header: Otm.def_cnt, dataIndex: 'cnt', align:'center', sortable: false, width: 100});

	var suite_defect_distribution_grid = Ext.create("Ext.grid.Panel",{
		id:'suite_defect_distribution_grid',
		store:suite_defect_distribution_store,
		autoScroll: true,
		border:false,
		columns:column
	});

	var suite_defect_distribution_panel = Ext.create("Ext.panel.Panel",{
		border:false,
		id:'suite_defect_distribution_panel',
		layout: 'vbox',
		pack: 'start',
		align: 'stretch',
		items:[{
			xtype:'panel',
			width:'100%',border:false,
			flex:2,layout:'fit',
			items:[get_pie_chart(suite_defect_distribution_store)]
		},{
			xtype:'panel',layout:'fit',
			width:'100%',border:true,
			flex:1,
			items:[suite_defect_distribution_grid]
		}],
		tbar:[suite_defect_distribution_plan_combo,'->',{
			xtype:'button',
			text:Otm.rep_data_table,
			handler:function(btn){
				get_html_grid_win(Ext.getCmp(add_panel_id).title,column,datas);
			}
		},'-',{
			xtype:'button',
			iconCls:'ico-export',
			text:Otm.com_export,
			handler:function(btn){
				var tp_seq = Ext.getCmp("suite_defect_distribution_plan_combo").getValue();
				export_data('plugin/report/report_export_v1','project_seq=<?=$project_seq?>&report_type=suite_defect_distribution_grid&tp_seq='+tp_seq);
			}
		}]
	});
	return suite_defect_distribution_panel;
}

function get_show_testcase_list(df_seq)
{
	if(!df_seq){
		return;
	}else{
		var defect_from_testcase_store = Ext.create('Ext.data.Store', {
			fields:['name','cnt'],
			id:'defect_from_testcase_store',
			proxy: {
				type	: 'ajax',
				url		: './index.php/Plugin_view/report/get_defect_from_testcase_list',
				extraParams: {
					pr_seq : <?=$project_seq?>,
					df_seq : df_seq
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
		var column = new Array();
		column.push({xtype: 'rownumberer',width: 30,sortable: false});

		column.push({header: Otm.tc_plan, dataIndex: 'tp_subject', align:'left', sortable: true, width: 100});
		column.push({header: Otm.tc, dataIndex: 'tc_subject', align:'left', sortable: false, minWidth:200,flex:1});
		column.push({header: Otm.com_creator, dataIndex: 'writer_name', align:'center', sortable: false, width: 100});
		column.push({header: Otm.com_user, dataIndex: 'assign_to', align:'center', sortable: false, width: 100});
		column.push({header: Otm.rep_final_executed_result, dataIndex: 'pco_name', align:'center', sortable: false, width: 100});
		column.push({header: Otm.tc_execution_user, dataIndex: 'execution_writer', align:'center', sortable: false, width: 100});
		column.push({header: Otm.tc_execution_regdate, dataIndex: 'execution_regdate', align:'center', sortable: false, width: 100});

		var defect_from_testcase_grid = Ext.create("Ext.grid.Panel",{
			id:'defect_from_testcase_grid',
			store:defect_from_testcase_store,
			autoScroll: true,
			border:false,
			columns:column
		});

		Ext.create('Ext.window.Window', {
			title : Otm.rep_defect_list,
			id	: 'get_show_testcase_list_win',
			height: document.body.clientHeight-200,
			width: document.body.clientWidth-200,
			layout: 'fit',
			resizable : true,
			autoScroll:true,
			modal : true,
			constrainHeader: true,
			items:[defect_from_testcase_grid],
			buttons:[{
				text:Otm.com_close,
				iconCls:'ico-close',
				handler:function(btn){
					Ext.getCmp('get_show_testcase_list_win').close();
				}
			}]
		}).show('',function(){
		});
	}
}

function get_show_defect_list(panel_type,tc_seq,defect_type)
{
	if(!tc_seq){
		return;
	}else{
		var tp_seq="";
		if(panel_type == "plan"){
			tp_seq = Ext.getCmp("plan_testcase_result_combo").getValue();
		}

		var testcase_from_defect_store = Ext.create('Ext.data.Store', {
			fields:['name','cnt'],
			id:'testcase_from_defect_store',
			proxy: {
				type	: 'ajax',
				url		: './index.php/Plugin_view/report/get_testcase_from_defect_list',
				extraParams: {
					pr_seq : <?=$project_seq?>,
					tc_seq : tc_seq,
					tp_seq : tp_seq,
					type : defect_type
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
		var column = new Array();
		column.push({xtype: 'rownumberer',width: 30,sortable: false});
		column.push({header: Otm.def+''+Otm.com_id, dataIndex: 'df_id', align:'left', sortable: true, width:100});
		column.push({header: Otm.def, dataIndex: 'df_subject', align:'left', sortable: true, flex:1,minWidth:150});
		column.push({header: Otm.com_user, dataIndex: 'assign_name', align:'left', sortable: true, width:100});
		column.push({header: Otm.com_creator, dataIndex: 'writer_name', align:'left', sortable: true, width:100});
		column.push({header: Otm.def+' '+Otm.def_status, dataIndex: 'status_name', align:'left', sortable: true, width:100});
		column.push({header: Otm.def+' '+Otm.def_severity, dataIndex: 'severity_name', align:'left', sortable: true, width:100});
		column.push({header: Otm.def+' '+Otm.def_priority, dataIndex: 'priority_name', align:'left', sortable: true, width:100});
		column.push({header: Otm.def+' '+Otm.def_frequency, dataIndex: 'frequency_name', align:'left', sortable: true, width:100});

		var testcase_from_defect_grid = Ext.create("Ext.grid.Panel",{
			id:'testcase_from_defect_grid',
			store:testcase_from_defect_store,
			autoScroll: true,
			border:false,
			columns:column
		});

		Ext.create('Ext.window.Window', {
			title:'Testcase Defect List',
			height: document.body.clientHeight-200,
			width: document.body.clientWidth-200,
			layout: 'fit',
			id:'get_show_defect_list_win',
			resizable : true,
			autoScroll:true,
			modal : true,
			constrainHeader: true,
			items:[testcase_from_defect_grid],
			buttons:[{
				text:Otm.com_close,
				iconCls:'ico-end',
				handler:function(btn){
					Ext.getCmp('get_show_defect_list_win').close();
				}
			}]
		}).show('',function(){
		});
	}
}

function get_show_testcase_result_list(tc_seq,tp_seq)
{
	if(!tc_seq){
		return;
	}else{
		var rep_testcase_result_store = Ext.create('Ext.data.Store', {
			fields:['name','cnt'],
			id:'rep_testcase_result_store',
			proxy: {
				type	: 'ajax',
				url		: './index.php/Plugin_view/report/get_testcase_result_list',
				extraParams: {
					pr_seq : <?=$project_seq?>,
					tc_seq : tc_seq,
					tp_seq : tp_seq
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
		var column = new Array();
		column.push({xtype: 'rownumberer',width: 30,sortable: false});
		column.push({header: Otm.tc_plan, dataIndex: 'tp_subject', align:'left', sortable: true, width: 100});
		column.push({header: Otm.tc_execution_result, dataIndex: 'pco_name', align:'left', sortable: true, width: 100});
		column.push({header: Otm.tc_execution_user, dataIndex: 'execution_user_name', align:'left', sortable: true, width: 100});
		column.push({header: Otm.tc_execution_regdate, dataIndex: 'regdate', align:'left', sortable: true, width: 100});
		column.push({header: Otm.def+' '+Otm.com_id, dataIndex: 'df_id', align:'left', sortable: true, width: 100});
		column.push({header: Otm.def, dataIndex: 'df_subject', align:'left', sortable: true, flex:1,minWidth:150});
		column.push({header: Otm.def_status, dataIndex: 'status_name', align:'center', sortable: true, width: 100});
		column.push({header: Otm.com_user, dataIndex: 'assign_name', align:'center', sortable: true, width: 100});
		column.push({header: Otm.def_severity, dataIndex: 'severity_name', align:'center', sortable: true, width: 100});
		column.push({header: Otm.def_priority, dataIndex: 'priority_name', align:'center', sortable: true, width: 100});
		column.push({header: Otm.def_frequency, dataIndex: 'frequency_name', align:'center', sortable: true, width: 100});

		var rep_testcase_result_grid = Ext.create("Ext.grid.Panel",{
			id:'testcase_from_defect_grid',
			store:rep_testcase_result_store,
			autoScroll: true,
			border:false,
			columns:column
		});

		Ext.create('Ext.window.Window', {
			title:'Testcase Result List',
			height: document.body.clientHeight-200,
			width: document.body.clientWidth-200,
			id:'get_show_testcase_result_list_win',
			layout: 'fit',
			resizable : true,
			autoScroll:true,
			modal : true,
			constrainHeader: true,
			items:[rep_testcase_result_grid],
			buttons:[{
				text:Otm.com_close,
				iconCls:'ico-close',
				handler:function(btn){
					Ext.getCmp('get_show_testcase_result_list_win').close();
				}
			}]
		}).show('',function(){
		});
	}
}

//결함 목록 정보
function get_defect_list_grid(add_panel_id)
{
	var defect_list_store = Ext.create('Ext.data.Store', {
		fields:['df_subject','tc_id','num1','num2'],
		pageSize: 50,
		proxy: {
			type	: 'ajax',
			url		: './index.php/Plugin_view/report/get_defect_list',
			extraParams: {
				pr_seq : <?=$project_seq?>
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

	var column = new Array();
	column.push({xtype: 'rownumberer',width: 30,sortable: false});
	column.push({header: Otm.tc_plan, dataIndex: 'tp_subject', align:'left', sortable: true,minWidth:50,width:100});
	column.push({header: Otm.def+' '+Otm.com_id, dataIndex: 'df_id', align:'left', sortable: true,minWidth:50,width:100});
	column.push({header: Otm.def, dataIndex: 'df_subject', align:'left', sortable: true,minWidth:200,flex:1});
	column.push({header: Otm.rep_testcase_conn_num, dataIndex: 'tc_cnt', align:'center', sortable: true,minWidth:50,width:100,renderer: function(value, label, storeItem) {
					if(value > 0){
						return "<a href=javascript:get_show_testcase_list('"+storeItem.data.df_seq+"')><span style=color:black>"+value+"</span></a>";
					}
					return '';
				}});

	column.push({header: Otm.com_user, dataIndex: 'df_assign_member', align:'center', sortable: true,minWidth:50,width:100});
	column.push({header: Otm.def_status, dataIndex: 'status_name', align:'center', sortable: true, minWidth:50,width:100});
	column.push({header: Otm.def_severity, dataIndex: 'severity_name', align:'center', sortable: true, minWidth:50,width:100});
	column.push({header: Otm.def_priority, dataIndex: 'priority_name', align:'center', sortable: true, minWidth:50,width:100});
	column.push({header: Otm.def_frequency, dataIndex: 'frequency_name', align:'center', sortable: true, minWidth:50,width:100});
	column.push({header: Otm.com_date, dataIndex: 'regdate', align:'center', sortable: true, minWidth:50,width:100});
	column.push({header: Otm.com_creator, dataIndex: 'writer_name', align:'center', sortable: true, minWidth:50,width:100});

	var defect_list_grid = Ext.create("Ext.grid.Panel",{
		flex:1,
		id:'defect_list_grid',
		store:defect_list_store,
		autoScroll: true,
		border:false,
		columns:column,
		tbar:['->',{
			xtype:'button',
			text:Otm.rep_data_table,
			handler:function(btn){
				var datas = "";
				if(defect_list_store.data.length > 0){
					datas = defect_list_store.proxy.reader.rawData.data;
				}
				get_html_grid_win(Ext.getCmp(add_panel_id).title,column,datas);
			}
		},'-',{
			xtype:'button',
			iconCls:'ico-export',
			text:Otm.com_export,
			handler:function(btn){
				export_data('plugin/report/report_export_v1','project_seq=<?=$project_seq?>&report_type=defect_list_grid');
			}
		}],
		bbar:Ext.create('Ext.PagingToolbar', {
			store: defect_list_store,
			displayInfo: true
		})
	});

	return defect_list_grid;
}


Ext.onReady(function(){
	var project_info_store = Ext.create('Ext.data.Store', {
		fields:['name','field1','field2'],
		id:'project_info_store',
		proxy: {
			type	: 'ajax',
			url : './index.php/Project_setup/project_info',
			extraParams: {
				pr_seq : <?=$project_seq?>
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

	project_info_store.load({
		callback: function(r,options,success){
			sdate = this.data.items[0].data.pr_startdate.substr(0,10);

			edate = this.data.items[0].data.pr_enddate.substr(0,10);
			if(edate == "0000-00-00"){
				edate = "<?=$today?>";
			}

			var report_testprogress_tab = Ext.create("Ext.tab.Panel",{
				layout:'fit',
				id :'report_testprogress_tab',
				activeTab: 0,
				border:false,
				items:[{
					title : Otm.rep_plantestcase_result_summary,
					layout:'fit',is_show : true,
					id:'rep_plantestcase_result_summary',
					items:[get_plantestcase_result_summary('rep_plantestcase_result_summary')]
				},{
					title : Otm.rep_alltestcase_result,
					layout:'fit',is_show : false,
					id:'rep_alltestcase_result',
					items:[]/*get_alltestcase_result_panel('rep_alltestcase_result')*/
				},{
					title : Otm.rep_plantestcase_result,
					layout:'fit',is_show : false,
					id:'rep_plantestcase_result',
					items:[]/*get_plantestcase_result_panel('rep_plantestcase_result')*/
				}],
				listeners: {
					tabchange:function(t,e){
						switch(e.id){
							case "rep_alltestcase_result":
								if(this.items.items[1].is_show != true){
									this.items.items[1].add(get_alltestcase_result_panel('rep_alltestcase_result'));
									this.items.items[1].is_show = true;
								}
							break;
							case "rep_plantestcase_result":
								if(this.items.items[2].is_show != true){
									this.items.items[2].add(get_plantestcase_result_panel('rep_plantestcase_result'));
									this.items.items[2].is_show = true;
								}
							break;
						}
					}
				}
			});
			var report_risk_tab = Ext.create("Ext.tab.Panel",{
				layout:'fit',
				id :'report_risk_tab',
				activeTab:0,
				border:false,
				items:[{
					title : Otm.report.risk_defect_summary,
					layout:'fit',is_show : false,
					id:'rep_risk_defect_summary',
					items:[]
				},{
					title : Otm.report.risk_tcresult_summary,
					layout:'fit',is_show : false,
					id:'rep_risk_tcresult_summary',
					items:[]
				},{
					title : Otm.report.risk_defect_into,
					layout:'fit',is_show : false,
					id:'rep_risk_defect_into',
					items:[]
				}],
				defaults: {
					listeners: {						
						activate: function(t, e) {							
							if(e.id == undefined){
								if(report_risk_tab.items.items[0].is_show != true){
									report_risk_tab.items.items[0].add(get_risk_defect_summary('rep_risk_defect_summary'));
									report_risk_tab.items.items[0].is_show = true;
								}
							}							
						}
					}
				},
				listeners: {	
					tabchange:function(t,e){						
						switch(e.id){
							case "rep_risk_tcresult_summary"://2번째
								if(report_risk_tab.items.items[1].is_show != true){
									report_risk_tab.items.items[1].add(get_risk_tcresult_summary('rep_risk_tcresult_summary'));
									report_risk_tab.items.items[1].is_show = true;
								}
							break;
							case "rep_risk_defect_into"://3번째
								if(report_risk_tab.items.items[2].is_show != true){
									report_risk_tab.items.items[2].add(get_risk_defect_info('rep_risk_defect_into'));
									report_risk_tab.items.items[2].is_show = true;
								}
							break;
							default://1번째
								if(report_risk_tab.items.items[0].is_show != true){
									report_risk_tab.items.items[0].add(get_risk_defect_summary('rep_risk_defect_summary'));
									report_risk_tab.items.items[0].is_show = true;
								}
							break;
						}
					}
				}
			});
			
			var report_defect_tab = Ext.create("Ext.tab.Panel",{
				layout:'fit',
				id :'report_defect_tab',
				activeTab:0,
				border:false,
				items:[{
					title : Otm.rep_defect_dashboard,
					layout:'fit',is_show : false,
					id:'rep_defect_dashboard',
					items:[]/*get_defect_dashboard_panel('rep_defect_dashboard')*/					
				},{
					title : Otm.rep_defect_scurve,
					layout:'fit',is_show : false,
					id:'rep_defect_scurve',
					items:[]///*get_defect_scurve_grid('rep_defect_scurve','month')*/					
				},{
					title : Otm.rep_suite_defect_distribution,
					layout:'fit',is_show : false,
					id:'rep_suite_defect_distribution',
					items:[]/*get_suite_defect_distribution_grid('rep_suite_defect_distribution')*/					
				},{
					title : Otm.rep_defect_list,
					layout:'fit',is_show : false,
					id:'rep_defect_list',
					items:[]/*get_defect_list_grid('rep_defect_list')*/					
				}],
				defaults: {
					listeners: {
						activate: function(t, e) {
							if(e.id == undefined){
								if(report_defect_tab.items.items[0].is_show != true){
									report_defect_tab.items.items[0].add(get_defect_dashboard_panel('rep_defect_dashboard'));
									report_defect_tab.items.items[0].is_show = true;
								}
							}
						}
					}
				},
				listeners: {	
					tabchange:function(t,e){
						switch(e.id){
							case "rep_defect_scurve":
								if(report_defect_tab.items.items[1].is_show != true){
									report_defect_tab.items.items[1].add(get_defect_scurve_grid('rep_defect_scurve','month'));
									report_defect_tab.items.items[1].is_show = true;
								}
							break;
							case "rep_suite_defect_distribution":
								if(report_defect_tab.items.items[2].is_show != true){
									report_defect_tab.items.items[2].add(get_suite_defect_distribution_grid('rep_suite_defect_distribution'));
									report_defect_tab.items.items[2].is_show = true;
								}
							break;
							case "rep_defect_list":
								if(report_defect_tab.items.items[3].is_show != true){
									report_defect_tab.items.items[3].add(get_defect_list_grid('rep_defect_list'));
									report_defect_tab.items.items[3].is_show = true;
								}
							break;
							default:
								if(report_defect_tab.items.items[0].is_show != true){
									report_defect_tab.items.items[0].add(get_defect_dashboard_panel('rep_defect_dashboard'));
									report_defect_tab.items.items[0].is_show = true;
								}
							break;
						}
					}
				}
			});

			var report_ViewPanel = Ext.create("Ext.tab.Panel",{
				layout:'fit',
				id:'report_ViewPanel',
				activeTab:0,
				border:false,
				items:[{
					title : Otm.rep_testprogress,
					id:'report_view_testprogress',
					layout:'fit',is_show : true,
					items:[report_testprogress_tab]
				},{
					title: Otm.def,
					id:'report_view_def',
					layout:'fit',is_show : false,
					items:[]//report_defect_tab
				},{
					title: Otm.report.risk,
					id:'report_view_risk',
					layout:'fit',is_show : false,
					items:[]
				}],
				listeners: {
					tabchange:function(t,e ){
						switch(e.id){
							case "report_view_def":
								if(this.items.items[1].is_show != true){
									this.items.items[1].add(report_defect_tab);
									this.items.items[1].is_show = true;
								}
							break;
							case "report_view_risk":
								if(this.items.items[2].is_show != true){
									this.items.items[2].add(report_risk_tab);
									this.items.items[2].is_show = true;
								}
							break;
						}
					}
				}
			});

			Ext.getCmp('report').removeAll();
			Ext.getCmp('report').doLayout(true,false);

			Ext.getCmp('report').add(report_ViewPanel);
			Ext.getCmp('report').doLayout(true,false);

			//Ext.getCmp("suite_defect_distribution_plan_combo").getStore().load();
		}
	});
});
</script>