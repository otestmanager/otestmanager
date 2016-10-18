<!DOCTYPE html>
<html>
	<head>
		<title>OTM</title>
<?
	$ext_url = "./resource/lib/ext-5.0";
	$skin_name = "ext-theme-gray";
	$resource_url = "./resource";
?>
		<link rel="stylesheet" type="text/css" href="<?=$ext_url?>/build/packages/<?=$skin_name?>/build/resources/<?=$skin_name?>-all.css">
		<script type="text/javascript" src="<?=$ext_url?>/build/ext-all.js"></script>
		<script type="text/javascript" src="<?=$ext_url?>/build/packages/ext-locale/build/ext-locale-en.js"></script>

		<script type="text/javascript" src="<?=$resource_url?>/js/lib.js"></script>
		<link rel="stylesheet" type="text/css" href="./resource/css/otm.css">
	</head>
	<body>

<script type="text/javascript">

	function login(){
		if(Ext.getCmp("is_email_save").getValue()){
			set_JSCookie('cookie_email', Ext.getCmp("mb_email").getValue(), 1);
		}else{
			set_JSCookie('cookie_email', '', -1);
		}

		Ext.Ajax.request({
			url : './index.php/Login/login_check',
			params :{
				mb_email	: Ext.getCmp('mb_email').getValue(),
				mb_pw		: Ext.getCmp('mb_pw').getValue()
			},
			method: 'POST',
			success: function ( result, request ) {
				if(result.responseText){
					if(result.responseText == "Login"){
						location.href='./';
					}else{
						Ext.getCmp("message").setHtml(result.responseText);
					}
				}
			},
			failure: function ( result, request ) {
				Ext.Msg.alert('OTM','Fail');
			}
		});
	}

	var lang = {
		xtype			: 'combo',
		margin			: '0 5 0 5',
		hideLabel		: true,
		store			: ["korea","english"],
		displayField	: 'value',
		typeAhead		: true,
		queryMode		: 'local',
		triggerAction	: 'all',
		emptyText		: 'Select',
		selectOnFocus	: true,
		width			: 100,
		value			: 'korea',
		indent			: true
	};

	var head_layout = {
		title			: "<a href='./' style='cursor:pointer;'><img src='./resource/img/otm2.png' height=30 width=100/></a>",
		region			:'north',
		split			: false,
		collapsible		: false,
		bodyStyle		: 'margin:0;',
		header			: {
			titlePosition: 0,
			style	: 'background:#2da5da;',
			items:[lang]
		}
	};

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
			xtype	: 'panel',
			border	: false,
			flex	: 1,
			bodyStyle: 'padding:10px;margin-left:10px;background:#2da5da;',
			html	: "<a href='/' style='cursor:pointer;'><img src='./resource/img/otm_logo1.png' height='34' style='margin-top:5px;' /></a>"
		}]
	};

	var footer_layout = {
		title		: 'Footer',
		region		: 'south',
		xtype		: 'box',
		id			: 'footer',
		split		: false,
		collapsible	: false,
		height		: 30,
		titleAlign	: 'center',
		html		: '<h1>'+Otm.com_msg_company_info+'</h1>'
	};

	var center_layout = {
		region			: 'center',
		collapsible		: false,
		layout			: 'fit',
		xtype			: 'panel',
		border			: false,
		frame			: false,
		bodyStyle		: "background:#2da5da;background-image:url(./resource/img/login_background1.png); background-repeat:no-repeat; background-position:center center;background-size: 100% 100%;",
		layout: {
			type	: 'vbox',
			align	: 'center',
			pack	: 'center'
		},
		items	: [{
				xtype	: 'label',
				id		: 'message',
				width	: 300,
				height	: 50,
				margin	: 10,
				html	: ''
		},{
			xtype		: 'textfield',
			id			: 'mb_email',
			emptyText	: 'Email',
			margin		: '10',
			width		: 300,
			height		: 33,
			allowBlank	: false,
			listeners	: {
				focus : function(){
					Ext.getCmp("message").setHtml('');
				},
				afterrender: function(fld) {
					fld.focus(false, 500);
				},
				'render' : function(cmp) {
					cmp.getEl().on('keypress', function(e) {
						if (e.getKey() == e.ENTER) {
							login();
						}
					});
				}
			}
		},{
			xtype		: 'textfield',
			id			: 'mb_pw',
			emptyText	: 'Password',
			margin		: '10',
			width		: 300,
			height		: 33,
			allowBlank	: false,
			inputType	: 'password',
			listeners	: {
				focus : function(){
					Ext.getCmp("message").setHtml('');
				},
				'render' : function(cmp) {
					cmp.getEl().on('keypress', function(e) {
						if (e.getKey() == e.ENTER) {
							login();
						}
					});
				}
			}
		},{
			xtype		: 'button',
			width		: 300,
			height		: 33,
			margin		: '10',
			text		: 'Login',
			handler		: function(e) {
				login();
			}
		},{
			width		: 300,
			height		: 33,
			border		: false,
			frame		: false,
			bodyStyle	: "background:#2da5da;",
			items	: [{
				xtype	: 'checkbox',
				id:'is_email_save',
				boxLabel: 'Save Email'
			}]
		},{
			xtype	: 'label',
			height	: 50,
			html	: "<font color=#ffffff>"+Otm.com_msg_company_info+"</font>"
		}]
	};

	Ext.onReady(function(){
		var viewport = new Ext.Viewport({
			layout		: 'border',
			defaults	: {
				collapsible	: true,
				split		: true,
				bodyStyle	: 'padding:0px'
			},
			items		: [
				center_layout
			]
		});

		var user_email = get_JSCookie('cookie_email');
		if(user_email != ""){
			Ext.getCmp("mb_email").setValue(user_email);
			Ext.getCmp("is_email_save").setValue(true);
		}
	});
</script>

	</body>
</html>