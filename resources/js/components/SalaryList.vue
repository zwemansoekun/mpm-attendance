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
                                <div class="col"><button type="button" class="btn btn-primary" @click="updateDelayMoney(d.id ,d)" onclick="this.blur();">編集</button></div>
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
                      <button type="button" style="background-color:#E7E6E6" class="btn  mr-3" onclick="this.blur();">エンジニアコスト一覧表</button>
                      <a type="button"  href="http://127.0.0.1:8000/export/" >
                            <button @click="excelExport()" class="btn mr-3" style="background-color:#E7E6E6" onclick="this.blur();">
                            給与明細作成
                            </button>
                        </a>
                      <button type="button" style="background-color:#E7E6E6" class="btn  mr-3" onclick="this.blur();">編集</button>
                </div>
             
                <div class="row" >
                    <div class="col-md-9">
                      
                            <span class="col-md-2 mt-4" style="background-color:#DEEBF7"> 
                                    {{this.paymentDate(this.select_date,1)}}     
                            </span>
                            <span class="col-md-2 mt-4"> 
                                    支給
                            </span>  
                            <!-- {{emps}} -->
                            <!-- {{salaries}} -->
                            <table id="salaryTable" class="table table-sm table-bordered">
                            <tr>
                                <th  class="border-bottom-0">
                                   
                                </th>
                                <th rowspan="3"  class="align-middle text-center">
                                    名前
                                </th>
                                <th rowspan="3" class="align-middle text-center">

                                </th>
                                <th colspan="5"  style="background-color:#FBE5D6" class="align-middle text-center">
                                    控除前
                                </th>
                                <th colspan="3"   style="background-color:#D9D9D9" class="align-middle text-center">
                                    控除額
                                </th>
                                <th colspan="3"  style="background-color:#D9D9D9" class="align-middle text-center">
                                    その他調整(控除の場合-)
                                </th>
                                <th rowspan="2" style="text-align: center;background-color:#DAE3F3"  class="align-middle text-center border-bottom-0">
                                    支給額
                                </th>
                            </tr>
                            <tr>  
                                <th   class="align-middle text-center border-bottom-0 border-top-0">
                                    従業員番号
                                </th>
                                <th  rowspan="2"  class="text-center align-middle"  style="background-color:#FBE5D6;width: 80px">
                                    基本給
                                </th>
                                <th   rowspan="2" class="text-center align-middle"  style="background-color:#FBE5D6;width: 80px">
                                    通勤交通費
                                </th>
                                <th   rowspan="2" class="text-center align-middle"  style="background-color:#FBE5D6;width: 80px">
                                    JLPT
                                </th>
                                <th  rowspan="2" class="text-center align-middle"  style="background-color:#FBE5D6;width: 80px">
                                    ボーナス
                                </th>
                                <th   rowspan="2" class="text-center align-middle"  style="background-color:#FBE5D6;width: 80px">
                                    合計
                                </th>
                                <th   rowspan="2" class="text-center align-middle" style="background-color:#D9D9D9;width: 80px">
                                    所得税
                                </th>
                                <th   rowspan="2" class="text-center align-middle" style="background-color:#D9D9D9;width: 80px">
                                    SSB
                                </th>
                                <th   rowspan="2" class="text-center align-middle" style="background-color:#D9D9D9;width: 80px">
                                    遅刻欠勤早退
                                </th> 
                                <th  rowspan="2" class="text-center" style="background-color:#D9D9D9;width: 80px">

                                </th>
                                <th  rowspan="2" class="text-center" style="background-color:#D9D9D9;width: 80px">
                                    
                                </th>
                                <th rowspan="2" class="text-center" style="background-color:#D9D9D9;width: 80px">
                                    
                                </th>
                                
                            </tr>
                            <tr>
                                <th  class="border-top-0">
                                </th>
                                <th class="align-middle text-right border-top-0" style="background-color:#DAE3F3">
                                     <!-- {{totalSalaryAmount}} -->
                                </th>  
                            </tr>

                            <tbody>
                                <!-- {{salaries}} -->
                                <!-- <tr> -->
                                    <!-- v-bind:key="key" v-for="(salary,key) in salaries" -->
                                    <!-- <table  class="table table-sm table-bordered"  style="position: absolute;"> -->
                                            <template  v-for="(salary,key) in salaries">
                                            
                                                    <!-- {{key}} -->
                                                    <tr v-bind:key="key">
                                                        <td rowspan="2"  class="text-center align-middle">{{emp_arr[salaries[key].emp_id]}}</td>
                                                        <td rowspan="2" class="text-center align-middle">{{name_arr[salaries[key].emp_id]}}<div>({{salaries[key].kana_name}})</div></td>
                                                        <td style="background-color:#D9D9D9" class="text-center align-middle">計算値</td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle">{{Math.trunc(salaries[key].salary_amount).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")}}</td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle">{{salaries[key].trans_money}}</td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle">{{salaries[key].jlpt}}</td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle">{{(parseInt(salaries[key].salary_amount)+parseInt(salaries[key].trans_money)).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")}}</td>

                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle">12458</td>

                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                    </tr>
                                                    <tr v-bind:key="'A'+key">
                                                        <td class="text-center align-middle">実際</td>
                                                        <td class="text-right align-middle" style="padding: 0px;">
                                                            <input name="pay_month[]" @change="updateInput" class="pay_month" style="text-align:right;" type="number" :value="``">
                                                        </td>
                                                        <td class="text-right align-middle" style="padding: 0px;">
                                                            <input name="trans_money[]" @change="updateInput" class="trans_money" style="text-align:right;" type="number" :value="``">
                                                        </td>
                                                        <td class="text-right align-middle" style="padding: 0px;">
                                                            <input name="jlpt[]"  @change="updateInput" class="jlpt" style="text-align:right;" type="number" :value="``">
                                                        </td>
                                                        <td class="text-right align-middle" style="padding: 0px;">
                                                            <input name="bonus[]" @change="updateInput" class="bonus" style="text-align:right;" type="number" :value="``">
                                                        </td>
                                                        <td class="text-right align-middle" style="padding: 0px;">

                                                            <input name="total_salary[]" @change="updateInput" class="total_salary" style="text-align:right;" type="number" :value="``">
                                                        </td>

                                                        <!-- 控除額 -->
                                                        <td class="text-right align-middle" style="padding: 0px;">
                                                            <input name="income_tax[]" @change="updateInput" class="inc_tax" style="text-align:right;" type="number" :value="``">
                                                        </td>
                                                        <td class="text-right align-middle" style="padding: 0px;">
                                                            <input name="ssb[]" @change="updateInput" class="ssb" style="text-align:right;" type="number" :value="``">
                                                        </td>
                                                        <td class="text-right align-middle" style="padding: 0px;">
                                                            <input name="leave_late[]" @change="updateInput" class="leave_late" style="text-align:right;" type="number" :value="``">
                                                        </td>

                                                        <td class="text-right align-middle" style="padding: 0px;">
                                                            <!-- <input name="leave_lateh" class="leave_lateh" style="text-align:right;" type="number" :value="``"> -->
                                                        </td>
                                                        <td class="text-right align-middle" style="padding: 0px;">
                                                            <!-- <input name="pay_month" class="pay_month" style="text-align:right;" type="number" :value="``"> -->
                                                        </td>
                                                        <td class="text-right align-middle" style="padding: 0px;">
                                                            <!-- <input name="pay_month" class="pay_month" style="text-align:right;" type="number" :value="``"> -->
                                                        </td>
                                                        <td class="text-right align-middle" style="padding: 0px;">
                                                            <!-- :value="``" -->
                                                            <input name="salary_amount[]" class="salary_amount" style="text-align:right;" type="number" >
                                                        </td>
                                                    </tr>
                                            
                                            </template>
                            </tbody>

                        </table>    
                    </div>



                    <div class="col-md-3">
                        <span  class="col-md-2 mt-4"></span>
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
                                 <th   class="align-middle text-center border-bottom-0" style="background-color:yellow">
                                        会社負担分
                                </th>
                            </tr>
                            <tr>
                                   <th   class="align-middle text-right border-top-0" style="background-color:yellow">
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
                year:'',
                month:'',
                salaries:[],
                emps:[],
                emp_arr:[],
                name_arr:[],
                salary_amount:[],
            }
        },
        computed: {
            // totalSalaryAmount:function(){
            //     let that=this;
            //     console.log('c',that.salary_amount);
            //     return true;
            // }                  
        },        
        created() {
            let that=this;
            this.axios({
                url:(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/settings",
                method: 'get'
            })
            .then(function (response) {
                that.setting=response.data;
            })
            .catch(function (error) {
            });

            this.axios({
                url:(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/setting/all",
                method: 'get'
            })
            .then(function (response) {
                that.settings=response.data;
            })
            .catch(function (error) {
            });

            this.axios({
                url:(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/delayTimes",
                method: 'get'
            })
            .then(function (response) {
                that.delays=response.data;
                that.delayDataCalculate();
            })
            .catch(function (error) {
            });

            this.axios
                .get('http://localhost:5000/attendances/all/date')
                .then(response => {
                    this.dates=response.data;
            }); 
            this.axios
                .get('http://localhost:5000/employees')
                .then(response => {
                
                    that.emps=response.data;
                   if(that.emps){
                  
                       for(let v in that.emps){
                            console.log('dddd',that.emps[v].id);
                           that.emp_arr[that.emps[v].id]=that.emps[v].employeeId;
                           that.name_arr[that.emps[v].id]=that.emps[v].name;
                       }
                     
                   }
                  
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
                    this.delays = response.data;
                    this.delayDataCalculate();

                    this.data_check_messg = true;
                    setTimeout(() => {
                        this.data_check_messg = false;
                    },2000)
                })
            },
            delayDataCalculate(){
                this.attendDelays = [];
                this.temp = [];
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
                let that=this;let val='';
             
                
                if(event.target.value!=''){
                    that.formChange=false;
                    val=event.target.value;

                    let split_date=val.split("/");                  
                    this.year=split_date[0];
                  
                    val=val.replace(this.year+"/",'');
                    this.month=val;

                }else{
                    return false;
                }

                // if(event.target.value!=''){

                    this.axios
                    .get((window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/salaryList/"+this.year+"-"+this.month)                 
                    .then(response => {
                        console.log('salary',response.data);
                        that.salaries=response.data;
                    })
                    .catch(function (error) {
                        console.log(error.response)
                    });

                // }
            },
            excelExport(){
                axios.get('http://127.0.0.1:8000/export')
                .then(()=>{
                    
                })
                .catch(()=> {
                    
                })
            },
            updateInput:function(event){ 
                    let that=this;
                    let b_salary=0,t_m=0,jlpt=0,bnu=0,total=0;  
                    let inc_tax=0,ssb=0,leave_late=0,salary_amount=0;
                     
                    b_salary=$(event.target).parent().parent().find('.pay_month').val()!=''?$(event.target).parent().parent().find('.pay_month').val():0;
                    t_m=$(event.target).parent().parent().find('.trans_money').val()!=''?$(event.target).parent().parent().find('.trans_money').val():0;
                    jlpt=$(event.target).parent().parent().find('.jlpt').val()!=''?$(event.target).parent().parent().find('.jlpt').val():0;
                    bnu=$(event.target).parent().parent().find('.bonus').val()!=''?$(event.target).parent().parent().find('.bonus').val():0;

                    inc_tax=$(event.target).parent().parent().find('.inc_tax').val()!=''?$(event.target).parent().parent().find('.inc_tax').val():0;
                    ssb=$(event.target).parent().parent().find('.ssb').val()!=''?$(event.target).parent().parent().find('.ssb').val():0;
                    leave_late=$(event.target).parent().parent().find('.leave_late').val()!=''?$(event.target).parent().parent().find('.leave_late').val():0;
                    
                    total=parseInt(b_salary)+parseInt(t_m)+parseInt(jlpt)+parseInt(bnu);
                    if(total<0){
                        total=0;
                    }
                    salary_amount=total - (parseInt(inc_tax)+parseInt(ssb)+parseInt(leave_late));
                    if(salary_amount<0){
                        salary_amount=0;
                    }
                    $(event.target).parent().parent().find('.total_salary').val(total.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                    $(event.target).parent().parent().find('.salary_amount').val(salary_amount.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                    that.salary_amount=salary_amount;


            }, 
        }
    }
</script>