<template>
  <Layout>
  <div>
    <h2 class="text-xl font-bold mb-4">Seitenaufrufe pro URL</h2>
    <canvas ref="canvas" style="max-height:500px;"></canvas>
  </div>
  </Layout>
</template>

<script>
import axios from "axios";
import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend,
  Title
} from "chart.js";
import Layout from "@/Application/Homepage/Shared/Layout.vue";
Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend, Title);

export default {

  name: "PageViewsChart",
  components: { Layout },
  data() {
    return {
      chart: null
    };
  },
  async mounted() {
    try {
      // 1️⃣ Daten vom Controller abrufen
      const res = await axios.get("/dboard/data");
      const payload = res.data;

      const labels = payload.labels || [];
      const datasets = payload.datasets || [];

      // 2️⃣ Chart rendern
      this.renderChart(labels, datasets);
    } catch (error) {
      console.error("Fehler beim Laden der Statistik:", error);
    }
  },
  methods: {
    renderChart(labels, datasets) {
      const ctx = this.$refs.canvas.getContext("2d");

      // Bestehenden Chart zerstören, falls vorhanden
      if (this.chart) this.chart.destroy();

      // Chart erstellen
      this.chart = new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: datasets
        },
        options: {
          responsive: true,
          plugins: {
            legend: { position: "top" },
            title: { display: true, text: "Seitenaufrufe pro URL nach DOM" }
          },
          scales: {
            x: { stacked: true },
            y: { stacked: true, beginAtZero: true, precision: 0 }
          }
        }
      });
    }
  },
  beforeUnmount() {
    if (this.chart) this.chart.destroy();
  }
};
</script>

<style scoped>
canvas {
  height: 400px;
  width: 100%;
}
</style>
