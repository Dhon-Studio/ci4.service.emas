<?php

namespace App\Controllers;

use App\Models\NewCertiEye;
use App\Models\Result;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;
use Ramsey\Uuid\Uuid;

class NewCertiEyeController extends ResourceController
{
    /**
     * @var \App\Models\CertiEye
     */
    protected $certieye;

    /**
     * @var \App\Models\Result
     */
    protected $result;

    public function __construct()
    {
        $this->certieye = new NewCertiEye();
        $this->result = new Result();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $result = $this->certieye->orderBy('pecahan', 'asc')->findAll();
        foreach ($result as $key => $value) {
            if ($value['pecahan'] == '0.5') $result[$key]['pecahan'] = '0,5';

            if ($value['jual'] == 0) {
                $result[$key]['jualString'] = 'Belum Ada Data';
                $result[$key]['updated_at'] = '-';
            } else $result[$key]['jualString'] = 'Rp ' . number_format($value['jual'], 0, ',', '.');
            if ($value['buyback'] == 0) $result[$key]['buybackString'] = 'Belum Ada Data';
            else $result[$key]['buybackString'] = 'Rp ' . number_format($value['buyback'], 0, ',', '.');
        }
        $this->result->Data = $result;

        return $this->respond($this->result);
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
            $data = $this->request->getPost();
            $exist = $this->certieye->where('pecahan', $data['pecahan'])->first();

            if ($exist == null) {
                $data['id'] = Uuid::uuid4();
                $this->certieye->insert($data);
            } else {
                $this->certieye->update($exist['id'], $data);
            }

            return $this->respondCreated($data);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage());
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
