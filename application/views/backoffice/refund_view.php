<?php $this->load->view(ADMIN_URL . '/header');
?>

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
                        Refund Report

                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            Refund Report

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
                            <div class="caption"> Refund Report
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                                <?php if ($this->session->flashdata('page_MSG')) { ?>
                                    <div class="alert alert-success">
                                        <?php echo $this->session->flashdata('page_MSG'); ?>
                                    </div>
                                <?php } ?>
                                <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th class="table-checkbox">#</th>
                                            <th><?php echo "Order ID" ?></th>
                                            <th><?php echo "Customer Name" ?></th>
                                            <th><?php echo "Customer Mobile" ?></th>
                                            <th><?php echo "Order Total" ?></th>
                                            <th><?php echo "Transaction ID" ?></th>
                                            <th><?php echo "Payment ID" ?></th>
                                            <th><?php echo "Date" ?></th>

                                            <th><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>
                                            <td></td>
                                            <td></td>


                                            <td></td>
                                            <td></td>

                                            <td></td>
                                            <td></td>
                                            <td></td>

                                            <td>
                                                <button class="btn btn-sm red filter-submit"><i class="fa fa-search"></i> <?php echo $this->lang->line('search') ?></button>
                                                <button class="btn btn-sm red filter-cancel"><i class="fa fa-times"></i> <?php echo $this->lang->line('reset') ?></button>
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
<div id="add_status" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo "Refund Money" ?></h4>
            </div>
            <div class="modal-body">
                <form id="form_add_status" name="form_add_status" method="post" class="form-horizontal" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="control-label col-md-3"><?php echo "Refund Value" ?><span class="required">*</span></label>
                                <div class="col-md-4">
                                    <input type="text" name="total_rate" id="total_rate" value="" maxlength="249" data-required="1" class="form-control" />
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="entity_id" id="entity_id" value="">
                                <input type="hidden" name="transaction_id" id="transaction_id" value="">
                                <input type="hidden" name="payment_id" id="payment_id" value="">
                                <input type="hidden" name="order_id" id="order_id" value="">
                                <label class="control-label col-md-3"><?php echo "Refund Reason" ?><span class="required">*</span></label>
                                <div class="col-md-4">
                                    <input type="text" required name="reason" id="reason" value="" maxlength="249" data-required="1" class="form-control" />
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

<!-- check status modal -->
<div id="check_status" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><?php echo "Check Status" ?></h4>
            </div>
            <div class="modal-body">
                <form id="form_check_status" name="form_check_status" method="post" class="form-horizontal" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <th>Name</th>
                                    <th>Value</th>
                                </thead>
                                <tbody class="check_status_table">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- check status modal -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
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
        Layout.init(); // init current layout
        grid = new Datatable();
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
                "sAjaxSource": "ajaxrefundview", // ajax source
                "aaSorting": [
                    [4, "desc"]
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
    });

    $('#form_add_status').submit(function(e) {
        e.preventDefault();
        var transaction_id = $('#transaction_id').val();
        var total_rate = $('#total_rate').val();
        var payment_id = $('#payment_id').val();
        var order_id = $('#order_id').val();
        var reason = $('#reason').val();
        if (reason != '') {
            jQuery.ajax({
                type: "POST",
                // dataType: "application/json",
                url: '<?php echo base_url() ?>v1/Bkash/refund',
                data: {
                    'payment_id': payment_id,
                    'trxID': transaction_id,
                    'total': total_rate,
                    'reason': reason
                },
                success: function(response) {
                    var data = response;
                    status = data.status;
                    refundTrxID = data.msg.refundTrxID;
                    if (status == 1) {
                        jQuery.ajax({
                            type: "POST",
                            dataType: "html",
                            url: '<?php echo base_url() ?>backoffice/system_option/UpdaterefundStatus',
                            data: {
                                'order_id': order_id,
                                'refundTrxID': refundTrxID,
                            },
                            success: function(response) {
                                // console.log(response);
                                grid.getDataTable().fnDraw();
                                $('#add_status').modal('hide');
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert(errorThrown);
                            }
                        });
                    }

                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });
        }
        //return false;
    });
    // method for deleting
    function confirm_refund(transaction_id, total_rate, payment_id, order_id) {
        $('#transaction_id').val(transaction_id);
        $('#total_rate').val(total_rate);
        $('#payment_id').val(payment_id);
        $('#order_id').val(order_id);
        $('#add_status').modal('show');
    }

    function check_status(transaction_id, payment_id) {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: '<?php echo base_url() ?>v1/Bkash/CheckrefundStatus',
            data: {
                'payment_id': payment_id,
                'trx_id': transaction_id,
            },
            success: function(response) {
                var data = response.msg;
                var html = "";
                Object.keys(data).map((k, i) => {
                    html += "<tr><td>" + k + "</td><td>" + data[k] + "</td>";
                });

                $(".check_status_table").html(html);
                $('#check_status').modal('show');

            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        })
    }

    // function confirm_refund(transaction_id, total_rate, payment_id, order_id) {
    //     bootbox.confirm({
    //         message: "<?php echo "Do you want to refund money ?"; ?>",
    //         buttons: {
    //             confirm: {
    //                 label: '<?php echo $this->lang->line('ok'); ?>',
    //             },
    //             cancel: {
    //                 label: '<?php echo $this->lang->line('cancel'); ?>',
    //             }
    //         },
    //         callback: function(deleteConfirm) {
    //             if (deleteConfirm) {
    //                 jQuery.ajax({
    //                     type: "POST",
    //                     // dataType: "application/json",
    //                     url: '<?php echo base_url() ?>v1/Bkash/refund',
    //                     data: {
    //                         'payment_id': payment_id,
    //                         'trxID': transaction_id,
    //                         'total': 11
    //                     },
    //                     success: function(response) {
    //                         console.log(response);

    //                         var data = JSON.parse(response);
    //                         status = data.status;
    //                         refundTrxID = data.refundTrxID;
    //                         if (status == 1) {
    //                             jQuery.ajax({
    //                                 type: "POST",
    //                                 dataType: "html",
    //                                 url: '<?php echo base_url() ?>v1/Bkash/UpdaterefundStatus',
    //                                 data: {
    //                                     'order_id': order_id,
    //                                     'refundTrxID': refundTrxID,
    //                                 },
    //                                 success: function(response) {
    //                                     // console.log(response);
    //                                     grid.getDataTable().fnDraw();

    //                                 },
    //                                 error: function(XMLHttpRequest, textStatus, errorThrown) {
    //                                     alert(errorThrown);
    //                                 }
    //                             });
    //                         }

    //                     },
    //                     error: function(XMLHttpRequest, textStatus, errorThrown) {
    //                         alert(errorThrown);
    //                     }
    //                 });
    //             }
    //         }
    //     });
    // }
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>