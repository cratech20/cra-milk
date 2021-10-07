<template>
	<div class="container-fluid">
	    <div class="row">
	      <div class="col-12">
	        <div class="card">
	          <div class="card-header">
	            <h3 class="card-title">Клиенты</h3>

	            <div class="card-tools">
	              <button class="btn btn-sm btn-primary">Нарегистрировать клиента</button>
	            </div>
	          </div>
	          <!-- /.card-header -->
	          <div class="card-body table-responsive p-0">
	            <table class="table table-hover text-nowrap">
	              <thead>
	                <tr>
	                  <th>№</th>
	                  <th>Название</th>
	                  <th>Действие</th>
	                </tr>
	              </thead>
	              <tbody>
	                <tr v-for="(item, key) in clients">
	                  <td>{{ key + 1 }}</td>
	                  <td>{{ item.name }}</td>
	                  <td>
	                    <button class="btn btn-sm btn-outline-primary">Перейти к подразделениям</button>
	                    <br />
	                    <button class="btn btn-sm btn-outline-primary">Перейти к фермам</button>
	                    <br />
	                    <button class="btn btn-sm btn-outline-primary">Перейти к группам коров</button>
	                    <br />
	                    <button class="btn btn-sm btn-outline-primary">Перейти к коровам</button>
	                    <br />
	                    <button class="btn btn-sm btn-outline-primary">Перейти к итоговой таблице по устройствам</button>
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
		        clients: [],
			}
		},
		created() {
			this.getClients()
		},
		methods: {
			getClients() {
				axios.get("/clients/get-all").then((response) => {
					this.clients = response.data.clients
				});
			}
		}
	}
</script>