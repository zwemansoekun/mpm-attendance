<template>
    <div class="col-md-10">
            
        <div class="container mt-5">
            <!-- //form -->
            <form @submit.prevent="updateMoney">
            <table class="table table-bordered">
                <tr class="d-flex">
                    <td class="col-6">為替レートデフォルト値(JPN/MMK)</td>
                    <td class="col-3">
                        <input type="number" class="form-control" v-model="setting.money">
                        
                    </td>
                    <td class="col-3 align-center">
                        <button class="btn btn-primary">編集</button>
                    </td>
                </tr>
            </table>
            </form>

            
            <table class="table table-bordered">
                <form @submit.prevent="updateAm">
                <tr class="d-flex">
                    <td class="col-6">AM遅刻許容時間デフォルト値（分)</td>
                     <td class="col-3">
                        <input type="text" class="form-control" v-model="setting.am">
                    </td>
                    <td class="col-3">
                        <button class="btn btn-primary">編集</button>
                    </td>
                </tr>
                </form>

                <form @submit.prevent="updatePm">
                <tr class="d-flex">
                    <td class="col-6">PM遅刻許容時間デフォルト値（分）</td>
                     <td class="col-3">
                        <input type="text" class="form-control" v-model="setting.pm">
                    </td>
                    <td class="col-3">
                        <button class="btn btn-primary">編集</button>
                    </td>
                </tr>
                </form>
            </table>
        
             <table class="table table-bordered">
                <thead>
                    <tr >
                        <th scope="col">年月</th>
                        <th scope="col">AM遅刻許容時間（分)</th>
                        <th scope="col">PM遅刻許容時間（分）</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="d in delays" :key="d.id">
                        <td>
                            {{ d.month}}
                        </td>
                        <td>
                            <div class="row">
                                <div class="col"><input type="text" class="form-control" v-model="d.am"></div>
                                <div class="col"><button class="btn btn-primary" @click="updateDelayAm(d.id ,d)">編集</button></div>
                            </div>
                        </td>
                        <td>
                             <div class="row">
                                <div class="col"><input type="text" class="form-control" v-model="d.pm"></div>
                                <div class="col"><button class="btn btn-primary" @click="updateDelayPm(d.id ,d)">編集</button></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

        </div>
       
    </div>    
</template>

<script>
       

    export default {
        
        data() {
            return {
                setting:{},
                dates:[],
                delays:[],
                delay:{},
                attendDelays:[]
            }
        },
        created() {
            this.axios
            .get('http://127.0.0.1:8000/api/setting')
            .then(response => {
                this.setting=response.data;
            });
            this.axios
            .get('http://127.0.0.1:8000/api/delayTimes')
            .then(response => {
                this.delays=response.data;
                
            });
            this.axios
                .post('http://localhost:5000/attendances/attendanceDate')
                .then(response => {
                    this.dates=response.data;
                    this.dates.forEach(date => {
                        this.delay.month = date.recordedDateTime;
                        this.attendDelays.push(this.delay);
                 })
            });
           
        },
        mounted(){
            this.storeDelayTime();
        },
       
        methods: {
            updateMoney(){
               this.axios
               .post('http://127.0.0.1:8000/api/setting/updateMoney', this.setting)
               .then((response) => {
                   this.$router.push({name: 'setting'});
               })
           },
            updateAm(){
               this.axios
               .post('http://127.0.0.1:8000/api/setting/updateAm', this.setting)
               .then((response) => {
                   this.$router.push({name: 'setting'});
               })
            },
            updatePm(){
               this.axios
               .post('http://127.0.0.1:8000/api/setting/updatePm', this.setting)
               .then((response) => {
                   this.$router.push({name: 'setting'});
               })
            },
            updateDelayAm(id , delayTime){
               this.axios
               .post('http://127.0.0.1:8000/api/delayTime/updateAm/'+id, delayTime)
               .then((response) => {
                   this.delays=response.data;
               })
            },
            updateDelayPm(id , delayTime){
               this.axios
               .post('http://127.0.0.1:8000/api/delayTime/updatePm/'+id, delayTime)
               .then((response) => {
                   this.delays=response.data;
               })
            },
            storeDelayTime(){
               this.axios
               .post('http://127.0.0.1:8000/api/delayTime/store', this.setting)
               .then((response) => {
                   console.log(response.data);
               })
            }
        }
    }
</script>