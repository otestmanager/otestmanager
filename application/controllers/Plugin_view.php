<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Class Plugin_view
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Plugin_view extends Controller
{
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
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

		$data['mb_email'] = $this->session->userdata('mb_email');
		$data['mb_name'] = $this->session->userdata('mb_name');
		$data['mb_is_admin'] = $this->session->userdata('mb_is_admin');

		if($this->input->post('project_seq', TRUE)){
			$data['project_seq'] = $this->input->post('project_seq', TRUE);
		}

		if($this->input->post('tcplan', TRUE)){
			$data['tcplan'] = $this->input->post('tcplan', TRUE);
		}

		if($this->input->post('subpage', TRUE)){
			$data['subpage'] = $this->input->post('subpage', TRUE);
		}

		$data['view'] = $method.'_v';

		if($method){
			$data['module_directory'] = $this->seg_exp[1];
			$data['controller'] = $this->seg_exp[1];

			if(@$this->seg_exp[2]) {
				$data['function'] = $this->seg_exp[2];
			}
			else{
				$data['function'] = '';
			}

			if(@$this->seg_exp[3]) {
				$data['param'] = $this->seg_exp[3];
			}

			$data['skin'] = 'default';

			$plugin = modules::run($data['module_directory'].'/'.$data['controller'], $data);
			echo $plugin;
		}
	}
}
//End of file Plugin_view.php
//Location: ./controllers/Plugin_view.php
