<style type="text/css">
    body {
        font-family: Arial
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

    .black-theme.pdf_main tfoot td.grand-total {
        color: #ffffff;
        background-color: #000000;
    }

    .modal-dialog {
        width: 750px !important;
        margin: 30px auto;
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

    /*new*/
    .fright {
        float: right;
    }

    .fleft {
        float: left;
    }

    .full-width100 {
        width: 100px;
    }

    .clr {
        clear: both;
        height: 10px
    }

    .div_1 {
        text-align: left;
        width: 5%;
        float: left;
        padding: 5px 0 5px 10px;
    }

    .div_2 {
        text-align: left;
        width: 17.5%;
        float: left;
        padding: 5px 0 5px 10px;
    }

    .div_3 {
        text-align: center;
        width: 15%;
        float: left;
        padding: 5px 0 5px 0
    }

    .div_4 {
        text-align: center;
        width: 15%;
        float: left;
        padding: 5px 0 5px 0
    }

    .div_5 {
        text-align: center;
        width: 20%;
        float: left;
        padding: 5px 0 5px 0
    }

    .b0 {
        border-bottom: 0
    }

    .width60 {
        width: 60%
    }

    .width15 {
        width: 15%
    }

    .width20 {
        width: 20%
    }
</style>
<?php
$form_action   = base_url() . ADMIN_URL . '/' . $this->controller_name . "/update_cart_data/" . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($menu_item->entity_id));
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h2 class="modal-title text-center"> Update Order #<?= $order_id ?></h2>
            <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
            <div class="pdf_main">
                <div class="segment-main">
                    <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN VALIDATION STATES-->
                            <div class="portlet box red">
                                <div class="portlet-title">
                                    <div class="caption"><?php echo $add_label; ?></div>
                                </div>
                                <div class="portlet-body form">
                                    <!-- BEGIN FORM-->
                                    <form action="<?php echo $form_action; ?>" id="form_add<?php echo $this->prefix ?>" name="form_add<?php echo $this->prefix ?>" method="post" class="form-horizontal" enctype="multipart/form-data">
                                        <div id="iframeloading" style="display: none;" class="frame-load">
                                            <img src="<?php echo base_url(); ?>assets/admin/img/loading-spinner-grey.gif" alt="loading" />
                                        </div>
                                        <div class="form-body">
                                            <?php if (!empty($Error)) { ?>
                                                <div class="alert alert-danger"><?php echo $Error; ?></div>
                                            <?php } ?>
                                            <?php if (validation_errors()) { ?>
                                                <div class="alert alert-danger">
                                                    <?php echo validation_errors(); ?>
                                                </div>
                                            <?php } ?>



                                            <div class="form-group">
                                                <?php $item_detail = unserialize($menu_item->item_detail);
                                                // echo "<pre>";
                                                // print_r($item_detail);
                                                // exit();
                                                if (!empty($item_detail)) {
                                                    $Subtotal = 0;
                                                    $i = 1;
                                                    $addons_name_list = '';
                                                    $variation_name_list = '';
                                                    $sl = 1;
                                                    foreach ($item_detail as $key => $value) {
                                                        $CI = &get_instance();
                                                        $CI->load->model('order_model');
                                                        $result = $CI->order_model->getVatSd($value['item_id']);
                                                        // echo "<pre>";
                                                        // print_r($result);
                                                        // exit();
                                                ?>

                                                        <div class="clone" id="cloneItem<?php echo $sl ?>">
                                                            <input type="hidden" value="<?php echo $sl ?>" id="last_cloned">
                                                            <input type="hidden" id="add_vat<?php echo $sl; ?>" value="<?php echo $result->vat ?>">
                                                            <input type="hidden" id="add_sd<?php echo $sl; ?>" value="<?php echo $result->sd ?>">
                                                            <input type="hidden" value="<?php echo $value['qty_no'] ?>" name="item_id[<?= $value['item_id'] ?>][quantity]">
                                                            <input type="hidden" value="<?php echo $value['rate'] ?>" name="item_id[<?= $value['item_id'] ?>][rate]">

                                                            <input type="hidden" value="<?php echo $value['item_name'] ?>" name="item_id[<?= $value['item_id'] ?>][menu_name]">
                                                            <input type="hidden" id="rate<?php echo $sl ?>" value="<?php echo $value['itemTotal'] ?>" name="item_id[<?= $value['item_id'] ?>][itemTotal]">
                                                            <input type="hidden" value="<?php echo $value['note'] ?>" name="item_id[<?= $value['item_id'] ?>][note]">
                                                            <input type="hidden" value="<?php echo $value['item_id'] ?>" name="item_id[<?= $value['item_id'] ?>][menu_id]">
                                                            <input type="hidden" value="<?php echo $value['is_deal'] ?>" name="item_id[<?= $value['item_id'] ?>][is_deal]">
                                                            <input type="hidden" value="<?php echo $value['is_customize'] ?>" name="item_id[<?= $value['item_id'] ?>][is_customize]">

                                                            <input type="hidden" value="<?php echo $value['offer_price'] ?>" name="item_id[<?= $value['item_id'] ?>][offer_price]">
                                                            <input type="hidden" id="order_id" value="<?php echo $value['order_id'] ?>" name="item_id[<?= $value['item_id'] ?>][order_id]">
                                                            <?php if ($value['is_customize'] == 1) { ?>
                                                                <input type="hidden" value="<?php echo $value['has_variation'] == 1 ? 1 : 0  ?>" name="item_id[<?= $value['item_id'] ?>][has_variation]">
                                                                <input type="hidden" value="" name="item_id[<?= $value['item_id'] ?>][variation_list]">


                                                                <?php if ($value['has_variation'] && $value['has_variation'] == 1) {
                                                                    $addons_list = 0; ?>

                                                                    <input type="hidden" id="has_it_variation" value="<?php echo $value['has_variation'] ?>">
                                                                    <?php $variation_name = '';
                                                                    $addons_category_list_sl = 0;
                                                                    $variation_sl = 0;
                                                                    foreach ($value['variation_list'] as $each_variation) {

                                                                    ?> <input type="hidden" value="<?php echo $each_variation['variation_id'] ?>" name="item_id[<?= $value['item_id'] ?>][variation_list][<?= $variation_sl; ?>][variation_id]">
                                                                        <input type="hidden" value="<?php echo $each_variation['variation_name'] ?>" name="item_id[<?= $value['item_id'] ?>][variation_list][<?= $variation_sl; ?>][variation_name]">
                                                                        <input type="hidden" value="<?php echo $each_variation['variation_price'] ?>" name="item_id[<?= $value['item_id'] ?>][variation_list][<?= $variation_sl; ?>][variation_price]">
                                                                        <?php $variation_name .= $each_variation['variation_name'] . ', ';
                                                                        $Subtotal = $Subtotal + $each_variation['variation_price'];
                                                                        $variation_s++;
                                                                        foreach ($each_variation['addons_category_list'] as $k => $val) {

                                                                        ?>

                                                                            <input type="hidden" value="<?php echo $val['addons_category_id'] ?>" name="item_id[<?= $value['item_id'] ?>][variation_list][<?= $variation_sl; ?>][addons_category_list][<?= $addons_category_list_sl; ?>][addons_category_id]">
                                                                            <input type="hidden" value="<?php echo $val['addons_category'] ?>" name="item_id[<?= $value['item_id'] ?>][variation_list][<?= $variation_sl; ?>][addons_category_list][<?= $addons_category_list_sl; ?>][addons_category]">
                                                                            <?php $addons_name = '';

                                                                            foreach ($val['addons_list'] as $m => $mn) {

                                                                            ?>
                                                                                <input type="hidden" value="<?php echo $mn['add_ons_id'] ?>" name="item_id[<?= $value['item_id'] ?>][variation_list][<?= $variation_sl; ?>][addons_category_list][<?= $addons_category_list_sl; ?>][addons_list][<?= $addons_list; ?>][add_ons_id]">
                                                                                <input type="hidden" value="<?php echo $mn['add_ons_name'] ?>" name="item_id[<?= $value['item_id'] ?>][variation_list][<?= $variation_sl; ?>][addons_category_list][<?= $addons_category_list_sl; ?>][addons_list][<?= $addons_list; ?>][add_ons_name]">
                                                                                <input type="hidden" value="<?php echo $mn['add_ons_price'] ?>" name="item_id[<?= $value['item_id'] ?>][variation_list][<?= $variation_sl; ?>][addons_category_list][<?= $addons_category_list_sl; ?>][addons_list][<?= $addons_list; ?>][add_ons_price]">
                                                                        <?php
                                                                                $addons_name .= $mn['add_ons_name'] . ', ';
                                                                                $Subtotal = $Subtotal + $mn['add_ons_price'];
                                                                                $addons_list++;
                                                                            }
                                                                            $addons_name_list .= '<p>' . substr($addons_name, 0, -2) . '</p>';
                                                                            $addons_category_list_sl++;
                                                                        }
                                                                    }
                                                                    $variation_name_list .= '<p>' . substr($variation_name, 0, -2) . '</p>';
                                                                } else {
                                                                    foreach ($value['addons_category_list'] as $k => $val) { ?>
                                                                        <input type="hidden" value="<?php echo $val['addons_category_id'] ?>" name="item_id[<?= $value['item_id'] ?>][addons_category_list][<?= $val['addons_category_id'] ?>][addons_category_id]">
                                                                        <input type="hidden" value="<?php echo $val['addons_category'] ?>" name="item_id[<?= $value['item_id'] ?>][addons_category_list][<?= $val['addons_category_id']; ?>][addons_category]">

                                                                        <?php $addons_name = '';
                                                                        foreach ($val['addons_list'] as $m => $mn) { ?>
                                                                            <input type="hidden" value="<?php echo $mn['add_ons_id'] ?>" name="item_id[<?= $value['item_id'] ?>][addons_category_list][<?= $val['addons_category_id'] ?>][addons_list][<?= $mn['add_ons_id'] ?>][add_ons_id]">
                                                                            <input type="hidden" value="<?php echo $mn['add_ons_name'] ?>" name="item_id[<?= $value['item_id'] ?>][addons_category_list][<?= $val['addons_category_id'] ?>][addons_list][<?= $mn['add_ons_id'] ?>][add_ons_name]">
                                                                            <input type="hidden" value="<?php echo $mn['add_ons_price'] ?>" name="item_id[<?= $value['item_id'] ?>][addons_category_list][<?= $val['addons_category_id'] ?>][addons_list][<?= $mn['add_ons_id'] ?>][add_ons_price]">
                                                            <?php $addons_name .= $mn['add_ons_name'] . ', ';
                                                                            if ($value['is_deal'] != 1) {
                                                                                $Subtotal = $Subtotal + $mn['add_ons_price'];
                                                                            }
                                                                        }
                                                                        if ($value['is_deal'] != 1) {
                                                                            $addons_name_list .= '<p><b>' . $val['addons_category'] . '</b>:' . substr($addons_name, 0, -2) . '</p>';
                                                                        } else {
                                                                            $addons_name_list .= '<p>' . substr($addons_name, 0, -2) . '</p>';
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                            $price = ($value['rate'] ? $value['rate'] : ($value['itemTotal'] / $value['qty_no'])); ?>
                                                            <div class="b0">
                                                                <div class="center div_1">
                                                                    <h5><?php echo $i ?>.</h5>
                                                                </div>
                                                                <div class="col-md-3"><?php echo $value['item_name']; ?>
                                                                    <br>
                                                                    <?php echo ($variation_name_list ? $variation_name_list : '') ?>
                                                                    <?php echo ($addons_name_list ? $addons_name_list : ''); ?>
                                                                </div>


                                                                <div class="center col-md-2"><?php echo $restaurant_detail->currency_symbol; ?><?php echo ($price) ? number_format_unchanged_precision($price) : number_format_unchanged_precision($price) ?></div>
                                                                <div class="center col-md-2"><?php echo $value['qty_no'] ?></div>
                                                                <div class="center div_5"><?php echo $restaurant_detail->currency_symbol; ?>
                                                                    <?php echo ($price)
                                                                        ? number_format_unchanged_precision($price * $value['qty_no'], $restaurant_detail->currency_code)
                                                                        : number_format_unchanged_precision($price * $value['qty_no'], $restaurant_detail->currency_code);
                                                                    $Subtotal = 0;
                                                                    $addons_name_list = '';
                                                                    $i = $i + 1;
                                                                    ?></div>

                                                            </div>

                                                            <!-- <div class="item-delete" onclick="deleteItem(<?php echo $sl; ?>)"><i class="fa fa-remove"></i></div> -->
                                                            <div class="col-md-1 remove">
                                                                <div class="item-delete" onclick="deleteItem(<?php echo $sl ?>)"><i class="fa fa-remove"></i></div>
                                                            </div>
                                                        </div>
                                                <?php
                                                        $sl++;
                                                    }
                                                } ?>

                                                <?php for ($i = 1, $inc = $sl; $i <= count($menu_item); $inc++, $i++) { ?>
                                                    <input type="hidden" value="<?php echo $inc ?>" id="last_cloned">
                                                    <div class="clone" id="cloneItem<?php echo $inc ?>">
                                                        <label class="control-label col-md-2 clone-label"><?php echo $this->lang->line('menu_item') ?><span class="required">*</span></label>
                                                        <div class="col-md-4">
                                                            <select class="form-control item_id validate-class" id="item_id<?php echo $inc ?>" onchange="getItemPrices(this.id,<?php echo $inc ?>)" style="width: 100%">
                                                                <option value=""><?php echo $this->lang->line('select') ?></option>
                                                                <?php
                                                                if (!empty($menu)) {
                                                                    foreach ($menu as $key => $value) { ?>
                                                                        <option data-id-name="<?php echo $value->name ?>" value="<?php echo $value->entity_id ?>" data-id="<?php echo $value->price ?>" data-id-addons="<?php echo $value->check_add_ons ?>"><?php echo $value->name ?></option>

                                                                <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <!-- addon  -->
                                                        <input type="hidden" id="order_id" value="<?php echo $menu_item->order_id ?>">
                                                        <input type="hidden" id="sl_no" value="<?php echo $inc ?>">


                                                        <!-- Update cart related  -->
                                                        <input type="hidden" id="item_id_<?php echo $inc ?>" value="" name="">
                                                        <input type="hidden" id="item_rate_<?php echo $inc ?>" value="" name="">
                                                        <input type="hidden" id="menu_name_<?php echo $inc ?>" value="" name="">
                                                        <input type="hidden" id="qnty_<?php echo $inc ?>" value="" name="">
                                                        <input type="hidden" id="rate_<?php echo $inc ?>" value="" name="">
                                                        <input type="hidden" id="customize_<?php echo $inc ?>" value="" name="">
                                                        <input type="hidden" id="is_deal_<?php echo $inc ?>" value="" name="">
                                                        <input type="hidden" id="note_<?php echo $inc ?>" value="" name="">
                                                        <input type="hidden" id="add_vat<?php echo $inc ?>" value="">
                                                        <input type="hidden" id="offer_price_<?php echo $inc ?>" value="" name="">
                                                        <input type="hidden" id="order_id_<?php echo $inc ?>" value="" name="">

                                                        <!-- Related to Variation -->
                                                        <input type="hidden" id="has_variation_<?php echo $inc ?>" value="" name="">
                                                        <input type="hidden" id="has_variation_id_<?php echo $inc ?>" value="" name="">
                                                        <input type="hidden" id="has_variation_name<?php echo $inc ?>" value="" name="">
                                                        <input type="hidden" id="has_variation_price<?php echo $inc ?>" value="" name="">


                                                        <!-- Update cart related data -->
                                                        <div class="col-md-2">
                                                            <input type="text" id="qty_no<?php echo $inc ?>" value="<?php echo isset($_POST['qty_no'][$i]) ? $_POST['qty_no'][$i] : '' ?>" maxlength="3" data-required="1" onkeyup="qty(this.id,<?php echo $inc ?>)" class="form-control qty validate-class" placeholder="<?php echo $this->lang->line('qty_no') ?>" />
                                                        </div>


                                                        <div class="col-md-2">
                                                            <input type="text" placeholder="<?php echo $this->lang->line('item_rate') ?>" id="rate<?php echo $inc ?>" value="<?php echo isset($_POST['rate'][$i]) ? $_POST['rate'][$i] : '' ?>" maxlength="20" data-required="1" class="form-control rate validate-class" readonly="" />
                                                        </div>
                                                        <div class="modal modal-main" id="addOnsdetails<?php echo $inc ?>">

                                                        </div>

                                                        <div class="col-md-1 remove"><?php if ($inc > $sl) { ?><div class="item-delete" onclick="deleteItem(<?php echo $inc ?>)"><i class="fa fa-remove"></i></div><?php } ?></div>
                                                    </div>

                                                <?php } ?>
                                                <div id="Optionplus" onclick="cloneItem()">
                                                    <div class="item-plus"><img src="<?php echo base_url(); ?>assets/admin/img/plus-round-icon.png" alt="" /></div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php echo $this->lang->line('sub_total') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                                <div class="col-md-4">
                                                    <input type="text" name="subtotal" id="subtotal" value="<?php echo ($order_details[0]->subtotal) ? $order_details[0]->subtotal : 0; ?>" maxlength="10" data-required="1" class="form-control" readonly="" />
                                                </div>
                                            </div>
                                            <input type="hidden" name="actual_coupon_type" id="actual_coupon_type" />
                                            <input type="hidden" name="coupon_name" id="coupon_name" />

                                            <input type="hidden" name="coupon_real_id" id="coupon_real_id" />
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php echo $this->lang->line('title_admin_coupon') ?></label>
                                                <div class="col-md-4">
                                                    <select name="coupon_id" class="form-control coupon_id" id="coupon_id" onchange="couponApplied(this)">
                                                        <option value=""><?php echo $this->lang->line('select') ?></option>

                                                    </select>
                                                </div>
                                                <span id="applied_coupon" style="display: none;"></span>
                                            </div>

                                            <div class="form-group" id="coupon_applied_msg_div" style="display: none;">
                                                <div class="col-md-3"></div>
                                                <div class="col-md-3" style="border: 1px solid black; border-radius: 5px; padding: 5px; margin: 0 5px;">
                                                    <h5 class="coupon_applied_msg"></h5>
                                                </div>
                                            </div>

                                            <input type="hidden" name="coupon_amount" id="coupon_amount" />
                                            <input type="hidden" name="item_coupon_discount" id="item_coupon_discount" value="0" />
                                            <input type="hidden" name="list_coupon_discount" id="list_coupon_discount" value="0" />
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php echo $this->lang->line('coupon_discount') ?></label>
                                                <div class="col-md-4">
                                                    <input type="text" data-value="" name="coupon_discount" id="coupon_discount" maxlength="10" data-required="1" class="form-control" readonly="" onchange="calculation()" /><label class="coupon-type"></label>
                                                    <input type="hidden" name="coupon_type" id="coupon_type" value="">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3">Vat <span class="currency-symbol"></span></label>
                                                <div class="col-md-4">
                                                    <input type="text" name="vat" id="vat" value="0" maxlength="10" data-required="1" class="form-control" readonly="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3">SD<span class="currency-symbol"></span></label>
                                                <div class="col-md-4">
                                                    <input type="text" name="sd" id="sd" value="0" maxlength="10" data-required="1" class="form-control" readonly="" />
                                                </div>
                                            </div>
                                            <input type="hidden" id="api_string" value="">
                                            <input type="hidden" id="restaurant_id" name="restaurant_id" value="<?php echo $order_details[0]->restaurant_id; ?>">
                                            <input type="hidden" id="user_id" name="user_id" value="<?php echo $order_details[0]->user_id; ?>">
                                            <input type="hidden" name="actual_coupon_type" id="actual_coupon_type" />
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php echo $this->lang->line('delivery_charge') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                                <div class="col-md-4">
                                                    <input type="text" name="deliveryCharge" id="deliveryCharge" value="<?php echo $order_details[0]->delivery_charge; ?>" maxlength="10" data-required="1" class="form-control" readonly="" />
                                                </div>
                                            </div>


                                            <input type="hidden" name="zone_id" id="zone_id" value=<?= $order_details[0]->zone_id ?> />

                                            <input type="hidden" name="selected_user" id="selected_user" value="" />
                                            <div class="form-group">
                                                <label class="control-label col-md-3"><?php echo $this->lang->line('total_rate') ?> <span class="currency-symbol"></span><span class="required">*</span></label>
                                                <div class="col-md-4">
                                                    <input type="text" name="total_rate" id="total_rate" value="<?php echo ($order_details[0]->total_rate) ? $order_details[0]->total_rate : 0; ?>" maxlength="10" data-required="1" class="form-control" readonly="" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal modal-main" id="delivery-not-avaliable">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <!-- Modal Header -->
                                                    <div class="modal-header">
                                                        <h4 class="modal-title"><?php echo $this->lang->line('delivery_not_available') ?></h4>
                                                        <button type="button" class="close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
                                                    </div>

                                                    <!-- Modal body -->
                                                    <div class="modal-body">
                                                        <div class="availability-popup">
                                                            <div class="availability-images">
                                                                <img src="<?php echo base_url(); ?>assets/front/images/no-delivery.png" alt="Booking availability">
                                                            </div>
                                                            <h2><?php echo $this->lang->line('avail_text1') ?></h2>
                                                            <p><?php echo $this->lang->line('avail_text2') ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-actions fluid">
                                            <div class="col-md-offset-3 col-md-9">
                                                <button type="submit" name="submit_page" id="submit_page" value="Submit" class="btn btn-success danger-btn"><?php echo "Update Cart" ?></button>
                                                <a class="btn btn-danger danger-btn" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name; ?>/view"><?php echo "Cancel" ?></a>
                                            </div>
                                        </div>
                                    </form>
                                    <!-- END FORM-->
                                </div>
                            </div>
                            <!-- END VALIDATION STATES-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        jQuery(document).ready(function() {
            calculation();
            // Layout.init(); // init current layout
            // $("#restaurant_id").select2({});
            // $("#user_id").select2({});
            getCoupons();
        });


        //Get item price
        function getItemPrices(id, num) {
            var element = $('#' + id).find('option:selected');
            var menu_id = $('#' + id).val();
            var extra = element.attr("data-id-addons");
            var menu_name = element.attr("data-id-name");
            var item_price = element.attr("data-id");
            // console.log(item_price);
            $('#item_rate_' + num).val(item_price);
            $('#item_rate_' + num).attr('name', 'item_id[' + menu_id + '][rate]');
            $('#item_id_' + num).val(menu_id);
            $('#item_id_' + num).attr('name', 'item_id[' + menu_id + '][menu_id]');
            $('#menu_name_' + num).val(menu_name);
            $('#menu_name_' + num).attr('name', 'item_id[' + menu_id + '][menu_name]');

            $('#note_' + num).val(0);
            $('#note_' + num).attr('name', 'item_id[' + menu_id + '][note]');
            $('#has_variation_' + num).val(0);
            $('#has_variation_' + num).attr('name', 'item_id[' + menu_id + '][has_variation]');
            $('#is_deal_' + num).val(0);
            $('#is_deal_' + num).attr('name', 'item_id[' + menu_id + '][id_deal]');

            var update_order_id = $('#order_id').val();
            $('#order_id_' + num).val(update_order_id);
            $('#order_id_' + num).attr('name', 'item_id[' + menu_id + '][order_id]');

            $('#offer_price_' + num).val(0);
            $('#offer_price_' + num).attr('name', 'item_id[' + menu_id + '][offer_price]');
            if (extra != 1) {
                $('#customize_' + num).val(0);
                $('#customize_' + num).attr('name', 'item_id[' + menu_id + '][is_customize]');
            }
            //Retrive Coupon
            var subtotal = $('#subtotal').val();
            var user_id = $('#user_id').val();
            var restaurant_id = $('#restaurant_id').val();
            // $.ajax({
            //     type: "POST",
            //     dataType: "json",
            //     url: BASEURL + "backoffice/Order/get_coupon",
            //     data: {
            //         'restaurant_id': restaurant_id,
            //         'user_id': user_id,
            //         'subtotal': subtotal,
            //         'order_delivery': 'Delivery'
            //     },
            //     success: function(html) {
            //         console.log(html);
            //     }
            // });

            // jQuery.ajax({
            //     type: "POST",
            //     dataType: "json",
            //     url: '<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getVatSd',
            //     data: {
            //         "entity_id": menu_id,

            //     },
            //     beforeSend: function() {
            //         $('#quotes-main-loader').show();
            //     },
            //     success: function(response) {
            //         // console.log(response);
            //         var vat = response.vat_sd['vat'];
            //         var sd = response.vat_sd['sd']
            //         if (vat == null) {
            //             vat = 0;
            //         }
            //         if (sd = 0) {
            //             sd = 0;
            //         }
            //         $('#add_vat' + num).val(vat);
            //         $('#add_sd' + num).val(sd);
            //         $('#qty_no' + num).val(1);
            //         $('#rate' + num).val(item_price);
            //         calculation();
            //         $('#quotes-main-loader').hide();
            //     },
            //     error: function(XMLHttpRequest, textStatus, errorThrown) {
            //         alert(errorThrown);
            //     }
            // });

            $('#qty_no' + num).val(1);
            $('#rate' + num).val(item_price);
            qty('qty_no' + num, num);


            if (extra == 1) {
                var sl_no = $('#sl_no').val();
                jQuery.ajax({
                    type: "POST",
                    dataType: "html",
                    url: '<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/UpdateCartAddOns',
                    data: {
                        "entity_id": menu_id,
                        'sl_no': sl_no

                    },
                    success: function(response) {
                        //alert(response);
                        $('#addOnsdetails' + sl_no).html(response);
                        $('#addOnsdetails' + sl_no).modal('show');
                        itemCoupon();
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });
                $('#customize_' + num).val(1);
                $('#customize_' + num).attr('name', 'item_id[' + menu_id + '][is_customize]');

            }

            // calculation();
        }

        function qty(id, num) {
            $("#list_coupon_discount").val(0);
            $("#item_coupon_discount").val(0);
            // console.log("test");
            $('#' + id).keyup(function() {
                this.value = this.value.replace(/[^0-9]/g, '');

            });
            var element = $('#item_id' + num).find('option:selected');
            // console.log(element);
            var extra = element.attr("data-id-addons");
            var vat = parseFloat($('#vat').val());
            var sd = parseFloat($('#sd').val());
            var add_vat = parseFloat($('#add_vat' + num).val());
            var add_sd = parseFloat($('#add_sd' + num).val());
            var final_vat = 0;
            var final_sd = 0;
            //console.log("vat", extra);
            if (extra == 1) {
                // var session_subTotal = ;
                var myTag = parseFloat($('#subTotal').val());
                console.log(myTag);

                $('#addOns' + num).val(myTag);
                console.log('mytag', myTag)
                // myTag = $('#rate'+num).val();
            } else {
                var myTag = element.attr("data-id");
                $('#addOns' + num).val(myTag);
            }
            var qtydata = parseFloat($('#qty_no' + num).val());
            //Update cart
            if (qtydata) {

                if (isNaN(qtydata)) {
                    qtydata = 0;
                }
                var total = parseFloat(qtydata * myTag);
                //Update Cart
                menu_id = $('#item_id' + num).val();
                $('#qnty_' + num).val(qtydata);
                $('#qnty_' + num).attr('name', 'item_id[' + menu_id + '][quantity]');
                $('#rate_' + num).val(total);
                $('#rate' + num).attr('name', 'item_id[' + menu_id + '][itemTotal]');

                $('#rate' + num).val(total);



            }

            itemCoupon();
            // calculation();
        }


        function couponCalculate(e) {
            $("#coupon_name").val();
            var coupon = $("#coupon_id option:selected").attr('data-coupon-name');
            $("#coupon_name").val(coupon);
            $("#applied_coupon").html(coupon);
            $("#applied_coupon").css("display", "block");
            getCoupons();
        }
        //calculate total rate
        function calculation() {
            return;
            var element = $('#coupon_id').find('option:selected');
            var type = element.attr("type");
            var coupon_id = element.attr("value");
            var coupon_type = element.attr("coupon_type");
            var amount = element.attr("amount");
            var max_amount = element.attr("max_amount");
            var i = 1;
            var sum = 0;
            var total_vat = 0;
            var total_sd = 0;
            var total_sum = 0;
            var delivery_charge = $('#deliveryCharge').val();
            while (i <= 20) {
                if (document.getElementById('rate' + i)) {
                    sum = parseFloat($('#rate' + i).val());
                    if (isNaN(sum)) {
                        sum = 0;
                    }
                    vat = parseFloat($('#add_vat' + i).val());
                    if (vat == null) {
                        vat = 0;
                    }

                    sd = parseFloat($('#add_sd' + i).val());
                    if (sd == null) {
                        sd = 0;
                    }
                    sd_count = parseFloat(sum * (sd / 100));
                    vat_count = parseFloat((sum + sd_count) * (vat / 100));
                    total_vat = parseFloat(total_vat + vat_count);
                    total_sd = parseFloat(total_sd + sd_count);
                    total_sum = parseFloat(total_sum + sum);
                    //console.log(total_vat);
                    i++;
                } else {
                    i++;
                }

            }

            $('#subtotal').val(total_sum);
            if (isNaN(total_vat)) {
                total_vat = 0;
            }
            if (isNaN(total_sd)) {
                total_sd = 0;
            }
            $('#vat').val(total_vat.toFixed(2));
            $('#sd').val(total_sd.toFixed(2));



            var item_discount_amount = parseFloat($("#item_coupon_discount").val());
            var list_discount_amount = parseFloat($("#list_coupon_discount").val());


            var discount_amount = item_discount_amount + list_discount_amount;

            $("#coupon_discount").val(discount_amount);

            if (delivery_charge) {
                total_sum = total_sum + parseFloat(delivery_charge) + total_vat + total_sd - (discount_amount ? discount_amount : 0);
                $('#total_rate').val(total_sum.toFixed(2));
            } else {
                total_sum = parseFloat(total_sum + total_vat + total_sd - (discount_amount ? discount_amount : 0));
                $('#total_rate').val(total_sum.toFixed(2));
            }
        }

        function cloneItem() {
            //alert("tst");
            var divid = $(".clone:last").attr('id');
            //console.log(divid);
            var getnum = divid.split('cloneItem');
            var oldNum = parseInt(getnum[1]);
            var newNum = parseInt(getnum[1]) + 1;
            console.log(newNum);
            newElem = $('#' + divid).clone().attr('id', 'cloneItem' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
            // newElem.find('#item_id'+oldNum).attr('id', 'item_id' + newNum).attr('name', 'item_id[' + newNum +']').attr('onchange','getItemPrices(this.id,'+newNum+')').prop('selected',false).attr('selected',false).val('').removeClass('error');

            newElem.find('#item_id' + oldNum).attr('id', 'item_id' + newNum).attr('onchange', 'getItemPrices(this.id,' + newNum + ')').removeClass('error').css('width', '100%');
            newElem.find('.item_id').last().next().next().remove(); //remove previous select2 values
            newElem.find('#rate' + oldNum).attr('id', 'rate' + newNum).val('').removeClass('error');
            newElem.find('#addOns' + oldNum).attr('id', 'addOns' + newNum).attr('name', 'addOns[' + newNum + ']').val('').removeClass('error');
            newElem.find('#qty_no' + oldNum).attr('id', 'qty_no' + newNum).attr('onkeyup', 'qty(this.id,' + newNum + ')').val('').removeClass('error');
            newElem.find('#add_vat' + oldNum).attr('id', 'add_vat' + newNum).attr('name', 'add_vat[' + newNum + ']').val('').removeClass('error');
            newElem.find('#add_sd' + oldNum).attr('id', 'add_sd' + newNum).attr('name', 'add_sd[' + newNum + ']').val('').removeClass('error');
            newElem.find('#add_vat_calc' + oldNum).attr('id', 'add_vat_calc' + newNum).attr('name', 'add_vat_calc[' + newNum + ']').val('').removeClass('error');
            newElem.find('#add_sd_calc' + oldNum).attr('id', 'add_sd_calc' + newNum).attr('name', 'add_sd_calc[' + newNum + ']').val('').removeClass('error');

            //Cart Update Related task
            $('#sl_no').val(newNum);
            newElem.find('#menu_name_' + oldNum).attr('id', 'menu_name_' + newNum).attr('name', '').attr('value', '').removeClass('error');
            newElem.find('#qnty_' + oldNum).attr('id', 'qnty_' + newNum).attr('name', '').attr('value', '').removeClass('error');
            newElem.find('#item_id_' + oldNum).attr('id', 'item_id_' + newNum).attr('name', '').attr('value', '').removeClass('error');
            newElem.find('#item_rate_' + oldNum).attr('id', 'item_rate_' + newNum).attr('name', '').attr('value', '').removeClass('error');
            //Variation related data
            newElem.find('#has_variation_' + oldNum).attr('id', 'has_variation_' + newNum).attr('name', '').attr('value', '').removeClass('error');
            newElem.find('#has_variation_id_' + oldNum).attr('id', 'has_variation_id_' + newNum).attr('name', '').attr('value', '').removeClass('error');
            newElem.find('#has_variation_name' + oldNum).attr('id', 'has_variation_name' + newNum).attr('name', '').attr('value', '').removeClass('error');
            newElem.find('#has_variation_price' + oldNum).attr('id', 'has_variation_price' + newNum).attr('name', '').attr('value', '').removeClass('error');
            //Variation related data
            newElem.find('#rate_' + oldNum).attr('id', 'rate_' + newNum).attr('name', '').attr('value', '').removeClass('error');
            newElem.find('#customize_' + oldNum).attr('id', 'customize_' + newNum).attr('name', '').attr('value', '').removeClass('error');
            newElem.find('#is_deal_' + oldNum).attr('id', 'is_deal_' + newNum).attr('name', '').attr('value', '').removeClass('error');
            newElem.find('#note_' + oldNum).attr('id', 'note_' + newNum).attr('name', '').attr('value', '').removeClass('error');
            newElem.find('#offer_price_' + oldNum).attr('id', 'offer_price_' + newNum).attr('name', '').attr('value', '').removeClass('error');
            newElem.find('#addOnsdetails' + oldNum).attr('id', 'addOnsdetails' + newNum).attr('class', 'modal modal-main').removeClass('error');
            newElem.find('#order_id_' + oldNum).attr('id', 'order_id_' + newNum).attr('name', '').attr('value', '').removeClass('error');

            newElem.find('.error').remove();
            newElem.find('.clone-label').css('visibility', 'hidden');

            $(".clone:last").after(newElem);
            $('#cloneItem' + newNum + ' .remove').html('<div class="item-delete" onclick="deleteItem(' + newNum + ')"><i class="fa fa-remove"></i></div>');
        }

        function deleteItem(id) {

            $("#list_coupon_discount").val(0);
            $("#item_coupon_discount").val(0);
            $('#cloneItem' + id).remove();
            getCoupons();
        }

        function getCoupons() {
            itemCoupon();
        }

        function itemCoupon() {
            $.ajax({
                type: "POST",
                processData: false,
                contentType: false,
                url: BASEURL + "backoffice/Order/itemCoupon",
                data: new FormData($("#form_add_order")[0]),
                success: function(data, status, xhr) {
                    var de = JSON.parse(data);
                    if (de.coupon_name && de.is_apply == true) {
                        $("#coupon_name").val(de.coupon_name);
                        $("#coupon_type").val(de.coupon_type);
                        $("#coupon_real_id").val(de.coupon_id);
                        $("#coupon_amount").val(de.coupon_amount);
                        $(".coupon_applied_msg").html('<strong>' + de.coupon_name + "</strong> applied");
                        $("#coupon_applied_msg_div").show();
                    } else {
                        $("#coupon_name").val('');
                        $("#coupon_type").val('');
                        $("#coupon_real_id").val('');
                        $("#coupon_amount").val('');
                        $(".coupon_applied_msg").html('');
                        $("#coupon_applied_msg_div").hide();
                    }
                    $("#coupon_discount").val(parseFloat(de.discount));
                    $("#vat").val(parseFloat(de.vat).toFixed(2));
                    $("#sd").val(parseFloat(de.sd).toFixed(2));
                    $("#subtotal").val(parseFloat(de.subtotal).toFixed(2));
                    $("#total_rate").val(parseFloat(de.total).toFixed(2));
                    $("#deliveryCharge").val(parseFloat(de.delivery_charge).toFixed(2));
                    $("#api_string").val(de.res_data_sring);
                    $("#coupon_id").html(de.coupon_list);
                }
            });
        }

        function couponApplied() {
            var coupon = $("#coupon_id option:selected").attr('data-coupon-name');
            $.ajax({
                type: "POST",
                // processData: false,
                // contentType: false,
                url: BASEURL + "backoffice/Order/couponApply",
                data: {
                    data_string: $("#api_string").val(),
                    coupon_name: coupon
                },
                success: function(data, status, xhr) {

                    var de = JSON.parse(data);
                    if (de.coupon_name && de.is_apply == true) {
                        $("#coupon_name").val(de.coupon_name);
                        $("#coupon_type").val(de.coupon_type);
                        $("#coupon_amount").val(de.coupon_amount);
                        $("#coupon_real_id").val(de.coupon_id);
                        $(".coupon_applied_msg").html('<strong>' + de.coupon_name + "</strong> applied");
                        $("#coupon_applied_msg_div").show();
                    } else {
                        $("#coupon_name").val('');
                        $("#coupon_type").val('');
                        $("#coupon_amount").val('');
                        $("#coupon_real_id").val('');
                        $(".coupon_applied_msg").html('');
                        $("#coupon_applied_msg_div").hide();
                        $("#coupon_applied_msg_div").hide();
                    }
                    // $("#coupon_name").val(de.coupon_name);
                    // $("#coupon_type").val(de.coupon_type);
                    $("#coupon_discount").val(parseFloat(de.discount));
                    $("#vat").val(parseFloat(de.vat).toFixed(2));
                    $("#sd").val(parseFloat(de.sd).toFixed(2));
                    $("#subtotal").val(parseFloat(de.subtotal).toFixed(2));
                    $("#total_rate").val(parseFloat(de.total).toFixed(2));
                    $("#deliveryCharge").val(parseFloat(de.delivery_charge).toFixed(2));

                }
            });
        }
    </script>
</div>