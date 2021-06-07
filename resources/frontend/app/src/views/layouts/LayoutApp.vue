<template>
  <v-app style="">
    <!-- Top App Bar -->
    <v-app-bar app clipped-left color="blue" dense>
      <div class="text-h6 white--text">Posting Console</div>
    </v-app-bar>
    <!--/end Top App Bar -->

    <!-- Left bar -->
    <v-navigation-drawer app clipped permanent color="#fafafa">
      <v-list flat>
        <!-- menu link -->
        <v-list-item
          link
          :to="{ name: 'dashboard.index' }"
          exact
          class="nav-theme-menu"
          color="primary"
          active-class="nav-theme-active"
        >
          <v-list-item-icon>
            <v-icon>dashboard</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title class="nav-theme-title">
              Dashboard
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
    <!--/end Left bar -->

    <v-main class="main-app-bg">
      <!-- Provides the application the proper gutter -->
      <v-container fluid class="pa-6">
        <router-view></router-view>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  data() {
    return {
      menuIndex: 1,
      isLoading: false,
    };
  },
  methods: {},
};
</script>

<style scoped>
</style>
