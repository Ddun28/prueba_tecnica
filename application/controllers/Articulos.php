<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Articulos  extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Articulo');
        $this->load->database();
    }

    function index(){}

    public function view() {
        $this->load->view('articulos/read'); // Carga la vista
    }
    
    public function read($page = 1) {
        $itemsPerPage = 5; // Numero de articulos por pagina
        $offset = ($page - 1) * $itemsPerPage;

        $articulos = $this->Articulo->findAll($itemsPerPage, $offset);
        $totalArticulos = $this->Articulo->countAll(); // Total de articulos en la bd

        header('Content-Type: application/json');
        echo json_encode([
            'articulos' => $articulos,
            'total' => $totalArticulos,
            'current_page' => $page,
            'total_pages' => ceil($totalArticulos / $itemsPerPage) // Calcular total de paginas
        ]);
    }

    public function create() {
    
    if ($this->input->server("REQUEST_METHOD") === "POST") {

        $codigo = $this->input->post('codigo');
        $nombre = $this->input->post('nombre');
        
        // Validar que los campos no esten vacios
        if (empty($codigo) || empty($nombre)) {
            echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
            return;
        }

        //Validacion del codigo
        if (!preg_match('/^[a-zA-Z]+$/', $codigo) || strlen($codigo) > 10) {
            echo json_encode(['success' => false, 'message' => 'El codigo debe ser solo texto y tener un maximo de 10 caracteres.']);
            return;
        }

        // Verificar si el codigo ya existe
        if ($this->Articulo->exists($codigo)) {
            echo json_encode(['success' => false, 'message' => 'El código ya existe.']);
            return;
        }

         // Validacion del nombre
         if (!preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
            echo json_encode(['success' => false, 'message' => 'El nombre debe ser solo texto.']);
            return;
        }

        $data = [
            'codigo' => $codigo,
            'nombre' => $nombre
        ];

        if ($this->Articulo->insert($data)) {
            echo json_encode(['success' => true, 'message' => 'Articulo creado con exito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al crear el artículo']);
        }
    } else {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
    }

    public function update() {
        if ($this->input->server("REQUEST_METHOD") === "POST") {
            $id = $this->input->post('id'); 
            $codigo = $this->input->post('codigo');
            $nombre = $this->input->post('nombre');
    
            // Validar que los campos no estén vacíos
            if (empty($id) || empty($codigo) || empty($nombre)) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                return;
            }

            // Validacion del codigo
        if (!preg_match('/^[a-zA-Z]+$/', $codigo) || strlen($codigo) > 10) {
            echo json_encode(['success' => false, 'message' => 'El codigo debe ser solo texto y tener un maximo de 10 caracteres.']);
            return;
        }

        // Validacion del nombre
        if (!preg_match('/^[a-zA-Z\s]+$/', $nombre)) {
            echo json_encode(['success' => false, 'message' => 'El nombre debe ser solo texto.']);
            return;
        }
            
            $data = [
                'codigo' => $codigo,
                'nombre' => $nombre
            ];
    
            if ($this->Articulo->update($id, $data)) {
                echo json_encode(['success' => true, 'message' => 'Artículo actualizado con éxito']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el artículo']);
            }
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        }
    }   
}