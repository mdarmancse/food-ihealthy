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
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-bd lobidrag" style="border:3px solid #374767">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <h3><b><?php echo $title ?></b> </h3>
                            </div>
                        </div>

                        <div class="panel-body">

                            <div class="table-responsive">
                                <table id="" class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo "sl." ?></th>
                                            <th><?php echo "Role Name" ?></th>

                                            <th width="130"><?php echo "Action" ?></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($user_count > 0) {
                                            // echo '<pre>';
                                            // print_r($user_list);
                                            // exit();
                                            foreach ($user_list as $key => $row) {
                                        ?>
                                                <tr>
                                                    <td><?php echo ++$key; ?></td>
                                                    <td><?php echo $row['type']; ?></td>
                                                    <td>
                                                        <center>

                                                            <a href="<?php echo base_url() . 'backoffice/Permission/edit_role/' . $row['id']; ?>" class="btn btn-info btn-xs" data-toggle="tooltip" data-placement="left" title="<?php echo "Update" ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>


                                                            <a href="<?php echo base_url() . 'backoffice/Permission/role_delete/' . $row['id']; ?>" onClick="return confirm('Are You Sure to Want to Delete?')" class=" btn btn-danger btn-xs" name="pidd" data-toggle="tooltip" data-placement="right" title="" data-original-title="<?php echo "Delete" ?> "><i class="fa fa-trash-o" aria-hidden="true"></i></a>

                                                        </center>
                                                    </td>




                                                </tr>
                                            <?php
                                            }
                                        } else {
                                            ?>
                                            <tr>
                                                <td></td>
                                                <td><?php echo "data_not_found"; ?></td>
                                                <td></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>



                                    </tbody>

                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $this->load->view(ADMIN_URL . '/footer'); ?>