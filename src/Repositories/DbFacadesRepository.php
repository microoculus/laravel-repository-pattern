<?php

namespace Microoculus\LaravelRepositoryPattern\Repositories;
use Illuminate\Database\Eloquent\Model;  
use Illuminate\Support\Facades\DB;


abstract class DbFacadesRepository 
{ 

    protected $Dbtable, $DbQuery = null, $DbClosureStatus, $DbClosureMethod;

    protected function __construct(Model $model) {
        $this->Dbtable = $model->getTable();
        $this->DbQuery = DB::table ($this->Dbtable);
    }

    

    public function DbGetAll($sortOrder= "ASC", $columns = ['*']) 
    {
        return $this->DbQuery->orderBy('id', $sortOrder)->get($columns);   
    }

    

    public function DbFind($id) 
    {
        return $this->DbQuery->find($id);
    }


    

    public function DbDelete($where = []) 
    {
        $this->DbQueryWhereConditions($where);
       return  $this->DbQuery->delete();
    }

    public function DbGetWhere(array $where, $columns = ['*'])
    {
        $this->DbQueryWhereConditions($where);
        return $this->DbQuery->get($columns);   
    }

    public function DbGetWhereFirst(array $where, $columns = ['*'])
    {
        $this->DbQueryWhereConditions($where);
        return $this->DbQuery->first($columns);
    }

    
     /***
     * Paginating methods
     */

    public function DbPaginate($columns = ['*'], $limit = 15)
    {
        return $this->DbQuery->paginate($limit, $columns);
    }

    public function DbCursorPaginate($sortOrder= "ASC", $columns = ['*'], $limit = 15)
    {
        return $this->DbQuery->orderBy('id', $sortOrder)->cursorPaginate($limit,$columns);
    }


     /***
     * Chaining methods
     */

     public function DbCleanQuery(){
        $this->DbQuery = DB::table ($this->table);
        return $this;
    }

    public function DbAddClosure($closureMethod){

        $this->DbClosureStatus = true;
        $this->DbClosureMethod = $closureMethod;
        $this->DbQuery  =  $this->DbQuery->when($this->DbClosureStatus,  $this->DbClosureMethod);
        return $this;
        
    }

     /***
     * Private methods methods
     */

     private function DbQueryWhereConditions($where = []){

        foreach ($where as $field => $value) {
            if (is_array($value)) {
                [$field, $condition, $val] = $value;
            } else {
                $condition = '=';
                $val = $value;
            }
            $this->DbQuery->where($field, $condition, $val);
        }
       
    }
}