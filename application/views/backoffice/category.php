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
                        <?php echo $this->lang->line('category') ?>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo base_url() ?>dashboard">
                                <?php echo $this->lang->line('home') ?> </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            <?php echo $this->lang->line('category') ?>
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
                            <div class="caption"><?php echo $this->lang->line('category') ?></div>
                            <div class="actions c-dropdown">
                                <?php if ($this->lpermission->method('menu_category', 'create')->access()) { ?>
                                    <a class="btn danger-btn btn-sm" href="<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name . '/add/en' ?>"><i class="fa fa-plus"></i> <?php echo $this->lang->line('add') ?></a>
                                <?php } ?>
                                <?php if ($this->lpermission->method('menu_category', 'update')->access()) { ?>
                                    <a class="btn danger-btn btn-sm" id="sortButton" data-toggle="modal" data-target="#sortModal"><i class="fa fa-sort"></i> Sort </a>
                                <?php } ?>

                            </div>
                        </div>
                        <div class="modal" tabindex="-1" role="dialog" id="sortModal">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Sort Category</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">

                                        <div class="col-auto my-3">
                                            <label class="control-label col-md-3" for=""><?php echo $this->lang->line('restaurant') ?></label>
                                            <div class="col-md-4">

                                                <select class="form-control" name="restaurant_id" id="restaurantDropdown" onchange="this.form.submit()">
                                                    <option value="none">Select</option>
                                                    <?php

                                                    foreach ($allRestaurant as $row) {
                                                    ?>

                                                        <option value="<?php echo $row->entity_id ?>">

                                                            <?php echo $row->name  ?>
                                                        </option>
                                                    <?php
                                                    }

                                                    ?>

                                                </select>

                                            </div>
                                        </div>

                                        <br><br>


                                        <ul class="reorder_ul reorder_menu_list">
                                            <!-- <li style="color: black;">gdgd</li> -->

                                            <li class="ui-sortable-handle">

                                                <a href="javascript:void(0);" class="menu_link">
                                                    <h4></h4>
                                                </a>

                                            </li>

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
                                <!-- <form method="post" class="form-horizontal" id="form">
                                    <div class="form-body">
                                        <div class="form-group">
                                            <div class="col-auto my-3">
                                                <label class="control-label col-md-3" for=""><?php echo $this->lang->line('restaurant') ?></label>
                                                <div class="col-md-4">

                                                    <select class="form-control" name="restaurant_id" id="restaurantDropdown" onchange="this.form.submit()">
                                                        <option value="none">Select</option>
                                                        <?php
                                                        $restaurant_id = $this->input->post('restaurant_id');
                                                        foreach ($allRestaurant as $row) {
                                                        ?>

                                                            <option value="<?php echo $row->entity_id ?>" <?php if ($restaurant_id == $row->entity_id) {  ?> selected <?php } ?>>

                                                                <?php echo $row->name  ?>
                                                            </option>
                                                        <?php
                                                        }

                                                        ?>

                                                    </select>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form> -->

                                <!-- <ul class="reorder_ul reorder_menu_list" style="color: black; border-bottom: 1px solid black;">
                                    <li style="color: black;">gdgd</li>
                                    <?php

                                    $items =  $this->category_model->getItem($restaurant_id);
                                    if (!empty($items)) {

                                        foreach ($items as $row) {

                                    ?>
                                            <li id="<?php echo $row->entity_id; ?>" class="ui-sortable-handle" >

                                                <a href="javascript:void(0);" class="menu_link" >
                                                    <h4><?php echo $row->name; ?></h4>
                                                </a>

                                            </li>
                                    <?php }
                                    } ?>
                                </ul> -->
                                <?php if ($this->session->flashdata('page_MSG')) { ?>
                                    <div class="alert alert-success">
                                        <?php echo $this->session->flashdata('page_MSG'); ?>
                                    </div>
                                <?php } ?>
                                <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th class="table-checkbox">#</th>
                                            <th><?php echo $this->lang->line('cat_name') ?></th>
                                            <?php foreach ($Languages as $lang) { ?>
                                                <th><?php echo $lang->language_slug; ?></th>
                                            <?php } ?>
                                            <th>Show in panel</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="page_title"></td>
                                            <?php foreach ($Languages as $lang) { ?>
                                                <td><input type="text" class="form-control form-filter input-sm" disabled="" name="<?php echo $lang->language_slug; ?>"></td>
                                            <?php } ?>
                                            <td></td>
                                            <td>
                                                <button class="btn btn-sm red filter-submit"><i class="fa fa-search"></i> <?php echo $this->lang->line('search') ?></button>
                                                <button class="btn btn-sm red filter-cancel"><i class="fa fa-times"></i> <?php echo $this->lang->line('reset') ?></button>
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
                        {
                            "bSortable": false
                        },
                    <?php } ?> {
                        "bSortable": false
                    }
                ],
                "sPaginationType": "bootstrap_full_number",
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
                            'content_id': content_id,
                            'entity_id': entity_id
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

    function disable_record(ID, Status) {
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
                            'tblname': 'category'
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
                            'tblname': 'category'
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

    function disable_category(entity_id, isactive) {
        var StatusVar = (isactive == 0) ? "<?php echo "Are you sure to show this category in panel?" ?>" : "<?php echo "Are you sure to hide this category in panel?"; ?>";
        console.log(isactive);
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
                        url: 'ajaxDisableCat',
                        data: {
                            'entity_id': entity_id,
                            'isactive': isactive,
                            'tblname': 'category'
                        },
                        success: function(response) {
                            console.log("Success");
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

    $('#restaurantDropdown').change(function() {
        // $('#myForm').submit();
        // $('#myForm').serialize();
        var item_id = $(this).val();
        $.ajax({
            type: "POST",
            // async: true,
            dataType: 'json',
            // dataType: "html",
            url: '<?php echo base_url() . ADMIN_URL . '/' . $this->controller_name ?>/getMenuCategory',
            data: {
                'item_id': item_id,
            },
            success: function(data) {

                // var data = JSON.stringify(data);
                // var value = json_decode(data);
                var html = '';
                var i;
                for (i in data) {
                    html += '<ul class="reorder_ul reorder_menu_list"><li class="ui-sortable-handle" id=' + data[i].id + '><a href="javascript:void(0);" class="menu_link"><h4>' + data[i].name + '</h4></a></li></ul>';

                }


                $('ul.reorder_menu_list').html(html);


            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });

    })

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
                url: "<?php echo base_url() . ADMIN_URL . '/category/orderUpdate'; ?>",
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
</script>
<?php $this->load->view(ADMIN_URL . '/footer'); ?>