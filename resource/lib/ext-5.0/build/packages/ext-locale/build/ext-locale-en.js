Ext.onReady(function(){
	if(Ext.data&&Ext.data.Types){
		Ext.data.Types.stripRe=/[\$,%]/g
	}
	if(Ext.Date){
		Ext.Date.monthNames=["January","February","March","April","May","June","July","August","September","October","November","December"];
		Ext.Date.getShortMonthName=function(a){
			return Ext.Date.monthNames[a].substring(0,3)
		};
		Ext.Date.monthNumbers={Jan:0,Feb:1,Mar:2,Apr:3,May:4,Jun:5,Jul:6,Aug:7,Sep:8,Oct:9,Nov:10,Dec:11};
		Ext.Date.getMonthNumber=function(a){
			return Ext.Date.monthNumbers[a.substring(0,1).toUpperCase()+a.substring(1,3).toLowerCase()]
		};
		Ext.Date.dayNames=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
		Ext.Date.getShortDayName=function(a){
			return Ext.Date.dayNames[a].substring(0,3)
		};
		Ext.Date.parseCodes.S.s="(?:st|nd|rd|th)"
	}
	if(Ext.util&&Ext.util.Format){
		Ext.apply(Ext.util.Format,{thousandSeparator:",",decimalSeparator:".",currencySign:"$",dateFormat:"m/d/Y"})
	}
});
Ext.define("Ext.locale.en.data.validator.Bound",{
	override:"Ext.data.validator.Bound",
	emptyMessage:"Must be present"
});
Ext.define("Ext.locale.en.data.validator.Email",{
	override:"Ext.data.validator.Email",
	message:"Is not a valid email address"
});
Ext.define("Ext.locale.en.data.validator.Exclusion",{
	override:"Ext.data.validator.Exclusion",
	message:"Is a value that has been excluded"
});
Ext.define("Ext.locale.en.data.validator.Format",{
	override:"Ext.data.validator.Format",
	message:"Is in the wrong format"
});
Ext.define("Ext.locale.en.data.validator.Inclusion",{
	override:"Ext.data.validator.Inclusion",
	message:"Is not in the list of acceptable values"
});
Ext.define("Ext.locale.en.data.validator.Length",{
	override:"Ext.data.validator.Length",
	minOnlyMessage:"Length must be at least {0}",
	maxOnlyMessage:"Length must be no more than {0}",
	bothMessage:"Length must be between {0} and {1}"
});
Ext.define("Ext.locale.en.data.validator.Presence",{
	override:"Ext.data.validator.Presence",
	message:"Must be present"
});
Ext.define("Ext.locale.en.data.validator.Range",{
	override:"Ext.data.validator.Range",
	minOnlyMessage:"Must be must be at least {0}",
	maxOnlyMessage:"Must be no more than than {0}",
	bothMessage:"Must be between {0} and {1}",
	nanMessage:"Must be numeric"
});
Ext.define("Ext.locale.en.view.View",{
	override:"Ext.view.View",emptyText:""
});
Ext.define("Ext.locale.en.grid.plugin.DragDrop",{
	override:"Ext.grid.plugin.DragDrop",dragText:"{0} selected row{1}"
});
Ext.define("Ext.locale.en.view.AbstractView",{
	override:"Ext.view.AbstractView",loadingText:"Loading..."
});
Ext.define("Ext.locale.en.picker.Date",{
	override:"Ext.picker.Date",
	todayText:"Today",
	minText:"This date is before the minimum date",
	maxText:"This date is after the maximum date",
	disabledDaysText:"",
	disabledDatesText:"",
	nextText:"Next Month (Control+Right)",
	prevText:"Previous Month (Control+Left)",
	monthYearText:"Choose a month (Control+Up/Down to move years)",
	todayTip:"{0} (Spacebar)",
	format:"m/d/y",startDay:0
});
Ext.define("Ext.locale.en.picker.Month",{
	override:"Ext.picker.Month",
	okText:"&#160;OK&#160;",
	cancelText:"Cancel"
});
Ext.define("Ext.locale.en.toolbar.Paging",{
	override:"Ext.PagingToolbar",
	beforePageText:"Page",
	afterPageText:"of {0}",
	firstText:"First Page",
	prevText:"Previous Page",
	nextText:"Next Page",
	lastText:"Last Page",
	refreshText:"Refresh",
	displayMsg:"Displaying {0} - {1} of {2}",
	emptyMsg:"No data to display"
});
Ext.define("Ext.locale.en.form.Basic",{
	override:"Ext.form.Basic",
	waitTitle:"Please Wait..."
});
Ext.define("Ext.locale.en.form.field.Base",{
	override:"Ext.form.field.Base",
	invalidText:"The value in this field is invalid"
});
Ext.define("Ext.locale.en.form.field.Text",{
	override:"Ext.form.field.Text",
	minLengthText:"The minimum length for this field is {0}",
	maxLengthText:"The maximum length for this field is {0}",
	blankText:"This field is required",
	regexText:"",
	emptyText:null
});
Ext.define("Ext.locale.en.form.field.Number",{
	override:"Ext.form.field.Number",
	decimalPrecision:2,
	minText:"The minimum value for this field is {0}",
	maxText:"The maximum value for this field is {0}",
	nanText:"{0} is not a valid number"
});
Ext.define("Ext.locale.en.form.field.Date",{
	override:"Ext.form.field.Date",
	disabledDaysText:"Disabled",
	disabledDatesText:"Disabled",
	minText:"The date in this field must be after {0}",
	maxText:"The date in this field must be before {0}",
	invalidText:"{0} is not a valid date - it must be in the format {1}",
	format:"m/d/y",
	altFormats:"m/d/Y|m-d-y|m-d-Y|m/d|m-d|md|mdy|mdY|d|Y-m-d"
});
Ext.define("Ext.locale.en.form.field.ComboBox",{
	override:"Ext.form.field.ComboBox",
	valueNotFoundText:undefined
	},
	function(){
		Ext.apply(Ext.form.field.ComboBox.prototype.defaultListConfig,{loadingText:"Loading..."})
	}
);
Ext.define("Ext.locale.en.form.field.VTypes",{
	override:"Ext.form.field.VTypes",
	emailText:'This field should be an e-mail address in the format "user@example.com"',
	urlText:'This field should be a URL in the format "http://www.example.com"',
	alphaText:"This field should only contain letters and _",
	alphanumText:"This field should only contain letters, numbers and _"
});
Ext.define("Ext.locale.en.form.field.HtmlEditor",{
	override:"Ext.form.field.HtmlEditor",
	createLinkText:"Please enter the URL for the link:"
	},
	function(){
		Ext.apply(Ext.form.field.HtmlEditor.prototype,{buttonTips:{bold:{title:"Bold (Ctrl+B)",text:"Make the selected text bold.",cls:Ext.baseCSSPrefix+"html-editor-tip"},italic:{title:"Italic (Ctrl+I)",text:"Make the selected text italic.",cls:Ext.baseCSSPrefix+"html-editor-tip"},underline:{title:"Underline (Ctrl+U)",text:"Underline the selected text.",cls:Ext.baseCSSPrefix+"html-editor-tip"},increasefontsize:{title:"Grow Text",text:"Increase the font size.",cls:Ext.baseCSSPrefix+"html-editor-tip"},decreasefontsize:{title:"Shrink Text",text:"Decrease the font size.",cls:Ext.baseCSSPrefix+"html-editor-tip"},backcolor:{title:"Text Highlight Color",text:"Change the background color of the selected text.",cls:Ext.baseCSSPrefix+"html-editor-tip"},forecolor:{title:"Font Color",text:"Change the color of the selected text.",cls:Ext.baseCSSPrefix+"html-editor-tip"},justifyleft:{title:"Align Text Left",text:"Align text to the left.",cls:Ext.baseCSSPrefix+"html-editor-tip"},justifycenter:{title:"Center Text",text:"Center text in the editor.",cls:Ext.baseCSSPrefix+"html-editor-tip"},justifyright:{title:"Align Text Right",text:"Align text to the right.",cls:Ext.baseCSSPrefix+"html-editor-tip"},insertunorderedlist:{title:"Bullet List",text:"Start a bulleted list.",cls:Ext.baseCSSPrefix+"html-editor-tip"},insertorderedlist:{title:"Numbered List",text:"Start a numbered list.",cls:Ext.baseCSSPrefix+"html-editor-tip"},createlink:{title:"Hyperlink",text:"Make the selected text a hyperlink.",cls:Ext.baseCSSPrefix+"html-editor-tip"},sourceedit:{title:"Source Edit",text:"Switch to source editing mode.",cls:Ext.baseCSSPrefix+"html-editor-tip"}}})
	}
);
Ext.define("Ext.locale.en.grid.header.Container",{
	override:"Ext.grid.header.Container",
	sortAscText:"Sort Ascending",
	sortDescText:"Sort Descending",
	columnsText:"Columns"
});
Ext.define("Ext.locale.en.grid.GroupingFeature",{
	override:"Ext.grid.feature.Grouping",
	emptyGroupText:"(None)",
	groupByText:"Group by this field",
	showGroupsText:"Show in Groups"
});
Ext.define("Ext.locale.en.grid.PropertyColumnModel",{
	override:"Ext.grid.PropertyColumnModel",
	nameText:"Name",
	valueText:"Value",
	dateFormat:"m/j/Y",
	trueText:"true",
	falseText:"false"
});
Ext.define("Ext.locale.en.grid.BooleanColumn",{
	override:"Ext.grid.BooleanColumn",
	trueText:"true",
	falseText:"false",
	undefinedText:"&#160;"
});
Ext.define("Ext.locale.en.grid.NumberColumn",{
	override:"Ext.grid.NumberColumn",
	format:"0,000.00"
});
Ext.define("Ext.locale.en.grid.DateColumn",{
	override:"Ext.grid.DateColumn",
	format:"m/d/Y"
});
Ext.define("Ext.locale.en.form.field.Time",{
	override:"Ext.form.field.Time",
	minText:"The time in this field must be equal to or after {0}",
	maxText:"The time in this field must be equal to or before {0}",
	invalidText:"{0} is not a valid time",
	format:"g:i A",
	altFormats:"g:ia|g:iA|g:i a|g:i A|h:i|g:i|H:i|ga|ha|gA|h a|g a|g A|gi|hi|gia|hia|g|H"
});
Ext.define("Ext.locale.en.form.field.File",{
	override:"Ext.form.field.File",
	buttonText:"Browse..."
});
Ext.define("Ext.locale.en.form.CheckboxGroup",{
	override:"Ext.form.CheckboxGroup",
	blankText:"You must select at least one item in this group"
});
Ext.define("Ext.locale.en.form.RadioGroup",{
	override:"Ext.form.RadioGroup",
	blankText:"You must select one item in this group"
});
Ext.define("Ext.locale.en.window.MessageBox",{
	override:"Ext.window.MessageBox",
	buttonText:{ok:"OK",cancel:"Cancel",yes:"Yes",no:"No"}
});
Ext.define("Ext.locale.en.grid.filters.Filters",{
	override:"Ext.grid.filters.Filters",
	menuFilterText:"Filters"
});
Ext.define("Ext.locale.en.grid.filters.filter.Boolean",{
	override:"Ext.grid.filters.filter.Boolean",yesText:"Yes",noText:"No"
});
Ext.define("Ext.locale.en.grid.filters.filter.Date",{
	override:"Ext.grid.filters.filter.Date",
	fields:{lt:{text:"Before"},gt:{text:"After"},eq:{text:"On"}},dateFormat:null
});
Ext.define("Ext.locale.en.grid.filters.filter.List",{
	override:"Ext.grid.filters.filter.List",
	loadingText:"Loading..."
});
Ext.define("Ext.locale.en.grid.filters.filter.Number",{
	override:"Ext.grid.filters.filter.Number",
	emptyText:"Enter Number..."
});
Ext.define("Ext.locale.en.grid.filters.filter.String",{
	override:"Ext.grid.filters.filter.String",emptyText:"Enter Filter Text..."
});
Ext.define("Ext.locale.en.Component",{
	override:"Ext.Component"
});

