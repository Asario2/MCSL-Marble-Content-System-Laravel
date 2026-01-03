<template>
<layout>
  <meta-header title="MCSL Points" />
<template #header>
      <Breadcrumb :breadcrumbs="breadcrumbs" />
    </template>
    <div class="block max-w-sm mx-auto sm:max-w-full p-4 bg-layout-sun-100 dark:bg-layout-night-100">

    <h1>MCSL Points</h1>

  <div class="bg-layout-sun-100 dark:bg-layout-night-50 lg:rounded-lg p-2 mb-6">
    <div v-if="text" class="text-layout-sun-1000 dark:text-layout-night-1000">
      <div v-html="text.text"></div>
    </div>
    <div v-else>
      <p class="text-gray-500 italic">Kein Willkommenstext vorhanden.</p>
    </div>
  </div>
    <h3>Übersicht</h3>
    <table class="w-half">
    <tbody>
        <tr>
            <td>
                Punkte
                <br>
            </td>
        </tr>

        <tr>
            <td><b>Aktion</b></td>
            <td><b>Punkte</b></td>
        </tr>

        <tr>
            <td>Bild bewerten</td>
            <td>1 Punkt</td>
        </tr>

        <tr>
            <td>Kommentar schreiben</td>
            <td>3 Punkte</td>
        </tr>

        <tr>
            <td>Shortpoem Vorschlagen</td>
            <td>5 Punkte</td>
        </tr>

        <tr>
            <td>Newsletter lesen</td>
            <td>8 Punkte</td>
        </tr>

        <tr>
            <td>
                <br><br>
                Prämien
                <br>
            </td>
        </tr>

        <tr>
            <td><b>Punkte</b></td>
            <td><b>Prämie</b></td>
        </tr>

        <tr>
            <td>ab 45 Punkte</td>
            <td>1 Zentangle Bild</td>
        </tr>

        <tr>
            <td>ab 51 Punkte</td>
            <td>1 Fineliner Bild</td>
        </tr>

        <tr>
            <td>ab 66 Punkte</td>
            <td>1 Fasermaler Bild</td>
        </tr>

        <tr>
            <td>ab 60 Punkte</td>
            <td>1 Ausgemaltes Bild</td>
        </tr>

        <tr>
            <td>ab 60 Punkte</td>
            <td>1 Keramik</td>
        </tr>
        <tr>
            <td>ab 75 Punkte</td>
            <td>1 3D Druck</td>
        </tr>

        <tr id='stats'>
            <td>ab 240 Punkte</td>
            <td>1 Acryl Bild</td>
        </tr>
    </tbody>
</table>
  <h3 class='ora'>Du hast aktuell {{MCSL_GLOB_PTS}} Punkte</h3>
  <div v-if="MCSL_GLOB_PTS > 45"><br /><a class="button-primary" href='/get_MCSL_Points_Preniums'>Jetzt Prämie Einlösen</a></div>
  <div v-else-if="!AID">Bitte <a href="/login">einloggen</a> oder <a href='/register'>Registrieren</a></div>
  <div v-else>Du hast leider noch nicht genug Punkte gesammelt um eine Prämie einzulösen</div>
  <h2>Statistiken für <span class='ncol'>{{ nick }}</span></h2>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- MCS Points -->
    <div class="bg-layout-sun-100 dark:bg-layout-night-100 border border-layout-sun-300 dark:border-layout-night-300 p-4 rounded-xl shadow-sm">
      <h3 class="text-lg font-semibold mb-3">MCSL    Points ({{ count_mcs }})</h3>
      <div class="relative h-64">
        <canvas ref="pointsChart" v-if="Object.values(mcslpoints).some(v => v > 0)"></canvas>
        <i v-else>Keine MCSL Points gefunden</i>
      </div>
    </div>

    <!-- Kommentare -->
    <div class="bg-layout-sun-100 dark:bg-layout-night-100 border border-layout-sun-300 dark:border-layout-night-300 p-4 rounded-xl shadow-sm">
      <h3 class="text-lg font-semibold mb-3">Kommentare ({{ count_com }})</h3>
      <div class="relative h-64">
        <canvas ref="commentsChart" v-if="Object.values(commentsStats).some(v => v > 0)"></canvas>
        <i v-else>Keine Kommentare gefunden</i>
      </div>
    </div>

    <!-- Bewertungen -->
    <div class="bg-layout-sun-100 dark:bg-layout-night-100 border border-layout-sun-300 dark:border-layout-night-300 p-4 rounded-xl shadow-sm" >
      <h3 class="text-lg font-semibold mb-3">Bewertungen nach Typ</h3>
      <div class="relative h-64" >
        <canvas ref="ratingsChart" v-if="ratingsStats.labels?.length > 0"></canvas>
        <i v-else>Keine Bewertungen gefunden</i>
      </div>
    </div>
    </div>
    <br />
    <div class="bg-layout-sun-100 dark:bg-layout-night-100
            border border-layout-sun-300 dark:border-layout-night-300
            p-4 rounded-xl shadow-sm">
    <h3 class="text-lg font-semibold mb-3">
        User-Aktivität (gestapelt) | {{ MCS_POINTS_TOTAL }} Punkte wurden insgesamt gesammelt
    </h3>
    <div class="relative h-80">
        <canvas ref="usersStackedChart"></canvas>
    </div>
    </div>
