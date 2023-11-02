<?php
if ($cat_id_all_addons) {
    $i = -1;
    foreach ($cat_id_all_addons['addons'] as $final_addon) {
        $i++; ?>
        <div data-repeater-item>
            <div class="form-group">
                <div class="col-md-4">
                    <label class="control-label"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                    <input type="text" name="add_ons_name" id="add_ons_name<?php echo $j ?>" value="<?php echo (!empty($final_addon[0])) ? $final_addon[0] : ''; ?>" class="form-control repeater_field name_repeater" maxlength="249">
                </div>
                <div class="col-md-4">
                    <label class="control-label"><?php echo $this->lang->line('price') ?><span class="required">*</span></label>
                    <input type="text" name="add_ons_price" id="add_ons_price<?php echo $j ?>" value="<?php echo (!empty($final_addon[1])) ? $final_addon[1] : ''; ?>" class="form-control repeater_field" maxlength="19">
                </div>
                <div class="col-sm-2 delete-repeat <?php echo ($i > 0) ? 'display-yes' : 'display-no'; ?>">
                    <label class="control-label">&nbsp;</label>
                    <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
                </div>
                <input type="hidden" name="multiple" value="<?php echo $_POST["is_multiple$value->entity_id"] ?>" />
            </div>
        </div>
    <?php }
} else { ?>
    <div data-repeater-item>
        <div class="form-group">
            <div class="col-md-4">
                <label class="control-label"><?php echo $this->lang->line('add_ons_name') ?><span class="required">*</span></label>
                <input type="text" name="add_ons_name" id="add_ons_name" value="" class="form-control repeater_field name_repeater" maxlength="249">
            </div>
            <div class="col-md-4">
                <label class="control-label"><?php echo $this->lang->line('price') ?><span class="required">*</span></label>
                <input type="text" name="add_ons_price" id="add_ons_price" value="" class="form-control repeater_field" maxlength="19">
            </div>
            <div class="col-sm-2 delete-repeat display-no">
                <label class="control-label">&nbsp;</label>
                <input data-repeater-delete class="btn btn-danger <?php echo ($i > 0) ? 'delete_repeater' : '' ?>" type="button" value="<?php echo $this->lang->line('delete') ?>" />
            </div>
            <input type="hidden" name="multiple" value="" />
        </div>
    </div>
<?php } ?>