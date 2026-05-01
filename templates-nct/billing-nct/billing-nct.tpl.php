<div class="container-fluid">

        <h2 class="mb-4">Generate Bill</h2>

        <div class="card">
                <div class="card-body">

                        <input type="hidden" id="appointment_id" value="%APPOINTMENT_ID%">

                        <div class="row mb-4">

                                <div class="col-md-4 position-relative">
                                        <label>Patient</label>
                                        <input type="text" id="patient_search" class="form-control" placeholder="Search Patient" value="%PATIENT_NAME%">
                                        <input type="hidden" id="patient_id">
                                        <div id="patient_results" class="list-group"></div>
                                </div>

                                <div class="row mt-3">
                                        <div class="col-md-4">
                                                <label>Phone Number</label>
                                                <input type="text" id="patient_phone" class="form-control" value="%PATIENT_PHONE%">
                                        </div>

                                        <div class="col-md-4">
                                                <label>Age</label>

                                                <div style="display:flex; gap:10px;">

                                                        <input type="number" id="patient_age" class="form-control" placeholder="Enter Age" style="flex:2;" value="%PATIENT_AGE%">

                                                        <select id="age_type" class="form-control" style="flex:1;">
                                                                <option value="years" %AGE_TYPE_YEARS%>Years</option>
                                                                <option value="months" %AGE_TYPE_MONTHS%>Months</option>
                                                                <option value="days" %AGE_TYPE_DAYS%>Days</option>
                                                        </select>

                                                </div>
                                        </div>

                                        <div class="col-md-4">
                                                <label>Gender</label>
                                                <select id="patient_gender" class="form-control">
                                                        <option value="">Select Gender</option>
                                                        <option value="male" %PATIENT_GENDER_MALE%>Male</option>
                                                        <option value="female" %PATIENT_GENDER_FEMALE%>Female</option>
                                                        <option value="other" %PATIENT_GENDER_OTHER%>Other</option>
                                                </select>
                                        </div>
                                </div>

                                <div class="col-md-4">
                                        <label>Doctor</label>
                                        <select class="form-control" id="doctor_select">
                                                <option value="">Select Doctor</option>
                                                %DOCTOR_OPTIONS%
                                        </select>
                                </div>

                                <div class="col-md-4">
                                        <label>Bill Date</label>
                                        <input type="date" class="form-control" id="bill_date" value="%BILL_DATE%">
                                </div>

                                <div class="col-md-4">
                                        <label>Recommended By Doctor</label>
                                        <input type="text" class="form-control" id="referred_doctor" placeholder="Doctor who recommended test (optional)">
                                </div>

                                <div class="col-md-4">
                                        <label>Promo Code</label>
                                        <input type="text" id="promo_code" class="form-control" placeholder="Enter promo code (optional)">
                                </div>

                        </div>

                        <table class="table table-bordered">
                                <thead>
                                        <tr>
                                                <th width="40%">Service</th>
                                                <th width="15%">Price</th>
                                                <th width="15%">Qty</th>
                                                <th width="20%">Total</th>
                                                <th width="10%">Action</th>
                                        </tr>
                                </thead>
                                <tbody id="billing_rows">
                                        <tr>
                                                <td class="position-relative">
                                                        <input type="text" class="form-control service-name" placeholder="Service Name">
                                                </td>

                                                <td>
                                                        <input type="number" class="form-control price">
                                                </td>

                                                <td>
                                                        <input type="number" value="1" class="form-control qty">
                                                </td>

                                                <td class="row-total">
                                                        ₹0
                                                </td>

                                                <td>
                                                        <button class="btn btn-danger btn-sm">X</button>
                                                </td>
                                        </tr>
                                </tbody>

                        </table>

                        <button type="button" class="btn btn-secondary mb-4 add-service">+ Add Service</button>

                        <div class="row">

                                <div class="col-md-8"></div>
                                <div class="col-md-4">
                                        <div class="card">
                                                <div class="card-body">

                                                        <div class="mb-2">
                                                                <h5>Subtotal: <span id="bill_subtotal">₹0</span></h5>
                                                        </div>

                                                        <div class="mb-2">
                                                                <label>Discount</label>
                                                                <input type="text" id="discount_input" class="form-control" placeholder="10% or 100">
                                                        </div>

                                                        <div class="mb-2">
                                                                <h4>Total: <span id="bill_total">₹0</span></h4>
                                                        </div>

                                                        <div class="payment-box">
                                                                <h4>Payment</h4>
                                                                <div>Paid: ₹<span id="bill_paid">0</span></div>
                                                                <div>Due: ₹<span id="bill_due">0</span></div>
                                                                <hr>

                                                                        <input type="number" id="payment_amount" class="form-control" placeholder="Enter amount">

                                                                        <select id="payment_method" class="form-control">
                                                                                <option value="Cash">Cash</option>
                                                                                <option value="UPI">UPI</option>
                                                                                <option value="Card">Card</option>
                                                                                <option value="Bank Transfer">Bank Transfer</option>
                                                                        </select>

                                                                        <button id="addPaymentBtn" class="btn btn-success">Add Payment</button>

                                                                <hr>

                                                                <div id="payment_history"></div>

                                                        </div>

                                                        <button type="button"  id="generateBillBtn" class="btn btn-ehw-green generate-bill-btn">Generate Bill</button>

                                                        <button type="button" id="updateBillBtn" class="btn btn-ehw-green" style="display:none;">Update Bill</button>

                                                </div>
                                        </div>

                                </div>

                        </div>

                </div>
        </div>

