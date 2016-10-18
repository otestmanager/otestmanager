<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Code
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Code extends Controller
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
		$this->load->model('code_m');
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
	* Function code_list
	*
	* @param array $type Post Data
	*
	* @return string
	*/
	public function code_list($type)
	{
		$data = array(
			'type' => $type
		);
		$list = $this->code_m->code_list($data);
		echo $this->return_json($list);
	}

	/**
	* Function code_list_workflow
	*
	* @param array $type Post Data
	*
	* @return string
	*/
	public function code_list_workflow($type)
	{
		$data = array(
			'type' => 'status',
			'rp_seq' => $this->input->post('rp_seq', TRUE)
		);

		$value_info = $this->code_m->code_list_workflow_valuefield($data);
		$list = $this->code_m->code_list_workflow($data, $value_info);
		echo '{success:true,totalCount: '.count($list).', head:'.json_encode($value_info).', data:'.json_encode($list).'}';
	}

	/**
	* Function update_workflow
	*
	* @return string
	*/
	public function update_workflow()
	{
		$data = array(
			'workflow_data' => $this->input->post('workflow_data', TRUE)
		);

		print $this->code_m->update_workflow($data);
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

		echo $this->code_m->create_code($data);
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
			'co_seq' => $this->input->post('co_seq', TRUE),
			'co_type' => $this->input->post('co_type', TRUE),
			'co_name' => $this->input->post('co_name', TRUE),
			'co_is_required' => $co_is_required,
			'co_is_default' => $co_is_default,
			'co_position' => $this->input->post('co_position',TRUE),
			'co_default_value' => $this->input->post('co_default_value',TRUE),
			'co_color' => $this->input->post('co_color',TRUE)
		);

		echo $this->code_m->update_code($data);
	}

	/**
	* Function update_sort_code
	*
	* @return string
	*/
	public function update_sort_code()
	{
		$data = array(
			'co_type' => $this->input->post('co_type', TRUE),
			'co_list' => $this->input->post('co_list', TRUE)
		);

		print $this->code_m->update_sort_code($data);
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
			'co_list' => $co_list
		);

		echo $this->code_m->delete_code($data);
	}
}
//End of file Code.php
//Location: ./controllers/Code.php