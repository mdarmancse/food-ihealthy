<?php
$this->load->view(ADMIN_URL . '/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/css/datetimepicker.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/css/jquery.timepicker.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar');

    if ($this->input->post()) {
        foreach ($this->input->post() as $key => $value) {
            $$key = @htmlspecialchars($this->input->post($key));
        }
    } else {
        $FieldsArray = array('content_id', 'entity_id', 'name', 'ingredients', 'restaurant_id', 'category_id', 'price', 'menu_detail', 'recipe_detail', 'recipe_time', 'popular_item', 'availability', 'image', 'is_veg', 'recipe_time', 'check_add_ons', 'item_slug', 'vat', 'sd');
        foreach ($FieldsArray as $key) {
            $$key = @htmlspecialchars($edit_records->$key);
        }
    }
    if (isset($edit_records) && $edit_records != "") {
        $add_label    = $this->lang->line('edit') . ' ' . $this->lang->line('menu');
        $form_action  = base_url() . ADMIN_URL . '/' . $this->controller_name . "/edit_menu/" . $this->uri->segment('4') . '/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    } else {
        $add_label    = $this->lang->line('add') . ' ' . $this->lang->line('menu');
        $form_action  = base_url() . ADMIN_URL . '/' . $this->controller_name . "/add_menu/" . $this->uri->segment('4');
        $addons_detail = 1;
        $add_ons = array();
        $add_ons_detail = array();
    }
    $usertypes = getUserTypeList($this->session->userdata('language_slug'));
    ?>

    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('menu') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/restaurant/view_menu"><?php echo $this->lang->line('menu') ?></a>
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
                            <form action="<?php echo $form_action; ?>" id="form_add<?php echo $this->menu_prefix ?>" name="form_add<?php echo $this->menu_prefix ?>" method="post" class="form-horizontal horizontal-form-deal" enctype="multipart/form-data">
                                <div id="iframeloading" class="frame-load" style="background-color: white; z-index:1000">
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
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="col-form-label col-sm-3"><?php echo $this->lang->line('res_name') ?><span class="required">*</span></label>
                                                <div class="col-sm-8">

                                                    <select name="restaurant_id" class="form-control" id="restaurant_id">
                                                        <option value="<?= $restaurant_id ?>" selected><?= $res_name ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label class="col-form-label col-sm-3"><?php echo $this->lang->line('category') ?><span class="required">*</span></label>
                                                <div class="col-sm-8">
                                                    <select name="category_id" class="form-control" id="category_id">
                                                        <option value="<?= $category_id ?>" selected><?= $cat_name ?></option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="menu_repeater">
                                        <div data-repeater-list="menu-group">
                                            <div data-repeater-item class="item-group">

                                                <div class="form-group name_div">

                                                    <label class="col-form-label col-md-3"><?php echo $this->lang->line('name') ?><span class="required">*</span></label>
                                                    <div class="col-md-8">
                                                        <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id; ?>" />
                                                        <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id) ? $content_id : $this->uri->segment('5'); ?>" />
                                                        <input type="hidden" id="item_slug" name="item_slug" value="<?php echo ($item_slug) ? $item_slug : ''; ?>" />
                                                        <input type="text" name="name" id="name" value="<?php echo $name; ?>" maxlength="249" data-required="1" class="form-control" required />
                                                    </div>

                                                </div>
                                                <?php $inc = 1; ?>

                                                <div class="form-group">
                                                    <label class="col-form-label col-md-3"><?php echo $this->lang->line('detail') ?></label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="menu_details" id="menu_details" value="<?php echo $menu_detail; ?>" maxlength="249" data-required="1" class="form-control" />
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="form-group">
                                                        <label class="col-form-label col-md-3"><?php echo $this->lang->line('popular_item'); ?></label>
                                                        <div class="col-md-1">
                                                            <input type="checkbox" name="popular_item" id="popular_item" value="1" <?php echo (isset($popular_item) && $popular_item == 1) ? 'checked' : '' ?> />
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label class="col-form-label col-md-3"><?php echo $this->lang->line('add_add_ons') ?></label>
                                                        <div class="col-md-8">

                                                            <input type="checkbox" name="check_add_ons" id="check_add_ons<?php echo $inc; ?>" value="1" <?php echo ($check_add_ons == 1) ? 'checked' : '' ?> class="add_ons" onchange="callfunction(this.id,<?php echo $inc; ?>)">
                                                        </div>
                                                    </div>

                                                </div>

                                                <?php if (!empty($addons_category)) { ?>

                                                    <div class="form-group category_wrap <?php echo ($check_add_ons == 1) ? 'display-yes' : 'display-no' ?>" id="category_wrap<?php echo $inc; ?>">
                                                        <div class="col-md-3"></div>
                                                        <div class="col-md-9">
                                                            <div class="form-check-inline">
                                                                <label class="form-check-label">
                                                                    <input type="radio" class="form-check-input variation_radio" name="optradio" value="1" onchange="variation_radio_change(this)" <?= isset($isEdit) ? ($has_variation ? 'checked' : '') : '' ?>> Add Variation(s)
                                                                </label>
                                                            </div>

                                                            <div class="form-check-inline">
                                                                <label class="form-check-label">
                                                                    <input type="radio" class="form-check-input variation_radio" name="optradio" value="2" onchange="variation_radio_change(this)" <?= isset($isEdit) ? ($has_variation ? '' : 'checked') : '' ?>> Add only add-ons
                                                                </label>
                                                            </div>
                                                        </div>


                                                        <div>
                                                            <div class="row variation_div <?php echo (isset($isEdit) && $has_variation == 1) ? 'display-yes' : 'display-no' ?>">
                                                                <?php if ($variation_detail) { ?>
                                                                    <div class="variation-repeater">
                                                                        <div data-repeater-list="variation-group">
                                                                            <?php $variation_count = -1;
                                                                            foreach ($variation_detail as $variation) {
                                                                                $variation_count++;
                                                                            ?>
                                                                                <div data-repeater-item class="variation-group-class">

                                                                                    <div class="row">
                                                                                        <div class="col-sm-2"></div>
                                                                                        <div class="col-md-8 variations">
                                                                                            <div class="form-group">
                                                                                                <div class="col-md-4">
                                                                                                    <label class="col-form-label"><?php echo "Variation Name" ?><span class="required">*</span></label>
                                                                                                    <input type="text" name="variation_name[]" id="variation_name_1" value="<?= $variation['variation_name'] ?>" class="form-control repeater_field name_repeater" maxlength="249">
                                                                                                </div>
                                                                                                <div class="col-md-4">
                                                                                                    <label class="col-form-label"><?php echo $this->lang->line('price') ?><span class="required">*</span></label>
                                                                                                    <input type="text" name="variation_price" id="variation_price<?php echo $j ?>" value="<?= $variation['variation_price'] ?>" class="form-control repeater_field" maxlength="19">
                                                                                                </div>
                                                                                                <div class="col-sm-2">
                                                                                                    <label style="width: 250px;" class="col-form-label col-md-3"><?php echo $this->lang->line('add_add_ons') ?></label>
                                                                                                    <div class="col-md-8">
                                                                                                        <input type="checkbox" name="variation_add_on" id="variation_add_on" value="1" <?php echo ($variation['variation_add_on'] == 1) ? 'checked' : '' ?> class="variation_add_ons" onchange="variation_addon_change(this)">
                                                                                                    </div>

                                                                                                    <button type="button" class="btn btn-sm btn-primary variation_view_button" style="<?= $variation['variation_add_on'] == 1 ? 'display: block' : 'display: none' ?>;"><i class="fa fa-eye"></i></button>

                                                                                                </div>
                                                                                                <div class="col-sm-2 delete-repeat">
                                                                                                    <label class="col-form-label">&nbsp;</label>
                                                                                                    <input data-repeater-delete class="btn btn-danger" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                                </div>
                                                                                                <input type="hidden" name="multiple" value="<?php echo $_POST["is_multiple$value->entity_id"] ?>" />
                                                                                            </div>
                                                                                            <div class="modal modal-main" id="addon-modal-1">
                                                                                                <div class="modal-dialog modal-dialog-centered">
                                                                                                    <div class="modal-content">
                                                                                                        <!-- Modal Header -->
                                                                                                        <div class="modal-header">
                                                                                                            <h4 class="modal-title">Add Addon</h4>
                                                                                                            <button type="button" class="close modal-close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
                                                                                                        </div>
                                                                                                        <!-- Modal body -->
                                                                                                        <div class="modal-body">
                                                                                                            <div class="add-on-cat-repeater">
                                                                                                                <div class="row" class="add-on-cat-repeater_wrap margin-bottom-10" style="max-height: 600px; overflow-x: auto; padding: 10px; background-color: #eee;">
                                                                                                                    <div data-repeater-list="add-on-parent-cat" class="col-md-12">
                                                                                                                        <?php if ($variation['variation_add_on']) {
                                                                                                                            foreach ($variation['addon_list'] as $var_addon) { ?>
                                                                                                                                <div data-repeater-item class="cat-parent-repeat-item cat_form_group">
                                                                                                                                    <div class="form-group">
                                                                                                                                        <label for="Add_on_category_1">Add On Category</label>
                                                                                                                                        <select id="Add_on_category_1" class="form-control addon_cat_class_modal" name="addons_category_id" onchange="getAddonCatAddons(this)">
                                                                                                                                            <option value="">Select One</option>
                                                                                                                                            <?php $is_multiple = '';
                                                                                                                                            $j = 1;
                                                                                                                                            foreach ($addons_category as $key => $value) {
                                                                                                                                            ?>
                                                                                                                                                <option value="<?= $value->entity_id ?>" <?= $value->entity_id == $var_addon['category_id'] ? 'selected' : '' ?>><?= $value->name ?></option>
                                                                                                                                            <?php } ?>
                                                                                                                                        </select>
                                                                                                                                        <input type="hidden" name="max_choice" value="<?= $var_addon['max_choice'] ? $var_addon['max_choice'] : ''  ?>" class="max_choice_hiden" />
                                                                                                                                    </div>
                                                                                                                                    <!-- <input type="checkbox" class="category_checkbox1" <?php echo (in_array($value->entity_id, $add_ons)) ? 'checked' : '' ?> name="addons_category_id[]" id="<?php echo $inc; ?>addons_category_id<?php echo $value->entity_id ?>" value="<?php echo $value->entity_id ?>" onchange="addAddons('<?php echo $inc; ?>','<?php echo $j ?>','<?php echo $value->entity_id ?>',this.id)"> <?php echo $value->name ?> -->
                                                                                                                                    <div class="col 1_is_multiple_<?php echo $value->entity_id ?>" id="1_is_multiple_<?php echo $value->entity_id ?>" style="display: block;">
                                                                                                                                        <input type="checkbox" name="is_multiple" id="is_multiple<?php echo $value->entity_id ?>" value="1" <?php echo ($var_addon['is_multiple']) ? 'checked' : '' ?> onchange="show_max(this)"><?php echo $this->lang->line('is_multiple') ?>
                                                                                                                                    </div>
                                                                                                                                    <div id="<?php echo $inc; ?>add_ons_category<?php echo $j; ?>" class="outer-repeater repeater_wrap add_ons_category<?php echo $value->entity_id ?>">
                                                                                                                                        <div data-repeater-list="add_ons_list" class="add_ons_detail<?php echo $value->entity_id ?> addon_rep_parent">

                                                                                                                                            <?php
                                                                                                                                            $i = -1;
                                                                                                                                            foreach ($var_addon['final_addon'] as $final_addon) {
                                                                                                                                                $i++;
                                                                                                                                            ?>
                                                                                                                                                <div data-repeater-item>
                                                                                                                                                    <div class="form-group">
                                                                                                                                                        <div class="col-md-4">
                                                                                                                                                            <label class="col-form-label"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                                                                                                                                                            <input type="text" name="add_ons_name" id="add_ons_name<?php echo $j ?>" value="<?php echo $final_addon[0]  ?>" class="form-control repeater_field name_repeater" maxlength="249">
                                                                                                                                                        </div>
                                                                                                                                                        <div class="col-md-4">
                                                                                                                                                            <label class="col-form-label"><?php echo $this->lang->line('price') ?><span class="required">*</span></label>
                                                                                                                                                            <input type="text" name="add_ons_price" id="add_ons_price<?php echo $j ?>" value="<?php echo $final_addon[1] ?>" class="form-control repeater_field" maxlength="19">
                                                                                                                                                        </div>
                                                                                                                                                        <div class="col-sm-2 delete-repeat <?php echo ($i > 0) ? 'display-yes' : 'display-no'; ?>">
                                                                                                                                                            <label class="col-form-label">&nbsp;</label>
                                                                                                                                                            <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                                                                                        </div>
                                                                                                                                                        <input type="hidden" name="multiple" value="<?php echo $_POST["is_multiple$value->entity_id"] ?>" />
                                                                                                                                                    </div>
                                                                                                                                                </div>
                                                                                                                                            <?php } ?>

                                                                                                                                        </div>
                                                                                                                                        <div class="form-group" id="max_choice_div" style="<?= $var_addon['max_choice'] ? 'display:block' : 'display:none'; ?>">
                                                                                                                                            <div class="col-md-6">
                                                                                                                                                <label class="col-form-label"><?php echo $this->lang->line('max_required_choice') ?><span class="required">*</span></label>
                                                                                                                                                <input type="number" name="max_choice" id="max_choice" value="<?= $var_addon['max_choice'] ? $var_addon['max_choice']  : '';  ?>" class="form-control repeater_field name_repeater max-req" min="1" title="max choice" onchange="max_choice_change(this)" onkeyup="max_choice_change(this)">
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                        <div class="form-group">
                                                                                                                                            <div class="col-md-12 add_ons_detail<?php echo $value->entity_id ?>">
                                                                                                                                                <input data-repeater-create class="btn btn-green addon-repeat-button" type="button" value="<?php echo $this->lang->line('add') ?>" />
                                                                                                                                            </div>
                                                                                                                                        </div>
                                                                                                                                        <?php //}
                                                                                                                                        ?>
                                                                                                                                    </div>
                                                                                                                                    <div class="col-sm-12 delete-repeat margin-bottom-10">
                                                                                                                                        <!-- <label class="col-form-label">&nbsp;</label> -->
                                                                                                                                        <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            <?php  }
                                                                                                                        } else { ?>
                                                                                                                            <div data-repeater-item class="cat-parent-repeat-item cat_form_group">

                                                                                                                                <div class="form-group">
                                                                                                                                    <label for="Add_on_category_1">Add On Category</label>
                                                                                                                                    <select id="Add_on_category_1" class="form-control addon_cat_class_modal" name="addons_category_id" onchange="getAddonCatAddons(this)">
                                                                                                                                        <option value="">Select One</option>
                                                                                                                                        <?php $is_multiple = '';
                                                                                                                                        $j = 1;
                                                                                                                                        foreach ($addons_category as $key => $value) {
                                                                                                                                            $addons_detail = (array_key_exists($value->entity_id, $add_ons_detail)) ? $add_ons_detail[$value->entity_id] : 1; ?>
                                                                                                                                            <option value="<?= $value->entity_id ?>"><?= $value->name ?></option>
                                                                                                                                        <?php } ?>
                                                                                                                                    </select>
                                                                                                                                    <input type="hidden" name="max_choice" value="" class="max_choice_hiden" />
                                                                                                                                </div>
                                                                                                                                <!-- <input type="checkbox" class="category_checkbox1" <?php echo (in_array($value->entity_id, $add_ons)) ? 'checked' : '' ?> name="addons_category_id[]" id="<?php echo $inc; ?>addons_category_id<?php echo $value->entity_id ?>" value="<?php echo $value->entity_id ?>" onchange="addAddons('<?php echo $inc; ?>','<?php echo $j ?>','<?php echo $value->entity_id ?>',this.id)"> <?php echo $value->name ?> -->
                                                                                                                                <div class="col 1_is_multiple_<?php echo $value->entity_id ?>" id="1_is_multiple_<?php echo $value->entity_id ?>" style="display: block;">
                                                                                                                                    <input type="checkbox" name="is_multiple" id="is_multiple<?php echo $value->entity_id ?>" value="1" <?php echo ($is_multiple) ? 'checked' : '' ?> onchange="show_max(this)"><?php echo $this->lang->line('is_multiple') ?>
                                                                                                                                </div>
                                                                                                                                <div id="<?php echo $inc; ?>add_ons_category<?php echo $j; ?>" class="outer-repeater repeater_wrap add_ons_category<?php echo $value->entity_id ?>">
                                                                                                                                    <div data-repeater-list="add_ons_list" class="add_ons_detail<?php echo $value->entity_id ?> addon_rep_parent">

                                                                                                                                        <div data-repeater-item>
                                                                                                                                            <div class="form-group">
                                                                                                                                                <div class="col-md-4">
                                                                                                                                                    <label class="col-form-label"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                                                                                                                                                    <input type="text" name="add_ons_name" id="add_ons_name<?php echo $j ?>" value="<?php echo (!empty($addons_detail[$i])) ? $addons_detail[$i]->add_ons_name : ''; ?>" class="form-control repeater_field name_repeater" maxlength="249">
                                                                                                                                                </div>
                                                                                                                                                <div class="col-md-4">
                                                                                                                                                    <label class="col-form-label"><?php echo $this->lang->line('price') ?><span class="required">*</span></label>
                                                                                                                                                    <input type="text" name="add_ons_price" id="add_ons_price<?php echo $j ?>" value="<?php echo (!empty($addons_detail[$i])) ? $addons_detail[$i]->add_ons_price : ''; ?>" class="form-control repeater_field" maxlength="19">
                                                                                                                                                </div>
                                                                                                                                                <div class="col-sm-2 delete-repeat <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'display-yes' : 'display-no'; ?>">
                                                                                                                                                    <label class="col-form-label">&nbsp;</label>
                                                                                                                                                    <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                                                                                </div>
                                                                                                                                                <input type="hidden" name="multiple" value="<?php echo $_POST["is_multiple$value->entity_id"] ?>" />
                                                                                                                                            </div>
                                                                                                                                        </div>

                                                                                                                                    </div>
                                                                                                                                    <div class="form-group" id="max_choice_div" style="display: none;">
                                                                                                                                        <div class="col-md-6">
                                                                                                                                            <label class="col-form-label"><?php echo $this->lang->line('max_required_choice') ?><span class="required">*</span></label>
                                                                                                                                            <input type="number" name="max_choice" id="max_choice<?php echo $j ?>" value="<?php echo (!empty($addons_detail[$i])) ? $addons_detail[$i]->max_choice : 0; ?>" class="form-control repeater_field name_repeater max-req" min="0" title="max choice" onchange="max_choice_change(this)" onkeyup="max_choice_change(this)">
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                    <div class="form-group">
                                                                                                                                        <div class="col-md-12 add_ons_detail<?php echo $value->entity_id ?>">
                                                                                                                                            <input data-repeater-create class="btn btn-green addon-repeat-button" type="button" value="<?php echo $this->lang->line('add') ?>" />
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                    <?php //}
                                                                                                                                    ?>
                                                                                                                                </div>
                                                                                                                                <div class="col-sm-12 delete-repeat margin-bottom-10">
                                                                                                                                    <!-- <label class="col-form-label">&nbsp;</label> -->
                                                                                                                                    <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        <?php } ?>

                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <input data-repeater-create class="btn btn-green margin-top-10" type="button" value="Add Category" />
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        <div class="modal-footer">
                                                                                                            <button type="button" class="btn btn-secondary modal-close" data-dismiss="modal">Close</button>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <!-- <div class="col-sm-2 delete-repeat-variation <?php echo ($variation_count > 0) ? 'display-yes' : 'display-no'; ?>">
                                                                                            <label class="col-form-label">&nbsp;</label>
                                                                                            <input data-repeater-delete class="btn btn-danger" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                        </div> -->
                                                                                    </div>
                                                                                </div>
                                                                            <?php } ?>
                                                                            <div class="row">
                                                                                <input data-repeater-create type="button" class="btn btn-green" value="Add" style="margin-left: 18%; margin-bottom: 20px; width: 30x;" />
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                <?php

                                                                } else { ?>
                                                                    <div class="variation-repeater">
                                                                        <div data-repeater-list="variation-group">
                                                                            <div data-repeater-item class="variation-group-class">

                                                                                <div class="row">
                                                                                    <div class="col-sm-2"></div>
                                                                                    <div class="col-md-8 variations">
                                                                                        <div class="form-group">
                                                                                            <div class="col-md-4">
                                                                                                <label class="col-form-label"><?php echo "Variation Name" ?><span class="required">*</span></label>
                                                                                                <input type="text" name="variation_name[]" id="variation_name_1" value="" class="form-control repeater_field name_repeater" maxlength="249">
                                                                                            </div>
                                                                                            <div class="col-md-4">
                                                                                                <label class="col-form-label"><?php echo $this->lang->line('price') ?><span class="required">*</span></label>
                                                                                                <input type="text" name="variation_price" id="variation_price<?php echo $j ?>" value="" class="form-control repeater_field" maxlength="19">
                                                                                            </div>
                                                                                            <div class="col-sm-2">
                                                                                                <label style="width: 250px;" class="col-form-label col-md-3"><?php echo $this->lang->line('add_add_ons') ?></label>
                                                                                                <div class="col-md-8">
                                                                                                    <input type="checkbox" name="variation_add_on" id="variation_add_on" value="1" <?php echo ($check_add_ons == 1) ? 'checked' : '' ?> class="variation_add_ons" onchange="variation_addon_change(this)">
                                                                                                </div>
                                                                                                <button type="button" class="btn btn-sm btn-primary variation_view_button" style="display: none;"><i class="fa fa-eye"></i></button>

                                                                                            </div>
                                                                                            <div class="col-sm-2 delete-repeat">
                                                                                                <label class="col-form-label">&nbsp;</label>
                                                                                                <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                            </div>
                                                                                            <input type="hidden" name="multiple" value="<?php echo $_POST["is_multiple$value->entity_id"] ?>" />
                                                                                        </div>
                                                                                        <div class="modal modal-main" id="addon-modal-1">
                                                                                            <div class="modal-dialog modal-dialog-centered">
                                                                                                <div class="modal-content">
                                                                                                    <!-- Modal Header -->
                                                                                                    <div class="modal-header">
                                                                                                        <h4 class="modal-title">Add Addon</h4>
                                                                                                        <button type="button" class="close modal-close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
                                                                                                    </div>
                                                                                                    <!-- Modal body -->
                                                                                                    <div class="modal-body">
                                                                                                        <div class="add-on-cat-repeater">
                                                                                                            <div class="row" class="add-on-cat-repeater_wrap margin-bottom-10" style="max-height: 600px; overflow-x: auto; padding: 10px; background-color: #eee;">
                                                                                                                <div data-repeater-list="add-on-parent-cat" class="col-md-12">
                                                                                                                    <div data-repeater-item class="cat-parent-repeat-item cat_form_group">
                                                                                                                        <div class="form-group">
                                                                                                                            <label for="Add_on_category_1">Add On Category</label>
                                                                                                                            <select id="Add_on_category_1" class="form-control addon_cat_class_modal" name="addons_category_id" onchange="getAddonCatAddons(this)">
                                                                                                                                <option value="">Select One</option>
                                                                                                                                <?php $is_multiple = '';
                                                                                                                                $j = 1;
                                                                                                                                foreach ($addons_category as $key => $value) {
                                                                                                                                    $addons_detail = (array_key_exists($value->entity_id, $add_ons_detail)) ? $add_ons_detail[$value->entity_id] : 1; ?>
                                                                                                                                    <option value="<?= $value->entity_id ?>"><?= $value->name ?></option>
                                                                                                                                <?php } ?>
                                                                                                                            </select>
                                                                                                                            <input type="hidden" name="max_choice" value="" class="max_choice_hiden" />
                                                                                                                        </div>
                                                                                                                        <div class="col 1_is_multiple_<?php echo $value->entity_id ?>" id="1_is_multiple_<?php echo $value->entity_id ?>" style="display: block;">
                                                                                                                            <input type="checkbox" name="is_multiple" id="is_multiple<?php echo $value->entity_id ?>" value="1" <?php echo ($is_multiple) ? 'checked' : '' ?> onchange="show_max(this)"><?php echo $this->lang->line('is_multiple') ?>
                                                                                                                        </div>
                                                                                                                        <div id="<?php echo $inc; ?>add_ons_category<?php echo $j; ?>" class="outer-repeater repeater_wrap add_ons_category<?php echo $value->entity_id ?> ">
                                                                                                                            <div data-repeater-list="add_ons_list" class="add_ons_detail<?php echo $value->entity_id ?> addon_rep_parent">

                                                                                                                                <div data-repeater-item>
                                                                                                                                    <div class="form-group">
                                                                                                                                        <div class="col-md-4">
                                                                                                                                            <label class="col-form-label"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                                                                                                                                            <input type="text" name="add_ons_name" id="add_ons_name<?php echo $j ?>" value="<?php echo (!empty($addons_detail[$i])) ? $addons_detail[$i]->add_ons_name : ''; ?>" class="form-control repeater_field name_repeater" maxlength="249">
                                                                                                                                        </div>
                                                                                                                                        <div class="col-md-4">
                                                                                                                                            <label class="col-form-label"><?php echo $this->lang->line('price') ?><span class="required">*</span></label>
                                                                                                                                            <input type="text" name="add_ons_price" id="add_ons_price<?php echo $j ?>" value="<?php echo (!empty($addons_detail[$i])) ? $addons_detail[$i]->add_ons_price : ''; ?>" class="form-control repeater_field" maxlength="19">
                                                                                                                                        </div>
                                                                                                                                        <div class="col-sm-2 delete-repeat <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'display-yes' : 'display-no'; ?>">
                                                                                                                                            <label class="col-form-label">&nbsp;</label>
                                                                                                                                            <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                                                                        </div>
                                                                                                                                        <input type="hidden" name="multiple" value="<?php echo $_POST["is_multiple$value->entity_id"] ?>" />
                                                                                                                                    </div>
                                                                                                                                </div>

                                                                                                                            </div>
                                                                                                                            <div class="form-group" id="max_choice_div" style="display: none;">
                                                                                                                                <div class="col-md-6">
                                                                                                                                    <label class="col-form-label"><?php echo $this->lang->line('max_required_choice') ?><span class="required">*</span></label>
                                                                                                                                    <input type="number" name="max_choice" id="max_choice<?php echo $j ?>" value="<?php echo (!empty($addons_detail[$i])) ? $addons_detail[$i]->max_choice : 0; ?>" class="form-control repeater_field name_repeater max-req" min="0" title="max choice" onchange="max_choice_change(this)" onkeyup="max_choice_change(this)">
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="form-group">
                                                                                                                                <div class="col-md-12 add_ons_detail<?php echo $value->entity_id ?>">
                                                                                                                                    <input data-repeater-create class="btn btn-green addon-repeat-button" type="button" value="<?php echo $this->lang->line('add') ?>" />
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <?php //}
                                                                                                                            ?>
                                                                                                                        </div>
                                                                                                                        <div class="col-sm-12 delete-repeat margin-bottom-10">
                                                                                                                            <!-- <label class="col-form-label">&nbsp;</label> -->
                                                                                                                            <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <input data-repeater-create class="btn btn-green margin-top-10" type="button" value="Add Category" />
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="modal-footer">
                                                                                                        <button type="button" class="btn btn-secondary modal-close" data-dismiss="modal">Close</button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                    </div>
                                                                                    <div class="col-sm-2 delete-repeat-variation <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'display-yes' : 'display-no'; ?>">
                                                                                        <label class="col-form-label">&nbsp;</label>
                                                                                        <input data-repeater-delete class="btn btn-danger" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                        </div>
                                                                        <div class="row">
                                                                            <input data-repeater-create type="button" class="btn btn-green" value="Add" style="margin-left: 18%; margin-bottom: 20px; width: 30x;" />
                                                                        </div>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                            <div class="row only_addon_div" style="<?php echo (isset($isEdit) && $has_variation != 1) ? 'display:block' : 'display:none' ?>">
                                                                <?php if ($add_ons_detail) { ?>
                                                                    <div class="row">
                                                                        <div class="col-sm-2"></div>
                                                                        <div class="only-add-on-cat-repeater col-sm-8" style="margin: 1px solid black; padding : 30px">
                                                                            <div class="row" class="only-add-on-cat-repeater_wrap margin-bottom-10" style="max-height: 600px; overflow-x: auto; padding: 10px; background-color: #eee;">
                                                                                <div data-repeater-list="only-add-on-parent-cat" class="col-md-12">
                                                                                    <?php
                                                                                    $each_addon_count = -1;
                                                                                    foreach ($add_ons_detail as $each_addon) {
                                                                                        $each_addon_count++;
                                                                                    ?>
                                                                                        <div data-repeater-item class="only-add-on-cat-parent-repeat-item cat_form_group">
                                                                                            <div>
                                                                                                <div class="form-group">
                                                                                                    <label for="Add_on_category_1">Add On Category</label>
                                                                                                    <select id="Add_on_category_1" class="form-control addon_cat_class" name="addons_category_id" onchange="getAddonCatAddons(this)">
                                                                                                        <option value="">Select One</option>
                                                                                                        <?php $is_multiple = '';
                                                                                                        $j = 1;
                                                                                                        foreach ($addons_category as $key => $value) {
                                                                                                            $addons_detail = (array_key_exists($value->entity_id, $add_ons_detail)) ? $add_ons_detail[$value->entity_id] : 1; ?>
                                                                                                            <option value="<?= $value->entity_id ?>" <?= $each_addon['category_id'] == $value->entity_id ? 'selected' : '' ?>><?= $value->name ?></option>
                                                                                                        <?php } ?>
                                                                                                    </select>
                                                                                                    <input type="hidden" name="max_choice" value="<?= $each_addon['max_choice'] ? $each_addon['max_choice'] : ''  ?>" class="max_choice_hiden" />
                                                                                                </div>
                                                                                                <div class="col 1_is_multiple_ id=" 1_is_multiple_" style="display: block;">
                                                                                                    <input type="checkbox" name="is_multiple" id="is_multiple" value="1" <?php echo ($each_addon['is_multiple']) ? 'checked' : '' ?> onchange="show_max(this)"><?php echo $this->lang->line('is_multiple') ?>
                                                                                                </div>
                                                                                                <div id="<?php echo $inc; ?>add_ons_category<?php echo $j; ?>" class="only-addon-outer-repeater repeater_wrap add_ons_category<?php echo $value->entity_id ?>">
                                                                                                    <div data-repeater-list="only-add-on-add_ons_list" class="add_ons_detail addon_rep_parent">
                                                                                                        <?php
                                                                                                        $i = -1;
                                                                                                        foreach ($each_addon['final_addon'] as $final_addon) {
                                                                                                            $i++; ?>
                                                                                                            <div data-repeater-item>
                                                                                                                <div class="form-group">
                                                                                                                    <div class="col-md-4">
                                                                                                                        <label class="col-form-label"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                                                                                                                        <input type="text" name="add_ons_name" id="add_ons_name<?php echo $j ?>" value="<?php echo (!empty($final_addon[0])) ? $final_addon[0] : ''; ?>" class="form-control repeater_field name_repeater" maxlength="249">
                                                                                                                    </div>
                                                                                                                    <div class="col-md-4">
                                                                                                                        <label class="col-form-label"><?php echo $this->lang->line('price') ?><span class="required">*</span></label>
                                                                                                                        <input type="text" name="add_ons_price" id="add_ons_price<?php echo $j ?>" value="<?php echo (!empty($final_addon[1])) ? $final_addon[1] : ''; ?>" class="form-control repeater_field" maxlength="19">
                                                                                                                    </div>
                                                                                                                    <div class="col-sm-2 delete-repeat <?php echo ($i > 0) ? 'display-yes' : 'display-no'; ?>">
                                                                                                                        <label class="col-form-label">&nbsp;</label>
                                                                                                                        <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                                                    </div>
                                                                                                                    <input type="hidden" name="multiple" value="<?php echo $_POST["is_multiple$value->entity_id"] ?>" />
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        <?php } ?>
                                                                                                    </div>
                                                                                                    <?php //if($i == 0){
                                                                                                    ?>
                                                                                                    <div class="form-group">
                                                                                                        <div class="col-md-12 add_ons_detail<?php echo $value->entity_id ?>">
                                                                                                            <input data-repeater-create class="btn btn-green addon-repeat-button" type="button" value="<?php echo $this->lang->line('add') ?>" />
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <div class="form-group" id="max_choice_div" style="<?= $each_addon['max_choice'] ? 'display:block' : 'display:none'; ?>">
                                                                                                        <div class="col-md-6">
                                                                                                            <label class="col-form-label"><?php echo $this->lang->line('max_required_choice') ?><span class="required">*</span></label>
                                                                                                            <input type="number" name="" id="max_choice<?php echo $j ?>" value="<?= $each_addon['max_choice'] ? $each_addon['max_choice'] : '' ?>" class="form-control repeater_field name_repeater max-req" min="0" title="max choice" onchange="max_choice_change(this)" onkeyup="max_choice_change(this)">
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    <?php //}
                                                                                                    ?>
                                                                                                </div>
                                                                                                <div class="col-sm-12 delete-repeat margin-bottom-10">
                                                                                                    <!-- <label class="col-form-label">&nbsp;</label> -->
                                                                                                    <input data-repeater-delete class="btn btn-danger <?php echo ($each_addon_count > 0) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                                </div>
                                                                                            </div>

                                                                                        </div>
                                                                                    <?php } ?>
                                                                                </div>
                                                                                <input data-repeater-create class="btn btn-green margin-top-10" type="button" value="Add Category" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                <?php } else { ?>

                                                                    <div class="row">
                                                                        <div class="col-sm-2"></div>
                                                                        <div class="only-add-on-cat-repeater col-sm-8" style="border: 1px solid black; padding: 30px">
                                                                            <div class="row" class="only-add-on-cat-repeater_wrap margin-bottom-10" style="max-height: 600px; overflow-x: auto; padding: 10px; background-color: #eee;">
                                                                                <div data-repeater-list="only-add-on-parent-cat" class="col-md-12">
                                                                                    <div data-repeater-item class="only-add-on-cat-parent-repeat-item cat_form_group">
                                                                                        <div class="form-group">
                                                                                            <label for="Add_on_category_1">Add On Category</label>
                                                                                            <select id="Add_on_category_1" class="form-control addon_cat_class" name="addons_category_id" onchange="getAddonCatAddons(this)">
                                                                                                <option value="">Select One</option>
                                                                                                <?php $is_multiple = '';
                                                                                                $j = 1;
                                                                                                foreach ($addons_category as $key => $value) {
                                                                                                    $addons_detail = (array_key_exists($value->entity_id, $add_ons_detail)) ? $add_ons_detail[$value->entity_id] : 1; ?>
                                                                                                    <option value=" <?= $value->entity_id ?> "><?= $value->name ?></option>
                                                                                                <?php } ?>
                                                                                            </select>
                                                                                            <input type="hidden" name="max_choice" value="" class="max_choice_hiden" />

                                                                                        </div>
                                                                                        <div class="col 1_is_multiple_ id=" 1_is_multiple_" style="display: block;">
                                                                                            <input type="checkbox" name="is_multiple" id="is_multiple" value="1" <?php echo ($is_multiple) ? 'checked' : '' ?> onchange="show_max(this)"><?php echo $this->lang->line('is_multiple') ?>
                                                                                        </div>
                                                                                        <div id="<?php echo $inc; ?>add_ons_category<?php echo $j; ?>" class="only-addon-outer-repeater repeater_wrap add_ons_category<?php echo $value->entity_id ?> ">
                                                                                            <div data-repeater-list="only-add-on-add_ons_list" class="add_ons_detail addon_rep_parent">
                                                                                                <div data-repeater-item>
                                                                                                    <div class="form-group">
                                                                                                        <div class="col-md-4">
                                                                                                            <label class="col-form-label"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                                                                                                            <input type="text" name="add_ons_name" id="add_ons_name<?php echo $j ?>" value="<?php echo (!empty($addons_detail[$i])) ? $addons_detail[$i]->add_ons_name : ''; ?>" class="form-control repeater_field name_repeater" maxlength="249">
                                                                                                        </div>
                                                                                                        <div class="col-md-4">
                                                                                                            <label class="col-form-label"><?php echo $this->lang->line('price') ?><span class="required">*</span></label>
                                                                                                            <input type="text" name="add_ons_price" id="add_ons_price<?php echo $j ?>" value="<?php echo (!empty($addons_detail[$i])) ? $addons_detail[$i]->add_ons_price : ''; ?>" class="form-control repeater_field" maxlength="19">
                                                                                                        </div>
                                                                                                        <div class="col-sm-2 delete-repeat <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'display-yes' : 'display-no'; ?>">
                                                                                                            <label class="col-form-label">&nbsp;</label>
                                                                                                            <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                                        </div>
                                                                                                        <input type="hidden" name="multiple" value="<?php echo $_POST["is_multiple$value->entity_id"] ?>" />
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <div class="col-md-12 add_ons_detail<?php echo $value->entity_id ?>">
                                                                                                    <input data-repeater-create class="btn btn-green addon-repeat-button" type="button" value="<?php echo $this->lang->line('add') ?>" />
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group" id="max_choice_div" style="display: none;">
                                                                                                <div class="col-md-6">
                                                                                                    <label class="col-form-label"><?php echo $this->lang->line('max_required_choice') ?><span class="required">*</span></label>
                                                                                                    <input type="number" name="" id="max_choice<?php echo $j ?>" value="<?php echo (!empty($addons_detail[$i])) ? $addons_detail[$i]->max_choice : 0; ?>" class="form-control repeater_field name_repeater max-req" min="0" title="max choice" onchange="max_choice_change(this)" onkeyup="max_choice_change(this)">
                                                                                                </div>
                                                                                            </div>
                                                                                            <?php //}
                                                                                            ?>
                                                                                        </div>
                                                                                        <div class="col-sm-12 delete-repeat margin-bottom-10">
                                                                                            <!-- <label class="col-form-label">&nbsp;</label> -->
                                                                                            <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0 && !empty($add_ons_detail)) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <input data-repeater-create class="btn btn-green margin-top-10" type="button" value="Add Category" />
                                                                        </div>
                                                                    </div>

                                                                <?php } ?>
                                                            </div>
                                                        </div>


                                                        <!-- <label  class="col-form-label col-md-3"><?php echo $this->lang->line('addons_category') ?></label> -->

                                                    </div>
                                                <?php } ?>
                                                <!-- <div style="display: flex; justify-content: space-between; width: 70%;"> -->
                                                <div class="row">

                                                    <div class="col-sm-12">
                                                        <div style="<?= $edit_records->check_add_ons == 1 ? '' : '' ?>" class="form-group price_tag <?php echo ($edit_records->check_add_ons == 1) ? 'display-no' : 'display-yes' ?>" id="price_tag<?php echo $inc; ?>">
                                                            <label class="col-form-label col-sm-3"><?php echo $this->lang->line('price') ?> <span id="currency-symbol"></span><span class="required">*</span></label>
                                                            <div class="col-sm-3">
                                                                <input type="text" name="price" id="price<?php echo $inc; ?>" value="<?php echo ($price) ? $price : 0 ?>" maxlength="19" data-required="1" class="form-control price" <?= $edit_records->check_add_ons == 1 ? '' :  'required' ?> />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="form-group">
                                                        <div class="col-sm-12">
                                                            <label class="col-form-label col-sm-3">VAT(%)</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" class="vat" name="vat" id="vat" value="<?php echo $vat ?>" maxlength="10" data-required="1" class="form-control" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <div class="form-group">
                                                            <label class="col-form-label col-sm-3">SD(%)</label>
                                                            <div class="col-sm-3">
                                                                <input type="text" class="sd" name="sd" id="sd" value="<?php echo $sd ?>" maxlength="10" data-required="1" class="form-control" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="col-form-label col-md-3"><?php echo $this->lang->line('image') ?></label>
                                                    <div class="col-md-4">
                                                        <div class="custom-file-upload">
                                                            <label for="Image<?php echo $inc; ?>" class="custom-file-upload">
                                                                <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                            </label>
                                                            <input type="file" name="Image" id="Image<?php echo $inc; ?>" accept="image/*" class="Image" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this,<?php echo $inc; ?>)" />
                                                        </div>
                                                        <p class="help-block"><?php echo $this->lang->line('img_allow'); ?><br /> <?php echo $this->lang->line('max_file_size'); ?><br /><?php echo $this->lang->line('recommended_size') . '290 * 210.'; ?></p>
                                                        <span class="error display-no" id="errormsg"></span>
                                                        <div id="img_gallery"></div>
                                                        <?php if ($isEdit && $image) { ?>
                                                            <img id="preview<?php echo $inc; ?>" name="preview" height='100' width='150' class="preview <?= isset($image) ? '' : 'display-no' ?>" src="<?= isset($image) ? image_url .  $image : '' ?>" />
                                                        <?php } else { ?>
                                                            <img id="preview<?php echo $inc; ?>" name="preview" height='100' width='150' class="preview display-no" />
                                                        <?php } ?>

                                                        <input type="hidden" class="uploaded_image" name="uploaded_image" id="uploaded_image" value="<?php echo isset($image) ? $image : ''; ?>" />
                                                    </div>
                                                </div>
                                                <div>

                                                    <div class="form-group">
                                                        <label class="col-form-label col-md-3"><?php echo $this->lang->line('recipe_time'); ?><span class="required">*</span></label>
                                                        <div class="col-md-8">
                                                            <input style="padding-left: 153px;" type="number" class="form-control" name="recipe_time" id="recipe_time" value="<?php echo $recipe_time ?>" required>
                                                        </div>
                                                    </div>

                                                </div>

                                                <div class="form-group company-timing">
                                                    <label class="control-label col-md-3"><?php echo "Menu Availability" ?></label>
                                                    <?php if (empty($_POST['timings'])) {
                                                        $business_timings = unserialize(html_entity_decode($availability));
                                                    } else {
                                                        $timingsArr = $_POST['timings'];
                                                        $newTimingArr = array();
                                                        foreach ($timingsArr as $key => $value) {
                                                            if (isset($value['on'])) {
                                                                $newTimingArr[$key]['open'] = '';
                                                                $newTimingArr[$key]['close'] = '';
                                                                $newTimingArr[$key]['on'] = '0';
                                                            } else {
                                                                if (!empty($value['open']) && !empty($value['close'])) {
                                                                    $newTimingArr[$key]['open'] = $value['open'];
                                                                    $newTimingArr[$key]['close'] = $value['close'];
                                                                    $newTimingArr[$key]['on'] = '1';
                                                                } else {
                                                                    $newTimingArr[$key]['open'] = '';
                                                                    $newTimingArr[$key]['close'] = '';
                                                                    $newTimingArr[$key]['on'] = '0';
                                                                }
                                                            }
                                                        }
                                                        $business_timings = $newTimingArr;
                                                    }  ?>
                                                    <div class="row">
                                                        <div class="col-sm-3"></div>
                                                        <div class="col-sm-8">
                                                            <table class="timingstable" width="100%" cellpadding="2" cellspacing="2">
                                                                <tr>
                                                                    <td><strong><?php echo "Morning" ?></strong></td>
                                                                    <td>
                                                                        <input type="hidden" class="morning-on" repeater-keep-full-name="on" name="[timings][morning][on]" value="morning">
                                                                        <input type="checkbox" repeater-keep-full-name="on" <?php echo (intval(@$business_timings['morning']['on'])) ? 'checked="checked"' : ''; ?> value="morning" class="close_bar_check morning-on-check" id="morning_close" name="[timings][morning][on]">
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-wrap">
                                                                            <!-- <input type="text" class="ophrs morning-open morning_open_hours" repeater-keep-full-name="on" lesserThan="#morning_close_hours" id="morning_open_hours" name="[timings][morning][open]" <?php echo (intval(@$business_timings['morning']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['morning']['open']; ?>" placeholder="Start Time" autocomplete="off"> -->
                                                                            <input type="text" class="ophrs morning-open morning_open_hours" repeater-keep-full-name="on" id="morning_open_hours" name="[timings][morning][open]" <?php echo (intval(@$business_timings['morning']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['morning']['open']; ?>" placeholder="Start Time" autocomplete="off">
                                                                        </div>
                                                                        <div class="td-wrap">
                                                                            <!-- <input type="text" class="clhrs morning-close morning_close_hours" repeater-keep-full-name="on" greaterThan="#morning_open_hours" placeholder="End Time" <?php echo (intval(@$business_timings['morning']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['morning']['close']; ?>" name="[timings][morning][close]" id="morning_close_hours" autocomplete="off"> -->
                                                                            <input type="text" class="clhrs morning-close morning_close_hours" repeater-keep-full-name="on" placeholder="End Time" <?php echo (intval(@$business_timings['morning']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['morning']['close']; ?>" name="[timings][morning][close]" id="morning_close_hours" autocomplete="off">
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <strong><?php echo "Lunch" ?></strong>
                                                                    </td>
                                                                    <td>
                                                                        <input type="hidden" class="lunch-on" repeater-keep-full-name="on" name="[timings][lunch][on]" value="lunch">
                                                                        <input repeater-keep-full-name="on" type="checkbox" <?php echo (intval(@$business_timings['lunch']['on'])) ? 'checked="checked"' : ''; ?> value="lunch" class="close_bar_check lunch-on-check" id="lunch_close" name="[timings][lunch][on]">
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-wrap">
                                                                            <!-- <input type="text" class="ophrs lunch-open lunch_open_hours" repeater-keep-full-name="on" lesserThan="#lunch_close_hours" placeholder="Start Time" <?php echo (intval(@$business_timings['lunch']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['lunch']['open']; ?>" name="[timings][lunch][open]" id="lunch_open_hours" autocomplete="off"> -->
                                                                            <input type="text" class="ophrs lunch-open lunch_open_hours" repeater-keep-full-name="on" placeholder="Start Time" <?php echo (intval(@$business_timings['lunch']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['lunch']['open']; ?>" name="[timings][lunch][open]" id="lunch_open_hours" autocomplete="off">
                                                                        </div>
                                                                        <div class="td-wrap">
                                                                            <!-- <input type="text" class="clhrs lunch-close lunch_close_hours" repeater-keep-full-name="on" greaterThan="#lunch_open_hours" placeholder="End Time" <?php echo (intval(@$business_timings['lunch']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['lunch']['close']; ?>" name="[timings][lunch][close]" id="lunch_close_hours" autocomplete="off"> -->
                                                                            <input type="text" class="clhrs lunch-close lunch_close_hours" repeater-keep-full-name="on" placeholder="End Time" <?php echo (intval(@$business_timings['lunch']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['lunch']['close']; ?>" name="[timings][lunch][close]" id="lunch_close_hours" autocomplete="off">
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong><?php echo "Dinner" ?></strong></td>
                                                                    <td>
                                                                        <input type="hidden" class="dinner-on" repeater-keep-full-name="on" name="[timings][dinner][on]" value="dinner">
                                                                        <input repeater-keep-full-name="on" type="checkbox" <?php echo (intval(@$business_timings['dinner']['on'])) ?  'checked="checked"' : ''; ?> value="dinner" class="close_bar_check dinner-on-check" id="dinner_close" name="[timings][dinner][on]">
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-wrap">
                                                                            <!-- <input type="text" class="ophrs dinner-open dinner_open_hours" repeater-keep-full-name="on" placeholder="Start Time" <?php echo (intval(@$business_timings['dinner']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['dinner']['open']; ?>" name="[timings][dinner][open]" id="dinner_open_hours" lesserThan="#dinner_close_hours" autocomplete="off"> -->
                                                                            <input type="text" class="ophrs dinner-open dinner_open_hours" repeater-keep-full-name="on" placeholder="Start Time" <?php echo (intval(@$business_timings['dinner']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['dinner']['open']; ?>" name="[timings][dinner][open]" id="dinner_open_hours" autocomplete="off">
                                                                        </div>
                                                                        <div class="td-wrap">
                                                                            <!-- <input type="text" class="clhrs dinner-close dinner_close_hours" repeater-keep-full-name="on" placeholder="End Time" <?php echo (intval(@$business_timings['dinner']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['dinner']['close']; ?>" name="[timings][dinner][close]" id="dinner_close_hours" greaterThan="#dinner_open_hours" autocomplete="off"> -->
                                                                            <input type="text" class="clhrs dinner-close dinner_close_hours" repeater-keep-full-name="on" placeholder="End Time" <?php echo (intval(@$business_timings['dinner']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['dinner']['close']; ?>" name="[timings][dinner][close]" id="dinner_close_hours" autocomplete="off">
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong><?php echo "Late Night" ?></strong></td>
                                                                    <td>
                                                                        <input type="hidden" class="late-night-on" repeater-keep-full-name="on" name="[timings][late_night][on]" value="late_night">
                                                                        </label><input type="checkbox" repeater-keep-full-name="on" <?php echo (intval(@$business_timings['late_night']['on'])) ? 'checked="checked"' : ''; ?> value="late_night" class="close_bar_check late-night-on-check" id="late_night_close" name="[timings][late_night][on]">
                                                                    </td>
                                                                    <td>
                                                                        <div class="td-wrap">
                                                                            <!-- <input type="text" class="ophrs late-night-open late_night_open_hours" repeater-keep-full-name="on" placeholder="Start Time" <?php echo (intval(@$business_timings['late_night']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['late_night']['open']; ?>" name="[timings][late_night][open]" id="late_night_open_hours" lesserThan="#late_night_open_hours" autocomplete="off"> -->
                                                                            <input type="text" class="ophrs late-night-open late_night_open_hours" repeater-keep-full-name="on" placeholder="Start Time" <?php echo (intval(@$business_timings['late_night']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['late_night']['open']; ?>" name="[timings][late_night][open]" id="late_night_open_hours" autocomplete="off">
                                                                        </div>
                                                                        <div class="td-wrap">
                                                                            <!-- <input type="text" class="clhrs late-night-close late_night_close_hours" repeater-keep-full-name="on" placeholder="End Time" <?php echo (intval(@$business_timings['late_night']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['late_night']['close']; ?>" name="[timings][late_night][close]" id="late_night_close_hours" greaterThan="#late_night_close_hours" autocomplete="off"> -->
                                                                            <input type="text" class="clhrs late-night-close late_night_close_hours" repeater-keep-full-name="on" placeholder="End Time" <?php echo (intval(@$business_timings['late_night']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['late_night']['close']; ?>" name="[timings][late_night][close]" id="late_night_close_hours" autocomplete="off">
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php if (!$isEdit) { ?>

                                                    <input data-repeater-delete type="button" class="btn btn-green" value="Delete" />

                                                <?php } ?>
                                            </div>

                                            <div style="border-bottom: outset;"></div>

                                        </div>
                                        <?php if (!$isEdit) { ?>
                                            <input data-repeater-create type="button" class="btn btn-green" value="Add" style="margin-left: 90%; margin-top:2%" />
                                        <?php } ?>
                                    </div>

                                    <div class="form-actions fluid">
                                        <div class="col-md-offset-3 col-md-9">
                                            <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn" onclick="return endDateValidation()"><?php echo $this->lang->line('submit'); ?></button>
                                            <a class="btn btn-danger danger-btn" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/view_menu"><?php echo $this->lang->line('cancel'); ?></a>
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
<script src="<?php echo base_url(); ?>assets/admin/scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/admin-management.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/repeater/jquery.repeater.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script>
    // function endDateValidation() {
    //     var count = $('.item-group').length;

    //     var input_val = $("#end_date").data("datetimepicker").getDate();
    //     var current_date = new Date();

    //     if (input_val < current_date) {
    //         alert("enter a valid date\nEnd Date must be a time a greater than current time")
    //         return false;
    //     }

    //     var count = $('.item-group').length;
    //     for (i = 1; i <= count; i++) {
    //         if($('#end_date'+count).length){
    //             if (!end_date_check(count)) {
    //                 alert("enter a valid date\nEnd Date must be a time a greater than current time");
    //                 return false;
    //             }
    //         }
    //     }
    //     return true;
    // }



    $(".close_bar_check").change(function() {
        var dy = this.value;
        if (!this.checked) {
            $("#" + dy + "_open_hours").val('');
            $("#" + dy + "_close_hours").val('');
            $("#" + dy + "_open_hours").attr('disabled', 'disabled');
            $("#" + dy + "_close_hours").attr('disabled', 'disabled');
            $("#" + dy + "_open_hours").removeAttr('required');
            $("#" + dy + "_close_hours").removeAttr('required');
        } else {
            $("#" + dy + "_open_hours").attr('required', 'required');
            $("#" + dy + "_close_hours").attr('required', 'required');
            $("#" + dy + "_open_hours").removeAttr('disabled');
            $("#" + dy + "_close_hours").removeAttr('disabled');
        }
        // return false;
    });

    $('.company-hours').click(function() {
        if ($(this).val() == '0') {
            $('.company-timing').hide();
            $('.hasDatepicker').each(function() {
                var id = $(this).attr('id');
                $('#' + id).val('');
            });
            $('#clickSameHours').prop('checked', false).attr('checked', false);
        } else {
            $('.company-timing').show();
        }
    });


    function end_date_check(count) {
        var current_date = new Date();
        var input_val = $("#end_date" + count).data("datetimepicker").getDate();
        if (input_val < current_date) {
            alert("enter a valid date\nEnd Date must be a time a greater than current time")
            return false;
        }


        return true;




    }

    jQuery(document).ready(function() {
        Layout.init(); // init current layout

        $("#iframeloading").addClass('display-no');

        $("#restaurant_id").select2({

        });

        $("#category_id").select2({

        });

        initRepeater();


        // $(function() {

        $('#morning_open_hours, #morning_close_hours, #lunch_open_hours, #lunch_close_hours, #dinner_open_hours, #dinner_close_hours, #late_night_open_hours, #late_night_close_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });


        <?php if ($this->session->userdata('UserType') == 'CentralAdmin') { ?>
            $("input").attr('disabled', 'disabled');
            $("select").attr('disabled', 'disabled');
            $("input.price").removeAttr('disabled');
        <?php } ?>

        var resSelect = $("#restaurant_id").select2({
            closeOnSelect: true,
            multiple: false,
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
        })

        var resSelect = $("#category_id").select2({
            closeOnSelect: true,
            multiple: false,
            minimumResultsForSearch: 10,
            ajax: {
                url: "<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getCategories",
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
        })

    });

    function resetEndDate(id) {
        // $(id).val('');
        $(id).val('')
    }

    // });

    $(function() {

        $('#end_date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            autoclose: true,

        });





    });


    $(function() {
        var count = $('.item-group').length;
        console.log('count')
        console.log(count)

        $('.end_date').datetimepicker({
            format: 'yyyy-mm-dd hh:ii',
            autoclose: true,
        });



    });


    $("#restaurant_id").change(function() {
        var res_id = this.value;
        $.ajax({
            type: "POST",
            url: '<?= base_url(ADMIN_URL) ?>/restaurant/get_restaurant_details',
            data: {
                'res_id': res_id
            },
            success: function(response) {
                var data = JSON.parse(response);
                $('.vat').val(data[0].vat);
                $('.sd').val(data[0].sd);

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        })
    })




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
        console.log(e.closest('.repeater_wrap'));
        $(e).closest('.cat_form_group').find(".max_choice_hiden").val($(e).val());
    }

    $(".modal-close").each(function() {
        $(this).on("click", function() {
            // console.log($('.modal-close'));
            $(this).closest('.modal-main').hide();
        })
    })

    function variation_addon_change(e) {
        // console.log(e);
        if ($(e).is(':checked')) {
            $(e).closest('.variations').find('.modal').show();
            $(e).closest('.variations').find('.variation_view_button').show();
        } else {
            $(e).closest('.variations').find('.variation_view_button').hide();
        }

    }

    $(".variation_view_button").click(function() {
        $(this).closest('.variations').find('.modal').show();
    })

    function variation_radio_change(e) {
        var val = $(e).val();
        // console.log($(e).closest(".category_wrap").find(".variation_div"));
        if (val == 1) {
            $(e).closest(".category_wrap").find(".variation_div").show();
            $(e).closest(".category_wrap").find(".only_addon_div").hide();
        }
        if (val == 2) {
            $(e).closest(".category_wrap").find(".variation_div").hide();
            $(e).closest(".category_wrap").find(".only_addon_div").show();
        }
    };

    function getAddonCatAddons(e) {
        addon_cat_id = $(e).val();
        // console.log($(e).closest('.cat_form_group').find('.addon_rep_parent'));

        $.ajax({
            type: "POST",
            url: '<?= base_url(ADMIN_URL) ?>/restaurant/getAddonCatAddons',
            dataType: "html",
            data: {
                'addon_cat_id': addon_cat_id
            },
            success: function(response) {
                // console.log(response);
                $(e).closest('.cat_form_group').find(".addon_rep_parent").html(response);

                // for initiating repeater in new added field
                $(e).closest('.cat_form_group').find('.addon-repeat-button').click();
                $(e).closest('.cat_form_group').find('[data-repeater-item=""]').last().remove();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        })

        $.ajax({
            type: "POST",
            url: '<?= base_url(ADMIN_URL) ?>/restaurant/getAddonMultiple',
            data: {
                'addon_cat_id': addon_cat_id
            },
            success: function(response) {

                var data = JSON.parse(response);
                if (data.is_multiple && data.is_multiple == 1) {
                    $(e).closest('.cat_form_group').find(":checkbox").prop("checked", true)
                    $(e).closest('.cat_form_group').find("#max_choice_div").find(".max_req").attr('required', 'true');
                    $(e).closest('.cat_form_group').find("#max_choice_div").show();
                    $(e).closest('.cat_form_group').find(".max-req").val(data.max_choice);
                    $(e).closest('.cat_form_group').find(".max_choice_hiden").val(data.max_choice);
                    // $(e).closest('.addon_rep_parent').find('.btn-danger').last().click();
                } else {
                    $(e).closest('.cat_form_group').find(":checkbox").prop("checked", false);
                    $(e).closest('.cat_form_group').find(".max_req").removeAttr('required');
                    $(e).closest('.cat_form_group').find("#max_choice_div").hide();
                    $(e).closest('.cat_form_group').find(".max_choice_hiden").val(0);
                    // $(e).closest('.addon_rep_parent').find('.btn-danger').last().click();

                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        })
    }

    function readURL(input, inc) {
        var fileInput = document.getElementById('Image' + inc);
        var filePath = fileInput.value;
        var fileUrl = window.URL.createObjectURL(fileInput.files[0]);
        var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
        if (input.files[0].size <= 10506316) { // 10 MB
            if (extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        if (extension == 'mp4') {
                            $('#source').attr('src', e.target.result);
                            $('#v-control').show();
                            $('#preview' + inc).attr('src', '').hide();
                        } else {
                            $('#preview' + inc).attr('src', e.target.result).attr('style', 'display: inline-block;');
                            $('#v-control').hide();
                            $('#source').attr('src', '');
                        }
                        $("#old").hide();
                        $('#errormsg').html('').hide();
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            } else {
                $('#preview' + inc).attr('src', '').attr('style', 'display: none;');
                $('#errormsg').html("<?php echo $this->lang->line('file_extenstion'); ?>").show();
                $('#Slider_image').val('');
                $("#old").show();
            }
        } else {
            $('#preview' + inc).attr('src', '').attr('style', 'display: none;');
            $('#errormsg').html("<?php echo $this->lang->line('file_size_msg'); ?>").show();
            $('#Slider_image').val('');
            $('#source').attr('src', '');
            $('#v-control').hide();
            $("#old").show();
        }
    }


    //repeater
    function initRepeater() {
        $('.menu_repeater').repeater({

            isFirstItemUndeletable: true,

            show: function() {
                var count = $('.item-group').length;
                console.log('inside menu repeater');
                console.log(count);
                // count++;
                var inputs;
                console.log(count);
                $(this).slideDown();
                $(this).find('.delete-repeat').show();

                var vat = $("#vat").val();
                var sd = $("#sd").val();
                //callfunction(count);
                // $(this).find('.repeater_menu_fields').attr('required', true);
                // $(this).find('.repeater_menu_fields').addClass('error');
                $(this).find('.add_ons').attr('id', 'check_add_ons' + count).attr('onchange', 'callfunction(this.id,' + count + ')');
                $(this).find('.category_wrap').attr('id', 'category_wrap' + count);
                $(this).find('.preview').attr('id', 'preview' + count);
                $(this).find('.Image').attr('id', 'Image' + count).attr('onchange', 'readURL(this,' + count + ')');
                $(this).find('.custom-file-upload').attr('for', 'Image' + count);
                $(this).find('.price').attr('id', 'price' + count);
                // $(this).find('.display').attr('id', 'dispaly' + count);
                $(this).find('.price_tag').attr('id', 'price_tag' + count);
                $(this).find('.require_field').attr('required', true);
                $(this).find('.end_date').attr('id', 'end_date' + count);
                $(this).find('.reset_end_date').attr('id', 'reset_end_date' + count).attr('onClick', "resetEndDate('#end_date" + count + "')");
                $(this).find('.name_div').css('margin-top', '10px');
                $(this).find('#vat').val(vat);
                $(this).find('#sd').val(sd);
                // $(this).find('.variation_add_ons').attr('onchange', 'variation_addon_change(this)');
                $(".morning-on-check").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][morning][on]');
                })
                $(".morning-on").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][morning][on]');
                })
                $(".morning-open").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][morning][open]');
                })
                $(".morning-close").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][morning][close]');
                })


                $(".lunch-on-check").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][lunch][on]');
                })
                $(".lunch-on").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][lunch][on]');
                })
                $(".lunch-open").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][lunch][open]');
                })
                $(".lunch-close").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][lunch][close]');
                })


                $(".dinner-on-check").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][dinner][on]');
                })
                $(".dinner-on").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][dinner][on]');
                })
                $(".dinner-open").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][dinner][open]');
                })
                $(".dinner-close").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][dinner][close]');
                })


                $(".late-night-on-check").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][late-light][on]');
                })
                $(".late-night-on").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][late-light][on]');
                })
                $(".late-night-open").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][late-light][open]');
                })
                $(".late-night-close").each(function(index, e) {
                    $(e).attr('name', 'menu-group' + '[' + index + ']' + '[timings][late-light][close]');
                })

                $(".close_bar_check").change(function(e) {
                    var dy = this.value;
                    if (!this.checked) {
                        $(this).parent().parent().find("." + dy + "_open_hours").val('');
                        $(this).parent().parent().find("." + dy + "_close_hours").val('');
                        $(this).parent().parent().find("." + dy + "_open_hours").attr('disabled', 'disabled');
                        $(this).parent().parent().find("." + dy + "_close_hours").attr('disabled', 'disabled');
                        $(this).parent().parent().find("." + dy + "_open_hours").removeAttr('required');
                        $(this).parent().parent().find("." + dy + "_close_hours").removeAttr('required');
                    } else {
                        $(this).parent().parent().find("." + dy + "_open_hours").attr('required', 'required');
                        $(this).parent().parent().find("." + dy + "_close_hours").attr('required', 'required');
                        $(this).parent().parent().find("." + dy + "_open_hours").removeAttr('disabled');
                        $(this).parent().parent().find("." + dy + "_close_hours").removeAttr('disabled');
                    }
                    // return false;
                });

                $(this).find('#morning_open_hours, #morning_close_hours, #lunch_open_hours, #lunch_close_hours, #dinner_open_hours, #dinner_close_hours, #late_night_open_hours, #late_night_close_hours').timepicker({
                    timeFormat: "HH:mm",
                    controlType: 'select',
                    ampm: true,
                    stepMinute: 5,
                    showButtonPanel: false
                });


                $(".modal-close").each(function() {
                    $(this).on("click", function() {
                        // console.log($('.modal-close'));
                        $(this).closest('.modal-main').hide();
                    })
                })

                $(".variation_view_button").click(function() {
                    $(this).closest('.variations').find('.modal').show();
                })

                $(function() {
                    var count = $('.item-group').length;
                    console.log('count')
                    console.log(count)

                    $('#end_date' + count).datetimepicker({
                        format: 'yyyy-mm-dd hh:ii',
                        autoclose: true,
                    });



                });





                //var adds = $(this).find('.category_checkbox');
                <?php
                $a = 1;
                foreach ($addons_category as $key => $value) {
                ?>


                    $(this).find('.category_checkbox1').attr('id', count + 'addons_category_id<?php echo $value->entity_id ?>').attr('onchange', 'addAddons(' + count + ',<?php echo $a; ?>,<?php echo $value->entity_id ?>,this.id)');
                    $('#' + count + 'addons_category_id<?php echo $value->entity_id ?>').attr('class', 'category_checkbox' + count); //rename class

                    $(this).find('.1_is_multiple_<?php echo $value->entity_id ?>').attr('class', count + '_is_multiple_' + <?php echo $value->entity_id ?>);
                    $('.' + count + '_is_multiple_' + <?php echo $value->entity_id ?>).attr('id', count + '_is_multiple_' + <?php echo $value->entity_id ?>).attr('style', 'display:none'); //rename id

                    // $(this).find('.repeater_wrap').attr('id', count + 'add_ons_category<?php echo $a ?>');
                    //$('#'+ count + 'add_ons_category<?php echo $a ?>').attr('class','repeater_wrap add_ons_category<?php echo $value->entity_id ?>');
                    $(this).find('.add_ons_category<?php echo $value->entity_id ?>').attr('id', count + 'add_ons_category<?php echo $a ?>');

                <?php
                    $a++;
                }
                ?>

            },
            hide: function(deleteElement) {
                $(this).slideUp(deleteElement);
            },


            repeaters: [{
                    selector: '.variation-repeater',
                    isFirstItemUndeletable: true,


                    show: function() {
                        var counts = $('.variation-repeater-class').length;

                        $(this).slideDown();
                        $(".modal-close").each(function() {
                            $(this).on("click", function() {
                                // console.log($('.modal-close'));
                                $(this).closest('.modal-main').hide();
                            })
                        })

                        $(".variation_view_button").click(function() {
                            $(this).closest('.variations').find('.modal').show();
                        })
                        // $(this).find('.delete-repeat-variation').show();
                        // $(this).find('.repeater_field').attr('required', true);
                        // $(this).find('.repeater_field').addClass('error');
                        // // $(this).find('.is_multiple').attr('id', 'is_multiple' + counts + 1);
                        // $(this).find('.name_repeater').attr('id', 'add_ons_name' + counts + 1);
                        // $(this).find('.price_repeater').attr('id', 'add_ons_price' + counts + 1);
                    },
                    hide: function(deleteElement) {
                        $(this).slideUp(deleteElement);
                    },

                    repeaters: [{

                        selector: '.add-on-cat-repeater',
                        isFirstItemUndeletable: true,


                        show: function() {
                            var counts = $('.add-on-cat-repeater_wrap').length;

                            $(this).slideDown();
                            // $(this).find('.delete-repeat').show();
                            // $(this).find('.repeater_field').attr('required', true);
                            // $(this).find('.repeater_field').addClass('error');
                            // // $(this).find('.is_multiple').attr('id', 'is_multiple' + counts + 1);
                            // $(this).find('.name_repeater').attr('id', 'add_ons_name' + counts + 1);
                            // $(this).find('.price_repeater').attr('id', 'add_ons_price' + counts + 1);
                        },
                        hide: function(deleteElement) {
                            $(this).slideUp(deleteElement);
                        },
                        repeaters: [{

                            selector: '.repeater_wrap',
                            isFirstItemUndeletable: true,

                            show: function() {
                                var counts = $('.outer-repeater').length;

                                $(this).slideDown();
                                $(this).find('.delete-repeat').show();
                                $(this).find('.repeater_field').attr('required', true);
                                $(this).find('.repeater_field').addClass('error');
                                // $(this).find('.is_multiple').attr('id', 'is_multiple' + counts + 1);
                                $(this).find('.name_repeater').attr('id', 'add_ons_name' + counts + 1);
                                // $(this).find('.price_repeater').attr('id', 'add_ons_price' + counts + 1);
                            },
                            hide: function(deleteElement) {
                                $(this).slideUp(deleteElement);
                            }
                        }]


                    }]
                },
                {
                    selector: '.only-add-on-cat-repeater',
                    isFirstItemUndeletable: true,


                    show: function() {
                        var counts = $('.only-add-on-cat-repeater').length;

                        $(this).slideDown();

                        // $(this).find('.delete-repeat-variation').show();
                        // $(this).find('.repeater_field').attr('required', true);
                        // $(this).find('.repeater_field').addClass('error');
                        // // $(this).find('.is_multiple').attr('id', 'is_multiple' + counts + 1);
                        // $(this).find('.name_repeater').attr('id', 'add_ons_name' + counts + 1);
                        // $(this).find('.price_repeater').attr('id', 'add_ons_price' + counts + 1);
                    },
                    hide: function(deleteElement) {
                        $(this).slideUp(deleteElement);
                    },
                    repeaters: [{

                        selector: '.only-addon-outer-repeater',
                        isFirstItemUndeletable: true,

                        show: function() {
                            var counts = $('.only-addon-outer-repeater').length;

                            $(this).slideDown();
                            $(this).find('.delete-repeat').show();
                            $(this).find('.repeater_field').attr('required', true);
                            $(this).find('.repeater_field').addClass('error');
                            // $(this).find('.is_multiple').attr('id', 'is_multiple' + counts + 1);
                            $(this).find('.name_repeater').attr('id', 'add_ons_name' + counts + 1);
                            $(this).find('.price_repeater').attr('id', 'add_ons_price' + counts + 1);
                        },
                        hide: function(deleteElement) {
                            $(this).slideUp(deleteElement);
                        }
                    }]
                }
            ]
        });
    }

    //add add ons
    function addAddons(inc, key, entity_id, id) {

        if ($('#' + id).is(':checked')) {
            console.log(entity_id);
            $('.category_checkbox' + inc).attr('required', false);
            $('#' + inc + 'add_ons_category' + key).show();

            $('.' + inc + '_is_multiple_' + entity_id).attr('style', 'display:block');
            //$('#is_multiple' + entity_id).attr('disabled', false);
            $('#' + inc + 'add_ons_category' + key).find('.repeater_field').attr('required', true);
            $('#' + inc + 'add_ons_category' + key).find('.repeater_field').addClass('error');
        } else {
            $('#' + inc + 'add_ons_category' + key).hide();


            $('.' + inc + '_is_multiple_' + entity_id).attr('style', 'display:none');
            $('#' + inc + 'add_ons_category' + key).find('.repeater_field').val('');
            $('#' + inc + 'add_ons_category' + key).find('.delete_repeater').trigger('click');
            $('#' + inc + 'add_ons_category' + key).find('.repeater_field').attr('required', false);
            $('#' + inc + 'add_ons_category' + key).find('.repeater_field').removeClass('error');
            $('#is_multiple' + entity_id).attr('checked', false);
            $('label.error').remove();
            var check = false;
            if ($('#check_add_ons' + inc).prop("checked") == true) { //to check addons is unchecked after being checked once/more.
                <?php

                foreach ($addons_category as $key => $value) {
                ?>

                    if ($('#' + inc + 'addons_category_id<?php echo $value->entity_id ?>').is(":checked")) { //to check if atleast one category is checked
                        check = true;
                    }


                <?php

                }
                ?>

                if (check == false) {
                    $('.category_checkbox' + inc).attr('required', true);
                }
            }

        }
    }

    function callfunction(id, count) {
        console.log(count);
        if ($('#' + id).is(':checked')) {
            $('.category_checkbox' + count).attr('required', true);
            $('#category_wrap' + count).show();
            $('#price_tag' + count).hide();
            $('#price' + count).val('').attr('required', false);
        } else {

            //$('.category_wrap').hide();
            $('#category_wrap' + count).hide();
            $('#price_tag' + count).show();
            $('#price' + count).attr('required', true);



            $('.category_checkbox' + count).attr('required', false);
            $('.category_checkbox' + count).attr('checked', false);

            <?php
            $a = 1;
            foreach ($addons_category as $key => $value) {
            ?>

                $(this).find('.category_checkbox' + count).attr('id', count + 'addons_category_id<?php echo $value->entity_id ?>').attr('checked', false);


                addAddons(count, <?php echo $a ?>, <?php echo $value->entity_id ?>, id);
            <?php
                $a++;
            }


            ?>
        }
    }
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>