<?php $this->load->view(ADMIN_URL . '/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
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
                        Vehicle
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            Vehicle
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
                            <div class="caption">Vehicle</div>
                            <div class="actions c-dropdown">
                                <a class="btn danger-btn btn-sm" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/add_vehicle"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                                <?php if ($this->session->flashdata('page_MSG')) { ?>
                                    <div class="alert alert-success">
                                        <?php echo $this->session->flashdata('page_MSG'); ?>
                                    </div>
                                <?php } ?>
                                <table class="table table-striped table-bordered table-hover" id="">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th class="table-checkbox">#</th>
                                            <th><?php echo "Vehicle Name" ?></th>
                                            <th><?php echo "Per Ride Charge" ?></th>
                                            <th><?php echo "Status" ?></th>
                                            <th><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="page_title"></td>
                                            <td></td>
                                            <td>
                                                <select name="status" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <option value="1"><?php echo $this->lang->line('active') ?></option>
                                                    <option value="0"><?php echo $this->lang->line('inactive') ?></option>
                                                </select>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm red filter-submit"><i class="fa fa-search"></i> <?php echo $this->lang->line('search') ?></button>
                                                <button class="btn btn-sm red filter-cancel"><i class="fa fa-times"></i> <?php echo $this->lang->line('reset') ?></button>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (isset($vehicle) && !empty($vehicle)) { ?>
                                            <?php $sl = 1; ?>
                                            <?php foreach ($vehicle as $c) {
                                                $status = $c['status'];
                                                if ($status == 1) {
                                                    $value = 'Deactive';
                                                } else {
                                                    $value = 'Active';
                                                } ?>

                                                <tr>
                                                    <td><?php echo $sl++; ?></td>
                                                    <td><?php echo html_escape($c['name']); ?></td>
                                                    <td><?php echo html_escape($c['price']); ?></td>
                                                    <td> <?php if ($c['status'] == 1) {
                                                                echo "Active";
                                                            } else echo "Deactive"; ?>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-sm danger-btn margin-bottom" href="<?php echo base_url() . 'backoffice/zone/vehicle_editdata/' . $c['entity_id'] ?>"><i class="fa fa-edit"></i>Edit</a>
                                                        <a class="btn btn-sm danger-btn margin-bottom" href="<?php echo base_url() . 'backoffice/zone/delete_vehicle/' . $c['entity_id']; ?>"><i class="fa fa-trash"></i>Delete</a>
                                                        <a class="btn btn-sm danger-btn margin-bottom" href="<?php echo base_url() . 'backoffice/zone/vehicle_status/' . $c['entity_id'] . '/' . $c['status'] ?>"><i class="fa fa-edit"></i>
                                                            <?php echo $value; ?>
                                                        </a>

                                                    </td>

                                                </tr>
                                        <?php }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/datatable.js"></script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>