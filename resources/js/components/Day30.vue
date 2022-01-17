<template>
    <div class="container-fluid">
        <div class="row">
            <div class="row">

                    <div class="col-lg-2 col-2">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success">Скачать</button>
                            <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" role="menu">
                                <a class="dropdown-item" href="#">Excel</a>
                                <a class="dropdown-item" href="#">Pdf</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-2">

                        <router-link :to="'/reports'" style="margin-left: 50px;" class="btn btn-success">Назад</router-link>
                    </div>
                </div>

        </div>
        <!-- /.row -->
        <div class="row table-responsive p-0">
            <table class="table table-hover text-nowrap">
                <thead>
                    <tr>
                        <th>№</th>
                        <th>
                            <a href="#" @click="sortBy('u_name')" :class="{ active: sortKey === 'u_name' }">
                            Корова
                            </a>
                        </th>
                        <th>
                        <a href="#" @click="sortBy('name')" :class="{ active: sortKey === 'name' }">
                            № коровы
                        </a>
                        </th>
                        <th>
                            <a href="#" @click="sortBy('serial_number')" :class="{ active: sortKey === 'serial_number' }">
                            Внутренний номер
                            </a>
                        </th>
                        <th v-for="item in date">
                            <a href="#" @click="sortBy('token')" :class="{ active: sortKey === 'token' }">
                            {{ item }}
                            </a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in data">
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.code }}</td>
                        <td>{{ item.num5 }}</td>
                        <td>{{ item.num }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div><!-- /.container-fluid -->
</template>
<script>

export default ({
    data() {
        return {
            data: [],
            date: []
        }
    },
    created() {
        this.getData()
    },
    methods: {
        getData() {
            axios.get("/reports/get-day-report30").then((response) => {
               this.date = response.data.dates
               this.data = response.data.value
            });
        }
    }
})
</script>

