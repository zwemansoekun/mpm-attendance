<template>
    <div class="col-md-10"> 
        <div class="container">
            <div class="row">
                <div class="col-md-4" id="app"> 
                    <datepicker class="datepicker1" :minimumView="'year'" :maximumView="'year'" v-model="customDate" 
                            :format="customFormatter" id="dtPicker"
                             v-on:selected="selectedDate()" ></datepicker>
                </div>  
            </div> 
        </div>   
               
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-4"> {{customFormatter(customDate)}}</div>
                    <input type="hidden" name="dtInput" :value="customDate">
            </div>

            <div class="row">
                <div class="col-md-4 mt-3">
                    <button type="button" class="btn btn-primary" ref="btnToggle" @click="addRow(holidays)" 
                        :disabled="!btnEnabled" >{{btnText.text}}
                    </button>
                </div>
                <div class="col-md-4 mt-3">
                    <button type="button" class="btn btn-primary" v-if="isBtnHidden" @click="copyRow()">前年の物をコピー</button>
                </div>
                <div class="col-md-4 mt-3">
                    <button type="button" class="btn btn-primary" :disabled="checkDeleteBtnDisable()" 
                        @click="deleteRow(customFormatter(customDate))">Delete</button><!--削除-->
                </div>
            </div> 
            
            <table class="table table-bordered mt-3" style="width: 73.5%;">
                <thead class="bg-info text-white">
                    <tr>
                        <th scope="col" style="width: 10%;"></th>
                        <th scope="col" style="width: 40%;">Date</th>
                        <th scope="col" style="width: 50%;">Description</th>
                    </tr>
                </thead>
                <tbody v-sortable.tr="holidays" v-if="isRowOne">
                    <tr v-for="holiday in holidays" :key="holiday.id">
                        <td class="align-middle">
                            <input type="checkbox" v-model="holiday.selected">
                        </td>
                        <td>
                            <span v-if="holiday.dtError" class="text-danger">Please enter the date</span><!--日付を入力してください-->
                            <span v-if="holiday.dtDuplicateError" class="text-danger">Duplicate dates</span><!--日付は重複しています -->
                            <span v-if="holiday.yearError" class="text-danger">
                                {{customFormatter(customDate)}} Please enter your holiday <!--の休日を入力してください-->
                            </span>
                            <datepicker class="datepicker1" :minimumView="'day'" :maximumView="'month'" v-model="holiday.date" 
                                :format="dayMonthFormatter" :typeable="true"></datepicker>
                        </td>
                        <td>
                            <span v-if="holiday.desError" class="text-danger">Please enter the description</span><!--名称を入力してください-->
                            <span v-if="holiday.desDuplicateError" class="text-danger">The description is duplicated</span><!--名称は重複しています-->
                            <input class="form-control" v-model="holiday.description" type="text"/>
                        </td>
                    </tr>
                </tbody>
                
                <tbody  v-if="isRowTwo">
                    <tr>
                        <td><input type="checkbox"></td>
                        <td><input class="form-control"  type="text" value=""/></td>
                        <td><input class="form-control"  type="text" value=""/></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<style >
