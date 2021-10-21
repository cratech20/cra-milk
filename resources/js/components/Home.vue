<template>
  <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>150</h3>

                <p>New Orders</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>53<sup style="font-size: 20px">%</sup></h3>

                <p>Bounce Rate</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>44</h3>

                <p>User Registrations</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>65</h3>

                <p>Unique Visitors</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-md-3">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Дата</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-10">
                <select class="form-control" v-model="selectedDate" @change="onChange($event)">
                    <option v-for="item in dates" :value="item">{{item}}</option>
                </select>
              </div>
              <!-- /.card-body -->
            </div>

            <div class="card" v-if="macs.length > 0">
              <div class="card-header">
                <h3 class="card-title">Коровы</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-10">
                <div style="margin-top: 10px; height: 300px; overflow-y: auto;">
                    <template v-for="item in macs">
                        <input type="radio" name="mac" @click="changeMac($event)" :id="item.code" :value="item.code">
                        <label :for="item.code">{{item.value}}</label><br>
                    </template>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <div class="col-md-9">
            <line-chart :chart-data="data" :height="300" :options="{responsive: true}"></line-chart>
        </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
</template>
<script>
    import LineChart from './LineChart.js'
    export default {
        components: {
            LineChart
        },
        data() {
            return {
                data: null,
                dates: [],
                macs: [],
                selectedDate: []
            }
        },
        created() {
            this.getData()
            this.getDates()
        },
        methods: {
            getData() {
                axios.get("/get-data").then((response) => {
                  this.data = response.data.data
                });
            },
            getDates() {
                axios.get("/get-dates").then((response) => {
                    this.dates = response.data
                });
            },
            onChange(event) {
                axios.post("/get-mac", {'data': event.target.value}).then((response) => {
                    this.macs = response.data
                });
            },
            changeMac(event) {
                let postData = {
                    'mac': event.target.value,
                    'date': this.selectedDate
                }

                axios.post("/get-chart-data", postData).then((response) => {
                    // this.macs = response.data
                    this.data = response.data.data
                });
            }
        }
    }
</script>
