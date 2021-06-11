<template>
    <div class="col-md-12 py-4"> 
        <div class="container-fluid">
            
            <div class="row">
                <div class="col-12">
                    <h4 class="card-title"> Holiday List </h4>
                    <hr class="underline_title">
                </div> 
                <div class="col-md-5" id="app"> 
                    <datepicker class="datepicker1" :minimumView="'year'" :maximumView="'year'" v-model="customDate" 
                            :format="customFormatter" id="dtPicker"
                             v-on:selected="selectedDate()"></datepicker>
                </div>  
            </div> 
        </div>   
               
        <div class="container-fluid mt-3">
            <div class="row">
                <div class="col-md-4"> <label class="select_year">{{customFormatter(customDate)}}</label></div>
                    <input type="hidden" name="dtInput" :value="customDate">
            </div>

            <div class="row">
                <div class="col-md-4">
                    <button type="button" class="btn btn_edit" ref="btnToggle" @click="addRow(holidays)" 
                        :disabled="!btnEnabled" >{{btnText.text}}
                    </button>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn_copy" v-if="isBtnHidden" @click="copyRow()">前年の物をコピー</button>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn_del" :disabled="checkDeleteBtnDisable()" 
                        @click="deleteRow(customFormatter(customDate))">Delete</button><!--削除-->
                </div>
            </div> 
            
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <table class="table mt-3" style="width: 80.5%; background-color:#87a484;">
                        <thead class="text-white" style="background-color:#6c8369;">
                            <tr style="line-height:35px;"> 
                                <th scope="col" style="width: 10%;"></th>
                                <th scope="col" style="width: 40%;"><span class="tbl_title">Date</span></th>
                                <th scope="col" style="width: 50%;"><span class="tbl_title">Description</span></th>
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
                                    <input class="form-control description" v-model="holiday.description" type="text"/>
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
        </div>
    </div>
</template>

<style >

.datepicker1 input, input[type=text].description {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 15px;
        background-color: #fff;
        height: 50px;
        width: 100%;
        padding: 12px 20px;
        margin: 0px 0;
        display: inline-block;
        border: 1px solid white;
        border-radius: 4px;
        box-sizing: border-box;
        color:black;
    
}

#dtPicker {
    background-color: white;
    border:1px solid gray;
}
#dtPicker:hover {
    background-color:silver;
    border:1px solid gray;
    color:black;
    transition: 0.5s;
    animation-delay: 0.8s;
    -webkit-animation-delay:0.8s;
}
input[type=checkbox] {
    margin-left: 25px;
}

.datepicker1 input:hover, input[type=text].description:hover {
    border:1px solid silver;
    background-color: #bfbfbf;
    color:black;
    /* letter-spacing: 1px; */
    /* transition: 0.5s;
    animation-delay: 0.8s;
    -webkit-animation-delay:0.8s; */
}





#app {
    margin:20px 0px;
}
.btn_del:disabled,
.btn_del[disabled]{
  border: 2px solid red;
  background-color: #fff;
  color: red;
  cursor: pointer;
}

.description {
    font-family: arial;
    font-size: 15px;
}

/* input[type=text].description {
      border: 1px solid white;
      background-color: silver;
      color:black;
      border-radius: 0px;
} */

span.tbl_title{
    font-family: arial;
    font-size: 16px;
}

label.select_year {
    font-family: verdana;
    font-size:18px;
    color:black;
    margin-left: 15px;
}

.btn_del, .btn_edit, .btn_copy {
    /* border-radius: 20px; */
    padding:12px 32px;
    border:2px solid white;
    background-color:red;
    color:white;
    font-family: verdana;
    font-size:14px;
}

.btn_copy {
    background-color: #008844;
    color:white;
}

.btn_edit {
    background-color:#005b96;
    color:#fff;
    border:1px solid #005b96;
}

.btn_edit:hover, .btn_del:hover, .btn_copy:hover {
    color:#fff;
    cursor:pointer;
}

/* .datepicker1 input{
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
} */
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
