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
            <!-- <div class="row">
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

                                    <input class="form-control" name="module_name" id="module_name" type="text" placeholder="<?php echo "Module Name" ?>" required="" tabindex="1" value="module_Name">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="module_name" class="col-sm-3 col-form-label"><?php echo "Module Description" ?> <i class="text-danger">*</i></label>
                                <div class="col-sm-6">

                                    <input class="form-control" name="module_description" id="module_description" type="text" placeholder="<?php echo "Module Description" ?>" required="" tabindex="1" value="module_description">
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
            </div> -->
            <div class="row">
                <?php
                if ($this->session->flashdata('page_MSG')) { ?>
                    <div class="alert alert-success">
                        <?php echo $this->session->flashdata('page_MSG'); ?>
                    </div>
                <?php } ?>
                <div class="col-md-12">
                    <!-- BEGIN VALIDATION STATES-->
                    <div class="portlet box">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h4><?php echo $this->lang->line('add_submodule'); ?> </h4>
                            </div>
                        </div>
                        <div class="portlet-body form">
                            <!-- BEGIN FORM-->
                            <?php echo form_open('backoffice/Permission/store_submodule/', array('class' => 'form-horizontal', 'id' => 'insert_module')) ?>
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo "Sub Module Name" ?><span class="required">*</span></label>
                                    <div class="col-md-4">
                                        <input type="text" name="sub_name" id="sub_name" value="" maxlength="249" data-required="1" class="form-control" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3"><?php echo "Module Name" ?> <span class="required">*</span></label>
                                    <div class="col-md-4">
                                        <select class="form-control" name="module_id" id="module_id">
                                            <option value=""><?php echo "select one" ?></option>
                                            <?php
                                            foreach ($module_list as $udata) {
                                            ?>
                                                <option value="<?php echo $udata['id'] ?>"><?php echo $udata['name'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions fluid">
                                <div class="col-md-offset-3 col-md-9">
                                    <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo $this->lang->line('submit') ?></button>

                                </div>
                            </div>
                            <?php echo form_close() ?>
                            <!-- END FORM-->
                        </div>
                    </div>
                    <!-- END VALIDATION STATES-->
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>