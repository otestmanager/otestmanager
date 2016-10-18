<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class FileDownload
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class FileDownload extends Controller {

	public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public function index()
	{
	}

	public function file_download_chk(){
		$pr_seq = $this->input->get_post('pr_seq',true);
		$category = $this->input->get_post('category',true);
		$target_seq = $this->input->get_post('target_seq',true);
		$of_no = $this->input->get_post('of_no',true);

		$arr = array();
		$str_sql = "select * from otm_file where otm_category='$category' and otm_project_pr_seq='$pr_seq' and target_seq='$target_seq' and of_no='$of_no'";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}

		$filepath = 'uploads/files/'.$pr_seq."/".$arr[0]->of_file;
		$filepath = addslashes($filepath);
		$filepath = trim(mb_convert_encoding($filepath,"euckr","utf-8"));

		$original = trim(mb_convert_encoding($arr[0]->of_source,"euckr","utf-8"));

		if (file_exists($filepath)) {
			print "{success:true,msg:'ok'}";
		}else{
			print "{success:false,msg:'no_file'}";
		}
	}

	public function file_download()
	{
		$pr_seq = $this->input->get_post('pr_seq',true);
		$category = $this->input->get_post('category',true);
		$target_seq = $this->input->get_post('target_seq',true);
		$of_no = $this->input->get_post('of_no',true);

		$arr = array();
		$str_sql = "select * from otm_file where otm_category='$category' and otm_project_pr_seq='$pr_seq' and target_seq='$target_seq' and of_no='$of_no'";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}

		if(sizeof($arr) == 1){
			$filepath = 'uploads/files/'.$pr_seq."/".$arr[0]->of_file;
			$filepath = addslashes($filepath);
			$filepath = trim(mb_convert_encoding($filepath,"euckr","utf-8"));

			$original = trim(mb_convert_encoding($arr[0]->of_source,"euckr","utf-8"));

			if (file_exists($filepath)) {
				if(eregi("msie", $_SERVER[HTTP_USER_AGENT]) && eregi("5\.5", $_SERVER[HTTP_USER_AGENT])) {
					header("content-type: doesn/matter");
					header("content-length: ".filesize("$filepath"));
					header("content-disposition: attachment; filename=\"$original\"");
					header("content-transfer-encoding: binary");
				} else {
					header("content-type: file/unknown");
					header("content-length: ".filesize("$filepath"));
					header("content-disposition: attachment; filename=\"$original\"");
					header("content-description: php generated data");
				}

				header("pragma: no-cache");
				header("expires: 0");
				flush();
				if (is_file("$filepath")) {
					$fp = fopen("$filepath", "rb");

					while(!feof($fp)) {
						echo fread($fp, 100*1024);
					}
					fclose ($fp);
				}
			}else{
					return "error";
			}
		}else{
			return "error";
		}
	}

	public function file_delete()
	{
		$mb_email = trim($this->session->userdata('mb_email'));
		$mb_is_admin = trim($this->session->userdata('mb_is_admin'));


		$pr_seq = $this->input->get_post('pr_seq',true);
		$category = $this->input->get_post('category',true);
		$target_seq = $this->input->get_post('target_seq',true);
		$of_no = $this->input->get_post('of_no',true);

		if($mb_is_admin == 'Y'){
			$str_quy = "delete from otm_file where otm_category='$category' and otm_project_pr_seq='$pr_seq' and target_seq='$target_seq' and of_no='$of_no'";
			$this->db->query($str_quy);

			print "{success:true,msg:'ok'}";
			exit;
		}else{
			$str_sql = "select count(*) as cnt from otm_project_member where otm_project_pr_seq='$pr_seq' and otm_member_mb_email='$mb_email'";
			$query = $this->db->query($str_sql);
			$tmp_arr="";
			foreach ($query->result() as $row){
				$tmp_arr = $row;
			}
			if($tmp_arr->cnt){
				$category_quy = "";
				switch($category){
					case "ID_DEFECT":
						$pmi_name_default_type = "defect_delete";
						$pmi_name_all_type = "defect_delete_all";
						$category_quy = "(d.pmi_name='defect_delete' or d.pmi_name='defect_delete_all') and";
					break;
					case "ID_TC":
						$pmi_name_default_type = "tc_delete";
						$pmi_name_all_type = "tc_delete_all";
						$category_quy = "(d.pmi_name='tc_delete' or d.pmi_name='tc_delete_all') and";
					break;
				}
				$str_sql = "
					select
						pmi_name,max(pmi_value) as pmi_value
					from
						otm_project_member as a,
						otm_project_member_role as b,
						otm_role as c,
						otm_role_permission as d
					where
						a.pm_seq=b.otm_project_member_pm_seq and
						b.otm_role_rp_seq=c.rp_seq and
						c.rp_seq=d.otm_role_rp_seq and
						$category_quy
						a.otm_member_mb_email='$mb_email'
					group by pmi_name
				";
				$query = $this->db->query($str_sql);
				$arr = array();
				foreach ($query->result() as $row){
					$arr[] = $row;
				}

				switch($category){
					case "ID_DEFECT":
					case "ID_TC":
						for($i=0;$i<sizeof($arr);$i++){
							if($arr[$i]->pmi_name == $pmi_name_default_type and $arr[$i]->pmi_value){
								$str_sql = "select count(*) as cnt from otm_file where otm_category='$category' and otm_project_pr_seq='$pr_seq' and target_seq='$target_seq' and of_no='$of_no' and writer='$mb_email'";
								$query = $this->db->query($str_sql);
								$tmp_arr="";
								foreach ($query->result() as $row){
									$tmp_arr = $row;
								}
								if($tmp_arr->cnt){
									print "{success:true,msg:'ok'}";
									exit;
								}
							}elseif($arr[$i]->pmi_name == $pmi_name_all_type and $arr[$i]->pmi_value){
								print "{success:true,msg:'ok'}";
								exit;
							}
						}
						print "{success:false,msg:'no_access'}";
						exit;
					break;
				}
			}else{
				print "{success:false,msg:'no_access'}";
			}
		}
	}

	public function file_download_link()
	{
		$target_seq = $this->input->get_post('target_seq',true);
		$of_no = $this->input->get_post('of_no',true);

		$arr = array();
		$str_sql = "select * from otm_file where target_seq='$target_seq' and of_no='$of_no'";
		$query = $this->db->query($str_sql);
		foreach ($query->result() as $row)
		{
			$arr[] = $row;
		}

		if(sizeof($arr) == 1){
			$filepath = 'uploads/files/'.$arr[0]->otm_project_pr_seq."/".$arr[0]->of_file;
			$filepath = addslashes($filepath);
			$filepath = trim(mb_convert_encoding($filepath,"euckr","utf-8"));

			$original = trim(mb_convert_encoding($arr[0]->of_source,"euckr","utf-8"));

			if (file_exists($filepath)) {
				if(eregi("msie", $_SERVER[HTTP_USER_AGENT]) && eregi("5\.5", $_SERVER[HTTP_USER_AGENT])) {
					header("content-type: doesn/matter");
					header("content-length: ".filesize("$filepath"));
					header("content-disposition: attachment; filename=\"$original\"");
					header("content-transfer-encoding: binary");
				} else {
					header("content-type: file/unknown");
					header("content-length: ".filesize("$filepath"));
					header("content-disposition: attachment; filename=\"$original\"");
					header("content-description: php generated data");
				}

				header("pragma: no-cache");
				header("expires: 0");
				flush();
				if (is_file("$filepath")) {
					$fp = fopen("$filepath", "rb");

					while(!feof($fp)) {
						echo fread($fp, 100*1024);
					}
					fclose ($fp);
				}
			}else{
					return "error";
			}
		}else{
			return "error";
		}
	}
}
//End of file FileDownload.php
//Location: ./controllers/FileDownload.php