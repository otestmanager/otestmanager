<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Login_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Login_m extends CI_Model
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
	* Function check_table
	*
	* @return none
	*/
	function check_table()
	{
		$str_sql = "show tables like 'ci_sessions'";
		$query = $this->db->query($str_sql);
		$result = $query->result();
		if( ! $result){	
			$str_sql = "create table if not exists ci_sessions (
				session_id varchar(40) default '0' not null,
				ip_address varchar(16) default '0' not null,
				user_agent varchar(120) not null,
				last_activity int(10) unsigned default 0 not null,
				user_data text not null,
				primary key (session_id),
				key 'last_activity_idx' ('last_activity')
			)";
			$query = $this->db->query($str_sql);
		}

		$str_sql = "show tables like 'member'";
		$query = $this->db->query($str_sql);
		$result = $query->result();
		if( ! $result){	
			$str_sql = 'create table if not exists member (
				seq int(11) NOT NULL auto_increment,
				email varchar(100) default NULL,
				reg_date datetime default NULL,
				passwd varchar(50) NOT NULL,
				primary key (seq)
			)';
			$query = $this->db->query($str_sql);
		}
	}
	
	/**
	* Function login
	*
	* @param array $data Post Data.
	*
	* @return array
	*/
	function login($data)
	{		
		$arr = "";
		$str_sql = "SELECT mb_email,mb_name,mb_lang,mb_is_admin FROM otm_member where mb_email='".$data['mb_email']."' and mb_pw=password('".$data['mb_pw']."') and mb_is_approved='Y'";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{			
			$arr = $row;
		}	
		return $arr;
	}	
}
//End of file Login_m.php
//Location: ./models/Login_m.php