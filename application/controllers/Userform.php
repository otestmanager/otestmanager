<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Userform
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Userform extends Controller
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
		$this->load->model('userform_m');
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
	* Function userform_list
	*
	* @return none
	*/
	public function userform_list()
	{
		$data = array(
			'pr_seq'=> $this->input->post('pr_seq', TRUE),
			'cf_category' => $this->input->post('cf_category', TRUE)
		);
		$list = $this->userform_m->userform_list($data);
		echo $this->return_json($list);
	}

	/**
	* Function create_userform
	*
	* @return none
	*/
	public function create_userform()
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$cf_is_required = ($this->input->post('cf_is_required', TRUE)==='true')?'Y':'N';
		$cf_is_display = ($this->input->post('cf_is_display', TRUE)==='true')?'Y':'N';
		$data = array(
			'cf_category' => $this->input->post('cf_category', TRUE),
			'cf_formtype' => $this->input->post('cf_formtype', TRUE),
			'cf_name' => $this->input->post('cf_name', TRUE),
			'cf_is_required' => $cf_is_required,
			'cf_is_display' => $cf_is_display,
			'cf_default_value' => $this->input->post('cf_default_value', TRUE),
			'cf_content' => $this->input->post('cf_content', TRUE),
			'writer' => $writer,
			'regdate' => $date,
			'last_writer' => '',
			'last_update' => ''
		);

		echo $this->userform_m->create_userform($data);
	}

	/**
	* Function update_userform
	*
	* @return none
	*/
	public function update_userform()
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$cf_is_required = ($this->input->post('cf_is_required', TRUE)==='true')?'Y':'N';
		$cf_is_display = ($this->input->post('cf_is_display', TRUE)==='true')?'Y':'N';
		$data = array(
			'cf_seq' => $this->input->post('cf_seq', TRUE),
			'cf_category' => $this->input->post('cf_category', TRUE),
			'cf_formtype' => $this->input->post('cf_formtype', TRUE),
			'cf_name' => $this->input->post('cf_name', TRUE),
			'cf_is_required' => $cf_is_required,
			'cf_is_display' => $cf_is_display,
			'cf_default_value' => $this->input->post('cf_default_value', TRUE),
			'cf_content' => $this->input->post('cf_content', TRUE),
			'last_writer' => $writer,
			'last_update' => $date
		);

		echo $this->userform_m->update_userform($data);
	}

	/**
	* Function delete_userform
	*
	* @return none
	*/
	public function delete_userform()
	{
		$cf_list = $this->input->post('cf_list', TRUE);
		$cf_list = json_decode($cf_list);
		$data = array(
			'cf_list' => $cf_list
		);

		echo $this->userform_m->delete_userform($data);
	}

	/**
	* Function option_list
	*
	* @return none
	*/
	public function option_list()
	{
		if($this->input->post('cf_seq', TRUE)){
			$data = array(
				'cf_seq' => $this->input->post('cf_seq', TRUE),
			);

			$option_data = $this->userform_m->option_list($data);
			$temp = json_decode($option_data);
			echo $this->return_json($temp);
		}
		else{
			echo '{success:true,totalCount: 0, data:[]}';
		}
	}

	/**
	* Function update_sort_userlist
	*
	* @return none
	*/
	public function update_sort_list()
	{
		$data = array(
			'userform_list' => $this->input->post('userform_list', TRUE)
		);

		print $this->userform_m->update_sort_list($data);
	}

}

/* End of file Userform.php */
/* Location: ./application/controllers/Userform.php */