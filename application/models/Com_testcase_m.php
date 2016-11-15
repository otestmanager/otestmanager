<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Common_testcase_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Com_testcase_m extends CI_Model
{
	public $tmp_array = Array();
	public $root_array = Array();
	public $return_array = Array();
	public $location_path = Array();

	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
	}

	function duplicate_check($data)
	{

		if($data['select_key'] != ''){
			$this->db->select($data['select_key']);
		}

		$this->db->from($data['table']);

		for($i=0; $i<count($data['key']); $i++)
		{
			$this->db->where($data['key'][$i]['column'],$data['key'][$i]['value']);
		}

		if($data['update_key'] != ''){
			$this->db->where($data['update_key']['column'].' !=',$data['update_key']['value']);
		}
		$query = $this->db->get();
		if($query->result()){
			if($data['select_key'] != ''){
				foreach ($query->result() as $row)
				{
					return $row->$data['select_key'];
				}
			}else{
				return 'Duplicate Value : "'.$data['key'][0]['value'].'"';
			}
		}
	}

	/**
	* Function product_tree_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function product_tree_list($data)
	{
		$temp_arr = array();

		$p_seq = $data['p_seq'];
		$node = $data['node'];
		$node = explode('_', $node);

		if($node[0] === 'root'){

			$this->db->from('otm_com_product');
			$this->db->order_by('p_seq asc');
			$query = $this->db->get();

			$i=0;
			foreach ($query->result() as $temp_row)
			{
				$temp_arr[$i]['seq'] = $temp_row->p_seq;
				$temp_arr[$i]['id'] = $temp_row->p_seq.'_comproduct';
				$temp_arr[$i]['text'] = $temp_row->p_subject;
				$temp_arr[$i]['type'] = 'product';
				$temp_arr[$i]['description'] = $temp_row->p_description;
				$temp_arr[$i]['writer'] = $temp_row->writer;
				$temp_arr[$i]['regdate'] = $temp_row->regdate;
				$temp_arr[$i]['leaf'] = FALSE;
				$i++;
			}
			return $temp_arr;
		}
		else{

			$this->db->from('otm_com_version');
			$this->db->where('otm_com_product_p_seq',($p_seq)?$p_seq:$node[0]);
			$this->db->order_by('v_seq asc');
			$query = $this->db->get();

			$i=0;
			foreach ($query->result() as $temp_row)
			{
				$temp_arr[$i]['p_seq'] = $node[0];
				$temp_arr[$i]['seq'] = $temp_row->v_seq;
				$temp_arr[$i]['id'] = $temp_row->v_seq.'_comversion';
				$temp_arr[$i]['text'] = $temp_row->v_version_name;
				$temp_arr[$i]['type'] = 'version';
				$temp_arr[$i]['writer'] = $temp_row->writer;
				$temp_arr[$i]['regdate'] = $temp_row->regdate;
				$temp_arr[$i]['leaf'] = TRUE;
				$i++;
			}
			return $temp_arr;
		}
	}

	/**
	* Function create_product
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_product($data)
	{
		$duplicate['table'] = 'otm_com_product';
		$duplicate['key'] = array(
								array('column'=>'p_subject','value'=>$data['p_subject'])
							);
		$duplicate['update_key'] = '';
		$check_value = $this->duplicate_check($duplicate);
		if($check_value){
			return $check_value;
		}

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$data2['p_subject'] = $data['p_subject'];
		$data2['p_description'] = $data['p_description'];
		$data2['writer'] = $writer;
		$data2['regdate'] = $date;
		$data2['last_writer'] = '';
		$data2['last_update'] = '';

		$this->db->insert('otm_com_product', $data2);
		$result = $this->db->insert_id();

		return $result.'_comproduct';
	}

	/**
	* Function update_product
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_product($data)
	{
		$duplicate['table'] = 'otm_com_product';
		$duplicate['key'] = array(
								array('column'=>'p_subject','value'=>$data['p_subject'])
							);
		$duplicate['update_key'] = array('column'=>'p_seq','value'=>$data['p_seq']);
		$check_value = $this->duplicate_check($duplicate);
		if($check_value){
			return $check_value;
		}

		$date = date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$modify_array = array(
			'p_subject' => $data['p_subject'],
			'p_description' => $data['p_description'],
			'last_writer' => $writer,
			'last_update' => $date
		);

		$this->db->where('p_seq', $data['p_seq']);
		$this->db->update('otm_com_product', $modify_array);

		return $data['p_seq'].'_comproduct';
	}

	/**
	* Function delete_product
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_product($data)
	{
		//제품 삭제요청에 의한 제품에 속해있는 버전 정보 삭제
		$data['p_seq'] = $data['seq'];
		$this->delete_version($data);

		//제품 삭제요청에 의한 해당 제품 삭제
		$delete_array = array(
			'p_seq' => $data['seq']
		);
		$result = $this->db->delete('otm_com_product', $delete_array);

		return $result;
	}

	/**
	* Function create_version
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_version($data)
	{
		$duplicate['table'] = 'otm_com_version';
		$duplicate['key'] = array(
								array('column'=>'v_version_name','value'=>$data['v_version_name']),
								array('column'=>'otm_com_product_p_seq','value'=>$data['p_seq'])
							);
		$duplicate['update_key'] = '';
		$check_value = $this->duplicate_check($duplicate);
		if($check_value){
			return $check_value;
		}

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$version_data['otm_com_product_p_seq'] = $data['p_seq'];
		$version_data['v_version_name'] = $data['v_version_name'];
		$version_data['v_version_description'] = $data['v_version_description'];
		$version_data['writer'] = $writer;
		$version_data['regdate'] = $date;
		$version_data['last_writer'] = '';
		$version_data['last_update'] = '';

		$this->db->insert('otm_com_version', $version_data);
		$result = $this->db->insert_id();

		return $result.'_comversion';
	}

	/**
	* Function update_version
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_version($data)
	{
		$duplicate['table'] = 'otm_com_version';
		$duplicate['key'] = array(
								array('column'=>'v_version_name','value'=>$data['v_version_name'])
							);
		$duplicate['update_key'] = array('column'=>'v_seq','value'=>$data['v_seq']);
		$check_value = $this->duplicate_check($duplicate);
		if($check_value){
			return $check_value;
		}

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$modify_array = array(
			'v_version_name' => $data['v_version_name'],
			'v_version_description' => $data['v_version_description'],
			'last_writer' => $writer,
			'last_update' => $date
		);
		$where = array('v_seq'=>$data['v_seq']);
		$this->db->update('otm_com_version', $modify_array, $where);

		return $data['v_seq'].'_comversion';
	}

	/**
	* Function delete_version
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_version($data)
	{
		if($data['p_seq']){
			//버전에 속해 있는 테스트케이스 삭제
			$this->db->select('v_seq');
			$this->db->where('otm_com_product_p_seq', $data['seq']);
			$query = $this->db->get('otm_com_version');
			$result = $query->result();
			if( ! $result){
			}
			else{
				foreach ($result as $temp_row)
				{
					$delete_array = array(
						'otm_com_version_v_seq' => $temp_row->v_seq
					);
					$result = $this->db->delete('otm_com_testcase', $delete_array);
				}
			}

			//제품 삭제요청에 의한 해당 제품 버전 삭제
			$delete_array = array(
				'otm_com_product_p_seq' => $data['seq']
			);
			$result = $this->db->delete('otm_com_version', $delete_array);
			return $result;
		}
		else{
			//버전에 속해 있는 테스트케이스 삭제
			$delete_array = array(
				'otm_com_version_v_seq' => $data['seq']
			);
			$result = $this->db->delete('otm_com_testcase', $delete_array);

			//버전 삭제요청에 의한 해당 버전 삭제
			$delete_array = array(
				'v_seq' => $data['seq']
			);
			$result = $this->db->delete('otm_com_version', $delete_array);
		}
		return $result;
	}

	/**
	* Function testcase_tree_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function testcase_tree_list($data)
	{
		$temp_arr = array();

		$v_seq = $data['v_seq'];
		$node = $data['node'];
		$node = explode('_', $node);

		if($node[0] === 'root'){
			$this->db->select('comtc.*,mb.mb_email as writer,mb.mb_name as writer_name,mb2.mb_email as last_writer,mb2.mb_name as last_writer_name');
			$this->db->from('otm_com_testcase as comtc');
			$this->db->join('otm_member as mb','comtc.writer=mb.mb_email','left');
			$this->db->join('otm_member as mb2','comtc.last_writer=mb2.mb_email','left');
			$this->db->where('otm_com_version_v_seq',$v_seq);
			$this->db->where('ct_inp_pid','ctc_0');
			$this->db->order_by('ct_ord,ct_seq asc');
			$query = $this->db->get();

			$i=0;
			foreach ($query->result() as $temp_row)
			{
				$temp_arr[$i]['v_seq'] = $temp_row->otm_com_version_v_seq;
				$temp_arr[$i]['seq'] = $temp_row->ct_seq;
				$temp_arr[$i]['pid'] = $temp_row->ct_inp_pid;
				$temp_arr[$i]['id'] = $temp_row->ct_inp_id;
				$temp_arr[$i]['out_id'] = $temp_row->ct_out_id;
				$temp_arr[$i]['text'] = $temp_row->ct_subject;
				$temp_arr[$i]['type'] = $temp_row->ct_is_task;
				$temp_arr[$i]['writer'] = $temp_row->writer;
				$temp_arr[$i]['writer_name'] = $temp_row->writer_name;
				$temp_arr[$i]['last_writer'] = $temp_row->last_writer;
				$temp_arr[$i]['last_writer_name'] = $temp_row->last_writer_name;
				$temp_arr[$i]['last_update'] = $temp_row->last_update;
				$temp_arr[$i]['leaf'] = ($temp_row->ct_is_task === 'folder')?FALSE:TRUE;
				$i++;
			}
			return $temp_arr;
		}
		else{
			$this->db->select('comtc.*,mb.mb_email as writer,mb.mb_name as writer_name,mb2.mb_email as last_writer,mb2.mb_name as last_writer_name');
			$this->db->from('otm_com_testcase as comtc');
			$this->db->join('otm_member as mb','comtc.writer=mb.mb_email','left');
			$this->db->join('otm_member as mb2','comtc.last_writer=mb2.mb_email','left');
			$this->db->where('ct_inp_pid',$data['node']);
			$this->db->order_by('ct_ord,ct_seq asc');
			$query = $this->db->get();

			$i=0;
			foreach ($query->result() as $temp_row)
			{
				$temp_arr[$i]['v_seq'] = $temp_row->otm_com_version_v_seq;
				$temp_arr[$i]['seq'] = $temp_row->ct_seq;
				$temp_arr[$i]['pid'] = $temp_row->ct_inp_pid;
				$temp_arr[$i]['id'] = $temp_row->ct_inp_id;
				$temp_arr[$i]['out_id'] = $temp_row->ct_out_id;
				$temp_arr[$i]['text'] = $temp_row->ct_subject;
				$temp_arr[$i]['type'] = $temp_row->ct_is_task;
				$temp_arr[$i]['writer'] = $temp_row->writer;
				$temp_arr[$i]['writer_name'] = $temp_row->writer_name;
				$temp_arr[$i]['last_writer'] = $temp_row->last_writer;
				$temp_arr[$i]['last_writer_name'] = $temp_row->last_writer_name;
				$temp_arr[$i]['last_update'] = $temp_row->last_update;
				$temp_arr[$i]['leaf'] = ($temp_row->ct_is_task === 'folder')?FALSE:TRUE;
				$i++;
			}
			return $temp_arr;
		}
	}

	/**
	* Function get_testcase_info
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function get_testcase_info($data)
	{
		$temp_arr = array();

		$this->db->select('comtc.*,mb.mb_email as writer,mb.mb_name as writer_name,mb2.mb_email as last_writer,mb2.mb_name as last_writer_name');
		$this->db->from('otm_com_testcase as comtc');
		$this->db->join('otm_member as mb','comtc.writer=mb.mb_email','left');
		$this->db->join('otm_member as mb2','comtc.last_writer=mb2.mb_email','left');
		$this->db->where('ct_inp_id', $data['id']);
		$this->db->order_by('ct_ord asc');
		$this->db->limit(1);
		$query = $this->db->get();

		foreach ($query->result() as $temp_row)
		{
			$temp_arr = $temp_row;
		}
		return $temp_arr;
	}

	/**
	* Function get_testcase_detail_info
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function get_testcase_detail_info($ct_seq)
	{
		$this->db->select('comtc.*,mb.mb_email as writer,mb.mb_name as writer_name,mb2.mb_email as last_writer,mb2.mb_name as last_writer_name');
		$this->db->from('otm_com_testcase as comtc');
		$this->db->join('otm_member as mb','comtc.writer=mb.mb_email','left');
		$this->db->join('otm_member as mb2','comtc.last_writer=mb2.mb_email','left');
		$this->db->where('ct_seq',$ct_seq);
		$this->db->order_by('ct_ord asc');
		$this->db->limit(1);
		$query = $this->db->get();
		$return_data = array();
		foreach ($query->result() as $temp_row)
		{
			$return_data['v_seq'] = $temp_row->otm_com_version_v_seq;
			$return_data['seq'] = $temp_row->ct_seq;
			$return_data['pid'] = $temp_row->ct_inp_pid;
			$return_data['id'] = $temp_row->ct_inp_id;
			$return_data['out_id'] = $temp_row->ct_out_id;
			$return_data['text'] = $temp_row->ct_subject;
			$return_data['type'] = $temp_row->ct_is_task;
			$return_data['writer'] = $temp_row->writer;
			$return_data['writer_name'] = $temp_row->writer_name;
			$return_data['last_writer'] = $temp_row->last_writer;
			$return_data['last_writer_name'] = $temp_row->last_writer_name;
			$return_data['last_update'] = $temp_row->last_update;
			$return_data['leaf'] = ($temp_row->ct_is_task === 'folder')?FALSE:TRUE;
		}

		return "{success:true, data:'".json_encode($return_data)."'}";
		exit;
	}

	/**
	* Function get_comtc_ord_maxval
	*
	* @return double
	*/
	private function get_comtc_ord_maxval($table,$ver_seq,$pid)
	{
		if($table == ''){
			$table = 'otm_com_testcase';
		}

		$str_sql = "select max(ct_ord) as max_ord from ".$table." where otm_com_version_v_seq='$ver_seq' and ct_inp_pid='$pid'";
		$query = $this->db->query($str_sql);
		$max_result = $query->result();

		if($max_result[0]->max_ord >= 1){
			$max_num = $max_result[0]->max_ord;
			$max_num++;
		}else{
			$max_num = 1;
		}
		return $max_num;
	}

	/**
	* Function create_testcase
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_testcase($data)
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$insert_array['otm_com_version_v_seq'] = $data['v_seq'];
		$insert_array['ct_subject'] = $data['ct_subject'];
		$insert_array['ct_description'] = $data['ct_description'];

		if(isset($data['pid']) && $data['pid'] === 'root') $data['pid'] = 'ctc_0';

		$insert_array['ct_inp_pid'] = ($data['pid'])?$data['pid']:'ctc_0';

		if(isset($data['ct_ord'])){
			$com_tc_ord = $data['ct_ord'];
		}else{
			$com_tc_ord = $this->get_comtc_ord_maxval("otm_com_testcase",$insert_array['otm_com_version_v_seq'],$insert_array["ct_inp_pid"]);
		}

		$insert_array['ct_ord'] = $com_tc_ord;
		$insert_array['writer'] = $writer;
		$insert_array['regdate'] = $date;
		$insert_array['last_writer'] = '';
		$insert_array['last_update'] = '';

		if($data['type'] === 'suite'){
			$id_head = 'cts_';
			$insert_array['ct_is_task'] = 'folder';
		}
		else{
			$id_head = 'ctc_';
			$insert_array['ct_is_task'] = 'file';
			$insert_array['ct_precondition'] = $data['ct_precondition'];
			$insert_array['ct_testdata'] = $data['ct_testdata'];
			$insert_array['ct_procedure'] = $data['ct_procedure'];
			$insert_array['ct_expected_result'] = $data['ct_expected_result'];
		}

		$this->db->insert('otm_com_testcase', $insert_array);
		$result = $this->db->insert_id();

		$modify_array['ct_inp_id'] = $id_head.$result;
		$modify_array['ct_out_id'] = ($data['ct_out_id'])?$data['ct_out_id']:$id_head.$result;
		$modify_array['ct_ord'] = $result;
		$where = array('ct_seq'=>$result);
		$this->db->update('otm_com_testcase', $modify_array, $where);

		if(isset($data['return_key'])){
			return $result;
		}

		return $this->get_testcase_detail_info($result);
		exit;
	}

	/**
	* Function update_testcase
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_testcase($data)
	{
		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$modify_array['ct_subject'] = $data['ct_subject'];
		$modify_array['ct_description'] = $data['ct_description'];

		if(isset($data['pid']) && $data['pid'] === 'root') $data['pid'] = 'ctc_0';

		$modify_array['ct_inp_pid'] = ($data['pid'])?$data['pid']:'ctc_0';
		$modify_array['last_writer'] = $writer;
		$modify_array['last_update'] = $date;

		$modify_array['ct_precondition'] = $data['ct_precondition'];
		$modify_array['ct_testdata'] = $data['ct_testdata'];
		$modify_array['ct_procedure'] = $data['ct_procedure'];
		$modify_array['ct_expected_result'] = $data['ct_expected_result'];

		if($data['type'] === 'suite'){
			$id_head = 'cts_';
		}
		else{
			$id_head = 'ctc_';
		}

		$where = array('ct_seq'=>$data['seq']);
		$modify_array['ct_out_id'] = ($data['ct_out_id'])?$data['ct_out_id']:$id_head.$data['seq'];
		$this->db->update('otm_com_testcase', $modify_array, $where);

		if(isset($data['return_key'])){
			return $modify_array['ct_out_id'];
		}
		return $this->get_testcase_detail_info($data['seq']);
		exit;
	}

	/**
	* Function delete_testcase
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_testcase($data)
	{
		$version = $data['version'];

		for($i=0; $i<count($data['list']); $i++){

			if(!isset($data['type']) || $data['type'][$i] === 'folder')
			{
				$this->db->select('ct_inp_id,ct_is_task');
				$this->db->where('ct_inp_pid', $data['list'][$i]);
				$this->db->where('otm_com_version_v_seq', $version);
				$query = $this->db->get('otm_com_testcase');
				$result = $query->result();
				if( ! $result){

				}
				else{

					$temp_arr = array();
					$temp_arr2 = array();
					foreach ($result as $temp_row)
					{
						$temp_arr[] = $temp_row->ct_inp_id;
						$temp_arr2[] = $temp_row->ct_is_task;
					}

					$new_data = array(
						'version' => $version,
						'list' => $temp_arr,
						'type' => $temp_arr2

					);
					$this->delete_testcase($new_data);
				}
			}

			$this->db->where('ct_inp_id', $data['list'][$i]);
			$this->db->delete('otm_com_testcase');//, $new_data);
		}
		return 'ok';
	}

	/**
	* Function move_testcase
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function move_testcase($data)
	{
		if(isset($data['target_id']) && $data['target_id'] === 'root') $data['target_id'] = 'ctc_0';

		$target_type = $data['target_type'];
		$target_id = $data['target_id'];
		$position = $data['position'];

		if($target_type == "file"){
			$select_size = count($data['select_id']);

			$str_sql = "select otm_com_version_v_seq,ct_inp_pid,ct_ord from otm_com_testcase where ct_inp_id='$target_id'";
			$query = $this->db->query($str_sql);
			$result = $query->result();
			$ct_inp_pid = $result[0]->ct_inp_pid;
			$ver_seq = $result[0]->otm_com_version_v_seq;
			$com_tc_ord = $result[0]->ct_ord;

			if($position == "before"){
				$str_sql = "update otm_com_testcase set ct_ord=ct_ord+$select_size where ct_inp_pid='$ct_inp_pid' and otm_com_version_v_seq='$ver_seq' and ct_ord>='$com_tc_ord'";
			}else if($position=="after"){
				$str_sql = "update otm_com_testcase set ct_ord=ct_ord+$select_size where ct_inp_pid='$ct_inp_pid' and otm_com_version_v_seq='$ver_seq' and ct_ord>'$com_tc_ord'";
				$com_tc_ord++;
			}
			$query = $this->db->query($str_sql);

			for($i=0; $i<count($data['select_id']); $i++){
				$modify_array['ct_inp_pid'] = $ct_inp_pid;
				$select_id = $data['select_id'][$i];

				$com_tc_ord += $i;
				$modify_array['ct_ord'] = $com_tc_ord;

				$where = array('ct_inp_id'=>$data['select_id'][$i]);
				$this->db->update('otm_com_testcase', $modify_array, $where);
			}
		}else{//folder
			if($position == "before" || $position == "after"){
				$select_size = count($data['select_id']);
				$modify_array['ct_inp_pid'] = $data['target_id'];

				$str_sql = "select otm_com_version_v_seq,ct_inp_pid,ct_ord from otm_com_testcase where ct_inp_id='$target_id'";
				$query = $this->db->query($str_sql);
				$result = $query->result();
				$ct_inp_pid = $result[0]->ct_inp_pid;
				$ver_seq = $result[0]->otm_com_version_v_seq;
				$com_tc_ord = $result[0]->ct_ord;

				$modify_array['ct_inp_pid'] = $ct_inp_pid;

				if($position == "before"){
					$str_sql = "update otm_com_testcase set ct_ord=ct_ord+$select_size where ct_inp_pid='$ct_inp_pid' and otm_com_version_v_seq='$ver_seq' and ct_ord>='$com_tc_ord'";
				}else if($position=="after"){
					$str_sql = "update otm_com_testcase set ct_ord=ct_ord+$select_size where ct_inp_pid='$ct_inp_pid' and otm_com_version_v_seq='$ver_seq' and ct_ord>'$com_tc_ord'";
					$com_tc_ord++;
				}
				$query = $this->db->query($str_sql);

			}else{
				$modify_array['ct_inp_pid'] = $data['target_id'];
			}

			for($i=0; $i<count($data['select_id']); $i++){
				$select_id = $data['select_id'][$i];

				$str_sql = "select otm_com_version_v_seq,ct_inp_pid from otm_com_testcase where ct_inp_id='$select_id'";
				$query = $this->db->query($str_sql);
				$result = $query->result();
				$ct_inp_pid = $result[0]->ct_inp_pid;
				$ver_seq = $result[0]->otm_com_version_v_seq;

				if($position == "before" || $position == "after"){
					$com_tc_ord += $i;
				}else{
					$com_tc_ord = $this->get_comtc_ord_maxval("otm_com_testcase",$ver_seq,$target_id);
				}

				$modify_array['ct_ord'] = $com_tc_ord;

				$where = array('ct_inp_id'=>$data['select_id'][$i]);
				$this->db->update('otm_com_testcase', $modify_array, $where);
			}
		}

		return 'ok';
	}

	/**
	* Function export
	*
	* @return array
	*/
	function export($data)
	{
		if(isset($data['function'])){
			switch($data['function'])
			{
				case 'comtc_list_export':
					return $this->comtc_list_export($data);
					break;
			}
		}
	}
	function get_location($pid){
		if($pid=="ctc_0" || $pid == ""){
			$location_reverse = array_reverse($this->location_path);

			return implode("/",$location_reverse);
		}
		for($j=0;$j<sizeof($this->tmp_array);$j++){
			if($this->tmp_array[$j]['id'] == $pid){
				array_push($this->location_path,$this->tmp_array[$j]['subject']);

				return $this->get_location($this->tmp_array[$j]['pid']);
				break;
			}
		}
	}

	function putTestCaseItem($id){
		$my_pid=0;
		for($j=0;$j<sizeof($this->tmp_array);$j++){
			if($this->tmp_array[$j]['pid'] == $id){
				$my_pid = $this->tmp_array[$j]['pid'];
				break;
			}
		}
		for($j=0;$j<sizeof($this->tmp_array);$j++){
			if($this->tmp_array[$j]['pid'] == $id){

				$this->tmp_array[$j]['location'] = $this->get_location($my_pid);
				$this->location_path=array();

				array_push($this->return_array,$this->tmp_array[$j]);
				$this->putTestCaseItem($this->tmp_array[$j]['id']);
			}
		}
	}

	/**
	* Function comtc_list_export
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function comtc_list_export($data)
	{
		$temp_arr = array();
		$v_seq = $data['seq'];

		$this->db->from('otm_com_testcase');
		$this->db->where('otm_com_version_v_seq',$v_seq);
		$this->db->order_by('ct_ord asc');
		$query = $this->db->get();

		foreach ($query->result() as $temp_row)
		{
			$temp_arr['location'] = "";
			$temp_arr['id'] = $temp_row->ct_inp_id;
			$temp_arr['tc_id'] = $temp_row->ct_out_id;
			$temp_arr['pid'] = $temp_row->ct_inp_pid;
			$temp_arr['subject'] = $temp_row->ct_subject;
			$temp_arr['precondition'] = $temp_row->ct_precondition;
			$temp_arr['testdata'] = $temp_row->ct_testdata;
			$temp_arr['procedure'] = $temp_row->ct_procedure;
			$temp_arr['expected_result'] = $temp_row->ct_expected_result;
			$temp_arr['description'] = $temp_row->ct_description;
			$temp_arr['ct_is_task'] = $temp_row->ct_is_task;

			if($temp_arr['pid'] == 'ctc_0'){
				array_push($this->root_array,$temp_arr);
			}

			array_push($this->tmp_array,$temp_arr);
		}

		for($i=0;$i<sizeof($this->root_array);$i++){
			array_push($this->return_array,$this->root_array[$i]);

			$this->putTestCaseItem($this->root_array[$i]['id']);
		}

		$folder_except = array();
		for($i=0;$i<sizeof($this->return_array);$i++){
			if($this->return_array[$i]['ct_is_task'] != "folder"){
				unset($this->return_array[$i]['id']);
				unset($this->return_array[$i]['pid']);
				unset($this->return_array[$i]['ct_is_task']);
				array_push($folder_except,$this->return_array[$i]);
			}
		}
		return $folder_except;
	}


	/**
	* Function import
	*
	* @return array
	*/
	function import($data)
	{
		$worksheet	= $data['import_data'];
		unset($data['import_data']);
		$highestRow	= $worksheet->getHighestRow();

		if($highestRow > 1001){
			$result_data['result'] = FALSE;
			$result_data['msg'] = 'Over Max Row(1000) : '.($highestRow -1);
			return $result_data;
		}

		$this->tmp_location_array = array();
		$ct_out_id = array();
		for ($row = 2; $row <= $highestRow; ++ $row) {
			$location_cell = $worksheet->getCellByColumnAndRow(0, $row);
			$location = $location_cell->getValue();
			$data['location'] = trim($location);
			$this->import_location($data);

			if($data['import_check_id']){
				$ct_id_cell = $worksheet->getCellByColumnAndRow(1, $row);
				$ct_id = $ct_id_cell->getValue();
				array_push($ct_out_id,trim($ct_id));
			}
		}

		if($data['import_check_id']){
			$duplicate_id_array = array();
			$duplicate_seq_array = array();
			$this->db->select('ct_seq, ct_out_id');
			$this->db->from('otm_com_testcase');
			$this->db->where('otm_com_version_v_seq',$data['version']);
			$this->db->where_in('ct_out_id',$ct_out_id);
			$query = $this->db->get();
			if($query->result()){
				foreach ($query->result() as $row)
				{
					array_push($duplicate_id_array,$row->ct_out_id);
					$duplicate_seq_array[$row->ct_out_id] = $row->ct_seq;
				}
			}
		}

		//$per = 0;
		for ($row = 2; $row <= $highestRow; ++ $row) {
			$col_array = array();

			for ($col = 0; $col < 8; ++ $col) {
				$cell = $worksheet->getCellByColumnAndRow($col, $row);
				$val = $cell->getValue();

				switch($col)
				{
					case 0:	$col_id = 'location';
							$val = trim($val);
							break;
					case 1:	$col_id = 'ct_id';
							//$ct_id = trim($val);
							$$val = trim($val);	
							break;
					case 2:	$col_id = 'ct_subject';
							//$subject = trim($val);			
							$val = trim($val);
							break;
					case 3:	$col_id = 'ct_precondition';	break;
					case 4:	$col_id = 'ct_testdata';		break;
					case 5:	$col_id = 'ct_procedure';		break;
					case 6:	$col_id = 'ct_expected_result';	break;
					case 7:	$col_id = 'ct_description';		break;
				}

				$col_array[$col_id] = $val;
			}

			$tmp = $this->tmp_location_array;
			$pid = $tmp[$col_array['location']];

			if (count($duplicate_id_array) > 0 && in_array($col_array['ct_id'], $duplicate_id_array)) {
				//update
				$update_data = array(
					'type' => 'file',
					'v_seq' => $data['version'],
					'pid' => $pid,
					'ct_out_id' => $col_array['ct_id'],
					'seq' => $duplicate_seq_array[$col_array['ct_id']],
					'ct_subject' => $col_array['ct_subject'],
					'ct_precondition' => $col_array['ct_precondition'],
					'ct_testdata' => $col_array['ct_testdata'],
					'ct_procedure' => $col_array['ct_procedure'],
					'ct_expected_result' => $col_array['ct_expected_result'],
					'ct_description' => $col_array['ct_description'],
					'ct_ord' => '1',
					'return_key' => 'seq'
				);

				$this->update_testcase($update_data);
			}else{
				//insert
				$create_data = array(
					'type' => 'file',
					'v_seq' => $data['version'],
					'pid' => $pid,
					'ct_out_id' => $col_array['ct_id'],
					'ct_subject' => $col_array['ct_subject'],
					'ct_precondition' => $col_array['ct_precondition'],
					'ct_testdata' => $col_array['ct_testdata'],
					'ct_procedure' => $col_array['ct_procedure'],
					'ct_expected_result' => $col_array['ct_expected_result'],
					'ct_description' => $col_array['ct_description'],
					'ct_ord' => '1',
					'return_key' => 'seq'
				);

				$this->create_testcase($create_data);
			}
			$str = "<script> top.myUpdateProgress(".round(($row/$highestRow)*100).",'Data Importing...');</script>";
			echo $str;
		}

		$result_data['result'] = TRUE;
		$result_data['msg'] = $highestRow;

		return $result_data;
	}


	/**
	* Function import_location
	*
	* @return array
	*/
	function import_location($data)
	{
		//Insert Suite
		$tmp = $this->tmp_location_array;

		if(isset($tmp[trim($data['location'])]))
		{
			$pid = $tmp[trim($data['location'])];
		}
		else
		{
			$location = explode('/',$data['location']);
			$pid = 'ctc_0';

			for($i=0; $i<count($location); $i++)
			{
				$subject = trim($location[$i]);
				if(($subject === '' OR $subject === NULL)) continue;

				$duplicate['table'] = 'otm_com_testcase';
				$duplicate['key'] = array(
										array('column'=>'otm_com_version_v_seq','value'=>$data['version']),
										array('column'=>'ct_subject','value'=>$subject),
										array('column'=>'ct_inp_pid','value'=>$pid),
										array('column'=>'ct_is_task','value'=>'folder')
									);
				$duplicate['update_key'] = '';
				$duplicate['select_key'] = 'ct_seq';

				$check_value = $this->duplicate_check($duplicate);
				if($check_value){
					$ct_seq = $check_value;
				}else{
					//insert
					$create_data = array(
						'type' => 'suite',
						'v_seq' => $data['version'],
						'pid' => $pid,
						'ct_out_id' => '',
						'ct_subject' => $subject,
						'ct_precondition' => '',
						'ct_testdata' => '',
						'ct_procedure' => '',
						'ct_expected_result' => '',
						'ct_description' => '',
						'ct_ord' => '1',
						'return_key' => 'seq'
					);

					$ct_seq = $this->com_testcase_m->create_testcase($create_data);
				}

				$pid = 'cts_'.$ct_seq;
			}

			$tmp[trim($data['location'])] = $pid;
			$this->tmp_location_array = $tmp;
		}
	}
}
//End of file Com_testcase_m.php
//Location: ./models/Com_testcase_m.php