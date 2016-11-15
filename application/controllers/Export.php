<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Export
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Export extends Controller {

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
		}

		if($this->input->post_get('tcplan', TRUE)){
			$data['tcplan'] = $this->input->post_get('tcplan', TRUE);
		}

		if($this->seg_exp[1] === 'plugin'){
			$controller = $this->seg_exp[2];
			$model = $controller.'_m';
			$function = $this->seg_exp[3];
			$module_directory = $controller;

			$data['view'] = $method.'_v';

			if($method){
				$data['module_directory'] =	$module_directory;
				$data['controller'] = $controller;
			
				$error_rep = error_reporting();
				error_reporting(0);  
				if($function) {
					$data['function'] = $function;
				}
				else{
					$data['function'] = '';
				}
				error_reporting($error_rep);

				$data['skin'] = 'default';

				$plugin = modules::run($data['module_directory'].'/'.$data['controller'], $data);

				if(isset($plugin)){
					$this->excel->setActiveSheetIndex(0);
					if($controller === 'report'){
						$this->excel->multi_stream($function.'.xls', $plugin);
					}else{
						$this->excel->stream($function.'.xls', $plugin);
					}
				}
			}
			exit;
		}else{
			$controller = $this->seg_exp[1];
			$model = $controller.'_m';
			$function = $this->seg_exp[2];
			$module_directory = '/application';

			$data['function'] = $function;

			// Load the Model
			$data = $this->load->model($model)->export($data);

			if(isset($data)){
				$this->excel->setActiveSheetIndex(0);
				$this->excel->stream($function.'.xls', $data);
			}
			exit;
		}
	}
}
//End of file Export.php
//Location: ./controllers/Export.php