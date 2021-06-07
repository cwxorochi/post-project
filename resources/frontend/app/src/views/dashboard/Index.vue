<template>
  <div>
    <v-row>
      <v-col md="5" sm="12" lg="5">
        <v-form ref="post_form" v-model="isValidated">
          <v-text-field
              label="Title"
              outlined
              dense
              :rules="[(v) => !!v || 'Title is required']"
              v-model="formData.title"
              required
          />
          <v-textarea
              outlined
              placeholder="Write something here"
              clearable-icon
              label="Content"
              counter
              rows="3"
              :rules="[(v) => !!v || 'Content is required']"
              v-model="formData.body"
              required
          ></v-textarea>
        </v-form>

        <v-btn class="float-right" @click="storePost" :loading="isSubmitting">
          Post
        </v-btn>
      </v-col>
      <v-col md="7" sm="12" lg="7">
        <recent-comment-card></recent-comment-card>
      </v-col>
    </v-row>

    <v-card class="mt-4">
      <v-card-title class="py-0">
        <div class="text-h6 py-3">Posts</div>
        <v-divider class="mx-3" vertical></v-divider>
        <v-spacer></v-spacer>
        <v-btn :loading="isUpdating" color="primary" @click="getPost">
          <v-icon class="mr-2">mdi-refresh</v-icon>
          Refresh
        </v-btn>
      </v-card-title>
      <v-divider></v-divider>
      <v-card-text>
        <!-- Post Data loading -->
        <v-row v-if="isUpdating">
          <v-col cols="12" md="4">
            <v-skeleton-loader
                v-bind="skeletonAttrs"
                type="list-item, divider, list-item-three-line, actions"
            ></v-skeleton-loader>
            <v-skeleton-loader
                v-bind="skeletonAttrs"
                type="list-item, divider, list-item-three-line, list-item-three-line, actions"
            ></v-skeleton-loader>
          </v-col>

          <v-col cols="12" md="4">
            <v-skeleton-loader
                v-bind="skeletonAttrs"
                type="list-item, divider, list-item-three-line, list-item-three-line, actions"
            ></v-skeleton-loader>
            <v-skeleton-loader
                v-bind="skeletonAttrs"
                type="list-item, divider, list-item-three-line, actions"
            ></v-skeleton-loader>
          </v-col>

          <v-col cols="12" md="4">
            <v-skeleton-loader
                v-bind="skeletonAttrs"
                type="list-item, divider, list-item-three-line, actions"
            ></v-skeleton-loader>

            <v-skeleton-loader
                v-bind="skeletonAttrs"
                type="list-item, divider, list-item-three-line, list-item-three-line, actions"
            ></v-skeleton-loader>
          </v-col>
        </v-row>
        <!-- Post Data exists -->
        <v-row
            v-else-if="postData.length > 0"
            v-masonry
            transition-duration="0.3s"
            item-selector=".item"
        >
          <v-col
              md="6"
              lg="3"
              sm="12"
              class="item"
              v-masonry-tile
              v-for="(post, index) in postData"
              :key="index + '_post'"
          >
            <post-card :post="post"></post-card>
          </v-col>
        </v-row>
        <!-- Post Data not exists -->
        <v-row v-else>
          <v-col>
            <v-alert type="info"> Let's start with adding one Post.</v-alert>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <snack-bar ref="snack_bar" variant="red"></snack-bar>
  </div>
</template>

<script>
import SnackBar from '@/components/SnackBar'
import PostCard from '@/views/dashboard/parts/PostCard'
import RecentCommentCard from '@/views/dashboard/parts/RecentCommentCard'

export default {
  name: 'DashboardIndex',
  components: {
    SnackBar,
    PostCard,
    RecentCommentCard,
  },
  data: () => ({
    isValidated: false,
    isUpdating: true,
    isSubmitting: false,
    postData: [],
    formData: {
      title: null,
      body: null,
    },
    skeletonAttrs: {
      class: 'mb-6',
      elevation: 2,
    },
  }),
  mounted () {
    window.Echo.channel('main-channel').listen('NewCommentEvent', (res) => {
      console.log(res, res.comment)
      let index = this.postData.findIndex(
          (item) => item.id == res.comment.post_id
      )
      if (this.postData[index].comments.findIndex((item) => item.id === res.comment.id) === -1) {
        this.postData[index].comments.push(res.comment)
        this.postData[index].total_number_of_comments += 1
      }
    })

    window.Echo.channel('main-channel').listen('NewPostEvent', (res) => {
      if (!this.postData.filter((item) => item.id === res.post.id).length > 0) {
        this.postData.push(res.post)
        setTimeout(() => {
          this.$redrawVueMasonry()
        }, 100)
      }
    })

    this.getPost()
  },
  methods: {
    getPost () {
      this.isUpdating = true
      window.axios
          .get('/api/posts')
          .then((res) => {
            if (res.data.success) {
              this.postData = res.data.data
              setTimeout(() => {
                this.$redrawVueMasonry()
              }, 100)
            }
          })
          .finally(() => (this.isUpdating = false))
    },
    storePost () {
      this.$refs.post_form.validate()
      if (!this.isValidated) {
        return
      }
      this.isSubmitting = false
      window.axios
          .post('/api/posts', this.formData)
          .then((res) => {
            if (res.data.success) {
              if (
                  !this.postData.filter((item) => item.id === res.post.id).length >
                  0
              ) {
                this.postData.push(res.data.data.post)
              }
              setTimeout(() => {
                this.$redrawVueMasonry()
              }, 100)
              this.$refs.post_form.reset()
              this.$refs.snack_bar.toast('Post stored.', 'success')
            }
          })
          .catch(() => {
            this.$refs.snack_bar.toast(
                'Something is wrong, please try again later.',
                'red'
            )
          })
          .finally(() => (this.isSubmitting = false))
    },
  },
}
</script>

