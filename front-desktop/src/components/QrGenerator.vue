<template>
  <div class="qr-container">
    <qrcode-vue :value="qrValue" :size="250" level="M" />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import QrcodeVue from 'qrcode.vue'

const qrValue = ref('')
const coursId = 1 // 🟡 à adapter selon le cours affiché
let intervalId = null

// Fonction qui appelle l'API Laravel pour récupérer un token temporaire
async function fetchToken() {
  try {
    const response = await fetch(`http://localhost:8000/api/generate-temporary-token/${coursId}`)
    const data = await response.json()
    qrValue.value = data.token // ou `scan-presence/${data.token}` si tu préfères encoder l'URL directement
  } catch (error) {
    console.error('Erreur génération QR code :', error)
  }
}

onMounted(() => {
  fetchToken()
  intervalId = setInterval(fetchToken, 5000)
})

onUnmounted(() => {
  clearInterval(intervalId)
})
</script>

<style scoped>
.qr-container {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 20px;
}
</style>