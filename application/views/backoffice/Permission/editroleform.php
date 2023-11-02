<?php
$this->load->view(ADMIN_URL . '/header');
?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar'); ?>

    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- New customer -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-bd lobidrag">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h2><b><?php echo $title ?> </b></h2>
                            </div>
                        </div>

                        <div class="panel-body">
                            <?php echo form_open("backoffice/Permission/updaterole/") ?>
                            <div class="form-group row">
                                <label for="type" class="col-sm-3 col-form-label"><?php echo "Role Name" ?> <i class="text-danger">*</i></label>
                                <div class="col-sm-6">
                                    <input type="text" value="<?php echo  $role['0']->type; ?>" tabindex="2" class="form-control" name="role_id" id="type" placeholder="Role Name" required />
                                </div>
                            </div>
                            <input type="hidden" name="rid" value="<?php echo $role['0']->id ?>">

                            <?php
                            $m = 0;
                            foreach ($module as $key => $value) {
                                $account_sub = $this->db->select('*')->from('sub_module')->where('mid', $value->id)->get()->result();

                            ?>
                                <table class="table table-bordered hidetable">
                                    <h2 class="hidetable"><?php echo $value->name; ?></h2>
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
                                        <?php
                                        ini_set('display_errors', 1);
                                        ini_set('display_startup_errors', 1);
                                        error_reporting(E_ALL);
                                        foreach ($account_sub as $key1 => $module_name) {
                                            $ck_data = $this->db->select('*')
                                                ->where('fk_module_id', $module_name->id)
                                                ->where('role_id', $role['0']->id)->get('role_permission')->row();
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
                                                            <input type="checkbox" class="create<?php echo $m ?>" name="create[<?php echo $m ?>][<?php echo $sl ?>][]" value="1" <?php echo ((@$ck_data->create == 1) ? "checked" : null) ?> id="create[<?php echo $m ?>]<?php echo $sl ?>">
                                                            <label for="create[<?php echo $m ?>]<?php echo $sl ?>"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="checkbox checkbox-success text-center" style="margin-left: 90px;">
                                                            <input type="checkbox" name="read[<?php echo $m ?>][<?php echo $sl ?>][]" class="read<?php echo $m ?>" value="1" <?php echo ((@$ck_data->read == 1) ? "checked" : null) ?> id="read[<?php echo $m ?>]<?php echo $sl ?>">
                                                            <label for="read[<?php echo $m ?>]<?php echo $sl ?>"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="checkbox checkbox-success text-center" style="margin-left: 90px;">
                                                            <input type="checkbox" name="update[<?php echo $m ?>][<?php echo $sl ?>][]" class="edit<?php echo $m ?>" value="1" <?php echo ((@$ck_data->update == 1) ? "checked" : null) ?> id="update[<?php echo $m ?>]<?php echo $sl ?>">
                                                            <label for="update[<?php echo $m ?>]<?php echo $sl ?>"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="checkbox checkbox-success text-center" style="margin-left: 90px;">
                                                            <input type="checkbox" name="delete[<?php echo $m ?>][<?php echo $sl ?>][]" class="delete<?php echo $m ?>" value="1" <?php echo ((@$ck_data->delete == 1) ? "checked" : null) ?> id="delete[<?php echo $m ?>]<?php echo $sl ?>">
                                                            <label for="delete[<?php echo $m ?>]<?php echo $sl ?>"></label>
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
                            } ?>

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