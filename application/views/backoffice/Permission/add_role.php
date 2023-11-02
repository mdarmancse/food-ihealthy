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
                    <div class="panel panel-bd lobidrag" style="border:3px solid #374767">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h2><b><?php echo $title ?> </b></h2>
                            </div>
                        </div>
                        <?php echo form_open("backoffice/Permission/storerole/") ?>
                        <div class="panel-body">
                            <div class="form-group row">
                                <label for="type" class="col-sm-3 col-form-label"><?php echo $this->lang->line('add_role'); ?> <i class="text-danger">*</i></label>
                                <div class="col-sm-6">
                                    <input type="text" tabindex="2" class="form-control" name="role_id" id="type" placeholder="<?php echo "Role Name" ?>" required />
                                </div>
                            </div>
                            <?php
                            if ($accounts) {
                                $m = 0;
                                foreach ($accounts as $key => $value) {
                                    $account_sub = $this->db->select('*')->from('sub_module')->where('mid', $value['id'])->get()->result();
                            ?>
                                    <table class="table table-bordered">
                                        <h2 class=""><?php echo $value['name']; ?></h2>
                                        <thead>
                                            <tr>
                                                <th><?php echo "Sl. No"; ?></th>
                                                <th><?php echo "Menu Name"; ?></th>
                                                <th><?php echo "Create"; ?> (<label for="checkAllcreate<?php echo $m ?>"><input type="checkbox" onclick="checkallcreate(<?php echo $m ?>)" id="checkAllcreate<?php echo $m ?>" name=""> All)</label></th>
                                                <th><?php echo "Read"; ?> (<input type="checkbox" onclick="checkallread(<?php echo $m ?>)" id="checkAllread<?php echo $m ?>" name=""> all)</th>
                                                <th><?php echo "Update"; ?> (<input type="checkbox" onclick="checkalledit(<?php echo $m ?>)" id="checkAlledit<?php echo $m ?>" name=""> all)</th>
                                                <th><?php echo "Delete"; ?> (<input type="checkbox" onclick="checkalldelete(<?php echo $m ?>)" id="checkAlldelete<?php echo $m ?>" name=""> all)</th>
                                            </tr>
                                        </thead>
                                        <?php $sl = 0 ?>
                                        <?php if (!empty($account_sub)) { ?>
                                            <?php foreach ($account_sub as $key1 => $module_name) {
                                            ?>

                                                <?php
                                                $createID = 'id="create' . $m . '' . $sl . '" class="create' . $m . '"';
                                                $readID   = 'id="read' . $m . '' . $sl . '" class="read' . $m . '"';
                                                $updateID = 'id="update' . $m . '' . $sl . '" class="edit' . $m . '"';
                                                $deleteID = 'id="delete' . $m . '' . $sl . '" class="delete' . $m . '"';
                                                ?>
                                                <tbody>
                                                    <tr>
                                                        <td><?php echo ($sl + 1) ?></td>
                                                        <td>
                                                            <?php echo $module_name->category ?>
                                                            <input type="hidden" name="fk_module_id[<?php echo $m ?>][<?php echo $sl ?>][]" value="<?php echo $module_name->id ?>" id="id_<?php echo $module_name->id ?>">
                                                        </td>
                                                        <td>
                                                            <div class="checkbox checkbox-success text-center" style="margin-left: 90px;">
                                                                <?php echo form_checkbox('create[' . $m . '][' . $sl . '][]', '1', null, $createID); ?>
                                                                <label for="create<?php echo $m ?><?php echo $sl ?>"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="checkbox checkbox-success text-center" style="margin-left: 90px;">
                                                                <?php echo form_checkbox('read[' . $m . '][' . $sl . '][]', '1', null, $readID); ?>
                                                                <label for=" read<?php echo $m ?><?php echo $sl ?>"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="checkbox checkbox-success text-center" style="margin-left: 90px;">
                                                                <?php echo form_checkbox('update[' . $m . '][' . $sl . '][]', '1', null, $updateID); ?>
                                                                <label for="update<?php echo $m ?><?php echo $sl ?>"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="checkbox checkbox-success text-center" style=" margin-left: 90px;">
                                                                <?php echo form_checkbox('delete[' . $m . '][' . $sl . '][]', '1', null, $deleteID); ?>
                                                                <label for="delete<?php echo $m ?><?php echo $sl ?>"></label>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <?php $sl++ ?>
                                            <?php } ?>
                                        <?php } //endif
                                        ?>
                                    </table>
                                <?php $m++;
                                }
                            } else { ?>
                                <center>
                                    <h1>No menu found!</h1>
                                </center>
                            <?php } ?>
                            <div class="form-group text-right">
                                <button type="reset" class="btn btn-primary w-md m-b-5"><?php echo "Reset" ?></button>
                                <button type="submit" class="btn btn-success w-md m-b-5"><?php echo "Save" ?></button>
                            </div>
                            <?php echo form_close() ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/admin-management.js"></script>

<?php $this->load->view(ADMIN_URL . '/footer'); ?>