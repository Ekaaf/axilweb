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
					<h2>Phonebook Using Datatable</h2>
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
	                        <button type="button" class="btn btn-primary" @click="getPhonebookDataByDatatable();" style="background: #417B74;">Search</button>
	                        <button type="button" class="btn btn-success" @click="resetParamsDatatable();" style="background: #5f5f5f;">Reset</button>
	                    </div>
	                </div>
	            </div>
	        </div>
			
			<div class="row no-gutters">
				<table class="table table-bordered" id="phonebooktabledatatable">
					<thead>
						<tr>
							<th scope="col">Date</th>
							<th scope="col">Phone Number</th>
							<th scope="col">Call Duration</th>
							<th scope="col">Status</th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
			
	<h1 class="text-center">Chart</h1>
	<div class="row my-4 mx-1">

	    <div class="col-sm-12 py-3 " style="border: 1px solid #dee2e6;">
	        <canvas id="my-chart" width="500" height="300"></canvas>
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
	    	status: "Select",
	    	from_date: "",
	    	to_date: "",
	    	pagenumber: "",
	    	pagelength: "",
	    	order: ""
	    },

	    mounted: function() {
	    	this.getPhonebookDataByDatatable();
	    },

	    methods: {
	    	getPhonebookDataByDatatable(){
    			var vm = this;
			    var table = $('#phonebooktabledatatable').DataTable({
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
			        order: [[0, 'desc']]
			    });
				table.on('draw', function () {
					var pageInfo = table.page.info();
					vm.pagenumber = pageInfo.page;
		        	vm.pagelength = pageInfo.length;
		        	vm.order = table.order();
		        	vm.getChartdata(vm.pagelength, vm.pagenumber, vm.order);
				} );
	    	},

	    	getChartdata(pagelength, pagenumber, order){
	    		console.log()
	    		var vm = this;
	    		axios({
                    url: "get-chart-data",
                    method: "GET",
                    dataType:"JSON",
                    params: {
					    status: vm.status,
					    from_date: vm.from_date,
					    to_date: vm.to_date,
					    pagelength: vm.pagelength,
					    pagenumber: vm.pagenumber,
					    order: vm.order[0]
				  	}
                }).then(function (response) {
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