/*com, pjt, def, tc, rep*/
Otm = new Array();
Otm.com = "Common";
Otm.com_number = "Number";
Otm.com_add = "Add";
Otm.com_update = "Update";
Otm.com_remove = "Delete";
Otm.com_view = "View";
Otm.com_export = "Export";
Otm.com_import = "Import";
Otm.com_login = "Login";
Otm.com_logout = "Logout";
Otm.com_close = "Close";
Otm.com_save = "Save";
Otm.com_cancel = "Cancel";
Otm.com_email = "E-mail";
Otm.com_pw = "Password";
Otm.com_pwok = "Confirm Password";
Otm.com_subject = "Title";//Subject
Otm.com_description = "Description";
Otm.com_start_date = "Start Date";
Otm.com_end_date = "End Date";
Otm.com_creator = "Creator";
Otm.com_date = "Registration Date";
Otm.com_modifiers = "Last Modifiers";
Otm.com_modified = "Last Modified Date";
Otm.com_system_setup = "System Setup";
Otm.com_user = "User";
Otm.com_member = "Member";
Otm.com_group = "Group";
Otm.com_code = "Code";
Otm.com_user_defined_form = "User Defined Form";

Otm.com_search = "Search";
Otm.com_attached_file = "Attached File";
Otm.com_loading = "Loading";
Otm.com_id = "ID";

