<template>
  <div class="rating-container border p-4 rounded-lg">
    <div class="stars flex gap-1">
      <span
        v-for="star in 5"
        :key="star"
        class="star"
        :class="{ filled: star <= rating, hovered: star <= hoverRating }"
        @mouseover="hoverRating = star"
        @mouseleave="hoverRating = 0"
        @click="setRating(star)"
      >
        <IconStar wi="24" he="24" />
      </span>
    </div>
  <p v-if="rating > 0 && !nopoints" class="mt-2 text-sm">
      Du hast {{ rating }} Sterne bewertet
    </p>
    <!-- 🔑 Child mit v-model -->
    <NoLogin
      v-if="!AID"
      v-model="form"
      :bothnicks="true"

    />


  </div>
</template>

<script>
import axios from "axios";
import IconStar from "@/Application/Components/Icons/IconStar.vue";
import NoLogin from "@/Application/Components/Social/NoLogin.vue";
import { CleanTable } from "@/helpers";
import { toastBus } from "@/utils/toastBus";
import { userStore } from "@/utils/userStore";
import { ratingBus } from "@/utils/ratingBus";

export default {
  name: "RatingInput",

  components: {
    IconStar,
    NoLogin,
  },

  props: {
    postId: {
      type: Number,
      required: true,
    },
    table: {
      type: String,
      required: true,
    },
  },

  data() {
    return {
      rating: 0,
      hoverRating: 0,
      nopoints:false,

      // 🔐 eingeloggter User?
      AID: true,

      // 🧾 Formular (kommt vom Child)
      form: {
        name: null,
        email: null,
        password: null,
      },

      emailValid: true,
    };
  },

  mounted() {
    // globaler Auth-Status
    this.AID = Boolean(window.authid);

//   console.log("Mounted RatingInput");
//   console.log("Auth:", this.AID);


  },

  methods: {

async setRating(star) {
// console.log("⭐ Stern geklickt:", star);
    if (!this.AID) {
    if (!this.form.email) {
      window.toastBus.emit({
        status: "error",
        message: "Bitte gib eine Email-Adresse/ einen Nick ein.",
        duration: 30000,
      });
      return;
    }

//     if (!this.validateEmail(this.form.email)) {

// // window.toastBus.emit({ message: "'.$output.'", type: "'.$status.'", duration: '.$seconds.' })';
//         window.toastBus.emit({
//         status: "error",
//         message: "Bitte gib eine gültige Email-Adresse ein.",
//         duration: 30000,
//       });
//       return;
//     }
  }

  this.rating = star;
  await this.saveRating(star);
},


    validateEmail(email) {
      if (!email) return false;
      return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },
    async ensureLogin() {
    if (this.logged) return true;

    try {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const { data } = await axios.post('/login-silent', {
        email: this.form.email,
        password: this.form.password,
        }, {
        withCredentials: true,
        headers: { 'X-CSRF-TOKEN': token },
        });

        // 🔥 Reaktive Updates
        if(data?.user_id && data?.user_id != "7" && !window.authid)
        {
        userStore.user.user_id = data.user_id;
        userStore.user.full_name = data.full_name;
        userStore.user.profile_photo_url = data.profile_photo_url;
        userStore.user.is_admin = data.is_admin;
        userStore.user.mcsl_points = await this.loadmcslpoints();

        this.logged = true;
        this.AID = true;
        window.toastBus.emit({type:"success",message:"Du wurdest erfolgreich eingeloggt"});
        }
        if(userStore.user.user_id == "7"){
            this.AID = false;
            return false;
        }
        return true;
    } catch (e) {
        console.error("Silent login failed", e);
        return false;
    }
    },
async loadmcslpoints() {
        try {
            const { data } = await axios.get('/api/mcslpoints/');
            this.mcslpoints = Number(data); // aktuelle Punkte
            return this.mcslpoints+1;
        } catch (err) {
            console.error('Fehler beim Laden der MCSL Points:', err);
        }
    },
    async saveRating(star) {

      const loggedIn = await this.ensureLogin();
     if (!loggedIn) return;
      try {

        const res = await axios.post("/save-rating", {
            rating: star,
            postId: this.postId,
            table: CleanTable(),
            password: this.form?.password,
            email: this.form.email,
        });

        // 🔁 andere Komponenten informieren
        ratingBus.emit("rating-updated", {
          postId: this.postId,
          email: this.form.email,
        });

        // 🔥 Parent-Event
        this.$emit("rated", {
          average: res.data.average,
          total: res.data.total,
        });
//         console.log(res);
        // ✅ Erfolg
        window.toastBus.emit({
          status: res.data.status,
          message: res.data.message,
          duration: res.data.duration,
        });

        if(res.data.status == "error"){
            this.AID = false;
            this.nopoints = true;
        }
        else{
            this.AID = true;
        }
      } catch (e) {
        if (e.response?.status === 401) {
          return;
        }

        console.error("Rating speichern fehlgeschlagen", e);

        window.toastBus.emit({
          status: "error",
          message: "Bewertung konnte nicht gespeichert werden",
        });
      }
    },
  },
};
</script>

<style scoped>
.star {
  cursor: pointer;
}
.star.filled {
  color: yellow;
}
.star.hovered {
  color: orange;
}
</style>
