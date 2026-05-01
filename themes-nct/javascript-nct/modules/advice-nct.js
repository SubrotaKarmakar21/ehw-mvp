jQuery(document).ready(function($){

    const canvas = document.getElementById("signaturePad");
    const signaturePad = new SignaturePad(canvas);

    $("#clearSignature").click(function(){
        signaturePad.clear();
    });

    $("#saveAdviceBtn").click(function(){

        // CHECKBOX VALIDATION
        if(!$("#doctor_confirmation").is(":checked")){
            alert("Please confirm the prescription before proceeding.");
            return;
        }

        // SIGNATURE VALIDATION
        if(signaturePad.isEmpty()){
            alert("Please provide your signature.");
            return;
        }

        var advice = $("#advice_box").val();
        var diet = $("#diet_box").val();
        var signature = signaturePad.toDataURL();

        $.ajax({
            url: SITE_URL + "modules-nct/advice-nct/ajax.advice-nct.php",
            type: "POST",
            dataType: "json",
            data:{
                action: "saveAdvice",
                prescription_id: prescription_id,
                general_advice: advice,
                diet_plan: diet,
                signature: signature
            },
            success:function(res){

                if(res.status === "success"){
                    window.location.href = SITE_URL + "modules-nct/prescription_view-nct/index.php?prescription_id="+prescription_id;

                } else {
                    alert(res.message || "Failed to save");
                }
            },
            error:function(xhr){
                console.log(xhr.responseText);
            }
        });

    });
});