Otm.com_name = "Name";
Otm.com_status = "Status";

Otm.com_all = "ALL";
Otm.com_approve = "Approve";
Otm.com_unapproved = "Unapproved";

Otm.com_userlist = "User List";
Otm.com_memberlist = "Member List";
Otm.com_admin = "Admin";
Otm.com_grouplist = "Group List";
Otm.com_groupname = "Group Name";
Otm.com_regist_date = "Registration Date";
Otm.com_contact_number = "Contact Number";


Otm.com_set_privacy = "Privacy Settings";
Otm.com_reset = "Reset";
Otm.com_default_value = "Default Value";
Otm.com_complete = "Complete";
Otm.com_sort = "Sort";
Otm.com_category = "Category";
Otm.com_form_type = "Form Type";
Otm.com_role = "Role";
Otm.com_def_lifecycle = "Defect Lifecycle";
Otm.com_role_auth = "Role and authority";
Otm.com_mandatory = "Mandatory";
Otm.com_detailed_data = "Detailed data";
Otm.com_management = "Management";
Otm.com_up = "Up";
Otm.com_down = "Down";
Otm.com_saved = "Saved";
Otm.com_progress = "In progress";
Otm.com_standby = "Standby";
Otm.com_completed = "Completed";
Otm.com_revision_history = "Revision history";
Otm.com_default_panel_close = "Detail View Close";
Otm.com_default_panel_open = "Detail View Open";
Otm.com_display_list = "Displayed in the List ";
Otm.com_search_condition_add = "Additional conditions";
Otm.com_search_condition_del = "Conditions deleted";


