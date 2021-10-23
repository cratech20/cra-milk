<template>
	<div class="container-fluid">
	    <div class="row">
	      <div class="col-12">
	        <div class="card">
	          <div class="card-header">
	            <h3 class="card-title">Шлюзы</h3>

	            <div class="card-tools">
                    <div class="input-group" style="width: 150px; float: right; margin-left: 10px;">
						<input type="text" v-model="search" class="form-control">
						<div class="input-group-append">
							<span class="input-group-text">Поиск</span>
						</div>
					</div>
	                <button @click="newModal" class="btn btn-sm btn-primary">Добавить шлюз</button>
	            </div>
	        </div>
	          <!-- /.card-header -->
	          <div class="card-body table-responsive p-0">
	            <table class="table table-hover text-nowrap">
	              <thead>
	                <tr>
	                  <th>№</th>
	                  <th>
						  <a href="#" @click="sortBy('u_name')" :class="{ active: sortKey === 'u_name' }">
							Клиент
						  </a>
					  </th>
	                  <th>
						<a href="#" @click="sortBy('name')" :class="{ active: sortKey === 'name' }">
							Название
						</a>
					  </th>
	                  <th>
						  <a href="#" @click="sortBy('serial_number')" :class="{ active: sortKey === 'serial_number' }">
							Серийный номер
						  </a>
					  </th>
	                  <th>
						  <a href="#" @click="sortBy('device_id')" :class="{ active: sortKey === 'device_id' }">
							ID
						  </a>
					  </th>
	                  <th>
						  <a href="#" @click="sortBy('created_at')" :class="{ active: sortKey === 'created_at' }">
							Дата добавления
						  </a>
					  </th>
	                  <th>Действие</th>
	                </tr>
	              </thead>
	              <tbody>
	                <tr v-for="(p, index) in filteredDevices" :key="index">
	                  <td>{{ index + 1 }}</td>
	                  <td>{{ p.u_name }}</td>
	                  <td>{{ p.name }}</td>
	                  <td>{{ p.serial_number }}</td>
	                  <td>{{ p.device_id }}</td>
	                  <td>{{ p.created_at }}</td>
	                  <td>
	                    <button @click="showMessage(p)" class="btn btn-sm btn-outline-primary">Просмотреть сообщения</button>
	                    <br />
	                    <button @click="editModal(p)" class="btn btn-sm btn-outline-primary">Редактировать</button>
	                    <br />
	                    <button @click="delGate(p.id)" class="btn btn-sm btn-outline-danger">Удалить</button>
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
	                <h5 class="modal-title">Добавление шлюза</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <!-- <form @submit.prevent="createUser"> -->
	            <form @submit.prevent="createGate()">
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
	                        <label>Описание</label>
	                        <input v-model="form.description" type="text" name="name"
	                            class="form-control" required :class="{ 'is-invalid': form.errors.has('description') }">
	                        <has-error :form="form" field="name"></has-error>
	                    </div>
                        <div class="form-group">
                            <label>Устройства</label>
                            <multiselect v-model="form.devices" required
                                tag-placeholder="Add this as new tag"
                                placeholder="Search or add a tag"
                                label="name"
                                track-by="code" :options="options" :multiple="true"
                                :taggable="true" @tag="addTag">
                            </multiselect>
                            <has-error :form="form" field="type"></has-error>
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
	                <h5 class="modal-title">Редактирование шлюза {{ editForm.name }}</h5>
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
	                        <label>Описание</label>
	                        <input v-model="editForm.description" type="text" name="name"
	                            class="form-control" required :class="{ 'is-invalid': form.errors.has('description') }">
	                        <has-error :form="form" field="name"></has-error>
	                    </div>
                        <div class="form-group">
                            <label>Устройства</label>
                            <multiselect v-model="editForm.devices" required
                                tag-placeholder="Add this as new tag"
                                placeholder="Search or add a tag"
                                label="name"
                                track-by="code" :options="options" :multiple="true"
                                :taggable="true" @tag="addTag">
                            </multiselect>
                            <has-error :form="form" field="type"></has-error>
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
	                <h5 class="modal-title" v-show="!editmode">Сообщения шлюза {{ messageForm.name }}</h5>
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
    import Multiselect from 'vue-multiselect'
	export default {
		components: { VueTimepicker, Multiselect },
		data() {
			return {
				csrf: document
		          .querySelector('meta[name="csrf-token"]')
		          .getAttribute("content"),
		        devices: [],
                search: '',
		        editmode: false,
		        form: new Form({
                  id: null,
		          name: '',
		          device_id: '',
		          password: '',
		          serial_number: '',
                  description: '',
                  devices: []
		        }),
		        editForm: new Form({
                  id: null,
		          name: '',
		          device_id: '',
		          password: '',
		          serial_number: '',
                  description: '',
                  devices: []
		        }),
		        messageForm: new Form({
		          name: '',
		          ya_id: '',
		          ya_password: '',
		          ya_number: ''
		        }),
		        time: new Date(),
		        message: [],
                gates: [],
				selectedGate: [],
				sortKey: 'name',
				reverse: false,
                options: [],
			}
		},
        computed: {
			sortedDevuces() {
				const k = this.sortKey;
				return this.gates.sort((a, b) => {
					return (a[k] < b[k] ? -1 : a[k] > b[k] ? 1 : 0) * [ 1, -1 ][+this.reverse];
				});
			},
            filteredDevices() {
                let obj = this.sortedDevuces;
				let newArray = [];
				let key = null;
				let el = null;
				const serach = this.search.toLowerCase();
				for (key in obj) {
					el = obj[key]
					if (el.name.toLowerCase().indexOf(serach) != -1) newArray.push(el);
				}
				return newArray;
            },
        },
		created() {
			this.getGates()
		},
		methods: {
            addTag (newTag) {
                const tag = {
                    name: newTag,
                    code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
                }
                this.options.push(tag)
                this.value.push(tag)
            },
			sortBy(sortKey) {
				this.reverse = (this.sortKey == sortKey) ? !this.reverse : false;
				this.sortKey = sortKey;
			},
            getDevices() {
				axios.get("/devices/get-all").then((response) => {
                    for (let i = 0; i < response.data.devices.length; i++) {
                        this.options.push({
                            'name': response.data.devices[i].name,
                            'code': response.data.devices[i].id
                        });
                    }
				});
			},
            getGates() {
				axios.get("/gates/get-gates").then((response) => {
					this.gates = response.data.gates
				});
			},
			newModal() {
				this.editmode = false;
		        this.form.reset();
		        $('#addNew').modal('show');
                this.getDevices()
			},
			editModal(item) {
				this.editmode = true;
		        this.editForm.reset();
		        $('#edit').modal('show');
		        this.editForm.fill(item);
                this.getDevices()
			},
			getDeviceMessage(item) {
				axios.get("/devices/"+item.device_id+"/messages").then((response) => {
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
			createGate() {
				axios.post("/gates/save", this.form)
		    	.then((response) => {
					this.getGates();
		            $('#addNew').modal('hide');

		            Toast.fire({
		                  icon: 'success',
		                  title: response.data.message
		            });

		            this.$Progress.finish();
				});
			},
            delGate(id) {
                Swal.fire({
                title: 'Вы уверены что хотите удалить шлюз?',
                text: "",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Да'
                }).then((result) => {

                    // Send request to the server
                    if (result.value) {
                        axios.get("/gates/del/"+id).then(()=>{
                                Swal.fire(
                                'Успешно!',
                                'Шлюз успешно удален',
                                'success'
                                );
                            this.getGates();
                        }).catch((data)=> {
                            Swal.fire("Ошибка!", data.message, "warning");
                        });
                    }
                })
            },
			updateClient() {
				axios.post("/gates/update", this.editForm)
		    	.then((response) => {
					this.getGates();
		            $('#edit').modal('hide');

		            Toast.fire({
		                  icon: 'success',
		                  title: response.data.message
		            });

		            this.$Progress.finish();
				});
			},
		}
	}
</script>
