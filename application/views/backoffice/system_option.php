<?php $this->load->view(ADMIN_URL . '/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/css/jquery.timepicker.css" />

<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar'); ?>
    <!-- END sidebar -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                        <?php echo $this->lang->line('titleadmin_systemoptions') ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('titleadmin_systemoptions') ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"> <?php echo $this->lang->line('titleadmin_systemoptions') ?></div>
                            <div class="actions">
                                <button class="btn danger-btn btn-sm" id="more_setting_button">Operation Settings</button>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo base_url() . ADMIN_URL; ?>/system_option/view" method="post" id="SystemOption" name="SystemOption" class="form-horizontal">
                                <div class="form-body">
                                    <?php
                                    if ($this->session->flashdata('SystemOptionMSG')) { ?>
                                        <div class="alert alert-success">
                                            <?php echo $this->session->flashdata('SystemOptionMSG'); ?>
                                        </div>
                                    <?php } ?>

                                    <?php
                                    foreach ($SystemOptionList as $key => $OptionDet) {
                                        if ($this->session->userdata('language_slug') == 'ar') {
                                            $optionName = $OptionDet->OptionName_ar;
                                        } else if ($this->session->userdata('language_slug') == 'fr') {
                                            $optionName = $OptionDet->OptionName_fr;
                                        } else {
                                            $optionName = $OptionDet->OptionName;
                                        }  ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $optionName; ?></label>
                                            <div class="col-md-6">
                                                <input type="hidden" name="SystemOptionID[]" value="<?php echo $OptionDet->SystemOptionID; ?>">
                                                <input type="text" name="OptionValue[]" value="<?php echo htmlentities($OptionDet->OptionValue); ?>" maxlength="250" class="form-control">
                                            </div>
                                        </div>

                                    <?php } ?>
                                </div>

                                <div class="form-actions fluid">
                                    <div class="col-md-offset-6 col-md-6 pull-right">
                                        <button type="submit" name="SubmitSystemSetting" id="SubmitSystemSetting" class="btn danger-btn" value="Submit"><?php echo $this->lang->line('submit') ?></button>
                                    </div>
                                </div>
                            </form>

                            <div id="extra_setting_modal" class="modal">
                                <div class="modal-dialog">
                                    <!-- Modal content-->
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">

                                            <form action="<?php echo  base_url() . ADMIN_URL . '/' . $this->controller_name . '/addMoreSettings'; ?>" id="form_add_more" name="form_add_more" method="post" class="form-horizontal" enctype="multipart/form-data">

                                                <div class="form-group">
                                                    <label class="control-label col-md-4">Operation On/Off</label>
                                                    <div class="col-md-6">
                                                        <input type="hidden" name="operation_on_off" value="<?= $extra_setting ? $extra_setting['operation_on_off'] : 0 ?>" id="operation_on_off">
                                                        <a style="cursor:pointer;" onclick="change_op()" id="operation_on_off_buttton">
                                                            <i style="font-size: 1.8em; padding-top: 5px;" class="<?= ($extra_setting && $extra_setting['operation_on_off'] == 1 ? 'fa fa-toggle-on' : 'fa fa-toggle-off')  ?>">
                                                            </i>
                                                        </a>
                                                    </div>
                                                </div>

                                                <div class="form-group op_img" style="display: <?= ($extra_setting && $extra_setting['operation_on_off'] == 1 ? 'none' : 'block')  ?>;">
                                                    <label class="control-label col-md-4">Image to show</label>
                                                    <div class="col-md-6">
                                                        <div class="custom-file-upload">
                                                            <label for="Image" class="custom-file-upload">
                                                                <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                                            </label>

                                                            <input type="file" name="Image" id="Image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this)" />
                                                        </div>
                                                        <p class="help-block"><?php echo $this->lang->line('img_allow') ?><br /><?php echo $this->lang->line('max_file_size') ?><br /><?php echo $this->lang->line('recommended_size') . '500 * 800.'; ?></p>
                                                        <span class="error display-no" id="errormsg"><?php echo $this->lang->line('file_extenstion') ?></span>
                                                        <div id="img_gallery"></div>
                                                        <img id="preview" height='100' width='150' class="display-no" />
                                                        <input type="hidden" name="uploaded_image" id="uploaded_image" value="<?php echo isset($extra_setting['operation_off_image']) ? $extra_setting['operation_off_image'] : ''; ?>" />
                                                    </div>
                                                </div>

                                                <div class="form-group" id="old" style="display: <?= ($extra_setting && $extra_setting['operation_on_off'] == 1 ? 'none' : 'block')  ?>;">
                                                    <label class="control-label col-md-4"></label>
                                                    <div class="col-md-6">
                                                        <?php if (isset($extra_setting['operation_off_image']) && $extra_setting['operation_off_image'] != '') { ?>
                                                            <span class="block"><?php echo $this->lang->line('selected_image') ?></span>
                                                            <img id='oldpic' class="img-responsive" src="<?php echo image_url . $extra_setting['operation_off_image']; ?>">
                                                        <?php }  ?>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label class="control-label col-md-4">Operation Time (HH:MM)</label>
                                                    <?php if (empty($_POST['operation_timing'])) {
                                                        $operation_timing = unserialize(html_entity_decode($extra_setting['operation_timing']));
                                                    } else {
                                                        $operation_timingsArr = $_POST['operation_timing'];
                                                        $newTimingArr = array();
                                                        foreach ($operation_timingsArr as $key => $value) {
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
                                                        $operation_timing = $newTimingArr;
                                                    }  ?>
                                                    <div class="col-md-8">
                                                        <table class="timingstable" width="100%" cellpadding="2" cellspacing="2">
                                                            <tr>
                                                                <td>
                                                                    <input type="hidden" name="operation_timing[time1][on]" value="on">
                                                                    <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$operation_timing['time1']['on'])) ? 'checked="checked"' : ''; ?> value="time1" class="break_close_bar_check" id="time1_close" name="operation_timing[time1][on]"></label>
                                                                </td>
                                                                <td>
                                                                    <div class="td-wrap">
                                                                        <input type="text" class="ophrs" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" lesserThan="#time1_close_hours" id="time1_open_hours" name="operation_timing[time1][open]" <?php echo (intval(@$operation_timing['time1']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$operation_timing['time1']['open']; ?>" placeholder="Start Time" autocomplete="off">
                                                                    </div>
                                                                    <div class="td-wrap">
                                                                        <input type="text" class="clhrs" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" greaterThan="#time1_open_hours" placeholder="End Time" <?php echo (intval(@$operation_timing['time1']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$operation_timing['time1']['close']; ?>" name="operation_timing[time1][close]" id="time1_close_hours" autocomplete="off">
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td>
                                                                    <input type="hidden" name="operation_timing[time2][on]" value="on">
                                                                    <label class="checkbox width-full"><input type="checkbox" <?php echo (intval(@$operation_timing['time2']['on'])) ? 'checked="checked"' : ''; ?> value="time2" class="break_close_bar_check" id="time2_close" name="operation_timing[time2][on]"></label>
                                                                </td>
                                                                <td>
                                                                    <div class="td-wrap">
                                                                        <input type="text" class="ophrs" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" lesserThan="#time2_close_hours" id="time2_open_hours" name="operation_timing[time2][open]" <?php echo (intval(@$operation_timing['time2']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$operation_timing['time2']['open']; ?>" placeholder="Start Time" autocomplete="off">
                                                                    </div>
                                                                    <div class="td-wrap">
                                                                        <input type="text" class="clhrs" pattern="([01]?[0-9]|2[0-3]):[0-5][0-9]" greaterThan="#time2_open_hours" placeholder="End Time" <?php echo (intval(@$operation_timing['time2']['on'])) ? '' : 'disabled="disabled"'; ?> value="<?php echo @$operation_timing['time2']['close']; ?>" name="operation_timing[time2][close]" id="time2_close_hours" autocomplete="off">
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                        </table>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <div class="col-sm-3 pull-right">
                                                        <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit') ?></button>
                                                    </div>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- END FORM-->
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->

