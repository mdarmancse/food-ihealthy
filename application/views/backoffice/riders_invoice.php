<style>
    .div-thead {
        color: #ffffff;
        font-size: 14px;
        background-color: #EA4D3C;
    }

    .div-thead-black {
        color: #ffffff;
        font-size: 14px;
        background-color: #000000;
    }


    table,
    th,
    td {

        border-collapse: collapse;
    }

    .segment-main {
        width: 100%;
        border: 2px solid #EA4D3C;
        font-size: 12px;
    }

    .div_1 {
        text-align: left;
        width: 5%;
        float: left;
        padding: 2px 0 2px 5px;
    }

    .div_11 {
        text-align: left;
        width: 25%;
        float: left;
        padding: 5px 0 5px 5px;
    }

    .div_2 {
        text-align: left;
        width: 35%;
        float: left;
        padding: 5px 0 5px 5px;
    }

    .div_3 {
        text-align: center;
        width: 10%;
        float: left;
        padding: 2px 0 2px 0
    }

    .div_4 {
        text-align: center;
        width: 10%;
        float: left;
        padding: 2px 0 2px 0
    }

    .div_5 {
        text-align: center;
        width: 10%;
        float: left;
        padding: 2px 0 2px 0
    }
</style>

<div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Rider's Invoice( Order ID: <?php echo $history[0]->entity_id; ?>)</h4>
        </div>
        <div class="modal-body">
            <div class="pdf_main">


                <!-- Header -->
                <div class="div-thead row">
                    <div>
                        <div class="div_1">#</div>
                        <div class="div_2"><?php echo $this->lang->line("item"); ?></div>

                        <div class="div_3"><?php echo $this->lang->line("price"); ?></div>
                        <div class="div_4"><?php echo $this->lang->line("qty"); ?></div>
                        <div class="div_5"><?php echo $this->lang->line("total"); ?></div>
                    </div>
                </div>
                <!-- body -->
                <div>
                    <?php $item_detail = unserialize($menu_item->item_detail);
                    if (!empty($item_detail)) {
                        $subtotal_sum = 0;
                        $Subtotal = 0;
                        $i = 0;
                        $addons_name_list = '';
                        $variation_name_list = '';
                        foreach ($item_detail as $key => $value) {
                            if ($value['is_customize'] == 1) {
                                if ($value['has_variation'] == 1) {
                                    $variation_name = '';
                                    foreach ($value['variation_list'] as $each_variation) {
                                        $variation_name .= $each_variation['variation_name'] . ', ';
                                        $Subtotal = $Subtotal + $each_variation['variation_price'];
                                        foreach ($each_variation['addons_category_list'] as $k => $val) {
                                            $addons_name = '';
                                            foreach ($val['addons_list'] as $m => $mn) {
                                                $addons_name .= $mn['add_ons_name'] . ', ';
                                                $Subtotal = $Subtotal + $mn['add_ons_price'];
                                            }
                                            $addons_name_list .= '<p>' . substr($addons_name, 0, -2) . '</p>';
                                        }
                                    }
                                    $variation_name_list .= '<p>' . substr($variation_name, 0, -2) . '</p>';
                                } else {
                                    foreach ($value['addons_category_list'] as $k => $val) {
                                        $addons_name = '';
                                        foreach ($val['addons_list'] as $m => $mn) {
                                            $addons_name .= $mn['add_ons_name'] . ', ';
                                            if ($value['is_deal'] != 1) {
                                                $Subtotal = $Subtotal + $mn['add_ons_price'];
                                                //$subtotal_sum = $subtotal_sum + $mn['add_ons_price'];
                                            }
                                        }
                                        if ($value['is_deal'] != 1) {
                                            $addons_name_list .= '<p><b>' . $val['addons_category'] . '</b>:' . substr($addons_name, 0, -2) . '</p>';
                                        } else {
                                            $addons_name_list .= '<p>' . substr($addons_name, 0, -2) . '</p>';
                                        }
                                    }
                                }
                                $subtotal_sum = $subtotal_sum + ($Subtotal * $value['qty_no']);
                            } else {
                                $price = ($value['offer_price']) ? $value['offer_price'] : $value['rate'];
                                $subtotal_sum = ($price * $value['qty_no']) + $subtotal_sum;
                            }

                            $i++; ?>
                            <div class="b0 row">
                                <div class="div_1"><?php echo $i ?></div>
                                <div class="div_2"><?php echo $value['item_name']; ?>
                                    <br>
                                    <?php echo ($variation_name_list ? $variation_name_list : '') ?>
                                    <?php echo $addons_name_list; ?>
                                </div>
                                <div class="center div_3"><?php echo $restaurant_detail->currency_symbol; ?><?php echo ($Subtotal) ? number_format_unchanged_precision($Subtotal, $restaurant_detail->currency_code) : number_format_unchanged_precision($price, $restaurant_detail->currency_code) ?></div>
                                <div class="center div_4"><?php echo $value['qty_no'] ?></div>
                                <div class="center div_5"><?php echo $restaurant_detail->currency_symbol; ?><?php echo ($Subtotal) ? number_format_unchanged_precision($Subtotal, $restaurant_detail->currency_code) : number_format_unchanged_precision($price * $value['qty_no'], $restaurant_detail->currency_code);
                                                                                                            $Subtotal = 0;
                                                                                                            $addons_name_list = '';
                                                                                                            $variation_name_list = ''; ?></div>
                            </div>

                    <?php }
                    } ?>
                </div>

                <br></br>
                <br></br>

                <div style="border-bottom: outset; border-color: #EA4D3C"></div>

                <br></br>

                <!-- Footer part for Price -->
                <table style="border: none;" width="45%" class="table-style">

                    <tr>
                        <td><strong>Payment Type: </strong></td>
                        <td><?php echo $history[0]->payment_option; ?>
                    </tr>
                    <br />
                    <tr>
                        <td><strong>Total Food Bill</strong></td>
                        <td><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($subtotal_sum, $restaurant_detail->currency_code) ?></td>
                    </tr>

                    <tr>
                        <td class="align-right">(+)SD</td>
                        <td class="align-left"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($history[0]->sd, $currency->currency_code); ?></td>
                    </tr>
                    <tr>
                        <td class="align-right">(+)VAT</td>
                        <td class="align-left"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($history[0]->vat, $currency->currency_code); ?></td>
                    </tr>
                    <?php $total_bill = $subtotal_sum + $history[0]->vat + $history[0]->sd;  ?>
                    <tr>
                        <td class="align-right grand-total"><strong>Subtotal</strong></td>
                        <td class="align-left grand-total"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($total_bill, $currency->currency_code); ?></td>
                    </tr>
                    <tr>
                        <td class="align-right grand-total">(-)Commission (<?php echo $history[0]->commission_rate; ?>%)</td>
                        <td class="align-left grand-total"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision(($history[0]->commission_value), $currency->currency_code); ?></td>
                    </tr>
                    <tr>
                        <td style="border-bottom: outset;border-color: #EA4D3C"></td>
                        <td style="border-bottom: outset;border-color: #EA4D3C"></td>
                    </tr>
                    <?php $restaurant_payable = $total_bill - ($history[0]->commission_value); ?>
                    <tr>
                        <td class="align-right grand-total"><strong>Restaurant Payable</strong></td>
                        <td class="align-left grand-total"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($restaurant_payable, $currency->currency_code); ?></td>
                    </tr>

                </table>

                <br></br>

                <div style="border-bottom: outset; border-color: #EA4D3C"></div>
                <h4>Customer Section</h4>
                <div style="border-bottom: outset; border-color: #EA4D3C"></div>
                <br></br>

                <!-- Customer Section -->
                <table style="border: none;" width="45%" class="table-style">
                    <tr>

                        <td><strong>Total Food Bill</strong></td>
                        <td><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($subtotal_sum, $restaurant_detail->currency_code) ?></td>
                    </tr>

                    <tr>
                        <td class="align-right">(+)SD</td>
                        <td class="align-left"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($history[0]->sd, $currency->currency_code); ?></td>
                    </tr>
                    <tr>
                        <td class="align-right">(+)VAT</td>
                        <td class="align-left"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($history[0]->vat, $currency->currency_code); ?></td>
                    </tr>
                    <tr>
                        <td class="align-right">Delivery Charge</td>
                        <td class="align-left"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($history[0]->delivery_charge, $currency->currency_code); ?></td>
                    </tr>
                    <?php $total_bill2 = $subtotal_sum + $history[0]->vat + $history[0]->sd + $history[0]->delivery_charge;  ?>
                    <tr>
                        <td class="align-right grand-total"><strong>Subtotal</strong></td>
                        <td class="align-left grand-total"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($total_bill2, $currency->currency_code); ?></td>
                    </tr>
                    <tr>
                        <td class="align-right grand-total">(-)Discount (<?php echo $history[0]->coupon_amount; ?>%)</td>
                        <td class="align-left grand-total"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($history[0]->coupon_discount, $currency->currency_code); ?></td>
                    </tr>
                    <tr>
                        <td style="border-bottom: outset;border-color: #EA4D3C"></td>
                        <td style="border-bottom: outset;border-color: #EA4D3C"></td>
                    </tr>
                    <?php $customer_payable = $total_bill2 - $history[0]->coupon_discount; ?>
                    <tr>
                        <td class="align-right grand-total"><strong>Customer Payable</strong></td>
                        <td class="align-left grand-total"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($customer_payable, $currency->currency_code); ?></td>
                    </tr>

                </table>

                <br></br>

                <div style="border-bottom: outset; border-color: #EA4D3C"></div>
                <h4>Riders Cash Section</h4>
                <div style="border-bottom: outset; border-color: #EA4D3C"></div>
                <br></br>

                <table style="border: none;" width="45%" class="table-style">
                    <tr>

                        <td>Customer Payable</td>
                        <td><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($customer_payable, $currency->currency_code) ?></td>
                    </tr>

                    <tr>
                        <td class="align-right">Restaurant Payable</td>
                        <td class="align-left"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($restaurant_payable, $currency->currency_code); ?></td>
                    </tr>

                    <tr>
                        <td style="border-bottom: outset;border-color: #EA4D3C"></td>
                        <td style="border-bottom: outset;border-color: #EA4D3C"></td>
                    </tr>
                    <?php $receivable = $customer_payable - $restaurant_payable; ?>
                    <tr>
                        <td class="align-right grand-total"><strong>Foodi payable / Receivable</strong></td>
                        <td class="align-left grand-total"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($receivable, $currency->currency_code); ?></td>
                    </tr>

                </table>

                <br></br>

                <div style="border-bottom: outset;border-color: #EA4D3C"></div>
                <h4>Rider's Earning Section</h4>
                <div style="border-bottom: outset;border-color: #EA4D3C"></div>
                <br></br>

                <table style="border: none;" width="45%" class="table-style">

                    <tr>
                        <td class="align-right grand-total">Foodi payable / Receivable</td>
                        <td class="align-left grand-total"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision(-$receivable, $currency->currency_code); ?></td>
                    </tr>
                    <tr>

                        <td>Per Ride Charge</td>
                        <td><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($vehicle_charge->price, $currency->currency_code) ?></td>
                    </tr>

                    <tr>
                        <td style="border-bottom: outset;border-color: #EA4D3C"></td>
                        <td style="border-bottom: outset;border-color: #EA4D3C"></td>
                    </tr>
                    <?php $rider_payable = -$receivable + $commission->OptionValue; ?>
                    <tr>
                        <td class="align-right grand-total"><strong>Foodi payable / Receivable</strong></td>
                        <td class="align-left grand-total"><?php echo $currency->currency_symbol; ?> <?php echo number_format_unchanged_precision($rider_payable + $vehicle_charge->price, $currency->currency_code); ?></td>
                    </tr>

                </table>
                <!-- Footer part for Price en$history[0]->commission_rated -->
            </div>
        </div>
    </div>
</div>