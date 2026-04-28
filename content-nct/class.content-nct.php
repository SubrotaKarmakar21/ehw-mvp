<?php
class Content extends Home {
	public function __construct($id=0,$module = '') {
		parent::__construct();
		$this->id = $id;
		$this->module = $module;
		//$arr=$this->getStaticPageContent(array('id'=>2),$displayType=1);
		// _print_r($arr);
	}
	public function getStaticPageContent($slug = ''){
		$final_result = array();
		
		$qryRes = $this->db->pdoQuery("SELECT * FROM tbl_content WHERE page_slug = '".$slug."' ")->result();
		$fetchRes = $qryRes;

		$id = (isset($fetchRes['id'])) ? filtering($fetchRes['id'],'output','int','') : 0;

		$this->pageName = $page_title 				= isset($fetchRes['page_title']) ? filtering($fetchRes['page_title'],'output','string','') : '';
		$meta_keyword 				= isset($fetchRes['meta_keyword']) ? filtering($fetchRes['meta_keyword'],'output','string','') : filtering($fetchRes['meta_keyword'],'output','string','');
		$meta_desc 					= isset($fetchRes['meta_desc']) ? filtering($fetchRes['meta_desc'],'output','string','') : filtering($fetchRes['meta_desc'],'output','string','');

		$this->pageDesc = $pageDesc = isset($fetchRes['page_desc']) ? filtering($fetchRes['page_desc'],'output','text','') : filtering($fetchRes['page_desc'],'output','text','');

		$final_result = array(
			"id"				=> $id,
			"page_name"			=> $page_title,
			"meta_keyword"		=> $meta_keyword,
			"meta_desc"			=> $meta_desc,
			"page_desc"			=> $pageDesc
		);
		return $final_result;
	}

	public function getPageContent(){
		$replace = array(
			'%PAGE_TITLE%' 				=> $this->pageName,
			'%PAGE_DESCRIPTION%' 		=> $this->pageDesc
		);

		return get_view(DIR_TMPL . $this -> module . "/" . $this -> module . ".tpl.php",$replace);

	}
}

?>
