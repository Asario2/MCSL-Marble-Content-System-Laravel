<template>
  <MetaHeader title="Statistiken" />
  <Layout>
  <div class="w-full">
    <canvas ref="canvas"></canvas>
  </div>
  </Layout>
</template>

<script>
import { Chart, BarController, BarElement, CategoryScale, LinearScale, Title, Tooltip, Legend } from 'chart.js';
import Layout from "@/Application/Homepage/Shared/Layout.vue";
import MetaHeader from '@/Application/Homepage/Shared/MetaHeader.vue';
Chart.register(BarController, BarElement, CategoryScale, LinearScale, Title, Tooltip, Legend);

export default {
  name: "ChartBar",

  components:{
    Layout,
    MetaHeader,
  },

  props: {
    labels: {
      type: Array,
      required: true
    },
    dataset: {
      type: Array,
      required: true
    },
    title: {
      type: String,
      default: ""
    }
  },

  mounted() {
    this.renderChart();
  },

  methods: {
    renderChart() {
      const context = this.$refs.canvas.getContext("2d");

      new Chart(context, {
        type: 'bar',
        data: {
          labels: this.labels,
          datasets: [
            {
              label: this.title,
              data: this.dataset,
              backgroundColor: "rgba(54, 162, 235, 0.5)",
              borderColor: "rgb(54, 162, 235)",
              borderWidth: 1
            }
          ]
        },
        options: {
          responsive: true,
          plugins: {
            title: {
              display: this.title !== "",
              text: this.title
            }
          }
        }
      });
    }
  }
};
</script>

<style scoped>
canvas {
  max-width: 100%;
}
</style>
<!--  -->
