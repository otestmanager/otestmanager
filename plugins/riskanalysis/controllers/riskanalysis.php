<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Riskanalysis
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/

class Riskanalysis extends Controller {
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('riskanalysis/riskanalysis_m');
		$this->load->library('File_Form');
		$this->load->library('History');
	}

	public function install()
	{
		$this->migration->install();
	}

	public function version()
	{
		$data = $this->data;
		$version = $data['param'];
		$this->migration->check_version($version);
	}

	/**
	* Function return_json
	*
	* @param array $temp_arr Array Data
	*
	* @return string
	*/
	public function return_json($temp_arr)
	{
		return '{success:true,totalCount: '.count($temp_arr).', data:'.json_encode($temp_arr).'}';
	}


	/**
	* Function riskanalysis
	*
	* @return string
	*/
	function riskanalysis($data = array())
	{
		$this->data = $data;

		if($data['function'] != 'install')
		{
			$check = $this->migration->_check_module('riskanalysis');
			if($check === FALSE)
			{
				//show_error('Module was not established. <br><br>Module Install : <a href="/index.php/Plugin_view/'.$data['module_directory'].'/install">Click</a>');
			}
		}

		if($data['subpage'] && $data['subpage'] !==''){
			$data['view'] = $data['module_directory']."_".$data['subpage']."_v";
		}else{
			$data['view'] = $data['module_directory']."_v";
		}
		//return $data['subpage'].'/'.$subpage.'/'.json_encode($data);
		$data['skin_dir'] = "plugins/".$data['module_directory']."/views/".$data['skin'];

		if($data['function']){
			return $this->$data['function']();
			exit;
		}else{
			$data['mb_lang'] = $this->session->userdata('mb_lang');
			return render($data);
		}
	}

	/**
	* Function riskanalysis_discussion
	*
	* @return string
	*/
	function riskanalysis_discussion($data = array())
	{
		$data = $this->data;

		$data['view'] = "riskanalysis_discussion_v";
		$data['skin_dir'] = "plugins/".$data['module_directory']."/views/".$data['skin'];
		$data['mb_lang'] = $this->session->userdata('mb_lang');

		//return json_encode($data);
		return render($data);
	}


	/**
	* Function riskanalysis_setup
	*
	* @return string
	*/
	function riskanalysis_setup($data = array())
	{
		$data = $this->data;

		$data['view'] = "riskanalysis_setup_v";
		$data['skin_dir'] = "plugins/".$data['module_directory']."/views/".$data['skin'];
		$data['mb_lang'] = $this->session->userdata('mb_lang');

		//return json_encode($data);
		return render($data);
	}


	/**
	* Function riskitem_list
	*
	* @return string
	*/
	function riskitem_list()
	{
		$data = array(
			'limit' => $this->input->post('limit',true),
			'page' => $this->input->post('page',true),
			'start' => $this->input->post('start',true),
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'node' => $this->input->post_get('node', TRUE)
		);
		
		$list = $this->riskanalysis_m->riskitem_list($data);
		return $this->return_json($list);
	}


	/**
	* Function view_riskitem
	*
	* @return string
	*/
	function view_riskitem()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'ri_seq' => $this->input->post_get('ri_seq', TRUE)
		);

		$info = $this->riskanalysis_m->view_riskitem($data);
		return $this->return_json($info);
	}
	

	/**
	* Function create_riskitem
	*
	* @return string
	*/
	function create_riskitem()
	{
		/*
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'node' => $this->input->post_get('node', TRUE),
			'ora_subject' => $this->input->post_get('ora_subject', TRUE),
			'permitions' => json_decode($this->input->post_get('permitions', TRUE))
		);

		return $this->riskanalysis_m->create_riskitem($data);
		*/

		$this->load->library('form_validation');

		$this->form_validation->set_rules('pr_seq','Project Seq', 'required');
		$this->form_validation->set_rules('riskitem_subjectForm','riskitem Subject', 'required');
		$this->form_validation->set_rules('riskitem_descriptionForm','riskitem Description', 'required');

		if($this->form_validation->run() == TRUE){
			$riskitem_data = array(
				'pr_seq' => $this->input->post('pr_seq',true),
				'ri_subject' => $this->input->post('riskitem_subjectForm',true),
				'ri_description' => $this->input->post('riskitem_descriptionForm',true),				
				'custom_form' => $this->input->post('custom_form',true)
			);

			if(isset($_FILES['form_file']))
			{
				$return_msg = $this->File_Form->file_chk('');
				switch($return_msg){
					case "max_size_over":
						print "{success:false,msg:'File Size Over (Max : 20M)'}";
						exit;
					break;
					case "not_support_extension":
					break;
				}
			}

			$ri_seq = $this->riskanalysis_m->create_riskitem($riskitem_data);

			if($ri_seq){
				print "{success:true,data:".json_encode($ri_seq)."}";
			}else{
				print "{success:false}";
			}
		}else{
			print "{success:false,msg:'".trim(strip_tags(validation_errors()))."'}";
			exit;
		}
	}


	/**
	* Function update_riskitem
	*
	* @return string
	*/
	function update_riskitem()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('pr_seq','Project Seq', 'required');
		$this->form_validation->set_rules('riskitem_seqForm','riskitem Seq', 'required');
		$this->form_validation->set_rules('riskitem_subjectForm','riskitem Subject', 'required');
		$this->form_validation->set_rules('riskitem_descriptionForm','riskitem Description', 'required');

		if($this->form_validation->run() == TRUE){
			$riskitem_data = array(
				'pr_seq' => $this->input->post('pr_seq',true),
				'ri_seq' => $this->input->post('riskitem_seqForm',true),
				'ri_subject' => $this->input->post('riskitem_subjectForm',true),
				'ri_description' => $this->input->post('riskitem_descriptionForm',true),
				'custom_form' => $this->input->post('custom_form',true)
			);
			if(isset($_FILES['form_file']))
			{
				$return_msg = $this->File_Form->file_chk('');
				switch($return_msg){
					case "max_size_over":
						print "{success:false,msg:'File Size Over (Max : 20M)'}";
						exit;
					break;
					case "not_support_extension":
					break;
				}
			}

			$ri_seq = $this->riskanalysis_m->update_riskitem($riskitem_data);

			if($ri_seq){
				print "{success:true,data:".json_encode($ri_seq)."}";
			}else{
				print "{success:false}";
			}
		}else{
			print "{success:false,msg:'".trim(strip_tags(validation_errors()))."'}";
			exit;
		}
	}


	/**
	* Function delete_riskitem
	*
	* @return string
	*/
	function delete_riskitem()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('pr_seq','Project Seq', 'required');

		$ri_list = json_decode($this->input->post('ri_list', TRUE));

		if($this->form_validation->run() == TRUE){
			$data = array(
				'pr_seq' => $this->input->post('pr_seq',true),
				'ri_list' => $ri_list
			);
			echo $this->riskanalysis_m->delete_riskitem($data);

		}else{
			echo validation_errors();
			exit;
		}
	}


	/**
	*******************************
	*	Riskitem Discussion
	*******************************
	*/


	/**
	* Function riskitem_discussion
	*
	* @return json string
	*/
	function riskitem_discussion()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE)
		);

		return $this->return_json($this->riskanalysis_m->riskitem_discussion($data));
	}


	/**
	* Function riskitem_discussion_save
	*
	* @return json string
	*/
	function riskitem_discussion_save()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'save_list' => json_decode($this->input->post('save_list', TRUE))

		);

		return $this->riskanalysis_m->riskitem_discussion_save($data);
	}


	/**
	*******************************
	*	Riskitem Discussion Result
	*******************************
	*/


	/**
	* Function riskitem_discussion_result
	*
	* @return json string
	*/
	function riskitem_discussion_result()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE)
		);

		return $this->return_json($this->riskanalysis_m->riskitem_discussion_result($data));
	}	


	/**
	*******************************
	*	riskanalysis_requirement
	*******************************
	*/

	/**
	* Function riskanalysis_requirement
	*
	* @return json string
	*/
	function riskanalysis_requirement()
	{
		if($this->input->post_get('ri_seq', TRUE)){
			$data = array(
				'pr_seq' => $this->input->post_get('pr_seq', TRUE),
				'ri_seq' => $this->input->post_get('ri_seq', TRUE),
				'type' => $this->input->post_get('type', TRUE),
			);
		}else{
			//return  $this->return_json(array('msg'=>'empty requirement data'));
			return  $this->return_json(array());
		}

		return  $this->return_json($this->riskanalysis_m->riskanalysis_requirement($data));
	}


	/**
	* Function riskitem_requirement_link
	*
	* @return json string
	*/
	function riskitem_requirement_link()
	{
		$data = array(
			'ri_seq' => $this->input->post_get('ri_seq', TRUE),
			'req_list' => json_decode($this->input->post_get('req_list', TRUE)),
		);
		return  $this->return_json($this->riskanalysis_m->riskitem_requirement_link($data));
	}



	/**
	*******************************
	*	Strategy
	*******************************
	*/

	/**
	* Function strategy
	*
	* @return json string
	*/
	function strategy()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE)
		);

		return $this->riskanalysis_m->strategy($data);
	}


	/**
	* Function strategy_save
	*
	* @return json string
	*/
	function strategy_save()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'riskarea_seq' => $this->input->post_get('riskarea_seq', TRUE),
			'testlevel_seq' => $this->input->post_get('testlevel_seq', TRUE),
			'value' => $this->input->post_get('value', FALSE)
		);

		return $this->riskanalysis_m->strategy_save($data);
	}


	/**
	*******************************
	*	Setup : Code
	*******************************
	*/


	/**
	* Function code_list
	*
	* @return json string
	*/
	function code_list()
	{
		$type = $this->input->post('type', TRUE);
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'type' => $this->input->post('type', TRUE)
		);

		switch($type)
		{
			case "likelihood":
			case "impact":
				$list = $this->riskanalysis_m->factor_list($data);
				break;
			default:
				$list = $this->riskanalysis_m->code_list($data);
				break;
		}


		return $this->return_json($list);
	}

	/**
	* Function create_code
	*
	* @return json string
	*/
	function create_code()
	{
		$type = $this->input->post('pco_type', TRUE);
		$pco_is_required = ($this->input->post('pco_is_required', TRUE)==='true')?'Y':'N';
		$pco_is_default = ($this->input->post('pco_is_default', TRUE)==='true')?'Y':'N';
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'pco_type' => $type,
			'pco_name' => $this->input->post('pco_name', TRUE),
			'pco_is_required' => $pco_is_required,
			'pco_is_default' => $pco_is_default,
			'pco_default_value' => $this->input->post('pco_default_value', TRUE),
			'pco_1' => $this->input->post('pco_1', TRUE),
			'pco_2' => $this->input->post('pco_2', TRUE)
		);

		switch($type)
		{
			case "likelihood":
			case "impact":
				$result = $this->riskanalysis_m->create_factor($data);
				break;
			default:
				$result = $this->riskanalysis_m->create_code($data);
				break;
		}

		return $result;
	}


	/**
	* Function update_code
	*
	* @return json string
	*/
	function update_code()
	{
		$type = $this->input->post('pco_type', TRUE);
		$pco_is_required = ($this->input->post('pco_is_required', TRUE)==='true')?'Y':'N';
		$pco_is_default = ($this->input->post('pco_is_default', TRUE)==='true')?'Y':'N';
		$data = array(
			'pco_seq' => $this->input->post('pco_seq', TRUE),
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'pco_type' => $type,
			'pco_name' => $this->input->post('pco_name', TRUE),
			'pco_is_required' => $pco_is_required,
			'pco_is_default' => $pco_is_default,
			'pco_default_value' => $this->input->post('pco_default_value', TRUE),
			'pco_1' => $this->input->post('pco_1', TRUE),
			'pco_2' => $this->input->post('pco_2', TRUE)
		);

		switch($type)
		{
			case "likelihood":
			case "impact":
				$result = $this->riskanalysis_m->update_factor($data);
				break;
			default:
				$result = $this->riskanalysis_m->update_code($data);
				break;
		}

		return $result;
	}


	/**
	* Function delete_code
	*
	* @return string
	*/
	public function delete_code()
	{
		$type = $this->input->post('pco_type', TRUE);
		$pco_list = $this->input->post('pco_list', TRUE);
		$pco_list = json_decode($pco_list);

		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'pco_list' => $pco_list
		);

		switch($type)
		{
			case "likelihood":
			case "impact":
				$result = $this->riskanalysis_m->delete_factor($data);
				break;
			default:
				$result = $this->riskanalysis_m->delete_code($data);
				break;
		}

		return $result;
	}


	/**
	* Function update_sort_code
	*
	* @return string
	*/
	public function update_sort_code()
	{
		$type = $this->input->post('pco_type', TRUE);
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'pco_type' => $this->input->post('pco_type', TRUE),
			'pco_list' => $this->input->post('pco_list', TRUE)
		);

		switch($type)
		{
			case "likelihood":
			case "impact":
				$result = $this->riskanalysis_m->update_sort_factor($data);
				break;
			default:
				$result = $this->riskanalysis_m->update_sort_code($data);
				break;
		}

		return $result;
	}

	

}
//End of file riskanalysis.php
//Location: ./controllers/riskanalysis.php
?>