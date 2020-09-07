<template>
    <div class="col-md-10">
            
        <div class="container-fluid">
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

            <table class="table table-bordered mt-5" v-if="formChange">
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

        
        <div class="container-fluid" v-if="!formChange">
                <div class="row" >
                    <div class="col-md-4 mt-4"> 
                        <h4><strong>給与手当一覧　{{this.select_date}}分</strong></h4>      
                    </div>
                </div>
            
                <div class="row justify-content-md-center mt-4"> 
                      <button type="button" class="btn btn-secondary mr-3" onclick="this.blur();">エンジニアコスト一覧表</button>
                      <button type="button" class="btn btn-secondary mr-3" onclick="this.blur();">給与明細作成</button>
                      <button type="button" class="btn btn-secondary mr-3" onclick="this.blur();">編集</button>
                </div>
                 <div class="row" >
                    <div class="col-md-2 mt-4"> 
                        {{this.paymentDate(this.select_date,1)}} 
                    </div>
                    <div class="col-md-3 mt-4"> 
                        支給
                    </div>    
                </div>
                <div class="row" >
                    <div class="col-md-9">
                             <table id="" class="table table-sm table-bordered">
                            <tr>
                                <th rowspan="2"  class="align-middle text-center">
                                    従業員番号
                                </th>
                                <th rowspan="2"  class="align-middle text-center">
                                    名前
                                </th>
                                <th rowspan="2" class="align-middle text-center">

                                </th>
                                <th colspan="5"  class="align-middle text-center">
                                    控除前
                                </th>
                                <th colspan="3"  class="align-middle text-center">
                                    控除額
                                </th>
                                <th colspan="3"  class="align-middle text-center">
                                    その他調整(控除の場合-)
                                </th>
                                <th rowspan="2" style="text-align: center;"  class="align-middle text-center">
                                    支給額
                                </th>
                            </tr>
                            <tr>  
                                <th  class="align-middle text-center">
                                    基本給
                                </th>
                                <th  class="align-middle text-center">
                                    通勤交通費
                                </th>
                                <th  class="align-middle text-center">
                                    JLPT
                                </th>
                                <th  class="align-middle text-center">
                                    ボーナス
                                </th>
                                <th  class="align-middle text-center">
                                    合計
                                </th>
                                <th  class="align-middle text-center">
                                    所得税
                                </th>
                                <th  class="align-middle text-center">
                                    SSB
                                </th>
                                <th  class="align-middle text-center">
                                    遅刻欠勤早退
                                </th> 
                                <th  class="align-middle text-center">

                                </th>
                                <th  class="align-middle text-center">
                                    
                                </th>
                                <th  class="align-middle text-center">
                                    
                                </th>
                                
                            </tr>
                        </table>    
                    </div>
                    <div class="col-md-3">
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th  colspan="2" class="align-middle text-center">
                                        SSB
                                </th>
                                 <th  rowspan="3" class="align-middle text-center">
                                        備考
                                </th>
                            </tr>
                            <tr>
                                <th rowspan="2" class="align-middle text-center">
                                        総額
                                </th>
                                 <th   class="align-middle text-center">
                                        会社負担分
                                </th>
                            </tr>
                            <tr>
                                   <th   class="align-middle text-center">
                                      300000
                                </th>   
                            </tr>
                        </table>
                    </div> 
                </div>
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
                data_check_messg:false,
                formChange:true,
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
            paymentDate(month,date_flash=''){
                var futureMonth = new Date(month);
                futureMonth.setMonth(futureMonth.getMonth() + 1);
                futureMonth.setDate(futureMonth.getDate()+ 9);
                if(date_flash!=''){
                   return moment(futureMonth).format('YYYY/MM/DD');     
                }else
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
            },
            dateChange:function($event){
                let that=this;
                that.formChange=false;


            }

        }
    }
</script>