<?php

namespace Axilweb\Phonebook;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Datatables;

use App\Phonebook;


class PhonebookController extends Controller
{
    public function phonebookdatatable(){
    	return view('phonebook::phonebookdatatable');
    }


    public function getPhonebookDataDatatable(Request $request){
        $items = DB::table('phonebook')->select("*");

        if($request->input('status')!="Select"){
            $items->where('status',$request->input('status'));
        }
        if($request->input('from_date')){
            $items->where('date','>=',$request->input('from_date'));
        }     
        if($request->input('to_date')){
            $items->where('date','<=', $request->input('to_date'));
        }
        return datatables()->of($items)
            ->make(true);
    }


    public function getChartData(Request $request){
        $items = DB::table('phonebook');
        $pagelength = $request->input('pagelength');
        $pagenumber = $request->input('pagenumber');
        
        $columns = ['date', 'phone', 'duration', 'status'];

        if($request->input('status')!="Select"){
            $items->where('status',$request->input('status'));
        }
        if($request->input('from_date')){
            $items->where('date','>=',$request->input('from_date'));
        }     
        if($request->input('to_date')){
            $items->where('date','<=', $request->input('to_date'));
        }
        if($request->input('order')){
            $order = $request->input('order');
            // dd($order);
            $items->orderBy($columns[$order[0]],$order[1]);
        }

        $skip = $pagelength*$pagenumber;
        $items->skip($skip)->take($pagelength);
        
        $chartData = $items->select('date', DB::raw('count(id) as no_of_call'))->groupBy('date')->get();
        $dates = [];
        $no_of_call = [];
        foreach ($chartData as $chart) {
            $dates[] = $chart->date;
            $no_of_call[] = $chart->no_of_call;
        }
        $result['dates'] = $dates;
        $result['no_of_call'] = $no_of_call;
        return response()->json($result);
    }


    public function phonebook(){
        return view('phonebook::phonebook');
    }


    public function getPhonebookData(Request $request){
        $perpage = $request->input('perpage');
        $totalPage = 1;
        $currentPage = $request->input('currentPage');
       
        $items = DB::table('phonebook');
       
        if($request->input('status')!="Select"){
            $items->where('status',$request->input('status'));
        }
        if($request->input('from_date')){
            $items->where('date','>=',$request->input('from_date'));
        }     
        if($request->input('to_date')){
            $items->where('date','<=', $request->input('to_date'));
        }
        $count = $items->count();
        
        $skip = $perpage*($currentPage-1);
        $items->skip($skip)->take($perpage);
        $totalPage = ceil($count/$perpage);
        
        $items->orderBy('date');
        $data = $items->get();
        
        $paginationData = $this->createPagination($currentPage, $totalPage);
        
        $chartData = $items->select('date', DB::raw('count(id) as no_of_call'))->groupBy('date')->get();
        $dates = [];
        $no_of_call = [];
        foreach ($chartData as $chart) {
            $dates[] = $chart->date;
            $no_of_call[] = $chart->no_of_call;
        }

        $result['perpage'] = $perpage;
        $result['total'] = $count;
        $result['totalPage'] = $totalPage;
        $result['currentPage'] = $currentPage;
        $result['paginationData'] = $paginationData;
        $result['data'] = $data;
        $result['dates'] = $dates;
        $result['no_of_call'] = $no_of_call;
        return response()->json($result);
    }


    public function createPagination($currentPage, $totalPage){
        $paginationData = [];
        if($totalPage>5){
            if($currentPage>2 && ($currentPage<$totalPage-2)){
                $paginationData = [$currentPage-2,$currentPage-1,$currentPage,$currentPage+1,$currentPage+2];
            }
           else if($currentPage<3){
                $paginationData = [1,2,3,4,5];
           }
           else if($currentPage+2>=$totalPage){
                $paginationData = [$totalPage-4,$totalPage-3,$totalPage-2,$totalPage-1,$totalPage];
           }
        }
        else{
            for ($i=1; $i <= $totalPage; $i++) { 
                array_push($paginationData, $i);
            }
        }
        return $paginationData;
    }
}
