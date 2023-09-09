import { defineConfig, loadEnv } from "vite";
import AutoImport from "unplugin-auto-import/vite";
import Components from "unplugin-vue-components/vite";
import fs from "fs";
import laravel from "laravel-vite-plugin";
import os from "os";
import vue from "@vitejs/plugin-vue";

export default defineConfig(({ command, mode }) => {
  const env = loadEnv(mode, process.cwd(), "");
  const host = env.APP_URL_BASE;
  const key =
    os.platform() === "linux"
      ? `/etc/apache2/ssl/tschool.key`
      : `C:/laragon/etc/ssl/laragon.key`;
  const cert =
    os.platform() === "linux"
      ? `/etc/apache2/ssl/tschool.crt`
      : `C:/laragon/etc/ssl/laragon.crt`;

  if (host.includes("test")) {
    https = {
      key: fs.readFileSync(key),
      cert: fs.readFileSync(cert),
    };
  } else {
    https = true;
  }

  return {
    server: {
      host,
      hmr: {
        host: host,
        overlay: false,
      },
      https: https,
      cors: true,
    },
    plugins: [
      laravel({
        input: "resources/js/main.js",
        refresh: true,
      }),
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
      include: ["@fawmi/vue-google-maps", "fast-deep-equal"],
    },
  };
});
