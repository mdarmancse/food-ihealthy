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
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Order ID</th>
                    <th>Delivered Order(Date & Time)</th>
                    <th>Restaurant</th>
                    <th>Customer Address</th>
                    <th>Payment Type</th>

                    <th>Customer Pay</th>
                    <th>Restaurant Pay</th>

                    <th>Cash in Hand(Rider)</th>
                    <th>Rider Earnings</th>
                    <th>Rider Payable</th>

                </tr>

            </thead>

            <tbody>
                <?php
                // echo "<pre>";
                // print_r($report->result());
                // exit();
                foreach ($report->result() as $key => $row) {
                    //     $restaurant_pay=$row->subtotal+$row->vat+$row->sd-$row->commission_value;
                    //     $hand_cash=$row->total_rate-$restaurant_pay;
                    //     $rider_payable=$hand_cash-$row->delivery_charge;
                    // $restaurant_pay = $row->subtotal + $row->vat + $record->sd - $row->commission_value;
                    // $hand_cash = $row->total_rate - $restaurant_pay;
                    // // $rider_payable = $hand_cash - $record->delivery_charge;
                    // $res_collectable = $hand_cash - ($vehicle_rate[0]->price + $delivery_charge);
                    $totalFoodBill += $rider_payable;
                    $totalDeliveryCharge += $row->delivery_charge;
                    $totalHandCash += $hand_cash;
                    $totalRestoPay += $restaurant_pay;
                    $totalCusPay += $row->total_rate;
                    $driver_id = $row->driver_id;
                    $vehicle_rate = $this->report_model->get_rate($driver_id);
                    // echo "<pre>";print_r($data['report']->result());exit();
                    $restaurant_pay = $row->subtotal + $row->vat + $row->sd - $row->commission_value;
                    // echo "<pre>";
                    // print_r($restaurant_pay);
                    // exit();
                    $hand_cash = $row->total_rate - $restaurant_pay;
                    // $rider_payable = $hand_cash - $record->delivery_charge;
                    $rider_payable = $hand_cash - $vehicle_rate[0]->price;
                    $user_detail = unserialize($row->user_detail);
                    //name has to be same as in the database.
                    echo '<tr>
                                <td>' . ++$key . '</td>
                                <td>' . $row->first_name . '</td>
                                <td>' . $row->mobile_number . '</td>
                                <td>' . $row->e_id . '</td>
                                <td>' . date("d-m-Y H:i:s", strtotime($row->time)) . '</td>
                                <td>' . $row->name . '</td>
                                <td>' . $user_detail['address'] . ', ' . $user_detail['landmark'] . ', ' . $user_detail['zipcode'] . ', ' . $user_detail['city'] . '</td>
                                <td>' . $row->payment_option . '</td>
                                <td>' . $row->total_rate . '</td>
                                <td>' . $restaurant_pay . '</td>
                                <td>' . number_format((float)$hand_cash, 2, '.', '') . '</td>
                                <td>' . $vehicle_rate[0]->price . '</td>
                                <td>' . number_format((float)$rider_payable, 2, '.', '') . '</td>



                    </tr>';
                }
                ?>
            </tbody>
            <tfoot>
                <?php
                echo   '<tr>
                              <td colspan="8">' . 'Total' . '</td>
                              <td>' . $totalCusPay . '</td>
                              <td>' . $totalRestoPay . '</td>
                              <td>' . $totalHandCash . '</td>
                              <td>' . $totalDeliveryCharge . '</td>
                               
                              <td>' . number_format((float)$totalFoodBill, 2, '.', '') . '</td>
                             
                            </tr>';
                ?>
            </tfoot>

        </table>
    </div>
    <!-- </div> -->

    <!-- body -->
    <div>

    </div>


    <!-- Footer part for Price end -->