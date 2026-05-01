jQuery(document).ready(function($){

        var payments = [];
        $("#payment_amount").on("input", function(){
                $(this).data("manual", true);
        });

        /* PATIENT SEARCH */
        $("#patient_search").keyup(function(){

                console.log("Typing detected");

                var keyword = $(this).val();

                if(keyword.length < 2){
                        $("#patient_results").html("");
                        return;
                }

                $.ajax({

                        url:SITE_URL + "modules-nct/billing-nct/ajax.billing-nct.php",
                        type:"POST",
                        dataType:"json",

                        data:{
                                action:"searchPatient",
                                keyword:keyword
                        },

                        success:function(res){

                                var html="";

                                res.forEach(function(p){

                                        var style = "";
                                        var label = "";

                                        if(!p.booking_date){
                                                style = "style='color:red;font-weight:600;'";
                                                label = " (No Appointment)";
                                        }else{
                                                label = " — " + p.booking_date;
                                        }

                                        html += `<a href="#"
                                                class="list-group-item list-group-item-action patient-item"
                                                ${style}
                                                data-id="${p.id}"
                                                data-name="${p.name}"
                                                data-phone="${p.phone_no}"
                                                data-gender="${p.gender}"
                                                data-dob="${p.date_of_birth}">
                                                ${p.name} (${p.phone_no})${label}
                                        </a>`;

                                });

                                $("#patient_results").html(html);
                        }

                });

        });

        /* PATIENT SELECT */
        $(document).on("click",".patient-item",function(e){

                e.preventDefault();

                var id = $(this).data("id");
                var name = $(this).data("name");
                var phone = $(this).data("phone");
                var gender = $(this).data("gender");
                var dob = $(this).data("dob");

                $("#patient_search").val(name);
                $("#patient_id").val(id);
                $("#patient_phone").val(phone);
                $("#patient_gender").val(gender);

                if(dob){

                        var birth = new Date(dob);
                        var today = new Date();

                        var diffTime = today - birth;
                        var diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));

                        if(diffDays < 30){

                                $("#patient_age").val(diffDays);
                                $("#age_type").val("days");

                        }else if(diffDays < 365){

                                var months = Math.floor(diffDays / 30);

                                $("#patient_age").val(months);
                                $("#age_type").val("months");

                        }else{

                                var years = Math.floor(diffDays / 365);

                                $("#patient_age").val(years);
                                $("#age_type").val("years");

                        }

                }

                $("#patient_phone").prop("readonly",true);
                $("#patient_age").prop("readonly",true);
                $("#patient_gender").prop("disabled",true);
                $("#patient_results").html("");
        });

        /* PATIENT SELECTED */
        $("#patient_search").change(function(){

                var patient_id = $("#patient_id").val();

                if(patient_id=="") return;

                $.ajax({

                        url: SITE_URL + "modules-nct/billing-nct/ajax.billing-nct.php",
                        type: "POST",
                        dataType: "json",

                        data:{
                                action:"getPatientAppointment",
                                patient_id:patient_id
                        },

                        success:function(res){

                                if(!res) return;

                                $("#bill_date").val(res.booking_date);

                                var row = `
                                        <tr>

                                                <td>
                                                        Consultation
                                                </td>

                                                <td>
                                                        <input type="number" class="form-control price" value="${res.consultation_fees}">
                                                </td>

                                                <td>
                                                        <input type="number" class="form-control qty" value="1">
                                                </td>

                                                <td class="row-total">
                                                        ₹${res.consultation_fees}
                                                </td>

                                                <td>
                                                        <button class="btn btn-danger btn-sm remove-row">X</button>
                                                </td>

                                        </tr>
                                `;

                        }


                });

        });

        /* ADD SERVICE ROW */
        $(document).on("click",".add-service",function(){

                var row = `
                <tr>

                        <td>
                                <input type="text" class="form-control service-name" placeholder="Service Name">
                        </td>

                        <td>
                                <input type="number" class="form-control price" value="0">
                        </td>

                        <td>
                                <input type="number" class="form-control qty" value="1">
                        </td>

                        <td class="row-total">
                                ₹0
                        </td>

                        <td>
                                <button type="button" class="btn btn-danger btn-sm remove-row">X</button>
                        </td>

                </tr>
                `;

                $("#billing_rows").append(row);

        });


        /* REMOVE SERVICE */
        $(document).on("click",".remove-row",function(){

                $(this).closest("tr").remove();
                calculateTotal();

        });

        /* AUTO FILL*/
        $(document).on("change","#doctor_select",function(){

                var fee = $("#doctor_select option:selected").data("fee");

                if(fee){

                        $(".price").val(fee);
                        $(".row-total").text("₹"+fee);

                }

        });

        /* SERVICE SEARCH */
        $(document).on("keyup",".service-name",function(){

                var input = $(this);
                var keyword = input.val();

                if(keyword.length < 2){
                        $(".service-results").remove();
                        return;
                }

                $.ajax({

                        url: SITE_URL + "modules-nct/billing-nct/ajax.billing-nct.php",
                        type:"POST",
                        dataType:"json",

                        data:{
                                action:"searchService",
                                keyword:keyword
                        },

                        success:function(res){

                                $(".service-results").remove();

                                var html = '<div class="list-group service-results" style="position:absolute; z-index:9999; width:100%;">';
                                res.forEach(function(s){

                                        html += `<a href="#"
                                        class="list-group-item list-group-item-action service-item"
                                        data-id="${s.id}"
                                        data-name="${s.service_name}"
                                        data-price="${s.price}">
                                        ${s.service_name} — ₹${s.price}
                                        </a>`;

                                });

                                html += "</div>";

                                input.after(html);

                        }

                });

        });


        /* SELECT SERVICE */
        $(document).on("click",".service-item",function(e){

                e.preventDefault();

                var name = $(this).data("name");
                var price = parseFloat($(this).data("price"));

                var row = $(this).closest("tr");

                row.find(".service-name").val(name);
                row.find(".price").val(price);
                row.find(".qty").val(1);
                row.find(".price").val(price);

                // trigger calculation for this row
                row.find(".price").trigger("change");

                $(".service-results").remove();

                calculateTotal();

        });

        $(document).on("click",function(e){

                if(!$(e.target).closest(".service-name").length){
                        $(".service-results").remove();
                }

        });

        /* AUTO CALCULATE */
        $(document).on("keyup change",".price, .qty",function(){

                var row = $(this).closest("tr");

                var price = parseFloat(row.find(".price").val()) || 0;
                var qty = parseFloat(row.find(".qty").val()) || 0;

                var total = price * qty;

                row.find(".row-total").html("₹"+total);

                calculateTotal();

        });

        /* DISCOUNT CHANGE */
        $(document).on("keyup change","#discount_input",function(){

                calculateTotal();

        });

        /* PAYMENT CHANGE */
        $(document).on("keyup change","#payment_amount",function(){

                calculateTotal();

        });

        /* ADD PAYMENT*/
        $(document).on("click","#addPaymentBtn",function(){

                var amount = parseFloat($("#payment_amount").val()) || 0;
                var method = $("#payment_method").val();

                if(amount <= 0){
                        alert("Enter payment amount");
                        return;
                }

                /* NEW BILL (NO billId) */
                if(!billId || billId == 0){

                        var paymentObj = {
                                id: Date.now(),
                                amount: amount,
                                method: method
                        };

                        payments.push(paymentObj);

                        updatePaymentUI(paymentObj);

                        return;

                }

                /* EXISTING BILL */
                $.ajax({

                        url: SITE_URL + "modules-nct/billing-nct/ajax.billing-nct.php",
                        type: "POST",
                        dataType: "json",

                        data:{
                                action:"addPayment",
                                bill_id: billId,
                                amount: amount,
                                method: method
                        },

                        success:function(res){

                                if(res.status === "success"){

                                        var paymentObj = {id: res.payment_id,amount: amount,method: method};

                                        updatePaymentUI(paymentObj);

                                }
                        }

                });

        });

        function updatePaymentUI(payment){

                var currentPaid = parseFloat($("#bill_paid").text()) || 0;
                var total = parseFloat($("#bill_total").text().replace("₹","")) || 0;

                var newPaid = currentPaid + payment.amount;

                var due = total - newPaid;

                $("#bill_paid").text(newPaid);
                $("#bill_due").text(due);

                var paymentRow = `
                        <div class="payment-row d-flex justify-content-between align-items-center border-bottom py-1" data-payment-id="${payment.id}">

                                <div>
                                        ₹<span class="payment-amount">${payment.amount}</span> - ${payment.method}
                                </div>

                                <button type="button" class="remove-payment btn btn-sm btn-danger">
                                        ✖
                                </button>

                        </div>
                `;

        $("#payment_history").append(paymentRow);

                $("#payment_amount").val("");
        }

        /* REMOVE PAYMENT ROW */
        $(document).on("click", ".remove-payment", function(){

                var row = $(this).closest(".payment-row");
                var amount = parseFloat(row.find(".payment-amount").text()) || 0;

                /* NEW BILL */
                if(!billId || billId == 0){

                        var id = row.attr("data-payment-id");

                        payments = payments.filter(function(p){
                        return p.id != id;
                });

                updateTotalsAfterDelete(amount);
                row.remove();
                return;
        }

        /* EXISTING BILL */
        if(!confirm("Delete this payment?")) return;

                var paymentId = row.data("payment-id");
                console.log("Payment Id:", paymentId);
                $.ajax({
                        url: SITE_URL + "modules-nct/billing-nct/ajax.billing-nct.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                                action: "deletePayment",
                                payment_id: paymentId
                        },
                        success: function(res){

                                if(res.status === "success"){

                                        updateTotalsAfterDelete(amount);
                                        row.remove();

                                }else{
                                        alert("Delete failed");
                                }

                        }
                });

        });

        /* GENERATE BILL BUTTON */
        $("#generateBillBtn").on("click", function(){
                var patient_id          = $("#patient_id").val();
                var patient_name        = $("#patient_search").val();
                var patient_age         = $("#patient_age").val();
                var patient_gender      = $("#patient_gender").val();
                var patient_phone       = $("#patient_phone").val();
                var doctor_id           = $("#doctor_select").val() || null;
                var referred_doctor     = $("#referred_doctor").val() || null;
                var promo_code          = $("#promo_code").val() || null;
                var bill_date           = $("#bill_date").val();

                var services = [];

                $("#billing_rows tr").each(function(){

                        var service = $(this).find(".service-name").val();

                                if(!service){
                                        service = $(this).find("td:first").text().trim();
                                }

                        var price = parseFloat($(this).find(".price").val()) || 0;
                        var qty = parseFloat($(this).find(".qty").val()) || 0;

                        if(service && price > 0){
                                services.push({
                                        service_name: service,
                                        price: price,
                                        qty: qty
                                });
                        }

                });

                /* REQUIRED FIELD VALIDATION */
                if(!patient_name || patient_name.trim() === ""){
                        alert("Patient name is required.");
                        $("#patient_search").focus();
                        return;
                }

                if(!patient_phone || patient_phone.trim() === ""){
                        alert("Phone number is required.");
                        $("#patient_phone").focus();
                        return;
                }

                if(!patient_age){
                        alert("Age is required.");
                        $("#patient_age").focus();
                        return;
                }

                if(!patient_gender || patient_gender === ""){
                        alert("Please select gender.");
                        $("#patient_gender").focus();
                        return;
                }

                if(services.length === 0){
                        alert("Please add at least one service.");
                        return;
                }

                console.log("Sending Data:");
                console.log("Age:", patient_age);
                console.log("Age Type:", $("#age_type").val());

                $.ajax({

                        url:SITE_URL + "modules-nct/billing-nct/ajax.billing-nct.php",
                        type:"POST",
                        dataType:"json",

                        data:{
                                action:"generateBill",
                                appointment_id:$("#appointment_id").val(),
                                patient_id:patient_id,
                                patient_name:patient_name,
                                patient_age:patient_age,
                                age_type:$("#age_type").val(),
                                patient_gender:patient_gender,
                                patient_phone:patient_phone,
                                doctor_id:doctor_id,
                                referred_doctor:referred_doctor,
                                promo_code:$("#promo_code").val(),
                                bill_date:bill_date,
                                discount: $("#discount_input").val(),
                                payments: payments,
                                services:services
                        },

                        success:function(res){

                                console.log("RAW RESPONSE:", res);

                                if(typeof res === "string"){

                                        try{
                                                res = JSON.parse(res.substring(res.lastIndexOf("{")));
                                        }catch(e){
                                                console.error("JSON parse failed:", res);
                                                alert("Bill generated but response corrupted.");
                                                return;
                                        }

                                }

                                if(res.status === "success"){
                                        window.location = SITE_URL + "modules-nct/invoice-nct/index.php?id=" + res.bill_id;

                                }else{

                                        alert("Bill generation failed");

                                }

                        },
                        error:function(xhr){
                                console.log("AJAX ERROR:",xhr.responseText);
                        }

                });

        });

        // CLOSE DROP-DOWN WHEN CLICK OUTSIDE
        $(document).click(function(e){
                if(!$(e.target).closest("#patient_search").length){
                        $("#patient_results").html("");
                }
        });

})

