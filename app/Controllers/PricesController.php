<?php

namespace App\Controllers;

use App\Models\Prices;
use App\Models\Result;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\HTTP\ResponseInterface;
use Ramsey\Uuid\Uuid;

class PricesController extends ResourceController
{
    /**
     * @var \App\Models\Prices
     */
    protected $price;

    /**
     * @var \App\Models\Result
     */
    protected $result;

    public function __construct()
    {
        $this->price = new Prices();
        $this->result = new Result();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return ResponseInterface
     */
    public function index()
    {
        $result = $this->price->orderBy('created_at', 'desc');
        $search = '';
        if ($this->request->getGet('keyword'))
            $search = strtolower($this->request->getGet('keyword'));
        if ((strpos($search, 'gr') !== false))
            $result
                ->where('size', $search . trim('gr'));
        else if (strlen($search) == 4 && is_numeric($search))
            $result
                ->where('year', $search);
        else if ($search == 'rm')
            $result
                ->where('redmark', 1);
        else
            $result
                ->like('group', $search)
                ->orLike('owner', $search)
                ->orLike('price', $search);
        $result = $result->findAll();

        foreach ($result as $key => $value) {
            $result[$key]['priceFormatted'] = 'IDR ' . number_format($value['price'], 2);
        }

        $this->result->setData($result);

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
            $post = $this->request->getPost();
            $post['id'] = Uuid::uuid4();
            if (isset($post['custom_date'])) $post['created_at'] = $post['custom_date'];
            $this->price->insert($post);

            $this->result->Message = 'Data berhasil ditambahkan';

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
            $post = $this->request->getJSON();
            if (isset($post->custom_date)) $post->created_at = $post->custom_date;
            $this->price->update($id, $post);

            $this->result->Message = 'Data berhasil diubah';

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
        try {
            $this->price->delete($id);

            $this->result->Message = 'Data berhasil dihapus';

            return $this->respond($this->result);
        } catch (\Throwable $th) {
            return $this->failForbidden($th->getMessage());
        }
    }
}
