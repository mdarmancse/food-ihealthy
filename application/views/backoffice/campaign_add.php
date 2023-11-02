<?php
$this->load->view(ADMIN_URL . '/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/css/datetimepicker.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/multiselect/sumoselect.min.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar');

    if ($this->input->post()) {
        foreach ($this->input->post() as $key => $value) {
            $$key = @htmlspecialchars($this->input->post($key));
        }
    } else {
        $FieldsArray = array('entity_id', 'name', 'description', 'image', 'start_date', 'end_date');
        foreach ($FieldsArray as $key) {
            $$key = @htmlspecialchars($edit_records->$key);
        }
    }
    if (isset($edit_records) && $edit_records != "") {

        $add_label    = 'Campaign';
        $user_action  = base_url() . ADMIN_URL . '/' . $this->controller_name . "/edit/" . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
        $restaurant_map = array_column($restaurant_map, 'restaurant_id');
    } else {
        $add_label    = 'Campaign';
        $user_action  = base_url() . ADMIN_URL . '/' . $this->controller_name . "/add";
        $restaurant_map = array();
    }

    ?>

    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">Campaign</h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                <?php echo $this->lang->line('home'); ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/view">Campaign</a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $add_label; ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE HEADER-->
            <!-- BEGIN PAGE CONTENT-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN VALIDATION STATES-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $add_label; ?></div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo $user_action; ?>" id="form_add<?php echo $this->prefix; ?>" name="form_add<?php echo $this->prefix; ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                                <div id="iframeloading" class="display-no frame-load" style="display: none;">
                                    <img src="<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif" alt="loading" />
                                </div>
                                <div class="form-body">
                                    <?php if (!empty($Error)) { ?>
                                        <div class="alert alert-danger"><?php echo $Error; ?></div>
                                    <?php } ?>
                                    <?php if (validation_errors()) { ?>
                                        <div class="alert alert-danger">
                                            <?php echo validation_errors(); ?>
                                        </div>
                                    <?php } ?>


                                    <div class="form-group">
                                        <label class="control-label col-md-3">Campaign Name<span class="required">*</span></label>
                                        <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id ?>">
                                        <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo isset($image) ? $image : ''; ?>" />
                                        <div class="col-md-8">
                                            <input type="text" maxlength="249" onblur="checkExist(this.value)" class="form-control" name="name" id="name" value="<?php echo $name ?>" />
                                            <div id="phoneExist"></div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('restaurant'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <select name="restaurant_id[]" multiple="" class="form-control" id="restaurant_id">
                                                <?php if (!empty($restaurant)) {
                                                    foreach ($restaurant as $key => $value) { ?>
                                                        <option value="<?php echo $value['entity_id'] ?>" <?php echo in_array($value['entity_id'], $restaurant_map) ? 'selected' : '' ?>><?php echo $value['name'] ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('description'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <textarea name="description" id="description" class="form-control"><?php echo $description ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('image')  ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="file" name="image" id="image" value="<?php echo $image; ?>" />
                                            <span class="help-block"><?php echo $this->lang->line('img_allow')  ?><br /><?php echo $this->lang->line('max_file_size') ?><br /></span>
                                        </div>
                                        <div class="col-md-1">
                                            <?php if (isset($image) && $image != '') { ?>
                                                <img class="img-responsive" src="<?php echo image_url . $image; ?>">
                                            <?php } ?>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('start_date'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input size="16" type="text" name="start_date" class="form-control" id="start_date" value="<?php echo ($start_date) ? date('Y-m-d H:i', strtotime($start_date)) : "" ?>" readonly="">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('end_date'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input size="16" type="text" name="end_date" class="form-control" id="end_date" value="<?php echo ($end_date) ? date('Y-m-d H:i', strtotime($end_date)) : "" ?>" readonly="">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit'); ?></button>
                                        <a class="btn btn-danger danger-btn" href="<?php echo base_url() . ADMIN_URL ?>/campaign/view"><?php echo $this->lang->line('cancel'); ?></a>
                                    </div>
                                </div>


                            </form>
                            <!-- END FORM-->
                        </div>
                    </div>
                    <!-- END VALIDATION STATES-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/admin-management.js"></script>
<?php if ($this->session->userdata("language_slug") == 'ar') {  ?>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<?php } ?>
<?php if ($this->session->userdata("language_slug") == 'fr') {  ?>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/repeater/jquery.repeater.js"></script>
<script>
    jQuery(document).ready(function() {
        Layout.init(); // init current layout
    });

    $('#restaurant_id').SumoSelect({
        selectAll: true,
        search: true
    });

    //check coupon exist
    function checkExist(campaign) {
        var entity_id = $('#entity_id').val();
        $.ajax({
            type: "POST",
            url: BASEURL + "backoffice/campaign/checkExist",
            data: 'coupon=' + campaign + '&entity_id=' + entity_id,
            cache: false,
            success: function(html) {
                if (html > 0) {
                    $('#phoneExist').show();
                    $('#phoneExist').html("Campaign Name Exist.");
                    $(':input[type="submit"]').prop("disabled", true);
                } else {
                    $('#phoneExist').html("");
                    $('#phoneExist').hide();
                    $(':input[type="submit"]').prop("disabled", false);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#phoneExist').show();
                $('#phoneExist').html(errorThrown);
            }
        });
    }
    // for datepicker
    $(function() {
        $('#start_date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            autoclose: true,
        });
        $('#end_date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            autoclose: true,
        });
    });


    $(document).ready(function() {
        markup();
    });
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>