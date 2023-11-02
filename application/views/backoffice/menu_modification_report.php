<?php $this->load->view(ADMIN_URL . '/header'); ?>

<!-- BEGIN PAGE LEVEL STYLES -->
<link href="<?php echo base_url(); ?>assets/admin/css/dataTables.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/admin/css/select2.min.css" rel="stylesheet" type="text/css" />
<!-- <link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" /> -->
<style type="text/css">
    .btn {
        color: #eee;
        background-color: #FFB300;
        border-color: #cccccc;
        display: block;
    }

    td,
    th {
        padding: 3px;
    }
</style>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">

    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar'); ?>
    <!-- END sidebar -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                        <?php echo "Invalid Menus"/*$this->lang->line('titleadmin_report_template')*/ ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo "Invalid Menus"/*$this->lang->line('titleadmin_report_template')*/ ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption">
                                <?php echo "Invalid Menus"/* $this->lang->line('titleadmin_report_template') ?> <?php echo $this->lang->line('list')*/ ?>
                            </div>
                            <!--  -->

                        </div>
                        <div class="portlet-body">

                            <div class="table-container">

                                <?php
                                if ($this->session->flashdata('page_MSG')) { ?>
                                    <div class="alert alert-success">
                                        <?php echo $this->session->flashdata('page_MSG'); ?>
                                    </div>
                                <?php } ?>
                                <div id="delete-msg" class="alert alert-success hidden">
                                    <?php echo $this->lang->line('success_delete'); ?>
                                </div>
                                <div style="overflow-x: auto;">
                                    <table class="table table-striped table-bordered table-hover" id="datatable_ajax">

                                        <!-- avobe code is for page template -->

                                        <div class="container">

                                            <div class="wrapper">
                                                <h4 align="center"><b><i>Menu Modification Report</i></b></h4>
                                                <hr>
                                            </div>
                                            <div class="wrapper">
                                                <div style="width: 85%; display: flex; justify-content: space-between;">
                                                    <div style="display: flex;" class="form-group">
                                                        <label style="width: 163px;" class="control-label col-md-3"><?php echo $this->lang->line('res_name') ?></label>
                                                        <div class="col-md-8">
                                                            <select name="restaurant_id" class="form-control" id="restaurant_id">
                                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                                <?php if (!empty($restaurant_list)) {
                                                                    foreach ($restaurant_list as $key => $value) { ?>
                                                                        <option value="<?php echo $value->entity_id ?>"><?php echo $value->name ?></option>
                                                                <?php }
                                                                } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="Data">




                                                <!-- <div class="table-responsive" style="overflow:auto; width:100%"> -->
                                                <table id="modification_List" class="table table-bordered table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Sl</th>
                                                            <th>Menu Name</th>
                                                            <th>Restaurant Name</th>
                                                            <th>Edit </th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                    <tfoot align="right">
                                                        <tr>
                                                            <th class="text-right"></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                                <!-- </div> -->

                                            </div>
                                        </div>


                                    </table>
                                </div>
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
<script src="<?php echo base_url(); ?>assets/admin/scripts/select2.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/dataTables.min.js"></script>
<!-- <div class="wait-loader display-no" id="quotes-main-loader"><img src="<?php echo base_url() ?>assets/admin/img/ajax-loader.gif" align="absmiddle"></div> -->
<script>
    $(document).ready(function() {
        $("#restaurant_id").select2({

        });
        var table = $('#modification_List').DataTable({

            'responsive': false,

            'pageLength': 10,
            'lengthMenu': [10, 20, 50, 100, 500, 1000, 1500, 2000],

            "aaSorting": [
                [1, "desc"]
            ],
            "columnDefs": [{
                    "bSortable": true,
                    "aTargets": [0, 1, 2]
                },

            ],
            'processing': true,
            'serverSide': true,

            dom: 'Bflrtip',
            buttons: [{
                extend: "excel",
                footer: true,
                exportOptions: {
                    columns: [0, 1, 2] //Your Colume value those you want excel
                },
                title: "Rider_List",
                className: "btn-md prints"
            }],
            'serverMethod': 'post',

            'ajax': {
                'url': "<?php echo base_url() ?>backoffice/Report_template/show_menu_modified",
                "data": function(data) {
                    data.restaurant_id = $('#restaurant_id').val();
                }
            },
            'columns': [{
                    data: 'sl',
                    orderable: false
                },
                {
                    data: 'menu_name',
                    orderable: true
                },
                {
                    data: 'res_name',
                    orderable: true
                },

                {
                    data: 'edit',
                    orderable: false
                }
            ],

        });

        $('#restaurant_id').change(function() {
            table.draw();

        });

    });
</script>

<?php $this->load->view(ADMIN_URL . '/footer'); ?>