<?php

/*
error_reporting(E_ALL & -E_DEPRECATED & -E_USER_DEPRECATED);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
*/

class MyServices {

    public function __construct($module = "", $reqData = array()){
    	foreach ($GLOBALS as $key => $values) {
        	$this->$key = $values;
    	}

    	$this->module   = $module;
    	$this->reqData  = $reqData;
    	$this->userData = getUserData();

    	$this->dataOnly = (isset($this->reqData['dataOnly']) && $this->reqData['dataOnly']==true)?true:false;
	$this->sessUserId = isset($_SESSION['sessUserId']) ? $_SESSION['sessUserId'] : 0;
    }

    /* ================================
       LIST SERVICES
    ================================= */
    public function getServicesList() {

        $clinic_id = $this->sessUserId;

        $services = $this->db->pdoQuery("
            SELECT s.*, c.category_name
            FROM tbl_clinic_services s
            JOIN tbl_service_categories c ON c.id = s.category_id
            WHERE s.clinic_id = ? AND s.status = ? AND s.doctor_id IS NULL
            ORDER BY s.created_date DESC
        ", [$this->sessUserId,'a'])->results();

        return $services;
    }

    /* ================================
       ADD SERVICE
    ================================= */
    public function addService() {

        $clinic_id = $this->sessUserId;

        $insert = [
            "clinic_id"   => $clinic_id,
            "category_id" => $_POST['category_id'],
            "service_name"=> $_POST['service_name'],
            "price"       => $_POST['price'],
            "description" => $_POST['description'],
            "status"      => 'a',
            "created_date"=> date("Y-m-d H:i:s")
        ];

        $this->db->insert("tbl_clinic_services", $insert);

        return true;
    }

    /* ================================
       UPDATE SERVICE
    ================================= */
    public function updateService($id) {

        $update = [
            "category_id" => $_POST['category_id'],
            "service_name"=> $_POST['service_name'],
            "price"       => $_POST['price'],
            "description" => $_POST['description'],
            "updated_date"=> date("Y-m-d H:i:s")
        ];

        $this->db->update("tbl_clinic_services", $update, ["id" => $id]);

        return true;
    }

    /* ================================
       DELETE SERVICE (SOFT DELETE)
    ================================= */
    public function deleteService($id) {

        $this->db->update("tbl_clinic_services", [
            "status" => 'd'
        ], ["id" => $id,
	    "clinic_id" => $this->sessUserId
	]);

        return true;
    }

    /* ================================
       PAGE OUTPUT
    ================================= */
    public function getPageContent(){
        $isTrash = isset($_GET['trash']) ? true : false;
        $statusCondition = $isTrash ? 'd' : 'a';

	$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
	$search = isset($_GET['search']) ? trim($_GET['search']) : '';
	$category = isset($_GET['category']) ? trim($_GET['category']) : '';
	$limit = 20;
	$offset = ($page - 1) * $limit;

	// If Add Service page requested

	if ($isTrash) {

    		$services = $this->db->pdoQuery("
    			SELECT s.*, c.category_name
    			FROM tbl_clinic_services s
    			JOIN tbl_service_categories c ON c.id = s.category_id
    			WHERE s.clinic_id = ?
    			AND s.status = ?
    			AND s.doctor_id IS NULL
    			ORDER BY s.created_date DESC
    			LIMIT $limit OFFSET $offset
		", [$this->sessUserId, $statusCondition])->results();

	} else {

    		$where = "WHERE s.clinic_id = ? AND s.status = ?";
		$params = [$this->sessUserId, $statusCondition];

		if($search != ''){
    			$where .= " AND s.service_name LIKE ?";
    			$params[] = "%".$search."%";
		}

		if($category != ''){
    			$where .= " AND c.category_name = ?";
    			$params[] = $category;
		}

		$services = $this->db->pdoQuery("
			SELECT s.*, c.category_name
			FROM tbl_clinic_services s
			JOIN tbl_service_categories c ON c.id = s.category_id
			$where
			ORDER BY s.created_date DESC
			LIMIT $limit OFFSET $offset
		", $params)->results();
	}

	$countWhere = "WHERE s.clinic_id = ? AND s.status = ?";
	$countParams = [$this->sessUserId, $statusCondition];

	if($search != ''){
        	$countWhere .= " AND s.service_name LIKE ?";
        	$countParams[] = "%".$search."%";
	}

	if($category != ''){
        	$countWhere .= " AND c.category_name = ?";
        	$countParams[] = $category;
	}

	$totalCount = $this->db->pdoQuery("
        	SELECT COUNT(*) as total
        	FROM tbl_clinic_services s
        	JOIN tbl_service_categories c ON c.id = s.category_id
        	$countWhere
	", $countParams)->result();

	$totalPages = ceil($totalCount['total'] / $limit);

    	$rows = '';

    	foreach($services as $s){
		$typeBadge = ($s['doctor_id'] != NULL)
    		? "<span class='badge bg-light text-primary border'>Consultation</span>"
    		: "<span class='badge bg-light text-dark border'>General</span>";

		if($isTrash){

    			$action = "
        			<div class='d-flex justify-content-end'>
            			<a href='?restore=".$s['id']."'
               				class='btn btn-sm btn-success'>
               				RESTORE
            			</a>
        		</div>
    			";

		}
		else if($s['doctor_id'] != NULL){

    			$action = "
        			<span class='text-muted d-flex align-items-center justify-content-end'>
            				<i class='bi bi-shield-lock me-1'></i>
            				To be managed from My Doctors
        			</span>
    			";

		}
		else {

    			$action = "
        			<div class='d-flex justify-content-end gap-2'>

            				<a href='?edit=".$s['id']."'
                				class='btn btn-sm btn-primary'>
                				EDIT
            				</a>

            				<a href='javascript:void(0);'
                				class='btn btn-sm btn-danger'
                				onclick='confirmDelete(".$s['id'].")'>
                				DELETE
            				</a>

        			</div>
    				";
			}

		$rows .= "
			<tr data-category='".$s['category_name']."'>
        			<td class='fw-semibold'>".$s['service_name']."</td>
        			<td>".$s['category_name']."</td>
        			<td class='fw-semibold'>₹ ".$s['price']."</td>
        			<td>".$typeBadge."</td>
        			<td class='text-end'>".$action."</td>
			</tr>
			";
    	}

	$pagination = '<nav><ul class="pagination justify-content-center mt-4">';

	for ($i = 1; $i <= $totalPages; $i++) {
    		$active = ($i == $page) ? 'active' : '';
		$url = $isTrash ? "my-services?trash=1&page=$i&search=".$search."&category=".$category : "my-services?page=$i&search=".$search."&category=".$category;
    		$pagination .= "
        		<li class='page-item $active'>
            			<a class='page-link' href='$url'>$i</a>
        		</li>
    		";
	}

	$pagination .= '</ul></nav>';

	$categories = $this->db->pdoQuery("
        	SELECT DISTINCT category_name
        	FROM tbl_service_categories
        	ORDER BY category_name ASC
	")->results();

	$categoryOptions = "<option value=''>Select a Category</option>";

	foreach($categories as $cat){
        	$categoryOptions .= "<option value='".$cat['category_name']."'>".$cat['category_name']."</option>";
	}

	$replace = array(
    		'%SERVICE_ROWS%' => $rows,
    		'%TRASH_BUTTON%' => $isTrash
        	? '<a href="my-services" class="btn btn-outline-primary btn-sm">Back</a>'
        	: '<a href="my-services?trash=1" class="btn btn-outline-secondary btn-sm">View Trash</a>',
    		'%PAGINATION%' => $pagination,
		'%CATEGORY_OPTIONS%' => $categoryOptions
	);

    	return get_view(DIR_TMPL . $this->module . "/my_services-nct.tpl.php", $replace);

    }

    /* ================================
       FORM
    ================================= */
    public function renderForm(){

    	$categories = $this->db->pdoQuery("
        	SELECT id, category_name
        	FROM tbl_service_categories
        	WHERE category_name != 'Consultation'
    	")->results();

    	$options = '';

    	foreach($categories as $cat){
        	$options .= "<option value='".$cat['id']."'>".$cat['category_name']."</option>";
    	}

    	return "
    		<div class='dashboard-content'>
        		<div class='container-fluid'>

            			<div class='card shadow-sm'>
                			<div class='card-body'>

                				<div class='d-flex justify-content-between align-items-center mb-4'>
                        				<h4 class='mb-0'>Add New Service</h4>
                        				<a href='my-services' class='btn btn-light'>Back</a>
                				</div>

                				<form method='post'>

                					<div class='mb-3'>
                						<label class='form-label'>Service Name</label>
                        					<input type='text' name='service_name' class='form-control' required>
                					</div>

                					<div class='mb-3'>
                						<label class='form-label'>Category</label>
                        					<select name='category_id' class='form-control' required>
                        						".$options."
                        					</select>
                					</div>

                					<div class='mb-3'>
                						<label class='form-label'>Price (₹)</label>
                        					<input type='number' name='price' step='0.01' class='form-control' required>
                					</div>

                					<div class='mb-3'>
                						<label class='form-label'>Description</label>
                        					<textarea name='description' class='form-control'></textarea>
                					</div>

                					<button type='submit' name='submit_service' class='btn btn-ehw-green'>
                						Save
                					</button>

						</form>

					</div>
            			</div>

        		</div>
    		</div>
    	";
    }

    public function getEditForm($id){

    	$service = $this->db->pdoQuery("
        	SELECT *
        	FROM tbl_clinic_services
        	WHERE id = ?
        	AND clinic_id = ?
        	AND status = 'a'
    	", [$id, $this->sessUserId])->result();

    	if(empty($service)){
        	header("Location: " . SITE_URL . "my-services");
        	exit;
    	}

    	// Load categories again
    	$categories = $this->db->pdoQuery("
        	SELECT id, category_name
        	FROM tbl_service_categories
        	WHERE category_name != 'Consultation'
    	")->results();

    	$options = '';

    	foreach($categories as $cat){
        	$selected = ($cat['id'] == $service['category_id']) ? "selected" : "";
        	$options .= "<option value='".$cat['id']."' ".$selected.">".$cat['category_name']."</option>";
    	}

    	return "
    		<div class='dashboard-content'>
        		<div class='container-fluid'>
            			<div class='card shadow-sm'>
                			<div class='card-body'>

                    				<div class='d-flex justify-content-between align-items-center mb-4'>
                        				<h4 class='mb-0'>Edit Service</h4>
                        				<a href='my-services' class='btn btn-light'>Back</a>
                    				</div>

                    				<form method='post'>

                        			<input type='hidden' name='edit_id' value='".$service['id']."'>

                        			<div class='mb-3'>
                            				<label class='form-label'>Service Name</label>
                            				<input type='text' name='service_name'
                                   			value='".$service['service_name']."'
                                   			class='form-control' required>
                        			</div>

                        			<div class='mb-3'>
                            				<label class='form-label'>Category</label>
                            				<select name='category_id' class='form-control' required>
                                				".$options."
                            				</select>
                        			</div>

                        			<div class='mb-3'>
                            				<label class='form-label'>Price (₹)</label>
                            				<input type='number' step='0.01'
                                   			name='price'
                                   			value='".$service['price']."'
                                   			class='form-control' required>
                        			</div>

                        			<div class='mb-3'>
                            				<label class='form-label'>Description</label>
                            				<textarea name='description' class='form-control'>".$service['description']."</textarea>
                        			</div>

                        			<button type='submit' name='update_service' class='btn btn-primary'>
                            				Update
                        			</button>

                    			</form>

                		</div>
            		</div>
        	</div>
    	</div>
    	";
    }

    public function restoreService($id) {

    	$this->db->update("tbl_clinic_services", [
        	"status" => 'a'
    	], ["id" => $id,
	    "clinic_id" => $this->sessUserId]);

    return true;
    }

    public function ajaxSearchServices(){

    	$search = isset($_GET['search']) ? trim($_GET['search']) : '';
    	$category = isset($_GET['category']) ? trim($_GET['category']) : '';

    	$where = "WHERE s.clinic_id = ? AND s.status = 'a'";
    	$params = [$this->sessUserId];

    	if($search != ''){
        	$where .= " AND s.service_name LIKE ?";
        	$params[] = "%".$search."%";
    	}

    	if($category != ''){
        	$where .= " AND c.category_name = ?";
        	$params[] = $category;
    	}

    	$services = $this->db->pdoQuery("
        	SELECT s.*, c.category_name
        	FROM tbl_clinic_services s
        	JOIN tbl_service_categories c ON c.id = s.category_id
        	$where
        	ORDER BY s.created_date DESC
        	LIMIT 50
    	", $params)->results();

    	$rows = "";

    	foreach($services as $s){

    		$typeBadge = ($s['doctor_id'] != NULL) ? "<span class='badge bg-light text-primary border'>Consultation</span>" : "<span class='badge bg-light text-dark border'>General</span>";

    		if($s['doctor_id'] != NULL){

        		$action = "
        			<span class='text-muted d-flex align-items-center justify-content-end'>
            				<i class='bi bi-shield-lock me-1'></i>
            				To be managed from My Doctors
        			</span>";

    			} else {

        			$action = "
        				<div class='d-flex justify-content-end gap-2'>

            					<a href='?edit=".$s['id']."'
               						class='btn btn-sm btn-primary'>
              						EDIT
            					</a>

            					<a href='javascript:void(0);'
               						class='btn btn-sm btn-danger' onclick='confirmDelete(".$s['id'].")'>
               						DELETE
            					</a>

        				</div>";
    			}

    			$rows .= "
    				<tr data-category='".$s['category_name']."'>
        				<td class='fw-semibold'>".$s['service_name']."</td>
        				<td>".$s['category_name']."</td>
        				<td class='fw-semibold'>₹ ".$s['price']."</td>
        				<td>".$typeBadge."</td>
        				<td class='text-end'>".$action."</td>
    				</tr>";
		}

    	echo $rows;
    	exit;
    }
}
