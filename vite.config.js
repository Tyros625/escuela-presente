import { defineConfig, loadEnv } from "vite";
import AutoImport from "unplugin-auto-import/vite";
import Components from "unplugin-vue-components/vite";
import fs from "fs";
import os from "os";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

function configureHttps(host) {
  if (host && host.includes("test") && os.platform() === "win32") {
    return {
      key: fs.readFileSync("C:/laragon/etc/ssl/laragon.key"),
      cert: fs.readFileSync("C:/laragon/etc/ssl/laragon.crt"),
    };
  }
  return true;
}

export default defineConfig(({ command, mode }) => {
  const env = loadEnv(mode, process.cwd(), "");
  const host = env.APP_URL_BASE;
  const https = configureHttps(host);

  const serverOptions = {
    host,
    https,
    cors: true,
    hmr: {
      host,
      overlay: true,
    },
  };

  const laravelPluginOptions = {
    input: "resources/js/main.js",
    refresh: true,
  };

  if (os.platform() === "darwin") {
    laravelPluginOptions.detectTls = host;
  }

  return {
    server: serverOptions,
    plugins: [
      laravel(laravelPluginOptions),
      vue({
        template: {
          transformAssetUrls: {
            base: null,
            includeAbsolute: false,
          },
        },
      }),
      Components({
        dirs: ["resources/js/components"],
        extensions: ["vue"],
        dts: "resources/js/components.d.ts",
        include: [/\.vue$/, /\.vue\?vue/],
        exclude: [
          /[\\/]node_modules[\\/]/,
          /[\\/]\.git[\\/]/,
          /[\\/]\.nuxt[\\/]/,
        ],
      }),
      AutoImport({
        imports: [
          "vue",
          "vue-router",
          {
            "@/mixins/toast": ["Toast"],
            "@/services/api": [["default", "api"]],
            axios: [["default", "axios"]],
          },
        ],
        dts: "resources/js/auto-imports.d.ts",
        vueTemplate: true,
        eslintrc: {
          enabled: true,
        },
      }),
    ],
    optimizeDeps: {
      include: ["fast-deep-equal"],
    },
  };
});
