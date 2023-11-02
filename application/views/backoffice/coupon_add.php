<?php
$this->load->view(ADMIN_URL . '/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/css/datetimepicker.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/multiselect/sumoselect.min.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/jquery-ui/jquery-ui.min.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar');

    if ($this->input->post()) {
        foreach ($this->input->post() as $key => $value) {
            $$key = @htmlspecialchars($this->input->post($key));
        }
    } else {
        $FieldsArray = array('entity_id', 'restaurant_id', 'user_id', 'name', 'description', 'amount_type', 'discount_amount', 'amount', 'start_date', 'end_date', 'max_amount', 'coupon_type', 'image', 'usablity', 'maximum_use', 'gradual_all_items');
        foreach ($FieldsArray as $key) {
            $$key = @htmlspecialchars($edit_records->$key);
        }
    }
    if (isset($edit_records) && $edit_records != "") {

        $add_label    = $this->lang->line('title_admin_couponedit');
        $user_action  = base_url() . ADMIN_URL . '/' . $this->controller_name . "/edit/" . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
        // $restaurant_map = array_column($restaurant_map, 'restaurant_id');
        // $user_map = array_column($user_map, 'user_id');
        // $item_map = array_column($item_map, 'item_id');
        // $itemDetail = $this->coupon_model->getItem($restaurant_map, $coupon_type, $type, $entity_id);
        $type = 'edit';
    } else {
        $add_label    = $this->lang->line('title_admin_couponadd');
        $user_action  = base_url() . ADMIN_URL . '/' . $this->controller_name . "/add";
        $restaurant_map = array();
        $item_map = array();
        $itemDetail = array();
        $type = 'add';
        $gradual_map = array('' => ''); // otherwise it doesnt show on add.
        $user_map = array();
    }

    ?>

    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('title_admin_coupon'); ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                <?php echo $this->lang->line('home'); ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/view"><?php echo $this->lang->line('title_admin_coupon'); ?></a>
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
                                <div id="iframeloading" class="frame-load" style="display: none; width:100vw; height:100vh; position: fixed; z-index:1000">
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
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('coupon_type'); ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <!--<select name="coupon_type" class="form-control <?php echo ($entity_id) ? 'coupon_id_wrap' : '' ?>" id="coupon_type" onchange="getCouponType(this.value)">-->
                                            <select name="coupon_type" class="form-control" id="coupon_type" onchange="getCouponType(this.value)">
                                                <option value=""><?php echo $this->lang->line('select'); ?></option>
                                                <?php $coupon_types = coupon_type();
                                                if (!empty($coupon_types)) {
                                                    foreach ($coupon_types as $key => $value) { ?>
                                                        <option value="<?php echo $key ?>" <?php echo ($key == $coupon_type) ? 'selected' : '' ?>><?php echo $value ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="coupon_area enable_coupon">
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('title_admin_coupon'); ?><span class="required">*</span></label>
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id ?>">
                                            <input type="hidden" name="gradual_all_items" id="gradual_all_items" value="<?php echo $gradual_all_items; ?>">
                                            <input type="hidden" name="uploaded_image" value="<?php echo isset($image) ? $image : ''; ?>" />
                                            <div class="col-md-8">
                                                <input type="text" maxlength="249" onblur="checkExist(this.value)" class="form-control upper-text" name="name" id="name" value="<?php echo $name ?>" />
                                                <div id="phoneExist"></div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('restaurant'); ?><span class="required">*</span></label>
                                            <div class="col-sm-1">
                                                <div class="form-check">
                                                    <input id="all_restaurant" class="form-check-input" type="checkbox" name="all_restaurant" value="true">
                                                    <label for="all_restaurant" class="form-check-label">All</label>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <select name="restaurant_id[]" multiple="" class="form-control" id="restaurant_id">
                                                    <?php foreach ($restaurant_map as $res) {  ?>
                                                        <option value="<?= $res->restaurant_id ?>" selected><?= $res->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group hidden-row-user" style="<?php echo ($coupon_type != 'selected_user') ? 'display:none' : '' ?>">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('user'); ?><span class="required">*</span></label>
                                            <div class="col-sm-1">
                                                <div class="form-check">
                                                    <input id="all_user" class="form-check-input" type="checkbox" name="all_user" value="true">
                                                    <label for="all_user" class="form-check-label">All</label>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <select name="user_id[]" multiple="" class="form-control" id="user_id">
                                                    <?php foreach ($user_map as $user) {  ?>
                                                        <option value="<?= $user->user_id ?>" selected><?= $user->first_name . ' ' . $user->last_name ?></option>
                                                    <?php } ?>
                                                </select>
                                                <!-- <input class="form-control user_autocomplete" type="text" name="user_id" id="user_id" onchange="userAutocomplete()" onkeyup="userAutocomplete()"> -->
                                            </div>
                                            <div class="col-md-1">
                                                <button class="btn btn-primary" type="button" onclick="showCsvModal()">CSV</button>
                                            </div>
                                        </div>
                                        <div class="form-group hidden-row gradual" style="<?php echo ($coupon_type == 'free_delivery' || $coupon_type == 'user_registration' || $coupon_type == 'discount_on_cart' || $coupon_type == 'selected_user' || ($coupon_type == 'gradual' && sizeof($restaurant_map) > 1)) ? 'display:none' : '' ?>">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('menu_item'); ?><span class="required">*</span></label>
                                            <div class="col-sm-1">
                                                <div class="form-check">
                                                    <input id="all_menu" class="form-check-input" type="checkbox" name="all_menu" value="true">
                                                    <label for="all_menu" class="form-check-label">All</label>
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <select name="item_id[]" multiple="" class="form-control" id="item_id">
                                                    <?php foreach ($item_map as $item) {  ?>
                                                        <option value="<?= $item->item_id ?>" selected><?= $item->name ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('description'); ?></label>
                                            <div class="col-md-8">
                                                <textarea name="description" id="description" class="form-control ckeditor"><?php echo $description ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('image')  ?></label>
                                            <div class="col-md-4">
                                                <input type="file" name="image" id="image" />
                                                <span class="help-block"><?php echo $this->lang->line('img_allow')  ?><br /><?php echo $this->lang->line('max_file_size') ?><br /><?php echo $this->lang->line('recommended_size') . '400 * 240.'; ?></span>
                                            </div>
                                            <div class="col-md-1">
                                                <?php if (isset($image) && $image != '') { ?>
                                                    <img class="img-responsive" src="<?php echo image_url . $image; ?>">
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php if ($coupon_type != 'free_delivery') { ?>
                                            <div class="form-group show-hidden-row gradual_discount_type" style="<?php echo ($coupon_type == 'free_delivery' || $coupon_type == 'gradual') ? 'display:none' : '' ?>">
                                                <label class="control-label col-md-3"><?php echo $this->lang->line('discount_type'); ?></label>
                                                <div class="col-sm-4">
                                                    <p>
                                                        <input type="radio" name="amount_type" id="MPercentage" <?php if (isset($amount_type) && $amount_type == "Percentage") echo "checked"; ?> value="Percentage" checked="checked">&nbsp;&nbsp;<?php echo $this->lang->line('percentage'); ?>
                                                    </p>
                                                    <p class="on_items" style="<?php echo ($coupon_type == 'discount_on_items') ? 'display:none' : '' ?>">
                                                        <input type="radio" name="amount_type" id="MAmount" <?php if (isset($amount_type) && $amount_type == "Amount") echo "checked"; ?> value="Amount">&nbsp;&nbsp;<?php echo $this->lang->line('amount'); ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="form-group show-hidden-row gradual_discount_type" style="<?php echo ($coupon_type == 'free_delivery' || $coupon_type == 'gradual') ? 'display:none' : '' ?>">
                                                <label class="control-label col-md-3"><?php echo $this->lang->line('amount'); ?><span class="required">*</span></label>
                                                <div class="col-sm-8 form-markup">
                                                    <label id="Percentage"><?php echo $this->lang->line('percentage'); ?> (%)</label>
                                                    <label id="Amount" style="display:none"><?php echo $this->lang->line('amount'); ?> (Ar) </label>
                                                    <br>
                                                    <input type="text" name="amount" id="amount" value="<?php echo ($amount) ? $amount : '' ?>" maxlength="19" data-required="1" class="form-control" />
                                                </div>
                                            </div>

                                            <div class="form-group show-hidden-row upto" style="<?php echo ($coupon_type == 'free_delivery' || $amount_type == "Amount") ? 'display:none' : '' ?>">
                                                <label class="control-label col-md-3">Discount Amount Up To</label>
                                                <div class="col-sm-8 form-markup">
                                                    <input type="text" name="discount_amount" id="discount_amount" value="<?php echo ($discount_amount) ? $discount_amount : '' ?>" maxlength="19" data-required="1" class="form-control" />
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <input type="hidden" value="<?php echo $type; ?>" id="type" />
                                        <div class="form-group gradual_discount_type on_items" style="<?php echo ($coupon_type == 'gradual' || $coupon_type == 'discount_on_items') ? 'display:none' : '' ?>">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('min_amount'); ?><span class="required">*</span></label>
                                            <div class="col-sm-8 form-markup">
                                                <input type="text" name="max_amount" id="max_amount" value="<?php echo ($max_amount) ? $max_amount : '' ?>" maxlength="19" data-required="1" class="form-control" />
                                            </div>
                                        </div>

                                        <!-- <div class="form-group"> -->
                                        <!--    <label class="control-label col-md-3">Discount Amount Up To</label>-->
                                        <!--    <div class="col-sm-8 form-markup">-->
                                        <!--          <input type="text" name="discount_amount" id="discount_amount" value="<?php echo ($discount_amount) ? $discount_amount : '' ?>" maxlength="19" data-required="1" class="form-control"/>  -->
                                        <!--    </div>  -->
                                        <!--</div> -->
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
                                        <!-- coupon max use -->
                                        <div class="form-group maximum_usage" style="<?php echo ($coupon_type == 'gradual' || $coupon_type == 'discount_on_items') ? 'display:none' : '' ?>">
                                            <label class="control-label col-md-3">Maximum Usage</label>
                                            <div class="col-sm-8 form-markup">
                                                <input type="text" name="maximum_use" id="maximum_use" value="<?php echo ($maximum_use) ? $maximum_use : '0' ?>" maxlength="19" data-required="1" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group gradual_discount_type on_items" style="<?php echo ($coupon_type == 'gradual' || $coupon_type == 'discount_on_items') ? 'display:none' : '' ?>">
                                            <label class="control-label col-md-3">Usability</label>
                                            <div class="col-md-8">
                                                <select name="usablity" class="form-control" id="usablity">
                                                    <option value="">Select Type</option>
                                                    <option value="regular" <?php if ($usablity == 'regular') { ?> selected <?php } ?>>Regular</option>
                                                    <option value="onetime" <?php if ($usablity == 'onetime') { ?> selected <?php } ?>>One Time</option>
                                                    <option style="<?php echo ($coupon_type == 'discount_on_cart' || $coupon_type == 'user_registration') ? 'display:block' : 'display:none' ?>" value="autoApply" <?php if ($usablity == 'autoApply') { ?> selected <?php } ?> id="auto">Auto Apply</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="gradual_repeater" id="repeater" style="<?php echo ($coupon_type == 'gradual') ? 'display:block;' : 'display:none;' ?>">
                                            <label>Gradual Sequence <span style="color: grey;">(Enter Discount Percentage And It's Sequence Accordingly)</span></label>
                                            <br><br />
                                            <div data-repeater-list="gradual_group">
                                                <?php
                                                $j = 1;
                                                for ($i = 0; $i < count($gradual_map); $i++) { ?>
                                                    <div data-repeater-item class="outer-repeater">

                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control gradual_discount" id="gradual_discount<?php echo $j; ?>" name="gradual_discount" value="<?php echo $gradual_map[$i]['percentage']; ?>" placeholder="Enter Percentage" />
                                                        </div>
                                                        <div class="col-md-5">
                                                            <input type="text" class="form-control sequence" id="sequence<?php echo $j; ?>" name="sequence" value="<?php echo $gradual_map[$i]['sequence'] ?>" placeholder="Enter Sequence" />
                                                        </div>
                                                        <input data-repeater-delete type="button" value="Delete" id="<?php echo $j; ?>" class="btn btn-danger delete_repeater" onclick="delete_gradual(this.id)" type="button" />

                                                    </div>
                                                    <br />
                                                <?php
                                                    $j++;
                                                }
                                                ?>

                                            </div>


                                            <div class="col-md-5">
                                                <input data-repeater-create type="button" value="Add" class="btn btn-danger" />
                                            </div>
                                        </div>
                                    </div>

                                    <br />

                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit'); ?></button>
                                        <a class="btn btn-danger danger-btn" href="<?php echo base_url() . ADMIN_URL ?>/coupon/view"><?php echo $this->lang->line('cancel'); ?></a>
                                    </div>
                                </div>


                                <!-- csv modal -->
                                <div id="csv_upload_modal" class="modal fade" role="dialog">
                                    <div class="modal-dialog">
                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h4 class="modal-title">Upload CSV</h4>
                                            </div>
                                            <div class="modal-body">
                                                <form id="form_add_status" name="form_add_status" method="post" class="form-horizontal" enctype="multipart/form-data">
                                                    <div class="row">
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">File</label>
                                                                <div class="col-sm-7">
                                                                    <input id="upload_csv" class="form-control-file" type="file" name="upload_csv" accept=".csv" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>">
                                                                </div>
                                                            </div>
                                                            <div class="form-actions fluid">
                                                                <div class="col-sm-6"></div>
                                                                <div class="col-md-6">
                                                                    <a href="<?= base_url("assets/admin/csv/sample_user_coupon.csv") ?>"><button type="button" class="btn btn-sm  btn-primary margin-bottom" id="submit_page">Download Sample</button></a>
                                                                    <button type="button" class="btn btn-sm  danger-btn filter-submit margin-bottom" data-dismiss="modal" id="submit_page" value="Done">Done</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
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
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
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
    var items = [];

    jQuery(document).ready(function() {
        Layout.init(); // init current layout

        $("#form_add_cpn").on('submit', function(e) {
            e.preventDefault();
            if (!$(this).validate()) {
                return;
            }
            $("#iframeloading").show();
            $("#submit_page").attr("disabled", "disabled");
            // $('input[type=checkbox]').attr('disabled', 'true');
            // $("#modal-submit").attr('diabled', 'true');
            const fd = new FormData($(this)[0]);
            fd.append("restaurant_id", JSON.stringify(fd.getAll("restaurant_id[]")));
            fd.append("item_id", JSON.stringify(fd.getAll("item_id[]")));
            fd.append("user_id", JSON.stringify(fd.getAll("user_id[]")));
            fd.append("submit_page", "Submit");
            fd.delete("restaurant_id[]");
            fd.delete("item_id[]");
            fd.delete("user_id[]");
            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                processData: false,
                contentType: false,
                data: fd,
                success: function(response) {

                    if (response === "success") {
                        window.location.href = "<?= base_url() . ADMIN_URL . '/' . $this->controller_name ?>/view"
                    } else {
                        location.reload();
                    }

                },
                error: function(xhr) {
                    console.log(xhr);
                }
            })
        })
    });


    $('#repeater').repeater({

        show: function() {
            var counts = $('.outer-repeater').length;
            //var final_count = count+1;
            console.log('count:', counts);
            $(this).slideDown();
            $(this).find('.delete_repeater').attr('id', counts).css('margin-bottom', '20px');

            // $(this).find('.repeater_field').attr('required', true);
            // $(this).find('.repeater_field').addClass('error');
            $(this).find('.gradual_discount').attr('id', 'gradual_discount' + counts);
            $(this).find('.sequence').attr('id', 'sequence' + counts);
        },

        hide: function(deleteElement) {
            if (confirm('Are you sure you want to delete this element?')) {
                $(this).slideUp(deleteElement);
            }
        },

        // isFirstItemUndeletable: true
    });

    // $('#item_id').SumoSelect({
    //     selectAll: true
    // });
    // $('#restaurant_id').SumoSelect({
    //     selectAll: true,
    //     search: true
    // });
    // $('#user_id').SumoSelect({
    //     //selectAll: true,
    //     search: true,
    //     searchText: 'Enter here.'
    // });

    //check coupon exist
    function checkExist(coupon) {
        var entity_id = $('#entity_id').val();
        $.ajax({
            type: "POST",
            url: BASEURL + "backoffice/coupon/checkExist",
            data: 'coupon=' + coupon + '&entity_id=' + entity_id,
            cache: false,
            success: function(html) {
                if (html > 0) {
                    $('#phoneExist').show();
                    $('#phoneExist').html("<?php echo $this->lang->line('coupon_exist'); ?>");
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
    $("#amount,#max_amount,#maximum_use").each(function() {
        $(this).keyup(function() {
            this.value = this.value.replace(/[^0-9\.]/g, '');
        });
    });


    // Markup Radio Button Validation
    function markup() {
        if ($("input[name=amount_type]:checked").val() == "Percentage") {
            $("#Amount").hide();
            $("#Percentage").show();
            $(".upto").show();
        } else if ($("input[name=amount_type]:checked").val() == "Amount") {
            $("#Percentage").hide();
            $("#Amount").show();
            $(".upto").hide();
        }
    }
    $(document).ready(function() {
        markup();
    });
    $("input[name=amount_type]:radio").click(function() {
        markup();
        if ($("input[name=amount_type]:checked").val() == "Percentage") {
            $("#amount").val('');
            $("#max_amount").attr('greater', '');
        } else if ($("input[name=amount_type]:checked").val() == "Amount") {
            $("#amount").val('');
            $("#max_amount").attr('greater', '#amount');
        }
    });
    //get coupon type
    function getCouponType(value) {
        $('.coupon_area').removeClass('enable_coupon');
        if (value == 'free_delivery') {
            $('.maximum_usage').show()
            $('.hidden-row').hide();
            $('.gradual_repeater').hide();
            $('.show-hidden-row').hide();
            $('.hidden-row-user').hide();
            $('#auto').hide();
            $('.on_items').show();
            $('#restaurant_id').val('');
            $('#restaurant_id')[0].sumo.reload();
            $('#restaurant_id').SumoSelect({
                selectAll: true
            });
        } else if (value == 'discount_on_cart' || value == 'user_registration') {
            $('#auto').show();
            $('.hidden-row-user').hide();
            $('.maximum_usage').show()
            $('.hidden-row').hide();
            $('.gradual_repeater').hide();
            $('.show-hidden-row').show();
            $('#restaurant_id').val('');
            $('.on_items').show();
            $('#restaurant_id')[0].sumo.reload();
            $('#restaurant_id').SumoSelect({
                selectAll: true
            });
        } else if (value == 'gradual') {
            $('.hidden-row-user').hide();
            $('.hidden-row').hide();
            $('#auto').hide();
            $('.maximum_usage').hide()
            $('.gradual_repeater').hide();
            $('.show-hidden-row').show();
            $('.gradual_discount_type').hide();
            $('#restaurant_id').val('');
            $('#restaurant_id')[0].sumo.reload();
            $('#restaurant_id').SumoSelect({
                selectAll: true
            });
        } else if (value == 'gradual') {
            $('.hidden-row').hide();
            $('#auto').hide();
            $('.maximum_usage').hide()
            $('.gradual_repeater').hide();
            $('.show-hidden-row').show();
            $('.gradual_discount_type').hide();
            $('#restaurant_id').val('');
            $('#restaurant_id')[0].sumo.reload();
            $('#restaurant_id').SumoSelect({
                selectAll: true
            });

            //if a restaurant's all items are currently in another active gradual
            jQuery.ajax({

                type: "POST",
                dataType: "html",
                url: '<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getNonGradualRestaurant',

                success: function(response) {
                    //alert(response)
                    $('#restaurant_id').empty().append(response);
                    $('#restaurant_id')[0].sumo.reload();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });
        } else if (value == 'discount_on_items') {
            $('.maximum_usage').hide()
            $('.on_items').hide();
            $('.hidden-row-user').hide();
            $('.hidden-row').show();
        } else if (value == 'selected_user') {
            $('.hidden-row').hide();
            $('.show-hidden-row').show();
            $('.hidden-row-user').show();
            $('.gradual_repeater').hide();
            $('#auto').hide();
            $('.on_items').show();
        } else {
            $('.hidden-row-user').hide();
            $('.maximum_usage').show()
            $('.hidden-row').show();
            $('#auto').hide();
            $('.gradual_repeater').hide();
            $('.show-hidden-row').show();
            $('#amount').attr('required', true);
            $('#restaurant_id').val('');
            $('#restaurant_id')[0].sumo.reload();
            $('#restaurant_id').SumoSelect({
                search: true,
                searchText: 'Enter here.'
            });
        }
    }

    function delete_gradual(id) {
        console.log(id);
        var type = $('#type').val();
        if (type == 'edit') {
            $('#sequence' + id).remove();
            $('#gradual_discount' + id).remove();
            $('#' + id).remove();
        }

    }

    //get items of restaurant
    $(document).ready(function() {
        $('#restaurant_id').change(function(event) {
            var coupon_type = $('#coupon_type').val();
            var type = $('#type').val();
            var entity_id = $('#entity_id').val();
            $('#gradual_all_items').val();

            var items = [];
            if ($(this).val() != null) {
                items.push($(this).val());
            }

            console.log(items[0].length)

            if (items[0].length > 1 && coupon_type == 'gradual') {

                $('.gradual').hide();
                $('.gradual_repeater').show();
                $('#gradual_all_items').val(1);
            }

            if (items[0].length == 1 && coupon_type == 'gradual') { // so that it cant affect on other coupons

                $('.gradual').show();
                $('.gradual_repeater').show();
                $('#gradual_all_items').val(0);
            }
            // jQuery.ajax({
            //     type: "POST",
            //     dataType: "html",
            //     url: '<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getItem',
            //     data: {
            //         'entity_id': items,
            //         'coupon_type': coupon_type,
            //         'type': type,
            //         'coupon_id': entity_id
            //     },
            //     success: function(response) {
            //         $('#item_id').empty().append(response);
            //         $('#item_id')[0].sumo.reload();
            //     },
            //     error: function(XMLHttpRequest, textStatus, errorThrown) {
            //         alert(errorThrown);
            //     }
            // });
        });
    });

    "use strict";


    // function userAutocomplete() {
    //     // Auto complete
    //     var options = {
    //         minLength: 0,
    //         source: function(request, response) {
    //             var user_info = $('#user_id').val();
    //             $.ajax({
    //                 url: "<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getUsers",
    //                 method: 'post',
    //                 dataType: "json",
    //                 data: {
    //                     user_info: user_info,
    //                 },
    //                 success: function(data) {
    //                     console.log(data);
    //                     response(data);
    //                 }
    //             });
    //         },
    //         focus: function(event, ui) {
    //             $(this).val(ui.item.label);
    //             return false;
    //         },
    //         select: function(event, ui) {
    //             $(this).parent().parent().find(".autocomplete_hidden_value").val(ui.item.value);
    //             $(this).val(ui.item.label);
    //             var id = ui.item.value;

    //             $(this).unbind("change");
    //             return false;
    //         }
    //     }

    //     $('body').on('keypress.autocomplete', '.user_autocomplete', function() {
    //         $(this).autocomplete(options);
    //     });

    // }

    // $("#restaurant_id").on('change', function() {
    //     var restaurant_id = $(this).val();
    //     if (restaurant_id == '' || restaurant_id == null) {
    //         $("#item_id").val("");
    //         $("#item_id > .select2-selection--multiple").html("");
    //     }
    // })

    $("#user_id").select2({
            // tags: true,
            multiple: true,
            closeOnSelect: false,
            scrollAfterSelect: true,
            // tokenSeparators: [',', ' '],
            minimumResultsForSearch: 10,
            ajax: {
                url: "<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getUsers",
                dataType: "json",
                type: "POST",
                data: function(params) {

                    var queryParameters = {
                        term: params.term
                    }
                    return queryParameters;
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.label,
                                id: item.id
                            }
                        })
                    };
                }
            }
        }).on('select2:selecting', e => $(e.currentTarget).data('scrolltop', $('.select2-results__options').scrollTop()))
        .on('select2:select', e => $('.select2-results__options').scrollTop($(e.currentTarget).data('scrolltop')))
        .on('select2:unselecting', e => $(e.currentTarget).data('scrolltop', $('.select2-results__options').scrollTop()))
        .on('select2:unselect', e => $('.select2-results__options').scrollTop($(e.currentTarget).data('scrolltop')));;


    var resSelect = $("#restaurant_id").select2({
            closeOnSelect: false,
            scrollAfterSelect: true,
            // tags: true,
            multiple: true,
            // tokenSeparators: [',', ' '],
            minimumResultsForSearch: 10,
            ajax: {
                url: "<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getRestaurants",
                dataType: "json",
                type: "POST",
                data: function(params) {

                    var queryParameters = {
                        term: params.term
                    }
                    return queryParameters;
                },
                processResults: function(data) {
                    return {
                        results: $.map(data, function(item) {
                            return {
                                text: item.label,
                                id: item.id
                            }
                        })
                    };
                }
            }
        }).on('select2:selecting', e => $(e.currentTarget).data('scrolltop', $('.select2-results__options').scrollTop()))
        .on('select2:select', e => $('.select2-results__options').scrollTop($(e.currentTarget).data('scrolltop')))
        .on('select2:unselecting', e => $(e.currentTarget).data('scrolltop', $('.select2-results__options').scrollTop()))
        .on('select2:unselect', e => $('.select2-results__options').scrollTop($(e.currentTarget).data('scrolltop')));;

    $("#item_id").select2({
            scrollAfterSelect: true,
            closeOnSelect: false,
            // tags: false,
            multiple: true,
            // tokenSeparators: [',', ' '],
            minimumResultsForSearch: 10,
            ajax: {
                url: "<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getRestaurantMenu",
                dataType: "json",
                type: "POST",
                data: function(params) {

                    var postData = {
                        term: params.term,
                        res_id: $("#restaurant_id").val(),
                        coupon_type: $('#coupon_type').val(),
                        type: $('#type').val(),
                        coupon_id: $('#entity_id').val(),
                    }
                    return postData;
                },
                processResults: function(data) {
                    console.log(data);
                    return {
                        results: $.map(data, function(res, key) {
                            data = {
                                text: key,
                                children: $.map(res, function(item) {
                                    return {
                                        text: item.label,
                                        id: item.id
                                    }
                                })
                            };

                            return data;
                        })
                    };
                }
            }
        }).on('select2:selecting', e => $(e.currentTarget).data('scrolltop', $('.select2-results__options').scrollTop()))
        .on('select2:select', e => $('.select2-results__options').scrollTop($(e.currentTarget).data('scrolltop')))
        .on('select2:unselecting', e => $(e.currentTarget).data('scrolltop', $('.select2-results__options').scrollTop()))
        .on('select2:unselect', e => $('.select2-results__options').scrollTop($(e.currentTarget).data('scrolltop')));;

    $(".select2-container--default").attr("style", "width:100%");

    function showCsvModal() {
        $("#csv_upload_modal").modal("show");
    }

    $("#all_restaurant").change(function() {
        if ($(this).is(':checked')) {
            $("#restaurant_id").attr("disabled", "disabled");
            $("#all_menu").attr("disabled", "disabled");
            $("#item_id").attr("disabled", "disabled");
        } else {
            $("#restaurant_id").removeAttr("disabled");
            $("#all_menu").removeAttr("disabled");
            $("#item_id").removeAttr("disabled");
        }
    })

    $("#all_menu").change(function() {
        if ($(this).is(':checked')) {
            $("#item_id").attr("disabled", "disabled");
        } else {
            $("#item_id").removeAttr("disabled");
        }
    })

    $("#all_user").change(function() {
        if ($(this).is(':checked')) {
            $("#user_id").attr("disabled", "disabled");
        } else {
            $("#user_id").removeAttr("disabled");
        }
    })
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>