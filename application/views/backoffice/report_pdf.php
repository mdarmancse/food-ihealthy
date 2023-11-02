<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<style type="text/css">
    body {
        font-family: Arial
    }

    .pdf_main {
        background: #fff;
        margin-left: 25px;
        margin-right: 25px;
    }

    .head-main {
        float: left;
        width: 100%;
        margin-bottom: 30px;
    }

    .pdf_main .logo {
        float: left;
        padding-top: 24px;
        width: 30%;
    }

    .pdf_main .logo:hover {
        opacity: 1;
    }

    .pdf_main .head-right {
        float: right;
        width: 330px;
    }

    .pdf_main .quote-title {
        float: right;
        text-align: right;
        width: 100%;
        padding-bottom: 15px;
    }

    .pdf_main .col-li {
        float: left;
        display: inline-block;
        text-align: center;
        padding: 0 5px;
        font-size: 12px;
        font-weight: 700;
        width: 120px;
    }

    .pdf_main .col-li span {
        font-weight: 400;
    }

    .pdf_main .col-li .icon {
        display: block;
        padding-bottom: 5px;
    }

    .pdf_main .main-container {
        float: left;
        width: 100%;
    }

    .pdf_main .head-main h3 {
        text-align: right;
        margin-bottom: 20px;
        float: right;
    }

    .pdf_main .head-right li.last,
    .pdf_main .head-right li:last-child {
        padding-right: 0px;
    }

    .pdf_main .colm {
        float: left;
        padding: 0 4%;
        width: 40%;
    }

    .pdf_main .footer {
        background-color: #0076c0;
        float: left;
        text-align: center;
        display: block;
        padding: 12px 0 0px;
        box-sizing: border-box;
        margin-top: 30px;
        width: 650px;
    }

    .pdf_main table {
        border: 2px #bebcbc solid;
        border-collapse: collapse;
    }

    .pdf_main table tbody td {
        border: none !important;
    }

    .pdf_main table th {
        border: none !important;
    }

    .pdf_main .pdf_table {
        margin-bottom: 50px;
        margin-bottom: 30pt;
    }

    .pdf_main .pdf_table p {
        color: #000000;
        font-size: 11px;
        font-weight: 400;
        margin-bottom: 10px;
    }

    .pdf_main .pdf_table table td[colspan="3"] {
        padding-top: 24px;
    }

    .pdf_main .pdf_table thead th,
    .pdf_main .pdf_table tfoot td.grand-total,
    .div-thead {
        color: #ffffff;
        font-size: 14px;
        background-color: #ffb300;
    }

    .div-thead-black {
        color: #ffffff;
        font-size: 14px;
        background-color: #000000;
    }

    .pdf_main .pdf_table {
        margin-bottom: 50px;
        margin-bottom: 30pt;
    }

    .pdf_main .pdf_table p {
        color: #000000;
        font-size: 11px;
        font-weight: 400;
        margin-bottom: 10px;
    }

    .black-theme.pdf_main .pdf_table thead th {
        color: #ffffff;
        font-size: 16px;
        background-color: #000000;
        text-align: left;
    }

    .black-theme.pdf_main tfoot td.grand-total {
        color: #ffffff;
        background-color: #000000;
    }

    .black-theme.pdf_main .footer {
        background-color: #000000;
        border-bottom: 3px #000000 solid;
    }

    .black-theme.pdf_main .footer li {
        border-right: 0;
    }

    .black-theme.pdf_main table tbody td {
        border: none !important;
    }

    .black-theme.pdf_main table th {
        border: none !important;
    }

    .lenth-sec {

        margin-left: 5px;
    }

    .lenth-sec>label {
        font-weight: 400;
    }

    .lenth-sec {
        height: 31px;
        vertical-align: top;
    }

    tr,
    td,
    th {
        border: 1px solid #bebcbc;
    }

    /*.pdf_main {
	margin-left: 38px;
	margin-right: 38px
}*/
    .table-style tr td,
    .table-style tr td {
        padding-top: 4px;
        padding-bottom: 4px;
    }

    .table-style tr .border-line {
        padding-bottom: 7px;
    }

    .segment-main {
        width: 100%;
        border: 2px solid #bebcbc;
        font-size: 12px;
    }

    .div_1 {
        text-align: left;
        width: 15%;
        float: left;
        padding: 5px 0 5px 10px;
        color: white;
    }
