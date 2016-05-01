<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Activity controllers class
 *
 * @package     SYSCMS
 * @subpackage  Controllers
 * @category    Controllers
 * @author      Sistiandy Syahbana nugraha <sistiandy.web.id>
 */
class Activity extends CI_Controller {

    public function __construct() {
        parent::__construct(TRUE);
        if ($this->session->userdata('logged') == NULL) {
            header("Location:" . site_url('admin/auth/login') . "?location=" . urlencode($_SERVER['REQUEST_URI']));
        }
        $this->load->model(array('Activity_model', 'Activity_log_model'));
        $this->load->library('upload');
    }

    // Activity view in list
    public function index($offset = NULL) {
        $this->load->library('pagination');
        $data['activity'] = $this->Activity_model->get(array('limit' => 10, 'offset' => $offset, 'status' => TRUE));
        $data['category'] = $this->Activity_model->get_category();
        $config['base_url'] = site_url('admin/activity/index');
        $config['total_rows'] = count($this->Activity_model->get(array('status' => TRUE)));
        $this->pagination->initialize($config);

        $data['title'] = 'Kegiatan';
        $data['main'] = 'admin/activity/activity_list';
        $this->load->view('admin/layout', $data);
    }

    function detail($id = NULL) {
        if ($this->Activity_model->get(array('id' => $id)) == NULL) {
            redirect('admin/activity');
        }
        $data['activity'] = $this->Activity_model->get(array('id' => $id));
        $data['title'] = 'Detail kegiatan';
        $data['main'] = 'admin/activity/activity_view';
        $this->load->view('admin/layout', $data);
    }

    // Category view in list
    public function category($offset = NULL) {
        $this->load->library('pagination');
        $data['categories'] = $this->Activity_model->get_category(array('limit' => 10, 'offset' => $offset));
        $config['base_url'] = site_url('admin/activity/category');
        $config['total_rows'] = $this->db->count_all('activity_category');
        $this->pagination->initialize($config);
        $data['title'] = 'Kategori Kegiatan';
        $data['main'] = 'admin/activity/category_list';
        $this->load->view('admin/layout', $data);
    }

    // Add Activity and Update
    public function add($id = NULL) {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('activity_title', 'Title', 'trim|required|xss_clean');
        $this->form_validation->set_rules('activity_description', 'Description', 'trim|required|xss_clean');
        $this->form_validation->set_rules('category_id_new', 'Kategori', 'is_unique[activity_category.category_name]');
        $this->form_validation->set_rules('activity_is_published', 'Publish Status', 'trim|required|xss_clean');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>', '</div>');
        $data['operation'] = is_null($id) ? 'Tambah' : 'Sunting';

        if ($_POST AND $this->form_validation->run() == TRUE) {
            if (!empty($_FILES['inputGambar']['name'])) {
                $params['activity_image'] = $this->do_upload();
            } elseif ($this->input->post('inputGambarExisting')) {
                $params['activity_image'] = $this->input->post('inputGambarExisting');
            } else {
                if ($this->input->post('activity_id')) {
                    $params['activity_image'] = $this->input->post('inputGambarCurrent');
                } else {
                    $params['activity_image'] = '';
                }
            }

            if ($this->input->post('activity_id')) {
                $params['activity_id'] = $this->input->post('activity_id');
            } else {
                $params['activity_input_date'] = date('Y-m-d H:i:s');
            }

            $params['user_id'] = $this->session->userdata('user_id');
            $params['activity_last_update'] = date('Y-m-d H:i:s');
            $params['activity_published_date'] = ($this->input->post('activity_published_date')) ? $this->input->post('activity_published_date') : date('Y-m-d H:i:s');
            $params['activity_title'] = $this->input->post('activity_title');
            $params['activity_description'] = stripslashes($this->input->post('activity_description'));
            $params['activity_content'] = stripslashes($this->input->post('activity_content'));
            $params['category_id'] = $this->input->post('category_id');
            $params['activity_is_published'] = $this->input->post('activity_is_published');
            $params['activity_is_commentable'] = $this->input->post('activity_is_commentable');
            $status = $this->Activity_model->add($params);


            // activity log
            $this->Activity_log_model->add(
                    array(
                        'log_date' => date('Y-m-d H:i:s'),
                        'user_id' => $this->session->userdata('user_id'),
                        'log_module' => 'Kegiatan',
                        'log_action' => $data['operation'],
                        'log_info' => 'ID:null;Title:' . $params['activity_title']
                    )
            );

            $this->session->set_flashdata('success', $data['operation'] . ' kegiatan berhasil');
            redirect('admin/activity');
        } else {
            if ($this->input->post('activity_id')) {
                redirect('admin/activity/edit/' . $this->input->post('activity_id'));
            }

            // Edit mode
            if (!is_null($id)) {
                $data['activity'] = $this->Activity_model->get(array('id' => $id));
            }
            $data['category'] = $this->get_category();
            $data['title'] = $data['operation'] . ' Kegiatan';
            $data['main'] = 'admin/activity/activity_add';
            $this->load->view('admin/layout', $data);
        }
    }

