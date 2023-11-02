<?php $this->load->view(ADMIN_URL . '/header'); ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/multiselect/sumoselect.min.css" />
<!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar'); ?>
    <!-- END sidebar -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content admin-dashboard">
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title"><?php echo $this->lang->line('sub_dashboard') ?> <small>/ <?php echo $this->lang->line('rider_dashboard') ?></small></h3>
                    <ul class="page-breadcrumb breadcrumb" style="display: flex; justify-content: space-between;">
                        <li><?php echo $this->lang->line('rider_dashboard') ?> </li>
                        <div>
                            <div class="col-sm-10 form-group">

                                <label class="control-label col-md-4"><?php echo "City" ?></label>
                                <div class=" col-md-8">
                                    <select class="form-control" name="city_id" id="city_id" onchange="loadData()">
                                        <option value="" selected><?php echo $this->lang->line('select') ?></option>
                                        <?php foreach ($city_data as $value) { ?>
                                            <option value=" <?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="loadData()"><i class="fa fa-refresh"></i></button>
                        </div>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12" id="main-data-div">

                </div>
            </div>

        </div>
        <div class="clearfix">
        </div>
    </div>
</div>
<!-- END CONTENT -->
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/index.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/admin-management.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/datatable.js"></script>
<?php if ($this->session->userdata("language_slug") == 'ar') {  ?>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<?php } ?>
<!-- END PAGE LEVEL SCRIPTS -->
<script>
    jQuery(document).ready(function() {

        Layout.init();
        loadData();

    });

    $("#city_id").change(function() {
        loadData();
    })

    function loadData() {
        const city_id = $("#city_id").val();
        $.ajax({
            type: "POST",
            url: 'ajaxView_overall',
            dataType: "html",
            data: {
                city_id: city_id,
            },
            success: function(response) {
                $("#main-data-div").html(response);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }

        })
    }
</script>
<!-- END JAVASCRIPTS -->
<?php $this->load->view(ADMIN_URL . '/footer'); ?>