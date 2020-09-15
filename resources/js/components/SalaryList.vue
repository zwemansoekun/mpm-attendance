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
                <!-- <form id="form" class="" @submit.prevent="SalarySave"  autocomplete="on"> -->
                <div class="row justify-content-md-center mt-4"> 
                      <button type="button" style="background-color:#E7E6E6" class="btn  mr-3" onclick="this.blur();">エンジニアコスト一覧表</button>

                      <a type="button"  href="http://127.0.0.1:8000/export/" >
                            <button @click="excelExport()" class="btn mr-3" style="background-color:#E7E6E6" onclick="this.blur();">
                            給与明細作成
                            </button>
                        </a>
                        
                      <button type="submit" form="form" style="background-color:#E7E6E6" class="btn  mr-3" onclick="this.blur();">編集</button>

                </div>
                 <form id="form" class="" @submit.prevent="SalarySave"  autocomplete="on">
                
                <div v-if="errors"  style="text-align: center">
                    <div v-for="(v,k) in errorsFun" :key="k"> 
                        <div v-if="error_check_messg">
                            <p class="alert alert-danger container mt-1 text-sm" role="alert">
                                <button type="button" class="close" data-dismiss="alert">x</button>
                                    <strong > {{ v }}</strong> 
                            </p>
                        </div>
                    </div>

                </div>                
                
                <div class="row" >
                    <div class="col-md-9">
                      
                            <span class="col-md-2 mt-4" style="background-color:#DEEBF7"> 
                                    {{this.paymentDate(this.select_date,1)}}     
                            </span>
                            <span class="col-md-2 mt-4"> 
                                    支給
                            </span>  
                            <form id="form1" class=""  autocomplete="on" >
                            <!-- {{emps}} -->
                            <!-- {{salaries}} -->
                            <horizontal-scroll>
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
                                <th  rowspan="2"  class="text-center align-middle"  style="background-color:#FBE5D6;">
                                    基本給
                                </th>
                                <th   rowspan="2" class="text-center align-middle"  style="background-color:#FBE5D6;">
                                    通勤交通費
                                </th>
                                <th   rowspan="2" class="text-center align-middle"  style="background-color:#FBE5D6;">
                                    JLPT
                                </th>
                                <th  rowspan="2" class="text-center align-middle"  style="background-color:#FBE5D6;">
                                    ボーナス
                                </th>
                                <th   rowspan="2" class="text-center align-middle"  style="background-color:#FBE5D6;">
                                    合計
                                </th>
                                <th   rowspan="2" class="text-center align-middle" style="background-color:#D9D9D9;">
                                    所得税
                                </th>
                                <th   rowspan="2" class="text-center align-middle" style="background-color:#D9D9D9;">
                                    SSB
                                </th>
                                <th   rowspan="2" class="text-center align-middle" style="background-color:#D9D9D9;">
                                    遅刻欠勤早退
                                </th> 
                                <th  rowspan="2" class="text-center" style="background-color:#D9D9D9;">

                                </th>
                                <th  rowspan="2" class="text-center" style="background-color:#D9D9D9;">
                                    
                                </th>
                                <th rowspan="2" class="text-center" style="background-color:#D9D9D9;">
                                    
                                </th>
                                
                            </tr>
                            <tr>
                                <th  class="border-top-0">
                                </th>
                                <th class="align-middle text-right border-top-0 cal_salaries" style="background-color:#DAE3F3;text-align:right;height: 34px;" v-text="total_payment">
                                        <!-- {{cal_salaries}} -->
                                        <!-- {{total_payment}} -->
                                </th>  
                            </tr>

                            <tbody>
                                <!-- {{salaries.length}} -->
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
                                                        <!-- {{salaries[key].bonus}} -->
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle">{{(parseInt(salaries[key].salary_amount)+parseInt(salaries[key].trans_money)+parseInt(salaries[key].jlpt)).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")}}</td>

                                                        <td style="background-color:#D9D9D9" class="text-right align-middle">{{salaries[key].income_tax}}</td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle">{{salaries[key].ssb!=(undefined || 0)?salaries[key].ssb+"%":''}}</td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle">
                                                          
                                                        </td>

                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle"></td>
                                                        <td style="background-color:#D9D9D9" class="text-right align-middle">

                                                                <!-- {{
                                                                   ( (parseInt(salaries[key].salary_amount)+parseInt(salaries[key].trans_money)+parseInt(salaries[key].jlpt))
                                                                    -
                                                                    (parseInt(salaries[key].ssb))
                                                                   ).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,")
                                                                }} -->

                                                        </td>
                                                    </tr>
                                                    <tr v-bind:key="'A'+key" :class="`index_${key}`">
                                                        <td class="text-center align-middle">実際 
                                                            <input name="pay_month[]"  class="pay_month" style="text-align:right;width:100px;" type="hidden" :value="`${year}/${month}`">
                                                            <input name="employee_id[]"  class="employee_id" style="text-align:right;width:100px;" type="hidden" :value="`${salaries[key].emp_id}`">
                                                        </td>
                                                        <template v-if="get_salary_data.length==0">
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <!-- @value="`${old(income)}`" -->
                                                            <input name="income[]" @change="updateInput" class="income" style="text-align:right;width:100px;padding-right: 3px;" type="text"  :value="`${salaries[key]}`!=undefined?Math.trunc(salaries[key].salary_amount).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="trans_money[]" @change="updateInput" class="trans_money" style="text-align:right;width:100px;padding-right: 3px;" type="text" :value="`${salaries[key]}`!=undefined?salaries[key].trans_money:'' ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="jlpt[]"  @change="updateInput" class="jlpt" style="text-align:right;width:100px;padding-right: 3px;" type="text" :value="`${salaries[key]}`!=undefined?salaries[key].jlpt:'' ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="bonus[]" @change="updateInput" class="bonus" style="text-align:right;width:100px;padding-right: 3px;" type="text" @value="`${old(bonus)}`">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                    <!-- name="total_salary[]" @change="updateInput" -->
                                                                <input class="total_salary" readonly name="total_salary[]" style="text-align:right;width:150px;padding-right: 3px;" type="text"  :value="`${salaries[key]}`!=undefined?(parseInt(salaries[key].salary_amount)+parseInt(salaries[key].trans_money)+parseInt(salaries[key].jlpt)).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>
                                                                
                                                            <!-- 控除額 -->
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="income_tax[]" @change="updateInput"  class="inc_tax" style="text-align:right;width:100px;padding-right: 3px;" type="text"  @value="`${old(income_tax)}`">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="ssb[]" @change="updateInput" class="ssb" style="text-align:right;width:100px;padding-right: 3px;" type="text" 
                                                                :value="
                                                                `${salaries[key].ssb}`!=(undefined || 0)? (300000*(salaries[key].ssb/100) ) :'' 
                                                                ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="leave_late[]" @change="updateInput" class="leave_late" style="text-align:right;width:100px;padding-right: 3px;" type="text" @value="`${old(leave_late)}`">
                                                            </td>

                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="adju1[]" @change="updateInput" class="adju1" style="text-align:right;width:100px;" type="text" @value="`${old(adju1)}`">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="adju2[]" @change="updateInput" class="adju2" style="text-align:right;width:100px;" type="text" @value="`${old(adju2)}`">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="adju3[]" @change="updateInput" class="adju3" style="text-align:right;width:100px;" type="text" @value="`${old(adju3)}`">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <!-- :value="``" -->
                                                                <input name="payment_amount[]" readonly class="payment_amount" style="text-align:right;width:150px;padding-right: 3px;" type="text" 
                                                                :value="`${salaries[key].payment}`.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,')">
                                                                 <!-- :value="`${(parseInt(salaries[key].salary_amount)+parseInt(salaries[key].trans_money)+parseInt(salaries[key].jlpt))-(parseInt(salaries[key].ssb))}`.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,')"> -->
                                                            </td>
                                                        </template>
                                                        <template v-else>                                              
                                                                                                                    
                                                             <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="id[]" class="form-control input-sm idx1" style="text-align: center;" type="hidden" :value="`${get_salary_data[key]}`!=undefined?get_salary_data[key].id:'' ">   
                                                                <input name="income[]" @change="updateInput" class="income" style="text-align:right;width:100px;padding-right: 3px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].income).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,') :'' ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="trans_money[]" @change="updateInput" class="trans_money" style="text-align:right;width:100px;padding-right: 3px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].trans_money).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="jlpt[]"  @change="updateInput" class="jlpt" style="text-align:right;width:100px;padding-right: 3px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].jlpt).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="bonus[]" @change="updateInput" class="bonus" style="text-align:right;width:100px;padding-right: 3px;" type="text"  :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].bonus).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                    <!-- name="total_salary[]" @change="updateInput" -->
                                                                <input class="total_salary" readonly name="total_salary[]" style="text-align:right;width:150px;padding-right: 3px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].total_salary).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>

                                                            <!-- 控除額 -->
                                                            <td class="text-right align-middle" style="padding: 0px;">                                                              
                                                                <input name="income_tax[]" @change="updateInput" class="inc_tax" style="text-align:right;width:100px;padding-right: 3px;" type="text"  :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].income_tax).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="ssb[]" @change="updateInput" class="ssb" style="text-align:right;width:100px;padding-right: 3px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].ssb).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="leave_late[]" @change="updateInput" class="leave_late" style="text-align:right;width:100px;padding-right: 3px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].leave_late).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>

                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="adju1[]" @change="updateInput" class="adju1" style="text-align:right;width:100px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].adju1).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <input name="adju2[]" @change="updateInput" class="adju2" style="text-align:right;width:100px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].adju2).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                               <input name="adju3[]" @change="updateInput" class="adju3" style="text-align:right;width:100px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].adju3).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>
                                                            <td class="text-right align-middle" style="padding: 0px;">
                                                                <!-- :value="``" -->
                                                                <input name="payment_amount[]" readonly class="payment_amount" style="text-align:right;width:150px;padding-right: 3px;" type="text" :value="`${get_salary_data[key]}`!=undefined?Math.trunc(get_salary_data[key].payment_amount).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                                            </td>
                                                        </template>    
                                                    </tr>
                                            
                                            </template>
                            </tbody>

                        </table>    
                           </horizontal-scroll>
                        </form>   
                    </div>
                    {{SsbMax}}    {{SsbPaid}}
                    <div class="col-md-3">
                        <span  class="col-md-2 mt-4"></span>
                        <form id="form2" class="" autocomplete="on" >
                        <horizontal-scroll>
                        <table id="ssbtable" class="table table-sm table-bordered">
                            <tr>
                                <th  colspan="2" class="align-middle text-center">
                                        SSB
                                </th>
                                 <th  rowspan="3" class="align-middle text-center">
                                        備考
                                </th>
                            </tr>
                            <tr>
                                <th rowspan="2" class="align-middle text-center" style="text-align: center;">
                                        総額
                                </th>
                                 <th   class="align-middle text-center border-bottom-0" style="background-color:yellow">
                                        会社負担分
                                </th>
                            </tr>
                            <tr>
                                <th   class="align-middle text-right border-top-0 paid-ssb" style="background-color:yellow;height: 31px;"  v-text="total_c_paid">
                                                                            
                                </th>   
                            </tr>
                            <tbody>
                                <!-- {{ssbs[0].id}} -->
                                   <template  v-for="key in salaries.length">
                                            
                                    <tr v-bind:key="key">
                                        <!-- {{ssbs.length!=0?Math.trunc(ssbs[0].total_amount).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"):'15,000'}} -->
                                       <td style="padding: 0px;width: 30%;text-align: right;background-color:#D9D9D9">15,000</td>
                                        <!-- {{ssbs.length!=0?Math.trunc(ssbs[0].c_paid).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"):'15,000'}} -->
                                       <td style="padding: 0px;width: 30%;text-align: right;background-color:#D9D9D9">15,000</td>
                                       <td style="padding: 0px;width: 40%;" rowspan="2">
                                            <template v-if="get_salary_data.length==0"> 
                                                <textarea name="remark[]" class="remark" style="text-align:left;width:100%;border: none;-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" @value="`${old(remark)}`"></textarea>
                                            </template>    
                                            <template v-else> 
                                                 <textarea name="remark[]" class="remark" style="text-align:left;width:100%;border: none;-webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;" :value="`${get_salary_data[key-1].ssbval}`!=undefined?get_salary_data[key-1].ssbval.remark:'' "></textarea>
                                            </template>
                                            <!-- <input name="remark[]"  class="remark" style="text-align:left;width:100px;" type="text" :value="``"> -->
                                       </td>
                                   </tr>  
                                    <template v-if="get_salary_data.length==0"> 
                                        <tr v-bind:key="'A'+key" :class="`index_${key-1}`">
                                            <td style="padding: 0px;width: 30%;text-align: right;height: 10%;">
                                                    <input name="ssb_total[]" @change="ssbCalc" class="ssb_total" style="text-align:right;width:100%;" type="text" :value="`15,000`">
                                            </td>
                                            <td style="padding: 0px;width: 30%;text-align: right;height: 10%;">
                                                    <input name="ssb_c_paid[]" @change="ssbCalc"  class="ssb_c_paid" style="text-align:right;width:100%;" type="text" :value="`${salaries[key-1].c_paid}`">
                                            </td>
                                        </tr>     
                                    </template>
                                    <template v-else>
                                         <tr v-bind:key="'A'+key" :class="`index_${key-1}`">
                                            <td style="padding: 0px;width: 30%;text-align: right;height: 10%;">    
                                                    <input name="id[]" class="form-control input-sm idx2" style="text-align: center;" type="hidden" :value="`${get_salary_data[key-1].ssbval}`!=undefined?get_salary_data[key-1].id:'' ">                                              
                                                    <input name="ssb_total[]" @change="ssbCalc" class="ssb_total" style="text-align:right;width:100%;" type="text" :value="`${get_salary_data[key-1].ssbval}`!=undefined?Math.trunc(get_salary_data[key-1].ssbval.total_amount).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                            </td>
                                            <td style="padding: 0px;width: 30%;text-align: right;height: 10%;">
                                                    <input name="ssb_c_paid[]" @change="ssbCalc"  class="ssb_c_paid" style="text-align:right;width:100%;" type="text" :value="`${get_salary_data[key-1].ssbval}`!=undefined?Math.trunc(get_salary_data[key-1].ssbval.c_paid).toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,'):'' ">
                                            </td>
                                        </tr>     
                                    </template>
                                   </template>

                            </tbody>    
                        </table>
                       </horizontal-scroll>
                       </form>
                    </div> 
                </div>
            </form>    
        </div>
    </div>    
</template>

<script>   
import HorizontalScroll from 'vue-horizontal-scroll'
import 'vue-horizontal-scroll/dist/vue-horizontal-scroll.css'
console.log(process.env.MIX_CLIENT_SECRET);
window.APP ="{{config('global')}}";//"{config('global')}" ;//JSON.parse($globals);    //;

    export default {
        
        data() {
            return {
               appx:window.APP,
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
                errors:null,
                focusRecord:'',
                ssbs:'',
                error_check_messg:false,
                get_salary_data:[],
                total_payment:'',
                // company_ssb:'',
                total_c_paid:'',
            }
        },
        components: {
            HorizontalScroll
        },
        computed: {
           errorsFun:function(){
                this.error_check_messg = true 
                return this.errors;
            },         
        },  
        mounted(){
        //    this.updateInput();
        },  
        watch : {
             deep: true,            
            error_check_messg:function(){
                if(this.error_check_messg==true){
                    setTimeout(() => {
                        this.error_check_messg = false
                    },5000)
                    return false;
                }
            }  
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
                            // console.log('dddd',that.emps[v].id);
                           that.emp_arr[that.emps[v].id]=that.emps[v].employeeId;
                           that.name_arr[that.emps[v].id]=that.emps[v].name;
                       }
                     
                   }
                  
            });
           
            // this.axios
            // .get((window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/salaryList/ssb/all")
            // .then(response => {
            //         that.ssbs=response.data;
            // })
            
           
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
            SalarySave:function(){
                let that=this;
                // let those=this;


                var validator1 = jQuery('#form1').validate({
                  
                    rules: {
                            "pay_month[]": "required",
                            "income[]": "required",
                            // "trans_money[]": "",
                            // "jlpt[]": "",
                            // "bonus[]": "",
                            "total_salary[]":"required",
                            "income_tax[]":"required",
                            // "ssb[]":"",
                            // "leave_late[]":"",
                            "payment_amount[]":"required",
                    },  
                    messages: {
                                
                            // "income[]": {
                            //     required: '基本給は必要です。',
                            // },     
                            // "total_salary[]": {
                            //     required: "合計は必要です。",    
                            // },     
                            // "income_tax[]": {
                            //     required: "所得税は必要です。",    
                            // },
                            // "payment_amount[]": {
                            //     required: "支給額は必要です。",    
                            // },
                    },
                    errorPlacement: function(error,element) {
                        return true;
                    },
                });

                
                var validator2 = jQuery('#form2').validate({
                  
                    rules: {
                            "ssb_total[]": "required",
                            "ssb_c_paid[]": "required",
                            // "remark[]": "",
                    }, 
                    errorPlacement: function(error,element) {
                        return true;
                    },
                });
                let temp_salary=[],pay_month=0,employee_id=0,income=0,trans_money=0,jlpt=0,bonus=0,inc_tax=0,ssb=0,leave_late=0,payment_amount=0,total_salary=0,adju1='',adju2='',adju3='';//,
                let temp_ssb=[],ssb_total=0,ssb_c_paid=0,remark='',id1='',id2='';
                if(jQuery('#form1').valid() && jQuery('#form2').valid()){
                    
                    // if(jQuery('#form1').valid()){
                        $('#salaryTable tr[class^=index_]').each(function (key,value) {
                            let that=this;   
                            $(that).find('td').each(function (index,v2) {
                                console.log('index',index);
                                    let isLastElement = index == $(that).find('td').length-1;
                                       console.log('isLastElement',$(that).find('td').length);
                                    if(jQuery(this).find('.pay_month').val()){
                                         console.log('pay_month',jQuery(this).find('.pay_month'));
                                    pay_month=jQuery(this).find('.pay_month').val();
                                    }
                                    if(jQuery(this).find('.employee_id').val()){
                                         console.log('employee_id',jQuery(this).find('.employee_id'));
                                    employee_id=jQuery(this).find('.employee_id').val();
                                    }
                                    if(jQuery(this).find('.income').val()){
                                         console.log('income',jQuery(this).find('.income'));
                                    income=jQuery(this).find('.income').val().toString().replace(/,/g , '');
                                    }
                                    if(jQuery(this).find('.trans_money').val()){
                                    trans_money=jQuery(this).find('.trans_money').val();
                                    }
                                    if(jQuery(this).find('.jlpt').val()){
                                        jlpt=jQuery(this).find('.jlpt').val();
                                    }
                                    if(jQuery(this).find('.bonus').val()){
                                        bonus=jQuery(this).find('.bonus').val();
                                    }
                                    if(jQuery(this).find('.total_salary').val()){
                                        total_salary=jQuery(this).find('.total_salary').val().toString().replace(/,/g , '');
                                    }
                                    if(jQuery(this).find('.inc_tax').val()){
                                        inc_tax=jQuery(this).find('.inc_tax').val();
                                    }
                                    if(jQuery(this).find('.ssb').val()){
                                        ssb=jQuery(this).find('.ssb').val();
                                    }
                                    if(jQuery(this).find('.leave_late').val()){
                                        leave_late=jQuery(this).find('.leave_late').val().toString().replace(/,/g , '');
                                    }
                                    if(jQuery(this).find('.adju1').val()){
                                        adju1=jQuery(this).find('.adju1').val().toString().replace(/,/g , '');
                                    }
                                      if(jQuery(this).find('.adju2').val()){
                                        adju2=jQuery(this).find('.adju2').val().toString().replace(/,/g , '');
                                    }
                                    if(jQuery(this).find('.adju3').val()){
                                        adju3=jQuery(this).find('.adju3').val().toString().replace(/,/g , '');
                                    }
                                    if(jQuery(this).find('.payment_amount').val()){
                                        payment_amount=jQuery(this).find('.payment_amount').val().toString().replace(/,/g , '');
                                        console.log('bbbbb',payment_amount);
                                    }
                                    if(jQuery(this).find('.idx1').val()){
                                       id1=jQuery(this).find('.idx1').val();
                                    }
                                    if (isLastElement) {
                                          console.log('final',isLastElement);
                                        if(id1!=''){
                                              temp_salary.push({"id":id1,"pay_month":pay_month,"employee_id":employee_id,"income":income,"trans_money":trans_money,"jlpt":jlpt,"bonus":bonus,"income_tax":inc_tax,"ssb":ssb,"leave_late":leave_late,"payment_amount":payment_amount,"total_salary":total_salary,"adju1":adju1,"adju2":adju2,"adju3":adju3});//
                                        }else{
                                              temp_salary.push({"pay_month":pay_month,"employee_id":employee_id,"income":income,"trans_money":trans_money,"jlpt":jlpt,"bonus":bonus,"income_tax":inc_tax,"ssb":ssb,"leave_late":leave_late,"payment_amount":payment_amount,"total_salary":total_salary,"adju1":adju1,"adju2":adju2,"adju3":adju3});//
                                        }
                                      
                                        id1='';pay_month=0;employee_id=0;income=0;trans_money=0;jlpt=0;bonus=0;inc_tax=0;ssb=0;leave_late=0;payment_amount=0;total_salary=0;adju1='';adju2='';adju3='';
                                        return false;
                                    }
                            })                          
                        })
                    // }

                    // if(jQuery('#form2').valid()){
                        $('#ssbtable tr[class^=index_]').each(function (key,value) {
                            let that=this;
                            $(that).find('td').each(function (index,v2) {
                                let isLastElement = index == $(that).find('td').length-1;
                                    
                                if(jQuery(this).find('.ssb_total').val()){
                                    ssb_total=jQuery(this).find('.ssb_total').val().toString().replace(/,/g , '');
                                }
                                if(jQuery(this).find('.ssb_c_paid').val()){
                                    ssb_c_paid=jQuery(this).find('.ssb_c_paid').val().toString().replace(/,/g , '')
                                }                              
                                if(jQuery(this).closest("tr").prev('tr').find('.remark').val()){
                                    remark=jQuery(this).closest("tr").prev('tr').find('.remark').val();
                                }
                                if(jQuery(this).find('.idx2').val()){
                                    id2=jQuery(this).find('.idx2').val();
                                }
                                if (isLastElement) {
                                    if(id2!=''){
                                         temp_ssb.push({"id":id2,"total_amount":ssb_total,"c_paid":ssb_c_paid,"remark":remark});
                                    }else{
                                         temp_ssb.push({"total_amount":ssb_total,"c_paid":ssb_c_paid,"remark":remark});
                                    }
                                   
                                    id2='';ssb_total=0;ssb_c_paid=0;remark='';
                                    return false;
                                }
                            });
                        });
                    // }
                    


                }else{
                     that.$fire({
                        title: "失敗！！",
                        text: "データを記入してください。",
                        type: "warning",
                        timer: 3000,
                        showCancelButton: false,
                        showConfirmButton: false,
                        // position:'center',
                        }).then(r => {
                        //    console.log(r.value);
                        }); 
                    return false;
                }

                // console.log('temp_salary',temp_salary);
                // console.log('temp_ssb',temp_ssb);
                this.axios({
                      url:(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/salaryList",
                      method: 'post',
                      data: {"form1": temp_salary,"form2": temp_ssb}
                    })
                    .then(function (response) {
                        console.log('aa1',response);
                          console.log('aa2',response.data);
                            console.log('aa3',response.data.message);

                        // your action after success                      
                        if(response.data){
                            // console.log(response.data);    
                            
                            // your action after success                      
                            //  console.log('message',response.message);
                            if(response.data.message==true){
                                that.salaries=[];
                                that.get_salary_data=[];
                                console.log(that.salaries);
                                that.update_call();  
                                that.$fire({
                                    title: "成功しました",
                                    text: "データの登録は成功しました。",
                                    type: "success",
                                    timer: 3000,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    // position:'center',
                                }).then(r => {
                                    //    console.log(r.value);
                                });
                            }else{
                                that.$fire({
                                title: "失敗！！",
                                text: "データの登録は出来ません。",
                                type: "error",
                                timer: 1500,
                                showCancelButton: false,
                                showConfirmButton: false,
                                // position:'center',
                                }).then(r => {
                                //    console.log(r.value);
                                }); 
                                that.errors=response.data.errors;                             
                            }                       
                      

                        }
                    })
                    .catch(function (error) {
                    });
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
                
                    that.total_payment='';
                    that.company_ssb='',
                    that.total_c_paid='',
                  
                    that.update_call();
                  

                }else{
                    return false;
                }

                // if(event.target.value!=''){

                    // this.axios
                    // .get((window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/salaryList/"+this.year+"-"+this.month)                 
                    // .then(response => {
                    //     // console.log('salary',response.data.length); 
                    //     that.salaries=response.data;
                    //     // console.log('salary',response.data.length);
                    // })
                    // .catch(function (error) {
                    //     // console.log(error.response)
                    // });

                // }
            },
            update_call:function(){
                    console.log('update');
                    let that=this;  
                    let up_data={                       
                        "pay_month":that.select_date,
                        // "employee_id ":
                    };
                    this.axios({
                      url:(window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/salaryList/getsalary",
                      method: 'post',
                      data:up_data,
                    })                  
                    .then(response=>{ 
                        // that.get_attend_data=[];
                        // that.data_combine=[];
                        // that.check_attend_data=false;                        
                        that.get_salary_data=response.data;
                        console.log('getsalary',that.get_salary_data);
                          console.log('data',response.data);
                        console.log('nores',that.get_salary_data);   
                        that.salaryList(that.get_salary_data);                  
                    })
                    .catch(function (error) {
                        console.log('nopar byar',that.get_salary_data); 
                        console.log('geterror',error.response);
                    }); 
            },
            salaryList:function(get_salary_data=''){
                    let that=this;  
                    let tem_salary=[];
                    let salaries_arr=[];
                    let total_payment=0;
                    // let company_ssb=0;
                    let total_c_paid=0;
                    this.axios
                    .get((window.location.protocol!=='https:'?'http:':'https:' )+ "//" + window.location.host + "/salaryList/"+this.year+"-"+this.month )                 
                    .then(response => {
                        console.log('salary',response.data.length); 
                        // that.salaries=response.data;
                        tem_salary=response.data;
                        console.log('salary1',tem_salary.length); 
                        if(get_salary_data==''){
                            for(let i=0;i<tem_salary.length;i++){
                               console.log('salary12',tem_salary[i]); 
                                tem_salary[i]['total']=(parseInt(tem_salary[i].salary_amount)+parseInt(tem_salary[i].trans_money)+parseInt(tem_salary[i].jlpt))
                                tem_salary[i]['payment']=parseInt(tem_salary[i]['total'])-(300000*(parseInt(tem_salary[i].ssb)/100));
                                total_payment+=tem_salary[i]['payment'];
                                total_c_paid+=(300000*(3/100));
                                tem_salary[i]['c_paid']=(300000*(3/100));
                                console.log('salary33',tem_salary); 
                            }
                        }else{
                            for(let i=0;i<get_salary_data.length;i++){
                            //    console.log('salary12',tem_salary[i]); 
 
                                // tem_salary[i]['total']=(parseInt(tem_salary[i].salary_amount)+parseInt(tem_salary[i].trans_money)+parseInt(tem_salary[i].jlpt))
                               
                                total_payment+=parseInt(get_salary_data[i].payment_amount);
                                total_c_paid+=parseInt(get_salary_data[i].ssbval.c_paid);
                                // console.log('salary33',tem_salary); 
                            }
                        }
                      
                        console.log('ok',tem_salary);
                        console.log('ok22',total_payment);
                        that.total_payment=total_payment;
                        // that.company_ssb=company_ssb;
                        that.salaries=tem_salary;
                        that.total_c_paid=total_c_paid;
                    })
                    .catch(function (error) {
                        console.log(error.response)
                    });
            },
            ssbCalc:function(event){
                    let cal_ssb=0;
                    $("input[name^='ssb_c_paid']").each(function() {
                        console.log('checkpoint1',$(this).val());
                        // console.log('comma remove',$(this).val().replace(/,/g , ''));
                        //    console.log(!isNaN($(this).val()));
                            //  console.log($(this).val().toString().replace(/,/g , ''));
                            if( !isNaN($(this).val().toString().replace(/,/g , '')) && $(this).val().toString().replace(/,/g , '')!='') {
                                 console.log('checkpoint12',$(this).val());
                                cal_ssb+=parseInt($(this).val().toString().replace(/,/g , ''));
                                 console.log('checkpoint123',cal_ssb);
                            }
                        //  that.salary_amounts+=parseInt($(this).val());
                    });
                     $("#ssbtable").find('.paid-ssb').text(cal_ssb.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
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
                    let inc_tax=0,ssb=0,leave_late=0,payment_amount=0,adju1=0,adju2=0,adju3=0;
                    
                 


                    b_salary=$(event.target).parent().parent().find('.income').val()!=''?$(event.target).parent().parent().find('.income').val():0;
                    t_m=$(event.target).parent().parent().find('.trans_money').val()!=''?$(event.target).parent().parent().find('.trans_money').val():0;
                    jlpt=$(event.target).parent().parent().find('.jlpt').val()!=''?$(event.target).parent().parent().find('.jlpt').val():0;
                    bnu=$(event.target).parent().parent().find('.bonus').val()!=''?$(event.target).parent().parent().find('.bonus').val():0;

                    inc_tax=$(event.target).parent().parent().find('.inc_tax').val()!=''?$(event.target).parent().parent().find('.inc_tax').val():0;
                    ssb=$(event.target).parent().parent().find('.ssb').val()!=''?$(event.target).parent().parent().find('.ssb').val():0;
                    leave_late=$(event.target).parent().parent().find('.leave_late').val()!=''?$(event.target).parent().parent().find('.leave_late').val():0;

                    adju1=$(event.target).parent().parent().find('.adju1').val()!=''?$(event.target).parent().parent().find('.adju1').val():0;
                    adju2=$(event.target).parent().parent().find('.adju2').val()!=''?$(event.target).parent().parent().find('.adju2').val():0;
                    adju3=$(event.target).parent().parent().find('.adju3').val()!=''?$(event.target).parent().parent().find('.adju3').val():0;
                    
                    total=parseInt(b_salary.toString().replace(/,/g , ''))+parseInt(t_m.toString().replace(/,/g , ''))+parseInt(jlpt.toString().replace(/,/g , ''))+parseInt(bnu.toString().replace(/,/g , ''));
                    if(total<0){
                        total=0;
                    }
                    payment_amount=total - ( (parseInt(inc_tax.toString().replace(/,/g , ''))+parseInt(ssb.toString().replace(/,/g , ''))+parseInt(leave_late.toString().replace(/,/g , ''))) +  (parseInt(adju1.toString().replace(/,/g , ''))+parseInt(adju2.toString().replace(/,/g , ''))+parseInt(adju3.toString().replace(/,/g , ''))) ) ;
                    if(payment_amount<0){
                        payment_amount=0;
                    }
                    // console.log('a',total);
                    // console.log('b',salary_amount);
                    $(event.target).parent().parent().find('.total_salary').val(!isNaN(total)?total.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"):0) ;
                    $(event.target).parent().parent().find('.payment_amount').val(!isNaN(payment_amount)?payment_amount.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"):0);
                    // that.salary_amounts.push(parseInt(salary_amount));
                    let cal_salary=0;
                    $("input[name^='payment_amount']").each(function() {
                        // console.log('checkpoint',$(this).val());
                        // console.log('comma remove',$(this).val().replace(/,/g , ''));
                            if($(this).val()!='') {
                                cal_salary+=parseInt($(this).val().toString().replace(/,/g , ''));
                                $("#salaryTable").find('.cal_salaries').text(cal_salary.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                            }
                        //  that.salary_amounts+=parseInt($(this).val());
                    });
                    // if(cal_salary>0){
                    //      console.log('calculate',cal_salary);
                    //        console.log('paths',$(event.target).parent().parent().parent().parent());
                    //        $("#salaryTable").find('.cal_salaries').text(cal_salary.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                    //        $(event.target).parent().parent().find('.salary_amount').val('');
                    //        $(event.target).parent().parent().find('.salary_amount').val(salary_amount.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                    //        $(event.target).parent().parent().find('.total_salary').val('');
                    //        $(event.target).parent().parent().find('.total_salary').val(total.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                    //     // $(event.target).parent().parent().parent().parent().find('.cal_salaries').text(cal_salary.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,"));
                    // }
                    
                    // that.cal_salaries=cal_salary;
                    // console.log('cal',that.cal_salary);
                    //  console.log('calculate',cal_salary);


            }, 
        }
    }
</script>