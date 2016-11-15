<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class CI_Common
{

	var $CI;
	var $result_str;

	function trim_text($str,$len,$tail="..")
	{
		 if(strlen($str)<$len) 
		 {

			return $str; //자를길이보다 문자열이 작으면 그냥 리턴

		 } else{
			$result_str='';
			for($i=0;$i<$len;$i++){
				if((Ord($str[$i])<=127)&&(Ord($str[$i])>=0)){$result_str .=$str[$i];}
				else if((Ord($str[$i])<=223)&&(Ord($str[$i])>=194)){$result_str .=$str[$i].$str[$i+1];$i+1;}
				else if((Ord($str[$i])<=239)&&(Ord($str[$i])>=224)){$result_str .=$str[$i].$str[$i+1].$str[$i+2];$i+2;}
				else if((Ord($str[$i])<=244)&&(Ord($str[$i])>=240)){$result_str .=$str[$i].$str[$i+1].$str[$i+2].$str[$i+3];$i+3;}
			}

			return $result_str.$tail;
		}
	}

	/**
	* checkmb=true, len=10
	* 한글과 Eng (한글=2*3 + 공백=1*1 + 영문=1*1 => 10)
	* checkmb=false, len=10
	* 한글과 Englis (모두 합쳐 10자)
	*/
	function strcut_utf8($str, $len, $checkmb=false, $tail='..') 
	{
		preg_match_all('/[\xEA-\xED][\x80-\xFF]{2}|./', $str, $match);

		$m = $match[0];
		$slen = strlen($str); // length of source string
		$tlen = strlen($tail); // length of tail string
		$mlen = count($m); // length of matched characters

		if ($slen <= $len) return $str;
		if (!$checkmb && $mlen <= $len) return $str;

		$ret = array();
		$count = 0;

		for ($i=0; $i < $len; $i++) 
		{
			$count += ($checkmb && strlen($m[$i]) > 1)?2:1;

			if ($count + $tlen > $len) break;
			$ret[] = $m[$i];
		}

		return join('', $ret).$tail;
	}

	function segment_explode($seg) { //세크먼트 앞뒤 '/' 제거후 uri를 배열로 반환
		$error_rep = error_reporting();
		error_reporting(0);
		$len = strlen($seg);
		
		if(substr($seg, 0, 1) == '/') {
			$seg = substr($seg, 1, $len);
  		}
  		
  		$len = strlen($seg);
  		if(substr($seg, -1) == '/') {
			$seg = substr($seg, 0, $len-1);
  		}
  		
		if(strpos($seg,'?'))
		{
			$seg_qexp = explode("?", $seg);
			$qseg_len = strlen($seg_qexp[0]);
			
			if(substr($seg_qexp[0], -1) == '/') 
			{
				$seg_exp = substr($seg_qexp[0], 0, $qseg_len-1);
			}
			
			$seg_exp = explode("/", $seg_qexp[0]);
			$seg_query_str = "?".$seg_qexp[1];
			
		}else{
			$seg_exp = explode("/", $seg);
		}
	
		// 쿼리스트링을 key(query_string)로 하여 배열로 반환
		if($seg_query_str and (substr($seg_query_str,0,1) == '?'))
		{
			$result=array();
			$str_cnt = strlen($seg_query_str);
			$query_s = substr($seg_query_str,1,$str_cnt);
	
			if(substr($query_s, 0, 1) == '&')
			{
				$query_s = substr($query_s,1,$str_cnt);
			}

			$strings = explode("&", $query_s);

			foreach ($strings as $strs) 
			{
				$a_arr = explode("=", $strs);
				$result = array_merge($result, array($a_arr[0]=>$a_arr[1]));
			}
			$d_arr = array('query_string'=>$result);

			//맨끝 쿼리스트링 제거
			array_pop($seg_exp);

			//쿼리스트링을 제거한 배열과 쿼리스트링을 배열화한 것을 합쳐서 반환

			$seg_exp = array_merge($seg_exp, $d_arr);
		}
		error_reporting($error_rep);
		return $seg_exp;
	}

	/**
	 * 배열(쿼리스트링 포함)에서 주소만들기
	 * @param Array $url : segment_explode 한 url값
	 * @param Array $add_url : 추가하려는 변수 배열
	 * @param Array $del_url : 삭제하려는 변수 배열
	 * @return String : 풀주소 리턴
	 */
	function segment_implode($url, $add_url, $del_url='') 
	{
		$error_rep = error_reporting();
		error_reporting(0);
		if ($url['query_string']) 
		{
			//print_r($url['query_string']);
			//쿼리스트링 만들기
			$q_str = $url['query_string'];
			//쿼리스트링을 키와 값으로 분리
			$qurey_string_key = array_keys($q_str);
			//$qurey_string_val = array_values($q_str);

			$key_cnt = count($qurey_string_key);
			//$val_cnt = count($qurey_string_val);

			if ($del_url) 
			{
				foreach($del_url as $durls) 
				{
					$arr_key = array_keys($qurey_string_key, $durls);

					if ($arr_key[0] == '0') 
					{
						//배열에서 처음에 위치할때
						array_shift($qurey_string_key);
						array_shift($qurey_string_val);

						if(count($qurey_string_val) == '0' and count($qurey_string_key) == '0') 
						{
							$q_str = array();
						} else {
							$q_str = array_combine($qurey_string_key, $qurey_string_val);
						}
					} elseif($arr_key[0] > 0 and $arr_key[0] < $key_cnt-1) {
						//배열 중간에 위치할때
						$f1 = array_splice($qurey_string_key, $arr_key[0]);
						$f2 = array_splice($qurey_string_val, $arr_key[0]);
						if(count($f1) == '0' and count($f2) == '0') 
						{
							$q_str = array();
						} else {
							$q_str = array_combine($f1, $f2);
						}
					} elseif($arr_key[0] == $key_cnt-1) {
						//배열 맨 마지막에 위치할때
						array_pop($qurey_string_key);
						array_pop($qurey_string_val);
						if(count($qurey_string_val) == '0' and count($qurey_string_key) == '0') 
						{
							$q_str = array();
						} else {
							$q_str = array_combine($qurey_string_key, $qurey_string_val);
						}
					}

				}
			}

			if ($add_url) 
			{
				$query_url = array_merge($q_str, $add_url);
			} else {
				$query_url = $q_str;
			}

			$q_url = array();
			foreach ($query_url as $key=>$val) 
			{
				$q_url[] = $key."=".$val;
			}
			$q1_url = implode('&', $q_url);
			//일반주소 만들기
			array_pop($url);
			$s_url = implode('/', $url);
			if ($q1_url)
			{
				$last_url = "/".$s_url."/?".$q1_url;
			} else {
				$last_url = "/".$s_url;
			}		
			error_reporting($error_rep);
			return $last_url;
		} else {
			if ($add_url) 
			{
				foreach ($add_url as $key=>$val) 
				{
					$q_url[] = $key."=".$val;
				}
				$q1_url = implode('&', $q_url);
				$s_url = implode('/', $url);
				error_reporting($error_rep);
				return "/".$s_url."/?".$q1_url;
			} else {
				$s_url = implode('/', $url);
				error_reporting($error_rep);
				return "/".$s_url;
			}
		}
	}

	/**
	 * 쿼리스트링 배열중에 해당 배열이 있으면 그 값을 반환,
	 * 없으면 1로 초기화
	 * @param Array $q_str : segment_explode 한 url값중 query_string 배열
	 * @param String $word : 검색하려는 값
	 * @param String $arg : 반환값 제어
	 * @return String : 리턴값
	 */
	function query_string_search($q_str, $word, $arg='') 
	{
		if($q_str) 
		{
			//쿼리스트링을 키와 값으로 분리
			$qurey_string_key = array_keys($q_str);
			$qurey_string_val = array_values($q_str);

			//검색어처리
			if($arg == 'q') 
			{
				if(in_array($word, $qurey_string_key)) 
				{
					$arr_key = array_keys($qurey_string_key, $word);
					$search_word = urldecode($qurey_string_val[$arr_key[0]]);
					$arr_key1 = array_keys($qurey_string_key, "sfl");
					$sfl = $qurey_string_val[$arr_key1[0]];
					$post = array('method'=>$sfl, 's_word'=>$search_word);
					return $post;
				} else {
					$post='';
					return $post;
				}
			} else {
				//검색어외 기타
				if(in_array($word, $qurey_string_key)) 
				{
					$arr_key = array_keys($qurey_string_key, $word);
					return $qurey_string_val[$arr_key[0]];
				} else {
					return false;
				}
			}
		  } else {
			return false;
		  }
	}

	/**
	 * url중 키값을 구분하여 값을 가져오도록
	 * @param Array $url : segment_explode 한 url값
	 * @param String $key : 가져오려는 값의 key
	 * @return String $url[$k] : 리턴값
	 */
	function url_explode($url,$key)
	{
		for($i=0; count($url)>$i; $i++ )
		{
			if(isset($url[$i]) && $url[$i] == $key) // if($url[$i] ==$key)
			{
				$k = $i+1;
				return $url[$k];
			}
		}
	}

	
	/**
	 * json decode
	 */
	function json_decode($content, $assoc=false) 
	{
		if (!function_exists('json_decode')) 
		{
			$CI =& get_instance();
			$CI->load->library('JSON');
			if ($assoc) {
				$json = new JSON(SERVICES_JSON_LOOSE_TYPE);
			}
			else {
				$json = new JSON;
			}
			return $json->decode($content);
		}else{
			return json_decode($content,$assoc);
		}
	}
	
	/**
	 * json encode
	 */
	function json_encode($content, $assoc=false) 
	{
		if (!function_exists('json_encode')) 
		{
			$CI =& get_instance();
			$CI->load->library('JSON');
			if ($assoc) {
				$json = new JSON(SERVICES_JSON_LOOSE_TYPE);
			} else {
				$json = new JSON;
			}
			return $json->encode($content);
		}else{
			return json_encode($content);
		}
	}

	/**
	 * 하뒤 디렉토리 파일까지 퍼미션 지정
	 * 
	 * @param String $path : 디렉토리
	 * @param int $filemode : 퍼미션 값
	 */
	function chmodr($path, $filemode) 
	{
		$CI =& get_instance();
		if (!is_dir($path))
			return @chmod($path, $filemode);

		$dh = opendir($path);
		while (($file = readdir($dh)) !== false) 
		{
			if($file != '.' && $file != '..') {
				$fullpath = $path.'/'.$file;

				if(is_link($fullpath))
					return FALSE;
				elseif(!is_dir($fullpath) && @!chmod($fullpath, $filemode))
						return FALSE;
				elseif(!$CI->common->chmodr($fullpath, $filemode))
					return FALSE;
			}
		}

		closedir($dh);

		if(@chmod($path, $filemode))
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * 디렉토리 삭제
	 * @param String $dir : 삭제할 디렉토리
	 * @return Boolean
	 */
	function deleteDirectory($dir) 
	{
		if (!file_exists($dir)) return true;
		if (!is_dir($dir) || is_link($dir)) return unlink($dir);

		foreach (scandir($dir) as $item) 
		{
			if ($item == '.' || $item == '..') continue;
			if (!$this->deleteDirectory($dir . "/" . $item)) 
			{
				@chmod($dir . "/" . $item, 0777);
				if (!$this->deleteDirectory($dir . "/" . $item)) return false;
			};
		}
		return rmdir($dir);
	}

	/**
	 * 디렉토리 통체로 복사하여 하위 디렉토리까지 복사.
	 *
	 * @param String $src : 복사 할 디렉토리.
	 * @param String $dst : 복사 될 디렉토리.
	 */
	function recurseCopy($src, $dst) 
	{
		$dir = opendir($src);
		@ mkdir($dst);
		while (false !== ($file = readdir($dir))) 
		{
			if (($file != '.') && ($file != '..')) 
			{
				if (is_dir($src . '/' . $file)) {
					$this->recurseCopy($src . '/' . $file, $dst . '/' . $file);
				} else {
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}

	/**
	 * $temp_file을 복사하여 $save_file을 생성한다. $option이 delete일 경우 $temp_file을 삭제
	 * @param String $temp_file : 원본파일의 경로 및 파일명
	 * @param String $save_file : 저장할 파일의 경로 및 파일명
	 * @param String $option : 옵션
	 */
	function copyFile($temp_file, $save_file, $option = "") 
	{
		@copy($temp_file, $save_file);
		$this->chmodr($save_file,0777);
		if ($option == "delete")
			@ unlink($temp_file);
		return true;
	}


	/** 
	 * object to Array
	 * 
	 * @param object $object 오브젝트
	 * @return Array $return 배열
	 */ 
	function objectToArray( $object )
	{
		$return = array();
		
		if( !is_object( $object ) && !is_array( $object ) )
		{
			return $object;
		}
		
		if( is_object( $object ) )
		{
			foreach($object as $key => $value){
				if(count($value) > 0){
					$value = $this->objectToArray($value);
				} else {
					if(isset($value)){
						if(isset($return[$key]))
							$value = (string)$value;
						else
							settype($value, 'array');
					}
				}

				if(isset($value)){
					if(isset($return[$key]))
						array_push($return[$key], $value);
					else
						$return[$key] = $value;
				}
			}
			
			return $return;
		}else{
			foreach($object as $key=> $value){
				if(count($value) > 0){
					$value = $this->objectToArray($value);
				} else {
					if(isset($value)){
						if(isset($return[$key]))
							$value = (string)$value;
						else
							settype($value, 'array');
					}
				}

				if(isset($value)){
					if(isset($return[$key]))
						array_push($return[$key], $value);
					else
						$return[$key] = $value;
				}
			}
		}

		return $return;
	}

	function computeDiff($from, $to)
	{
		$diffValues = array();
		$diffMask = array();

		$dm = array();
		$n1 = count($from);
		$n2 = count($to);

		for ($j = -1; $j < $n2; $j++) $dm[-1][$j] = 0;
		for ($i = -1; $i < $n1; $i++) $dm[$i][-1] = 0;
		for ($i = 0; $i < $n1; $i++)
		{
			for ($j = 0; $j < $n2; $j++)
			{
				if ($from[$i] == $to[$j])
				{
					$ad = $dm[$i - 1][$j - 1];
					$dm[$i][$j] = $ad + 1;
				}
				else
				{
					$a1 = $dm[$i - 1][$j];
					$a2 = $dm[$i][$j - 1];
					$dm[$i][$j] = max($a1, $a2);
				}
			}
		}

		$i = $n1 - 1;
		$j = $n2 - 1;
		while (($i > -1) || ($j > -1))
		{
			if ($j > -1)
			{
				if ($dm[$i][$j - 1] == $dm[$i][$j])
				{
					$diffValues[] = $to[$j];
					$diffMask[] = 1;
					$j--;  
					continue;              
				}
			}
			if ($i > -1)
			{
				if ($dm[$i - 1][$j] == $dm[$i][$j])
				{
					$diffValues[] = $from[$i];
					$diffMask[] = -1;
					$i--;
					continue;              
				}
			}
			{
				$diffValues[] = $from[$i];
				$diffMask[] = 0;
				$i--;
				$j--;
			}
		}    

		$diffValues = array_reverse($diffValues);
		$diffMask = array_reverse($diffMask);

		return array('values' => $diffValues, 'mask' => $diffMask);
	}

	function diffline($line1, $line2)
	{
		//변경 사항이 한자일때 결함 발생 : 변경전 내용만 보여주기
		return $line1;

		$diff = $this->computeDiff(str_split($line1), str_split($line2));
		$diffval = $diff['values'];
		$diffmask = $diff['mask'];

		$n = count($diffval);
		$pmc = 0;
		$result = '';
		for ($i = 0; $i < $n; $i++)
		{
			$mc = $diffmask[$i];
			if ($mc != $pmc)
			{
				switch ($pmc)
				{
					case -1: $result .= '</del>'; break;
					case 1: $result .= '</ins>'; break;
				}
				switch ($mc)
				{
					case -1: $result .= '<del>'; break;
					case 1: $result .= '<ins>'; break;
				}
			}
			$result .= $diffval[$i];

			$pmc = $mc;
		}
		switch ($pmc)
		{
			case -1: $result .= '</del>'; break;
			case 1: $result .= '</ins>'; break;
		}

		return $result;
	}
}

/* End of file Common.php */
/* Location: ./application/libraries/Common.php */