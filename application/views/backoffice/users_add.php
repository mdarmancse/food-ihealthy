<?php
$this->load->view(ADMIN_URL . '/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar');

    if ($this->input->post()) {
        foreach ($this->input->post() as $key => $value) {
            $$key = @htmlspecialchars($this->input->post($key));
        }
    } else {
        $FieldsArray = array('entity_id', 'first_name', 'last_name', 'email', 'mobile_number', 'phone_number', 'user_type', 'image', 'nid', 'nid_back', 'gnid_front', 'gnid_back', 'electricity_bill', 'nameplate', 'bkash_no', 'nagad_no');
        foreach ($FieldsArray as $key) {
            $$key = @htmlspecialchars($edit_records->$key);
        }
    }
    $module =  ($user_type != 'Driver' && $this->uri->segment(4) != 'driver') ? $this->lang->line('users') : $this->lang->line('driver');

    if (isset($edit_records) && $edit_records != "") {
        $add_label    = $this->lang->line('edit') . ' ' . $module;
        $form_action      = base_url() . ADMIN_URL . '/' . $this->controller_name . "/edit/" . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($edit_records->entity_id));
    } else {
        $add_label    = $this->lang->line('add') . ' ' . $module;
        $form_action      = base_url() . ADMIN_URL . '/' . $this->controller_name . "/add";
    }
    $usertypes = getUserTypeList($this->session->userdata('language_slug'));
    ?>

    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo ($user_type != 'Driver' && $this->uri->segment(4) != 'driver') ? $this->lang->line('users') : $this->lang->line('driver') ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo ($user_type != 'Driver' && $this->uri->segment(4) != 'driver') ? '<a href=' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/view>' . $this->lang->line('users') . '</a>' : '<a href=' . base_url() . ADMIN_URL . '/' . $this->controller_name . '/driver/>' . $this->lang->line('driver') . '</a>' ?>
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
                                <div id="iframeloading" class="frame-load display-no" style="display: none;">
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
                                    <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id; ?>" />
                                    <?php if ($user_type != 'Driver' && $this->uri->segment(4) != 'driver') { ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('first_name') ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="first_name" id="first_name" value="<?php echo $first_name; ?>" maxlength="249" data-required="1" class="form-control" />

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('last_name') ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="last_name" id="last_name" value="<?php echo $last_name; ?>" maxlength="249" data-required="1" class="form-control" />
                                            </div>
                                        </div>
                                    <?php } else { ?>

                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('name') ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <!-- <input type="hidden" name="rider_id" id="rider_id" value="6" /> -->
                                                <input type="text" name="first_name" id="first_name" value="<?php echo $first_name; ?>" maxlength="249" data-required="1" class="form-control" />
                                            </div>
                                        </div>
                                    <?php  } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('image'); ?></label>
                                        <div class="col-md-4">
                                            <div class="custom-file-upload">
                                                <label for="Image" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                </label>
                                                <input type="file" name="Image" id="Image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this)" />
                                            </div>

                                            <p class="help-block"><?php echo $this->lang->line('img_allow') ?><br /> <?php echo $this->lang->line('max_file_size') ?><br /><?php echo $this->lang->line('recommended_size') . '291 * 215.'; ?></p>
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
                                    <!-- <div class="form-group">
                                        <label class="control-label col-md-3"><?php //echo $this->lang->line('phone_number')
                                                                                ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="phone_number" id="phone_number" value="<?php //echo $phone_number;
                                                                                                            ?>" maxlength="20" data-required="1" class="form-control"/>
                                        </div>
                                    </div>   -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('phone_number') ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" onblur="checkExist(this.value)" name="mobile_number" id="mobile_number" value="<?php echo $mobile_number; ?>" data-required="1" class="form-control" required />
                                        </div>
                                        <div id="phoneExist"></div>
                                    </div>
                                    <!-- Newly Added -->
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo "Present Address" ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="present_address" rows="3" value="<?php echo $edit_records->present_address; ?>" maxlength="249" data-required="1" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo "Permanent Address" ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="text" name="permanent_address" value="<?php echo $edit_records->permanent_address; ?>" maxlength="249" data-required="1" class="form-control" />
                                        </div>
                                    </div>
                                    <?php if ($user_type == 'Driver' || $this->uri->segment(4) == 'driver') { ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Bkash No.</label>
                                            <div class="col-md-4">
                                                <input type="bkash_no" name="bkash_no" id="bkash_no" value="<?php echo $bkash_no ?  $bkash_no : '' ?>" maxlength="99" class="form-control" />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="control-label col-md-3">Nagad No.</label>
                                            <div class="col-md-4">
                                                <input type="nagad_no" name="nagad_no" id="nagad_no" value="<?php echo $nagad_no ?  $nagad_no : '' ?>" maxlength="99" class="form-control" />
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($user_type != 'Driver' && $this->uri->segment(4) != 'driver') { ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('email') ?><span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="email" name="email" id="email" onblur="checkEmail(this.value,'<?php echo $entity_id ?>')" value="<?php echo $email; ?>" maxlength="99" data-required="1" class="form-control" />
                                            </div>
                                            <div id="EmailExist"></div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($user_type == 'Driver' || $this->uri->segment(4) == 'driver') { ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('user_type') ?> <span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <input type="text" name="user_type" id="user_type" value="Driver" readonly="" class="form-control">
                                            </div>
                                        </div>


                                        <!-- nid -->

                                        <div class="form-group">
                                            <label class="control-label col-md-3"> NID Image (Front)<span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <div class="custom-file-upload">
                                                    <label for="nid" class="custom-file-upload">
                                                        <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                    </label>
                                                    <input type="file" name="nid" id="nid" accept="image/*" value="" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="nidReadURL(this)" />
                                                    <span class="error display-no" id="errormsgnid"><?php echo $this->lang->line('file_extenstion') ?></span>
                                                </div>
                                                <p class="help-block"><?php echo $this->lang->line('img_allow') ?><br /><?php echo $this->lang->line('max_file_size_rider') ?><br /></p>

                                                <div id="img_gallery"></div>
                                                <img id="nidPreview" height='100' width='150' class="display-no" />
                                                <input type="hidden" name="uploaded_nid_image" id="uploaded_nid_image" value="<?php echo isset($nid) ? $nid : ''; ?>" />
                                            </div>
                                        </div>
                                        <!-- NID -->
                                        <div class="form-group" id="oldNidImage">
                                            <label class="control-label col-md-3"></label>
                                            <div class="col-md-4">
                                                <?php if (isset($nid) && $nid != '') { ?>
                                                    <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                    <img id='oldNidPic' class="img-responsive" src="<?php echo image_url . $nid; ?>">
                                                <?php }  ?>
                                            </div>
                                        </div>


                                        <!-- nid back-->
                                        <div class="form-group">
                                            <label class="control-label col-md-3"> NID Image (Back)<span class="required">*</span></label>
                                            <div class="col-md-4">
                                                <div class="custom-file-upload">
                                                    <label for="nid_back" class="custom-file-upload">
                                                        <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                    </label>
                                                    <input type="file" name="nid_back" id="nid_back" accept="image/*" value="" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="nidBackReadURL(this)" />
                                                    <span class="error display-no" id="errormsgnidBack""><?php echo $this->lang->line('file_extenstion') ?></span>
                                                </div>
                                                <p class=" help-block"><?php echo $this->lang->line('img_allow') ?><br /><?php echo $this->lang->line('max_file_size_rider') ?><br /></p>

                                                        <div id="img_gallery"></div>
                                                        <img id="nidBackPreview" height='100' width='150' class="display-no" />
                                                        <input type="hidden" name="uploaded_nid_back_image" id="uploaded_nid_back_image" value="<?php echo isset($nid_back) ? $nid_back : ''; ?>" />
                                                </div>
                                            </div>
                                            <!-- NID back-->
                                            <div class="form-group" id="oldNidBackImage">
                                                <label class="control-label col-md-3"></label>
                                                <div class="col-md-4">
                                                    <?php if (isset($nid_back) && $nid_back != '') { ?>
                                                        <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                        <img id='oldNidBackPic' class="img-responsive" src="<?php echo image_url . $nid_back; ?>">
                                                    <?php }  ?>
                                                </div>
                                            </div>
                                            <!-- This Section is for Rider Information -->

                                            <!-- Guardian NID Front -->
                                            <div class="form-group">
                                                <label class="control-label col-md-3"> Guardian NID (Front)<span class="required">*</span></label>
                                                <div class="col-md-4">
                                                    <div class="custom-file-upload">
                                                        <label for="gnid" class="custom-file-upload">
                                                            <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                        </label>
                                                        <input type="file" name="gnid" id="gnid" accept="image/*" value="" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="gnidReadURL(this)" required />
                                                        <span class="error display-no" id="errormsggnid"><?php echo $this->lang->line('file_extenstion') ?></span>
                                                    </div>
                                                    <p class="help-block"><?php echo $this->lang->line('img_allow') ?><br /><?php echo $this->lang->line('max_file_size_rider') ?><br /></p>

                                                    <div id="img_gallery"></div>
                                                    <img id="gnidPreview" height='100' width='150' class="display-no" />
                                                    <input type="hidden" name="uploaded_gnid_image" id="uploaded_gnid_image" value="<?php echo isset($gnid_front) ? $gnid_front : ''; ?>" />
                                                </div>
                                            </div>

                                            <div class="form-group" id="oldgNidImage">
                                                <label class="control-label col-md-3"></label>
                                                <div class="col-md-4">
                                                    <?php if (isset($gnid_front) && $gnid_front != '') { ?>
                                                        <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                        <img id='oldgNidPic' class="img-responsive" src="<?php echo image_url . $gnid_front; ?>">
                                                    <?php }  ?>
                                                </div>
                                            </div>
                                            <!-- Guardian NID Front -->

                                            <!-- Guardian NID Back -->
                                            <div class="form-group">
                                                <label class="control-label col-md-3"> Guardian NID (Back)<span class="required">*</span></label>
                                                <div class="col-md-4">
                                                    <div class="custom-file-upload">
                                                        <label for="gnid_back" class="custom-file-upload">
                                                            <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                        </label>
                                                        <input type="file" name="gnid_back" id="gnid_back" accept="image/*" value="" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="gnid_backReadURL(this)" />
                                                        <span class="error display-no" id="errormsggnid_back"><?php echo $this->lang->line('file_extenstion') ?></span>
                                                    </div>
                                                    <p class="help-block"><?php echo $this->lang->line('img_allow') ?><br /><?php echo $this->lang->line('max_file_size_rider') ?><br /></p>

                                                    <div id="img_gallery"></div>
                                                    <img id="gnid_backPreview" height='100' width='150' class="display-no" />
                                                    <input type="hidden" name="uploaded_gnid_back_image" id="uploaded_gnid_back_image" value="<?php echo isset($gnid_back) ? $gnid_back : ''; ?>" />
                                                </div>
                                            </div>

                                            <div class="form-group" id="oldgNid_backImage">
                                                <label class="control-label col-md-3"></label>
                                                <div class="col-md-4">
                                                    <?php if (isset($gnid_back) && $gnid_back != '') { ?>
                                                        <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                        <img id='oldgNid_backPic' class="img-responsive" src="<?php echo image_url . $gnid_back; ?>">
                                                    <?php }  ?>
                                                </div>
                                            </div>
                                            <!-- Guardian NID Back -->


                                            <!-- Electricity Bill -->
                                            <div class="form-group">
                                                <label class="control-label col-md-3"> Electricity Bill</label>
                                                <div class="col-md-4">
                                                    <div class="custom-file-upload">
                                                        <label for="ebill" class="custom-file-upload">
                                                            <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                        </label>
                                                        <input type="file" name="ebill" id="ebill" accept="image/*" value="" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="ebillReadURL(this)" />
                                                        <span class="error display-no" id="errormsgebill"><?php echo $this->lang->line('file_extenstion') ?></span>
                                                    </div>
                                                    <p class="help-block"><?php echo $this->lang->line('img_allow') ?><br /><?php echo $this->lang->line('max_file_size_rider') ?><br /></p>

                                                    <div id="img_gallery"></div>
                                                    <img id="ebillPreview" height='100' width='150' class="display-no" />
                                                    <input type="hidden" name="uploaded_ebill_image" id="uploaded_ebill_image" value="<?php echo isset($electricity_bill) ? $electricity_bill : ''; ?>" />
                                                </div>
                                            </div>

                                            <div class="form-group" id="oldebillImage">
                                                <label class="control-label col-md-3"></label>
                                                <div class="col-md-4">
                                                    <?php if (isset($electricity_bill) && $electricity_bill != '') { ?>
                                                        <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                        <img id='oldebillPic' class="img-responsive" src="<?php echo image_url . $electricity_bill; ?>">
                                                    <?php }  ?>
                                                </div>
                                            </div>
                                            <!-- Electricity Bill -->


                                            <!-- Nameplate Image -->
                                            <div class="form-group">
                                                <label class="control-label col-md-3"> House Nameplate Image</label>
                                                <div class="col-md-4">
                                                    <div class="custom-file-upload">
                                                        <label for="nameplate" class="custom-file-upload">
                                                            <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                        </label>
                                                        <input type="file" name="nameplate" id="nameplate" accept="image/*" value="" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="nameplateReadURL(this)" />
                                                        <span class="error display-no" id="errormsgnameplate"><?php echo $this->lang->line('file_extenstion') ?></span>
                                                    </div>
                                                    <p class="help-block"><?php echo $this->lang->line('img_allow') ?><br /><?php echo $this->lang->line('max_file_size_rider') ?><br /></p>

                                                    <div id="img_gallery"></div>
                                                    <img id="nameplatePreview" height='100' width='150' class="display-no" />
                                                    <input type="hidden" name="uploaded_nameplate_image" id="uploaded_nameplate_image" value="<?php echo isset($nameplate) ? $nameplate : ''; ?>" />
                                                </div>
                                            </div>

                                            <div class="form-group" id="oldnameplateImage">
                                                <label class="control-label col-md-3"></label>
                                                <div class="col-md-4">
                                                    <?php if (isset($nameplate) && $nameplate != '') { ?>
                                                        <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                        <img id='oldnameplatePic' class="img-responsive" src="<?php echo image_url . $nameplate; ?>">
                                                    <?php }  ?>
                                                </div>
                                            </div>
                                            <!-- Nameplate Image -->


                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php echo "Vehicle Type" ?> <span class="required">*</span></label>
                                                <div class="col-md-4">
                                                    <select class="form-control" name="v_type" id="v_type">
                                                        <option value="0"><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($vehicle_data as $value) {
                                                            if ($value->entity_id == $edit_records->v_type) {
                                                        ?>
                                                                <option value="<?php echo $value->entity_id; ?>" selected><?php echo $value->name; ?></option>
                                                            <?php } else { ?>
                                                                <option value="<?php echo $value->entity_id; ?>"><?php echo $value->name; ?></option>
                                                        <?php }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php echo "City Name" ?> <span class="required">*</span></label>
                                                <div class="col-md-4">
                                                    <select class="form-control" name="city_id" id="city_id" onchange="getzone(this.id,this.value)">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($city_data as $value) {
                                                            if ($value->id == $edit_records->city_id) {

                                                        ?>
                                                                <option value=" <?php echo $value->id; ?>" selected><?php echo $value->name; ?></option>
                                                            <?php } else { ?>
                                                                <option value=" <?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php echo "Zone Name" ?> <span class="required">*</span></label>
                                                <div class="col-md-4">
                                                    <select class="form-control" name="zone_id" id="zone_id">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($zone_data as $value) {
                                                            if ($value->entity_id == $edit_records->zone_id) {
                                                        ?>
                                                                <option value="<?php echo $value->entity_id; ?>" selected><?php echo $value->area_name; ?></option>
                                                            <?php } else { ?>
                                                                <option value="<?php echo $value->entity_id; ?>"><?php echo $value->area_name; ?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                </div>
                                            </div>


                                            <!-- End Section for Rider Information -->

                                        <?php } else { ?>
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php echo "User Type" ?> <span class="required">*</span></label>
                                                <div class="col-md-4">
                                                    <select class="form-control" name="user_type" id="user_type">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>
                                                        <?php foreach ($usertypes as $key => $value) { ?>
                                                            <option value="<?php echo $key; ?>" <?php echo ($user_type == $key) ? "selected" : "" ?>><?php echo $value; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if ($entity_id) { ?>
                                            <h3><?php echo $this->lang->line('change_pass') ?></h3>
                                        <?php } ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('password') ?> <?php echo ($entity_id) ? '' : '<span class="required">*</span>' ?></label>
                                            <div class="col-md-4">
                                                <input type="password" name="password" id="password" value="" maxlength="249" data-required="1" class="form-control" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $this->lang->line('confirm_pass') ?><?php echo ($entity_id) ? '' : '<span class="required">*</span>' ?></label>
                                            <div class="col-md-4">
                                                <input type="password" name="confirm_password" id="confirm_password" value="" maxlength="249" data-required="1" class="form-control" />
                                            </div>
                                        </div>
                                        </div>
                                        <div class="form-actions fluid">
                                            <div class="col-md-offset-3 col-md-9">
                                                <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit') ?></button>
                                                <?php if ($user_type != '' && $user_type != 'Driver') { ?>
                                                    <a class="btn btn-danger danger-btn" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name; ?>/view"><?php echo $this->lang->line('cancel') ?></a>
                                                <?php } else { ?>
                                                    <a class="btn btn-danger danger-btn" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name; ?>/driver"><?php echo $this->lang->line('cancel') ?></a>
                                                <?php } ?>
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
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/ckeditor/ckeditor.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
    //get items
    function getzone(id, entity_id) {
        //  console.log(entity_id);
        jQuery.ajax({
            type: "POST",
            dataType: "html",
            url: '<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getzone',
            data: {
                'entity_id': entity_id,
            },
            success: function(response) {
                //alert(response);
                $('#zone_id').empty().append(response);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
        var element = $('#' + id).find('option:selected');
    }
    jQuery(document).ready(function() {
        Layout.init(); // init current layout
    });

    function readURL(input) {
        var fileInput = document.getElementById('Image');
        var filePath = fileInput.value;
        var extension = filePath.substr((filePath.lastIndexOf('.') + 1));
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
                $('#errormsg').html('<?php echo $this->lang->line('file_extenstion') ?>').show();
                $('#Slider_image').val('');
                $("#old").show();
            }
        } else {
            $('#preview').attr('src', '').attr('style', 'display: none;');
            $('#errormsg').html('<?php echo $this->lang->line('file_size_msg') ?>').show();
            $('#Slider_image').val('');
            $("#old").show();
        }
    }

    //nid
    function nidReadURL(nidImageInput) {

        var fileInput = document.getElementById('nid');
        var filePath = fileInput.value;
        var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
        var file_size = fileInput.size;
        if (nidImageInput.files[0].size <= 307200) { // 300kb
            if (extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
                if (nidImageInput.files && nidImageInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {

                        $('#nidPreview').attr('src', e.target.result).attr('style', 'display: inline-block;');
                        $("#oldNidImage").hide();
                        $('#errormsgnid').html('').hide();
                    }
                    reader.readAsDataURL(nidImageInput.files[0]);
                }
            } else {

                $('#nidPreview').attr('src', '').attr('style', 'display: none;');
                $('#errormsgnid').html("<?php echo $this->lang->line('file_extenstion') ?>").show();
                // $('#Slider_image').val('');
                // $("#Slider_image").show();
            }
        } else {

            $('#nidPreview').attr('src', '').attr('style', 'display: none;');
            $('#errormsgnid').html("File size is bigger than 300 KB").show();
            //$('#Slider_image').val('');
            $("#oldNidImage").show();
        }
    }
    //Guardian nid Front
    function gnidReadURL(gnidImageInput) {

        var fileInput = document.getElementById('gnid');
        var filePath = fileInput.value;
        var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
        var file_size = fileInput.size;
        if (gnidImageInput.files[0].size <= 307200) { // 300kb
            if (extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
                if (gnidImageInput.files && gnidImageInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {

                        $('#gnidPreview').attr('src', e.target.result).attr('style', 'display: inline-block;');
                        $("#oldgNidImage").hide();
                        $('#errormsggnid').html('').hide();
                    }
                    reader.readAsDataURL(gnidImageInput.files[0]);
                }
            } else {

                $('#gnidPreview').attr('src', '').attr('style', 'display: none;');
                $('#errormsggnid').html("<?php echo $this->lang->line('file_extenstion') ?>").show();
                // $('#Slider_image').val('');
                // $("#Slider_image").show();
            }
        } else {

            $('#gnidPreview').attr('src', '').attr('style', 'display: none;');
            $('#errormsggnid').html("File size is bigger than 300 KB").show();
            //$('#Slider_image').val('');
            $("#oldgNidImage").show();
        }
    }
    //Guardian nid Back
    function gnid_backReadURL(gnid_backImageInput) {

        var fileInput = document.getElementById('gnid_back');
        var filePath = fileInput.value;
        var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
        var file_size = fileInput.size;
        if (gnid_backImageInput.files[0].size <= 307200) { // 300kb
            if (extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
                if (gnid_backImageInput.files && gnid_backImageInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {

                        $('#gnid_backPreview').attr('src', e.target.result).attr('style', 'display: inline-block;');
                        $("#oldgNid_backImage").hide();
                        $('#errormsggnid').html('').hide();
                    }
                    reader.readAsDataURL(gnid_backImageInput.files[0]);
                }
            } else {


                $('#gnid_backPreview').attr('src', '').attr('style', 'display: none;');
                $('#errormsggnid').html("<?php echo $this->lang->line('file_extenstion') ?>").show();
                // $('#Slider_image').val('');
                // $("#Slider_image").show();
            }
        } else {

            $('#gnid_backPreview').attr('src', '').attr('style', 'display: none;');
            $('#errormsggnid_back').html("File size is bigger than 300 KB").show();
            //$('#Slider_image').val('');
            $("#oldgNid_backImage").show();
        }
    }

    //Electricity Bill
    function ebillReadURL(ebillImageInput) {

        var fileInput = document.getElementById('ebill');
        var filePath = fileInput.value;
        var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
        var file_size = fileInput.size;
        if (ebillImageInput.files[0].size <= 307200) { // 300kb
            if (extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
                if (ebillImageInput.files && ebillImageInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {

                        $('#ebillPreview').attr('src', e.target.result).attr('style', 'display: inline-block;');
                        $("#oldebillImage").hide();
                        $('#errormsgebill').html('').hide();
                    }
                    reader.readAsDataURL(ebillImageInput.files[0]);
                }
            } else {


                $('#ebill_backPreview').attr('src', '').attr('style', 'display: none;');
                $('#errormsgebill').html("<?php echo $this->lang->line('file_extenstion') ?>").show();
                // $('#Slider_image').val('');
                // $("#Slider_image").show();
            }
        } else {

            $('#ebillPreview').attr('src', '').attr('style', 'display: none;');
            $('#errormsgebill').html("File size is bigger than 300 KB").show();
            //$('#Slider_image').val('');
            $("#oldebillImage").show();
        }
    }
    //Electricity Bill
    function nameplateReadURL(nameplateImageInput) {

        var fileInput = document.getElementById('nameplate');
        var filePath = fileInput.value;
        var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
        var file_size = fileInput.size;
        if (nameplateImageInput.files[0].size <= 307200) { // 300kb
            if (extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
                if (nameplateImageInput.files && nameplateImageInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {

                        $('#nameplatePreview').attr('src', e.target.result).attr('style', 'display: inline-block;');
                        $("#oldnameplateImage").hide();
                        $('#errormsgnameplate').html('').hide();
                    }
                    reader.readAsDataURL(nameplateImageInput.files[0]);
                }
            } else {


                $('#nameplatePreview').attr('src', '').attr('style', 'display: none;');
                $('#errormsgnameplate').html("<?php echo $this->lang->line('file_extenstion') ?>").show();
                // $('#Slider_image').val('');
                // $("#Slider_image").show();
            }
        } else {

            $('#nameplatePreview').attr('src', '').attr('style', 'display: none;');
            $('#errormsgnameplate').html("File size is bigger than 300 KB").show();
            //$('#Slider_image').val('');
            $("#oldnameplateImage").show();
        }
    }


    //nid back
    function nidBackReadURL(nidImageInput) {

        var fileInput = document.getElementById('nid_back');
        var filePath = fileInput.value;
        var extension = filePath.substr((filePath.lastIndexOf('.') + 1)).toLowerCase();
        var file_size = fileInput.size;
        if (nidImageInput.files[0].size <= 307200) { // 300kB
            if (extension == 'png' || extension == 'jpg' || extension == 'jpeg' || extension == 'gif') {
                if (nidImageInput.files && nidImageInput.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {

                        $('#nidBackPreview').attr('src', e.target.result).attr('style', 'display: inline-block;');
                        $("#oldNidBackImage").hide();
                        $('#errormsgnidBack').html('').hide();
                    }
                    reader.readAsDataURL(nidImageInput.files[0]);
                }
            } else {

                $('#nidBackPreview').attr('src', '').attr('style', 'display: none;');
                $('#errormsgnidBack').html("<?php echo $this->lang->line('file_extenstion') ?>").show();
                // $('#Slider_image').val('');
                // $("#Slider_image").show();
            }
        } else {

            $('#nidBackPreview').attr('src', '').attr('style', 'display: none;');
            $('#errormsgnidBack').html("File size is bigger than 300 KB").show();
            //$('#Slider_image').val('');
            $("#oldNidBackImage").show();
        }
    }
    //check phone number exist
    function checkExist(mobile_number) {
        var entity_id = $('#entity_id').val();
        $.ajax({
            type: "POST",
            url: BASEURL + "<?php echo ADMIN_URL ?>/users/checkExist",
            data: 'mobile_number=' + mobile_number + '&entity_id=' + entity_id,
            cache: false,
            success: function(html) {
                if (html > 0) {
                    $('#phoneExist').show();
                    $('#phoneExist').html("<?php echo $this->lang->line('phone_exist'); ?>");
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
        $.ajax({
            type: "POST",
            url: BASEURL + "<?php echo ADMIN_URL ?>/users/checkEmailExist",
            data: 'email=' + email + '&entity_id=' + entity_id,
            cache: false,
            success: function(html) {
                if (html > 0) {
                    $('#EmailExist').show();
                    $('#EmailExist').html('<?php echo $this->lang->line('alredy_exist'); ?>');
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