<div class="modal-dialog" style="min-width:fit-content">
    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"><?php echo $this->lang->line('status') ?> <?php echo $this->lang->line('list') ?></h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption"><?php echo $this->lang->line('status') ?> <?php echo $this->lang->line('list') ?></div>
                            <div class="actions"></div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr role="row" class="">
                                            <th><?php echo $this->lang->line('order') ?>#</th>
                                            <th><?php echo $this->lang->line('status') ?></th>
                                            <th><?php echo $this->lang->line('date/time') ?></th>
                                            <th><?php echo $this->lang->line('changed_by') ?></th>
                                            <th><?php echo "Email" ?></th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($history)) {
                                            foreach ($history as $key => $value) { ?>
                                                <tr role="row" class="">
                                                    <td><?php echo $value->order_id; ?></td>
                                                    <td><?php echo ucfirst($value->order_status); ?></td>
                                                    <td><?php echo date('d-m-Y g:i A', strtotime($value->time)); ?></td>
                                                    <td><?php echo $value->status_created_by; ?></td>
                                                    <td><?php echo $value->email; ?></td>

                                                </tr>
                                        <?php }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
        </div>
    </div>
</div>