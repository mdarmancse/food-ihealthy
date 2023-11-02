<!-- BEGIN PAGE LEVEL STYLES -->
<?php $this->load->view(ADMIN_URL . '/header'); ?>
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" > -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"> -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/multiselect/sumoselect.min.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/multiselect/bootstrap-multiselect.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/multiselect/bootstrap-multiselect.min.css" /> -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/multiselect-master/styles/multiselect.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/css/datetimepicker.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />

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
        $FieldsArray = array('entity_id', 'notification_title', 'notification_description', 'selection_type');
        foreach ($FieldsArray as $key) {
            $$key = @htmlspecialchars($editNotificationDetail->$key);
        }
    }
    /*$add_label    = "Send Notification";
$form_action      = base_url().ADMIN_URL."/notification/add";*/

    if (isset($editNotificationDetail) && $editNotificationDetail != "") {
        $add_label    = $this->lang->line('edit') . ' ' . $this->lang->line('notification');
        $form_action      = base_url() . ADMIN_URL . '/' . $this->controller_name . "/edit/" . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($editNotificationDetail->entity_id));
    } else {
        $add_label    = $this->lang->line('add') . ' ' . $this->lang->line('notification');
        $form_action      = base_url() . ADMIN_URL . '/' . $this->controller_name . "/add";
    }

    ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('notification'); ?></h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL; ?>">
                                <?php echo $this->lang->line('home'); ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/notification/view"><?php echo $this->lang->line('notification'); ?></a>
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
                            <form action="<?php echo $form_action; ?>" id="form_add_notification" name="form_add_notification" method="post" class="form-horizontal" enctype="multipart/form-data">
                                <div class="form-body">
                                    <?php if (!empty($Error)) { ?>
                                        <div class="alert alert-danger"><?php echo $Error; ?></div>
                                    <?php } ?>
                                    <?php if (validation_errors()) { ?>
                                        <div class="alert alert-danger">
                                            <?php echo validation_errors(); ?>
                                        </div>
                                    <?php } ?>
                                    <!-- <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('users'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" id="entity_id" name="entity_id" value="<?php echo $entity_id; ?>" />

                                            <select name="user_id[]" placeholder="Select Users" multiple="multiple" class="form-control" id="user_id">
                                                <?php foreach ($users as $key => $user) { ?>
                                                    <option value="<?php echo $user->entity_id ?>" <?php echo (in_array($user->entity_id, $NotificationUsers)) ? 'selected' : ''; ?>><?php echo $user->first_name . ' ' . $user->last_name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div> -->


                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('users'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <div class="radio-btn-list">
                                                <label>
                                                    <input type="radio" name="users" value="all_users" <?php echo ($selection_type == 'all_users') ? 'checked' : '' ?> onclick="allUsers();">
                                                    <span>Select All Users</span>
                                                </label>
                                            </div>



                                            <div class="radio-btn-list">
                                                <label>
                                                    <input type="radio" name="users" value="specific_users" <?php echo ($selection_type == 'specific_users') ? 'checked'  : '' ?> onclick="showUserList();">
                                                    <span>Select Specific Users</span>
                                                </label>
                                            </div>

                                            <div id="specific_users" class=<?php echo ($selection_type == 'specific_users') ? '' : 'display-no' ?>>
                                                <h5>Choose Specific User</h5>

                                                <div class="login-details">
                                                    <div class="form-group">
                                                        <select name="user_id[]" placeholder="Select Users" multiple="multiple" class="form-control" id="user_id">
                                                            <?php
                                                            if ($NotificationUsers) {
                                                                foreach ($NotificationUsers as $noti) { ?>
                                                                    <option value="<?= $noti['user_id'] ?>" selected><?= $noti['first_name'] . ' ' . $noti['last_name'] . ' (' . $noti['mobile_number'] . ')' ?></option>
                                                            <?php }
                                                            }
                                                            ?>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('title'); ?><span class="required">*</span></label>
                                        <div class="col-md-4">
                                            <input type="hidden" name="entity_id" id="entity_id" value="<?php echo $entity_id; ?>" />
                                            <input type="text" name="notification_title" id="notification_title" value="<?php echo $notification_title; ?>" maxlength="249" data-required="1" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('image') ?></label>
                                        <div class="col-md-4">
                                            <div class="custom-file-upload">
                                                <label for="image" class="custom-file-upload">
                                                    <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                </label>
                                                <input type="file" name="image" id="image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this)" />
                                            </div>
                                            <p class="help-block"><?php echo $this->lang->line('img_allow'); ?><br /> <?php echo $this->lang->line('max_file_size'); ?><br /><?php echo $this->lang->line('recommended_size') . '290 * 210.'; ?></p>
                                            <span class="error display-no" id="errormsg"></span>
                                            <div id="img_gallery"></div>
                                            <img id="preview" height='100' width='150' class="display-no" />
                                            <video controls id="v-control" class="display-no">
                                                <source id="source" src="" type="video/mp4">
                                            </video>
                                            <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo isset($image) ? $image : ''; ?>" />
                                        </div>
                                    </div>
                                    <div class="form-group" id="old">
                                        <label class="control-label col-md-3"></label>
                                        <div class="col-md-4">
                                            <?php if (isset($image) && $image != '') { ?>
                                                <span class="block"><?php echo $this->lang->line('selected_image'); ?></span>
                                                <img id='oldpic' class="img-responsive" src="<?php echo image_url . $image; ?>">
                                            <?php }  ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('message'); ?></label>
                                        <div class="col-md-4">
                                            <textarea class="form-control" name="notification_description" id="notification_description" rows="6" data-required="1"><?php echo $notification_description; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo $this->lang->line('save_noti'); ?></label>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="save" id="save" value="1">
                                        </div>
                                    </div>


                                </div>
                                <div class="form-actions fluid">
                                    <div class="col-md-offset-3 col-md-9">
                                        <button type="submit" name="submitNotification" id="submitNotification" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit') ?></button>
                                        <a class="btn btn-danger danger-btn" href="<?php echo base_url() . ADMIN_URL; ?>/notification/view"><?php echo $this->lang->line('cancel') ?></a>
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

<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/admin-management.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<!-- <script src="<?php echo base_url(); ?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/plugins/multiselect/bootstrap-multiselect.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/plugins/multiselect/bootstrap-multiselect.min.js"></script> -->
<!-- <script src="<?php echo base_url(); ?>assets/admin/plugins/multiselect/require-2.3.5.min.js"></script> -->
<!-- <script src="<?php echo base_url(); ?>assets/admin/plugins/multiselect-master/multiselect.min.js"></script> -->
<!-- <script src="<?php echo base_url(); ?>assets/admin/plugins/multiselect-master/scripts/multiselect.js"></script> -->
<script>
    jQuery(document).ready(function() {
        Layout.init(); // init current layout
        // $('#user_id').SumoSelect({
        //     selectAll: false,
        //     search: true,
        // });


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
        })

        $(".select2-container--default").attr("style", "width:100%");


    });

    function readURL(input) {
        var fileInput = document.getElementById('image');
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
                            $('#preview').attr('src', '').hide();
                        } else {
                            $('#preview').attr('src', e.target.result).attr('style', 'display: inline-block;');
                            $('#v-control').hide();
                            $('#source').attr('src', '');
                        }
                        $("#old").hide();
                        $('#errormsg').html('').hide();
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            } else {
                $('#preview').attr('src', '').attr('style', 'display: none;');
                $('#errormsg').html("<?php echo $this->lang->line('file_extenstion'); ?>").show();
                $('#Slider_image').val('');
                $("#old").show();
            }
        } else {
            $('#preview').attr('src', '').attr('style', 'display: none;');
            $('#errormsg').html("<?php echo $this->lang->line('file_size_msg'); ?>").show();
            $('#Slider_image').val('');
            $('#source').attr('src', '');
            $('#v-control').hide();
            $("#old").show();
        }
    }
    // for datepicker


    function showUserList() {
        document.getElementById('specific_users').style.display = 'block';
    }

    function allUsers() {
        document.getElementById('specific_users').style.display = 'none';
    }
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>