Otm.com_period = "Period";
Otm.com_year = "Year";
Otm.com_month = "Month";
Otm.com_week = "Week";
Otm.com_day = "Day";

Otm.com_link = 'Connect';
Otm.com_unlink = 'Disconnect';
Otm.com_linklist = 'Link List';


Otm.com_unable_delete ="Unable to delete";
Otm.com_tc_link_cnt = "Number of connected test cases";
Otm.com_plugin_info = "Plug-in information";

Otm.com_mail_alram = "Mail Alram";
Otm.com_msg_mail_sended = "Send Mail Success.";

Otm.com_msg_delete_default_value = "You can not delete the default value.";
Otm.com_msg_update_default_value = "You can not change the default value.";
Otm.com_msg_should_default_value = "There should be a default value.";

Otm.com_msg_update = "Would you like to change ?";
Otm.com_msg_add = "Would you like to add ?";

Otm.com_msg_connected = "Connected.";
Otm.com_msg_disconnected = "Disconnected.";

Otm.com_msg_duplicate_id = "There are duplicate IDs. Would you like to update them ?";


Otm.com_msg_deleteConfirm = 'Would you like to delete the selected item?';
Otm.com_msg_NotSelectData = "Please select an item.";
Otm.com_msg_NotSelectRole = "Please select an Role.";
Otm.com_msg_default_mandatory = "Setting the default value is mandatory.";
Otm.com_msg_default_detailed_data = "There must be one more detailed data, the default value has to be chosen.";
Otm.com_msg_detailed_mandatory = "Detailed data is mandatory.";
Otm.com_msg_opt_detailed_mandatory = "Setting the Option Detailed data value is mandatory.";
Otm.com_msg_NotChanged = "No changes have been made to this";
Otm.com_msg_save = 'Saved successfully';
Otm.com_msg_only_one = "Please choose only one";
Otm.com_msg_NotVersion_add = "When you choose version, you can add. <br>Please select version.";
Otm.com_msg_testcase_inadd = "You can not add in testcases.";
Otm.com_msg_processing_data = "Processing data";
Otm.com_msg_plan_in_testcase = "You copy testcases which were chosen from the left to the plan on the right";
Otm.com_msg_product_del_alldata = "If you delete the product, all data beloning to it will also be deleted.";
Otm.com_msg_version_del_alldata = "If you delete the version, all data beloning to it will also be deleted.";
Otm.com_msg_responsible_person = "Please choose a responsible person";
Otm.com_msg_select_items_assign = "Please select items to assign";
Otm.com_msg_deadline = "Please choose a Deadline";
Otm.com_msg_cannot_edit_backlog = "You can not edit Back Log";
Otm.com_msg_cannot_del_backlog = "You can not delete Back Log";
Otm.com_msg_can_use_backlog = "You can use only in Back Log";
Otm.com_msg_plan_alldata_delete = "If you delete the plan, all data beloning to it will also be deleted";

