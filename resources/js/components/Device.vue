<template>
	<div class="container-fluid">
	    <div class="row">
	      <div class="col-12">
	        <div class="card">
	          <div class="card-header">
	            <h3 class="card-title">Устройства</h3>

	            <div class="card-tools">
	              <button @click="newModal" class="btn btn-sm btn-primary">Добавить устройство</button>
	            </div>
	          </div>
	          <!-- /.card-header -->
	          <div class="card-body table-responsive p-0">
	            <table class="table table-hover text-nowrap">
	              <thead>
	                <tr>
	                  <th>№</th>
	                  <th>Клиент</th>
	                  <th>Название</th>
	                  <th>Серийный номер</th>
	                  <th>ID</th>
	                  <th>Дата добавления</th>
	                  <th>Действие</th>
	                </tr>
	              </thead>
	              <tbody>
	                <tr v-for="(item, key) in devices">
	                  <td>{{ key + 1 }}</td>
	                  <td>{{ item.u_name }}</td>
	                  <td>{{ item.name }}</td>
	                  <td>{{ item.serial_number }}</td>
	                  <td>{{ item.device_id }}</td>
	                  <td>{{ item.created_at }}</td>
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
	    <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" v-show="!editmode">Добавление устройства</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- <form @submit.prevent="createUser"> -->
            <form @submit.prevent="editmode ? updateClient() : createDevice()">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Название</label>
                        <input v-model="form.name" type="text" name="name"
                            class="form-control" required :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>
                    <div class="form-group">
                        <label>ID в Я.Облако *</label>
                        <input v-model="form.ya_id" type="text" name="ya_id"
                            class="form-control" required :class="{ 'is-invalid': form.errors.has('ya_id') }">
                        <has-error :form="form" field="ya_id"></has-error>
                    </div>
                    <div class="form-group">
                        <label>Пароль в Я.Облако</label>
                        <input v-model="form.ya_password" type="text" name="ya_password"
                            class="form-control" required :class="{ 'is-invalid': form.errors.has('ya_password') }">
                        <has-error :form="form" field="ya_password"></has-error>
                    </div>
                    <div class="form-group">
                        <label>Серийный номер *</label>
                        <input v-model="form.ya_number" type="text" name="ya_number"
                            class="form-control" required :class="{ 'is-invalid': form.errors.has('ya_number') }">
                        <has-error :form="form" field="ya_number"></has-error>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button v-show="!editmode" type="submit" class="btn btn-primary">Добавить</button>
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
		        devices: [],
		        editmode: false,
		        form: new Form({
		          name: '',
		          ya_id: '',
		          ya_password: '',
		          ya_number: ''
		        }),
			}
		},
		created() {
			this.getDevices()
		},
		methods: {
			getDevices() {
				axios.get("/devices/get-all").then((response) => {
					this.devices = response.data.devices
				});
			},
			newModal() {
				this.editmode = false;
		        this.form.reset();
		        $('#addNew').modal('show');
			},
			createDevice() {
				axios.post("/devices/save", this.form)
		    	.then((response) => {
					this.getDevices();
		            $('#addNew').modal('hide');

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