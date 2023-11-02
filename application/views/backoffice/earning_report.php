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
                        <?php echo "CRM Report"/*$this->lang->line('titleadmin_report_template')*/ ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo "CRM Report"/*$this->lang->line('titleadmin_report_template')*/ ?>
                            <i class="fa fa-angle-right"></i>
                            <?php echo "Earning Report" ?> </a>
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
                                <?php echo "REPORT"/* $this->lang->line('titleadmin_report_template') ?> <?php echo $this->lang->line('list')*/ ?>
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
                                                <h4 align="center"><b><i>Earning Report</i></b></h4>
                                                <hr>
                                            </div>

                                            <div class="Data">
                                                <form method="get" action="<?php echo base_url() . ADMIN_URL ?>/Report_template/allRiderPDF" target="_blank">

                                                    <table class="formcontrols">
                                                        <div class="container">
                                                            <div class="row">
                                                                <div class="col-md-4">

                                                                    <td>
                                                                        <label> User Name: </label>
                                                                    </td>
                                                                    <td>
                                                                        <!-- <input type="" id="entity_id" name=""> -->
                                                                        <select name="entity_id" id="entity_id" class="riders" style="height: 30px;width: 190.59px">
                                                                            <option value="">Select</option>
                                                                            <?php
                                                                            foreach ($groups->result() as $row) {
                                                                                if (isset($entity_id) && $row->entity_id == $entity_id) { ?>
                                                                                    <option value="<?php echo $row->entity_id; ?>" selected="selected"><?php echo $row->first_name . " " . $row->mobile_number; ?></option>
                                                                                <?php } else { ?>
                                                                                    <option value="<?php echo $row->entity_id; ?>"><?php echo $row->first_name . " " . $row->mobile_number; ?></option>
                                                                            <?php }
                                                                            }
                                                                            ?>
                                                                        </select>

                                                                    </td>

                                                                </div>
                                                                <div class="col-md-4">

                                                                    <td style="padding-left:55px;">
                                                                        <label> From Date: </label>
                                                                    </td>
                                                                    <td>
                                                                        <?php if (isset($fdate)) { ?>
                                                                            <input type="date" id="from_date" name="from_date" value="<?php echo $fdate ?>" placeholder="datetime" style="height: 30px">
                                                                        <?php } else { ?>
                                                                            <input type="date" id="from_date" name="from_date" value="" placeholder="datetime" style="height: 30px">



                                                                    </td>

                                                                </div>
                                                                <div class="col-md-4">

                                                                    <td style="padding-left:55px;">
                                                                        <label> To Date: </label>
                                                                    </td>
                                                                    <td>

                                                                    <?php } ?>
                                                                    <?php if (isset($fdate)) { ?>
                                                                        <input type="date" id="to_date" name="to_date" value="<?php echo $tdate ?>" placeholder="datetime" style="height: 30px">
                                                                    <?php } else { ?>
                                                                        <input type="date" id="to_date" name="to_date" value="" placeholder="datetime" style="height: 30px">
                                                                    <?php } ?>


                                                                    </td>

                                                                </div>

                                                            </div>
                                                        </div>



                                                    </table>
                                                    <button type="submit" class=" btn btn-outline-success mr-1 mb-1 waves-effect waves-light">

                                                        <i class="fa fa-printer"></i> PDF
                                                    </button>
                                                </form>
                                                <br>



                                                <!-- <div class="table-responsive" style="overflow:auto; width:100%"> -->
                                                <table id="earningList" class="table table-bordered table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Sl</th>
                                                            <th>User ID</th>
                                                            <th>User Name</th>
                                                            <th>Order ID</th>
                                                            <th>Order Total</th>
                                                            <th>Points</th>
                                                            <th>Date</th>
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
<script type="text/javascript">
    $(document).ready(function() {
        $('.riders').select2();
    });
</script>

<script>
    $(document).ready(function() {

        var table = $('#earningList').DataTable({

            'responsive': false,

            'pageLength': 200,
            'lengthMenu': [200, 250, 300, 350, 400, 450, 500, 600],

            "aaSorting": [

            ],
            "columnDefs": [{
                    "bSortable": true,
                    "aTargets": [0, 1, 2, 3]
                },

            ],
            'processing': true,
            'serverSide': true,

            dom: 'Bflrtip',
            buttons: [{
                    extend: "excel",
                    footer: true,
                    exportOptions: {
                        columns: [0, 1, 2, 3] //Your Colume value those you want excel
                    },
                    title: "Rider_List",
                    className: "btn-md prints"
                }



            ],

            'serverMethod': 'post',

            'ajax': {
                'url': "<?php echo base_url() ?>backoffice/System_option/Earning_report",
                "data": function(data) {
                    data.searchUser = $('#entity_id').val();
                    data.searchFromDate = $('#from_date').val();
                    data.searchToDate = $('#to_date').val();
                }

            },

            'columns': [{
                    data: 'sl',
                    orderable: false
                },
                {
                    data: 'user_id',
                    orderable: false
                },
                {
                    data: 'name',
                    orderable: false
                },
                {
                    data: 'order_id',
                    orderable: true
                },
                {
                    data: 'total_rate',
                    orderable: false
                },
                {
                    data: 'points',
                    orderable: false
                },
                {
                    data: 'date',
                    orderable: true
                },
            ],

            "footerCallback": function(row, data, start, end, display) {
                var api = this.api(),
                    data;

                // converting to interger to find total
                var intVal = function(i) {
                    return typeof i === 'string' ?
                        i.replace(/[\$,]/g, '') * 1 :
                        typeof i === 'number' ?
                        i : 0;
                };

                // computing column Total of the complete result

                var faTotal = api
                    .column(4)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);

                var dcTotal = api
                    .column(5)
                    .data()
                    .reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                // Update footer by showing the total with the reference of the column index
                $(api.column(0).footer()).html('Total');


                $(api.column(4).footer()).html(faTotal.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
                $(api.column(5).footer()).html(dcTotal.toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }));
            },



        });

        $('#entity_id,#from_date,#to_date').change(function() {
            table.draw();

        });

    });
</script>

<?php $this->load->view(ADMIN_URL . '/footer'); ?>