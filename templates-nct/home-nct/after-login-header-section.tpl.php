<ul class="navbar-nav me-auto">
	<!-- <li class="nav-item">
		<a href="#" class="nav-icon-btn nav-link">
			<img src="{SITE_IMG}notification.svg" alt>
		</a>
	</li> -->
	%generate_bill_button%
	<li class="nav-item">
		<a href="{SITE_ADD_APPOINTMENT}" class="header-btn nav-link btn">
			Add an Appointment
		</a>
	</li>
	%add_doctor_button%
	<li class="nav-item dropdown profile-menu">
		<a class="nav-link dropdown-toggle nav-icon-btn p-0" href="javascript:void(0);" role="button" data-bs-toggle="dropdown" aria-expanded="false">
			<img src="%profile_photo%" alt="%alt_name%" class="upd_prop_img_list_header">
		</a>

		<ul class="dropdown-menu dropdown-menu-end">
			<li><a class="dropdown-item" href="{SITE_DASHBOARD}">Dashboard</a></li>
			<li><a class="dropdown-item" href="{SITE_PROFILE}">My Profile</a></li>
			%my_doctors_menu%
			%my_services_menu%
			%my_patients_menu%
			%billing_menu%
			<li><a class="dropdown-item" href="{SITE_ACCOUNT_SETTINGS}">Account Settings</a></li>

			<li>
				<hr class="dropdown-divider">
			</li>
			<li><a class="dropdown-item" href="{SITE_LOGOUT}">Logout</a></li>
		</ul>
	</li>
</ul>
