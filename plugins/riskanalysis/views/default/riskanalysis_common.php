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

function move_page(type)
{
	var title = '';
	var id = '';
	var url = '';

	switch(type)
	{
		case 'individual':
			title = 'individual';
			id = 'riskanalysis_individual';
			url = './index.php/Plugin_view/riskanalysis/riskanalysis_individual';
			break;
		case 'discussion':
			title = 'discussion';
			id = 'riskanalysis_discussion';
			url = './index.php/Plugin_view/riskanalysis/riskanalysis_discussion';
			break;
		case 'setup':
			title = 'Setup';
			id = 'riskanalysis_setup';
			url = './index.php/Plugin_view/riskanalysis/riskanalysis_setup';
			break;
		default:
			title	= 'main';
			id	= 'riskanalysis_main';
			url = "./index.php/Plugin_view/riskanalysis";
			break;
	}


	Ext.getCmp('riskanalysis').removeAll();

	Ext.getCmp('riskanalysis').add({
		layout	:'fit',
		xtype	: 'panel',
		//title	: title,
		id		: id,
		closable: false,
		plain	: true,
		scope	: this,
		loader	: {
			autoLoad: true,
			loadMask: true,
			scripts	: true,
			url : url,
			params: {project_seq:project_seq}
		},
		listeners:{
			render: function(tab){
			},
			activate : function(tabpanel){
			}
		}
	}).show();
}

function riskitem_view_panel(obj)
{
	var target_panel = obj.target;
	Ext.getCmp(target_panel).update('');
	Ext.getCmp(target_panel).removeAll();

	riskanalysis_riskitem_requirement_unlink_store.removeAll();
	riskanalysis_riskitem_requirement_link_store.removeAll();
	//riskanalysis_riskitem_tc_store.removeAll();

	//if(obj.ri_seq == ''){
	//	return;
	//}else{
	//}
	var riskanalysis_riskitem_east_panel = Ext.getCmp('riskanalysis_riskitem_east_panel');

	var Records = Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected.items;
		
	if(Records.length > 1){
		console.log('a');
		riskanalysis_riskitem_east_panel.collapse();
		//riskanalysis_riskitem_requirement_unlink_store.load({params:{'ri_seq':''}});
		//riskanalysis_riskitem_requirement_link_store.load({params:{'ri_seq':''}});
		//riskanalysis_riskitem_tc_store.load();

		//riskanalysis_riskitem_requirement_unlink_store.removeAll();
		//riskanalysis_riskitem_requirement_unlink_store.sync();

		//riskanalysis_riskitem_requirement_link_store.removeAll();
		//riskanalysis_riskitem_requirement_link_store.sync();		
		return;
	}
	/*else if(Records.length == 1){
		//if(!obj.pr_seq && !obj.ri_seq){
		console.log('b');	
		//riskanalysis_riskitem_east_panel.collapse();
		//riskanalysis_riskitem_requirement_unlink_store.removeAll();
		//riskanalysis_riskitem_requirement_link_store.removeAll();
		//return;
		obj.ri_seq = Records[0].data.ri_seq;
		obj.pr_seq = project_seq;
	}else{
		console.log('c');
		//riskanalysis_riskitem_east_panel.expand();
	}
	*/

	Ext.Ajax.request({
		url : "./index.php/Plugin_view/riskanalysis/view_riskitem",
		params : obj,
		method: 'POST',
		success: function ( result, request ) {
			if(result.responseText){
				var riskitem_info = _getCustomform_view(riskitem_customform_store,result.responseText);

				var printFile = _common_fileView('riskitemGrid',Ext.decode(riskitem_info.data.fileform));
				riskitem_info.data.fileform = printFile;

				var default_fieldset = {
					xtype		: 'fieldset',
					collapsible	: false,
					collapsed	: false,
					border		: false,
					items		: [{
						xtype		: 'displayfield',
						fieldLabel	: 'seq',
						hidden		: true,
						value		: riskitem_info.data.ri_seq
					},{
						xtype		: 'displayfield',
						fieldLabel	: Otm.com_creator,
						value		: riskitem_info.data.writer
					},{
						xtype		: 'displayfield',
						fieldLabel	: Otm.com_date,
						value		: (riskitem_info.data.regdate)?riskitem_info.data.regdate.substr(0,10):''
					},{xtype : 'menuseparator',width : '100%'}]
				};

				var content_fieldset = {
						xtype		: 'fieldset',
						collapsible	: false,
						collapsed	: false,
						border		: false,
						items		: [{
							xtype		: 'displayfield',
							fieldLabel	: Otm.com_subject,
							value		: riskitem_info.data.ri_subject
						},{
							xtype		: 'displayfield', multiline	: true,
							fieldLabel	: Otm.com_description,
							value		: riskitem_info.data.ri_description
						}]
					};

				var userform_fieldset = {
					xtype		: 'fieldset',
					collapsible	: false,
					collapsed	: false,
					border		: false,
					items		: [{
						border		: false,
						html		: riskitem_info.data.user_form
					},{xtype : 'menuseparator',width : '100%'}]
				};

				var attached_file_fieldset = {
					xtype		: 'fieldset',
					title		: Otm.com_attached_file,
					collapsible	: false,
					collapsed	: false,
					//border		: false,
					items		: [{
						border		: false,
						html		: riskitem_info.data.fileform
					},{xtype : 'menuseparator',width : '100%'}]
				};

				var view_form = {
					xtype		: 'form',
					collapsible	: false,
					border		: false,
					bodyStyle	: 'padding: 10px;',
					autoScroll	: true,
					items	: [
						default_fieldset,
						content_fieldset,
						userform_fieldset,						
						attached_file_fieldset
					]
				};

				Ext.getCmp(target_panel).removeAll();
				Ext.getCmp(target_panel).add({
					region	: 'center', layout:'fit', xtype:'panel',
					animation: false, autoScroll: true,
					items : [view_form]
				});
				
				riskanalysis_riskitem_requirement_unlink_store.load({params:{'ri_seq':obj.ri_seq}});
				riskanalysis_riskitem_requirement_link_store.load({params:{'ri_seq':obj.ri_seq}});

				riskanalysis_riskitem_tc_store.reload();//{params:{'ri_seq':obj.ri_seq}});

				riskanalysis_riskitem_east_panel.expand();
			}
		},
		failure: function ( result, request ) {
			Ext.Msg.alert("OTM","DataBase Select Error");
		}
	});
}


