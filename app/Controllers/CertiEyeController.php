<?php

namespace App\Controllers;

use App\Models\CertiEye;
use App\Models\PriceChanges;
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
     * @var \App\Models\PriceChanges
     */
    protected $changes;

    /**
     * @var \App\Models\Result
     */
    protected $result;

    public function __construct()
    {
        $this->model = new CertiEye();
        $this->changes = new PriceChanges();
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

            $order = array('0,5', 1, 2, 3, 5, 10);

            usort($data, function ($a, $b) use ($order) {
                $pos_a = array_search($a['pecahan'], $order);
                $pos_b = array_search($b['pecahan'], $order);
                return $pos_a - $pos_b;
            });

            $changes = $this->changes->orderBy('created_at', 'desc')->first()['changes'];

            foreach ($data as $key => $value) {
                $change = $value['pecahan'] == '0,5' ? $changes / 2 : $changes * $value['pecahan'];
                $data[$key]['jual'] = $value['jual'] + $change;
                $data[$key]['buyback'] = $value['buyback'] + $change;
            }

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
        //
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
        //
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
