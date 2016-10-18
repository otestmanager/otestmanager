<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
	- User Guide
		EN : https://www.codeigniter.com/userguide2/libraries/language.html
		KO : http://cikorea.net/user_guide_2.1.0/libraries/language.html
			 http://cikorea.net/user_guide_2.1.0/helpers/language_helper.html

	- Usage
		: Activate 'post_controller_constructor' hooks

		application/config/hooks.php
			- $hook['post_controller_constructor'] =
				...

	- Example :
		echo lang('language_key', 'form_item_id');
	 // becomes <label for="form_item_id">language_key</label>
*/

$lang['msg_empty_defect_id_rule'] = "결함 ID 체계를 등록해주세요.";

$lang['project'] = "프로젝트";
$lang['name'] = "이름";
$lang['count'] = "수";
$lang['member'] = "참여자";

$lang['defect'] = "결함";
$lang['close_defect'] = "종료 결함";

$lang['subject'] = "제목";
$lang['description'] = "설명";
$lang['link_cnt'] = "TC 연결 개수";
$lang['plan'] = "차수";
$lang['responsible_person'] = "담당자";
$lang['writer'] = "작성자";
$lang['status'] = "상태";
$lang['severity'] = "심각도";
$lang['priority'] = "우선순위";
$lang['frequency'] = "재현빈도";
$lang['regdate'] = "작성일";
$lang['start_date'] = "시작일";
$lang['end_date'] = "종료일";

$lang['setup'] = "설정";

$lang['user'] = "사용자";
$lang['id_structure'] = "아이디 체계 관리";
$lang['code'] = "코드";
$lang['user_defined_form'] = "사용자 정의 서식";
$lang['lifecycle'] = "수명 주기";

/* End of file otm_lang.php */
/* Location: ./system/language/korean/otm_lang.php */