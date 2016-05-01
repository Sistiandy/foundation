<div class="col-md-12 col-sm-12 col-xs-12 main post-inherit">
    <div class="x_panel post-inherit">
        <h3>
            Daftar Kegiatan
            <a href="<?php echo site_url('admin/activity/add'); ?>" ><span class="glyphicon glyphicon-plus-sign"></span></a>
        </h3>

        <!-- Indicates a successful or positive action -->

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th class="controls" align="center">JUDUL</th>
                        <th class="controls" align="center">PENULIS</th>
                        <th class="controls" align="center">KATEGORI</th>
                        <th class="controls" align="center">TANGGAL</th>
                        <th class="controls" align="center">STATUS</th>
                        <th class="controls" align="center">AKSI</th>
                    </tr>
                </thead>
                <?php
                if (!empty($activity)) {
                    foreach ($activity as $row) {
                        ?>
                        <tbody>
                            <tr>
                                <td ><?php echo $row['activity_title']; ?></td>
                                <td ><?php echo $row['user_name']; ?></td>
                                <td ><?php echo $row['category_name']; ?></td>
                                <td ><?php echo pretty_date($row['activity_input_date'], 'l, d/m/Y', FALSE); ?></td>
                                <td ><?php echo ($row['activity_is_published'] == 0) ? 'Draft' : 'Terbit'; ?></td>
                                <td>
                                    <a class="btn btn-warning btn-xs" href="<?php echo site_url('admin/activity/detail/' . $row['activity_id']); ?>" ><span class="glyphicon glyphicon-eye-open"></span></a>
                                    <a class="btn btn-success btn-xs" href="<?php echo site_url('admin/activity/edit/' . $row['activity_id']); ?>" ><span class="glyphicon glyphicon-edit"></span></a>
                                </td>
                            </tr>
                        </tbody>
                        <?php
                    }
                } else {
                    ?>
                    <tbody>
                        <tr id="row">
                            <td colspan="6" align="center">Data Kosong</td>
                        </tr>
                    </tbody>
                    <?php
                }
                ?>   
            </table>
        </div>
        <div >
            <?php echo $this->pagination->create_links(); ?>
        </div>
    </div>
</div>