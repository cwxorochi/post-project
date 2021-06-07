<template>
  <v-app>
    <v-container
        class="text-center"
        fill-height
        style="height: calc(100vh - 58px);"
    >
      <v-row align="center" class="text-center">
        <v-col>
          <h1 class="display-2 warning--text">Whoops, 404</h1>

          <p>The page you were looking for does not exist</p>

          <v-btn
              :to="'/'"
              color="grey darken-2"
              dark
              class="mt-4"
          >
            Get me out of here!
          </v-btn>
        </v-col>
      </v-row>
    </v-container>
  </v-app>
</template>

<script>
  export default {
    props: {
      lang: {
        type: String,
        default: 'en',
      },
    },
    created () {
      this.$ssrContext && this.$ssrContext.res.status(404)
    },
  }
</script>
