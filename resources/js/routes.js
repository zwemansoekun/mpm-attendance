import AttendList from './components/AttendList.vue';
import Setting from './components/Setting.vue';
import SalaryHistory from './components/SalaryHistory.vue';

export const routes = [
    {
        name: 'attlist',
        path: '/attendList',
        component: AttendList
    },
    {
        name: 'setting',
        path: '/setting',
        component: Setting
    },
    {
        name: 'salaryHistory',
        path: '/salaryHistory',
        component: SalaryHistory
    }  
];