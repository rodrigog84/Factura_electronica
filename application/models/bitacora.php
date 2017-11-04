<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bitacora extends CI_Model {

    var $operacion  = '';
    var $tabla      = '';
    var $usuario    = '';
    var $host       = '';
    var $modificado = '';
    var $id_tabla      = '';


    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }

    function getOperacion($op){
        $opr = "";
        if ($op == "I") {
            $opr = "INSERTAR";
        } else if( $op == "M") {
            $opr = "MODIFICAR";
        } else if ($op == "D") {
            $opr = "DESACTIVAR";
        } else if ($op == "A") {
            $opr = "ACTIVAR";
        } 
        return $opr;
    }

    function logger($op, $tabla, $id)
    {
        $this->operacion  = $this->getOperacion($op); 
        $this->tabla      = $tabla;
        $this->id_tabla   = $id;
        $this->usuario    = $this->session->userdata('id_usu');
        $this->host       = $this->get_client_ip();
        $this->modificado = date("Y-m-d H:i:s");

        $this->db->insert('bitacora', $this);
    }
    function get_client_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {   //to check ip is pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    } 
}
?>