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

	/**
	* Center Panel
	*/
	function get_strategy_form()
	{
		Ext.Ajax.request({
			url : './index.php/Plugin_view/riskanalysis/strategy',
			params : {pr_seq : project_seq},
			method: 'POST',
			success: function ( result, request ) {
				if(result.responseText){

					Ext.getCmp('riskanalysis_strategy_list').removeAll();

					var strategy_info = Ext.decode(result.responseText);
					//console.log(strategy_info.data);

					var riskarea = Ext.decode(strategy_info.data.riskarea);
					var testlevel = Ext.decode(strategy_info.data.testlevel);

					var strategy_data = strategy_info.data.strategy_data;

					if(strategy_data && strategy_data !=''){
						strategy_data = Ext.decode(strategy_info.data.strategy_data);
					}

					//console.log(testlevel.length);
					//console.log(riskarea.length);
					var width = 1/(testlevel.length + 1);

					Ext.getCmp('riskanalysis_strategy_list').layout.columns = testlevel.length + 1;

					Ext.getCmp('riskanalysis_strategy_list').add({
							bodyStyle	: 'background-color:#999999;padding:5px;font-weight: bold;color:#ffffff;text-align:center;',
							html	: '리스크영역',
							minWidth : 100
						});

					for (var idx in testlevel) {
						var item = {
							bodyStyle	: 'background-color:#999999;padding:5px;font-weight: bold;color:#ffffff;text-align:center;',
							html: testlevel[idx]['pco_name'],
							minWidth : 100
						};
						Ext.getCmp('riskanalysis_strategy_list').add(item);
					}

					for (var riskarea_idx in riskarea) {
						var item = {
							xtype	: 'panel',
							//bodyStyle	: 'background-color:#999999;padding:5px;font-weight: bold;color:#ffffff;text-align:center;',
							bodyStyle	: 'padding:5px;font-weight: bold;text-align:center;',
							//html: '<div style="height:100%;vertical-align:middle;"><br>'+riskarea[riskarea_idx]+'</div>'
							html: riskarea[riskarea_idx]['pco_name'],
							minWidth : 100
						};
						Ext.getCmp('riskanalysis_strategy_list').add(item);


						//전략 데이터
						for (var testlevel_idx in testlevel) {
							//console.log(riskarea_idx, testlevel_idx);
							//console.log('strategy_'+riskarea_idx+'_'+testlevel_idx);
							//var check_seq = riskarea[riskarea_idx]['pco_name']+':'+riskarea[riskarea_idx]['pco_seq']
							//	+'_'+testlevel[testlevel_idx]['pco_name']+':'+testlevel[testlevel_idx]['pco_seq'];

							//var map = riskarea[riskarea_idx]['pco_seq']+'_'+testlevel[testlevel_idx]['pco_seq'];

							var strategy_id = 'strategy_'+riskarea_idx+'_'+testlevel_idx;
							var strategy_id = 'strategy_'+riskarea[riskarea_idx]['pco_seq']+'_'+testlevel[testlevel_idx]['pco_seq'];

							var strategy_value = '';//check_seq+'<br>(sample)<br>- 설계기법<br><br>- 투입인력<br><br>- 완료조건<br><br>- 중단/재게 조건<br><br>- 재 테스트 여부<br><br>- 기타<br><br>';

							//console.log(strategy_data);

							//console.log(strategy_data[riskarea[riskarea_idx]['pco_seq']][testlevel[testlevel_idx]['pco_seq']]);
							if(strategy_data && strategy_data !=''){
								if(strategy_data[riskarea[riskarea_idx]['pco_seq']] && strategy_data[riskarea[riskarea_idx]['pco_seq']][testlevel[testlevel_idx]['pco_seq']]){
									strategy_value = br2nl(strategy_data[riskarea[riskarea_idx]['pco_seq']][testlevel[testlevel_idx]['pco_seq']]);
								}
							}


							var item = {
								//xtype	: 'textarea',
								xtype	: 'panel',
								id		: strategy_id,
								bodyStyle	: 'padding:5px;',
								autoScroll: true,
								listeners: {
									dblclick : {
										fn: function(a,b,c,d) {
											//console.log(this.id, a, b,c,d);
											//console.log("double click");
											//console.log(strategy_id);
											//console.log(a.target, b.id,c,d);
											//console.log(d.info.target);
											var strategy_id= d.info.target.replace('#', "");

											Ext.create('Ext.window.Window', {
												title	: '수정',
												id	: strategy_id+'_window',
												height	: 600,
												width	: 400,
												layout	: 'fit',
												//resizable : false,
												modal	: true,
												constrainHeader: true,
												items	: [{
													xtype	: 'textarea',
													id	: strategy_id+'_value',
													value	: nl2br(Ext.getCmp(strategy_id).value)
														//Ext.getCmp(strategy_id).getEl().dom.innerHTML//nl2br(strategy_value)//Ext.util.Format.htmlEncode(strategy_value)
													//).getEl().dom.innerHTML
												}],
												buttons:[{
													text:Otm.com_save,
													//formBind: true,
													iconCls:'ico-save',
													handler:function(btn){			
														

														//console.log(strategy_id);
														var value = Ext.getCmp(strategy_id+'_value').getValue();
														//console.log(value);

														var temp_id = strategy_id.split('_');

														Ext.Ajax.request({
															url		: './index.php/Plugin_view/riskanalysis/strategy_save',
															params	: {
																pr_seq : project_seq,
																riskarea_seq : temp_id[1],
																testlevel_seq : temp_id[2],
																value	: value																
															},
															method	: 'POST',
															success	: function ( result, request ) {																	
																//console.log(value);
																//console.log(br2nl(value));
																//console.log(nl2br(value));

																get_strategy_form();

																/*
																Ext.getCmp(strategy_id).update(br2nl(value));
																Ext.getCmp(strategy_id).value = br2nl(value);
																
																Ext.getCmp('riskanalysis_strategy_list').doLayout(true,true);
																*/

																Ext.getCmp(strategy_id+'_window').close();
															},
															failure: function ( result, request ) {
																Ext.Msg.alert('OTM',"fail");
															}
														});
													}
												}]
											}).show();
										},
										// You can also pass 'body' if you don't want click on the header or
										// docked elements
										element: 'el'
									}
								},
								html: strategy_value,
								value: strategy_value
							};
							Ext.getCmp('riskanalysis_strategy_list').add(item);
						}
					}

					var me = Ext.getCmp('riskanalysis_strategy_list');
					var aHeights = [],aWidths = [],
						n = me.items.getCount(),
						i;

					for(i = 0; i < n; i++) {
						//me.items.getAt(i).setHeight(0);
					};

					for(i = 0; i < n; i++) {
						aHeights.push(me.items.getAt(i).getEl().parent().getHeight());
						//aWidths.push(me.items.getAt(i).getEl().parent().getWidth());
					};

					for(i = 0; i < n; i++) {
						me.items.getAt(i).setHeight(aHeights[i]);
						//me.items.getAt(i).setWidth(aWidths[i]);
					};


				}else{
					Ext.Msg.alert("OTM",result.responseText);
				}
			},
			failure: function ( result, request ) {
				alert("fail");
			}
		});
	}

	var riskanalysis_strategy_center_panel =  {
		region	: 'center',
		//layout	: 'column',
		layout : 'fit',
		xtype	: 'panel',
		items	:[{
			autoScroll: true,
			layout: {
				type: 'table',
				tableAttrs: {
					style: {
						//height: '100%',
						width: '100%'
					}
				},
				tdAttrs : {
					style : {
						verticalAlign : 'middle'
					}
				},
				columns: 2
			},
			defaults: {
				height: '100%'
			},
			id		: 'riskanalysis_strategy_list',
			items	: []//get_strategy_form()
			/*,listeners: {
				resize: function(me){
					var aHeights = [],
						n = me.items.getCount(),
						i;

					for(i = 0; i < n; i++) {
						//me.items.getAt(i).setHeight(0);
					};

					for(i = 0; i < n; i++) {
						aHeights.push(me.items.getAt(i).getEl().parent().getHeight());
					};

					for(i = 0; i < n; i++) {
						me.items.getAt(i).setHeight(aHeights[i]);
					};
				}
			}*/
			/*,listeners: {
				resize: function(me){
					if(me.isVisible() && !me.collapsed) {
						var aWidths = [],
							aHeights = [],
							n = me.items.getCount(),
							i;

						for(i = 0; i < n; i++) {
							me.items.getAt(i).setSize(0, 0);
						}

						for(i = 0; i < n; i++) {
							aWidths.push(me.items.getAt(i).getEl().parent().getWidth());
							aHeights.push(me.items.getAt(i).getEl().parent().getHeight());
						}

						for(i = 0; i < n; i++) {
							me.items.getAt(i).setSize(aWidths[i], aHeights[i]);
						}
					}
				}
			}*/


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
			title		: '전략수립',
			items		: [riskanalysis_strategy_center_panel]
		};

		Ext.getCmp('riskanalysis_strategy').add(main_panel);
		Ext.getCmp('riskanalysis_strategy').doLayout(true,false);

		get_strategy_form();
	});

</script>