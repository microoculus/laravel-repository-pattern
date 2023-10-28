<?php
namespace Microoculus\LaravelRepositoryPattern\Traits;

trait HasSlugTrait {
    public function findBySlug($slug, $columns = ['*']){
        return $this->model->findBySlug($slug);
    }

    public function findSlug($slug, $columns = ['*']) 
    {
        return $this->model->where('slug', $slug)->first($columns);
    }
    public function findSlugWith( $slug, $with = []) 
    {
        return $this->model->where('slug', $slug)->with($with)->first();
       
    }

    public function deleteSlug($slug) 
    {
        $model =$this->model->where('slug', $slug)->first();
        $model->delete();
        return true;
    }
    public function deleteBySlug($slug) 
    {
        $model = $this->model->findBySlug($slug);
        $model->delete();
        return true;
    }

    public function updateSlug($slug, array $details) 
    {
        $model =$this->model->where('slug', $slug)->first();
        $model->fill($details);
        $model->save();
        return $model;
    }
    public function updateBySlug($slug, array $details) 
    {
        $model =$this->model->findBySlug($slug);
        $model->fill($details);
        $model->save();
        return $model;
    }
}