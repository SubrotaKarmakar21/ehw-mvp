<?php
class MyPatients
{
    public function __construct($module = "", $reqData = array())
    {
        foreach ($GLOBALS as $key => $values) {
            $this->$key = $values;
        }
        $this->module   = $module;
        $this->reqData  = $reqData;
        $this->userData = getUserData();

        $this->dataOnly = (isset($this->reqData['dataOnly']) && $this->reqData['dataOnly']==true)?true:false;
        $this->sessUserId = (isset($reqData['userId']) && $reqData['userId'] >0)?$reqData['userId']:$this -> sessUserId;
        //dump_exit($this->userData);
        //TODO:: set height width of featured projects

        $adminUserId = isset($_SESSION['adminUserId']) ? $_SESSION['adminUserId'] : 0;
        if ($this->sessUserId <= 0) {
            $clinic_id = isset($this->reqData['clinic_id']) && $this->reqData['clinic_id'] != '' ? decryptIt($this->reqData['clinic_id']) : 0;
        } else{
            $clinic_id = $this->sessUserId;
        }
        $this->clinic_id = $clinic_id;
    }

    public function getPageContent(){

        $data                       = $this->getItemsList($this->clinic_id, 1);
        $my_items_list_html         = isset($data['content']) ? $data['content'] : '';
        $my_item_total_pages        = isset($data['pagination']['total_pages']) ? $data['pagination']['total_pages'] : 0;
        $total_upcoming_records     = isset($data['totalRows']) ? (int) $data['totalRows'] : 0;

        $replace = array(
            '%my_items_list_html%'              => $my_items_list_html,
            '%my_item_total_pages%'             => $my_item_total_pages,
            '%clinic_id%'                       => encryptIt($this->clinic_id),
            '%hide_if_no_data%'                 => $total_upcoming_records <= 0 ? 'hidden' : '',
        );

        return get_view(DIR_TMPL . $this->module . "/" . $this->module . ".tpl.php", $replace);
    }

    public function getItemsList($user_id = 0, $currentPage = 1, $getPagination = true) {

        $array = array();
        $final_result_array = array();
        $final_result_html = $my_posted_job_html = NULL;
        $totalRows = $showableRows = 0;

        $limit = 10;
        $limit          = isset($this->reqData['per_page_limit']) && $this->reqData['per_page_limit'] > 0 ? $this->reqData['per_page_limit'] : $limit;
        $keyword        = isset($this->reqData['keyword']) ? $this->reqData['keyword'] : '';
        $search_date    = isset($this->reqData['search_date']) && $this->reqData['search_date'] != '' ? $this->reqData['search_date'] : '';


        $offset = ($currentPage - 1 ) * $limit;


        $where = '';

        if($getPagination == true){
            $limit_query = " LIMIT " . $limit . " OFFSET " . $offset;
        }

        $data_selection_query = "SELECT  a.*,CONCAT(a.first_name,' ',a.last_name) AS user_name ";
        $count_selection_query = "SELECT count(a.id) as no_of_patients ";

        $query = " FROM tbl_users AS a WHERE a.parent_id = ".(int)$user_id." AND a.user_type = 'patient' ";

	if($keyword != ''){

    		$keyword = addslashes($keyword);

    		$query .= " AND (CONCAT(a.first_name,' ',a.last_name) LIKE '%$keyword%' OR a.phone_no LIKE '%$keyword%') ";
	}

	$query .= " ORDER BY a.id DESC ";
        $getAllResults = $this->db->pdoQuery($count_selection_query . $query)->result();

        $totalRows = $getAllResults['no_of_patients'];
        $getShowableResults = $this->db->pdoQuery($data_selection_query . $query . $limit_query)->results();

        if (count($getShowableResults) > 0) {
            $showableRows = count($getShowableResults);

            $companies_ul_tpl = new MainTemplater(DIR_TMPL . $this->module . "/items_rows-nct.tpl.php");
            $single_company_li_tpl_parsed = $companies_ul_tpl->parse();

            foreach ($getShowableResults as $key => $value) {

                $id             = isset($value['id']) ? $value['id'] : 0;
                $user_type      = isset($value['user_type']) ? $value['user_type'] : '';
                $first_name     = isset($value['first_name']) ? $value['first_name'] : '';
                $last_name      = isset($value['last_name']) ? $value['last_name'] : '';
                $gender         = isset($value['gender']) ? $value['gender'] : '';
                $address        = isset($value['address']) ? (string) filtering($value['address'],'output','string') : '';

                $address = trim($address);

                if (mb_strlen($address, 'UTF-8') > 50) {
                    $address = mb_substr($address, 0, 50, 'UTF-8') . '...';
                } else{
                    $address = $address != '' ? $address : '-';
                }

		$age_display = '';

		if(!empty($value['date_of_birth'])){

    			$dob = new DateTime($value['date_of_birth']);
    			$now = new DateTime();
    			$diff = $now->diff($dob);

    			if($diff->y > 0){
        			$age_display = $diff->y . 'Y';
    			}
    			elseif($diff->m > 0){
        			$age_display = $diff->m . 'M';
    			}
    			else{
        			$age_display = $diff->d . 'D';
    			}
		}

                $replace = array(
                    "%id%"                          => $id,
                    '%user_name%'                   => $first_name.' '.$last_name,
                    "%first_name%"                  => isset($value['first_name']) ? $value['first_name'] : '',
                    "%last_name%"                   => isset($value['last_name']) ? $value['last_name'] : '',
                    '%phone_no%'                    => isset($value['phone_no']) ? $value['phone_no'] : '',
                    '%phone_country_code%'          => isset($value['phone_country_code']) ? $value['phone_country_code'] : '',
                    '%address%'                     => $address,
                    '%gender%'                      => $value['gender'] == 'n' ? '-' : ucfirst($value['gender']),
		    '%date_of_birth%' 		    => $age_display,
                );

                $my_posted_job_html .= str_replace(array_keys($replace), array_values($replace), $single_company_li_tpl_parsed);

            }
        } else {
            $no_result_found_tpl = new MainTemplater(DIR_TMPL . $this->module . "/noresult-nct.tpl.php");
            $final_result_html = $no_result_found_tpl->parse();

            $replace = array(
            );

            $my_posted_job_html = str_replace(array_keys($replace), array_values($replace), $final_result_html);
        }

        $page_data = getPagerData($totalRows, $limit,$currentPage);

        $final_result_array['content'] = $my_posted_job_html;
        $page_data = getPagerData($totalRows, $limit,$currentPage);
        $final_result_array['pagination'] = array('current_page'=>$currentPage,'total_pages'=>$page_data->numPages,'total'=>$totalRows);
        $final_result_array['totalRows'] = $totalRows;

        return $final_result_array;

    }

