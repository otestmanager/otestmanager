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

var colorArr = ["#00b8bf","#0080c0","#ff8000","#ff0000","#8600a8","#00ff00","#80ff00","#0000ff","#809609", "#0e518d", "#8d0e1b", "#e67904","#ffca20","#910f75","#82b638","#a66011"];
var colorArray = Array(0x00b8bf,0x0080c0,0xff8000,0xff0000,0x8600a8,0x00ff00,0x80ff00,0x0000ff,0x0000ff,0x809609,0x0e518d,0x8d0e1b,0xe67904,0xffca20,0x910f75,0x82b638,0xa66011);

function get_bar_chart(store,type)
{
	switch(type)
	{
		case "left":
			return Ext.create('Ext.panel.Panel', {
				layout: 'fit',
				items: [{
					xtype: 'cartesian',
					flipXY: true,
					//위치
					axes: [{
						type: 'numeric',
						position: 'bottom',
						minimum: 0
					}, {
						type: 'category',
						position: 'left'
					}],
					//차트타입
					series: {
						type: 'bar',
						xField: 'name',
						yField: 'cnt',
						//그래프 내에 출력할 값
						label: {
							field: 'cnt',
							display: 'insideEnd'
						}
					},
					//데이터 출력
					store: store
				}]
			});
			break;
		default:
			return Ext.create('Ext.panel.Panel', {
				layout: 'fit',
				items: [{
					xtype: 'cartesian',
					legend: {
						position: 'bottom'
						//docked: 'right'
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
			break;
	}
}



	/**
	* Center Panel
	*/

	var risk_chart_store = Ext.create('Ext.data.Store', {
		fields:['name', 'cnt'],
		proxy: {
			type	: 'ajax',	
			url		: './index.php/Plugin_view/riskanalysis/riskanalysis_riskarea_riskitem_chart',
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
/*
	var data = [];
	data.push({name : 'FTA',cnt : 0});
	data.push({name : 'ITA',cnt : 0});
	data.push({name : 'STTA',cnt : 2});
	data.push({name : 'STA',cnt : 1});

	var risk_chart_store = Ext.create('Ext.data.JsonStore', {
		fields:['name', 'cnt'],
		data: data
	});
*/


	var req_chart_store = Ext.create('Ext.data.Store', {
		fields:['name', 'cnt'],
		proxy: {
			type	: 'ajax',	
			url		: './index.php/Plugin_view/riskanalysis/riskanalysis_riskitem_requirement_chart',
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
/*
	var data = [];
	data.push({name : '회원가입',cnt : 5});
	data.push({name : '로그인',cnt : 6});
	data.push({name : '게시판',cnt : 10});

	var req_chart_store = Ext.create('Ext.data.JsonStore', {
		fields:['name', 'cnt'],
		data: data
	});
*/
	var riskanalysis_center_panel =  {
		region	: 'center',
		xtype	: 'panel',
		layout: 'vbox',
		pack: 'start',
		align: 'stretch',
		items	: [{
			xtype:'panel',
			title:'리스크 영역별 아이템 현황',
			width:'100%',border:false,
			flex:2,layout:'fit',bodyStyle:'padding:20px',
			items:[get_bar_chart(risk_chart_store,'left')]
		},{
			xtype:'panel',
			title:'리스크 아이템별 요구사항 현황',
			width:'100%',border:false,
			flex:2,layout:'fit',bodyStyle:'padding:20px',
			items:[get_bar_chart(req_chart_store)]
		}]
	};


	Ext.onReady(function(){
		var main_panel = {
			layout		: 'border',
			id			: 'riskanalysis_main',
			defaults	: {
				collapsible	: false,
				split		: false,
				bodyStyle	: 'padding:0px'
			},
			items		: [riskanalysis_center_panel]
		};
		Ext.getCmp('riskanalysis').removeAll();

		Ext.getCmp('riskanalysis').add(main_panel);
		Ext.getCmp('riskanalysis').doLayout(true,false);
	});
</script>