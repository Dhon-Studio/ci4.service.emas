<?php

namespace App\Controllers;

use App\Models\CertiEye;
use App\Models\Result;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class CertiEyeController extends ResourceController
{
    /**
     * @var \App\Models\CertiEye
     */
    protected $model;

    /**
     * @var \App\Models\Result
     */
    protected $result;

    public function __construct()
    {
        $this->model = new CertiEye();
        $this->result = new Result();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return ResponseInterface
     */
    public function index()
    {
        try {
            $data = $this->model->findAll();

            $this->result->Data = json_decode(json_encode($data, JSON_NUMERIC_CHECK), true);

            return $this->respond($this->result);
        } catch (\Throwable $th) {
            return $this->failForbidden($th->getMessage());
        }
    }

    /**
     * Return the properties of a resource object
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return ResponseInterface
     */
    public function create()
    {
        try {
            $id = $this->model->insert($this->request->getPost());

            $this->result->Data = $this->model->where('id', $id)->first();

            return $this->respond($this->result);
        } catch (\Throwable $th) {
            return $this->failForbidden($th->getMessage());
        }
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        try {
            $this->model->updateBatch($this->request->getJsonVar());

            $this->result->Data = $this->model->where('id', $id)->first();

            return $this->respond($this->result);
        } catch (\Throwable $th) {
            return $this->failForbidden($th->getMessage());
        }
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        //
    }
}
