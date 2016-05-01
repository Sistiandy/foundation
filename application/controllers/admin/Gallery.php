<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Gallery controllers class
 *
 * @package     SYSCMS
 * @subpackage  Controllers
 * @category    Controllers
 * @author      Sistiandy Syahbana nugraha <sistiandy.web.id>
 */
class Gallery extends CI_Controller {

    public function __construct() {
        parent::__construct(TRUE);
        if ($this->session->userdata('logged') == NULL) {
            header("Location:" . site_url('admin/auth/login') . "?location=" . urlencode($_SERVER['REQUEST_URI']));
        }
        $this->load->model(array('Gallery_model', 'Activity_log_model'));
        $this->load->library('upload');
    }

    // Gallery view in list
    public function index($offset = NULL) {
        $this->load->library('pagination');
        $data['gallery'] = $this->Gallery_model->get(array('limit' => 10, 'offset' => $offset, 'status' => TRUE));
        $config['base_url'] = site_url('admin/gallery/index');
        $config['total_rows'] = count($this->Gallery_model->get(array('status' => TRUE)));
        $this->pagination->initialize($config);

        $data['title'] = 'Galeri';
        $data['main'] = 'admin/gallery/gallery_list';
        $this->load->view('admin/layout', $data);
    }

    function detail($id = NULL) {
        if ($this->Gallery_model->get(array('id' => $id)) == NULL) {
            redirect('admin/gallery');
        }
        $data['gallery'] = $this->Gallery_model->get(array('id' => $id));
        $data['title'] = 'Detail galeri';
        $data['main'] = 'admin/gallery/gallery_view';
        $this->load->view('admin/layout', $data);
    }

    // Add Gallery and Update
    public function add($id = NULL) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('gallery_title', 'Title', 'trim|required|xss_clean');
        $this->form_validation->set_rules('gallery_description', 'Description', 'trim|required|xss_clean');
        $this->form_validation->set_rules('gallery_is_published', 'Publish Status', 'trim|required|xss_clean');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>', '</div>');
        $data['operation'] = is_null($id) ? 'Tambah' : 'Sunting';

        if ($_POST AND $this->form_validation->run() == TRUE) {
            if (!empty($_FILES['inputGambar']['name'])) {
                $params['gallery_image'] = $this->do_upload();
            } elseif ($this->input->post('inputGambarExisting')) {
                $params['gallery_image'] = $this->input->post('inputGambarExisting');
            } else {
                if ($this->input->post('gallery_id')) {
                    $params['gallery_image'] = $this->input->post('inputGambarCurrent');
                } else {
                    $params['gallery_image'] = '';
                }
            }

            if ($this->input->post('gallery_id')) {
                $params['gallery_id'] = $this->input->post('gallery_id');
            } else {
                $params['gallery_input_date'] = date('Y-m-d H:i:s');
            }

            $params['user_id'] = $this->session->userdata('user_id');
            $params['gallery_last_update'] = date('Y-m-d H:i:s');
            $params['gallery_published_date'] = ($this->input->post('gallery_published_date')) ? $this->input->post('gallery_published_date') : date('Y-m-d H:i:s');
            $params['gallery_title'] = $this->input->post('gallery_title');
            $params['gallery_description'] = stripslashes($this->input->post('gallery_description'));
            $params['gallery_is_published'] = $this->input->post('gallery_is_published');
            $status = $this->Gallery_model->add($params);


            // activity log
            $this->Activity_log_model->add(
                    array(
                        'log_date' => date('Y-m-d H:i:s'),
                        'user_id' => $this->session->userdata('user_id'),
                        'log_module' => 'Galeri',
                        'log_action' => $data['operation'],
                        'log_info' => 'ID:null;Title:' . $params['gallery_title']
                    )
            );

            $this->session->set_flashdata('success', $data['operation'] . ' galeri berhasil');
            redirect('admin/gallery');
        } else {
            if ($this->input->post('gallery_id')) {
                redirect('admin/gallery/edit/' . $this->input->post('gallery_id'));
            }

            // Edit mode
            if (!is_null($id)) {
                $data['gallery'] = $this->Gallery_model->get(array('id' => $id));
            }
            $data['title'] = $data['operation'] . ' Galeri';
            $data['main'] = 'admin/gallery/gallery_add';
            $this->load->view('admin/layout', $data);
        }
    }

    // Delete Gallery
    public function delete($id = NULL) {
        if ($_POST) {
            $this->Gallery_model->delete($this->input->post('del_id'));
            // activity log
            $this->Activity_log_model->add(
                    array(
                        'log_date' => date('Y-m-d H:i:s'),
                        'user_id' => $this->session->userdata('user_id'),
                        'log_module' => 'Galeri',
                        'log_action' => 'Hapus',
                        'log_info' => 'ID:' . $this->input->post('del_id') . ';Title:' . $this->input->post('del_name')
                    )
            );
            $this->session->set_flashdata('success', 'Hapus galeri berhasil');
            redirect('admin/gallery');
        } elseif (!$_POST) {
            $this->session->set_flashdata('delete', 'Delete');
            redirect('admin/gallery/edit/' . $id);
        }
    }

}

/* End of file gallery.php */
/* Location: ./application/controllers/admin/gallery.php */
