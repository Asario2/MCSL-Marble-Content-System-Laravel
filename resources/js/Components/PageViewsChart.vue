<template>
    <Layout>
        <MetaHeader title="Statistik" />
    <template #header>
      <breadcrumb :breadcrumbs="breadcrumbs" :current="'Private_Nachrichten'"></breadcrumb>
    </template>        <div class="w-full p-4">

            <canvas ref="canvas"></canvas>
        </div>
    </Layout>
</template>

<script>
import axios from "axios";
import { Chart, BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from "chart.js";
import Layout from "@/Application/Homepage/Shared/Layout.vue";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend);


export default {
  name: "PageViewsChart",

components:{
    Layout,
    MetaHeader,
},

  data() {
    return {
      chart: null,
    };
  },

  async mounted() {
    const res = await axios.get("/api/pageviews");
    const data = res.data;

    const labels = data.map(item => item.url);
    const values = data.map(item => item.views);

    this.draw(labels, values);
  },

  methods: {
    draw(labels, values) {
      const ctx = this.$refs.canvas.getContext("2d");

      this.chart = new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Seitenaufrufe",
              data: values,
              backgroundColor: "rgba(54, 162, 235, 0.5)",
              borderColor: "rgb(54, 162, 235)",
              borderWidth: 1,
            },
          ],
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: true }
          },
        },
      });
    },
  },
};
</script>
