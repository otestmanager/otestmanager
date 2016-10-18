<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_storage extends CI_Migration {

	public function up()
	{
		/**	Users Table
		*	Add Column Device
		*/
		$fields = array(
			'otm_project_storage_ops_seq' => array('type' => 'int(11)')
		);
		$this->dbforge->add_column('otm_file', $fields);

		/**
		* OTM New Table
		*/
		$str_sql = 'create table if not exists otm_project_storage (
					ops_seq int(11) not null auto_increment,
					otm_project_pr_seq int(11) not null,
					ops_subject varchar(255) not null,
					ops_pid varchar(255) not null default "root",
					ops_ord int(11) not null default 0,
					ops_data_trach char(1) default "n",
					writer varchar(60) not null,
					regdate datetime not null,
					last_writer varchar(60),
					last_update datetime,
					primary key (ops_seq)
			)';
		$this->migration->check_table('otm_project_storage', $str_sql);

		$str_sql = 'create table if not exists otm_project_storage_permission (
					psp_seq int(11) not null auto_increment,
					otm_project_pr_seq int(11) not null,
					otm_project_storage_ops_seq int(11) not null,
					otm_role_rp_seq int(11) not null,
					psp_read char(1) default "",
					psp_write char(1) default "",
					psp_delete char(1) default "",
					primary key (psp_seq)
			)';
		$this->migration->check_table('otm_project_storage_permission', $str_sql);
	}

	public function down()
	{
		//$this->dbforge->drop_table('');
	}
}

/* End of file 001_Storage.php */
/* Location: ./migratioins/001_Storage.php */