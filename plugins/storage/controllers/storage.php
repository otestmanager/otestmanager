<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Storage
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/

class Storage extends Controller {
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model('storage/storage_m');
		$this->load->library('File_Form');
		$this->load->library('History');
	}

	public function install()
	{
		//return "aaa";
		return $this->migration->install();
	}

	public function version()
	{
		$data = $this->data;
		$version = $data['param'];
		$this->migration->check_version($version);
	}

	/**
	* Function return_json
	*
	* @param array $temp_arr Array Data
	*
	* @return string
	*/
	public function return_json($temp_arr)
	{
		return '{success:true,totalCount: '.count($temp_arr).', data:'.json_encode($temp_arr).'}';
	}

	function storage_role()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('project_seq', TRUE),
			'pc_category' => 'ID_TC',
			'type' => 'view'
		);
		$this->load->model('project_setup_m');
		$storage_role = $this->project_setup_m->user_project_role($data);

		for($i=0; $i<count($storage_role); $i++){
			if($storage_role[$i]->pmi_value === '1'){
				$role[$storage_role[$i]->pmi_name] = true;
			}
		}
		return $role;
	}

	/**
	* Function storage
	*
	* @return string
	*/
	function storage($data = array())
	{
		$this->data = $data;

		if($data['function'] != 'install')
		{
			$check = $this->migration->_check_module('storage');
			if($check === FALSE)
			{
				//show_error('Module was not established. <br><br>Module Install : <a href="/index.php/Plugin_view/'.$data['module_directory'].'/install">Click</a>');
			}
		}

		$data['view'] = $data['module_directory']."_v";
		$data['skin_dir'] = "plugins/".$data['module_directory']."/views/".$data['skin'];

		if($data['function']){
			return $this->$data['function']();
			exit;
		}else{
			$data['mb_lang'] = $this->session->userdata('mb_lang');
			return render($data);
		}
	}

	/**
	* Function storage_tree_list
	*
	* @return string
	*/
	function storage_tree_list()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'node' => $this->input->post_get('node', TRUE)
		);

		return json_encode($this->storage_m->storage_tree_list($data));
	}


	/**
	* Function create_folder
	*
	* @return string
	*/
	function create_folder()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'node' => $this->input->post_get('node', TRUE),
			'ops_subject' => $this->input->post_get('ops_subject', TRUE),
			'permitions' => json_decode($this->input->post_get('permitions', TRUE))
		);

		return $this->storage_m->create_folder($data);
	}


	/**
	* Function update_folder
	*
	* @return string
	*/
	function update_folder()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'node' => $this->input->post_get('node', TRUE),
			'ops_subject' => $this->input->post_get('ops_subject', TRUE),
			'permitions' => json_decode($this->input->post_get('permitions', TRUE))
		);

		return $this->storage_m->update_folder($data);
	}


	/**
	* Function delete_folder
	*
	* @return string
	*/
	function delete_folder()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'node' => $this->input->post_get('node', TRUE)
		);

		return $this->storage_m->delete_folder($data);
	}


	/**
	* Function delete_file
	*
	* @return string
	*/
	function delete_file()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq',true),
			'target_seq' => $this->input->post('target_seq',true),
			'of_no' => $this->input->post('of_no',true)
		);

		return $this->storage_m->delete_file($data);
	}


	/**
	* Function move_folder
	*
	* @return json
	*/
	public function move_folder()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			'target_id' => $this->input->post('target_id', TRUE),
			'position' => $this->input->post('position', TRUE),
			'select_id' => json_decode($this->input->post('select_id', TRUE))
		);

		echo $this->storage_m->move_folder($data);
	}


	/**
	* Function move_file
	*
	* @return json
	*/
	public function move_file()
	{
		$data = array(
			'pr_seq' => $this->input->post('pr_seq', TRUE),
			//'target_id' => $this->input->post('target_id', TRUE),
			'target_seq' => $this->input->post('target_seq', TRUE),
			//'position' => $this->input->post('position', TRUE),
			'select_id' => json_decode($this->input->post('select_id', TRUE))
		);

		echo $this->storage_m->move_file($data);
	}

	/**
	* Function file_upload
	*
	* @return string
	*/
	function file_upload()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'node' => $this->input->post_get('node', TRUE)
		);

		return $this->storage_m->file_upload($data);
	}

	/**
	* Function file_upload_draw
	*
	* @return string
	*/
	function file_upload_draw()
	{
		if(isset($GLOBALS["HTTP_RAW_POST_DATA"])){
			$rawImage = $GLOBALS["HTTP_RAW_POST_DATA"];
			$removeHeaders = substr($rawImage, strpos($rawImage, ",")+1);
			$decode = base64_decode($removeHeaders);
			$fopen = fopen('uploads/files/73/ddd.png','wb');
			fwrite($fopen, $decode);
			fclose($fopen);
			return 'uploads/files/73/ddd.png';
		}else{
			return 'none';
		}
		return;

		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'node' => $this->input->post_get('node', TRUE),
			'name' => $this->input->post_get('name', TRUE)
			//,	'dataURL' => $this->input->post('dataURL', TRUE)
		);

		$pr_seq = $data['pr_seq'];
		$target_seq = ($data['node'] == 'root')?0:$data['node'];

		$dataURL = $this->input->post('dataURL', TRUE);

		$dataURL2 = substr($dataURL,strpos($dataURL,",")+1);
		$dataURL2 = base64_decode($dataURL2);
		$file = 'uploads/files/'.$data['pr_seq']."/".'output.png';
		file_put_contents($file, $dataURL2);

		$dataURL3 = str_replace('data:image/png;base64,', '', $dataURL);
		$dataURL3 = str_replace(' ', '+', $dataURL3);
		$dataURL3 = base64_decode($dataURL3);

		$default_directory = 'uploads/files/'.$data['pr_seq']."/";
		$upload_file_name = mktime()."_24.png";
		$directory = $default_directory.$upload_file_name;
		file_put_contents($directory, $dataURL3);

		$data = substr($dataURL, strpos($dataURL, ",") + 1);
		$decodedData = base64_decode($data);

		$fp = fopen($directory, 'wb');
		fwrite($fp, $decodedData);
		fclose($fp);
		exit;

		$file_data = array();
		$file_data['source']['name'] = $data['name'].'.png';
		$file_data['source']['tmp_name'] = $dataURL;
		$file_data['source']['size'] = '300';
		$file_data['source']['type'] = 'image/png';

		$file_data['category'] = 'ID_STORAGE';
		$file_data['pr_seq'] = $pr_seq;
		$file_data['target_seq'] = $target_seq;
		$file_data['otm_project_storage_ops_seq'] = $target_seq;
		$file_data['of_no'] = $i;

		$this->File_Form->file_upload($file_data);

		return "{success:true, data:'ok'}";
	}


	/**
	* Function storage_list
	*
	* @return string
	*/
	function storage_list()
	{
		$data = array(
			'limit' => $this->input->post('limit',true),
			'page' => $this->input->post('page',true),
			'start' => $this->input->post('start',true),
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'node' => $this->input->post_get('node', TRUE)
		);

		return $this->storage_m->storage_list($data);
	}

	/**
	* Function storage_permissioin_list
	*
	* @return string
	*/
	function storage_permissioin_list()
	{
		$data = array(
			'pr_seq' => $this->input->post_get('pr_seq', TRUE),
			'node' => $this->input->post_get('node', TRUE),
			'action' => $this->input->post_get('action', TRUE)
		);

		return $this->return_json($this->storage_m->storage_permissioin_list($data));
	}


}
//End of file storage.php
//Location: ./controllers/storage.php
?>