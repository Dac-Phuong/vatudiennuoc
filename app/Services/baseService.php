<?php
namespace App\Services;

use App\Traits\ThrowsValidationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

abstract class BaseService
{
    use ThrowsValidationException;
    protected $model;

    abstract protected function setModel();
    public function __construct()
    {
        $this->setModel();
    }
    protected function getModel()
    {
        return $this->model;
    }

    /**
     * Xử lý lỗi và ghi log lỗi
     *
     * @param \Exception $exception
     * @return void
     */
    protected function handleException(\Throwable $th)
    {
        Log::error(date('Y-m-d H:i:s') . ' -Error in ' . $th->getFile() . ' on line ' . $th->getLine() . ': ' . $th->getMessage());
        throw $th;
    }

    public function create(array $data)
    {
        try {
            return $this->getModel()->create($data);
        } catch (\Throwable $th) {
            $this->handleException($th);
            return false;
        }
    }

    public function findById(int $id)
    {
        try {
            $model = $this->getModel()->find($id);

            if (! $model) {
                return false;
            }

            return $model;
        } catch (\Throwable $th) {
            $this->handleException($th);
            return false;
        }
    }

    public function update(int $id, array $data)
    {
        try {
            $model = $this->getModel()->find($id);
            if (! $model) {
                return false;
            }
            return $model->update($data);
        } catch (\Throwable $th) {
            $this->handleException($th);
            return false;
        }
    }

    public function delete(int $id)
    {
        try {
            $model = $this->getModel()->find($id);
            if (! $model) {
                throw new \Exception('Record not found');
            }
            return $model->delete();
        } catch (\Throwable $th) {
            $this->handleException($th);
            return false;
        }
    }

    public function deleteByKey($key, $value)
    {
        try {
            return $this->getModel()
                ->where($key, $value)
                ->delete();
        } catch (\Throwable $th) {
            $this->handleException($th);
            return false;
        }
    }

    public function findBy($key, $value)
    {
        try {
            return $this->getModel()
                ->where($key, $value)
                ->first();
        } catch (\Throwable $th) {
            $this->handleException($th);
            return false;
        }
    }

    public function getAll()
    {
        try {
            return $this->getModel()->get();
        } catch (\Throwable $th) {
            $this->handleException($th);
            return false;
        }
    }

    public function getByFilter(array $filter)
    {
        try {
            $query = $this->getModel();
            foreach ($filter as $key => $value) {
                $query->where($key, $value);
            }
            return $query->get();
        } catch (\Throwable $th) {
            $this->handleException($th);
            return false;
        }
    }

    public function uploadImage($file, $folder = 'default')
    {
        try {
            if ($file instanceof UploadedFile) {
                // Nếu là file upload từ request
                $filename = 'image-' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path     = $file->storeAs($folder, $filename, 'public');

            } elseif (is_string($file) && file_exists($file)) {
                // Nếu là string path local
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                $filename  = 'image-' . uniqid() . '.' . $extension;
                $path      = $folder . '/' . $filename;
                Storage::disk('public')->put($path, file_get_contents($file));
            } else {
                throw new \Exception("File không hợp lệ");
            }
            // Trả về URL public
            return asset('storage/' . $path);

        } catch (\Throwable $th) {
            $this->handleException($th);
            return false;
        }
    }
    public function findBySlug(string $slug)
    {
        try {
            return $this->getModel()
                ->where('slug', $slug)
                ->first();
        } catch (\Throwable $th) {
            $this->handleException($th);
            return false;
        }
    }
    public function getClass()
    {

        return get_class($this->getModel());
    }
    function getAllActive()
    {
        try {
            $status = $this->model::STATUS_ACTIVE;
            return $this->model::where('status', $status)->get();
        } catch (\Throwable $th) {
            $this->handleException($th);
            return [];
        }
    }
}
