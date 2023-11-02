<?php $this->load->view(ADMIN_URL . '/header'); ?>
<!-- BEGIN PAGE LEVEL STYLES -->
<script src="<?php echo base_url(); ?>assets/admin/plugins/jquery-ui/jquery-ui.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.css" />
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
    <?php $this->load->view(ADMIN_URL . '/sidebar'); ?>
    <!-- END sidebar -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                        <?php echo $this->lang->line('restaurant') ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() . ADMIN_URL ?>/dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('restaurant') ?>
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>
            <!-- END PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('restaurant') ?></div>
                            <div class="actions c-dropdown">
                                <?php if ($this->lpermission->method('restaurant', 'create')->access()) {
                                ?>
                                    <a class="btn danger-btn btn-sm" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name . '/add/en' ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                                <?php }
                                ?>

                                <?php if ($this->lpermission->method('restaurant', 'update')->access()) { ?>
                                    <a class="btn danger-btn btn-sm" id="sortButton" data-toggle="modal" data-target="#sortModal"><i class="fa fa-sort"></i> Sort </a>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="modal" tabindex="-1" role="dialog" id="sortModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Sort Restaurant Zonewise</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div>
                                            <select name="zone_id" id="zone_id" class="form-control input-lg" style="width:100%">
                                                <option value="" selected>Select</option>
                                                <?php

                                                foreach ($zone as $value) { ?>
                                                    <option value="<?php echo $value->entity_id ?>"><?php echo $value->area_name ?></option>
                                                <?php  } ?>
                                            </select>
                                        </div>



                                        <ul class="reorder_ul reorder_menu_list">
                                            <!-- <li style=" color: black;">gdgd</li> -->

                                        </ul>




                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                        <button type="button" id="saveButton" class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            </div>
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
                                <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th class="table-checkbox">#</th>
                                            <th><?php echo $this->lang->line('title') ?></th>
                                            <?php foreach ($Languages as $lang) { ?>
                                                <th><?php echo $lang->language_slug; ?></th>
                                            <?php } ?>
                                            <th><?php echo "App Link" ?></th>
                                            <th><?php echo $this->lang->line('status') ?></th>
                                            <th><?php echo $this->lang->line('action') ?></th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="page_title"></td>
                                            <?php foreach ($Languages as $lang) { ?>
                                                <td><input type="text" class="form-control form-filter input-sm" disabled="" name="<?php echo $lang->language_slug; ?>"></td>
                                            <?php } ?>
                                            <td></td>
                                            <td>
                                                <select name="status" class="form-control form-filter input-sm">
                                                    <option value=""><?php echo $this->lang->line('select') ?></option>
                                                    <option value="1"><?php echo $this->lang->line('active') ?></option>
                                                    <option value="0"><?php echo $this->lang->line('inactive') ?></option>
                                                </select>
                                            </td>
                                            <td>
                                                <div class="margin-bottom-5">
                                                    <button class="btn btn-sm  danger-btn filter-submit margin-bottom"><i class="fa fa-search"></i> <?php echo $this->lang->line('search') ?></button>
                                                </div>
                                                <button class="btn btn-sm danger-btn filter-cancel"><i class="fa fa-times"></i> <?php echo $this->lang->line('reset') ?></button>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
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
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/admin/plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo base_url(); ?>assets/admin/scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/admin/scripts/datatable.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" defer></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
<script>
    var grid;

    jQuery(document).ready(function() {
        Layout.init(); // init current layout
        grid = new Datatable();
        grid.init({
            src: $("#datatable_ajax"),
            onSuccess: function(grid) {
                // execute some code after table records loaded
            },
            onError: function(grid) {
                // execute some code on network or other general error
            },
            dataTable: { // here you can define a typical datatable settings from http://datatables.net/usage/options
                "sDom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",
                "aoColumns": [{
                        "bSortable": false
                    },
                    {
                        "bSortable": false
                    },
                    <?php foreach ($Languages as $lang) { ?> {
                            "bSortable": false
                        },
                    <?php } ?> null,
                    {
                        "bSortable": false
                    },
                    {
                        "bSortable": false
                    }
                ],
                "sPaginationType": "bootstrap_full_number",

                "iDisplayLength": 25,
                "oLanguage": {
                    "sProcessing": sProcessing,
                    "sLengthMenu": sLengthMenu,


                    "sInfo": sInfo,
                    "sInfoEmpty": sInfoEmpty,
                    "sGroupActions": sGroupActions,
                    "sAjaxRequestGeneralError": sAjaxRequestGeneralError,
                    "sEmptyTable": sEmptyTable,
                    "sZeroRecords": sZeroRecords,
                    "oPaginate": {
                        "sPrevious": sPrevious,
                        "sNext": sNext,
                        "sPage": sPage,
                        "sPageOf": sPageOf,
                        "sFirst": sFirst,
                        "sLast": sLast
                    }
                },
                "bServerSide": true, // server side processing
                "sAjaxSource": "ajaxview", // ajax source
                "aaSorting": [
                    [3, "desc"]
                ] // set first column as a default sort by asc
            }
        });
        $('#datatable_ajax_filter').addClass('hide');
        $('input.form-filter, select.form-filter').keydown(function(e) {
            if (e.keyCode == 13) {
                grid.addAjaxParam($(this).attr("name"), $(this).val());
                grid.getDataTable().fnDraw();
            }
        });

        $("#zone_id").select2({
            dropdownParent: $("#sortModal"),

        });
    });
    $('#zone_id').on('change', function() {
        let zone_id = $("#zone_id").val();
        jQuery.ajax({
            type: "POST",
            dataType: "html",
            url: '<?php echo base_url() . ADMIN_URL . '/' . "Restaurant" ?>/getAllRestaurant_Zonewise',
            data: {
                'zone_id': zone_id,
            },
            success: function(response) {
                $('.reorder_menu_list').empty().append(response);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    });
    // method for deleting
    function deleteDetail(entity_id, content_id) {
        bootbox.confirm({
            message: "<?php echo $this->lang->line('delete_module'); ?>",
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                },
                cancel: {
                    label: '<?php echo $this->lang->line('cancel'); ?>',
                }
            },
            callback: function(deleteConfirm) {
                if (deleteConfirm) {
                    jQuery.ajax({
                        type: "POST",
                        dataType: "html",
                        url: 'ajaxDelete',
                        data: {
                            'tblname': 'restaurant',
                            'entity_id': entity_id,
                            'content_id': content_id
                        },
                        success: function(response) {
                            grid.getDataTable().fnDraw();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert(errorThrown);
                        }
                    });
                }
            }
        });
    }
    $('#sortButton').on('click', function() {
        $("ul.reorder_menu_list").sortable({
            tolerance: 'pointer',
        });
        $('.menu_link').attr("href", "javascript:void(0);");
        $('.menu_link').css("cursor", "move");
        $("#saveButton").click(function(e) {


            var list = [];
            $("ul.reorder_menu_list li").each(function() {
                list.push($(this).attr('id'));
            });

            $.ajax({
                type: "POST",
                url: "<?php echo base_url() . ADMIN_URL . '/restaurant/orderUpdate'; ?>",
                data: {
                    ids: " " + list + ""
                },
                success: function() {
                    window.location.reload();
                },
                error: function() {
                    alert(console.error());
                }
            });
            return false;
            // }
            e.preventDefault();
        });
    });

    function deleteAll(content_id) {
        bootbox.confirm({
            message: "<?php echo $this->lang->line('delete_module'); ?>",
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                },
                cancel: {
                    label: '<?php echo $this->lang->line('cancel'); ?>',
                }
            },
            callback: function(deleteConfirm) {
                if (deleteConfirm) {
                    jQuery.ajax({
                        type: "POST",
                        dataType: "json",
                        url: 'ajaxDeleteAll',
                        data: {
                            'tblname': 'restaurant',
                            'content_id': content_id
                        },
                        success: function(response) {
                            grid.getDataTable().fnDraw();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert(errorThrown);
                        }
                    });
                }
            }
        });
    }

    function disablePage(ID, Status) {
        var StatusVar = (status == 0) ? "<?php echo $this->lang->line('active_module'); ?>" : "<?php echo $this->lang->line('deactive_module'); ?>";
        bootbox.confirm({
            message: StatusVar,
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                },
                cancel: {
                    label: '<?php echo $this->lang->line('cancel'); ?>',
                }
            },
            callback: function(disableConfirm) {
                if (disableConfirm) {
                    jQuery.ajax({
                        type: "POST",
                        dataType: "json",
                        url: 'ajaxDisable',
                        data: {
                            'entity_id': ID,
                            'status': Status,
                            'tblname': 'restaurant'
                        },
                        success: function(response) {
                            grid.getDataTable().fnDraw();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert(errorThrown);
                        }
                    });
                }
            }
        });
    }

    function disableAll(ContentID, Status) {
        var StatusVar = (status == 0) ? "<?php echo $this->lang->line('active_module'); ?>" : "<?php echo $this->lang->line('deactive_module'); ?>";
        bootbox.confirm({
            message: StatusVar,
            buttons: {
                confirm: {
                    label: '<?php echo $this->lang->line('ok'); ?>',
                },
                cancel: {
                    label: '<?php echo $this->lang->line('cancel'); ?>',
                }
            },
            callback: function(disableConfirm) {
                if (disableConfirm) {
                    jQuery.ajax({
                        type: "POST",
                        dataType: "json",
                        url: 'ajaxDisableAll',
                        data: {
                            'content_id': ContentID,
                            'status': Status,
                            'tblname': 'restaurant'
                        },
                        success: function(response) {
                            grid.getDataTable().fnDraw();
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert(errorThrown);
                        }
                    });
                }
            }
        });
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        toastr.success("Copied to clipboard!")
    }

    // $('#zone_id').on('change', function() {
    //     let zone_id = $("#zone_id").val();
    //     let restaurants = <?php echo json_encode($allRestaurant); ?>;
    //     console.log(restaurants);
    //     var filterres = [];
    //     restaurants.map(restaurant => {
    //         if (restaurant.zone_id == zone_id) {
    //             filterres.push({
    //                 id: restaurant.entity_id,
    //                 name: restaurant.name,
    //                 zone_id: restaurant.zone_id
    //             });
    //             // $('ul.reorder_menu_list').append(restaurant.name);
    //         }
    //     });

    // });
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>