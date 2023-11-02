<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/multiselect/sumoselect.min.css" />
<?php $this->load->view(ADMIN_URL . '/header'); ?>
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar'); ?>
    <!-- END sidebar -->
    <?php
    if (isset($vehicle_update) && $vehicle_update != "") {
        $add_label    = $this->lang->line('edit') . ' Vehicle';
        $form_action      = base_url() . ADMIN_URL . "/" . $this->controller_name . "/update_vehicle/" . $this->uri->segment('4');
    } else {
        $add_label    = $this->lang->line('add') . ' Vehicle';
        $form_action      = base_url() . ADMIN_URL . "/" . $this->controller_name . "/add_vehicle/" . $this->uri->segment('4');
    }
    ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">Add Vehicle</h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/vehicle_view">Vehicle Area</a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo "Add Vehicle"; ?>
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
                            <form action="<?php echo $form_action; ?>" id="form_add_<?php echo $this->prefix ?>" name="form_add_<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                                <div class="form-body">
                                    <?php if (validation_errors()) { ?>
                                        <div class="alert alert-danger">
                                            <?php echo validation_errors(); ?>
                                        </div>
                                    <?php } ?>

                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo "Vehicle Type"; ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo ($vehicle_update[0]['entity_id'] ?
                                                                                                            $vehicle_update[0]['entity_id'] : '') ?>" />

                                            <input type="text" name="name" id="name" value="<?php echo ($vehicle_update[0]['name'] ?
                                                                                                $vehicle_update[0]['name'] : '') ?>" maxlength="249" data-required="1" class="form-control required" onchange="handleAction()" />

                                        </div>

                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo "Per Ride Charge"; ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="price" id="price" value="<?php echo ($vehicle_update[0]['price'] ?
                                                                                                    $vehicle_update[0]['price'] : 0) ?>" maxlength="249" data-required="1" class="form-control required" onchange="handleAction()" />
                                        </div>
                                    </div>

                                </div>
                                <div class="form-actions right">
                                    <a class="btn default" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/vehicle_view"><?php echo $this->lang->line('cancel') ?></a>
                                    <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn red"><?php echo "Submit" ?></button>
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
<script src="<?php echo base_url(); ?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="//maps.google.com/maps/api/js?key=<?= MAP_API_KEY ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/gmaps/gmaps.min.js"></script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>