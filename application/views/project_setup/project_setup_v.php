<?php
/**
 * @copyright Copyright STA
 * Created on 2014. 09. 19.
 * @author STA <otm@sta.co.kr>
 */
?>
<script type="text/javascript">
function get_select_project_tree_node(){
	var project_treePanel = Ext.getCmp('project_treePanel');
	var node = project_treePanel.getSelectionModel().getSelection();
	return node;
}

/**
	Code Info of Project
*/
function getProjectCode(project_seq,type,title){
	if(!type){
		type = 'status';
		title = Otm.def+' '+Otm.def_status;
	}

	Ext.Ajax.request({
		url : './index.php/Project_setup/code_list/'+type,
		method: 'POST',
		params : {
			pr_seq : project_seq
		},
		success: function ( result, request ) {
			var result = Ext.decode(result.responseText);

			var panel = Ext.create("Ext.form.Panel",{
				title	:	title,
				layout: {
					type	: 'hbox',
					align	: 'stretch',
					defaultMargins: {top: 0, right: 0, bottom: 0, left: 0}
				},
				margin: 5,
				items	:[]
			});

			for(var i=0; i<result.data.length; i++){
				panel.add({
					flex: 1,
					xtype: 'checkboxfield',
					boxLabel: result.data[i].pco_name,
					pco_seq: result.data[i].pco_seq,
					checked: (result.data[i].pco_is_use == 'Y')?true:false
				});
			}

			switch(type){
				case "status":
					Ext.getCmp('projectsetupCodeDefectPanel').add(panel);
					getProjectCode(project_seq,'severity',Otm.def+' '+Otm.def_severity);
					break;
				case "severity":
					Ext.getCmp('projectsetupCodeDefectPanel').add(panel);
					getProjectCode(project_seq,'priority',Otm.def+' '+Otm.def_priority);
					break;
				case "priority":
					Ext.getCmp('projectsetupCodeDefectPanel').add(panel);
					getProjectCode(project_seq,'frequency',Otm.def+' '+Otm.def_frequency);
					break;
				case "frequency":
					Ext.getCmp('projectsetupCodeDefectPanel').add(panel);
					getProjectCode(project_seq,'tc_item',Otm.tc_input_item);
					break;
				case "tc_item":
					Ext.getCmp('projectsetupCodeTCPanel').add(panel);
					getProjectCode(project_seq,'tc_result',Otm.tc_execution_result_item_all);
					break;
				case "tc_result":
					Ext.getCmp('projectsetupCodeTCPanel').add(panel);
					break;
			}

		},
		failure: function ( result, request ) {
			alert("fail");
		}
	});
}


/**
	UserForm Info of Project
*/

function getProjectUserForm(project_seq){

	Ext.Ajax.request({
		url : './index.php/Project_setup/userform_list',
		method: 'POST',
		params : {
			pr_seq : project_seq
		},
		success: function ( result, request ) {
			var result = Ext.decode(result.responseText);

			var defect_panel = Ext.create("Ext.Panel",{
				title	: Otm.def+' '+Otm.def_item,
				id		: 'projectsetup_userForm_defect_Panel',
				layout: {
					type	: 'hbox',
					align	: 'stretch',
					defaultMargins: {top: 0, right: 0, bottom: 0, left: 0}
				},
				margin: 5,
				items	:[]
			});

			for(var i=0; i<result.data.length; i++){
				if(result.data[i].pc_category == "ID_DEFECT"){
					defect_panel.add({
						flex	: 1,
						xtype	: 'checkboxfield',
						id		: 'userfrom_defect-'+result.data[i].pc_seq,
						boxLabel	: result.data[i].pc_name+'('+result.data[i].pc_formtype+')',
						pc_seq	: result.data[i].pc_seq,
						checked: (result.data[i].pc_is_use == 'Y')?true:false
					});
				}
			}
			Ext.getCmp('projectsetupUserFormPanel').add(defect_panel);

			var tc_panel = Ext.create("Ext.Panel",{
				title	: Otm.tc+' '+Otm.def_item,
				id		: 'projectsetup_userForm_tc_Panel',
				layout: {
					type	: 'hbox',
					align	: 'stretch',
					defaultMargins: {top: 0, right: 0, bottom: 0, left: 0}
				},
				margin: 5,
				items	:[]
			});

			for(var i=0; i<result.data.length; i++){
				if(result.data[i].pc_category == "ID_TC"){
					tc_panel.add({
						flex	: 1,
						xtype	: 'checkboxfield',
						id		: 'userfrom_tc-'+result.data[i].pc_seq,
						boxLabel	: result.data[i].pc_name+'('+result.data[i].pc_formtype+')',
						pc_seq	: result.data[i].pc_seq,
						checked: (result.data[i].pc_is_use == 'Y')?true:false
					});
				}
			}
			Ext.getCmp('projectsetupUserFormPanel').add(tc_panel);
		},
		failure: function ( result, request ) {
			alert("fail");
		}
	});
}

