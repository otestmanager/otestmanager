<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Group_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Group_m extends CI_Model
{
	public $tmp_array = Array();
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
	}

	function duplicate_check($data){
		$this->db->from($data['table']);

		$this->db->where($data['key']['column'],$data['key']['value']);
		if($data['update_key'] != ''){
			$this->db->where($data['update_key']['column'].' !=',$data['update_key']['value']);
		}
		$query = $this->db->get();
		if($query->result()){
			return 'Duplicate Value : "'.$data['key']['value'].'"';
		}
	}

	/**
	* Function grouplist
	*
	* @return array
	*/
	function grouplist()
	{
		$temp_arr = array();

		$str_sql = '
				select
					a.gr_seq,gr_name,a.regdate,b.mb_name as writer,gr_content,
					b2.mb_name as last_writer,a.last_update,
					(select count(*) from otm_group_member as m,otm_member o where m.otm_member_mb_email=o.mb_email and m.otm_group_gr_seq=a.gr_seq)as gr_user_cnt
				from
				otm_group as a
				left join otm_member as b
				on a.writer=b.mb_email
				left join otm_member as b2
				on a.last_writer=b2.mb_email
		';

		$query = $this->db->query($str_sql);

		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
	}

	/**
	* Function create_group
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function create_group($data)
	{
		$duplicate['table'] = 'otm_group';
		$duplicate['key'] = array('column'=>'gr_name','value'=>$data['gr_name']);
		$duplicate['update_key'] = '';
		if($this->duplicate_check($duplicate)){
			return $this->duplicate_check($duplicate);
		}

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$data['writer'] = $writer;
		$data['regdate'] = $date;
		$data['last_writer'] = '';
		$data['last_update'] = '';

		$this->db->insert('otm_group', $data);
		$result = $this->db->insert_id();
		return 'ok';
	}

	/**
	* Function update_group
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function update_group($data)
	{
		$duplicate['table'] = 'otm_group';
		$duplicate['key'] = array('column'=>'gr_name','value'=>$data['gr_name']);
		$duplicate['update_key'] = array('column'=>'gr_seq','value'=>$data['gr_seq']);

		if($this->duplicate_check($duplicate)){
			return $this->duplicate_check($duplicate);
		}

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$data['last_writer'] = $writer;
		$data['last_update'] = $date;

		$this->db->where('gr_seq', $data['gr_seq']);
		$this->db->update('otm_group', $data);
		return 'ok';
	}

	/**
	* Function delete_group
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function delete_group($data)
	{
		$this->db->where('gr_seq', $data['gr_seq']);
		$this->db->delete('otm_group');

		$this->db->where('otm_group_gr_seq', $data['gr_seq']);
		$this->db->delete('otm_group_member');

		return 'ok';
	}

	/**
	* Function group_userlist
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function group_userlist($data)
	{
		$temp_arr = array();

		$this->db->select('otm_member.*');
		$this->db->from('otm_group_member');
		$this->db->join('otm_member', 'otm_group_member.otm_member_mb_email = otm_member.mb_email');
		$this->db->where('otm_group_gr_seq', $data['gr_seq']);
		$query = $this->db->get();

		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
	}

	/**
	* Function insert_group_user
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function insert_group_user($data)
	{
		if($data['userlist'] === 'all'){
			$query = $this->db->get('otm_member');
			foreach ($query->result() as $temp_row)
			{
				$this->db->where('otm_group_gr_seq', $data['gr_seq']);
				$this->db->where('otm_member_mb_email', $temp_row->mb_email);
				$query = $this->db->get('otm_group_member');
				$result = $query->result();
				if( ! $result){
					$new_data['otm_group_gr_seq'] = $data['gr_seq'];
					$new_data['otm_member_mb_email'] = $temp_row->mb_email;
					$this->db->insert('otm_group_member', $new_data);
				}
			}
		}
		else{
			for($i=0; $i<count($data['userlist']); $i++){

				$this->db->where('otm_group_gr_seq', $data['gr_seq']);
				$this->db->where('otm_member_mb_email', $data['userlist'][$i]);
				$query = $this->db->get('otm_group_member');
				$result = $query->result();
				if( ! $result){
					$new_data['otm_group_gr_seq'] = $data['gr_seq'];
					$new_data['otm_member_mb_email'] = $data['userlist'][$i];
					$this->db->insert('otm_group_member', $new_data);
				}
			}
		}
		$result = $this->db->insert_id();
		return 'ok';
	}

	/**
	* Function delete_group_user
	*
	* @param array $data Post Data.
	*
	* @return string
	*/
	function delete_group_user($data)
	{
		for($i=0; $i<count($data['userlist']); $i++){
			$where_data['otm_group_gr_seq'] = $data['gr_seq'];
			$where_data['otm_member_mb_email'] = $data['userlist'][$i];

			$this->db->delete('otm_group_member', $where_data);
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
				case 'group_list_export':
					return $this->group_list_export($data);
					break;
			}
		}
	}

	/**
	* Function group_list_export
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function group_list_export($data)
	{
		$temp_arr = array();
		$str_sql = "
		select
			g.*,
			group_concat(a.mb_name) as all_mb_name,
			group_concat(a.mb_email) as all_mb_email
		from
		otm_group as g
		left outer join

		(
			select * from otm_member m,otm_group_member gm where m.mb_email=gm.otm_member_mb_email
		) as a
		on
		g.gr_seq=a.otm_group_gr_seq
		group by g.gr_seq
		";
		$query = $this->db->query($str_sql);

		foreach ($query->result() as $temp_row)
		{
			$temp_arr['gr_name'] = $temp_row->gr_name;

			$temp_arr['member_name'] = $temp_row->all_mb_name;
			$temp_arr['member_email'] = $temp_row->all_mb_email;

			$temp_arr['content'] = $temp_row->gr_content;
			$temp_arr['writer'] = $temp_row->writer;
			$temp_arr['regdate'] = $temp_row->regdate;
			$temp_arr['last_writer'] = $temp_row->last_writer;
			$temp_arr['last_update'] = $temp_row->last_update;

			array_push($this->tmp_array,$temp_arr);
		}
		return $this->tmp_array;
	}
}
//End of file Group_m.php
//Location: ./models/Group_m.php