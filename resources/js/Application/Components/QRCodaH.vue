<template>
    <div class="p-4 max-w-md mx-auto">
      <!-- URL Input -->
      <label for="url" class="block mb-2 font-medium">URL eingeben:</label>
      <input
        id="url"
        type="text"
        v-model="url"
        placeholder="https://example.com"
        class="w-full border p-2 rounded mb-4"
      />

      <!-- Button zum Generieren -->
      <button
        @click="generateQRCode(url)"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 mb-4"
      >
        QR-Code generieren
      </button>

      <!-- QR Code Canvas -->
      <canvas v-if="qrGenerated" ref="qrCanvas"></canvas>

      <div v-else class="text-gray-500">
        Gib eine URL ein und klicke auf "QR-Code generieren"
      </div>
    </div>
  </template>

  <script>
  import QRCode from "qrcode";

  export default {
    name: "QrCodeGenerator",
    data() {
      return {
        url: "",
        qrGenerated: false,
      };
    },
    methods: {
      async generateQRCode(value) {
        if (!value) {
          this.qrGenerated = false;
          const canvas = this.$refs.qrCanvas;
          if (canvas) {
            const ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);
          }
          return;
        }

        try {
          const canvas = this.$refs.qrCanvas;
          await QRCode.toCanvas(canvas, value, {
            width: 350,
            margin: 2,
            color: {
              dark: "#000000",
              light: "#ffffff",
            },
          });
          this.qrGenerated = true;
        } catch (err) {
          console.error("Fehler beim Generieren des QR-Codes:", err);
          this.qrGenerated = false;
        }
      },
    },
  };
  </script>

  <style scoped>
  canvas {
    display: block;
    margin: 0 auto;
  }
  </style>

