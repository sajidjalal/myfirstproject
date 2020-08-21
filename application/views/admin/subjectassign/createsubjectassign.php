<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-map-o"></i> <?php echo $this->lang->line('examinations'); ?> <small><?php echo $this->lang->line('student_fee1'); ?></small>  </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <?php
            if ($this->rbac->hasPrivilege('marks_grade', 'can_add')) {
                ?>
                <div class="col-md-5">
                    <!-- Horizontal Form -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Assign Grade / Mark to subjects</h3>
                        </div><!-- /.box-header -->
                        <!-- form start -->
                        <form id="form1" action="<?php echo site_url('admin/subjectassign/create') ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8">
                            <div class="box-body">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <?php echo $this->session->flashdata('msg') ?>
                                <?php } ?>
                                <?php
                                if (isset($error_message)) {
                                    echo "<div class='alert alert-danger'>" . $error_message . "</div>";
                                }
                                ?>      
                                <?php echo $this->customlib->getCSRF(); ?>                   
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Select Class</label><small class="req"> *</small>
                                    <select class = "form-control" name = "class">
										<option value = "">Select Class</option>
										<?php
											foreach($listclasses as $class){
												?>
												<option value = "<?php echo $class['id']; ?>"><?php echo $class['class']; ?></option>
												<?php
											}
										?>
									</select>
                                    <span class="text-danger"><?php echo form_error('name'); ?></span>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Assign Marks / Grades</label><small class="req"> *</small>
									<?php
										foreach($listsubject as $subject){
											?>
											<div class = "row">
												<div class = "col-md-7">
												<input type = "checkbox" name = "subject[]" value = "<?php echo $subject['id']; ?>"/>
												<?php echo $subject['name']; ?></div>
												<div class = "col-md-5">
													<select class = "form-control" name = "markgrade[]">
														<option value = "">Select </option>
														<option value = "grade">Grade</option>
														<option value = "mark">Mark</option>
													</select>
												</div>
											</div>
											<?php
										}
									?>
                                    <span class="text-danger"><?php echo form_error('mark_from'); ?></span>
                                </div>
                            </div><!-- /.box-body -->

                            <div class="box-footer">
                                <button type="submit" class="btn btn-info pull-right"><?php echo $this->lang->line('save'); ?></button>
                            </div>
                        </form>
                    </div>

                </div><!--/.col (right) -->
                <!-- left column -->

            <?php } ?>
            <div class="col-md-<?php
            if ($this->rbac->hasPrivilege('marks_grade', 'can_add')) {
                echo "7";
            } else {
                echo "12";
            }
            ?>">
                <!-- general form elements -->
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title titlefix">Class Subject List</h3>
                        <div class="box-tools pull-right">
                        </div><!-- /.box-tools -->
                    </div><!-- /.box-header -->
                    <div class="box-body">
                        <div class="mailbox-controls">
                            <div class="pull-right">

                            </div><!-- /.pull-right -->
                        </div>
                        <div class="table-responsive mailbox-messages">
                            <div class="download_label">Class Subject List</div>
                            <table class="table table-striped table-bordered table-hover example">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Subjects</th>
										<th>Grade / Marks</th>
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
									
                                    if (empty($classsubject)) {
                                        
                                    } else {
                                        foreach ($classsubject as $a) {
                                            ?>
                                            <tr>
                                                <td class="mailbox-name">
                                                    <a href="#" data-toggle="popover" class="detail_popover" ><?php echo $a['class'] ?></a>
                                                </td>
                                                <td class="mailbox-name">
													<?php 
														foreach($a['subject'] as $b){
															echo $b['name'].'<br/>';
														}
													?>
												</td>
                                                <td class="mailbox-name"> 
													<?php 
														foreach($a['mark_grade'] as $b){
															echo $b.'<br/>';
														}
													?>
												</td>
                                                <td class="mailbox-date pull-right">
                                                    <?php
                                                    if ($this->rbac->hasPrivilege('marks_grade', 'can_edit')) {
                                                        ?>
                                                        <a href="<?php echo base_url(); ?>admin/subjectassign/edit/<?php echo $a['id'] ?>" class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <?php
                                                    }
                                                    if ($this->rbac->hasPrivilege('marks_grade', 'can_delete')) {
                                                        ?>
                                                        <a href="<?php echo base_url(); ?>admin/subjectassign/delete/<?php echo $a['id'] ?>"class="btn btn-default btn-xs"  data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>

                                </tbody>
                            </table><!-- /.table -->
                        </div><!-- /.mail-box-messages -->
                    </div><!-- /.box-body -->
                </div>
            </div><!--/.col (left) -->
            <!-- right column -->

        </div>
        <div class="row">
            <!-- left column -->
            <!-- right column -->
            <div class="col-md-12">
                <!-- Horizontal Form -->
            </div><!--/.col (right) -->
        </div>   <!-- /.row -->
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->
<script type="text/javascript">
    $(document).ready(function () {
        $('#postdate').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true
        });
        $("#btnreset").click(function () {
            $("#form1")[0].reset();
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('.detail_popover').popover({
            placement: 'right',
            trigger: 'hover',
            container: 'body',
            html: true,
            content: function () {
                return $(this).closest('td').find('.fee_detail_popover').html();
            }
        });
    });
</script>