function riskitem_save(saveType){

	var URL = "./index.php/Plugin_view/riskanalysis/create_riskitem";
	if(Ext.getCmp("riskitem_seqForm").getValue() >= 1){
		URL = "./index.php/Plugin_view/riskanalysis/update_riskitem";
	}
	var ri_seq = Ext.getCmp("riskitem_seqForm").getValue();

	var user_customform_result = new Array();
	var commit_info = Ext.getCmp("riskitem_writeForm").getForm().getValues();
	for(var i=0;i<customform_seq.length;i++){
		user_customform_result.push({
			name	: customform_seq[i].name,
			seq		: customform_seq[i].seq,
			type	: customform_seq[i].type,
			value	: eval("commit_info.custom_"+customform_seq[i].seq)
		});
	}

	var select = Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().selected;

	if(Ext.getCmp("riskitem_writeForm").getForm().isValid()){

		var params = {
				writer : (select.items[0] && select.items[0].data.writer)?select.items[0].data.writer:'',
				pr_seq	: project_seq,
				custom_form : Ext.encode(user_customform_result)
			};

		Ext.getCmp("riskitem_writeForm").getForm().submit({
			url: URL,
			method:'POST',
			params: params,
			success: function(rsp, o){
				var info = Ext.decode(o.response.responseText);
				ri_seq = info.data.ri_seq;

				riskanalysis_riskitem_store.reload({
					callback:function(){
						//if(!ri_seq){
						//	Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().select(0);
						//}else{
							for(var i=0;i<riskanalysis_riskitem_store.data.length;i++){
								if(riskanalysis_riskitem_store.data.items[i].data.ri_seq == ri_seq){
									Ext.getCmp("riskanalysis_riskitemGrid").getSelectionModel().select(i);
								}
							}
						//}
					}
				})
				return;
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
}
</script>