    public function get_user_info($id = 0) {
        $html = '';

        $query = "SELECT  u.*,CONCAT(u.first_name,' ',u.last_name) AS user_name
        FROM tbl_users AS u
        WHERE u.id = ".(int) $id." AND user_type = 'patient' ORDER BY u.id DESC ";

        $userInfo = $this->db->pdoQuery($query)->result();

        if (!empty($userInfo)) {
            $id             = isset($userInfo['id']) ? $userInfo['id'] : 0;
            $user_type      = isset($userInfo['user_type']) ? $userInfo['user_type'] : '';
            $first_name     = isset($userInfo['first_name']) ? $userInfo['first_name'] : '';
            $last_name      = isset($userInfo['last_name']) ? $userInfo['last_name'] : '';
	    $age_display = '';

	    if(!empty($userInfo['date_of_birth'])){

    		$dob = new DateTime($userInfo['date_of_birth']);
    		$now = new DateTime();
    		$diff = $now->diff($dob);

    		if($diff->y > 0){
        		$age_display = $diff->y . 'Y';
    		}
    		elseif($diff->m > 0){
        		$age_display = $diff->m . 'M';
    		}
    		else{
        		$age_display = $diff->d . 'D';
    		}
	    }

            $companies_ul_tpl = new MainTemplater(DIR_TMPL . $this->module . "/info_modal_html-nct.tpl.php");
            $single_company_li_tpl_parsed = $companies_ul_tpl->parse();

            $replace = array(
                "%id%"                          => $id,
                "%uploaded_image%"              => get_image_url($userInfo['profile_photo'],"profile_photo",'th2_'),
                '%user_name%'                   => $first_name.' '.$last_name,
                "%first_name%"                  => isset($userInfo['first_name']) ? $userInfo['first_name'] : '',
                "%last_name%"                   => isset($userInfo['last_name']) ? $userInfo['last_name'] : '',
                '%phone_no%'                    => isset($userInfo['phone_no']) ? $userInfo['phone_no'] : '',
                '%phone_country_code%'          => isset($userInfo['phone_country_code']) ? $userInfo['phone_country_code'] : '',
                '%address%'                     => isset($userInfo['address']) ? $userInfo['address'] : '',
                '%gender%'                      => $userInfo['gender'] == 'n' ? '-' : ucfirst($userInfo['gender']),
		"%date_of_birth%" 		=> $age_display,
            );

            $html .= str_replace(array_keys($replace), array_values($replace), $single_company_li_tpl_parsed);
        }

        return $html;

    }
}
