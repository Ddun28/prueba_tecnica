<?php
    class Articulo extends CI_Model {
        
        public $table = 'articulos';
        public $table_id = 'id';

        public function __construct() {}

        function insert($data){
            $this->db->insert($this->table, $data);
            return $this->db->insert_id();
        }

        function findById($id){
            $this->db->select('codigo, nombre');
            $this->db->from($this->table);
            $this->db->where($this->table_id, $id);
           
            $query = $this->db->get();
            return $query->row();
        }

        function findAll($limit = null, $offset = null) {
            $this->db->select('codigo, nombre, id');
            $this->db->from($this->table);
            
            if ($limit !== null) {
                $this->db->limit($limit, $offset);
            }
            
            $query = $this->db->get();
            return $query->result();
        }
    
        function countAll() {
            return $this->db->count_all($this->table);
        }

        public function update($id, $data) {
            $this->db->where($this->table_id, $id);
            return $this->db->update($this->table, $data);
        }

        //Metodo para comprobar si el codigo existe
        public function exists($codigo) {
            $this->db->where('codigo', $codigo);
            $query = $this->db->get($this->table);
            return $query->num_rows() > 0; // Retorna true si existe
        }        
};