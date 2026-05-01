<?php

class Dashboard
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
    }

    public function getPageContent(){

        $data1                                  = $this->getMyAppointmentList('past',$this->sessUserId, 1);
        $my_past_appointment_list_html          = isset($data1['content']) ? $data1['content'] : '';
        $my_past_appointments_total_pages       = isset($data1['pagination']['total_pages']) ? $data1['pagination']['total_pages'] : 0;
        $total_past_records                     = isset($data1['totalRows']) ? (int) $data1['totalRows'] : 0;


        $data                               	= $this->getMyAppointmentList('upcoming',$this->sessUserId, 1);
        $my_appointment_list_html               = isset($data['content']) ? $data['content'] : '';
        $my_appointments_total_pages            = isset($data['pagination']['total_pages']) ? $data['pagination']['total_pages'] : 0;
        $total_upcoming_records               	= isset($data['totalRows']) ? (int) $data['totalRows'] : 0;

        $replace = array(
	   '%WELCOME_NAME%' 				=> $this->getWelcomeName(),
           '%my_appointment_list_html%'                 => $my_appointment_list_html,
            '%my_appointments_total_pages%'             => $my_appointments_total_pages,
            '%hide_when_no_data_in_upcoming%'           => $total_upcoming_records <= 0 ? 'hidden' : '',

            '%my_past_appointment_list_html%'           => $my_past_appointment_list_html,
            '%my_past_appointments_total_pages%'        => $my_past_appointments_total_pages,
            '%hide_when_no_data_in_past%'               => $total_past_records <= 0 ? 'hidden' : '',
        );

        return get_view(DIR_TMPL . $this->module . "/" . $this->module . ".tpl.php", $replace);
    }

    public function getWelcomeName(){

    	$user = $this->db->pdoQuery("
        	SELECT user_type, first_name, last_name, clinic_name
        	FROM tbl_users
        	WHERE id = '".(int)$this->sessUserId."'
    	")->result();

    	if($user['user_type'] == 'clinic'){
        	return $user['clinic_name'];
    	}

    	if($user['user_type'] == 'doctor'){
        	return "Dr. ".$user['first_name']." ".$user['last_name'];
    	}

    	return $user['first_name']." ".$user['last_name'];
    }

    public function getMyAppointmentList($data_type = 'upcoming',$user_id = 0, $currentPage = 1, $getPagination = true) {

        $array = array();
        $final_result_array = array();
        $final_result_html = $my_posted_job_html = NULL;
        $totalRows = $showableRows = 0;

        $limit = 8;
        $limit          = isset($this->reqData['per_page_limit']) && $this->reqData['per_page_limit'] > 0 ? $this->reqData['per_page_limit'] : $limit;
        $keyword        = isset($this->reqData['keyword']) ? $this->reqData['keyword'] : '';
        $search_date    = isset($this->reqData['search_date']) && $this->reqData['search_date'] != '' ? $this->reqData['search_date'] : '';


        $offset = ($currentPage - 1 ) * $limit;


        $where = '';

        if($getPagination == true){
            $limit_query = " LIMIT " . $limit . " OFFSET " . $offset;
        }

        if ($keyword != '') {
            $where.= " AND LOWER(CONCAT(a.first_name,' ',a.last_name)) LIKE '%".strtolower($keyword)."%' ";
        }
        if ($search_date != '') {
            $where.= " AND a.booking_date = '".$search_date."' ";
        }

        if ($data_type == 'past') {
            $where.= " AND a.booking_date < '".date("Y-m-d")."' ";
        } else{
            $where.= " AND a.booking_date >= '".date("Y-m-d")."' ";
        }

	$data_selection_query = "SELECT  a.*,CONCAT(a.first_name,' ',a.last_name) AS user_name,c.parent_id,CONCAT(c.first_name,' ',c.last_name) AS doctor_name ";
        $count_selection_query = "SELECT count(a.id) as no_of_appointments ";

        $query = " FROM tbl_appointment AS a
		LEFT JOIN tbl_users AS c ON c.id = a.doctor_id
		WHERE (a.doctor_id = '".(int) $user_id."' OR c.parent_id = '".(int) $user_id."')
		AND a.is_active = 'y'
		AND a.first_name != ''
		".$where." ";

	if ($data_type == 'past') {
    		$query .= " ORDER BY a.booking_date DESC, a.from_time DESC, a.id DESC ";
	} else {
    		$query .= " ORDER BY a.booking_date ASC, a.from_time ASC, a.id ASC ";
	}

        $getAllResults = $this->db->pdoQuery($count_selection_query . $query)->result();
        $totalRows = $getAllResults['no_of_appointments'];
        $getShowableResults = $this->db->pdoQuery($data_selection_query . $query . $limit_query)->results();

        if (count($getShowableResults) > 0) {
            $showableRows = count($getShowableResults);

            $companies_ul_tpl = new MainTemplater(DIR_TMPL . $this->module . "/items_rows-nct.tpl.php");
            $single_company_li_tpl_parsed = $companies_ul_tpl->parse();

            $groupedData = [];

            foreach ($getShowableResults as $row) {
                if ($data_type == 'past') {
                    // Month wise key (2025-11)
                    $monthKey = date('Y-m', strtotime($row['booking_date']));
                    $groupedData[$monthKey][] = $row;
                } else {
                    // Date wise key (2025-12-26)
                    $groupedData[$row['booking_date']][] = $row;
                }
            }

            if ($data_type == 'upcoming') {
                // ASC order
                ksort($groupedData);
            }

            foreach ($groupedData as $booking_date => $appointments) {
                if ($data_type == 'past') {

                    $dateHeading = date('M, Y', strtotime($booking_date . '-01'));
                    $my_posted_job_html.= '
                    <div class="slot-block">
                    <div class="row">
                    <div class="col-xl-12 col-lg-12">
                    <h2 class="section-title">
                    '.$dateHeading.'
                    </h2>
                    </div>
                    <div class="col-xl-12 col-lg-12">
                    <div class="row row-cols-1 row-cols-xl-2 row-cols-lg-2 row-cols-md-2 g-3">';
                } else{
                    $dateHeading = convertDate($booking_date, false, 'elevate_health');
                    $my_posted_job_html.= '
                    <div class="slot-block">
                    <div class="row">
                    <div class="col-xl-3 col-lg-4">
                    <h2 class="section-title">
                    '.$dateHeading.'
                    </h2>
                    </div>
                    <div class="col-xl-9 col-lg-8">
                    <div class="row row-cols-1 row-cols-xl-2 row-cols-lg-2 row-cols-md-2 g-3">';
                }

                foreach ($appointments as $value) {

		    $bill = $this->db->pdoQuery("
    			SELECT id
    			FROM tbl_bills
    			WHERE appointment_id = '".(int)$value['id']."'
    			LIMIT 1
		    ")->result();

		    $no_show = false;

		    if($data_type == 'past'){

    			if(empty($bill['id'])){
        		$no_show = true;
    		    	}
		    }

		    $status_badge = '';

		    if($no_show){
    			$status_badge = '<span class="badge bg-danger ms-2">No Show</span>';
		    }

                    $age = '';

		    if ($value['date_of_birth'] != '' && $value['date_of_birth'] != '0000-00-00') {

    			$dob = new DateTime($value['date_of_birth']);
    			$now = new DateTime();

			$dob -> setTime(0,0,0);
			$now -> setTime(0,0,0);

    			$diff = $now->diff($dob);

    			if ($diff->y > 0) {
        			$age = $diff->y . 'Y';
    			} elseif ($diff->m > 0) {
        			$age = $diff->m . 'M';
    			} else {
        			$age = $diff->d . 'D';
    			}
		    }

                    $date_time_slot = $value['from_time'].' to '.$value['to_time'];
                    if ($data_type == 'past') {
                        $date_time_slot = date('jS M', strtotime($value['booking_date'])).', '.$date_time_slot;
                    }

                    $doctor_name_html = '';
                    if ($this->sessUserType == 'clinic') {
                        $doctor_name_html = '
                        <div class="pt-info pt-doctor-name">
                        <div class="info-cell">
                        <span class="pt-icon">Doctor:</span>Dr. '.$value['doctor_name'].'
                        </div>
                        </div>';
                    }

		    $action_buttons = '<div style="margin-top:10px; display:flex; gap:10px;">';

		    $action_buttons .= '<button class="btn ehw-btn-secondary w-100 view-history-btn" data-id="'.$value['id'].'">View History</button>';

			if($data_type == 'upcoming'){

    				if($this->sessUserType == 'clinic'){
        				$action_buttons .= '<button class="btn ehw-btn-primary w-100 generate-bill-btn" data-id="'.$value['id'].'">Generate Bill</button>';
				}

    				else if($this->sessUserType == 'doctor'){
        				$action_buttons .= '<button class="btn ehw-btn-primary w-100 write-prescription-btn" data-id="'.$value['id'].'">Write Prescription</button>';
    				}
			}

		    $action_buttons .= '</div>';

                    $replace = array(
                        '%ID%'                      => $value['id'],
                        '%user_name%'               => $value['user_name'].' '.$status_badge,
                        '%time%'                    => $date_time_slot,
                        '%gender%'                  => ucfirst($value['gender']),
                        '%age%'                     => $age,
                        '%case_type%'               => get_case_type($value['case_type']),
                        '%doctor_name_html%'        => $doctor_name_html,
			'%ACTION_BUTTONS%'    	    => $action_buttons
                    );

                    $my_posted_job_html .= str_replace(array_keys($replace), array_values($replace), $single_company_li_tpl_parsed);
                }

                $my_posted_job_html.= '</div>
                </div>
                </div>
                </div>';
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
}
