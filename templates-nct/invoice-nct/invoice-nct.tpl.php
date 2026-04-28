<div class="container invoice-container">

<div id="invoice-print-area" class="invoice-page" style="position:relative;">

%CANCELLED_WATERMARK%

<div class="invoice-content">

	<!-- HEADER -->
	<div class="invoice-header" style="position:relative;">

		<!-- BILL INFO TOP RIGHT -->
    		<div style="width:100%; text-align:right; font-size:16px; top:0; margin-bottom:10px;">
        		<p style="margin:0;"><b>Bill No:</b> %BILL_NUMBER%</p>
			%GSTIN_LINE%
		</div>

    		<img src="%CLINIC_BANNER%" class="clinic-banner" style="display:block; width: 80%; margin-left:-10mm; margin-top:-15mm; margin-bottom: 0">

    		<div class="clinic-info" style="width:45%; margin-top:0; margin-left:-8mm; margin-bottom:10px">
        		<p style="margin:0;">%CLINIC_ADDRESS%</p>
        		<p style="margin:0;">Contact Number: %CLINIC_PHONE%</p>
    		</div>

    	</div>

	<hr class="invoice-divider">

	<h2 class="invoice-title">Invoice</h2>

	<!-- PATIENT + INVOICE INFO -->
	<table width="100%" style="margin-bottom:20px;">
		<tr>

			<td width="50%" style="vertical-align:top;">

				<p><b>Patient:</b> %PATIENT%</p>
				<p><b>Age:</b> %AGE%</p>
				<p><b>Gender:</b> %GENDER%</p>
				<p><b>Phone:</b> %PHONE%</p>

			</td>

				<td width="50%" style="vertical-align:top;">
				<p><b>Doctor:</b> %DOCTOR%</p>
				<p><b>Referred By:</b> %REFERRED%</p>
				<p><b>Date:</b> %DATE%</p>

			</td>

		</tr>
	</table>

	</div>

	<!-- SERVICES TABLE -->
	<table class="table table-bordered invoice-table">

		<thead>
			<tr>
				<th>Service</th>
				<th>Price</th>
				<th>Qty</th>
				<th>Total</th>
			</tr>
		</thead>

		<tbody>

			%ROWS%

		</tbody>

	</table>

	<table width="100%" style="margin-top:20px; text-align:right;">

		<tr>
			<td style="width:75%"></td>
			<td style="width:25%">

				<p><b>Subtotal:</b> ₹%SUBTOTAL%</p>

				<p><b>Discount:</b> ₹%DISCOUNT%</p>

				<h3>Total: ₹%TOTAL%</h3>

			</td>
		</tr>

</table>

<h4 style="margin-top:30px">Payments</h4>

<table class="table table-bordered">

<thead>
<tr>
<th>Date</th>
<th>Method</th>
<th>Amount</th>
</tr>
</thead>

<div style="margin-top:15px;text-align:left">

<b>Paid:</b> ₹%PAID% <br>
<b>Due:</b> ₹%DUE%

</div>

<tbody>
%PAYMENT_ROWS%
</tbody>

</table>

</div>
</div>

%PRINT_BUTTON_HTML%

</div>
