<template>
  <v-card>
    <v-card-title class="py-0">
      <div class="text-h6 py-3">Recent Comments</div>
      <v-divider class="mx-3" vertical></v-divider>
      <v-text-field
          prepend-inner-icon="search"
          v-model="searchInput"
          @input="debounceSearch"
          dense
          hide-details="auto"
      >
        <template #append>
          <v-btn
              v-if="isSearching"
              disabled
              loading
              small
              text
              class="small-icon-button"
          ></v-btn>
        </template>
      </v-text-field>
    </v-card-title>
    <v-divider></v-divider>
    <v-card-text class="recent-comment-container">
      <v-overlay :value="isSearching" absolute>
        <v-progress-circular indeterminate size="64"></v-progress-circular>
      </v-overlay>
      <v-row>
        <v-col
            v-for="item in commentData"
            :key="item.id + '_recent_comment'"
            md="6"
            sm="12"
        >
          <v-card>
            <v-list-item>
              <v-list-item-content>
                <v-list-item-title>
                  <div>Post: {{ item.post.post_title }}</div>
                  <v-divider class="my-2"></v-divider>
                </v-list-item-title>
                <v-list-item-subtitle>
                  <div class="d-flex">
                    <div class="font-weight-bold">
                      {{ item.user.name }}
                    </div>
                    <v-spacer></v-spacer>
                    <div class="text-caption grey--text">
                      {{ item.formatted_created_at }}
                    </div>
                  </div>

                  <div class="mt-1">
                    <v-icon small> mdi-comment-outline </v-icon>
                    {{ item.body }}
                  </div>
                </v-list-item-subtitle>
              </v-list-item-content>
            </v-list-item>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  name: "RecentCommentCard",
  data() {
    return {
      isSearching: false,
      searchInput: null,
      debounce: null,
      commentData: [],
    };
  },
  mounted() {
    window.Echo.channel("main-channel").listen("NewCommentEvent", () => {
      this.getRecentComments();
    });

    window.hub.$on("recent-comment-reload", () => {
      this.getRecentComments();
    });
    this.getRecentComments();
  },
  methods: {
    debounceSearch() {
      this.isSearching = true;
      clearTimeout(this.debounce);
      this.debounce = setTimeout(() => {
        this.getRecentComments();
      }, 600);
    },
    getRecentComments() {
      this.isSearching = true;
      window.axios
          .get("/api/comments", {
            params: { pattern: this.searchInput, limiter: 10 },
          })
          .then((res) => {
            if (res.data.success) {
              this.commentData = res.data.data;
            }
          })
          .finally(() => (this.isSearching = false));
    },
  },
};
</script>

<style lang="scss" scoped>
.recent-comment-container {
  position: relative;
  max-height: 20vh;
  overflow: auto;
}
</style>
