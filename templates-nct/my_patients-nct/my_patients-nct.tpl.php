<main class="flex-shrink-0 inner-main">

    <section class="apoiment-list gray-bg pt-0">
        <div class="container">
            <div class="inner-spacer">
                <div class="slot-block">
                    <div class="common-white-box table-box">
                        <div class="inner-title" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">

    				<h1>My Patients</h1>

    				<div style="display:flex;gap:10px;align-items:center;">

        				<input type="text"
        				id="patient_search"
        				class="form-control"
        				placeholder="Search by Name or Phone"
        				style="width:250px;height:40px;">

        				<a href="{SITE_ADD_PATIENTS}" class="medium-btn-border btn">
            					<i class="fa-solid fa-plus"></i> Add Patient
        				</a>

    				</div>

			</div>
                        <div class="table-responsive">
                            <table class="table common-table">
                                <thead class="%hide_if_no_data%">
                                    <tr>
                                        <th scope="col">Full Name</th>
                                        <th scope="col">Gender</th>
                                        <th scope="col">Age</th>
                                        <th scope="col">Phone No.</th>
                                        <th scope="col">Address</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="my_items_list_container">
                                    %my_items_list_html%
                                </tbody>
                            </table>
                        </div>
                        <div id="my_item_paging" class="paging-bottom pagination-main mt-md-4 mt-2"></div>
                        <input type="hidden" name="my_item_total_pages" id="my_item_total_pages" value="%my_item_total_pages%">
                        <input type="hidden" name="my_item_page_val" id="my_item_page_val" value="1">
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script type="text/javascript">
    var clinic_id = '%clinic_id%';
</script>

<script>
document.addEventListener("DOMContentLoaded", function(){

    const searchInput = document.getElementById("patient_search");
    const tableBody = document.querySelector("#my_items_list_container");

    let debounceTimer;

    searchInput.addEventListener("keyup", function(){

        clearTimeout(debounceTimer);

        const keyword = this.value.trim();

        debounceTimer = setTimeout(function(){

            fetch("/my-patients?ajaxSearch=1&keyword=" + encodeURIComponent(keyword))
            .then(response => response.text())
            .then(data => {
                tableBody.innerHTML = data;
            });

        },300);

    });

});
</script>

<div id="view_info_modal" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Patient's Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="user_info_container">

            </div>
            <div class="modal-footer" style="display:flex;justify-content:space-between;align-items:center;">

    		<button type="button" id="view-details-btn" class="btn btn-ehw-green">
        		View Details
    		</button>

    		<button type="button" class="medium-border-btn sm-btn" data-bs-dismiss="modal">
        		Close
    		</button>

</div>
        </div>
    </div>
</div>

<script>
document.addEventListener("click", function(e){

    // When modal opens
    if(e.target.closest(".open_view_info_modal")){
        const btn = e.target.closest(".open_view_info_modal");

        const patientId = btn.getAttribute("data-id");

        console.log("Selected Patient ID:", patientId); // DEBUG

        const viewBtn = document.getElementById("view-details-btn");

        if(viewBtn){
            viewBtn.setAttribute("data-id", patientId);
        }
    }

    // When View Details clicked
    if(e.target.id === "view-details-btn"){
        const patientId = e.target.getAttribute("data-id");

        console.log("Redirecting with ID:", patientId); // DEBUG

        if(patientId && !isNaN(patientId)){
            window.location.href = "/modules-nct/patient_details-nct/index.php?patient_id=" + patientId;
        } else {
            console.error("Invalid patient ID:", patientId);
        }
    }

});
</script>
