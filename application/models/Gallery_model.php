<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** 
* Gallery Model Class
 *
 * @package     SYSCMS
 * @subpackage  Models
 * @category    Models
 * @author      Sistiandy Syahbana nugraha <sistiandy.web.id>
 */

class Gallery_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // Get From Databases
    function get($params = array())
    {
        if(isset($params['id']))
        {
            $this->db->where('gallery.gallery_id', $params['id']);
        }

        if(isset($params['limit']))
        {
            if(!isset($params['offset']))
            {
                $params['offset'] = NULL;
            }

            $this->db->limit($params['limit'], $params['offset']);
        }

        if(isset($params['order_by']))
        {
            $this->db->order_by($params['order_by'], 'desc');
        }
        else
        {
            $this->db->order_by('gallery_published_date', 'desc');
        }

        $this->db->select('gallery.gallery_id, gallery_title, gallery_description, gallery_image,
            gallery_is_published, gallery_published_date, gallery_input_date, galler_last_update');
        $this->db->select('user_user_id, user_name');
        $this->db->join('user', 'user.user_id = gallery.user_user_id', 'left');
        $res = $this->db->get('gallery');

        if(isset($params['id']))
        {
            return $res->row_array();
        }
        else
        {
            return $res->result_array();
        }
    }

    // Add and update to database
    function add($data = array()) {
        
         if(isset($data['gallery_id'])) {
            $this->db->set('gallery_id', $data['gallery_id']);
        }
        
         if(isset($data['gallery_title'])) {
            $this->db->set('gallery_title', $data['gallery_title']);
        }
        
         if(isset($data['gallery_description'])) {
            $this->db->set('gallery_description', $data['gallery_description']);
        }
        
         if(isset($data['gallery_image'])) {
            $this->db->set('gallery_image', $data['gallery_image']);
        }
        
         if(isset($data['user_id'])) {
            $this->db->set('user_user_id', $data['user_id']);
        }
        
         if(isset($data['gallery_is_published'])) {
            $this->db->set('gallery_is_published', $data['gallery_is_published']);
        }
        
         if(isset($data['gallery_published_date'])) {
            $this->db->set('gallery_published_date', $data['gallery_published_date']);
        }
        
         if(isset($data['gallery_input_date'])) {
            $this->db->set('gallery_input_date', $data['gallery_input_date']);
        }
        
         if(isset($data['gallery_last_update'])) {
            $this->db->set('gallery_last_update', $data['gallery_last_update']);
        }
        
        if (isset($data['gallery_id'])) {
            $this->db->where('gallery_id', $data['gallery_id']);
            $this->db->update('gallery');
            $id = $data['gallery_id'];
        } else {
            $this->db->insert('gallery');
            $id = $this->db->insert_id();
        }

        $status = $this->db->affected_rows();
        return ($status == 0) ? FALSE : $id;
    }
    
    // Delete to database
    function delete($id) {
        $this->db->where('gallery_id', $id);
        $this->db->delete('gallery');
    }
    
}