function loadAppointment(patient_id){

    $.ajax({

        url: SITE_URL + "modules-nct/billing-nct/ajax.billing-nct.php",
        type: "POST",
        dataType: "json",

        data:{
            action:"getPatientAppointment",
            patient_id:patient_id
        },

        success:function(res){

            if(!res) return;

            /* create consultation row */
            var row = `
            <tr>

                <td>
                    Consultation
                </td>

                <td>
                    <input type="number" class="form-control price" value="${res.consultation_fees}" readonly>
                </td>

                <td>
                    <input type="number" class="form-control qty" value="1" readonly>
                </td>

                <td class="row-total">
                    ₹${res.consultation_fees}
                </td>

                <td></td>

            </tr>
            `;

            $("#billing_rows").html(row);

            calculateTotal();

        }

    });
}

/* TOTAL BILL */
function calculateTotal(){

    var subtotal = 0;

    $("#billing_rows tr").each(function(){

        var price = parseFloat($(this).find(".price").val()) || 0;
        var qty = parseFloat($(this).find(".qty").val()) || 0;

        subtotal += price * qty;

    });

    $("#bill_subtotal").html("₹"+subtotal);

    var discount_input = $("#discount_input").val();
    var discount_amount = 0;

    if(discount_input){

        discount_input = discount_input.toString().trim();

        if(discount_input.includes("%")){

            var percent = parseFloat(discount_input.replace("%","")) || 0;
            discount_amount = subtotal * percent / 100;

        }else{

            discount_amount = parseFloat(discount_input) || 0;

        }

    }

    var final_total = subtotal - discount_amount;

    if(final_total < 0){
        final_total = 0;
    }

    $("#bill_total").html("₹"+final_total);

    if(!$("#payment_amount").data("manual")){
        $("#payment_amount").val(final_total);
    }

    var paid = parseFloat($("#bill_paid").text()) || 0;

    $("#bill_paid").text(paid);

    var due = final_total - paid;

    if(due < 0){
        due = 0;
    }

    $("#bill_due").text(due);
}

function updateTotalsAfterDelete(amount){

    var currentPaid = parseFloat($("#bill_paid").text()) || 0;
    var total = parseFloat($("#bill_total").text().replace("₹","")) || 0;

    var newPaid = currentPaid - amount;
    var due = total - newPaid;

    // safety checks
    if(newPaid < 0) newPaid = 0;
    if(due < 0) due = 0;

    $("#bill_paid").text(newPaid);
    $("#bill_due").text(due);
}
