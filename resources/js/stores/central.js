import { defineStore } from "pinia";
import api from "@/services/api";

const defaultUser = {
  name: "",
  email: "",
};

export const useCentralStore = defineStore("central", {
  state: () => ({
    user: JSON.parse(localStorage.getItem("user")) || defaultUser,
    token: localStorage.getItem("token") || "",
  }),
  getters: {
    getUser: (state) => state.user,
    getToken: (state) => state.token,
    isLoggedIn: (state) => !!state.token,
  },
  actions: {
    async login(email, password) {
      try {
        const { data } = await api.post(`/login`, {
          email,
          password,
        });

        Toast.fire({
          icon: "success",
          title: data.message,
        });

        console.log(data);

        // update pinia state
        this.user = data.data.user;
        this.token = data.data.token;

        // store user details and jwt in local storage to keep user logged in between page refreshes
        localStorage.setItem("token", this.token);
        localStorage.setItem("user", JSON.stringify(this.user));

        return 200;
      } catch (error) {
        Toast.fire({
          icon: "error",
          title: error.response.data.message,
        });
        return 404;
      }
    },
    logout() {
      api.get("/logout");

      Object.assign(this.user, defaultUser);
      localStorage.clear();
      this.token = "";
    },
  },
});
