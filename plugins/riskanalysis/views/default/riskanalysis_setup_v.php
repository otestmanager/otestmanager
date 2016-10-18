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

function riskanalysis_code_grid_select_item(grid_id){
	var grid = Ext.getCmp(grid_id);
	if(grid.getSelectionModel().selected.length >= 1){
		var selItem = grid.getSelectionModel().selected.items[0];
		return selItem;
	}else{
		Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
		return null;
	}
}

function riskanalysis_code_actioin_control(type,action){

	var params = {
		pr_seq	: project_seq,
		pco_type:type,
		pco_name:Ext.getCmp(type+'_name').getValue(),
		pco_default_value : (Ext.getCmp(type+'_default_value'))?Ext.getCmp(type+'_default_value').getValue():''
	};

	switch(action){
		case "delete":
			var selItem = riskanalysis_code_grid_select_item('riskanalysis_'+type+'_grid');
			var Records = Ext.getCmp('riskanalysis_'+type+'_grid').getSelectionModel().selected.items;

			if(selItem && Records.length > 0){
				Ext.Msg.confirm('OTM',Otm.com_msg_deleteConfirm,function(bt){
					if(bt=='yes'){
						var pco_list = Array();
						url = './index.php/Plugin_view/riskanalysis/delete_code';

						for(var i=0; i<Records.length; i++){
							pco_list.push(Records[i].data['pco_seq']);
						}

						params.pco_list = Ext.encode(pco_list);

						Ext.Ajax.request({
							url		: url,
							params	: params,
							method	: 'POST',
							success	: function ( result, request ) {
								if(result.responseText=="ok"){
									Ext.getCmp('riskanalysis_'+type+'_grid').getStore().reload();
									Ext.getCmp('riskanalysis_'+type+'_form').reset();
									Ext.getCmp('riskanalysis_'+type+'_form').setTitle(Otm.com_add);
								}else{
									Ext.Msg.alert("OTM",result.responseText);
								}
							},
							failure: function ( result, request ) {
								alert("fail");
							}
						});
					}else{
						return;
					}
				});
				return;
			}else{
				Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
				return;
			}

			break;
		case "create":
		case "update":
		//default: //create, update
			if(Ext.getCmp(type+'_is_required')){
				params.pco_is_required = Ext.getCmp(type+'_is_required').getValue();
			}
			if(Ext.getCmp(type+'_is_default')){
				params.pco_is_default = Ext.getCmp(type+'_is_default').getValue();
			}

			var url = './index.php/Plugin_view/riskanalysis/create_code';

			if(Ext.getCmp(type+'_save_type').getValue()=='update'){
				var selItem = riskanalysis_code_grid_select_item('riskanalysis_'+type+'_grid');
				if(selItem){
					params.pco_seq =selItem.data.pco_seq;
					url = './index.php/Plugin_view/riskanalysis/update_code';
				}else{
					Ext.Msg.alert("OTM",Otm.com_msg_NotSelectData);
					return;
				}
			}
			
			/*
			params.pco_1 = Ext.getCmp(type+'_pco_1').getValue();
			params.pco_2 = Ext.getCmp(type+'_pco_2').getValue();
			
			var check_data = Ext.getCmp('riskanalysis_'+type+'_grid').getStore().data.items;

			for(var i=0; i<check_data.length; i++){
				if(Ext.getCmp(type+'_save_type').getValue()=='update'){
					if(check_data[i].data.pco_seq == params.pco_seq){
					}else{
						if(check_data[i].data.pco_1 == params.pco_1 && check_data[i].data.pco_2 == params.pco_2){
							Ext.Msg.alert('OTM i','likelihood point와 impact point가 동일한 영역이 있습니다.<br>두 개의 point가 겹치지 않도록 설정하세요.');
							return;
						}
					}
				}else{
					if(check_data[i].data.pco_1 == params.pco_1 && check_data[i].data.pco_2 == params.pco_2){
						Ext.Msg.alert('OTM i','likelihood point와 impact point가 동일한 영역이 있습니다.<br>두 개의 point가 겹치지 않도록 설정하세요.');
						return;
					}
				}
			}
			*/

			if(type == 'riskarea'){
				var check_data = Ext.getCmp('riskanalysis_'+type+'_grid').getStore().data.items;

				for(var i=0; i<check_data.length; i++){
					if(Ext.getCmp(type+'_save_type').getValue()=='update'){
						if(check_data[i].data.pco_seq == params.pco_seq){
						}else{
							if(check_data[i].data.pco_default_value == params.pco_default_value){
								Ext.Msg.alert('OTM i','점수가 동일한 영역이 있습니다.');
								return;
							}
						}
					}else{
						if(check_data[i].data.pco_default_value == params.pco_default_value){
							Ext.Msg.alert('OTM i','점수가 동일한 영역이 있습니다.');
							return;
						}
					}
				}
			}


			break;
		case "up":
			var selItem = riskanalysis_code_grid_select_item('riskanalysis_'+type+'_grid');
			if(selItem){
				var store = Ext.getCmp('riskanalysis_'+type+'_grid').getStore();

				var sort_num = 0;
				var seq_arr = new Array();
				for(var i=0;i<store.data.items.length;i++){
					seq_arr.push(store.data.items[i].data.pco_seq);
				}

				var except_seq_arr = new Array();
				for(var i=0;i<seq_arr.length;i++){
					if(seq_arr[i] !=  selItem.data.pco_seq){
						except_seq_arr.push(seq_arr[i]);
					}else{
						sort_num = i-1;
					}
				}
				if(sort_num < 0){
					return;
				}else{
					var k=0;
					var final_arr = new Array();
					for(var i=0;i<seq_arr.length;i++){
						if(i==sort_num){
							final_arr.push(seq_arr[k+1]);
						}else{
							final_arr.push(except_seq_arr[k]);
							k++;
						}
					}

					var params = {
							pr_seq:project_seq,
							pco_type:type,
							pco_list:Ext.encode(final_arr)
						};

					var url = './index.php/Plugin_view/riskanalysis/update_sort_code';
					//console.log(type,action,url,params);


					Ext.Ajax.request({
						url		: url,
						params	: params,
						method	: 'POST',
						success	: function ( result, request ) {
							pro_riskanalysis_select_action = true;
							store.reload({
								callback:function(){
									if(pro_riskanalysis_select_action){
										Ext.getCmp('riskanalysis_'+type+'_grid').getSelectionModel().select(sort_num);
										pro_riskanalysis_select_action = false;
									}
								}
							});
						},
						failure: function ( result, request ) {
							Ext.Msg.alert("OTM",result.responseText);
						}
					});

				}
			}
			return;
			break;
		case "down":
			var selItem = riskanalysis_code_grid_select_item('riskanalysis_'+type+'_grid');
			if(selItem){
				var store = Ext.getCmp('riskanalysis_'+type+'_grid').getStore();

				var sort_num = 0;
				var seq_arr = new Array();
				for(var i=0;i<store.data.items.length;i++){
					seq_arr.push(store.data.items[i].data.pco_seq);
				}

				var except_seq_arr = new Array();
				for(var i=0;i<seq_arr.length;i++){
					if(seq_arr[i] !=  selItem.data.pco_seq){
						except_seq_arr.push(seq_arr[i]);
					}else{
						sort_num = i+1;
					}
				}

				if(sort_num >= seq_arr.length){
					return;
				}else{
					var k=0;
					var final_arr = new Array();
					for(var i=0;i<seq_arr.length;i++){
						if(i==sort_num){
							final_arr.push(seq_arr[k-1]);
						}else{
							final_arr.push(except_seq_arr[k]);
							k++;
						}
					}

					var params = {
							pr_seq:project_seq,
							pco_type:type,
							pco_list:Ext.encode(final_arr)
						};

					var url = './index.php/Plugin_view/riskanalysis/update_sort_code';
					//console.log(type,action,url,params);


					Ext.Ajax.request({
						url		: url,
						params	: params,
						method	: 'POST',
						success	: function ( result, request ) {
							pro_riskanalysis_select_action = true;
							store.reload({
								callback:function(){
									if(pro_riskanalysis_select_action){
										Ext.getCmp('riskanalysis_'+type+'_grid').getSelectionModel().select(sort_num);
										pro_riskanalysis_select_action = false;
									}
								}
							});
						},
						failure: function ( result, request ) {
							Ext.Msg.alert("OTM",result.responseText);
						}
					});

				}
			}
			return;
			break;
	}


	Ext.Ajax.request({
		url		: url,
		params	: params,
		method	: 'POST',
		success	: function ( result, request ) {
			if(result.responseText=="ok"){
				Ext.getCmp('riskanalysis_'+type+'_grid').getStore().reload();
				Ext.getCmp('riskanalysis_'+type+'_form').reset();
				Ext.getCmp('riskanalysis_'+type+'_form').setTitle(Otm.com_add);
			}else{
				Ext.Msg.alert("OTM",result.responseText);
			}
		},
		failure: function ( result, request ) {
			alert("fail");
		}
	});
}


	/**
	* Center Panel
	*/
	function riskanalysis_get_store(url,params){
		params.pr_seq = project_seq;

		var store = Ext.create('Ext.data.Store', {
			//fields:['ora_code_seq','','ora_code_position'],
			fields:['pco_seq','pco_type','pco_name','pco_is_required','pco_is_default','pco_position','pco_default_value','pco_color','pco_is_use','pco_1','pco_2'],
			proxy: {
				type	: 'ajax',
				url		: url,
				extraParams: params,
				actionMethods: {
					create: 'POST', read: 'POST', update: 'POST', destroy: 'POST'
				},
				reader: {
					type: 'json',
					totalProperty: 'totalCount',
					rootProperty: 'data'
				}
			}
		});

		return store;
	};

	function get_grid(type){
		var url = './index.php/Plugin_view/riskanalysis/code_list';
		var params = {pr_seq:project_seq};
			params.type = type;

		var columns = [];
		switch(type){
			case "likelihood":
			case "impact":
				columns = [
					Ext.create('Ext.grid.RowNumberer'),
					{header: '팩터명',	dataIndex: 'pco_name', flex: 1},
					{header: Otm.com_sort,	dataIndex: 'pco_position', width:50, hidden:true}
				];
				break;
			case "riskarea":
				columns = [
					Ext.create('Ext.grid.RowNumberer'),
					{header: '리스크영역명',	dataIndex: 'pco_name', flex: 1},
					{header: '점수(<=)',	dataIndex: 'pco_default_value', width : 100},
					//{header: 'likelihood point(<)',	dataIndex: 'pco_1', width : 100},
					//{header: 'impact point(<)',	dataIndex: 'pco_2', width : 100},
					{header: Otm.com_sort,	dataIndex: 'pco_position', width:50, hidden:true}
				];
				params.type = 'riskarea';
				break;
			case "riskpoint":
				columns = [
					Ext.create('Ext.grid.RowNumberer'),
					{header: '평가등급명',	dataIndex: 'pco_name', flex: 1},
					{header: '점수',	dataIndex: 'pco_default_value', flex: 1},
					{header: Otm.com_sort,	dataIndex: 'pco_position', width:50, hidden:true}
				];
				params.type = 'riskpoint';
				break;
			default:
				columns = [
					Ext.create('Ext.grid.RowNumberer'),
					{header: type,	dataIndex: 'pco_name', flex: 1},
					{header: Otm.com_sort,	dataIndex: 'pco_position', width:50, hidden:true}
				];
				break;
		}

		var store = riskanalysis_get_store(url,params);

		var pro_riskanalysis_select_action = false;

		var grid = {
			region	: 'center',
			layout	: 'fit',
			xtype	: 'gridpanel',
			id		: 'riskanalysis_'+type+'_grid',
			flex	: 1,
			store	: store,
			viewConfig	: {
				listeners: {
					viewready: function(){
					   this.store.load();
					}
				}
			},
			columns: columns,
			listeners: {
				select: function(item, record, eOpts ){
					if(record){
						Ext.getCmp(type+'_save_type').setValue('update');
						Ext.getCmp(type+'_name').setValue(record.data.pco_name);
						if(Ext.getCmp(type+'_default_value')){
							Ext.getCmp(type+'_default_value').setValue(record.data.pco_default_value);
						}

						if(Ext.getCmp(type+'_pco_1')){
							Ext.getCmp(type+'_pco_1').setValue(record.data.pco_1);
						}

						if(Ext.getCmp(type+'_pco_2')){
							Ext.getCmp(type+'_pco_2').setValue(record.data.pco_2);
						}

						Ext.getCmp('riskanalysis_'+type+'_form').expand();
						Ext.getCmp('riskanalysis_'+type+'_form').setTitle(Otm.com_update);
					}
				}
			},
			tbar	: [
				{xtype: 'button', text: Otm.com_add, iconCls:'ico-add',	handler: function() {
					Ext.getCmp('riskanalysis_'+type+'_form').reset();
					Ext.getCmp('riskanalysis_'+type+'_form').expand();
					Ext.getCmp('riskanalysis_'+type+'_form').setTitle(Otm.com_add);
				}},'-',
				{xtype: 'button', text: Otm.com_remove,iconCls:'ico-remove', handler: function() {
					riskanalysis_code_actioin_control(type,'delete');
				}},{
					xtype: 'tbseparator'
				},{
					text:Otm.com_up,
					iconCls:'ico-up',
					handler:function(btn){
						riskanalysis_code_actioin_control(type,'up');

					}
				},{
					xtype: 'tbseparator'
				},{
					text:Otm.com_down,
					iconCls:'ico-down',
					handler:function(btn){
						riskanalysis_code_actioin_control(type,'down');


					}
				}
			]
		};

		return grid;
	};

	function get_form(type){
		var title = '';
		var items = [];

		title = Otm.com_add;
		items = [{
			xtype	: 'hiddenfield',
			id		: type+'_save_type',
			value	: 'create'
		},{
			xtype	: 'textfield',
			fieldLabel: type+'(*)',
			minLength:1,maxLength:100,
			id		: type+'_name',
			allowBlank: false
		}];

		switch(type){
			case "riskarea":
				title = Otm.com_add;
				items = [{
					xtype	: 'hiddenfield',
					id		: type+'_save_type',
					value	: 'create'
				},{
					xtype	: 'textfield',
					fieldLabel: '리스크영역명(*)',
					minLength:1,maxLength:100,
					id		: type+'_name',
					allowBlank: false
				},{
					xtype	: 'numberfield',
					fieldLabel: '점수(*)',				
					minValue: 1,
					id		: type+'_default_value',
					allowBlank: false
				},{
					xtype	: 'numberfield',
					fieldLabel: 'likelihood point(*)',
					minValue:1,
					id		: type+'_pco_1',
					//allowBlank: false
					hidden	: true
				},{
					xtype	: 'numberfield',
					fieldLabel: 'impact point(*)',
					minValue:1,
					id		: type+'_pco_2',
					//allowBlank: false
					hidden	: true
				}];
				break;
			case "riskpoint":

				//여기
				// 점수 필드 추가
				title = Otm.com_add;
				items = [{
					xtype	: 'hiddenfield',
					id		: type+'_save_type',
					value	: 'create'
				},{
					xtype	: 'textfield',
					fieldLabel: '평가등급명(*)',
					minLength:1,maxLength:100,
					id		: type+'_name',
					allowBlank: false
				},{
					xtype	: 'numberfield',
					fieldLabel: '점수(*)',
					//value	: 1,
					maxValue: 9,
					minValue: 1,
					id		: type+'_default_value',
					allowBlank: false
				}];


				break;
			default:
				title = Otm.com_add;
				items = [{
					xtype	: 'hiddenfield',
					id		: type+'_save_type',
					value	: 'create'
				},{
					xtype	: 'textfield',
					fieldLabel: type+'(*)',
					minLength:1,maxLength:100,
					id		: type+'_name',
					allowBlank: false
				}];
				break;
		}

		var form_id = 'riskanalysis_'+type+'_form';

		var form = Ext.create("Ext.form.Panel",{
			region		: 'east',
			xtype		: 'panel',
			title		: title,
			id			: form_id,
			flex		: 1,
			autoScroll	: true,
			collapsible	: true,
			collapsed	: false,
			animation	: false,
			border		: false,
			defaults	: {
				anchor: '100%',
				layout: {
					type: 'hbox',
					defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
				},
				margin: 5
			},
			items	: items,
			buttons : [{
				text:Otm.com_save,
				iconCls:'ico-save',
				disabled: true,
				formBind: true,
				handler:function(btn){
					var action = Ext.getCmp(type+'_save_type').getValue();
					riskanalysis_code_actioin_control(type,action);
				}
			}]
		});

		return form;
	};



	var riskanalysis_setup_riskarea = {
		layout	: 'border',
		xtype	: 'panel',
		title	: '리스크 영역',
		//title	: 'riskanalysis_setup_riskarea',
		flex	: 1,
		height	: 210,
		items	: [get_grid('riskarea'),get_form('riskarea')]
	};

	var riskanalysis_setup_riskpoint = {
		layout	: 'border',
		xtype	: 'panel',
		title	: '리스크 평가등급',
		//title	: 'riskanalysis_setup_riskpoint',
		flex	: 1,
		height	: 210,
		items	: [get_grid('riskpoint'),get_form('riskpoint')]
	};

	var riskanalysis_setup_testlevel = {
		layout	: 'border',
		xtype	: 'panel',
		title	: '테스트 레벨',
		//title	: 'riskanalysis_setup_testlevel',
		flex	: 1,
		height	: 210,
		items	: [get_grid('testlevel'),get_form('testlevel')]
	};

	var riskanalysis_setup_riskfactor_l = {
		layout	: 'border',
		xtype	: 'panel',
		title	: '리스트 팩터(Likelihood)',
		//title	: 'riskanalysis_setup_riskfactor',
		flex	: 1,
		height	: 210,
		items	: [get_grid('likelihood'),get_form('likelihood')]
	};

	var riskanalysis_setup_riskfactor_i = {
		layout	: 'border',
		xtype	: 'panel',
		title	: '리스크 팩터(Impact)',
		//title	: 'riskanalysis_setup_riskfactor',
		flex	: 1,
		height	: 210,
		items	: [get_grid('impact'),get_form('impact')]
	};

	var riskanalysis_setup_center_panel =  {
		region	: 'center',
		xtype	: 'panel',
		autoScroll : true,
		defaults	: {
			anchor: '100%',
			layout: {
				type	: 'hbox',
				align	: 'stretch',
				defaultMargins: {top: 0, right: 5, bottom: 0, left: 0}
			},
			margin: 5
		},
		items	:[riskanalysis_setup_riskarea, riskanalysis_setup_riskpoint, riskanalysis_setup_testlevel, riskanalysis_setup_riskfactor_l,riskanalysis_setup_riskfactor_i]
				//riskanalysis_setup_evaluationUnit]
	};

	Ext.onReady(function(){
		var main_panel = {
			layout		: 'border',
			defaults	: {
				collapsible	: false,
				split		: false,
				bodyStyle	: 'padding:0px'
			},
			title		: '리스크분석 설정',
			items		: [riskanalysis_setup_center_panel]
		};

		Ext.getCmp('riskanalysis_setup').add(main_panel);
		Ext.getCmp('riskanalysis_setup').doLayout(true,false);
	});

</script>