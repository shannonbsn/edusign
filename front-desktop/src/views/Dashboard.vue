<template>
  <div class="dashboard-container">
    <h2>📚 QR Code dynamique pour le cours</h2>

    <div v-if="currentQrCode && qrCodeExpiresIn > 0" class="qrcode-container">
      <h3>QR Code pour {{ selectedCourse?.title || 'le cours en cours' }}</h3>
      <qrcode-vue :value="currentQrCode" :size="150"></qrcode-vue>
      <p>Ce QR Code expirera dans {{ qrCodeExpiresIn }} secondes ⏳</p>
    </div>

    <p v-if="qrCodeExpiresIn === 0" class="expired-message">❌ Ce QR Code a expiré</p>

    <button @click="fetchNewQrCode">🔄 Générer un nouveau QR Code</button>
  </div>
</template>

<script>
import axios from 'axios';
import QrcodeVue from 'qrcode.vue';

export default {
  components: { QrcodeVue },
  data() {
    return {
      selectedCourse: null, 
      currentQrCode: "", 
      qrCodeExpiresIn: 10 // Temps avant expiration du QR Code
    };
  },
  async mounted() {
    this.fetchNewQrCode();
    setInterval(() => {
      if (this.qrCodeExpiresIn > 0) this.qrCodeExpiresIn--;
    }, 1000); // Décompte toutes les secondes
    setInterval(this.fetchNewQrCode, 10000); // Mise à jour toutes les 10s
  },
  methods: {
    async fetchNewQrCode() {
      try {
        const response = await axios.get('http://localhost:8000/api/generate-temporary-token/1', {
          headers: { Authorization: `Bearer ${localStorage.getItem('authToken')}` }
        });
        this.currentQrCode = response.data.token;
        this.qrCodeExpiresIn = 10; // Reset du compte à rebours
      } catch (error) {
        console.error("Erreur de récupération du QR Code", error);
      }
    }
  }
};
</script>

<style>
.qrcode-container {
  text-align: center;
}
.expired-message {
  color: red;
  font-weight: bold;
}
</style>
