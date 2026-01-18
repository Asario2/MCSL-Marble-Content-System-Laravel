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
      <div v-html="text"></div>
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
 </div>
</layout>
</template>

<script>

import MetaHeader from "@/Application/Homepage/Shared/MetaHeader.vue";
import Layout from "@/Application/Homepage/Shared/ab/Layout.vue";
import Breadcrumb from "@/Application/Components/Content/Breadcrumb.vue";
import { nl2br } from "@/helpers";


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
  MCSL_GLOB_PTS: {
    type: Number,
    default: 0
},
  nick:String,
  MCS_POINTS_TOTAL:Number,
  breadcrumbs:String,
  },

  data() {
    return {

    };
  },
  mounted(){
    console.log(this.text);
  },
  method:{
    nl2br,
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
