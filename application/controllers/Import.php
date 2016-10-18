<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Import
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Import extends Controller {

	public function __construct() {
		parent::__construct();
		$this->load->database();
		// Load the Library
		$this->load->library("excel");
		$this->output->enable_profiler(FALSE);
		$this->seg_exp = $this->common->segment_explode($this->uri->uri_string());
	}

	/**
	* Function _remap
	*
	* @param array $method Array Data
	*
	* @return string
	*/
	function _remap($method)
	{
		$data = array();
		if($this->input->post_get('seq', TRUE)){
			$data['seq'] = $this->input->post_get('seq', TRUE);
		}

		if($this->input->post_get('project_seq', TRUE)){
			$data['project_seq'] = $this->input->post_get('project_seq', TRUE);
			$data['pr_seq'] = $this->input->post_get('project_seq', TRUE);
		}

		if($this->input->post_get('tcplan', TRUE)){
			$data['tcplan'] = $this->input->post_get('tcplan', TRUE);
		}

		if($this->input->post_get('version', TRUE)){
			$data['version'] = $this->input->post_get('version', TRUE);
		}

		if($this->input->post_get('import_check_id', TRUE)){
			$data['import_check_id'] = ($this->input->post_get('import_check_id', TRUE) === 'true')?true:false;
		}

		if($this->input->post_get('update', TRUE)){
			$data['update'] = ($this->input->post_get('update', TRUE) === 'true')?true:false;
		}

		$FileName = mktime().'_import.xls';

		@chmod("uploads", 0707);
		move_uploaded_file($_FILES['form_file']["tmp_name"],"uploads/".$FileName);

		$path = "uploads/".$FileName;

		//$objPHPExcel	= PHPExcel_IOFactory::load($path);
		$objReader = PHPExcel_IOFactory::createReader('Excel5');
		$objReader->setReadDataOnly(true);
		$objPHPExcel = $objReader->load($path);
		unlink($path);

		$sheetCount = $objPHPExcel->getSheetCount();
		$sheetNames = $objPHPExcel->getSheetNames();

		$sheet_index = ($this->input->post_get('sheet_index', TRUE) >= 0)?$this->input->post_get('sheet_index', TRUE):'';

		if($sheetCount > 1 && $sheet_index == ''){
				echo '{success:true,sheet_count:'.$sheetCount.', sheet_names :'.json_encode($sheetNames).', index: "'.$sheet_index.'"}';
				exit;

		}else{
			//echo '{success:true,msg:'.$sheetCount."/".json_encode($sheetNames).', index: "'.$sheet_index.'"}';
			//exit;
		}

		if($sheetCount == 1){
			$sheet_index = 0;
		}

		$worksheet		= $objPHPExcel->getSheet($sheet_index);

		$data['import_data'] = $worksheet;
		unset($objPHPExcel);
		unset($worksheet);

		if($this->seg_exp[1] === 'plugin'){
			$controller = $this->seg_exp[2];
			$model = $controller.'_m';
			$function = $this->seg_exp[3];
			$module_directory = $controller;

			$data['view'] = $method.'_v';

			if($method){
				$data['module_directory'] =	$module_directory;
				$data['controller'] = $controller;

				if(@$function) {
					$data['function'] = $function;
				}
				else{
					$data['function'] = '';
				}

				$data['skin'] = 'default';

				$value = $this->load->model($module_directory.'/'.$model)->import($data);
				if($value['result'] === TRUE){
					echo '{success:true,msg:'.$value['msg'].'}';
				}else{
					echo '{success:false,msg:\''.$value['msg'].'\'}';
				}

				exit;
			}
			exit;
		}else{
			$controller = $this->seg_exp[1];
			$model = $controller.'_m';
			$function = $this->seg_exp[2];
			$module_directory = '/application';

			$data['function'] = $function;

			// Load the Model
			$value = $this->load->model($model)->import($data);
			if($value['result'] === TRUE){
				echo '{success:true,msg:'.$value['msg'].'}';
			}else{
				echo '{success:false,msg:\''.$value['msg'].'\'}';
			}
			exit;
		}
	}

	function duplicate_check($data){

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
			foreach ($query->result() as $row)
			{
				return $row->$data['select_key'];
			}
		}
	}
}
//End of file Import.php
//Location: ./controllers/Import.php