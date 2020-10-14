<template>
    <div class="col-md-10">
            
        <div class="container">
            <div class="alert alert-success" role="alert" v-if="data_check_messg">
                 <strong >Data is Successfully Saved!</strong> 
            </div>
            <!-- //form -->
            <form @submit.prevent="updateMoney">
            <table class="table table-bordered">
                <tr class="d-flex">
                    <th class="col-6 bg-info text-white" scope="row">Exchange rate default value (JPN / MMK)</th><!--為替レートデフォルト値(JPN/MMK)-->
                    <td class="col-3">
                        <label class="text-danger" v-if="errorMoney">{{ errorMoney }}</label>
                        <input class="form-control" v-model.number="setting.money">
                    </td>
                    <td class="col-3 text-center">
                        <button type="submit" class="btn btn-primary" onclick="this.blur();">Edit</button>
                    </td>
                </tr>
            </table>
            </form>

            <table class="table table-bordered">

                <tr class="d-flex">
                    <th class="col-6 bg-info text-white" scope="row">AM late allowance time default value (minutes)</th><!--AM遅刻許容時間デフォルト値（分)-->
                     <td class="col-3">
                        <label class="text-danger" v-if="errorAm">{{ errorAm }}</label> 
                        <input type="text" class="form-control" v-model="setting.am">
                    </td>
                    <td class="col-3 text-center">
                        <button class="btn btn-primary" @click="updateAm" onclick="this.blur();">Edit</button>
                    </td>
                </tr>
                <tr class="d-flex">
                    <th class="col-6 bg-info text-white" scope="row">PM late allowance time default value (minutes)</th><!--PM遅刻許容時間デフォルト値（分）-->
                     <td class="col-3">
                         <label class="text-danger" v-if="errorPm">{{ errorPm }}</label>
                        <input type="text" class="form-control" v-model="setting.pm">
                    </td>
                    <td class="col-3 text-center">
                        <button class="btn btn-primary" @click="updatePm" onclick="this.blur();">Edit</button>
                    </td>
                </tr>
                
            </table>

            <table class="table table-bordered">
                <thead class="bg-info text-white">
                    <tr>
                        <th scope="col">Year/Month</th><!--年月-->
                        <th scope="col">AM late allowance time (minutes)</th><!-- AM遅刻許容時間（分)-->
                        <th scope="col">PM late allowance time (minutes)</th><!-- PM遅刻許容時間（分）-->
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="d in attendDelays" :key="d.id">
                        <td>
                            {{ d.month}}
                        </td>
                        <td>
                            <div class="row" v-if="d.amDelayError">
                                <div class="col text-danger">{{d.amDelayErrorMsg}}</div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" v-model="d.am">
                                </div>
                                <div class="col"><button class="btn btn-primary" @click="updateDelayAm(d.id ,d)" onclick="this.blur();">Edit</button></div>
                            </div>
                            
                        </td>
                        <td>
                            <div class="row" v-if="d.pmDelayError">
                                <div class="col text-danger">{{d.pmDelayErrorMsg}}</div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" v-model="d.pm">
                                </div>
                                <div class="col"><button class="btn btn-primary" @click="updateDelayPm(d.id ,d)" onclick="this.blur();">Edit</button></div>
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
                settings: [],
                attendDelays:[],
                temp: [],
                errorMoney: null,
                errorAm: null,
                errorPm: null,
                data_check_messg:false
            }
        },
        created() {
            let that=this;
            this.axios({
                url:process.env.MIX_APP_URL+"/settings",//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/settings",
                method: 'get'
            })
            .then(function (response) {
                that.setting=response.data;
            })
            .catch(function (error) {
            });

            this.axios({
                url:process.env.MIX_APP_URL+"/setting/all",//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/setting/all",
                method: 'get'
            })
            .then(function (response) {
                that.settings=response.data;
            })
            .catch(function (error) {
            });

            this.axios
                .get(process.env.MIX_APP_API_URL+'/attendances/all/date')
                .then(response => {
                    this.dates=response.data;
            });

            this.axios({
                url:process.env.MIX_APP_URL+"/delayTimes",//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/delayTimes",
                method: 'get'
            })
            .then(function (response) {
                that.delays=response.data;
                that.delayDataCalculate();
            })
            .catch(function (error) {
            });          
           
        },
        methods: {
            updateMoney(){
                this.errorMoney = null;
                if(this.setting.money == 0){
                   return this.errorMoney = 'Please enter the Exchange rate default value.';//為替レートデフォルト値を入力してください。
                }else if(!this.validateDecimal(this.setting.money) ){
                    return this.errorMoney = 'Please Enter the exchange rate default value in the formula.';//為替レートデフォルト値を数式で入力してください。
                }

                let that=this;
                this.axios({
                    url:process.env.MIX_APP_URL+"/setting/updateMoney/"+this.setting.id,//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/setting/updateMoney/"+this.setting.id,
                    method: 'post',
                    data: this.setting
                })
                .then(function (response) {
                    that.setting = response.data;
                    that.errorAm = null;
                    that.errorPm = null;
                    
                    that.data_check_messg = true;
                    setTimeout(() => {
                        that.data_check_messg = false;
                    },2000)
                })
                .catch(function (error) {
                });
           },
            updateAm(){
                this.errorAm = null;
                if(this.setting.am == 0){
                   return this.errorAm = 'Please Enter the  AM Late Allowance Time default value.';//AM遅刻許容時間デフォルト値を入力してください。
                }else if(!this.validateNumber(this.setting.am) ){
                    return this.errorAm = 'Please Enter the  AM Late Allowance Time default value in the formula.';//AM遅刻許容時間デフォルト値を数式で入力してください。
                }

                let that=this;
                this.axios({
                    url:process.env.MIX_APP_URL+"/setting/updateAm/"+this.setting.id,//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/setting/updateAm/"+this.setting.id,
                    method: 'post',
                    data: this.setting
                })
                .then(function (response) {
                    that.setting = response.data;
                    that.errorMoney = null;
                    that.errorPm = null;
                    
                    that.data_check_messg = true;
                    setTimeout(() => {
                        that.data_check_messg = false;
                    },2000)
                })
                .catch(function (error) {
                });
            },
            updatePm(){
                this.errorPm = null;
                if(this.setting.pm == 0){
                    return this.errorPm =  'Please Enter the  PM Late Allowance Time default value in the formula.'; //'PM遅刻許容時間デフォルト値を入力してください。';
                }else if(!this.validateNumber(this.setting.pm) ){
                    return this.errorPm =  'Please Enter the  PM Late Allowance Time default value in the formula.';//'PM遅刻許容時間デフォルト値を数式で入力してください。';
                }

                let that=this;
                this.axios({
                    url:process.env.MIX_APP_URL+"/setting/updatePm/"+this.setting.id,//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/setting/updatePm/"+this.setting.id,
                    method: 'post',
                    data: this.setting
                })
                .then(function (response) {
                    that.setting = response.data;
                    that.errorMoney = null;
                    that.errorAm = null;
                    
                    that.data_check_messg = true;
                    setTimeout(() => {
                        that.data_check_messg = false;
                    },2000)
                })
                .catch(function (error) {
                });
            },
            updateDelayAm(id , delayTime){
                if(delayTime.am == 0){
                    Vue.set(delayTime,"amDelayErrorMsg", "Please enter the AM late allowance time.");//AM遅刻許容時間を入力してください。
                    Vue.set(delayTime,"amDelayError",true);
                    return ;
                }else if(!this.validateNumber(delayTime.am) ){
                    Vue.set(delayTime,"amDelayErrorMsg", "Please enter the AM late allowance time in the formula.");//AM遅刻許容時間を数式で入力してください。
                    Vue.set(delayTime,"amDelayError",true);
                    return ;
                }

                let that=this;
                this.axios({
                    url:process.env.MIX_APP_URL+"/delayTime/updateAm/"+id,//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/delayTime/updateAm/"+id,
                    method: 'post',
                    data: delayTime
                })
                .then(function (response) {
                    let i = that.attendDelays.map(item => item.month).indexOf(delayTime.month); // find index of object
                    that.attendDelays[i] = response.data;

                    that.data_check_messg = true;
                    setTimeout(() => {
                        that.data_check_messg = false;
                    },2000)
                })
                .catch(function (error) {
                });
            },
            updateDelayPm(id , delayTime){
                 if(delayTime.pm == 0){
                    Vue.set(delayTime,"pmDelayErrorMsg", "Please enter the PM late allowance time.");//PM遅刻許容時間を入力してください。
                    Vue.set(delayTime,"pmDelayError",true); 
                    return ;
                }else if(!this.validateNumber(delayTime.pm) ){
                    Vue.set(delayTime,"pmDelayErrorMsg", "Please enter the PM late allowance time in the formula.");//PM遅刻許容時間を数式で入力してください。
                    Vue.set(delayTime,"pmDelayError",true);
                   return ;
                }

                let that=this;
                this.axios({
                    url:process.env.MIX_APP_URL+"/delayTime/updatePm/"+id,//(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/delayTime/updatePm/"+id,
                    method: 'post',
                    data: delayTime
                })
                .then(function (response) {
                    let i = that.attendDelays.map(item => item.month).indexOf(delayTime.month); // find index of object
                    that.attendDelays[i] = response.data;

                    that.data_check_messg = true;
                    setTimeout(() => {
                        that.data_check_messg = false;
                    },2000)
                })
                .catch(function (error) {
                });
            },
            validateNumber: function(number){
                var re = /^\d*$/;
                return re.test(number);
            },
            validateDecimal: function(number){
                var re = /^-?\d+(?:\.?\d+)?$/;
                return re.test(number);
            },

            delayDataCalculate(){
                if(this.delays.length == 0){
                    
                    this.dates.forEach(d => {
                        let tempSetting  = this.settings.filter(s => s.create_month < d.recordedDateTime);
                        tempSetting.sort(compareSetting);

                        if(tempSetting == 0){
                            let data1 ={};
                            data1.month = d.recordedDateTime;
                            data1.am = this.setting.am;
                            data1.pm = this.setting.pm;
                            data1.money = this.setting.money;
                            this.attendDelays.push(data1);
                        }else {
                           let data1 ={};
                            data1.month = d.recordedDateTime;
                            data1.am = tempSetting[tempSetting.length-1].am;
                            data1.pm = tempSetting[tempSetting.length-1].pm;
                            data1.money = tempSetting[tempSetting.length-1].money;
                            this.attendDelays.push(data1);
                        }
                    })
                     
                }else{
                    this.dates.forEach(d => {
                        let tempSetting  = this.settings.filter(s => s.create_month < d.recordedDateTime);
                        tempSetting.sort(compareSetting);

                        let sameMonth = this.delays.filter(a => a.month == d.recordedDateTime);
                        if(sameMonth.length == 0){
                            let data1 ={};
                            data1.month = d.recordedDateTime;
                            data1.am =  tempSetting[tempSetting.length-1].am;
                            data1.pm =  tempSetting[tempSetting.length-1].pm;
                            data1.money =  tempSetting[tempSetting.length-1].money;
                            this.temp.push(data1);
                        }
                    })
                    this.attendDelays = this.delays.concat(this.temp);
                }
                function compare(a, b) {
                    if (a.month < b.month)
                        return -1;
                    if (a.month > b.month)
                        return 1;
                    return 0;
                }

                function compareSetting(a, b){
                    if (a.create_month < b.create_month)
                        return -1;
                    if (a.create_month > b.create_month)
                        return 1;
                    return 0;
                }

                this.attendDelays.sort(compare);
            }


        }
    }
</script>