    // Add Category
    public function add_category($id = NULL) {
        $this->load->library('form_validation');
        if ($this->input->post('category_id')) {
            $this->form_validation->set_rules('category_name', 'Name', 'trim|required|xss_clean');
        } else {
            $this->form_validation->set_rules('category_name', 'Name', 'trim|required|xss_clean|is_unique[activity_category.category_name]');
        }
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>', '</div>');
        $data['operation'] = is_null($id) ? 'Tambah' : 'Sunting';

        if ($_POST AND $this->form_validation->run() == TRUE) {
            if ($this->input->post('category_id')) {
                $params['category_id'] = $this->input->post('category_id');
            }
            $params['category_name'] = $this->input->post('category_name');
            $res = $this->Activity_model->add_category($params);

            // activity log
            $this->Activity_log_model->add(
                    array(
                        'log_date' => date('Y-m-d H:i:s'),
                        'user_id' => $this->session->userdata('user_id'),
                        'log_module' => 'Kegiatan',
                        'log_action' => $data['operation'],
                        'log_info' => 'ID:null;Title:' . $params['category_name']
                    )
            );

            if ($this->input->is_ajax_request()) {
                echo $res;
            } else {
                $this->session->set_flashdata('success', $data['operation'] . ' kategori berhasil');
                redirect('admin/activity/category');
            }
        } else {
            if ($this->input->post('category_id')) {
                redirect('admin/activity/category/edit/' . $this->input->post('category_id'));
            }

            // Edit mode
            if (!is_null($id)) {
                if ($id == 1) {
                    redirect('admin/activity/category/');
                }
                $data['category'] = $this->Activity_model->get_category(array('id' => $id));
            }
            $data['title'] = 'Tambah Kategori';
            $data['main'] = 'admin/activity/category_add';
            $this->load->view('admin/layout', $data);
        }
    }

    protected function get_category() {
        $res = json_encode($this->Activity_model->get_category());
        return $res;
    }

    // Delete Activity
    public function delete($id = NULL) {
        if ($_POST) {
            $this->Activity_model->delete($this->input->post('del_id'));
            // activity log
            $this->Activity_log_model->add(
                    array(
                        'log_date' => date('Y-m-d H:i:s'),
                        'user_id' => $this->session->userdata('user_id'),
                        'log_module' => 'Kegiatan',
                        'log_action' => 'Hapus',
                        'log_info' => 'ID:' . $this->input->post('del_id') . ';Title:' . $this->input->post('del_name')
                    )
            );
            $this->session->set_flashdata('success', 'Hapus kegiatan berhasil');
            redirect('admin/activity');
        } elseif (!$_POST) {
            $this->session->set_flashdata('delete', 'Delete');
            redirect('admin/activity/edit/' . $id);
        }
    }

    // Delete Category
    public function delete_category($id = NULL) {
        if ($_POST) {
            $params['category_id'] = '1';
            $this->Activity_model->set_default_category($id, $params);

            $this->Activity_model->delete_category($this->input->post('del_id'));
            // activity log
            $this->Activity_log_model->add(
                    array(
                        'log_date' => date('Y-m-d H:i:s'),
                        'user_id' => $this->session->userdata('user_id'),
                        'log_module' => 'Kategori Kegiatan',
                        'log_action' => 'Hapus',
                        'log_info' => 'ID:' . $this->input->post('del_id') . ';Title:' . $this->input->post('del_name')
                    )
            );
            $this->session->set_flashdata('success', 'Hapus kategori kegiatan berhasil');
            redirect('admin/activity/category');
        } elseif (!$_POST) {
            $this->session->set_flashdata('delete', 'Delete');
            redirect('admin/activity/category/edit/' . $id);
        }
    }

}

/* End of file activity.php */
/* Location: ./application/controllers/admin/activity.php */
