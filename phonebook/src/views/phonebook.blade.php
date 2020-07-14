@extends('phonebook::layout.master')

@section('page_title')
	Phonebook
@endsection

@section('main_container')
<div id="app">
	<div class="row my-4 mx-1">
	    <div class="col-sm-12 py-3 " style="border: 1px solid #dee2e6;">
	        <div class="row no-gutters mb-4">
				<div class="col-12 px-2">
					<h2>Phonebook</h2>
				</div>
			</div>
			<div class="row no-gutters">
	            <div class="col-4 px-2">
	                <div class="form-group row">
	                    <label class="col-sm-3 col-form-label">Status</label>
	                    <div class="col-sm-9">
	                        <select class="form-control" id="status" name="status" v-model="status">
								<option v-for="st in statuses" >@{{st}}</option>
	                        </select>
	                    </div>
	                </div>
	            </div>
	            <div class="col-4 px-2">
	                <div class="form-group row">
	                    <label class="col-sm-3 col-form-label">From Date</label>
	                    <div class="col-sm-9">
	                        <input type="date" class="form-control" id="from_date" name="from_date" v-model="from_date" placeholder="From Date">
	                    </div>
	                </div>
	            </div>
	            <div class="col-4 px-2">
	                <div class="form-group row">
	                    <label class="col-sm-3 col-form-label">To Date</label>
	                    <div class="col-sm-9">
	                        <input type="date" class="form-control" id="to_date" name="to_date" v-model="to_date" placeholder="To Date">
	                    </div>
	                </div>
	            </div>
	        </div>

	        <div class="row no-gutters">
	            <div class="col-4 px-2">
	                
	            </div>
	            <div class="col-4 px-2">
	               
	            </div>
	            <div class="col-4 px-2">
	                <div class="form-group row">
	                    <label class="col-sm-3 col-form-label"></label>
	                    <div class="col-sm-9 text-right">
	                        <button type="button" class="btn btn-primary" @click="getPhonebookData();" style="background: #417B74;">Search</button>
	                        <button type="button" class="btn btn-success" @click="resetParamsDatatable();" style="background: #5f5f5f;">Reset</button>
	                    </div>
	                </div>
	            </div>
	        </div>
	        <div class="row my-4 mx-1">
				<div class="col-4 px-2">
					<!-- <h2>Table</h2> -->
				</div>
				<div class="col-4 px-2">
				
				</div>
				<div class="col-4" style="padding-right: 0px!important;">
					<select class="form-control col-6" id="perpage" name="perpage" v-model="perpage" style="float: right;" @change="getPhonebookData(1)">
						<option v-for="pagedata in pagination" >@{{pagedata}}</option>
			        </select>
				</div>
			</div>

			<table class="table table-bordered" id="phonebooktable">
			  	<thead>
			    	<tr>
						<th scope="col">Date</th>
						<th scope="col">Phone Number</th>
						<th scope="col">Call Duration</th>
						<th scope="col">Status</th>
				    </tr>
			  	</thead>
			  	<tbody >
					<tr v-for="item in items">
						<td>@{{item.date}}</td>
						<td>@{{item.phone}}</td>
						<td>@{{item.duration}}</td>
						<td>@{{item.status}}</td>
					</tr>
					<tr v-if="total==0">
						<td colspan="4" style="text-align: center;" >No Data Found</td>
					</tr>
			  	</tbody>
			</table>

			<nav aria-label="Page navigation example" v-if="perpage!='All'">
				<ul class="pagination">
					<li class="page-item"><a class="page-link" href="javascript:void(0);" v-if="currentPage!=1" @click="getPhonebookData(prev)">Previous</a></li>
					<li class="page-item" v-for="pagenumber in paginationData" v-bind:class="{ active: pagenumber==currentPage }"><a class="page-link" href="javascript:void(0);" @click="getPhonebookData(pagenumber)">@{{pagenumber}}</a></li>


					<li class="page-item"><a class="page-link" href="javascript:void(0);" v-if="currentPage!=totalPage && total!=0" @click="getPhonebookData(next)">Next</a></li>
				</ul>
			</nav>

		</div>
	</div>

	<h1 class="text-center">Chart</h1>
	<div class="row my-4 mx-1">
		<div class="col-sm-12 py-3 " style="border: 1px solid #dee2e6;">
	    	<canvas id="my-chart" width="500" height="auto"></canvas>
		</div>
	</div>
</div>
@endsection


<style type="text/css">
	.dataTables_wrapper{
		width: 100%!important;
	}
	#phonebooktabledatatable{
		width: 100%!important;
	}
</style>

@section('page_bottom_js')
<script>
    var vm = new Vue({
	    el: '#app',

	    data: {
	    	statuses: ["Select", 'In-call', 'hold', 'call back', 'do not call'],
	    	pagination: [5,10,25,50,100],
	    	perpage: 5,
	    	currentPage: 1,
	    	totalPage: 1,
	    	total: 0,
	    	paginationData: [],
	    	prev: "",
	    	next: "",
	    	status: "Select",
	    	from_date: "",
	    	to_date: "",
	    	items: []
	    },

	    mounted: function() {
	    	this.getPhonebookData(this.currentPage);
	    },

	    methods: {
	    	getPhonebookDataByDatatable(){
    			var vm = this;
			    $('#phonebooktabledatatable').DataTable({
			        processing: true,
			        serverSide: true,
			        pageLength: 10,
			        language: {
			            processing: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i><span class="sr-only">Loading...</span>',
			        },
			        destroy: true,
			        searching: false,
			        ajax: {
			            url: "get-phonebook-data-datatable",
			            type: 'GET',
			            data: function ( d ) {
			                d.status = vm.status;
			                d.from_date = vm.from_date
			                d.to_date = vm.to_date
			            },
			        },
			        columns:[
			                    { data: 'date', name: 'date' },
			                    { data: 'phone', name: 'phone' },
			                    { data: 'duration', name: 'duration' },
			                    { data: 'status', name: 'status' },
			                ],
			        order: [[0, 'desc']],
			    });
	    	},

	    	getPhonebookData(currentPage){
	    		var vm = this;
	    		if(!currentPage){
	    			var currentPage = 1
	    		}
	    		else{
	    			var currentPage = currentPage;	
	    		}
	    		
	    		console.log(currentPage);
	    		axios({
                    url: "get-phonebook-data",
                    method: "GET",
                    dataType:"JSON",
                    params: {
					    perpage: vm.perpage,
					    currentPage: currentPage,
					    status: vm.status,
					    from_date: vm.from_date,
					    to_date: vm.to_date
				  	}
                }).then(function (response) {
                	$("#pagination").empty();
                	vm.items = response.data.data;
                	vm.currentPage = response.data.currentPage;
                	vm.totalPage = response.data.totalPage;
                	vm.total = response.data.total;
                	vm.paginationData = response.data.paginationData;
                	vm.prev = parseInt(vm.currentPage)-1;
                	vm.next = parseInt(vm.currentPage)+1;
                	vm.createChart(response.data.dates, response.data.no_of_call);
                }).catch(function (error) {

                });
	    	},
	    	
	    	resetParamsDatatable() {
	    		var vm = this;
	    		vm.status = "Select";
	    		vm.from_date = "";
	    		vm.to_date = "";
	    	},

	    	createChart(dates,no_of_call) {
	    		new Chart(document.getElementById('my-chart'), {
			  		type: 'line',
				  	data: {
				    	labels: dates,
				    	datasets: [
					      	{
						        label: 'No of calls by Date',
						        data: no_of_call
					      	}
				    	]	
				  	}
				});
	    	}
	    }
  	});
</script>
@endsection