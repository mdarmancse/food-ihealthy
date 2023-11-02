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
                        <?php echo "Reward Point Setting" ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                            <!-- <i class="fa fa-dollar"></i> -->
                        </li>
                        <li>
                            <?php echo "Reward Point Setting" ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Add Voucher/Coupon</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="form_add_status" name="form_add_status" method="post" class="form-horizontal" enctype="multipart/form-data">
                                <div class="form-group m-bt">
                                    <label class="control-label col-md-6">Voucher/Coupon Name<span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="name" value="" maxlength="250" class="form-control" required>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group m-bt">
                                    <label class="control-label col-md-6">Voucher/Coupon Type<span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <select class="form-control" name="type" id="type" onchange="showDiv('hidden_div', this)" required>
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <option value="Coupon"><?php echo "Coupon" ?></option>
                                            <option value="Voucher"><?php echo "Voucher" ?></option>
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group m-bt">
                                    <label class="control-label col-md-6">Cost (In Points)<span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="cost" value="" maxlength="250" class="form-control" required>
                                    </div>
                                </div>
                                <br>
                                <div class="form-group m-bt" id="hidden_div">
                                    <label class="control-label col-md-6">Value (In Taka)<span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" name="value" value="" maxlength="250" class="form-control">

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-6"><?php echo $this->lang->line('image'); ?><span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <div class="custom-file-upload">
                                            <label for="Image" class="custom-file-upload">
                                                <i class="fa fa-cloud-upload"></i> <?php echo $this->lang->line('no_file') ?>
                                            </label>
                                            <input type="file" name="Image" id="Image" accept="image/*" data-msg-accept="<?php echo $this->lang->line('file_extenstion') ?>" onchange="readURL(this)" required />
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
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-sm  danger-btn filter-submit margin-bottom voucher_submit" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save') ?></span></button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            <!-- END PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">

                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <form action="<?php echo base_url() . ADMIN_URL; ?>/system_option/reward_view" method="post" id="SystemOption" name="SystemOption" class="form-horizontal">
                                <div class="form-body">
                                    <?php
                                    if ($this->session->flashdata('SystemOptionMSG')) { ?>
                                        <div class="alert alert-success">
                                            <?php echo $this->session->flashdata('SystemOptionMSG'); ?>
                                        </div>
                                    <?php } ?>

                                    <?php
                                    foreach ($Reward_System_List as $key => $OptionVal) {

                                        $optionName = $OptionVal->name;

                                    ?>
                                        <div class="form-group">
                                            <label class="control-label col-md-3"><?php echo $optionName; ?></label>
                                            <div class="col-md-6">
                                                <input type="hidden" name="entity_id[]" value="<?php echo $OptionVal->entity_id; ?>">
                                                <input type="text" name="value[]" value="<?php echo htmlentities($OptionVal->value); ?>" maxlength="250" class="form-control">
                                            </div>
                                        </div>

                                    <?php } ?>
                                    <div class="form-group">
                                        <label class="control-label col-md-3"><?php echo "Vouchers"; ?></label>
                                        <div class="col-md-6">
                                            <table class="table table-striped table-bordered">
                                                <tr>
                                                    <th>Sl. No.</th>
                                                    <th>Name</th>
                                                    <th>Value</th>
                                                    <th colspan="2" class="text-center">Action</th>
                                                </tr>
                                                <?php

                                                $sl = 1;
                                                foreach ($vouchers_list as $key => $voucher) {
                                                    $status = "Active";
                                                    if ($voucher->status == 1) {
                                                        $status = "Deactive";
                                                    }
                                                    $optionName = $OptionVal->name;

                                                ?>
                                                    <tr>
                                                        <td><?php echo $sl++; ?></td>
                                                        <td><?php echo $voucher->name; ?></td>
                                                        <td><?php echo $voucher->value ? $voucher->value : 'N\A'; ?></td>

                                                        <td><input value='<?php echo $status ?>' type='button' class='btn btn-primary' onclick="disable_record(<?php echo $voucher->entity_id; ?>,<?php echo $voucher->status; ?>)"></td>
                                                        <td><input value="Delete" type='button' class='btn btn-danger' onclick="soft_delete(<?php echo $voucher->entity_id; ?>)"></td>

                                                    </tr>

                                                <?php } ?>
                                            </table>
                                        </div>
                                        <?php if ($this->lpermission->method('reward_point_setting', 'create')->access()) { ?>
                                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                                                Add Voucher
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>


                                <div class="form-actions fluid">
                                    <div class="col-md-offset-6 col-md-6 pull-right">
                                        <button type="submit" name="SubmitSystemSetting" id="SubmitSystemSetting" class="btn danger-btn" value="Submit"><?php echo $this->lang->line('submit') ?></button>
                                    </div>
                                </div>
                            </form>




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
<style>
    table,
    th,
    td {
        border: 1px solid black;
    }

    th,
    td {
        padding-right: 10px;
    }

    .m-bt {
        margin-bottom: 5px;
    }

    #hidden_div {
        display: none;
    }