function getProjectSetup_setup_form(){

	var subjectForm = {
		id: 'project_setup_pname',
		anchor: '100%',
		fieldLabel: Otm.pjt_name+'(*)',
		allowBlank : false,
		xtype: 'textfield'
	};
	var startDateForm = {
		id: 'project_setup_pstartdate',
		anchor: '50%',
		fieldLabel: Otm.com_start_date+'(*)',
		format:"Y-m-d",
		editable: false,
		allowBlank : false,
		endDateField: 'project_setup_penddate',vtype: 'daterange',
		xtype: 'datefield'
	};
	var endDateForm = {
		id: 'project_setup_penddate',
		anchor: '50%',
		fieldLabel: Otm.com_end_date,
		format:"Y-m-d",
		editable: false,
		startDateField: 'project_setup_pstartdate',vtype: 'daterange',
		xtype: 'datefield'
	};
	var descriptionForm = {
		id: 'project_setup_pdescription',
		anchor: '100%',
		fieldLabel: Otm.com_description,
		height:'100',
		xtype: 'textarea'
	};

	var projectForm = Ext.create("Ext.form.Panel",{
		id:'project_setup_projectForm',
		bodyStyle:'padding:10px;',
		border:false,
		anchor:'100%',
		items:[{
			id: 'project_setup_pseq',
			anchor: '100%',
			xtype: 'hiddenfield'
		},{
			xtype:'fieldset',
			title:Otm.pjt_info,
			width:'100%',anchor:'100%',flex:1,
			items:[subjectForm,startDateForm,endDateForm,descriptionForm]
		}]
	});

	return projectForm;
}


/**
	Call Project Setup data
*/
function get_project_data()
{
	var node = get_select_project_tree_node();
	if(node && node[0].data.pr_seq){

		Ext.Ajax.request({
			url : './index.php/Project_setup/project_info',
			method: 'POST',
			params : {
				pr_seq : node[0].data.pr_seq
			},
			success: function ( result, request ) {
				var result = Ext.decode(result.responseText);

				var sdate = result.data[0].pr_startdate.substr(0,10);
				var edate = result.data[0].pr_enddate.substr(0,10);

				Ext.getCmp('project_setup_pseq').setValue(node[0].data.pr_seq);
				Ext.getCmp('project_setup_pname').setValue(result.data[0].pr_name);
				Ext.getCmp('project_setup_pstartdate').setValue(sdate);
				Ext.getCmp('project_setup_penddate').setValue(edate);
				Ext.getCmp('project_setup_pdescription').setValue(result.data[0].pr_description);

			},
			failure: function ( result, request ) {
				alert("fail");
			}
		});
	}else{
		Ext.Msg.alert('OTM','Project Setup : Project Select error');
	}
};

get_project_data();

var projectsetup_main_panel =  Ext.create("Ext.form.Panel",{
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
	items	:[getProjectSetup_setup_form()],
	buttons : [
	{
		text	:Otm.com_save,
		disabled: true,
		formBind: true,
		iconCls:'ico-save',
		handler:function(btn){

			var params = {
				pr_seq			: Ext.getCmp('project_setup_pseq').getValue(),
				pr_name			: Ext.getCmp('project_setup_pname').getValue(),
				pr_startdate	: Ext.getCmp('project_setup_pstartdate').getValue(),
				pr_enddate		: Ext.getCmp('project_setup_penddate').getValue(),
				pr_description	: Ext.getCmp('project_setup_pdescription').getValue()
			};

			if(Ext.getCmp("projectsetup_userForm_defect_Panel"))
			{
				var userform_list = new Array();
				var userform_items = Ext.getCmp("projectsetup_userForm_defect_Panel").items;
				for(var i=0; i<userform_items.length; i++){

					if(userform_items.items[i].checked){
						userform_list.push(userform_items.items[i].pc_seq);
					}
				}

				params.userfrom_list = Ext.encode(userform_list);
			}

			if(Ext.getCmp("projectsetupCodeDefectPanel"))
			{
				var code_list = new Array();
				var code_defect_items = Ext.getCmp("projectsetupCodeDefectPanel").items;
				for(var i=0; i<code_defect_items.length; i++){
					var item = code_defect_items.items[i].items;
					for(var j=0; j<item.length; j++){
						if(item.items[j].checked){
							code_list.push(item.items[j].pco_seq);
						}
					}
				}

				var code_tc_items = Ext.getCmp("projectsetupCodeTCPanel").items;
				for(var i=0; i<code_tc_items.length; i++){
					var item = code_tc_items.items[i].items;
					for(var j=0; j<item.length; j++){
						if(item.items[j].checked){
							code_list.push(item.items[j].pco_seq);
						}
					}
				}
				params.code_list = Ext.encode(code_list);
			}

			Ext.Ajax.request({
				url : './index.php/Project_setup/project_update',
				method: 'POST',
				params : params,
				success: function ( result, request ) {
					var result = Ext.decode(result.responseText);
					Ext.Msg.alert('OTM',Otm.com_msg_save);
					if(Ext.getCmp('projectGrid'))
						Ext.getCmp('projectGrid').getStore().reload();

					if(Ext.getCmp('project_treePanel'))
						Ext.getCmp('project_treePanel').getStore().load();
				},
				failure: function ( result, request ) {
					alert("fail");
				}
			});

		}
	}]
});

Ext.onReady(function(){
	var main_panel = {
		layout		: 'border',
		defaults	: {
			collapsible	: false,
			split		: true,
			bodyStyle	: 'padding:0px'
		},
		items		: [projectsetup_main_panel]
	};
	Ext.getCmp('project_setup_main').add(main_panel);
	Ext.getCmp('project_setup_main').doLayout();
});
</script>
