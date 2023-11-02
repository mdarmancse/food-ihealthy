<?php
$this->load->view(ADMIN_URL . '/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/css/datepicker.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/multiselect/sumoselect.min.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container" id="page-container">
    <input id="refresh_time" type="hidden" value="<?php echo $auto_refresh_time ?>">
    <!-- <meta http-equiv="refresh" id="mtlink" content=" <?php echo $auto_refresh_time ?>;URL='<?php echo base_url() . ADMIN_URL ?>/order/view'"> -->
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar');


    ?>

    <!-- END sidebar -->
    <!-- BEGIN CONTENT -->
    <div class=" page-content-wrapper">

        <div class="page-content">

            <input type="hidden" name="order_entity_id" id="order_entity_id" value="">
            <input type="hidden" name="user_type" id="user_type" value="<?php echo $this->session->userdata('UserType') ?>">
            <!-- BEGIN PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                        Dispatch Panel
                    </h3>

                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <a href="#">
                                Sub Dashboard </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            Dispatch Panel
                        </li>
                    </ul>
                    <!-- upadte cart -->
                    <div class="modal modal-main" id="update_cart">

                    </div>

                    <div class="modal modal-main" id="addOnsdetails">

                    </div>
                    <!-- upadte cart -->

                    <!-- END PAGE TITLE & BREADCRUMB-->
                    <div class="portlet box red">
                        <div class="portlet-title filter-portlet-caption" style="cursor: pointer;">
                            <div class="caption">Filters & Overview</div>
                            <div class="actions">
                                <div class="filter-caret"><span><i class="fa fa-caret-down"></i></span></div>
                                <input type="hidden" value="0" id="toggle-value">
                            </div>
                        </div>
                        <div class="portlet-body display-no filter-portlet-body">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th><?php echo "From Date" ?></th>
                                        <th><?php echo "End Date" ?></th>
                                        <th><?php echo "City" ?></th>
                                        <th><?php echo "Zone" ?></th>
                                        <!-- <th><?php echo $this->lang->line('action') ?></th> -->
                                    </tr>
                                    <tr role="row" class="filter">
                                        <td><input type="date" id="start_date" class="form-control start_date input-sm" name="start_date"></td>
                                        <td><input type="date" id="end_date" class="form-control end_date input-sm" name="end_date"></td>
                                        <td>
                                            <select onchange="getzone(this.id,this.value)" name=" city_id" id="filter_data" class="form-control city_id input-sm">
                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                <?php
                                                foreach ($city_data as $value) { ?>
                                                    <option value="<?php echo $value->id ?>"><?php echo $value->name ?></option>
                                                <?php  } ?>
                                            </select>
                                        </td>
                                        <td>
                                            <select name="zone_id[]" id="zone_id" multiple="" class="form-control zone_id input-sm">
                                                <?php
                                                foreach ($zone_data as $value) { ?>
                                                    <option value="<?php echo $value->entity_id ?>"><?php echo $value->area_name ?></option>
                                                <?php  } ?>
                                            </select>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <?php if ($this->lpermission->method('rider_overview', 'read')->access()) { ?>
                                <div class="row">
                                    <div class="col-sm-12 " id="main-data-div">
                                        <div class="col-sm-4">
                                            <div class="dashboard-stat blue-madison" onclick="show_driver_list(1)" style="background:#9fed85;height: 120px; border-radius: 5px !important;cursor:pointer" id="online_rider">
                                                <div class=" visual">
                                                    <i class="fa fa-list"></i>
                                                </div>
                                                <div class="details">
                                                    <h2 id="online" value="<?php echo $total_online_rider['online'] ?>"><?php echo $total_online_rider['online'] ?></h2>
                                                    <h5 class="desc" style="color:black">Total Online Riders</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="dashboard-stat blue-madison" onclick="show_driver_list(2)" style="background:#9fed85;height: 120px; border-radius: 5px !important;cursor:pointer">
                                                <div class="visual">
                                                    <i class="fa fa-list"></i>
                                                </div>
                                                <div class="details">
                                                    <h2 id="offline" value="<?php echo $total_online_rider['offline'] ?>"><?php echo $total_online_rider['offline'] ?></h2>
                                                    <h5 class="desc" style="color:black">Total Offline Riders</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-4">
                                            <div class="dashboard-stat blue-madison" onclick="show_driver_list(3)" style="background:#9fed85;height: 120px; border-radius: 5px !important;cursor:pointer">
                                                <div class="visual">
                                                    <i class="fa fa-list"></i>
                                                </div>
                                                <div class="details">
                                                    <h2 id="inactive" value="<?php echo $total_inactive_rider ?>"><?php echo $total_inactive_rider ?></h2>
                                                    <h5 class="desc" style="color:black">Total Inactive Riders</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <?php if ($this->lpermission->method('order_overview', 'read')->access()) { ?>
                                <div class="row margin-top-10">
                                    <div class="col-sm-12 " id="main-data-div">
                                        <div class="col-sm-3">
                                            <div class="dashboard-stat blue-madison" style="height: 120px; border-radius: 5px !important; cursor:pointer" id="unassigned_filter">
                                                <div class="visual">
                                                    <i class="fa fa-list"></i>
                                                </div>
                                                <div class="details">
                                                    <p class="number" id="unassigned"><?php echo ($unassigned_order ? $unassigned_order : 0) ?></p>
                                                    <h5 class="desc">Unassigned Orders</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="dashboard-stat blue-madison" style="height: 120px; border-radius: 5px !important;cursor:pointer" id="cancelled_filter">
                                                <div class=" visual">
                                                    <i class="fa fa-list"></i>
                                                </div>
                                                <div class="details">
                                                    <p class="number" id="cancel"><?php echo ($cancelled_order['total_cancelled_order'] ? $cancelled_order['total_cancelled_order'] : 0) ?></p>
                                                    <h5 class="desc">Cancelled Orders</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="dashboard-stat blue-madison" style="height: 120px; border-radius: 5px !important;cursor:pointer" id="delivered_filter">
                                                <div class=" visual">
                                                    <i class="fa fa-list"></i>
                                                </div>
                                                <div class="details">
                                                    <p class="number" id="delivered"><?php echo ($total_delivered['total_deliverd_order'] ? $total_delivered['total_deliverd_order'] : 0) ?></p>
                                                    <h5 class="desc">Total Delivered</h5>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <div class="dashboard-stat blue-madison" style="height: 120px; border-radius: 5px !important;cursor:pointer" id="accepted_filter">
                                                <div class=" visual">
                                                    <i class="fa fa-list"></i>
                                                </div>
                                                <div class="details">
                                                    <p class="number" id="accepted"><?php echo ($accepted_order['total_accepted_order'] ? $accepted_order['total_accepted_order'] : 0) ?></p>
                                                    <h5 class="desc">Accepted Orders</h5>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>


            <!-- END PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('order') ?> <?php echo $this->lang->line('list') ?></div>
                            <!-- <div class="actions">
                                <?php if ($this->lpermission->method('orders', 'create')->access()) { ?>
                                    <a class="btn danger-btn btn-sm" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name; ?>/add"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                                <?php } ?>

                                <?php if ($this->lpermission->method('orders', 'delete')->access()) { ?>
                                    <button class="btn danger-btn btn-sm" id="delete_order"><i class="fa fa-times"></i> <?php echo $this->lang->line('delete') ?></button>
                                <?php } ?>

                            </div> -->
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
                                <table class="table table-striped table-bordered table-hover order-table" id="datatable_ajax">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th class="table-checkbox"><input type="checkbox" class="group-checkable"></th>
                                            <th><?php echo $this->lang->line('order') ?>#</th>
                                            <th><?php echo $this->lang->line('restaurant') ?></th>
                                            <th><?php echo "Zone" ?></th>
                                            <th><?php echo $this->lang->line('user') ?></th>
                                            <th><?php echo $this->lang->line('order_total') ?></th>
                                            <th style=""><?php echo "Payable to Restaurant" ?></th>
                                            <th><?php echo "Payment Type" ?></th>
                                            <th><?php echo $this->lang->line('order_assign') ?></th>
                                            <th><?php echo $this->lang->line('order_status') ?></th>
                                            <th><?php echo $this->lang->line('order_date') ?></th>
                                            <th><?php echo $this->lang->line('order_type') ?></th>
                                            <th><?php echo $this->lang->line('status') ?></th>
                                            <th><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="order"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="restaurant"></td>
                                            <td></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="page_title"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="order_total"></td>
                                            <td></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="pay_type"></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="driver"></td>
                                            <td>
                                                <select name="order_status" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <?php $order_status = order_status($this->session->userdata('language_slug'));
                                                    foreach ($order_status as $key => $value) { ?>
                                                        <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                                    <?php  } ?>
                                                </select>
                                            </td>
                                            <td></td>
                                            <td><select name="order_delivery" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <option value="Delivery"><?php echo $this->lang->line('delivery') ?></option>
                                                    <option value="PickUp"><?php echo $this->lang->line('pickup') ?></option>
                                                </select>
                                            </td>
                                            <td></td>
                                            <td>
                                                <div class="margin-bottom-5">
                                                    <button class="btn btn-sm  danger-btn filter-submit margin-bottom"><i class="fa fa-search"></i> <?php echo $this->lang->line('search') ?></button>
                                                </div>
                                                <button class="btn btn-sm danger-btn filter-cancel"><i class="fa fa-times"></i> <?php echo $this->lang->line('reset') ?></button>
                                            </td>
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
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>

<!-- Modal -->
<div id="add_status" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('update_status') ?></h4>
            </div>
            <div class="modal-body">
                <form id="form_add_status" name="form_add_status" method="post" class="form-horizontal" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input type="hidden" name="entity_id" id="entity_id" value="">
                                <input type="hidden" name="user_id" id="user_id" value="">
                                <label class="control-label col-md-4"><?php echo $this->lang->line('status') ?><span class="required">*</span></label>
                                <div class="col-sm-8">
                                    <select name="order_status" id="order_status" class="form-control form-filter input-sm">
                                        <!-- <option value=""><?php echo $this->lang->line('select') ?></option>
                                        <?php $order_status = order_status($this->session->userdata('language_slug'));
                                        foreach ($order_status as $key => $value) { ?>
                                            <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                        <?php  } ?> -->
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions fluid">
                                <div class="col-md-12 text-center">
                                    <div id="loadingModal" class="loader-c display-no"><img src="<?php echo base_url() ?>assets/admin/img/loading-spinner-grey.gif" align="absmiddle"></div>
                                    <button type="submit" class="btn btn-sm  danger-btn filter-submit margin-bottom" name="submit_page" id="submit_page" value="Save"><span><?php echo $this->lang->line('save') ?></span></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--  -->
<div id="rider_invoice" class="modal fade" role="dialog">
</div>
<div id="no_driver_shown" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-center"><?php echo $this->lang->line('assign_driver') ?></h4>
            </div>
            <div class="modal-body text-center">
                <h2>Cannot assign right now. Check if any rider is available.</h2>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div id="assign_driver" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('assign_driver') . "      " ?><span id="assign_entity_id"></span></h4>


            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <!-- BEGIN EXAMPLE TABLE PORTLET-->
                        <div class="portlet box red">
                            <div class="portlet-title">
                                <div class="caption"><?php echo "Rider Track" ?> </div>
                            </div>
                            <div class="portlet-body">
                                <div class="table-container" id="track_order_content">

                                    <div class="track-order-main">
                                        <div class="track-order-map">
                                            <div class="row">
                                                <div class="col-md-12 modal_body_map">
                                                    <div class="location-map" id="location-map">
                                                        <div id="map_canvas"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- END EXAMPLE TABLE PORTLET-->
                    </div>
                </div>
                <!-- //Data Print for Driver -->
                <table class="table table-striped table-bordered table-hover" id="DriverList">
                    <thead>
                        <tr role="row" class="heading">
                            <th class="table-checkbox">#</th>
                            <th><?php echo "Rider Name" ?></th>
                            <th><?php echo "Driver to Restaurant" ?></th>
                            <th><?php echo "Restaurant to Customer" ?></th>
                            <th><?php echo "Mobile Number" ?></th>
                            <th><?php echo "Active Order" ?></th>
                            <th><?php echo "Assign Driver" ?></th>

                        </tr>

                    </thead>
                    <tbody id="test">





                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<div id="driver_list_status" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('view_comment') ?></h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr role="row" class="heading">
                            <th class="table-checkbox">#</th>
                            <th><?php echo "Rider Name" ?></th>

                            <th><?php echo "Mobile Number" ?></th>


                        </tr>

                    </thead>
                    <tbody id="show_drivers">





                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="view_comment" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo $this->lang->line('view_comment') ?></h4>
            </div>
            <div class="modal-body">
                <form id="form_view_comment" name="form_view_comment" method="post" class="form-horizontal" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label col-md-4"><?php echo $this->lang->line('comment') ?><span class="required">*</span></label>
                                <div class="col-sm-8">
                                    <textarea disabled class="form-control txt-extra-commment" name="extra_comment" id="extra_comment" rows="6" data-required="1"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="view_status_history" class="modal fade" role="dialog">
</div>
<div class="wait-loader display-no" id="quotes-main-loader"><img src="<?php echo base_url() ?>assets/admin/img/ajax-loader.gif" align="absmiddle"></div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/scripts/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-validation/js/jquery.validate.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script type="text/javascript" src="<?php echo base_url() ?>/assets/admin/plugins/uniform/jquery.uniform.min.js"></script>
<script type="text/javascript" src="<?php echo base_url() ?>/assets/admin/plugins/uniform/css/uniform.default.min.css"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/datatable.js"></script>
<script src="<?php echo base_url(); ?>assets/admin/plugins/multiselect/jquery.sumoselect.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="<?php echo base_url(); ?>assets/admin/pages/scripts/admin-management.js"></script>
<?php if ($this->session->userdata("language_slug") == 'ar') {  ?>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/admin/pages/scripts/localization/messages_ar.js"> </script>
<?php } ?>
<?php if ($this->session->userdata("language_slug") == 'fr') {  ?>
    <script type="text/javascript" src="<?php echo base_url() ?>assets/admin/pages/scripts/localization/messages_fr.js"> </script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= MAP_API_KEY ?>&libraries=places"></script>


<script>
    var grid;
    jQuery(document).ready(function() {


        $(".date-picker").datepicker({
            format: "dd-mm-yyyy",
            endDate: '+0d',
            /*startView: "months",
            minViewMode: "months",*/
            autoclose: true
        });
        Layout.init(); // init current layout
        grid = new Datatable();

        // $('select.zone_id').change(function(e) {
        //     grid.clearAjaxParams('zone_id');
        // });
        grid.init({
            src: $("#datatable_ajax"),
            onSuccess: function(grid) {
                // execute some code after table records loaded
            },
            onError: function(grid) {
                // execute some code on network or other general error
            },


            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options
                "sDom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "aoColumns": [{
                        "bSortable": false
                    },
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    {
                        "bSortable": false
                    }
                ],



                "aoColumnDefs": [{
                        "aTargets": [6],
                        "fnCreatedCell": function(nTd, sData, oData, iRow, iCol) {
                            $(nTd).css('white-space', 'normal !important')

                        }
                    },

                ],
                "sPaginationType": "bootstrap_full_number",
                "iDisplayLength": 20,
                // 'pageLength': 500,
                // 'lengthMenu': [50, 100, 500, 1000, 1500, 2000, 2500, 3000],
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
                    }
                },
                aLengthMenu: [
                    // set available records per page
                    [20, 25, 50, 100, 200, -1],
                    [20, 25, 50, 100, 200, "All"],
                ],
                // "fnRowCallback": function(nRow, aData) {
                //     if (aData[6] == 'Placed') {
                //         $('td:eq(6)', nRow).css('color', 'Red');
                //     }
                // },
                "fnRowCallback": function(nRow, aData) {
                    if (aData[6] == 'Pre-Order') {
                        $('td', nRow).css('background-color', '#C0E8D5');
                    }
                    if (aData[6] == 'Placed') {
                        $('td', nRow).css('background-color', '#F1E788');
                    }
                    if (aData[6] == 'Preparing') {
                        $('td', nRow).css('background-color', '#C5E17A');
                    }
                    if (aData[6] == 'On Going') {
                        $('td', nRow).css('background-color', '#ACACE6');
                    }
                    if (aData[6] == 'Cancel') {
                        $('td', nRow).css('background-color', '#FE6F5E');
                    }
                },
                "bServerSide": true, // server side processing
                "sAjaxSource": "ajaxview", // ajax source
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

                grid.addAjaxParam("is_unassigned", 0);


            }
        });
        $('input.start_date, input.end_date,select.zone_id,select.city_id').change(function(e) {
            grid.addAjaxParam($(this).attr("name"), $(this).val());
            grid.getDataTable().fnDraw();
        });
        $("#unassigned_filter").click(function(e) {
            grid.addAjaxParam("is_unassigned", 1);
            grid.addAjaxParam("is_accepted", 0);
            grid.addAjaxParam("is_delivered", 0);
            grid.addAjaxParam("is_cancelled", 0);
            grid.getDataTable().fnDraw();

        });
        $("#cancelled_filter").click(function(e) {
            grid.addAjaxParam("is_cancelled", 1);
            grid.addAjaxParam("is_delivered", 0);
            grid.addAjaxParam("is_accepted", 0);
            grid.addAjaxParam("is_unassigned", 0);
            grid.getDataTable().fnDraw();

        });
        $("#delivered_filter").click(function(e) {
            grid.addAjaxParam("is_delivered", 1);
            grid.addAjaxParam("is_cancelled", 0);
            grid.addAjaxParam("is_accepted", 0);
            grid.addAjaxParam("is_unassigned", 0);
            grid.getDataTable().fnDraw();

        });
        $("#accepted_filter").click(function(e) {
            grid.addAjaxParam("is_accepted", 1);
            grid.addAjaxParam("is_delivered", 0);
            grid.addAjaxParam("is_cancelled", 0);
            grid.addAjaxParam("is_unassigned", 0);
            grid.getDataTable().fnDraw();

        });

        let refresh_time = $('#refresh_time').val();
        setInterval(function() {


            // $('.start_date').val('');
            // $('.end_date').val('');
            // $('.city_id').val('');
            // grid.clearAjaxParams();
            <?php if ($this->lpermission->method('rider_list', 'read')->access()) { ?>

                filter_driver();
            <?php } ?>
            grid.getDataTable().fnDraw();



        }, refresh_time * 1000);

        $(".filter-portlet-caption").click(function() {
            tog_val = $("#toggle-value").val();
            $("#toggle-value").val(tog_val == 1 ? 0 : 1);
            $(".filter-portlet-body").slideToggle(500, function() {
                $(".filter-caret > span > i").removeClass(tog_val == 1 ? "fa-caret-up" : "fa-caret-down");
                $(".filter-caret > span > i").addClass(tog_val == 1 ? "fa-caret-down" : "fa-caret-up");
            });
        });

    });

    $('#zone_id').SumoSelect({
        search: true,
    });
    // update driver for a order
    //function FilterDriver() {
    <?php if ($this->lpermission->method('rider_list', 'read')->access()) { ?>
        jQuery('input.start_date, input.end_date,select.zone_id,select.city_id').on('change', function() {
            filter_driver();
        });

        function filter_driver() {
            var start_date = $('.start_date').val();
            var end_date = $('.end_date').val();
            var city_id = $('.city_id').val();
            var zone_id = $('.zone_id').val();
            //console.log(zone_id);
            $.ajax({
                type: "POST",
                dataType: "json",
                url: BASEURL + "backoffice/Order/FilterDriver",
                data: {
                    'start_date': start_date,
                    'end_date': end_date,
                    'city_id': city_id,
                    'zone_id': zone_id
                },
                success: function(html) {
                    var data = html;
                    var online_rider = data.total_online_rider;
                    var offline = data.offline;
                    var unassigned = data.unassigned_order;
                    var can = data.cancelled_order;
                    var del = data.total_delivered;
                    var acc = data.accepted_order
                    var delivered = del.total_deliverd_order;
                    var cancel = can.total_cancelled_order
                    var accepted = acc.total_accepted_order
                    var online = online_rider.online
                    var offline = online_rider.offline
                    var inactive = data.inactive_rider
                    // if (city_id || zone_id) {
                    document.getElementById("online").innerHTML = online;
                    document.getElementById("offline").innerHTML = offline;
                    document.getElementById("inactive").innerHTML = inactive
                    // }
                    // console.log(data);
                    document.getElementById("unassigned").innerHTML = unassigned;
                    document.getElementById("delivered").innerHTML = delivered;
                    document.getElementById("cancel").innerHTML = cancel;
                    document.getElementById("accepted").innerHTML = accepted;


                }
            });
        }
    <?php } ?>

    function getzone(id, entity_id) {
        //  console.log(entity_id);
        jQuery.ajax({
            type: "POST",
            dataType: "html",
            url: '<?php echo base_url() . ADMIN_URL . '/' . "Users" ?>/getzone',
            data: {
                'entity_id': entity_id,
            },
            success: function(response) {
                //alert(response);
                $('#zone_id').empty().append(response);
                $('#zone_id')[0].sumo.reload();

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
        var element = $('#' + id).find('option:selected');
    }

    function show_driver_list(status) {
        $(".dashboard-stat").css("pointer-events", "none");

        setTimeout(() => {
            $(".dashboard-stat").css("pointer-events", "auto");
        }, 2000);

        var status = status;
        var city_id = $('.city_id').val();
        var zone_id = $('.zone_id').val();
        $("#show_drivers").html("");
        $.ajax({
            type: "POST",
            dataType: "json",
            url: BASEURL + "backoffice/Order/GetDriverDetails",
            data: {
                'status': status,
                'city_id': city_id,
                'zone_id': zone_id
            },

            success: function(html) {

                var drivers = html.driver_information

                // console.log(drivers);
                for (var i = 0; i < drivers.length; i += 1) {
                    if (drivers[i]['last_name'] == null || drivers[i]['last_name'] == '') {
                        var last_name = ''
                    }
                    var data = `<tr>
                                    <td id='id'>` + (i + 1) + `</td>
                                    <td id='name'>` + drivers[i]['first_name'] + last_name + `</td>

                                    <td id='mobile'>` + drivers[i]['mobile_number'] + `</td>
                                    </tr>`;
                    $("#show_drivers").append(data);
                }
                $('#driver_list_status').modal('show');

            }
        });


    }
    // update driver for a order
    function updateDriver(entity_id) {
        document.getElementById('assign_entity_id').innerHTML = "Order #" + entity_id;
        $('#order_entity_id').val(entity_id);
        var order_id = $('#order_entity_id').val();


        if (order_id != '') {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: BASEURL + "backoffice/Order/GetDriver",
                // data: order_id,
                data: {
                    'order_id': order_id
                },
                success: function(html) {
                    $("#test").html("");
                    $("#driver_id").html("");

                    var latestOrder = html.latestOrder
                    console.log(latestOrder);
                    if (latestOrder[0]) {
                        initMap();

                        function initMap() {

                            map = new google.maps.Map(document.getElementById('map_canvas'), {
                                center: new google.maps.LatLng(parseFloat(20.055), parseFloat(20.968)),
                                // center: {
                                //     lat: parseFloat(latestOrder[Object.keys(latestOrder)[0]]['latitude']).toFixed(2),
                                //     lng: parseFloat(latestOrder[Object.keys(latestOrder)[0]]['longitude']).toFixed(2)
                                // },
                                zoom: 8




                            });



                            console.log(new google.maps.LatLng(parseFloat(20.055), parseFloat(20.968)));

                            console.log(map);
                            // map.fitBounds(bounds);


                            // var directionsService = new google.maps.DirectionsService;
                            var infowindow = new google.maps.InfoWindow();

                            var bounds = new google.maps.LatLngBounds();
                            var waypoints = Array();
                            //For User  Location
                            var position = {

                                lat: parseFloat(latestOrder[Object.keys(latestOrder)[0]]['user_latitude']),
                                lng: parseFloat(latestOrder[Object.keys(latestOrder)[0]]['user_longitude'])
                            };
                            var icon = '<?php echo base_url(); ?>' + 'assets/front/images/user-home.png';
                            marker = new google.maps.Marker({
                                position: position,
                                map: map,
                                animation: google.maps.Animation.DROP,
                                icon: icon
                            });
                            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                                return function() {
                                    infowindow.setContent("User To Restaurant Distance: " + latestOrder[0]['user_distance']);
                                    infowindow.open(map, marker);
                                }
                            })(marker));
                            bounds.extend(marker.position);
                            waypoints.push({
                                location: marker.position,
                                stopover: true
                            });

                            //Restaurant Location
                            var position = {
                                lat: parseFloat(latestOrder[Object.keys(latestOrder)[0]]['resLat']),
                                lng: parseFloat(latestOrder[Object.keys(latestOrder)[0]]['resLong'])
                            };
                            var icon = '<?php echo base_url(); ?>' + 'assets/front/images/restaurant.png';
                            marker = new google.maps.Marker({
                                position: position,
                                map: map,
                                animation: google.maps.Animation.DROP,
                                icon: icon
                            });
                            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                                return function() {
                                    infowindow.setContent(latestOrder[Object.keys(latestOrder)[0]]['restaurant_name'] + " " + latestOrder[Object.keys(latestOrder)[0]]['restaurant_address']);
                                    infowindow.open(map, marker);
                                }
                            })(marker));
                            bounds.extend(marker.position);
                            waypoints.push({
                                location: marker.position,
                                stopover: true
                            });


                            // driver location
                            for (var i = 0; i < latestOrder.length; i += 1) {
                                if (latestOrder[Object.keys(latestOrder)[i]]['flag'] == 1) {
                                    var data = `<tr>
                                    <td id='id'>` + (i + 1) + `</td>
                                    <td id='name'>` + latestOrder[Object.keys(latestOrder)[i]]['driver_fname'] + `</td>
                                    <td id='distance'>` + latestOrder[Object.keys(latestOrder)[i]]['distance'] + `</td>
                                    <td id='user_distance'>` + latestOrder[Object.keys(latestOrder)[0]]['user_distance'] + `</td>
                                    <td id='mobile'>` + latestOrder[Object.keys(latestOrder)[i]]['mobile_number'] + `</td>
                                    <td id='order_count'>` + latestOrder[Object.keys(latestOrder)[i]]['order_count'] + `</td>
                                     <td><input id='assign_driver' value='Assign' type='button' class='btn btn-primary' onclick="assign_driver_to_order('` + latestOrder[Object.keys(latestOrder)[i]]['user_id'] + `')"></td>

                                    </tr>`;
                                    // var driver = `<option value = "` + latestOrder[Object.keys(latestOrder)[i]]['user_id'] + `" >` + latestOrder[Object.keys(latestOrder)[i]]['driver_fname'] + `</option>`;

                                    // $("#driver_id").append(driver);
                                    $("#test").append(data);

                                    var driver_fname = latestOrder[Object.keys(latestOrder)[i]]['driver_fname'];
                                    var distance = latestOrder[Object.keys(latestOrder)[i]]['distance'];
                                    var position = {
                                        lat: parseFloat(latestOrder[Object.keys(latestOrder)[i]]['latitude']),
                                        lng: parseFloat(latestOrder[Object.keys(latestOrder)[i]]['longitude'])
                                    };
                                    var icon = '<?php echo base_url(); ?>' + 'assets/front/images/driver.png';
                                    marker = new google.maps.Marker({
                                        position: position,
                                        map: map,
                                        animation: google.maps.Animation.DROP,
                                        icon: icon
                                    });
                                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                                        return function() {

                                            infowindow.setContent(driver_fname + " " + "Driver to Restaurant Distance  " + distance);
                                            infowindow.open(map, marker);
                                        }
                                    })(marker));
                                    bounds.extend(marker.position);
                                    waypoints.push({
                                        location: marker.position,
                                        stopover: true
                                    });
                                }

                            }
                            map.fitBounds(bounds);




                            // $("#test").html("");
                        }
                        $('#assign_driver').modal('show');

                    } else {
                        $('#no_driver_shown').modal('show');
                    }

                    //return false;
                }
            });
        }

    }
    // submitting the assigning driver popup
    // $('#form_assign_driver').submit(function() {
    function assign_driver_to_order(driver_id) {
        if (!driver_id) {
            driver_id = $('#driver_id').val();
        }
        console.log(driver_id);
        // var driver_id = $('#driver_id').val();
        var entity_id = $('#order_entity_id').val();

        if (driver_id != '') {
            $.ajax({
                type: "POST",
                dataType: "html",
                url: BASEURL + "backoffice/order/assignDriver",
                // data: $('#form_assign_driver').serialize(),
                data: {
                    'driver_id': driver_id,
                    'order_entity_id': entity_id
                },
                cache: false,
                beforeSend: function() {
                    $('#quotes-main-loader').show();
                },
                success: function(html) {
                    if (html == "success") {
                        $('#quotes-main-loader').hide();
                        $('#assign_driver').modal('hide');
                        grid.getDataTable().fnDraw();
                    }
                    return false;
                }
            });
        }
        return false;
    }

    // method for deleting
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
                            'entity_id': entity_id
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
    // method for deleting
    function verify_order(order_id) {
        bootbox.confirm({
            message: "<?php echo "Do you want to verify this order?"; ?>",
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                },
                cancel: {
                    label: '<?php echo $this->lang->line('cancel'); ?>',
                }
            },
            callback: function(verify_order) {
                if (verify_order) {
                    jQuery.ajax({
                        type: "POST",
                        dataType: "html",
                        url: 'verify_Order',
                        data: {
                            'order_id': order_id
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

    // method for reject order
    function rejectOrder(user_id, restaurant_id, order_id, not_delievered = 0) {
        bootbox.confirm({
            message: "<?php echo $this->lang->line('reject_module'); ?>",
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                },
                cancel: {
                    label: '<?php echo $this->lang->line('cancel'); ?>',
                }
            },
            callback: function(rejectConfirm) {
                if (rejectConfirm) {
                    jQuery.ajax({
                        type: "POST",
                        dataType: "json",
                        url: 'ajaxReject',
                        data: {
                            'user_id': user_id,
                            'restaurant_id': restaurant_id,
                            'order_id': order_id,
                            'not_delivered': not_delievered,
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
    //Method for Change Delivered Status
    // method for reject order
    function not_delievered(user_id, restaurant_id, order_id, not_delievered) {
        bootbox.confirm({
            message: "<?php echo "Do you want to change the order status to not delivered?"; ?>",
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                },
                cancel: {
                    label: '<?php echo $this->lang->line('cancel'); ?>',
                }
            },
            callback: function(rejectConfirm) {
                if (rejectConfirm) {
                    jQuery.ajax({
                        type: "POST",
                        dataType: "json",
                        url: 'not_delievered',
                        data: {
                            'user_id': user_id,
                            'restaurant_id': restaurant_id,
                            'order_id': order_id,
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
    // method for Cancel order
    function cancel_order(user_id, restaurant_id, order_id, not_delievered) {
        bootbox.confirm({
            message: "<?php echo "Do you want to cancel this order?"; ?>",
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                },
                cancel: {
                    label: '<?php echo $this->lang->line('cancel'); ?>',
                }
            },
            callback: function(rejectConfirm) {
                if (rejectConfirm) {
                    jQuery.ajax({
                        type: "POST",
                        dataType: "json",
                        url: 'order_cancelled',
                        data: {
                            'user_id': user_id,
                            'restaurant_id': restaurant_id,
                            'order_id': order_id,
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
    //get invoice
    function getInvoice(entity_id) {
        $.ajax({
            type: "POST",
            dataType: "html",
            url: BASEURL + "backoffice/order/getInvoice",
            data: {
                'entity_id': entity_id
            },
            cache: false,
            beforeSend: function() {
                $('#quotes-main-loader').show();
            },
            success: function(html) {
                $('#quotes-main-loader').hide();
                var WinPrint = window.open('<?php echo base_url() ?>' + html, '_blank', 'left=0,top=0,width=650,height=630,toolbar=0,status=0');
            }
        });
    }
    //add status
    function updateStatus(entity_id, status, user_id) {
        $('#entity_id').val(entity_id);
        $('#user_id').val(user_id);
        UserType = $('#user_type').val();
        if ("<?= $this->lpermission->method('update_status_special', 'update')->access() ?>") {
            $('#order_status').empty().append(
                '<option value="" ><?php echo $this->lang->line('select'); ?></option><option value="preorder">Pre Order</option><option value="placed">Placed</option><option value="accepted_by_restaurant">Accepted</option><option value="preparing">Preparing -Accepted by Rider</option><option value="onGoing">On Going</option><option value="delivered">Delivered</option><option value="cancel">Cancel</option><option value="not_delivered">Not Delivered</option>'
            );
        }
        if (status == 'preparing') {
            $('#order_status').empty().append(
                '<option value="" ><?php echo $this->lang->line('select'); ?></option><option value="delivered">Delivered</option><option value="onGoing">On Going</option>'
            );
        }
        if (status == 'onGoing') {
            $('#order_status').empty().append(
                '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="delivered">Delivered</option>'
            );
        }
        if (status == 'placed' || status == 'preorder') {
            $('#order_status').empty().append(
                '<option value=""><?php echo $this->lang->line('select'); ?></option><option value="preparing" >Preparing</option><option value="delivered">Delivered</option><option value="onGoing">On Going</option><option value="cancel">Cancel</option>'
            );
        }
        if (status == "delivered" && !"<?= $this->lpermission->method('update_status_special', 'update')->access() ?>") {
            $('.update_status').attr('disabled', 'disabled');
        }

        $('#add_status').modal('show');
    }
    //view comment
    function viewComment(entity_id) {
        $.ajax({
            type: "POST",
            url: BASEURL + "backoffice/order/viewComment",
            data: {
                "entity_id": entity_id
            },
            beforeSend: function() {
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                $('#quotes-main-loader').hide();
                $('textarea#extra_comment').val(response);
                $('#view_comment').modal('show');
            }
        });
        return false;
    }
    $('#form_add_status').submit(function() {
        $.ajax({
            type: "POST",
            dataType: "html",
            url: BASEURL + "backoffice/order/updateOrderStatus",
            data: $('#form_add_status').serialize(),
            cache: false,
            beforeSend: function() {
                $('#quotes-main-loader').show();
            },
            success: function(html) {
                $('#quotes-main-loader').hide();
                $('#add_status').modal('hide');
                grid.getDataTable().fnDraw();
            }
        });
        return false;
    });
    //delete multiple
    $('#delete_order').click(function(e) {
        e.preventDefault();
        var records = grid.getSelectedRows();
        if (!jQuery.isEmptyObject(records)) {
            var CommissionIds = Array();
            var amount = '0.00';
            for (var i in records) {
                var val = records[i]["value"];
                CommissionIds.push(val);
            }
            var CommissionIdComma = CommissionIds.join(",");
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
                            url: 'deleteMultiOrder',
                            data: {
                                'arrayData': CommissionIdComma
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
        } else {
            bootbox.alert({
                message: "<?php echo $this->lang->line('checkbox'); ?>",
                buttons: {
                    ok: {
                        label: '<?php echo $this->lang->line('ok'); ?>',
                    }
                }
            });
        }
    });

    function statusHistory(order_id) {
        jQuery.ajax({
            type: "POST",
            url: 'statusHistory',
            data: {
                'order_id': order_id
            },
            cache: false,
            success: function(response) {
                $('#view_status_history').html(response);
                $('#view_status_history').modal('show');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
    // method for update status
    function disableDetail(entity_id, restaurant_id, order_id, order_status) {
        bootbox.confirm({
            message: "<?php echo $this->lang->line('accept_order'); ?>",
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
                        dataType: "json",
                        url: 'ajaxdisable',
                        data: {
                            'entity_id': entity_id,
                            'restaurant_id': restaurant_id,
                            'order_id': order_id,
                            'order_status': order_status
                        },
                        success: function(response) {
                            if (response == 0) {
                                bootbox.alert('Order already accepted');
                            }
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

    $('#assign_driver').on('hidden.bs.modal', function(e) {
        $(this).find("input[type=select]").val('').end();
        $('#form_assign_driver').validate().resetForm();
    });

    function riderInvoice(id) {

        jQuery.ajax({
            type: "POST",
            url: 'riderInvoice',
            data: {
                'order_id': id,
            },
            beforeSend: function() {
                $('#quotes-main-loader').show();
            },
            cache: false,
            success: function(response) {
                $('#quotes-main-loader').hide();
                $('#rider_invoice').html(response);
                $('#rider_invoice').modal('show');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }

    //Cart Update Related Functions
    //Update Cart
    function update_cart_item(entity_id, res_id) {
        $.ajax({
            type: "POST",
            dataType: "html",
            url: BASEURL + "backoffice/order/update_item",
            data: {
                'entity_id': entity_id,
                'restaurant_id': res_id
            },
            cache: false,
            success: function(html) {
                $('#update_cart').html(html);

                $('#update_cart').modal('show');
                // $("#item_id2").select2({
                //     dropdownParent: $('#update_cart .modal-content')
                // });
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }

    // function get_coupon() {
    //     var subtotal = $('#subtotal').val();
    //     var user_id = $('#user_id').val();
    //     var restaurant_id = $('#restaurant_id').val();
    //     $.ajax({
    //         type: "POST",
    //         dataType: "json",
    //         url: BASEURL + "backoffice/Order/get_coupon",
    //         data: {
    //             'restaurant_id': restaurant_id,
    //             'user_id': user_id,
    //             'subtotal': subtotal,
    //             'order_delivery': 'Delivery'
    //         },
    //         success: function(html) {
    //             console.log(html);
    //         }
    //     });


    // }
</script>


<!-- <script type="text/javascript">
    jQuery(document).ready(function() {
        initMap();

        function initMap() {
            map = new google.maps.Map(document.getElementById('map_canvas'), {
                center: {
                    lat: 20.055,
                    lng: 20.968
                },
                zoom: 2
            });
            var directionsService = new google.maps.DirectionsService;
            var infowindow = new google.maps.InfoWindow();
            //var directionsDisplay = new google.maps.DirectionsRenderer;
            var directionsDisplay = new google.maps.DirectionsRenderer({
                polylineOptions: {
                    strokeColor: "#FFB300"
                }
            });
            directionsDisplay.setOptions({
                suppressMarkers: true
            });
            directionsDisplay.setMap(map);

            var bounds = new google.maps.LatLngBounds();
            var waypoints = Array();


            if (latestOrder[0]['user_latitude'] && latestOrder[0]['user_latitude'])
                //users location
                // console.log(latestOrder);
                var position = {
                    lat: latestOrder[0]['user_longitude'],
                    lng: latestOrder[0]['user_latitude']
                };
            var icon = '<?php echo base_url(); ?>' + 'assets/front/images/user-home.png';
            marker = new google.maps.Marker({
                position: position,
                map: map,
                animation: google.maps.Animation.DROP,
                icon: icon
            });
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infowindow.setContent('<?php echo "User" . "<br>" . "Dhaka"; ?>');
                    infowindow.open(map, marker);
                }
            })(marker));
            bounds.extend(marker.position);
            waypoints.push({
                location: marker.position,
                stopover: true
            });


            map.fitBounds(bounds);
            var locationCount = waypoints.length;
            if (locationCount > 0) {
                var start = waypoints[0].location;
                var end = waypoints[locationCount - 1].location;
                directionsService.route({
                    origin: start,
                    destination: end,
                    waypoints: waypoints,
                    optimizeWaypoints: true,
                    travelMode: google.maps.TravelMode.DRIVING
                }, function(response, status) {
                    if (status === 'OK') {
                        directionsDisplay.setDirections(response);
                    } else {
                        window.alert('Problem in showing direction due to ' + status);
                    }
                });
            }
        }

        // var i = setInterval(function() {
        //     var order_id = '<?php echo $order_id; ?>';
        //     jQuery.ajax({
        //         type: "POST",
        //         dataType: "html",
        //         async: false,
        //         url: BASEURL + 'backoffice/order/ajax_track_order',
        //         data: {
        //             "order_id": order_id
        //         },
        //         success: function(response) {
        //             $('#track_order_content').html(response);
        //         },
        //         error: function(XMLHttpRequest, textStatus, errorThrown) {}
        //     });
        // }, 10000);

    });
</script> -->


<?php $this->load->view(ADMIN_URL . '/footer'); ?>