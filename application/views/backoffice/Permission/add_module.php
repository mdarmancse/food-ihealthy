<?php
$this->load->view(ADMIN_URL . '/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar'); ?>

    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- Alert Message -->
            <?php
            $message = $this->session->userdata('message');
            if (isset($message)) {
            ?>
                <div class="alert alert-info alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $message ?>
                </div>
            <?php
                $this->session->unset_userdata('message');
            }
            $error_message = $this->session->userdata('error_message');
            if (isset($error_message)) {
            ?>
                <div class="alert alert-danger alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    <?php echo $error_message ?>
                </div>
            <?php
                $this->session->unset_userdata('error_message');
            }
            ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-bd lobidrag">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h4><?php echo $this->lang->line('add_module'); ?> </h4>
                            </div>
                        </div>
                        <?php echo form_open('backoffice/Permission/store_module/', array('class' => 'form-vertical', 'id' => 'insert_module')) ?>
                        <div class="panel-body">

                            <div class="form-group row">
                                <label for="module_name" class="col-sm-3 col-form-label"><?php echo "Module Name" ?> <i class="text-danger">*</i></label>
                                <div class="col-sm-6">

                                    <input class="form-control" name="module_name" id="module_name" type="text" placeholder="<?php echo "Module Name" ?>" required="" tabindex="1" value="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="module_name" class="col-sm-3 col-form-label"><?php echo "Module Description" ?> <i class="text-danger">*</i></label>
                                <div class="col-sm-6">

                                    <input class="form-control" name="module_description" id="module_description" type="text" placeholder="<?php echo "Module Description" ?>" required="" tabindex="1" value="">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-8">


                                </div>
                                <div class="col-sm-2">
                                    <input type="submit" id="add-module" class="btn btn-primary btn-large" name="add-module" value="<?php echo "Save" ?>" tabindex="4" />

                                </div>
                            </div>
                        </div>
                        <?php echo form_close() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>