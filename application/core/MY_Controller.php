<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Router class */
require APPPATH."third_party/MX/Controller.php";

class Controller extends MX_Controller {
	function __construct()
	{
		parent::__construct();

		if($this->session->userdata('logged_in') !== TRUE){
			$tmp_class = strtolower($this->router->class);
			$tmp_method = strtolower($this->router->method);

			if($tmp_class === 'plugin_view')
			{
				$this->output->enable_profiler(FALSE);
				$this->seg_exp = $this->common->segment_explode($this->uri->uri_string());

				$tmp_method = $this->seg_exp[2];
			}

			if($tmp_class !== 'otm' AND $tmp_class !== 'login'
				AND $tmp_method !== 'install' AND $tmp_method !== 'version' AND $tmp_class !== 'filedownload'){
				redirect('', 'location');
			}
		}else{
			/*
			switch($this->session->userdata('mb_lang'))
			{
				case "ko":
					$this->config->set_item('language', 'korean');
					$this->lang->load("otm","korean");
					break;
				case "en":
				default:
					$this->config->set_item('language', 'english');
					$this->lang->load("otm","english");
					break;
			}
			*/
		}
	}
}