Otm.com_msg_cannot_fun_plan = "You can add/change/delete plan";
Otm.com_msg_suite_tc_add_info = "It is added into the selected suite. <br> You can not add in testcases.<br>If you want to add in the top priority, you just unselect the choice. For unselecting, you just select the chosen suite, pressing \"Ctrl\".";
Otm.com_msg_select_item_change = "You can change the selected item(s)";
Otm.com_msg_select_item_delete = "You can delete the selected item(s)<br>If you delete suite, all data belonging to it will also be deleted";
Otm.com_msg_plan_use_suite = "You can use only in plan. <br> You can not assing a responsible person on suite";
Otm.com_msg_plan_select_plan = "When you choose plan, you can add. <br>Please select plan";
Otm.com_msg_select_plan = "Please select plan";
Otm.com_msg_copy_selecttc = "Please select testcases to copy.";
Otm.com_msg_please_search_keyword = "Please, input keyword for search.";
Otm.com_msg_NotVersion = "Please select version.";
Otm.com_msg_select_user = "Please select User.";
Otm.com_msg_select_role = "Please select Role.";

Otm.com_msg_isdelete_allProject = "When items are deleted, all data belonging to the project will also be deleted. <br> Do you really want to delete ?";
Otm.com_msg_isupdate_allProject = "When items are changed, all data belonging to the project will also be changed. <br> Do you really want to change ?";

