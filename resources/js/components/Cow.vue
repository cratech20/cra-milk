<template>
	<div class="container-fluid">
	    <div class="row">
	      <div class="col-12">
	        <div class="card">
	          <div class="card-header">
	            <h3 class="card-title">Список коров компании {{client.name}}</h3>

	            <div class="card-tools">
	              
	            </div>
	          </div>
	          <!-- /.card-header -->
	          <div class="card-body table-responsive p-0">
	            <table class="table table-hover table-responsive p-10 text-nowrap">
		            <thead>
		                <tr>
		                  	<th>№</th>
		                  	<th>Группа</th>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Внутренний номер</th>
		                  	<th>Действие</th>
		                </tr>
		            </thead>
		            <tbody>
		                <tr v-for="(item, key) in cows">
		                  <td>{{ key + 1 }}</td>
		                  <td>{{ item.group }}</td>
		                  <td>{{ item.cow_id }}</td>
		                  <td>{{ item.calculated_name }}</td>
		                  <td>{{ item.internal_code }}</td>
		                  
		                  <td>
		                    <button @click="edit(item)" class="btn btn-sm btn-outline-primary">Редактировать</button>
		                  </td>
		                </tr>
		            </tbody>
	            </table>
	          </div>
	          <!-- /.card-body -->
	        </div>
	        <!-- /.card -->
	      </div>
	    </div>
	    
	</div><!-- /.container-fluid -->
</template>

<script>
	export default {
		data() {
			return {
				csrf: document
		          .querySelector('meta[name="csrf-token"]')
		          .getAttribute("content"),
		        client: [],
		        id: this.$route.params.id,
		        cows: []
			}
		},
		created() {
			this.getClient()
			this.getCow()
		},
		methods: {
			getClient() {
				axios.get("/clients/get/"+this.id).then((response) => {
					this.client = response.data.client
				});
			},
			getCow() {
				axios.get("/clients/"+this.id+"/get-cows").then((response) => {
					this.cows = response.data.cows
				});
			},
			edit(item) {

			}
		}
	}
</script>