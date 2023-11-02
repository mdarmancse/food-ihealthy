<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<!-- <title>Rider Report</title> -->
<style type="text/css">
    body {
        font-family: Arial
    }

    .pdf_main {
        background: #fff;
        margin-left: 25px;
        margin-right: 25px;
    }

    th,
    td {
        border: 1px solid;
        padding-left: 5px;
        font-size: 12px;
    }

    .div-thead {
        color: #ffffff;
        font-size: 14px;
        background-color: #FFB300;
    }



    img {
        float: left;
        width: 5%;
        height: 5%
    }
</style>

<div class="head-main">
    <!-- <div class="logo"> <img src="<?php echo base_url(); ?>/assets/admin/img/logo.png" alt="" width="240" height="122"/> </div> -->

</div>


<!-- Header -->
<div class="pdf_main">

    <div style="text-align: right;font-size: 12px">

        <div><img style="margin-top: -15px; margin-left: 360px;" src="assets/admin/img/logo.png" alt=""></div>

    </div>
    <p style="text-align: center;"><?php echo strtoupper($title); ?><?php if (!empty($from_date && $to_date)) {
                                                                        echo ' From ' . date('d/m/Y', strtotime($from_date)) . " To " . date('d/m/Y', strtotime($to_date));
                                                                    } elseif (!empty($from_date)) {
                                                                        echo ' From ' . date('d/m/Y', strtotime($from_date));
                                                                    } elseif (!empty($to_date)) {
                                                                        echo " Till " . date('d/m/Y', strtotime($to_date));
                                                                    } ?>

    </p>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">

            <thead>
                <tr class="div-thead">
                    <th>Sl</th>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Bkash No.</th>
                    <th>Nagad No.</th>
                    <th>Total Attendance</th>
                    <th>Zone</th>
                    <th>Total Working Hours</th>
                    <th>Total Order</th>
                    <th>Vehicle</th>

                </tr>

            </thead>

            <tbody>
                <?php
                foreach ($report as $key => $row) {
                    echo '<tr>
                                <td>' . ++$key . '</td>
                                <td>' . $row['rider_id'] . '</td>
                                <td>' . $row['first_name'] . '</td>
                                <td>' . $row['mobile_number'] . '</td>
                                <td>' . $row['bkash_no'] . '</td>
                                <td>' . $row['nagad_no'] . '</td>
                                <td>' . $row['total_attendance'] . '</td>
                                <td>' . $row['zone'] . '</td>
                                <td>' . $row['total_working_hours'] . '</td>
                                <td>' . $row['total_order'] . '</td>
                                <td>' . $row['vehicle'] . '</td>



                    </tr>';
                }
                ?>
            </tbody>

        </table>
    </div>
    <!-- </div> -->

    <!-- body -->
    <div>

    </div>


    <!-- Footer part for Price end -->