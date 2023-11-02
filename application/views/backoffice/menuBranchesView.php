<div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <!-- Modal Header -->
        <div class="modal-header">
            <h4 class="modal-title">Assign Menu to Branches</h4>
            <button type="button" class="close modal-close" data-dismiss="modal"><i class="iicon-icon-23"></i></button>
        </div>
        <!-- Modal body -->
        <form action="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/updateBranchMenu" id="branchMenuUpdateForm" method="post" enctype='multipart/form-data'>
            <div class="modal-body" id="modalData">
                <input type="hidden" name="restaurant_id" value="<?= $restaurant_id ?>">
                <input type="hidden" name="menu_id" value="<?= $menu_id ?>">
                <input type="hidden" name="menu_group_id" value="<?= $menu_group_id ?>">
                <?php
                foreach ($branches as $br) { ?>
                    <div class="form-check form-check-inline">
                        <input id="<?= $br['branch_id'] ?>" class="form-check-input" type="checkbox" name="branch_id[]" value="<?= $br['branch_id'] ?>" <?= $br['status'] ? "checked" : '' ?>>
                        <label for="<?= $br['branch_id'] ?>" class="form-check-label"><?= $br['branch_name'] ?></label>
                        <?php if($br['br_menu_id']){ ?>
                        <a href="<?= base_url() . ADMIN_URL . '/' . $this->controller_name . '/edit_menu/' . 'en'. '/' . str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($br['br_menu_id'])) . '" title="' . $this->lang->line('click_edit')?>"><i class="fa fa-edit"></i> </a>
                        <?php } ?>
                    </div>
                <?php }
                ?>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-secondary modal-submit" data-dismiss="modal">Update</button>
                <button type="button" class="btn btn-secondary modal-close" data-dismiss="modal">Close</button>
            </div>
        </form>
    </div>
</div>

<script type="text/javascript">
    $("#branchMenuUpdateForm").on('submit', function(e) {
        e.preventDefault();
        // $('input[type=checkbox]').attr('disabled', 'true');
        // $("#modal-submit").attr('diabled', 'true');
        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            processData: false,
            contentType: false,
            data: new FormData($(this)[0]),
            success: function(response) {
                $("#modalData").html(
                    "<h3 class='text-success'>Successfully Updated.</h3>"
                )

                setTimeout(() => {
                    $("#menu-branch-modal").hide();
                    grid.getDataTable().fnDraw();
                }, 500);

            },
            error: function(xhr) {
                $("#modalData").html(
                    "<h3 class='text-danger'>Something went wrong.</h3>"
                )
            }
        })
    })

    $(".modal-close").on("click", function() {
        $("#menu-branch-modal").hide();
    })
</script>