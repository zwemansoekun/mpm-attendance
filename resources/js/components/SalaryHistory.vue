<template>
     <div class="col-md-10"> 
        <div class="container-fulid">
             
            <div class="row">
                <div class="col-md-3"> 
                    <select class="form-control" id="selectEmployee" name="employ_selected" required focus v-model="select_employee">
                        <option value="" disabled selected>Please select employee</option>        
                        <option v-bind:key="emp.id" v-for="emp in emps" v-bind:label="employeeCodeAndName(emp)"> {{ emp.id }} {{ emp.employeeId }} {{emp.name }}</option>
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
                        <td style="width:130px;">名前</td>
                        <td style="width:130px;">{{emp_name}}</td>
                    </tr>
                    <tr>
                        <td>名前(フリガナ)</td>
                        <td v-if="!edit">{{empData.kana_name}}</td>
                        <td v-if="edit">
                            <label class="text-danger" v-if="empData.kanaErr">{{ empData.kanaErr }}</label>
                            <input type="text" class="form-control form-control-sm" v-model="empData.kana_name">
                        </td>
                    </tr>
                    <tr>
                        <td>入社日</td>
                        <td v-if="!edit" class="text-right"><span v-if="empData.entry_date">{{ customFormatter(empData.entry_date) }}</span></td>
                        <td v-if="edit">
                            <label class="text-danger" v-if="empData.entryErr">{{ empData.entryErr }}</label>
                            <input type="date" class="form-control form-control-sm" v-model="empData.entry_date">
                        </td>
                    </tr>
                    <tr>
                        <td>生年月日</td>
                        <td v-if="!edit" class="text-right"><span v-if="empData.dob">{{ customFormatter(empData.dob) }}</span></td>
                        <td v-if="edit">
                            <label class="text-danger" v-if="empData.dobErr">{{ empData.dobErr }}</label>
                            <input type="date" class="form-control form-control-sm" v-model="empData.dob">
                        </td>
                    </tr>
                </table>

