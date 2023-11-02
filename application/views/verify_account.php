<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('header'); ?>
<input type="hidden" name="base_url" id="base_url" value="<?= base_url() ?>">
<section class="content-area user-page login-bg">
    <div class="container-fluid">
        <div class="row" style="justify-content: center;">
            <div class="user-form">
                <div class=" login-div" style="margin-right: auto;">
                    <div class="logo">
                        <a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>assets/front/images/logo.png" alt="Logo" style="width: 150px; height: 150px;"></a>
                    </div>
                    <div class="alert alert-danger display-no" id="reg_error"></div>
                    <div class="alert alert-success display-no" id="reg_success"></div>
                    <div id="verify_div">
                        <form id="form_front_verify" name="form_front_verify" method="post" class="form-horizontal float-form">
                            <div class="form-body">
                                <?php if (!empty($this->session->flashdata('error_MSG'))) { ?>
                                    <div class="alert alert-danger">
                                        <?php echo $this->session->flashdata('error_MSG'); ?>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($this->session->flashdata('success_MSG'))) { ?>
                                    <div class="alert alert-success">
                                        <?php echo $this->session->flashdata('success_MSG'); ?>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($success)) { ?>
                                    <div class="alert alert-success"><?php echo $success; ?></div>
                                <?php } ?>
                                <?php if (!empty($error)) { ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php } ?>
                                <?php if (validation_errors()) { ?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors(); ?>
                                    </div>
                                <?php } ?>
                                <h3>Send OTP to verify.</h3>
                                <p>Your aren't verified. <br>Send OTP to <?= substr_replace($this->session->userdata("temp_mobile"), 'xxxxxxxxx', 0, 9); ?>
                                <div class="action-button">
                                    <a href="<?php echo base_url() . 'home/login'; ?>" class="btn btn-secondary"><?php echo $this->lang->line('title_login') ?></a>
                                    <button type="submit" name="submit_page" id="submit_page" value="Send" class="btn btn-primary">Send</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="otp_div" style="display: none;">
                        <form id="form_reg_verify_otp" name="form_reg_verify_otp" method="post" class="form-horizontal float-form">
                            <div class="form-body">
                                <?php if (validation_errors()) { ?>
                                    <div class="alert alert-danger">
                                        <?php echo validation_errors(); ?>
                                    </div>
                                <?php } ?>
                                <div class="form-group">
                                    <input type="number" name="otp" id="otp" class="form-control" placeholder=" ">
                                    <label><?php echo "OTP" ?></label>
                                    <input type="hidden" name="phone_number" id="phone_number" value="<?= $this->session->userdata("temp_mobile") ?>">
                                </div>
                                <div class="action-button">
                                    <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-6 login-bg"></div> -->
        </div>
    </div>
</section>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url(); ?>assets/front/js/scripts/admin-management-front.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/front/js/scripts/front-validations.js"></script>
<?php if ($this->session->userdata("language_slug") == 'fr') {  ?>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
</body>

</html>