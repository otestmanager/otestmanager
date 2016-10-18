var mb_is_admin = "<?=$this->session->userdata('mb_is_admin')?>";
Ext.create('Ext.window.Window', {
	title		: Otm.com_view,
	id			: 'popup_view',
	height		: 600,
	width		: 800,
	minWidth	: 500,
	closeAction	: 'hide',
	autoScroll	: true,
	modal		: true,
	constrainHeader: true,
	items		: [],
	buttons:[{
		text:Otm.com_close,
		handler:function(btn){
			Ext.getCmp('popup_view').hide();
		}
	}]
});

/**
*	Languages Controll
*		START
*/
var total_languages = [
	['en', 'English', 'ascii'],
	['ko', 'Korean']
];

//total_languages.push(['project-ko', 'Project Korean']);

var language_store = Ext.create('Ext.data.ArrayStore', {
	fields: ['code', 'language'],
	data : total_languages
});
/**
*	Languages Controll
*		END
*/


/**
*	JSCookie Controll
*		START
*/
function set_JSCookie(cName, cValue, cDay)
{
	var expire = new Date();
	expire.setDate(expire.getDate() + cDay);
	cookies = cName + '=' + escape(cValue) + '; path=/ ';
	if(typeof cDay != 'undefined') cookies += ';expires=' + expire.toGMTString() + ';';
	document.cookie = cookies;
}

function get_JSCookie(cName)
{
	cName = cName + '=';
	var cookieData = document.cookie;
	var start = cookieData.indexOf(cName);
	var cValue = '';
	if(start != -1){
	   start += cName.length;
	   var end = cookieData.indexOf(';', start);
	   if(end == -1)end = cookieData.length;
	   cValue = cookieData.substring(start, end);
	}
	return unescape(cValue);
}
/**
*	JSCookie Controll
*		END
*/


/**
*	Export Controll
*		START
*/
function export_data(url,method)
{
	var fullUrl = './index.php/Export/'+url;
	if(method){
		fullUrl += "?"+method;
	}
	window.open(fullUrl,'hiddenframe');
	setTimeout('closeHiddenFrame()',600000);
}

function closeHiddenFrame()
{
	window.open('about:blank','hiddenframe');
}
/**
*	Export Controll
*		END
*/


/**
*	File Controll
*		START
*/
function _common_fileDownload(pr_seq,category,target_seq,of_no)
{
	Ext.Ajax.request({
		url : "./index.php/FileDownload/file_download_chk",
		params :{
			pr_seq : pr_seq,
			category : category,
			target_seq : target_seq,
			of_no : of_no
		},
		method: 'POST',
		success: function ( result, request ) {
			var file_info = Ext.decode(result.responseText);
			if(file_info.success == true){
				var file_url = './index.php/FileDownload/file_download?pr_seq='+pr_seq+'&category='+category+'&target_seq='+target_seq+'&of_no='+of_no;
				window.open (file_url,'hiddenframe','');
			}else{
				switch(file_info.msg){
					case "no_file":
						Ext.Msg.alert("OTM","Not File");
					break;
					case "no_access":
						Ext.Msg.alert("OTM","Not Access");
					break;
				}
			}
		},
		failure: function ( result, request ) {
			alert("fail");
		}
	});
}
function _common_fileDelete(grid_id,pr_seq,category,target_seq,of_no)
{
	Ext.Msg.confirm("OTM",Otm.com_msg_deleteConfirm, function (btn) {
		if(btn == "yes") {
			Ext.Ajax.request({
				url : "./index.php/FileDownload/file_delete",
				params :{
					pr_seq : pr_seq,
					category : category,
					target_seq : target_seq,
					of_no : of_no
				},
				method: 'POST',
				success: function ( result, request ) {
					var file_info = Ext.decode(result.responseText);
					if(file_info.success == true){
						var div_id = category+"_"+target_seq+"_"+of_no;
						document.getElementById(div_id).style.display="none";
					}else{
						switch(file_info.msg){
							case "no_access":
								Ext.Msg.alert("OTM","Not Access");
							break;
						}
					}
				},
				failure: function ( result, request ) {
					Ext.Msg.alert("OTM","Fail");
				}
			});
		}else{
			return;
		}
	});
}

function _common_fileView(grid_id,fileInfo)
{
	var imageArr = new Array("gif","png","jpg","jpeg");

	var printFile = "";
	for(var i=0;i<fileInfo.length;i++){
		var div_id = fileInfo[i].otm_category+"_"+fileInfo[i].target_seq+"_"+fileInfo[i].of_no;

		var is_imageInfo = fileInfo[i].of_source.split(".");
		var extension = is_imageInfo[is_imageInfo.length - 1];
		var isImg = false;
		extension = extension.toLowerCase();

		for(var k=0;k<imageArr.length;k++){
			if(extension == imageArr[k]){
				isImg = true;
				break;
			}
		}
		var preview = "";
		var div_height = '25px;';
		if(isImg){
			if(fileInfo[i].of_width > 0 && fileInfo[i].of_height > 0){
				var imageInfo = "./uploads/files/"+fileInfo[i].otm_project_pr_seq+"/"+fileInfo[i].of_file;
				var of_source = escape(fileInfo[i].of_source);

				preview = '<div style="float:left;margin-left:30px;height:100px;" ><a href=javascript:_common_imageView("'+fileInfo[i].otm_project_pr_seq+'","'+fileInfo[i].of_file+'","'+of_source+'","'+fileInfo[i].of_width+'","'+fileInfo[i].of_height+'")><img style="margin-top:5px;border:3px solid gray;" src="'+imageInfo+'" width="100" height="100"></a></div>';
				div_height = '110px;';
			}
		}

		printFile += '<div id="'+div_id+'" style="height:'+div_height+';border-bottom:1px dotted gray;clear:both;">';
		printFile += '<div style="float:left;"><a href=javascript:_common_fileDownload("'+fileInfo[i].otm_project_pr_seq+'","'+fileInfo[i].otm_category+'","'+fileInfo[i].target_seq+'","'+fileInfo[i].of_no+'");>'+fileInfo[i].of_source+'</a></div>';
		printFile += preview;
		printFile += '<div style="float:right;"><a href=javascript:_common_fileDelete("'+grid_id+'","'+fileInfo[i].otm_project_pr_seq+'","'+fileInfo[i].otm_category+'","'+fileInfo[i].target_seq+'","'+fileInfo[i].of_no+'");>'+Otm.com_remove+'</a></div>';
		printFile += '</div>';
	}
	return printFile;
}

