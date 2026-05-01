<!-- HEADER -->
<table width="100%" style="font-family:Arial; font-size:14px;">
<tr>

<td style="width:70%; vertical-align:top;">
<img src="%CLINIC_BANNER%" style="width:80%; margin-top:-15mm; margin-left:-10mm; margin-bottom:0">

<p style="margin-top:0; margin-bottom:2px; font-weight:bold; color:#10443e;">%CLINIC_ADDRESS%</p>
<p style="margin-top:0; margin-bottom:2px; font-weight:bold; color:#10443e;">Contact No.: %CLINIC_PHONE%</p>
</td>

<td style="width:30%; text-align:right; vertical-align:top;">
    <div><b>Bill No:</b> %BILL_NUMBER%</div>
    %GSTIN_LINE%
</td>

</tr>
</table>
<hr style="border:0; border-top:1px solid #999; margin:10px 0 15px 0; color:#93d694;">

<h2 style="text-align:center; color:#19715c;">Invoice</h2>

<table width="100%" style="margin-bottom:20px;">
<tr>

<td width="50%" style="vertical-align:top;">
<b>Patient:</b> %PATIENT%<br>
<b>Age:</b> %AGE%<br>
<b>Gender:</b> %GENDER%<br>
<b>Phone:</b> %PHONE%
</td>

<td width="50%" style="vertical-align:top;">
<b>Doctor:</b> %DOCTOR%<br>
<b>Referred By:</b> %REFERRED%<br>
<b>Date:</b> %DATE%
</td>

</tr>
</table>

<table width="100%" border="1" cellpadding="6" cellspacing="0">
<thead>
<tr>
<th style="background:#15523e; color:white;">Service</th>
<th style="background:#15523e; color:white;">Price</th>
<th style="background:#15523e; color:white;">Qty</th>
<th style="background:#15523e; color:white;">Total</th>
</tr>
</thead>

<tbody>
%ROWS%
</tbody>

</table>

<!-- BILL SUMMARY -->

<table width="40%" border="0" cellpadding="2" cellspacing="0" style="margin-top:20px; margin-left:auto;">
<tr>
<td><b>Subtotal:</b></td>
<td align="left">₹%SUBTOTAL%</td>
</tr>

<tr>
<td><b>Discount:</b></td>
<td align="left">₹%DISCOUNT%</td>
</tr>

<tr>
<td><b>TOTAL:</b></td>
<td align="left">₹%TOTAL%</td>
</tr>
</table>

<div style="clear:both;"></div>

<h4 style="margin-top:40px; font-size:20px; color:#19715c;">Payments</h4>

<p>
<b>Paid:</b> ₹%PAID% <br>
<b>Due:</b> ₹%DUE%
</p>

<table width="100%" border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;">
<thead>
<tr>
<th style="background:#93d694;">Date</th>
<th style="background:#93d694;">Method</th>
<th style="text-align:right; background:#93d694;">Amount</th>
</tr>
</thead>

<tbody>
%PAYMENT_ROWS%
</tbody>
</table>

<div style="clear:both;"></div>

</div>
