import { defineStore } from "pinia";
import api from "@/services/api";

const defaultUser = {
  first_name: "",
  is_super_admin: false,
};

export const useUserStore = defineStore("userStore", {
  id: "auth",
  state: () => ({
    user: JSON.parse(localStorage.getItem("user")) || defaultUser,
    permissions: localStorage.getItem("permissions") || [],
    token: localStorage.getItem("token") || "",
  }),
  getters: {
    getUser: (state) => state.user,
    getToken: (state) => state.token,
    isLoggedIn: (state) => !!state.token,
    getPermissions: (state) => state.permissions,
    isSuperAdmin: (state) => state.user.is_super_admin,
    userCan: (state) => (permission) => {
      if (state.user.is_super_admin) {
        return true;
      }
      return state.permissions.includes(permission);
    },
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

        // update pinia state
        this.user = data.data.user;
        this.permissions = data.data.permissions;
        this.token = data.data.token;

        // store user details and jwt in local storage to keep user logged in between page refreshes
        localStorage.setItem(
          "permissions",
          JSON.stringify(data.data.permissions)
        );
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
      api.post("/logout");

      Object.assign(this.user, defaultUser);
      localStorage.clear();
      this.token = "";
    },
  },
});
