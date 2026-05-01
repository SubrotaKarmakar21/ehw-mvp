<?php
class MyDoctors
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
            '%my_items_list_html%'          => $my_items_list_html,
            '%my_item_total_pages%'         => $my_item_total_pages,
            '%clinic_id%'                   => encryptIt($this->clinic_id),
            '%hide_if_no_data%'             => $total_upcoming_records <= 0 ? 'hidden' : '',
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

        $data_selection_query = "SELECT  u.*,CONCAT(u.first_name,' ',u.last_name) AS user_name ";
        $count_selection_query = "SELECT count(u.id) as no_of_doctors ";

        $query = " FROM tbl_users AS u
        WHERE u.parent_id = ".(int) $user_id." AND user_type = 'doctor' ORDER BY u.id DESC ";

        $getAllResults = $this->db->pdoQuery($count_selection_query . $query)->result();

        $totalRows = $getAllResults['no_of_doctors'];
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

                $type_of_doctors = $this->db->pdoQuery("
                    SELECT ud.type_of_doctor_id,d.name
                    FROM tbl_users_doctor_type AS ud
                    LEFT JOIN tbl_type_of_doctors AS d ON d.id = ud.type_of_doctor_id
                    WHERE ud.user_id = ".(int) $id."
                    ORDER BY ud.id ASC ")->results();
                $type_of_doctors_str = '';
                if (count($type_of_doctors) > 0) {
                    foreach ($type_of_doctors as $key => $v2) {
                        $type_of_doctors_str.= '<li class="tag">'.$v2['name'].'</li>';
                    }
                }

                $specialties = $this->db->pdoQuery("
                    SELECT ud.specialties_id,d.name
                    FROM tbl_users_specialties AS ud
                    LEFT JOIN tbl_specialties AS d ON d.id = ud.specialties_id
                    WHERE ud.user_id = ".$id." 
                    ORDER BY ud.id ASC ")->results();

                $specialties_str = '';
                if (count($specialties) > 0) {
                    foreach ($specialties as $key => $v1) {
                        $specialties_str.= '<li class="tag">'.$v1['name'].'</li>';
                    }
                }

                $consultation_fees = isset($value['consultation_fees']) ? $value['consultation_fees'] : '';
                $practicing_since = isset($value['practicing_since']) && $value['practicing_since'] != '' ? date("m-Y", strtotime($value['practicing_since'])) : '';
                $time_slot_html = get_time_slot_table($id);

                $replace = array(
                    "%id%"                          => encryptIt($value['id']),
                    "%uploaded_image%"              => get_image_url($value['profile_photo'],"profile_photo",'th2_'),
                    '%user_name%'                   => 'Dr. '.$first_name.' '.$last_name,
                    "%first_name%"                  => isset($value['first_name']) ? $value['first_name'] : '',
                    "%last_name%"                   => isset($value['last_name']) ? $value['last_name'] : '',
                    '%phone_no%'                    => isset($value['phone_no']) ? $value['phone_no'] : '',
                    '%phone_country_code%'          => isset($value['phone_country_code']) ? $value['phone_country_code'] : '',
                    '%address%'                     => isset($value['address']) ? $value['address'] : '',
                    '%type_of_doctors_str%'         => $type_of_doctors_str,
                    '%gender%'                      => $value['gender'] == 'n' ? '-' : ucfirst($value['gender']),
                    "%email_address%"               => $value['email'],
                    '%specialties_str%'             => $specialties_str,
                    '%practicing_since%'            => $practicing_since,
                    '%consultation_fees%'           => $consultation_fees,
                    '%time_slot_html%'              => $time_slot_html,
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

    public function get_user_info() {

	$id = isset($this->reqData['id']) ? decryptIt($this->reqData['id']) : 0;

        $html = '';

        $query = "SELECT  u.*,CONCAT(u.first_name,' ',u.last_name) AS user_name
        FROM tbl_users AS u
        WHERE u.id = ".(int) $id." AND user_type = 'doctor' ORDER BY u.id DESC ";

        $userInfo = $this->db->pdoQuery($query)->result();

        $id             = isset($userInfo['id']) ? $userInfo['id'] : 0;
        $user_type      = isset($userInfo['user_type']) ? $userInfo['user_type'] : '';
        $first_name     = isset($userInfo['first_name']) ? $userInfo['first_name'] : '';
        $last_name      = isset($userInfo['last_name']) ? $userInfo['last_name'] : '';

        $type_of_doctors = $this->db->pdoQuery("
            SELECT ud.type_of_doctor_id,d.name
            FROM tbl_users_doctor_type AS ud
            LEFT JOIN tbl_type_of_doctors AS d ON d.id = ud.type_of_doctor_id
            WHERE ud.user_id = ".(int) $id."
            ORDER BY ud.id ASC ")->results();
        $type_of_doctors_str = '';
        if (count($type_of_doctors) > 0) {
            foreach ($type_of_doctors as $key => $v2) {
                $type_of_doctors_str.= '<li class="tag">'.$v2['name'].'</li>';
            }
        }

        $specialties = $this->db->pdoQuery("
            SELECT ud.specialties_id,d.name
            FROM tbl_users_specialties AS ud
            LEFT JOIN tbl_specialties AS d ON d.id = ud.specialties_id
            WHERE ud.user_id = ".$id."
            ORDER BY ud.id ASC ")->results();

        $specialties_str = '';
        if (count($specialties) > 0) {
            foreach ($specialties as $key => $v1) {
                $specialties_str.= '<li class="tag">'.$v1['name'].'</li>';
            }
        }

        $consultation_fees = isset($userInfo['consultation_fees']) ? $userInfo['consultation_fees'] : '';
        $practicing_since = isset($userInfo['practicing_since']) && $userInfo['practicing_since'] != '' ? date("m-Y", strtotime($userInfo['practicing_since'])) : '';

        $companies_ul_tpl = new MainTemplater(DIR_TMPL . $this->module . "/info_modal_html-nct.tpl.php");
        $single_company_li_tpl_parsed = $companies_ul_tpl->parse();

        $time_slot_html = get_time_slot_table($userInfo['id']);

        $replace = array(
            "%id%"                          => encryptIt($userInfo['id']),
            "%uploaded_image%"              => get_image_url($userInfo['profile_photo'],"profile_photo",'th2_'),
            '%user_name%'                   => 'Dr. '.$first_name.' '.$last_name,
            "%first_name%"                  => isset($userInfo['first_name']) ? $userInfo['first_name'] : '',
            "%last_name%"                   => isset($userInfo['last_name']) ? $userInfo['last_name'] : '',
            '%phone_no%'                    => isset($userInfo['phone_no']) ? $userInfo['phone_no'] : '',
            '%phone_country_code%'          => isset($userInfo['phone_country_code']) ? $userInfo['phone_country_code'] : '',
            '%address%'                     => isset($userInfo['address']) ? $userInfo['address'] : '',
            '%type_of_doctors_str%'         => $type_of_doctors_str,
            '%gender%'                      => $userInfo['gender'] == 'n' ? '-' : ucfirst($userInfo['gender']),
	    '%doctor_description%'	    => isset($userInfo['doctor_description']) && $userInfo['doctor_description'] != '' ? $userInfo['doctor_description'] : '',
            "%email_address%"               => $userInfo['email'],
            '%specialties_str%'             => $specialties_str,
            '%practicing_since%'            => $practicing_since,
            '%consultation_fees%'           => $consultation_fees,
            '%time_slot_html%'              => $time_slot_html,
            '%associated_himself_text%'     => $userInfo['associated_to_existing_clinic'] == 'y' ? '<li class="profile-cell">
        This doctor is associated with this clinic
    </li>' : '',
        );

        $html .= str_replace(array_keys($replace), array_values($replace), $single_company_li_tpl_parsed);

        return $html;

    }
}