function _common_imageView(pr_seq,of_file,of_source,of_width,of_height)
{
	if(pr_seq && of_file && of_source){
		var win = Ext.getCmp('image_preview');
		if(win){
			win.close();
		}
		of_source = unescape(of_source);

		var imageInfo = "./uploads/files/"+pr_seq+"/"+of_file;

		var win_width = 0;
		var win_height = 0;
		var isAutoScroll = true;
		var doc_width = document.body.clientWidth - 50;
		var doc_height = document.body.clientHeight - 50;
		var html = "";

		if(doc_width > of_width && doc_height > of_height){
			isAutoScroll = false;
			win_width = parseInt(of_width)+10;
			win_height = parseInt(of_height) + 20;
		}else if(doc_width < of_width && doc_height < of_height){
			isAutoScroll = true;
			win_width = parseInt(doc_width);
			win_height = parseInt(doc_height);
		}else if(doc_width > of_width && doc_height < of_height){
			isAutoScroll = true;
			win_width = parseInt(of_width)+10;
			win_height = parseInt(doc_height);
		}else if(doc_width < of_width && doc_height > of_height){
			isAutoScroll = true;
			win_width = parseInt(doc_width)+10;
			win_height = parseInt(of_height);
		}
		html = '<img src="'+imageInfo+'" width="'+of_width+'" height="'+of_height+'">';

		Ext.create('Ext.window.Window', {
			title: of_source,
			id:'image_preview',
			width: win_width,
			height: win_height,
			layout: 'border',
			collapsible: false,
			bodyStyle: 'padding: 0px;',
			autoScroll: isAutoScroll,
			modal : true,
			html:html,
			buttons:[{
				text:Otm.com_close,
				handler:function(btn){
					Ext.getCmp('image_preview').close();
				}
			}]
		}).show();
	}else{
		return;
	}
}
/**
*	Export Controll
*		END
*/



/**
*	Custom Form Controll
*		START
*/
customform_seq = new Array();
function _getCustomfield(data,view_id)
{
	if(typeof(view_id) === 'undefined')
	{
		view_id = '';
	}

	var isBlank = true;
	var isBlankText = "";
	if(data.pc_is_required == 'Y'){
		isBlankText = "(*)";
		isBlank = false;
	}
	var customform_field = {};
	switch(data.pc_formtype){
		case "textfield":
			customform_field = {
				xtype: 'textfield',	width:'100%',
				id:view_id+'custom_'+data.pc_seq,name:view_id+'custom_'+data.pc_seq,
				fieldLabel : data.pc_name+''+isBlankText,
				allowBlank : isBlank,
				value	: data.pc_default_value
			};
		break;
		case "textarea":
			customform_field = {
				xtype: 'textarea',	width:'100%',
				id:view_id+'custom_'+data.pc_seq,name:view_id+'custom_'+data.pc_seq,
				fieldLabel : data.pc_name+''+isBlankText,
				allowBlank : isBlank,
				value	: data.pc_default_value,
				grow : true,
				growMax: 200
			};
		break;
		case "combo":
			var tmp_store = Ext.create('Ext.data.Store', {
				fields:['name', 'is_required'],
				data:{'items':[]},
				proxy: {
					type: 'memory',
					reader: {
						type: 'json',
						rootProperty: 'items'
					}
				}
			});

			if(data.pc_content){
				tmp_store.add(Ext.decode(data.pc_content));
			}


			customform_field = {
				xtype: 'combo',	width:'100%',
				id:view_id+'custom_'+data.pc_seq,name:view_id+'custom_'+data.pc_seq,
				fieldLabel : data.pc_name+''+isBlankText,
				displayField:'name',valueField:'name',editable: false,
				queryParam: 'q',queryMode: 'local',
				allowBlank : isBlank,
				value : data.pc_default_value,
				store	:tmp_store
			};
		break;
		case "checkbox":
			var db_data = Ext.decode(data.pc_content);
			var tmp_data = new Array();
			for(var j=0;j<db_data.length;j++){
				tmp_data.push({
					boxLabel:db_data[j].name,
					name : view_id+'custom_'+data.pc_seq,
					inputValue : db_data[j].name,
					checked:(db_data[j].is_required == 'Y')?true:false
				});
			}

			var tmp_checkbox = {
				xtype: 'checkboxgroup',	width:'100%',
				id:view_id+'custom_'+data.pc_seq,
				fieldLabel : data.pc_name+''+isBlankText,
				columns: 2,
				items: tmp_data
			}

			customform_field = tmp_checkbox;
		break;
		case "radio":
			var db_data = Ext.decode(data.pc_content);
			var tmp_data = new Array();
			for(var j=0;j<db_data.length;j++){
				tmp_data.push({
					boxLabel:db_data[j].name,
					name : view_id+'custom_'+data.pc_seq,
					inputValue : db_data[j].name,
					checked:(db_data[j].is_required == 'Y')?true:false
				});
			}

			var tmp_checkbox = {
				xtype: 'radiogroup',	width:'100%',
				id:view_id+'custom_'+data.pc_seq,
				fieldLabel : data.pc_name+''+isBlankText,
				columns: 2,
				items: tmp_data
			}

			customform_field = tmp_checkbox;
		break;
		case "datefield":
			customform_field = {
				xtype: 'datefield',	width:'50%',
				id:view_id+'custom_'+data.pc_seq,name:view_id+'custom_'+data.pc_seq,
				fieldLabel : data.pc_name+''+isBlankText,
				allowBlank : isBlank,format:"Y-m-d",editable: false,
				value	: data.pc_default_value.substr(0,10)
			};
		break;
	}

	var is_duplication = false;
	for(var jj=0;jj<customform_seq.length;jj++){
		if(customform_seq[jj].seq == data.pc_seq){
			is_duplication = true;
		}
	}
	if(is_duplication === true){
		//continue;
	}else{
		customform_seq.push({
			name	: data.pc_name,
			seq		: data.pc_seq,
			type	: data.pc_formtype
		});
	}

	return customform_field;
}
function _setCustomform(type,r,view_id)
{
	if(typeof(view_id) === 'undefined')
	{
		view_id = '';
	}

	if(typeof Ext.getCmp(type+'_customform_field') != 'undefined'){
		return;
	}
	var customform_field = Ext.create("Ext.Panel",{
		id:type+'_customform_field',
		layout:'vbox',
		border:false,
		items:[]
	});

	for(var i=0;i<r.length;i++){
		if(type != r[i].data.pc_category){
			continue;
		}
		if(type == 'TC_ITEM' && r[i].data.pc_default_value == 'Y'){
			continue;
		}

		var isBlank = true;
		var isBlankText = "";
		if(r[i].data.pc_is_required == 'Y'){
			isBlankText = "(*)";
			isBlank = false;
		}
		switch(r[i].data.pc_formtype){
			case "textfield":
				customform_field.add({
					xtype: 'textfield',	width:'100%',
					id:view_id+'custom_'+r[i].data.pc_seq,name:view_id+'custom_'+r[i].data.pc_seq,
					fieldLabel : r[i].data.pc_name+''+isBlankText,
					allowBlank : isBlank,
					value	: r[i].data.pc_default_value
				});
			break;
			case "textarea":
				customform_field.add({
					xtype: 'textarea',	width:'100%',
					id:view_id+'custom_'+r[i].data.pc_seq,name:view_id+'custom_'+r[i].data.pc_seq,
					fieldLabel : r[i].data.pc_name+''+isBlankText,
					allowBlank : isBlank,
					value	: r[i].data.pc_default_value,
					grow : true,
					growMax: 200
				});
			break;
			case "combo":
				var tmp_store = Ext.create('Ext.data.Store', {
					fields:['name', 'is_required'],
					data:{'items':[]},
					proxy: {
						type: 'memory',
						reader: {
							type: 'json',
							rootProperty: 'items'
						}
					}
				});

				if(r[i].data.pc_content){
					tmp_store.add(Ext.decode(r[i].data.pc_content));
				}


				customform_field.add({
					xtype: 'combo',	width:'100%',
					id:view_id+'custom_'+r[i].data.pc_seq,name:view_id+'custom_'+r[i].data.pc_seq,
					fieldLabel : r[i].data.pc_name+''+isBlankText,
					displayField:'name',valueField:'name',editable: false,
					queryParam: 'q',queryMode: 'local',
					allowBlank : isBlank,
					value : r[i].data.pc_default_value,
					store	:tmp_store
				});
			break;
			case "checkbox":
				var db_data = Ext.decode(r[i].data.pc_content);
				var tmp_data = new Array();
				for(var j=0;j<db_data.length;j++){
					tmp_data.push({
						boxLabel:db_data[j].name,
						name : view_id+'custom_'+r[i].data.pc_seq,
						inputValue : db_data[j].name,
						checked:(db_data[j].is_required == 'Y')?true:false
					});
				}

				var tmp_checkbox = {
					xtype: 'checkboxgroup',	width:'100%',
					id:view_id+'custom_'+r[i].data.pc_seq,
					fieldLabel : r[i].data.pc_name+''+isBlankText,
					columns: 2,
					items: tmp_data
				}

				customform_field.add(tmp_checkbox);
			break;
			case "radio":
				var db_data = Ext.decode(r[i].data.pc_content);
				var tmp_data = new Array();
				for(var j=0;j<db_data.length;j++){
					tmp_data.push({
						boxLabel:db_data[j].name,
						name : view_id+'custom_'+r[i].data.pc_seq,
						inputValue : db_data[j].name,
						checked:(db_data[j].is_required == 'Y')?true:false
					});
				}

				var tmp_checkbox = {
					xtype: 'radiogroup',	width:'100%',
					id:view_id+'custom_'+r[i].data.pc_seq,
					fieldLabel : r[i].data.pc_name+''+isBlankText,
					columns: 2,
					items: tmp_data
				}

				customform_field.add(tmp_checkbox);
			break;
			case "datefield":
				customform_field.add({
					xtype: 'datefield',	width:'50%',
					id:view_id+'custom_'+r[i].data.pc_seq,name:view_id+'custom_'+r[i].data.pc_seq,
					fieldLabel : r[i].data.pc_name+''+isBlankText,
					allowBlank : isBlank,format:"Y-m-d",editable: false,
					value	: r[i].data.pc_default_value.substr(0,10)
				});
			break;
		}
		var is_duplication = false;
		for(var jj=0;jj<customform_seq.length;jj++){
			if(customform_seq[jj].seq == r[i].data.pc_seq){
				is_duplication = true;
			}
		}
		if(is_duplication === true){
			continue;
		}
		customform_seq.push({
			name	: r[i].data.pc_name,
			seq		: r[i].data.pc_seq,
			type	: r[i].data.pc_formtype
		});
	}
	return customform_field;
}
function _setCustomform_userdata(customform_seq,df_customform,view_id)
{
	if(typeof(view_id) === 'undefined')
	{
		view_id = '';
	}

	if(df_customform != null){
		df_customform = Ext.decode(df_customform);

		if(customform_seq.length >= 1 && df_customform.length>=1){
			for(var i=0;i<customform_seq.length;i++){

				for(var j=0;j<df_customform.length;j++){

					if(customform_seq[i].seq == df_customform[j].seq){
						switch(df_customform[j].formtype){
							case "checkbox":
								var chkInfo = Ext.getCmp(view_id+'custom_'+customform_seq[i].seq).items;
								var arr_data = df_customform[j].value;

								for(var k=0;k<chkInfo.length;k++){
									if(typeof arr_data == "string"){
										if(chkInfo.items[k].inputValue == arr_data){
											chkInfo.items[k].setValue(true);
										}else{
											chkInfo.items[k].setValue(false);
										}
									}else{
										for(var kk=0;kk<arr_data.length;kk++){
											if(chkInfo.items[k].inputValue == arr_data[kk]){
												chkInfo.items[k].setValue(true);
												break;
											}else{
												chkInfo.items[k].setValue(false);
											}
										}
									}
								}
							break;
							case "radio":
								var chkInfo = Ext.getCmp(view_id+'custom_'+customform_seq[i].seq).items;
								for(var k=0;k<chkInfo.length;k++){
									if(chkInfo.items[k].inputValue == df_customform[j].value){
										chkInfo.items[k].setValue(true);
										break;
									}else{
										chkInfo.items[k].setValue(false);
									}
								}
							break;
							default:
								Ext.getCmp(view_id+'custom_'+customform_seq[i].seq).setValue(df_customform[j].value);
							break;
						}

						break;
					}
				}
			}
		}
	}
}

