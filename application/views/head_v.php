<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1" />
		<title>OTM</title>
<?
	$login_user_email = $this->session->userdata('mb_email');
	$login_user_isadmin = $this->session->userdata('mb_is_admin');

	$resource_url = "./resource/";
	$ext_url = $resource_url."lib/ext-5.0/build/";
	$ext_file = "ext-all.js";
	$skin_name = "ext-theme-gray";

	$user_lang = $this->session->userdata('mb_lang');
?>
<link rel="stylesheet" type="text/css" href="<?=$ext_url?>packages/<?=$skin_name?>/build/resources/<?=$skin_name?>-all.css">
<script type="text/javascript" src="<?=$ext_url?>/<?=$ext_file?>"></script>

<script type="text/javascript" src="<?=$ext_url?>packages/sencha-charts/build/sencha-charts.js"></script>
<script type="text/javascript" src="<?=$ext_url?>packages/ext-locale/build/ext-locale-<?=$this->session->userdata('mb_lang')?>.js"></script>

<script type="text/javascript" src="<?=$resource_url?>js/Ext.define.js"></script>
<script type="text/javascript" src="<?=$resource_url?>js/lib.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$resource_url?>css/otm.css">

<script type="text/javascript">
	Ext.tip.QuickTipManager.init();
	var login_user_email = '<?=$login_user_email?>';
	var login_user_isadmin = '<?=$login_user_isadmin?>';
	var user_lang = '<?=$user_lang?>';

	function register_form_chk()
	{
		Ext.Ajax.request({
			url		: './index.php/User/user_view',
			params	: {},
			method	: 'POST',
			success	: function ( result, request ) {
				if(result.responseText){
					var obj = Ext.decode(result.responseText);
					if(obj.totalCount == 1){
						register_form_window(obj.data[0]);
					}
				}
			},
			failure: function ( result, request ) {
			}
		});
	}

	function register_form_window(data){
		var win = Ext.getCmp('register_form_window');
		if(win){
			win.close();
		}

		var register_form = Ext.create("Ext.form.Panel",{
			id		: 'register_form',
			border	: false,
			bodyStyle: 'padding: 10px;',
			autoScroll: true,
			labelWidth:'10',
			items	: [{
				xtype:'hiddenfield',
				name:'mb_email',
				value: "<?=$this->session->userdata('mb_email')?>"
			},{
				anchor: '100%',
				fieldLabel: Otm.com_email,
				minLength:2,
				readOnly:true,
				disabled:true,
				maxLength:100,
				xtype:'textfield',
				value: "<?=$this->session->userdata('mb_email')?>"
			},{
				xtype:'textfield',
				fieldLabel: Otm.com_pw,
				name:'mb_pw',
				allowBlank : true,
				inputType	: 'password'
			},{
				xtype:'textfield',
				fieldLabel: Otm.com_pwok,
				allowBlank : true,
				name:'mb_pw_re',
				inputType	: 'password'
			},{
				xtype:'textfield',
				fieldLabel: Otm.com_name+'(*)',
				allowBlank : false,
				name:'mb_name',
				value : data.mb_name
			},{
				xtype:'textfield',
				fieldLabel: Otm.com_contact_number,
				allowBlank : true,
				name:'mb_tel',
				value : data.mb_tel
			},{
				xtype:'textarea',
				fieldLabel: Otm.com_description,
				anchor:'100%',
				name:'mb_memo',
				height:100,
				allowBlank : true,
				value : data.mb_memo
			}],
			buttons:[{
				text	: Otm.com_save,
				iconCls	: 'ico-save',
				disabled: true,
				formBind: true,
				handler	: function(btn){
					if(Ext.getCmp("register_form").getForm().isValid()){
						Ext.getCmp("register_form").getForm().submit({
							url : './index.php/User/register_update',
							method:'POST',
							params: {},
							waitMsg: Otm.com_msg_processing_data,
							success: function(rsp, result){
								location.href='/';
							},
							failure: function(rsp, result, r){
								Ext.Msg.alert('OTM',result.response.responseText);
							}
						});
					}
				}
			},{
				text	: Otm.com_close,
				handler	: function(btn){
					Ext.getCmp('register_form_window').close();
				}
			}]
		});

		Ext.create('Ext.window.Window', {
			title	: Otm.com_set_privacy,
			id		: 'register_form_window',
			height	: 350,
			width	: 400,
			layout	: 'fit',
			resizable : false,
			modal	: true,
			constrainHeader: true,
			items	: [register_form]
		}).show();
	}

	var h_userLanguage = {
		xtype		: 'combo',
		store		: language_store,
		queryMode	: 'local',
        hideLabel	: true,
		displayField: 'language',
		valueField	: 'code',
		editable	: false,
		value		: "<?=$this->session->userdata('mb_lang')?>",
		listeners	: {
			select:function(combo,record,index){
				Ext.Ajax.request({
					url : './index.php/User/language_update',
					method: 'POST',
					params: {mb_lang:record[0].data.code},
					success: function ( result, request ) {
						location.href='/';
					},
					failure: function ( result, request ) {
						alert("fail");
					}
				});
			}
		}
	};

	var h_userNick_info = {
		xtype	: 'button',
		margin	: '0 0 0 10',
		text	: "<?=$this->session->userdata('mb_name')?>",
		handler	: function(){
			register_form_chk();
		}
	};

	var h_lan_com = {
		xtype		: 'combo',
		margin		: '0 5 0 5',
		hideLabel	: true,
		store		: ["korea","english"],
		displayField: 'value',
		typeAhead	: true,
		queryMode	: 'local',
		emptyText	: 'Select',
		width		: 100,
		value		: 'korea',
		indent		: true,
		triggerAction: 'all',
		selectOnFocus: true
	};

	var h_logout_btn = {
		xtype	: 'button',
		margin	: '0 0 0 10',
		text	: Otm.com_logout,
		handler	: function(){
			Ext.Ajax.request({
				url		: './index.php/Login/logout',
				method	: 'POST',
				success	: function ( result, request ) {
					if(result.responseText){
						if(result.responseText == "logout"){
							location.href='/';
						}
					}
				},
				failure	: function ( result, request ) {
					alert("fail");
				}
			});
		}
	};

	var h_info_btn = {
		xtype	: 'button',
		margin	: '0 0 0 10',
		text	: 'INFO',
		handler	: function(){
			Ext.create('Ext.window.Window', {
				title		: 'OTM Infomation',
				id			: 'info_window',
				height		: 400,
				width		: 550,
				layout		: 'fit',
				resizable	: false,
				modal		: true,
				constrainHeader: true,
				items		: [{
					layout	: 'fit',
					autoScroll	: true,
					bodyStyle: 'padding:10px;',
					scope	: this,
					loader	: {
						autoLoad: true,
						loadMask: true,
						params	: {},
						scripts	: true,
						url		: './index.php/Plugin/core_info',
					},
					items	: []
				}],
				buttons		:[{
					text	:Otm.com_close,
					handler	:function(btn){
						Ext.getCmp('info_window').close();
					}
				}]
			}).show();
		}
	};

	var head_logo_html = "<a href='/' style='cursor:pointer;'><img src='./resource/img/otm_logo1.png' height='34' style='margin-top:5px;' /></a>";

	if(user_lang == 'project-ko'){
		head_logo_html = "<div><div onclick='location.href=\"/\"' style='width:320px;color:white;font-size:30px;font-weight:bold;cursor:pointer;'>Nipa 과제 관리 시스템</div></div>";
	}

	var head_layout = {
		region		: 'north',
		layout		: 'hbox',
		autoScroll	: false,
		split		: false,
        collapsible	: false,
		height		: 60,
		bodyStyle	: 'padding:0px;background:#2da5da;',
		layoutConfig: {
			align:'middle'
		},
		items:[{
			xtype		: 'panel',
			border		: false,
			flex		: 1,
			bodyStyle	: 'padding:10px;margin-left:10px;background:#2da5da;',
			html		: head_logo_html
		},{
			border		: false,
			layout		: 'hbox',
			bodyStyle	: 'text-align:right;padding:20px;background:#2da5da;',
			items		: [h_userLanguage,h_userNick_info,h_logout_btn,h_info_btn]
		}]
	};
</script>
<CENTER><div id="progressBar" style="padding:50 10 10 10;"></div></CENTER>
<script>
var pbar = new Ext.ProgressBar({
		text	: 'Initializing...',
		id		: 'pbar',
		hidden	: true,
		autoWidth: false,
		width	: '500px',
		cls		: 'left-align'
	});

function myUpdateProgress(percent,msg){
	if(percent == 100){
		Ext.getCmp('pbar').hide();
	}else{
		Ext.getCmp('pbar').show();
	}
	Ext.getCmp('pbar').updateProgress(parseInt(percent)/100, percent + '% - '+msg);
}
</script>