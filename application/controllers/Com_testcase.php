<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Com_testcase
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Com_testcase extends Controller
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
		$this->load->model('com_testcase_m');
	}
	public function aaaaa($temp_arr){
		echo json_encode($this->com_testcase_m->comtc_list_export_test());
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
	* Function index
	*
	* @return string
	*/
	public function index()
	{
		$this->load->view('common_testcase/common_testcase_v');
	}

	/**
	* Function product_tree_list
	*
	* @return json
	*/
	public function product_tree_list()
	{
		$data = array(
			'node' => $this->input->post_get('node', TRUE),
			'p_seq' => $this->input->post_get('p_seq', TRUE),
			'v_seq' => $this->input->post_get('v_seq', TRUE)
		);

		$product_list = $this->com_testcase_m->product_tree_list($data);
		echo json_encode($product_list);
	}

	/**
	* Function create_product
	*
	* @return json
	*/
	public function create_product()
	{
		$data = array(
			'p_subject' => $this->input->post('p_subject', TRUE),
			'p_description' => $this->input->post('p_description', TRUE)
		);

		echo $this->com_testcase_m->create_product($data);
	}

	/**
	* Function update_product
	*
	* @return json
	*/
	public function update_product()
	{
		$data = array(
			'p_seq' => $this->input->post('p_seq', TRUE),
			'p_subject' => $this->input->post('p_subject', TRUE),
			'p_description' => $this->input->post('p_description', TRUE)
		);

		echo $this->com_testcase_m->update_product($data);
	}

	/**
	* Function delete_product
	*
	* @return json
	*/
	public function delete_product()
	{
		$data = array(
			'seq' => $this->input->post('seq', TRUE)
		);

		echo $this->com_testcase_m->delete_product($data);
	}

	/**
	* Function create_version
	*
	* @return json
	*/
	public function create_version()
	{
		$data = array(
			'p_seq' => $this->input->post('p_seq', TRUE),
			'v_version_name' => $this->input->post('v_version_name', TRUE),
			'v_version_description' => $this->input->post('v_version_description', TRUE)
		);

		echo $this->com_testcase_m->create_version($data);
	}

	/**
	* Function update_version
	*
	* @return json
	*/
	public function update_version()
	{
		$data = array(
			'v_seq' => $this->input->post('v_seq', TRUE),
			'v_version_name' => $this->input->post('v_version_name', TRUE),
			'v_version_description' => $this->input->post('v_version_description', TRUE)
		);

		echo $this->com_testcase_m->update_version($data);
	}

	/**
	* Function delete_version
	*
	* @return json
	*/
	public function delete_version()
	{
		$data = array(
			'seq' => $this->input->post('seq', TRUE),
			'p_seq' => $this->input->post('p_seq', TRUE)
		);

		echo $this->com_testcase_m->delete_version($data);
	}

	/**
	* Function testcase_tree_list
	*
	* @return json
	*/
	public function testcase_tree_list()
	{
		$data = array(
			'node' => $this->input->get('node', TRUE),
			'v_seq' => $this->input->get('v_seq', TRUE)
		);

		$product_list = $this->com_testcase_m->testcase_tree_list($data);
		echo json_encode($product_list);
	}

	/**
	* Function get_testcase_info
	*
	* @return json
	*/
	public function get_testcase_info()
	{
		$data = array(
			'id' => $this->input->post('id', TRUE)
		);

		$list = $this->com_testcase_m->get_testcase_info($data);
		echo $this->return_json($list);
	}

	/**
	* Function create_testcase
	*
	* @return json
	*/
	public function create_testcase()
	{
		$data = array(
			'type' => $this->input->post('type', TRUE),
			'v_seq' => $this->input->post('v_seq', TRUE),
			'pid' => $this->input->post('pid', TRUE),
			'ct_out_id' => $this->input->post('out_id', TRUE),
			'ct_subject' => $this->input->post('ct_subject', TRUE),
			'ct_precondition' => $this->input->post('ct_precondition', TRUE),
			'ct_testdata' => $this->input->post('ct_testdata', TRUE),
			'ct_procedure' => $this->input->post('ct_procedure', TRUE),
			'ct_expected_result' => $this->input->post('ct_expected_result', TRUE),
			'ct_description' => $this->input->post('ct_description', TRUE)
		);

		echo $this->com_testcase_m->create_testcase($data);
	}

	/**
	* Function update_testcase
	*
	* @return json
	*/
	public function update_testcase()
	{
		$data = array(
			'seq' => $this->input->post('seq', TRUE),
			'type' => $this->input->post('type', TRUE),
			'v_seq' => $this->input->post('v_seq', TRUE),
			'pid' => $this->input->post('pid', TRUE),
			'ct_out_id' => $this->input->post('out_id', TRUE),
			'ct_subject' => $this->input->post('ct_subject', TRUE),
			'ct_precondition' => $this->input->post('ct_precondition', TRUE),
			'ct_testdata' => $this->input->post('ct_testdata', TRUE),
			'ct_procedure' => $this->input->post('ct_procedure', TRUE),
			'ct_expected_result' => $this->input->post('ct_expected_result', TRUE),
			'ct_description' => $this->input->post('ct_description', TRUE)
		);

		echo $this->com_testcase_m->update_testcase($data);
	}


	/**
	* Function delete_testcase
	*
	* @return json
	*/
	public function delete_testcase()
	{
		$data = array(
			'version' => json_decode($this->input->post('version', TRUE)),
			'list' => json_decode($this->input->post('list', TRUE))
		);

		echo $this->com_testcase_m->delete_testcase($data);
	}

	/**
	* Function move_testcase
	*
	* @return json
	*/
	public function move_testcase()
	{
		$data = array(
			'target_id' => $this->input->post('target_id', TRUE),
			'target_type' => $this->input->post('target_type', TRUE),
			'position' => $this->input->post('position', TRUE),
			'select_id' => json_decode($this->input->post('select_id', TRUE))
		);

		echo $this->com_testcase_m->move_testcase($data);
	}
}
//End of file Com_testcase.php
//Location: ./controllers/Com_testcase.php