.datepicker1 input{
    height: calc(1.6em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 0.9rem;
    font-weight: 400;
    line-height: 1.6;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}
</style>

<script>
import Datepicker from 'vuejs-datepicker'
import { required, minLength, between } from 'vuelidate/lib/validators'
var moment = require('moment'); 
    export default {
        components: {
            Datepicker
        },
        data: function () {
            
            return {
                customDate: new Date(),holidays: [],seen: true, btnText: {text:'Edit'},//編集
                isBtnHidden:false, isRowOne:true, isRowTwo: false, btnEnabled:true,
                deleteItems: []
               
            }
        },
        
        created() {
            this.axios
                .get(process.env.MIX_APP_URL+'/api/holidays')
                .then(response => {
                    this.holidays = response.data;
                    
                });
        },
        methods :{
            checkDeleteBtnDisable: function(){
                if(this.holidays.length > 0){
                    for(let i=0; i < this.holidays.length; i++){
                        if(this.holidays[i].selected){
                            return false;
                        }
                    }
                    return true;
                }
                return true;
            },
            customFormatter(date) {
                return moment(date).format('YYYY');
            },
            addRow: function (holidays) {
                try {                              
                    if(this.$refs.btnToggle.innerText !=='Save'){//保存
                        this.holidays.splice(holidays.length + 1, 0, {});
                        this.seen =  !this.seen;
                        this.$refs.btnToggle.innerText = this.show?'Edit': 'Save';//保存
                    }else{
                        for(let i=0; i <this.holidays.length; i++){
                            Vue.set(holidays[i],"dtDuplicateError",false);
                            Vue.set(holidays[i],"desDuplicateError",false);
                            if(this.holidays[i].date === "" || this.holidays[i].date == undefined){
                                Vue.set(holidays[i],"dtError",true);
                            }
                            if(this.holidays[i].description === "" || this.holidays[i].description == undefined){
                                Vue.set(holidays[i],"desError",true);
                            }
                        }
                         
                        for(let i=0; i <this.holidays.length; i++){
                          if((holidays[i].date == undefined && holiday[i].dtError == true)
                          || (holidays[i].description == undefined && holiday[i].desError == true)){
                              return this.holidays;
                          }
                        }
                        for(let i=0; i <this.holidays.length; i++){
                              holidays[i]["date"] = moment(holidays[i]["date"]).format('YYYY-MM-D');
                        }
                        for (var i = 0; i < holidays.length; i++) {
                            for(var j= i+ 1; j < holidays.length; j++){
                                if (holidays[i]["date"] === holidays[j]["date"]) {
                                    holidays[j]["dtDuplicateError"] = true;
                                }
                                if (holidays[i]["description"] === holidays[j]["description"]) {
                                    holidays[j]["desDuplicateError"] = true;
                                }
                            }
                            holidays[i]["dtError"] = false;
                            holidays[i]["desError"] = false;
                        }
                        for(let i=0; i <this.holidays.length; i++){
                            if(holidays[i].dtDuplicateError == true && holiday[i].desDuplicateError == true){
                                return this.holidays;
                          }
                        }
                        this.axios
                        .post(process.env.MIX_APP_URL+'/api/holidays', Object.values(this.holidays))
                        .then(response => (
                            this.$refs.btnToggle.innerText = 'Edit',
                            this.holidays =[],
                            this.axios
                                .get(process.env.MIX_APP_URL+'/api/holidays/findYear/'+ moment(this.customDate).format("yyyy"))
                                .then(response => (
                                    this.holidays = response.data
                                ))
                        ))
                        .catch(error=> {
                            
                        });
                     
                    }
                    
                } catch(e){
                   
                }
            },
            
            selectedDate: function(e){
                this.$nextTick(function(){
                    this.returnValue();
                });
            },
            returnValue: function(){
                this.loadingAlert();
                this.axios
                    .get(process.env.MIX_APP_URL+'/api/holidays/findYear/'+ moment(this.customDate).format("yyyy"))
                    .then(response => {
                        this.holidays = response.data;
                       if(moment(this.customDate).format("yyyy") !== moment(new Date()).format("yyyy")){
                            this.$refs.btnToggle.innerText = 'Edit';
                            this.isBtnHidden = true;
                            if(this.holidays.length != 0){
                                this.isRowOne = true;
                                this.isRowTwo = false;
                                this.btnEnabled = true;
                            }else{
                                this.btnEnabled = false;
                                this.isRowOne = false;
                                this.isRowTwo = true;
                            }
                        }else{
                            this.isRowTwo = true;
                            this.btnEnabled = true;
                            this.isBtnHidden = false;
                            if(this.holidays.length != 0){
                                this.isRowOne = true;
                                this.isRowTwo = false;
                            }else{
                                this.isRowOne = false;
                                this.isRowTwo = true;
                            }
                        }
                    });
                },
            copyRow: function () {
                this.isRowOne = true;
                this.isRowTwo = false;
                this.axios
                    .get(process.env.MIX_APP_URL+'/api/holidays/copy')
                    .then(response => {
                        
                        this.holidays = response.data,
                        this.btnEnabled = true;
                    });
            },
            loadingAlert:function(){
                  let that=this;
                   that.$swal({
                        title: 'Please wait!',
                        // add a custom html tags by defining a html method.
                        html: 'Loading......',
                         timer: 60,
                        showCloseButton: false,
                        showCancelButton: false,
                        showConfirmButton: false,
                        focusConfirm: false,
                        allowOutsideClick: false,
                        //  onBeforeOpen: () => {
                        //     that.$swal.showLoading();
                        //     },
                        }) 
            },  
            deleteRow: function(date){
                for(let i=0; i <this.holidays.length; i++){    
                    if(this.holidays[i].selected){
                        console.log(this.holidays[i].id)
                        this.deleteItems.push(this.holidays[i].id);
                    }
                }
                this.deleteItems.forEach((value, index) => {
                    if(value == 'undefined'){
                         this.holidays.splice(holidays.length - 1, 0, {});
                    }else{
                        
                        this.axios
                            .post(process.env.MIX_APP_URL+'/api/holidays/deleteRow/'+value+'/'+date)
                            .then(response => {
                                this.holidays = response.data;
                        });
                    }
                });
               
            },
            dayMonthFormatter(date) {
                return moment(date).format('MM-D');
            },
        },
        directives: {
            sortable: {
                twoWay: true,
                deep: true
            }
        }
    }
</script>