<?php

namespace App\Models;

class Result
{
    public $Data = null;
    public $Success = true;
    public $Message = "";

    public function setData($value)
    {
        if ($this->Data !== $value) { // Check if the value actually changed
            $this->Data = $value;
            $this->_onDataChange(); // Call a method to handle the change
        }
    }

    private function _onDataChange()
    {
        $this->Message = $this->Data != null && $this->Data != [] ? "Data berhasil didapatkan" : "Data tidak ditemukan";
    }
}
