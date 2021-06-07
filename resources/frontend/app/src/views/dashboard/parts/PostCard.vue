<template>
  <v-card>
    <v-card-text>
      <div class="d-flex justify-space-between align-center">
        <!-- Post title -->
        <div class="text-h6" v-if="!isEdit">
          {{ post.post_title }}
        </div>
        <v-text-field
          v-else
          v-model="postFormData.title"
          dense
          hide-details="auto"
          outlined
          class="mr-2"
          @change="isPostFormDirty = true"
        />

        <!-- Post action button -->
        <v-btn
          small
          v-if="!isEdit"
          color="primary"
          class="small-icon-button"
          @click="triggerEditMode"
        >
          <v-icon small>mdi-pencil</v-icon>
        </v-btn>
        <v-btn
          small
          v-else
          color="success"
          class="small-icon-button"
          @click="submitPostEditData"
          :loading="isPostSubmitting"
        >
          <v-icon small>mdi-check</v-icon>
        </v-btn>
      </div>

      <v-divider class="mt-2 mb-4"></v-divider>

      <!-- Post body -->
      <div class="text-body-1" v-if="!isEdit" v-html="formattedBody"></div>
      <v-textarea
        v-model="postFormData.body"
        v-else
        hide-details="auto"
        outlined
        @change="isPostFormDirty = true"
      ></v-textarea>

      <v-divider class="mt-6 mb-2"></v-divider>
      <div class="d-flex align-center">
        <div>Comments</div>
        <v-spacer></v-spacer>
        <v-chip
          color="primary"
          class="text-caption rounded-circle"
          dark
          outlined
        >
          {{ post.total_number_of_comments }}
        </v-chip>
      </div>

      <!-- comments -->
      <v-list-item
        v-for="comment in post.comments"
        :key="comment.id + '_comment'"
        class="px-0"
      >
        <v-list-item-avatar>
          <v-avatar class="text-uppercase" color="teal">{{
            comment.user.name.charAt(0)
          }}</v-avatar>
        </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title>
            <div class="d-flex">
              <div>
                {{ comment.user.name }}
              </div>
              <v-spacer></v-spacer>
              <div class="text-caption grey--text">
                {{ comment.formatted_created_at }}
              </div>
            </div>
          </v-list-item-title>
          <v-list-item-subtitle v-html="comment.body"></v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>

      <!-- Comment Field -->
      <div class="mt-3">
        <v-text-field
          v-model="formData.comment"
          outlined
          hide-details="auto"
          dense
          name="comment"
          autocomplete="new-password"
          @focus="isCommentInputActive = true"
        >
        </v-text-field>
        <v-expand-transition @after-enter="redrawMasonry">
          <v-card elevation="0" class="my-2" v-show="isCommentInputActive">
            <div class="d-flex justify-end">
              <v-btn text small class="mr-1" @click="cancelComment"
                >Cancel</v-btn
              >
              <v-btn
                color="primary"
                small
                @click="submitComment"
                :loading="isSubmitting"
                >Comment</v-btn
              >
            </div>
          </v-card>
        </v-expand-transition>
      </div>
    </v-card-text>

    <snack-bar ref="snack_bar"></snack-bar>
  </v-card>
</template>

<script>
import SnackBar from "@/components/SnackBar";
export default {
  name: "PostCard",
  components: {
    SnackBar,
  },
  props: {
    post: {
      type: Object,
      default: null,
    },
  },
  data() {
    return {
      isCommentInputActive: false,
      isSubmitting: false,
      isEdit: false,
      formData: {
        post_id: this.post.id,
        comment: null,
      },
      isPostFormDirty: false,
      isPostSubmitting: false,
      postFormData: {
        title: this.post.post_title,
        body: this.post.post_body,
      },
    };
  },
  computed: {
    formattedBody() {
      return this.post.post_body.replaceAll("\n", "<br/>");
    },
  },
  watch: {
    isCommentInputActive() {
      setTimeout(() => {
        this.$redrawVueMasonry();
      }, 100);
    },
  },
  methods: {
    redrawMasonry() {
      this.$redrawVueMasonry();
    },
    cancelComment() {
      this.formData.comment = null;
      this.isCommentInputActive = false;
    },
    submitComment() {
      this.isSubmitting = true;
      window.axios
        .post("/api/comments", this.formData)
        .then((res) => {
          if (res.data.success) {
            this.$refs.snack_bar.toast("Comment submitted.", "success");
            if (this.post.comments.findIndex((item) => item.id === res.data.data.comment.id) === -1) {
              this.post.comments.push(res.data.data.comment);
              this.post.total_number_of_comments += 1;
            }

            window.hub.$emit("recent-comment-reload");
            this.cancelComment();
            setTimeout(() => {
              this.$redrawVueMasonry();
            }, 100);
          }
        })
        .finally(() => (this.isSubmitting = false));
    },

    triggerEditMode() {
      this.isEdit = true;
      setTimeout(() => {
        this.$redrawVueMasonry();
      }, 100);
    },

    submitPostEditData() {
      if (this.isPostFormDirty) {
        this.isPostSubmitting = true;
        window.axios
          .put(`/api/posts/${this.post.id}`, this.postFormData)
          .then((res) => {
            if (res.data.success) {
              this.$refs.snack_bar.toast("Post edited.", "success");
              this.post.post_title = res.data.data.post.post_title;
              this.post.post_body = res.data.data.post.post_body;
              this.isEdit = false;
              this.isPostFormDirty = false;
              setTimeout(() => {
                this.$redrawVueMasonry();
              }, 100);
            }
          })
          .catch(() => {
            this.$refs.snack_bar.toast(
              "Something is wrong, please try again later.",
              "red"
            );
          })
          .finally(() => (this.isPostSubmitting = false));
      } else {
        this.isEdit = false;
        setTimeout(() => {
          this.$redrawVueMasonry();
        }, 100);
      }
    },
  },
};
</script>

<style scoped>
</style>
