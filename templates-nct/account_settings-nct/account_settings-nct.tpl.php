<main class="flex-shrink-0 inner-main">

    <section class="apoiment-list gray-bg ">
        <div class="container">
            <div class="common-white-box profile-box">
                <ul class="nav nav-tabs common-tabs pt-0" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane"
                        type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Change Password</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                        type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Delete/Deactivate Account</button>
                    </li>

                </ul>

                <div class="tab-content list-container" id="myTabContent">
                    <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                        <form method="POST" id="frm_changepassword" name="frm_changepassword">
                            <div class="form-group">
                                <label for="oldPassword" class="form-label">{MEND_SIGN}Password: </label> 
                                <input type="text" class="form-control logintextbox-bg required" name="oldPassword" id="oldPassword">
                            </div>
                            <div class="form-group">
                                <label for="newPassword" class="form-label">{MEND_SIGN}New password: </label> 
                                <input type="text" class="form-control logintextbox-bg required" name="newPassword" id="newPassword">
                            </div>
                            <div class="form-group">
                                <label for="confirmNewPassword" class="form-label">{MEND_SIGN}Confirm New password:</label> 
                                <input type="text" class="form-control logintextbox-bg required" name="confirmNewPassword" id="confirmNewPassword">
                            </div>
                            <div class="btn-block text-center">
                                <input type="hidden" name="action" value="changepassword" >
                                <button type="button" class="btn lg-btn" name="change_password" id="changePasswordBtn">Change Password</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="1">
                        <div class="delete-account-block">
                            <span class="delete-icon-lg"><i class="fa-solid fa-user-xmark"></i></span>    

                            <p>Deleting your account will erase all your data and you will have to join the platform as a brand new user. Instead, you may choose to deactivate your account now. You can reactivate it anytime in future and all your data will be restored.</p>
                        </div>
                        <div class="btn-block text-center">
                            <button type="button" class="btn lg-btn-border me-2" id="deactive_account">Deactivate Account</button>
                            <button type="button" class="btn lg-btn" id="delete_account">Delete Account</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>