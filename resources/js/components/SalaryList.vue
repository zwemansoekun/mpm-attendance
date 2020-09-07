<template>
    <div class="col-md-10">
            
        <div class="container">
            <div class="row">
                <div class="col-md-4"> 
                    <select class="form-control" id="selectDate"  @change="dateChange($event)" name="date_selected" required focus v-model="select_date">
                        <option value="" disabled selected>Please select Year/Month</option>        
                        <option v-bind:key="date.id" v-for="date in dates">{{ date.recordedDateTime }}</option>   
                    </select>              
                </div>
            </div>

            <div class="alert alert-success mt-5" role="alert" v-if="data_check_messg">
                 <strong >データは成功しました。</strong> 
            </div>

            <table class="table table-bordered mt-5">
                <thead>
                    <tr>
                        <th scope="col">年月</th>
                        <th scope="col">支給日</th>
                        <th scope="col">JPN/MMK</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="d in attendDelays" :key="d.id">
                        <td>
                            {{ d.month}}
                        </td>
                        <td>
                            {{ paymentDate(d.month)}}
                        </td>
                        <td>
                            <div class="row" v-if="d.moneyDelayError">
                                <div class="col text-danger">{{d.moneyDelayErrorMsg}}</div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <input type="text" class="form-control" v-model="d.money">
                                </div>
                                <div class="col"><button class="btn btn-primary" @click="updateDelayMoney(d.id ,d)" onclick="this.blur();">編集</button></div>
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
                select_date:'',
                dates:[],
                setting:{},
                settings: [],
                delays:[],
                attendDelays:[],
                temp: [],
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
                .get('http://127.0.0.1:8000/api/setting/all')
                .then(response => {
                    this.settings=response.data;
                });
            this.axios
            .get('http://127.0.0.1:8000/api/delayTimes')
            .then(response => {
                this.delays=response.data;
                this.delayDataCalculate();
                
            });
            this.axios
                .get('http://localhost:5000/attendances/all/date')
                .then(response => {
                    this.dates=response.data;
            }); 
           
        },
        methods: {
            paymentDate(month){
                var futureMonth = new Date(month);
                futureMonth.setMonth(futureMonth.getMonth() + 1);
                futureMonth.setDate(futureMonth.getDate()+ 9);
                return moment(futureMonth).format('MM/DD/YYYY');
            },
            updateDelayMoney(id , delayTime){
                if(delayTime.money == 0){
                    Vue.set(delayTime,"moneyDelayErrorMsg", "JPN/MMKを入力してください。");
                    Vue.set(delayTime,"moneyDelayError",true);
                    return ;
                }else if(!this.validateDecimal(delayTime.money) ){
                    Vue.set(delayTime,"moneyDelayErrorMsg", "JPN/MMKを数式で入力してください。");
                    Vue.set(delayTime,"moneyDelayError",true);
                    return ;
                }

                this.axios
                .post('http://127.0.0.1:8000/api/delayTime/updateMoney/'+id, delayTime)
                .then((response) => {
                    let i = this.attendDelays.map(item => item.month).indexOf(delayTime.month); // find index of your object
                    this.attendDelays[i] = response.data;

                    this.data_check_messg = true;
                    setTimeout(() => {
                        this.data_check_messg = false;
                    },2000)
                })
            },
            delayDataCalculate(){
                let results = [];
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
            },
            validateDecimal: function(number){
                var re = /^-?\d+(?:\.?\d+)?$/;
                return re.test(number);
            }

        }
    }
</script>