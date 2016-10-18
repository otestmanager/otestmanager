<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class User_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class User_m extends CI_Model
{
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
	}

	function duplicate_check($data){
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
			return '동일한 "'.$data['key'][0]['value'].'" 이(가) 있습니다.';
		}
	}

	/**
	* Function userlist
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function userlist($data)
	{
		$temp_arr = array();
		$pr_seq = (isset($data['pr_seq']))?$data['pr_seq']:null;
		$start = (isset($data['start']))?$data['start']:null;
		$limit = (isset($data['limit']))?$data['limit']:null;
		if($start != null && $limit != null){
			$limitSql = " limit $limit OFFSET $start ";
		}else{
			$limitSql = "";
		}

		$project_member_join_quy = "";
		$project_member_where_quy = "";
		if($pr_seq){
			$project_member_join_quy = "
				left join
					(select * from otm_project_member where otm_project_pr_seq='$pr_seq') as b
					on a.mb_email = b.otm_member_mb_email
			";
			$project_member_where_quy = " b.otm_member_mb_email is null	";
		}
		$status_quy = '';
		if($data['user_search_status']){
			$status_quy = ' where mb_is_approved=\''.$data['user_search_status'].'\'';
		}

		if($project_member_where_quy != ''){
			if($project_member_where_quy != ''){
				$status_quy .= " where $project_member_where_quy";
			}else{
				$status_quy .= " and $project_member_where_quy";
			}
		}


		if($data['user_search_searchfield'] && $data['user_search_searchtext']){
			if($status_quy){
				$where_quy = ' and '.$data['user_search_searchfield'].' like \'%'.$data['user_search_searchtext'].'%\' ';
			}else{
				$where_quy = ' where '.$data['user_search_searchfield'].' like \'%'.$data['user_search_searchtext'].'%\' ';
			}

			if($data['user_search_status']){
				$where_quy .= " and mb_is_approved='".$data['user_search_status']."'";
			}
			if($pr_seq){
				$where_quy .= " and $project_member_where_quy";
			}

			$str_sql = "
				select
					a.*,g.user_group_name
				from
				otm_member as a
				left outer join
				(
					select
						group_concat(a.gr_name) as user_group_name,
						b.otm_member_mb_email
					from
					otm_group as a, otm_group_member as b
					where a.gr_seq=b.otm_group_gr_seq
					group by b.otm_member_mb_email
				) as g
				on
				a.mb_email=g.otm_member_mb_email
				$project_member_join_quy
				$status_quy
				$where_quy
			";

		}elseif($data['user_search_group']){
			$gr_seq = $data['user_search_group'];
			$str_sql = "
				select
					a.*,g.user_group_name from
				(
					select * from otm_member as a,otm_group_member as b
					where a.mb_email=b.otm_member_mb_email and b.otm_group_gr_seq='$gr_seq'
				) as a
				left outer join
				(
					select
						group_concat(a.gr_name) as user_group_name,
						gr_seq,
						b.otm_member_mb_email
					from
					otm_group as a, otm_group_member as b
					where a.gr_seq=b.otm_group_gr_seq
					group by b.otm_member_mb_email
				) as g
				on
				a.mb_email=g.otm_member_mb_email
				$project_member_join_quy
				$status_quy";
		}else{
			$str_sql = "
				select
					a.*,g.user_group_name
				from
				otm_member as a
				left outer join
				(
					select
						group_concat(a.gr_name) as user_group_name,
						b.otm_member_mb_email
					from
					otm_group as a, otm_group_member as b
					where a.gr_seq=b.otm_group_gr_seq
					group by b.otm_member_mb_email
				) as g
				on
				a.mb_email=g.otm_member_mb_email
				$project_member_join_quy
				$status_quy ";
		}

		$str_sql_cnt = "select count(*) as cnt from ($str_sql) as a";
		$query = $this->db->query($str_sql_cnt);
		$cnt_result = $query->result();


		$str_sql = "select * from ($str_sql) as a order by mb_name asc $limitSql";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $temp_row)
		{
			$temp_row->mb_pw = '';
			$temp_arr[] = $temp_row;
		}

		return "{success:true,totalCount: ".$cnt_result[0]->cnt.", data:".json_encode($temp_arr)."}";
		exit;
	}

	/**
	* Function user_view
	*
	* @return array
	*/
	function user_view()
	{
		$mb_email = $this->session->userdata('mb_email');

		$temp_arr = array();
		$str_sql = "select mb_name,mb_tel,mb_memo from otm_member where mb_email='$mb_email'";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $temp_row)
		{
			$temp_arr[] = $temp_row;
		}
		return $temp_arr;
		exit;
	}

	/**
	* Function create_user
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function create_user($data)
	{

		$duplicate['table'] = 'otm_member';
		$duplicate['key'] = array(
								array('column'=>'mb_email','value'=>$data['mb_email'])
							);
		$duplicate['update_key'] = '';
		$check_value = $this->duplicate_check($duplicate);
		if($check_value){
			return $check_value;
		}

		$date=date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$str_sql = "insert into otm_member(mb_email,mb_name,mb_pw,mb_is_admin,mb_is_approved,mb_memo,writer,regdate,mb_tel) values('".$data['mb_email']."','".$data['mb_name']."',password('".$data['mb_pw']."'),'".$data['mb_is_admin']."','".$data['mb_is_approved']."','".$data['mb_memo']."','".$writer."','".$date."','".$data['mb_tel']."')";
		$this->db->query($str_sql);
		return 'ok';

		$data['writer'] = $writer;
		$data['regdate'] = $date;
		$data['last_writer'] = $writer;
		$data['last_update'] = $date;

		$this->db->insert('otm_member',$data);
		$result = $this->db->insert_id();
		return 'ok';
	}

	/**
	* Function update_user
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function update_user($data)
	{
		$date = date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$pw_sql = '';
		if($data['mb_pw']){
			$pw_sql = " mb_pw = password('".$data['mb_pw']."'),";
		}
		$str_sql = "update otm_member set
				mb_name = '".$data['mb_name']."',
				".$pw_sql."
				mb_is_admin ='".$data['mb_is_admin']."',
				mb_is_approved ='".$data['mb_is_approved']."',
				mb_memo ='".$data['mb_memo']."',
				mb_tel = '".$data['mb_tel']."',
				last_writer ='".$writer."',
				last_update ='".$date."'
			where
				mb_email='".$data['mb_email']."'
		";
		$this->db->query($str_sql);

		return 'ok';
	}

	/**
	* Function register_update
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function register_update($data)
	{
		$date = date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$pw_sql = '';
		if($data['mb_pw']){
			$pw_sql = " mb_pw = password('".$data['mb_pw']."'),";
		}
		$str_sql = "update otm_member set
				mb_name = '".$data['mb_name']."',
				".$pw_sql."
				mb_memo ='".$data['mb_memo']."',
				mb_tel = '".$data['mb_tel']."',
				last_writer ='".$writer."',
				last_update ='".$date."'
			where
				mb_email='".$data['mb_email']."'
		";
		$this->db->query($str_sql);

		$newdata = array(
			'mb_name'	=> $data['mb_name']
		);
		$this->session->set_userdata($newdata);

		return "{success:true}";
	}

	/**
	* Function language_update
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function language_update($data)
	{
		$date = date('Y-m-d H:i:s');
		$writer = $this->session->userdata('mb_email');

		$str_sql = "update otm_member set
				mb_lang = '".$data['mb_lang']."',
				last_writer ='".$writer."',
				last_update ='".$date."'
			where
				mb_email='".$writer."'
		";
		$this->db->query($str_sql);

		return true;
	}

	/**
	* Function delete_user
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function delete_user($data)
	{
		$this->db->where('mb_email', $data['mb_email']);
		$this->db->delete('otm_member');
		return 'ok';
	}
}
//End of file User_m.php
//Location: ./models/User_m.php