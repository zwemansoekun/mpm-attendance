import AttendList from './components/AttendList.vue';
import Setting from './components/Setting.vue';

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
    }  
];