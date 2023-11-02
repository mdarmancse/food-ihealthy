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
                        <?php echo 'Rider'; ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo 'Rider'; ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE header-->

            <div class="row">
                <div class="col-md-12">
                    <br>
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box ">
                        <!-- <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('menu') ?></div>
                            <div class="actions c-dropdown">
                                <a class="btn danger-btn btn-sm" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name . '/add_menu/en' ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                            </div>
                        </div> -->
                        <?php if (isset($zone_id) && $zone_id) { ?>
                            <input type="hidden" name="zone_id" id="zone_id" value="<?= $zone_id ?>">
                        <?php } else { ?>
                            <div>
                                <div class="col-md-6">

                                    <div class="form-group">

                                        <label class="control-label col-md-3" style="margin-top: 20px;"><?php echo "City Name" ?></label>
                                        <div class="col-md-4">
                                            <br>
                                            <select class="form-control" name="city_id" id="city_id" onchange="getzone(this.id,this.value)">
                                                <option value="" selected onchange="getzone(this.id,this.value)"><?php echo $this->lang->line('select') ?></option>
                                                <?php foreach ($city_data as $value) { ?>
                                                    <option value=" <?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label col-md-3" style="margin-top: 20px;font-size:13px"><?php echo "Zone Name" ?> </label>
                                        <div class="col-md-4">
                                            <br>
                                            <select class="form-control" name="zone_id" id="zone_id">
                                                <option value="" selected><?php echo $this->lang->line('select') ?></option>
                                                <?php foreach ($zone_data as $value) { ?>
                                                    <option value="<?php echo $value->entity_id; ?>"><?php echo $value->area_name; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

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

                                <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th class="table-checkbox">#</th>
                                            <th><?php echo "Rider Name" ?></th>
                                            <th><?php echo "Phone Number" ?></th>
                                            <th><?php echo "Online Status" ?></th>
                                            <th><?php echo "Average Accept Rate" ?></th>
                                            <th><?php echo "Average Cancel Rate" ?></th>
                                            <th><?php echo "Avg. Delivery Time" ?></th>

                                            <th><?php echo "Total Delivered Orders" ?></th>
                                            <th><?php echo "Total Cancelled Orders" ?></th>
                                        </tr>


                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>

            <div class="modal modal-main" id="menu-branch-modal">

            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/scripts/dataTables.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/datatable.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/admin-management.js"></script>
<script>
    var grid;
    var table;
    jQuery(document).ready(function() {
        Layout.init(); // init current layout
        initDT();
    });

    function initDT() {
        var city_id = $("#city_id").val();
        // alert(city_id);
        var ajaxUri = '<?= base_url() . ADMIN_URL . '/' . $this->controller_name . "/ajaxviewRider" ?>';
        grid = new Datatable();
        <?php if (isset($zone_id) && $zone_id) { ?>
            grid.addAjaxParam('zone_id', $("#zone_id").val());
        <?php } ?>
        table = grid.init({
            src: $("#datatable_ajax"),
            onSuccess: function(grid) {
                // execute some code after table records loaded
            },
            onError: function(grid) {
                // execute some code on network or other general error
            },
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options
                "sDom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'Bf>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "aoColumns": [{
                        "bSortable": false
                    },
                    {
                        "bSortable": false
                    },
                    {
                        "bSortable": false
                    },
                    {
                        "bSortable": false
                    },
                    {
                        "bSortable": false
                    },
                    {
                        "bSortable": false
                    },
                    {
                        "bSortable": false
                    },
                    {
                        "bSortable": false
                    },
                    {
                        "bSortable": false
                    }
                ],
                "sPaginationType": "bootstrap_full_number",
                "oLanguage": {
                    "sProcessing": sProcessing,
                    "sLengthMenu": sLengthMenu,
                    "sInfo": sInfo,
                    "sInfoEmpty": sInfoEmpty,
                    "sGroupActions": sGroupActions,
                    "sAjaxRequestGeneralError": sAjaxRequestGeneralError,
                    "sEmptyTable": sEmptyTable,
                    "sZeroRecords": sZeroRecords,
                    "oPaginate": {
                        "sPrevious": sPrevious,
                        "sNext": sNext,
                        "sPage": sPage,
                        "sPageOf": sPageOf,
                        "sFirst": sFirst,
                        "sLast": sLast
                    },


                },
                "stateSave": true,
                "bServerSide": true, // server side processing
                "bProcessing": true,
                "sAjaxSource": ajaxUri, // ajax source
                "deferRender": true,
                buttons: [{
                    extend: "csvHtml5",
                    footer: false,
                    exportOptions: {
                        columns: [0, 1, 2, 4, 5, 6, 7, 8],

                        format: {
                            header: function(data, columnIdx) {
                                return columnIdx === 0 ? "SL" : data;
                            }
                        }
                    },
                    charset: "utf-16",
                    title: "Rider List",
                    className: "btn-md prints",

                }],
                "aaSorting": [
                    [1, "desc"]
                ] // set first column as a default sort by asc
            }
        });
        $('#datatable_ajax_filter').addClass('hide');
        $('input.form-filter, select.form-filter').keydown(function(e) {
            if (e.keyCode == 13) {
                grid.addAjaxParam($(this).attr("name"), $(this).val());
                grid.getDataTable().fnDraw();
            }
        });
        $('#zone_id,#city_id').change(function(e) {

            grid.addAjaxParam($(this).attr("name"), $(this).val());
            grid.getDataTable().fnDraw();


        });
    }

    function getzone(id, entity_id) {
        //  console.log(entity_id);
        jQuery.ajax({
            type: "POST",
            dataType: "html",
            url: '<?php echo base_url() . ADMIN_URL . '/' . 'Users' ?>/getzone',
            data: {
                'entity_id': entity_id,
            },
            success: function(response) {
                //alert(response);
                $('#zone_id').empty().append(response);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
        var element = $('#' + id).find('option:selected');
    }
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>