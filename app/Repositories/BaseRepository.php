<?php

namespace App\Repositories;

use App\Repositories\BaseRepositoryInterface;
use Illuminate\Support\Facades\Storage;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    public function getAll()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        $result = $this->model->find($id);

        return $result;
    }

    public function create($attributes = [])
    {
        return $this->model->create($attributes);
    }

    public function update($id, $attributes = [])
    {
        $result = $this->find($id);
        if ($result) {
            $result->update($attributes);
            return $result;
        }

        return false;
    }

    public function delete($id)
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();

            return true;
        }

        return false;
    }

    public function saveFile($file, $location, $old_image = ''){
        $extension = $file->getClientOriginalExtension();
        $filename = time().'.'.$extension;
        $path = $file->storeAs('/public/' . $location, $filename);
        $this->deleteFile($old_image);
        return substr($path, 7);
    }

    public function deleteFile($old_image = ''){
        Storage::delete('/public/' . $old_image);
    }
}
