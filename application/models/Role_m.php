<?php if( ! defined('BASEPATH') )exit('No direct script access allowed');
/**
 * Class Role_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Role_m extends CI_Model
{
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
	}

	/**
	* Function role list
	*
	* @return array
	*/
	function role_list()
	{
		$temp_arr = array();

		$this->db->select('rp_seq,rp_name,otm_role.regdate,otm_member.mb_name as writer');
		$this->db->from('otm_role');
		$this->db->order_by('rp_seq asc');
		$this->db->join('otm_member', 'otm_member.mb_email=otm_role.writer');

		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
	}

	/**
	* Function get permission
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function get_permission($data)
	{
		$rp_seq = $data['rp_seq'];

		$temp_arr = array();

		$this->db->select('pmi_category,pmi_name,pmi_value');
		$this->db->from('otm_role_permission');
		$this->db->order_by('pmi_seq asc');
		$this->db->where('otm_role_rp_seq', $rp_seq);

		$query = $this->db->get();
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
	}

	/**
	* Function create role
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_role($data)
	{
		$role_name = $data['rp_name'];
		$date = date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$str_sql_cnt = "select count(*) as cnt from otm_role where rp_name='$role_name'";
		$query = $this->db->query($str_sql_cnt);
		$cnt_result = $query->result();
		if($cnt_result[0]->cnt > 0){
			return "Role Duplication";
		}else{
			$this->db->set('rp_name', $role_name);
			$this->db->set('writer', $writer);
			$this->db->set('regdate', $date);
			$this->db->set('last_writer', $writer);
			$this->db->set('last_update', $date);

			$this->db->insert('otm_role');
			$result_role = $this->db->insert_id();

			$permission_info = json_decode($data['permission']);

			for($i=0;$i<count($permission_info);$i++){
				$category	= trim($permission_info[$i]->category);
				$pmi_name	= trim($permission_info[$i]->id);
				$pmi_value	= trim($permission_info[$i]->value);

				$this->db->set('otm_role_rp_seq', $result_role);
				$this->db->set('pmi_category', $category);
				$this->db->set('pmi_name', $pmi_name);
				$this->db->set('pmi_value', $pmi_value);

				$this->db->insert('otm_role_permission');
			}

			return "ok";
		}
	}

	/**
	* Function udpate role
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_role($data)
	{
		$rp_seq = $data['rp_seq'];
		$rp_name = $data['rp_name'];

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$str_sql_cnt = "select count(*) as cnt from otm_role where rp_name='$rp_name' and rp_seq!='$rp_seq'";
		$query = $this->db->query($str_sql_cnt);
		$cnt_result = $query->result();
		if($cnt_result[0]->cnt > 0){
			return "Role Duplication";
		}else{

			$modify_array = array(
				'rp_name'			=> $rp_name,
				'last_writer'		=> $writer,
				'last_update'		=> $date
			);
			$where = array('rp_seq'=>$rp_seq);
			$this->db->update('otm_role', $modify_array, $where);


			$permission_info = json_decode($data['permission']);
			for($i=0;$i<count($permission_info);$i++){
				$modify_permission_array = array(
					'pmi_value'		=> $permission_info[$i]->value
				);
				$permission_where = array('otm_role_rp_seq'=>$rp_seq,'pmi_name'=>$permission_info[$i]->id);
				$this->db->update('otm_role_permission', $modify_permission_array, $permission_where);
			}
			return "ok";
		}
	}

	/**
	* Function delete role
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_role($data)
	{
		$rp_seq = $data['rp_seq'];
		$delete_array = array(
			'rp_seq' => $rp_seq
		);
		$result = $this->db->delete('otm_role', $delete_array);


		$delete_array = array(
			'otm_role_rp_seq' => $rp_seq
		);
		$result = $this->db->delete('otm_role_permission', $delete_array);

		return $result;
	}

}
//End of file Role_m.php
//Location: ./models/Role_m.php