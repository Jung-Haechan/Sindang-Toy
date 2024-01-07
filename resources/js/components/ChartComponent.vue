<template>
    <div>비교할 사람의 이름을 공백으로 나눠서 입력하세요.</div>
    <input type="text" placeholder="ex) 이준석 한동훈 이재명" style="border: 1px solid grey; padding: 7px; margin: 10px" v-model="names" @keyup.enter="getNewsCount">
    <button style="border: 1px solid grey; padding: 7px" @click="getNewsCount">검색</button>
    <Line :data="data" :options="options" style="width: 100%"/>
</template>

<script lang="ts">
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
} from 'chart.js'
import { Line } from 'vue-chartjs'
import axios from "axios";
import {ref} from "vue";

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
)

export default {
    name: 'App',
    components: {
        Line
    },
    setup() {
        const data = ref({
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
            datasets: [
                {
                    label: 'Data One',
                    backgroundColor: '#f87979',
                    data: [40, 39, 10, 40, 39, 80, 40]
                },
                {
                    label: 'Data Two',
                    backgroundColor: '#021532',
                    borderColor: '#021532',
                    data: [14, 28, 32, 40, 5, 87, 62]
                }
            ]
        })
        const names = ref('')
        const options = {
            responsive: false,
            maintainAspectRatio: false
        }

        function getNewsCount() {
            axios.get('/api/newsCount', {
                params: {
                    names: names.value,
                }
            }).then((res) => {
                data.value = res.data
            })
        }

        getNewsCount()

        return {
            data,
            options,
            names,
            getNewsCount
        }
    }
}
</script>
