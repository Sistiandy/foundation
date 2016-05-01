<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/** 
* Activity Model Class
 *
 * @package     SYSCMS
 * @subpackage  Models
 * @category    Models
 * @author      Sistiandy Syahbana nugraha <sistiandy.web.id>
 */

class Activity_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // Get From Databases
    function get($params = array())
    {
        if(isset($params['id']))
        {
            $this->db->where('activity.activity_id', $params['id']);
        }
        if(isset($params['category_id']))
        {
            $this->db->where('activity.category_id', $params['category_id']);
        }
        if(isset($params['user_id']))
        {
            $this->db->where('activity.user_id', $params['user_id']);
        }
        if(isset($params['activity_title']))
        {
            $this->db->like('activity_title', $params['activity_title']);
        }
        if(isset($params['activity_is_published']))
        {
            $this->db->where('activity_is_published', $params['activity_is_published']);
        }
        if(isset($params['activity_published_date']))
        {
            $this->db->where('activity_published_date', $params['activity_published_date']);
        }
        if(isset($params['date_start']) AND isset($params['date_end']))
        {
            $this->db->where('activity_published_date', $params['date_start']);
            $this->db->or_where('activity_published_date', $params['date_end']);
        }

        if(isset($params['status']))
        {
            $this->db->where('activity_is_published', $params['status']);
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
            $this->db->order_by('activity_last_update', 'desc');
        }

        $this->db->select('activity.activity_id, activity_title, activity_description, activity_image,
            activity_published_date, activity_is_published, activity_category_category_id, 
            activity_category.category_name, user_user_id, user.user_name, activity_input_date,
            activity_last_update');
        $this->db->join('activity_category', 'activity_category.category_id = activity.activity_category_category_id', 'left');
        $this->db->join('user', 'user.user_id = activity.user_user_id', 'left');
        $res = $this->db->get('activity');

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
        
         if(isset($data['activity_id'])) {
            $this->db->set('activity_id', $data['activity_id']);
        }
        
         if(isset($data['activity_title'])) {
            $this->db->set('activity_title', $data['activity_title']);
        }
        
         if(isset($data['activity_description'])) {
            $this->db->set('activity_description', $data['activity_description']);
        }
        
         if(isset($data['activity_published_date'])) {
            $this->db->set('activity_published_date', $data['activity_published_date']);
        }
        
         if(isset($data['activity_image'])) {
            $this->db->set('activity_image', $data['activity_image']);
        }
        
         if(isset($data['activity_input_date'])) {
            $this->db->set('activity_input_date', $data['activity_input_date']);
        }
        
         if(isset($data['activity_last_update'])) {
            $this->db->set('activity_last_update', $data['activity_last_update']);
        }
        
         if(isset($data['activity_is_published'])) {
            $this->db->set('activity_is_published', $data['activity_is_published']);
        }
        
         if(isset($data['user_id'])) {
            $this->db->set('user_user_id', $data['user_id']);
        }
        
         if(isset($data['category_id'])) {
            $this->db->set('activity_category_category_id', $data['category_id']);
        }
        
        if (isset($data['activity_id'])) {
            $this->db->where('activity_id', $data['activity_id']);
            $this->db->update('activity');
            $id = $data['activity_id'];
        } else {
            $this->db->insert('activity');
            $id = $this->db->insert_id();
        }

        $status = $this->db->affected_rows();
        return ($status == 0) ? FALSE : $id;
    }
    
    // Delete to database
    function delete($id) {
        $this->db->where('activity_id', $id);
        $this->db->delete('activity');
    }
    
    // Get category from database
    function get_category($params = array())
    {
        if(isset($params['id']))
        {
            $this->db->where('category_id', $params['id']);
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
            $this->db->order_by('category_id', 'desc');
        }

        $this->db->select('category_id, category_name');
        $res = $this->db->get('activity_category');

        if(isset($params['id']))
        {
            return $res->row_array();
        }
        else
        {
            return $res->result_array();
        }
    }
    
    // Add and Update category to database
    function add_category($data = array()) {
        $param = array(
            'category_name' => $data['category_name'],
        );
        $this->db->set($param);
        
        if (isset($data['category_id'])) {
            $this->db->where('category_id', $data['category_id']);
            $this->db->update('activity_category');
            $id = $data['category_id'];
        } else {
            $this->db->insert('activity_category');
            $id = $this->db->insert_id();
        }

        $status = $this->db->affected_rows();
        return ($status == 0) ? FALSE : $id;
    }
    
    // Delete category to database
    function delete_category($id) {
        $this->db->where('category_id', $id);
        $this->db->delete('activity_category');
    }

    // Set Default category
    function set_default_category($id,$params) {
        $this->db->where('activity_category_category_id', $id);
        $this->db->update('activity', $params);
    }
    
}
