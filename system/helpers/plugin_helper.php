<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function run($data = array()){
	require_once PLUGIN_ROOT.'/'.$data['module_directory'].'/controllers/'.$data['controller'].EXT;
	$module = new $data['controller']();

	$method = 'run';
	if (isset($data['method']) && strcmp($method, $data['method'])) {
		$methods = get_class_methods($module); // method_exists function is not case-sensitive
		if (in_array($data['method'], $methods, true)) {
			call_user_func_array(array(&$module, $method), array($data)); // first. $module->run($data) execute
			$method = $data['method'];
		}
	}
	echo call_user_func_array(array(&$module, $method), array($data));
}

function render($data = array()) {
	extract($data); //$data['name'] => $name 형으로 변경 
	include PLUGIN_ROOT.'/'.$data['module_directory'].'/views/'.$data['skin'].'/'.$data['view'].EXT;

}

function load($object) {
	$this->$object =& load_class(ucfirst($object));
}

function _assign_libraries() {
	$ci =& get_instance();
	foreach (get_object_vars($ci) as $key => $object) {
		$this->$key =& $ci->$key;
	}
}