<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Id_rule
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Id_rule extends Controller
{
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('id_rule_m');
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
	* Function id_rule_list
	*
	* @param array $type Post Data
	*
	* @return string
	*/
	public function id_rule_list($type)
	{
		$data = array(
			'type' => $type,
			'xtype'=> $this->input->post('xtype', TRUE)
		);
		$list = $this->id_rule_m->id_rule_list($data);
		echo $this->return_json($list);
	}

	/**
	* Function check_id_rule
	*
	* @param array $type Post Data
	*
	* @return string
	*/
	public function check_id_rule()
	{
		$co_is_required = ($this->input->post('co_is_required', TRUE)==='true')?'Y':'N';
		$co_is_default = ($this->input->post('co_is_default', TRUE)==='true')?'Y':'N';

		$data = array(
			'action_type' => $this->input->post('action_type', TRUE),
			'co_seq' => $this->input->post('co_seq', TRUE),
			'co_type' => $this->input->post('co_type', TRUE),
			'co_name' => $this->input->post('co_name', TRUE),
			'co_is_required' => $co_is_required,
			'co_is_default' => $co_is_default,
			'co_position' => $this->input->post('co_position',TRUE),
			'co_default_value' => $this->input->post('co_default_value',TRUE),
			'co_color' => $this->input->post('co_color',TRUE),
			'check' => true
		);

		$list = $this->id_rule_m->check_id_rule($data);
		echo $this->return_json($list);
	}


	/**
	* Function create_code
	*
	* @return string
	*/
	public function create_code()
	{
		$co_is_required = ($this->input->post('co_is_required', TRUE)==='true')?'Y':'N';
		$co_is_default = ($this->input->post('co_is_default', TRUE)==='true')?'Y':'N';
		$data = array(
			'co_type' => $this->input->post('co_type', TRUE),
			'co_name' => $this->input->post('co_name', TRUE),
			'co_is_required' => $co_is_required,
			'co_is_default' => $co_is_default,
			'co_position' => $this->input->post('co_position',TRUE),
			'co_default_value' => $this->input->post('co_default_value',TRUE),
			'co_color' => $this->input->post('co_color',TRUE)
		);

		echo $this->id_rule_m->create_code($data);
	}

	/**
	* Function update_code
	*
	* @return string
	*/
	public function update_code()
	{
		$co_is_required = ($this->input->post('co_is_required', TRUE)==='true')?'Y':'N';
		$co_is_default = ($this->input->post('co_is_default', TRUE)==='true')?'Y':'N';
		$data = array(
			'action_type' => $this->input->post('action_type', TRUE),
			'co_seq' => $this->input->post('co_seq', TRUE),
			'co_type' => $this->input->post('co_type', TRUE),
			'co_name' => $this->input->post('co_name', TRUE),
			'co_is_required' => $co_is_required,
			'co_is_default' => $co_is_default,
			'co_position' => $this->input->post('co_position',TRUE),
			'co_default_value' => $this->input->post('co_default_value',TRUE),
			'co_color' => $this->input->post('co_color',TRUE),
			'before_name' => $this->input->post('before_name',TRUE),
			'check' => $this->input->post('check',TRUE),
			'check_type' => $this->input->post('check_type',TRUE)
		);

		echo $this->id_rule_m->update_code($data);
	}

	/**
	* Function delete_code
	*
	* @return string
	*/
	public function delete_code()
	{
		$co_list = $this->input->post('co_list', TRUE);
		$co_list = json_decode($co_list);
		$data = array(
			'co_list'	=> $co_list,
			'action_type' => $this->input->post('action_type', TRUE),
			'co_type'	=> $this->input->post('co_type', TRUE)
		);

		echo $this->id_rule_m->delete_code($data);
	}
}
//End of file Id_rule.php
//Location: ./controllers/Id_rule.php