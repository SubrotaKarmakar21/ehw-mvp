<div style="font-family: DejaVu Sans; font-size:14px; color:#333; padding:10px;">

    <!-- HEADER -->
    <table width="100%">
        <tr>
            <td width="55%" valign="top">
                <div style="font-size:28px; font-weight:2000; color:#2e7d6b; margin-top:-10mm;">
                    <strong>%DOCTOR_NAME%</strong>
                </div>

                <div style="font-size:16px; font-weight:600; color:#32745c; margin-top:3px;">
                    %DOCTOR_CATEGORY%
                </div>

                <div style="font-size:13px; color:#2e7d6b; margin-top:4px; line-height:1.5;">
                    %DOCTOR_DESCRIPTION%
                </div>
            </td>

            <td width="45%" valign="top" align="right">
                <img src="%CLINIC_BANNER%" style="width:60%; max-height:120px; margin-top:-15mm; margin-rightt:-10mm;"><br>

                <div style="font-size:14px; color:#2e7d6b; text-align:right;">
                    %CLINIC_INFO%
                </div>
            </td>
        </tr>
    </table>

    <div style="border-top:2px solid #999; margin:12px 0;"></div>

    <!-- PATIENT INFO -->
    <table width="100%" style="font-size:15px; margin:15px 0;">
        <tr>
            <td>
                <b>Patient:</b> %PATIENT_NAME% &nbsp;&nbsp;
                <b>Age:</b> %PATIENT_AGE% &nbsp;&nbsp;
                <b>Gender:</b> %PATIENT_GENDER%
            </td>

            <td align="right">
                <b>Date:</b> %PRESCRIPTION_DATE%
            </td>
        </tr>
    </table>

    <!-- VITALS -->
    <div style="margin-bottom:10px;">
        %VITALS%
    </div>

    <div style="border-top:1px solid #ccc; margin:12px 0;"></div>

    <!-- CORE -->
    %COMPLAINTS%
    %DIAGNOSIS%
    %MEDICATIONS%
    %INVESTIGATIONS%
    %FOLLOWUP%

    <div style="border-top:1px solid #ccc; margin:12px 0;"></div>

    <!-- ADVICE -->
    %GENERAL_ADVICE%
    %DIET_PLAN%
    %DOS_DONTS%

    <!-- SIGNATURE -->
    <div style="margin-top:30px; text-align:right;">
        %DOCTOR_SIGNATURE%
    </div>

</div>