</div>

<script>
var SITE_URL = "<?php echo SITE_URL; ?>";
</script>

<script src = "{SITE_JS}jquery.min.js"></script>

<script>

var paymentMode = "%PAYMENT_MODE%";

if(paymentMode == 1){

        // lock all inputs
        $('input').prop('readonly', true);
        $('select').prop('disabled', true);

        // unlock payment fields
        $('#payment_amount').prop('readonly', false);
        $('#payment_method').prop('disabled', false);

        // enable add payment
        $('#addPaymentBtn').prop('disabled', false);

        // disable service editing
        $('.add-service').hide();

        // hide generate button
        $('#generateBillBtn').hide();

        // show update button
        $('#updateBillBtn').show();

        // disable delete buttons
        $('#billing_rows button').hide();

}

</script>

<script>

var billId = "%BILL_ID%";

if(billId > 0){

        $.get(SITE_URL + "modules-nct/billing-nct/ajax.load_bill.php?id="+billId,function(res){

                var data = JSON.parse(res);

                /* fill patient data */

                $('#patient_search').val(data.patient_name);
                $('#patient_phone').val(data.patient_phone);

                if(data.patient_dob){

                        let dob = new Date(data.patient_dob + "T00:00:00");
                        let now = new Date();

                        let diffDays = Math.floor((now - dob) / (1000 * 60 * 60 * 24));

                        if(diffDays < 30){
                                $('#age_type').val('days');
                                $('#patient_age').val(diffDays);
                        }
                        else if(diffDays < 365){
                                let months = Math.floor(diffDays / 30);
                                $('#age_type').val('months');
                                $('#patient_age').val(months);
                        }
                        else{
                                let years = Math.floor(diffDays / 365);
                                $('#age_type').val('years');
                                $('#patient_age').val(years);
                        }

                }

                $('#patient_gender').val(data.patient_gender);
                $('#doctor_select').val(data.doctor_id);
                $('#referred_doctor').val(data.referred_doctor);
                $('#promo_code').val(data.promo_code);
                $('#bill_date').val(data.bill_date);

                /* LOAD SERVICES */

                $('#billing_rows').html('');

                data.items.forEach(function(item){

                        var row = `
                                <tr>

                                        <td>
                                                <input type="text" class="form-control service-name" value="${item.service_name}" readonly>
                                        </td>

                                        <td>
                                                <input type="number" class="form-control price" value="${item.price}" readonly>
                                        </td>

                                        <td>
                                                <input type="number" class="form-control qty" value="${item.qty}" readonly>
                                        </td>

                                        <td class="row-total">
                                                ₹${item.total}
                                        </td>

                                        <td></td>

                                </tr>
                        `;

                        $('#billing_rows').append(row);

                });


                /* LOAD PAYMENT HISTORY */

                $('#payment_history').html('');

                data.payments.forEach(function(p){

                        var paymentRow = `
                                <div class="payment-row" data-payment-id="${p.id}" style="margin-bottom:5px; display:flex; justify-content:space-between; align-items:center;">

                                        <div>
                                                ${p.created_at} - ${p.payment_method} - ₹<span class="payment-amount">${p.amount}</span>
                                        </div>

                                        <button type="button" class="remove-payment" style="background:red;color:white;border:none;padding:2px 8px;border-radius:4px;">
                                                ✖
                                        </button>

                                </div>
                        `;

                        $('#payment_history').append(paymentRow);

                });

                /* LOAD BILL SUMMARY */

                $('#bill_subtotal').text("₹"+data.subtotal);
                $('#discount_input').val(data.discount);
                $('#bill_total').text("₹"+data.total);
                $('#bill_paid').text(data.paid);
                $('#bill_due').text(data.due);
        });

}

</script>

<script>
$('#updateBillBtn').click(function(){

    if(!billId){
        alert("Bill ID not found");
        return;
    }

    window.location.href = SITE_URL + "modules-nct/invoice-nct/index.php?id=" + billId;

});
</script>

<script>
var appointmentDoctorId = "%APPOINTMENT_DOCTOR_ID%";
var appointmentFees = "%APPOINTMENT_FEES%";

if(appointmentDoctorId){

    $("#doctor_select").val(appointmentDoctorId);

    var row = `
        <tr>
                <td>Consultation</td>
                <td><input type="number" class="form-control price" value="${appointmentFees}"></td>
                <td><input type="number" class="form-control qty" value="1"></td>
                <td class="row-total">₹${appointmentFees}</td>
        </tr>
    `;

    $("#billing_rows").html(row);

    setTimeout(function(){
        calculateTotal();
    }, 100);
}
</script>

<script>
var appointmentMode = "%APPOINTMENT_MODE%";

if(appointmentMode == 1){

    $("#patient_search").prop("readonly", true);
    $("#patient_phone").prop("readonly", true);
    $("#patient_age").prop("readonly", true);
    $("#age_type").prop("disabled", true);
    $("#patient_gender").prop("disabled", true);

    $("#doctor_select").prop("disabled", true);
    $("#bill_date").prop("readonly", true);

}
</script>

<script defer src="{SITE_JS}modules/billing-nct.js"></script>
