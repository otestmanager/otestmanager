<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_requirement extends CI_Migration {
	
	public function up()
	{
		/**
		* OTM New Table
		*/	

		/**
		* OTM Requirement
		* req_priority		: 중요도
		* req_difficulty	: 난이도
		* req_accept		: 수용여부
		* req_assign		: 담당자
		*/
		$str_sql = 'create table if not exists otm_requirement (
			req_seq int(11) not null auto_increment,
			otm_project_pr_seq int(11) not null default 0,
			req_subject varchar(255) not null,
			req_description text,
			req_priority varchar(255),
			req_difficulty varchar(255),
			req_accept varchar(255),
			req_assign varchar(255),
			writer varchar(255),
			regdate datetime,
			last_writer varchar(255),
			last_update datetime,
			primary key (req_seq)
		)';
		$this->migration->check_table('otm_requirement', $str_sql);


		/**
		* OTM Requirement : Custom value		
		*/
		$str_sql = 'create table if not exists otm_requirement_custom_value(
			reqcv_seq int(11) not null auto_increment,
			otm_requirement_req_seq int(11) not null,
			otm_project_customform_pc_seq int(11) not null,
			reqcv_custom_type varchar(255) not null,
			reqcv_custom_value text,
			primary key (reqcv_seq)
		)';
		$this->migration->check_table('otm_requirement_custom_value', $str_sql);


		/**
		* OTM Requirement : History
		*/
		$str_sql = 'create table if not exists otm_requirement_historys(
			reqh_seq int(11) not null auto_increment,
			otm_project_pr_seq int(11) not null default 0,
			otm_requirement_req_seq int(11) not null,
			writer varchar(255) not null,
			regdate datetime not null,
			primary key (reqh_seq)
		)';
		$this->migration->check_table('otm_requirement_historys', $str_sql);


		$str_sql = 'create table if not exists otm_requirement_history_details(
			reqhd_seq int(11) not null auto_increment,
			otm_requirement_historys_reqh_seq int(11) not null,
			action_type varchar(30) not null,
			old_value	text,
			value		text,
			primary key (reqhd_seq)
		)';
		$this->migration->check_table('otm_requirement_historys_details', $str_sql);

	}

	public function down()
	{
		//$this->dbforge->drop_table('table_name');
	}
	
}