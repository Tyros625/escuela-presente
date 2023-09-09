import router from "@/router/tenant";
import axios from "axios";
import forEach from "lodash/forEach";

const getToken = () => localStorage.getItem("token");
const getAuthorizationHeader = () => `Bearer ${getToken()}`;
/**
 * Axios basic configuration
 */
const config = {
  baseURL: "/api",
};

/**
 * Creating the instance of Axios
 * It is because in large-scale application, we may need
 * to consume APIs from more than a single server,
 */
const api = axios.create(config);

/**
 * Auth interceptors
 * @description Add auth tokens to every outgoing request.
 * @param {*} config
 */
const authInterceptor = (config) => {
  config.headers.Authorization = getAuthorizationHeader();
  config.headers.common.Accept = "Application/json";
  // config.headers["Access-Control-Allow-Origin"] = "*";
  return config;
};

/**
 * Logger interceptors
 * @description Log app requests.
 * @param {*} config
 */
const loggerInterceptor = (config) =>
  /** Add logging here */
  config;

/** Adding the request interceptors */
api.interceptors.request.use(authInterceptor);
api.interceptors.request.use(loggerInterceptor);

/** Adding the response interceptors */
api.interceptors.response.use(
  (response) => {
    if (!response.config.noloading) {
      //store.commit("endLoading");
    }
    return response;
  },
  function (error) {
    console.log("Ocurrio un error", error.response);
    if (axios.isCancel(error)) {
      console.log("Ocurrio una cancelación");
      Toast.fire({ icon: "error", title: error.message });
      //store.commit("endLoading");
      return Promise.reject(error.message);
    }

    let json = error.response.data;
    let message = json.message;

    //store.commit("endLoading");

    console.log(router);

    //If error 401 redirect to login
    if (
      error.response.status === 401 &&
      router.currentRoute.name !== "auth-signin"
    ) {
      router.push("/login");
    } else if (error.response.status === 404) {
      message = "El recurso no existe";
    }

    if (error?.config?.showErrors !== false) {
      if (json.errors !== undefined) {
        let errors = [];
        forEach(json.errors, (err) => {
          errors.push(err[0]);
        });

        Toast.fire({ icon: "error", title: errors.join("<br>") });
      } else {
        console.log(message, json);
        Toast.fire({ icon: "error", title: message });
      }
    }

    return Promise.reject(error.response);
  }
);

export default api;
