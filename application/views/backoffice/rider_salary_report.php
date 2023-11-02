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
                        <?php echo "Report Template"/*$this->lang->line('titleadmin_report_template')*/ ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo "Report Template"/*$this->lang->line('titleadmin_report_template')*/ ?>
                            <i class="fa fa-angle-right"></i>
                        </li>

                        <li>
                            <?php echo "Rider's Salary Report" ?>
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
                                <?php echo "Rider's Salary Report"/* $this->lang->line('titleadmin_report_template') ?> <?php echo $this->lang->line('list')*/ ?>
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
                                                <h4 align="center"><b><i>Rider's Salary Report</i></b></h4>
                                                <hr>
                                            </div>

                                            <div class="Data">

                                                <form method="get" action="<?php echo base_url() . ADMIN_URL ?>/Report_template/riderSalaryPDF" target="_blank">

                                                    <table class="formcontrols">
                                                        <div class="container">
                                                            <div class="row">
                                                                <div class="col-md-4" style="margin-left: 10px;">

                                                                    <td>
                                                                        <label> City Name: </label>
                                                                    </td>
                                                                    <td>
                                                                        <!-- <input type="" id="entity_id" name=""> -->
                                                                        <select name="city_id" id="city_id" onchange="getzone(this.id,this.value)" class="city" style="height: 30px;width: 140.59px">
                                                                            <option value="">Select</option>
                                                                            <?php
                                                                            foreach ($city_data as $city) {
                                                                            ?>

                                                                                <option value="<?php echo $city->id; ?>">
                                                                                    <?php echo $city->name; ?>
                                                                                </option>
                                                                            <?php
                                                                            }
                                                                            ?>
                                                                        </select>

                                                                    </td>

                                                                </div>
                                                                <div class="col-md-4">
                                                                    <td>
                                                                        <label> Zone Name: </label>
                                                                    </td>
                                                                    <td>
                                                                        <!-- <input type="" id="entity_id" name=""> -->
                                                                        <select name="zone_id" id="zone_id" class="zone_id" style="height: 30px;width: 190.59px">
                                                                            <option value="">Select</option>
                                                                            <?php
                                                                            foreach ($zone_data as $zone) {
                                                                            ?>
                                                                                <option value="<?php echo $zone->entity_id; ?>">
                                                                                    <?php echo $zone->area_name; ?>
                                                                                </option>
                                                                            <?php
                                                                            }
                                                                            ?>
                                                                        </select>

                                                                    </td>

                                                                </div>
                                                                <div class="col-md-4">

                                                                    <td>
                                                                        <label> Riders Name: </label>
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

                                                            </div>
                                                        </div>
                                                        <br />

                                                        <tr>
                                                            <td>

                                                            </td>

                                                        </tr>


                                                    </table>
                                                    <button type="submit" class=" btn btn-outline-success mr-1 mb-1 waves-effect waves-light">

                                                        <i class="fa fa-printer"></i> PDF
                                                    </button>
                                                </form>
                                                <br>


                                                <!-- <div class="table-responsive" style="overflow:auto; width:100%"> -->
                                                <table id="riderList" class="table table-bordered table-striped table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th>Sl</th>
                                                            <th>Bkash No.</th>
                                                            <th>Nagad No.</th>
                                                            <th>Name</th>
                                                            <th>Mobile</th>
                                                            <th>Rider ID</th>
                                                            <th>Total Attendance</th>
                                                            <th>Zone</th>
                                                            <th>Total Working Hours</th>
                                                            <th>Total Order</th>
                                                            <th>Vehicle</th>
                                                            <th>Vehicle Charge</th>
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
    //get items
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
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
        var element = $('#' + id).find('option:selected');
    }
    $('select').on('change', function() {
        // alert(this.value);
        var city_id = $('#city_id').val();
        var zone_id = $('#zone_id').val();
        jQuery.ajax({
            type: "POST",
            dataType: "html",
            url: '<?php echo base_url() . ADMIN_URL . '/' . "Report_template" ?>/getdrivers',
            data: {
                'city_id': city_id,
                'zone_id': zone_id
            },
            success: function(response) {
                $('#entity_id').empty().append(response);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });
</script>
<script>
    // function getPDF(entity_id, Tdate, Fdate, type) {
    //     $.ajax({
    //         type: "POST",
    //         dataType: "html",
    //         url: BASEURL + "backoffice/Report_template/getPDF",
    //         data: {
    //             'entity_id': entity_id,
    //             'ToDate': Tdate,
    //             'FromDate': Fdate,
    //             'type': type
    //         },
    //         cache: false,
    //         beforeSend: function() {
    //             $('#quotes-main-loader').show();
    //         },
    //         success: function(html) {
    //             $('#quotes-main-loader').hide();
    //             var WinPrint = window.open('<?php echo base_url() ?>' + html, '_blank', 'left=0,top=0,width=650,height=630,toolbar=0,status=0');
    //         }
    //     });
    // }
</script>

<script>
    $(document).ready(function() {

        var table = $('#riderList').DataTable({

            'responsive': false,

            'pageLength': 500,
            'lengthMenu': [50, 100, 500, 1000, 1500, 2000, 2500, 3000],

            "aaSorting": [
                [1, "desc"]
            ],
            "columnDefs": [{
                    "bSortable": true,
                    "aTargets": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
                },

            ],
            'processing': true,
            'serverSide': true,

            dom: 'Bflrtip',
            buttons: [{
                    extend: "excel",
                    footer: true,
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] //Your Colume value those you want excel
                    },
                    title: "Rider_List",
                    className: "btn-md prints"
                }



            ],

            'serverMethod': 'post',

            'ajax': {
                'url': "<?php echo base_url() ?>backoffice/Report_template/ajaxRiderSalary",
                "data": function(data) {

                    data.city_id = $('#city_id').val();
                    data.zone_id = $('#zone_id').val();
                    data.from_date = $('#from_date').val();
                    data.to_date = $('#to_date').val();
                    data.rider_id = $('#entity_id').val();

                    //data.todate = $('#to_date').val();
                }

            },

            'columns': [{
                    data: 'sl',
                    orderable: false
                },
                {
                    data: 'bkash_no',
                    orderable: false
                },
                {
                    data: 'nagad_no',
                    orderable: false
                },
                {
                    data: 'first_name',
                    orderable: false
                },
                {
                    data: 'mobile_number',
                    orderable: false
                },
                {
                    data: 'rider_id',
                    orderable: true
                },

                {
                    data: 'total_attendance',
                    orderable: false
                },
                {
                    data: 'zone',
                    orderable: false
                },
                {
                    data: 'total_working_hours',
                    orderable: false
                },
                {
                    data: 'total_order',
                    orderable: false
                },
                {
                    data: 'vehicle',
                    orderable: false
                },
                {
                    data: 'vehicle_price',
                    orderable: false
                },
                // {data:'restaurant_pay',orderable:false},
                // {data:'coupon_amount',orderable:false},
                // {data:'coupon_discount',orderable:false},



            ],


        });

        $('#entity_id, #from_date, #to_date, #city_id, #zone_id').change(function() {

            table.draw();

        });

    });
</script>

<?php $this->load->view(ADMIN_URL . '/footer'); ?>