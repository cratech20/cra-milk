<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Пользователи</h3>

            <div class="card-tools">
              <button @click="editRight" class="btn btn-sm btn-primary">Назначить роли</button>
              <button @click="newModal" class="btn btn-sm btn-success">Зарегистрировать
                  пользователя</button>
              <a href="" class="btn btn-sm btn-success">Зарегистрировать
                  клиента</a>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap">
              <thead>
                <tr>
                  <th>№</th>
                  <th>Имя</th>
                  <th>E-mail</th>
                  <th>Действие</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(item, key) in users">
                  <td>{{ key + 1 }}</td>
                  <td>{{ item.name }}</td>
                  <td>{{ item.email }}</td>
                  <td>
                    <button @click="editModal(item)" class="btn btn-sm btn-outline-primary">Редактировать</button>
                    <a href="#" class="btn btn-sm btn-outline-danger">Заблокировать</a>
                    <button @click="delUser(item.id)" class="btn btn-sm btn-outline-danger">Удалить</button>
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
    <!-- Modal -->
    <div class="modal fade" id="editRight" tabindex="-1" role="dialog" aria-labelledby="editRight" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" v-show="editrightflag">Роли пользователей</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- <form @submit.prevent="editRight"> -->
            <form @submit.prevent="updateRight()">
                <div class="modal-body table-responsive p-0">
                  <p>
                    * - админ - admin<br>
                    * - производство - production<br>
                    * - сервисник - service technician<br>
                    * - монтажник - fitter<br>
                    * - техподдержка - support<br>
                    * - менеджер от нас - manager<br>
                    * - предприятие - client<br>
                    * - разработчик - developer<br>
                    * - наш работник - employee
                  </p>

                  <table class="table table-hover">
                    <thead>
                        <tr>
                          <th>Имя</th>
                          <th v-for="(item, index) in roles">{{item.name}}</th>
                        </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(item2, index2) in users">
                        <td>{{item2.name}} ({{item2.email}})</td>
                        <td v-for="(item3, index) in roles">
                          <template v-for="item4 in item2.roles">
                            <input type="checkbox" v-show="item4.name == item3.name" checked value="1" :name="'users['+item2.id+']['+item3.name+']'">
                            <input type="checkbox" v-show="item4.name != item3.name" value="1" :name="'users['+item2.id+']['+item3.name+']'">
                          </template>
                          
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                    
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button v-show="editmode" type="submit" class="btn btn-success">Сохранить</button>
                    <button v-show="!editmode" type="submit" class="btn btn-primary">Создать</button>
                </div>
              </form>
            
            </div>
        </div>
    </div>
    <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" v-show="!editmode">Регистрация пользователя</h5>
                <h5 class="modal-title" v-show="editmode">Редактирование пользователя</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- <form @submit.prevent="createUser"> -->
            <form @submit.prevent="editmode ? updateUser() : createUser()">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Название</label>
                        <input v-model="form.name" type="text" name="name"
                            class="form-control" required :class="{ 'is-invalid': form.errors.has('name') }">
                        <has-error :form="form" field="name"></has-error>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input v-model="form.email" type="text" name="email"
                            class="form-control" required :class="{ 'is-invalid': form.errors.has('email') }">
                        <has-error :form="form" field="email"></has-error>
                    </div>
                    <div class="form-group">
                        <label>Новый пароль</label>
                        <input v-model="form.password" type="password" name="password"
                            class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                        <has-error :form="form" field="password"></has-error>
                    </div>

                    <div class="form-group">
                        <label>Роль</label>
                        <multiselect v-model="form.role" tag-placeholder="Add this as new tag" placeholder="Search or add a tag" label="name" track-by="code" :options="options" :multiple="true" :taggable="true" @tag="addTag"></multiselect>
                        <has-error :form="form" field="type"></has-error>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
                    <button v-show="editmode" type="submit" class="btn btn-success">Сохранить</button>
                    <button v-show="!editmode" type="submit" class="btn btn-primary">Создать</button>
                </div>
              </form>
            
            </div>
        </div>
    </div>
  </div><!-- /.container-fluid -->
</template>

<script>
  import Multiselect from 'vue-multiselect'
  export default {
    components: { Multiselect },
    data() {
      return {
        csrf: document
          .querySelector('meta[name="csrf-token"]')
          .getAttribute("content"),
        users: [],
        editmode: false,
        editrightflag: false,
        roles: [],
        options: [],
        options2: [],
        form: new Form({
          id : '',
          name: '',
          email: '',
          password: '',
          role: [],
        }),
        formRight: new Form({
          id : '',
        })
      }
    },
    created() {
      this.getUsers();
    },
    methods: {
      getUsersAndRoles() {
        axios.get("/users/roles").then((response) => {
          for (let i = 0; i < response.data.roles.length; i++) {
            this.options.push({
              'name': response.data.roles[i].name,
              'code': response.data.roles[i].id
            });
          }
        });
      },
      getUsers() {
        axios.get("/users/getall").then((response) => {
          this.users = response.data.users;
        });
      },
      editRight() {
        this.getUsersAndRoles();
        this.editrightflag = true;
        this.formRight.reset();
        $('#editRight').modal('show');
      },
      updateRight() {

      },
      delUser(id) {
        Swal.fire({
        title: 'Вы уверены что хотите удалить пользователя?',
        text: "",
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Да'
        }).then((result) => {

            // Send request to the server
           if (result.value) {
              axios.get("/users/delete/" + id).then(()=>{
                      Swal.fire(
                      'Успешно!',
                      'Пользователь успешно удален',
                      'success'
                      );
                  this.getUsers();
              }).catch((data)=> {
                Swal.fire("Ошибка!", data.message, "warning");
              });
           }
        })
      },
      newModal(){
        this.getUsersAndRoles();
        this.editmode = false;
        this.form.reset();
        $('#addNew').modal('show');
      },
      editModal(user){
        this.getUsersAndRoles();
        this.editmode = true;
        this.form.reset();
        $('#addNew').modal('show');
        this.form.fill(user);
      },
      createUser(){
        axios.post('/users/create', this.form)
        .then((response)=>{
            this.getUsers();
            $('#addNew').modal('hide');

            Toast.fire({
                  icon: 'success',
                  title: response.data.message
            });

            this.$Progress.finish();
        })
      },
      addTag (newTag) {
        const tag = {
          name: newTag,
          code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
        }
        this.options.push(tag)
        this.value.push(tag)
      },
      updateUser(){
        axios.post('users/change-password', this.form)
        .then((response) => {
            this.getUsers();
            $('#addNew').modal('hide');
            Toast.fire({
              icon: 'success',
              title: response.data.message
            });
            this.$Progress.finish();
        })
      },
    }
  }
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>