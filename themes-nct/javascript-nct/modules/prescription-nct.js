jQuery(document).ready(function($){

// BMI CALCULATION
$(document).on("input", "#height, #weight", function(){

    let height = parseFloat($("#height").val());
    let weight = parseFloat($("#weight").val());

    if(height > 0 && weight > 0){

        // convert cm to meters
        let heightInMeters = height / 100;

        let bmi = weight / (heightInMeters * heightInMeters);

        $("#bmi").val(bmi.toFixed(1));
    }
});

// ADD NEW COMPLAINT ROW
$(document).on("click", "#add-complaint", function(){

    let row = `
        <tr class="complaint-row">
            <td>
                <input type="text" name="complaints[]" class="form-control" placeholder="e.g. Fever, headache...">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger remove-complaint">×</button>
            </td>
        </tr>
    `;

    $("#complaints-body").append(row);
});

// REMOVE ROW
$(document).on("click", ".remove-complaint", function(){

    if($("#complaints-body tr").length > 1){
        $(this).closest("tr").remove();
    } else {
        alert("At least one complaint is required");
    }

});

// ADD MEDICINE ROW
$(document).on("click", "#add-medicine", function(){

    let count = $("#medicine-body tr").length + 1;

    let row = `
    <tr>

        <td class="serial">${count}</td>

        <td>
            <input type="text" class="form-control medicine-name" placeholder="Medicine name">
        </td>

        <td><input type="text" class="form-control dose" placeholder="1"></td>
        <td><input type="text" class="form-control dose" placeholder="0"></td>
        <td><input type="text" class="form-control dose" placeholder="1"></td>
        <td><input type="text" class="form-control dose" placeholder="0"></td>

        <td>
            <input type="number" class="form-control duration" placeholder="5">
        </td>

        <td>
            <select class="form-control duration-type">
                <option value="days">Days</option>
                <option value="weeks">Weeks</option>
                <option value="months">Months</option>
            </select>
        </td>

        <td>
            <input type="text" class="form-control advise" placeholder="After food">
        </td>

        <td>
            <input type="text" class="form-control remarks" placeholder="Optional">
        </td>

        <td class="text-center">
            <button type="button" class="btn btn-outline-danger remove-medicine">×</button>
        </td>

    </tr>
    `;

    $("#medicine-body").append(row);
});

// REMOVE MEDICINE
$(document).on("click", ".remove-medicine", function(){

    if($("#medicine-body tr").length > 1){
        $(this).closest("tr").remove();
        updateSerial();
    } else {
        alert("At least one medicine is required");
    }

});

// UPDATE SERIAL NUMBERS
function updateSerial(){
    $("#medicine-body tr").each(function(index){
        $(this).find(".serial").text(index + 1);
    });
}

/* ADD ROW */
$("#addInvestigationBtn").click(function(){

    	var row = `
        <div class="row mb-2 investigation-row">

            <div class="col-md-10 position-relative">
                <input type="text" class="form-control investigation-input" placeholder="Search or type test name">
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-investigation w-100">Remove</button>
            </div>

        </div>
        `;

        $("#investigation_container").append(row);
});


/* REMOVE ROW */
$(document).on("click", ".remove-investigation", function(){
        $(this).closest(".investigation-row").remove();
});


/* SEARCH TEST */
$(document).on("keyup", ".investigation-input", function(){

        var input = $(this);
        var keyword = input.val();

        if(keyword.length < 2){
            $(".test-results").remove();
            return;
        }

        $.ajax({
            url: SITE_URL + "modules-nct/prescription-nct/ajax.prescription-nct.php",
            type: "POST",
            dataType: "json",
            data:{
                action:"searchTests",
                keyword: keyword
            },
            success:function(res){

                $(".test-results").remove();

                var html = '<div class="list-group test-results" style="position:absolute; z-index:9999; width:100%;">';

                res.forEach(function(t){
                    html += `<a href="#" class="list-group-item list-group-item-action test-item"
                                data-name="${t.service_name}">
                                ${t.service_name}
                            </a>`;
                });

                html += "</div>";

                input.after(html);
            }
	});
});

/* SELECT TEST */
$(document).on("click", ".test-item", function(e){
        e.preventDefault();

        var name = $(this).data("name");

        var input = $(this).closest(".position-relative").find(".investigation-input");

        input.val(name);

        $(".test-results").remove();
});


/* CLICK OUTSIDE */
$(document).on("click", function(e){
        if(!$(e.target).closest(".investigation-input").length){
            $(".test-results").remove();
        }
});

// NEXT BUTTON FUNCTION
$(document).on("click", "#nextBtn", function(){

    var appointment_id = new URLSearchParams(window.location.search).get("appointment_id");

    var data = {

        action: "savePrescriptionDraft",

        appointment_id: appointment_id,

        patient_name: "%PATIENT_NAME%",
        patient_age: "%PATIENT_AGE%",
        patient_gender: "%PATIENT_GENDER%",

        height: $("#height").val(),
        weight: $("#weight").val(),
        bmi: $("#bmi").val(),

        bp: $("#bp").val(),
        pulse: $("#pulse").val(),
        rr: $("#rr").val(),
        spo2: $("#spo2").val(),

        complaints: collectComplaints(),
        diagnosis: $("#diagnosis").val(),
        medications: collectMedications(),
        investigations: collectInvestigations(),

        followup_date: $("#followup_date").val(),
        followup_notes: $("#followup_notes").val()
    };

    $.ajax({
        url: SITE_URL + "modules-nct/prescription-nct/ajax.prescription-nct.php",
        type: "POST",
        dataType: "json",
        data: data,

        success: function(res){

            if(res.status === "success"){

                // redirect to next page
                window.location.href =
                SITE_URL + "modules-nct/advice-nct/index.php?prescription_id=" + res.prescription_id;

            }else{
                alert("Failed to save prescription");
            }
        },

        error:function(xhr){
            console.log(xhr.responseText);
        }
    });

});

function collectComplaints(){
    var arr = [];

    $("#complaints-body input[name='complaints[]']").each(function(){

        var val = $(this).val();

        if(val && val.trim() !== ''){
            arr.push(val.trim());
        }
    });

    return arr;
}

function collectMedications(){
    var meds = [];

    $("#medicine-body tr").each(function(){

        var row = $(this);

        var med = row.find(".medicine-name").val();

        if(!med) return;

        var doses = row.find(".dose");

        meds.push({
            medicine: med,
            morning: $(doses[0]).val(),
            afternoon: $(doses[1]).val(),
            evening: $(doses[2]).val(),
            night: $(doses[3]).val(),
            duration: row.find(".duration").val(),
            duration_type: row.find(".duration-type").val(),
            advise: row.find(".advise").val(),
            remarks: row.find(".remarks").val()
        });

    });

    return meds;
}

function collectInvestigations(){
    var arr = [];

    $(".investigation-row").each(function(){
        var val = $(this).find("input").val();
        if(val){
            arr.push(val);
        }
    });

    return arr;
}

});