function _getCustomform_view(customform_store,data)
{
	var defect_info = Ext.decode(data);
	var user_data = Ext.decode(defect_info.data.df_customform);
	defect_info.data.user_form="";

	for(var i=0;i<customform_store.data.items.length;i++){
		formInfo = customform_store.data.items[i].data;
		user_value = "";
		if(user_data){
			for(var j=0;j<user_data.length;j++){
				if(user_data[j]!=""){
					if(formInfo.pc_seq == user_data[j].seq){
						user_value = user_data[j].value;
					}
				}
			}
		}

		if(user_value == null){
			user_value = "";
		}

		defect_info.data.user_form += "<div style='padding:5px;'><label style='padding:3px;font-weight:bold;' for='"+formInfo.pc_seq+"'>"+formInfo.pc_name+"</label> <div style='border: 1px dotted #48BAE4; height: auto; padding:10px;' border=1>"+user_value.replace(/(?:\r\n|\r|\n)/g, '<br />')+"</div></div> ";

		/*
		if(customform_store.data.items[i].data.pc_formtype == "textarea"){
			defect_info.data.user_form += "<label for='"+formInfo.pc_seq+"'>"+formInfo.pc_name+"</label> <textarea name='"+formInfo.pc_seq+"' style='padding:3px;border:1px dotted gray;width:100%;font-size:12px;' readonly=readonly>"+user_value+"</textarea> ";
		}else{
			defect_info.data.user_form += "<label for='"+formInfo.pc_seq+"'>"+formInfo.pc_name+"</label> <input name='"+formInfo.pc_seq+"' style='padding:3px;border:1px dotted gray;width:100%;font-size:12px;' readonly=readonly type=text value='"+user_value+"'/ >";
		}
		*/
	}
	return defect_info;
}

