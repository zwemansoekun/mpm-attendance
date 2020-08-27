<template>
     <div class="col-md-10"> 
        <div class="container-fulid">
             
            <div class="row">
                <div class="col-md-4"> 
                    <select class="form-control" id="selectEmployee" name="employ_selected" required focus v-model="select_employee">
                        <option value="" disabled selected>Please select employee</option>        
                        <option v-bind:key="emp.id" v-for="emp in emps" >{{ emp.id }} {{emp.name }}</option>
                    </select>               
                </div>
            </div>  


            <div class="row mt-5"  v-if="form_summary_open">
                <div class="col-md-4">
                    <label>{{emp_no}}  {{emp_name}}</label>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-primary float-right" @click="showHistory">閲覧履歴</button>
                </div>
            </div>

            <div class="row mt-2"  v-if="form_summary_open">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <td>名前(フリガナ)</td>
                            <td style="width: 250px;"><span>{{empData.kana_name}}</span></td>
                        </tr>
                        <tr>
                            <td>入社日</td>
                            <td><span v-if="empData.entry_date">{{ customFormatter(empData.entry_date) }}</span></td>
                        </tr>
                        <tr>
                            <td>生年月日</td>
                            <td><span v-if="empData.dob">{{ customFormatter(empData.dob) }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>


            <div class="row mt-2" v-if="form_summary_open">
                <div class="col-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <td colspan="2">基本給</td>
                            <td style="width: 250px;">{{ numberFormatter(empDetail.salary_amount) }}</td>
                        </tr>
                        <tr >
                            <td rowspan="2"> 手当</td>
                            <td>通勤交通費(片道/回)</td>
                            <td>{{ numberFormatter(empDetail.trans_money) }}</td>
                        </tr>
                        <tr >
                            <td>JLPT</td>
                            <td>{{ numberFormatter(empDetail.jlpt) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">SSB負担割合（0又は2%)</td>
                            <td>{{ numberFormatter(empDetail.ssb) }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">ポジション</td>
                            <td>{{ empDetail.position }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">住所</td>
                            <td>{{ empDetail.address }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">電話番号</td>
                            <td>{{ empDetail.phone_no }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">身分証番号</td>
                            <td>{{ empDetail.nrc_no }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">給与振込先銀行口座</td>
                            <td>{{ empDetail.bank_account }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">家族構成</td>
                            <td>{{ empDetail.member }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">配偶者や子供の有無</td>
                            <td>{{ empDetail.child }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">緊急連絡先</td>
                            <td>{{ empDetail.emg_ph_no }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">通勤手段/時間（分）</td>
                            <td>{{ empDetail.waste_time }}</td>
                        </tr>
                    </table>
                </div>
            </div>


            <div v-if="form_detail_open" class="mt-5">
                <button type="button" class="btn btn-primary" @click="newHistoryCreate" v-if="!edit">編集</button>
                <button type="button" class="btn btn-primary" @click="updateEmpDetail" v-if="edit">編集終了</button>

                <div class="alert alert-danger mt-3" role="alert" v-if="duplicateErrors.length > 0">
                    <ul v-for="error in duplicateErrors" :key="error">
                        <li >{{ error }}</li>
                    </ul>
                </div>

                <div class="alert alert-success mt-3" role="alert" v-if="success_msg">
                    <strong >データは成功しました。</strong> 
                </div>
                    
                <table class="table table-sm table-bordered mt-2 mb-0" style="width:260px;">
                    <tr>
                        <td style="width:130px;">Name</td>
                        <td style="width:130px;">{{emp_name}}</td>
                    </tr>
                    <tr>
                        <td>Name(Katanaka)</td>
                        <td>{{empData.kana_name}}</td>
                    </tr>
                    <tr>
                        <td>入社日</td>
                        <td class="text-right"><span v-if="empData.entry_date">{{ customFormatter(empData.entry_date) }}</span></td>
                    </tr>
                    <tr>
                        <td>生年月日</td>
                        <td class="text-right"><span v-if="empData.dob">{{ customFormatter(empData.dob) }}</span></td>
                    </tr>
                </table>

<!-- text-nowrap -->
                <div class="table-responsive ">
                    <table class=" table-sm table-bordered">
                        <tr>
                            <th rowspan="2" style="width:130px;">給与年月</th>
                            <th rowspan="2" style="width:130px;">基本給</th>
                            <th colspan="2">手当</th>
                            <th rowspan="2">SSB負担割合(%)</th>
                            <th rowspan="2">ポジション</th>
                            <th rowspan="2">住所</th>
                            <th rowspan="2">電話番号</th>
                            <th rowspan="2">身分証番号</th>
                            <th rowspan="2">給与振込先銀行口座</th>
                            <th rowspan="2">家族構成</th>
                            <th rowspan="2">配偶者や子供の有無</th>
                            <th rowspan="2">緊急連絡先</th>
                            <th rowspan="2">通勤手段/時間（分）</th>
                        </tr>
                        <tr>
                            <th>通勤交通費(片道/回)</th>
                            <th>JLPT</th>
                        </tr>
                        <tr v-for="detail in empDetails" :key="detail.id">
                            <td class="align-bottom">
                                <label class="text-danger" v-if="detail.payMonthErr">{{ detail.payMonthErr }}</label>
                                <span v-if="!edit">{{ detail.pay_month }}</span>
                                <input type="text" class="form-control" v-model="detail.pay_month" v-if="edit">
                            </td>
                            <td class="text-right align-bottom" v-if="!edit && !detail.color">
                                <span v-if="!edit">{{ numberFormatter(detail.salary_amount) }}</span>
                            </td>
                            <td class="text-right bg-info align-bottom" v-if="!edit && detail.color">
                                <span v-if="!edit">{{ numberFormatter(detail.salary_amount) }}</span>
                            </td>
                            <td class="text-right align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.salaryAmountErr">{{ detail.salaryAmountErr }}</label>
                                <input type="text" class="form-control" v-model="detail.salary_amount" v-if="edit">
                            </td>
                            <td class="text-right align-bottom">
                                <label class="text-danger" v-if="detail.transMoneyErr">{{ detail.transMoneyErr }}</label>
                                <span v-if="!edit">{{numberFormatter(detail.trans_money) }}</span>
                                <input type="text" class="form-control" v-model="detail.trans_money" v-if="edit">
                            </td>
                            <td class="text-right align-bottom">
                                <label class="text-danger" v-if="detail.jlptErr">{{ detail.jlptErr }}</label>
                                <span v-if="!edit">{{ numberFormatter(detail.jlpt) }}</span>
                                <input type="text" class="form-control" v-model="detail.jlpt" v-if="edit">
                            </td>
                            <td class="text-right align-bottom">
                                <label class="text-danger" v-if="detail.ssbErr">{{ detail.ssbErr }}</label>
                                <span v-if="!edit">{{ numberFormatter(detail.ssb) }}</span>
                                <input type="text" class="form-control" v-model="detail.ssb" v-if="edit">
                            </td>
                            <td class="align-bottom">
                                <label class="text-danger" v-if="detail.positionErr">{{ detail.positionErr }}</label>
                                <span v-if="!edit">{{ detail.position }}</span>
                                <input type="text" class="form-control" v-model="detail.position" v-if="edit">
                            </td>
                            <td class="align-bottom">
                                <label class="text-danger" v-if="detail.addressErr">{{ detail.addressErr }}</label>
                                <span v-if="!edit">{{ detail.address }}</span>
                                <input type="text" class="form-control" v-model="detail.address" v-if="edit">
                            </td>
                            <td class="align-bottom">
                                <label class="text-danger" v-if="detail.phoneNoErr">{{ detail.phoneNoErr }}</label>
                                <span v-if="!edit">{{ detail.phone_no }}</span>
                                <input type="text" class="form-control" v-model="detail.phone_no" v-if="edit">

                            </td>
                            <td class="align-bottom">
                                <label class="text-danger" v-if="detail.nrcNoErr">{{ detail.nrcNoErr }}</label>
                                <span v-if="!edit">{{ detail.nrc_no }}</span>
                                <input type="text" class="form-control" v-model="detail.nrc_no" v-if="edit">

                            </td>
                            <td class="text-right align-bottom">
                                <label class="text-danger" v-if="detail.bankAccErr">{{ detail.bankAccErr }}</label>
                                <span v-if="!edit">{{ detail.bank_account }}</span>
                                <input type="text" class="form-control" v-model="detail.bank_account" v-if="edit">

                            </td>
                            <td class="align-bottom">
                                <label class="text-danger" v-if="detail.memberErr">{{ detail.memberErr }}</label>
                                <span v-if="!edit">{{ detail.member }}</span>
                                <input type="text" class="form-control" v-model="detail.member" v-if="edit">

                            </td>
                            <td class="align-bottom">
                                <label class="text-danger" v-if="detail.childErr">{{ detail.childErr }}</label>
                                <span v-if="!edit">{{ detail.child }}</span>
                                <input type="text" class="form-control" v-model="detail.child" v-if="edit">

                            </td>
                            <td class="align-bottom">
                                <label class="text-danger" v-if="detail.emgPhErr">{{ detail.emgPhErr }}</label>
                                <span v-if="!edit">{{ detail.emg_ph_no }}</span>
                                <input type="text" class="form-control" v-model="detail.emg_ph_no" v-if="edit">

                            </td>
                            <td class="align-bottom">
                                <label class="text-danger" v-if="detail.wasteTimeErr">{{ detail.wasteTimeErr }}</label>
                                <span v-if="!edit">{{ detail.waste_time }}</span>
                                <input type="text" class="form-control" v-model="detail.waste_time" v-if="edit">
                            </td>
                        </tr>
                    
                    </table>
                </div>

            </div>

        </div>
    </div>
</template>

<script>

var moment = require('moment');
var numeral = require("numeral");

   

    export default {
        data() {
            return {
                select_employee:'',
                emps:[],
                emp_no:'',
                emp_name:'',
                empData: {},
                empDetail: {},
                empDetails: [],
                form_summary_open:false,
                form_detail_open:false,
                edit:false,
                errorFlg: false,
                success_msg: false,
                duplicateErrors: [],

            }
        },
        created() {
            this.axios
                .get('http://localhost:5000/employees')
                .then(response => {
                    this.emps=response.data;
                });
        },
        watch: {
            select_employee:function (val) {

                if(val!=''){

                    let split_name=val.split(" ");
                    this.emp_no=split_name[0];

                    val=val.replace(this.emp_no,'');
                    this.emp_name=val;

                }  

                if(val!=''){                            
                    this.form_summary_open = true;
                    this.form_detail_open = false;
                     this.edit = false;
                    this.errorFlg =false;
                    this.duplicateErrors = [];
                    this.findEmployeeData();
                    this.findLastHistory();
                    
                }
              
            }
        },
        methods: {
            customFormatter(date) {
                return moment(date).format('MM/DD/YYYY');
            },
            numberFormatter(data){
                return numeral(data).format('0,0');
            },
            findEmployeeData(){
                this.axios
                .get('http://127.0.0.1:8000/api/employee/'+this.emp_no)
                .then((response) => {
                    this.empData = response.data;
                })
            },
            findLastHistory(){
                this.axios
                .get('http://127.0.0.1:8000/api/employeeDetail/lastData/'+this.emp_no)
                .then((response) => {
                    this.empDetail = response.data;
                })
            },
            showHistory(){
                this.form_detail_open = true;
                this.form_summary_open = false;
                this.findEmployeeHistory();
            },
            findEmployeeHistory(){
                this.axios
                .get('http://127.0.0.1:8000/api/employeeDetail/'+this.empData.id)
                .then((response) => {
                    this.empDetails = response.data;
                    for(let i=0; i < this.empDetails.length; i++)
                    {
                        for(let j= i+1; j < this.empDetails.length; j++)
                        {
                            if(this.empDetails[i]['salary_amount'] != this.empDetails[j]['salary_amount'])
                            {
                                Vue.set(this.empDetails[j], 'color', true);
                            }
                        }
                    }
                    
                })
            },
            newHistoryCreate(){
                this.edit = true;
                console.log("newObject");
                let newObj = {};
                console.log(this.empDetails.length);
                if(this.empDetails.length > 0){
                    newObj.pay_month = "";
                    newObj.salary_amount = this.empDetails[this.empDetails.length-1].salary_amount;
                    newObj.trans_money = this.empDetails[this.empDetails.length-1].trans_money;
                    newObj.jlpt = this.empDetails[this.empDetails.length-1].jlpt;
                    newObj.ssb = this.empDetails[this.empDetails.length-1].ssb;
                    newObj.position = this.empDetails[this.empDetails.length-1].position;
                    newObj.address = this.empDetails[this.empDetails.length-1].address;
                    newObj.phone_no = this.empDetails[this.empDetails.length-1].phone_no;
                    newObj.nrc_no = this.empDetails[this.empDetails.length-1].nrc_no;
                    newObj.bank_account = this.empDetails[this.empDetails.length-1].bank_account;
                    newObj.member = this.empDetails[this.empDetails.length-1].member;
                    newObj.child = this.empDetails[this.empDetails.length-1].child;
                    newObj.emg_ph_no = this.empDetails[this.empDetails.length-1].emg_ph_no;
                    newObj.waste_time = this.empDetails[this.empDetails.length-1].waste_time;
                    newObj.employee_id = this.empData.id;
                }else{
                    newObj.pay_month = '';
                    newObj.salary_amount = 0;
                    newObj.trans_money = 0;
                    newObj.jlpt = 0;
                    newObj.ssb = 0;
                    newObj.position ='';
                    newObj.address =''
                    newObj.phone_no ='';
                    newObj.nrc_no ='';
                    newObj.bank_account ='';
                    newObj.member ='';
                    newObj.child ='';
                    newObj.emg_ph_no ='';
                    newObj.waste_time ='';
                    newObj.employee_id = this.empData.id;
                }

                this.empDetails.push(newObj);
            },
            updateEmpDetail(){
                console.log("update");
                this.duplicateErrors = [];
                this.errorFlg = false;
                this.success_msg = false;
                for(let i=0; i < this.empDetails.length; i++)
                {
                    Vue.set(this.empDetails[i] , 'payMonthErr','');
                    Vue.set(this.empDetails[i] , 'salaryAmountErr','');
                    Vue.set(this.empDetails[i] , 'transMoneyErr','');
                    Vue.set(this.empDetails[i] , 'jlptErr','');
                    Vue.set(this.empDetails[i] , 'ssbErr','');
                    Vue.set(this.empDetails[i] , 'positionErr','');
                    Vue.set(this.empDetails[i] , 'addressErr','');
                    Vue.set(this.empDetails[i] , 'phoneNoErr','');
                    Vue.set(this.empDetails[i] , 'nrcNoErr','');
                    Vue.set(this.empDetails[i] , 'BankAccErr','');
                    Vue.set(this.empDetails[i] , 'memberErr','');
                    Vue.set(this.empDetails[i] , 'childErr','');
                    Vue.set(this.empDetails[i] , 'emgPhErr','');
                    Vue.set(this.empDetails[i] , 'wasteTimeErr','');
                    
                    if(this.empDetails[i].pay_month == '')
                    {
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i] , 'payMonthErr','Pay Month data is required.');
                    }else if(!this.validatePayMonthFormat(this.empDetails[i].pay_month)){
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i] , 'payMonthErr','Pay Month format is YYYY/MM.');
                    }

                    if(this.empDetails[i].salary_amount == 0)
                    {
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'salaryAmountErr', 'Salary Amount is required.' )
                    }else if(!this.validateDecimal(this.empDetails[i].salary_amount))
                    {
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'salaryAmountErr', 'Salary Amount format error.' )
                    }

                    // if(this.empDetails[i].trans_money == 0){
                    //     this.errorFlg = true;
                    //     Vue.set(this.empDetails[i], 'transMoneyErr', 'Transportaion is required.')
                    // }

                    // if(this.empDetails[i].jlpt == 0){
                    //     this.errorFlg = true;
                    //     Vue.set(this.empDetails[i], 'jlptErr', 'JLPT is required.')
                    // }

                    // if(this.empDetails[i].ssb == 0){
                    //     this.errorFlg = true;
                    //     Vue.set(this.empDetails[i], 'ssbErr', 'SSB is required.')
                    // }

                    if(this.empDetails[i].position == ''){
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'positionErr', 'Position is required.')
                    }

                    if(this.empDetails[i].address == ''){
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'addressErr', 'Address is required.')
                    }

                    if(this.empDetails[i].phone_no == ''){
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'phoneNoErr', 'Phone No is required.')
                    }

                    if(this.empDetails[i].nrc_no == ''){
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'nrcNoErr', 'NRC No is required.')
                    }

                    if(this.empDetails[i].bank_account == ''){
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'bankAccErr', 'bankAcc is required.')
                    }

                    // if(this.empDetails[i].member == ''){
                    //     this.errorFlg = true;
                    //     Vue.set(this.empDetails[i], 'memberErr', 'member is required.')
                    // }

                    // if(this.empDetails[i].child == ''){
                    //     this.errorFlg = true;
                    //     Vue.set(this.empDetails[i], 'childErr', 'child is required.')
                    // }

                    // if(this.empDetails[i].emg_ph_no == ''){
                    //     this.errorFlg = true;
                    //     Vue.set(this.empDetails[i], 'emgPhErr', 'Emg phone is required.')
                    // }

                    // if(this.empDetails[i].waste_time == ''){
                    //     this.errorFlg = true;
                    //     Vue.set(this.empDetails[i], 'wasteTimeErr', 'waste_time is required.')
                    // }
    
                }

                console.log(this.errorFlg);
                if(this.errorFlg){
                    return this.empDetails;
                }

                for(let i=0; i < this.empDetails.length; i++)
                {
                    for(let j= i+1; j < this.empDetails.length; j++)
                    {
                        if(this.empDetails[i]['pay_month'] == this.empDetails[j]['pay_month'])
                        {
                            this.duplicateErrors.push(this.empDetails[i]['pay_month']+' is duplicate.');
                        }
                    }
                }

                if(this.duplicateErrors.length > 0){
                    return this.duplicateErrors;
                }


                this.axios
                .post('http://127.0.0.1:8000/api/employeeDetail/updateAll',this.empDetails)
                .then((response) => {
                    console.log(response.data);
                    this.findEmployeeHistory();
                    this.edit = false;
                    this.success_msg = true;
                    setTimeout(() => {
                        this.success_msg = false;
                    },2000)
                })

                
            },
            validateDecimal: function(number){
                var re = /^-?\d+(?:\.?\d+)?$/;
                return re.test(number);
            },
            validatePayMonthFormat: function(value){

                console.log(value);
                //var re = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/ ;
                var re = /^\d{4}\/(0[1-9]|1\d|2\d|3[01])$/ ;
                return re.test(value);
            }

        }
    }
</script>