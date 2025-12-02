<template>
  <Layout>
    <template #header>
      <breadcrumb :breadcrumbs="breadcrumbs" :current="'Zugriff Statistik'"></breadcrumb>
    </template>

    <div class="flex justify-between items-center mb-4">

  <!-- Linker Bereich -->
  <div class="flex items-center">
    <h2 class="text-xl font-bold">Seitenaufrufe pro URL</h2>
  </div>

  <!-- Rechter Bereich -->
  <div class="flex items-center space-x-4">

    <!-- Zeitraum -->
    <div class="flex items-center space-x-2">
      <span>Zeitraum</span>
      <select
        v-model="month"
        @change="loadData"
        class="p-2.5 text-sm rounded-lg border border-layout-sun-300
               text-layout-sun-900 bg-layout-sun-50
               focus:ring-primary-sun-500 focus:border-primary-sun-500
               dark:border-layout-night-300 dark:text-layout-night-900
               dark:bg-layout-night-50 dark:placeholder-layout-night-400
               dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
      >
        <option value="1">1 Monat</option>
        <option value="2">2 Monate &nbsp;&nbsp;&nbsp;&nbsp;</option>
        <option value="3">3 Monate</option>
        <option value="4">4 Monate</option>
        <option value="5">5 Monate</option>
      </select>
    </div>

    <!-- Domain -->
    <div class="flex items-center space-x-2" v-if="modulRights?.StatisticsAll">
      <span>Domain(s)</span>
      <select
        v-model="dom"
        @change="loadData"
        class="p-2.5 text-sm rounded-lg border border-layout-sun-300
               text-layout-sun-900 bg-layout-sun-50
               focus:ring-primary-sun-500 focus:border-primary-sun-500
               dark:border-layout-night-300 dark:text-layout-night-900
               dark:bg-layout-night-50 dark:placeholder-layout-night-400
               dark:focus:ring-primary-night-500 dark:focus:border-primary-night-500"
      >
        <option value="">Alle Domains</option>
        <option value="ab">Asarios Blog</option>
        <option value="dag">Monika Dargies&nbsp;&nbsp;</option>
        <option value="mfx">MarbleFX</option>
      </select>
    </div>

  </div>

</div>

    <canvas ref="canvas" style="max-height:500px;"></canvas>

  </Layout>
</template>

<script>
import axios from "axios";
import { loadRights } from '@/helpers';
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
import Layout from "@/Application/Admin/Shared/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import { SD } from "@/helpers";

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend, Title);

export default {
  name: "PageViewsChart",
  components: { Layout, Breadcrumb },

  data() {
    return {
      chart: null,
      dom: "",     // Domain-Auswahl
      month: '1',
      modulRights: null,
    };
  },

  async mounted() {
    this.loadData();
    this.modulRights = await loadRights();
  },

  methods: {
    SD,

    async loadData() {
      try {
        const res = await axios.get("/dboard/data", {
          params: { dom: this.dom, month:this.month }
        });

        const payload = res.data;

        this.renderChart(payload.labels || [], payload.datasets || []);
      } catch (error) {
        console.error("Fehler beim Laden der Statistik:", error);
      }
    },
    async loadMonth() {
      return this.month;
    },

    renderChart(labels, datasets) {
      const ctx = this.$refs.canvas.getContext("2d");

      if (this.chart) this.chart.destroy();

      this.chart = new Chart(ctx, {
        type: "bar",
        data: { labels, datasets },
        options: {
          responsive: true,
          plugins: {
            legend: { position: "top" },
            title: { display: true, text: "Seitenaufrufe pro URL nach Domain" }
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
