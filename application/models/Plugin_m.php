<?php if( ! defined('BASEPATH') )exit('No direct script access allowed');
/**
 * Class Plugin_m
 *
 * @category  Application
 * @package   OTM
 * @author    OTM <otm@sta.co.kr>
 * @copyright 2014 STA
 * @license   http://www.sten.or.kr/otm GNU General Public License
 * @link      http://pear.php.net/package/PHP_CodeSniffer
 * @link      http://codeigniter.com/user_guide/general/styleguide.html
*/
class Plugin_m extends CI_Model
{
	/**
	* Function __construct
	*
	* @return none
	*/
	function __construct()
	{
		parent::__construct();
	}


	/**
	* Function core_info
	*
	* @return array
	*/
	function core_info()
	{
		$mb_lang = $this->session->userdata('mb_lang');

		switch($mb_lang)
		{
			case "project-ko":
			case "ko":
				$text_welcome =	'OTestManager에 오신 것을 환영합니다!';

				$text_not_found_info = '업데이트 정보가 없습니다.';
				$text_install_version =	'설치 버전';
				$text_update_version =	'업데이트 버전';
				$text_update =	'업데이트 하기';
				$text_new_version =	'새 버전이 있습니다.';

				$text_community  = '사용자 커뮤니티';
				$text_community_content  = 'OTestManager 사용자 및 개발자의 커뮤니티 형성을 목적으로 개설 된 카페 입니다.<br>
					사용자 커뮤니티에서는 OTestManager의 소스 다운로드, 메뉴얼 및 다양한 정보를 자료실 및 여러 게시판에서
					제공하고 있습니다.<br>

					OTestManager 사용 및 개발에 대한 자유로운 의사소통과 다양한 정보 공유를 통해 커뮤니티가 활성화 될 수 있
					도록 많은 분들의 관심과 참여를 부탁 드립니다.^^';

				$text_sourceforge  = '소스포지';
				$text_sourceforge_content  = '소스포지에서는 소스 최신버전 및 메뉴얼 등을 제공하고 있으며, <a target="_blank" href="http://sourceforge.net/p/otestmanager/svn">SVN</a>을 이용하여 항상 최신 버전의 OTestManager를 이용하실 수 있습니다.';
				$text_sourceforge_manual  = '메뉴얼';
				$text_sourceforge_new_version  = '최신버전 다운로드';

				$text_sten_content = "STEN (Software Test Engineers Network - SW 테스트 전문가 네트워크)은 소프트웨어 테스팅을 수행하는 실무자들을 위한 소프트웨어 테스팅 전문 그룹입니다. 소프트웨어 테스팅 관련 자료 및 정보 공유, 전문가 네트워크 구성, 테스팅의 중요성 인식 확산 등을 목적으로 2002년 10월에 설립되었습니다.";
				$text_sta_content = "㈜STA테스팅컨설팅은 테스트 엔지니어들의 모임인 STEN 커뮤니티를 기반으로 성장하여 국내 SW테스트 분야를 선도하고 있으며, 글로벌 SW테스팅 리더를 비젼으로 SW테스팅 분야를 리드하고자 하는 SW테스팅 전문 기업입니다.<br>수년간 국내 SW테스팅 교육시장의 90%이상을 차지해오고 있고, 컨설팅 사업의 확대, 테스트 관리자동화 솔루션(TPMS)의 개발, 전문 도서 출판, 국내외 세미나/컨퍼런스 개최 등 지속적인 성장을 해오고 있습니다.";
				break;
			default:
				$text_welcome =	'Welcome to OTestManager!';

				$text_not_found_info = 'Update Information not found.';
				$text_install_version =	'Install Version ';
				$text_update_version =	'Update Version ';
				$text_update =	'Update Now';
				$text_new_version =	'New Version';

				$text_community  = 'User Community';
				$text_community_content  = 'OTestManager User Community.<br>';

				$text_sourceforge  = 'Sourceforge';
				$text_sourceforge_content  = 'OTestManager Sourceforge. <br>New Version Source, New Vesion Manual, <a target="_blank" href="http://sourceforge.net/p/otestmanager/svn">SVN</a> etc.';
				$text_sourceforge_manual  = 'Manual';
				$text_sourceforge_new_version  = 'Source Download';

				$text_sten_content = "STEN (Software Test Engineers Network)";
				$text_sta_content = "STA";
				break;
		}

		$info = array();
		$modules = $this->migration->display_all_migrations();

		foreach ($modules as $module=>$v) {
			$module =  str_replace('\\', '', $module);
			$module =  str_replace('/', '', $module);
			if($module === 'Otm'){
				$current = $this->migration->_check_module($module);

				$info['name'] = $module;
				$info['current_ver'] = $current['version'];

				$pieces = explode("/", $v[count($v)-1]);
				$name = basename($pieces[count($pieces)-1], '.php');
				$info['migration_ver'] = (int)str_replace('_'.$module, '', $name);
			}
		}

		$return_content = "<div>
			<div>
				<div style=\"padding:5px;font-size:14px;font-weight:bold;\">".$text_welcome."</div>
			</div>
			";

		if($info['current_ver'] < $info['migration_ver']){
			$info['migration_ver'] = $info['current_ver']+1;
			$return_content .= "<center><span style=\"padding:5px;background-color:pink;text-align:center;\">
					".$text_new_version." <input type=button onClick=\"javascript:plugin_migration('./index.php/Otm/version/".$info['migration_ver']."')\" value=\"".$text_update."\">(".$text_install_version."   : ".$info['current_ver'].", ".$text_update_version." : ".$info['migration_ver'].")
				</span></center>";
			$return_content .= "<div style=\"padding:5px;\"></div>";
		}else{
			$return_content .= "
				<center><span style=\"padding:5px;background-color:skyblue;text-align:center;\">
					".$text_not_found_info." ".$text_install_version." : ".$info['current_ver']."
				</span></center>
				<div style=\"padding:5px;\"></div>";
		}

		$return_content .= "
			<div style=\"float:left;padding:5px;\">
				<div style=\"float:left;width:120px;\">
					<a target=\"_blank\" href=\"http://www.sten.or.kr/otm\">".$text_community."</a>
				</div>
				<div style=\"margin-left: 120px;padding-left:5px;border-left: 1px solid black;\">
					".$text_community_content."
				</div>
			</div>

			<div style=\"float:left;padding:5px;\"></div>

			<div style=\"float:left;padding:5px;\">
				<div style=\"float:left;width:120px;border-right: 1px solid black;\">
					<a target=\"_blank\" href=\"http://sourceforge.net/projects/otestmanager/\">".$text_sourceforge."</a><br>
						- <a target=\"_blank\" href=\"http://sourceforge.net/projects/otestmanager/files/OTM_Manual/\">".$text_sourceforge_manual."</a><br>
						- <a target=\"_blank\" href=\"http://sourceforge.net/p/otestmanager/svn/HEAD/tarball\">".$text_sourceforge_new_version."</a><br>
				</div>
				<div style=\"margin-left: 120px;padding-left:5px;\">
					".$text_sourceforge_content."
				</div>
			</div>

			<div style=\"float:left;padding:5px;\"></div>

			<div style=\"float:left;padding:5px;\">
				<div style=\"float:left;width:120px;\">
					<a target=\"_blank\" href=\"http://www.sten.or.kr\">STEN</a><br>
				</div>
				<div style=\"margin-left: 120px;padding-left:5px;border-left: 1px solid black;\">
					".$text_sten_content."
				</div>
			</div>

			<div style=\"float:left;padding:5px;\"></div>

			<div style=\"float:left;padding:5px;\">
				<div style=\"float:left;width:120px;\">
					<a target=\"_blank\" href=\"http://www.sta.co.kr\">STA</a><br>
				</div>
				<div style=\"margin-left: 120px;padding-left:5px;border-left: 1px solid black;\">
					".$text_sta_content."
				</div>
			</div>
		</div>";

		return $return_content;
	}