function _getCustomform_testcase(customform_store,data)
{
	var testcase_info = Ext.decode(data);
	var user_data = Ext.decode(testcase_info.data.df_customform);
	testcase_info.data.user_form="";

	for(var i=0;i<customform_store.data.items.length;i++){

		if(customform_store.data.items[i].data.pc_category != 'ID_TC'){
			continue;
		}

		formInfo = customform_store.data.items[i].data;
		user_value = "";
		if(user_data){
			for(var j=0;j<user_data.length;j++){
				if(user_data[j]!=""){
					if(formInfo.pc_seq == user_data[j].seq){
						user_value = user_data[j].value;
					}
				}
			}
		}

		if(user_value && user_value != ""){
			user_value = user_value.replace(/(?:\r\n|\r|\n)/g, '<br />');
		}

		if(user_value == null){
			user_value = "";
		}

		testcase_info.data.user_form += "<div style='padding:5px;'><label style='padding:3px;font-weight:bold;' for='"+formInfo.pc_seq+"'>"+formInfo.pc_name+"</label> <div style='border: 1px dotted #48BAE4; height: auto; padding:10px;' border=1>"+user_value+"</div></div> ";
	}

	return testcase_info.data.user_form;
}

function _getCustomform_tc_item(customform_store,data)
{
	var testcase_info = Ext.decode(data);
	var user_data = Ext.decode(testcase_info.data.df_customform);
	testcase_info.data.tc_item_form="";

	for(var i=0;i<customform_store.data.items.length;i++){

		if(customform_store.data.items[i].data.pc_category != 'TC_ITEM'){
			continue;
		}
		if(customform_store.data.items[i].data.pc_default_value == 'Y'){
			continue;
		}


		formInfo = customform_store.data.items[i].data;
		user_value = "";
		if(user_data){
			for(var j=0;j<user_data.length;j++){
				if(user_data[j]!=""){
					if((formInfo.pc_seq == user_data[j].seq) && user_data[j].value){
						user_value = user_data[j].value;
					}
				}
			}
		}

		if(user_value != ""){
			user_value = user_value.replace(/(?:\r\n|\r|\n)/g, '<br />');
		}

		if(user_value == null){
			user_value = "";
		}

		testcase_info.data.tc_item_form += "<div style='padding:5px;'><label style='padding:3px;font-weight:bold;' for='"+formInfo.pc_seq+"'>"+formInfo.pc_name+"</label> <div style='border: 1px dotted #48BAE4; height: auto; padding:10px;' border=1>"+user_value+"</div></div> ";
	}
	return testcase_info.data.tc_item_form;
}
/**
*	Custom Form Controll
*		END
*/


/**
*	Roll Controll
*		START
*/
function check_role(type)
{
	if(mb_is_admin == 'Y' || (member_role_store && member_role_store[type])){
		return true;
	}else{
		return false;
	}
}

var member_role_store = new Array();
var defect_workflow = '';
Ext.override(Ext.Component,{
	listeners: {
		/**
		 * Beforerender event used to translate labels of component
		 */
		beforerender: function(component, eOpts){
			if(component.xtype == 'tab'){
				var view_role = '';
				var pc_category = '';
				var tab_id = component.config.card.id;
				var project_setup = tab_id.split('_');
				if(project_setup[0]+'_'+project_setup[1] == 'project_setup'){
					tab_id = 'project_setup';
				}

				switch(tab_id){
					case 'defect':
						pc_category = 'ID_DEFECT';
						view_role = 'defect_view';
						break;
					case 'testcase':
						pc_category = 'ID_TC';
						view_role = 'tc_view';
						break;
					case 'report':
						pc_category = 'ID_REPORT';
						view_role = 'report_view';
						break;
					case 'project_setup':
						pc_category = 'ID_PROJECT';
						view_role = 'project_edit';
						break;
					case 'comtc_main':
						pc_category = 'ID_COMTC';
						/*view_role = 'comtc_view';*/
						break;
					case 'dashboard':
						pc_category = 'ID_PROJECT';
						/*view_role = 'project_view';*/
						break;
					default:
						break;
				}

				if(!component.config.card.pr_seq && !pc_category){
					return;
				}

				Ext.Ajax.request({
					url : './index.php/Project_setup/user_project_role',
					params : {
						pr_seq		: component.config.card.pr_seq,
						pc_category : pc_category
					},
					method: 'POST',
					success: function ( result, request ) {
						if(result.responseText){
							var role_info = Ext.decode(result.responseText);
							defect_workflow = role_info.defect_workflow;

							var tmp_store = role_info.data;
							var temp_arr = new Array();
							for(var i=0; i<tmp_store.length; i++){
								if(tmp_store[i].pmi_value == 1){

									var type = tmp_store[i].pmi_name.split('_');
									if(type[2] && type[2] == 'all'){
										var check_name = type[0]+'_'+type[1];
										if(! temp_arr[check_name]){
											temp_arr[check_name] = tmp_store[i].pmi_value;
										}
									}

									if(! temp_arr[tmp_store[i].pmi_name]){
										temp_arr[tmp_store[i].pmi_name] = tmp_store[i].pmi_value;
									}
								}
							}

							member_role_store = temp_arr;
							if(view_role != ''){
								if(member_role_store && member_role_store[view_role] || mb_is_admin == 'Y'){
								}else{
									var msg = component.text+' '+Otm.com_view+Otm.com_msg_noRole;
									if(tab_id == 'project_setup'){
										msg = component.text+' '+Otm.com_update+Otm.com_msg_noRole;
									}
									Ext.Msg.alert('OTM',msg);
									component.card.mask();
								}
							}
						}
					},
					failure: function ( result, request ){
					}
				});

			}
			/*component.translate();*/
		}
	}
});


Ext.override(Ext.Button, {
	afterRender : function(){
		if(this.action_type && this.action_type != ''){
			if(member_role_store && member_role_store[this.action_type] || mb_is_admin == 'Y'){
			}else{
				if(this.disabled == false){
					this.setDisabled(true);
					this.setTooltip(this.text+Otm.com_msg_noRole);
				}
			}
		}
	}
});

Ext.override(Ext.menu.Item, {
	afterRender : function(){
		if(this.action_type && this.action_type != ''){
			if(member_role_store && member_role_store[this.action_type] || mb_is_admin == 'Y'){
			}else{
				if(this.disabled == false){
					this.setDisabled(true);
					this.setTooltip(this.text+Otm.com_msg_noRole);
				}
			}
		}
	}
});
/**
*	Roll Controll
*		END
*/



