<template>
	<div class="container-fluid">
	    <div class="row">
	      <div class="col-12">
	        <div class="card">
	          <div class="card-header">
	            <h3 class="card-title">Устройства</h3>

	            <div class="card-tools">
	              <button @click="newModal" class="btn btn-sm btn-primary">Добавить устройство</button>
	              <button @click="() => {this.showManagment =! this.showManagment}" class="btn btn-sm btn-primary" :disabled="checked.length == 0">Управление</button>
	            </div>
	        </div>
	          	<div class="card-header" v-if="showManagment">
	            	<h3 class="card-title" style="width: 15%;">Действия</h3>
	            	<select class="form-control" @change="onChange($event)" style="width: 30%; margin-right: 10px; float:left;">
	            		<option v-for="(item, key) in managmentOpt" :value="item.code">{{ item.name }}</option>
	            	</select>
	            	<input type="number" v-model="selectedManagment.num"  v-show="selectedManagment.com == 'setnum'" class="form-control" style="width: 20%; float:left;">
	            	<vue-timepicker v-show="selectedManagment.com == 'settime'" format="HH:mm" v-model="selectedManagment.time" manual-input></vue-timepicker>
	            	<input type="number" v-model="selectedManagment.cal"  v-show="selectedManagment.com == 'setcal'" class="form-control" style="width: 20%; float:left;">
	            	<div class="card-tools">
	            		<button @click="run" class="btn btn-sm btn-primary">Выполнить</button>
	            	</div>
	            </div>
	          <!-- /.card-header -->
	          <div class="card-body table-responsive p-0">
	            <table class="table table-hover text-nowrap">
	              <thead>
	                <tr>
	                  <th></th>
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
	                  <td><input type="checkbox" v-model="checked" :value="item"></td>
	                  <td>{{ key + 1 }}</td>
	                  <td>{{ item.u_name }}</td>
	                  <td>{{ item.name }}</td>
	                  <td>{{ item.serial_number }}</td>
	                  <td>{{ item.device_id }}</td>
	                  <td>{{ item.created_at }}</td>
	                  <td>
	                    <button @click="showMessage(item)" class="btn btn-sm btn-outline-primary">Просмотреть сообщения</button>
	                    <br />
	                    <button @click="editModal(item)" class="btn btn-sm btn-outline-primary">Редактировать</button>
	                    <br />
	                    <button class="btn btn-sm btn-outline-danger">Удалить</button>
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
	                <h5 class="modal-title">Добавление устройства</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <!-- <form @submit.prevent="createUser"> -->
	            <form @submit.prevent="createDevice()">
	                <div class="modal-body">
	                    <div class="form-group">
	                        <label>Название</label>
	                        <input v-model="form.name" type="text" name="name"
	                            class="form-control" required :class="{ 'is-invalid': form.errors.has('name') }">
	                        <has-error :form="form" field="name"></has-error>
	                    </div>
	                    <div class="form-group">
	                        <label>ID в Я.Облако *</label>
	                        <input v-model="form.device_id" type="text" name="device_id"
	                            class="form-control" required :class="{ 'is-invalid': form.errors.has('device_id') }">
	                        <has-error :form="form" field="device_id"></has-error>
	                    </div>
	                    <div class="form-group">
	                        <label>Пароль в Я.Облако</label>
	                        <input v-model="form.password" type="text" name="password"
	                            class="form-control" required :class="{ 'is-invalid': form.errors.has('assword') }">
	                        <has-error :form="form" field="password"></has-error>
	                    </div>
	                    <div class="form-group">
	                        <label>Серийный номер *</label>
	                        <input v-model="form.serial_number" type="text" name="serial_number"
	                            class="form-control" required :class="{ 'is-invalid': form.errors.has('serial_number') }">
	                        <has-error :form="form" field="serial_number"></has-error>
	                    </div>
						<div class="form-group">
	                        <label>Шлюз *</label>
	                        <select class="form-control" v-model="form.gate_id" required>
								<option v-for="(item, key) in gates" :value="item.id">{{ item.name }}</option>
							</select>
	                        <has-error :form="form" field="serial_number"></has-error>
	                    </div>
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
	                    <button type="submit" class="btn btn-primary">Добавить</button>
	                </div>
	              </form>

	            </div>
	        </div>
	    </div>

        <div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="edit" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title">Редактирование устройства {{ editForm.name }}</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <!-- <form @submit.prevent="createUser"> -->
	            <form @submit.prevent="updateClient()">
	                <div class="modal-body">
	                    <div class="form-group">
	                        <label>Название</label>
	                        <input v-model="editForm.name" type="text" name="name"
	                            class="form-control" required :class="{ 'is-invalid': form.errors.has('name') }">
	                        <has-error :form="form" field="name"></has-error>
	                    </div>
	                    <div class="form-group">
	                        <label>ID в Я.Облако *</label>
	                        <input v-model="editForm.device_id" type="text" name="device_id"
	                            class="form-control" required :class="{ 'is-invalid': editForm.errors.has('device_id') }">
	                        <has-error :form="editForm" field="device_id"></has-error>
	                    </div>
	                    <div class="form-group">
	                        <label>Пароль в Я.Облако</label>
	                        <input v-model="editForm.password" type="text" name="password"
	                            class="form-control" required :class="{ 'is-invalid': form.errors.has('password') }">
	                        <has-error :form="form" field="password"></has-error>
	                    </div>
	                    <div class="form-group">
	                        <label>Серийный номер *</label>
	                        <input v-model="editForm.serial_number" type="text" name="serial_number"
	                            class="form-control" required :class="{ 'is-invalid': form.errors.has('serial_number') }">
	                        <has-error :form="form" field="serial_number"></has-error>
	                    </div>
						<div class="form-group">
	                        <label>Шлюз *</label>
	                        <select class="form-control" v-model="editForm.gate_id" required>
								<option v-for="(item, key) in gates" :value="item.id">{{ item.name }}</option>
							</select>
	                        <has-error :form="form" field="serial_number"></has-error>
	                    </div>
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
	                    <button type="submit" class="btn btn-primary">Сохранить</button>
	                </div>
	              </form>

	            </div>
	        </div>
	    </div>

	    <div class="modal fade" id="message" tabindex="-1" role="dialog" aria-labelledby="message" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" v-show="!editmode">Сообщения устройства {{ messageForm.name }}</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <!-- <form @submit.prevent="createUser"> -->
	            <form @submit.prevent="editmode ? updateClient() : createDevice()">
	                <div class="modal-body">
	                    <div class="form-group">
	                        <label>Сообщения</label>
                                <div class="form-control" style="height: calc(20em + 0.75rem + 2px); overflow-y: auto;">
                                    <p v-for="(item, index) in message">

                                        {{ item.payload }}

                                    </p>
                                </div>
	                        <has-error :form="form" field="name"></has-error>
	                    </div>
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
	                </div>
	              </form>

	            </div>
	        </div>
	    </div>
    </div>
	</div><!-- /.container-fluid -->
