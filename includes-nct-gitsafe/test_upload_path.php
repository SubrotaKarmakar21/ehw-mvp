<?php

require_once "includes-nct/config-nct.php";

echo "DIR_UPD: " . DIR_UPD . "<br>";
echo "DIR_UPD_CLINIC_BANNER: " . DIR_UPD_CLINIC_BANNER . "<br>";
echo "DIR_UPD_PROFILE_IMAGE: " . DIR_UPD_PROFILE_IMAGE . "<br>";

echo "<br>Writable check:<br>";

echo "clinicBanner-nct writable: ";
var_dump(is_writable(DIR_UPD_CLINIC_BANNER));

echo "<br>profilePhoto-nct writable: ";
var_dump(is_writable(DIR_UPD_PROFILE_IMAGE));
