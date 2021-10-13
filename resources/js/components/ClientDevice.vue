<template>
	<div class="container-fluid">
	    <div class="row">
	      <div class="col-12">
	        <div class="card">
	          <div class="card-header">
	            <h3 class="card-title">Итоговая таблица по устройствам клиента {{client.name}}</h3>

	            <div class="card-tools">
	              
	            </div>
	          </div>
	          <!-- /.card-header -->
	          <div class="card-body table-responsive p-0">
	            <table class="table table-hover table-responsive p-10 text-nowrap">
		            <thead>
		                <tr>
		                  	<th>№</th>
		                  	<th>Подразделение</th>
                            <th>Ферма</th>
                            <th>Устройство</th>
                            <th>Серийный номер</th>
                            <th>ID</th>
                            <th>Дата добавления</th>
		                  	<th>Действие</th>
		                </tr>
		            </thead>
		            <tbody>
		                <tr v-for="(item, key) in devices">
		                  <td>{{ key + 1 }}</td>
		                  <td>{{ item.group }}</td>
		                  <td>{{ item.cow_id }}</td>
		                  <td>{{ item.name }}</td>
		                  <td>{{ item.serial_number }}</td>
		                  <td>{{ item.device_id }}</td>
		                  <td>{{ item.created_at }}</td>
		                  
		                  <td>
		                  	<button @click="message(item)" class="btn btn-sm btn-outline-primary">Сообщения</button>
		                    <button @click="edit(item)" class="btn btn-sm btn-outline-primary">Редактировать</button>
		                    <button @click="management(item)" class="btn btn-sm btn-outline-primary">Управление</button>
		                  </td>
		                </tr>
		            </tbody>
	            </table>
	            <v-pagination v-model="devices" :page-count="30"></v-pagination>
	          </div>
	          <!-- /.card-body -->
	        </div>
	        <!-- /.card -->
	      </div>
	    </div>

	    <div class="modal fade" id="message" tabindex="-1" role="dialog" aria-labelledby="message" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title">Сообщения устройства {{ formMessage.name }}</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <!-- <form @submit.prevent="editRight"> -->
	            <form @submit.prevent="save()">
	                
	                    
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
	                </div>
	              </form>
	            
	            </div>
	        </div>
        </div>
        <div class="modal fade" id="management" tabindex="-1" role="dialog" aria-labelledby="management" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
		            <div class="modal-header">
		                <h5 class="modal-title">Управление устройством {{ formManagement.name }}</h5>
		                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		                    <span aria-hidden="true">&times;</span>
		                </button>
		            </div>

		            <!-- <form @submit.prevent="editRight"> -->
		            <form @submit.prevent="save()">
		            	<div class="modal-body">
		                	<button class="btn btn-sm  btn-primary">Перезагрузить</button>
		                	<br /><br />
		                	<button class="btn btn-sm  btn-primary">Установить время</button>
		                	<br /><br />
		                	<button class="btn btn-sm  btn-primary">Установить номер</button>
		                	<br /><br />
		                	<button class="btn btn-sm  btn-primary">Установить вес</button>
		                </div>

		                <div class="modal-footer">
		                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		                </div>
		            </form>
	            
	            </div>
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
		        devices: [],
		        form: new Form({
		        	id: '',
		        	internal_code: '',
		        }),
		        formMessage: new Form({
		        	id: '',
		        	name: '',
		        	message: '',
		        }),
		        formManagement: new Form({
		        	id: '',
		        	name: '',
		        	message: '',
		        }),
			}
		},
		created() {
			this.getClient()
			this.getDevices()
		},
		methods: {
			getClient() {
				axios.get("/clients/get/"+this.id).then((response) => {
					this.client = response.data.client
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
			},
			getDeviceMessage(item) {
				axios.get("/devices/"+item.id+"/messages").then((response) => {
					this.devices = response.data.devices
				});
			},
			message(item) {
				this.getDeviceMessage(item);
				this.formMessage.reset();
		        $('#message').modal('show');
		        this.formMessage.fill(item);
			},
			management(item) {
				this.formManagement.reset();
		        $('#management').modal('show');
		        this.formManagement.fill(item);
			}
		}
	}
</script>