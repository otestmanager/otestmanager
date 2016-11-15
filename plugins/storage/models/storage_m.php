<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Storage_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Storage_m extends CI_Model {

	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->load->library('File_Form');

		$this->tmp_array = Array();
		$this->root_array = Array();
		$this->return_array = Array();
		$this->location_path = Array();
	}

	/**
	* Function Permission Check
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function permission_Check($data)
	{
		/*
			Permission Check
		*/
		$mb_email = $this->session->userdata('mb_email');

		$auth_read_array = array();
		$auth_wd_array = array();

		$this->db->from('otm_project_member as pm');
		$this->db->join('otm_project_member_role as pmr','otm_project_member_pm_seq=pm.pm_seq','left');
		$this->db->join('otm_project_storage_permission as psp','psp.otm_role_rp_seq=pmr.otm_role_rp_seq','left');
		$this->db->where('pm.otm_project_pr_seq',$data['pr_seq']);
		$this->db->where('psp.otm_project_pr_seq',$data['pr_seq']);
		$this->db->where('pm.otm_member_mb_email',$mb_email);
		$this->db->where('psp.psp_read','1');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			if (in_array($row->otm_project_storage_ops_seq, $auth_read_array)) {
			}else{
				array_push($auth_read_array,$row->otm_project_storage_ops_seq);
			}

			if(!$auth_wd_array[$row->otm_project_storage_ops_seq]['psp_write']){
				$auth_wd_array[$row->otm_project_storage_ops_seq]['psp_write'] = $row->psp_write;
			}
			if(!$auth_wd_array[$row->otm_project_storage_ops_seq]['psp_delete']){
				$auth_wd_array[$row->otm_project_storage_ops_seq]['psp_delete'] = $row->psp_delete;
			}
		}

		$temp_arry = array();
		array_push($temp_arry, $auth_read_array);
		array_push($temp_arry, $auth_wd_array);
		return $temp_arry;
	}


	/**
	* Function plan_tree_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function storage_tree_list($data)
	{
		$mb_email = $this->session->userdata('mb_email');
		/*
			Permission Check
		*/

		$permission = $this->permission_Check($data);

		$auth_read_array = $permission[0];
		$auth_wd_array = $permission[1];

		$return_arr = array();
		$this->db->from('otm_project_storage');
		$this->db->where('otm_project_pr_seq',$data['pr_seq']);
		$this->db->where('ops_pid',$data['node']);
		$this->db->where('ops_data_trach','n');

		$this->db->order_by('ABS(ops_ord)','asc');
		$this->db->order_by('ABS(ops_seq)','asc');
		$query = $this->db->get();

		foreach ($query->result() as $row)
		{
			if (in_array($row->ops_seq, $auth_read_array)) {
			}else{
				if($mb_email == $row->writer || $this->session->userdata('mb_is_admin') === 'Y'){
				}else{
					continue;
				}
			}
			$temp_arr = array();
			$temp_arr['expanded'] = false;
			$temp_arr['leaf'] = false;
			$temp_arr['pr_seq'] = $row->otm_project_pr_seq;
			$temp_arr['ops_seq'] = $row->ops_seq;
			$temp_arr['id'] = $row->ops_seq;
			$temp_arr['text'] = $row->ops_subject;
			$temp_arr['ops_ord'] = $row->ops_ord;
			$temp_arr['pid'] = $row->ops_pid;
			$temp_arr['writer'] = $row->writer;
			$temp_arr['iconCls'] = "x-tree-icon-parent";
			$temp_arr['psp_write'] = $auth_wd_array[$row->ops_seq]['psp_write'];
			$temp_arr['psp_delete'] = $auth_wd_array[$row->ops_seq]['psp_delete'];

			array_push($return_arr, $temp_arr);
		}

		return $return_arr;
	}

	/**
	* Function create_folder
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_folder($data)
	{
		$pr_seq	= $data['pr_seq'];
		$pid	= $data['node'];
		$ops_subject	= $data['ops_subject'];

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$ops_ord = $this->get_ord_maxval("otm_project_storage",$pr_seq,$pid);

		$this->db->set('otm_project_pr_seq', $pr_seq);
		$this->db->set('ops_subject', $ops_subject);
		$this->db->set('ops_pid', $pid);
		$this->db->set('ops_ord', $ops_ord);

		$this->db->set('writer', $writer);
		$this->db->set('regdate', $date);

		$this->db->insert('otm_project_storage');
		$ops_seq = $this->db->insert_id();

		$permitions	= $data['permitions'];
		for($i=0; $i<count($permitions); $i++){
			$rp_seq = $permitions[$i]->rp_seq;
			$psp_read = (bool)$permitions[$i]->psp_read;
			$psp_write = (bool)$permitions[$i]->psp_write;
			$psp_delete = (bool)$permitions[$i]->psp_delete;

			if($psp_read || $psp_write || $psp_delete){
				$this->db->set('otm_project_pr_seq', $pr_seq);
				$this->db->set('otm_project_storage_ops_seq', $ops_seq);
				$this->db->set('otm_role_rp_seq', $rp_seq);
				$this->db->set('psp_read', ($psp_read)?'1':'');
				$this->db->set('psp_write', ($psp_write)?'1':'');
				$this->db->set('psp_delete', ($psp_delete)?'1':'');

				$this->db->insert('otm_project_storage_permission');
			}
		}

		$permission = $this->permission_Check($data);

		//$auth_read_array = $permission[0];
		$auth_wd_array = $permission[1];

		$temp_arr['psp_write'] = $auth_wd_array[$ops_seq]['psp_write'];
		$temp_arr['psp_delete'] = $auth_wd_array[$ops_seq]['psp_delete'];

		return "{success:true, ops_seq:'".$ops_seq."', permission:'".json_encode($temp_arr)."'}";
	}


	/**
	* Function update_folder
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_folder($data)
	{
		$pr_seq	= $data['pr_seq'];
		$ops_seq	= $data['node'];
		$ops_subject	= $data['ops_subject'];

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$new_data = array(
			'ops_subject'	=> $ops_subject,
			'last_writer'	=> $writer,
			'last_update'	=> $date
		);

		$this->db->where('ops_seq', $ops_seq);
		$this->db->update('otm_project_storage', $new_data);

		$permitions	= $data['permitions'];
		for($i=0; $i<count($permitions); $i++){
			$rp_seq = $permitions[$i]->rp_seq;
			$psp_read = (bool)$permitions[$i]->psp_read;
			$psp_write = (bool)$permitions[$i]->psp_write;
			$psp_delete = (bool)$permitions[$i]->psp_delete;

			$this->db->where('otm_project_pr_seq', $pr_seq);
			$this->db->where('otm_project_storage_ops_seq', $ops_seq);
			$this->db->where('otm_role_rp_seq', $rp_seq);
			$q = $this->db->get('otm_project_storage_permission');
			if ( $q->num_rows() > 0 )  {
				$new_data = array(
					'psp_read'	=> ($psp_read)?'1':'',
					'psp_write'	=> ($psp_write)?'1':'',
					'psp_delete'=> ($psp_delete)?'1':''
				);

				$this->db->where('otm_project_pr_seq', $pr_seq);
				$this->db->where('otm_project_storage_ops_seq', $ops_seq);
				$this->db->where('otm_role_rp_seq', $rp_seq);

				$this->db->update('otm_project_storage_permission', $new_data);
			}else{
				if($psp_read || $psp_write || $psp_delete){
					$this->db->set('otm_project_pr_seq', $pr_seq);
					$this->db->set('otm_project_storage_ops_seq', $ops_seq);
					$this->db->set('otm_role_rp_seq', $rp_seq);
					$this->db->set('psp_read', ($psp_read)?'1':'');
					$this->db->set('psp_write', ($psp_write)?'1':'');
					$this->db->set('psp_delete', ($psp_delete)?'1':'');

					$this->db->insert('otm_project_storage_permission');
				}
			}
		}

		$permission = $this->permission_Check($data);

		$auth_wd_array = $permission[1];

		$temp_arr['psp_write'] = $auth_wd_array[$ops_seq]['psp_write'];
		$temp_arr['psp_delete'] = $auth_wd_array[$ops_seq]['psp_delete'];

		return "{success:true, ops_seq:'".$ops_seq."', permission:'".json_encode($temp_arr)."'}";
	}


	/**
	* Function delete_folder
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_folder($data)
	{
		//$pr_seq	= $data['pr_seq'];
		$node	= $data['node'];

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$new_data = array(
			'ops_data_trach'	=> 'y',
			'last_writer'	=> $writer,
			'last_update'	=> $date
		);

		$this->db->where('ops_seq', $node);
		$this->db->update('otm_project_storage', $new_data);

		/*
			관련 데이터 삭제
		*/
		$children = $this->storage_tree_list($data);
		for($i=0; $i<count($children); $i++){
			$find_data = $data;
			$find_data['node'] = $children[$i]['id'];
			$this->delete_folder($find_data);
		}

		return "{success:true, node:'".$node."'}";
		/*
			해당 폴더 및 하위 폴더와 파일들 모두 삭제해야 할 경우
		*/
		$delete_array = array(
			'ops_seq' => $node
		);
		$result = $this->db->delete('otm_project_storage', $delete_array);
		return "{success:true, node:'".$result."'}";
	}

	/**
	* Function get_ord_maxval
	*
	* @return double
	*/
	private function get_ord_maxval($table,$pr_seq,$pid)
	{
		$str_sql = "select max(ops_ord) as max_ord from $table where otm_project_pr_seq='$pr_seq' and ops_pid='$pid'";

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
	* Function delete_file
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_file($data)
	{
		$category = 'ID_STORAGE';
		$pr_seq = $data['pr_seq'];
		$target_seq = $data['target_seq'];
		$of_no = $data['of_no'];

		$str_quy = "delete from otm_file where otm_category='$category' and otm_project_pr_seq='$pr_seq' and target_seq='$target_seq' and of_no in ($of_no)";
		$this->db->query($str_quy);

		print "{success:true,msg:'ok'}";
	}


	/**
	* Function move_folder
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function move_folder($data)
	{
		if(isset($data['target_id']) && $data['target_id'] === 'root') $data['target_id'] = 'root';

		$pr_seq		= $data['pr_seq'];
		$target_id = $data['target_id'];
		$position = $data['position'];

		if($position == "before" || $position == "after"){
			$select_size = count($data['select_id']);
			$modify_array['ops_pid'] = $data['target_id'];

			$str_sql = "select * from otm_project_storage where otm_project_pr_seq='$pr_seq' and ops_seq='$target_id'";
			$query = $this->db->query($str_sql);
			$result = $query->result();
			$ops_pid = $result[0]->ops_pid;
			$pr_seq = $result[0]->otm_project_pr_seq;
			$ops_ord = $result[0]->ops_ord;

			$modify_array['ops_pid'] = $ops_pid;

			if($position == "before"){
				$str_sql = "update otm_project_storage set ops_ord=ops_ord+$select_size where ops_pid='$ops_pid' and otm_project_pr_seq='$pr_seq' and ops_ord>='$ops_ord'";
			}else if($position=="after"){
				$str_sql = "update otm_project_storage set ops_ord=ops_ord+$select_size where ops_pid='$ops_pid' and otm_project_pr_seq='$pr_seq' and ops_ord>'$ops_ord'";
				$ops_ord++;
			}
			$query = $this->db->query($str_sql);

		}else{
			$modify_array['ops_pid'] = $data['target_id'];
		}

		for($i=0; $i<count($data['select_id']); $i++){
			$select_id = $data['select_id'][$i];

			$str_sql = "select * from otm_project_storage where otm_project_pr_seq='$pr_seq' and ops_seq='$select_id'";
			$query = $this->db->query($str_sql);
			$result = $query->result();
			$ops_pid = $result[0]->ops_pid;
			$pr_seq = $result[0]->otm_project_pr_seq;

			if($position == "before" || $position == "after"){
				$ops_ord += $i;
			}else{
				$ops_ord = $this->get_ord_maxval('otm_project_storage',$pr_seq,$data['target_id']);
			}

			$modify_array['ops_ord'] = $ops_ord;

			$where = array('ops_seq'=>$data['select_id'][$i]);
			$this->db->update('otm_project_storage', $modify_array, $where);
		}

		return 'ok';
	}


	/**
	* Function move_file
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function move_file($data)
	{
		$pr_seq		= $data['pr_seq'];
		$target_seq = $data['target_seq'];

		$modify_array['target_seq'] = $target_seq;

		for($i=0; $i<count($data['select_id']); $i++){
			$select_id = $data['select_id'][$i];

			/*
				$select_id->target_seq
				$select_id->of_no

				위의 파일을 업데이트

				$select_id->target_seq 을 $target_seq 으로
				$select_id->of_no은 $target_seq의 max of_no으로
			*/

			$str_sql = "select max(of_no) as max_ord from otm_file where otm_project_pr_seq='$pr_seq' and target_seq='$target_seq'";
			$query = $this->db->query($str_sql);
			$max_result = $query->result();
			if($max_result[0]->max_ord >= 1){
				$max_num = $max_result[0]->max_ord;
				$max_num++;
			}else{
				$max_num = 1;
			}

			$of_no = $max_num;

			$modify_array['target_seq'] = $target_seq;
			$modify_array['of_no'] = $of_no;

			$where = array('target_seq'=>$select_id->target_seq,'of_no'=>$select_id->of_no);
			$this->db->update('otm_file', $modify_array, $where);
		}

		return "{success:true, target_seq:'$target_seq'}";
	}

	/**
	* Function file_upload
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function file_upload($data)
	{
		$pr_seq = $data['pr_seq'];
		$target_seq = ($data['node'] == 'root')?0:$data['node'];

		if(isset($_FILES['form_file']))
		{
			for($i=0;$i<sizeof($_FILES['form_file']['name']);$i++){
				if($_FILES['form_file']['name'][$i]){
					$file_data = array();
					$file_data['source']['name'] = $_FILES['form_file']['name'][$i];
					$file_data['source']['tmp_name'] = $_FILES['form_file']['tmp_name'][$i];
					$file_data['source']['size'] = $_FILES['form_file']['size'][$i];
					$file_data['source']['type'] = $_FILES['form_file']['type'][$i];

					$file_data['category'] = 'ID_STORAGE';
					$file_data['pr_seq'] = $pr_seq;
					$file_data['target_seq'] = $target_seq;
					$file_data['otm_project_storage_ops_seq'] = $target_seq;
					$file_data['of_no'] = $i;

					$this->File_Form->file_upload($file_data);
				}
			}
		}

		return "{success:true, data:'ok'}";
	}

	/**
	* Function storage_tree_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function storage_list($data)
	{
		$pr_seq = $data['pr_seq'];
		$node = (!$data['node'] || $data['node'] == 'root')?0:$data['node'];
		$start = $data['start'];
		$limit = $data['limit'];
		if($start != null && $limit != null){
			$limitSql = " limit $limit OFFSET $start ";
		}else{
			$limitSql = "";
		}

		/**
			Get Project Member
		*/
		$member_arr = array();
		$this->db->from('otm_member');
		$query = $this->db->get();
		foreach ($query->result() as $row)
		{
			$member_arr[$row->mb_email] = $row->mb_name;
		}

		$this->db->start_cache();
		$this->db->from('otm_file as a');
		$this->db->where('a.otm_project_pr_seq',$pr_seq);
		$this->db->where('a.otm_category','ID_STORAGE');
		$this->db->where('a.target_seq',$node);
		$this->db->stop_cache();

		$cnt_result = $this->db->count_all_results();

		//$order_by_sql = " order by a.of_no desc ";
		$sort = $data['sort'][0];

		if($sort){
			//$order_by_sql = " order by ";
			foreach($sort as $row => $v){
				//$order_by_sql .= $v.' ';
				$this->db->order_by($v,'');
			}
		}else{
			$this->db->order_by('a.of_no','desc');
		}
		if($limitSql != ""){
			$this->db->limit($limit,$start);
		}
		$query = $this->db->get();

		foreach ($query->result() as $row)
		{
			$row->regdate = substr($row->regdate, 0, 10);
			$arr[] = $row;
		}
		return "{success:true,totalCount: ".$cnt_result.", data:".json_encode($arr)."}";
	}


	/**
	* Function storage_permissioin_list
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function storage_permissioin_list($data)
	{
		$pr_seq = $data['pr_seq'];
		$node = (!$data['node'] || $data['node'] == 'root')?0:$data['node'];
		$action = $data['action']; //add or update

		$temp_arr = array();

		$this->db->from('otm_role as rp');
		$this->db->order_by('rp_seq asc');

		//추가일때와 수정일때 구분
		if($node > 0 && $action == 'storage_update'){
			$this->db->join('otm_project_storage_permission as psp', "psp.otm_role_rp_seq=rp.rp_seq and psp.otm_project_pr_seq='$pr_seq' and psp.otm_project_storage_ops_seq = '$node'",'left');

			$this->db->select("rp.rp_seq, rp.rp_name, psp.psp_read, psp.psp_write, psp.psp_delete");
		}else{
			$this->db->select("rp.rp_seq, rp.rp_name, '1' as psp_read, '1' as psp_write, '1' as psp_delete");
		}

		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}

		return $temp_arr;
	}
}
//End of file storage_m.php
//Location: ./models/storage_m.php