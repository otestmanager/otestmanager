<?php
/**
 * Class Tracking
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/

class Tracking extends Controller {
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('tracking/tracking_m');

		$this->load->model('testcase/testcase_m');
		$this->load->model('defect/defect_m');
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
	* Function defect
	*
	* @return string
	*/
	function tracking($data = array())
	{
		$data['view'] = $data['module_directory']."_v";
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
	* Function testcase_list
	*
	* @return string
	*/
	function testcase_list(){
		$data = array(
			'pr_seq' => $this->input->post_get('project_seq',true),
			'tcplan' => $this->input->post_get('tcplan',true)
		);

		$testcase_list = $this->tracking_m->testcase_list($data);
		echo $this->return_json($testcase_list);
	}

	/**
	* Function defect_list
	*
	* @return string
	*/
	function defect_list()
	{
		$sort = $this->input->post('sort',true);

		$data = array(
			'project_seq' => $this->input->post('project_seq',true),
			'limit' => $this->input->post('limit',true),
			'page' => $this->input->post('page',true),
			'start' => $this->input->post('start',true),
			'sort' => ($sort)?json_decode($sort):null
		);

		return $this->tracking_m->defect_list($data);
		exit;
	}

	/**
	* Function set_link
	*
	* @return string
	*/
	function set_link()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq',true),
			'tl_seq' => $this->input->post_get('tl_seq',true),
			'df_seq' => $this->input->post_get('df_seq',true),
			'result_value' => $this->input->post_get('result_value',true),
			'result_msg' => $this->input->post_get('result_msg',true)
		);

		$return = $this->tracking_m->set_link($data);
		echo $this->return_json($return);
	}

	/**
	* Function set_unlink
	*
	* @return string
	*/
	function set_unlink()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq',true),
			'tl_seq' => $this->input->post_get('tl_seq',true),
			'df_seq' => $this->input->post_get('df_seq',true)
		);

		$return = $this->tracking_m->set_unlink($data);
		echo $this->return_json($return);
	}
}
//End of file tracking.php
//Location: ./controllers/tracking.php
?>