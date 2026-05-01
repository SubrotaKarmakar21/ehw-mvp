<link href="{SITE_CSS}intlTelInput.css" rel="stylesheet" type="text/css"/>

<main class="flex-shrink-0 inner-main">

    <section class="auth-section gray-bg">
        <div class="container">
            <div class="auth-box">
                <div class="fom-sm">
                    <div class="auth-top">
                        <h1>Contact Us</h1>
                    </div>

                    <form id="contactForm" name="contactForm" method="POST">

                        <div class="form-group">
                            <label for="first_name" class="is-label-txt">{MEND_SIGN}First Name: &nbsp;</label> 
                            <input type="text" class="form-control" name="first_name" id="first_name" value="%first_name%" placeholder="Enter First Name" />
                        </div>

                        <div class="form-group">
                            <label for="last_name" class="is-label-txt">{MEND_SIGN}Last Name: &nbsp;</label> 
                            <input type="text" class="form-control" name="last_name" id="last_name" value="%last_name%" placeholder="Enter Last Name" />
                        </div>

                        <div class="form-group">
                            <label for="country_number" class="is-label-txt ">{MEND_SIGN}{label_phone_number}</label>
                            <div class="country-field">
                         
                                <input type="text" name="phone_no" id="country_number" placeholder="{placholder_phone_number}" class="form-control" value="%phone_no%">
                                <input type="hidden" name="phone_country_code" id="phone_country_code" value="%phone_country_code%">
                                <input type="hidden" name="phone_iso2_code" id="phone_iso2_code" value="%phone_iso2_code%"></div>
                           
                        </div>

                        <div class="form-group">
                            <label for="email_address" class="is-label-txt">{label_email_address}: &nbsp;</label>
                            <input type="text" class="form-control" name="email_address" id="email_address" value="%email_address%" %READ_ONLY% placeholder="{placeholder_email_address}" />
                        </div>

                        <div class="form-group">
                            <label for="message" class="is-label-txt">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="6" placeholder="Enter Message"></textarea>
                        </div>
                        <div class="form-group text-center cf">
                            <input type="hidden" name="action" value="method">
                            <input type="hidden" name="method" value="submitContactForm">
                            <button type="submit" class="light-orange-btn lg-btn" name="btmContactUs" id="btmContactUs">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
       
    </section>
</main>