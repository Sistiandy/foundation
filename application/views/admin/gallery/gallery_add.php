<script type="text/javascript">
    var token_name = "<?php echo $this->security->get_csrf_token_name() ?>";
    var csrf_hash = "<?php echo $this->security->get_csrf_hash() ?>";
</script>
<script src="<?php echo base_url('/media/js/modalpopup.js'); ?>"></script>
<link href="<?php echo base_url('/media/css/modalpopup.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('media/css/jasny-bootstrap.min.css'); ?>" rel="stylesheet" media="screen">
<script src="<?php echo base_url('media/js/jasny-bootstrap.min.js'); ?>"></script>

<?php $this->load->view('admin/tinymce_init'); ?>

<?php
if (isset($gallery)) {
    $inputJudulValue = $gallery['gallery_title'];
    $inputRingkasanValue = $gallery['gallery_description'];
} else {
    $inputJudulValue = set_value('gallery_title');
    $inputRingkasanValue = set_value('gallery_description');
}
?>
<div class="col-md-12 col-sm-12 col-xs-12 main post-inherit">
    <div class="x_panel post-inherit">
        <?php if (!isset($gallery)) echo validation_errors(); ?>
        <?php echo form_open_multipart(current_url()); ?>
        <div>
            <h3><?php echo $operation; ?> Galeri</h3><br>
        </div>

        <div class="row">
            <div class="col-sm-9 col-md-9">
                <?php if (isset($gallery)): ?>
                    <input type="hidden" name="gallery_id" value="<?php echo $gallery['gallery_id']; ?>" />
                <?php endif; ?>
                <label >Judul Galeri *</label>
                <input name="gallery_title" placeholder="Judul Galeri" type="text" class="form-control" value="<?php echo $inputJudulValue; ?>"><br>
                <label >Deskripsi Galeri *</label>
                <textarea name="gallery_description" rows="10" class="mce-init"><?php echo $inputRingkasanValue; ?></textarea><br />
                <p style="color:#9C9C9C;margin-top: 5px"><i>*) Field Wajib Diisi</i></p>
                <div class="form-group">
                    <div class="box4">
                        <label for="image">Unggah File Gambar</label>
                        <!--<input id="image" type="file" name="inputGambar">-->
                        <a name="action" id="openmm"type="submit" value="save" class="btn btn-info"><i class="fa fa-upload"></i> Upload</a>
                        <div style="display: none;" ><a href="" id="opentiny">Open</a></div>
                        <input type="hidden" name="inputGambarExisting">
                        <input type="hidden" name="inputGambarExistingId">

                        <?php if (isset($gallery) AND !empty($gallery['gallery_image'])): ?>
                            <div class="thumbnail mt10">
                                <img class="previewTarget" src="<?php echo $gallery['gallery_image']; ?>" style="width: 280px !important" >
                            </div>
                            <input type="hidden" name="inputGambarCurrent" value="<?php echo $gallery['gallery_image']; ?>">
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-9 col-md-3">
                <div class="form-group">
                    <label>Status Publikasi</label>
                    <div class="radio">
                        <label>
                            <input type="radio" name="gallery_is_published" value="0" <?php echo ($inputStatus == 0) ? 'checked' : ''; ?>> Draft
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="gallery_is_published" value="1" <?php echo ($inputStatus == 1) ? 'checked' : ''; ?>> Terbit
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="inputPublish">Tanggal Publikasi </label>
                        <input id="inputPublish" placeholder="Tanggal Publikasi" name="gallery_published_date" type="text" class="form-control datepicker hasDatepickerr" value="<?php echo $inputPublishValue; ?>">
                    </div>
                </div>
                <hr>
                <div class="form-group" ng-controller="CategoriesCtrl">
                    <label>Kategori</label>
                    <div class=" input-group">
                        <select name="category_id" class="form-control" style="position:initial;" id="selectCat">
                            <option ng-repeat="category in categories" ng-selected="category_data.index == category.category_id" value="{{category.category_id}}">{{category.category_name}}</option>
                        </select>
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#category" aria-expanded="false">
                                <span class="fa fa-plus"></span>
                            </button>
                        </div>
                    </div>
                    <div class="collapse" id="category">
                        <div class="input-group">
                            <input class="form-control" placeholder="Tambah Baru" id="appendedInputButton" type="text" ng-model="categoryText">
                            <div class="input-group-btn">
                                <button class="btn btn-default simpan" type="button" ng-click="addCategory()" ng-disabled="!(!!categoryText)">Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    <button name="action" type="submit" value="save" class="btn btn-success btn-form"><i class="fa fa-check"></i> Simpan</button>
                    <a href="<?php echo site_url('admin/gallery'); ?>" class="btn btn-info btn-form"><i class="fa fa-arrow-left"></i> Batal</a>
                    <?php if (isset($gallery)): ?>
                        <a href="<?php echo site_url('admin/gallery/delete/' . $gallery['gallery_id']); ?>" class="btn btn-danger btn-form" ><i class="fa fa-trash"></i> Hapus</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<?php if (isset($gallery)): ?>
    <!-- Delete Confirmation -->
    <div class="modal fade" id="confirm-del">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><b><span class="fa fa-warning"></span> Konfirmasi Penghapusan</b></h4>
                </div>
                <div class="modal-body">
                    <p>Data yang dipilih akan dihapus oleh sistem, apakah anda yakin?;</p>
                </div>
                <?php echo form_open('admin/gallery/delete/' . $gallery['gallery_id']); ?>
                <div class="modal-footer">
                    <a><button style="float: right;margin-left: 10px" type="button" class="btn btn-default" data-dismiss="modal">Tidak</button></a>
                    <input type="hidden" name="del_id" value="<?php echo $gallery['gallery_id'] ?>" />
                    <input type="hidden" name="del_name" value="<?php echo $gallery['gallery_title'] ?>" />
                    <button type="submit" class="btn btn-danger"> Ya</button>
                </div>
                <?php echo form_close(); ?>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <?php if ($this->session->flashdata('delete')) { ?>
        <script type="text/javascript">
            $(window).load(function() {
                $('#confirm-del').modal('show');
            });
        </script>
    <?php }
    ?>
<?php endif; ?>