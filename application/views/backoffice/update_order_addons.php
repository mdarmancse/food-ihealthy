<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title"> Select Your Choice</h4>
            <button type="button" onclick="hide_addon_model()" class="close"><i class="iicon-icon-23"></i></button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">
            <div id="custom_items_form">
                <div class="popup-radio-btn-main">
                    <!-- <input type="hidden" name="restaurant_id" id="restaurant_id" value="<?php echo $result[0]['items'][0]['restaurant_id']; ?>">-->
                    <!-- <input type="hidden" name="menu_id" id="menu_id" value="<?php echo $menuId; ?>"> -->
                    <div class="item-price-label">
                        <span><?php echo $this->lang->line('item') ?></span>
                        <span><?php echo $this->lang->line('price') ?></span>
                        <input type="hidden" value="<?php echo $sl_no ?>" id="sl_no">
                    </div>
                    <?php
                    $test = array();
                    if ($result[0]['items']) {
                        $test = $result[0]['items'];
                    }
                    if (isset($test[0]['is_customize']) && $test[0]['is_customize'] == 1) { ?>
                        <input type="hidden" id="variation_value_<?php echo $test[0]['menu_id'] ?>" value="<?php echo ($test[0]['has_variation']) ? $test[0]['has_variation'] : 0 ?>">
                        <?php if (isset($test[0]['has_variation']) && $test[0]['has_variation'] == 1) { ?> <h2>Variation List<br></h2>
                            <?php foreach ($test[0]['variation_list'] as  $key => $addvalue) {
                            ?>

                                <input type="radio" name="variation_addon" class="radio_addons" value="<?php echo $addvalue['variation_id']; ?>" id="<?php echo $addvalue['variation_name'] ?>" amount="<?php echo $addvalue['variation_price']; ?>" add_ons_id="<?php echo $addvalue['variation_id']; ?>" onchange="getItemPrice(this.id,<?php echo $addvalue['variation_price']; ?>,0)">
                                <span><?php echo $addvalue['variation_name']; ?></span><br>
                                <span style="float: right;"><?php echo $addvalue['variation_price']; ?> </span>
                            <?php } ?>
                            <?php foreach ($test[0]['variation_list'] as  $key => $addvalue) { ?>


                                <?php if ($addvalue['hasVariationAddon'] == 1) { ?>
                                    <div style="display:none" class="desc" id="<?php echo "variation_" . $addvalue['variation_id']; ?>">
                                        <?php foreach ($addvalue['addons_category_list'] as $key => $addonvalue) { ?>
                                            <span><?php echo $addonvalue['addons_category']; ?></span>
                                            <input type="hidden" value="<?php echo $addonvalue['addons_category']; ?>" id="addons_category_name">
                                            <!-- Update Addon Related Task -->
                                            <input type="hidden" id="addon_cat_<?php echo $sl_no . "_" . $addonvalue['addons_category_id'] ?>" name="" value="">
                                            <input type="hidden" id="addon_cat_name_<?php echo $sl_no . "_" . $addonvalue['addons_category_id'] ?>" name="" value="">

                                            <div class="radio-btn-list">
                                                <label>
                                                    <?php if ($addonvalue['is_multiple'] == 1) {
                                                        foreach ($addonvalue['addons_list'] as $keys => $addoncategory) { ?>
                                                            <input add_on_name="<?php echo $addoncategory['add_ons_name']; ?>" add_on_category_id="<?php echo $addonvalue['addons_category_id'] ?>" type="checkbox" class="check_addons" name="<?php echo $key . '-' . $keys; ?>" id="<?php echo $addoncategory['add_ons_name'] . '-' . $keys; ?>" value="1" onchange="getItemPrice(this.id,<?php echo $addoncategory['add_ons_price']; ?>,<?php echo $addonvalue['is_multiple'] ?>)" menu_id="<?php echo $addvalue['menu_id'] ?>" amount="<?php echo $addoncategory['add_ons_price']; ?>" add_ons_id="<?php echo $addoncategory['add_ons_id']; ?>" add_on_cat_name="<?php echo $addonvalue['addons_category']; ?>">
                                                            <span><?php echo $addoncategory['add_ons_name']; ?></span><br>

                                                            <span style="float:right; margin-left:590px"><?php echo $addoncategory['add_ons_price']; ?> </span>
                                                            <!-- Update Addon Related Task -->
                                                            <input type="hidden" value="" name="" id="addon_id_<?php echo $sl_no . "_" . $addoncategory['add_ons_id'] ?>">
                                                            <input type="hidden" value="" name="" id="addon_id_name_<?php echo $sl_no . "_" . $addoncategory['add_ons_id'] ?>">
                                                            <input type="hidden" value="" name="" id="addon_id_price_<?php echo $sl_no . "_" . $addoncategory['add_ons_id'] ?>">


                                                        <?php

                                                        }
                                                    } else {
                                                        foreach ($addonvalue['addons_list'] as $keys => $addoncategory) { ?>

                                                            <input type="radio" add_on_category_id="0" class="radio_addons" name="<?php echo $key; ?>" add_on_category_id="66" id="<?php echo $addoncategory['add_ons_name'] . '-' . $keys; ?>" value="1" onchange="getItemPrice(this.id,<?php echo $addoncategory['add_ons_price']; ?>,<?php echo $addoncategory['is_multiple']; ?>)" amount="<?php echo $addoncategory['add_ons_price']; ?>" add_ons_id="<?php echo $addoncategory['add_ons_id']; ?>">
                                                            <span><?php echo $addoncategory['add_ons_name']; ?></span>
                                                            <br>
                                                            <span style="float:right; margin-left:590px"><?php echo $addoncategory['add_ons_price']; ?> </span>
                                                    <?php }
                                                    } ?>

                                                </label>

                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            <?php }
                        } else {
                            foreach ($test[0]['addons_category_list'] as $key => $addonvalue) { ?>
                                <span><?php echo $addonvalue['addons_category']; ?></span><br>
                                <input type="hidden" value="<?php echo $addonvalue['addons_category']; ?>" id="addons_category_name">
                                <!-- Update Addon Related Task -->
                                <input type="hidden" id="addon_cat_<?php echo $sl_no . "_" . $addonvalue['addons_category_id'] ?>" name="" value="">
                                <input type="hidden" id="addon_cat_name_<?php echo $sl_no . "_" . $addonvalue['addons_category_id'] ?>" name="" value="">
                                <div class="radio-btn-list">
                                    <label>
                                        <?php if ($addonvalue['is_multiple'] == 1) {
                                            foreach ($addonvalue['addons_list'] as $keys => $addoncategory) { ?>
                                                <input add_on_name="<?php echo $addoncategory['add_ons_name']; ?>" add_on_category_id="<?php echo $addonvalue['addons_category_id'] ?>" type="checkbox" class="check_addons" name="<?php echo $key . '-' . $keys; ?>" id="<?php echo $addoncategory['add_ons_name'] . '-' . $keys; ?>" value="1" onchange="getItemPrice(this.id,<?php echo $addoncategory['add_ons_price']; ?>,<?php echo $addonvalue['is_multiple'] ?>)" menu_id="<?php echo $addvalue['menu_id'] ?>" amount="<?php echo $addoncategory['add_ons_price']; ?>" add_ons_id="<?php echo $addoncategory['add_ons_id']; ?>" add_on_cat_name="<?php echo $addonvalue['addons_category']; ?>">
                                                <!-- Update Addon Related Task -->
                                                <input type="hidden" value="" name="" id="addon_id_<?php echo $sl_no . "_" . $addoncategory['add_ons_id'] ?>">
                                                <input type="hidden" value="" name="" id="addon_id_name_<?php echo $sl_no . "_" . $addoncategory['add_ons_id'] ?>">
                                                <input type="hidden" value="" name="" id="addon_id_price_<?php echo $sl_no . "_" . $addoncategory['add_ons_id'] ?>">
                                                <span><?php echo $addoncategory['add_ons_name']; ?><br><span style="float:right;margin-left:590px"><?php echo $addoncategory['add_ons_price']; ?> </span></span>
                                            <?php

                                            }
                                        } else {
                                            foreach ($addonvalue['addons_list'] as $keys => $addoncategory) { ?>
                                                <input type="radio" add_on_category_id="0" class="radio_addons" name="<?php echo $key; ?>" add_on_category_id="66" id="<?php echo $addoncategory['add_ons_name'] . '-' . $keys; ?>" value="1" onchange="getItemPrice(this.id,<?php echo $addoncategory['add_ons_price']; ?>,<?php echo $addoncategory['is_multiple']; ?>)" amount="<?php echo $addoncategory['add_ons_price']; ?>" add_ons_id="<?php echo $addoncategory['add_ons_id']; ?>">
                                                <span><?php echo $addoncategory['add_ons_name']; ?></span><br>
                                                <span style="float:right;margin-left:590px"><?php echo $addoncategory['add_ons_price']; ?> </span>
                                        <?php }
                                        } ?>

                                    </label>

                                </div>
                    <?php }
                        }
                    }
                    ?>

                </div>
                <div class="popup-total-main">
                    <div class="popup-total">
                        <h2><?php echo $this->lang->line('total') ?></h2>
                    </div>
                    <div class="total-price">
                        <input type="hidden" name="subTotal" id="subTotal" value="0">
                        <strong><?php echo $currency_symbol->currency_symbol; ?> <span id="totalPrice<?php echo $sl_no; ?>">0</span></strong>
                        <!-- onclick="AddToCart('<?php //echo $result[0]['items'][0]['menu_id'];
                                                    ?>')" -->
                        <button type="button" class="addtocart btn addtocart" id="addtocart" onclick="addOnsItems()"><?php echo $this->lang->line('add') ?></button>
                    </div>
                </div>
                <input type="hidden" name="" id="radio_total<?php echo $sl_no; ?>">
                <input type="hidden" name="" id="check_total<?php echo $sl_no; ?>">
            </div>
        </div>
    </div>
</div>
<script>
    // $("div.desc").hide();
    $("input[name$='variation_addon']").click(function() {
        var test = $(this).val();
        var variation_id = (this.checked ? $(this).attr("add_ons_id") : 0);
        var variation_name = (this.checked ? $(this).attr("id") : '');
        var variation_price = (this.checked ? $(this).attr("amount") : '');
        // variation related code
        var sl_no = $('#sl_no').val();
        var addons_category_id = (this.checked ? $(this).attr("add_on_category_id") : 0);
        var menu_id = $('#item_id_' + sl_no).val();
        if (variation_id != null) {
            //Has Variation Checking
            $('#has_variation_' + sl_no).val(1);
            $('#has_variation_' + sl_no).attr('name', 'item_id[' + menu_id + '][has_variation]');
            $('#has_variation_id_' + sl_no).val(variation_id);
            $('#has_variation_id_' + sl_no).attr('name', 'item_id[' + menu_id + '][variation_list][0][variation_id]');
            $('#has_variation_name' + sl_no).val(variation_name);
            $('#has_variation_name' + sl_no).attr('name', 'item_id[' + menu_id + '][variation_list][0][variation_name]');
            $('#has_variation_price' + sl_no).val(variation_price);
            $('#has_variation_price' + sl_no).attr('name', 'item_id[' + menu_id + '][variation_list][0][variation_price]');

        }

        $("div.desc").hide();
        $("#variation_" + test).show();
    });
</script>

<script type="text/javascript">
    //get item price
    var totalPrice = 0;
    var radiototalPrice = 0;
    var checktotalPrice = 0;

    function hide_addon_model() {
        var sl_no = $('#sl_no').val();
        $('#addOnsdetails' + sl_no).modal('hide');
    }

    function getItemPrice(id, price, is_multiple) {
        // alert("test");
        var totalPrice = 0;
        radiototalPrice = 0;
        checktotalPrice = 0;
        var cnt = 0;
        var sl = 0;
        var sl_no = $('#sl_no').val();
        $(".check_addons:checkbox:checked").change(function() {
            var ischecked = $(this).is(':checked');
            // alert(ischecked);
            if (ischecked == false) {
                var addons_category_id = (this.checked ? $(this).attr("add_on_category_id") : $(this).attr("add_on_category_id"));
                var add_on_id = (this.checked ? $(this).attr("add_ons_id") : $(this).attr("add_ons_id"));
                var sl_no = $('#sl_no').val();
                var menu_id = $('#item_id_' + sl_no).val();
                var has_variation = $('#variation_value_' + menu_id).val();
                if (has_variation == 1) {

                    $('#addon_cat_' + sl_no + "_" + addons_category_id).removeAttr('name');

                    $('#addon_cat_name_' + sl_no + "_" + addons_category_id).removeAttr('name');
                    // Addon list

                    $('#addon_id_' + sl_no + "_" + add_on_id).removeAttr('name');

                    $('#addon_id_name_' + sl_no + "_" + add_on_id).removeAttr('name');

                    $('#addon_id_price_' + sl_no + "_" + add_on_id).removeAttr('name');
                } else {

                    $('#addon_cat_' + sl_no + "_" + addons_category_id).removeAttr('name');

                    $('#addon_cat_name_' + sl_no + "_" + addons_category_id).removeAttr('name');
                    // Addon list

                    $('#addon_id_' + sl_no + "_" + add_on_id).removeAttr('name');

                    $('#addon_id_name_' + sl_no + "_" + add_on_id).removeAttr('name');

                    $('#addon_id_price_' + sl_no + "_" + add_on_id).removeAttr('name');
                }
            }
        });
        if (is_multiple != 1) {
            $("input:radio.radio_addons:checked").each(function() {
                var sThisVal = (this.checked ? $(this).attr("amount") : 0);
                console.log(sThisVal);
                radiototalPrice = parseFloat(radiototalPrice) + parseFloat(sThisVal);
                radioprice = radiototalPrice
            });
            $('#radio_total' + sl_no).val(radioprice);
        } else {
            $('.check_addons:checkbox:checked').each(function() {
                // alert("Test");
                var addons_category_id = (this.checked ? $(this).attr("add_on_category_id") : 0);
                var add_on_id = (this.checked ? $(this).attr("add_ons_id") : 0);
                var sThisVal = (this.checked ? $(this).attr("amount") : 0);
                // addon list add_on_name
                var addons_category_id = (this.checked ? $(this).attr("add_on_category_id") : 0);
                var addons_category_name = (this.checked ? $(this).attr("add_on_cat_name") : 0);
                var addons_name = (this.checked ? $(this).attr("add_on_name") : 0);
                var add_on_id = (this.checked ? $(this).attr("add_ons_id") : 0);
                var price = (this.checked ? $(this).attr("amount") : 0);
                var sl_no = $('#sl_no').val();
                var menu_id = $('#item_id_' + sl_no).val();
                var has_variation = $('#variation_value_' + menu_id).val();
                if (this.checked) {
                    if (has_variation == 1) {
                        $('#addon_cat_' + sl_no + "_" + addons_category_id).val(addons_category_id);
                        $('#addon_cat_' + sl_no + "_" + addons_category_id).attr('name', 'item_id[' + menu_id + '][variation_list][0][addons_category_list][' + addons_category_id + '][addons_category_id]');
                        $('#addon_cat_name_' + sl_no + "_" + addons_category_id).val(addons_category_name);
                        $('#addon_cat_name_' + sl_no + "_" + addons_category_id).attr('name', 'item_id[' + menu_id + '][variation_list][0][addons_category_list][' + addons_category_id + '][addons_category_name]');
                        // Addon list
                        $('#addon_id_' + sl_no + "_" + add_on_id).val(add_on_id);
                        $('#addon_id_' + sl_no + "_" + add_on_id).attr('name', 'item_id[' + menu_id + '][variation_list][0][addons_category_list][' + addons_category_id + '][addons_list][' + add_on_id + '][add_ons_id]');
                        $('#addon_id_name_' + sl_no + "_" + add_on_id).val(addons_name);
                        $('#addon_id_name_' + sl_no + "_" + add_on_id).attr('name', 'item_id[' + menu_id + '][variation_list][0][addons_category_list][' + addons_category_id + '][addons_list][' + add_on_id + '][add_ons_name]');
                        $('#addon_id_price_' + sl_no + "_" + add_on_id).val(price);
                        $('#addon_id_price_' + sl_no + "_" + add_on_id).attr('name', 'item_id[' + menu_id + '][variation_list][0][addons_category_list][' + addons_category_id + '][addons_list][' + add_on_id + '][add_ons_price]');
                    } else {
                        $('#addon_cat_' + sl_no + "_" + addons_category_id).val(addons_category_id);
                        $('#addon_cat_' + sl_no + "_" + addons_category_id).attr('name', 'item_id[' + menu_id + '][addons_category_list][' + addons_category_id + '][addons_category_id]');
                        $('#addon_cat_name_' + sl_no + "_" + addons_category_id).val(addons_category_name);
                        $('#addon_cat_name_' + sl_no + "_" + addons_category_id).attr('name', 'item_id[' + menu_id + '][addons_category_list][' + addons_category_id + '][addons_category_name]');
                        // Addon list
                        $('#addon_id_' + sl_no + "_" + add_on_id).val(add_on_id);
                        $('#addon_id_' + sl_no + "_" + add_on_id).attr('name', 'item_id[' + menu_id + '][addons_category_list][' + addons_category_id + '][addons_list][' + add_on_id + '][add_ons_id]');
                        $('#addon_id_name_' + sl_no + "_" + add_on_id).val(addons_name);
                        $('#addon_id_name_' + sl_no + "_" + add_on_id).attr('name', 'item_id[' + menu_id + '][addons_category_list][' + addons_category_id + '][addons_list][' + add_on_id + '][add_ons_name]');
                        $('#addon_id_price_' + sl_no + "_" + add_on_id).val(price);
                        $('#addon_id_price_' + sl_no + "_" + add_on_id).attr('name', 'item_id[' + menu_id + '][addons_category_list][' + addons_category_id + '][addons_list][' + add_on_id + '][add_ons_price]');
                    }
                } else {
                    console.log("test Add ojn")
                }


                checktotalPrice = parseFloat(checktotalPrice) + parseFloat(sThisVal);

            });
            $('#check_total' + sl_no).val(checktotalPrice)
        }
        totalPrice = ($("#radio_total" + sl_no).val() != '' ? parseFloat($("#radio_total" + sl_no).val()) : 0) + ($("#check_total" + sl_no).val() != '' ? parseFloat($("#check_total" + sl_no).val()) : 0);
        $('#totalPrice' + sl_no).html(totalPrice);
        console.log(totalPrice);
        $('#subTotal').val(totalPrice);
        // totalPrice = radioprice + checktotalPrice;
        // $('#totalPrice' + sl_no).html(totalPrice);
        // $('#subTotal').val(totalPrice);
    }


    function addOnsItems() {

        var subTotal = $('#subTotal').val();
        var valueArray = [];
        var sl_no = $('#sl_no').val();
        var cnt = 0;
        $('.check_addons:checkbox:checked').each(function() {

            var addons_id = $(this).attr("add_ons_id");
            var menu_id = $(this).attr("menu_id");
            valueArray.push({
                "add_ons_id": addons_id,
                'menu_id': menu_id
            });
            cnt++;
        });
        //console.log("array " + valueArray);
        $("#custom_items_form input[type=radio][class='radio_addons']:checked").each(function() {
            var addons_id = $(this).attr("add_ons_id");
            var menu_id = $(this).attr("menu_id");
            //console.log(addons_id);
            valueArray.push({
                "add_ons_id": addons_id,
                'menu_id': menu_id
            });
        });
        //  console.log("array " + valueArray);
        // send addons array to cart
        if (valueArray.length > 0) {
            jQuery.ajax({
                type: "POST",
                url: BASEURL + 'backoffice/order/addOns',

                data: {
                    'add_ons_array': valueArray,
                    'subTotal': subTotal
                },
                beforeSend: function() {
                    $('#quotes-main-loader').show();
                },
                success: function(response) {
                    $('#quotes-main-loader').hide();
                    $('#addOnsdetails' + sl_no).modal('hide');
                    $('#item_subtotal').val(subTotal);
                    var sl = $('#sl_no').val();
                    $('#rate' + sl).val(subTotal);
                    calculation();
                    $('input:checkbox').removeAttr('checked');
                    itemCoupon();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });
        }

        itemCoupon();


    }
</script>

<style>
    .item-price-label {
        display: -webkit-box;
        display: -moz-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }



    .item-price-label>span {
        text-transform: uppercase;
        font-size: 18px;
        font-weight: 700;
        margin-top: -18px;
    }

    .item-price-label>span {
        text-transform: uppercase;
        font-size: 18px;
    }

    .item-price-label>span {
        font-size: 16px;
    }

    .popup-total-main .popup-total h2 {
        font-size: 20px;
        font-weight: 700;
        color: #161212;
        margin: 0px;
    }

    .popup-total-main {
        display: -webkit-box;
        display: -moz-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        align-items: center;
        background: #fff;
        padding: 15px 15px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
        border-radius: 5px;
        margin-bottom: 17px;
    }

    .popup-total-main .total-price {
        display: -webkit-box;
        display: -moz-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        align-items: center;
        margin: 0 0 0 auto;
    }

    .popup-total-main .total-price strong {
        color: #ffb300;
        color: var(--main-color);
        font-size: 25px;
        font-weight: 700;
        margin-right: 20px;
    }

    .popup-total-main .addtocart.btn {
        padding: 0.2rem 2.0rem;
        font-size: 16px;
    }

    .popup-total-main .popup-total h2 {
        font-size: 20px;
    }

    .popup-total-main .total-price strong {
        font-size: 25px;
    }

    .popup-total-main {
        padding: 7px;
    }

    .popup-total-main .addtocart.btn {
        padding: 1px 10px;
        font-size: 14px;
    }

    .popup-total-main .total-price strong {
        font-size: 20px;
    }
</style>