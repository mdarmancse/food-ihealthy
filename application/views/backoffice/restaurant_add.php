<?php
$this->load->view(ADMIN_URL . '/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
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
        $FieldsArray = array('content_id', 'entity_id', 'name', 'phone_number', 'email', 'address', 'landmark', 'latitude', 'longitude', 'country', 'vat', 'sd', 'city', 'zipcode', 'amount_type', 'created_by', 'amount', 'enable_hours', 'timings', 'break_timing', 'image', 'cover_image', 'price_range', 'is_veg', 'driver_commission', 'currency_id', 'restaurant_slug', 'commission', 'zonal_admin', 'central_admin', 'status', 'price_value', 'delivery_time');
        foreach ($FieldsArray as $key) {
            $$key = @htmlspecialchars($edit_records->$key);
        }
    }
    if (isset($edit_records) && $edit_records != "") {
        $add_label    = $this->lang->line('edit') . ' ' . $this->lang->line('restaurant');
        $form_action  = base_url() . ADMIN_URL . '/' . $this->controller_name . "/edit/" . $this->uri->segment('4') . '/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    } else {
        $add_label    = $this->lang->line('add') . ' ' . $this->lang->line('restaurant');
        $form_action  = base_url() . ADMIN_URL . '/' . $this->controller_name . "/add/" . $this->uri->segment('4');
    }
    $usertypes = getUserTypeList($this->session->userdata('language_slug'));
    ?>

    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('restaurant') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/view"><?php echo $this->lang->line('restaurant') ?></a>
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
                            <form action="<?php echo $form_action; ?>" id="form_add<?php echo $this->prefix; ?>" name="form_add<?php echo $this->prefix; ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                                <div id="iframeloading" class="frame-load display-no">
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
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_name') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id; ?>" />
                                            <input type="hidden" id="status" name="status" value="<?php echo $status; ?>" />
                                            <input type="hidden" id="content_id" name="content_id" value="<?php echo ($content_id) ? $content_id : $this->uri->segment('5'); ?>" />
                                            <input type="hidden" id="restaurant_slug" name="restaurant_slug" value="<?php echo ($restaurant_slug) ? $restaurant_slug : ''; ?>" />
                                            <input type="text" name="name" id="name" value="<?php echo $name; ?>" maxlength="249" data-required="1" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('phone_number') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="phone_number" id="phone_number" value="<?php echo $phone_number; ?>" maxlength="20" data-required="1" class="form-control" onblur="checkExist(this.value)" />
                                        </div>
                                        <div id="phoneExist"></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('email') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="email" name="email" id="email" value="<?php echo $email; ?>" maxlength="100" data-required="1" class="form-control" onblur="checkEmail(this.value,'<?php echo $entity_id ?>')" />
                                        </div>
                                        <div id="EmailExist"></div>
                                    </div>

                                    <?php if (!($this->session->userdata('UserType') == 'Admin') || ($this->session->userdata('UserType') == 'CentralAdmin')) {  ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Central Admin</span></label>
                                            <div class="col-md-4">

                                                <select class="form-control" name="central_admin" id="central_admin">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php
                                                    if (!empty($centralAdmin)) {
                                                        foreach ($centralAdmin as $key => $value) { ?>
                                                            <option value="<?php echo $value->entity_id; ?>" <?php if ($central_admin == $value->entity_id) {  ?> selected <?php } ?>><?php echo $value->first_name . ' ' . $value->last_name; ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-3">Admin User</span></label>
                                            <div class="col-md-4">

                                                <select class="form-control" name="admin_user" id="admin_user">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php if (!empty($admin)) {
                                                        foreach ($admin as $key => $value) { ?>
                                                            <option value="<?php echo $value->entity_id; ?>" <?php if ($created_by == $value->entity_id) {  ?> selected <?php } ?>><?php echo $value->first_name . ' ' . $value->last_name; ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-3">Zonal Admin</span></label>
                                            <div class="col-md-4">

                                                <select class="form-control" name="zonal_admin" id="zonal_admin">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php if (!empty($zonalAdmin)) {
                                                        foreach ($zonalAdmin as $key => $value) { ?>
                                                            <option value="<?php echo $value->entity_id; ?>" <?php if ($zonal_admin == $value->entity_id) {  ?> selected <?php } ?>><?php echo $value->first_name . ' ' . $value->last_name; ?></option>
                                                    <?php }
                                                    } ?>
                                                </select>

                                            </div>
                                        </div>

                                    <?php } ?>


                                    <!-- Image -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('image') ?></label>
                                        <div class="col-md-4">
                                            <div class="custom-file-upload">
                                                <!-- no file-->
                                                <label for="Image" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                </label>

                                                <input type="file" name="Image" id="Image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this)" />
                                            </div>
                                            <!--only jpeg, ...... -->
                                            <p class="help-block"><?php echo $this->lang->line('img_allow') ?><br /><?php echo $this->lang->line('max_file_size') ?><br /><?php echo $this->lang->line('recommended_size') . '500 * 450.'; ?></p>
                                            <span class="error display-no" id="errormsg"><?php echo $this->lang->line('file_extenstion') ?></span>
                                            <div id="img_gallery"></div>
                                            <img id="preview" height='100' width='150' class="display-no" />
                                            <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo isset($image) ? $image : ''; ?>" />
                                        </div>
                                    </div>

                                    <div class="form-group" id="old">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if (isset($image) && $image != '') { ?>
                                                <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                <img id='oldpic' class="img-responsive" src="<?php echo image_url . $image; ?>">
                                            <?php }  ?>
                                        </div>
                                    </div>

                                    <!-- Cover Image -->

                                    <div class="form-group">
                                        <label class="control-label col-md-3"> Cover Image </label>
                                        <div class="col-md-4">
                                            <div class="custom-file-upload">
                                                <label for="CoverImage" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                </label>
                                                <input type="file" name="CoverImage" id="CoverImage" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="CoverImagereadURL(this)" />
                                            </div>
                                            <p class="help-block"><?php echo $this->lang->line('img_allow') ?><br /><?php echo $this->lang->line('max_file_size') ?><br /></p>
                                            <span class="error display-no" id="errormsg"><?php echo $this->lang->line('file_extenstion') ?></span>
                                            <div id="img_gallery"></div>
                                            <img id="coverPreview" height='100' width='150' class="display-no" />
                                            <input type="hidden" name="uploaded_cover_image" id="uploaded_cover_image" value="<?php echo isset($cover_image) ? $cover_image : ''; ?>" />
                                        </div>
                                    </div>




                                    <!-- Cover Image -->
                                    <div class="form-group" id="oldCoverImage">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if (isset($cover_image) && $cover_image != '') { ?>
                                                <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                <img id='oldCoverPic' class="img-responsive" src="<?php echo image_url . $cover_image; ?>">
                                            <?php }  ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('address') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="address" id="address" value="<?php echo $address ?>" maxlength="255" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('landmark') ?></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="landmark" id="landmark" value="<?php echo $landmark ?>" maxlength="255" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('latitude') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="latitude" id="latitude" value="<?php echo $latitude ?>" maxlength="50" />
                                        </div>
                                        <a href="#basic" data-toggle="modal" class="btn red default"> <?php echo $this->lang->line('pick_lat_long') ?> </a>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('longitude') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="longitude" id="longitude" value="<?php echo $longitude ?>" maxlength="50" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('zipcode') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="zipcode" id="zipcode" value="<?php echo $zipcode ?>" maxlength="10" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('country') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="country" id="country" value="<?php echo ($country) ? $country : 'Bangladesh'; ?>" maxlength="50" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('city') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" class="form-control" name="city" id="city" value="<?php echo $city ?>" maxlength="50" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('currency') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <?php $currency = ($res_currency_id) ? $res_currency_id : $currency_id; ?>
                                            <?php $point = "style='pointer-events: none;'"; ?>
                                            <select class="form-control" name="currency_id" id="currency_id" <?php echo ($currency) ? "readonly " . $point : "" ?>>
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php if (!empty($currencies)) {
                                                    foreach ($currencies as $key => $value) { ?>
                                                        <option value="<?php echo $value['currency_id']; ?>" <?php echo (($currency == $value['currency_id']) || ($value['currency_id'] == 18)) ? "selected" : "" ?>><?php echo $value['country_name'] . ' - ' . $value['currency_code']; ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3">Commission(%)<span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="number" name="commission" id="commission" <?= ($this->session->userdata('UserType') == 'CentralAdmin' || $this->session->userdata('UserType') == 'Admin') ? 'disabled="disabled"' : '' ?> value="<?php echo $commission ?>" maxlength="10" data-required="1" class="form-control" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3">Vat(%)<span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="number" name="vat" id="vat" <?= ($this->session->userdata('UserType') == 'CentralAdmin') ? 'disabled="disabled"' : '' ?> value="<?php echo ($vat) ? $vat : 0 ?>" maxlength="10" data-required="1" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3">Sd(%)<span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="number" name="sd" id="sd" <?= ($this->session->userdata('UserType') == 'CentralAdmin') ? 'disabled="disabled"' : '' ?> value="<?php echo ($sd) ? $sd : 0 ?>" maxlength="10" data-required="1" class="form-control" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('food_type') ?><span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="radio" name="is_veg" id="is_veg" value="1" checked="" <?php echo ($is_veg) ? ($is_veg == '1') ? 'checked' : '' : 'checked' ?>><?php echo $this->lang->line('veg') ?>
                                            <input type="radio" name="is_veg" id="non-veg" value="0" <?php echo ($is_veg == '0') ? 'checked' : '' ?>><?php echo $this->lang->line('non_veg') ?>
                                            <input type="radio" name="is_veg" id="non-veg" value="2" <?php echo ($is_veg == '2') ? 'checked' : '' ?>><?php echo $this->lang->line('both') ?>
                                        </div>
                                    </div>

                                    <!--Price Range-->

                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('price_range'); ?></label>
                                        <!-- <div class="col-md-8">
                                            <input type="radio" id="idlowPriceRange" name="priceRange" value="1" <?php echo ($price_range == 1) ? 'checked' : "" ?>> $
                                            <input type="radio" id="idMediumPriceRange" name="priceRange" value="2" <?php echo ($price_range == 2) ? 'checked' : "" ?>> $$
                                            <input type="radio" id="idHighPriceRange" name="priceRange" value="3" <?php echo ($price_range == 3) ? 'checked' : "" ?>> $$$
                                        </div> -->
                                        <?php $array = explode("-", $price_range); ?>
                                        <div class="col-md-3">
                                            <input type="number" class="form-control" placeholder="From" name="start_range" id="start_range" value="<?php echo $array[0]; ?>" maxlength="49">
                                        </div>

                                        <div class="col-md-3">

                                            <input type="number" class="form-control" name="end_range" placeholder="To" id="end_range" value="<?php echo $array[1]; ?>" maxlength="49">
                                        </div>

                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3">Delivery Time</label>
                                        <div class="col-md-4">
                                            <input type="number" class="form-control" name="delivery_time" id="delivery_time" value="<?php echo $delivery_time ?>" maxlength="50" />
                                        </div>
                                    </div>

                                    <div class="form-group display-hide">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('enable_hours') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="radio" checked name="enable_hours" id="radioTrue" value="1" class="company-hours"> <label for="radioTrue"><?php echo $this->lang->line('yes') ?></label>
                                            <!-- <input type="radio" <?php echo ($enable_hours == '0') ? 'checked' : '' ?> name="enable_hours" id="radioFalse" value="0" class="company-hours"> <label for="radioFalse"><?php echo $this->lang->line('no') ?></label> -->
                                        </div>
                                    </div>
                                    <div class="form-group company-timing">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('res_time') ?></label>
                                        <?php if (empty($_POST['timings'])) {
                                            $business_timings = unserialize(html_entity_decode($timings));
                                        } else {
                                            $timingsArr = $_POST['timings'];
                                            $newTimingArr = array();
                                            foreach ($timingsArr as $key => $value) {
                                                if (isset($value['off'])) {
                                                    $newTimingArr[$key]['open'] = '';
                                                    $newTimingArr[$key]['close'] = '';
                                                    $newTimingArr[$key]['off'] = '0';
                                                } else {
                                                    if (!empty($value['open']) && !empty($value['close'])) {
                                                        $newTimingArr[$key]['open'] = $value['open'];
                                                        $newTimingArr[$key]['close'] = $value['close'];
                                                        $newTimingArr[$key]['off'] = '1';
                                                    } else {
                                                        $newTimingArr[$key]['open'] = '';
                                                        $newTimingArr[$key]['close'] = '';
                                                        $newTimingArr[$key]['off'] = '0';
                                                    }
                                                }
                                            }
                                            $business_timings = $newTimingArr;
                                        }  ?>
                                        <div class="col-md-12">
                                            <table class="timingstable" width="100%" cellpadding="2" cellspacing="2">
                                                <tr>
                                                    <td><strong>&nbsp;</strong></td>
                                                    <td colspan="2">
                                                        <label class="checkbox chk-clicksame">
                                                            <input type="checkbox" id="clickSameHours">
                                                            <?php echo $this->lang->line('time_msg') ?> </label><br />
                                                        <span id="alertSpan" class="alert-spantg"></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('mon') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" lesserThan="#monday_close_hours" id="monday_open_hours" name="timings[monday][open]" <?php echo (intval(@$business_timings['monday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['monday']['open']; ?>" placeholder="<?php echo $this->lang->line('opening_hours') ?>">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" greaterThan="#monday_open_hours" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['monday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['monday']['close']; ?>" name="timings[monday][close]" id="monday_close_hours">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['monday']['off'])) ? '' : 'checked="checked"'; ?> value="monday" class="close_bar_check" id="monday_close" name="timings[monday][off]"><?php echo $this->lang->line('close_msg') ?> <?php echo $this->lang->line('mon') ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('tue') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" lesserThan="#tuesday_close_hours" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php echo (intval(@$business_timings['tuesday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['tuesday']['open']; ?>" name="timings[tuesday][open]" id="tuesday_open_hours">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" greaterThan="#tuesday_open_hours" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['tuesday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['tuesday']['close']; ?>" name="timings[tuesday][close]" id="tuesday_close_hours">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['tuesday']['off'])) ? '' : 'checked="checked"'; ?> value="tuesday" class="close_bar_check" id="tuesday_close" name="timings[tuesday][off]"><?php echo $this->lang->line('close_msg') ?> <?php echo $this->lang->line('tue') ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('wed') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php echo (intval(@$business_timings['wednesday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['wednesday']['open']; ?>" name="timings[wednesday][open]" id="wednesday_open_hours" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" lesserThan="#wednesday_close_hours">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['wednesday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['wednesday']['close']; ?>" name="timings[wednesday][close]" id="wednesday_close_hours" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" greaterThan="#wednesday_open_hours">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['wednesday']['off'])) ? '' : 'checked="checked"'; ?> value="wednesday" class="close_bar_check" id="wednesday_close" name="timings[wednesday][off]"><?php echo $this->lang->line('close_msg') ?> <?php echo $this->lang->line('wed') ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('thurs') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php echo (intval(@$business_timings['thursday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['thursday']['open']; ?>" name="timings[thursday][open]" id="thursday_open_hours" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" lesserThan="#thursday_open_hours">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['thursday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['thursday']['close']; ?>" name="timings[thursday][close]" id="thursday_close_hours" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" greaterThan="#thursday_close_hours">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['thursday']['off'])) ? '' : 'checked="checked"'; ?> value="thursday" class="close_bar_check" id="thursday_close" name="timings[thursday][off]"><?php echo $this->lang->line('close_msg') ?> <?php echo $this->lang->line('thurs') ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('fri') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php echo (intval(@$business_timings['friday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['friday']['open']; ?>" name="timings[friday][open]" id="friday_open_hours" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" lesserThan="#friday_open_hours">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['friday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['friday']['close']; ?>" name="timings[friday][close]" id="friday_close_hours" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" greaterThan="#friday_close_hours">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['friday']['off'])) ? '' : 'checked="checked"'; ?> value="friday" class="close_bar_check" id="friday_close" name="timings[friday][off]"><?php echo $this->lang->line('close_msg') ?> <?php echo $this->lang->line('fri') ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('sat') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php echo (intval(@$business_timings['saturday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['saturday']['open']; ?>" name="timings[saturday][open]" id="saturday_open_hours" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" lesserThan="#saturday_open_hours">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['saturday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['saturday']['close']; ?>" name="timings[saturday][close]" id="saturday_close_hours" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" greaterThan="#saturday_close_hours">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['saturday']['off'])) ? '' : 'checked="checked"'; ?> value="saturday" class="close_bar_check" id="saturday_close" name="timings[saturday][off]"><?php echo $this->lang->line('close_msg') ?> <?php echo $this->lang->line('sat') ?></label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong><?php echo $this->lang->line('sun') ?></strong></td>
                                                    <td>
                                                        <div class="td-wrap">
                                                            <input type="text" class="ophrs" placeholder="<?php echo $this->lang->line('opening_hours') ?>" <?php echo (intval(@$business_timings['sunday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['sunday']['open']; ?>" name="timings[sunday][open]" id="sunday_open_hours" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" lesserThan="#sunday_open_hours">
                                                        </div>
                                                        <div class="td-wrap">
                                                            <input type="text" class="clhrs" placeholder="<?php echo $this->lang->line('closing_hours') ?>" <?php echo (intval(@$business_timings['sunday']['off'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$business_timings['sunday']['close']; ?>" name="timings[sunday][close]" id="sunday_close_hours" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" greaterThan="#sunday_close_hours">
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$business_timings['sunday']['off'])) ? '' : 'checked="checked"'; ?> value="sunday" class="close_bar_check" id="sunday_close" name="timings[sunday][off]"><?php echo $this->lang->line('close_msg') ?> <?php echo $this->lang->line('sun') ?></label>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="form-group company-timing">
                                        <label class="control-label col-md-3"><?php echo "Break Timing" ?></label>
                                        <?php if (empty($_POST['break_timing'])) {
                                            $break_timing = unserialize(html_entity_decode($break_timing));
                                        } else {
                                            $break_timingsArr = $_POST['break_timing'];
                                            $newTimingArr_break = array();
                                            foreach ($break_timingsArr as $key => $value) {
                                                if (isset($value['on'])) {
                                                    $newTimingArr_break[$key]['open'] = '';
                                                    $newTimingArr_break[$key]['close'] = '';
                                                    $newTimingArr_break[$key]['on'] = '0';
                                                } else {
                                                    if (!empty($value['open']) && !empty($value['close'])) {
                                                        $newTimingArr_break[$key]['open'] = $value['open'];
                                                        $newTimingArr_break[$key]['close'] = $value['close'];
                                                        $newTimingArr_break[$key]['on'] = '1';
                                                    } else {
                                                        $newTimingArr_break[$key]['open'] = '';
                                                        $newTimingArr_break[$key]['close'] = '';
                                                        $newTimingArr_break[$key]['on'] = '0';
                                                    }
                                                }
                                            }
                                            $break_timing = $newTimingArr_break;
                                        }  ?>
                                        <div class="row">
                                            <div class="col-sm-3"></div>
                                            <div class="col-sm-8">
                                                <table class="timingstable" width="100%" cellpadding="2" cellspacing="2">
                                                    <tr>
                                                        <td><strong><?php echo "Break 1" ?></strong></td>
                                                        <td>
                                                            <input type="hidden" name="break_timing[break1][on]" value="off">
                                                            <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$break_timing['break1']['on'])) ? 'checked="checked"' : ''; ?> value="break1" class="break_close_bar_check" id="break1_close" name="break_timing[break1][on]"></label>
                                                        </td>
                                                        <td>
                                                            <div class="td-wrap">
                                                                <input type="text" class="ophrs" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" lesserThan="#break1_close_hours" id="break1_open_hours" name="break_timing[break1][open]" <?php echo (intval(@$break_timing['break1']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$break_timing['break1']['open']; ?>" placeholder="Start Time" autocomplete="off">
                                                            </div>
                                                            <div class="td-wrap">
                                                                <input type="text" class="clhrs" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" greaterThan="#break1_open_hours" placeholder="End Time" <?php echo (intval(@$break_timing['break1']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$break_timing['break1']['close']; ?>" name="break_timing[break1][close]" id="break1_close_hours" autocomplete="off">
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td><strong><?php echo "Break 2" ?></strong></td>
                                                        <td>
                                                            <input type="hidden" name="break_timing[break2][on]" value="off">
                                                            <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$break_timing['break2']['on'])) ? 'checked="checked"' : ''; ?> value="break2" class="break_close_bar_check" id="break2_close" name="break_timing[break2][on]"></label>
                                                        </td>
                                                        <td>
                                                            <div class="td-wrap">
                                                                <input type="text" class="ophrs" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" lesserThan="#break2_close_hours" id="break2_open_hours" name="break_timing[break2][open]" <?php echo (intval(@$break_timing['break2']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$break_timing['break2']['open']; ?>" placeholder="Start Time" autocomplete="off">
                                                            </div>
                                                            <div class="td-wrap">
                                                                <input type="text" class="clhrs" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" greaterThan="#break2_open_hours" placeholder="End Time" <?php echo (intval(@$break_timing['break2']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$break_timing['break2']['close']; ?>" name="break_timing[break2][close]" id="break2_close_hours" autocomplete="off">
                                                            </div>
                                                            <!-- </td> -->
                                                    </tr>

                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn btn-danger danger-btn" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/view"><?php echo $this->lang->line('cancel') ?></a>
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
<div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"><?php echo $this->lang->line('lat_long_msg') ?></h4>
            </div>
            <div class="modal-body">
                <form class="form-inline margin-bottom-10" action="#">
                    <div class="input-group">
                        <input type="text" class="form-control" id="gmap_geocoding_address" placeholder="<?php echo $this->lang->line('address') ?>">
                        <span class="input-group-btn">
                            <button class="btn blue" id="gmap_geocoding_btn"><i class="fa fa-search"></i></button>
                        </span>
                    </div>
                </form>
                <div id="gmap_geocoding" class="gmaps">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal"><?php echo $this->lang->line('close') ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<div id="mansi_map"></div>

<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="<?php echo base_url(); ?>assets/admin/scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/admin-management.js"></script>
<script src="//maps.google.com/maps/api/js?key=<?= MAP_API_KEY ?>" type="text/javascript"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/gmaps/gmaps.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/jquery-ui-timepicker-addon.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />

<script>
    jQuery(document).ready(function() {
        Layout.init(); // init current layout
    });
    $("#basic").on("shown.bs.modal", function() {
        mapGeocoding(); // init geocoding Maps
    });

    var currentLat = $("#latitude").val();
    var currentLng = $("#longitude").val();

    var mapGeocoding = function() {
        var map = new GMaps({
            div: '#gmap_geocoding',
            lat: (currentLat) ? currentLat : 22.359091474324057,
            lng: (currentLng) ? currentLng : 91.82152204807325,
            click: function(e) {
                placeMarker(e.latLng);
            }
        });
        map.addMarker({
            lat: (currentLat) ? currentLat : 22.359091474324057,
            lng: (currentLng) ? currentLng : 91.82152204807325,
            title: 'GEC Circle',
            draggable: true,
            dragend: function(event) {
                $("#latitude").val(event.latLng.lat());
                $("#longitude").val(event.latLng.lng());
            }
        });

        function placeMarker(location) {
            map.removeMarkers();
            $("#latitude").val(location.lat());
            $("#longitude").val(location.lng());
            map.addMarker({
                lat: location.lat(),
                lng: location.lng(),
                draggable: true,
                dragend: function(event) {
                    $("#latitude").val(event.latLng.lat());
                    $("#longitude").val(event.latLng.lng());
                }
            })
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                initialLocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                map.setCenter(initialLocation);
            });
        }
        var handleAction = function() {
            var text = $.trim($('#gmap_geocoding_address').val());
            GMaps.geocode({
                address: text,
                callback: function(results, status) {
                    if (status == 'OK') {
                        map.removeMarkers();
                        var latlng = results[0].geometry.location;
                        map.setCenter(latlng.lat(), latlng.lng());
                        map.addMarker({
                            lat: latlng.lat(),
                            lng: latlng.lng(),
                            draggable: true,
                            dragend: function(event) {
                                $("#latitude").val(event.latLng.lat());
                                $("#longitude").val(event.latLng.lng());
                            }
                        });
                        $("#latitude").val(latlng.lat());
                        $("#longitude").val(latlng.lng());
                    }
                }
            });
        }
        $('#gmap_geocoding_btn').click(function(e) {
            e.preventDefault();
            handleAction();
        });
        $("#gmap_geocoding_address").keypress(function(e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            if (keycode == '13') {
                e.preventDefault();
                handleAction();
            }
        });
    }
    // Markup Radio Button Validation
    function markup() {
        if ($("input[name=amount_type]:checked").val() == "Percentage") {
            $("#Amount").hide();
            $("#Percentage").show();
        } else if ($("input[name=amount_type]:checked").val() == "Amount") {
            $("#Percentage").hide();
            $("#Amount").show();
        }
    }
    $(document).ready(function() {
        markup();

        $("#admin_user, #zonal_admin").select2({

        });
    });
    $("input[name=amount_type]:radio").click(function() {
        markup();
        if ($("input[name=amount_type]:checked").val() == "Percentage") {
            $("#amount").val('');
        } else if ($("input[name=amount_type]:checked").val() == "Amount") {
            $("#amount").val('');
        }
    });
    //for company timing
    $(function() {

        $('#monday_open_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#monday_close_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#tuesday_open_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#tuesday_close_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#wednesday_open_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#wednesday_close_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#thursday_open_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#thursday_close_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#friday_open_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#friday_close_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#saturday_open_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#saturday_close_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#sunday_open_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });
        $('#sunday_close_hours').timepicker({
            timeFormat: "HH:mm",
            controlType: 'select',
            ampm: true,
            stepMinute: 5,
            showButtonPanel: false
        });

        $(".close_bar_check").change(function() {
            var dy = this.value;

            if (this.checked) {
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
            return false;
        });
        $("#clickSameHours").change(function() {
            $('#alertSpan').html('');
            if (this.checked) {
                var ophrs = $('#monday_open_hours').val();
                var clhrs = $('#monday_close_hours').val();
                if (ophrs != '' && clhrs != '') {
                    $('#alertSpan').html('');
                    $(".close_bar_check").each(function(i) {
                        this.checked = false;
                        var parent = $(this).closest('tr');
                        $(parent).find('input').eq(0).removeAttr('disabled');
                        $(parent).find('input').eq(1).removeAttr('disabled');
                        $(parent).find('input').eq(0).val(ophrs);
                        $(parent).find('input').eq(1).val(clhrs);
                    });
                } else {
                    $('#alertSpan').html("<?php echo $this->lang->line('open_close_msg') ?>");
                    $(this).removeAttr("checked");
                }
            } else {
                $('#alertSpan').html('');
            }
            return false;
        });
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

    $('#break1_open_hours').timepicker({
        timeFormat: "HH:mm",
        controlType: 'select',
        ampm: true,
        stepMinute: 5,
        showButtonPanel: false
    });
    $('#break1_close_hours').timepicker({
        timeFormat: "HH:mm",
        controlType: 'select',
        ampm: true,
        stepMinute: 5,
        showButtonPanel: false
    });

    $('#break2_open_hours').timepicker({
        timeFormat: "HH:mm",
        controlType: 'select',
        ampm: true,
        stepMinute: 5,
        showButtonPanel: false
    });
    $('#break2_close_hours').timepicker({
        timeFormat: "HH:mm",
        controlType: 'select',
        ampm: true,
        stepMinute: 5,
        showButtonPanel: false
    });

    $(".break_close_bar_check").change(function() {
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
        return false;
    });


    function CoverImagereadURL(coverImageInput) {

        var fileInput = document.getElementById('CoverImage');
        var filePath = fileInput.value;
        var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
        var file_size = fileInput.size;
        if (coverImageInput.files[0].size <= 5242880) { // 5 MB
            if (extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
                if (coverImageInput.files && coverImageInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {

                        $('#coverPreview').attr('src', e.target.result).attr('style', 'display: inline-block;');
                        $("#oldCoverImage").hide();
                        $('#errormsg').html('').hide();
                    }
                    reader.readAsDataURL(coverImageInput.files[0]);
                }
            } else {

                $('#coverPreview').attr('src', '').attr('style', 'display: none;');
                $('#errormsg').html("<?php echo $this->lang->line('file_extenstion') ?>").show();
                $('#Slider_image').val('');
                $("#Slider_image").show();
            }
        } else {

            $('#coverPreview').attr('src', '').attr('style', 'display: none;');
            $('#errormsg').html("<?php echo $this->lang->line('file_size_msg') ?>").show();
            $('#Slider_image').val('');
            $("#oldCoverImage").show();
        }
    }



    function readURL(input) {
        var fileInput = document.getElementById('Image');
        var filePath = fileInput.value;
        var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
        var file_size = fileInput.size;
        if (input.files[0].size <= 5242880) { // 5 MB
            if (extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#preview').attr('src', e.target.result).attr('style', 'display: inline-block;');

                        $("#old").hide();
                        $('#errormsg').html('').hide();
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            } else {
                $('#preview').attr('src', '').attr('style', 'display: none;');

                $('#errormsg').html("<?php echo $this->lang->line('file_extenstion') ?>").show();
                $('#Slider_image').val('');
                $("#old").show();
            }
        } else {
            $('#preview').attr('src', '').attr('style', 'display: none;');
            $('#errormsg').html("<?php echo $this->lang->line('file_size_msg') ?>").show();
            $('#Slider_image').val('');
            $("#old").show();
        }
    }
    //check phone number exist
    function checkExist(phone_number) {
        var entity_id = $('#entity_id').val();
        var content_id = $('#content_id').val();
        $.ajax({
            type: "POST",
            url: BASEURL + "<?php echo ADMIN_URL ?>/restaurant/checkExist",
            data: 'phone_number=' + phone_number + '&entity_id=' + entity_id + '&content_id=' + content_id,
            cache: false,
            success: function(html) {
                if (html > 0) {
                    $('#phoneExist').show();
                    $('#phoneExist').html("<?php echo $this->lang->line('phones_exist'); ?>");
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
    // admin email exist check
    function checkEmail(email, entity_id) {
        var content_id = $('#content_id').val();
        $.ajax({
            type: "POST",
            url: BASEURL + "<?php echo ADMIN_URL ?>/restaurant/checkEmailExist",
            data: 'email=' + email + '&entity_id=' + entity_id + '&content_id=' + content_id,
            cache: false,
            success: function(html) {
                if (html > 0) {
                    $('#EmailExist').show();
                    $('#EmailExist').html("<?php echo $this->lang->line('email_exist'); ?>");
                    $(':input[type="submit"]').prop("disabled", true);
                } else {
                    $('#EmailExist').html("");
                    $('#EmailExist').hide();
                    $(':input[type="submit"]').prop("disabled", false);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#EmailExist').show();
                $('#EmailExist').html(errorThrown);
            }
        });
    }
</script>

<?php $this->load->view(ADMIN_URL . '/footer'); ?>