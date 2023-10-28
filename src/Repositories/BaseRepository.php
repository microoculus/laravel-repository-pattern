<?php

namespace Microoculus\LaravelRepositoryPattern\Repositories;
use Illuminate\Database\Eloquent\Model;
use Microoculus\LaravelRepositoryPattern\Repositories\DbFacadesRepository;  

class BaseRepository extends DbFacadesRepository
{     
    /**      
     * @var Model      
     */     
     public $model, $query, $closureStatus , $closureMethod;       

    /**      
     * BaseRepository constructor.      
     *      
     * @param Model $model      
     */     
    public function __construct(Model $model)     
    {         
        $this->model = $model;
        $this->query = $this->model->query();
        $this->closureStatus = false;
        parent::__construct($model);

    }


    public function getAll($sort = 'asc') 
    {
        if(strtolower($sort) == "desc"){
            return  $this->model->all()->sortDesc();
        }else{
            return  $this->model->all();
        }
        
    }
   

    public function getAllWithRelations($with = [], $columns = ['*']){
        return  $this->model->with($with)->get($columns);
    }

    public function getAllColumnWise($columns = ['*']) 
    {
        return  $this->model->all($columns);
    }

    public function find($id) 
    {
        return $this->model->findOrFail($id);
    }

    public function findWith($id, $with = []) 
    {
        return $this->model->with($with)->findOrFail($id);
    }

    public function delete($id) 
    {
        $this->model->destroy($id);
        return true;
    }

    public function store(array $details) 
    {
        $model = $this->model;
        $model->fill($details);
        $model->save();
        return $model;
    }

    public function update($id, array $details) 
    {
        $model = $this->model->findOrFail($id);
        $model->fill($details);
        $model->save();
        return $model;
    }

    
    public function paginate($columns = ['*'], $limit = null)
    {
        return $this->model->paginate($limit, $columns);
    }

    public function cursorPaginate($sortOrder= "ASC", $columns = ['*'], $limit = null)
    {
        return $this->model->orderBy('id', $sortOrder)->cursorPaginate($limit, $columns);
    }

    public function getWhere(array $where, $columns = ['*'])
    {
        $this->queryWhereConditions($where);
       return $this->query->get($columns);
    }

    public function getWhereFirst(array $where, $columns = ['*'])
    {
        $this->queryWhereConditions($where);
       return $this->query->first($columns);
    }

    public function getWhereFirstWith(array $where, $with = [], $columns = ['*'])
    {
        $this->queryWhereConditions($where);
       return $this->query->with($with)->first($columns);
    }

    public function getWherepaginate(array $where, $columns = ['*'], $limit = null)
    {
        $this->queryWhereConditions($where);
       return $this->query->paginate($limit,$columns);
    }
    public function getWhereCursorpaginate(array $where, $sortOrder= "ASC", $columns = ['*'], $limit = null)
    {
        $this->queryWhereConditions($where);
        return $this->query->orderBy('id', $sortOrder)->cursorPaginate($limit, $columns);   
    }

    public function updateOrCreate($where = [], $details = []){
        return $this->model->updateOrCreate(
            $where,
            $details
        );
    }
   
    public function eagerWith($with = [], $columns = ['*']){
        return  $this->model->with($with)->get();
    }

    public function eagerWithWhere($where = [], $with = [], $columns = ['*']){
        $this->queryWhereConditions($where);
       return $this->query->with($with)->get($columns);
    }

    public function firstOrNew($where = [], $details = []){

        $model = $this->model->firstOrNew($where, $details);
        $model->save();
        return  $model;
    }

    public function firstOrCreate($where = [], $details = [])
    {
        $model = $this->model->firstOrCreate($where, $details);
        return  $model;
    }

    public function firstOr($where = [], $details = [])
    {
        //TODO:
    }
   
 
    // Sluggable records

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

    public function findWhereExist(array $where = []){
        $this->queryWhereConditions($where);
        return $this->query->exists();
    }

    public function insertMultiple($details = []){
        $model = $this->model;
        $model->insert($details);
        return $model;
    }
    public function getOrderBy($where = [], $orderByColumn = "id", $orderBySort = "ASC", $columns = ['*'] ){
        if(!empty($where)) $this->queryWhereConditions($where);
         $records =  $this->query->orderBy($orderByColumn,$orderBySort)->get($columns);
         $this->cleanQuery();
         return $records;
    }

    /***
     * Chaining methods
     */
    public function cleanQuery(){
        $this->query = $this->model->query();
        return $this;
    }

    public function whereCondition($where = []){
        $this->queryWhereConditions($where);
        return $this;
    }

     /***
     * Closure methods
     */
    public function addClosure($closureMethod){

        $this->closureStatus = true;
        $this->closureMethod = $closureMethod;
        $this->query  =  $this->query->when($this->closureStatus,  $this->closureMethod);
        $this->model  =  $this->model->when($this->closureStatus,  $this->closureMethod);
        return $this;
        
    }

    

    /***
     * Private methods methods
     */

    private function queryWhereConditions($where = []){

        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $this->query =   $this->query->where($field, $condition, $val);
            } else {
                $this->query  =  $this->query->where($field, '=', $value);
            }
        }
       
    }

    
  

     
}


   


// https://dev.to/codeanddeploy/laravel-8-eloquent-query-first-and-firstorfail-example-2h44