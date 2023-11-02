<?php $this->load->view(ADMIN_URL . '/header'); ?>
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
        $FieldsArray = array('content_id', 'entity_id', 'cat_entity_id', 'name', 'image', 'cat_is_multiple', 'cat_max_choice');
        foreach ($FieldsArray as $key) {
            $$key = @htmlspecialchars($edit_records[0]->$key);
        }

        $entity_id = $cat_entity_id;
    }
    if (isset($edit_records) && $edit_records != "") {
        $add_label    = $this->lang->line('edit') . ' ' . $this->lang->line('addons_category');
        $form_action      = base_url() . ADMIN_URL . "/" . $this->controller_name . "/edit/" . $this->uri->segment('4') . '/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    } else {
        $add_label    = $this->lang->line('add') . ' ' . $this->lang->line('addons_category');
        $form_action      = base_url() . ADMIN_URL . "/" . $this->controller_name . "/add/" . $this->uri->segment('4');
    } ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('addons_category') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL; ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/view"><?php echo $this->lang->line('addons_category') ?></a>
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
                            <form action="<?php echo $form_action; ?>" id="form_add_<?php echo $this->prefix ?>" name="form_add_<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                                <div class="form-body">
                                    <?php if (validation_errors()) { ?>
                                        <div class="alert alert-danger">
                                            <?php echo validation_errors(); ?>
                                        </div>
                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('cat_name'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id; ?>" />
                                            <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id) ? $content_id : $this->uri->segment('5'); ?>" />
                                            <input type="text" name="name" id="name" value="<?php echo $name; ?>" maxlength="249" data-required="1" class="form-control required" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Preset Addons<span class="required">*</span></label>
                                        <div class="col-sm-6 repeater_wrap" style="padding: 10px;">
                                            <div class="col" id="1_is_multiple_">
                                                <input type="checkbox" name="is_multiple" id="is_multiple" value="1" <?= isset($cat_is_multiple) && $cat_is_multiple ? 'checked' : '' ?> onchange="show_max(this)"><?php echo $this->lang->line('is_multiple') ?>
                                            </div>
                                            <div id="addons_repeater" class="addons_repeater repeater_wrap " style="margin: 0%;">
                                                <div data-repeater-list="add_ons_list" class="add_ons_detail">
                                                    <?php
                                                    if ($edit_records) {
                                                        $i = 0;
                                                        foreach ($edit_records as $addons) { ?>
                                                            <div data-repeater-item>
                                                                <div class="form-group">
                                                                    <div class="col-md-4">
                                                                        <label class="control-label"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                                                                        <input type="text" name="add_ons_name" id="add_ons_name<?php echo $j ?>" value="<?= $addons ? $addons->addon_name : '' ?>" class="form-control repeater_field name_repeater" maxlength="249">
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <label class="control-label"><?php echo $this->lang->line('price') ?><span class="required">*</span></label>
                                                                        <input type="number" name="add_ons_price" id="" value="<?= $addons ? intval($addons->addon_price) : '' ?>" class="form-control repeater_field digits price_repeater" min="0" maxlength="19">
                                                                    </div>
                                                                    <div class="col-sm-2 delete-repeat <?php echo ($i > 0) ? 'display-yes' : 'display-no'; ?>">
                                                                        <label class="control-label">&nbsp;</label>
                                                                        <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php
                                                            $i++;
                                                        }
                                                    } else { ?>
                                                        <div data-repeater-item>
                                                            <div class="form-group">
                                                                <div class="col-md-4">
                                                                    <label class="control-label"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                                                                    <input type="text" name="add_ons_name" id="add_ons_name<?php echo $j ?>" value="" class="form-control repeater_field name_repeater" maxlength="249">
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <label class="control-label"><?php echo $this->lang->line('price') ?><span class="required">*</span></label>
                                                                    <input type="text" name="add_ons_price" id="" value="" class="form-control repeater_field digits price_repeater" min="0" maxlength="19">
                                                                </div>
                                                                <div class="col-sm-2 delete-repeat <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'display-yes' : 'display-no'; ?>">
                                                                    <label class="control-label">&nbsp;</label>
                                                                    <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-12 add_ons_detail">
                                                        <input data-repeater-create class="btn btn-green" type="button" value="<?php echo $this->lang->line('add') ?>" />
                                                    </div>
                                                </div>
                                                <div class="form-group" id="max_choice_div" style="<?= $cat_max_choice ? 'display: block' : 'display:none' ?>">
                                                    <div class="col-md-6">
                                                        <label class="control-label"><?php echo $this->lang->line('max_required_choice') ?><span class="required">*</span></label>
                                                        <input type="number" name="max_choice" id="max_choice" value="<?php echo $cat_max_choice ?  $cat_max_choice : 0; ?>" class="form-control repeater_field name_repeater max-req" min="0" title="max choice" onchange="max_choice_change(this)" onkeyup="max_choice_change(this)">
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions right">
                                    <a class="btn default" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/view"><?php echo $this->lang->line('cancel') ?></a>
                                    <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn red"><?php echo $this->lang->line('submit') ?></button>
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
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/repeater/jquery.repeater.js"></script>
<script>
    jQuery(document).ready(function() {
        Layout.init(); // init current layout
    });

    $('.addons_repeater').repeater({

        isFirstItemUndeletable: true,

        show: function() {
            $(this).slideDown();
            $(this).find('.delete-repeat').show();
        },

        hide: function(deleteElement) {
            $(this).slideUp(deleteElement);
        },
    });

    function show_max(e) {
        if ($(e).is(':checked')) {
            $(e).parent().parent().find("#max_choice_div").find(".max_req").attr('required', 'true');
            $(e).parent().parent().find("#max_choice_div").show();
            // $(e).parent().parent().find("#max_choice_hiden").removeAttr("disabled");
        } else {
            $(e).parent().parent().find("#max_choice_div").find(".max_req").removeAttr('required');
            $(e).parent().parent().find("#max_choice_div").hide();
            // $(e).parent().parent().find("#max_choice_hiden").attr("disabled", "disabled");

        }
    }

    function max_choice_change(e) {
        $("#max_choice").val($(e).val());
    }
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>