/**
*	History Controll
*		START
*/
function _history_convert_detail(data)
{
	detail = '';
	switch(data.action_type)
	{
		case 'create':
			detail = '<div style="padding:5px;width:100%;word-break:break-all;">신규 등록</div>';
			break;
		case 'df_subject':
		case 'tc_subject':
			detail = '<div style="padding:5px;width:100%;word-break:break-all;"> 제목을 "<font color=blue>' + data.old_value + '</font>" 에서 "<font color=red>' + data.value + '</font>" (으)로 변경.</div>';
			break;
		case 'df_description':
		case 'tc_description':
			var div_id = 'history_' + (Math.round(Math.random() * 10000) + 1);
			detail = '<div style="padding:5px;width:100%;word-break:break-all;">'+
				'설명 변경 : <input type=button value="view" onclick="javascript:document.getElementById(\''+div_id+'\').style.display=\'block\';"><br>'+
				'<div id="'+div_id+'" style="display:none;"><input type=button value="close" onclick="javascript:document.getElementById(\''+div_id+'\').style.display=\'none\';"><br>' + data.value + '</div></div>';

			detail = '<div style="padding:5px;width:100%;word-break:break-all;">'+'설명 변경 : <br>'+ data.value + '</div>';
			break;
		case 'df_status':
			if(data.old_value  && data.value){
				var old_value = defect_code_store.findRecord('pco_seq', data.old_value).get('pco_name');
				var value = defect_code_store.findRecord('pco_seq', data.value).get('pco_name');
				detail = '<div style="padding:5px;width:100%;word-break:break-all;"> 상태를 <font color=blue>' + old_value + '</font> 에서 <font color=red>' + value + '</font> (으)로 변경.</div>';
			}
			break;
		case 'df_severity':
			var old_value = defect_code_store.findRecord('pco_seq', data.old_value).get('pco_name');
			var value = defect_code_store.findRecord('pco_seq', data.value).get('pco_name');
			detail = '<div style="padding:5px;width:100%;word-break:break-all;"> 심각도를 <font color=blue>' + old_value + '</font> 에서 <font color=red>' + value + '</font> (으)로 변경.</div>';
			break;
		case 'df_priority':
			var old_value = defect_code_store.findRecord('pco_seq', data.old_value).get('pco_name');
			var value = defect_code_store.findRecord('pco_seq', data.value).get('pco_name');
			detail = '<div style="padding:5px;width:100%;word-break:break-all;"> 우선순위를 <font color=blue>' + old_value + '</font> 에서 <font color=red>' + value + '</font> (으)로 변경.</div>';
			break;
		case 'df_frequency':
			var old_value = defect_code_store.findRecord('pco_seq', data.old_value).get('pco_name');
			var value = defect_code_store.findRecord('pco_seq', data.value).get('pco_name');
			detail = '<div style="padding:5px;width:100%;word-break:break-all;"> 재현빈도를 <font color=blue>' + old_value + '</font> 에서 <font color=red>' + value + '</font> (으)로 변경.</div>';
			break;
		case 'df_start_date':
			data.old_value = data.old_value.substring(0, 10);
			if(data.old_value == '0000-00-00'){
				detail = '<div style="padding:5px;width:100%;word-break:break-all;"> 시작일을 <font color=red>' + data.value + '</font> 로 변경.</div>';
			}else{
				detail = '<div style="padding:5px;width:100%;word-break:break-all;"> 시작일을 <font color=blue>' + data.old_value + '</font> 에서 <font color=red>' + data.value + '</font> 로 변경.</div>';
			}
			break;
		case 'df_end_date':
			data.old_value = data.old_value.substring(0, 10);
			if(data.old_value == '0000-00-00'){
				detail = '<div style="padding:5px;width:100%;word-break:break-all;"> 종료일을 <font color=red>' + data.value + '</font> 로 변경.</div>';
			}else{
				detail = '<div style="padding:5px;width:100%;word-break:break-all;"> 종료일을 <font color=blue>' + data.old_value + '</font> 에서 <font color=red>' + data.value + '</font> 로 변경.</div>';
			}
			break;
		case 'df_assign':
			detail = '<div style="padding:5px;width:100%;word-break:break-all;">담당자를 <font color=blue>'+data.mb_name+'</font> (으)로 지정.</div>';
			break;
		case 'df_file':
		case 'tc_file':
			detail = '<div style="padding:5px;width:100%;word-break:break-all;">파일 등록 : ' +data.value+ '</div>';
			break;
		case 'df_project_copy':
		case 'tc_project_copy':
			detail = '<div style="padding:5px;width:100%;word-break:break-all;"> '+data.old_value+'번 프로젝트에서 복사해 왔습니다.</div>';
			break;
		default:
			if(!data.old_value || data.old_value == ''){
				detail = '<div style="padding:5px;width:100%;word-break:break-all;"> '+data.action_type+' 을(를) "<font color=red>' + data.value + '</font>" (으)로 변경.</div>';
			}else{
				detail = '<div style="padding:5px;width:100%;word-break:break-all;"> '+data.action_type+' 을(를) "<font color=blue>' + data.old_value + '</font>" 에서 "<font color=red>' + data.value + '</font>" (으)로 변경.</div>';
			}
			break;
		case 'tracking_tc':
		case 'tracking_df':

			detail = '<div style="padding:5px;width:100%;word-break:break-all;"> ';

			if(data.action_type == 'tracking_tc'){
				detail += '결함 ('+data.old_value+') 을(를)';
			}else if(data.action_type == 'tracking_df'){
				detail += '테스트케이스 ('+data.old_value+') 을(를)';
			}

			if(data.value == 'set_link'){
				detail += ' <font color=blue> '+Otm.com_msg_connected+'</font> </div>';
			}else if(data.value == 'set_unlink'){
				detail += ' <font color=red> '+Otm.com_msg_disconnected+'</font> </div>';
			}
			break;
	}

	if(data.action_type !='create' && data.value == ''){
		return '';
	}

	return detail;
}

function _history_view(data)
{
	var return_html = '';

	if(data && data.length > 0){
		return_html += '<div style="padding:10px;line-height:20px;">';

		for(var i=0;i<data.length;i++)
		{
			return_html += '<div style="padding:5px;text-decoration: underline;width:100%;word-break:break-all;"><font color=blue>'+ data[i].mb_name + '</font>(' + data[i].writer + ')가 <font color=blue>'+ data[i].regdate + '</font> 에</div>';

			var detail = data[i].detail;
			for(var k=0;k<detail.length;k++){
				return_html += _history_convert_detail(detail[k]);
			}
			return_html += '<div style="padding:5px;border-bottom:1px dotted gray;clear:both;"></div>';
		}

		return_html += '</div>';
	}

	return return_html;
}
/**
*	Export Controll
*		END
*/




