<main class="flex-shrink-0 auth-main">

    <section class="auth-section">

        <div class="container">

            <div class="auth-box">


                <div class="fom-sm">
                    <div class="auth-top">
                        <figure>
                            <a href="{SITE_URL}">
                                <img src="{SITE_LOGO_URL}" alt="{SITE_NM}">
                            </a>
                        </figure>
                        <h1>Select User Type</h1>
                    </div>

                    <form id="formUserType" method="post">
                        <div class="form-group">
                            <div class="row row-cols-2 row-cols-md-2 g-3 g-md-4">
                                <div class="col">
                                    <div class="icon-check">
                                        <input type="radio" name="user_type" id="user_type_doctor" value="doctor" class="form-radio" required>
                                        <label for="user_type_doctor" class="radio-label radio-icon-v ms-auto">
                                            <span class="icon" aria-hidden="true">
                                                <img src="{SITE_IMG}doctor.svg" alt="Doctor">
                                            </span>
                                            <span class="r-label">Doctor</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="icon-check">
                                        <input type="radio" name="user_type" id="user_type_clinic" value="clinic" class="form-radio" required>
                                        <label for="user_type_clinic" class="radio-label radio-icon-v">
                                            <span class="icon" aria-hidden="true">
                                                <img src="{SITE_IMG}hospital.svg" alt="Clinic">
                                            </span>
                                            <span class="r-label">Clinic</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="btn-block d-flex justify-content-center">
                            <button type="submit" name="submitUserTypeFrm" class="btn lg-btn w-100" value="submit">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="auth-bg"></div>
    </section>
</main>