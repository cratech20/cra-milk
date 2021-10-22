<template>
	<div class="container-fluid">
	    <div class="row">
	      <div class="col-12">
	        <div class="card">
	          <div class="card-header">
	            <h3 class="card-title">Клиенты</h3>

	            <div class="card-tools">
	              <button @click="newModal" class="btn btn-sm btn-primary">Зарегистрировать клиента</button>
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
	                    <button @click="devisionModal(item)" class="btn btn-sm btn-outline-primary">Перейти к подразделениям</button>
	                    <br />
	                    <button @click="farmModal(item)" class="btn btn-sm  btn-outline-primary">Перейти к фермам</button>
	                    <br />
	                    <button @click="cowGroupModal(item)" class="btn btn-sm btn-outline-primary">Перейти к группам коров</button>
	                    <br />
	                    <router-link :to="'/clients/'+item.id+'/cows'" class="btn btn-sm btn-outline-primary">Перейти к коровам</router-link>
	                    <br />
	                    <router-link :to="'/clients/'+item.id+'/devices'" class="btn btn-sm btn-outline-primary">Перейти к итоговой таблице по устройствам</router-link>
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
	                <h5 class="modal-title" v-show="!editmode">Регистрация клиента</h5>
	                <h5 class="modal-title" v-show="editmode">Редактирование клиента</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <!-- <form @submit.prevent="createUser"> -->
	            <form @submit.prevent="editmode ? updateClient() : createClient()">
	                <div class="modal-body">
	                    <div class="form-group">
	                        <label>ИНН</label>
	                        <input v-model="form.inn" type="number" name="inn"
	                            class="form-control" required :class="{ 'is-invalid': form.errors.has('name') }">
	                        <has-error :form="form" field="inn"></has-error>
	                    </div>

	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
	                    <button v-show="!editmode" type="submit" class="btn btn-primary">Создать</button>
	                </div>
	              </form>

	            </div>
	        </div>
    	</div>

    	<div class="modal fade" id="devisionModal" tabindex="-1" role="dialog" aria-labelledby="devisionModal" aria-hidden="true">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
	            <div class="modal-header">
	                <h5 class="modal-title" v-show="devisionFlag">Список подразделений компании {{formDevision.name}}</h5>
	                <h5 class="modal-title" v-show="farmFlag">Список ферм компании {{formDevision.name}}</h5>
	                <h5 class="modal-title" v-show="cowGroupFlag">Список групп коров компании {{formDevision.name}}</h5>
	                <h5 class="modal-title" v-show="cowFlag">Список коров компании {{formDevision.name}}</h5>
	                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>

	            <!-- <form @submit.prevent="createUser"> -->
	            <template v-if="devisionFlag">
	            	<form @submit.prevent="createDevision()">
		            	<div class="modal-body">
		                    <table class="table table-hover text-nowrap">
					            <thead>
					                <tr>
					                  <th>№</th>
					                  <th>Название</th>
					                  <th>Действие</th>
					                </tr>
					            </thead>
					            <tbody v-if="devisionFlag">
					                <tr v-for="(item, key) in devisions">
					                  <td>{{ key + 1 }}</td>
					                  <td>{{ item.name }}</td>
					                  <td>
					                    <button @click="devisionDel(item.id)" class="btn btn-sm btn-outline-danger">Удалить</button>
					                  </td>
					                </tr>
					            </tbody>
					            <tbody v-if="farmFlag">
					                <tr v-for="(item, key) in farms">
					                  <td>{{ key + 1 }}</td>
					                  <td>{{ item.name }}</td>
					                  <td>
					                    <button @click="devisionDel(item.id)" class="btn btn-sm btn-outline-danger">Удалить</button>
					                  </td>
					                </tr>
					            </tbody>
				            </table>

		       				<div class="modal-body">
			                    <div class="form-group">
			                        <label>Название</label>
			                        <input v-model="formDevision.newDevision" type="text" name="newDevision"
			                            class="form-control" :class="{ 'is-invalid': form.errors.has('newDevision') }">
			                        <has-error :form="form" field="newDevision"></has-error>
			                    </div>

			                </div>
		                </div>
		                <div class="modal-footer">
		                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		                    <button v-show="!editmode" type="submit" class="btn btn-primary">Создать</button>
		                </div>
		              </form>
	            </template>

	            <template v-if="farmFlag">
	            	<form @submit.prevent="createFarm()">
		            	<div class="modal-body">
		                    <table class="table table-hover text-nowrap">
					            <thead>
					                <tr>
					                  <th>№</th>
					                  <th>Название</th>
					                  <th>Действие</th>
					                </tr>
					            </thead>
					            <tbody v-if="farmFlag">
					                <tr v-for="(item, key) in farms">
					                  <td>{{ key + 1 }}</td>
					                  <td>{{ item.name }}</td>
					                  <td>
					                    <button @click="farmDel(item.id)" class="btn btn-sm btn-outline-danger">Удалить</button>
					                  </td>
					                </tr>
					            </tbody>
				            </table>

		       				<div class="modal-body">
			                    <div class="form-group">
			                        <label>Название</label>
			                        <input v-model="formFarm.newFarm" type="text" name="newFarm"
			                            class="form-control" :class="{ 'is-invalid': form.errors.has('newFarm') }">
			                        <has-error :form="form" field="newFarm"></has-error>
			                    </div>

			                </div>
		                </div>
		                <div class="modal-footer">
		                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		                    <button v-show="!editmode" type="submit" class="btn btn-primary">Создать</button>
		                </div>
		              </form>
	            </template>

	            <template v-if="cowGroupFlag">
	            	<form @submit.prevent="createCowGroup()">
		            	<div class="modal-body">
		            		<button @click="cowGroupReset()" class="btn btn-sm btn-outline-primary">Обновить группировку и наименование коров</button>
		                    <table class="table table-hover text-nowrap">
					            <thead>
					                <tr>
					                  <th>№</th>
					                  <th>Название</th>
					                  <th>Действие</th>
					                </tr>
					            </thead>
					            <tbody v-if="cowGroupFlag">
					                <tr v-for="(item, key) in cowGroups">
					                  <td>{{ key + 1 }}</td>
					                  <td>{{ item.name }}</td>
					                  <td>
					                    <button @click="cowGroupDel(item.id)" class="btn btn-sm btn-outline-danger">Удалить</button>
					                  </td>
					                </tr>
					            </tbody>
				            </table>

		       				<div class="modal-body">
			                    <div class="form-group">
			                        <label>Название</label>
			                        <input v-model="cowGroupForm.newCowGroup" type="text" name="newCowGroup"
			                            class="form-control" :class="{ 'is-invalid': form.errors.has('newCowGroup') }">
			                        <has-error :form="form" field="newCowGroup"></has-error>
			                    </div>

			                </div>
		                </div>
		                <div class="modal-footer">
		                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		                    <button v-show="!editmode" type="submit" class="btn btn-primary">Создать</button>
		                </div>
		              </form>
	            </template>

	            <template v-if="cowFlag">
	            	<form @submit.prevent="createCow()">
		            	<div class="modal-body table-responsive p-10">
		            		<button @click="cowReset()" class="btn btn-sm btn-outline-primary">Обновить группировку и наименование коров</button>
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
					            <tbody v-if="cowFlag">
					                <tr v-for="(item, key) in cows">
					                  <td>{{ key + 1 }}</td>
					                  <td>{{ item.group }}</td>
					                  <td>{{ item.cow_id }}</td>
					                  <td>{{ item.calculated_name }}</td>
					                  <td v-if="!editFlag[item.id]">{{ item.internal_code }}</td>
					                  <td v-else>
					                  <input type="number" class="form-control" v-model="item.internal_code">
					                  </td>
					                  <td>
					                    <button @click="edit(item)" class="btn btn-sm btn-outline-primary">Редактировать</button>
					                  </td>
					                </tr>
					            </tbody>
				            </table>


		                </div>
		                <div class="modal-footer">
		                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
		                </div>
		              </form>
	            </template>
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
		        clients: [],
		        editmode: false,
		        devisionFlag: false,
		        farmFlag: false,
		        cowGroupFlag: false,
		        cowFlag: false,
		        form: new Form({
		          inn: '',
		        }),
		        formDevision: new Form({
		        	id: '',
		        	name: '',
		        }),
		        formFarm: new Form({
		        	id: '',
		        	name: '',
		        }),
		        cowGroupForm: new Form({
		        	id: '',
		        	name: '',
		        }),
		        cowForm: new Form({
		        	id: '',
		        	name: '',
		        }),
		        devisions: [],
		        farms: [],
		        cowGroups: [],
		        cows:[],
		        editFlag: false,
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
			},
			devisionDel(id) {
				axios.post('/clients/divisions/del', {'id': id})
		        .then((response)=>{
		            this.getDevisions(this.formDevision.id);
		            this.formDevision.newDevision = ''
		            Toast.fire({
		                  icon: 'success',
		                  title: response.data.message
		            });

		            this.$Progress.finish();
		            return false;
		        })
			},
			farmDel(id) {
				axios.post('/clients/farms/del', {'id': id})
		        .then((response)=>{
		            this.getFarms(this.formFarm.id);
		            this.formFarm.newFarm = ''
		            Toast.fire({
		                  icon: 'success',
		                  title: response.data.message
		            });

		            this.$Progress.finish();
		            return false;
		        })
			},
			cowGroupDel(id) {
				axios.post('/clients/cows/groups/del', {'id': id})
		        .then((response)=>{
		            this.getCowGroup(this.cowGroupForm.id);
		            this.cowGroupForm.newCowGroup = ''
		            Toast.fire({
		                  icon: 'success',
		                  title: response.data.message
		            });

		            this.$Progress.finish();
		            return false;
		        })
			},
			createDevision() {
				axios.post('/clients/divisions/save', this.formDevision)
		        .then((response)=>{
		            this.getDevisions(this.formDevision.id);
		            this.formDevision.newDevision = ''
		            Toast.fire({
		                  icon: 'success',
		                  title: response.data.message
		            });

		            this.$Progress.finish();
		        })
			},
			createFarm() {
				axios.post('/clients/farms/save', this.formFarm)
		        .then((response)=>{
		            this.getFarms(this.formFarm.id);
		            this.formFarm.newFarm = ''
		            Toast.fire({
		                  icon: 'success',
		                  title: response.data.message
		            });

		            this.$Progress.finish();
		        })
			},
			createCowGroup() {
				axios.post('/clients/cows/groups/save', this.cowGroupForm)
		        .then((response)=>{
		            this.getCowGroup(this.cowGroupForm.id);
		            this.cowGroupForm.newCowGroup = ''
		            Toast.fire({
		                  icon: 'success',
		                  title: response.data.message
		            });

		            this.$Progress.finish();
		        })
			},
			getDevisions(id) {
				axios.get("/clients/"+id+"/divisions").then((response) => {
					this.devisions = response.data.divisions
				});
			},
			getFarms(id) {
				axios.get("/clients/"+id+"/farms").then((response) => {
					this.farms = response.data.farms
				});
			},
			getCowGroup(id) {
				axios.get("/clients/"+id+"/cows/groups").then((response) => {
					this.cowGroups = response.data.cowGroups
				});
			},
			getCow(id) {
				axios.get("/clients/"+id+"/cows").then((response) => {
					this.cows = response.data.cows
				});
			},
			devisionModal(client) {
				this.getDevisions(client.id);
				this.devisionFlag = true;
				this.farmFlag = false;
				this.cowFlag = false;
				this.cowGroupFlag = false;
		        this.formDevision.reset();
		        $('#devisionModal').modal('show');
		        this.formDevision.fill(client);
			},
			farmModal(client) {
				this.getFarms(client.id);
				this.devisionFlag = false;
				this.cowFlag = false;
				this.cowGroupFlag = false;
				this.farmFlag = true;
		        this.formFarm.reset();
		        $('#devisionModal').modal('show');
		        this.formFarm.fill(client);
			},
			cowGroupModal(client) {
				this.getCowGroup(client.id);
				this.cowGroupFlag = true;
				this.cowFlag = false;
				this.devisionFlag = false;
				this.farmFlag = false;
		        this.cowGroupForm.reset();
		        $('#devisionModal').modal('show');
		        this.cowGroupForm.fill(client);
			},
			cowModal(client) {
				this.getCow(client.id);
				this.cowFlag = true;
				this.cowGroupFlag = false;
				this.devisionFlag = false;
				this.farmFlag = false;
		        this.cowGroupForm.reset();
		        $('#devisionModal').modal('show');
		        this.cowGroupForm.fill(client);
			},
			newModal(){
		        this.editmode = false;
		        this.form.reset();
		        $('#addNew').modal('show');
		    },
		    createClient() {
		    	axios.post("/users/create/form", this.form)
		    	.then((response) => {
					this.getClients();
		            $('#addNew').modal('hide');

		            Toast.fire({
		                  icon: 'success',
		                  title: response.data.message
		            });

		            this.$Progress.finish();
				});
		    },
		    cowGroupReset() {
		    	axios.get("/clients/cows/linking").then((response) => {
					//this.farms = response.data.farms
				});
		    },
		    edit(item) {
		    	this.editFlag[item.id] = true
		    },
		}
	}
</script>