/**
*	View Content Controll : Defect, TestCase
*		START
*/
function popup_view(type,seq)
{
	if(type == 'defect')
	{
		var obj ={
			target : 'popup_view',
			df_seq : seq,
			pr_seq : project_seq
		};
		get_defect_view_panel(obj);
	}else if(type == 'testcase'){
		var obj ={
			target : 'popup_view',
			tl_seq : seq,
			pr_seq : project_seq
		};
		get_testcase_view_panel(obj);
	}else{
		Ext.getCmp('popup_view').update('');
		return;
	}
	Ext.getCmp('popup_view').show();
}

function get_testcase_view_panel(obj)
{
	var target_panel = obj.target;

	Ext.getCmp(target_panel).update('');

	Ext.Ajax.request({
		url		: './index.php/Plugin_view/testcase/get_testcase_info',
		params	: {
			id		: obj.id,
			tl_seq	: obj.tl_seq,
			pr_seq	: obj.pr_seq
		},
		method	: 'POST',
		success	: function ( result, request ) {
			if(result.responseText){
				var info = Ext.decode(result.responseText);

				if(info.data.data.tc_description){
					info.data.data.tc_description = info.data.data.tc_description.replace(/\n/g, '<br>');
				}

				info.data.data.tc_item_form = _getCustomform_tc_item(testcase_customform_store,result.responseText);
				info.data.data.user_form = _getCustomform_testcase(testcase_customform_store,result.responseText);

				var printFile = _common_fileView('testcaseGrid',Ext.decode(info.data.fileform));
				info.data.fileform = printFile;

				info.data.testcase_history = _history_view(Ext.decode(info.data.testcase_history));

				var default_fieldset = {
					xtype		: 'fieldset',
					collapsible	: false,
					collapsed	: false,
					border		: false,
					items		: [{
						xtype		: 'displayfield',
						fieldLabel	: Otm.tc_plan,
						value		: info.data.data.plan_name
					},{
						xtype		: 'displayfield',
						fieldLabel	: 'ID',
						value		: info.data.data.tc_out_id
					},{
						xtype		: 'displayfield',
						fieldLabel	: Otm.com_creator,
						value		: info.data.data.writer_name
					},{
						xtype		: 'displayfield',
						fieldLabel	: Otm.com_date,
						value		: info.data.data.regdate.substr(0,10)
					},{xtype : 'menuseparator',width : '100%'}]
				};

				var tc_item_form_fieldset = {
					xtype		: 'fieldset',
					collapsible	: false,
					collapsed	: false,
					border		: false,
					items		: [{
						border		: false,
						html		: info.data.data.tc_item_form
					},{xtype : 'menuseparator',width : '100%'}]
				};
				var userform_fieldset = {
					xtype		: 'fieldset',
					collapsible	: false,
					collapsed	: false,
					border		: false,
					items		: [{
						border		: false,
						html		: info.data.data.user_form
					},{xtype : 'menuseparator',width : '100%'}]
				};

				var attached_file_fieldset = {
					xtype		: 'fieldset',
					title		: Otm.com_attached_file,
					collapsible	: false,
					collapsed	: false,
					items		: [{
						border		: false,
						html		: info.data.fileform
					},{xtype : 'menuseparator',width : '100%'}]
				};

				var history_fieldset = {
					xtype		:'fieldset',
					title		: Otm.com_revision_history,
					collapsible	: true,
					collapsed	: true,
					items		: [{
						border	: false,
						html	: info.data.testcase_history
					}]
				};

				if(info.data.data.tc_is_task == 'folder'){
					var content_fieldset = {
							xtype		: 'fieldset',
							collapsible	: false,
							collapsed	: false,
							border		: false,
							items		: [{
								xtype		: 'displayfield',
								fieldLabel	: Otm.tc_suite+' '+Otm.com_name,
								value		: info.data.data.tc_subject
							},{
								xtype		: 'displayfield', multiline	: true,
								fieldLabel	: Otm.com_description,
								value		: info.data.data.tc_description
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
							history_fieldset
						]
					};
				}else{
					var content_fieldset = {
							xtype		: 'fieldset',
							collapsible	: false,
							collapsed	: false,
							border		: false,
							items		: [{
								xtype		: 'displayfield',
								fieldLabel	: Otm.tc+' '+Otm.com_name,
								value		: info.data.data.tc_subject
							}]
						};

					var racking_result_grid = {};

					if(info.data.tc_result.length > 0){
						var tracking_result_store = Ext.create('Ext.data.Store', {
							fields	: ['pco_name', 'tr_description','writer_name','regdate','df_id'],
							data:{'items':[]},
							proxy: {
								type: 'memory',
								reader: {
									type: 'json',
									rootProperty: 'items'
								}
							}
						});

						for(var i=0; i<info.data.tc_result.length; i++)
						{
							var r = info.data.tc_result;

							tracking_result_store.add({
								pco_seq						: r[i].pco_seq,
								pco_name					: r[i].pco_name,
								tr_description				: r[i].tr_description.replace(/\n/g, '<br>'),
								writer_name					: r[i].writer_name,
								regdate						: r[i].regdate,
								df_seq						: r[i].df_seq,
								df_id						: r[i].df_id,
								df_subject					: r[i].df_subject,
								tr_seq						: r[i].tr_seq,
								otm_testcase_link_tl_seq	: r[i].otm_testcase_link_tl_seq
							});
						}

						var racking_result_grid = new Ext.grid.GridPanel({
							layout		: 'fit',
							store		: tracking_result_store,
							border		: true,
							forceFit	: true,
							autoWidth	: true,
							columns		: [{
									text: Otm.com_number,
									dataIndex: 'df_seq',align:'center',
									hidden:true,
									width:50
								},{
									text: Otm.tc_execution_result,
									dataIndex: 'pco_name',
									width: 50
								},{
									text: Otm.tc_execution+' '+Otm.com_description,
									dataIndex: 'tr_description',
									flex:1,
									width: 150
								},{
									text: Otm.tc_execution_user,
									dataIndex: 'writer_name',
									width: 50
								},{
									text: Otm.tc_execution_regdate,
									dataIndex: 'regdate',
									width: 80,
									renderer:function(value,index,record){
										if(value){
											var value = value.substr(0,10);
										}else{
											value = '';
										}
										return value;
									}
								},{
									text: Otm.def,
									dataIndex: 'df_id',align:'center',
									width: 80,
									renderer: function(value, metaData, record, rowIndex, colIndex, store){

										if(value){
											return '<a href=javascript:popup_view("defect","'+record.data.df_seq+'");>'+value+'</a>';
										}else{
											if(target_panel == 'tracking_testcase_east_view_panel' || target_panel == 'popup_view'){
												return '<input type=button disabled="true" value="'+Otm.def+' '+Otm.com_add+'"/>';
											}else{
												var btn = '<input type="button" onClick="testcase_defect_write(this.id);" id="trseq_'+record.data.tr_seq+'" value="'+Otm.def+' '+Otm.com_add+'">';

												if(mb_is_admin == 'Y' || (member_role_store && member_role_store['defect_add'])){
												}else{
													btn = '<span style="color:red;">['+Otm.def+' '+Otm.com_add+Otm.com_msg_noRole+']</span>';
												}

												return btn;
											}
										}
									}
								}
							],
							listeners: {
								scope : this,
								rowdblclick:function(grid, rowIndex, e) {
									html = rowIndex.data.tr_description;
									var doc_width = document.body.clientWidth - 100;
									var doc_height = document.body.clientHeight - 50;

									Ext.create('Ext.window.Window', {
										title: Otm.tc_execution+' '+Otm.com_description,
										width: doc_width,
										height: doc_height,
										id:'otm_tc_result_description_win',
										layout: 'border',
										collapsible: true,
										bodyStyle: 'padding: 20px;',
										autoScroll: true,
										modal : true,
										html:html,
										buttons:[{
											text:Otm.com_close,
											handler:function(btn){
												Ext.getCmp('otm_tc_result_description_win').close();
											}
										}]
									}).show();
								}
							}
						});
					}

					var racking_result_fieldset = {
							xtype		: 'fieldset',
							title		: Otm.def+' '+Otm.com_linklist,
							collapsible	: false,
							collapsed	: false,
							items		: [racking_result_grid]
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
							tc_item_form_fieldset,
							userform_fieldset,
							racking_result_fieldset,
							attached_file_fieldset,
							history_fieldset
						]
					};
				}

				Ext.getCmp(target_panel).removeAll();
				Ext.getCmp(target_panel).add(view_form);
			}
		},
		failure: function ( result, request ) {
			Ext.Msg.alert('OTM','DataBase Select Error');
		}
	});
}

