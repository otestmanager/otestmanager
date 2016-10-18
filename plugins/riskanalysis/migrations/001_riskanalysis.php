<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_riskanalysis extends CI_Migration {

	public function up()
	{
		/**
		* OTM Risk Analysis
		*/
		$str_sql = 'create table if not exists otm_riskitem (
					ri_seq int(11) not null auto_increment,
					otm_project_pr_seq int(11) not null,
					ri_subject varchar(255) not null,
					ri_description text,
					writer varchar(255) not null,
					regdate datetime not null,
					last_writer varchar(255),
					last_update datetime,
					primary key (ri_seq)
			)';
		$this->migration->check_table('otm_riskitem', $str_sql);

		/**
		* OTM RIskitem Custom Value
		*/
		$str_sql = 'create table if not exists otm_riskitem_custom_value (
					rcv_seq int(11) not null auto_increment,
					otm_riskitem_ri_seq int(11) not null,
					otm_project_customform_pc_seq int(11) not null,
					rcv_custom_type varchar(255) not null,
					rcv_custom_value text,
					primary key (rcv_seq)
			)';
		$this->migration->check_table('otm_riskitem_custom_value', $str_sql);


		$str_sql = 'CREATE TABLE IF NOT EXISTS otm_riskfactor (
				rf_seq INT(11) NOT NULL auto_increment,
				otm_project_pr_seq INT(11) NOT NULL,
				rf_subject VARCHAR(255) NULL,
				rf_description TEXT NULL,
				rf_type VARCHAR(20) NULL,
				rf_ord INT(11) NULL,
				writer VARCHAR(255) NULL,
				regdate DATETIME NULL,
				last_writer VARCHAR(255) NULL,
				last_regdate DATETIME NULL,
				PRIMARY KEY (rf_seq)
			)';
		$this->migration->check_table('otm_riskfactor', $str_sql);


		$str_sql = 'CREATE TABLE IF NOT EXISTS otm_riskitem_factor_value (
				rifv_seq INT(11) NOT NULL auto_increment,
				otm_riskitem_ri_seq INT(11) NOT NULL,
				otm_riskfactor_rf_seq INT(11) NOT NULL,
				rifv_value INT(11) NULL,
				regdate DATETIME NULL,
				PRIMARY KEY (rifv_seq)
			)';
		$this->migration->check_table('otm_riskitem_factor_value', $str_sql);


		/*
		*	Mapping Tables
		*/
		$str_sql = 'CREATE TABLE IF NOT EXISTS otm_risk_req_mapping (
				rrl_seq INT(11) NOT NULL auto_increment,
				otm_requirement_req_seq INT(11) NOT NULL,
				otm_riskitem_ri_seq INT NOT NULL,
				PRIMARY KEY (rrl_seq)
			)';
		$this->migration->check_table('otm_risk_req_mapping', $str_sql);


		$str_sql = 'CREATE TABLE IF NOT EXISTS otm_risk_tc_mapping (
				rtm_seq INT(11) NOT NULL auto_increment,
				otm_riskitem_ri_seq INT(11) NOT NULL,
				otm_testcase_tc_seq INT(11) NOT NULL,
				PRIMARY KEY (rtm_seq)
			)';
		$this->migration->check_table('otm_risk_tc_mapping', $str_sql);


		/*
		*	OTM Strategy
		*/

		$str_sql = 'CREATE TABLE IF NOT EXISTS otm_strategy (
				str_seq INT(11) NOT NULL auto_increment,
				otm_project_pr_seq INT(11) NOT NULL,
				riskarea_pco_seq INT(11) NOT NULL,
				testlevel_pco_seq INT(11) NOT NULL,
				str_description TEXT NULL,
				writer VARCHAR(255) NULL,
				regdate DATETIME NULL,
				last_writer VARCHAR(255) NULL,
				last_regdate DATETIME NULL,
				PRIMARY KEY (str_seq)
			)';
		$this->migration->check_table('otm_strategy', $str_sql);



		/**
		* OTM New Table
		*/

	}

	public function down()
	{
		//$this->dbforge->drop_table('table name');
	}
}