<?php
$this->load->view(ADMIN_URL . '/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar'); ?>
    <!-- END sidebar -->
    <?php
    if ($this->input->post()) {
        foreach ($this->input->post() as $key => $value) {
            $$key = @htmlspecialchars($this->input->post($key));
        }
    } else {
        $FieldsArray = array('content_id', 'entity_id', 'name', 'description', 'meta_title', 'meta_keyword', 'meta_description', 'image');
        foreach ($FieldsArray as $key) {
            $$key = @htmlspecialchars($edit_records->$key);
        }
    }
    if (isset($edit_records) && $edit_records != "") {
        $add_label    = $this->lang->line('edit') . ' ' . $this->lang->line('cms');
        $form_action  = base_url() . ADMIN_URL . '/' . $this->controller_name . "/edit/" . $this->uri->segment('4') . "/" . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    } else {
        $add_label    = $this->lang->line('add') . ' ' . $this->lang->line('cms');
        $form_action  = base_url() . ADMIN_URL . '/' . $this->controller_name . "/add/" . $this->uri->segment('4');
    } ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('cms')  ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url(); ?>admin">
                                <?php echo $this->lang->line('home')  ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/view"><?php echo $this->lang->line('cms')  ?></a>
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
                            <form action="<?php echo $form_action; ?>" id="form_add<?php echo $this->prefix ?>" name="form_add<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
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
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('title')  ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="uploadedCms_image" value="<?php echo isset($image) ? $image : ''; ?>" />
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id; ?>" />
                                            <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id) ? $content_id : $this->uri->segment('5'); ?>" />
                                            <input type="text" name="name" id="name" value="<?php echo $name; ?>" maxlength="249" data-required="1" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('bammer_img')  ?></label>
                                        <div class="col-md-4">
                                            <input type="file" name="CMSImage" id="CMSImage" />
                                            <span class="help-block"><?php echo $this->lang->line('img_allow')  ?></span>
                                        </div>
                                        <div class="col-md-1">
                                            <?php if (isset($image) && $image != '') { ?>
                                                <img class="img-responsive" src="<?php echo image_url . $image; ?>">
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('content')  ?><span class="required">*</span></label>
                                        <div class="col-md-9">
                                            <textarea class="ckeditor form-control" name="description" id="description" rows="6" data-required="1"><?php echo $description; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions right">
                                    <a class="btn default" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name; ?>/view"><?php echo $this->lang->line('cancel')  ?></a>
                                    <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn green"><?php echo $this->lang->line('submit')  ?></button>
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
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
    jQuery(document).ready(function() {
        Layout.init(); // init current layout
    });
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>