function get_defect_view_panel(obj)
{
	var target_panel = obj.target;

	Ext.getCmp(target_panel).update('');

	Ext.Ajax.request({
		url : "./index.php/Plugin_view/defect/view_defect",
		params : obj,
		method: 'POST',
		success: function ( result, request ) {
			if(result.responseText){
				var defect_info = _getCustomform_view(defect_customform_store,result.responseText);

				if(!defect_info.data.dc_to){
					defect_info.data.dc_to = "";
				}
				if(defect_info.data.dc_start_date){
					defect_info.data.dc_start_date = defect_info.data.dc_start_date.substr(0,10);
				}else{
					defect_info.data.dc_start_date="";
				}
				if(defect_info.data.dc_end_date){
					defect_info.data.dc_end_date = defect_info.data.dc_end_date.substr(0,10);
				}else{
					defect_info.data.dc_end_date ="";
				}

				if(defect_info.data.dc_start_date == '0000-00-00')
					defect_info.data.dc_start_date = '';
				if(defect_info.data.dc_end_date == '0000-00-00')
					defect_info.data.dc_end_date = '';

				defect_info.data.regdate = defect_info.data.regdate.substr(0,10);

				var printFile = _common_fileView('defectGrid',Ext.decode(defect_info.data.fileform));
				defect_info.data.fileform = printFile;

				defect_info.data.defect_history = _history_view(Ext.decode(defect_info.data.defect_history));

				var default_fieldset = {
					xtype		: 'fieldset',
					collapsible	: false,
					collapsed	: false,
					border		: false,
					items		: [{
						xtype		: 'displayfield',
						fieldLabel	: 'ID',
						value		: defect_info.data.df_id
					},{
						xtype		: 'displayfield',
						fieldLabel	: Otm.com_creator,
						value		: defect_info.data.writer
					},{
						xtype		: 'displayfield',
						fieldLabel	: Otm.com_date,
						value		: defect_info.data.regdate.substr(0,10)
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
							value		: defect_info.data.df_subject
						},{
							xtype		: 'displayfield', multiline	: true,
							fieldLabel	: Otm.com_description,
							value		: defect_info.data.df_description
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.def_severity,
							value		: defect_info.data.severity_name
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.def_priority,
							value		: defect_info.data.priority_name
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.def_frequency,
							value		: defect_info.data.frequency_name
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.def_status,
							value		: defect_info.data.status_name
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.com_user,
							value		: defect_info.data.dc_to
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.com_start_date,
							value		: defect_info.data.dc_start_date
						},{
							xtype		: 'displayfield',
							fieldLabel	: Otm.com_end_date,
							value		: defect_info.data.dc_end_date
						}]
					};

				var userform_fieldset = {
					xtype		: 'fieldset',
					collapsible	: false,
					collapsed	: false,
					border		: false,
					items		: [{
						border		: false,
						html		: defect_info.data.user_form
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
						html		: defect_info.data.fileform
					},{xtype : 'menuseparator',width : '100%'}]
				};

				var history_fieldset = {
					xtype		:'fieldset',
					title		: Otm.com_revision_history,
					collapsible	: true,
					collapsed	: false,
					items		: [{
						border	: false,
						html	: defect_info.data.defect_history
					}]
				};

				var racking_result_grid = {};

				if(defect_info.data.tc_result.length > 0){
					var tracking_result_store = Ext.create('Ext.data.Store', {
						fields	: ['pco_name', 'tr_description','writer_name','regdate','df_id'],
						data:{'items':[]},
						proxy: {
							type: 'memory',
							reader: {
								type: 'json',
								rootProperty: 'items'
							}
						}
					});

					for(var i=0; i<defect_info.data.tc_result.length; i++)
					{
						var r = defect_info.data.tc_result;

						tracking_result_store.add({
							pco_seq						: r[i].pco_seq,
							pco_name					: r[i].pco_name,
							tr_description				: r[i].tr_description.replace(/\n/g, '<br>'),
							writer_name					: r[i].writer_name,
							regdate						: r[i].regdate,
							df_seq						: r[i].df_seq,
							tc_seq						: r[i].tc_seq,
							tc_out_id					: r[i].tc_out_id,
							tc_subject					: r[i].tc_subject,
							tr_seq						: r[i].tr_seq,
							otm_testcase_link_tl_seq	: r[i].otm_testcase_link_tl_seq
						});
					}

					var racking_result_grid = new Ext.grid.GridPanel({
						layout		: 'fit',
						store		: tracking_result_store,
						border		: true,
						forceFit	: true,
						autoWidth	: true,
						columns		: [{
								text: Otm.com_number,
								dataIndex: 'df_seq',align:'center',
								hidden:true,
								width:50
							},{
								text: Otm.tc_execution_result,
								dataIndex: 'pco_name',
								width: 50
							},{
								text: Otm.tc_execution+' '+Otm.com_description,
								dataIndex: 'tr_description',
								flex:1,
								width: 150
							},{
								text: Otm.tc_execution_user,
								dataIndex: 'writer_name',
								width: 50
							},{
								text: Otm.tc_execution_regdate,
								dataIndex: 'regdate',
								width: 80,
								renderer:function(value,index,record){
									if(value){
										var value = value.substr(0,10);
									}else{
										value = '';
									}
									return value;
								}
							},{
								text: 'TC ID',
								dataIndex: 'tc_out_id',align:'center',
								width: 80,
								renderer: function(value, metaData, record, rowIndex, colIndex, store){
									if(value){
										if(mb_is_admin == 'Y' || (member_role_store && member_role_store['tc_view'])){
											return '<a href=javascript:popup_view("testcase","'+record.data.otm_testcase_link_tl_seq+'");>'+value+'</a>';
										}else{
											return '<span style="color:red;">['+Otm.tc+' '+Otm.com_view+Otm.com_msg_noRole+']</span>';
										}
									}else{
										return '';
									}
								}
							}
						]
					});
				}

				var racking_result_fieldset = {
					xtype		: 'fieldset',
					title		: Otm.tc+' '+Otm.com_linklist,
					collapsible	: false,
					collapsed	: false,
					items		: [racking_result_grid]
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
						racking_result_fieldset,
						attached_file_fieldset,
						history_fieldset
					]
				};

				Ext.getCmp(target_panel).removeAll();
				Ext.getCmp(target_panel).add({
					region	: 'center', layout:'fit', xtype:'panel',
					animation: false, autoScroll: true,
					items : [view_form]
				});
			}
		},
		failure: function ( result, request ) {
			Ext.Msg.alert("OTM","DataBase Select Error");
		}
	});
}
/**
*	View Content Controll : Defect, TestCase
*		END
*/



