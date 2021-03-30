<?php

namespace App\Traits;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponse{
    protected function successResponse($data,$code){
        return response()->json($data,$code);
    }

    protected function errorResponse($message, $code){
        return response()->json(['error' => $message,'code' => $code],$code);
    }

    protected function showAll(Collection $collection, $code = 200){
        if($collection->isEmpty()){
            return $this->successResponse(['data'=> $collection],$code);
        }

        $collection = $this->filterData($collection);
        //$collection = $this->sortData($collection);

        return $this->successResponse(['data'=> $collection],$code);
    }

    protected function showOne($instance,$code = 200){
        return $this->successResponse(['data' => $instance ],$code);
    }

    protected function showList($instance , $code = 200){
        return $this->successResponse(['data' => $instance ],$code);
    }

    protected function showMessage($message,$code = 200){
        return $this->successResponse(['data' => $message],$code);
    }

    protected function sortData(Collection $collection){
        if(request()->has('sort_by')){
            $attribute = request()->sort_by;
            $collection = $collection->sortBy->{$attribute};
        }
        return $collection;
    }

    protected function filterData(Collection $collection){
        foreach (request()->query() as $query => $value){
            if(isset($query,$value)){
                $collection = $collection->where($query,$value)->values();
            }
        }
        return $collection;
    }

    protected function filterDataLike(Collection $collection){
        foreach (request()->query() as $query => $value){
            if(isset($query,$value)){
                $collection = $collection->filter(function($item) use ($query, $value){
                    return false !== stristr($item[$query], $value);
                });
            }
        }
        return $collection;
    }
}

