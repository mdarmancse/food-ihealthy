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
                    <h3><?php echo $this->lang->line('welcome_to') ?> <?php echo $this->lang->line('site_title'); ?>!</h3>
                    <div class="alert alert-danger display-no" id="reg_error"></div>
                    <div class="alert alert-success display-no" id="reg_success"></div>
                    <div id="registration_div">
                        <form id="form_front_registration" name="form_front_registration" method="post" class="form-horizontal float-form">
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
                                <div class="form-group">
                                    <input type="text" name="name" id="name" class="form-control" placeholder="" value="<?php echo $_POST["name"] ?>">
                                    <label><?php echo $this->lang->line('name') ?></label>
                                </div>
                                <div class="form-group">
                                    <input type="number" name="phone_number" id="phone_number" class="form-control" placeholder=" " value="<?php echo $_POST["phone_number"] ?>">
                                    <label><?php echo $this->lang->line('phone_number') ?></label>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" id="password" class="form-control" placeholder=" ">
                                    <label><?php echo $this->lang->line('password') ?></label>
                                </div>
                                <div class="action-button">
                                    <a href="<?php echo base_url() . 'home/login'; ?>" class="btn btn-secondary"><?php echo $this->lang->line('title_login') ?></a>
                                    <button type="submit" name="submit_page" id="submit_page" value="Register" class="btn btn-primary"><?php echo $this->lang->line('sign_up') ?></button>
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