<?php

namespace App\Controllers;

use App\Models\CertiEye;
use App\Models\PriceChanges;
use App\Models\Result;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use DOMDocument;
use DOMXPath;
use GuzzleHttp\Client;
use Ramsey\Uuid\Uuid;

class PriceChangesController extends ResourceController
{
    /**
     * @var \App\Models\PriceChanges
     */
    protected $model;

    /**
     * @var \App\Models\CertiEye
     */
    protected $certi;

    /**
     * @var \App\Models\Result
     */
    protected $result;

    public function __construct()
    {
        $this->model = new PriceChanges();
        $this->certi = new CertiEye();
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
            $data = $this->model->orderBy('created_at', 'desc')->findAll();

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
        $change = $this->model->orderBy('created_at', 'desc')->first()['changes'];
        $certi1gr = $this->certi->where('pecahan', 1)->first()['jual'] + $change;

        $httpClient = new Client();
        $response = $httpClient->get('https://www.logammulia.com/id/harga-emas-hari-ini');
        $htmlString = (string) $response->getBody();
        //add this line to suppress any warnings
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);
        $xpath = new DOMXPath($doc);

        $datas = $xpath->evaluate('//table[@class="table table-bordered"]');
        $extractedDatas = [];
        foreach ($datas as $data) {
            array_push($extractedDatas, $data->textContent . PHP_EOL);
        }

        $crawled = explode("\n\n\n", $extractedDatas[0]);

        $price = 0;
        foreach ($crawled as $crawl) {
            if (strpos($crawl, "1 gr") !== false) {
                $price = (int)str_replace(",", "", explode("\n", $crawl)[1]);
                break;
            }
        }

        if ($price != $certi1gr) {
            $post['id'] = Uuid::uuid4();
            $post['changes'] = $change + $price - $certi1gr + 25000;
            $id = $this->model->insert($post);
        }

        $this->result->Data = $this->model->where('id', $id)->first();

        return $this->respond($this->result);
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
            $changes = $this->model->orderBy('created_at', 'desc')->first()['changes'];

            $post = $this->request->getPost();
            $post['id'] = Uuid::uuid4();
            $post['changes'] = $changes + $post['changes'];
            $id = $this->model->insert($post);

            $this->result->Data = $this->model->where('id', $id)->first();
            $this->result->Data['changes'] = $this->request->getPost('changes');

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
