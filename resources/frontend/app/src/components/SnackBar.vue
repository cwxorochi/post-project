<template>
  <v-snackbar
      dark
      v-model="snackbar"
  >
    {{ text }}

    <template v-slot:action="{ attrs }">
      <v-btn
          :color="variant"
          text
          v-bind="attrs"
          @click="snackbar = false"
      >
        Close
      </v-btn>
    </template>
  </v-snackbar>
</template>

<script>
export default {
  name: "SnackBar",
  data: () => ({
    snackbar: false,
    text: null,
    variant: 'red',
  }),
  methods: {
    toast (text, variant) {
      this.text = text
      this.variant = variant
      this.snackbar = true
    }
  }
}
</script>

<style scoped>

</style>