<!-- <pre>{{ ratingsStats }}</pre> -->
</div>
</layout>
</template>

<script>
import { Chart,  CategoryScale, ArcElement, Tooltip, Legend, PieController, LinearScale,    BarController,  DoughnutController,BarElement } from "chart.js";
import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Layout from "@/Application/Homepage/Shared/ab/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import { CleanId } from "@/helpers"
Chart.register(ArcElement, Tooltip, Legend,  CategoryScale, LinearScale, PieController, DoughnutController, BarController,  BarElement);

export default {
  name: "McsStatsCharts",
  components: { Layout, MetaHeader,Breadcrumb, },

  props: {
    mcslpoints: { type: Object, required: true },
    commentsStats: { type: Object, required: true },
    ratingsStats: { type: Object, required: true },
    activity: { type: Object, required: true },
    text: [Array, Object],
    count_mcs: Number,
    count_com: Number,
     stackedUserStats: {
    type: Object,
    required: true,
  },
  MCSL_GLOB_PTS:String,
  nick:String,
  MCS_POINTS_TOTAL:Number,
  breadcrumbs:String,
  },

  data() {
    return {
      pointsChartInstance: null,
      commentsChartInstance: null,
      ratingsChartInstance: null,
      COLORS_13: [
        "#3b82f6", "#10b981", "#ffc900", "#ef4444",
        "#8b5cf6", "#22c55e", "#eab308", "#ec4899",
        "#14b8a6", "#f97316", "#0ea5e9", "#a855f7", "#64748b"
      ],
    };
  },
watch: {
  stackedUserStats: {
    immediate: true,
    async handler(val) {
      if (!val || !val.labels || !val.labels.length) return;

      await this.$nextTick();

      if (!this.$refs.usersStackedChart) return;

      this.renderUsersStackedChart();
    },
  },
  text: {
    deep: true,
    immediate: true,
    handler() {
      if (!window.location.hash) return;

      requestAnimationFrame(() => {
        this.scrollToHashAnchor();
      });
    },
  },
},
computed:{
    AID()
    {
        return window.authid;
    }

},
  mounted() {
    this.renderPointsChart();
    this.renderCommentsChart();
    this.renderRatingsChart();
    // this.renderUsersStackedChart();

    this.darkObserver = new MutationObserver(() => {
      if (this.$refs.pointsChart) this.renderPointsChart();
      if (this.$refs.commentsChart) this.renderCommentsChart();
      if (this.$refs.ratingsChart) this.renderRatingsChart();
    });

    this.darkObserver.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ["class"],
    });
  },

  beforeUnmount() {
    if (this.pointsChartInstance) this.pointsChartInstance.destroy();
    if (this.commentsChartInstance) this.commentsChartInstance.destroy();
    if (this.ratingsChartInstance) this.ratingsChartInstance.destroy();
    if (this.darkObserver) this.darkObserver.disconnect();
  },

  methods: {
    CleanId,
      getHashElement() {
        const hash = window.location.hash;
        console.log('DEBUG: window.location.hash =', hash);

        if (!hash) return null;

        // erlaubt: #st123 ODER #123
        const raw = hash.replace('#', '');
        const el = document.getElementById(raw) || document.getElementById(`st${raw}`);
        console.log('DEBUG: target element =', el);
        return el;
    },

        scrollToHashAnchor() {
    const el = this.getHashElement();
    if (!el) return;

    console.log('DEBUG: scrolling to element', el);

    const scroll = () => {
        const y = el.getBoundingClientRect().top + window.pageYOffset - 124;
        window.scrollTo({ top: y, behavior: 'smooth' });
        console.log('DEBUG: scrolling to y =', y);
    };

    // Prüfe, ob Bilder noch laden
    const imgs = el.closest('#gallery')?.querySelectorAll('img');
    if (!imgs || imgs.length === 0) {
        scroll();
        return;
    }

    let loaded = 0;
    imgs.forEach((img) => {
        if (img.complete) loaded++;
        else img.addEventListener('load', () => {
        loaded++;
        if (loaded === imgs.length) scroll();
        });
    });

    // Falls alle Bilder schon geladen
    if (loaded === imgs.length) scroll();
    },
    isDark() {

        return document.querySelector('main')?.classList.contains('dark');
    },
    renderUsersStackedChart() {
    if (this.usersStackedChartInstance) {
        this.usersStackedChartInstance.destroy();
    }

    this.usersStackedChartInstance = new Chart(this.$refs.usersStackedChart, {
        type: "bar",
        data: {
        labels: this.stackedUserStats.labels,
        datasets: [
            {
            label: "Kommentare",
            data: this.stackedUserStats.datasets[0].data,
            backgroundColor: "#3b82f6",
            stack: "stack1",
            },
            {
            label: "Ratings",
            data: this.stackedUserStats.datasets[1].data,
            backgroundColor: "#10b981",
            stack: "stack1",
            },
            {
            label: "Shortpoems",
            data: this.stackedUserStats.datasets[2].data,
            backgroundColor: "#ffc900",
            stack: "stack1",
            },
            {
            label: "Newsletter",
            data: this.stackedUserStats.datasets[3].data,
            backgroundColor: "#ef4444",
            stack: "stack1",
            },
        ],
        },
        options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
            stacked: true,
            ticks: { color: this.getChartColors().text },
            },
            y: {
            stacked: true,
            ticks: { color: this.getChartColors().text },
            },
        },
        plugins: {
            legend: {
            position: "top",
            labels: {
                color: this.getChartColors().text,
            },
            },
        },
        },
    });
    },

    getChartColors() {
      const dark = this.isDark();
      return {
        text: dark ? "#ddd" : "#000",
        tooltipBg: dark ? "#ccc" : "#eee",
        tooltipText: dark ? "#111827" : "#222",
      };
    },

    baseOptions() {
      const c = this.getChartColors();
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: "right",
            labels: {
              color: c.text,
              boxWidth: 16,
              boxHeight: 16,
              padding: 3,
              usePointStyle: false, // kein weißer

            },
          },
          tooltip: {
            backgroundColor: c.tooltipBg,
            titleColor: c.tooltipText,
            bodyColor: c.tooltipText,
          },
        },
      };
    },

    renderPointsChart() {
      if (this.pointsChartInstance) this.pointsChartInstance.destroy();

      this.pointsChartInstance = new Chart(this.$refs.pointsChart, {
        type: "doughnut",
        data: {
          labels: ["Bilderbewertungen", "Kommentare", "Newsletter", "Shortpoems"],
          datasets: [{
            data: [
            this.mcslpoints?.ratings,
            this.mcslpoints?.comments,

              this.mcslpoints?.newsletter,

              this.mcslpoints?.shortpoems
            ],
            backgroundColor: this.COLORS_13.slice(0, 4),
          }],
        },
        options: this.baseOptions(),
      });
    },

    renderCommentsChart() {
      if (this.commentsChartInstance) this.commentsChartInstance.destroy();

      this.commentsChartInstance = new Chart(this.$refs.commentsChart, {
        type: "doughnut",
        data: {
          labels: ["Blog", "Did you know", "Images", "Shortpoems"],
          datasets: [{
            data: [
              this.commentsStats?.blogs,
              this.commentsStats?.didyouknow,
              this.commentsStats?.images,
              this.commentsStats?.shortpoems
            ],
            backgroundColor: this.COLORS_13.slice(0, 4),
          }],
        },
        options: { ...this.baseOptions(), cutout: "55%" },
      });
    },

    renderRatingsChart() {
      if (!this.$refs.ratingsChart) return;
      if (this.ratingsChartInstance) this.ratingsChartInstance.destroy();

      this.ratingsChartInstance = new Chart(this.$refs.ratingsChart, {
        type: "doughnut",
        data: {
          labels: this.ratingsStats.labels,
          datasets: [{
            data: this.ratingsStats.values,
            backgroundColor: this.COLORS_13.slice(0, this.ratingsStats.values.length),
          }],
        },
        options: { ...this.baseOptions(), cutout: "55%" },
      });
    },
  },
};
</script>
<style>
.button-primary{
        background-color:#222;
        padding:5px 7px !important;
        border-radius:6px;
        border:2px solid #ffc600;
        line-height:20px;
        font-size:20px;
        color:#ffc600;
        font-family:Tahoma;
        margin:3px;
        text-decoration:none;
    }.button-primary:hover,a.button-primary:visited:hover{
    background-color:#ffc600 !important;
    color:#222 !important;
    border:3px solid #ffc600 !important;
    padding:5px 7px !important;
    }
    .ora{
        color:#ffc600;
        font-weight:bold;
}
.w-half{
    width:600px;
    max-width:100%;
}
.ncol{
    color:#3b82f6;
}
</style>