</style>

<div class="head-main">
    <!-- <div class="logo"> <img src="<?php echo base_url(); ?>/assets/admin/img/logo.png" alt="" width="240" height="122"/> </div> -->

</div>


<!-- Header -->
<div class="pdf_main">

    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <?php if ($report) { ?>
                <thead>
                    <tr class="div-thead">
                        <th class="div_1">Serial No.</th>
                        <th class="div_1">Order Number</th>
                        <th class="div_1">User Name</th>
                        <th class="div_1">Delivery Address</th>
                        <th class="div_1">Delivery Date</th>
                        <th class="div_1">Item Name (Quantity)</th>
                        <th class="div_1">Food Price</th>
                        <th class="div_1">Delivery Charge</th>
                        <th class="div_1">Discount</th>
                        <th class="div_1">VAT</th>
                        <th class="div_1">SD</th>
                        <th class="div_1">Total</th>
                    </tr>
                </thead>


            <?php
            } else {
            ?>
                <thead>
                    <tr class="div-thead">
                        <th class="div_1">Serial No.</th>
                        <th class="div_1">Order ID</th>
                        <th class="div_1">Driver Earnings</th>
                        <th class="div_1">Commission</th>
                        <th class="div_1">Net Received</th>
                    </tr>
                </thead>
            <?php }
            if ($report) {
                $num = 1;
                foreach ($report->result() as $row) {

                    $user_detail = unserialize($row->user_detail);
                    $items = unserialize($row->item_detail);

                    foreach ($items as $key => $value) {
                        $product[] = "<li>" . $value['item_name'] . "(" .  $value['qty_no'] . ')';
                    }
                    $string = implode(" ", $product);
                    unset($product);

                    echo '
    <tr>
    <td>' . $num . '</td>
    <td>' . $row->entity_id . '</td>
    <td>' . $user_detail['first_name'] . ' ' . $user_detail['last_name'] . '</td>
    <td>' . $user_detail['address'] . '<br> ' . $user_detail['landmark'] . '<br>' . $user_detail['zipcode'] . '</td>
    <td>' . date('d/m/Y h:i:s A', strtotime($row->order_date)) . '</td>
    <td>' . $string . '</td>
    <td>' . $row->subtotal . '</td>
    <td>' . $row->delivery_charge . '</td>
    <td>' . $row->coupon_discount . '</td>
    <td>' . $row->vat . '</td>
    <td>' . $row->sd . '</td>
    <td>' . $row->total_rate . '</td>
    </tr>
    ';

                    $total_subtotal += $row->subtotal;
                    $delivery_charge += $row->delivery_charge;
                    $coupon_discount += $row->coupon_discount;
                    $vat += $row->vat;
                    $sd += $row->sd;
                    $total_rate += $row->total_rate;
                    $num++;
                }

                echo '
    <tr>

    <td><b>Total</b></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>' . $total_subtotal . '</td>
    <td>' . $delivery_charge . '</td>
    <td>' . $coupon_discount . '</td>
    <td>' . $vat . '</td>
    <td>' . $sd . '</td>
    <td>' . $total_rate . '</td>

    </tr>';
    
            }

            if ($rider) {
                foreach ($rider->result() as $row) {
                    $total = $row->total_rate - $row->commission;
                    echo '
<tr>
<td>' . $row->entity_id . '</td>
<td>' . $row->total_rate . '</td>
<td>' . $row->commission . '</td>

<td>' . $total . '</td>
</tr>
';

                    $total_rate += $row->total_rate;
                    $commission += $row->commission;
                    //$restaurent_payable+=$row->coupon_discount;
                    $totals += $total;
                }
                echo '
<tr>

<td><b>Total</b></td>
<td></td>
<td>' . $total_rate . '</td>
<td>' . $commission . '</td>

<td>' . $totals . '</td>

</tr>  ';
            }
            ?>
        </table>
    </div>
    <!-- </div> -->

    <!-- body -->
    <div>

    </div>


    <!-- Footer part for Price end -->