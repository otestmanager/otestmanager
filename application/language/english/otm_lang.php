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

$lang['msg_empty_defect_id_rule'] = "Please, set defect id rule.";

$lang['project'] = "Project";
$lang['name'] = "Name";
$lang['count'] = " Count";
$lang['member'] = "Member";

$lang['defect'] = "Defect";
$lang['close_defect'] = "Close Defect";

$lang['subject'] = "Subejct";
$lang['description'] = "Description";
$lang['link_cnt'] = "TC Link Count";
$lang['plan'] = "Plan";
$lang['responsible_person'] = "Responsible Person";
$lang['writer'] = "Writer";
$lang['status'] = "Status";
$lang['severity'] = "Severity";
$lang['priority'] = "Priority";
$lang['frequency'] = "Frequency";
$lang['regdate'] = "Reg-Date";
$lang['start_date'] = "Start Date";
$lang['end_date'] = "End Date";

$lang['setup'] = "Setup";

$lang['user'] = "User";
$lang['id_structure'] = "ID Structure";
$lang['code'] = "Code";
$lang['user_defined_form'] = "User Defined Form";
$lang['lifecycle'] = "Lifecycle";

/* End of file otm_lang.php */
/* Location: ./system/language/korean/otm_lang.php */