</template>

<script>
	import VueTimepicker from 'vue2-timepicker'
	import 'vue2-timepicker/dist/VueTimepicker.css'
	export default {
		components: { VueTimepicker },
		data() {
			return {
				csrf: document
		          .querySelector('meta[name="csrf-token"]')
		          .getAttribute("content"),
		        devices: [],
		        editmode: false,
		        form: new Form({
                  id: null,
		          name: '',
		          device_id: '',
		          password: '',
		          serial_number: '',
                  gate_id: null
		        }),
		        editForm: new Form({
                  id: null,
		          name: '',
		          device_id: '',
		          password: '',
		          serial_number: '',
                  gate_id: null
		        }),
		        messageForm: new Form({
		          name: '',
		          ya_id: '',
		          ya_password: '',
		          ya_number: ''
		        }),
		        checked: [],
		        showManagment: false,
		        managmentOpt: [
		        	{"code": "reset", "name": "Перезагрузка"},
		        	{"code": "update", "name": "Обновление"},
		        	{"code": "settime", "name": "Установить время"},
		        	{"code": "setnum", "name": "Установить номер прибора"},
		        	{"code": "setcal", "name": "Установить весовой коэффициент"},
		        ],
		        selectedManagment: {
		        	'com': 'reset',
		        	'time': null,
		        	'num': null,
		        	'cal': null
		        },
		        time: new Date(),
		        message: [],
                gates: [],
				selectedGate: []
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
            getGates() {
				axios.get("/devices/get-gates").then((response) => {
					this.gates = response.data.gates
				});
			},
			newModal() {
                this.getGates()
				this.editmode = false;
		        this.form.reset();
		        $('#addNew').modal('show');
			},
			editModal(item) {
                console.log(item)
                this.getGates()
				this.editmode = true;
		        this.editForm.reset();
		        $('#edit').modal('show');
		        this.editForm.fill(item);
			},
			getDeviceMessage(item) {
				axios.get("/devices/"+item.device_id+"/messages").then((response) => {
					//this.devices = response.data.devices
					this.message = response.data.json
				});
			},
			showMessage(item) {
				this.getDeviceMessage(item);
				this.editmode = false;
		        this.messageForm.reset();
		        $('#message').modal('show');
		        this.messageForm.fill(item);
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
			},
			updateClient() {

				axios.post("/devices/update", this.editForm)
		    	.then((response) => {
					this.getDevices();
		            $('#edit').modal('hide');

		            Toast.fire({
		                  icon: 'success',
		                  title: response.data.message
		            });

		            this.$Progress.finish();
				});
			},
			run() {
				let data = {
					'checked': this.checked,
					'selectedManagment': this.selectedManagment
				}

				axios.post("/devices/command", data)
		    	.then((response) => {

				});
			},
			onChange(event) {
				this.selectedManagment.com = event.target.value
			},
			onChangeGate(event) {
                if (this.editmode) {
                    this.editForm.gate = event.target.value
                } else {
                    this.form.gate = event.target.value
                }

			}
		}
	}
</script>