</style>
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


    $('select').on('change', function() {
        var type = this.val();
        if (type == "Voucher") {
            $("#voucher_value").hide();
        }
    });

    function showDiv(divId, element) {
        document.getElementById(divId).style.display = element.value == "Coupon" ? 'block' : 'none';
    }

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

    function deleteDetail(entity_id) {
        bootbox.confirm({
            message: "<?php echo $this->lang->line('delete_module'); ?>",
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                },
                cancel: {
                    label: '<?php echo $this->lang->line('cancel'); ?>',
                }
            },
            callback: function(deleteConfirm) {
                if (deleteConfirm) {
                    jQuery.ajax({
                        type: "POST",
                        dataType: "html",
                        url: 'ajaxDelete',
                        data: {
                            'entity_id': entity_id,
                            'table': 'reward_point_setting'
                        },
                        success: function(response) {
                            grid.getDataTable().fnDraw();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert(errorThrown);
                        }
                    });
                }
            }
        });
    }

    function disable_record(entity_id, Status) {
        var StatusVar = (Status == 0) ? "<?php echo $this->lang->line('active_module'); ?>" : "<?php echo $this->lang->line('deactive_module'); ?>";
        bootbox.confirm({
            message: StatusVar,
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                },
                cancel: {
                    label: '<?php echo $this->lang->line('cancel'); ?>',
                }
            },
            callback: function(disableConfirm) {
                if (disableConfirm) {
                    jQuery.ajax({
                        type: "POST",
                        dataType: "json",
                        url: 'ajaxDisable',
                        data: {
                            'entity_id': entity_id,
                            'status': Status,
                            'tblname': 'reward_point_setting'
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert(errorThrown);
                        }
                    });
                }
            }
        });
    }

    function soft_delete(entity_id) {
        var StatusVar = "<?php echo "Want to hide this Voucher ? "; ?>";
        bootbox.confirm({
            message: StatusVar,
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                },
                cancel: {
                    label: '<?php echo $this->lang->line('cancel'); ?>',
                }
            },
            callback: function(disableConfirm) {
                if (disableConfirm) {
                    jQuery.ajax({
                        type: "POST",
                        dataType: "json",
                        url: 'softdelete',
                        data: {
                            'entity_id': entity_id,
                            'tblname': 'reward_point_setting'
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert(errorThrown);
                        }
                    });
                }
            }
        });
    }


    $('#form_add_status').submit(function(e) {
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: BASEURL + "backoffice/System_option/AddVoucher",
            data: new FormData($(this)[0]),
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#quotes-main-loader').show();
            },
            success: function(html) {
                $('#quotes-main-loader').hide();
                $('#exampleModal').modal('hide');
                location.reload();
            }
        });
        return false;
    });
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>