<?php
$count = 0;
foreach ($rider_stats as $rd) {
    $count++;
?>

    <div class="col-sm-6">
        <div class="dashboard-stat blue-madison" style="height: 120px; border-radius: 5px !important;">
            <div class="visual">
                <i class="fa fa-list"></i>
            </div>
            <div class="details">
                <p class="number"><?= $rd['number'] ?></p>
                <h5 class="desc"><?= $rd['name'] ?></h5>
            </div>
        </div>
    </div>

<?php } ?>

<style scoped>
    table {
        /* table-layout: fixed; */
    }

    .table>thead>tr>th {
        white-space: normal !important;
    }
</style>
<div class="table-responsive">

    <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
        <thead>
            <tr role="row" class="heading">
                <th>#</th>
                <th><?php echo "Zone Name" ?></th>
                <th>Open Rider</th>
                <th>Rider with 1 Order</th>
                <th>Rider with 2 Orders</th>
                <th>Rider with 3 Orders</th>
                <th>Total Delivered Order(s)</th>
                <th>Avg DT</th>
                <th>Avg AR</th>
                <th>Total Cancelled Order(s)</th>
                <th>Avg CR</th>
                <th>Order Cancelled for Rider Issue</th>
                <th><?php echo $this->lang->line('action') ?></th>
            </tr>

        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/datatable.js"></script>
<script>
    var grid;
    jQuery(document).ready(function() {
        // Layout.init(); // init current layout
        grid = new Datatable();
        grid.addAjaxParam('city_id', $("#city_id").val());

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
                    {
                        "bSortable": true
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
                    }
                },
                "bServerSide": true, // server side processing
                "sAjaxSource": "ajaxview", // ajax source
                "aaSorting": [
                    [0, "desc"]
                ] // set first column as a default sort by asc
            }
        });
        $('#datatable_ajax_filter').addClass('hide');

    });
</script>