<!--table-responsive text-nowrap -->
                <div>
                    <table class="table table-sm table-bordered">
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
                            <td v-if="!edit">
                                <span v-if="!edit">{{ yearMonthFormatter(detail.pay_month) }}</span>
                            </td>
                            <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.payMonthErr">{{ detail.payMonthErr }}</label>
                                <datepicker class="datepicker" :minimumView="'month'" :maximumView="'month'" v-model="detail.pay_month" :format="yearMonthFormatter" :typeable=true></datepicker>
                            </td>  

                            <td class="text-right" v-if="!edit && !detail.color">
                                <span v-if="!edit">{{ numberFormatter(detail.salary_amount) }}</span>
                            </td>
                            <td class="text-right bg-info" v-if="!edit && detail.color">
                                <span v-if="!edit">{{ numberFormatter(detail.salary_amount) }}</span>
                            </td>
                            <td class="text-right align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.salaryAmountErr">{{ detail.salaryAmountErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.salary_amount" v-if="edit">
                            </td>

                            <td class="text-right" v-if="!edit">
                                <span v-if="!edit">{{numberFormatter(detail.trans_money) }}</span>
                            </td>
                            <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.transMoneyErr">{{ detail.transMoneyErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.trans_money" v-if="edit">
                            </td>

                            <td class="text-right" v-if="!edit">
                                <span v-if="!edit">{{ numberFormatter(detail.jlpt) }}</span>
                            </td>
                            <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.jlptErr">{{ detail.jlptErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.jlpt" v-if="edit">
                            </td>

                            <td class="text-right" v-if="!edit">
                                <span v-if="!edit">{{ numberFormatter(detail.ssb) }}</span>
                            </td>
                            <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.ssbErr">{{ detail.ssbErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.ssb" v-if="edit">
                            </td>

                            <td v-if="!edit">
                                <span v-if="!edit">{{ detail.position }}</span>
                            </td>
                            <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.positionErr">{{ detail.positionErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.position" v-if="edit">
                            </td>

                            <td v-if="!edit">
                                <span v-if="!edit">{{ detail.address }}</span>
                            </td>
                            <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.addressErr">{{ detail.addressErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.address" v-if="edit">
                            </td>

                            <td v-if="!edit">
                                <span v-if="!edit">{{ detail.phone_no }}</span>
                            </td>
                            <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.phoneNoErr">{{ detail.phoneNoErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.phone_no" v-if="edit">
                            </td>

                            <td v-if="!edit">
                                <span v-if="!edit">{{ detail.nrc_no }}</span>
                            </td>
                             <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.nrcNoErr">{{ detail.nrcNoErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.nrc_no" v-if="edit">
                            </td>

                            <td class="text-right" v-if="!edit">
                                <span v-if="!edit">{{ detail.bank_account }}</span>
                            </td>
                            <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.bankAccErr">{{ detail.bankAccErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.bank_account" v-if="edit">
                            </td>

                            <td v-if="!edit">
                                <span v-if="!edit">{{ detail.member }}</span>
                            </td>
                            <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.memberErr">{{ detail.memberErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.member" v-if="edit">
                            </td>

                            <td v-if="!edit">
                                <span v-if="!edit">{{ detail.child }}</span>
                            </td>
                            <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.childErr">{{ detail.childErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.child" v-if="edit">
                            </td>

                            <td v-if="!edit">
                                <span v-if="!edit">{{ detail.emg_ph_no }}</span>
                            </td>
                            <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.emgPhErr">{{ detail.emgPhErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.emg_ph_no" v-if="edit">
                            </td>

                            <td v-if="!edit">
                                <span v-if="!edit">{{ detail.waste_time }}</span>
                            </td>
                            <td class="align-bottom" v-if="edit">
                                <label class="text-danger" v-if="detail.wasteTimeErr">{{ detail.wasteTimeErr }}</label>
                                <input type="text" class="form-control form-control-sm" v-model="detail.waste_time" v-if="edit">
                            </td>
                        </tr>
                    
                    </table>
                </div>

            </div>

        </div>
    </div>
</template>
<style >
.datepicker input{
    height: calc(1.5em + 0.5rem + 2px);
    padding: 0.25rem 0.5rem;
    font-size: 0.7875rem;
    line-height: 1.5;
    border-radius: 0.2rem;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    font-weight: 400;
}
</style>

<script>
var numeral = require("numeral");
import Datepicker from 'vuejs-datepicker';

    export default {
        components: {
            Datepicker
        },
        data() {
            return {
                select_employee:'',
                emps:[],
                emp_id:'',
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

                this.empDetails = [];

                if(val!=''){
                    let split_name=val.split(" ");
                    this.emp_id=split_name[0];
                    this.emp_no=split_name[1];

                    val=val.replace(this.emp_id,'');
                    val=val.replace(this.emp_no,'');
                    this.emp_name=val;

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
            yearMonthFormatter(value){
                if(value != ''){
                    var dtObj = new Date(value); 
                    return moment(dtObj).format('YYYY/MM');
                }
            },
            numberFormatter(data){
                return numeral(data).format('0,0');   
            },
            findEmployeeData(){
                this.axios
                .get('http://127.0.0.1:8000/api/employee/'+this.emp_id)
                .then((response) => {
                    this.empData = response.data;
                })
            },
            findLastHistory(){
                this.axios
                .get('http://127.0.0.1:8000/api/employeeDetail/lastData/'+this.emp_id)
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
                .get('http://127.0.0.1:8000/api/employeeDetail/'+this.emp_id)
                .then((response) => {
                    this.empDetails = response.data;
                    for(let i=0; i < this.empDetails.length; i++)
                    {  
                        var dtObj = new Date(this.empDetails[i]['pay_month']); 

                        this.empDetails[i]['pay_month'] = dtObj;
                        if(i+1 < this.empDetails.length && this.empDetails[i]['salary_amount'] != this.empDetails[i+1]['salary_amount'])
                        {
                            Vue.set(this.empDetails[i+1], 'color', true);
                        }
                    }
                    
                })
            },
            newHistoryCreate(){
                this.edit = true;
                let newObj = {};
                if(this.empDetails.length > 0){
                   
                    var payMonth = new Date(this.empDetails[this.empDetails.length-1].pay_month);
                    newObj.pay_month = payMonth.setMonth(payMonth.getMonth() + 1); //next month add

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
                    newObj.emp_id = this.emp_id;
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
                    newObj.emp_id = this.emp_id;
                }

                this.empDetails.push(newObj);
            },
            updateEmpDetail(){
                this.duplicateErrors = [];
                this.errorFlg = false;
                this.success_msg = false;
                Vue.set(this.empData, 'kanaErr','');
                Vue.set(this.empData, 'entryErr', '');
                Vue.set(this.empData, 'dobErr' , '');

                if(this.empData.kana_name == undefined || this.empData.kana_name == ''){
                    this.errorFlg = true;
                    Vue.set(this.empData, 'kanaErr','名前(フリガナ)を入力してください。');
                }

                if(this.empData.entry_date == undefined || this.empData.entry_date == ''){
                    this.errorFlg = true;
                    Vue.set(this.empData, 'entryErr', '入社日を入力してください。');
                }else if(!this.validateDateFormat(this.empData.entry_date)){
                    this.errorFlg = true;
                    Vue.set(this.empData, 'entryErr', '入社日をフォーマット(MM/DD/YYYY)で入力してください。');
                }

                if(this.empData.dob == undefined || this.empData.dob == ''){
                    this.errorFlg = true;
                    Vue.set(this.empData, 'dobErr' , '生年月日を入力してください。');
                }

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
                    Vue.set(this.empDetails[i] , 'bankAccErr','');
                    Vue.set(this.empDetails[i] , 'memberErr','');
                    Vue.set(this.empDetails[i] , 'childErr','');
                    Vue.set(this.empDetails[i] , 'emgPhErr','');
                    Vue.set(this.empDetails[i] , 'wasteTimeErr','');
                    
                    if(this.empDetails[i].pay_month == null)
                    {
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i] , 'payMonthErr','給与年月を入力してください。');
                    } else{
                        var date = new Date(this.empDetails[i].pay_month);
                        var month = date.getMonth(); //Be careful! January is 0 not 1
                        var year = date.getFullYear();
                                
                        this.empDetails[i].pay_month = year+"/"+((month + 1) <10 ? '0'+(month + 1) : (month + 1));

                        if(!this.validatePayMonthFormat(this.empDetails[i].pay_month)){
                            this.errorFlg = true;
                            Vue.set(this.empDetails[i] , 'payMonthErr','給与年月をフォーマット(YYYY/MM)で入力してください。');
                        }
                    }

                    if(this.empDetails[i].salary_amount == 0)
                    {
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'salaryAmountErr', '基本給を入力してください。' );
                    }else if(!this.validateDecimal(this.empDetails[i].salary_amount))
                    {
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'salaryAmountErr', '基本給は数を入れて下さい。' );
                    }

                    if(this.empDetails[i].trans_money == '')
                    {
                        this.empDetails[i].trans_money = 0;
                    }else if(!this.validateNumber(this.empDetails[i].trans_money))
                    {
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'transMoneyErr', '通勤交通費は数を入れて下さい。');
                    }

                    if(this.empDetails[i].jlpt == '')
                    {
                        this.empDetails[i].jlpt = 0;
                    }else if(!this.validateNumber(this.empDetails[i].jlpt))
                    {
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'jlptErr', 'JLPTは数を入れて下さい。');
                    }

                    if(this.empDetails[i].ssb == ''){
                        this.empDetails[i].ssb = 0;
                    }else if(!this.validateNumber(this.empDetails[i].ssb))
                    {
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'ssbErr', 'SSB負担割合は数を入れて下さい。');
                    }

                    if(this.empDetails[i].position == ''){
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'positionErr', 'ポジションを入力してください。');
                    }

                    if(this.empDetails[i].address == ''){
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'addressErr', '住所を入力してください。');
                    }

                    if(this.empDetails[i].phone_no == ''){
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'phoneNoErr', '電話番号を入力してください。');
                    }

                    if(this.empDetails[i].nrc_no == ''){
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'nrcNoErr', '身分証番号を入力してください。');
                    }

                    if(this.empDetails[i].bank_account == ''){
                        this.errorFlg = true;
                        Vue.set(this.empDetails[i], 'bankAccErr', '給与振込先銀行口座を入力してください。');
                    }
    
                }

                if(this.errorFlg){
                    
                    return this.empDetails;
                }

                for(let i=0; i < this.empDetails.length; i++)
                {
                    for(let j= i+1; j < this.empDetails.length; j++)
                    {
                        if(this.empDetails[i]['pay_month'] == this.empDetails[j]['pay_month'])
                        {
                            this.duplicateErrors.push(this.empDetails[i]['pay_month']+'は重複しています。');
                        }
                    }
                }

                if(this.duplicateErrors.length > 0){
                    return this.duplicateErrors;
                }

                this.axios
                .post('http://127.0.0.1:8000/api/employee/save/'+this.emp_id,this.empData)
                .then((response) => {

                })
                
                this.axios
                .post('http://127.0.0.1:8000/api/employeeDetail/updateAll',this.empDetails)
                .then((response) => {
                    this.edit = false;
                    this.findEmployeeHistory();
                    
                    this.success_msg = true;
                    setTimeout(() => {
                        this.success_msg = false;
                    },2000)
                })
                
            },
            validateNumber: function(value){
                var regex = /^\d*$/;
                return regex.test(value);
            },
            validateDecimal: function(value){
                var regex = /^-?\d+(?:\.?\d+)?$/;
                return regex.test(value);
            },
            validatePayMonthFormat: function(value){
                //var re = /^(0[1-9]|1[0-2])\/(0[1-9]|1\d|2\d|3[01])\/(19|20)\d{2}$/ ;
                var regex = /^\d{4}\/(0?[1-9]|1[012])$/ ;
                return regex.test(value);
            },
            validateDateFormat: function(value){
                var regex = /^\d{4}[\/\-](0?[1-9]|1[012])[\/\-](0?[1-9]|[12][0-9]|3[01])$/ ;
                return regex.test(value);
            },
            employeeCodeAndName: function(value){
                return value.employeeId+' '+value.name;
            }

        }
    }
</script>