</div>
<div class="wait-loader display-no" id="quotes-main-loader"><img src="<?php echo base_url() ?>assets/admin/img/ajax-loader.gif" align="absmiddle"></div>
<!-- BEGIN PAGE LEVEL PLUGINS -->


<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
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

    $('#time1_open_hours').timepicker({
        timeFormat: "HH:mm",
        controlType: 'select',
        ampm: true,
        stepMinute: 5,
        showButtonPanel: false
    });
    $('#time1_close_hours').timepicker({
        timeFormat: "HH:mm",
        controlType: 'select',
        ampm: true,
        stepMinute: 5,
        showButtonPanel: false
    });

    $('#time2_open_hours').timepicker({
        timeFormat: "HH:mm",
        controlType: 'select',
        ampm: true,
        stepMinute: 5,
        showButtonPanel: false
    });
    $('#time2_close_hours').timepicker({
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

    $("#more_setting_button").click(function() {
        $("#extra_setting_modal").modal("show");
    })

    // $("#form_add_more").on('submit', function(e) {
    //     e.preventDefault();

    //     $.ajax({
    //         url: $(this).attr('action'),
    //         method: $(this).attr('method'),
    //         // dataType : 'json',
    //         data: new FormData(this),
    //         processData: false,
    //         contentType: false,
    //         beforeSend: function() {
    //             // $('#quotes-main-loader').show();
    //         },
    //         success: function(d) {

    //             // $("#quotes-main-loader").hide();

    //             // $("#extra_setting_modal").modal("hide");

    //             location.reload();

    //         },
    //         error: function(xhr) {

    //             alert('failed!');
    //         }
    //     });
    // })

    function change_op() {
        const current = $("#operation_on_off").val();
        let new_value;

        (current == 1) ?
        new_value = 0:
            new_value = 1;

        bootbox.confirm({
            message: `Are you sure you want to turn ${(current == 1 ? 'off' : 'on')} operation?`,
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                },
                cancel: {
                    label: '<?php echo $this->lang->line('cancel'); ?>',
                }
            },
            callback: function(confirm) {
                if (confirm) {
                    $("#operation_on_off").val(new_value);
                    $("#operation_on_off_buttton i").removeClass(current == 1 ? "fa-toggle-on" : "fa-toggle-off");
                    $("#operation_on_off_buttton i").addClass(current == 1 ? "fa-toggle-off" : "fa-toggle-on");
                    (current == 0) ?
                    $('.op_img').hide():
                        $('.op_img').show();

                    (current == 0) ?
                    $('#old').hide():
                        $('#old').show()
                }
            }
        });
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
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>