/**
*	Tree Controll
*		START
*/
NodeReload = function(node,newNode)
{
	switch(node.cmd){
		case "add":
			AddTreeItem(node);
			break;
		case "delete":
			DeleteTreeItem(node);
			break;
		case "update":
			UpdateTreeItem(node);
			break;
		case "ContextMenuTitle":
			UpdateContextMenuTitle(node,newNode);
			break;
		case "ContextMenuType":
			return UpdateContextMenuType(node,newNode);
			break;
	}
}
UpdateTreeItem = function(r)
{
	var sel_node = Ext.getCmp(r.target_grid).getSelectionModel().selected;

	for(var i=0;i<sel_node.length;i++){
		for (var idx in r) {
			if(sel_node.items[i].data.type == 'folder'){
				if(idx == 'assign_name' || idx == 'deadline_date' || idx == 'assign_to'){
					continue;
				}
			}
			sel_node.items[i].set(idx,r[idx]);
		}
	}
}

UpdateContextMenuTitle = function()
{
	if(node){
		var pNode = node.parentNode;
		if(!pNode || pNode == ""){
			return;
		}

		newNode.text = my_unescape(newNode.text);

		pNode.replaceChild(newNode,node);
		pNode.expand();
		if(newNode.leaf == false){
			newNode.expand();
		}
	}
}

DeleteTreeItem = function(node)
{
	if(node){
		for(var i=node.length-1;i>=0;i--){
			if(node[i]){
				node[i].remove().destroy();
			}
		}
	}
}
AddTreeItem = function(r)
{
	var node = Ext.getCmp(r.target_grid).getSelectionModel().selectionStart;

	var gridTreeInfo = {
		v_seq			: r.v_seq, // 공통 tc때문에 추가됨. kslovee
		seq				: r.seq,   //공통 tc때문에 추가됨.  kslovee

		pr_seq			: r.pr_seq,
		tp_seq			: r.tp_seq,
		tc_seq			: r.tc_seq,
		tl_seq			: r.tl_seq,
		pid				: r.pid,
		id				: r.id,
		out_id			: r.out_id,
		text			: r.text,
		type			: r.type,
		writer			: r.writer,
		writer_name		: r.writer_name,
		regdate			: r.regdate,
		last_writer		: r.last_writer,
		last_writer_name		: r.last_writer_name,
		last_update		: r.last_update,
		leaf			: r.leaf,
		assign_name		: '',
		deadline_date	: '',
		result_value	: '',
		result_writer	: '',
		result_writer_name	: ''
	}
	for (var idx in r) {
		gridTreeInfo[idx] = r[idx];
	}


	if(typeof node == "undefined" || node == null){
		Ext.getCmp(r.target_grid).getRootNode().appendChild(gridTreeInfo);
	}else{
		node.appendChild(gridTreeInfo);
		if(node.leaf == false && node.expanded == false){
			node.expand();
		}
	}
}
/**
*	Tree Controll
*		END
*/


function getSliderBar(panelId){
	var sliderBar = new Ext.Slider({
		width: 100,	increment: 1,	minValue: 0,value:0,
		maxValue: 10, plugins: new Ext.slider.Tip(),
		listeners:{
			change : function(slider, thumb, newValue, oldValue){
				var selectValue = newValue.value;
				var rootNode = Ext.getCmp(panelId);
				rootNode.collapseAll();

				if(selectValue > 0){
					var node = rootNode.root;
				}
			}
		}
	});
	return sliderBar;
}

function tree_OpenClose_Btn(panelId)
{
	var tree_open_close_btn = [{
			xtype:'button',
			text:Otm.tree_expandAll,
			handler:function(){
				Ext.getCmp(panelId).expandAll();
			}
		},'-',{
			xtype:'button',
			text:Otm.tree_collapseAll,
			handler:function(){
				Ext.getCmp(panelId).collapseAll();
			}
		},'-',{
			xtype:'button',
			text:Otm.tree_select_expand,
			handler:function(){
				if(Ext.getCmp(panelId).getSelectionModel().getSelection().length > 0){
					var selectItem = Ext.getCmp(panelId).getSelectionModel().getSelection();
					for(var i=0;i<selectItem.length;i++){
						selectItem[i].expand(true);
					}
				}
			}
		},'-',{
			xtype:'button',
			text:Otm.tree_select_collapse,
			handler:function(){
				if(Ext.getCmp(panelId).getSelectionModel().getSelection().length > 0){
					var selectItem = Ext.getCmp(panelId).getSelectionModel().getSelection();
					for(var i=0;i<selectItem.length;i++){
						selectItem[i].collapse(true);
					}
				}
			}
		},'-',{
			xtype:'button',
			text:Otm.reload,
			handler:function(){
				Ext.getCmp(panelId).getStore().reload();
			}
		}]
	return tree_open_close_btn;
}

function leadingZeros(n, digits) {
	var zero = '';
	n = n.toString();

	if (n.length < digits) {
	for (i = 0; i < digits - n.length; i++)
		zero += '0';
	}
	return zero + n;
}
function getTimeStamp() {
	var d = new Date();

	var s =
	leadingZeros(d.getFullYear(), 4) + '-' +
	leadingZeros(d.getMonth() + 1, 2) + '-' +
	leadingZeros(d.getDate(), 2) + ' ' +

	leadingZeros(d.getHours(), 2) + ':' +
	leadingZeros(d.getMinutes(), 2) + ':' +
	leadingZeros(d.getSeconds(), 2);

	return s;
}

function nl2br(str) {
    return str.replace(/<\s*\/?br>/ig, "\n");
}
function br2nl(str) {
    return str.replace(/\n/g, "<br>");
}