<div class="container">

<div class="billing-summary">

    <div class="summary-box total-box">
        <div class="summary-title">Total Amount Collected (Last 30 Days)</div>
        <div class="summary-value">₹%TOTAL_COLLECTED%</div>
    </div>

    <div class="summary-box today-box">
        <div class="summary-title">Today's Collection</div>
        <div class="summary-value">₹%TODAY_COLLECTION%</div>
    </div>

    <div class="summary-box due-box">
        <div class="summary-title">Total Due</div>
        <div class="summary-value">₹%TOTAL_DUE%</div>

	<button onclick="window.location.href='?view_due=1'" class="due-view-btn">
		View
	</button>
    </div>

</div>

<h2>Billing History</h2>

<form method="GET" style="margin-bottom:20px;">

    <div style="text-align:right; margin-bottom:15px;">
    	<button type="button" onclick="window.location.href='?view_trash=1'" class="btn btn-danger">
        	Trash
    	</button>
    </div>

    <input type="text" name="search" placeholder="Search Bill ID, Patient, Doctor or Referred By"
           value="%SEARCH%" style="padding:6px;width:250px;">

    <input type="date" name="date"
           value="%DATE%" style="padding:6px;">

</form>

<table class="table table-bordered" id="billingTable">

<thead>
<tr>
%TABLE_HEADER%
</tr>
</thead>

<tbody>

%ROWS%

</tbody>

<!-- DELETE BILL MODAL -->
<div id="deleteBillModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">

    <div style="background:#fff; width:400px; margin:100px auto; padding:20px; border-radius:10px;">

        <h4>Cancel Bill</h4>

        <label>Select Reason:</label>
        <select id="delete_reason" class="form-control">
            <option value="">Select reason</option>
            <option value="Wrong entry">Wrong entry</option>
            <option value="Duplicate bill">Duplicate bill</option>
            <option value="Patient cancelled">Patient cancelled</option>
            <option value="Other">Other</option>
        </select>

        <br>

        <label>Type CONFIRM to proceed:</label>
        <input type="text" id="confirm_text" class="form-control" placeholder="Type CONFIRM">

        <br>

        <div style="text-align:right;">
            <button id="cancelDeleteBtn" class="btn btn-secondary">Cancel</button>
            <button id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
        </div>

    </div>

</div>

</table>
%PAGINATION%
</div>

<script>
var SITE_URL = "<?php echo SITE_URL; ?>";
</script>

<script>

document.addEventListener("DOMContentLoaded", function(){

    const searchBox = document.querySelector("input[name='search']");
    const dateFilter = document.querySelector("input[name='date']");
    const tableBody = document.querySelector("#billingTable tbody");

    let debounceTimer;

    function runSearch(){

        const keyword = searchBox.value.trim();
        const date = dateFilter.value;

	let url = "/modules-nct/manage_bills-nct/?ajaxSearch=1";

        if(keyword !== ""){
            url += "&search=" + encodeURIComponent(keyword);
        }

        if(date !== ""){
            url += "&date=" + encodeURIComponent(date);
        }

        fetch(url)
        .then(response => response.text())
        .then(data => {
            tableBody.innerHTML = data;
        });

    }

    searchBox.addEventListener("keyup", function(){

        clearTimeout(debounceTimer);

        debounceTimer = setTimeout(function(){
            runSearch();
        }, 300);

    });

    dateFilter.addEventListener("change", function(){
        runSearch();
    });

});

</script>


<style>

.billing-summary{
    display:flex;
    gap:20px;
    margin-bottom:25px;
}

.summary-box{
    flex:1;
    padding:18px;
    border-radius:8px;
    color:white;
}

.summary-title{
    font-size:14px;
    opacity:0.9;
}

.summary-value{
    font-size:24px;
    font-weight:bold;
    margin-top:6px;
}

.total-box{
    background:#3a3a3a;
}

.today-box{
    background:#1e8e3e;
}

.due-box{
    background:#c62828;
}

.due-view-btn{
    margin-top:10px;
    padding:6px 14px;
    border:none;
    background:white;
    color:#c62828;
    border-radius:4px;
    cursor:pointer;
    font-size:13px;
}
</style>

<script>
let selectedBillId = null;

document.addEventListener("click", function(e){

    let target = e.target.closest(".delete-bill");

    if(target){

        selectedBillId = target.getAttribute("data-id");

        // OPEN MODAL
        document.getElementById("deleteBillModal").style.display = "block";
    }

});
</script>

<script>
// CLOSE MODAL
document.getElementById("cancelDeleteBtn").onclick = function(){
    document.getElementById("deleteBillModal").style.display = "none";
};

// CONFIRM DELETE
document.getElementById("confirmDeleteBtn").onclick = function(){

    let reason = document.getElementById("delete_reason").value;
    let confirmText = document.getElementById("confirm_text").value;

    if(reason === ""){
        alert("Please select a reason");
        return;
    }

    if(confirmText !== "CONFIRM"){
        alert("Please type CONFIRM correctly");
        return;
    }

    fetch(SITE_URL + "modules-nct/manage_bills-nct/ajax.delete_bill.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "action=delete_bill&bill_id=" + selectedBillId + "&reason=" + encodeURIComponent(reason)
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);

        if(data.status){
            location.reload();
        }
    })
    .catch(err => {
        console.error(err);
        alert("Error occurred");
    });

};
</script>
