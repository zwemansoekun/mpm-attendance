<template>
    <div class="col-md-10">
            
        <div class="container mt-5">
            <div class="alert alert-success" role="alert" v-if="data_check_messg">
                 <strong >データは成功しました。</strong> 
            </div>
            <!-- //form -->
            <form @submit.prevent="updateMoney">
            <table class="table table-bordered">
                <tr class="d-flex">
                    <td class="col-6">為替レートデフォルト値(JPN/MMK)</td>
                    <td class="col-3">
                        {{ errorMoney }}
                        <input class="form-control" v-model.number="setting.money">
                    </td>
                    <td class="col-3 align-center">
                        <button type="submit" class="btn btn-primary" onclick="this.blur();">編集</button>
                    </td>
                </tr>
            </table>
            </form>

            <table class="table table-bordered">

                <tr class="d-flex">
                    <td class="col-6">AM遅刻許容時間デフォルト値（分)</td>
                     <td class="col-3">
                          {{ errorAm }}
                        <input type="text" class="form-control" v-model="setting.am">
                    </td>
                    <td class="col-3">
                        <button class="btn btn-primary" @click="updateAm" onclick="this.blur();">編集</button>
                    </td>
                </tr>
                <tr class="d-flex">
                    <td class="col-6">PM遅刻許容時間デフォルト値（分）</td>
                     <td class="col-3">
                          {{ errorPm }}
                        <input type="text" class="form-control" v-model="setting.pm">
                    </td>
                    <td class="col-3">
                        <button class="btn btn-primary" @click="updatePm" onclick="this.blur();">編集</button>
                    </td>
                </tr>
                
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
                            <div class="row" v-if="d.amDelayError">
                                <div class="col">{{d.amDelayErrorMsg}}</div>
                             </div>
                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" v-model="d.am">
                                </div>
                                <div class="col"><button class="btn btn-primary" @click="updateDelayAm(d.id ,d)" onclick="this.blur();">編集</button></div>
                            </div>
                        </td>
                        <td>
                            <div class="row" v-if="d.pmDelayError">
                                <div class="col">{{d.pmDelayErrorMsg}}</div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" v-model="d.pm">
                                </div>
                                <div class="col"><button class="btn btn-primary" @click="updateDelayPm(d.id ,d)" onclick="this.blur();">編集</button></div>
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
                attendDelays:[],
                errorMoney: null,
                errorAm: null,
                errorPm: null,
                successMsg: null,
                data_check_messg:false
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
                .get('http://localhost:5000/attendances/all/date')
                .then(response => {
                    this.dates=response.data;
                    this.dates.forEach(date => {
                        this.delay.month = date.recordedDateTime;
                        this.attendDelays.push(this.delay);
                 })
            });
           
        },
        methods: {
            updateMoney(){
                this.errorMoney = null;
                if(this.setting.money == 0){
                   return this.errorMoney = '為替レートデフォルト値を入力してください。';
                }else if(!this.validateDecimal(this.setting.money) ){
                    return this.errorMoney = '為替レートデフォルト値を数式で入力してください。';
                }
                this.axios
                .post('http://127.0.0.1:8000/api/setting/updateMoney', this.setting)
                .then((response) => {

                    this.setting = response.data;
                    this.errorAm = null;
                    this.errorPm = null;
                    
                    this.data_check_messg = true;
                    setTimeout(() => {
                        this.data_check_messg = false;
                    },2000)

                }) 
           },
            updateAm(){
                this.errorAm = null;
                if(this.setting.am == 0){
                   return this.errorAm = 'AM遅刻許容時間デフォルト値を入力してください。';
                }else if(!this.validateNumber(this.setting.am) ){
                    return this.errorAm = 'AM遅刻許容時間デフォルト値を数式で入力してください。';
                }
                this.axios
                .post('http://127.0.0.1:8000/api/setting/updateAm', this.setting)
                .then((response) => {

                    this.setting = response.data;
                    this.errorMoney = null;
                    this.errorPm = null;
                   
                    this.data_check_messg = true;
                    setTimeout(() => {
                        this.data_check_messg = false;
                    },2000)
                })
            },
            updatePm(){
                this.errorPm = null;
                if(this.setting.pm == 0){
                   return this.errorPm = 'PM遅刻許容時間デフォルト値を入力してください。';
                }else if(!this.validateNumber(this.setting.pm) ){
                    return this.errorPm = 'PM遅刻許容時間デフォルト値を数式で入力してください。';
                }
                this.axios
                .post('http://127.0.0.1:8000/api/setting/updatePm', this.setting)
                .then((response) => {

                    this.setting = response.data;
                    this.errorMoney = null;
                    this.errorAm = null;
                   
                    this.data_check_messg = true;
                    setTimeout(() => {
                        this.data_check_messg = false;
                    },2000)

                })
            },
            updateDelayAm(id , delayTime){
                if(delayTime.am == 0){
                    Vue.set(delayTime,"amDelayErrorMsg", "AM遅刻許容時間を入力してください。");
                    Vue.set(delayTime,"amDelayError",true);
                    return ;
                }else if(!this.validateNumber(delayTime.am) ){
                    Vue.set(delayTime,"amDelayErrorMsg", "AM遅刻許容時間を数式で入力してください。");
                    Vue.set(delayTime,"amDelayError",true);
                    return ;
                }

                this.axios
                .post('http://127.0.0.1:8000/api/delayTime/updateAm/'+id, delayTime)
                .then((response) => {
                   this.delays=response.data;
                   this.data_check_messg = true;
                    setTimeout(() => {
                        this.data_check_messg = false;
                    },2000)
                })
            },
            updateDelayPm(id , delayTime){
                 if(delayTime.pm == 0){
                    Vue.set(delayTime,"pmDelayErrorMsg", "PM遅刻許容時間を入力してください。");
                    Vue.set(delayTime,"pmDelayError",true); 
                    return ;
                }else if(!this.validateNumber(delayTime.pm) ){
                    Vue.set(delayTime,"pmDelayErrorMsg", "PM遅刻許容時間を数式で入力してください。");
                    Vue.set(delayTime,"pmDelayError",true);
                   return ;
                }
               this.axios
               .post('http://127.0.0.1:8000/api/delayTime/updatePm/'+id, delayTime)
               .then((response) => {
                   this.delays=response.data;
                   this.data_check_messg = true;
                    setTimeout(() => {
                        this.data_check_messg = false;
                    },2000)
               })
            },
            validateNumber: function(number){
                var re = /^\d*$/;
                return re.test(number);
            },
            validateDecimal: function(number){
                var re = /^-?\d+(?:\.?\d+)?$/;
                return re.test(number);
            }

        }
    }
</script>