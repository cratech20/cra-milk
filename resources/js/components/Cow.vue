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
                            <th>Устройство</th>
		                  	<th>Действие</th>
		                </tr>
		            </thead>
		            <tbody>
		                <tr v-for="(item, key) in cows">
		                  <td>{{ key + 1 }}</td>
		                  <td>{{ item.g_name }}</td>
		                  <td>{{ item.cow_id }}</td>
		                  <td v-if="item.name">{{ item.name }}</td>
                          <td v-if="!item.name">Корова ID {{item.id}} ({{item.cow_id}})</td>
                          <td>{{ item.internal_code }}</td>
                          <td>{{ item.d_name }}</td>

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
	    <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title">Редактирование</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <!-- <form @submit.prevent="editRight"> -->
	            <form @submit.prevent="save()">
	                <div class="modal-body table-responsive p-10">
	                  <div class="form-group">
		                    <label>Внутренний номер</label>
		                    <input v-model="form.internal_code" type="number" name="internal_code"
		                        class="form-control" required :class="{ 'is-invalid': form.errors.has('internal_code') }">
		                    <has-error :form="form" field="internal_code"></has-error>
		                </div>
                        <div class="form-group">
	                        <label>Группа коров</label>
	                        <select class="form-control" v-model="form.group_id" required>
								<option v-for="(item, index) in cowGroups" :value="item.id">{{item.name}}</option>
							</select>
	                        <has-error :form="form" field="serial_number"></has-error>
	                    </div>
                        <div class="form-group">
                            <div style="width: 60%; float: left;">
                                <label>Устройство</label>
                                <select class="form-control" v-model="form.device_id" required>
                                    <option v-for="(item, index) in devices" :value="item.id">{{item.name}}</option>
                                </select>
                            </div>

                            <button v-show="form.device_id" class="btn btn-sm btn-outline-danger"
                                style="width: 30%; margin-left: 5%; margin-top: 8%;"
                                @click.prevent="detachDevice()"
                            >
                                Отвязать
                            </button>
	                        <has-error :form="form" field="serial_number"></has-error>
	                    </div>
	                </div>

	                <div class="modal-footer">
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
	                    <button type="submit" class="btn btn-success">Сохранить</button>
	                </div>
	              </form>

	            </div>
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
		        cows: [],
		        form: new Form({
		        	id: '',
		        	internal_code: '',
                    group_id: '',
                    device_id: ''
		        }),
                cowGroups: [],
                devices: []
			}
		},
		created() {
			this.getClient()
			this.getCow()
            this.getDevices()
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
            getCowGroup() {
				axios.get("/clients/"+this.id+"/cows/groups").then((response) => {
					this.cowGroups = response.data.cowGroups
				});
			},
            getDevices() {
                axios.get("/clients/"+this.id+"/all-devices").then((response) => {
					this.devices = response.data.devices
				});
            },
			edit(item) {
		        this.form.reset();
		        $('#edit').modal('show');
		        this.form.fill(item);
                this.getCowGroup()
                this.getDevices()
			},
            detachDevice() {
                axios.post("/devices/detach", {'data': this.form}).then((response) => {
					this.getDevices()

		            Toast.fire({
		                  icon: 'success',
		                  title: response.data.message
		            });

		            this.$Progress.finish();
				});
            },
			save() {
				axios.post("/clients/cows/edit", {'data': this.form}).then((response) => {
					this.getCow()
					$('#edit').modal('hide');

		            Toast.fire({
		                  icon: 'success',
		                  title: response.data.message
		            });

		            this.$Progress.finish();
				});
			}
		}
	}
</script>