Otm.com_msg_overwritten_commontc = "It will be overwritten when there is same ID. <br> Will you bring common test cases which you chosen ?";
Otm.com_msg_authorization_add_defects = "No authorization to add defects";
Otm.com_msg_default_user_notchange = "Default user can not change";
Otm.com_msg_default_user_notdel = "Default user can not delete";
Otm.com_msg_please_search_con = "Please choose the search condition";
Otm.com_msg_please_search_word = "Please input search word";
Otm.com_msg_duplicate_data = "Duplicate data are already existed";
Otm.com_msg_please_select_change = "Please select data to change";
Otm.com_msg_youneed_auth = "You need the authorization of the item creater or administrator";;//"You need the authorization of the project creater or administrator";
Otm.com_msg_please_choose_products = "Please choose products to add to version";
Otm.com_msg_root_cannot_modify = "Root can not be modified.";
Otm.com_msg_root_cannot_delete = "Root can not be deleted.";
Otm.com_msg_execution_description_save = "There is no Execution of the description. <br>Do you really want to save ?";
Otm.com_msg_notselect_project_copy = "Please select the project you want to copy";

Otm.com_msg_noRole = ' [No authority]';


Otm.com_msg_project_update_auth = "* The authority of project change is the participating project setting authority."
Otm.com_msg_tc_view_auth = "* The authority of seeing test cases enable to see test cases which assigned to themselves.";
Otm.com_msg_tc_allview_auth = "* If you don't have authority to see all test cases, you can not see test cases at backlog.";

Otm.com_msg_assign_cancel = "Would you like to cancel at a responsible person of selected testcase?";

Otm.tree_expandAll = "Expand All";
Otm.tree_collapseAll = "Collapse All";
Otm.tree_select_expand = "Select expansion";
Otm.tree_select_collapse = "Select collapse";
Otm.reload = "Reload";

Otm.dashboard = "Dashboard";
Otm.group = "Group";
Otm.copy = "Copy";

Otm.com_msg_NotSelectGroup = "Please select an group.";
Otm.com_msg_DeleteGroup = "When erase group, low rank group and all project are erased.";
Otm.com_join = "Join";
Otm.com_nonattendance = "Nonattendance";

/*pjt*/
Otm.pjt = "Project";
Otm.pjt_info = "Project Information";
Otm.pjt_name = "Project Name";
Otm.pjt_creator = "Project Creator";
Otm.pjt_member = "Project Member";

/*def*/
Otm.def = "Defect";
Otm.def_cnt = "Defect Count";
Otm.def_status = "Status";
Otm.def_severity = "Severity";
Otm.def_priority = "Priority";
Otm.def_frequency = "Frequency of reproduction";
Otm.def_assignment = "Assignment";
Otm.def_item = "Item";
Otm.def_dashboard = "Defect Dashboard";
Otm.def_allview = "View All";
Otm.def_writed_defect = "Writed";
Otm.def_assigned_defect = "Assigned";

/*tc*/
Otm.tc = "TestCase";
Otm.tc_execution = "Execution";
Otm.tc_input_item = "Input items for testcase";
Otm.tc_execution_result = "Execution result";
Otm.tc_execution_result_item = "Execution result items";
Otm.tc_execution_result_item_all = "Execution result items for testcase";
Otm.tc_execution_user = "Executed User";
Otm.tc_execution_regdate = "Executed date";

Otm.tc_plan = "Plan";
Otm.tc_master_suite = "Master Suite";
Otm.tc_suite = "Suite";
Otm.tc_status = "Status";
Otm.tc_plan_copy = "Copy to plan";
Otm.tc_precondition = "Precondition";
Otm.tc_testdata = "Test data";

Otm.tc_action_performed = "Action performed";
Otm.tc_expected_result = "Expected result";
Otm.tc_remarks = "Remarks";
Otm.tc_assign_persion = "Assign a responsible person";
Otm.tc_assign_persion_cancel = "Cancel a responsible person";
Otm.tc_deadline = "Deadline";

/*rep*/
Otm.rep = "Report";
Otm.rep_plan_suite_result = "Suite result by plan";
Otm.rep_plan_tc_result = "Test case executed result by plan";

