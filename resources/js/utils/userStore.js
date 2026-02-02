// utils/userStore.js
import { reactive } from "vue";


export const userStore = reactive({
  user: {
    user_id: null,
    full_name: null,
    profile_photo_url: null,
    is_admin: false,
    mcsl_points: 0
  }
});

