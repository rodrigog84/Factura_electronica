<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Accionistas_model extends CI_Model {


    /*function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }*/
    
    function get_all($start, $limit)
    {
        $query = $this->db->query("SELECT c . * , tc.id AS idtc, tc.nombre AS tcnombre
            FROM  accionistas c
            LEFT JOIN accionistas tc ON c.estado = tc.id
            LIMIT {$start}, {$limit} ");
        return $query;
    }

    function get_all_by_ftipo($start, $limit, $fTipo)
    {
        $query = $this->db->query("SELECT c . * , tc.id AS idtc, tc.nombre AS tcnombre
            FROM  accionistas c
            INNER JOIN accionistas tc ON c.estado = tc.id
            WHERE tc.id = {$fTipo}
            LIMIT {$start}, {$limit} ");
        return $query;
    }
}