	/**
	* Function Plugin list
	*
	* @return array
	*/
	function Plugin_list()
	{
		$mb_lang = $this->session->userdata('mb_lang');

		switch($mb_lang)
		{
			case "project-ko":
			case "ko":
				$company_link = '<a target="_blank" href="http://www.sta.co.kr/">STA테스팅컨설팅 제작</a>';
				$text_not_found_info = '플러그인 정보가 없습니다.';
				$text_version_name = '버전 ';
				$text_new_version =	'최신 버전입니다.';
				$text_update_new_version =	' 의 새 버전이 있습니다.';
				$text_update =	'업데이트 하기';
				$text_not_install =	' 이(가) 설치 되어 있지 않습니다.';
				$text_install =	'설치 하기';
				$text_not_found_migration =	'마이그레이션 정보가 없습니다.';

				break;
			default:
				$company_link = '<a target="_blank" href="http://www.sta.co.kr/">STA Consulting Inc.</a>';
				$text_not_found_info = 'Plug-in Information not found.';
				$text_version_name = 'Version';
				$text_new_version =	'New Version Installed';
				$text_update_new_version =	' have a new version.';
				$text_update =	'Update Now';
				$text_not_install =	' is not installed.';
				$text_install =	'Install Now';
				$text_not_found_migration =	'Migration Information not found.';
				break;
		}


		$core_info = array();

		$migrations_list = array();
		$migrations = array();
		$modules = $this->migration->display_all_migrations();
		//$modules = $this->migration->list_all_modules();

		foreach ($modules as $module=>$v) {
			$module =  str_replace('\\', '', $module);
			$module =  str_replace('/', '', $module);
			$current = $this->migration->_check_module($module);

			$temp_data['service_id'] = strtolower($module);
			$temp_data['name'] = $module;
			$temp_data['current_ver'] = $current['version'];

			$pieces = explode("/", $v[count($v)-1]);
			$mi_version = basename($pieces[count($pieces)-1], '.php');

			/*
			 *	Plug-in Infomations
			 */
			$mb_lang = $this->session->userdata('mb_lang');
			$name		 = $module;
			$version	 = $temp_data['current_ver'];
			$description = '<br><br>';
			$url		 = '';
			$company	 = '';
			$ishidden	 = '';
			$company_link = '';

			$filepath = PLUGIN_ROOT . '/'. strtolower($module) . '/info.xml';

			if(is_file("$filepath")){
				$xml = file_get_contents($filepath);

				//Set up the parser object
				$parser = new XMLParser($xml);
				//Work the magic...
				$parser->Parse();

				foreach($parser->document->data as $rating)
				{
					if($rating->tagAttrs['type'] == $mb_lang){

						$name		 = $rating->name[0]->tagData;//defect, tc
						$ishidden	 = $rating->ishidden[0]->tagData;//defect, tc
						$version	 = $rating->version[0]->tagData;
						$description = $rating->description[0]->tagData;
						$url		 = $rating->url[0]->tagData;
						$company	 = $rating->company[0]->tagData;
						$company_link = '<a target="_blank" href="'.$url.'">'.$company.'</a>';

						if($rating->iconcls)
							$temp_data['iconcls'] = $rating->iconcls[0]->tagData;
						//if($rating->subpage)
							$temp_data['subpage'] = $rating->subpage[0]->tagData;

						$temp_data['order'] = ($rating->order[0]->tagData)?$rating->order[0]->tagData:1000;
						$temp_data['categoryid'] = $rating->categoryid[0]->tagData;
						$temp_data['userform'] = $rating->userform[0]->tagData;
					}
				}

				if($ishidden && $ishidden == "true")
				{
					continue;
				}
			}else{
				$description = $text_not_found_info;
				continue;
			}
			/*
			 *	Plug-in Infomations
			 */

			$temp_data['name'] = $name;
			$temp_data['ishidden'] = $ishidden;
			$temp_data['migration_ver'] = (int)str_replace('_'.$module, '', $mi_version);
			$temp_data['description'] = $description.'<br><br>';

			$version_info_text = ($temp_data['current_ver'])?$text_version_name.$temp_data['current_ver']:$text_version_name.$version;
			$temp_data['description'] .= $version_info_text.' | '.$company_link.'</span>';
			$temp_data['version_info'] = '<center><span style="padding:10px;background-color:skyblue;">'.$text_new_version.'</span></center>';

			if($temp_data['current_ver'] < $temp_data['migration_ver']){
				$info['migration_ver'] = $info['current_ver']+1;
				$temp_data['version_info'] = '<center><span style="padding:10px;background-color:pink;">'.$temp_data['name'].$text_update_new_version.' <input type=button onClick="javascript:plugin_migration(\'./index.php/Plugin_view/'.$module.'/version/'.$temp_data['migration_ver'].'\')" value="'.$text_update.'"></span></center>';
			}

			if(!$temp_data['current_ver']){
				$temp_data['version_info'] = '<center><span style="padding:10px;background-color:pink;">'.$temp_data['name'].$text_not_install.' <input type=button onClick="javascript:plugin_migration(\'./index.php/Plugin_view/'.$module.'/install/\')" value="'.$text_install.'"></span></center>';
			}

			if($module === 'Otm'){
				$core_info = $temp_data;
				continue;
			}

			$migrations_list[$module] = (int)str_replace('_'.$module, '', $mi_version);
			array_push($migrations, $temp_data);
		}

		$plugins = $this->migration->list_all_modules();
		foreach ($plugins as $plugin=>$v) {
			$module = str_replace('\\', '', $v[1]);
			$module =  str_replace('/', '', $module);

			//if(strtolower($module) == 'mantis'){
			//	continue;
			//}

			if(empty($migrations_list[$module])){

				/*
				 *	Plug-in Infomations
				 */
				$mb_lang = $this->session->userdata('mb_lang');
				$name		 = $module;
				$version	 = '';
				$description = '<br><br>';
				$url		 = '';
				$company	 = '';
				$ishidden	 = '';
				$company_link = '';

				$filepath = PLUGIN_ROOT . '/'. strtolower($module) . '/info.xml';

				if(is_file("$filepath")){
					$xml = file_get_contents($filepath);

					//Set up the parser object
					$parser = new XMLParser($xml);
					//Work the magic...
					$parser->Parse();

					foreach($parser->document->data as $rating)
					{
						if($rating->tagAttrs['type'] == $mb_lang){
							$name		 = $rating->name[0]->tagData;//리포트, tc defect 연결
							$ishidden	 = $rating->ishidden[0]->tagData;
							$version	 = $rating->version[0]->tagData;
							$description = $rating->description[0]->tagData;
							$url		 = $rating->url[0]->tagData;
							$company	 = $rating->company[0]->tagData;
							$company_link = '<a target="_blank" href="'.$url.'">'.$company.'</a>';

							if($rating->iconcls)
								$temp_data['iconcls'] = $rating->iconcls[0]->tagData;
							//if($rating->subpage)
								$temp_data['subpage'] = $rating->subpage[0]->tagData;

							$temp_data['order'] = ($rating->order[0]->tagData)?$rating->order[0]->tagData:1000;
							$temp_data['categoryid'] = $rating->categoryid[0]->tagData;
							$temp_data['userform'] = $rating->userform[0]->tagData;
						}
					}

					if($ishidden && $ishidden == "true")
					{
						continue;
					}
				}else{
					$description = $text_not_found_info;
					continue;
				}
				/*
				 *	Plug-in Infomations
				 */
				$temp_data['service_id'] = strtolower($module);
				$temp_data['name'] = $name;
				$temp_data['ishidden'] = $ishidden;
				$temp_data['current_ver'] = '';
				$temp_data['migration_ver'] = '';
				$temp_data['description'] = $description.'<br><span style="padding:10px;">'.$text_not_found_migration;

				$version_info_text = ($temp_data['current_ver'])?$text_version_name .$temp_data['current_ver']:$text_version_name .$version;

				if($version_info_text && $company_link){
					$temp_data['description'] .= '<br><br>'.$version_info_text.' | '.$company_link.'</span>';
				}

				$temp_data['version_info'] = '';
				array_push($migrations, $temp_data);
			}
		}


		$sortArray = array();
		foreach($migrations as $plugin){
			foreach($plugin as $key=>$value){
				if(!isset($sortArray[$key])){
					$sortArray[$key] = array();
				}
				$sortArray[$key][] = $value;
			}
		}
		$orderby = "order"; //change this to whatever key you want from the array
		//array_multisort($sortArray[$orderby],SORT_DESC,$migrations);
		array_multisort($sortArray[$orderby],SORT_ASC,$migrations);

		$service_info['migrations'] = $migrations;
		$service_info['core_info'] = $core_info;

		return $service_info;
	}
}
//End of file Plugin_m.php
//Location: ./models/Plugin_m.php