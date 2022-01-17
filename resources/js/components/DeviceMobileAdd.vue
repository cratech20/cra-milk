<template>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Добавление устройства</h3>
        </div>

        <div class="card-body table-responsive">
            <form @submit.prevent="createDevice()">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Название</label>
                        <input type="text" v-model="device.name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>ID в Я.Облако *</label>
                        <input type="text" v-model="device.device_id" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Пароль в Я.Облако</label>
                        <input type="text" v-model="device.password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Серийный номер *</label>
                        <input type="text" v-model="device.serial_number" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Токен</label>
                        <input type="text" v-model="device.token" class="form-control" disabled>
                    </div>
                    <div class="form-group">
                        <label for="gate">Шлюз *</label>
                        <select class="form-control" v-model="device.gate_id" id="gate" name="gate">
                            <option v-for="(item, key) in gates" :value="item.id">{{ item.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button @click="back" class="btn btn-sm btn-outline-primary">Назад</button>
                    <input type="submit" class="btn btn-sm btn-outline-primary" value="Сохранить">
                </div>
            </form>
        </div>
    </div>
</template>
<script>

export default {
    data() {
        return {
            device: new Form({
                id: null,
                name: '',
                device_id: '',
                password: '',
                serial_number: '',
                gate_id: null,
                token: ''
            }),
            gates: [],
        }
    },
    created() {
        this.isMobile();
        this.getGates();
        this.getToken();
    },
    methods: {
        isMobile() {
            if( screen.width <= 760 ) {
            } else {
                this.$router.push("/devices")
            }
        },
        getToken() {
            axios.get("/devices/get-token").then((response) => {
                this.device.token = response.data
            });
        },
        getGates() {
            axios.get("/gates/get-gates").then((response) => {
                this.gates = response.data.gates
            });
        },
        back() {
            this.$router.push("/devices")
        },
        getDevice() {
            axios.get("/devices/"+this.id).then((response) => {
                this.device = response.data
            });
        },
        createDevice() {
            axios.post("/devices/save", this.device)
            .then((response) => {
                this.$router.push("/devices")

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
