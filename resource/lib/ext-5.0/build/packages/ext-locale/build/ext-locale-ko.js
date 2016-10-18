Ext.onReady(function(){
	if(Ext.Date){
		Ext.Date.monthNames=["1월","2월","3월","4월","5월","6월","7월","8월","9월","10월","11월","12월"];
		Ext.Date.dayNames=["일","월","화","수","목","금","토"]
	}
	if(Ext.util&&Ext.util.Format){
		Ext.apply(Ext.util.Format,{
			thousandSeparator:",",
			decimalSeparator:".",
			currencySign:"\u20a9",
			dateFormat:"m/d/Y"})
	}
});
Ext.define("Ext.locale.ko.view.View",{
	override:"Ext.view.View",emptyText:""
});
Ext.define("Ext.locale.ko.grid.plugin.DragDrop",{
	override:"Ext.grid.plugin.DragDrop",
	dragText:"{0} 개가 선택되었습니다."
});
Ext.define("Ext.locale.ko.tab.Tab",{
	override:"Ext.tab.Tab",closeText:"닫기"
});
Ext.define("Ext.locale.ko.form.field.Base",{
	override:"Ext.form.field.Base",
	invalidText:"올바른 값이 아닙니다."
});
Ext.define("Ext.locale.ko.view.AbstractView",{
	override:"Ext.view.AbstractView",
	loadingText:"로딩중..."
});
Ext.define("Ext.locale.ko.picker.Date",{
	override:"Ext.picker.Date",
	todayText:"오늘",
	minText:"최소 날짜범위를 넘었습니다.",
	maxText:"최대 날짜범위를 넘었습니다.",
	disabledDaysText:"",
	disabledDatesText:"",
	nextText:"다음달(컨트롤키+오른쪽 화살표)",
	prevText:"이전달 (컨트롤키+왼족 화살표)",
	monthYearText:"월을 선택해주세요. (컨트롤키+위/아래 화살표)",
	todayTip:"{0} (스페이스바)",
	format:"m/d/y",startDay:0
});
Ext.define("Ext.locale.ko.picker.Month",{
	override:"Ext.picker.Month",
	okText:"확인",
	cancelText:"취소"
});
Ext.define("Ext.locale.ko.toolbar.Paging",{
	override:"Ext.PagingToolbar",
	beforePageText:"페이지",
	afterPageText:"/ {0}",
	firstText:"첫 페이지",
	prevText:"이전 페이지",
	nextText:"다음 페이지",
	lastText:"마지막 페이지",
	refreshText:"새로고침",
	displayMsg:"전체 {2} 중 {0} - {1}",
	emptyMsg:"표시할 데이터가 없습니다."
});
Ext.define("Ext.locale.ko.form.field.Text",{
	override:"Ext.form.field.Text",
	minLengthText:"최소길이는 {0}입니다.",
	maxLengthText:"최대길이는 {0}입니다.",
	blankText:"값을 입력해주세요.",
	regexText:"",
	emptyText:null
});
Ext.define("Ext.locale.ko.form.field.Number",{
	override:"Ext.form.field.Number",
	minText:"최소값은 {0}입니다.",
	maxText:"최대값은 {0}입니다.",
	nanText:"{0}는 올바른 숫자가 아닙니다."
});
Ext.define("Ext.locale.ko.form.field.Date",{
	override:"Ext.form.field.Date",
	disabledDaysText:"비활성",
	disabledDatesText:"비활성",
	minText:"{0}일 이후여야 합니다.",
	maxText:"{0}일 이전이어야 합니다.",
	invalidText:"{0}는 올바른 날짜형식이 아닙니다. - 다음과 같은 형식이어야 합니다. {1}",
	format:"m/d/y"
});
Ext.define("Ext.locale.ko.form.field.ComboBox",{
	override:"Ext.form.field.ComboBox",
	valueNotFoundText:undefined
	},
	function(){
		Ext.apply(Ext.form.field.ComboBox.prototype.defaultListConfig,{loadingText:"로딩중..."})
});
Ext.define("Ext.locale.ko.form.field.VTypes",{
	override:"Ext.form.field.VTypes",
	emailText:'이메일 주소 형식에 맞게 입력해야합니다. (예: "user@example.com")',
	urlText:'URL 형식에 맞게 입력해야합니다. (예: "http://www.example.com")',
	alphaText:"영문, 밑줄(_)만 입력할 수 있습니다.",
	alphanumText:"영문, 숫자, 밑줄(_)만 입력할 수 있습니다."
});
Ext.define("Ext.locale.ko.form.field.HtmlEditor",{
	override:"Ext.form.field.HtmlEditor",
	createLinkText:"URL을 입력해주세요:"
	},
	function(){
		Ext.apply(
			Ext.form.field.HtmlEditor.prototype,{
				buttonTips:{
					bold:{
						title:"굵게 (Ctrl+B)",text:"선택한 텍스트를 굵게 표시합니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					italic:{
						title:"기울임꼴 (Ctrl+I)",text:"선택한 텍스트를 기울임꼴로 표시합니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					underline:{
						title:"밑줄 (Ctrl+U)",text:"선택한 텍스트에 밑줄을 표시합니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					increasefontsize:{
						title:"글꼴크기 늘림",text:"글꼴 크기를 크게 합니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					decreasefontsize:{
						title:"글꼴크기 줄임",text:"글꼴 크기를 작게 합니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					backcolor:{
						title:"텍스트 강조 색",text:"선택한 텍스트의 배경색을 변경합니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					forecolor:{
						title:"글꼴색",text:"선택한 텍스트의 색을 변경합니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					justifyleft:{
						title:"텍스트 왼쪽 맞춤",text:"왼쪽에 텍스트를 맞춥니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					justifycenter:{
						title:"가운데 맞춤",text:"가운데에 텍스트를 맞춥니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					justifyright:{
						title:"텍스트 오른쪽 맞춤",text:"오른쪽에 텍스트를 맞춥니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					insertunorderedlist:{
						title:"글머리 기호",text:"글머리 기호 목록을 시작합니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					insertorderedlist:{
						title:"번호 매기기",text:"번호 매기기 목록을 시작합니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					createlink:{
						title:"하이퍼링크",text:"선택한 텍스트에 하이퍼링크를 만듭니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					},
					sourceedit:{
						title:"소스편집",text:"소스편집 모드로 변환합니다.",cls:Ext.baseCSSPrefix+"html-editor-tip"
					}
				}
			}
		)
});
Ext.define("Ext.locale.ko.grid.header.Container",{
	override:"Ext.grid.header.Container",
	sortAscText:"오름차순 정렬",
	sortDescText:"내림차순 정렬",
	lockText:"칼럼 잠금",
	unlockText:"칼럼 잠금해제",
	columnsText:"칼럼 목록"
});
Ext.define("Ext.locale.ko.grid.GroupingFeature",{
	override:"Ext.grid.feature.Grouping",
	emptyGroupText:"(None)",
	groupByText:"현재 필드로 그룹핑합니다.",
	showGroupsText:"그룹으로 보여주기"
});
Ext.define("Ext.locale.ko.grid.PropertyColumnModel",{
	override:"Ext.grid.PropertyColumnModel",
	nameText:"항목",
	valueText:"값",
	dateFormat:"m/j/Y"
});
Ext.define("Ext.locale.ko.window.MessageBox",{
	override:"Ext.window.MessageBox",
	buttonText:{ok:"확인",cancel:"취소",yes:"예",no:"아니오"}
});
Ext.define("Ext.locale.ko.Component",{
	override:"Ext.Component"
});

/*com, pjt, def, tc, rep*/
Otm = new Array();
Otm.com = "공통";
Otm.com_number = "번호";
Otm.com_add = "추가";
Otm.com_update = "수정";
Otm.com_remove = "삭제";
Otm.com_view = "보기";
Otm.com_export = "내보내기";
Otm.com_import = "가져오기";
Otm.com_login = "로그인";
Otm.com_logout = "로그아웃";
Otm.com_close = "닫기";
Otm.com_save = "저장";
Otm.com_cancel = "취소";
Otm.com_email = "이메일";
Otm.com_pw = "패스워드";
Otm.com_pwok = "패스워드 확인";
Otm.com_subject = "제목";
Otm.com_description = "설명";
Otm.com_start_date = "시작일";
Otm.com_end_date = "종료일";
Otm.com_creator = "작성자";
Otm.com_date = "작성일";
Otm.com_modifiers = "수정자";
Otm.com_modified = "수정일";
Otm.com_system_setup = "환경설정";
Otm.com_user = "담당자";
Otm.com_member = "사용자";
Otm.com_group = "그룹";
Otm.com_code = "코드";
Otm.com_user_defined_form = "사용자 정의 서식";
Otm.com_search = "검색";
Otm.com_attached_file = "첨부파일";
Otm.com_loading = "로딩중";
Otm.com_id = "아이디";
Otm.com_name = "이름";
Otm.com_status = "상태";
Otm.com_all = "전체";
Otm.com_approve = "승인";
Otm.com_unapproved = "미승인";
Otm.com_userlist = "사용자 리스트";
Otm.com_memberlist = "사용자 리스트";
Otm.com_admin = "관리자";
Otm.com_grouplist = "그룹 리스트";
Otm.com_groupname = "그룹명";
Otm.com_regist_date = "등록일";
Otm.com_contact_number = "연락처";
Otm.com_set_privacy = "개인 정보 설정";
Otm.com_reset = "재설정";
Otm.com_default_value = "기본값";
Otm.com_complete = "완료";
Otm.com_sort = "정렬";
Otm.com_category = "분류";
Otm.com_form_type = "폼 형식";
Otm.com_role = "역할";
Otm.com_def_lifecycle = "결함 수명 주기";
Otm.com_role_auth = "역할 및 권한";
Otm.com_mandatory = "필수";
Otm.com_detailed_data = "상세데이터";
Otm.com_management = "관리";
Otm.com_up = "위로";
Otm.com_down = "아래로";
Otm.com_saved = "저장되었습니다.";
Otm.com_progress = "진행중";
Otm.com_standby = "대기";
Otm.com_completed = "종료";
Otm.com_revision_history = "변경이력";
Otm.com_default_panel_close = "상세보기창 닫기";
Otm.com_default_panel_open = "상세보기창 열기";
Otm.com_display_list = "목록에 표시";
Otm.com_search_condition_add = "조건 추가";
Otm.com_search_condition_del = "조건 삭제";


Otm.com_period = "기간";
Otm.com_year = "년";
Otm.com_month = "월";
Otm.com_week = "주";
Otm.com_day = "일";

Otm.com_link = '연결';
Otm.com_unlink = '연결 해제';
Otm.com_linklist = '연결 목록';


Otm.com_unable_delete ="삭제불가";
Otm.com_tc_link_cnt = "TC 연결 개수";
Otm.com_plugin_info = "플러그인 정보";

Otm.com_mail_alram = "메일 알림";
Otm.com_msg_mail_sended = "메일을 발송하였습니다.";

Otm.com_msg_delete_default_value = "기본값은 삭제 할 수 없습니다.";
Otm.com_msg_update_default_value = "기본값은 수정 할 수 없습니다.";
Otm.com_msg_should_default_value = "하나는 기본값이 있어야 합니다.";

Otm.com_msg_update = "수정하시겠습니까?";
Otm.com_msg_add = "추가하시겠습니까?";

Otm.com_msg_connected = "연결했습니다.";
Otm.com_msg_disconnected = "연결 해제했습니다.";

Otm.com_msg_duplicate_id = "중복된 ID가 있습니다. 중복 ID를 업데이트하시겠습니까?";


Otm.com_msg_deleteConfirm = '선택하신 항목을 삭제 하시겠습니까?';
Otm.com_msg_NotSelectData = "항목을 선택하여 주세요.";
Otm.com_msg_NotSelectRole = "역할을 선택하여 주세요.";
Otm.com_msg_default_mandatory = "기본값 설정은 필수 선택입니다";
Otm.com_msg_default_detailed_data = "하나 이상의 상세데이터가 있어야 하며, 기본값을 선택해야 합니다.";
Otm.com_msg_detailed_mandatory = "상세데이터는 필수 입니다";
Otm.com_msg_opt_detailed_mandatory = "옵션 상세데이터는 필수 입니다.";
Otm.com_msg_NotChanged = "변경된 사항이 없습니다.";
Otm.com_msg_save = "정상적으로 저장 되었습니다";
Otm.com_msg_only_one = "하나만 선택하여 주세요";
Otm.com_msg_NotVersion_add = "버전을 선택해야 추가 할 수 있습니다.<br>버전을 선택해 주세요.";
Otm.com_msg_testcase_inadd = "테스트케이스에는 추가 할 수 없습니다.";
Otm.com_msg_processing_data = "로딩중...";//"데이터 처리중";

Otm.com_msg_plan_in_testcase = "좌측에 선택된 테스트케이스를 우측의 차수에 복사합니다.";
Otm.com_msg_product_del_alldata = "제품을 삭제하면 제품에 속해 있는 모든 데이터가 삭제됩니다.";
Otm.com_msg_version_del_alldata = "버전을 삭제하면 버전에 속해 있는 모든 데이터가 삭제됩니다.";
Otm.com_msg_responsible_person = "담당자를 선택해 주세요.";
Otm.com_msg_select_items_assign = "할당할 대상을 선택해 주세요.";
Otm.com_msg_deadline = "기한을 선택해 주세요.";
Otm.com_msg_cannot_edit_backlog = "Back Log는 수정할 수 없습니다.";
Otm.com_msg_cannot_del_backlog = "Back Log는 삭제할 수 없습니다.";
Otm.com_msg_can_use_backlog = "Back Log에서만 사용할 수 있습니다.";
Otm.com_msg_plan_alldata_delete = "차수를 삭제하면 차수에 포함된 모든 데이터가 삭제됩니다.";

Otm.com_msg_cannot_fun_plan = "차수를 추가/수정/삭제 할 수 있습니다.";
Otm.com_msg_suite_tc_add_info = "선택된 스윗에 추가 됩니다.<br>테스트케이스에는 추가 할 수 없습니다.<br>최상위에 추가를 원할 경우 선택을 해제하면 됩니다. 선택 대상 해제는 \"Ctrl\"를 누르고 선택된 대상을 선택하면 됩니다.";
Otm.com_msg_select_item_change = "선택된 대상을 수정합니다.";
Otm.com_msg_select_item_delete = "선택된 대상을 삭제합니다. <br>스윗을 삭제 시 스윗에 포함된 모든 대상이 삭제 됩니다.";
Otm.com_msg_plan_use_suite = "차수에서만 사용할 수 있습니다.<br>스윗은 담당자 지정이 안됩니다.";
Otm.com_msg_plan_select_plan = "차수를 선택해야 추가 할 수 있습니다.<br>차수를 선택해 주세요.";
Otm.com_msg_select_plan = "차수를 선택해 주세요.";
Otm.com_msg_copy_selecttc = "복사할 테스트케이스를 선택해 주세요.";
Otm.com_msg_please_search_keyword = "검색명을 입력하세요";
Otm.com_msg_NotVersion = "버전을 선택해 주세요.";
Otm.com_msg_select_user = "사용자를 선택해 주세요.";
Otm.com_msg_select_role = "역할을 선택해 주세요.";


Otm.com_msg_isdelete_allProject = "항목 삭제 시 모든 프로젝트에 반영됩니다.<br>삭제 하시겠습니까?";
Otm.com_msg_isupdate_allProject = "항목 수정 시 모든 프로젝트에 반영됩니다.<br>수정 하시겠습니까?";

Otm.com_msg_overwritten_commontc = "동일한 ID가 있을 경우 덮어씁니다.<br>선택한 공통테스트케이스를 가져오시겠습니까?";
Otm.com_msg_authorization_add_defects = "결함 추가 권한이 없습니다";
Otm.com_msg_default_user_notchange = "기본 사용자는 수정을 할 수 없습니다.";
Otm.com_msg_default_user_notdel = "기본 사용자는 삭제를 할 수 없습니다.";
Otm.com_msg_please_search_con = "검색 조건을 선택해 주세요";
Otm.com_msg_please_search_word = "검색명을 입력해 주세요";
Otm.com_msg_duplicate_data = "중복 데이터가 존재합니다.";
Otm.com_msg_please_select_change = "수정할 데이터를 선택해 주세요";
Otm.com_msg_youneed_auth = "생성자이거나 관리자 권한이 있어야 합니다.";//"프로젝트 생성자이거나 관리자 권한이 있어야 합니다.";
Otm.com_msg_please_choose_products = "버전에 추가할 제품을 선택해 주세요.";
Otm.com_msg_root_cannot_modify = "Root는 수정을 할수 없습니다.";
Otm.com_msg_root_cannot_delete = "Root는 삭제를 할수 없습니다.";
Otm.com_msg_execution_description_save = "실행 설명글이 없습니다. 저장하시겠습니까?";
Otm.com_msg_notselect_project_copy = "복사 할 프로젝트를 선택하세요";

Otm.com_msg_noRole = ' [권한이 없습니다]';


Otm.com_msg_project_update_auth = "* 프로젝트 수정 권한은 참여하고 있는 프로젝트의 설정 권한입니다."
Otm.com_msg_tc_view_auth = "* 테스트케이스 보기 권한은 자신이 담당자로 지정된 차수의 테스트케이스만 볼 수 있습니다.";
Otm.com_msg_tc_allview_auth = "* 테스트케이스 전체보기 권한이 없을 경우 backlog의 테스트케이스를 볼 수 없습니다.";

Otm.com_msg_assign_cancel = "선택하신 테스트 케이스의 담당자 지정을 취소하시겠습니까?";

Otm.tree_expandAll = "전체 열기";
Otm.tree_collapseAll = "전체 닫기";
Otm.tree_select_expand = "선택 열기";
Otm.tree_select_collapse = "선택 닫기";
Otm.reload = "새로고침";

Otm.dashboard = "대시보드";
Otm.group = "그룹";
Otm.copy = "복사";

Otm.com_msg_NotSelectGroup = "그룹을 선택해 주세요.";
Otm.com_msg_DeleteGroup = "그룹 삭제 시 하위 그룹과 프로젝트 모두 삭제 됩니다.";
Otm.com_join = "참여";
Otm.com_nonattendance = "불참";



/*pjt*/
Otm.pjt = "프로젝트";
Otm.pjt_info = "프로젝트 정보";
Otm.pjt_name = "프로젝트 명";
Otm.pjt_creator = "프로젝트 작성자";
Otm.pjt_member = "참여자";

/*def*/
Otm.def = "결함";
Otm.def_cnt = "결함 수";
Otm.def_status = "상태";
Otm.def_severity = "심각도";
Otm.def_priority = "우선순위";
Otm.def_frequency = "재현빈도";
Otm.def_assignment = "할당";
Otm.def_item = "항목";
Otm.def_dashboard = "결함 대시보드";
Otm.def_allview = "전체 보기";
Otm.def_writed_defect = "작성한 결함";
Otm.def_assigned_defect = "할당받은 결함";

/*tc*/
Otm.tc = "테스트케이스";
Otm.tc_execution = "실행";
Otm.tc_input_item = "테스트케이스 입력항목";
Otm.tc_execution_result = "실행결과";
Otm.tc_execution_result_item = "실행결과 항목";
Otm.tc_execution_result_item_all = "테스트케이스 실행결과 항목";
Otm.tc_execution_user = "실행자";//"수행자";
Otm.tc_execution_regdate = "실행일";//"수행일";

Otm.tc_plan = "차수";
Otm.tc_master_suite = "마스터 스윗";
Otm.tc_suite = "스윗";
Otm.tc_status = "상태";
Otm.tc_plan_copy = "차수에 복사";
Otm.tc_precondition = "사전조건";
Otm.tc_testdata = "테스트데이터";

Otm.tc_action_performed = "실행절차";
Otm.tc_expected_result = "예상결과";
Otm.tc_remarks = "비고";
Otm.tc_assign_persion = "담당자 지정";
Otm.tc_assign_persion_cancel = "담당자 취소";
Otm.tc_deadline = "기한";

/*rep*/
Otm.rep = "리포트";
Otm.rep_plan_suite_result = "차수별 스윗 결과";
Otm.rep_plan_tc_result = "차수별 테스트케이스 실행 결과";

Otm.rep_defect_scurve = "누적결함 S-커브";
Otm.rep_data_unit = "데이터 단위";

//Otm.rep_testcase_result_summary = "테스트케이스 실행 결과 요약";
Otm.rep_suite_result = "스윗별 테스트 결과";
//Otm.rep_testcase_result = "테스트케이스 실행 결과";
Otm.rep_plan_defect_list = "차수별 결함 목록";
Otm.rep_defect_list = "결함 목록";
Otm.rep_suite_defect_distribution = "스윗별 결함 분포도";

Otm.rep_old = "과거 리포트";

Otm.rep_testprogress = "테스트 진척도";
Otm.rep_defect_dashboard = "결함 대시보드";

Otm.rep_alltestcase_result_summary = "전체 실행 결과 요약";
Otm.rep_alltestcase_result = "전체 실행 결과";
Otm.rep_plantestcase_result_summary = "실행 결과 요약";
Otm.rep_plantestcase_result = "차수별 실행 결과";

Otm.rep_defect_status_info			= "결함 상태 현황";
Otm.rep_defect_severity_info		= "결함 심각도 현황";
Otm.rep_defect_priority_info		= "결함 우선순위 현황";
Otm.rep_defect_frequency_info		= "결함 재현빈도 현황"
Otm.rep_tc_result_summary = "실행 결과 현황";
Otm.rep_data_table = "데이터 테이블";
Otm.rep_defect_conn_number = "결함 연결 개수";

Otm.rep_open_defect = "오픈 결함";
Otm.rep_close_defect = "종료 결함";
Otm.rep_final_executed_result = "최종 실행 결과";
Otm.rep_testcase_conn_num = "테스트케이스 연결 개수";



/*file_doc*/
Otm.file_doc = "파일(문서)";



/*comtc*/
Otm.comtc = "공통 테스트케이스";
Otm.comtc_products = "제품";
Otm.comtc_productname = "제품명";
Otm.comtc_version = "버전";
Otm.comtc_versionname = "버전명";

/*requirement*/
Otm.requirement = "요구사항";

Otm.com_usage = "사용";
Otm.id_rule = {
	project_id_rule			: "프로젝트 ID 체계",
	tc_id_rule				: "테스트케이스 ID 체계",
	df_id_rule				: "결함 ID 체계",
	date_type				: "등록날짜 형식",
	date_type_select		: "등록날짜 형식 선택",
	number_type				: "일련 번호 자리",
	mumber_type_select		: "일련 번호 자리 선택",
	fixed_value				: "고정 값",
	separator_select		: "구분자 선택",
	no_select				: "선택 없음",
	temp_display			: "{고정값}{등록 날짜 형식}{구분자}{일련번호자리}",
	over_id_number_msg		: "ID 체계에서 설정한 일련 번호 자리를 초과했습니다.<br>ID 체계에서 일련 번호 자리를 수정해주세요.",
	delete_defaultvalue_msg	: "기본 값으로 지정된 ID체계는 삭제할 수 없습니다.",
	tc_update_msg			: "이전에 발급된 테스트 케이스 ID에는 적용되지 않으며 신규로 추가되는 테스트케이스부터 적용됩니다.<br>테스트 케이스 ID 체계를 수정하시겠습니까?",
	tc_add_msg				: "이전에 발급된 테스트 케이스 ID에는 적용되지 않으며 신규로 추가되는 테스트케이스부터 적용됩니다.<br>테스트 케이스 ID 체계를 추가하시겠습니까?",
	df_update_msg			: "이전에 발급된 결함 ID에는 적용되지 않으며 신규로 추가되는 결함부터 적용됩니다.<br>결함 ID 체계를 수정하시겠습니까?",
	df_add_msg				: "이전에 발급된 결함 ID에는 적용되지 않으며 신규로 추가되는 결함부터 적용됩니다.<br>결함 ID 체계를 추가하시겠습니까?",
	fixed_value_msg			: "ID 고정 값을 입력해주세요."
};

Otm.comtc_msg_unsupport_service = "공통 테스트케이스는 OTM의 프로세스상 기능 제약이 있습니다.<br>이를 해결하기 위해 프로젝트간 테스트케이스 이동 및 복사를 고려하고 있습니다.<br><br>2016년 내에 공통 테스트케이스는 삭제하고, 프로젝트간 테스트케이스 복사 기능을 개발할 계획입니다.<br>공통 테스트케이스 사용을 지양해 주시기 바랍니다.<br>감사합니다.";

Otm.com_msg_company_info = "Copyright 2015 (주)STA테스팅컨설팅 Corporation. All Rights Reserved.";

Otm.com_msg_project_copy_not_item = "실행결과, 결함 연결은 되지 않습니다.";