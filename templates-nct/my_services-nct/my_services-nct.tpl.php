<div class="dashboard-content">
    <div class="container-fluid">

        <div class="card shadow-sm mb-5">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">My Services</h4>

                    <a href="my-services?add=1" class="btn btn-ehw-green">
                        <i class="bi bi-plus-circle me-1"></i>
                        Add Service
                    </a>

		    <div class="d-flex gap-2">
			%TRASH_BUTTON%
		    </div>
                </div>

		<div class="row mb-3">

			<div class="col-md-4">
				<input type="text" name="search" id="serviceSearch" class="form-control" placeholder="Search service name..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>" autocomplete="off">
			</div>

			<div class="col-md-3">
				<select name="category" id="categoryFilter" class="form-control">%CATEGORY_OPTIONS%</select>
			</div>

		</div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="servicesTable">
                        <thead class="table-light">
                            <tr>
                                <th>Service Name</th>
                                <th>Category</th>
                                <th>Price (₹)</th>
                                <th>Type</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            %SERVICE_ROWS%
                        </tbody>
                    </table>
                </div>
	    <div class="mt-4">
		%PAGINATION%
	    </div>
            </div>
        </div>

    </div>

    <script>
	function confirmDelete(id) {
    		if(confirm("Are you sure you want to move this service to Trash?")) {
        		window.location.href = "my-services?delete=" + id;
    		}
	}
</script>
</div>

<script>

document.addEventListener("DOMContentLoaded", function(){

    const searchBox = document.getElementById("serviceSearch");
    const categoryFilter = document.getElementById("categoryFilter");
    const tableBody = document.querySelector("#servicesTable tbody");

    let debounceTimer;

    function runSearch(){

        const keyword = searchBox.value.trim();
        const category = categoryFilter.value;

        let url = "my-services?ajaxSearch=1";

        if(keyword !== ""){
            url += "&search=" + encodeURIComponent(keyword);
        }

        if(category !== ""){
            url += "&category=" + encodeURIComponent(category);
        }

        fetch(url)
        .then(response => response.text())
        .then(data => {
            tableBody.innerHTML = data;
        });

    }

    // Text search
    searchBox.addEventListener("keyup", function(){

        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(function(){
            runSearch();
        }, 300);

    });

    // Category dropdown
    categoryFilter.addEventListener("change", function(){
        runSearch();
    });

});

</script>
