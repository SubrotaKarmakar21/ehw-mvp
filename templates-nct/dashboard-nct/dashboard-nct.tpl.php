<main class="flex-shrink-0 inner-main">

    <div class="container">
        <div class="dashboard-welcome">
        	Welcome %WELCOME_NAME%
       	</div>
    </div>

    <section class="apoiment-list gray-bg pt-0">

	<div class="container">
	</div>

        <ul class="nav nav-tabs common-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane"
                type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Upcoming</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Past</button>
            </li>

        </ul>

        <div class="tab-content list-container" id="myTabContent">
            <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                <div class="container">
                    <div class="tab-filter %hide_when_no_data_in_upcoming%">
                        <div class="row tab-filter-row">
                            <div class="col-lg-9 col-xl-10">
                                <div class="row">
                            <div class="col-lg-4 col-md-5">
                                <div class="form-group tab-search">
                                    <input type="text" name="upcoming-keyword" id="upcoming-keyword" class="form-control" placeholder="Search">
                                    <button type="button" class="tab-search-btn" id="search-keyword-upcoming">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-5">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="upcoming-search-date" placeholder="yyyy-mm-dd" readonly>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-2 upcoming_clear_seach_container hidden">
                                <div class="form-group">
                                    <button type="button" id="removed_upcoming_search_data"  class="btn btn-link">Clear Search</button>
                                </div>
                            </div>
                        </div>
                            </div>
                            <div class="col-lg-3 col-xl-2 text-end">
                                <div class=" %hide_when_no_data_in_upcoming% form-group" id="my_appointment_excel">
                        <a href="{SITE_URL}ajax-dashboard-nct?action=export_excel&data_type=upcoming" class="btn sm-btn">
                          Export Data
                        </a>
                    </div>
                            </div>
                        </div>
                    </div>

                    <div id="my_appointment_list_container">
                        %my_appointment_list_html%
                    </div>

                    <div id="my_appointments_paging" class="paging-bottom pagination-main mt-md-4 mt-2"></div>
                    <input type="hidden" name="my_appointments_total_pages" id="my_appointments_total_pages" value="%my_appointments_total_pages%">
                    <input type="hidden" name="my_appointments_page_val" id="my_appointments_page_val" value="1">
                </div>
            </div>
            <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                <div class="container">
                    <div class="tab-filter %hide_when_no_data_in_past%">
                        <div class="row tab-filter-row">
                             <div class="col-lg-9 col-xl-10">
                                 <div class="row">
                                     <div class="col-lg-4 col-md-5">
                                <div class="form-group tab-search">
                                    <input type="text" name="past-keyword" id="past-keyword" class="form-control" placeholder="Search">
                                    <button type="button" class="tab-search-btn" id="search-keyword-past">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-5">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="past-search-date" placeholder="yyyy-mm-dd" readonly>
                                </div>
                            </div>

                            <div class="col-lg-3 col-md-2 past_clear_seach_container hidden">
                                <div class="form-group">
                                    <button type="button" id="removed_past_search_data" class="btn btn-link">Clear Search</button>
                                </div>
                            </div>
                                 </div>
                             </div>
                             <div class="col-lg-3 col-xl-2 text-end">
                                  <div class="%hide_when_no_data_in_past% form-group" id="my_past_appointment_excel">
                        <a href="{SITE_URL}ajax-dashboard-nct?action=export_excel&data_type=past" class="btn sm-btn">
                            Export Data
                        </a>
                    </div>
                             </div>
                        </div>
                    </div>

                    <div id="my_past_appointment_list_container">
                        %my_past_appointment_list_html%
                    </div>

                    <div id="my_past_appointments_paging" class="paging-bottom pagination-main mt-md-4 mt-2"></div>
                    <input type="hidden" name="my_past_appointments_total_pages" id="my_past_appointments_total_pages" value="%my_past_appointments_total_pages%">
                    <input type="hidden" name="my_past_appointments_page_val" id="my_past_appointments_page_val" value="1">
                </div>
            </div>
        </div>
    </section>
</main>

<script src="{SITE_JS}jquery.min.js"></script>

<script>
var SITE_URL = "<?php echo SITE_URL; ?>";
</script>

<script>
$(document).on("click",".generate-bill-btn",function(){

    var appointment_id = $(this).data("id");

    console.log("Clicked:", appointment_id);

    window.location.href = SITE_URL + "billing?appointment_id=" + appointment_id;

});
</script>

<script>
$(document).on("click", ".write-prescription-btn", function(){
    var appointment_id = $(this).data("id");

    window.location.href = SITE_URL + "modules-nct/prescription-nct/index.php?appointment_id=" + appointment_id;
});
</script>
