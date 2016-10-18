<?php
class LanguageLoader
{
	function initialize() {
		$ci =& get_instance();
		$ci->load->helper('language');

		$site_lang = $ci->session->userdata('mb_lang');
		switch($site_lang)
		{
			case "en":
				$ci->lang->load('otm','english');
				break;
			case "ko":
				$ci->lang->load('otm','korean');
				break;
			default:
				$ci->lang->load('otm',$site_lang);
				break;
		}
	}
}
?>