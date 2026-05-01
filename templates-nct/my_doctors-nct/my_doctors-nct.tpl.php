
<main class="flex-shrink-0 inner-main">

    <section class="apoiment-list gray-bg pt-0 my-doctors">
        <div class="container">
            <div class="inner-spacer">

                <div class="slot-block">
                    <div class="common-white-box table-box">
                        <div class="inner-title">
                            <h1>My Doctors</h1>
                            <a href="{SITE_ADD_DOCTORS}" class="medium-btn-border btn"><i class="fa-solid fa-plus"></i> Add Doctor</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table common-table %hide_if_no_data%">
                                <thead>
                                    <tr>
                                        <th scope="col">Full Name</th>
                                        <th scope="col">Phone No.</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Gender</th>
                                        <th scope="col">Practicing since</th>
                                        <th scope="col">Type of Professional</th>
                                        <th>Specialties</th>
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


<div id="view_info_modal" class="modal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Doctors Info</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="user_info_container">

            </div>
            <div class="modal-footer">
                <button type="button" class="medium-border-btn sm-btn" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>