Otm.rep_defect_scurve = "Accumulated S-curve";
Otm.rep_data_unit = "Data Unit";

//Otm.rep_testcase_result_summary = "Summary of test case executed result";
Otm.rep_suite_result = "Test result by suites";
//Otm.rep_testcase_result = "Test case executed result";
Otm.rep_plan_defect_list = "Defect list by plan";
Otm.rep_defect_list = "Defects list";
Otm.rep_suite_defect_distribution = "Defect distrubution by suites";

Otm.rep_old = "Old Report";
Otm.rep_testprogress = "Test Progress";
Otm.rep_defect_dashboard = "Defect Dashboard";

Otm.rep_alltestcase_result_summary = "Summary of all test case executed result";
Otm.rep_alltestcase_result = "Total executed result";
Otm.rep_plantestcase_result_summary = "Summary of executed result";
Otm.rep_plantestcase_result = "Executed result by plan";

Otm.rep_defect_status_info			= "Defect status infomation";
Otm.rep_defect_severity_info		= "Defect severity information";
Otm.rep_defect_priority_info		= "Defect priority information";
Otm.rep_defect_frequency_info		= "Defect frequency information";
Otm.rep_tc_result_summary = "Executed result information";
Otm.rep_data_table = "Data Table";
Otm.rep_defect_conn_number = "Number of connected defects";

Otm.rep_open_defect = "Opened defects";
Otm.rep_close_defect = "Closed defects";
Otm.rep_final_executed_result = "Final executed result";
Otm.rep_testcase_conn_num = "Number of connected test cases";


/*file_doc*/
Otm.file_doc = "Document Management";




/*comtc*/
Otm.comtc = "Common Testcases Management";
Otm.comtc_products = "Products";
Otm.comtc_productname = "Product Name";
Otm.comtc_version = "Version";
Otm.comtc_versionname = "Version Name";

/*requirement*/
Otm.requirement = "Requirement";


Otm.com_usage = "Usage";
Otm.id_rule = {
	project_id_rule			: "Project ID structure",
	tc_id_rule				: "Test case ID structure",
	df_id_rule				: "Defect ID structure",
	date_type				: "Registration date form",
	date_type_select		: "Registration date form selection",
	number_type				: "Serial number position",
	mumber_type_select		: "Serial number position selection",
	fixed_value				: "Fixed value",
	separator_select		: "Separator selection",
	no_select				: "No selection",
	temp_display			: "{Fixed value}{Registration date form}{Separator}{Serial number position}",
	over_id_number_msg		: "You exceeded the serial number position which was set at ID structure. <br> Please change the serial number position at ID structure.",
	delete_defaultvalue_msg	: "You can't delete the ID structure that was set as the default value.",
	tc_update_msg			: "It will not be applied to the previous test case ID, but it will be applied to the newly added test cases. <br> Would you like to change test case ID structure ?",
	tc_add_msg				: "It will not be applied to the previous test case ID, but it will be applied to the newly added test cases. <br> Would you like to add test case ID structure ?",
	df_update_msg			: "It will not be applied to the previous defect ID, but it will be applied to the newly added defects. <br> Would you like to change defect ID structure ?",
	df_add_msg				: "It will not be applied to the previous defect ID, but it will be applied to the newly added defects. <br> Would you like to add defect ID structure ?",
	fixed_value_msg			: "Please input fixed ID value."
};

Otm.comtc_msg_unsupport_service = "Commonness test case has OTestManager process function restriction.<br>I am considering test case removal and copy between project to solve this.<br><br>Within 2016 years<br>Commonness test case plans to erase, and develop test case copy function between project.<br>Please sublate to use commonness test case.<br><br>Thank you.";

Otm.com_msg_company_info = "Copyright STA Testing Consulting 2015 Corporation. All Rights Reserved.";

Otm.com_msg_project_copy_not_item = "Run a result, defects are not connected.";