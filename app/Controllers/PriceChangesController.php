<?php

namespace App\Controllers;

use App\Models\CertiEye;
use App\Models\PriceChanges;
use App\Models\Result;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use DateTime;
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
            $dataPre = $this->model->orderBy('created_at', 'desc');

            $data = null;
            if ($this->request->getGet('array')) {
                $data = $dataPre->findAll();

                foreach ($data as $key => $value) {
                    $nextIndex = $key + 1;
                    if (isset($data[$nextIndex])) {
                        $data[$key]['changes'] -= $data[$nextIndex]['changes'];
                    }
                    $data[$key]['changesFormatted'] = 'IDR ' . number_format($data[$key]['changes'], 2);
                }
            } else {
                $dataPre->first();
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
        $change = $this->model->orderBy('created_at', 'desc')->first()['changes'];
        $certi1gr = $this->certi->where('pecahan', 1)->first()['jual'] + $change;

        $httpClient = new Client();
        $response = $httpClient->get('https://anekalogam.co.id/id');
        $htmlString = (string) $response->getBody();
        //add this line to suppress any warnings
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML($htmlString);
        $xpath = new DOMXPath($doc);

        $datas = $xpath->evaluate('//table[@class="table lm-table"]');
        $extractedDatas = [];
        foreach ($datas as $data) {
            array_push($extractedDatas, $data->textContent . PHP_EOL);
        }

        $crawled = explode(
            "%0A%09%09%09%09%09%09%09%09%09%09++%0A%09%09%09%09%09++++%0A%09%09%09%09%09%09%09",
            urlencode($extractedDatas[0])
        );

        $price = 0;
        foreach ($crawled as $crawl) {
            if (strpos($crawl, "1gram") !== false) {
                $price = (int)str_replace(
                    [".", "%0A%09%09%09%09%09++++%09%0A%09%09%09%09%09++++%0A%09%09%09%09%09++++%0A%09%09%09%09%09++++%09%0A++++++++++++++++++++++++++++%09Rp%0A%09%09%09%09%09++++%09%091.370.000%0A%09%09%09%09%09++++%09%0A%09%09%09%09%09++++%0A%09%09%09%09%09++"],
                    ["", ""],
                    explode("%0A%09%09%09%09%09++++%0A%0A%09%09%09%09%09++++%0A%09%09%09%09%09++++%09%0A++++++++++++++++++++++++++++%09Rp%0A%09%09%09%09%09++++%09%09", $crawl)[1]
                ) + 25000;
                break;
            }
        }

        if ($price != $certi1gr) {
            $post['id'] = Uuid::uuid4();
            $post['changes'] = $change + $price - $certi1gr;
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
            $changeExist = $this->model->orderBy('created_at', 'desc')->findAll();
            $changes = count($changeExist) > 0 ? $changeExist[0]['changes'] : 0;

            $date = json_decode(json_encode(new DateTime()));
            $onlyDate = substr($date->date, 0, 10);
            foreach ($changeExist as $key => $value) {
                if (substr($value['created_at'], 0, 10) == $onlyDate) return $this->failForbidden('Tanggal sudah terdapat perubahan');
                else continue;
            }

            $post = $this->request->getPost();
            $post['id'] = Uuid::uuid4();
            $post['changes'] = $changes + $post['changes'];
            if (isset($post['custom_date']) && $post['custom_date']) $post['created_at'] = $post['custom_date'];
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
        try {
            $update = $this->request->getJSON();

            $all = $this->model->orderBy('created_at', 'desc')->findAll();
            $index = array_search($id, array_column($all, 'id'));
            $nextIndex = $index + 1;
            if (isset($all[$nextIndex])) {
                $update->changes = $all[$nextIndex]['changes'] + $update->changes;
            }
            if (isset($update->custom_date) && $update->custom_date) $update->created_at = $update->custom_date;
            $this->model->update($id, $update);

            $result = $this->model->where('id', $id)->first();

            $this->result->Data = $result;
            $this->result->Message = "Berhasil diubah";

            return $this->respond($this->result);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage());
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
